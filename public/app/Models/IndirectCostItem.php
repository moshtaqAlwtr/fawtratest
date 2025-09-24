<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndirectCostItem extends Model
{
    protected $table = 'indirect_cost_items';

    protected $fillable = [
        'indirect_costs_id', 'restriction_id', 'restriction_total', 'manufacturing_order_id', 'manufacturing_price'
    ];

    public function IndirectCost()
    {
        return $this->belongsTo(IndirectCost::class, 'indirect_costs_id');
    }

    public function ManufacturingOrder()
    {
        return $this->belongsTo(ManufacturOrders::class, 'manufacturing_order_id');
    }

}
