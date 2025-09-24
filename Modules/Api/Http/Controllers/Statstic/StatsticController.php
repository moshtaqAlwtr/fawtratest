<?php

namespace Modules\Api\Http\Controllers\Statstic;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Modules\Api\Http\Resources\Finance\ReceiptResource;
use App\Models\Expense;
use App\Traits\ApiResponseTrait;
use App\Models\ExpensesCategory;
use App\Models\User;
use App\Models\Log as ModelsLog;
use App\Models\AccountSetting;
use App\Models\Branch;
use App\Models\Client;
use App\Models\ClientEmployee;
use App\Models\Target;

use App\Models\Employee;
use App\Models\Revenue;
use App\Models\EmployeeClientVisit;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\notifications;
use App\Models\PaymentsProcess;
use App\Models\ReceiptCategory;
use App\Models\Receipt;
use Illuminate\Support\Arr;
use App\Models\TaxSitting;
use App\Models\Treasury;
use App\Models\TreasuryEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatsticController extends Controller
{
    use ApiResponseTrait;
   

public function index(Request $request)
{
    $month = $request->input('month', now()->format('Y-m'));
    [$year, $monthNum] = explode('-', $month);

    // الفواتير المرتجعة
    $returnedInvoiceIds = Invoice::whereNotNull('reference_number')->pluck('reference_number');
    $returnedIds = Invoice::where('type', 'returned')->pluck('id');
    $excludedInvoiceIds = $returnedInvoiceIds->merge($returnedIds)->unique();

    // الفواتير الصالحة
    $validInvoices = Invoice::where('type', 'normal')
        ->whereNotIn('id', $excludedInvoiceIds)
        ->get(['id', 'client_id', 'grand_total', 'created_by']);

    $totalSales = $validInvoices->sum('grand_total');

    // المدفوعات
    $payments = PaymentsProcess::whereIn('invoice_id', $validInvoices->pluck('id'))->get(['id', 'amount', 'invoice_id', 'employee_id', 'created_at']);
    $totalPayment = $payments->sum('amount');

    // السندات
    $receipts = Receipt::with('account')->whereHas('account')->get(['id', 'amount', 'account_id', 'created_by', 'created_at']);
    $totalReceipts = $receipts->sum('amount');

    // العملاء المرتبطين بفروع وأحياء
    $clients = Client::with(['branch', 'Neighborhoodname.Region'])->whereNotNull('branch_id')->get();

    // تجميعات
    $invoiceByClient = $validInvoices->groupBy('client_id');
    $paymentByInvoice = $payments->groupBy('invoice_id');
    $receiptByClient = $receipts->groupBy(fn($r) => optional($r->account)->client_id);

    // أداء الموظفين بالنسبة للمبيعات
    $employeesSales = $validInvoices->groupBy('created_by')->map(fn($group, $id) => [
        'user_id' => $id,
        'sales' => $group->sum('grand_total')
    ]);

    $chartData = $employeesSales->map(function ($data) use ($totalSales) {
        $user = User::find($data['user_id']);
        return [
            'name' => $user?->name ?? 'غير معروف',
            'sales' => $data['sales'],
            'percentage' => ($totalSales > 0) ? round(($data['sales'] / $totalSales) * 100, 2) : 0
        ];
    })->values();

    // أداء الفروع
    $branchesPerformance = $clients->groupBy('branch_id')->map(function ($group, $branchId) use ($invoiceByClient, $paymentByInvoice, $receiptByClient) {
        $branchName = optional($group->first()->branch)->name ?? 'غير معروف';
        $payments = 0;
        $receipts = 0;

        foreach ($group as $client) {
            $invoiceIds = collect($invoiceByClient->get($client->id, []))->pluck('id');
            $payments += $invoiceIds->flatMap(fn($id) => collect($paymentByInvoice->get($id, [])))->sum('amount');
            $receipts += collect($receiptByClient->get($client->id, []))->sum('amount');
        }

        return (object)[
            'branch_id' => $branchId,
            'branch_name' => $branchName,
            'total_collected' => $payments + $receipts,
            'payments' => $payments,
            'receipts' => $receipts,
        ];
    })->sortByDesc('total_collected')->values();

    // أداء المناطق
    $regionPerformance = $clients->groupBy(fn($client) => optional(optional($client->Neighborhoodname)->Region)->name ?? 'غير معروف')
        ->map(function ($group, $regionName) use ($invoiceByClient, $paymentByInvoice, $receiptByClient) {
            $payments = 0;
            $receipts = 0;

            foreach ($group as $client) {
                $invoiceIds = collect($invoiceByClient->get($client->id, []))->pluck('id');
                $payments += $invoiceIds->flatMap(fn($id) => collect($paymentByInvoice->get($id, [])))->sum('amount');
                $receipts += collect($receiptByClient->get($client->id, []))->sum('amount');
            }

            return (object)[
                'region_name' => $regionName,
                'total_collected' => $payments + $receipts,
                'payments' => $payments,
                'receipts' => $receipts,
            ];
        })->sortByDesc('total_collected')->values();

    // أداء الأحياء
    $neighborhoodPerformance = $clients->groupBy(fn($client) => optional($client->Neighborhoodname)->name ?? 'غير معروف')
        ->map(function ($group, $neighName) use ($invoiceByClient, $paymentByInvoice, $receiptByClient) {
            $payments = 0;
            $receipts = 0;

            foreach ($group as $client) {
                $invoiceIds = collect($invoiceByClient->get($client->id, []))->pluck('id');
                $payments += $invoiceIds->flatMap(fn($id) => collect($paymentByInvoice->get($id, [])))->sum('amount');
                $receipts += collect($receiptByClient->get($client->id, []))->sum('amount');
            }

            return (object)[
                'neighborhood_name' => $neighName,
                'total_collected' => $payments + $receipts,
                'payments' => $payments,
                'receipts' => $receipts,
            ];
        })->sortByDesc('total_collected')->values();

    // إحصائيات أداء الموظفين مقارنة بالهدف الشهري
    $defaultTarget = Target::find(1)?->value ?? 35000;
    $employeeIds = $validInvoices->pluck('created_by')->unique();

    $employeeStats = $employeeIds->map(function ($userId) use ($defaultTarget, $monthNum, $year, $excludedInvoiceIds) {
        $user = User::find($userId);
        $invoiceIds = Invoice::where('created_by', $userId)
            ->where('type', 'normal')
            ->whereNotIn('id', $excludedInvoiceIds)
            ->pluck('id');

        $payments = PaymentsProcess::with('invoice')
            ->whereMonth('created_at', $monthNum)
            ->whereYear('created_at', $year)
            ->whereHas('invoice', function ($q) use ($excludedInvoiceIds) {
                $q->where('type', 'normal')->whereNotIn('id', $excludedInvoiceIds);
            })
            ->get();

        $paymentsTotal = $payments->filter(function ($payment) use ($userId, $invoiceIds) {
            return $payment->employee_id == $userId || $invoiceIds->contains($payment->invoice_id);
        })->sum('amount');

        $receiptsTotal = Receipt::where('created_by', $userId)
            ->whereMonth('created_at', $monthNum)
            ->whereYear('created_at', $year)
            ->sum('amount');

        $total = $paymentsTotal + $receiptsTotal;
        $target = $user?->target?->monthly_target ?? $defaultTarget;
        $percentage = $target > 0 ? round(($total / $target) * 100, 2) : 0;

        $clientsCount = $user?->employee_id ? ClientEmployee::where('employee_id', $user->employee_id)->count() : 0;

        return [
            'name' => $user?->name ?? 'غير معروف',
            'payments' => $paymentsTotal,
            'receipts' => $receiptsTotal,
            'total' => $total,
            'target' => $target,
            'percentage' => $percentage,
            'clients_count' => $clientsCount,
        ];
    })->sortByDesc('total')->values();

    return response()->json([
        'status' => true,
        'data' => [
            'TotalSales' => $totalSales,
            'TotalPayment' => $totalPayment,
            'TotalReceipts' => $totalReceipts,
            'ChartData' => $chartData,
            'branches_top_3' => $branchesPerformance->take(3)->values(),
            'regions_top_3' => $regionPerformance->take(3)->values(),
            'neighborhoods_top_3' => $neighborhoodPerformance->take(3)->values(),
            'employees_stats' => $employeeStats,
        ]
    ]);
}




}




















