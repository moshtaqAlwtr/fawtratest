<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'number_suply', 'trade_name', 'first_name', 'last_name', 'phone', 'mobile', 'email',
        'street1', 'street2', 'city', 'region', 'postal_code', 'country', 'tax_number',
        'commercial_registration', 'opening_balance', 'opening_balance_date', 'currency',
        'notes', 'attachments', 'created_by', 'updated_by', 'branch_id', 'account_id'
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'opening_balance_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // العلاقة مع فواتير الشراء
    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class, 'supplier_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // العلاقة مع المدفوعات
    public function payments()
    {
        return $this->hasMany(PaymentsProcess::class, 'supplier_id');
    }

    // العلاقة مع الفرع
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
public function account()
{
    // العلاقة أصبحت hasOne لأن account_id موجود في جدول الحسابات
    return $this->hasOne(Account::class, 'supplier_id'); // تأكد من اسم العمود الصحيح
}
    // حساب إجمالي المشتريات
    public function getTotalPurchasesAttribute()
    {
        return $this->purchaseInvoices->sum('grand_total');
    }

    // حساب إجمالي المدفوعات
    public function getTotalPaymentsAttribute()
    {
        return $this->payments->sum('amount');
    }

    // حساب التسويات (إذا كانت موجودة)
    public function getAdjustmentsAttribute()
    {
        // إذا كان لديك نموذج للتسويات، يمكنك استخدامه هنا
        return 0; // افتراضيًا، يمكنك تعديل هذا الجزء
    }

    // حساب الرصيد
    public function getBalanceAttribute()
    {
        return $this->opening_balance + $this->total_purchases - $this->total_payments + $this->adjustments;
    }
public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
    public function getFullAddressAttribute()
    {
        $address = [];
        if ($this->street1) {
            $address[] = $this->street1;
        }
        if ($this->street2) {
            $address[] = $this->street2;
        }
        if ($this->city) {
            $address[] = $this->city;
        }
        if ($this->region) {
            $address[] = $this->region;
        }
        if ($this->postal_code) {
            $address[] = $this->postal_code;
        }
        if ($this->country) {
            $address[] = $this->country;
        }

        return implode(', ', $address);
    }



}
