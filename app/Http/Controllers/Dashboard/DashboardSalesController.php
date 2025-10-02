<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Neighborhood;
use App\Models\PaymentsProcess;
use App\Models\Receipt;
use App\Models\Target;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Support\Collection;
use App\Models\ClientEmployee;
use App\Models\Statuses;
use Carbon\Carbon;
use DB;

class DashboardSalesController extends Controller
{
   public function index(Request $request)
{
    $user = auth()->user();

    // تحديد الفرع الفعال للفلترة
    $currentBranchId = $user->branch_id;
    $isMainBranch = false;

    if ($currentBranchId) {
        $mainBranch = Branch::where('is_main', true)->first();
        $currentBranch = Branch::find($currentBranchId);
        $isMainBranch = $currentBranch && $mainBranch && $currentBranch->name === $mainBranch->name;
    }

    // الفرع الفعّال: إذا كان رئيسي = null (يعرض الكل)، وإلا فرع المستخدم
    $effectiveBranchId = $isMainBranch ? null : $currentBranchId;

    // ========== فواتير مستبعدة (مرتجعة) ==========
    $returnedInvoiceIds = Invoice::whereNotNull('reference_number')
        ->pluck('reference_number')
        ->toArray();

    $excludedInvoiceIds = array_unique(array_merge(
        $returnedInvoiceIds,
        Invoice::where('type', 'returned')->pluck('id')->toArray()
    ));

    // ========== 1) إحصائيات العملاء مع الفلترة ==========
    $clientCountQuery = Client::query();

    if ($effectiveBranchId) {
        $clientCountQuery->where('branch_id', $effectiveBranchId);
    }

    $ClientCount = $clientCountQuery->count();

    // العملاء حسب الحالة (مع فلترة الفرع)
    $clientStatusCounts = $this->getClientStatusCounts($effectiveBranchId, $isMainBranch);

    // العملاء حسب الفرع
    $clientCountByBranch = $this->getClientCountByBranch($effectiveBranchId, $isMainBranch);

    // ========== 2) المبيعات الإجمالية مع الفلترة ==========
    $invoiceQuery = Invoice::where('type', 'normal')
        ->whereNotIn('id', $excludedInvoiceIds);

    if ($effectiveBranchId) {
        $invoiceQuery->whereHas('client', function($q) use ($effectiveBranchId) {
            $q->where('branch_id', $effectiveBranchId);
        });
    }

    $Invoice = $invoiceQuery->sum('grand_total');

    // ========== 3) الزيارات مع الفلترة ==========
    $visitQuery = Visit::query();

    if ($effectiveBranchId) {
        $visitQuery->whereHas('client', function($q) use ($effectiveBranchId) {
            $q->where('branch_id', $effectiveBranchId);
        });
    }

    $Visit = $visitQuery->count();

    // ========== 4) بيانات المجموعات (Regions) مع الفلترة ==========
    $groupsQuery = Neighborhood::with('Region')
        ->join('clients', 'neighborhoods.client_id', '=', 'clients.id')
        ->leftJoin('invoices', function($join) use ($excludedInvoiceIds) {
            $join->on('neighborhoods.client_id', '=', 'invoices.client_id')
                 ->where('invoices.type', '=', 'normal')
                 ->whereNotIn('invoices.id', $excludedInvoiceIds);
        })
        ->leftJoin('region_groubs', 'neighborhoods.region_id', '=', 'region_groubs.id');

    if ($effectiveBranchId) {
        $groupsQuery->where('clients.branch_id', $effectiveBranchId);
    }

    $groups = $groupsQuery
        ->select(
            'neighborhoods.region_id',
            'region_groubs.name as region_name',
            DB::raw('COALESCE(SUM(invoices.grand_total), 0) as total_sales')
        )
        ->groupBy('neighborhoods.region_id', 'region_groubs.name')
        ->get();

    // ========== 5) مدفوعات حسب المجموعة مع الفلترة ==========
    $paymentsQuery = Neighborhood::join('region_groubs', 'neighborhoods.region_id', '=', 'region_groubs.id')
        ->join('clients', 'neighborhoods.client_id', '=', 'clients.id')
        ->join('invoices', 'clients.id', '=', 'invoices.client_id')
        ->join('payments_process', 'invoices.id', '=', 'payments_process.invoice_id')
        ->where('invoices.type', 'normal')
        ->whereNotIn('invoices.id', $excludedInvoiceIds);

    if ($effectiveBranchId) {
        $paymentsQuery->where('clients.branch_id', $effectiveBranchId);
    }

    $payments = $paymentsQuery
        ->select('neighborhoods.region_id', DB::raw('COALESCE(SUM(payments_process.amount), 0) as total_payments'))
        ->groupBy('neighborhoods.region_id')
        ->get()
        ->keyBy('region_id');

    // ========== 6) سندات القبض مع الفلترة ==========
    $receiptsQuery = Neighborhood::join('region_groubs', 'neighborhoods.region_id', '=', 'region_groubs.id')
        ->join('clients', 'neighborhoods.client_id', '=', 'clients.id')
        ->join('accounts', 'clients.id', '=', 'accounts.client_id')
        ->join('receipts', 'accounts.id', '=', 'receipts.account_id');

    if ($effectiveBranchId) {
        $receiptsQuery->where('clients.branch_id', $effectiveBranchId);
    }

    $receipts = $receiptsQuery
        ->select('neighborhoods.region_id', DB::raw('COALESCE(SUM(receipts.amount), 0) as total_receipts'))
        ->groupBy('neighborhoods.region_id')
        ->get()
        ->keyBy('region_id');

    // ========== 7) دمج بيانات الرسم للمجموعات ==========
    $groupChartData = $groups->map(function ($item) use ($payments, $receipts) {
        return [
            'region'    => $item->region_name ?? 'غير معروف',
            'sales'     => (float) $item->total_sales,
            'payments'  => (float) ($payments[$item->region_id]->total_payments ?? 0),
            'receipts'  => (float) ($receipts[$item->region_id]->total_receipts ?? 0),
        ];
    });

    // إجماليات مفلترة
    $totalSales    = $groups->sum('total_sales');
    $totalPayments = $payments->sum('total_payments');
    $totalReceipts = $receipts->sum('total_receipts');

    // ========== 8) مبيعات الموظفين مع الفلترة ==========
    $employeesSalesQuery = Invoice::selectRaw('created_by, COALESCE(SUM(grand_total), 0) as sales')
        ->where('type', 'normal')
        ->whereNotIn('id', $excludedInvoiceIds);

    if ($effectiveBranchId) {
        $employeesSalesQuery->whereHas('client', function($q) use ($effectiveBranchId) {
            $q->where('branch_id', $effectiveBranchId);
        });
    }

    $employeesSales = $employeesSalesQuery
        ->groupBy('created_by')
        ->get();

    $totalSalesForPercent = max(1, $totalSales);
    $chartData = $employeesSales->map(function ($employee) use ($totalSalesForPercent, $effectiveBranchId) {
        $user = User::find($employee->created_by);
        if (!$user) return null;

        // تأكد من أن الموظف من نفس الفرع
        if ($effectiveBranchId && $user->branch_id != $effectiveBranchId) {
            return null;
        }

        return [
            'name'       => $user->name,
            'sales'      => $employee->sales,
            'percentage' => round(($employee->sales / $totalSalesForPercent) * 100, 2),
        ];
    })->filter()->values();

    // ========== 9) بطاقات أداء الموظفين مع الفلترة ==========
    $defaultTarget = optional(Target::find(1))->value ?? 35000;
    $month = $request->input('month', now()->format('Y-m'));
    [$year, $monthNum] = explode('-', $month);

    // الموظفين النشطين في الشهر المحدد (مع فلترة الفرع)
    $invoiceEmployeeIdsQuery = Invoice::whereMonth('created_at', $monthNum)
        ->whereYear('created_at', $year)
        ->where('type', 'normal')
        ->whereNotIn('id', $excludedInvoiceIds);

    if ($effectiveBranchId) {
        $invoiceEmployeeIdsQuery->whereHas('client', function($q) use ($effectiveBranchId) {
            $q->where('branch_id', $effectiveBranchId);
        });
    }

    $invoiceEmployeeIds = $invoiceEmployeeIdsQuery->pluck('created_by')->unique();

    $receiptEmployeeIdsQuery = Receipt::whereMonth('created_at', $monthNum)
        ->whereYear('created_at', $year);

    if ($effectiveBranchId) {
        $receiptEmployeeIdsQuery->whereHas('account.client', function($q) use ($effectiveBranchId) {
            $q->where('branch_id', $effectiveBranchId);
        });
    }

    $receiptEmployeeIds = $receiptEmployeeIdsQuery->pluck('created_by')->unique();

    $employeeIds = $invoiceEmployeeIds->merge($receiptEmployeeIds)->unique();

    // فلترة الموظفين حسب الفرع
    if ($effectiveBranchId) {
        $branchEmployeeIds = User::where('branch_id', $effectiveBranchId)->pluck('id');
        $employeeIds = $employeeIds->intersect($branchEmployeeIds);
    }

    $cards = $employeeIds->map(function ($userId) use ($defaultTarget, $monthNum, $year, $excludedInvoiceIds, $effectiveBranchId) {
        $usr = User::find($userId);
        if (!$usr) return null;

        // فواتير الموظف المعتبرة (مع فلترة الفرع)
        $invoiceIdsQuery = Invoice::where('created_by', $userId)
            ->where('type', 'normal')
            ->whereNotIn('id', $excludedInvoiceIds);

        if ($effectiveBranchId) {
            $invoiceIdsQuery->whereHas('client', function($q) use ($effectiveBranchId) {
                $q->where('branch_id', $effectiveBranchId);
            });
        }

        $invoiceIds = $invoiceIdsQuery->pluck('id');

        // مدفوعات هذا الشهر
        $paymentsTotal = PaymentsProcess::whereIn('invoice_id', $invoiceIds)
            ->whereMonth('created_at', $monthNum)
            ->whereYear('created_at', $year)
            ->sum('amount');

        // سندات هذا الشهر (مع فلترة الفرع)
        $receiptsQuery = Receipt::where('created_by', $userId)
            ->whereMonth('created_at', $monthNum)
            ->whereYear('created_at', $year);

        if ($effectiveBranchId) {
            $receiptsQuery->whereHas('account.client', function($q) use ($effectiveBranchId) {
                $q->where('branch_id', $effectiveBranchId);
            });
        }

        $receiptsTotal = $receiptsQuery->sum('amount');

        $totalCollected = $paymentsTotal + $receiptsTotal;
        $target = optional($usr->target)->monthly_target ?? $defaultTarget;
        $percentage = $target > 0 ? round(($totalCollected / $target) * 100, 2) : 0;

        // عدد العملاء (مع فلترة الفرع)
        $clientCount = 0;
        if ($usr->employee_id) {
            $clientCountQuery = ClientEmployee::where('employee_id', $usr->employee_id);

            if ($effectiveBranchId) {
                $clientCountQuery->whereHas('client', function($q) use ($effectiveBranchId) {
                    $q->where('branch_id', $effectiveBranchId);
                });
            }

            $clientCount = $clientCountQuery->count();
        }

        return [
            'name'          => $usr->name,
            'payments'      => $paymentsTotal,
            'receipts'      => $receiptsTotal,
            'total'         => $totalCollected,
            'target'        => $target,
            'percentage'    => $percentage,
            'clients_count' => $clientCount,
        ];
    })->filter()->sortByDesc('total')->values();

    // ========== 10) أداء الفروع ==========
    $branchesPerformance = $this->calculateBranchesPerformance($excludedInvoiceIds, $effectiveBranchId, $isMainBranch);

    $maxTotal = $branchesPerformance->max('total_collected') ?: 1;
    $branchesPerformance = $branchesPerformance->map(function ($branch) use ($maxTotal) {
        $branch->percentage = round(($branch->total_collected / $maxTotal) * 100, 2);
        return $branch;
    });

    // ========== 11) أداء الأحياء/المناطق ==========
    $neighborhoodPerformance = $this->calculateNeighborhoodPerformance($excludedInvoiceIds, $effectiveBranchId, $isMainBranch);
    $regionPerformance = $this->calculateRegionPerformance($excludedInvoiceIds, $effectiveBranchId, $isMainBranch);

    $averageBranchCollection = $branchesPerformance->avg('total_collected') ?? 0;
    $lowestRegions = $regionPerformance->sortBy('total_collected')->take(3)->values();

    return view('dashboard.sales.index', compact(
        'ClientCount',
        'clientStatusCounts',
        'clientCountByBranch',
        'Invoice',
        'Visit',
        'cards',
        'averageBranchCollection',
        'month',
        'lowestRegions',
        'branchesPerformance',
        'regionPerformance',
        'neighborhoodPerformance',
        'groupChartData',
        'groups',
        'chartData',
        'totalSales',
        'totalPayments',
        'totalReceipts',
        'isMainBranch'
    ));
}

