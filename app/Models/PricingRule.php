<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    use HasFactory;

    // تحديد اسم الجدول
    protected $table = 'pricing_rules';

    // تحديد الحقول القابلة للتحديث عبر الـ Mass Assignment
    protected $fillable = [
        'pricingName',
        'status',
        'currency',
        'pricingMethod',
        'dailyPrice'
    ];
    public function units()
    {
        return $this->hasMany(UnitType::class, 'pricing_rule_id');
    }
    

}
