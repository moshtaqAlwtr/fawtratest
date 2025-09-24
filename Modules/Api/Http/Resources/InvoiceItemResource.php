<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'item' => $this->item,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'tax_1' => $this->tax_1,
            'tax_2' => $this->tax_2,
            'type' => $this->type,
            'total' => $this->unit_price * $this->quantity - $this->discount,
             'barcode' => $this->product?->barcode,
                'unit' => $this->product?->unit,
        ];
    }
}
