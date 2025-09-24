<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltySetting extends Model
{
    use HasFactory;

    // تحديد الجدول المرتبط بالنموذج
    protected $table = 'loyalty_sittings';

    // تحديد الحقول القابلة للتعبئة
    protected $fillable = [
        'minimum_import_points',
        'client_credit_type_id',
        'client_loyalty_conversion_factor',
        'allow_decimal',
    ];

    // يمكنك إضافة علاقات هنا إذا لزم الأمر
    // على سبيل المثال، إذا كان لديك علاقة مع نموذج ClientCreditType
    public function clientCreditType()
    {
        return $this->belongsTo(BalanceType::class, 'client_credit_type_id');
    }
}
