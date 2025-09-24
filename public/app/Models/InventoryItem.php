<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'adjustment_id',
        'product_id',
        'quantity_in_system',
        'quantity_in_stock',
        'quantity_difference',
        'image',
        'note',
      
    ];
    public function product()
{
    return $this->belongsTo(Product::class);
}
}
