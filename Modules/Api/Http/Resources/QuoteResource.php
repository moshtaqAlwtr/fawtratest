<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
   public function toArray($request)
{
    return [
        'id' => $this->id,
        'code' => $this->code,
        'client' => new ClientResource($this->client),
        'creator' => $this->creator->name,
        'currency' => $this->currency,
        'grand_total' => $this->grand_total,
        'status' => $this->status,
        'created_at' => $this->created_at->toDateTimeString(),
        'items' => QuoteItemResource::collection($this->whenLoaded('items')),
    ];
}

}
