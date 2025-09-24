<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray($request)
{
        return [
            'id' => $this->id,
             'client_id' => $this->client_id,
             'latitude' => $this->latitude,
             'longitude' => $this->longitude,
        ];
}

}
