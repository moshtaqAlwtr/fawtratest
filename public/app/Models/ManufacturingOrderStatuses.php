<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingOrderStatuses extends Model
{
    protected $table = 'manufacturing_order_statuses';
    protected $fillable = ['active'];

    public function manualOrderStatus()
    {
        return $this->hasMany(ManualManufacturingOrderStatuses::class,'order_status_id');
    }

}
