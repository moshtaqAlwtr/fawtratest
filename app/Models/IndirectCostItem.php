<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndirectCostItem extends Model
{
    protected $table = 'indirect_cost_items';

    protected $fillable = [
        'indirect_costs_id',
        'restriction_id',
        'restriction_total',
        'manufacturing_order_id',
        'manufacturing_price'
    ];

    protected $casts = [
        'restriction_total' => 'decimal:2',
        'manufacturing_price' => 'decimal:2',
    ];

    /**
     * العلاقة مع التكلفة غير المباشرة الرئيسية
     */
    public function indirectCost()
    {
        return $this->belongsTo(IndirectCost::class, 'indirect_costs_id');
    }

    /**
     * العلاقة مع أمر التصنيع
     */
    public function manufacturingOrder()
    {
        return $this->belongsTo(ManufacturOrders::class, 'manufacturing_order_id');
    }

    /**
     * العلاقة مع القيد المحاسبي
     */
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'restriction_id');
    }

    /**
     * دالة مساعدة للحصول على اسم القيد
     */
    public function getRestrictionNameAttribute()
    {
        if ($this->journalEntry) {
            return $this->journalEntry->reference_number . ' - ' . $this->journalEntry->description;
        }
        return 'غير محدد';
    }

    /**
     * دالة مساعدة للحصول على اسم أمر التصنيع
     */
    public function getManufacturingOrderNameAttribute()
    {
        return $this->manufacturingOrder ? $this->manufacturingOrder->name : 'غير محدد';
    }

    /**
     * دالة مساعدة لحساب المجموع الكلي للعنصر
     */
    public function getTotalAmountAttribute()
    {
        return $this->restriction_total + $this->manufacturing_price;
    }
}