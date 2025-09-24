<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehousePermits extends Model
{
    use HasFactory;
    protected $table = 'warehouse_permits';
    protected $fillable = ['permission_type', 'permission_date','reference_type','reference_id','permission_source_id', 'sub_account', 'number', 'store_houses_id', 'from_store_houses_id', 'to_store_houses_id', 'grand_total', 'details', 'attachments', 'created_by'];

    public function storeHouse()
    {
        return $this->belongsTo(StoreHouse::class, 'store_houses_id');
    }

    public function fromStoreHouse()
    {
        return $this->belongsTo(StoreHouse::class, 'from_store_houses_id');
    }

    public function toStoreHouse()
    {
        return $this->belongsTo(StoreHouse::class, 'to_store_houses_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'warehouse_permits_products', 'warehouse_permits_id', 'product_id');
    }
// مصدر الإذن - إذا له علاقة
public function reference()
{
    return $this->belongsTo(WarehousePermitsProducts::class, 'reference_id');
}


// الفرع
public function branch()
{
    return $this->belongsTo(Branch::class, 'branch_id');
}

    public function warehousePermitsProducts()
    {
        return $this->hasMany(WarehousePermitsProducts::class, 'warehouse_permits_id');
    }
// app/Models/WarehousePermits.php

public function items()
{
    return $this->hasMany(WarehousePermitsProducts::class, 'warehouse_permits_id');
}

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
public function permissionSource()
{
    return $this->belongsTo(PermissionSource::class, 'permission_source_id', 'id');
}

}
