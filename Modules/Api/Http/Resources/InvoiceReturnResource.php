<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Api\Http\Resources\ClientResource;

class InvoiceReturnResource extends JsonResource
{
  public function toArray($request)
    {
        $client = $this->client;

        return [
            'id' => $this->id,
            'invoice_number' => '#' . $this->id,
            'client_name' => $client
                ? ($client->trade_name ?: $client->first_name . ' ' . $client->last_name)
                : 'عميل غير معروف',
            'tax_number' => $client->tax_number ?? '-',
            'address' => $client->full_address ?? '-',
            'created_at' => $this->created_at->format('H:i:s d/m/Y'),
            'reference_number' => $this->reference_number ?? '--',
            'grand_total' => number_format($this->grand_total ?? $this->total, 2),
            'currency' => $this->currency ?? 'SAR',
            'created_by' => optional($this->createdByUser)->name ?? 'غير محدد',

            
        ];
    }


}




















