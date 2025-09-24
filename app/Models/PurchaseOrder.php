<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    // تحديد اسم الجدول (اختياري إذا كان الاسم الافتراضي `purchase_orders` صحيحًا)
    protected $table = 'purchase_orders';

    // الحقول القابلة للتعبئة
    protected $fillable = ['title', 'code', 'order_date', 'due_date', 'notes', 'attachments', 'status', 'created_by', 'updated_by'];

    // الحقول التي سيتم تحويلها إلى نوع Date تلقائيًا
    protected $dates = ['order_date', 'due_date', 'deleted_at'];

    /**
     * العلاقات
     */

    // علاقة مع المستخدم الذي أنشأ الطلب
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // علاقة مع المستخدم الذي عدّل الطلب
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // علاقة مع المنتجات (علاقة متعددة إلى متعددة)
    public function productDetails()
    {
        return $this->hasMany(ProductDetails::class, 'purchase_order_id');
    }
    public   function items()
    {
        return $this->belongsTo(InvoiceItem::class, 'purchase_order_id');
    }
}
