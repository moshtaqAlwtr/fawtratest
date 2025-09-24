<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientLocationResource extends JsonResource
{
     public function toArray($request)
    {
        // locations قد تكون Collection
        $loc = $this->locations;
        if ($loc instanceof \Illuminate\Support\Collection) {
            // خذ أول عنصر/أحدث عنصر حسب حالتك
            $loc = $loc->first(); // أو ->sortByDesc('id')->first();
        }

        return [
            'id'           => $this->id,
            'code'         => $this->code,
            'name'         => $this->trade_name,
            'phone'        => $this->phone,

            'balance'      => optional($this->accountClient)->balance ?? 0,
            'status'       => optional($this->statusClient)->name,
            'status_color' => optional($this->statusClient)->color,
            'branch'       => optional($this->branch)->name,
            'region'       => optional(optional($this->neighborhood)->region)->name,
            'neighborhood' => optional($this->neighborhood)->name,

            'latitude'     => $loc?->latitude ? (float) $loc->latitude : null,
            'longitude'    => $loc?->longitude ? (float) $loc->longitude : null,

            'distance_km'  => isset($this->distance) ? (float) $this->distance : null,
        ];
    }

}
