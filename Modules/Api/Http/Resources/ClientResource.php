<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request)
{
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->trade_name,
            'tax_number' => $this->tax_number,
            'phone' => $this->phone,
            'emdail' => $this->email,
            'type' => $this->type,
            'visit_type' => $this->visit_type,
            'balance' => optional($this->account_client)->balance ?? 0,
            'status' => optional($this->status_client)->name,
            'status_color' => optional($this->status_client)->color,
            'branch' => optional($this->branch)->name,
            // 'region' => optional(optional($this->Neighborhood)->Region)->name,
            // 'neighborhood' => optional($this->Neighborhood)->name,
            
             'region'       => optional(optional($this->Neighborhood)->Region)->name,
                'neighborhood' => optional($this->Neighborhood)->name,

            // 'employee' => optional($this->employee)->name,
            'latitude'  => optional($this->locations)->latitude,
            'longitude' => optional($this->locations)->longitude,
// 'contacts'  => ContactResource::collection($this->whenLoaded('contacts')),


            // 'categories' => $this->whenLoaded('categoriesClient', fn() => $this->categoriesClient->pluck('name')),
 // لو العلاقة موجودة
            'distance_km' => $this->distance,
            'created_at' => $this->created_at?->format('Y-m-d'),
            
            // 'analytics' => $this->when(isset($this->analytics), fn() => $this->analytics),
        ];
}

}
