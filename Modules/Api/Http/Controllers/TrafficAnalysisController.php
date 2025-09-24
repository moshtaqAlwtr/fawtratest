<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Branch;
use Modules\Api\Http\Resources\TrafficAnalysisCollectionResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;


class TrafficAnalysisController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
public function apiTrafficAnalysis(Request $request)
{
    try {
        $year = $request->get('year', now()->year);
        $weeks = $this->generateYearWeeks($year);

        $startDate = Carbon::createFromDate($year, 1, 1)->startOfWeek();
        $endDate = Carbon::createFromDate($year, 12, 31)->endOfWeek();

        $excludedInvoiceIds = \App\Models\Invoice::whereNotNull('reference_number')->pluck('reference_number')
            ->merge(\App\Models\Invoice::where('type', 'returned')->pluck('id'))
            ->unique()->toArray();

        // تحميل الفروع والمجموعات والعملاء بعلاقاتهم
        $branches = Branch::with([
            'regionGroups.neighborhoods.client' => function ($q) use ($startDate, $endDate) {
                $q->with([
                    'neighborhood.Region.branch',
                    'status_client',
                    'invoices' => fn($q) => $q->whereBetween('invoices.created_at', [$startDate, $endDate]),
                    'payments' => fn($q) => $q->whereBetween('payments_process.created_at', [$startDate, $endDate]),
                    'visits' => fn($q) => $q->whereBetween('visits.created_at', [$startDate, $endDate]),
                    'appointmentNotes' => fn($q) => $q->whereBetween('client_relations.created_at', [$startDate, $endDate]),
                    'accounts.receipts' => fn($q) => $q->whereBetween('receipts.created_at', [$startDate, $endDate]),
                ]);
            }
        ])->get();

        // تجميع بيانات التحصيل والزيارات لكل عميل لكل أسبوع
        $clientWeeklyStats = [];

        foreach ($weeks as $week) {
            $start = $week['start']->copy()->startOfDay();
            $end = $week['end']->copy()->endOfDay();
            $weekNumber = $week['week_number'];

            // التحصيل: المدفوعات
            $payments = DB::table('payments_process')
                ->join('invoices', 'payments_process.invoice_id', '=', 'invoices.id')
                ->where('invoices.type', 'normal')
                ->whereNotIn('invoices.id', $excludedInvoiceIds)
                ->whereBetween('payments_process.created_at', [$start, $end])
                ->select('invoices.client_id', DB::raw('SUM(payments_process.amount) as total'))
                ->groupBy('invoices.client_id')
                ->get();

            foreach ($payments as $row) {
                $clientWeeklyStats[$row->client_id][$weekNumber]['collection'] =
                    ($clientWeeklyStats[$row->client_id][$weekNumber]['collection'] ?? 0) + $row->total;
            }

            // التحصيل: سندات القبض
            $receipts = DB::table('receipts')
                ->join('accounts', 'receipts.account_id', '=', 'accounts.id')
                ->whereBetween('receipts.created_at', [$start, $end])
                ->select('accounts.client_id', DB::raw('SUM(receipts.amount) as total'))
                ->groupBy('accounts.client_id')
                ->get();

            foreach ($receipts as $row) {
                $clientWeeklyStats[$row->client_id][$weekNumber]['collection'] =
                    ($clientWeeklyStats[$row->client_id][$weekNumber]['collection'] ?? 0) + $row->total;
            }

            // الزيارات
            $visits = DB::table('visits')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('client_id, COUNT(DISTINCT DATE_FORMAT(created_at, "%Y-%m-%d %H")) as visit_count')
                ->groupBy('client_id')
                ->get();

            foreach ($visits as $row) {
                $clientWeeklyStats[$row->client_id][$weekNumber]['visits'] = $row->visit_count;
            }
        }

        // تشكيل النتائج النهائية
        $result = [];
        $totalClients = 0;

        foreach ($branches as $branch) {
            $branchData = [
                'id' => $branch->id,
                'name' => $branch->name,
                'groups' => []
            ];

            foreach ($branch->regionGroups as $group) {
                $groupClients = $group->neighborhoods
                    ->flatMap(fn($n) => $n->client ? [$n->client] : [])
                    ->filter()
                    ->unique('id');

                $groupData = [
                    'id' => $group->id,
                    'name' => $group->name,
                    'clients' => []
                ];

                foreach ($groupClients as $client) {
                    $totalClients++;

                    $clientData = [
                        'id' => $client->id,
                        'name' => $client->trade_name,
                        'code' => $client->code,
                        'neighborhood' => $client->neighborhood->name ?? null,
                        'status' => $client->status_client
                            ? ['name' => $client->status_client->name, 'color' => $client->status_client->color]
                            : ['name' => 'غير محدد', 'color' => '#6c757d'],
                        'weekly_data' => [],
                    ];

                    foreach ($weeks as $week) {
                        $weekNumber = $week['week_number'];

                        $clientData['weekly_data'][$weekNumber] = [
                            'collection' => $clientWeeklyStats[$client->id][$weekNumber]['collection'] ?? 0,
                            'visit_count' => $clientWeeklyStats[$client->id][$weekNumber]['visits'] ?? 0,
                            'invoices' => $client->invoices->whereBetween('created_at', [$week['start'], $week['end']])->values(),
                            'payments' => $client->payments->whereBetween('created_at', [$week['start'], $week['end']])->values(),
                            'visits' => $client->visits->whereBetween('created_at', [$week['start'], $week['end']])->values(),
                            'appointment_notes' => $client->appointmentNotes->whereBetween('created_at', [$week['start'], $week['end']])->values(),
                            'receipts' => $client->accounts
                                ->flatMap(fn($account) => $account->receipts->whereBetween('created_at', [$week['start'], $week['end']]))
                                ->values(),
                        ];
                    }

                    $groupData['clients'][] = $clientData;
                }

                $branchData['groups'][] = $groupData;
            }

            $result[] = $branchData;
        }

        // عمل Pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $paginator = new LengthAwarePaginator(
            collect($result)->forPage($page, $perPage)->values(),
            count($result),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $this->paginatedResponse(
            new \Modules\Api\Http\Resources\TrafficAnalysisCollectionResource($paginator),
            'تم جلب تحليل حركة العملاء بنجاح'
        );
    } catch (\Throwable $e) {
        return $this->errorResponse('حدث خطأ أثناء جلب البيانات', 500, $e->getMessage());
    }
}