    // === الدوال المساعدة ===

    private function getClientStatusCounts($branchId, $isMainBranch)
    {
        $query = Client::select('status_id', DB::raw('COUNT(*) as count'));

        if (!$isMainBranch && $branchId) {
            $query->where('branch_id', $branchId);
        }

        $statusCounts = $query
            ->whereNotNull('status_id')
            ->groupBy('status_id')
            ->get()
            ->keyBy('status_id');

        $statuses = Statuses::all();

        $result = [];
        foreach ($statuses as $status) {
            $result[] = [
                'id' => $status->id,
                'name' => $status->name,
                'color' => $status->color ?? '#6c757d',
                'count' => isset($statusCounts[$status->id]) ? $statusCounts[$status->id]->count : 0
            ];
        }

        return collect($result);
    }

    private function getClientCountByBranch($currentBranchId, $isMainBranch)
    {
        $query = Client::select('branch_id', DB::raw('COUNT(*) as count'))
            ->whereNotNull('branch_id')
            ->groupBy('branch_id');

        if (!$isMainBranch && $currentBranchId) {
            $query->where('branch_id', $currentBranchId);
        }

        $branchCounts = $query->get()->keyBy('branch_id');

        $branchesQuery = Branch::query();
        if (!$isMainBranch && $currentBranchId) {
            $branchesQuery->where('id', $currentBranchId);
        }
        $branches = $branchesQuery->get();

        $result = [];
        foreach ($branches as $branch) {
            $result[] = [
                'id' => $branch->id,
                'name' => $branch->name,
                'count' => isset($branchCounts[$branch->id]) ? $branchCounts[$branch->id]->count : 0
            ];
        }

        return collect($result);
    }

