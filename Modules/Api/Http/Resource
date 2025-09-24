<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'created_at'    => $this->created_at->format('Y-m-d H:i'),
            
            'employee' => [
                'id'   => optional($this->employee)->id,
                'name' => optional($this->employee)->name ?? 'غير محدد',
            ],

            'status' => [
                'value' => $this->status,
                'text'  => $this->status_text ?? '',   // تأكد أنك ترجعها من الـ Model أو accessor
                'color' => $this->status_color ?? '',  // نفس الشيء، تأكد أنها موجودة بالـ Model
            ],
        ];
    }
}
