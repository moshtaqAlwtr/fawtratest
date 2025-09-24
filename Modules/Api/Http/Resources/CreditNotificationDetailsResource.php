<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditNotificationDetailsResource extends JsonResource
{
  public function toArray($request)
    {
        $currency = $this->account_setting->currency ?? 'SAR';

        return [
            'id' => $this->id,
            'credit_number' => $this->id,
            'credit_date' => $this->created_at ? $this->created_at->format('Y-m-d') : null,

            'client' => [
                'id' => $this->client_id,
                'name' => $this->client?->trade_name ?: $this->client?->first_name . ' ' . $this->client?->last_name,
                'tax_number' => $this->client?->tax_number,
                'mobile' => $this->client?->mobile,
                'address' => $this->client?->full_address,
            ],
            'items' => $this->items->map(function ($item) {
                return [
                    'item' => $item->item,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => number_format($item->unit_price, 2),
                    'discount' => number_format($item->discount, 2),
                    'total' => number_format($item->total, 2),
                ];
            }),
            'subtotal' => number_format($this->subtotal ?? 0, 2),
            'total_discount' => number_format($this->total_discount ?? 0, 2),
            'taxes' => $this->whenLoaded('taxes', function () {
                return $this->taxes->map(function ($tax) {
                    return [
                        'name' => $tax->name,
                        'rate' => $tax->rate,
                        'value' => number_format($tax->value, 2)
                    ];
                });
            }),
            'grand_total' => number_format($this->grand_total ?? 0, 2),
            'notes' => $this->notes,
            'status' => $this->status,
            'created_by' => $this->createdBy?->name,
        ];
    }


}




















