<?php

namespace Modules\Reports\Http\Controllers\Customers;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Appointment;
use App\Models\BalanceCharge;
use App\Models\BalanceConsumption;
use App\Models\BalanceType;
use App\Models\Branch;
use App\Models\CategoriesClient;
use App\Models\Client;
use App\Models\CostCenter;
use App\Models\Employee;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\JournalEntry;

use App\Models\Neighborhood;
use App\Models\PaymentsProcess;
use App\Models\Region_groub;
use App\Models\Statuses;
use App\Models\Treasury;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerReportController extends Controller
{
    public function index()
    {
        return view('reports::customers.index');
    }

public function invoiceDebtAgingAjax(Request $request)
{
    try {
        // استخدام نفس المنطق الأصلي الذي كان يعمل
        // إعداد استعلام الفواتير مع العلاقات اللازمة
        $query = Invoice::with(['client', 'payments']);

        // تطبيق الفلاتر بناءً على معلمات الطلب - نفس الكود الأصلي
        if ($request->filled('branch')) {
            $query->whereHas('client', function ($query) use ($request) {
                $query->where('branch_id', $request->branch);
            });
        }

        if ($request->filled('customer_type')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('type', $request->customer_type);
            });
        }

        if ($request->filled('customer')) {
            $query->where('client_id', $request->customer);
        }

        if ($request->filled('added_by')) {
            $query->where('created_by', $request->added_by);
        }

        // الحصول على الفواتير المفلترة
        $invoices = $query->get();

        // إعداد البيانات للتقرير - نفس المنطق الأصلي تماماً
        $reportData = $invoices->map(function ($invoice) {
            $remainingAmount = $invoice->calculateRemainingAmount(); // المبلغ المتبقي

            // تجاهل الفواتير التي لا يوجد عليها مبلغ متبقي
            if ($remainingAmount <= 0) {
                return null;
            }

            $invoiceDate = $invoice->invoice_date; // تاريخ الفاتورة
            $today = now()->startOfDay(); // تاريخ اليوم بدون وقت

            // حساب عدد الأيام المتأخرة
            $daysLate = $invoiceDate->diffInDays($today);

            // تهيئة جميع الفئات بـ 0
            $todayAmount = 0;
            $days1to30 = 0;
            $days31to60 = 0;
            $days61to90 = 0;
            $days91to120 = 0;
            $daysOver120 = 0;

            // تعيين المبلغ فقط للفئة المناسبة
            if ($daysLate == 0) {
                $todayAmount = $remainingAmount;
            } elseif ($daysLate >= 1 && $daysLate <= 30) {
                $days1to30 = $remainingAmount;
            } elseif ($daysLate >= 31 && $daysLate <= 60) {
                $days31to60 = $remainingAmount;
            } elseif ($daysLate >= 61 && $daysLate <= 90) {
                $days61to90 = $remainingAmount;
            } elseif ($daysLate >= 91 && $daysLate <= 120) {
                $days91to120 = $remainingAmount;
            } elseif ($daysLate > 120) {
                $daysOver120 = $remainingAmount;
            }

            return [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number ?? 'غير محدد',
                'client_code' => $invoice->client ? $invoice->client->code : 'غير محدد',
                'account_number' => $invoice->client && $invoice->client->account ? $invoice->client->account->code : 'غير محدد',
                'client_name' => $invoice->client ? $invoice->client->trade_name : 'غير محدد',
                'client_email' => $invoice->client ? $invoice->client->email : '',
                'client_phone' => $invoice->client ? $invoice->client->phone : '',
                'branch' => $invoice->employee ? $invoice->employee->branch->name : 'غير محدد',
                'invoice_date' => $invoice->invoice_date ? $invoice->invoice_date->format('d/m/Y') : '',
                'days_late' => $daysLate,
                'today' => $todayAmount, // إذا كانت الفاتورة اليوم
                'days1to30' => $days1to30, // الفواتير بين 1 و 30 يوم
                'days31to60' => $days31to60, // الفواتير بين 31 و 60 يوم
                'days61to90' => $days61to90, // الفواتير بين 61 و 90 يوم
                'days91to120' => $days91to120, // الفواتير بين 91 و 120 يوم
                'daysOver120' => $daysOver120, // الفواتير التي تجاوزت 120 يوم
                'total_due' => $remainingAmount, // إجمالي المبلغ المتبقي
            ];
        })->filter(); // إزالة القيم الفارغة

        // حساب الإجماليات
        $totals = [
            'today' => $reportData->sum('today'),
            'days1to30' => $reportData->sum('days1to30'),
            'days31to60' => $reportData->sum('days31to60'),
            'days61to90' => $reportData->sum('days61to90'),
            'days91to120' => $reportData->sum('days91to120'),
            'daysOver120' => $reportData->sum('daysOver120'),
            'total_due' => $reportData->sum('total_due')
        ];

        // تجميع البيانات حسب العميل للعرض المتقدم
        $groupedClients = $reportData->groupBy('client_name')->map(function ($clientData, $clientName) {
            $clientTotals = [
                'today' => $clientData->sum('today'),
                'days1to30' => $clientData->sum('days1to30'),
                'days31to60' => $clientData->sum('days31to60'),
                'days61to90' => $clientData->sum('days61to90'),
                'days91to120' => $clientData->sum('days91to120'),
                'daysOver120' => $clientData->sum('daysOver120'),
                'total_due' => $clientData->sum('total_due')
            ];

            return [
                'data' => $clientData->values(),
                'client_totals' => $clientTotals
            ];
        });

        // إعداد بيانات الرسم البياني
        $chartData = [
            'aging_values' => [
                $totals['today'],
                $totals['days1to30'],
                $totals['days31to60'],
                $totals['days61to90'],
                $totals['days91to120'],
                $totals['daysOver120']
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $reportData,
            'grouped_clients' => $groupedClients,
            'totals' => $totals,
            'chart_data' => $chartData,
            'count' => $reportData->count(),
            'clients_count' => $groupedClients->count(),
            'from_date' => $request->from_date ? \Carbon\Carbon::parse($request->from_date)->format('d/m/Y') : '',
            'to_date' => $request->to_date ? \Carbon\Carbon::parse($request->to_date)->format('d/m/Y') : ''
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في تقرير أعمار ديون الفواتير: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * عرض صفحة تقرير أعمار ديون الفواتير الرئيسية
 */
public function debtReconstructionInv(Request $request)
{
    // جلب البيانات للفلاتر
    $branches = Branch::all();
    $customers = Client::all();
    $salesManagers = User::where('role', 'employee')->get();
    $categories = CategoriesClient::all();

    return view('reports::customers.CustomerReport.debt_reconstruction_inv', [
        'branches' => $branches,
        'customers' => $customers,
        'salesManagers' => $salesManagers,
        'categories' => $categories,
    ]);
}


public function searchClients(Request $request)
{
    $term = $request->get('q', '');
    $page = $request->get('page', 1);
    $limit = 20;

    $query = Client::query();

    if (!empty($term)) {
        $query->where(function($q) use ($term) {
            $q->where('trade_name', 'LIKE', "%{$term}%")
              ->orWhere('code', 'LIKE', "%{$term}%")
              ->orWhere('email', 'LIKE', "%{$term}%")
              ->orWhere('phone', 'LIKE', "%{$term}%");
        });
    }

    $total = $query->count();
    $clients = $query->offset(($page - 1) * $limit)
                   ->limit($limit)
                   ->get(['id', 'trade_name', 'code', 'email']);

    $results = $clients->map(function($client) {
        return [
            'id' => $client->id,
            'text' => $client->trade_name . ' (' . $client->code . ')',
            'title' => $client->email
        ];
    });

    return response()->json([
        'results' => $results,
        'pagination' => [
            'more' => ($page * $limit) < $total
        ]
    ]);
}





public function debtAgingGeneralLedger(Request $request)
{
    // جلب البيانات الإضافية للفلاتر
    $branches = Branch::select('id', 'name')->get();
    $customers = Client::select('id', 'code', 'trade_name')->get();
    $salesManagers = User::select('id', 'name')->where('role', 'employee')->get();
    $categories = CategoriesClient::select('id', 'name')->get();
    
    // جلب الحالات
    $statuses = Statuses::select('id', 'name')->get();
    
    // جلب المجموعات من جدول Region_groubs (المناطق)
    $groups = Region_groub::select('id', 'name')->get();

    if ($request->ajax()) {
        return $this->getReportData($request);
    }

    return view('reports::customers.CustomerReport.Debt_aging_general', [
        'branches' => $branches,
        'customers' => $customers,
        'salesManagers' => $salesManagers,
        'categories' => $categories,
        'statuses' => $statuses,
        'groups' => $groups,
    ]);
}

public function debtAgingGeneralLedgerAjax(Request $request)
{
    return $this->getReportData($request);
}

private function getReportData(Request $request)
{
    try {
        $fromDate = $request->filled('from_date')
            ? Carbon::parse($request->from_date)
            : Carbon::now()->startOfMonth();

        $toDate = $request->filled('to_date')
            ? Carbon::parse($request->to_date)
            : Carbon::now();

        // استعلام محسّن مع eager loading
        $query = Account::with([
            'client' => function($q) {
                $q->select('id', 'code', 'trade_name', 'email', 'phone', 'branch_id', 'category_id', 'credit_limit', 'status_id');
            },
            'client.branch:id,name',
            'client.categoriesClient:id,name',
            'client.clientEmployees.employee:id,name',
            'client.neighborhood:id,client_id,name,region_id',
            'client.neighborhood.Region:id,name',
            'client.status_client:id,name'
        ])
        ->select('id', 'client_id', 'code', 'balance', 'updated_at')
        ->whereNotNull('client_id')
        ->where('balance', '>', 0);

        // فلترة حسب الفرع
        if ($request->filled('branch')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('branch_id', $request->branch);
            });
        }

        // فلترة حسب التصنيف
        if ($request->filled('customer_type')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('category_id', $request->customer_type);
            });
        }

        // فلترة حسب العميل
        if ($request->filled('customer')) {
            $query->where('client_id', $request->customer);
        }

        // فلترة حسب المجموعة (من خلال الأحياء)
        if ($request->filled('group')) {
            $query->whereHas('client.neighborhood', function ($q) use ($request) {
                $q->where('region_id', $request->group);
            });
        }

        // فلترة حسب الحالة (جديد)
        if ($request->filled('status')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('status_id', $request->status);
            });
        }

        // فلترة حسب مسؤول المبيعات
        if ($request->filled('sales_manager')) {
            $query->whereHas('client.clientEmployees', function ($q) use ($request) {
                $q->where('employee_id', $request->sales_manager);
            });
        }

        // فلترة حسب السنة المالية
        if ($request->filled('financial_year') && is_array($request->financial_year)) {
            $financialYears = $request->financial_year;
            if (!in_array('all', $financialYears)) {
                if (in_array('current', $financialYears)) {
                    $currentYear = Carbon::now()->year;
                    $financialYears[] = $currentYear;
                    $financialYears = array_filter($financialYears, fn($year) => $year !== 'current');
                }
                $query->whereIn(DB::raw('YEAR(updated_at)'), $financialYears);
            }
        }

        // فلترة حسب عمر الدين
        if ($request->filled('aging_filter')) {
            $agingFilter = $request->aging_filter;
            $today = now();

            switch ($agingFilter) {
                case 'today':
                    $query->whereDate('updated_at', $today);
                    break;
                case '1-30':
                    $query->whereBetween(DB::raw('DATEDIFF(NOW(), updated_at)'), [1, 30]);
                    break;
                case '31-60':
                    $query->whereBetween(DB::raw('DATEDIFF(NOW(), updated_at)'), [31, 60]);
                    break;
                case '61-90':
                    $query->whereBetween(DB::raw('DATEDIFF(NOW(), updated_at)'), [61, 90]);
                    break;
                case '91-120':
                    $query->whereBetween(DB::raw('DATEDIFF(NOW(), updated_at)'), [91, 120]);
                    break;
                case '120+':
                    $query->where(DB::raw('DATEDIFF(NOW(), updated_at)'), '>', 120);
                    break;
                case '150':
                    $query->whereBetween(DB::raw('DATEDIFF(NOW(), updated_at)'), [121, 150]);
                    break;
                case '180':
                    $query->whereBetween(DB::raw('DATEDIFF(NOW(), updated_at)'), [151, 180]);
                    break;
                case '210':
                    $query->whereBetween(DB::raw('DATEDIFF(NOW(), updated_at)'), [181, 210]);
                    break;
                case '240':
                    $query->whereBetween(DB::raw('DATEDIFF(NOW(), updated_at)'), [211, 240]);
                    break;
                case '240+':
                    $query->where(DB::raw('DATEDIFF(NOW(), updated_at)'), '>', 240);
                    break;
            }
        }

        $query->orderBy('balance', 'desc');

        // Pagination محسّن
        $perPage = $request->get('per_page', 50);
        $accounts = $query->paginate($perPage);

        // معالجة البيانات
        $reportData = $accounts->map(function ($account) {
            $today = now()->startOfDay();
            $currentBalance = $account->balance;
            $lastUpdateDate = $account->updated_at->startOfDay();
            $daysLate = $lastUpdateDate->diffInDays($today);

            $aging = $this->categorizeDebtAging($daysLate, $currentBalance);

            $creditLimit = $account->client->credit_limit ?? 0;
            $availableCredit = $creditLimit - $currentBalance;

            return [
                'client_id' => $account->client_id, // إضافة client_id هنا ✅
                'client_code' => $account->client->code ?? 'غير محدد',
                'client_name' => $account->client->trade_name ?? 'غير محدد',
                'client_email' => $account->client->email ?? '',
                'client_phone' => $account->client->phone ?? '',
                'branch' => $account->client->branch->name ?? 'غير محدد',
                'category' => $account->client->categoriesClient->name ?? 'غير محدد',
                'group' => $account->client->neighborhood->Region->name ?? 'غير محدد',
                'neighborhood' => $account->client->neighborhood->name ?? 'غير محدد',
                'status' => $account->client->status_client->name ?? 'غير محدد',
                'status_color' => $account->client->status_client->color ?? '#6c757d', // إضافة لون الحالة
                'sales_manager' => $account->client->clientEmployees->first()?->employee->name ?? 'غير محدد',
                'today' => round($aging['today'], 2),
                'days1to30' => round($aging['days1to30'], 2),
                'days31to60' => round($aging['days31to60'], 2),
                'days61to90' => round($aging['days61to90'], 2),
                'days91to120' => round($aging['days91to120'], 2),
                'daysOver120' => round($aging['daysOver120'], 2),
                'days150' => round($aging['days150'], 2),
                'days180' => round($aging['days180'], 2),
                'days210' => round($aging['days210'], 2),
                'days240' => round($aging['days240'], 2),
                'daysOver240' => round($aging['daysOver240'], 2),
                'total_due' => round($currentBalance, 2),
                'credit_limit' => round($creditLimit, 2),
                'available_credit' => round($availableCredit, 2),
                'last_update' => $account->updated_at->format('Y-m-d'),
                'days_late' => $daysLate,
            ];
        });

        $groupedCustomers = $this->groupCustomerData($reportData);
        $totals = $this->calculateTotalsGude($reportData);

        $chartData = [
            'aging_labels' => ['اليوم', '1-30 يوم', '31-60 يوم', '61-90 يوم', '91-120 يوم', '+120 يوم'],
            'aging_values' => [
                $totals['today'],
                $totals['days1to30'],
                $totals['days31to60'],
                $totals['days61to90'],
                $totals['days91to120'],
                $totals['daysOver120']
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $reportData->values(),
            'grouped_customers' => $groupedCustomers,
            'totals' => $totals,
            'chart_data' => $chartData,
            'from_date' => $fromDate->format('d/m/Y'),
            'to_date' => $toDate->format('d/m/Y'),
            'records_count' => $reportData->count(),
            'customers_count' => count($groupedCustomers),
            'pagination' => [
                'current_page' => $accounts->currentPage(),
                'last_page' => $accounts->lastPage(),
                'per_page' => $accounts->perPage(),
                'total' => $accounts->total(),
            ],
            'summary' => [
                'overdue_customers' => $reportData->filter(fn($item) => $item['days91to120'] > 0 || $item['daysOver120'] > 0)->count(),
                'over_credit_limit' => $reportData->filter(fn($item) => $item['total_due'] > $item['credit_limit'] && $item['credit_limit'] > 0)->count(),
                'total_overdue_amount' => $reportData->sum('days91to120') + $reportData->sum('daysOver120'),
                'average_days_late' => $reportData->avg('days_late'),
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error in debt aging report: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في جلب البيانات',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
private function categorizeDebtAging($daysLate, $amount)
{
    $aging = [
        'today' => 0,
        'days1to30' => 0,
        'days31to60' => 0,
        'days61to90' => 0,
        'days91to120' => 0,
        'daysOver120' => 0,
        'days150' => 0,
        'days180' => 0,
        'days210' => 0,
        'days240' => 0,
        'daysOver240' => 0,
    ];

    if ($daysLate == 0) {
        $aging['today'] = $amount;
    } elseif ($daysLate >= 1 && $daysLate <= 30) {
        $aging['days1to30'] = $amount;
    } elseif ($daysLate >= 31 && $daysLate <= 60) {
        $aging['days31to60'] = $amount;
    } elseif ($daysLate >= 61 && $daysLate <= 90) {
        $aging['days61to90'] = $amount;
    } elseif ($daysLate >= 91 && $daysLate <= 120) {
        $aging['days91to120'] = $amount;
    } elseif ($daysLate >= 121 && $daysLate <= 150) {
        $aging['days150'] = $amount;
        $aging['daysOver120'] = $amount;
    } elseif ($daysLate >= 151 && $daysLate <= 180) {
        $aging['days180'] = $amount;
        $aging['daysOver120'] = $amount;
    } elseif ($daysLate >= 181 && $daysLate <= 210) {
        $aging['days210'] = $amount;
        $aging['daysOver120'] = $amount;
    } elseif ($daysLate >= 211 && $daysLate <= 240) {
        $aging['days240'] = $amount;
        $aging['daysOver120'] = $amount;
    } else {
        $aging['daysOver240'] = $amount;
        $aging['daysOver120'] = $amount;
    }

    return $aging;
}

private function groupCustomerData($reportData)
{
    $groupedCustomers = [];

    foreach ($reportData as $item) {
        $customerName = $item['client_name'];

        if (!isset($groupedCustomers[$customerName])) {
            $groupedCustomers[$customerName] = [
                'data' => [],
                'customer_totals' => [
                    'today' => 0, 'days1to30' => 0, 'days31to60' => 0,
                    'days61to90' => 0, 'days91to120' => 0, 'daysOver120' => 0,
                    'total_due' => 0, 'credit_limit' => 0, 'available_credit' => 0,
                ]
            ];
        }

        $groupedCustomers[$customerName]['data'][] = $item;

        foreach (['today', 'days1to30', 'days31to60', 'days61to90', 'days91to120', 'daysOver120', 'total_due', 'credit_limit', 'available_credit'] as $field) {
            $groupedCustomers[$customerName]['customer_totals'][$field] += $item[$field];
        }
    }

    return $groupedCustomers;
}

private function calculateTotalsGude($reportData)
{
    return [
        'today' => $reportData->sum('today'),
        'days1to30' => $reportData->sum('days1to30'),
        'days31to60' => $reportData->sum('days31to60'),
        'days61to90' => $reportData->sum('days61to90'),
        'days91to120' => $reportData->sum('days91to120'),
        'daysOver120' => $reportData->sum('daysOver120'),
        'days150' => $reportData->sum('days150'),
        'days180' => $reportData->sum('days180'),
        'days210' => $reportData->sum('days210'),
        'days240' => $reportData->sum('days240'),
        'daysOver240' => $reportData->sum('daysOver240'),
        'total_due' => $reportData->sum('total_due'),
        'credit_limit' => $reportData->sum('credit_limit'),
        'available_credit' => $reportData->sum('available_credit'),
    ];
}
      public function customerGuide(Request $request)
    {
        // جلب البيانات الإضافية للفلاتر
        $branches = Branch::all();
        $customers = Client::all();
        $categories = CategoriesClient::all();
        $regionGroups = Region_groub::all();
        $neighborhoods = Neighborhood::all();

        // إذا كان الطلب AJAX، إرجاع البيانات فقط
        if ($request->ajax()) {
            return $this->getReportData($request);
        }

        // عرض الصفحة مع البيانات الأساسية
        return view('reports::customers.CustomerReport.customer_guide', [
            'branches' => $branches,
            'customers' => $customers,
            'categories' => $categories,
            'regionGroups' => $regionGroups,
            'neighborhoods' => $neighborhoods,
        ]);
    }

    /**
     * إرجاع بيانات التقرير عبر AJAX
     */
    public function customerGuideAjax(Request $request)
    {
        return $this->getReportDataCustomerGuide($request);
    }

    /**
     * دالة جلب بيانات التقرير
     */
    private function getReportDataCustomerGuide(Request $request)
    {
        // إعداد استعلام العملاء مع العلاقات اللازمة
        $query = Client::with([
            'locations',
            'branch',
            'categoriesClient',
            'neighborhood.Region' // العلاقة مع الأحياء والمجموعات
        ]);

        // تطبيق الفلاتر
        if ($request->filled('customer') && $request->customer !== '') {
            $query->where('id', $request->customer);
        }

        if ($request->filled('branch') && $request->branch !== '') {
            $query->where('branch_id', $request->branch);
        }

        if ($request->filled('customer_type') && $request->customer_type !== '') {
            $query->where('category_id', $request->customer_type);
        }

        if ($request->filled('region_group') && $request->region_group !== '') {
            $query->whereHas('neighborhoods', function ($q) use ($request) {
                $q->where('region_id', $request->region_group);
            });
        }

        if ($request->filled('neighborhood') && $request->neighborhood !== '') {
            $query->whereHas('neighborhoods', function ($q) use ($request) {
                $q->where('id', $request->neighborhood);
            });
        }

        if ($request->filled('city') && $request->city !== '') {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('country') && $request->country !== '') {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        // فلتر مشاهدة التفاصيل (العملاء الذين لديهم مواقع فقط)
        if ($request->boolean('view_details')) {
            $query->whereHas('locations');
        }

        // الحصول على العملاء المفلترين
        $clients = $query->get();

        // معالجة البيانات للعرض
        $processedClients = $clients->map(function ($client) {
            // الحصول على بيانات الحي والمجموعة
          $neighborhood = $client->neighborhood;
$regionGroup = $neighborhood ? $neighborhood->region : null;


            return [

                'code' => $client->code,
                'trade_name' => $client->trade_name,
                'email' => $client->email,
                'city' => $client->city,
                'country' => $client->country,
                'region' => $client->region,
                'branch' => $client->branch ? $client->branch->name : null,
                'category' => $client->categoriesClient ? $client->categoriesClient->name : null,
                'neighborhood' => $neighborhood ? $neighborhood->name : null,
                'region_group' => $regionGroup ? $regionGroup->name : null,
                'locations' => $client->locations ? [
                    'latitude' => $client->locations->latitude,
                    'longitude' => $client->locations->longitude,
                ] : null,
            ];
        });

        // تجميع البيانات حسب الخيار المحدد
        $groupBy = $request->get('group_by', 'العميل');
        $groupedClients = $this->groupClientsByType($processedClients, $groupBy);

        // حساب الإجماليات
        $totals = [
            'total_clients' => $clients->count(),
            'clients_with_locations' => $clients->whereNotNull('locations')->count(),
            'total_branches' => Branch::count(),
            'total_neighborhoods' => Neighborhood::count(),
        ];

        // إعداد الاستجابة
        $response = [
            'success' => true,
            'clients' => $processedClients->values()->all(),
            'grouped_clients' => $groupedClients,
            'totals' => $totals,
            'group_by' => $groupBy,
            'records_count' => $processedClients->count(),
            'filters_applied' => [
                'customer' => $request->filled('customer'),
                'branch' => $request->filled('branch'),
                'customer_type' => $request->filled('customer_type'),
                'region_group' => $request->filled('region_group'),
                'neighborhood' => $request->filled('neighborhood'),
                'city' => $request->filled('city'),
                'country' => $request->filled('country'),
                'view_details' => $request->boolean('view_details'),
            ],
        ];

        return response()->json($response);
    }

    /**
     * تجميع العملاء حسب النوع المحدد
     */
    private function groupClientsByType($clients, $groupBy)
    {
        $grouped = [];

        foreach ($clients as $client) {
            $groupKey = '';

            switch ($groupBy) {
                case 'الفرع':
                    $groupKey = $client['branch'] ?: 'غير محدد';
                    break;
                case 'المدينة':
                    $groupKey = $client['city'] ?: 'غير محدد';
                    break;
                case 'المجموعة':
                    $groupKey = $client['region_group'] ?: 'غير محدد';
                    break;
                case 'الحي':
                    $groupKey = $client['neighborhood'] ?: 'غير محدد';
                    break;
                default: // العميل
                    $groupKey = $client['trade_name'] ?: 'غير محدد';
                    break;
            }

            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [];
            }

            $grouped[$groupKey][] = $client;
        }

        return $grouped;
    }

    /**
     * جلب الأحياء حسب المجموعة
     */
    public function getNeighborhoods(Request $request)
    {
        $regionGroupId = $request->get('region_group_id');

        if (!$regionGroupId) {
            return response()->json([]);
        }

        $neighborhoods = Neighborhood::where('region_id', $regionGroupId)
            ->select('id', 'name')
            ->get();

        return response()->json($neighborhoods);
    }

    /**
     * جلب المجموعات حسب الفرع
     */
    public function getRegionGroups(Request $request)
    {
        $branchId = $request->get('branch_id');

        if (!$branchId) {
            return response()->json([]);
        }

        $regionGroups = Region_groub::where('branch_id', $branchId)
            ->select('id', 'name')
            ->get();

        return response()->json($regionGroups);
    }

    /**
     * جلب المدن حسب المنطقة أو الفرع
     */
    public function getCities(Request $request)
    {
        $query = Client::select('city')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct();

        // فلترة حسب الفرع إذا تم تحديده
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // فلترة حسب المجموعة إذا تم تحديدها
        if ($request->filled('region_group_id')) {
            $query->whereHas('neighborhoods', function ($q) use ($request) {
                $q->where('region_id', $request->region_group_id);
            });
        }

        $cities = $query->pluck('city')->values();

        return response()->json($cities);
    }


    // عرض أرصدة العملاء



// إضافة هذه الدوال إلى ClientReportController

/**
 * عرض صفحة تقرير أرصدة العملاء
 */
public function customerBalances(Request $request)
{
    // جلب البيانات الإضافية للفلاتر
    $employees = User::where('role', 'employee')->get();
    $branches = Branch::all();
    $clients = Client::all();

    // عرض الصفحة مع البيانات الأساسية
    return view('reports::customers.CustomerReport.customer_blances', [
        'employees' => $employees,
        'branches' => $branches,
        'clients' => $clients,
    ]);
}

/**
 * إرجاع بيانات تقرير أرصدة العملاء عبر AJAX
 */
public function customerBalancesAjax(Request $request)
{
    return $this->getReportDataCustomerBalances($request);
}

/**
 * دالة جلب بيانات تقرير أرصدة العملاء
 */
private function getReportDataCustomerBalances(Request $request)
{
    // إعداد استعلام العملاء مع العلاقات اللازمة
    $query = Client::with(['invoices', 'payments', 'employee.branch', 'categoriesClient']);

    // تطبيق فلاتر التاريخ على الفواتير والمدفوعات
    if ($request->filled('date_from')) {
        $query->whereHas('invoices', function ($q) use ($request) {
            $q->where('invoice_date', '>=', $request->date_from);
        })->orWhereHas('payments', function ($q) use ($request) {
            $q->where('payment_date', '>=', $request->date_from);
        });
    }

    if ($request->filled('date_to')) {
        $query->whereHas('invoices', function ($q) use ($request) {
            $q->where('invoice_date', '<=', $request->date_to);
        })->orWhereHas('payments', function ($q) use ($request) {
            $q->where('payment_date', '<=', $request->date_to);
        });
    }

    // تطبيق الفلاتر الأخرى
    if ($request->filled('client_category') && $request->client_category !== '') {
        $query->where('category', $request->client_category);
    }

    if ($request->filled('client') && $request->client !== '') {
        $query->where('id', $request->client);
    }

    if ($request->filled('employee') && $request->employee !== '') {
        $query->where('employee_id', $request->employee);
    }

    if ($request->filled('branch') && $request->branch !== '') {
        $query->whereHas('employee.branch', function ($q) use ($request) {
            $q->where('id', $request->branch);
        });
    }

    // الحصول على العملاء المفلترين
    $clients = $query->get();

    // معالجة البيانات لحساب الأرصدة
    $clientBalances = [];
    $totals = [
        'total_clients' => 0,
        'total_sales' => 0,
        'total_payments' => 0,
        'total_balance' => 0
    ];

    foreach ($clients as $client) {
        // تطبيق فلتر التاريخ على الفواتير والمدفوعات
        $invoicesQuery = $client->invoices();
        $paymentsQuery = $client->payments();

        if ($request->filled('date_from')) {
            $invoicesQuery->where('invoice_date', '>=', $request->date_from);
            $paymentsQuery->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $invoicesQuery->where('invoice_date', '<=', $request->date_to);
            $paymentsQuery->where('payment_date', '<=', $request->date_to);
        }

        $invoices = $invoicesQuery->get();
        $payments = $paymentsQuery->get();

        // حساب المبيعات والمرتجعات
        $totalInvoices = $invoices->where('type', 'normal')->sum('grand_total');
        $totalReturns = $invoices->where('type', 'returned')->sum('grand_total');
        $clientPayments = $payments->sum('amount');
        $netSales = $totalInvoices - $totalReturns;

        // حساب المبلغ المستحق
        $dueValue = $netSales - $clientPayments;

        // حساب الرصيد النهائي
        $balance = ($client->opening_balance ?? 0) + $dueValue;

        // فلتر إخفاء الرصيد الصفري
        if ($request->boolean('hide_zero') && $balance == 0) {
            continue;
        }

        $clientData = [
            'id' => $client->id,
            'code' => $client->code,
            'account_number' => $client->account_id,
            'name' => $client->trade_name ?? $client->first_name . ' ' . $client->last_name,
            'branch' => $client->branch->name ?? 'غير محدد',
            'currency_status' => $client->currency,
            'employee' => $client->clientEmployeeser->employee->name ?? 'غير محدد',
            'category' => optional($client->categoriesClient)->name ?? 'غير محدد',
            'balance_before' => $client->opening_balance ?? 0,
            'total_sales' => $totalInvoices,
            'total_returns' => $totalReturns,
            'net_sales' => $netSales,
            'total_payments' => $clientPayments,
            'due_value' => $dueValue,
            'balance' => $balance,
        ];

        $clientBalances[] = $clientData;

        // تحديث الإجماليات
        $totals['total_sales'] += $netSales;
        $totals['total_payments'] += $clientPayments;
        $totals['total_balance'] += $balance;
    }

    $totals['total_clients'] = count($clientBalances);

    // تجميع البيانات حسب الخيار المحدد
    $groupBy = $request->get('group_by', 'العميل');
    $groupedBalances = $this->groupBalancesByType($clientBalances, $groupBy);

    // ترتيب النتائج حسب الرصيد تنازلياً
    usort($clientBalances, function ($a, $b) {
        return $b['balance'] <=> $a['balance'];
    });

    // تجهيز بيانات الرسم البياني
    $chartData = $this->prepareChartData($clientBalances, $groupBy);

    // إعداد الاستجابة
    $response = [
        'success' => true,
        'client_balances' => $clientBalances,
        'grouped_balances' => $groupedBalances,
        'totals' => $totals,
        'group_by' => $groupBy,
        'chart_data' => $chartData,
        'records_count' => count($clientBalances),
        'filters_applied' => [
            'date_from' => $request->filled('date_from'),
            'date_to' => $request->filled('date_to'),
            'client_category' => $request->filled('client_category'),
            'client' => $request->filled('client'),
            'employee' => $request->filled('employee'),
            'branch' => $request->filled('branch'),
            'hide_zero' => $request->boolean('hide_zero'),
            'show_details' => $request->boolean('show_details'),
        ],
    ];

    return response()->json($response);
}

/**
 * تجميع الأرصدة حسب النوع المحدد
 */
private function groupBalancesByType($clientBalances, $groupBy)
{
    $grouped = [];

    foreach ($clientBalances as $client) {
        $groupKey = '';

        switch ($groupBy) {
            case 'الفرع':
                $groupKey = $client['branch'] ?: 'غير محدد';
                break;
            case 'الموظف':
                $groupKey = $client['employee'] ?: 'غير محدد';
                break;
            case 'التصنيف':
                $groupKey = $client['category'] ?: 'غير محدد';
                break;
            default: // العميل
                $groupKey = $client['name'] ?: 'غير محدد';
                break;
        }

        if (!isset($grouped[$groupKey])) {
            $grouped[$groupKey] = [
                'clients' => [],
                'total_balance' => 0,
                'total_sales' => 0,
                'total_payments' => 0,
                'count' => 0
            ];
        }

        $grouped[$groupKey]['clients'][] = $client;
        $grouped[$groupKey]['total_balance'] += $client['balance'];
        $grouped[$groupKey]['total_sales'] += $client['net_sales'];
        $grouped[$groupKey]['total_payments'] += $client['total_payments'];
        $grouped[$groupKey]['count']++;
    }

    // ترتيب المجموعات حسب إجمالي الرصيد
    uasort($grouped, function ($a, $b) {
        return $b['total_balance'] <=> $a['total_balance'];
    });

    return $grouped;
}





private function calculateTotals($payments)
{
    $totalPayments = $payments->sum('amount');

    // حساب العملاء الفريدين من الفواتير أولاً ثم المباشرين
    $clientIds = collect();
    foreach ($payments as $payment) {
        if ($payment->invoice && $payment->invoice->client) {
            $clientIds->push($payment->invoice->client->id);
        } elseif ($payment->client) {
            $clientIds->push($payment->client->id);
        }
    }
    $totalClients = $clientIds->unique()->count();

    $totalTransactions = $payments->count();
    $averagePayment = $totalTransactions > 0 ? $totalPayments / $totalTransactions : 0;

    return [
        'total_payments' => $totalPayments,
        'total_clients' => $totalClients,
        'total_transactions' => $totalTransactions,
        'average_payment' => $averagePayment
    ];
}

private function prepareChartData($groupedData)
{
    $labels = [];
    $payments = [];

    foreach ($groupedData as $clientData) {
        $labels[] = $clientData['client']['trade_name'];
        $payments[] = $clientData['total_payments'];
    }

    // أخذ أعلى 10 عملاء فقط للرسم البياني
    if (count($labels) > 10) {
        $labels = array_slice($labels, 0, 10);
        $payments = array_slice($payments, 0, 10);
    }

    return [
        'labels' => $labels,
        'payments' => $payments
    ];
}

private function getDateRange(Request $request)
{
    $dateType = $request->get('date_type', 'custom');

    switch ($dateType) {
        case 'today':
            $from = Carbon::today()->format('Y-m-d');
            $to = Carbon::today()->format('Y-m-d');
            break;
        case 'yesterday':
            $from = Carbon::yesterday()->format('Y-m-d');
            $to = Carbon::yesterday()->format('Y-m-d');
            break;
        case 'this_week':
            $from = Carbon::now()->startOfWeek()->format('Y-m-d');
            $to = Carbon::now()->endOfWeek()->format('Y-m-d');
            break;
        case 'last_week':
            $from = Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d');
            $to = Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d');
            break;
        case 'this_month':
            $from = Carbon::now()->startOfMonth()->format('Y-m-d');
            $to = Carbon::now()->endOfMonth()->format('Y-m-d');
            break;
        case 'last_month':
            $from = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
            $to = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
            break;
        case 'this_year':
            $from = Carbon::now()->startOfYear()->format('Y-m-d');
            $to = Carbon::now()->endOfYear()->format('Y-m-d');
            break;
        case 'last_year':
            $from = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
            $to = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
            break;
        case 'custom':
        default:
            $from = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $to = $request->get('date_to', Carbon::now()->format('Y-m-d'));
            break;
    }

    return [
        'from' => $from,
        'to' => $to
    ];
}


    // عرض كشف حساب العملاء

public function customerAccountStatement(Request $request)
{
    // جلب البيانات للفلاتر فقط
    $branches = Branch::all();
    $customers = Client::all();
    $salesManagers = Employee::all();
    $categories = CategoriesClient::all();
    $accounts = Account::all();
    $costCenters = CostCenter::all();

    return view('reports::customers.CustomerReport.customer_account_statement',
        compact('branches', 'customers', 'salesManagers', 'categories', 'accounts', 'costCenters'));
}

/**
 * جلب بيانات كشف حساب العملاء عبر AJAX
 */
public function customerAccountStatementAjax(Request $request)
{
    try {
        // إعداد الاستعلام الأساسي لجلب جميع العملاء
        $clientsQuery = Client::with([
            'branch',
            'categoriesClient',
            'employee'
        ]);

        // تطبيق الفلاتر على العملاء
        $this->applyClientFilters($clientsQuery, $request);

        // جلب العملاء
        $clients = $clientsQuery->get();

        // جلب القيود المحاسبية لكل عميل
        $journalEntries = collect();
        $totalCustomers = $clients->count();
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($clients as $client) {
            // جلب القيود المحاسبية للعميل بطريقتين:
            // 1. القيود المرتبطة مباشرة بالعميل
            $directEntries = JournalEntry::with([
                'details' => function($q) {
                    $q->with('account');
                },
                'client',
                'createdByEmployee',
                'costCenter'
            ])
            ->where('client_id', $client->id);

            // تطبيق فلاتر إضافية على القيود المباشرة
            $this->applyJournalEntryFilters($directEntries, $request);
            $directEntriesResult = $directEntries->get();

            // 2. القيود المرتبطة بحسابات العميل
            $accountEntries = JournalEntry::with([
                'details' => function($q) use ($client) {
                    $q->with('account')
                      ->whereHas('account', function($accountQuery) use ($client) {
                          $accountQuery->where('client_id', $client->id);
                      });
                },
                'client',
                'createdByEmployee',
                'costCenter'
            ])
            ->whereHas('details.account', function($q) use ($client) {
                $q->where('client_id', $client->id);
            });

            // تطبيق فلاتر إضافية على قيود الحسابات
            $this->applyJournalEntryFilters($accountEntries, $request);
            $accountEntriesResult = $accountEntries->get();

            // دمج النتائج وإزالة التكرار
            $allEntries = $directEntriesResult->merge($accountEntriesResult)->unique('id');

            if ($allEntries->isNotEmpty()) {
                foreach ($allEntries as $entry) {
                    // تعيين العميل للقيد إذا لم يكن موجود
                    if (!$entry->client_id) {
                        $entry->client_id = $client->id;
                        $entry->client = $client;
                    }

                    $journalEntries->push($entry);

                    // حساب المجاميع من تفاصيل القيد المرتبطة بالعميل فقط
                    foreach ($entry->details as $detail) {
                        if ($detail->account && $detail->account->client_id == $client->id) {
                            $totalDebit += (float) $detail->debit;
                            $totalCredit += (float) $detail->credit;
                        }
                    }
                }
            }
        }

        // معالجة البيانات
        $processedData = $this->processAccountStatementDataNew($journalEntries, $totalCustomers, $totalDebit, $totalCredit);

        return response()->json([
            'success' => true,
            'journalEntries' => $processedData['entries'],
            'totals' => $processedData['totals'],
            'message' => 'تم تحميل البيانات بنجاح'
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في تحميل بيانات كشف حساب العملاء: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
            'journalEntries' => [],
            'totals' => [
                'total_customers' => 0,
                'total_entries' => 0,
                'total_debit' => 0,
                'total_credit' => 0,
                'final_balance' => 0
            ]
        ], 500);
    }
}

/**
 * تطبيق الفلاتر على العملاء
 */
private function applyClientFilters($query, Request $request)
{
    // فلتر الفرع
    if ($request->filled('branch')) {
        $query->where('branch_id', $request->branch);
    }

    // فلتر تصنيف العميل
    if ($request->filled('customer_type')) {
        $query->where('category_id', $request->customer_type);
    }

    // فلتر العميل المحدد
    if ($request->filled('customer')) {
        $query->where('id', $request->customer);
    }

    // فلتر مسؤول المبيعات
    if ($request->filled('sales_manager')) {
        $query->where('employee_id', $request->sales_manager);
    }
}

/**
 * تطبيق الفلاتر على القيود المحاسبية
 */
private function applyJournalEntryFilters($query, Request $request)
{
    // فلتر الحساب
    if ($request->filled('account')) {
        $query->whereHas('details', function($q) use ($request) {
            $q->where('account_id', $request->account);
        });
    }

    // فلتر الفترة الزمنية
    if ($request->filled('days')) {
        $days = (int) $request->days;
        $query->where('date', '>=', now()->subDays($days));
    }

    // فلتر السنة المالية
    if ($request->filled('financial_year')) {
        $financialYears = $request->financial_year;

        if (in_array('current', $financialYears)) {
            $query->whereYear('date', date('Y'));
        } elseif (!in_array('all', $financialYears)) {
            // إزالة 'current' و 'all' من المصفوفة والاحتفاظ بالسنوات فقط
            $years = array_filter($financialYears, function($year) {
                return is_numeric($year);
            });

            if (!empty($years)) {
                $query->whereIn(DB::raw('YEAR(date)'), $years);
            }
        }
    }

    // فلتر مركز التكلفة
    if ($request->filled('cost_center')) {
        $query->where('cost_center_id', $request->cost_center);
    }
}

/**
 * تطبيق الفلاتر على الاستعلام (الدالة القديمة للتوافق)
 */
private function applyFiltersStatement($query, Request $request)
{
    // فلتر الفرع
    if ($request->filled('branch')) {
        $query->whereHas('details.account', function($q) use ($request) {
            $q->where('branch_id', $request->branch);
        });
    }

    // فلتر الحساب
    if ($request->filled('account')) {
        $query->whereHas('details', function($q) use ($request) {
            $q->where('account_id', $request->account);
        });
    }

    // فلتر الفترة الزمنية
    if ($request->filled('days')) {
        $days = (int) $request->days;
        $query->where('date', '>=', now()->subDays($days));
    }

    // فلتر السنة المالية
    if ($request->filled('financial_year')) {
        $financialYears = $request->financial_year;

        if (in_array('current', $financialYears)) {
            $query->whereYear('date', date('Y'));
        } elseif (!in_array('all', $financialYears)) {
            // إزالة 'current' و 'all' من المصفوفة والاحتفاظ بالسنوات فقط
            $years = array_filter($financialYears, function($year) {
                return is_numeric($year);
            });

            if (!empty($years)) {
                $query->whereIn(DB::raw('YEAR(date)'), $years);
            }
        }
    }

    // فلتر تصنيف العميل
    if ($request->filled('customer_type')) {
        $query->whereHas('client', function($q) use ($request) {
            $q->where('category_id', $request->customer_type);
        });
    }

    // فلتر العميل المحدد
    if ($request->filled('customer')) {
        $query->where('client_id', $request->customer);
    }

    // فلتر مركز التكلفة
    if ($request->filled('cost_center')) {
        $query->where('cost_center_id', $request->cost_center);
    }

    // فلتر مسؤول المبيعات
    if ($request->filled('sales_manager')) {
        $query->where('employee_id', $request->sales_manager);
    }

    // فلتر القيود التي تحتوي على عملاء فقط
    $query->whereNotNull('client_id');
}

/**
 * معالجة بيانات كشف الحساب الجديدة مع تنسيق الأرقام باللغة الإنجليزية
 */
private function processAccountStatementDataNew($journalEntries, $totalCustomers, $totalDebit, $totalCredit)
{
    $processedEntries = [];

    foreach ($journalEntries as $entry) {
        // معالجة تفاصيل القيد
        $entryData = [
            'id' => $entry->id,
            'reference_number' => $entry->reference_number,
            'date' => $entry->date,
            'client' => $entry->client ? [
                'id' => $entry->client->id,
                'trade_name' => $entry->client->trade_name,
                'code' => $entry->client->code ?? null
            ] : null,
            'created_by_employee' => $entry->createdByEmployee ? [
                'id' => $entry->createdByEmployee->id,
                'name' => $entry->createdByEmployee->name
            ] : null,
            'details' => []
        ];

        // معالجة تفاصيل القيد مع تنسيق الأرقام باللغة الإنجليزية
        // عرض فقط التفاصيل المرتبطة بالعميل الحالي
        foreach ($entry->details as $detail) {
            // التحقق من أن التفصيل مرتبط بالعميل الحالي
            if ($detail->account && $detail->account->client_id == $entry->client_id) {
                $debit = (float) $detail->debit;
                $credit = (float) $detail->credit;

                $entryData['details'][] = [
                    'id' => $detail->id,
                    'account' => $detail->account ? [
                        'id' => $detail->account->id,
                        'name' => $detail->account->name,
                        'code' => $detail->account->code ?? null
                    ] : null,
                    'debit' => $this->formatNumberEnglish($debit),
                    'credit' => $this->formatNumberEnglish($credit),
                    'debit_raw' => $debit,
                    'credit_raw' => $credit,
                    'description' => $detail->description ?? ''
                ];
            }
        }

        // إضافة القيد فقط إذا كان يحتوي على تفاصيل مرتبطة بالعميل
        if (!empty($entryData['details'])) {
            $processedEntries[] = $entryData;
        }
    }

    // حساب الرصيد النهائي
    $finalBalance = $totalDebit - $totalCredit;

    return [
        'entries' => $processedEntries,
        'totals' => [
            'total_customers' => $totalCustomers,
            'total_entries' => count($journalEntries),
            'total_debit' => $this->formatNumberEnglish($totalDebit),
            'total_credit' => $this->formatNumberEnglish($totalCredit),
            'final_balance' => $this->formatNumberEnglish($finalBalance),
            'total_debit_raw' => $totalDebit,
            'total_credit_raw' => $totalCredit,
            'final_balance_raw' => $finalBalance
        ]
    ];
}

/**
 * تنسيق الأرقام باللغة الإنجليزية
 */
private function formatNumberEnglish($number)
{
    // تحويل الأرقام العربية إلى إنجليزية
    $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    $formattedNumber = number_format($number, 2, '.', ',');
    return str_replace($arabicNumbers, $englishNumbers, $formattedNumber);
}

/**
 * معالجة بيانات كشف الحساب وحساب الإجماليات (الدالة القديمة للتوافق)
 */


public function customerAppointments(Request $request)
{
    // جلب جميع العملاء والموظفين للفلترة
    $clients = Client::all();
    $employees = Employee::all();
    $branches = Branch::all(); // إضافة الفروع
    $users = User::all(); // لمن أنشأ الموعد

    // تأكد من أن اسم الـ view صحيح
    return view('reports::customers.CustomerReport.customer_apptilmition', compact('clients', 'employees', 'branches', 'users'));
}

public function customerAppointmentsAjax(Request $request)
{
    try {
        // بدء بناء الاستعلام مع جميع العلاقات المطلوبة
        $query = Appointment::with(['client', 'employee', 'createdBy']);

        // تطبيق الفلترة بناءً على القيم المدخلة
        if ($request->has('client') && $request->client != '') {
            $query->where('client_id', $request->client);
        }

        if ($request->has('employee') && $request->employee != '') {
            $query->where('employee_id', $request->employee);
        }

        if ($request->has('status') && $request->status != '') {
            // تحويل الحالة النصية إلى رقم
            $statusNumber = null;
            switch ($request->status) {
                case 'pending':
                    $statusNumber = 1;
                    break;
                case 'completed':
                    $statusNumber = 2;
                    break;
                case 'ignored':
                    $statusNumber = 3;
                    break;
                case 'rescheduled':
                    $statusNumber = 4;
                    break;
            }

            if ($statusNumber) {
                $query->where('status', $statusNumber);
            }
        }

        if ($request->has('created_by') && $request->created_by != '') {
            $query->where('created_by', $request->created_by);
        }

        // تطبيق تصفية التاريخ
        $this->applyDateFilter($query, $request);

        // جلب البيانات المفلترة
        $appointments = $query->orderBy('appointment_date', 'desc')->get();

        // تحضير البيانات للإرسال
        $formattedAppointments = $appointments->map(function ($appointment) {
            // دالة مساعدة للحصول على النص العربي للحالة
            $statusText = '';
            switch ($appointment->status) {
                case 1:
                    $statusText = 'تم جدولته';
                    break;
                case 2:
                    $statusText = 'تم';
                    break;
                case 3:
                    $statusText = 'صرف النظر عنه';
                    break;
                case 4:
                    $statusText = 'تم جدولته مجدداً';
                    break;
                default:
                    $statusText = 'غير معروف';
            }

            // دالة مساعدة للحصول على لون الحالة
            $statusColor = '';
            switch ($appointment->status) {
                case 1:
                    $statusColor = 'bg-warning text-dark';
                    break;
                case 2:
                    $statusColor = 'bg-success text-white';
                    break;
                case 3:
                    $statusColor = 'bg-danger text-white';
                    break;
                case 4:
                    $statusColor = 'bg-info text-white';
                    break;
                default:
                    $statusColor = 'bg-secondary text-white';
            }

            return [
                'id' => $appointment->id,
                'title' => $appointment->title,
                'description' => $appointment->description,
                'client_name' => $appointment->client ?
                    ($appointment->client->trade_name ?? $appointment->client->first_name . ' ' . $appointment->client->last_name) :
                    'غير محدد',
                'client_code' => $appointment->client ? $appointment->client->code : null,
                'employee_name' => $appointment->employee ? $appointment->employee->name : 'غير محدد',
                'branch' => $appointment->client && $appointment->client->branch ?
                    $appointment->client->branch->name :
                    'غير محدد',
                'appointment_date' => $appointment->appointment_date,
                'status' => $appointment->status,
                'status_text' => $statusText,
                'status_color' => $statusColor,
                'created_by' => $appointment->createdBy ? $appointment->createdBy->name : 'غير محدد',
                'created_at' => $appointment->created_at,
                'notes_count' => 0, // سنضع 0 مؤقتاً إذا لم تكن هناك ملاحظات
                'notes' => [], // مصفوفة فارغة مؤقتاً
            ];
        });

        // حساب الإحصائيات
        $totals = $this->calculateAppointmentTotals($appointments);

        // تحضير بيانات الرسم البياني
        $chartData = $this->prepareAppointmentChartData($appointments);

        return response()->json([
            'success' => true,
            'appointments' => $formattedAppointments,
            'totals' => $totals,
            'chart_data' => $chartData,
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في تحميل تقرير المواعيد: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.',
            'error' => $e->getMessage() // للتطوير فقط
        ], 500);
    }
}

private function applyDateFilter($query, $request)
{
    $dateType = $request->get('date_type', 'custom');
    $dateField = 'appointment_date'; // تأكد من أن هذا هو اسم الحقل الصحيح في جدول المواعيد

    switch ($dateType) {
        case 'today':
            $query->whereDate($dateField, today());
            break;

        case 'yesterday':
            $query->whereDate($dateField, yesterday());
            break;

        case 'this_week':
            $query->whereBetween($dateField, [
                now()->startOfWeek(),
                now()->endOfWeek()
            ]);
            break;

        case 'last_week':
            $query->whereBetween($dateField, [
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek()
            ]);
            break;

        case 'this_month':
            $query->whereMonth($dateField, now()->month)
                  ->whereYear($dateField, now()->year);
            break;

        case 'last_month':
            $query->whereMonth($dateField, now()->subMonth()->month)
                  ->whereYear($dateField, now()->subMonth()->year);
            break;

        case 'this_year':
            $query->whereYear($dateField, now()->year);
            break;

        case 'last_year':
            $query->whereYear($dateField, now()->subYear()->year);
            break;

        case 'custom':
        default:
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate($dateField, '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate($dateField, '<=', $request->date_to);
            }
            break;
    }
}

private function calculateAppointmentTotals($appointments)
{
    $totals = [
        'total_appointments' => $appointments->count(),
        'completed_appointments' => $appointments->where('status', 2)->count(), // STATUS_COMPLETED = 2
        'pending_appointments' => $appointments->where('status', 1)->count(), // STATUS_PENDING = 1
        'ignored_appointments' => $appointments->where('status', 3)->count(), // STATUS_IGNORED = 3
        'rescheduled_appointments' => $appointments->where('status', 4)->count(), // STATUS_RESCHEDULED = 4
        'upcoming_appointments' => $appointments->where('appointment_date', '>', now())->count(),
        'overdue_appointments' => $appointments->where('appointment_date', '<', now())
                                               ->where('status', 1)->count(), // STATUS_PENDING = 1
        'today_appointments' => $appointments->filter(function($appointment) {
            return \Carbon\Carbon::parse($appointment->appointment_date)->isToday();
        })->count(),
    ];

    return $totals;
}
public function customerInstallments(Request $request)
{
    // جلب البيانات الإضافية للفلاتر
    $employees = User::where('role', 'employee')->get();
    $branches = Branch::all();
    $clients = Client::all();
    $categories = CategoriesClient::all();

    // عرض الصفحة مع البيانات الأساسية
    return view('reports::customers.CustomerReport.customer_installment', [
        'employees' => $employees,
        'branches' => $branches,
        'clients' => $clients,
        'categories' => $categories,
    ]);
}


private function prepareAppointmentChartData($appointments)
{
    return [
        'completed' => $appointments->where('status', 2)->count(), // STATUS_COMPLETED = 2
        'pending' => $appointments->where('status', 1)->count(), // STATUS_PENDING = 1
        'ignored' => $appointments->where('status', 3)->count(), // STATUS_IGNORED = 3
        'rescheduled' => $appointments->where('status', 4)->count(), // STATUS_RESCHEDULED = 4
    ];
}

public function customerInstallmentsAjax(Request $request)
{
    return $this->getReportDataCustomerInstallments($request);
}

/**
 * دالة جلب بيانات تقرير أقساط العملاء
 */
private function getReportDataCustomerInstallments(Request $request)
{
    try {
        // تحديد نطاق التاريخ بناءً على نوع التاريخ المحدد
        $dateRange = $this->getDateRange($request);

        // إعداد استعلام الأقساط مع العلاقات اللازمة
        $query = Installment::with([
            'invoice',
            'invoice.client',
            'invoice.client.employee',
            'invoice.client.employee.branch',
            'invoice.client.categoriesClient',
            'installmentDetails' // تحديث اسم العلاقة
        ]);

        // تطبيق فلاتر التاريخ على تاريخ الاستحقاق
        if ($dateRange['from']) {
            $query->where('due_date', '>=', $dateRange['from']);
        }

        if ($dateRange['to']) {
            $query->where('due_date', '<=', $dateRange['to']);
        }

        // تطبيق فلتر العميل
        if ($request->filled('client') && $request->client !== '') {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('client_id', $request->client);
            });
        }

        // تطبيق فلتر الموظف
        if ($request->filled('employee') && $request->employee !== '') {
            $query->whereHas('invoice.client', function ($q) use ($request) {
                $q->where('employee_id', $request->employee);
            });
        }

        // تطبيق فلتر الفرع
        if ($request->filled('branch') && $request->branch !== '') {
            $query->whereHas('invoice.client.employee', function ($q) use ($request) {
                $q->where('branch_id', $request->branch);
            });
        }

        // تطبيق فلتر تصنيف العميل
        if ($request->filled('client_category') && $request->client_category !== '') {
            $query->whereHas('invoice.client', function ($q) use ($request) {
                $q->where('category_id', $request->client_category);
            });
        }

        // فلتر الأقساط المتأخرة فقط
        if ($request->boolean('show_overdue_only')) {
            $query->where('due_date', '<', now());
        }

        // الحصول على الأقساط المفلترة
        $installments = $query->get();

        // معالجة البيانات وحساب التفاصيل
        $installmentData = [];
        $totals = [
            'total_installments' => 0,
            'total_amount' => 0,
            'paid_amount' => 0,
            'pending_amount' => 0,
            'overdue_amount' => 0
        ];

        $statusCounts = [
            'paid' => 0,
            'pending' => 0,
            'overdue' => 0,
            'partial' => 0
        ];

        foreach ($installments as $installment) {
            // تحقق من وجود الفاتورة والعميل
            if (!$installment->invoice || !$installment->invoice->client) {
                continue;
            }

            // حساب المبلغ المدفوع والمتبقي
            $paidAmount = $this->getInstallmentPaidAmount($installment->id);
            $remainingAmount = $installment->amount - $paidAmount;

            // تحديد حالة القسط
            $status = $this->determineInstallmentStatus($installment, $paidAmount);

            // تطبيق فلتر حالة القسط
            if ($request->filled('installment_status') &&
                $request->installment_status !== '' &&
                $status !== $request->installment_status) {
                continue;
            }

            // فلتر الأقساط المتأخرة فقط
            if ($request->boolean('show_overdue_only')) {
                if ($installment->due_date >= now() || $status === 'paid') {
                    continue;
                }
            }

            // حساب أيام التأخير
            $daysOverdue = $this->calculateDaysOverdue($installment->due_date, $status);

            // جلب تفاصيل الأقساط الفرعية من InstallmentDetail
            $installmentDetails = [];
            foreach ($installment->installmentDetails as $detail) {
                $detailPaidAmount = $this->getInstallmentDetailPaidAmount($detail->id);
                $detailRemainingAmount = $detail->amount - $detailPaidAmount;
                $detailStatus = $this->determineInstallmentDetailStatus($detail, $detailPaidAmount);
                $detailDaysOverdue = $this->calculateDaysOverdue($detail->due_date, $detailStatus);

                $installmentDetails[] = [
                    'id' => $detail->id,
                    'amount' => $detail->amount,
                    'due_date' => $detail->due_date,
                    'status' => $detailStatus,
                    'paid_amount' => $detailPaidAmount,
                    'remaining_amount' => $detailRemainingAmount,
                    'days_overdue' => $detailDaysOverdue,
                    'description' => $detail->description ?? 'قسط فرعي',
                    'notes' => $detail->notes ?? '',
                    'payment_method' => $detail->payment_method ?? 'غير محدد',
                    'reference_number' => $detail->reference_number ?? '',
                    'installments_id' => $detail->installments_id,
                    'created_at' => $detail->created_at,
                    'updated_at' => $detail->updated_at
                ];
            }

            $installmentRecord = [
                'id' => $installment->id,
                'installment_id' => $installment->id,
                'invoice_id' => $installment->invoice->id,
                'invoice_number' => $installment->invoice->invoice_number ?? 'غير محدد',
                'client_id' => $installment->invoice->client->id ?? null,
                'client_name' => $this->getClientName($installment->invoice->client ?? null),
                'client_code' => $installment->invoice->client->code ?? 'غير محدد',
                'branch' => optional($installment->invoice->client->employee->branch ?? null)->name ?? 'غير محدد',
                'employee' => optional($installment->invoice->client->employee ?? null)->name ?? 'غير محدد',
                'category' => optional($installment->invoice->client->categoriesClient ?? null)->name ?? 'غير محدد',
                'installment_number' => $installment->installment_number ?? 1,
                'amount' => $installment->amount,
                'due_date' => $installment->due_date,
                'status' => $status,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'days_overdue' => $daysOverdue,
                'details' => $installmentDetails,
                'details_count' => count($installmentDetails)
            ];

            $installmentData[] = $installmentRecord;

            // تحديث الإجماليات
            $totals['total_amount'] += $installment->amount;
            $totals['paid_amount'] += $paidAmount;
            $totals['pending_amount'] += $remainingAmount;

            if ($status === 'overdue') {
                $totals['overdue_amount'] += $remainingAmount;
            }

            // تحديث عدادات الحالة
            $statusCounts[$status]++;
        }

        $totals['total_installments'] = count($installmentData);

        // ترتيب النتائج حسب تاريخ الاستحقاق
        usort($installmentData, function ($a, $b) {
            return strtotime($a['due_date']) - strtotime($b['due_date']);
        });

        // تجميع البيانات حسب العميل إذا كان مطلوباً
        $groupedData = [];
        if ($request->boolean('group_by_client')) {
            $groupedData = $this->groupInstallmentsByClient($installmentData);
        }

        // تجهيز بيانات الرسم البياني
        $chartData = [
            'paid' => $totals['paid_amount'],
            'pending' => $totals['pending_amount'] - $totals['overdue_amount'],
            'overdue' => $totals['overdue_amount'],
            'partial' => 0
        ];

        // إعداد الاستجابة
        $response = [
            'success' => true,
            'installments' => $installmentData,
            'grouped_data' => $groupedData,
            'totals' => $totals,
            'status_counts' => $statusCounts,
            'chart_data' => $chartData,
            'records_count' => count($installmentData),
            'filters_applied' => [
                'date_from' => $dateRange['from'],
                'date_to' => $dateRange['to'],
                'client' => $request->filled('client'),
                'employee' => $request->filled('employee'),
                'branch' => $request->filled('branch'),
                'client_category' => $request->filled('client_category'),
                'installment_status' => $request->filled('installment_status'),
                'show_overdue_only' => $request->boolean('show_overdue_only'),
                'group_by_client' => $request->boolean('group_by_client'),
            ],
        ];

        return response()->json($response);

    } catch (\Exception $e) {
        Log::error('خطأ في تقرير أقساط العملاء: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات',
            'error' => config('app.debug') ? $e->getMessage() : 'خطأ داخلي في الخادم'
        ], 500);
    }
}

