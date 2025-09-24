<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditNotificationResource extends JsonResource
{
  public function toArray($request)
{
    return [
        'id'            => $this->id,
        'credit_number' => $this->credit_number,
        'client' => [
            'id'         => optional($this->client)->id,
            'name'       => optional($this->client)->trade_name ?? optional($this->client)->first_name . ' ' . optional($this->client)->last_name,
            'tax_number' => optional($this->client)->tax_number,
            'address'    => optional($this->client)->full_address,
        ],
        'credit_date'   => $this->credit_date,
        'grand_total'   => (float) $this->grand_total,
        'status'        => $this->status,
        'status_label'  => match ($this->status) {
            1 => 'مسودة',
            2 => 'قيد الانتظار',
            3 => 'معتمد',
            4 => 'تم التحويل إلى فاتورة',
            5 => 'ملغى',
            default => 'غير معروف',
        },
        'created_by' => $this->createdBy->name,
        // 'created_by' => [
        //     'id'   => optional($this->createdBy)->id,
        //     'name' => optional($this->createdBy)->name,
        // ],
    ];
}


}
