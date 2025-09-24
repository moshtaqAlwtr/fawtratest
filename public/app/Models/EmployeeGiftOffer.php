<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeGiftOffer extends Model
{
    protected $table = 'employee_gift_offer'; // اسم الجدول

    protected $fillable = [
        'user_id',
        'gift_id',
    ];

    // علاقة بالمستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة بالهدية
    public function giftOffer()
    {
        return $this->belongsTo(GiftOffer::class, 'gift_id');
    }
}

