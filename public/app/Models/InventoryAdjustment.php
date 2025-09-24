<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    protected $fillable = [
      'stock_id',
        'inventory_time',
        'quantity_in_system',
        'quantity_in_stock',
        'quantity_difference',
        'status',
        'calculation_type',
      
    ];
    
    public function items()
{
    return $this->hasMany(InventoryItem::class, 'adjustment_id');
}

public function storeHouse()
{
    return $this->belongsTo(StoreHouse::class, 'stock_id');
}

}
