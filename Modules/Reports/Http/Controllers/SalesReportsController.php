<?php

namespace Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Branch;
use App\Models\CategoriesClient;
use App\Models\Category;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentsProcess;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Status;
use App\Models\StoreHouse;
use App\Models\Treasury;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpParser\Builder\Function_;

class SalesReportsController extends Controller
{
    // تقارير المبيعات
    public function index()
    {
        return view('reports::sals.index');
    }

    public function byEmployee(Request $request)
    {
        // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
        $employees = Employee::all();
        $branches = Branch::all();
        $categories = CategoriesClient::all();
        $clients = Client::with('branch')->get();
        $users = User::where('role', 'employee')->get();

        // 2. تحديد التواريخ الافتراضية
        $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : now()->subMonth();
        $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : now();

        // 3. إعداد البيانات الافتراضية الفارغة
        $totals = [
            'paid_amount' => 0,
            'unpaid_amount' => 0,
            'returned_amount' => 0,
            'total_amount' => 0,
            'total_sales' => 0,
            'total_returns' => 0,
        ];

        $groupedInvoices = collect();
        $employeeTotals = collect();
        $chartData = ['labels' => [], 'values' => []];

        // 4. إرجاع العرض
        return view('reports::sals.salesRport.Sales_By_Employee', compact('groupedInvoices', 'employees', 'clients', 'categories', 'branches', 'totals', 'chartData', 'fromDate', 'toDate', 'employeeTotals', 'users'));
    }

