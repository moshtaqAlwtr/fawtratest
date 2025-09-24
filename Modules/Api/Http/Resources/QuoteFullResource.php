<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuoteFullResource extends JsonResource
{
 public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'code'        => $this->code,
            'quote_date'  => $this->created_at->toDateString(),
            'status'      => $this->status,
            'notes'       => $this->notes,
            'currency'    => $this->currency,
            'sub_total'   => $this->sub_total,
            'discount'    => $this->discount,
            'grand_total' => $this->grand_total,

            'client'      => new ClientResource($this->client),
            'employee'    => new EmployeeResource($this->employee),
            'items'       => QuoteItemResource::collection($this->whenLoaded('items')),

            'taxes'       => $this->relations['taxes'] ?? [],
        ];
    }
}











