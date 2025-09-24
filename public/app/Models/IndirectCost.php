<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndirectCost extends Model
{
    protected $table = 'indirect_costs';

    protected $fillable = [
        'account_id', 'from_date', 'to_date', 'based_on', 'total'
    ];

    public function indirectCostItems()
    {
        return $this->hasMany(IndirectCostItem::class, 'indirect_costs_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
