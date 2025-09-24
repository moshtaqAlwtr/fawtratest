<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodicInvoiceInstance extends Model
{
    protected $fillable = [
        'periodic_invoice_id', // معرف الفاتورة الدورية الأصلية
        'invoice_id',         // معرف الفاتورة الفعلية المنشأة
        'instance_number',    // رقم النسخة (مثلاً: الفاتورة الأولى، الثانية، إلخ)
        'due_date',           // تاريخ استحقاق هذه النسخة
        'status'
    ];

    protected $casts = [
        'due_date' => 'date',
        'status' => 'integer'
    ];

    // العلاقة مع الفاتورة الدورية الأصلية (النموذج)
    // مثال: $instance->periodicInvoice->invoice_number
    public function periodicInvoice(): BelongsTo
    {
        return $this->belongsTo(PeriodicInvoice::class);
    }

    // العلاقة مع الفاتورة الفعلية المنشأة
    // مثال: $instance->invoice->total_amount
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
