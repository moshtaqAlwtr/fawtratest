<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceAgent extends Model
{
    use HasFactory;

    // تحديد اسم الجدول (اختياري إذا كان الاسم مطابقًا للمعيار)
    protected $table = 'insurance_agents';

    // تحديد الحقول القابلة للتعبئة
    protected $fillable = [
        'name',
        'phone',
        'email',
        'location',
        'status',
        'attachments',
    ];

    /**
     * العلاقة مع فئات وكيل التأمين
     */
    public function categories()
    {
        return $this->hasMany(InsuranceAgentCategory::class);
    }

    /**
     * العلاقة مع الفئات النشطة فقط
     */
    public function activeCategories()
    {
        return $this->hasMany(InsuranceAgentCategory::class)->where('status', 1);
    }
}
