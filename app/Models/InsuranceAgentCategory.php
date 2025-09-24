<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceAgentCategory extends Model
{
    use HasFactory;

    // تحديد اسم الجدول (اختياري إذا كان الاسم مطابقًا للمعيار)
    protected $table = 'insurance_agent_categories';

    // تحديد الحقول القابلة للتعبئة
    protected $fillable = ['category_id', 'insurance_agent_id', 'name', 'discount', 'company_copayment', 'client_copayment', 'max_copayment', 'status', 'type'];

    // تعريف العلاقة مع نموذج وكيل التأمين
    public function insuranceAgent()
    {
        return $this->belongsTo(InsuranceAgent::class);
    }

    // تعريف العلاقة مع نموذج التصنيف
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
