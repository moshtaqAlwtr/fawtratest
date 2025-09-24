<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray($request)
{
        return [
            'id' => $this->id,
            'name' => $this->first_name,
         
          
        ];
}

}
