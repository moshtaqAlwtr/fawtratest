<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientocationResource extends JsonResource
{
    public function toArray($request)
{
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->trade_name,
            'phone' => $this->phone,
            
          
           
            'balance' => optional($this->account_client)->balance ?? 0,
            'status' => optional($this->status_client)->name,
            'status_color' => optional($this->status_client)->color,
            'branch' => optional($this->branch)->name,
            'region' => optional(optional($this->neighborhood)->region)->name,
            'neighborhood' => optional($this->neighborhood)->name,
            
            'latitude'  => optional($this->locations)->latitude,
            'longitude' => optional($this->locations)->longitude,
// 'contacts'  => ContactResource::collection($this->whenLoaded('contacts')),


            // 'categories' => $this->whenLoaded('categoriesClient', fn() => $this->categoriesClient->pluck('name')),
 // لو العلاقة موجودة
            'distance_km' => $this->distance,

            
           
        ];
}

}
