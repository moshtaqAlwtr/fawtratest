<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturOrders extends Model
{
    protected $table = 'manufactur_orders';

    protected $fillable = [
        'name', 'code', 'from_date', 'to_date', 'account_id',
        'employee_id', 'client_id', 'product_id', 'quantity',
        'production_material_id', 'production_path_id', 'last_total_cost',
        'created_by', 'updated_by'
    ];

    public function manufacturOrdersItem()
    {
        return $this->hasMany(ManufacturOrdersItem::class,'manufactur_order_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productionMaterial()
    {
        return $this->belongsTo(ProductionMaterials::class);
    }

    public function productionPath()
    {
        return $this->belongsTo(ProductionPath::class);
    }

    public function manufacturOrders()
    {
        return $this->hasMany(ManufacturOrdersItem::class);
    }


}
