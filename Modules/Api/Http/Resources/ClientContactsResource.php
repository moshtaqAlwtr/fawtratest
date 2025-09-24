<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientContactsResource extends JsonResource
{
    public function toArray($request)
{
        return [
            'id' => $this->id,
            'name' => $this->trade_name,
            'phone' => $this->phone,
            'email' => $this->email,
          
        ];
}

}
