<?php

namespace Modules\Api\Http\Resources\Finance;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'code'              => $this->code,
            'date'              => $this->date,
            'description'       => $this->description,
            'amount'            => $this->amount,
            'status'            => $this->status,
            'created_by'        => $this->createdBy->name,
         
            'account'           => $this->account?->name,
            'supplier'          => $this->Supplier?->trade_name,
            'created_at'        => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
