<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehousePermitsProducts extends Model
{
    use HasFactory;

    protected $table = 'warehouse_permits_products';

    protected $fillable = [
        'warehouse_permits_id',
        'product_id',
        'quantity',
        'unit_price',
        'total',
        'stock_before',
        'stock_after'
    ];

    public function warehousePermits()
    {
        return $this->belongsTo(WarehousePermits::class, 'warehouse_permits_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
