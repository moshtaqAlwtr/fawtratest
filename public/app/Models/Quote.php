<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    const STATUS_DRAFT = 1;
    const STATUS_PENDING = 2;
    const STATUS_APPROVED = 3;
    const STATUS_CONVERTED = 4; // تم التحويل إلى فاتورة
    const STATUS_REJECTED = 5;
    use HasFactory;

    // تحديد المفتاح الأساسي إذا كان غير افتراضي
    protected $primaryKey = 'id'; // المفتاح الافتراضي بالفعل (id)

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'client_id', // العميل
        'created_by', // المستخدم الذي أنشأ العرض
        'quote_date', // تاريخ عرض السعر
        'quote_number', // رقم عرض السعر
        'subtotal', // المجموع الفرعي
        'status', // الحالة (1: Draft, 2: Pending, 3: Approved, 4: Converted to Invoice, 5: Cancelled)
        'total_discount', // مجموع الخصومات
        'total_tax', // مجموع الضرائب
        'shipping_cost', // تكلفة الشحن
        'next_payment', // الدفعة القادمة
        'grand_total', // المجموع الكلي
        'notes', // الملاحظات/الشروط
        'discount_type', // نوع الخصم (مبلغ أو نسبة)
        'discount_amount', // قيمة الخصم
        'tax_type', // نوع الضريبة (القيمة المضافة، صفرية، معفاة)
    ];

    // العلاقات

    /**
     * علاقة العرض بالعميل
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * علاقة العرض بالموظف المسؤول (المستخدم الذي أنشأ العرض)
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

    /**
     * علاقة العرض ببنوده في جدول invoice_items
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'quotation_id');
    }
    public function employee()
{
    return $this->belongsTo(Employee::class, 'created_by');
}
}
