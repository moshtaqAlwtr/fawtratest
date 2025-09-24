<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuoteItemResource extends JsonResource
{
 public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'quote_id'      => $this->quote_id,
            'product_id'    => $this->product_id,
            'item'          => $this->item,
            'description'   => $this->description,
            'quantity'      => $this->quantity,
            'unit_price'    => $this->unit_price,
            'discount'      => $this->discount,
            'discount_type' => $this->discount_type,
            'tax_1'         => $this->tax_1,
            'tax_2'         => $this->tax_2,
            'total'         => $this->total,
            'product'       => $this->whenLoaded('product', function () {
                return [
                    'id'       => $this->product->id,
                    'name'     => $this->product->name,
                    'barcode'  => $this->product->barcode,
                    'unit'     => $this->product->unit,
                ];
            }),
        ];
    }

}







