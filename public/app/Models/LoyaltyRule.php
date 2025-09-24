<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyRule extends Model
{
    use HasFactory;

    // تحديد اسم الجدول إذا كان مختلفًا عن الاسم الافتراضي
    protected $table = 'loyalty_rules';

    // تحديد الأعمدة القابلة للتعبئة
    protected $fillable = [
        'name',
        'status',
        'priority_level',
        'collection_factor',
        'minimum_total_spent',
        'currency_type',
        'period',
        'period_unit',
    ];

    // علاقة مع نموذج العملاء
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_loyalty_rule', 'loyalty_rule_id', 'client_id');
    }
}
