<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    use HasFactory;

    /**
     * اسم الجدول المرتبط بالموديل.
     */
    protected $table = 'unit_types';

    /**
     * الحقول القابلة للتعبئة.
     */
    protected $fillable = [
        'name',
        'status',
        'pricing_rule_id',
        'check_in_time',
        'check_out_time',
        'tax1',
        'tax2',
        'description',
    ];

    /**
     * العلاقة مع قاعدة التسعير.
     */
    public function pricingRule()
    {
        return $this->belongsTo(PricingRule::class, 'pricing_rule_id');
    }
    
}
