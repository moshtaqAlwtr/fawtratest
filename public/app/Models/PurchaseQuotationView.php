<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseQuotationView extends Model
{
    protected $table = 'purchase_quotations_view';

    protected $fillable = ['supplier_id', 'account_id', 'code', 'date', 'valid_days', 'total_discount', 'grand_total', 'status', 'created_by', 'updated_by'];

    protected $casts = [
        'date' => 'date',
        'valid_days' => 'integer',
        'total_discount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'status' => 'integer',
    ];

    // العلاقة مع المورد
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'quotes_purchase_order_id');
    }

    // العلاقة مع الحساب
    public function account()
    {
        return $this->belongsTo(Account::class);
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

    // العلاقة مع تفاصيل المنتجات

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

    // دالة للتحقق من صلاحية العرض
    public function isValid()
    {
        if ($this->valid_days <= 0) {
            return true;
        }

        $validUntil = $this->date->addDays($this->valid_days);
        return now()->lte($validUntil);
    }
}
