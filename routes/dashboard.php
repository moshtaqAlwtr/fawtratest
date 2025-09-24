<?php

use App\Http\Controllers\Dashboard\DashboardSalesController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Neighborhood;
use App\Models\Visit;
use App\Models\User;
use App\Models\PaymentsProcess;
use App\Models\ClientEmployee;
use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Models\Target;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__ . '/auth.php';

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'check.branch']
    ],
    function () {

        Route::get('', function (Request $request) {

        $ClientCount = Client::count();
        $Invoice = Invoice::where('type', 'normal')->sum('grand_total');
        $Visit = Visit::count();





        // 1. جلب بيانات الفواتير والمبيعات
        $groups = Neighborhood::with('Region')
            ->leftJoin('invoices', 'neighborhoods.client_id', '=', 'invoices.client_id')
            ->select('region_id', DB::raw('COALESCE(SUM(invoices.grand_total), 0) as total_sales'))
            ->groupBy('region_id')
            ->get();

        // 2. حساب المدفوعات لكل مجموعة
        $payments = Neighborhood::join('region_groubs', 'neighborhoods.region_id', '=', 'region_groubs.id')
            ->join('clients', 'neighborhoods.client_id', '=', 'clients.id')
            ->join('invoices', 'clients.id', '=', 'invoices.client_id')
            ->join('payments_process', 'invoices.id', '=', 'payments_process.invoice_id')
            ->select('neighborhoods.region_id', DB::raw('COALESCE(SUM(payments_process.amount), 0) as total_payments'))
            ->groupBy('neighborhoods.region_id')
            ->get()
            ->keyBy('region_id');



        // 3. حساب سندات القبض لكل مجموعة
        $receipts = Neighborhood::join('region_groubs', 'neighborhoods.region_id', '=', 'region_groubs.id')
            ->join('clients', 'neighborhoods.client_id', '=', 'clients.id')
            ->join('accounts', 'clients.id', '=', 'accounts.client_id')
            ->join('receipts', 'accounts.id', '=', 'receipts.account_id')
            ->select('neighborhoods.region_id', DB::raw('COALESCE(SUM(receipts.amount), 0) as total_receipts'))
            ->groupBy('neighborhoods.region_id')
            ->get()
            ->keyBy('region_id');


        // 4. دمج البيانات في groupChartData
        $groupChartData = $groups->map(function ($item) use ($payments, $receipts) {
            return [
                'region'    => optional($item->region)->name ?? 'غير معروف',
                'sales'     => (float) $item->total_sales,
                'payments'  => (float) ($payments[$item->region_id]->total_payments ?? 0),
                'receipts'  => (float) ($receipts[$item->region_id]->total_receipts ?? 0),
            ];
        });




        ///
        // حساب إجمالي المبيعات
        $totalSales = Invoice::where('type', 'normal')->sum('grand_total');

        // الحصول على مبيعات الموظفين
        $employeesSales = Invoice::selectRaw('created_by, COALESCE(SUM(grand_total), 0) as sales')
            ->groupBy('created_by')
            ->get();


        // إنشاء البيانات للمخطط البياني
        $chartData = $employeesSales->map(function ($employee) use ($totalSales) {
            $user = User::find($employee->created_by);
            return [
                'name' => $user ? $user->name : 'غير معروف',
                'sales' => $employee->sales,
                'percentage' => ($totalSales > 0) ? round(($employee->sales / $totalSales) * 100, 2) : 0
            ];
        });

        $defaultTarget = Target::find(1)->value ?? 35000;
        // الشهر المحدد
        $month = $request->input('month', now()->format('Y-m'));
        [$year, $monthNum] = explode('-', $month);

        // الموظفون الذين لديهم فواتير هذا الشهر (لتحديد من له مدفوعات)
        $invoiceEmployeeIds = Invoice::whereMonth('created_at', $monthNum)
            ->whereYear('created_at', $year)
            ->pluck('created_by')
            ->unique();

        // الموظفون الذين أنشؤوا سندات قبض هذا الشهر
        $receiptEmployeeIds = Receipt::whereMonth('created_at', $monthNum)
            ->whereYear('created_at', $year)
            ->pluck('created_by')
            ->unique();

        // دمج كل من لديه نشاط في هذا الشهر
        $employeeIds = $invoiceEmployeeIds->merge($receiptEmployeeIds)->unique();

        // استخراج بيانات الأداء
        $cards = $employeeIds->map(function ($userId) use ($defaultTarget, $monthNum, $year) {
            $user = User::find($userId);

            $returnedInvoiceIds = Invoice::whereNotNull('reference_number')
                ->pluck('reference_number')
                ->toArray();

            // الفواتير الأصلية التي يجب استبعادها = كل فاتورة تم عمل راجع لها
            // بالإضافة إلى الفواتير التي تم تصنيفها صراحةً على أنها راجعة
            $excludedInvoiceIds = array_unique(array_merge(
                $returnedInvoiceIds,
                Invoice::where('type', 'returned')->pluck('id')->toArray()
            ));



            $invoiceIds = Invoice::where('created_by', $userId)->where('type', 'normal')->whereNotIn('id', $excludedInvoiceIds) // ✅ استبعاد الفواتير التي لها راجع
                ->pluck('id');

            $paymentsTotal = PaymentsProcess::whereIn('invoice_id', $invoiceIds)->whereMonth('created_at', $monthNum)
                ->whereYear('created_at', $year)->sum('amount');

            $receiptsTotal = Receipt::where('created_by', $userId)
                ->whereMonth('created_at', $monthNum)
                ->whereYear('created_at', $year)
                ->sum('amount');

            $totalCollected = $paymentsTotal + $receiptsTotal;

            $target = $user->target?->monthly_target ?? $defaultTarget;
            $percentage = $target > 0 ? round(($totalCollected / $target) * 100, 2) : 0;

$clientCount = 0;
if ($user && $user->employee_id) {
    $clientCount = ClientEmployee::where('employee_id', $user->employee_id)->count();
}

            return [
                'name' => $user?->name ?? 'غير معروف',
                'payments' => $paymentsTotal,
                'receipts' => $receiptsTotal,
                'total' => $totalCollected,
                'target' => $target,
                'percentage' => $percentage,
                'clients_count' => $clientCount,
            ];
        });

        // ✅ الترتيب تنازليًا حسب المبلغ المحصل
        $cards = $cards->sortByDesc('total')->values();

// تحميل الفواتير المستبعدة مرة واحدة
$returnedInvoiceIds = Invoice::whereNotNull('reference_number')
    ->pluck('reference_number')
    ->toArray();

$excludedInvoiceIds = array_unique(array_merge(
    $returnedInvoiceIds,
    Invoice::where('type', 'returned')->pluck('id')->toArray()
));

// تحميل كل العملاء المرتبطين بفروع
$clients = Client::with('branch')->whereNotNull('branch_id')->get();

// تحميل جميع الفواتير الصالحة دفعة واحدة
$validInvoices = Invoice::where('type', 'normal')
    ->whereNotIn('id', $excludedInvoiceIds)
    ->get(['id', 'client_id']);

// تحميل جميع المدفوعات المرتبطة بالفواتير
$payments = PaymentsProcess::whereIn('invoice_id', $validInvoices->pluck('id'))
    ->get(['invoice_id', 'amount']);

// تحميل جميع السندات المرتبطة بحسابات العملاء
$receipts = Receipt::with('account')
    ->whereHas('account')
    ->get(['id', 'amount', 'account_id']);

$invoiceByClient = $validInvoices->groupBy('client_id');
$paymentsByInvoice = $payments->groupBy('invoice_id');
$receiptsByClient = $receipts->groupBy(fn($receipt) => optional($receipt->account)->client_id);

// تجميع أداء الفروع
$branchesPerformance = $clients->groupBy('branch_id')->map(function ($clientsInBranch, $branchId) use ($invoiceByClient, $paymentsByInvoice, $receiptsByClient) {
    $branchName = optional($clientsInBranch->first()->branch)->name ?? 'غير معروف';

    $totalPayments = 0;
    $totalReceipts = 0;

    foreach ($clientsInBranch as $client) {
      $invoiceIds = isset($invoiceByClient[$client->id])
    ? $invoiceByClient[$client->id]->pluck('id')
    : collect();

        $payments = $invoiceIds->flatMap(function ($id) use ($paymentsByInvoice) {
            return $paymentsByInvoice[$id] ?? collect();
        })->sum('amount');

        $receipts = $receiptsByClient[$client->id] ?? collect();
        $receiptsSum = $receipts->sum('amount');

        $totalPayments += $payments;
        $totalReceipts += $receiptsSum;
    }

    return (object)[
        'branch_id' => $branchId,
        'branch_name' => $branchName,
        'total_collected' => $totalPayments + $totalReceipts,
        'payments' => $totalPayments,
        'receipts' => $totalReceipts,
    ];
})->sortByDesc('total_collected')->values();



  // 1. تحميل الفواتير الصالحة دفعة واحدة
$validInvoices = Invoice::where('type', 'normal')
    ->whereNotIn('id', $excludedInvoiceIds)
    ->get(['id', 'client_id']);

$invoiceByClient = $validInvoices->groupBy('client_id');

// 2. تحميل المدفوعات المرتبطة بالفواتير
$payments = PaymentsProcess::whereIn('invoice_id', $validInvoices->pluck('id'))
    ->get(['invoice_id', 'amount']);

$paymentByInvoice = $payments->groupBy('invoice_id');

// 3. تحميل السندات مع الحسابات المرتبطة
$receipts = Receipt::with('account')->whereHas('account')->get();
$receiptByClient = $receipts->groupBy(fn($r) => optional($r->account)->client_id);

// 4. تحميل العملاء مع الحي فقط
$clients = Client::with('Neighborhoodname')->whereHas('Neighborhoodname')->get();

// 5. حساب الأداء حسب الحي
$neighborhoodPerformance = $clients
    ->groupBy(fn($client) => $client->Neighborhoodname->name ?? 'غير معروف')
    ->map(function ($clientsInNeighborhood, $neighborhoodName) use ($invoiceByClient, $paymentByInvoice, $receiptByClient) {
        $totalPayments = 0;
        $totalReceipts = 0;

        foreach ($clientsInNeighborhood as $client) {
            $invoices = $invoiceByClient[$client->id] ?? collect();
            $invoiceIds = $invoices->pluck('id');

            $payments = $invoiceIds->flatMap(function ($id) use ($paymentByInvoice) {
                return $paymentByInvoice[$id] ?? collect();
            })->sum('amount');

            $receipts = $receiptByClient[$client->id] ?? collect();
            $receiptsSum = $receipts->sum('amount');

            $totalPayments += $payments;
            $totalReceipts += $receiptsSum;
        }

        return (object)[
            'neighborhood_name' => $neighborhoodName,
            'total_collected' => $totalPayments + $totalReceipts,
            'payments' => $totalPayments,
            'receipts' => $totalReceipts,
        ];
    })
    ->sortByDesc('total_collected')
    ->values();



// إضافة النسبة لكل فرع (مقارنة بأعلى تحصيل)
$maxTotal = $branchesPerformance->max('total_collected') ?: 1;

$branchesPerformance = $branchesPerformance->map(function ($branch) use ($maxTotal) {
    $branch->percentage = round(($branch->total_collected / $maxTotal) * 100, 2);
    return $branch;
});

// المناطق او المجموعات
// 1. الفواتير الصالحة دفعة واحدة
$validInvoices = Invoice::where('type', 'normal')
    ->whereNotIn('id', $excludedInvoiceIds)
    ->get(['id', 'client_id']);

$invoiceByClient = $validInvoices->groupBy('client_id');

// 2. المدفوعات دفعة واحدة
$payments = PaymentsProcess::whereIn('invoice_id', $validInvoices->pluck('id'))
    ->get(['invoice_id', 'amount']);

$paymentByInvoice = $payments->groupBy('invoice_id');

// 3. السندات مع الحسابات دفعة واحدة
$receipts = Receipt::with('account')->whereHas('account')->get();
$receiptByClient = $receipts->groupBy(fn($r) => optional($r->account)->client_id);

// 4. العملاء مع الحي والمنطقة دفعة واحدة
$clients = Client::with('Neighborhoodname.Region')
    ->whereHas('Neighborhoodname.Region')
    ->get();

// 5. الأداء حسب المنطقة
$regionPerformance = $clients
    ->groupBy(fn($client) => $client->Neighborhoodname->Region->name ?? 'غير معروف')
    ->map(function ($clientsInRegion, $regionName) use ($invoiceByClient, $paymentByInvoice, $receiptByClient) {
        $totalPayments = 0;
        $totalReceipts = 0;

        foreach ($clientsInRegion as $client) {
            $invoices = $invoiceByClient[$client->id] ?? collect();
            $invoiceIds = $invoices->pluck('id');

            $payments = $invoiceIds->flatMap(function ($id) use ($paymentByInvoice) {
                return $paymentByInvoice[$id] ?? collect();
            })->sum('amount');

            $receipts = $receiptByClient[$client->id] ?? collect();
            $receiptsSum = $receipts->sum('amount');

            $totalPayments += $payments;
            $totalReceipts += $receiptsSum;
        }

        return (object)[
            'region_name' => $regionName,
            'total_collected' => $totalPayments + $totalReceipts,
            'payments' => $totalPayments,
            'receipts' => $totalReceipts,
        ];
    })
    ->sortByDesc('total_collected')
    ->values();





// 3. متوسط التحصيل على مستوى الفروع
$averageBranchCollection = $branchesPerformance->avg('total_collected');





        $totalSales    = $groups->sum('total_sales');
        $totalPayments = $payments->sum('total_payments');
        $totalReceipts = $receipts->sum('total_receipts');

        $averageRegionCollection = $regionPerformance->avg('total_collected');

        $lowestRegions = $regionPerformance->sortBy('total_collected')->take(3)->values();





        return view('dashboard.sales.index', compact('ClientCount', 'cards','averageBranchCollection', 'month','lowestRegions','branchesPerformance','regionPerformance','neighborhoodPerformance', 'groupChartData', 'Invoice', 'groups', 'Visit', 'chartData', 'totalSales', 'totalPayments', 'totalReceipts'));

        })->middleware(['auth']);

        Route::prefix('dashboard')->middleware(['auth'])->group(function () {
            #questions routes
            Route::prefix('sales')->group(function () {
                Route::get('/index', [DashboardSalesController::class, 'index'])->name('dashboard_sales.index');
            });
        });

    }


);







