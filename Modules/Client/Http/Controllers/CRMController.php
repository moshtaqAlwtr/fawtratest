<?php

namespace Modules\Client\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientRelation;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\Region_groub;


class CRMController extends Controller
{

    public function mang_client(Request $request)
    {
        $clientGroups=Region_groub::all();
        
        // جلب الفواتير مع علاقاتها
        $invoices = Invoice::with([
            'client', 
            'employee', 
            'payments', 
            'treasury',
            'items.product'
        ])->get();
        
        // جلب جميع الملاحظات
        $notes=ClientRelation::all();

        // جلب العملاء مع علاقاتهم
        $clients = Client::with([
            'invoices' => function($query) {
                $query->with(['employee', 'payments', 'treasury', 'items.product']);
            },
            'appointmentNotes.employee',
            'clientRelations' => function ($query) {
                $query->with(['employee', 'location'])->orderBy('date', 'desc');
            },
        ])
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->full_name,
                    'phone' => $client->phone,
                    'balance' => $client->balance,
                    'invoices' => $client->invoices->map(function ($invoice) {
                        return [
                            'id' => $invoice->id,
                            'number' => $invoice->code,
                            'date' => $invoice->invoice_date->format('Y-m-d'),
                            'issue_date' => $invoice->issue_date ? $invoice->issue_date->format('Y-m-d') : null,
                            'amount' => $invoice->grand_total,
                            'subtotal' => $invoice->subtotal,
                            'tax_total' => $invoice->tax_total,
                            'discount_amount' => $invoice->discount_amount,
                            'status' => $invoice->payment_status,
                            'remaining' => $invoice->remaining_amount,
                            'paymentMethod' => $invoice->payment_method,
                            'employee' => $invoice->employee->name ?? 'غير محدد',
                            'treasury' => $invoice->treasury->name ?? 'غير محدد',
                            'currency' => $invoice->currency,
                            'notes' => $invoice->notes,
                            'is_paid' => $invoice->is_paid,
                            'payment_terms' => $invoice->payment_terms,
                            'reference_number' => $invoice->reference_number,
                            'type' => $invoice->type,
                            'items_count' => $invoice->items->count(),
                            'total_payments' => $invoice->payments->sum('amount'),
                            'created_at' => $invoice->created_at->format('Y-m-d H:i'),
                            'updated_at' => $invoice->updated_at->format('Y-m-d H:i'),
                        ];
                    }),
                    'appointmentNotes' => $client->appointmentNotes->map(function ($note) {
                        return [
                            'id' => $note->id,
                            'date' => $note->date,
                            'employee' => $note->employee->name ?? 'غير محدد',
                            'content' => $note->description,
                            'status' => $note->status,
                        ];
                    }),
                    // ملاحظات العلاقات (ClientRelation)
                    'clientRelations' => $client->clientRelations->map(function ($relation) {
                        return [
                            'id' => $relation->id,
                            'description' => $relation->description,
                            'process' => $relation->process,
                            'date' => $relation->date,
                            'time' => $relation->time,
                            'employee' => $relation->employee->name ?? 'غير محدد',
                            'created_at' => $relation->created_at,
                            'additional_data' => $relation->additional_data,
                            'site_type' => $relation->site_type_text,
                            'status' => $relation->status,
                            'site_type_raw' => $relation->site_type,
                            'competitor_documents' => $relation->competitor_documents,
                        ];
                    }),
                ];
            });

        return view('client::relestion_mang_client', [
            'clients' => $clients,
            'invoices'=>$invoices,
            'notes'=>$notes,
            'clientGroups'=>$clientGroups
        ]);
    }
}
