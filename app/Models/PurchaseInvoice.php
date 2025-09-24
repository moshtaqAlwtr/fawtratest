<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $table = 'purchase_invoices';


    protected $fillable = ['code', 'type', 'reference_id', 'receiving_status', 'attachments', 'supplier_id', 'account_id','due_value', 'date', 'terms', 'status','payment_status', 'discount_amount', 'discount_percentage', 'discount_type', 'advance_payment', 'advance_payment_type', 'is_paid', 'payment_method', 'reference_number', 'tax_type', 'shipping_cost', 'subtotal', 'total_discount', 'total_tax', 'grand_total', 'notes', 'is_received', 'received_date', 'created_by', 'updated_by'];

    // علاقة بسيطة مع البنود
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class,'purchase_invoice_id');
    }

    public function purchaseItems(){
    return $this->hasMany(InvoiceItem::class);
}
    // علاقة مع بنود أمر الشراء

    public function payments_process()
    {
        return $this->hasMany(PaymentsProcess::class, 'purchases_id');
    }
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'purchase_invoice_id');
    }

    // علاقة مع بنود فاتورة الشراء

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function employee()
{
    return $this->belongsTo(Employee::class, 'employee_id');
}
public function payments()
{
    return $this->hasMany(PaymentsProcess::class,'purchases_id');
}

public function  user()
{
    return $this->belongsTo(User::class);
}

// علاقة مع الفاتورة الأصلية (للمرتجعات)
public function originalInvoice()
{
    return $this->belongsTo(PurchaseInvoice::class, 'reference_id');
}

// علاقة مع المرتجعات المرتبطة بهذه الفاتورة
public function returns()
{
    return $this->hasMany(PurchaseInvoice::class, 'reference_id');
}
}