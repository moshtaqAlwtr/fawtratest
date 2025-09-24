<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodicInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'periodic_invoice_id',
        'product_id',
        'item',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'tax_1',
        'tax_2',
        'total'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_1' => 'decimal:2',
        'tax_2' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function periodic_invoice()
    {
        return $this->belongsTo(PeriodicInvoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
