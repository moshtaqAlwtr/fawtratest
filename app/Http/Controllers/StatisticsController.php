<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
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
use Carbon\Carbon;
use DB;



class StatisticsController extends Controller

{
    public function index()
    {

    }

    public function StatisticsGroup(Request $request)
    {
        $returnedInvoiceIds = Invoice::whereNotNull('reference_number')
                ->pluck('reference_number')
                ->toArray();

            // الفواتير الأصلية التي يجب استبعادها = كل فاتورة تم عمل راجع لها
            // بالإضافة إلى الفواتير التي تم تصنيفها صراحةً على أنها راجعة
            $excludedInvoiceIds = array_unique(array_merge(
                $returnedInvoiceIds,
                Invoice::where('type', 'returned')->pluck('id')->toArray()
            ));


 $branchesPerformance = Client::with('branch')
    ->whereNotNull('branch_id')
    ->get()
    ->groupBy('branch_id')
    ->map(function (Collection $clientsInBranch) use ($excludedInvoiceIds,$request) {
        $branchName = optional($clientsInBranch->first()->branch)->name ?? 'غير معروف';

        $totalPayments = 0;
        $totalReceipts = 0;
$dateFrom = $request->input('date_from');
$dateTo = $request->input('date_to');
        foreach ($clientsInBranch as $client) {
            $invoiceIds = Invoice::where('client_id', $client->id)
                ->where('type', 'normal')
                ->whereNotIn('id', $excludedInvoiceIds)
                ->pluck('id');

            

$payments = PaymentsProcess::whereIn('invoice_id', $invoiceIds)
    ->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo]))
    ->sum('amount');

            $receipts = Receipt::whereHas('account', function ($q) use ($client) {
                $q->where('client_id', $client->id);
            })->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo]))->sum('amount');

            $totalPayments += $payments;
            $totalReceipts += $receipts;
        }

        return (object)[
            'branch_id' => $clientsInBranch->first()->branch_id,
            'branch_name' => $branchName,
            'total_collected' => $totalPayments + $totalReceipts,
            'payments' => $totalPayments,
            'receipts' => $totalReceipts,
        ];
    })->sortByDesc('total_collected')->values();
    
    return view('Statistics.Branches', compact('branchesPerformance'));
    
    }
    
public function Group(Request $request)
{
    $year = $request->input('year', now()->year);

    // استبعاد الفواتير المرجعة
    $returnedInvoiceIds = Invoice::whereNotNull('reference_number')->pluck('reference_number')->toArray();
    $excludedInvoiceIds = array_unique(array_merge(
        $returnedInvoiceIds,
        Invoice::where('type', 'returned')->pluck('id')->toArray()
    ));

    $regionPerformance = Client::with('Neighborhoodname.Region')
        ->whereHas('Neighborhoodname.Region')
        ->get()
        ->groupBy(function ($client) {
            return $client->Neighborhoodname->Region->name ?? 'غير معروف';
        })
        ->map(function ($clientsInRegion, $regionName) use ($excludedInvoiceIds, $year) {
            $monthlyTotals = array_fill(1, 12, 0); // من 1 إلى 12

            foreach ($clientsInRegion as $client) {
                $invoiceIds = Invoice::where('client_id', $client->id)
                    ->where('type', 'normal')
                    ->whereNotIn('id', $excludedInvoiceIds)
                    ->pluck('id');

                for ($month = 1; $month <= 12; $month++) {
                    $payments = PaymentsProcess::whereIn('invoice_id', $invoiceIds)
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->sum('amount');

                    $receipts = Receipt::whereHas('account', function ($q) use ($client) {
                        $q->where('client_id', $client->id);
                    })
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->sum('amount');

                    $monthlyTotals[$month] += ($payments + $receipts);
                }
            }

            return (object)[
                'region_name' => $regionName,
                'monthly' => $monthlyTotals,
                'total_collected' => array_sum($monthlyTotals),
            ];
        })
        ->sortByDesc('total_collected')
        ->values();

    // لحساب الإجمالي لكل شهر عبر كل المناطق
    $monthlyTotals = array_fill(1, 12, 0);
    foreach ($regionPerformance as $region) {
        foreach ($region->monthly as $month => $value) {
            $monthlyTotals[$month] += $value;
        }
    }

    return view('Statistics.Group', compact('regionPerformance', 'monthlyTotals', 'year'));
}


    
    public function neighborhood (Request $request)
    {
         $returnedInvoiceIds = Invoice::whereNotNull('reference_number')
                ->pluck('reference_number')
                ->toArray();

            // الفواتير الأصلية التي يجب استبعادها = كل فاتورة تم عمل راجع لها
            // بالإضافة إلى الفواتير التي تم تصنيفها صراحةً على أنها راجعة
            $excludedInvoiceIds = array_unique(array_merge(
                $returnedInvoiceIds,
                Invoice::where('type', 'returned')->pluck('id')->toArray()
            ));
            
         $neighborhoodPerformance = Client::with(['Neighborhoodname'])
    ->whereHas('Neighborhoodname')
    ->get()
    ->groupBy(fn($client) => $client->Neighborhoodname->name ?? 'غير معروف')
    ->map(function ($clientsInNeighborhood, $neighborhoodName) use ($excludedInvoiceIds,$request) {
        $totalPayments = 0;
        $totalReceipts = 0;

$dateFrom = $request->input('date_from');
$dateTo = $request->input('date_to');

        foreach ($clientsInNeighborhood as $client) {
            $invoiceIds = Invoice::where('client_id', $client->id)
                ->where('type', 'normal')
                ->whereNotIn('id', $excludedInvoiceIds)
                ->pluck('id');

            $payments = PaymentsProcess::whereIn('invoice_id', $invoiceIds)->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo]))->sum('amount');

            $receipts = Receipt::whereHas('account', function ($q) use ($client) {
                $q->where('client_id', $client->id);
            })->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo]))->sum('amount');

            $totalPayments += $payments;
            $totalReceipts += $receipts;
        }

        $totalCollected = $totalPayments + $totalReceipts;

        return (object)[
            'neighborhood_name' => $neighborhoodName,
            'total_collected' => $totalCollected,
            'payments' => $totalPayments,
            'receipts' => $totalReceipts,
        ];
    })
    ->sortByDesc('total_collected') // ✅ الأفضل أولاً
    ->values();
    
     return view('Statistics.Neighborhoods', compact('neighborhoodPerformance'));
    }
    
    
}
