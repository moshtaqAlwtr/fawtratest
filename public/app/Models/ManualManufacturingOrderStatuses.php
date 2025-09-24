<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualManufacturingOrderStatuses extends Model
{
    protected $table = 'manual_manufacturing_order_statuses';

    protected $fillable = [
        'order_status_id',
        'name',
        'color'
    ];

    public function orderStatus()
    {
        return $this->belongsTo(ManufacturingOrderStatuses::class,'order_status_id');
    }
}
