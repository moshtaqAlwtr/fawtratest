<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodicInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'details_subscription', // تفاصيل الاشتراك
        'client_id',
        'first_invoice_date',
        'repeat_count',
        'repeat_type',
        'repeat_interval',
        'invoice_days_offset', // إصدار الفاتورة قبل عدد معين من الأيام
        'total',
        'grand_total',
        'subtotal',
        'status',
        'shipping_cost',
        'total_tax',
        'discount_type',
        'discount_amount',
        'total_discount',
        'notes',
        'is_active',
        'auto_generate',
        'show_from_to_dates',
        'disable_partial_payment',
        'payment_terms', // شروط الدفع
    ];

    protected $casts = [
        'first_invoice_date' => 'date',
        'is_active' => 'boolean',
        'auto_generate' => 'boolean',
        'show_from_to_dates' => 'boolean',
        'disable_partial_payment' => 'boolean',
        'total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    // العلاقة مع العميل
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // العلاقة مع عناصر الفاتورة
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'periodic_invoice_id');
    }

    // العلاقة مع عناصر الفاتورة (نفس العلاقة باسم مختلف للتوافق)
    public function invoice_items()
    {
        return $this->hasMany(InvoiceItem::class, 'periodic_invoice_id');
    }

    // العلاقة مع الفواتير
    public function invoices()
    {
        return $this->hasManyThrough(
            Invoice::class,
            PeriodicInvoiceInstance::class,
            'periodic_invoice_id', // Foreign key on periodic_invoice_instances table...
            'id', // Foreign key on invoices table...
            'id', // Local key on periodic_invoices table...
            'invoice_id', // Local key on periodic_invoice_instances table...
        );
    }

    // العلاقة مع instances الفواتير
    public function instances()
    {
        return $this->hasMany(PeriodicInvoiceInstance::class);
    }

    // دالة لحساب تاريخ الفاتورة التالية
    public function calculateNextDueDate($currentDate)
    {
        $date = \Carbon\Carbon::parse($currentDate);

        switch ($this->repeat_type) {
            case 1: // weekly
                return $date->addWeeks($this->repeat_interval);
            case 2: // bi-weekly
                return $date->addWeeks($this->repeat_interval * 2);
            case 3: // monthly
                return $date->addMonths($this->repeat_interval);
            case 4: // bi-monthly
                return $date->addMonths($this->repeat_interval * 2);
            case 5: // yearly
                return $date->addYears($this->repeat_interval);
            case 6: // annual
                return $date->addYears($this->repeat_interval * 2);
            default:
                return null;
        }
    }
}
