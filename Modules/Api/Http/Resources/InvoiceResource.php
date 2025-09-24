<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Api\Http\Resources\ClientResource;

class InvoiceResource extends JsonResource
{
public function toArray($request)
{
   return [
    'id' => $this->id,
    'invoice_number' => $this->id, // يفضل عرض code إن وجد
    'created_at' => $this->created_at->toIso8601String(),

    'client' => [
        'id' => $this->client_id,
        'name' => optional($this->client)->trade_name,
        'tax_number' => optional($this->client)->tax_number,
        'address' => optional($this->client)->full_address,
        'phone' => optional($this->client)->phone,
    ],

    'created_by' => optional($this->createdByUser)->name,
    'type' => $this->type,

    'payment_status' => (int) $this->payment_status,
    'payment_status_label' => match ((int) $this->payment_status) {
        1 => 'مدفوعة بالكامل',
        2 => 'مدفوعة جزئياً',
        3 => 'غير مدفوعة',
        4 => 'مستلمة',
        default => 'غير معروفة',
    },

    'payments_count' => $this->payments()->count(),
    'grand_total' => round((float) $this->grand_total, 2),
    'due_value' => round((float) $this->due_value, 2),
    'currency' => $this->currency ?? 'SAR', // اجعلها ديناميكية إن وُجدت
];

}


}
