<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'id', 'code', 'amount', 'description', 'date', 'incomes_category_id',
        'seller', 'client_id', 'account_id', 'is_recurring', 'recurring_frequency', 'end_date',
        'tax1', 'tax2', 'tax1_amount', 'tax2_amount', 'attachments', 'cost_centers_enabled','treasury_id', 'created_at', 'updated_at'
    ];

    // العلاقة مع الموظف
    public function incomes_category()
    {
        return $this->belongsTo(ReceiptCategory::class, 'incomes_category_id');
    }

    // العلاقة مع الخزينة
    public function treasury()
    {
        return $this->belongsTo(Treasury::class, 'treasury_id');
    }

    // العلاقة مع الحساب
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    // العلاقة مع العميل
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class , 'created_by');
    }

    // Scope للبحث عن السندات حسب الحالة
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope للبحث عن السندات في فترة معينة
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // دالة لإنشاء رقم سند تلقائي
    public static function generateCode()
    {
        $lastReceipt = self::latest()->first();
        $lastNumber = $lastReceipt ? intval(substr($lastReceipt->code, 3)) : 0;
        $newNumber = $lastNumber + 1;
        return 'RCP' . str_pad($newNumber, 7, '0', STR_PAD_LEFT);
    }
public function employee(){
    return $this->belongsTo(Employee::class,'employee_id');
}
public function branch(){
    return $this->belongsTo(Branch::class,'branch_id');
}

}
