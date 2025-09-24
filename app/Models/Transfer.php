<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_treasury_id',
        'to_treasury_id',
        'amount',
        'transfer_date',
        'notes',
        'created_by',
    ];

    // العلاقة مع الخزينة المصدر
    public function fromTreasury()
    {
        return $this->belongsTo(Treasury::class, 'from_treasury_id');
    }

    // العلاقة مع الخزينة الهدف
    public function toTreasury()
    {
        return $this->belongsTo(Treasury::class, 'to_treasury_id');
    }

    // العلاقة مع المستخدم الذي أنشأ التحويل
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
