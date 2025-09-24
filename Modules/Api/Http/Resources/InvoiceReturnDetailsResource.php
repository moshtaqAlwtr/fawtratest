<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Api\Http\Resources\ClientResource;

class InvoiceReturnDetailsResource extends JsonResource
{
public function toArray($request)
    {
        $client = $this->client;

        return [
            'id' => $this->id,
            'formatted_number' => str_pad($this->id, 5, '0', STR_PAD_LEFT),
            'date' => $this->created_at->format('Y/m/d'),
            'client' => [
                'name' => $client
                    ? ($client->trade_name ?: $client->first_name . ' ' . $client->last_name)
                    : 'عميل غير معروف',
                'mobile' => $client->mobile ?? 'غير متوفر',
                'tax_number' => $client->tax_number ?? 'غير متوفر',
            ],
            'original_invoice_id' => $this->reference_number ?? $this->id,
            'return_reason' => $this->return_reason ?? 'لم يتم تحديد سبب',
            'currency' => $this->account_setting->currency ?? 'SAR',
            'items' => $this->items->map(function ($item, $index) {
                return [
                    'index' => $index + 1,
                    'name' => $item->item,
                    'quantity' => $item->quantity,
                    'unit_price' => number_format($item->unit_price, 2),
                    'total' => number_format($item->total, 2),
                ];
            }),
            'totals' => [
                'grand_total' => number_format($this->grand_total ?? 0, 2),
                'subtotal' => number_format($this->subtotal ?? 0, 2),
                'tax_total' => number_format($this->tax_total ?? 0, 2),
                'discount' => number_format($this->total_discount ?? 0, 2),
            ],
        ];
    }


}



