    /**
     * دالة AJAX لجلب بيانات التقرير
     */
    public function byEmployeeAjax(Request $request)
    {
        try {
            // 1. التحقق من صحة البيانات المدخلة
            $validatedData = $request->validate([
                'category' => 'nullable|exists:categories_clients,id',
                'client' => 'nullable|exists:clients,id',
                'branch' => 'nullable|exists:branches,id',
                'status' => 'nullable|in:0,1,2,3,5',
                'invoice_type' => 'nullable|in:normal,returned',
                'order_origin' => 'nullable|exists:employees,id',
                'added_by' => 'nullable|exists:users,id',
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'report_type' => 'nullable|in:daily,weekly,monthly,yearly,sales_manager,employee,returns',
                'date_type' => 'nullable|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year,custom',
            ]);

            // 2. تحديد نطاق التاريخ بناءً على نوع التاريخ المحدد
            $fromDate = now()->subMonth();
            $toDate = now();

            if ($request->filled('date_type') && $request->date_type !== 'custom') {
                $dateRange = $this->getDateRange($request->date_type);
                $fromDate = $dateRange['from'];
                $toDate = $dateRange['to'];
            } elseif ($request->filled('from_date') && $request->filled('to_date')) {
                $fromDate = Carbon::parse($request->from_date);
                $toDate = Carbon::parse($request->to_date);
            } elseif ($request->filled('from_date')) {
                $fromDate = Carbon::parse($request->from_date);
            } elseif ($request->filled('to_date')) {
                $toDate = Carbon::parse($request->to_date);
            }

            // 3. بناء استعلام الفواتير مع العلاقات
            $invoices = Invoice::with(['client.branch', 'createdByUser', 'employee'])
                ->orderBy('invoice_date', 'desc')
                ->orderBy('created_at', 'desc');

            $invoices->whereNotIn('id', function ($query) {
                $query
                    ->select('reference_number')
                    ->from('invoices')
                    ->whereIn('type', ['normal', 'returned'])
                    ->whereNotNull('reference_number');
            });

            // 4. تطبيق الفلاتر
            // فلتر فئة العميل
            if ($request->filled('category')) {
                $invoices->whereHas('client', function ($query) use ($request) {
                    $query->where('category_id', $request->category);
                });
            }

            // فلتر العميل
            if ($request->filled('client')) {
                $invoices->where('client_id', $request->client);
            }

            // فلتر الفرع
            if ($request->filled('branch')) {
                $invoices->whereHas('client', function ($query) use ($request) {
                    $query->where('branch_id', $request->branch);
                });
            }

            // فلتر حالة الدفع
            if ($request->filled('status')) {
                $invoices->where('payment_status', $request->status);
            }

            // فلتر نوع الفاتورة
            if ($request->filled('invoice_type')) {
                if ($request->invoice_type === 'normal') {
                    $invoices->whereNotIn('type', ['return', 'returned']);
                } else {
                    $invoices->whereIn('type', ['return', 'returned']);
                }
            }

            // فلتر الموظف
            if ($request->filled('order_origin')) {
                $invoices->where(function ($query) use ($request) {
                    $query->where('employee_id', $request->order_origin)->orWhere('created_by', $request->order_origin);
                });
            }

            // فلتر المضيف بواسطة
            if ($request->filled('added_by')) {
                $invoices->where('created_by', $request->added_by);
            }

            // فلتر نطاق التاريخ
            $invoices->whereBetween('invoice_date', [$fromDate, $toDate]);

            // فلتر نوع التقرير
            if ($request->filled('report_type')) {
                switch ($request->report_type) {
                    case 'yearly':
                        $invoices->whereYear('invoice_date', $toDate->year);
                        break;
                    case 'monthly':
                        $invoices->whereMonth('invoice_date', $toDate->month)->whereYear('invoice_date', $toDate->year);
                        break;
                    case 'weekly':
                        $invoices->whereBetween('invoice_date', [$toDate->copy()->startOfWeek(), $toDate->copy()->endOfWeek()]);
                        break;
                    case 'daily':
                        $invoices->whereDate('invoice_date', $toDate->toDateString());
                        break;
                    case 'sales_manager':
                        $invoices->whereHas('createdByUser', function ($query) {
                            $query->whereHas('roles', function ($q) {
                                $q->where('name', 'sales_manager');
                            });
                        });
                        break;
                    case 'employee':
                        $invoices->whereHas('createdByUser', function ($query) {
                            $query->whereHas('roles', function ($q) {
                                $q->where('name', 'employee');
                            });
                        });
                        break;
                    case 'returns':
                        $invoices->whereIn('type', ['return', 'returned']);
                        break;
                }
            }

            // 5. الحصول على النتائج
            $invoices = $invoices->get();

            // 6. معالجة البيانات وتجميعها
            $groupedInvoices = collect();
            $totals = [
                'paid_amount' => 0,
                'unpaid_amount' => 0,
                'returned_amount' => 0,
                'total_amount' => 0,
                'total_sales' => 0,
                'total_returns' => 0,
            ];

            if ($invoices->isNotEmpty()) {
                // تجميع الفواتير حسب الموظف
                $groupedInvoices = $invoices->groupBy(function ($invoice) {
                    return $invoice->employee_id ?? $invoice->created_by;
                });

                // حساب الإجماليات العامة
                foreach ($invoices as $invoice) {
                    $isReturn = in_array($invoice->type, ['return', 'returned']);

                    if ($isReturn) {
                        $totals['returned_amount'] += $invoice->grand_total;
                        $totals['total_returns'] += $invoice->grand_total;
                        $totals['total_amount'] -= $invoice->grand_total;
                    } else {
                        $totals['total_sales'] += $invoice->grand_total;
                        $totals['total_amount'] += $invoice->grand_total;

                        if ($invoice->payment_status == 1) {
                            $totals['paid_amount'] += $invoice->grand_total;
                        } else {
                            $totals['paid_amount'] += $invoice->paid_amount;
                            $totals['unpaid_amount'] += $invoice->due_value;
                        }
                    }
                }

                // تحويل المجموعات إلى array للإرسال عبر JSON
                $groupedInvoicesArray = [];
                foreach ($groupedInvoices as $employeeId => $employeeInvoices) {
                    $groupedInvoicesArray[$employeeId] = $employeeInvoices
                        ->map(function ($invoice) {
                            return [
                                'id' => $invoice->id,
                                'code' => $invoice->code,
                                'invoice_date' => $invoice->invoice_date,
                                'type' => $invoice->type,
                                'grand_total' => $invoice->grand_total,
                                'paid_amount' => $invoice->paid_amount,
                                'due_value' => $invoice->due_value,
                                'payment_status' => $invoice->payment_status,
                                'client' => $invoice->client
                                    ? [
                                        'id' => $invoice->client->id,
                                        'trade_name' => $invoice->client->trade_name,
                                    ]
                                    : null,
                                'created_by_user' => $invoice->createdByUser
                                    ? [
                                        'id' => $invoice->createdByUser->id,
                                        'name' => $invoice->createdByUser->name,
                                    ]
                                    : null,
                                'employee' => $invoice->employee
                                    ? [
                                        'id' => $invoice->employee->id,
                                        'name' => $invoice->employee->name,
                                    ]
                                    : null,
                            ];
                        })
                        ->toArray();
                }
            } else {
                $groupedInvoicesArray = [];
            }

            // 7. إرجاع البيانات كـ JSON
            return response()->json([
                'success' => true,
                'grouped_invoices' => $groupedInvoicesArray,
                'totals' => $totals,
                'from_date' => $fromDate->format('d/m/Y'),
                'to_date' => $toDate->format('d/m/Y'),
                'invoice_count' => $invoices->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    private function getDateRange($dateType)
    {
        $today = now();

        switch ($dateType) {
            case 'today':
                return [
                    'from' => $today->copy()->startOfDay(),
                    'to' => $today->copy()->endOfDay(),
                ];

            case 'yesterday':
                $yesterday = $today->copy()->subDay();
                return [
                    'from' => $yesterday->copy()->startOfDay(),
                    'to' => $yesterday->copy()->endOfDay(),
                ];

            case 'this_week':
                return [
                    'from' => $today->copy()->startOfWeek(),
                    'to' => $today->copy()->endOfWeek(),
                ];

            case 'last_week':
                return [
                    'from' => $today->copy()->subWeek()->startOfWeek(),
                    'to' => $today->copy()->subWeek()->endOfWeek(),
                ];

            case 'this_month':
                return [
                    'from' => $today->copy()->startOfMonth(),
                    'to' => $today->copy()->endOfMonth(),
                ];

            case 'last_month':
                return [
                    'from' => $today->copy()->subMonth()->startOfMonth(),
                    'to' => $today->copy()->subMonth()->endOfMonth(),
                ];

            case 'this_year':
                return [
                    'from' => $today->copy()->startOfYear(),
                    'to' => $today->copy()->endOfYear(),
                ];

            case 'last_year':
                return [
                    'from' => $today->copy()->subYear()->startOfYear(),
                    'to' => $today->copy()->subYear()->endOfYear(),
                ];

            default:
                return [
                    'from' => $today->copy()->subMonth(),
                    'to' => $today,
                ];
        }
    }


    public function byProduct(Request $request)
    {
        // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
        $products = Product::all();
        $categories = Category::all();
        $branches = Branch::all();
        $client_categories = CategoriesClient::all();
        $clients = Client::with('branch')->get();
        $users = User::where('role', 'employee')->get();
        $storehouses = StoreHouse::all();

        // 2. تحديد التواريخ الافتراضية
        $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : now()->subMonth();
        $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : now();

        // 3. إعداد البيانات الافتراضية الفارغة
        $totals = [
            'total_quantity' => 0,
            'total_amount' => 0,
            'total_discount' => 0,
            'total_sales' => 0,
            'total_returns' => 0,
            'total_invoices' => 0,
        ];

        $groupedProducts = collect();
        $productTotals = collect();
        $chartData = ['labels' => [], 'quantities' => [], 'amounts' => []];

        // 4. إرجاع العرض
        return view('reports::sals.salesRport.by_Product', compact('groupedProducts', 'products', 'categories', 'clients', 'client_categories', 'branches', 'storehouses', 'totals', 'chartData', 'fromDate', 'toDate', 'productTotals', 'users'));
    }

    /**
     * دالة AJAX لجلب بيانات تقرير المنتجات
     */
    public function byProductReportAjax(Request $request)
    {
        try {
            // 1. التحقق من صحة البيانات المدخلة
            $validatedData = $request->validate([
                'product' => 'nullable|exists:products,id',
                'category' => 'nullable|exists:categories,id',
                'client' => 'nullable|exists:clients,id',
                'client_category' => 'nullable|exists:categories_clients,id',
                'branch' => 'nullable|exists:branches,id',
                'storehouse' => 'nullable|exists:store_houses,id',
                'status' => 'nullable|in:0,1,2,3,5',
                'invoice_type' => 'nullable|in:normal,returned',
                'added_by' => 'nullable|exists:users,id',
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'report_type' => 'nullable|in:daily,weekly,monthly,yearly,sales_manager,employee,returns',
                'date_type' => 'nullable|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year,custom',
            ]);

            // 2. تحديد نطاق التاريخ بناءً على نوع التاريخ المحدد
            $fromDate = now()->subMonth();
            $toDate = now();

            if ($request->filled('date_type') && $request->date_type !== 'custom') {
                $dateRange = $this->getDateRange($request->date_type);
                $fromDate = $dateRange['from'];
                $toDate = $dateRange['to'];
            } elseif ($request->filled('from_date') && $request->filled('to_date')) {
                $fromDate = Carbon::parse($request->from_date);
                $toDate = Carbon::parse($request->to_date);
            } elseif ($request->filled('from_date')) {
                $fromDate = Carbon::parse($request->from_date);
            } elseif ($request->filled('to_date')) {
                $toDate = Carbon::parse($request->to_date);
            }

            // 3. بناء استعلام عناصر الفواتير مع العلاقات
            $invoiceItems = InvoiceItem::with([
                'product.category',
                'invoice' => function ($q) {
                    $q->with(['client.branch', 'createdByUser']);
                },
                'storeHouse',
            ])
                ->whereHas('invoice', function ($query) use ($fromDate, $toDate) {
                    $query
                        ->whereNotIn('id', function ($subQuery) {
                            $subQuery
                                ->select('reference_number')
                                ->from('invoices')
                                ->whereIn('type', ['normal', 'returned'])
                                ->whereNotNull('reference_number');
                        })
                        ->whereBetween('invoice_date', [$fromDate, $toDate]);
                })
                ->orderBy('created_at', 'desc');

            // 4. تطبيق الفلاتر
            // فلتر المنتج
            if ($request->filled('product')) {
                $invoiceItems->where('product_id', $request->product);
            }

            // فلتر فئة المنتج
            if ($request->filled('category')) {
                $invoiceItems->whereHas('product', function ($query) use ($request) {
                    $query->where('category_id', $request->category);
                });
            }

            // فلتر فئة العميل
            if ($request->filled('client_category')) {
                $invoiceItems->whereHas('invoice.client', function ($query) use ($request) {
                    $query->where('category_id', $request->client_category);
                });
            }

            // فلتر العميل
            if ($request->filled('client')) {
                $invoiceItems->whereHas('invoice', function ($query) use ($request) {
                    $query->where('client_id', $request->client);
                });
            }

            // فلتر الفرع
            if ($request->filled('branch')) {
                $invoiceItems->whereHas('invoice.client', function ($query) use ($request) {
                    $query->where('branch_id', $request->branch);
                });
            }

            // فلتر المخزن
            if ($request->filled('storehouse')) {
                $invoiceItems->where('store_house_id', $request->storehouse);
            }

            // فلتر حالة الدفع
            if ($request->filled('status')) {
                $invoiceItems->whereHas('invoice', function ($query) use ($request) {
                    $query->where('payment_status', $request->status);
                });
            }

            // فلتر نوع الفاتورة
            if ($request->filled('invoice_type')) {
                $invoiceItems->whereHas('invoice', function ($query) use ($request) {
                    if ($request->invoice_type === 'normal') {
                        $query->whereNotIn('type', ['return', 'returned']);
                    } else {
                        $query->whereIn('type', ['return', 'returned']);
                    }
                });
            }

            // فلتر المضيف بواسطة
            if ($request->filled('added_by')) {
                $invoiceItems->whereHas('invoice', function ($query) use ($request) {
                    $query->where('created_by', $request->added_by);
                });
            }

            // فلتر نوع التقرير
            if ($request->filled('report_type')) {
                $invoiceItems->whereHas('invoice', function ($query) use ($request, $toDate) {
                    switch ($request->report_type) {
                        case 'yearly':
                            $query->whereYear('invoice_date', $toDate->year);
                            break;
                        case 'monthly':
                            $query->whereMonth('invoice_date', $toDate->month)->whereYear('invoice_date', $toDate->year);
                            break;
                        case 'weekly':
                            $query->whereBetween('invoice_date', [$toDate->copy()->startOfWeek(), $toDate->copy()->endOfWeek()]);
                            break;
                        case 'daily':
                            $query->whereDate('invoice_date', $toDate->toDateString());
                            break;
                        case 'sales_manager':
                            $query->whereHas('createdByUser', function ($q) {
                                $q->whereHas('roles', function ($role) {
                                    $role->where('name', 'sales_manager');
                                });
                            });
                            break;
                        case 'employee':
                            $query->whereHas('createdByUser', function ($q) {
                                $q->whereHas('roles', function ($role) {
                                    $role->where('name', 'employee');
                                });
                            });
                            break;
                        case 'returns':
                            $query->whereIn('type', ['return', 'returned']);
                            break;
                    }
                });
            }

            // 5. الحصول على النتائج
            $invoiceItems = $invoiceItems->get();

            // 6. معالجة البيانات وتجميعها
            $groupedProducts = collect();
            $totals = [
                'total_quantity' => 0,
                'total_amount' => 0,
                'total_discount' => 0,
                'total_sales' => 0,
                'total_returns' => 0,
                'total_invoices' => 0,
            ];

            if ($invoiceItems->isNotEmpty()) {
                // تجميع العناصر حسب المنتج
                $groupedProducts = $invoiceItems->groupBy('product_id');

                // حساب الإجماليات العامة
                foreach ($invoiceItems as $item) {
                    $isReturn = in_array($item->invoice->type, ['return', 'returned']);
                    $itemTotal = $item->quantity * $item->unit_price;

                    $totals['total_quantity'] += $item->quantity;
                    $totals['total_discount'] += $item->discount_amount ?? 0;

                    if ($isReturn) {
                        $totals['total_returns'] += $itemTotal;
                        $totals['total_amount'] -= $itemTotal;
                    } else {
                        $totals['total_sales'] += $itemTotal;
                        $totals['total_amount'] += $itemTotal;
                    }
                }

                $totals['total_invoices'] = $invoiceItems->groupBy('invoice_id')->count();

                // تحويل المجموعات إلى array للإرسال عبر JSON
                $groupedProductsArray = [];
                foreach ($groupedProducts as $productId => $productItems) {
                    $groupedProductsArray[$productId] = $productItems
                        ->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'discount_amount' => $item->discount_amount,
                                'total_amount' => $item->quantity * $item->unit_price,
                                'product' => [
                                    'id' => $item->product->id,
                                    'name' => $item->product->name,
                                    'code' => $item->product->code,
                                    'category' => $item->product->category
                                        ? [
                                            'id' => $item->product->category->id,
                                            'name' => $item->product->category->name,
                                        ]
                                        : null,
                                ],
                                'invoice' => [
                                    'id' => $item->invoice->id,
                                    'code' => $item->invoice->code,
                                    'invoice_date' => $item->invoice->invoice_date,
                                    'type' => $item->invoice->type,
                                    'payment_status' => $item->invoice->payment_status,
                                    'client' => $item->invoice->client
                                        ? [
                                            'id' => $item->invoice->client->id,
                                            'trade_name' => $item->invoice->client->trade_name,
                                        ]
                                        : null,
                                    'created_by_user' => $item->invoice->createdByUser
                                        ? [
                                            'id' => $item->invoice->createdByUser->id,
                                            'name' => $item->invoice->createdByUser->name,
                                        ]
                                        : null,
                                ],
                                'store_house' => $item->storeHouse
                                    ? [
                                        'id' => $item->storeHouse->id,
                                        'name' => $item->storeHouse->name,
                                    ]
                                    : null,
                            ];
                        })
                        ->toArray();
                }
            } else {
                $groupedProductsArray = [];
            }

            // 7. إرجاع البيانات كـ JSON
            return response()->json([
                'success' => true,
                'grouped_products' => $groupedProductsArray,
                'totals' => $totals,
                'from_date' => $fromDate->format('d/m/Y'),
                'to_date' => $toDate->format('d/m/Y'),
                'items_count' => $invoiceItems->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // Helper method to prepare chart data
    protected function prepareChartData1($payments)
    {
        // Group payments by client
        $groupedPayments = $payments->groupBy('clients_id');

        $chartLabels = [];
        $chartValues = [];

        foreach ($groupedPayments as $clientId => $clientPayments) {
            $client = Client::find($clientId);
            $chartLabels[] = $client ? $client->trade_name : "عميل $clientId";
            $chartValues[] = $clientPayments->sum('amount');
        }

        return [
            'labels' => $chartLabels,
            'values' => $chartValues,
        ];
    }
    public function employeePaymentsReceiptsReport(Request $request)
    {
        // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        $clients = Client::all();
        $branches = Branch::orderBy('name')->get();
        $accounts = Account::all();
        $treasuries = Treasury::all();
        $paymentMethods = [['id' => 1, 'name' => 'نقدي'], ['id' => 2, 'name' => 'شيك'], ['id' => 3, 'name' => 'تحويل بنكي'], ['id' => 4, 'name' => 'بطاقة ائتمان']];

        // 2. تحديد التواريخ الافتراضية
        $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : now()->subMonth();
        $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : now();

        // 3. إعداد البيانات الافتراضية الفارغة
        $totals = [
            'total_payments' => 0,
            'total_receipts' => 0,
            'total_amount' => 0,
            'payments_count' => 0,
            'receipts_count' => 0,
            'total_count' => 0,
        ];

        $groupedData = collect();
        $chartData = ['labels' => [], 'payments' => [], 'receipts' => []];

        // 4. إرجاع العرض
        return view('reports::sals.payments.employee_report', compact('groupedData', 'employees', 'clients', 'branches', 'accounts', 'treasuries', 'paymentMethods', 'totals', 'chartData', 'fromDate', 'toDate'));
    }

    public function employeePaymentsReceiptsReportAjax(Request $request)
    {
        try {
            // 1. التحقق من صحة البيانات المدخلة
            $validatedData = $request->validate([
                'employee' => 'nullable|exists:users,id',
                'client' => 'nullable|exists:clients,id',
                'branch' => 'nullable|exists:branches,id',
                'account' => 'nullable|exists:accounts,id',
                'treasury' => 'nullable|exists:treasuries,id',
                'payment_method' => 'nullable|in:1,2,3,4',
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'date_type' => 'nullable|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year,custom',
                'report_type' => 'nullable|in:payments,receipts,both',
            ]);

            // 2. تحديد نطاق التاريخ بناءً على نوع التاريخ المحدد
            $fromDate = now()->subMonth();
            $toDate = now();

            if ($request->filled('date_type') && $request->date_type !== 'custom') {
                $dateRange = $this->getDateRange($request->date_type);
                $fromDate = $dateRange['from'];
                $toDate = $dateRange['to'];
            } elseif ($request->filled('from_date') && $request->filled('to_date')) {
                $fromDate = Carbon::parse($request->from_date);
                $toDate = Carbon::parse($request->to_date);
            } elseif ($request->filled('from_date')) {
                $fromDate = Carbon::parse($request->from_date);
            } elseif ($request->filled('to_date')) {
                $toDate = Carbon::parse($request->to_date);
            }

            // 3. جلب بيانات المدفوعات
            $paymentsQuery = PaymentsProcess::with(['invoice.client', 'invoice.createdByUser'])
                ->where('type', 'client payments') // فلترة على نوع الدفع
                ->whereBetween('payment_date', [$fromDate, $toDate])
                ->whereHas('invoice', function ($q) {
                    $q->whereIn('type', ['normal']);
                });

            // 4. جلب بيانات سندات القبض
            $receiptsQuery = Receipt::with(['client', 'account.client', 'user', 'treasury'])
                ->whereBetween('date', [$fromDate, $toDate])
                ->whereHas('account', function ($q) {
                    $q->whereNotNull('client_id');
                });

            // 5. تطبيق الفلاتر على المدفوعات
            if ($request->filled('employee')) {
                $paymentsQuery->whereHas('invoice', function ($q) use ($request) {
                    $q->where('created_by', $request->employee);
                });
            }

            if ($request->filled('client')) {
                $paymentsQuery->where('client_id', $request->client);
            }

            if ($request->filled('branch')) {
                $paymentsQuery->whereHas('invoice.client', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch);
                });
            }

            if ($request->filled('payment_method')) {
                $paymentsQuery->where('payment_method', $request->payment_method);
            }

            // 6. تطبيق الفلاتر على سندات القبض
            if ($request->filled('employee')) {
                $receiptsQuery->where('created_by', $request->employee);
            }

            if ($request->filled('client')) {
                $receiptsQuery->where('client_id', $request->client);
            }

            if ($request->filled('account')) {
                $receiptsQuery->where('account_id', $request->account);
            }

            if ($request->filled('treasury')) {
                $receiptsQuery->where('treasury_id', $request->treasury);
            }

            // 7. جلب البيانات حسب نوع التقرير
            $payments = collect();
            $receipts = collect();

            if (!$request->filled('report_type') || $request->report_type === 'both' || $request->report_type === 'payments') {
                $payments = $paymentsQuery->orderBy('payment_date', 'desc')->get();
            }

            if (!$request->filled('report_type') || $request->report_type === 'both' || $request->report_type === 'receipts') {
                $receipts = $receiptsQuery->orderBy('date', 'desc')->get();
            }

            // 8. معالجة البيانات وتجميعها حسب الموظف
            $groupedData = collect();

            // تجميع المدفوعات حسب الموظف
            if ($payments->isNotEmpty()) {
                $groupedPayments = $payments->groupBy(function ($payment) {
                    return $payment->invoice && $payment->invoice->createdByUser ? $payment->invoice->createdByUser->id : 'unknown';
                });

                foreach ($groupedPayments as $employeeId => $employeePayments) {
                    if ($employeeId !== 'unknown') {
                        $employee = $employeePayments->first()->invoice->createdByUser;
                        if (!$groupedData->has($employeeId)) {
                            $groupedData->put($employeeId, [
                                'employee' => $employee,
                                'payments' => collect(),
                                'receipts' => collect(),
                                'total_payments' => 0,
                                'total_receipts' => 0,
                                'total_amount' => 0,
                            ]);
                        }
                        $currentData = $groupedData->get($employeeId);
                        $currentData['payments'] = $employeePayments;
                        $currentData['total_payments'] = $employeePayments->sum('amount');
                        $groupedData->put($employeeId, $currentData);
                    }
                }
            }

            // تجميع سندات القبض حسب الموظف
            if ($receipts->isNotEmpty()) {
                $groupedReceipts = $receipts->groupBy('created_by');

                foreach ($groupedReceipts as $employeeId => $employeeReceipts) {
                    if ($employeeId) {
                        $employee = $employeeReceipts->first()->user;
                        if (!$groupedData->has($employeeId)) {
                            $groupedData->put($employeeId, [
                                'employee' => $employee,
                                'payments' => collect(),
                                'receipts' => collect(),
                                'total_payments' => 0,
                                'total_receipts' => 0,
                                'total_amount' => 0,
                            ]);
                        }
                        $currentData = $groupedData->get($employeeId);
                        $currentData['receipts'] = $employeeReceipts;
                        $currentData['total_receipts'] = $employeeReceipts->sum('amount');
                        $groupedData->put($employeeId, $currentData);
                    }
                }
            }

            // حساب المجاميع للموظفين
            $groupedDataArray = $groupedData->toArray();
            foreach ($groupedDataArray as $employeeId => &$data) {
                $data['total_amount'] = $data['total_payments'] + $data['total_receipts'];
            }
            $groupedData = collect($groupedDataArray);

            // 9. حساب الإجماليات العامة
            $totals = [
                'total_payments' => $payments->sum('amount'),
                'total_receipts' => $receipts->sum('amount'),
                'total_amount' => $payments->sum('amount') + $receipts->sum('amount'),
                'payments_count' => $payments->count(),
                'receipts_count' => $receipts->count(),
                'total_count' => $payments->count() + $receipts->count(),
            ];

            // 10. إعداد بيانات الرسم البياني
            $chartData = [
                'labels' => $groupedData
                    ->map(function ($data) {
                        return $data['employee']->name ?? 'غير محدد';
                    })
                    ->values()
                    ->toArray(),
                'payments' => $groupedData
                    ->map(function ($data) {
                        return $data['total_payments'];
                    })
                    ->values()
                    ->toArray(),
                'receipts' => $groupedData
                    ->map(function ($data) {
                        return $data['total_receipts'];
                    })
                    ->values()
                    ->toArray(),
            ];

            // 11. تحويل البيانات للإرسال عبر JSON
            $groupedDataArray = [];
            foreach ($groupedData as $employeeId => $data) {
                $groupedDataArray[$employeeId] = [
                    'employee' => [
                        'id' => $data['employee']->id,
                        'name' => $data['employee']->name,
                    ],
                    'payments' => $data['payments']
                        ->map(function ($payment) {
                            return [
                                'id' => $payment->id,
                                'amount' => $payment->amount,
                                'payment_date' => $payment->payment_date,
                                'payment_method' => $payment->payment_method,
                                'reference_number' => $payment->reference_number,
                                'notes' => $payment->notes,
                                'invoice' => $payment->invoice
                                    ? [
                                        'id' => $payment->invoice->id,
                                        'code' => $payment->invoice->code,
                                        'client' => $payment->invoice->client
                                            ? [
                                                'id' => $payment->invoice->client->id,
                                                'trade_name' => $payment->invoice->client->trade_name,
                                            ]
                                            : null,
                                    ]
                                    : null,
                            ];
                        })
                        ->toArray(),
                    'receipts' => $data['receipts']
                        ->map(function ($receipt) {
                            return [
                                'id' => $receipt->id,
                                'code' => $receipt->code,
                                'amount' => $receipt->amount,
                                'date' => $receipt->date,
                                'description' => $receipt->description,
                                'client' => $receipt->account && $receipt->account->client
                                    ? [
                                        'id' => $receipt->account->client->id,
                                        'trade_name' => $receipt->account->client->trade_name,
                                    ]
                                    : null,
                                'account' => $receipt->account
                                    ? [
                                        'id' => $receipt->account->id,
                                        'name' => $receipt->account->name,
                                        'client' => $receipt->account->client
                                            ? [
                                                'id' => $receipt->account->client->id,
                                                'trade_name' => $receipt->account->client->trade_name,
                                            ]
                                            : null,
                                    ]
                                    : null,
                                'treasury' => $receipt->treasury
                                    ? [
                                        'id' => $receipt->treasury->id,
                                        'name' => $receipt->treasury->name,
                                    ]
                                    : null,
                            ];
                        })
                        ->toArray(),
                    'total_payments' => $data['total_payments'],
                    'total_receipts' => $data['total_receipts'],
                    'total_amount' => $data['total_amount'],
                ];
            }

            // 12. إرجاع البيانات كـ JSON
            return response()->json([
                'success' => true,
                'grouped_data' => $groupedDataArray,
                'totals' => $totals,
                'chart_data' => $chartData,
                'from_date' => $fromDate->format('d/m/Y'),
                'to_date' => $toDate->format('d/m/Y'),
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }


public function paymentMethodReport(Request $request)
{
    // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
    $employees = User::where('role', 'employee')->orderBy('name')->get();
    $clients = Client::all();
    $branches = Branch::orderBy('name')->get();
    $accounts = Account::all();
    $treasuries = Treasury::all();
    $paymentMethods = [
        ['id' => 1, 'name' => 'نقدي'],
        ['id' => 2, 'name' => 'شيك'],
        ['id' => 3, 'name' => 'تحويل بنكي'],
        ['id' => 4, 'name' => 'بطاقة ائتمان']
    ];

    // 2. تحديد التواريخ الافتراضية
    $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : now()->subMonth();
    $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : now();

    // 3. إعداد البيانات الافتراضية الفارغة
    $totals = [
        'total_payments' => 0,
        'total_receipts' => 0,
        'total_amount' => 0,
        'payments_count' => 0,
        'receipts_count' => 0,
        'total_count' => 0,
    ];

    $groupedData = collect();
    $chartData = ['labels' => [], 'payments' => [], 'receipts' => []];

    // 4. إرجاع العرض - تأكد من مسار الـ view الصحيح
    return view('reports::sals.payments.payment_method_report', compact(
        'groupedData',
        'employees',
        'clients',
        'branches',
        'accounts',
        'treasuries',
        'paymentMethods',
        'totals',
        'chartData',
        'fromDate',
        'toDate'
    ));
}


public function paymentMethodReportAjax(Request $request)
{
    try {
        // 1. التحقق من صحة البيانات المدخلة
        $validatedData = $request->validate([
            'employee' => 'nullable|exists:users,id',
            'client' => 'nullable|exists:clients,id',
            'branch' => 'nullable|exists:branches,id',
            'account' => 'nullable|exists:accounts,id',
            'treasury' => 'nullable|exists:treasuries,id',
            'payment_method' => 'nullable|in:1,2,3,4',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'date_type' => 'nullable|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year,custom',
            'report_type' => 'nullable|in:payments,receipts,both',
        ]);

        // 2. تحديد نطاق التاريخ بناءً على نوع التاريخ المحدد
        $fromDate = now()->subMonth();
        $toDate = now();

        if ($request->filled('date_type') && $request->date_type !== 'custom') {
            $dateRange = $this->getDateRange($request->date_type);
            $fromDate = $dateRange['from'];
            $toDate = $dateRange['to'];
        } elseif ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = Carbon::parse($request->from_date);
            $toDate = Carbon::parse($request->to_date);
        } elseif ($request->filled('from_date')) {
            $fromDate = Carbon::parse($request->from_date);
        } elseif ($request->filled('to_date')) {
            $toDate = Carbon::parse($request->to_date);
        }

        // 3. جلب بيانات المدفوعات
        $paymentsQuery = PaymentsProcess::with(['invoice.client', 'invoice.createdByUser'])
            ->where('type', 'client payments')
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->whereHas('invoice', function ($q) {
                $q->whereIn('type', ['normal']);
            });

        // 4. جلب بيانات سندات القبض
$receiptsQuery = Receipt::with(['account.client', 'user', 'treasury'])
    ->whereBetween('date', [$fromDate, $toDate])
    ->whereHas('account', function ($q) {
        $q->whereNotNull('client_id');
    });
        // 5. تطبيق الفلاتر على المدفوعات
        if ($request->filled('employee')) {
            $paymentsQuery->whereHas('invoice', function ($q) use ($request) {
                $q->where('created_by', $request->employee);
            });
        }

        if ($request->filled('client')) {
            $paymentsQuery->where('client_id', $request->client);
        }

        if ($request->filled('branch')) {
            $paymentsQuery->whereHas('invoice.client', function ($q) use ($request) {
                $q->where('branch_id', $request->branch);
            });
        }

        if ($request->filled('payment_method')) {
            $paymentsQuery->where('payment_method', $request->payment_method);
        }

        // 6. تطبيق الفلاتر على سندات القبض
        if ($request->filled('employee')) {
            $receiptsQuery->where('created_by', $request->employee);
        }

        if ($request->filled('client')) {
            $receiptsQuery->where('client_id', $request->client);
        }

        if ($request->filled('account')) {
            $receiptsQuery->where('account_id', $request->account);
        }

        if ($request->filled('treasury')) {
            $receiptsQuery->where('treasury_id', $request->treasury);
        }

        // 7. جلب البيانات حسب نوع التقرير
        $payments = collect();
        $receipts = collect();

        if (!$request->filled('report_type') || $request->report_type === 'both' || $request->report_type === 'payments') {
            $payments = $paymentsQuery->orderBy('payment_date', 'desc')->get();
        }

        if (!$request->filled('report_type') || $request->report_type === 'both' || $request->report_type === 'receipts') {
            $receipts = $receiptsQuery->orderBy('date', 'desc')->get();
        }

        // 8. معالجة البيانات وتجميعها حسب طريقة الدفع
        $groupedData = collect();
        $paymentMethods = [
            1 => 'نقدي',
            2 => 'شيك',
            3 => 'تحويل بنكي',
            4 => 'بطاقة ائتمان'
        ];

        // تجميع المدفوعات حسب طريقة الدفع
        if ($payments->isNotEmpty()) {
            $groupedPayments = $payments->groupBy('payment_method');

            foreach ($groupedPayments as $methodId => $methodPayments) {
                if (isset($paymentMethods[$methodId])) {
                    if (!$groupedData->has($methodId)) {
                        $groupedData->put($methodId, [
                            'payment_method' => [
                                'id' => $methodId,
                                'name' => $paymentMethods[$methodId]
                            ],
                            'payments' => collect(),
                            'receipts' => collect(),
                            'total_payments' => 0,
                            'total_receipts' => 0,
                            'total_amount' => 0,
                        ]);
                    }
                    $currentData = $groupedData->get($methodId);
                    $currentData['payments'] = $methodPayments;
                    $currentData['total_payments'] = $methodPayments->sum('amount');
                    $groupedData->put($methodId, $currentData);
                }
            }
        }

        // تجميع سندات القبض حسب نوع الحساب/الخزينة (نعتبرها كطريقة دفع)
        if ($receipts->isNotEmpty()) {
            foreach ($receipts as $receipt) {
                // نعتبر سندات القبض كطريقة دفع "نقدي" افتراضياً
                $methodId = 1; // نقدي

                if (!$groupedData->has($methodId)) {
                    $groupedData->put($methodId, [
                        'payment_method' => [
                            'id' => $methodId,
                            'name' => $paymentMethods[$methodId]
                        ],
                        'payments' => collect(),
                        'receipts' => collect(),
                        'total_payments' => 0,
                        'total_receipts' => 0,
                        'total_amount' => 0,
                    ]);
                }

                $currentData = $groupedData->get($methodId);
                $currentData['receipts']->push($receipt);
                $groupedData->put($methodId, $currentData);
            }

            // إعادة حساب مجاميع سندات القبض
            foreach ($groupedData as $methodId => $data) {
                if ($data['receipts']->isNotEmpty()) {
                    $data['total_receipts'] = $data['receipts']->sum('amount');
                    $groupedData->put($methodId, $data);
                }
            }
        }

        // حساب المجاميع لطرق الدفع
        $groupedDataArray = $groupedData->toArray();
        foreach ($groupedDataArray as $methodId => &$data) {
            $data['total_amount'] = $data['total_payments'] + $data['total_receipts'];
        }
        $groupedData = collect($groupedDataArray);

        // 9. حساب الإجماليات العامة
        $totals = [
            'total_payments' => $payments->sum('amount'),
            'total_receipts' => $receipts->sum('amount'),
            'total_amount' => $payments->sum('amount') + $receipts->sum('amount'),
            'payments_count' => $payments->count(),
            'receipts_count' => $receipts->count(),
            'total_count' => $payments->count() + $receipts->count(),
        ];

        // 10. إعداد بيانات الرسم البياني
        $chartData = [
            'labels' => $groupedData
                ->map(function ($data) {
                    return $data['payment_method']['name'] ?? 'غير محدد';
                })
                ->values()
                ->toArray(),
            'payments' => $groupedData
                ->map(function ($data) {
                    return $data['total_payments'];
                })
                ->values()
                ->toArray(),
            'receipts' => $groupedData
                ->map(function ($data) {
                    return $data['total_receipts'];
                })
                ->values()
                ->toArray(),
        ];

        // 11. تحويل البيانات للإرسال عبر JSON
        $groupedDataArray = [];
        foreach ($groupedData as $methodId => $data) {
            $groupedDataArray[$methodId] = [
                'payment_method' => [
                    'id' => $data['payment_method']['id'],
                    'name' => $data['payment_method']['name'],
                ],
                'payments' => $data['payments']
                    ->map(function ($payment) {
                        return [
                            'id' => $payment->id,
                            'amount' => $payment->amount,
                            'payment_date' => $payment->payment_date,
                            'payment_method' => $payment->payment_method,
                            'reference_number' => $payment->reference_number,
                            'notes' => $payment->notes,
                            'invoice' => $payment->invoice
                                ? [
                                    'id' => $payment->invoice->id,
                                    'code' => $payment->invoice->code,
                                    'client' => $payment->invoice->client
                                        ? [
                                            'id' => $payment->invoice->client->id,
                                            'trade_name' => $payment->invoice->client->trade_name,
                                        ]
                                        : null,
                                ]
                                : null,
                            'employee' => $payment->invoice && $payment->invoice->createdByUser
                                ? [
                                    'id' => $payment->invoice->createdByUser->id,
                                    'name' => $payment->invoice->createdByUser->name,
                                ]
                                : null,
                        ];
                    })
                    ->toArray(),
                'receipts' => $data['receipts']
                    ->map(function ($receipt) {
                        return [
                            'id' => $receipt->id,
                            'code' => $receipt->code,
                            'amount' => $receipt->amount,
                            'date' => $receipt->date,
                            'description' => $receipt->description,
                            'client' => $receipt->account && $receipt->account->client
                                ? [
                                    'id' => $receipt->account->client->id,
                                    'trade_name' => $receipt->account->client->trade_name,
                                ]
                                : null,
                            'account' => $receipt->account
                                ? [
                                    'id' => $receipt->account->id,
                                    'name' => $receipt->account->name,
                                ]
                                : null,
                            'treasury' => $receipt->treasury
                                ? [
                                    'id' => $receipt->treasury->id,
                                    'name' => $receipt->treasury->name,
                                ]
                                : null,
                            'employee' => $receipt->user
                                ? [
                                    'id' => $receipt->user->id,
                                    'name' => $receipt->user->name,
                                ]
                                : null,
                        ];
                    })
                    ->toArray(),
                'total_payments' => $data['total_payments'],
                'total_receipts' => $data['total_receipts'],
                'total_amount' => $data['total_amount'],
            ];
        }

        // 12. إرجاع البيانات كـ JSON
        return response()->json([
            'success' => true,
            'grouped_data' => $groupedDataArray,
            'totals' => $totals,
            'chart_data' => $chartData,
            'from_date' => $fromDate->format('d/m/Y'),
            'to_date' => $toDate->format('d/m/Y'),
        ]);
    } catch (\Exception $e) {
        return response()->json(
            [
                'success' => false,
                'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
            ],
            500,
        );
    }
}

/**
 * دالة مساعدة لتحديد نطاقات التاريخ بناءً على النوع المحدد
 */



    // دالة مساعدة لتجميع المدفوعات حسب الفترة
    protected function groupPaymentsByPeriod($payments, $period)
    {
        $grouped = [];

        foreach ($payments as $payment) {
            $periodKey = $this->getPeriodKey($payment->payment_date, $period);

            if (!isset($grouped[$periodKey])) {
                $grouped[$periodKey] = [
                    'period_name' => $this->getPeriodName($payment->payment_date, $period),
                    'items' => [],
                    'total_amount' => 0,
                    'count' => 0, // إضافة عداد للمدفوعات
                ];
            }

            $grouped[$periodKey]['items'][] = $payment;
            $grouped[$periodKey]['total_amount'] += $payment->amount;
            $grouped[$periodKey]['count']++; // زيادة العداد
        }

        return [
            'grouped_data' => $grouped,
            'grand_total' => array_sum(array_column($grouped, 'total_amount')),
        ];
    }

    // دالة مساعدة للحصول على مفتاح الفترة
    protected function getPeriodKey($date, $period)
    {
        switch ($period) {
            case 'daily':
                return $date->format('Y-m-d');
            case 'weekly':
                return $date->format('Y-W');
            case 'monthly':
                return $date->format('Y-m');
            case 'yearly':
                return $date->format('Y');
            default:
                return $date->format('Y-m-d');
        }
    }

    // دالة مساعدة للحصول على اسم الفترة
    protected function getPeriodName($date, $period)
    {
        switch ($period) {
            case 'daily':
                return $date->locale('ar')->isoFormat('dddd، LL');
            case 'weekly':
                return 'الأسبوع ' . $date->weekOfYear . ' (' . $date->startOfWeek()->format('Y-m-d') . ' إلى ' . $date->endOfWeek()->format('Y-m-d') . ')';
            case 'monthly':
                return $date->locale('ar')->isoFormat('MMMM YYYY');
            case 'yearly':
                return $date->format('Y');
            default:
                return $date->format('Y-m-d');
        }
    }


    /**
     * تقرير أرباح الموظفين
     */

    public function employeeComprehensiveProfitsReport(Request $request)
    {
        // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        $clients = Client::orderBy('trade_name')->get();
        $branches = Branch::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $brands = Product::distinct()->pluck('brand')->filter();

        // 2. تحديد التواريخ الافتراضية
        $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : now()->subMonth();
        $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : now();

        // 3. إعداد البيانات الافتراضية الفارغة
        $totals = [
            'total_revenue' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'total_profit_margin' => 0,
            'total_transactions' => 0,
            'total_quantity_sold' => 0,
        ];

        $groupedData = collect();
        $chartData = ['labels' => [], 'profits' => [], 'revenues' => [], 'costs' => []];

        // 4. إرجاع العرض
        return view('reports::sals.proudect_proifd.employee_profits', compact(
            'groupedData',
            'employees',
            'clients',
            'branches',
            'products',
            'categories',
            'brands',
            'totals',
            'chartData',
            'fromDate',
            'toDate'
        ));
    }

    public function employeeComprehensiveProfitsReportAjax(Request $request)
    {
        try {
            // 1. التحقق من صحة البيانات المدخلة
            $validatedData = $request->validate([
                'employee' => 'nullable|exists:users,id',
                'client' => 'nullable|exists:clients,id',
                'branch' => 'nullable|exists:branches,id',
                'product' => 'nullable|exists:products,id',
                'category' => 'nullable|exists:categories,id',
                'brand' => 'nullable|string',
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'date_type' => 'nullable|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year,custom',
                'report_type' => 'nullable|in:employee_profits,client_profits,product_profits,comprehensive',
            ]);

            // 2. تحديد نطاق التاريخ بناءً على نوع التاريخ المحدد
            $fromDate = now()->subMonth();
            $toDate = now();

            if ($request->filled('date_type') && $request->date_type !== 'custom') {
                $dateRange = $this->getDateRange($request->date_type);
                $fromDate = $dateRange['from'];
                $toDate = $dateRange['to'];
            } elseif ($request->filled('from_date') && $request->filled('to_date')) {
                $fromDate = Carbon::parse($request->from_date);
                $toDate = Carbon::parse($request->to_date);
            } elseif ($request->filled('from_date')) {
                $fromDate = Carbon::parse($request->from_date);
            } elseif ($request->filled('to_date')) {
                $toDate = Carbon::parse($request->to_date);
            }

            // 3. جلب بيانات المبيعات مع التفاصيل
            $salesQuery = InvoiceItem::select([
                'invoice_items.*',
                'invoices.invoice_date',
                'invoices.created_by as employee_id',
                'invoices.client_id',

                'products.name as product_name',
                'products.category_id',
                'products.brand',
                'products.purchase_price',
                'users.name as employee_name',
                'clients.trade_name as client_name',
                'categories.name as category_name'
            ])
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->join('users', 'invoices.created_by', '=', 'users.id')
            ->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('invoices.invoice_date', [$fromDate, $toDate])
            ->whereIn('invoices.type', ['normal', 'pos']);

            // 4. تطبيق الفلاتر
            if ($request->filled('employee')) {
                $salesQuery->where('invoices.created_by', $request->employee);
            }

            if ($request->filled('client')) {
                $salesQuery->where('invoices.client_id', $request->client);
            }

            if ($request->filled('branch')) {
                $salesQuery->where('invoices.branch_id', $request->branch);
            }

            if ($request->filled('product')) {
                $salesQuery->where('invoice_items.product_id', $request->product);
            }

            if ($request->filled('category')) {
                $salesQuery->where('products.category_id', $request->category);
            }

            if ($request->filled('brand')) {
                $salesQuery->where('products.brand', $request->brand);
            }

            // 5. جلب البيانات وحساب الأرباح
            $salesData = $salesQuery->orderBy('invoices.invoice_date', 'desc')->get();

            // 6. معالجة البيانات وتجميعها حسب نوع التقرير
            $reportType = $request->input('report_type', 'comprehensive');
            $groupedData = $this->processDataByReportType($salesData, $reportType);

            // 7. حساب الإجماليات العامة
            $totals = $this->calculateTotals($salesData);

            // 8. إعداد بيانات الرسم البياني
            $chartData = $this->prepareChartDataProfits($groupedData, $reportType);

            // 9. تحويل البيانات للإرسال عبر JSON
            $groupedDataArray = $this->formatDataForJson($groupedData, $reportType);

            // 10. إرجاع البيانات كـ JSON
            return response()->json([
                'success' => true,
                'grouped_data' => $groupedDataArray,
                'totals' => $totals,
                'chart_data' => $chartData,
                'report_type' => $reportType,
                'from_date' => $fromDate->format('d/m/Y'),
                'to_date' => $toDate->format('d/m/Y'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
            ], 500);
        }
    }



    private function processDataByReportType($salesData, $reportType)
    {
        switch ($reportType) {
            case 'employee_profits':
                return $this->groupByEmployee($salesData);
            case 'client_profits':
                return $this->groupByClient($salesData);
            case 'product_profits':
                return $this->groupByProduct($salesData);
            case 'comprehensive':
            default:
                return $this->groupComprehensive($salesData);
        }
    }

    private function groupByEmployee($salesData)
    {
        return $salesData->groupBy('employee_id')->map(function ($items, $employeeId) {
            $employee = $items->first();

            $totalRevenue = $items->sum('total');
            $totalCost = $items->sum(function ($item) {
                return $item->quantity * ($item->purchase_price ?? 0);
            });
            $totalProfit = $totalRevenue - $totalCost;
            $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

            return [
                'id' => $employeeId,
                'name' => $employee->employee_name,
                'type' => 'employee',
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'profit_margin' => $profitMargin,
                'total_quantity' => $items->sum('quantity'),
                'transactions_count' => $items->count(),
                'items' => $items->toArray()
            ];
        });
    }

    private function groupByClient($salesData)
    {
        return $salesData->groupBy('client_id')->map(function ($items, $clientId) {
            $client = $items->first();

            $totalRevenue = $items->sum('total');
            $totalCost = $items->sum(function ($item) {
                return $item->quantity * ($item->purchase_price ?? 0);
            });
            $totalProfit = $totalRevenue - $totalCost;
            $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

            return [
                'id' => $clientId,
                'name' => $client->client_name ?? 'عميل غير محدد',
                'type' => 'client',
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'profit_margin' => $profitMargin,
                'total_quantity' => $items->sum('quantity'),
                'transactions_count' => $items->count(),
                'items' => $items->toArray()
            ];
        });
    }

    private function groupByProduct($salesData)
    {
        return $salesData->groupBy('product_id')->map(function ($items, $productId) {
            $product = $items->first();

            $totalRevenue = $items->sum('total');
            $totalCost = $items->sum(function ($item) {
                return $item->quantity * ($item->purchase_price ?? 0);
            });
            $totalProfit = $totalRevenue - $totalCost;
            $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

            return [
                'id' => $productId,
                'name' => $product->product_name,
                'type' => 'product',
                'category' => $product->category_name ?? 'غير محدد',
                'brand' => $product->brand ?? 'غير محدد',
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'profit_margin' => $profitMargin,
                'total_quantity' => $items->sum('quantity'),
                'transactions_count' => $items->count(),
                'items' => $items->toArray()
            ];
        });
    }

    private function groupComprehensive($salesData)
    {
        $employees = $this->groupByEmployee($salesData);
        $clients = $this->groupByClient($salesData);
        $products = $this->groupByProduct($salesData);

        return [
            'employees' => $employees,
            'clients' => $clients,
            'products' => $products
        ];
    }

    private function calculateTotals($salesData)
    {
        $totalRevenue = $salesData->sum('total');
        $totalCost = $salesData->sum(function ($item) {
            return $item->quantity * ($item->purchase_price ?? 0);
        });
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'total_profit_margin' => $profitMargin,
            'total_transactions' => $salesData->count(),
            'total_quantity_sold' => $salesData->sum('quantity'),
        ];
    }


    private function formatDataForJson($groupedData, $reportType)
    {
        if ($reportType === 'comprehensive') {
            return [
                'employees' => $groupedData['employees']->values()->toArray(),
                'clients' => $groupedData['clients']->values()->toArray(),
                'products' => $groupedData['products']->values()->toArray()
            ];
        }

        return $groupedData->values()->toArray();
    }
private function prepareChartDataProfits($groupedData, $reportType)
    {
        if ($reportType === 'comprehensive') {
            // للتقرير الشامل، نأخذ بيانات الموظفين
            $data = $groupedData['employees'] ?? collect();
        } else {
            $data = $groupedData;
        }

        $labels = $data->pluck('name')->take(10)->toArray();
        $profits = $data->pluck('total_profit')->take(10)->toArray();
        $revenues = $data->pluck('total_revenue')->take(10)->toArray();
        $costs = $data->pluck('total_cost')->take(10)->toArray();

        return [
            'labels' => $labels,
            'profits' => $profits,
            'revenues' => $revenues,
            'costs' => $costs
        ];
    }

    public function byItemReport(Request $request)
    {
        // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
        $clients = Client::all();
        $employees = Employee::all();
        $products = Product::all();
        $branches = Branch::all();
        $categories = Category::all();
        $storehouses = StoreHouse::all();
        $users = User::where('role', 'employee')->get();

        // 2. تحديد التواريخ الافتراضية
        $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : now()->subMonth();
        $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : now();

        // 3. إعداد البيانات الافتراضية الفارغة
        $totals = [
            'total_quantity' => 0,
            'total_amount' => 0,
            'total_discount' => 0,
            'total_sales' => 0,
            'total_returns' => 0,
            'total_invoices' => 0,
        ];

        $groupedItems = collect();
        $itemTotals = collect();
        $chartData = ['labels' => [], 'quantities' => [], 'amounts' => []];

        // 4. إرجاع العرض
        return view('reports::sals.salesRport.itemReport', compact('groupedItems', 'clients', 'employees', 'products', 'branches', 'categories', 'storehouses', 'totals', 'chartData', 'fromDate', 'toDate', 'itemTotals', 'users'));
    }

    /**
     * دالة AJAX لجلب بيانات تقرير البنود
     */
    public function byItemReportAjax(Request $request)
    {
        try {
            // 1. التحقق من صحة البيانات المدخلة
            $validatedData = $request->validate([
                'item' => 'nullable|exists:products,id',
                'category' => 'nullable|exists:categories,id',
                'client' => 'nullable|exists:clients,id',
                'branch' => 'nullable|exists:branches,id',
                'storehouse' => 'nullable|exists:store_houses,id',
                'employee' => 'nullable|exists:employees,id',
                'status' => 'nullable|in:فاتورة,مسودة',
                'invoice_type' => 'nullable|in:1,2,3',
                'added_by' => 'nullable|exists:users,id',
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'period' => 'nullable|in:daily,weekly,monthly,yearly',
                'date_type' => 'nullable|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year,custom',
            ]);

            // 2. تحديد نطاق التاريخ بناءً على نوع التاريخ المحدد
            $fromDate = now()->subMonth();
            $toDate = now();

            if ($request->filled('date_type') && $request->date_type !== 'custom') {
                $dateRange = $this->getDateRange($request->date_type);
                $fromDate = $dateRange['from'];
                $toDate = $dateRange['to'];
            } elseif ($request->filled('from_date') && $request->filled('to_date')) {
                $fromDate = Carbon::parse($request->from_date);
                $toDate = Carbon::parse($request->to_date);
            } elseif ($request->filled('from_date')) {
                $fromDate = Carbon::parse($request->from_date);
            } elseif ($request->filled('to_date')) {
                $toDate = Carbon::parse($request->to_date);
            }

            // 3. بناء استعلام عناصر الفواتير مع العلاقات
            $invoiceItems = InvoiceItem::with([
                'product.category',
                'invoice' => function ($q) {
                    $q->with(['client.branch', 'createdByUser', 'employee']);
                },
                'storeHouse',
            ])
                ->whereHas('invoice', function ($query) use ($fromDate, $toDate) {
                    $query
                        ->whereNotIn('id', function ($subQuery) {
                            $subQuery
                                ->select('reference_number')
                                ->from('invoices')
                                ->whereIn('type', ['normal', 'returned'])
                                ->whereNotNull('reference_number');
                        })
                        ->whereBetween('invoice_date', [$fromDate, $toDate]);
                })
                ->orderBy('created_at', 'desc');

            // 4. تطبيق الفلاتر
            // فلتر البند/المنتج
            if ($request->filled('item')) {
                $invoiceItems->where('product_id', $request->item);
            }

            // فلتر فئة المنتج
            if ($request->filled('category')) {
                $invoiceItems->whereHas('product', function ($query) use ($request) {
                    $query->where('category_id', $request->category);
                });
            }

            // فلتر العميل
            if ($request->filled('client')) {
                $invoiceItems->whereHas('invoice', function ($query) use ($request) {
                    $query->where('client_id', $request->client);
                });
            }

            // فلتر الفرع
            if ($request->filled('branch')) {
                $invoiceItems->whereHas('invoice.client', function ($query) use ($request) {
                    $query->where('branch_id', $request->branch);
                });
            }

            // فلتر المخزن
            if ($request->filled('storehouse')) {
                $invoiceItems->where('store_house_id', $request->storehouse);
            }

            // فلتر الموظف
            if ($request->filled('employee')) {
                $invoiceItems->whereHas('invoice', function ($query) use ($request) {
                    $query->where('employee_id', $request->employee);
                });
            }

            // فلتر حالة الفاتورة
            if ($request->filled('status')) {
                $invoiceItems->whereHas('invoice', function ($query) use ($request) {
                    if ($request->status === 'فاتورة') {
                        $query->where('payment_status', '!=', 'draft');
                    } elseif ($request->status === 'مسودة') {
                        $query->where('payment_status', 'draft');
                    }
                });
            }

            // فلتر نوع الفاتورة
            if ($request->filled('invoice_type')) {
                $invoiceItems->whereHas('invoice', function ($query) use ($request) {
                    switch ($request->invoice_type) {
                        case '1':
                            $query->whereIn('type', ['return', 'returned']);
                            break;
                        case '2':
                            $query->where('type', 'debit_note');
                            break;
                        case '3':
                            $query->where('type', 'credit_note');
                            break;
                    }
                });
            }

            // فلتر المضيف بواسطة
            if ($request->filled('added_by')) {
                $invoiceItems->whereHas('invoice', function ($query) use ($request) {
                    $query->where('created_by', $request->added_by);
                });
            }

            // فلتر الفترة
            if ($request->filled('period')) {
                $invoiceItems->whereHas('invoice', function ($query) use ($request, $toDate) {
                    $now = Carbon::now();
                    switch ($request->period) {
                        case 'daily':
                            $query->whereDate('invoice_date', $now->toDateString());
                            break;
                        case 'weekly':
                            $query->whereBetween('invoice_date', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()]);
                            break;
                        case 'monthly':
                            $query->whereMonth('invoice_date', $now->month);
                            break;
                        case 'yearly':
                            $query->whereYear('invoice_date', $now->year);
                            break;
                    }
                });
            }

            // 5. الحصول على النتائج
            $invoiceItems = $invoiceItems->get();

            // 6. معالجة البيانات وتجميعها
            $groupedItems = collect();
            $totals = [
                'total_quantity' => 0,
                'total_amount' => 0,
                'total_discount' => 0,
                'total_sales' => 0,
                'total_returns' => 0,
                'total_invoices' => 0,
            ];

            if ($invoiceItems->isNotEmpty()) {
                // تجميع العناصر حسب البند
                $groupedItems = $invoiceItems->groupBy('product.name');

                // حساب الإجماليات العامة
                foreach ($invoiceItems as $item) {
                    $isReturn = in_array($item->invoice->type, ['return', 'returned']);
                    $itemTotal = $item->quantity * $item->unit_price;

                    $totals['total_quantity'] += $item->quantity;
                    $totals['total_discount'] += $item->discount_amount ?? 0;

                    if ($isReturn) {
                        $totals['total_returns'] += $itemTotal;
                        $totals['total_amount'] -= $itemTotal;
                    } else {
                        $totals['total_sales'] += $itemTotal;
                        $totals['total_amount'] += $itemTotal;
                    }
                }

                $totals['total_invoices'] = $invoiceItems->groupBy('invoice_id')->count();

                // تحويل المجموعات إلى array للإرسال عبر JSON
                $groupedItemsArray = [];
                foreach ($groupedItems as $itemName => $itemsData) {
                    $groupedItemsArray[$itemName] = $itemsData
                        ->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'discount_amount' => $item->discount_amount,
                                'total_amount' => $item->quantity * $item->unit_price,
                                'product' => [
                                    'id' => $item->product->id,
                                    'name' => $item->product->name,
                                    'code' => $item->product->code,
                                    'category' => $item->product->category
                                        ? [
                                            'id' => $item->product->category->id,
                                            'name' => $item->product->category->name,
                                        ]
                                        : null,
                                ],
                                'invoice' => [
                                    'id' => $item->invoice->id,
                                    'code' => $item->invoice->code,
                                    'invoice_date' => $item->invoice->invoice_date,
                                    'type' => $item->invoice->type,
                                    'payment_status' => $item->invoice->payment_status,
                                    'client' => $item->invoice->client
                                        ? [
                                            'id' => $item->invoice->client->id,
                                            'trade_name' => $item->invoice->client->trade_name,
                                        ]
                                        : null,
                                    'employee' => $item->invoice->employee
                                        ? [
                                            'id' => $item->invoice->employee->id,
                                            'full_name' => $item->invoice->employee->full_name,
                                        ]
                                        : null,
                                    'created_by_user' => $item->invoice->createdByUser
                                        ? [
                                            'id' => $item->invoice->createdByUser->id,
                                            'name' => $item->invoice->createdByUser->name,
                                        ]
                                        : null,
                                ],
                                'store_house' => $item->storeHouse
                                    ? [
                                        'id' => $item->storeHouse->id,
                                        'name' => $item->storeHouse->name,
                                    ]
                                    : null,
                            ];
                        })
                        ->toArray();
                }
            } else {
                $groupedItemsArray = [];
            }

            // 7. إرجاع البيانات كـ JSON
            return response()->json([
                'success' => true,
                'grouped_items' => $groupedItemsArray,
                'totals' => $totals,
                'from_date' => $fromDate->format('d/m/Y'),
                'to_date' => $toDate->format('d/m/Y'),
                'items_count' => $invoiceItems->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
