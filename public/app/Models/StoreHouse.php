<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreHouse extends Model
{
    use HasFactory;
    protected $table = 'store_houses';
    protected $fillable = ['id', 'name', 'shipping_address', 'status', 'major', 'view_permissions', 'edit_stock_permissions', 'crate_invoices_permissions','value_of_view_permissions', 'value_of_edit_stock_permissions', 'value_of_crate_invoices_permissions', 'created_at', 'updated_at'];

    public function productDetails()
    {
        return $this->hasMany(ProductDetails::class, 'purchase_order_id');
    }

    public function transfersFrom()
    {
        return $this->hasMany(WarehousePermits::class, 'from_store_houses_id');
    }

    public function transfersTo()
    {
        return $this->hasMany(WarehousePermits::class, 'to_store_houses_id');
    }

    public function warehousePermits()
    {
        return $this->hasMany(WarehousePermits::class, 'store_houses_id');
    }
public function products()
{
    return $this->belongsToMany(Product::class, 'product_details', 'store_houses_id', 'product_id');
}
public function categories()
{
    return $this->belongsToMany(Category::class, 'product_details', 'store_houses_id', 'category_id');
}
}
