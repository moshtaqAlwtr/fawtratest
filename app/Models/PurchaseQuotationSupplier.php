<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseQuotationSupplier extends Model
{
    protected $table = 'purchase_quotation_supplier';

    protected $fillable = ['purchase_quotation_id', 'supplier_id', 'created_by', 'updated_by'];



    // العلاقة مع طلب عرض السعر
    public function purchaseQuotation(): BelongsTo
    {
        return $this->belongsTo(PurchaseQuotation::class);
    }

    // العلاقة مع المورد
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // العلاقة مع المستخدم الذي أنشأ السجل
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // العلاقة مع المستخدم الذي عدل السجل
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scope للبحث حسب الحالة
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope للبحث حسب نطاق السعر
    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('quoted_price', [$min, $max]);
    }

    // دالة مساعدة لتحديث الحالة
    public function updateStatus($status)
    {
        return $this->update([
            'status' => $status,
            'response_date' => now(),
            'updated_by' => auth()->id(),
        ]);
    }
}
