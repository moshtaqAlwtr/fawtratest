<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VisitResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'visit_date'      => $this->visit_date,
            'departure_time'  => $this->departure_time ?? '--',
            'notes'           => $this->notes ?? '--',

            'employee' => [
                'id'   => optional($this->employee)->id,
                'name' => optional($this->employee)->name ?? 'غير محدد',
            ],

           
            // 'client_deposit' => optional($this->client->latestStatus)->deposit_count ?? null,
        ];
    }
}
