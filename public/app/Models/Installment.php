<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;


    protected $table = 'installments'; // اسم جدول القسط
    protected $fillable = [
        'invoice_id',       // معرف الفاتورة
        'amount',           // مبلغ القسط
        'installment_number', // رقم القسط
        'due_date',         // تاريخ الاستحقاق
    ];

    // تعريف العلاقة مع نموذج الفاتورة
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    public function payment()
    {
        return $this->belongsTo(PaymentsProcess::class, 'installment_id'); // إذا كان القسط ينتمي إلى عملية دفع
    }
    public function details()
{
    return $this->hasMany(InstallmentDetail::class, 'installments_id');
}
}
