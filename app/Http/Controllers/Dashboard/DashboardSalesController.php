<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
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
use App\Models\ClientEmployee;
use App\Models\Statuses;
use DB;
use Log;

class DashboardSalesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                throw new \Exception('المستخدم غير مسجل دخول');
            }

            // ========== تحديد الفرع الفعال من المستخدم مباشرة ==========
            $currentBranchId = $user->branch_id;
            $isMainBranch = false;

            if ($currentBranchId) {
                $currentBranch = Branch::find($currentBranchId);
                if ($currentBranch && $currentBranch->is_main) {
                    $isMainBranch = true;
                }
            }

            // الفرع الفعال للفلترة
            $effectiveBranchId = ($isMainBranch || !$currentBranchId) ? null : $currentBranchId;

            Log::info('Dashboard Loaded', [
                'user_id' => $user->id,
                'user_branch_id' => $currentBranchId,
                'effective_branch_id' => $effectiveBranchId,
                'is_main_branch' => $isMainBranch
            ]);

            // ========== فواتير مستبعدة ==========
            $returnedInvoiceIds = Invoice::whereNotNull('reference_number')
                ->pluck('reference_number')
                ->toArray();

            $excludedInvoiceIds = array_unique(array_merge(
                $returnedInvoiceIds,
                Invoice::where('type', 'returned')->pluck('id')->toArray()
            ));

            // ========== 1) إحصائيات العملاء ==========
            $clientCountQuery = Client::query();
            if ($effectiveBranchId) {
                $clientCountQuery->where('branch_id', $effectiveBranchId);
            }
            $ClientCount = $clientCountQuery->count();

            $clientStatusCounts = $this->getClientStatusCounts($effectiveBranchId, $isMainBranch);
            $clientCountByBranch = $this->getClientCountByBranch($effectiveBranchId, $isMainBranch);

            // ========== 2) المبيعات ==========
            $invoiceQuery = Invoice::where('type', 'normal')
                ->whereNotIn('id', $excludedInvoiceIds);

            if ($effectiveBranchId) {
                $invoiceQuery->whereHas('client', function($q) use ($effectiveBranchId) {
                    $q->where('branch_id', $effectiveBranchId);
                });
            }
            $Invoice = $invoiceQuery->sum('grand_total') ?? 0;

            // ========== 3) الزيارات ==========
            $visitQuery = Visit::query();
            if ($effectiveBranchId) {
                $visitQuery->whereHas('client', function($q) use ($effectiveBranchId) {
                    $q->where('branch_id', $effectiveBranchId);
                });
            }
            $Visit = $visitQuery->count();

            // ========== 4) زيارات اليوم ==========
            $todayVisitsQuery = Visit::whereDate('created_at', today())
                ->with(['client', 'employee']);

            if ($effectiveBranchId) {
                $todayVisitsQuery->whereHas('client', function($q) use ($effectiveBranchId) {
                    $q->where('branch_id', $effectiveBranchId);
                });
            }
            $todayVisits = $todayVisitsQuery->get();

            // ========== 5) العملاء المفلترين ==========
            $clientIdsQuery = Client::query();
            if ($effectiveBranchId) {
                $clientIdsQuery->where('branch_id', $effectiveBranchId);
            }
            $filteredClientIds = $clientIdsQuery->pluck('id')->toArray();

            if (empty($filteredClientIds)) {
                $groups = collect([]);
                $payments = collect([]);
                $receipts = collect([]);
            } else {
                // ========== 6) المجموعات ==========
                $groups = DB::table('neighborhoods')
                    ->join('region_groubs', 'neighborhoods.region_id', '=', 'region_groubs.id')
                    ->leftJoin('invoices', function($join) use ($excludedInvoiceIds, $filteredClientIds) {
                        $join->on('neighborhoods.client_id', '=', 'invoices.client_id')
                             ->where('invoices.type', '=', 'normal')
                             ->whereNotIn('invoices.id', $excludedInvoiceIds);
                    })
                    ->whereIn('neighborhoods.client_id', $filteredClientIds)
                    ->select(
                        'neighborhoods.region_id',
                        'region_groubs.name as region_name',
                        DB::raw('COALESCE(SUM(invoices.grand_total), 0) as total_sales')
                    )
                    ->groupBy('neighborhoods.region_id', 'region_groubs.name')
                    ->get();

                // ========== 7) المدفوعات ==========
                $validInvoiceIds = DB::table('invoices')
                    ->whereIn('client_id', $filteredClientIds)
                    ->where('type', 'normal')
                    ->whereNotIn('id', $excludedInvoiceIds)
                    ->pluck('id')
                    ->toArray();

                $payments = DB::table('neighborhoods')
                    ->join('invoices', 'neighborhoods.client_id', '=', 'invoices.client_id')
                    ->leftJoin('payments_process', 'invoices.id', '=', 'payments_process.invoice_id')
                    ->whereIn('neighborhoods.client_id', $filteredClientIds)
                    ->whereIn('invoices.id', $validInvoiceIds)
                    ->select(
                        'neighborhoods.region_id',
                        DB::raw('COALESCE(SUM(payments_process.amount), 0) as total_payments')
                    )
                    ->groupBy('neighborhoods.region_id')
                    ->get()
                    ->keyBy('region_id');

                // ========== 8) السندات ==========
                $receipts = DB::table('neighborhoods')
                    ->join('accounts', 'neighborhoods.client_id', '=', 'accounts.client_id')
                    ->leftJoin('receipts', 'accounts.id', '=', 'receipts.account_id')
                    ->whereIn('neighborhoods.client_id', $filteredClientIds)
                    ->select(
                        'neighborhoods.region_id',
                        DB::raw('COALESCE(SUM(receipts.amount), 0) as total_receipts')
                    )
                    ->groupBy('neighborhoods.region_id')
                    ->get()
                    ->keyBy('region_id');
            }

            // ========== 9) دمج البيانات ==========
            $groupChartData = $groups->map(function ($item) use ($payments, $receipts) {
                return [
                    'region'    => $item->region_name ?? 'غير معروف',
                    'sales'     => (float) $item->total_sales,
                    'payments'  => (float) (isset($payments[$item->region_id]) ? $payments[$item->region_id]->total_payments : 0),
                    'receipts'  => (float) (isset($receipts[$item->region_id]) ? $receipts[$item->region_id]->total_receipts : 0),
                ];
            });

            $totalSales    = $groups->sum('total_sales') ?? 0;
            $totalPayments = $payments->sum('total_payments') ?? 0;
            $totalReceipts = $receipts->sum('total_receipts') ?? 0;

            // ========== 10) مبيعات الموظفين ==========
            $employeesSalesQuery = Invoice::selectRaw('created_by, COALESCE(SUM(grand_total), 0) as sales')
                ->where('type', 'normal')
                ->whereNotIn('id', $excludedInvoiceIds);

            if ($effectiveBranchId) {
                $employeesSalesQuery->whereHas('client', function($q) use ($effectiveBranchId) {
                    $q->where('branch_id', $effectiveBranchId);
                });
            }

            $employeesSales = $employeesSalesQuery->groupBy('created_by')->get();
            $totalSalesForPercent = max(1, $totalSales);

            $chartData = $employeesSales->map(function ($employee) use ($totalSalesForPercent, $effectiveBranchId) {
                $user = User::find($employee->created_by);
                if (!$user) return null;

                if ($effectiveBranchId && $user->branch_id != $effectiveBranchId) {
                    return null;
                }

                return [
                    'name'       => $user->name,
                    'sales'      => $employee->sales,
                    'percentage' => round(($employee->sales / $totalSalesForPercent) * 100, 2),
                ];
            })->filter()->values();

            // ========== 11) بطاقات الموظفين ==========
            $defaultTarget = optional(Target::find(1))->value ?? 35000;
            $month = $request->input('month', now()->format('Y-m'));
            [$year, $monthNum] = explode('-', $month);

            $cards = $this->getEmployeeCards($monthNum, $year, $defaultTarget, $excludedInvoiceIds, $effectiveBranchId);

            // ========== 12) الأداء ==========
            $branchesPerformance = $this->calculateBranchesPerformance($excludedInvoiceIds, $effectiveBranchId, $isMainBranch);

            $maxTotal = $branchesPerformance->max('total_collected') ?: 1;
            $branchesPerformance = $branchesPerformance->map(function ($branch) use ($maxTotal) {
                $branch->percentage = round(($branch->total_collected / $maxTotal) * 100, 2);
                return $branch;
            });

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
                'isMainBranch',
                'todayVisits'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()->withErrors([
                'حدث خطأ: ' . $e->getMessage() . ' | الملف: ' . basename($e->getFile()) . ' | السطر: ' . $e->getLine()
            ]);
        }
    }

    private function getEmployeeCards($monthNum, $year, $defaultTarget, $excludedInvoiceIds, $branchId)
    {
        $filteredClientIds = Client::when($branchId, function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->pluck('id')->toArray();

        $invoiceEmployeeIds = Invoice::whereIn('client_id', $filteredClientIds)
            ->whereMonth('created_at', $monthNum)
            ->whereYear('created_at', $year)
            ->where('type', 'normal')
            ->whereNotIn('id', $excludedInvoiceIds)
            ->pluck('created_by')->unique();

        $receiptEmployeeIds = Receipt::join('accounts', 'receipts.account_id', '=', 'accounts.id')
            ->whereIn('accounts.client_id', $filteredClientIds)
            ->whereMonth('receipts.created_at', $monthNum)
            ->whereYear('receipts.created_at', $year)
            ->pluck('receipts.created_by')->unique();

        $employeeIds = $invoiceEmployeeIds->merge($receiptEmployeeIds)->unique();

        if ($branchId) {
            $branchEmployeeIds = User::where('branch_id', $branchId)->pluck('id');
            $employeeIds = $employeeIds->intersect($branchEmployeeIds);
        }

        return $employeeIds->map(function ($userId) use ($defaultTarget, $monthNum, $year, $excludedInvoiceIds, $filteredClientIds) {
            $usr = User::find($userId);
            if (!$usr) return null;

            $invoiceIds = Invoice::whereIn('client_id', $filteredClientIds)
                ->where('created_by', $userId)
                ->where('type', 'normal')
                ->whereNotIn('id', $excludedInvoiceIds)
                ->pluck('id');

            $paymentsTotal = PaymentsProcess::whereIn('invoice_id', $invoiceIds)
                ->whereMonth('created_at', $monthNum)
                ->whereYear('created_at', $year)
                ->sum('amount') ?? 0;

            $receiptsTotal = Receipt::join('accounts', 'receipts.account_id', '=', 'accounts.id')
                ->whereIn('accounts.client_id', $filteredClientIds)
                ->where('receipts.created_by', $userId)
                ->whereMonth('receipts.created_at', $monthNum)
                ->whereYear('receipts.created_at', $year)
                ->sum('receipts.amount') ?? 0;

            $totalCollected = $paymentsTotal + $receiptsTotal;
            $target = optional($usr->target)->monthly_target ?? $defaultTarget;
            $percentage = $target > 0 ? round(($totalCollected / $target) * 100, 2) : 0;

            $clientCount = 0;
            if ($usr->employee_id) {
                $clientCount = ClientEmployee::where('employee_id', $usr->employee_id)
                    ->whereHas('client', function($q) use ($filteredClientIds) {
                        $q->whereIn('id', $filteredClientIds);
                    })
                    ->count();
            }

            return [
                'name' => $usr->name,
                'payments' => $paymentsTotal,
                'receipts' => $receiptsTotal,
                'total' => $totalCollected,
                'target' => $target,
                'percentage' => $percentage,
                'clients_count' => $clientCount,
            ];
        })->filter()->sortByDesc('total')->values();
    }

    private function getClientStatusCounts($branchId, $isMainBranch)
    {
        $query = Client::select('status_id', DB::raw('COUNT(*) as count'));

        if (!$isMainBranch && $branchId) {
            $query->where('branch_id', $branchId);
        }

        $statusCounts = $query->whereNotNull('status_id')
            ->groupBy('status_id')
            ->get()
            ->keyBy('status_id');

        $statuses = Statuses::all();

        return $statuses->map(function ($status) use ($statusCounts) {
            return [
                'id' => $status->id,
                'name' => $status->name,
                'color' => $status->color ?? '#6c757d',
                'count' => isset($statusCounts[$status->id]) ? $statusCounts[$status->id]->count : 0
            ];
        });
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

        return $branches->map(function ($branch) use ($branchCounts) {
            return [
                'id' => $branch->id,
                'name' => $branch->name,
                'count' => isset($branchCounts[$branch->id]) ? $branchCounts[$branch->id]->count : 0
            ];
        });
    }

    private function calculateBranchesPerformance($excludedInvoiceIds, $branchId, $isMainBranch)
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
            ->groupBy(fn($client) => optional($client->Neighborhoodname)->name ?? 'غير معروف')
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
            ->groupBy(fn($client) => optional(optional($client->Neighborhoodname)->Region)->name ?? 'غير معروف')
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
