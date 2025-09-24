<?php

namespace Modules\Api\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
{
   public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'unit_price' => $this->sale_price,
        ];
    }
}








