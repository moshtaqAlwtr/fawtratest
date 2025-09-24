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
}
