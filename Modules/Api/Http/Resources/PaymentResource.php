<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Api\Http\Resources\ClientResource;

class PaymentResource extends JsonResource
{
public function toArray($request)
{
    return [
        'id'              => $this->id,
        'payment_number'  => str_pad($this->id, 6, '0', STR_PAD_LEFT),
        'payment_date'    => $this->payment_date?->format('Y-m-d'),
        'amount'          => (float) $this->amount,
        'status_label'    => match ($this->payment_status) {
            1 => 'مكتمل',
            2 => 'غير مكتمل',
            3 => 'مسودة',
            4 => 'تحت المراجعة',
            5 => 'فاشلة',
            default => 'غير معروف',
        },
        'is_confirmed'    => $this->payment_status == 1,

        'client' => [
            'id'    => $this->invoice?->client?->id,
            'name'  => $this->invoice?->client?->trade_name,
            'phone' => $this->invoice?->client?->phone,
        ],

        // 'employee' => [
        //     'id'   => $this->employee?->id,
        //     'name' => $this->employee?->name,
        // ],

        'invoice' => [
            'id'         => $this->invoice?->id,
            'code'       => $this->invoice?->code,
            'employee'   => $this->invoice?->employee?->full_name,
        ],

        // 'branch' => [
        //     'name'    => $this->branch?->name ?? 'مؤسسة أعمال خاصة للنجارة',
        //     'address' => $this->branch?->address ?? 'الرياض',
        // ],
    ];
}




}
