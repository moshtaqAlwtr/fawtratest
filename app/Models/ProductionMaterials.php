<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionMaterials extends Model
{
    protected $table = 'production_materials';
    protected $fillable = [
        'name', 'code', 'product_id','last_total_cost',
        'account_id', 'production_path_id', 'quantity', 'status',
        'default', 'created_by', 'updated_by'
    ];

    public function ProductionMaterialsItem()
    {
        return $this->hasMany(ProductionMaterialsItem::class, 'production_material_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function productionPath()
    {
        return $this->belongsTo(ProductionPath::class, 'production_path_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

}
