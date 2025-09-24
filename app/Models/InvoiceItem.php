<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'product_id',
        'packege_id',
        'quotation_id',
        'credit_note_id', // إضافة حقل credit_note_id
        'purchase_invoice_id',
        'purchase_quotation_id',
        'store_house_id',
        'purchase_order_id',
        'periodic_invoice_id',
        // إضافة حقل periodic_invoice_id
        'item',
        'quotes_purchase_order_id',
        'purchase_invoice_id_type',
        'description',
        'unit_price',
        'quantity',
        'discount',
        'tax_1',
        'tax_2',
        'total',
        'type',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_1' => 'decimal:2',
        'tax_2' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // public function invoice()
    // {
    //     return $this->hasMany(Invoice::class, 'invoice_id');
    // }
    public function invoice()
    {
        // استبدل 'invoice_id' باسم العمود الفعلي إذا كان مختلفاً
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function quotesPurchase()
    {
        return $this->hasMany(PurchaseQuotationView::class, 'quotes_purchase_order_id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quote::class, 'quotation_id');
    }
    // في ملف InvoiceItem.php
    public function storeHouse()
    {
        return $this->belongsTo(StoreHouse::class, 'store_house_id');
    }

    public function creditNote()
    {
        return $this->belongsTo(CreditNotification::class, 'credit_note_id');
    }

    // حساب المجموع الفرعي قبل الضرائب
    public function calculateSubtotal()
    {
        return $this->unit_price * $this->quantity - $this->discount;
    }

    // حساب مجموع الضرائب
    public function calculateTaxAmount()
    {
        $subtotal = $this->calculateSubtotal();
        return $subtotal * ($this->tax_1 / 100) + $subtotal * ($this->tax_2 / 100);
    }

    // حساب المجموع الكلي
    public function calculateTotal()
    {
        return $this->calculateSubtotal() + $this->calculateTaxAmount();
    }

    // تحديث المجموع
    public function updateTotal()
    {
        $this->total = $this->calculateTotal();
        return $this;
    }

    // إضافة علاقة مع الدفع (PaymentVoucherDetail)
    public function paymentVoucherDetails()
    {
        return $this->hasMany(PaymentVoucherDetail::class, 'invoice_item_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id'); // Adjust if your foreign key is different
    }

    public function item()
    {
        return $this->belongsTo(Product::class); // Adjust if your foreign key is different
    }

    public function orderPurchase()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id'); // Adjust if your foreign key is different
    }
   public function purchaseQuotation()
    {
        return $this->belongsTo(PurchaseQuotation::class, 'purchase_quotation_id'); // Adjust if your foreign key is different
    }

       public function permissionSource()
    {
        return $this->belongsTo(PermissionSource::class); // Adjust if your foreign key is different
    }

}
