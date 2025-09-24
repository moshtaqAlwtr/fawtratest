<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PurchaseQuotation extends Model
{
    protected $fillable = [
        'code',
        'order_date',
        'due_date',
        'status',
        'notes',
        'attachments',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'order_date' => 'date',
        'due_date' => 'date'
    ];

    // العلاقة مع المنتجات من خلال جدول العناصر
    public function items()
    {
        return $this->hasMany(ProductDetails::class , 'purchase_quotation_id');
    }

    // العلاقة مع الموردين
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseQuotationSupplier::class, 'supplier_id');
    }
    // العلاقة مع المستخدم الذي أنشأ الطلب
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // العلاقة مع المستخدم الذي عدل الطلب
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scope للبحث حسب الكود
    public function scopeByCode($query, $code)
    {
        return $query->where('code', 'like', "%{$code}%");
    }

    // Scope للبحث حسب التاريخ
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('order_date', [$startDate, $endDate]);
        }
        return $query;
    }
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'purchase_quotation_supplier', 'purchase_quotation_id', 'supplier_id')
            ->withTimestamps();
    }

    // العلاقة مع جدول العلاقة مع الموردين
    public function supplierRelations()
    {
        return $this->hasMany(PurchaseQuotationSupplier::class, 'purchase_quotation_id');
    }
}