private function fetchPaymentsPerWeek($weeks, $excludedInvoiceIds)
{
    $result = [];
    foreach ($weeks as $week) {
        $rows = DB::table('payments_process')
            ->join('invoices', 'payments_process.invoice_id', '=', 'invoices.id')
            ->where('invoices.type', 'normal')
            ->whereNotIn('invoices.id', $excludedInvoiceIds)
            ->whereBetween('payments_process.created_at', [$week['start'], $week['end']])
            ->select('invoices.client_id', DB::raw('SUM(payments_process.amount) as total'))
            ->groupBy('invoices.client_id')
            ->get();

        foreach ($rows as $row) {
            $result[$row->client_id][$week['week_number']] = $row->total;
        }
    }
    return $result;
}

private function fetchReceiptsPerWeek($weeks)
{
    $result = [];
    foreach ($weeks as $week) {
        $rows = DB::table('receipts')
            ->join('accounts', 'receipts.account_id', '=', 'accounts.id')
            ->whereBetween('receipts.created_at', [$week['start'], $week['end']])
            ->select('accounts.client_id', DB::raw('SUM(receipts.amount) as total'))
            ->groupBy('accounts.client_id')
            ->get();

        foreach ($rows as $row) {
            $result[$row->client_id][$week['week_number']] = $row->total;
        }
    }
    return $result;
}

private function fetchVisitsPerWeek($weeks)
{
    $result = [];
    foreach ($weeks as $week) {
        $rows = DB::table('visits')
            ->whereBetween('created_at', [$week['start'], $week['end']])
            ->selectRaw('client_id, COUNT(DISTINCT DATE_FORMAT(created_at, "%Y-%m-%d %H")) as visit_count')
            ->groupBy('client_id')
            ->get();

        foreach ($rows as $row) {
            $result[$row->client_id][$week['week_number']] = $row->visit_count;
        }
    }
    return $result;
}


public function generateYearWeeks( $currentYear = null)
{
     $currentYear =  $currentYear ?? now()->year;
    $start = Carbon::createFromDate( $currentYear, 1, 1)->startOfWeek();
    $end = Carbon::createFromDate( $currentYear, 12, 31)->endOfWeek();

    $weeks = [];
    $weekNumber = 1;
    while ($start->lte($end)) {
        $weeks[] = [
            'week_number' => $weekNumber,
            'start' => $start->copy(),
            'end' => $start->copy()->endOfWeek(),
        ];
        $start->addWeek();
        $weekNumber++;
    }

    return $weeks;
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('api::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('api::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('api::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
