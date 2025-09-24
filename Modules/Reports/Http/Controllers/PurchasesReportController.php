<?php

namespace Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Employee;
use App\Models\InvoiceItem;
use App\Models\PaymentsProcess;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\StoreHouse;
use App\Models\Supplier;
use App\Models\Treasury;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PurchasesReportController extends Controller
{
    public function index()
    {
        return view('reports::purchases.index');
    }

  public function purchaseByEmployee(Request $request)
    {
        // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
        $employees = User::all();
        $branches = Branch::all();
        $suppliers = Supplier::all();

        // 2. تحديد التواريخ الافتراضية
        $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : now()->subMonth();
        $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : now();

        // 3. إعداد البيانات الافتراضية الفارغة
        $totals = [
            'paid_amount' => 0,
            'unpaid_amount' => 0,
            'returned_amount' => 0,
            'total_amount' => 0,
            'total_purchases' => 0,
            'total_returns' => 0,
        ];

        $groupedInvoices = collect();
        $employeeTotals = collect();
        $chartData = ['labels' => [], 'values' => []];

        // 4. إرجاع العرض
        return view('reports::purchases.purchaseReport.Purchase_By_Employee', compact(
            'groupedInvoices',
            'employees',
            'suppliers',
            'branches',
            'totals',
            'chartData',
            'fromDate',
            'toDate',
            'employeeTotals'
        ));
    }

    /**
     * دالة AJAX لجلب بيانات تقرير المشتريات
     */
   public function purchaseByEmployeeAjax(Request $request)
{
    try {
        // 1. التحقق من صحة البيانات المدخلة
        $validatedData = $request->validate([
            'supplier' => 'nullable|exists:suppliers,id',
            'branch' => 'nullable|exists:branches,id',
            'status' => 'nullable|in:0,1,5',
            'invoice_type' => 'nullable|in:invoice,return,requested,city_notice,all',
            'added_by' => 'nullable|exists:users,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'report_type' => 'nullable|in:daily,weekly,monthly,yearly,employee,returns',
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

        // 3. بناء استعلام فواتير المشتريات مع العلاقات
        $invoices = PurchaseInvoice::with(['creator', 'supplier', 'branch', 'payments_process'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        // استبعاد الفواتير المرجعية
        $invoices->whereNotIn('id', function ($query) {
            $query
                ->select('reference_number')
                ->from('purchase_invoices')
                ->whereIn('type', ['invoice', 'Return', 'Requested', 'City Notice'])
                ->whereNotNull('reference_number');
        });

        // 4. تطبيق الفلاتر
        // فلتر المورد
        if ($request->filled('supplier')) {
            $invoices->where('supplier_id', $request->supplier);
        }

        // فلتر الفرع
        if ($request->filled('branch')) {
            $invoices->where('branch_id', $request->branch);
        }

        // فلتر حالة الدفع
        if ($request->filled('status')) {
            switch ($request->status) {
                case '1': // مدفوعة
                    $invoices->where('is_paid', 1);
                    break;
                case '0': // غير مدفوعة
                    $invoices->where('is_paid', 0);
                    break;
                case '5': // مرتجعة
                    $invoices->where('type', 'Return');
                    break;
            }
        }

        // فلتر نوع الفاتورة - محدث لدعم 4 أنواع
        if ($request->filled('invoice_type') && $request->invoice_type !== 'all') {
            switch ($request->invoice_type) {
                case 'invoice':
                    $invoices->where('type', 'invoice');
                    break;
                case 'return':
                    $invoices->where('type', 'Return');
                    break;
                case 'requested':
                    $invoices->where('type', 'Requested');
                    break;
                case 'city_notice':
                    $invoices->where('type', 'City Notice');
                    break;
            }
        }

        // فلتر المضيف بواسطة
        if ($request->filled('added_by')) {
            $invoices->where('created_by', $request->added_by);
        }

        // فلتر نطاق التاريخ
        $invoices->whereBetween('date', [$fromDate, $toDate]);

        // فلتر نوع التقرير
        if ($request->filled('report_type')) {
            switch ($request->report_type) {
                case 'yearly':
                    $invoices->whereYear('date', $toDate->year);
                    break;
                case 'monthly':
                    $invoices->whereMonth('date', $toDate->month)->whereYear('date', $toDate->year);
                    break;
                case 'weekly':
                    $invoices->whereBetween('date', [$toDate->copy()->startOfWeek(), $toDate->copy()->endOfWeek()]);
                    break;
                case 'daily':
                    $invoices->whereDate('date', $toDate->toDateString());
                    break;
                case 'employee':
                    $invoices->whereHas('creator', function ($query) {
                        $query->whereHas('roles', function ($q) {
                            $q->where('name', 'employee');
                        });
                    });
                    break;
                case 'returns':
                    $invoices->where('type', 'Return');
                    break;
            }
        }

        // 5. الحصول على النتائج
        $invoices = $invoices->get();

        // 6. معالجة البيانات وتجميعها حسب النوع
        $groupedInvoices = collect();
        $totals = [
            'invoice_amount' => 0,        // فواتير عادية
            'return_amount' => 0,         // مرتجعات
            'requested_amount' => 0,      // مطلوبة
            'city_notice_amount' => 0,    // إشعار مدينة
            'paid_amount' => 0,
            'unpaid_amount' => 0,
            'total_amount' => 0,
            'invoice_count' => 0,
            'return_count' => 0,
            'requested_count' => 0,
            'city_notice_count' => 0,
        ];

        // دالة مساعدة لتحديد نوع الفاتورة
        $getInvoiceTypeInfo = function($type) {
            switch($type) {
                case 'invoice':
                    return [
                        'name' => 'فاتورة عادية',
                        'icon' => 'fas fa-file-invoice',
                        'class' => 'invoice',
                        'badge_class' => 'bg-primary'
                    ];
                case 'Return':
                    return [
                        'name' => 'مرتجع',
                        'icon' => 'fas fa-undo',
                        'class' => 'return',
                        'badge_class' => 'bg-danger'
                    ];
                case 'Requested':
                    return [
                        'name' => 'مطلوبة',
                        'icon' => 'fas fa-clock',
                        'class' => 'requested',
                        'badge_class' => 'bg-warning'
                    ];
                case 'City Notice':
                    return [
                        'name' => 'إشعار مدينة',
                        'icon' => 'fas fa-bell',
                        'class' => 'city_notice',
                        'badge_class' => 'bg-info'
                    ];
                default:
                    return [
                        'name' => 'غير محدد',
                        'icon' => 'fas fa-question',
                        'class' => 'unknown',
                        'badge_class' => 'bg-secondary'
                    ];
            }
        };

        if ($invoices->isNotEmpty()) {
            // تجميع الفواتير حسب الموظف
            $groupedInvoices = $invoices->groupBy('created_by');

            // حساب الإجماليات العامة
            foreach ($invoices as $invoice) {
                $typeInfo = $getInvoiceTypeInfo($invoice->type);

                // حساب الإجماليات حسب النوع
                switch($invoice->type) {
                    case 'invoice':
                        $totals['invoice_amount'] += $invoice->grand_total;
                        $totals['invoice_count']++;
                        $totals['total_amount'] += $invoice->grand_total;
                        break;
                    case 'Return':
                        $totals['return_amount'] += $invoice->grand_total;
                        $totals['return_count']++;
                        $totals['total_amount'] -= $invoice->grand_total; // المرتجعات تُطرح
                        break;
                    case 'Requested':
                        $totals['requested_amount'] += $invoice->grand_total;
                        $totals['requested_count']++;
                        $totals['total_amount'] += $invoice->grand_total;
                        break;
                    case 'City Notice':
                        $totals['city_notice_amount'] += $invoice->grand_total;
                        $totals['city_notice_count']++;
                        $totals['total_amount'] += $invoice->grand_total;
                        break;
                }

                // حساب المدفوع وغير المدفوع (للفواتير العادية والمطلوبة فقط)
                if (in_array($invoice->type, ['invoice', 'Requested'])) {
                    if ($invoice->is_paid == 1) {
                        $totals['paid_amount'] += $invoice->grand_total;
                    } else {
                        $paidAmount = $invoice->payments_process->sum('amount');
                        $totals['paid_amount'] += $paidAmount;
                        $totals['unpaid_amount'] += max($invoice->grand_total - $paidAmount, 0);
                    }
                }
            }

            // تحويل المجموعات إلى array للإرسال عبر JSON
            $groupedInvoicesArray = [];
            foreach ($groupedInvoices as $employeeId => $employeeInvoices) {
                $groupedInvoicesArray[$employeeId] = $employeeInvoices
                    ->map(function ($invoice) use ($getInvoiceTypeInfo) {
                        $typeInfo = $getInvoiceTypeInfo($invoice->type);
                        $paidAmount = $invoice->is_paid == 1 ? $invoice->grand_total : $invoice->payments_process->sum('amount');
                        $dueAmount = max($invoice->grand_total - $paidAmount, 0);

                        return [
                            'id' => $invoice->id,
                            'code' => $invoice->code,
                            'date' => $invoice->date,
                            'type' => $invoice->type,
                            'type_info' => $typeInfo,
                            'grand_total' => $invoice->grand_total,
                            'paid_amount' => $paidAmount,
                            'due_value' => $dueAmount,
                            'is_paid' => $invoice->is_paid,
                            'supplier' => $invoice->supplier
                                ? [
                                    'id' => $invoice->supplier->id,
                                    'trade_name' => $invoice->supplier->trade_name,
                                ]
                                : null,
                            'creator' => $invoice->creator
                                ? [
                                    'id' => $invoice->creator->id,
                                    'name' => $invoice->creator->name,
                                ]
                                : null,
                            'branch' => $invoice->branch
                                ? [
                                    'id' => $invoice->branch->id,
                                    'name' => $invoice->branch->name,
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
            'types_breakdown' => [
                'invoice' => [
                    'count' => $totals['invoice_count'],
                    'amount' => $totals['invoice_amount']
                ],
                'return' => [
                    'count' => $totals['return_count'],
                    'amount' => $totals['return_amount']
                ],
                'requested' => [
                    'count' => $totals['requested_count'],
                    'amount' => $totals['requested_amount']
                ],
                'city_notice' => [
                    'count' => $totals['city_notice_count'],
                    'amount' => $totals['city_notice_amount']
                ]
            ]
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
     * دالة للحصول على نطاق التاريخ بناءً على النوع المحدد
     */
    private function getDateRange($dateType)
    {
        $today = now();

        switch ($dateType) {
            case 'today':
                return [
                    'from' => $today->copy()->startOfDay(),
                    'to' => $today->copy()->endOfDay()
                ];
            case 'yesterday':
                return [
                    'from' => $today->copy()->subDay()->startOfDay(),
                    'to' => $today->copy()->subDay()->endOfDay()
                ];
            case 'this_week':
                return [
                    'from' => $today->copy()->startOfWeek(),
                    'to' => $today->copy()->endOfWeek()
                ];
            case 'last_week':
                return [
                    'from' => $today->copy()->subWeek()->startOfWeek(),
                    'to' => $today->copy()->subWeek()->endOfWeek()
                ];
            case 'this_month':
                return [
                    'from' => $today->copy()->startOfMonth(),
                    'to' => $today->copy()->endOfMonth()
                ];
            case 'last_month':
                return [
                    'from' => $today->copy()->subMonth()->startOfMonth(),
                    'to' => $today->copy()->subMonth()->endOfMonth()
                ];
            case 'this_year':
                return [
                    'from' => $today->copy()->startOfYear(),
                    'to' => $today->copy()->endOfYear()
                ];
            case 'last_year':
                return [
                    'from' => $today->copy()->subYear()->startOfYear(),
                    'to' => $today->copy()->subYear()->endOfYear()
                ];
            default:
                return [
                    'from' => $today->copy()->subMonth(),
                    'to' => $today->copy()
                ];
        }
    }

    /**
     * عرض صفحة دليل الموردين مع البيانات الأساسية
     */
public function SuppliersDirectory(Request $request)
{
    // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
    $employees = User::whereHas('roles', function ($query) {
        $query->whereIn('name', ['admin', 'employee', 'manager']);
    })->orderBy('name')->get();

    $branches = Branch::orderBy('name')->get();

    // الحصول على المدن والبلدان المتاحة من قاعدة البيانات
    $cities = Supplier::whereNotNull('city')
        ->where('city', '!=', '')
        ->distinct()
        ->pluck('city')
        ->sort()
        ->values();

    $countries = Supplier::whereNotNull('country')
        ->where('country', '!=', '')
        ->distinct()
        ->pluck('country')
        ->sort()
        ->values();

    // 2. إعداد البيانات الافتراضية الفارغة
    $totals = [
        'total_suppliers' => 0,
        'total_balance' => 0,
        'total_purchases' => 0,
        'total_payments' => 0,
        'active_suppliers' => 0,
        'inactive_suppliers' => 0,
    ];

    $groupedSuppliers = collect();
    $chartData = ['labels' => [], 'values' => []];

    // 3. إرجاع العرض
    return view('reports::purchases.purchaseReport.suppliers_directory', compact(
        'employees',
        'branches',
        'cities',
        'countries',
        'totals',
        'chartData',
        'groupedSuppliers'
    ));
}

/**
 * دالة AJAX لجلب بيانات دليل الموردين
 */
public function SuppliersDirectoryAjax(Request $request)
{
    try {
        // 1. التحقق من صحة البيانات المدخلة
        $validatedData = $request->validate([
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'created_by' => 'nullable|exists:users,id',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_code' => 'nullable|string|max:255',
            'balance_type' => 'nullable|in:positive,negative,zero,all',
            'group_by' => 'nullable|in:branch,city,country,creator,none',
            'sort_by' => 'nullable|in:name,code,balance,city,created_at',
            'sort_direction' => 'nullable|in:asc,desc',
            'status' => 'nullable|in:active,inactive,all',
        ]);

        // 2. بناء استعلام الموردين مع العلاقات
        $suppliers = Supplier::with(['creator', 'branch', 'purchaseInvoices', 'payments', 'account'])
            ->orderBy('trade_name', 'asc');

        // 3. تطبيق الفلاتر

        // فلتر المدينة
        if ($request->filled('city')) {
            $suppliers->where('city', 'like', '%' . $request->city . '%');
        }

        // فلتر البلد
        if ($request->filled('country')) {
            $suppliers->where('country', 'like', '%' . $request->country . '%');
        }

        // فلتر الفرع
        if ($request->filled('branch_id')) {
            $suppliers->where('branch_id', $request->branch_id);
        }

        // فلتر المنشئ
        if ($request->filled('created_by')) {
            $suppliers->where('created_by', $request->created_by);
        }

        // فلتر اسم المورد
        if ($request->filled('supplier_name')) {
            $suppliers->where('trade_name', 'like', '%' . $request->supplier_name . '%');
        }

        // فلتر كود المورد
        if ($request->filled('supplier_code')) {
            $suppliers->where('number_suply', 'like', '%' . $request->supplier_code . '%');
        }

        // 4. الحصول على النتائج
        $suppliers = $suppliers->get();

        // 5. حساب البيانات المالية لكل مورد
        $suppliers = $suppliers->map(function ($supplier) {
            // حساب إجمالي المشتريات
            $totalPurchases = $supplier->purchaseInvoices()
                ->whereIn('type', ['invoice', 'Requested'])
                ->sum('grand_total');

            // حساب إجمالي المرتجعات
            $totalReturns = $supplier->purchaseInvoices()
                ->where('type', 'Return')
                ->sum('grand_total');

            // حساب إجمالي المدفوعات
            $totalPayments = $supplier->payments()->sum('amount');

            // حساب الرصيد الحالي
            $currentBalance = ($supplier->opening_balance ?? 0) + $totalPurchases - $totalReturns - $totalPayments;

            // إضافة البيانات المحسوبة
            $supplier->total_purchases = $totalPurchases;
            $supplier->total_returns = $totalReturns;
            $supplier->total_payments = $totalPayments;
            $supplier->current_balance = $currentBalance;
            $supplier->net_purchases = $totalPurchases - $totalReturns;

            return $supplier;
        });

        // 6. تطبيق فلتر نوع الرصيد بعد الحساب
        if ($request->filled('balance_type') && $request->balance_type !== 'all') {
            $suppliers = $suppliers->filter(function ($supplier) use ($request) {
                switch ($request->balance_type) {
                    case 'positive':
                        return $supplier->current_balance > 0;
                    case 'negative':
                        return $supplier->current_balance < 0;
                    case 'zero':
                        return $supplier->current_balance == 0;
                    default:
                        return true;
                }
            });
        }

        // 7. تطبيق الترتيب
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        $suppliers = $suppliers->sortBy(function ($supplier) use ($sortBy) {
            switch ($sortBy) {
                case 'name':
                    return $supplier->trade_name;
                case 'code':
                    return $supplier->number_suply;
                case 'balance':
                    return $supplier->current_balance;
                case 'city':
                    return $supplier->city;
                case 'created_at':
                    return $supplier->created_at;
                default:
                    return $supplier->trade_name;
            }
        }, SORT_REGULAR, $sortDirection === 'desc');

        // 8. تجميع البيانات حسب النوع المحدد
        $groupedSuppliers = collect();
        $groupBy = $request->get('group_by', 'none');

        if ($groupBy !== 'none') {
            $groupedSuppliers = $suppliers->groupBy(function ($supplier) use ($groupBy) {
                switch ($groupBy) {
                    case 'branch':
                        return $supplier->branch ? $supplier->branch->name : 'بدون فرع';
                    case 'city':
                        return $supplier->city ?: 'بدون مدينة';
                    case 'country':
                        return $supplier->country ?: 'بدون بلد';
                    case 'creator':
                        return $supplier->creator ? $supplier->creator->name : 'غير محدد';
                    default:
                        return 'الكل';
                }
            });
        } else {
            $groupedSuppliers = collect(['الكل' => $suppliers]);
        }

        // 9. حساب الإجماليات
        $totals = [
            'total_suppliers' => $suppliers->count(),
            'total_balance' => $suppliers->sum('current_balance'),
            'total_purchases' => $suppliers->sum('total_purchases'),
            'total_payments' => $suppliers->sum('total_payments'),
            'total_returns' => $suppliers->sum('total_returns'),
            'net_purchases' => $suppliers->sum('net_purchases'),
            'positive_balance_count' => $suppliers->where('current_balance', '>', 0)->count(),
            'negative_balance_count' => $suppliers->where('current_balance', '<', 0)->count(),
            'zero_balance_count' => $suppliers->where('current_balance', '=', 0)->count(),
            'positive_balance_amount' => $suppliers->where('current_balance', '>', 0)->sum('current_balance'),
            'negative_balance_amount' => $suppliers->where('current_balance', '<', 0)->sum('current_balance'),
        ];

        // 10. إعداد بيانات الرسم البياني
        $chartData = [
            'labels' => [],
            'values' => [],
            'balance_chart' => [
                'labels' => ['رصيد موجب', 'رصيد سالب', 'رصيد صفر'],
                'values' => [
                    $totals['positive_balance_count'],
                    $totals['negative_balance_count'],
                    $totals['zero_balance_count']
                ]
            ],
            'top_suppliers' => $suppliers->sortByDesc('current_balance')->take(10)->map(function ($supplier) {
                return [
                    'name' => $supplier->trade_name,
                    'balance' => $supplier->current_balance
                ];
            })->values()
        ];

        // 11. تحويل المجموعات إلى array للإرسال عبر JSON
        $groupedSuppliersArray = [];
        foreach ($groupedSuppliers as $groupName => $groupSuppliers) {
            $groupedSuppliersArray[$groupName] = [
                'suppliers' => $groupSuppliers->map(function ($supplier) {
                    return [
                        'id' => $supplier->id,
                        'number_suply' => $supplier->number_suply,
                        'trade_name' => $supplier->trade_name,
                        'full_name' => $supplier->full_name,
                        'phone' => $supplier->phone,
                        'mobile' => $supplier->mobile,
                        'email' => $supplier->email,
                        'full_address' => $supplier->full_address,
                        'city' => $supplier->city,
                        'country' => $supplier->country,
                        'opening_balance' => $supplier->opening_balance,
                        'current_balance' => $supplier->current_balance,
                        'total_purchases' => $supplier->total_purchases,
                        'total_returns' => $supplier->total_returns,
                        'total_payments' => $supplier->total_payments,
                        'net_purchases' => $supplier->net_purchases,
                        'tax_number' => $supplier->tax_number,
                        'commercial_registration' => $supplier->commercial_registration,
                        'created_at' => $supplier->created_at->format('d/m/Y'),
                        'branch' => $supplier->branch ? [
                            'id' => $supplier->branch->id,
                            'name' => $supplier->branch->name,
                        ] : null,
                        'creator' => $supplier->creator ? [
                            'id' => $supplier->creator->id,
                            'name' => $supplier->creator->name,
                        ] : null,
                    ];
                })->toArray(),
                'group_totals' => [
                    'count' => $groupSuppliers->count(),
                    'total_balance' => $groupSuppliers->sum('current_balance'),
                    'total_purchases' => $groupSuppliers->sum('total_purchases'),
                    'total_returns' => $groupSuppliers->sum('total_returns'),
                    'total_payments' => $groupSuppliers->sum('total_payments'),
                ]
            ];
        }

        // 12. إرجاع البيانات كـ JSON
        return response()->json([
            'success' => true,
            'grouped_suppliers' => $groupedSuppliersArray,
            'totals' => $totals,
            'chart_data' => $chartData,
            'group_by' => $groupBy,
            'filters_applied' => array_filter($validatedData),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
        ], 500);
    }
}

public function supplierDebtAging(Request $request)
{
    // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
    $employees = User::where('role', 'employee')->get();

    $branches = Branch::orderBy('name')->get();
    $suppliers = Supplier::orderBy('trade_name')->get();
    // 2. إعداد التواريخ الافتراضية
    $fromDate = $request->get('from_date') ? Carbon::parse($request->from_date) : Carbon::now()->startOfMonth();
    $toDate = $request->get('to_date') ? Carbon::parse($request->to_date) : Carbon::now();

    // 3. إعداد البيانات الافتراضية الفارغة
    $totals = [
        'today' => 0,
        'days1to30' => 0,
        'days31to60' => 0,
        'days61to90' => 0,
        'days91to120' => 0,
        'daysOver120' => 0,
        'total_due' => 0,
    ];

    $reportData = collect();

    // 4. إرجاع العرض
    return view('reports::purchases.purchaseReport.supplier_debt_aging', compact(
        'employees',
        'branches',
        'suppliers',

        'totals',
        'reportData',
        'fromDate',
        'toDate'
    ));
}

/**
 * دالة AJAX لجلب بيانات تقرير أعمار ديون الموردين
 */
public function supplierDebtAgingAjax(Request $request)
{
    try {
        // 1. التحقق من صحة البيانات المدخلة
        $validatedData = $request->validate([
            'supplier' => 'nullable|exists:suppliers,id',
            'branch' => 'nullable|exists:branches,id',
            'supplier_type' => 'nullable|exists:categories_suppliers,id',
            'added_by' => 'nullable|exists:users,id',
            'days' => 'nullable|integer|min:1|max:365',
            'financial_year' => 'nullable|array',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        // 2. إعداد استعلام الحسابات مع العلاقات اللازمة
        $query = Account::with(['supplier', 'branch'])
            ->whereNotNull('supplier_id') // نريد فقط حسابات الموردين
            ->where('balance', '>', 0); // نريد فقط الحسابات التي عليها مديونية

        // 3. تطبيق الفلاتر
        if ($request->filled('branch')) {
            $query->whereHas('supplier', function ($q) use ($request) {
                $q->where('branch_id', $request->branch);
            });
        }

        if ($request->filled('supplier_type')) {
            $query->whereHas('supplier', function ($q) use ($request) {
                $q->where('category_id', $request->supplier_type);
            });
        }

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        if ($request->filled('added_by')) {
            $query->whereHas('supplier', function ($q) use ($request) {
                $q->where('created_by', $request->added_by);
            });
        }

        // 4. الحصول على الحسابات المفلترة
        $accounts = $query->get();

        // 5. إعداد البيانات للتقرير
        $reportData = $accounts->map(function ($account) use ($request) {
            $today = now()->startOfDay();
            $daysFilter = $request->get('days', 30); // القيمة الافتراضية 30 يوم

            // تهيئة المتغيرات
            $todayAmount = 0;
            $days1to30 = 0;
            $days31to60 = 0;
            $days61to90 = 0;
            $days91to120 = 0;
            $daysOver120 = 0;

            // الحصول على الرصيد الحالي
            $currentBalance = $account->balance;

            // تصنيف الرصيد حسب تاريخ آخر تحديث للحساب
            if ($currentBalance > 0) {
                $lastUpdateDate = $account->updated_at->startOfDay();
                $daysLate = $lastUpdateDate->diffInDays($today);

                // تصنيف المبلغ حسب عمر الدين
                if ($daysLate == 0) {
                    $todayAmount = $currentBalance;
                } elseif ($daysLate >= 1 && $daysLate <= 30) {
                    $days1to30 = $currentBalance;
                } elseif ($daysLate >= 31 && $daysLate <= 60) {
                    $days31to60 = $currentBalance;
                } elseif ($daysLate >= 61 && $daysLate <= 90) {
                    $days61to90 = $currentBalance;
                } elseif ($daysLate >= 91 && $daysLate <= 120) {
                    $days91to120 = $currentBalance;
                } else {
                    $daysOver120 = $currentBalance;
                }
            }

            // إعداد بيانات الصف
            return [
                'supplier_code' => $account->supplier->number_suply ?? 'غير محدد',
                'account_number' => $account->code ?? 'غير محدد',
                'supplier_name' => $account->supplier->trade_name ?? 'غير محدد',
                'branch' => $account->supplier->branch->name ?? 'غير محدد',
                'supplier_phone' => $account->supplier->phone ?? 'غير محدد',
                'supplier_email' => $account->supplier->email ?? 'غير محدد',
                'today' => round($todayAmount, 2),
                'days1to30' => round($days1to30, 2),
                'days31to60' => round($days31to60, 2),
                'days61to90' => round($days61to90, 2),
                'days91to120' => round($days91to120, 2),
                'daysOver120' => round($daysOver120, 2),
                'total_due' => round($currentBalance, 2),
                'credit_limit' => round($account->credit_limit ?? 0, 2),
                'available_credit' => round($account->credit_limit - $currentBalance ?? 0, 2),
                'last_update' => $account->updated_at->format('Y-m-d'),
                'days_late' => $lastUpdateDate->diffInDays($today),
            ];
        });

        // 6. حساب الإجماليات
        $totals = [
            'today' => $reportData->sum('today'),
            'days1to30' => $reportData->sum('days1to30'),
            'days31to60' => $reportData->sum('days31to60'),
            'days61to90' => $reportData->sum('days61to90'),
            'days91to120' => $reportData->sum('days91to120'),
            'daysOver120' => $reportData->sum('daysOver120'),
            'total_due' => $reportData->sum('total_due'),
            'total_suppliers' => $reportData->count(),
            'average_days_late' => $reportData->avg('days_late'),
        ];

        // 7. تجميع البيانات حسب الموردين
        $groupedSuppliers = $reportData->groupBy('supplier_name')->map(function ($supplierData, $supplierName) {
            return [
                'supplier_name' => $supplierName,
                'data' => $supplierData->toArray(),
                'supplier_totals' => [
                    'today' => $supplierData->sum('today'),
                    'days1to30' => $supplierData->sum('days1to30'),
                    'days31to60' => $supplierData->sum('days31to60'),
                    'days61to90' => $supplierData->sum('days61to90'),
                    'days91to120' => $supplierData->sum('days91to120'),
                    'daysOver120' => $supplierData->sum('daysOver120'),
                    'total_due' => $supplierData->sum('total_due'),
                ]
            ];
        });

        // 8. إعداد بيانات الرسم البياني
        $chartData = [
            'aging_labels' => ['اليوم', '1-30 يوم', '31-60 يوم', '61-90 يوم', '91-120 يوم', '+120 يوم'],
            'aging_values' => [
                $totals['today'],
                $totals['days1to30'],
                $totals['days31to60'],
                $totals['days61to90'],
                $totals['days91to120'],
                $totals['daysOver120']
            ],
            'suppliers_chart' => $reportData->take(10)->map(function ($item) {
                return [
                    'name' => $item['supplier_name'],
                    'value' => $item['total_due']
                ];
            })->values()
        ];

        // 9. إعداد التواريخ للعرض
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('d/m/Y'));
        $toDate = $request->get('to_date', now()->format('d/m/Y'));

        // 10. إرجاع البيانات كـ JSON
        return response()->json([
            'success' => true,
            'grouped_suppliers' => $groupedSuppliers,
            'report_data' => $reportData,
            'totals' => $totals,
            'chart_data' => $chartData,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'filters_applied' => array_filter($validatedData),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
        ], 500);
    }
}
public function byProduct(Request $request)
    {
        // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
        $products = Product::all();
        $categories = Category::all();
        $branches = Branch::all();
        $suppliers = Supplier::all();
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
            'total_purchases' => 0,
            'total_returns' => 0,
            'total_invoices' => 0,
        ];

        $groupedProducts = collect();
        $productTotals = collect();
        $chartData = ['labels' => [], 'quantities' => [], 'amounts' => []];

        // 4. إرجاع العرض
        return view('reports::purchases.purchaseReport.by_Product', compact(
            'groupedProducts',
            'products',
            'categories',
            'suppliers',
            'branches',
            'storehouses',
            'totals',
            'chartData',
            'fromDate',
            'toDate',
            'productTotals',
            'users'
        ));
    }

    /**
     * دالة AJAX لجلب بيانات تقرير المنتجات للمشتريات
     */
    public function byProductReportAjax(Request $request)
    {
        try {
            // 1. التحقق من صحة البيانات المدخلة
            $validatedData = $request->validate([
                'product' => 'nullable|exists:products,id',
                'category' => 'nullable|exists:categories,id',
                'supplier' => 'nullable|exists:suppliers,id',
                'branch' => 'nullable|exists:branches,id',
                'storehouse' => 'nullable|exists:store_houses,id',
                'status' => 'nullable|in:0,1,2,3,5',
                'invoice_type' => 'nullable|in:invoice,Return,Requested,City Notice',
                'receiving_status' => 'nullable|in:not_received,received,partially_received',
                'added_by' => 'nullable|exists:users,id',
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'report_type' => 'nullable|in:daily,weekly,monthly,yearly,purchase_manager,employee,returns',
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

            // 3. بناء استعلام عناصر فواتير المشتريات مع العلاقات
            $invoiceItems = InvoiceItem::with([
                'product.category',
                'purchaseInvoice' => function ($q) {
                    $q->with(['supplier', 'creator']);
                },
                'storeHouse',
            ])
                ->whereHas('purchaseInvoice', function ($query) use ($fromDate, $toDate) {
                    $query
                        // استبعاد الفواتير الأصلية التي لها مرتجعات فقط (ليس أوامر الشراء)
                        ->where(function ($q) {
                            $q->where('type', '!=', 'invoice') // إذا لم تكن فاتورة، فلا تستبعد
                              ->orWhereNotIn('id', function ($subQuery) {
                                  $subQuery
                                      ->select('reference_id')
                                      ->from('purchase_invoices')
                                      ->where('type', 'Return')
                                      ->whereNotNull('reference_id');
                              });
                        })
                        ->whereBetween('date', [$fromDate, $toDate]);
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

            // فلتر المورد
            if ($request->filled('supplier')) {
                $invoiceItems->whereHas('purchaseInvoice', function ($query) use ($request) {
                    $query->where('supplier_id', $request->supplier);
                });
            }

            // فلتر الفرع - إذا كان المورد مرتبط بفرع
            if ($request->filled('branch')) {
                $invoiceItems->whereHas('purchaseInvoice.supplier', function ($query) use ($request) {
                    $query->where('branch_id', $request->branch);
                });
            }

            // فلتر المخزن
            if ($request->filled('storehouse')) {
                $invoiceItems->where('store_house_id', $request->storehouse);
            }

            // فلتر حالة الدفع
            if ($request->filled('status')) {
                $invoiceItems->whereHas('purchaseInvoice', function ($query) use ($request) {
                    $query->where('payment_status', $request->status);
                });
            }

            // فلتر حالة الاستلام
            if ($request->filled('receiving_status')) {
                $invoiceItems->whereHas('purchaseInvoice', function ($query) use ($request) {
                    $query->where('receiving_status', $request->receiving_status);
                });
            }

            // فلتر نوع الفاتورة
            if ($request->filled('invoice_type')) {
                $invoiceItems->whereHas('purchaseInvoice', function ($query) use ($request) {
                    $query->where('type', $request->invoice_type);
                });
            }

            // فلتر المضيف بواسطة
            if ($request->filled('added_by')) {
                $invoiceItems->whereHas('purchaseInvoice', function ($query) use ($request) {
                    $query->where('created_by', $request->added_by);
                });
            }

            // فلتر نوع التقرير
            if ($request->filled('report_type')) {
                $invoiceItems->whereHas('purchaseInvoice', function ($query) use ($request, $toDate) {
                    switch ($request->report_type) {
                        case 'yearly':
                            $query->whereYear('date', $toDate->year);
                            break;
                        case 'monthly':
                            $query->whereMonth('date', $toDate->month)->whereYear('date', $toDate->year);
                            break;
                        case 'weekly':
                            $query->whereBetween('date', [$toDate->copy()->startOfWeek(), $toDate->copy()->endOfWeek()]);
                            break;
                        case 'daily':
                            $query->whereDate('date', $toDate->toDateString());
                            break;
                        case 'purchase_manager':
                            $query->whereHas('creator', function ($q) {
                                $q->whereHas('roles', function ($role) {
                                    $role->where('name', 'purchase_manager');
                                });
                            });
                            break;
                        case 'employee':
                            $query->whereHas('creator', function ($q) {
                                $q->whereHas('roles', function ($role) {
                                    $role->where('name', 'employee');
                                });
                            });
                            break;
                        case 'returns':
                            $query->where('type', 'Return');
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
                'total_purchases' => 0,
                'total_returns' => 0,
                'total_invoices' => 0,
            ];

            if ($invoiceItems->isNotEmpty()) {
                // تجميع العناصر حسب المنتج
                $groupedProducts = $invoiceItems->groupBy('product_id');

                // حساب الإجماليات العامة
                foreach ($invoiceItems as $item) {
                    $isReturn = ($item->purchaseInvoice->type === 'Return');
                    $itemTotal = $item->quantity * $item->unit_price;

                    $totals['total_quantity'] += $item->quantity;
                    $totals['total_discount'] += $item->discount_amount ?? 0;

                    if ($isReturn) {
                        $totals['total_returns'] += $itemTotal;
                        $totals['total_amount'] -= $itemTotal;
                    } else {
                        $totals['total_purchases'] += $itemTotal;
                        $totals['total_amount'] += $itemTotal;
                    }
                }

                $totals['total_invoices'] = $invoiceItems->groupBy('purchase_invoice_id')->count();

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
                                    'id' => $item->purchaseInvoice->id,
                                    'code' => $item->purchaseInvoice->code,
                                    'date' => $item->purchaseInvoice->date,
                                    'type' => $item->purchaseInvoice->type,
                                    'payment_status' => $item->purchaseInvoice->payment_status,
                                    'receiving_status' => $item->purchaseInvoice->receiving_status,
                                    'supplier' => $item->purchaseInvoice->supplier
                                        ? [
                                            'id' => $item->purchaseInvoice->supplier->id,
                                            'name' => $item->purchaseInvoice->supplier->name,
                                            'trade_name' => $item->purchaseInvoice->supplier->trade_name ?? $item->purchaseInvoice->supplier->name,
                                        ]
                                        : null,
                                    'created_by_user' => $item->purchaseInvoice->creator
                                        ? [
                                            'id' => $item->purchaseInvoice->creator->id,
                                            'name' => $item->purchaseInvoice->creator->name,
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


public function employeeSupplierPaymentsReportAjax(Request $request)
{

        // 1. التحقق من صحة البيانات المدخلة
        $validatedData = $request->validate([
            'employee' => 'nullable|exists:users,id',
            'supplier' => 'nullable|exists:suppliers,id',
            'branch' => 'nullable|exists:branches,id',
            'account' => 'nullable|exists:accounts,id',
            'treasury' => 'nullable|exists:treasuries,id',
            'payment_method' => 'nullable|in:1,2,3,4',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'date_type' => 'nullable|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year,custom',
            'report_type' => 'nullable|in:payments,both',
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

        // 3. جلب بيانات مدفوعات الموردين
        $paymentsQuery = PaymentsProcess::with(['purchase_invoice.supplier', 'purchase_invoice.creator'])
            ->where('type', 'supplier payments') // فلترة على نوع الدفع للموردين
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->whereHas('purchase_invoice', function ($q) {
                $q->whereIn('type', ['invoice', 'quotation']);
            });

        // 4. تطبيق الفلاتر على المدفوعات
        if ($request->filled('employee')) {
            $paymentsQuery->whereHas('purchase_invoice', function ($q) use ($request) {
                $q->where('created_by', $request->employee);
            });
        }

        if ($request->filled('supplier')) {
            $paymentsQuery->where('supplier_id', $request->supplier);
        }

        if ($request->filled('branch')) {
            $paymentsQuery->whereHas('purchaseInvoice.supplier', function ($q) use ($request) {
                $q->where('branch_id', $request->branch);
            });
        }

        if ($request->filled('payment_method')) {
            $paymentsQuery->where('payment_method', $request->payment_method);
        }

        // 5. جلب البيانات
        $payments = collect();

        if (!$request->filled('report_type') || $request->report_type === 'both' || $request->report_type === 'payments') {
            $payments = $paymentsQuery->orderBy('payment_date', 'desc')->get();
        }

        // 6. معالجة البيانات وتجميعها حسب الموظف
        $groupedData = collect();

        // تجميع المدفوعات حسب الموظف
        if ($payments->isNotEmpty()) {
            $groupedPayments = $payments->groupBy(function ($payment) {
                return $payment->purchaseInvoice && $payment->purchaseInvoice->creator ? $payment->purchaseInvoice->creator->id : 'unknown';
            });

            foreach ($groupedPayments as $employeeId => $employeePayments) {
                if ($employeeId !== 'unknown') {
                    $employee = $employeePayments->first()->purchaseInvoice->creator;
                    if (!$groupedData->has($employeeId)) {
                        $groupedData->put($employeeId, [
                            'employee' => $employee,
                            'payments' => collect(),
                            'total_payments' => 0,
                            'total_amount' => 0,
                        ]);
                    }
                    $currentData = $groupedData->get($employeeId);
                    $currentData['payments'] = $employeePayments;
                    $currentData['total_payments'] = $employeePayments->sum('amount');
                    $currentData['total_amount'] = $employeePayments->sum('amount');
                    $groupedData->put($employeeId, $currentData);
                }
            }
        }

        // 7. حساب الإجماليات العامة
        $totals = [
            'total_payments' => $payments->sum('amount'),
            'total_amount' => $payments->sum('amount'),
            'payments_count' => $payments->count(),
            'total_count' => $payments->count(),
        ];

        // 8. إعداد بيانات الرسم البياني
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
        ];

        // 9. تحويل البيانات للإرسال عبر JSON
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
                            'purchase_invoice' => $payment->purchaseInvoice
                                ? [
                                    'id' => $payment->purchaseInvoice->id,
                                    'code' => $payment->purchaseInvoice->code,
                                    'supplier' => $payment->purchaseInvoice->supplier
                                        ? [
                                            'id' => $payment->purchaseInvoice->supplier->id,
                                            'trade_name' => $payment->purchaseInvoice->supplier->trade_name,
                                        ]
                                        : null,
                                ]
                                : null,
                        ];
                    })
                    ->toArray(),
                'total_payments' => $data['total_payments'],
                'total_amount' => $data['total_amount'],
            ];
        }

        // 10. إرجاع البيانات كـ JSON
        return response()->json([
            'success' => true,
            'grouped_data' => $groupedDataArray,
            'totals' => $totals,
            'chart_data' => $chartData,
            'from_date' => $fromDate->format('d/m/Y'),
            'to_date' => $toDate->format('d/m/Y'),
        ]);

}

public function employeeSupplierPaymentsReport(Request $request)
{
    // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
    $employees = User::where('role', 'employee')->orderBy('name')->get();
    $suppliers = Supplier::all();
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
        'total_amount' => 0,
        'payments_count' => 0,
        'total_count' => 0,
    ];

    $groupedData = collect();
    $chartData = ['labels' => [], 'payments' => []];

    // 4. إرجاع العرض
    return view('reports::purchases.purchaseReport.supplier_payments', compact(
        'groupedData',
        'employees',
        'suppliers',
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

public function prodectPurchases(Request $request)
{
    // جلب البيانات المطلوبة للفلترة
    $suppliers = Supplier::all();
    $employees = Employee::all();
    $products = Product::all();

    // جلب فواتير المنتجات مع العلاقات
    $productInvoices = InvoiceItem::query()
        ->with(['product', 'invoice', 'purchaseInvoice', 'employee'])
        ->when($request->supplier_id, function ($query) use ($request) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        })
        ->when($request->employee_id, function ($query) use ($request) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('employee_id', $request->employee_id);
            });
        })
        ->when($request->product_id, function ($query) use ($request) {
            $query->where('product_id', $request->product_id);
        })
        ->when($request->from_date, function ($query) use ($request) {
            $query->whereDate('created_at', '>=', $request->from_date);
        })
        ->when($request->to_date, function ($query) use ($request) {
            $query->whereDate('created_at', '<=', $request->to_date);
        })
        ->orderBy('created_at', $request->order_by == 'asc' ? 'asc' : 'desc')
        ->get();

    // تجميع الفواتير حسب المنتج
    $groupedInvoices = $productInvoices->groupBy('product_id');

    // حساب الإجماليات لكل منتج
    $productTotals = [];
    foreach ($groupedInvoices as $productId => $invoices) {
        $productTotals[$productId] = [
            'total_quantity' => $invoices->sum('quantity'),
            'total_tax' => $invoices->sum(function ($invoice) {
                return $invoice->tax_1 + $invoice->tax_2;
            }),
            'total_amount' => $invoices->sum('total'),
        ];
    }

    // حساب الإجماليات الكلية
    $totalQuantity = $productInvoices->sum('quantity');
    $totalTax = $productInvoices->sum(function ($invoice) {
        return $invoice->tax_1 + $invoice->tax_2;
    });
    $totalAmount = $productInvoices->sum('total');

    // بيانات المخطط البياني
    $chartData = [
        'labels' => $groupedInvoices->map(function ($invoices) {
            return $invoices->first()->supplier->trade_name ?? 'غير معروف'; // استخدام قيمة افتراضية إذا كان supplier غير موجود
        }),
        'data' => $groupedInvoices->map(function ($invoices) {
            return $invoices->sum('grand_total');
        }),
    ];

    return view('reports.purchases.prodect.prodect_purchases', compact('groupedInvoices', 'productTotals', 'chartData', 'suppliers', 'employees', 'products', 'totalQuantity', 'totalTax', 'totalAmount'));
}

public function supplierPayments(Request $request)
{
    try {
        // Default to current month if no date range specified
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth());
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth());

        $employees = Employee::all();
        $branches = Branch::all();

        // Default report period
        $reportPeriod = $request->input('report_period', 'monthly');

        // Base query for payments
        $query = PaymentsProcess::with(['supplier', 'purchase', 'employee','purchase.creator'])
            ->whereNotNull('purchases_id')
            ->whereBetween('payment_date', [$fromDate, $toDate]);

        // Apply filters
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->input('supplier'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('employee')) {
            $query->where('employee_id', $request->input('employee'));
        }

        if ($request->filled('branch')) {
            $query->where('branch_id', $request->input('branch'));
        }

        // Fetch payments
        $payments = $query->get();

        // Group payments based on report period
        $groupedPayments = $payments->groupBy(function ($payment) use ($reportPeriod) {
            switch ($reportPeriod) {
                case 'daily':
                    return $payment->payment_date->format('Y-m-d');
                case 'weekly':
                    return $payment->payment_date->format('Y-W');
                case 'monthly':
                    return $payment->payment_date->format('Y-m');
                case 'yearly':
                    return $payment->payment_date->format('Y');
                default:
                    return $payment->payment_date->format('Y-m-d');
            }
        });

        // Prepare chart data
        $chartData = [
            'labels' => $groupedPayments->keys()->toArray(),
            'values' => $groupedPayments
                ->map(function ($periodPayments) {
                    return $periodPayments->sum('amount');
                })
                ->values()
                ->toArray(),
            'paymentMethods' => [
                $payments->where('payment_method', 1)->count(), // Cash
                $payments->where('payment_method', 2)->count(), // Check
                $payments->where('payment_method', 3)->count(), // Bank Transfer
            ],
        ];

        // Fetch suppliers for filter dropdown
        $suppliers = Supplier::all();
        $accounts = Account::all();
        $treasuries = Treasury::all();

        // Payment methods array
        $paymentMethods = [
            ['id' => 1, 'name' => 'نقدي'],
            ['id' => 2, 'name' => 'شيك'],
            ['id' => 3, 'name' => 'تحويل بنكي'],
            ['id' => 4, 'name' => 'بطاقة ائتمان'],
        ];

        return view('reports::purchases.purchaseReport.supplier_payments', [
            'payments' => $payments,
            'groupedPayments' => $groupedPayments,
            'chartData' => $chartData,
            'suppliers' => $suppliers,
            'accounts' => $accounts,
            'treasuries' => $treasuries,
            'paymentMethods' => $paymentMethods,
            'fromDate' => Carbon::parse($fromDate),
            'toDate' => Carbon::parse($toDate),
            'reportPeriod' => $reportPeriod,
            'employees' => $employees,
            'branches' => $branches,
        ]);
    } catch (\Exception $e) {
        // Log the full error
        Log::error('Error in supplier payments report', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Return an error view or redirect with a message
        return back()->with('error', 'حدث خطأ أثناء إنشاء التقرير: ' . $e->getMessage());
    }
}

public function supplierPaymentsData(Request $request)
{
    try {
        // Default to current month if no date range specified
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth());
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth());

        // Handle date type selection
        if ($request->filled('date_type') && $request->date_type !== 'custom') {
            switch ($request->date_type) {
                case 'today':
                    $fromDate = Carbon::today();
                    $toDate = Carbon::today();
                    break;
                case 'yesterday':
                    $fromDate = Carbon::yesterday();
                    $toDate = Carbon::yesterday();
                    break;
                case 'this_week':
                    $fromDate = Carbon::now()->startOfWeek();
                    $toDate = Carbon::now()->endOfWeek();
                    break;
                case 'last_week':
                    $fromDate = Carbon::now()->subWeek()->startOfWeek();
                    $toDate = Carbon::now()->subWeek()->endOfWeek();
                    break;
                case 'this_month':
                    $fromDate = Carbon::now()->startOfMonth();
                    $toDate = Carbon::now()->endOfMonth();
                    break;
                case 'last_month':
                    $fromDate = Carbon::now()->subMonth()->startOfMonth();
                    $toDate = Carbon::now()->subMonth()->endOfMonth();
                    break;
                case 'this_year':
                    $fromDate = Carbon::now()->startOfYear();
                    $toDate = Carbon::now()->endOfYear();
                    break;
                case 'last_year':
                    $fromDate = Carbon::now()->subYear()->startOfYear();
                    $toDate = Carbon::now()->subYear()->endOfYear();
                    break;
            }
        }

        // Base query for payments
        $query = PaymentsProcess::with(['supplier', 'purchase', 'employee', 'purchase.creator'])
            ->whereNotNull('purchases_id')
            ->whereBetween('payment_date', [$fromDate, $toDate]);

        // Apply filters
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->input('supplier'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('employee')) {
            $query->where('employee_id', $request->input('employee'));
        }

        if ($request->filled('branch')) {
            $query->where('branch_id', $request->input('branch'));
        }

        if ($request->filled('account')) {
            $query->where('account_id', $request->input('account'));
        }

        if ($request->filled('treasury')) {
            $query->where('treasury_id', $request->input('treasury'));
        }

        // Fetch payments
        $payments = $query->orderBy('payment_date', 'desc')->get();

        // Calculate totals
        $totalAmount = $payments->sum('amount');
        $totalCount = $payments->count();

        // Group payments for chart
        $groupedPayments = $payments->groupBy(function ($payment) {
            return $payment->payment_date->format('Y-m-d');
        });

        // Prepare chart data
        $chartData = [
            'labels' => $groupedPayments->keys()->toArray(),
            'values' => $groupedPayments->map(function ($dayPayments) {
                return $dayPayments->sum('amount');
            })->values()->toArray(),
        ];

        // Format payments for table display
        $formattedPayments = $payments->map(function ($payment, $index) {
            return [
                'index' => $index + 1,
                'employee_name' => $payment->employee->name ?? 'غير محدد',
                'payment_date' => $payment->payment_date->format('Y-m-d'),
                'supplier_name' => $payment->supplier->trade_name ?? 'غير محدد',
                'reference' => $payment->reference ?? $payment->id,
                'payment_method' => $this->getPaymentMethodName($payment->payment_method),
                'amount' => number_format($payment->amount, 2),
                'notes' => $payment->notes ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'grouped_data' => $formattedPayments,
            'totals' => [
                'total_payments' => $totalCount,
                'total_amount' => number_format($totalAmount, 2),
                'total_count' => $totalCount,
            ],
            'chart_data' => $chartData,
            'from_date' => Carbon::parse($fromDate)->format('Y-m-d'),
            'to_date' => Carbon::parse($toDate)->format('Y-m-d'),
        ]);
    } catch (\Exception $e) {
        Log::error('Error in supplier payments data', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء تحميل البيانات: ' . $e->getMessage(),
        ], 500);
    }
}

private function getPaymentMethodName($method)
{
    $methods = [
        1 => 'نقدي',
        2 => 'شيك',
        3 => 'تحويل بنكي',
        4 => 'بطاقة ائتمان',
    ];

    return $methods[$method] ?? 'غير محدد';
}

}

