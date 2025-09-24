<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Api\Http\Resources\ClientResource;
use Modules\Api\Http\Resources\InvoiceItemResource;

class InvoiceFullResource extends JsonResource
{
   public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'created_at' => $this->invoice_date,
            'sub_total' => $this->sub_total,
            'tax_total' => $this->tax_total,
            'discount_amount' => $this->discount_amount,
            'grand_total' => $this->grand_total,
             'payments_count' => $this->payments()->count(),
    'grand_total' => round((float) $this->grand_total, 2),
    'due_value' => round((float) $this->due_value, 2),
    'currency' => $this->currency ?? 'SAR', // اجعلها ديناميكية إن و 
    
    
      'payment_status' => (int) $this->payment_status,
    'payment_status_label' => match ((int) $this->payment_status) {
        1 => 'مدفوعة بالكامل',
        2 => 'مدفوعة جزئياً',
        3 => 'غير مدفوعة',
        4 => 'مستلمة',
        default => 'غير معروفة',
    },
     'created_by' => optional($this->createdByUser)->name,
    'type' => $this->type,
            'client' => new ClientResource($this->client),
            'items' => InvoiceItemResource::collection($this->items),
            'notes' => $this->relations['notes'] ?? [],
            'returns' => $this->relations['returns'] ?? [],
            // 'taxes' => $this->relations['taxes'] ?? [],
            'logs' => $this->relations['logs'] ?? [],
            'qrcode' => $this->qrcode, // نص فقط بدون تحويل لصورة


        ];
    }

 

}

