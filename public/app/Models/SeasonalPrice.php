<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonalPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit_type_id',
        'date_from',
        'date_to',
        'pricing_rule_id',
        'working_days',
    ];

    protected $casts = [
        'working_days' => 'array', // لتحويل الحقل JSON إلى مصفوفة
    ];
    public function pricingRule()
{
    return $this->belongsTo(PricingRule::class, 'pricing_rule_id');
}

public function unitType()
{
    return $this->belongsTo(UnitType::class, 'unit_type_id');
}

}

