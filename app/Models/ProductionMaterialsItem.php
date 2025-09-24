<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionMaterialsItem extends Model
{
    protected $table = 'production_materials_items';
    protected $fillable = [
        'production_material_id',
        'raw_product_id', 'raw_unit_price', 'raw_quantity','raw_production_stage_id',
        'raw_total', 'expenses_account_id', 'expenses_cost_type',
        'expenses_price', 'expenses_description', 'expenses_total','expenses_production_stage_id',
        'workstation_id', 'operating_time', 'manu_total_cost', 'manu_description','manu_cost_type','manu_production_stage_id',
        'manu_total', 'end_life_product_id', 'end_life_unit_price',
        'end_life_quantity', 'end_life_total','end_life_production_stage_id'
    ];

    public function productionMaterial()
    {
        return $this->belongsTo(ProductionMaterials::class, 'production_material_id');
    }

    public function rawProduct()
    {
        return $this->belongsTo(Product::class, 'raw_product_id');
    }

    public function rawProductionStage()
    {
        return $this->belongsTo(ProductionStage::class, 'raw_production_stage_id');
    }

    public function expensesProductionStage()
    {
        return $this->belongsTo(ProductionStage::class, 'expenses_production_stage_id');
    }

    public function workshopProductionStage()
    {
        return $this->belongsTo(ProductionStage::class, 'manu_production_stage_id');
    }

    public function endLifeProductionStage()
    {
        return $this->belongsTo(ProductionStage::class, 'end_life_production_stage_id');
    }

    public function endLifeProduct()
    {
        return $this->belongsTo(Product::class, 'end_life_product_id');
    }

    public function workStation()
    {
        return $this->belongsTo(WorkStations::class, 'workstation_id');
    }

    public function expensesAccount()
    {
        return $this->belongsTo(Account::class, 'expenses_account_id');
    }
    
}