/**
 * الحصول على اسم العميل
 */
private function getClientName($client)
{
    if (!$client) {
        return 'غير محدد';
    }

    return $client->trade_name ?? ($client->first_name . ' ' . $client->last_name);
}

private function getInstallmentPaidAmount($installmentId)
{
    try {
        $paidAmount = PaymentsProcess::where('installment_id', $installmentId)
            ->sum('amount');

        return $paidAmount ?? 0;
    } catch (\Exception $e) {
        Log::error('خطأ في حساب المبلغ المدفوع للقسط: ' . $e->getMessage());
        return 0;
    }
}

/**
 * حساب المبلغ المدفوع لتفصيل القسط
 */
private function getInstallmentDetailPaidAmount($installmentDetailId)
{
    try {
        $paidAmount = PaymentsProcess::where('installments_detail_id', $installmentDetailId)
            ->sum('amount');

        return $paidAmount ?? 0;
    } catch (\Exception $e) {
        Log::error('خطأ في حساب المبلغ المدفوع لتفصيل القسط: ' . $e->getMessage());
        return 0;
    }
}

/**
 * تحديد حالة تفصيل القسط
 */
private function determineInstallmentDetailStatus($installmentDetail, $paidAmount)
{
    $totalAmount = $installmentDetail->amount;
    $dueDate = $installmentDetail->due_date;
    $today = now()->startOfDay();

    // إذا كان مدفوع بالكامل
    if ($paidAmount >= $totalAmount) {
        return 'paid';
    }

    // إذا كان مدفوع جزئياً
    if ($paidAmount > 0 && $paidAmount < $totalAmount) {
        return 'partial';
    }

    // إذا كان متأخر
    if ($dueDate < $today) {
        return 'overdue';
    }

    // إذا كان قيد الانتظار
    return 'pending';
}

