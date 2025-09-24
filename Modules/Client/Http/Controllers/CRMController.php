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

    // ... existing methods ...
public function mang_client(Request $request)
    {
        $clientGroups=Region_groub::all();
        $invoices=Invoice::all();
        $notes=ClientRelation::all();
        $clients = Client::with([
            'invoices',
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
                            'amount' => $invoice->grand_total,
                            'status' => $invoice->payment_status,
                            'remaining' => $invoice->remaining_amount,
                            'paymentMethod' => $invoice->payment_method,
                        ];
                    }),
                    'notes' => $client->appointmentNotes->map(function ($note) {
                        return [
                            'id' => $note->id,
                            'date' => $note->date,
                            'employee' => $note->employee->name ?? 'غير محدد',
                            'content' => $note->description,
                            'status' => $note->status,
                        ];
                    }),
                    'relations' => $client->clientRelations->map(function ($relation) {
                        return [
                            'id' => $relation->id,
                            'status' => $relation->status,
                            'process' => $relation->process,
                            'time' => $relation->time,
                            'date' => $relation->date,
                            'employee' => $relation->employee->name ?? 'غير محدد',
                            'description' => $relation->description,
                            'location' => $relation->location
                                ? [
                                    'id' => $relation->location->id,
                                    'address' => $relation->location->address,
                                    'coordinates' => $relation->location->coordinates,
                                ]
                                : null,
                            'site_type' => $relation->site_type,
                            'competitor_documents' => $relation->competitor_documents,
                            'additional_data' => $relation->additional_data,
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
