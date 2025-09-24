<?php

namespace Modules\Api\Services;

use App\Models\{Invoice, PaymentsProcess, Receipt, Account, Target};
use Carbon\Carbon;

class ClientAnalyticsService
{
    public static function summarize($clients)
    {
        $clientIds = $clients->pluck('id');
        $currentYear = now()->year;
        $monthlyTarget = Target::find(2)->value ?? 648;

        $returnedInvoiceIds = Invoice::whereNotNull('reference_number')->pluck('reference_number')->toArray();
        $excludedInvoiceIds = array_unique(array_merge($returnedInvoiceIds, Invoice::where('type', 'returned')->pluck('id')->toArray()));

        $invoices = Invoice::whereIn('client_id', $clientIds)
            ->where('type', 'normal')
            ->whereNotIn('id', $excludedInvoiceIds)
            ->get();

        $invoiceIdsByClient = $invoices->groupBy('client_id')->map->pluck('id');
        $payments = PaymentsProcess::whereIn('invoice_id', $invoices->pluck('id'))
            ->whereYear('created_at', $currentYear)
            ->get()
            ->groupBy('invoice_id');

        $receipts = Receipt::with('account')
            ->whereHas('account', fn($q) => $q->whereIn('client_id', $clientIds))
            ->whereYear('created_at', $currentYear)
            ->get()
            ->groupBy(fn($receipt) => $receipt->account->client_id);

        $months = ['يناير' => 1, 'فبراير' => 2, 'مارس' => 3, 'أبريل' => 4, 'مايو' => 5, 'يونيو' => 6, 'يوليو' => 7, 'أغسطس' => 8, 'سبتمبر' => 9, 'أكتوبر' => 10, 'نوفمبر' => 11, 'ديسمبر' => 12];

        $getClassification = function ($percentage, $collected = 0) {
            if ($collected == 0) return ['group' => 'D', 'class' => 'secondary'];
            if ($percentage > 100) return ['group' => 'A++', 'class' => 'primary'];
            if ($percentage >= 60) return ['group' => 'A', 'class' => 'success'];
            if ($percentage >= 30) return ['group' => 'B', 'class' => 'warning'];
            return ['group' => 'C', 'class' => 'danger'];
        };

        $summary = [];

        foreach ($clients as $client) {
            $invoiceIds = $invoiceIdsByClient[$client->id] ?? collect();
            $monthly = [];
            $totalYearlyCollected = 0;

            foreach ($months as $monthName => $monthNumber) {
                $paymentsTotal = 0;
                foreach ($invoiceIds as $invoiceId) {
                    if (isset($payments[$invoiceId])) {
                        $paymentsTotal += $payments[$invoiceId]
                            ->filter(fn($p) => Carbon::parse($p->created_at)->month == $monthNumber)
                            ->sum('amount');
                    }
                }

                $receiptsTotal = isset($receipts[$client->id])
                    ? $receipts[$client->id]->filter(fn($r) => Carbon::parse($r->created_at)->month == $monthNumber)->sum('amount')
                    : 0;

                $monthlyCollected = $paymentsTotal + $receiptsTotal;
                $totalYearlyCollected += $monthlyCollected;

                $percentage = $monthlyTarget > 0 ? round(($monthlyCollected / $monthlyTarget) * 100, 2) : 0;
                $classification = $getClassification($percentage, $monthlyCollected);

                $monthly[$monthName] = [
                    'collected' => $monthlyCollected,
                    'payments_total' => $paymentsTotal,
                    'receipts_total' => $receiptsTotal,
                    'target' => $monthlyTarget,
                    'percentage' => $percentage,
                    'group' => $classification['group'],
                    'group_class' => $classification['class'],
                    'month_number' => $monthNumber,
                ];
            }

            $summary[$client->id] = [
                'id' => $client->id,
                'monthly' => $monthly,
                'total_collected' => $totalYearlyCollected,
                'classification' => $getClassification(
                    $monthlyTarget > 0 ? round(($totalYearlyCollected / ($monthlyTarget * 12)) * 100, 2) : 0,
                    $totalYearlyCollected
                ),
            ];
        }

        return $summary;
    }
}
