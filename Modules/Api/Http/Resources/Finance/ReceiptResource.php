<?php

namespace Modules\Api\Http\Resources\Finance;

use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
{
   public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'date' => $this->date,
            'description' => $this->description,
            'amount' => $this->amount,
            'attachments' => $this->attachments 
                ? asset('assets/uploads/incomes/' . $this->attachments) 
                : null,
            // 'account' => [
            //     'id' => optional($this->account)->id,
            //     'name' => optional($this->account)->name,
            // ],
            'account' => optional($this->account)->name,
            // 'user' => [
            //     'id' => optional($this->user)->id,
            //     'name' => optional($this->user)->name,
            // ],
            'user' => optional($this->user)->name,
            'store_id' => $this->store_id,
        ];
    }
}








