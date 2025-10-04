<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ClientRelation;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class AboFalehController extends Controller
{
    public function reportTrac(Request $request)
    {
        $allUsers = User::whereIn('role', ['employee', 'manager'])->get();

        $userId = $request->input('user_id', auth()->id());
        $user = User::find($userId);

        if (!$user || !$user->employee) {
            return view('dashboard.abo_faleh.reportTrack', [
                'user' => $user,
                'all' => collect(),
                'from' => now()->subDays(7)->startOfDay(),
                'to' => now()->endOfDay(),
                'allUsers' => $allUsers,
            ]);
        }

        $employee = $user;

        $fromDate = $request->input('from_date', now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();

        $notesRaw = ClientRelation::where('employee_id', $employee->id)
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $notesMap = collect();

        foreach ($notesRaw as $note) {
            $key = $note->client_id . '_' . Carbon::parse($note->created_at)->format('Y-m-d');
            if (!$notesMap->has($key)) {
                $notesMap->put($key, $note);
            }
        }

        $visits = $employee->visits()
            ->whereBetween('arrival_time', [$from, $to])
            ->get()
            ->map(function ($v) use ($notesMap) {
                $clientId = $v->client_id;
                $date = optional($v->arrival_time)->format('Y-m-d');
                $key = $clientId . '_' . $date;

                $note = $notesMap->get($key);
                $description_note = $note ? $note->description : '--';
                if ($note)
                    $notesMap->forget($key);

                return [
                    'type' => 'زيارة',
                    'group' => $v->client->group->name ?? '--',
                    'client' => $v->client->trade_name ?? '--',
                    'arrival' => optional($v->arrival_time)->format('H:i'),
                    'departure' => optional($v->departure_time)->format('H:i'),
                    'date' => $date,
                    'receipt' => '--',
                    'payment' => '--',
                    'invoice' => '--',
                    'credit_note' => '--',
                    'description_visit' => $v->notes ?? '--',
                    'description_note' => $description_note,
                ];
            });

        $invoices = $employee->invoices()
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->map(function ($i) use ($notesMap) {
                $clientId = $i->client_id;
                $date = optional($i->created_at)->format('Y-m-d');
                $key = $clientId . '_' . $date;

                $note = $notesMap->get($key);
                $description_note = $note ? $note->description : '--';
                if ($note)
                    $notesMap->forget($key);

                return [
                    'type' => $i->type == 'returned' ? 'فاتورة مرتجعة' : 'فاتورة',
                    'group' => $i->client->group->name ?? '--',
                    'client' => $i->client->trade_name ?? '--',
                    'arrival' => optional($i->created_at)->format('H:i'),
                    'departure' => '--',
                    'date' => $date,
                    'receipt' => '--',
                    'payment' => '--',
                    'invoice' => $i->type == 'returned' ? '--' : number_format($i->grand_total, 2),
                    'credit_note' => $i->type == 'returned' ? number_format($i->grand_total, 2) : '--',
                    'description_visit' => '',
                    'description_note' => $description_note,
                ];
            });

        $payments = $employee->payments()
            ->whereBetween('payment_date', [$from, $to])
            ->get()
            ->map(function ($p) use ($notesMap) {
                $clientId = optional($p->invoice)->client_id;
                $date = optional($p->payment_date)->format('Y-m-d');
                $key = $clientId . '_' . $date;

                $note = $notesMap->get($key);
                $description_note = $note ? $note->description : '--';
                if ($note)
                    $notesMap->forget($key);

                return [
                    'type' => 'مدفوع',
                    'group' => optional($p->invoice->client)->group->name ?? '--',
                    'client' => optional($p->invoice->client)->trade_name ?? '--',
                    'arrival' => optional($p->payment_date)->format('H:i'),
                    'departure' => '--',
                    'date' => $date,
                    'receipt' => '--',
                    'payment' => number_format($p->amount, 2),
                    'invoice' => '--',
                    'credit_note' => '--',
                    'description_visit' => '',
                    'description_note' => $description_note,
                ];
            });

        $receipts = $employee->receipts()
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->map(function ($r) use ($notesMap) {
                $clientId = optional($r->account)->id;
                $date = optional($r->created_at)->format('Y-m-d');
                $key = $clientId . '_' . $date;

                $note = $notesMap->get($key);
                $description_note = $note ? $note->description : '--';
                if ($note)
                    $notesMap->forget($key);

                return [
                    'type' => 'سند قبض',
                    'group' => optional($r->account->group)->name ?? '--',
                    'client' => optional($r->account)->name ?? '--',
                    'arrival' => optional($r->created_at)->format('H:i'),
                    'departure' => '--',
                    'date' => $date,
                    'receipt' => number_format($r->amount, 2),
                    'payment' => '--',
                    'invoice' => '--',
                    'credit_note' => '--',
                    'description_visit' => '',
                    'description_note' => $description_note,
                ];
            });

        $expenses = $employee->expenses()
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->map(function ($e) {
                return [
                    'type' => 'سند صرف',
                    'group' => '--',
                    'client' => $e->name,
                    'arrival' => optional($e->created_at)->format('H:i'),
                    'departure' => '--',
                    'date' => optional($e->created_at)->format('Y-m-d'),
                    'receipt' => '--',
                    'payment' => '--',
                    'invoice' => '--',
                    'credit_note' => '--',
                    'expense' => number_format($e->amount, 2),
                    'description_note' => $e->description ?? '--',
                ];
            });

        $all = collect()
            ->merge($visits)
            ->merge($invoices)
            ->merge($payments)
            ->merge($receipts)
            ->merge($expenses)
            ->sortByDesc(fn($row) => $row['date'] . ' ' . ($row['arrival'] ?? '00:00'));

        return view('dashboard.ABO_FALEH.reportTrack', compact('user', 'all', 'from', 'to', 'allUsers'));
    }

public function index(Request $request)
{
    $users = User::withCount([
            'visits',
            'notes',
        ])
        ->whereIn('role', ['employee', 'manager'])
        ->get();

    foreach ($users as $user) {
        // 1. حساب مجموع النفقات (Expenses)
        $user->expenses_sum = optional(optional($user->employee)->expenses)->sum('amount') ?? 0;

        // 2. دمج سندات القبض والمدفوعات معاً
        $receiptsSum = $user->receipts()->sum('amount') ?? 0;
        $paymentsSum = $user->payments()->sum('amount') ?? 0;
        $user->payments_receipts_sum = $receiptsSum + $paymentsSum;
        $user->receipts_sum = $receiptsSum;
        $user->payments_sum = $paymentsSum;

        // 3. جلب الفواتير المدفوعة (استبعاد التي لديها مرتجعات)
        $paidInvoicesQuery = $user->invoices()
            ->where('is_paid', true)
            ->where(function($q) {
                // استبعاد الفواتير الأصلية التي لديها مرتجعات
                $q->whereNull('type')
                  ->whereDoesntHave('referenceInvoice', function($subQ) {
                      $subQ->where('type', 'return');
                  });
            })
            ->orWhere(function($q) use ($user) {
                // أو الفواتير من نوع return (المرتجعات نفسها)
                $q->where('type', 'return')
                  ->where('employee_id', $user->id); // تأكد من الربط بالموظف
            });

        $user->paid_invoices_count = $paidInvoicesQuery->count();
        $user->paid_invoices_sum = $paidInvoicesQuery->sum('grand_total'); // ← هذا السطر كان ناقص

        // 4. الفواتير غير المدفوعة (بدون مدفوعات) - استبعاد المرتجعات
        $unpaidInvoicesQuery = $user->invoices()
            ->where('is_paid', false)
            ->whereDoesntHave('payments')
            ->where(function($q) {
                $q->whereNull('type')
                  ->orWhere('type', '!=', 'return');
            });

        $user->unpaid_invoices_count = $unpaidInvoicesQuery->count();
        $user->unpaid_invoices_sum = $unpaidInvoicesQuery->sum('grand_total');
    }

    return view('dashboard.abo_faleh.index', [
        'employees' => $users,
        'request' => $request,
    ]);
}
}