    private function calculateBranchesPerformance($excludedInvoiceIds, $branchId = null, $isMainBranch = false)
    {
        $validInvoicesQuery = Invoice::where('type', 'normal')
            ->whereNotIn('id', $excludedInvoiceIds);

        if (!$isMainBranch && $branchId) {
            $validInvoicesQuery->whereHas('client', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $validInvoices = $validInvoicesQuery->get(['id', 'client_id']);
        $invoiceByClient = $validInvoices->groupBy('client_id');

        $payments = PaymentsProcess::whereIn('invoice_id', $validInvoices->pluck('id'))
            ->get(['invoice_id', 'amount']);
        $paymentsByInvoice = $payments->groupBy('invoice_id');

        $receiptsQuery = Receipt::with('account')->whereHas('account');

        if (!$isMainBranch && $branchId) {
            $receiptsQuery->whereHas('account.client', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $receipts = $receiptsQuery->get(['id', 'amount', 'account_id']);
        $receiptsByClient = $receipts->groupBy(fn($receipt) => optional($receipt->account)->client_id);

        $clientsQuery = Client::with('branch')->whereNotNull('branch_id');

        if (!$isMainBranch && $branchId) {
            $clientsQuery->where('branch_id', $branchId);
        }

        $clients = $clientsQuery->get();

        return $clients->groupBy('branch_id')->map(function ($clientsInBranch, $branchId) use ($invoiceByClient, $paymentsByInvoice, $receiptsByClient) {
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
    }

    private function calculateNeighborhoodPerformance($excludedInvoiceIds, $branchId, $isMainBranch)
    {
        $validInvoicesQuery = Invoice::where('type', 'normal')
            ->whereNotIn('id', $excludedInvoiceIds);

        if (!$isMainBranch && $branchId) {
            $validInvoicesQuery->whereHas('client', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $validInvoices = $validInvoicesQuery->get(['id', 'client_id']);
        $invoiceByClient = $validInvoices->groupBy('client_id');

        $payments = PaymentsProcess::whereIn('invoice_id', $validInvoices->pluck('id'))
            ->get(['invoice_id', 'amount']);
        $paymentByInvoice = $payments->groupBy('invoice_id');

        $receiptsQuery = Receipt::with('account')->whereHas('account');

        if (!$isMainBranch && $branchId) {
            $receiptsQuery->whereHas('account.client', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $receipts = $receiptsQuery->get();
        $receiptByClient = $receipts->groupBy(fn($r) => optional($r->account)->client_id);

        $clientsQuery = Client::with('Neighborhood')->whereHas('Neighborhood');

        if (!$isMainBranch && $branchId) {
            $clientsQuery->where('branch_id', $branchId);
        }

        $clients = $clientsQuery->get();

        return $clients
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
    }

    private function calculateRegionPerformance($excludedInvoiceIds, $branchId, $isMainBranch)
    {
        $validInvoicesQuery = Invoice::where('type', 'normal')
            ->whereNotIn('id', $excludedInvoiceIds);

        if (!$isMainBranch && $branchId) {
            $validInvoicesQuery->whereHas('client', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $validInvoices = $validInvoicesQuery->get(['id', 'client_id']);
        $invoiceByClient = $validInvoices->groupBy('client_id');

        $payments = PaymentsProcess::whereIn('invoice_id', $validInvoices->pluck('id'))
            ->get(['invoice_id', 'amount']);
        $paymentByInvoice = $payments->groupBy('invoice_id');

        $receiptsQuery = Receipt::with('account')->whereHas('account');

        if (!$isMainBranch && $branchId) {
            $receiptsQuery->whereHas('account.client', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $receipts = $receiptsQuery->get();
        $receiptByClient = $receipts->groupBy(fn($r) => optional($r->account)->client_id);

        $clientsQuery = Client::with('Neighborhood.Region')
            ->whereHas('Neighborhood.Region');

        if (!$isMainBranch && $branchId) {
            $clientsQuery->where('branch_id', $branchId);
        }

        $clients = $clientsQuery->get();

        return $clients
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
    }
}