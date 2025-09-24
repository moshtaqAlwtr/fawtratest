<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseQuotationView extends Model
{
    protected $table = 'purchase_quotations_view';

    // الحقول التي يمكن تعبئتها
    protected $fillable = [
        'supplier_id',
        'code',
        'date',
        'quotation_id',
        'valid_days',
        'total_discount',
        'grand_total',
        'status',
        'created_by',
        'updated_by',
        'purchase_price_number', // رقم عرض الشراء
        'notes', // الملاحظات/الشروط
        'discount_amount', // قيمة الخصم الإجمالي
        'discount_type', // نوع الخصم (مبلغ أو نسبة)
        'adjustment_label', // وصف التسوية
        'adjustment_type', // نوع التسوية (خصم أو إضافة)
        'adjustment_value', // قيمة التسوية
        'tax_type', // نوع الضريبة (قيمة مضافة، صفرية، معفاة)
        'shipping_cost', // تكلفة الشحن
        'subtotal', // المجموع الفرعي قبل الخصم والضريبة
        'total_tax', // إجمالي الضريبة
'tax_id'
    ];

    // تحويل أنواع الحقول
    protected $casts = [
        'date' => 'date',
        'valid_days' => 'integer',
        'total_discount' => 'decimal:2',
        'grand_total' => 'decimal:2',

        'discount_amount' => 'decimal:2',
        'adjustment_value' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_tax' => 'decimal:2',
    ];

    // العلاقة مع المورد
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // العلاقة مع العناصر (المنتجات)
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'quotes_purchase_order_id');
    }

    // العلاقة مع منشئ العرض
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // العلاقة مع محدث العرض
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function quotesPurchase()
    {
        return $this->belongsTo(PurchaseQuotationView::class, 'quotes_purchase_order_id');
    }

    // نطاق للعروض النشطة
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // دالة لحساب المجموع النهائي
    public function calculateGrandTotal()
    {
        $total = $this->items->sum('row_total');
        $this->grand_total = $total - $this->total_discount;
        return $this->grand_total;
    }

    // دالة لتحديث حالة العرض
    public function updateStatus($status)
    {
        $this->status = $status;
        return $this->save();
    }

    public function tax(){
        return $this->belongsTo(Tax::class);
    }
    // دالة للتحقق من صلاحية العرض
    public function isValid()
    {
        if ($this->valid_days <= 0) {
            return true;
        }

        $validUntil = $this->date->addDays($this->valid_days);
        return now()->lte($validUntil);
    }
    public  function  account(){
        return $this->belongsTo(Account::class);
    }
    public function  orderPurchase(){
        return $this->hasOne(PurchaseQuotation::class,"order_id");
    }
public function generatedQuotations()
{
    return $this->hasMany(PurchaseQuotationView::class, 'quotation_id');
}
public function taxes()
{
    return $this->hasMany (TaxInvoice::class);
}
}