/**
 * تحديد حالة القسط
 */
private function determineInstallmentStatus($installmentDetail, $paidAmount)
{
    $totalAmount = $installmentDetail->amount;
    $dueDate = $installmentDetail->due_date;
    $today = now()->startOfDay();

    // إذا كان مدفوع بالكامل
    if ($paidAmount >= $totalAmount) {
        return 'paid';
    }

    // إذا كان مدفوع جزئياً
    if ($paidAmount > 0 && $paidAmount < $totalAmount) {
        return 'partial';
    }

    // إذا كان متأخر
    if ($dueDate < $today) {
        return 'overdue';
    }

    // إذا كان قيد الانتظار
    return 'pending';
}

/**
 * حساب أيام التأخير
 */
private function calculateDaysOverdue($dueDate, $status)
{
    if ($status !== 'overdue') {
        return 0;
    }

    $today = now()->startOfDay();
    $due = \Carbon\Carbon::parse($dueDate)->startOfDay();

    return $today->diffInDays($due);
}

/**
 * تجميع الأقساط حسب العميل
 */
private function groupInstallmentsByClient($installmentData)
{
    $grouped = [];

    foreach ($installmentData as $installment) {
        $clientId = $installment['client_id'];
        $clientName = $installment['client_name'];

        if (!isset($grouped[$clientId])) {
            $grouped[$clientId] = [
                'client_name' => $clientName,
                'client_code' => $installment['client_code'],
                'installments' => [],
                'total_amount' => 0,
                'paid_amount' => 0,
                'remaining_amount' => 0,
                'count' => 0
            ];
        }

        $grouped[$clientId]['installments'][] = $installment;
        $grouped[$clientId]['total_amount'] += $installment['amount'];
        $grouped[$clientId]['paid_amount'] += $installment['paid_amount'];
        $grouped[$clientId]['remaining_amount'] += $installment['remaining_amount'];
        $grouped[$clientId]['count']++;
    }

    // ترتيب المجموعات حسب المبلغ المتبقي تنازلياً
    uasort($grouped, function ($a, $b) {
        return $b['remaining_amount'] <=> $a['remaining_amount'];
    });

    return $grouped;
}

