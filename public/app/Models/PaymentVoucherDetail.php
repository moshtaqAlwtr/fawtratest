<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentVoucherDetail extends Model
{
    use HasFactory;

    // اسم الجدول
    protected $table = 'payment_voucher_details';

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'payment_id',
        'unit',
        'amount',
        'category',
        'tax_id',
        'description',
    ];

    // العلاقة مع سند الصرف
    public function paymentVoucher()
    {
        return $this->belongsTo(PaymentVoucher::class, 'payment_voucher_id');
    }

    // العلاقة مع جدول الضرائب
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }
}