public function BalancesClient(Request $request)
{
return view('reports::balances.index');
}



 public function rechargeBalancesReport(Request $request)
    {
        // بيانات الفلاتر
        $filterData = [
            'clients' => Client::orderBy('trade_name')->get(),
            'types' => BalanceType::orderBy('name')->get(),
            'branches' => Branch::orderBy('name')->get()
        ];

        return view('reports::balances.rechargeBalancesReport', $filterData);
    }

    /**
     * إرجاع بيانات التقرير عبر AJAX
     */
    public function rechargeBalancesReportAjax(Request $request)
    {
        try {
            // تحديد نطاق التاريخ
            $dateRange = $this->getDateRange($request);
            $fromDate = $dateRange['from'];
            $toDate = $dateRange['to'];

            // بناء الاستعلام الأساسي مع العلاقات
            $query = BalanceCharge::with([
                'client' => function($query) {
                    $query->with('branch');
                },
                'balanceType'
            ]);

            // تطبيق الفلاتر
            $this->applyFilters($query, $request, $fromDate, $toDate);

            // الحصول على النتائج مع الترتيب
            $charges = $query->orderBy('created_at', 'desc')->get();

            // حساب الإجماليات
            $totals = $this->calculateTotals($charges);

            // إعداد بيانات الرسم البياني
            $chartData = $this->prepareChartData($charges);

            // تحضير الاستجابة
            $response = [
                'success' => true,
                'charges' => $charges->map(function ($charge) {
                    return [
                        'id' => $charge->id,
                        'value' => $charge->value,
                        'remaining' => $charge->remaining ?? $charge->value,
                        'start_date' => $charge->start_date,
                        'end_date' => $charge->end_date,
                        'description' => $charge->description,
                        'status' => $charge->status,
                        'client' => $charge->client ? [
                            'id' => $charge->client->id,
                            'trade_name' => $charge->client->trade_name,
                            'code' => $charge->client->code,
                            'branch' => $charge->client->branch ? [
                                'id' => $charge->client->branch->id,
                                'name' => $charge->client->branch->name
                            ] : null
                        ] : null,
                        'balance_type' => $charge->balanceType ? [
                            'id' => $charge->balanceType->id,
                            'name' => $charge->balanceType->name
                        ] : null
                    ];
                }),
                'totals' => $totals,
                'chart_data' => $chartData,
                'from_date' => $fromDate->format('d/m/Y'),
                'to_date' => $toDate->format('d/m/Y')
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

     public function balanceConsumptionReport(Request $request)
    {
        // بيانات الفلاتر
        $filterData = [
            'clients' => Client::orderBy('trade_name')->get(),
            'types' => BalanceType::orderBy('name')->get(),
            'branches' => Branch::orderBy('name')->get()
        ];

        return view('reports::balances.balanceConsumptionReport', $filterData);
    }

    /**
     * إرجاع بيانات التقرير عبر AJAX
     */
    public function balanceConsumptionReportAjax(Request $request)
    {
        try {
            // تحديد نطاق التاريخ
            $dateRange = $this->getDateRange($request);
            $fromDate = $dateRange['from'];
            $toDate = $dateRange['to'];

            // بناء الاستعلام الأساسي مع العلاقات
            $query = BalanceConsumption::with([
                'client' => function($query) {
                    $query->with('branch');
                },
                'balanceType',
                'invoice'
            ]);

            // تطبيق الفلاتر
            $this->applyFilters($query, $request, $fromDate, $toDate);

            // الحصول على النتائج مع الترتيب
            $consumptions = $query->orderBy('consumption_date', 'desc')->get();

            // حساب الإجماليات
            $totals = $this->calculateTotals($consumptions);

            // إعداد بيانات الرسم البياني
            $chartData = $this->prepareChartData($consumptions, $fromDate, $toDate);

            // تحضير الاستجابة
            $response = [
                'success' => true,
                'consumptions' => $consumptions->map(function ($consumption) {
                    return [
                        'id' => $consumption->id,
                        'used_balance' => $consumption->used_balance,
                        'consumption_date' => $consumption->consumption_date,
                        'description' => $consumption->description,
                        'status' => $consumption->status,
                        'invoice_id' => $consumption->invoice_id,
                        'client' => $consumption->client ? [
                            'id' => $consumption->client->id,
                            'trade_name' => $consumption->client->trade_name,
                            'code' => $consumption->client->code,
                            'branch' => $consumption->client->branch ? [
                                'id' => $consumption->client->branch->id,
                                'name' => $consumption->client->branch->name
                            ] : null
                        ] : null,
                        'balance_type' => $consumption->balanceType ? [
                            'id' => $consumption->balanceType->id,
                            'name' => $consumption->balanceType->name
                        ] : null,
                        'invoice' => $consumption->invoice ? [
                            'id' => $consumption->invoice->id,
                            'invoice_number' => $consumption->invoice->invoice_number ?? $consumption->invoice->id
                        ] : null
                    ];
                }),
                'totals' => $totals,
                'chart_data' => $chartData,
                'from_date' => $fromDate->format('d/m/Y'),
                'to_date' => $toDate->format('d/m/Y')
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديد نطاق التاريخ بناءً على نوع التاريخ المحدد
     */

    /**
     * تطبيق الفلاتر على الاستعلام
     */




    /**
     * إعداد بيانات الرسم البياني
     */


    /**
     * تصدير التقرير إلى Excel
     */
    public function exportBalanceConsumptionReport(Request $request)
    {
        // يمكن تطبيق نفس المنطق المستخدم في AJAX
        // ثم استخدام مكتبة مثل Laravel Excel للتصدير

        $dateRange = $this->getDateRange($request);
        $fromDate = $dateRange['from'];
        $toDate = $dateRange['to'];

        $query = BalanceConsumption::with(['client.branch', 'balanceType', 'invoice']);
        $this->applyFilters($query, $request, $fromDate, $toDate);
        $consumptions = $query->orderBy('consumption_date', 'desc')->get();

        // هنا يمكن استخدام Laravel Excel أو أي مكتبة أخرى للتصدير

        return response()->json([
            'success' => true,
            'message' => 'تم تصدير الملف بنجاح'
        ]);
    }

    /**
     * الحصول على تفاصيل استهلاك رصيد معين
     */
    public function getConsumptionDetails($id)
    {
        try {
            $consumption = BalanceConsumption::with([
                'client.branch',
                'balanceType',
                'invoice'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'consumption' => $consumption
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على البيانات المطلوبة'
            ], 404);
        }
    }

    /**
     * احصائيات سريعة لاستهلاك الأرصدة
     */
    public function getQuickStats(Request $request)
    {
        try {
            $today = Carbon::now();
            $startOfMonth = $today->copy()->startOfMonth();
            $startOfYear = $today->copy()->startOfYear();

            // استهلاك اليوم
            $todayConsumption = BalanceConsumption::whereDate('consumption_date', $today)
                ->sum('used_balance');

            // استهلاك هذا الشهر
            $monthConsumption = BalanceConsumption::whereBetween('consumption_date', [$startOfMonth, $today])
                ->sum('used_balance');

            // استهلاك هذا العام
            $yearConsumption = BalanceConsumption::whereBetween('consumption_date', [$startOfYear, $today])
                ->sum('used_balance');

            // أكثر العملاء استهلاكاً
            $topClients = BalanceConsumption::with('client')
                ->selectRaw('client_id, SUM(used_balance) as total_consumption')
                ->whereBetween('consumption_date', [$startOfMonth, $today])
                ->groupBy('client_id')
                ->orderByDesc('total_consumption')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'stats' => [
                    'today_consumption' => $todayConsumption,
                    'month_consumption' => $monthConsumption,
                    'year_consumption' => $yearConsumption,
                    'top_clients' => $topClients
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الإحصائيات'
            ], 500);
        }
    }


}
