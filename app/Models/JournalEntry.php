<?php

namespace App\Models;

use Faker\Provider\ar_EG\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Payments;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'date',
        'description',
        'status',
        'currency',
        'attachment',
        'client_id',
        'employee_id',
        'purchase_invoice_id',
        'invoice_id',
        'cost_center_id',
        'salary_id',
        'created_by_employee',
        'approved_by_employee'
    ];

    protected $casts = [
        'date' => 'date',
        'status' => 'integer'
    ];

    // العلاقة مع التفاصيل


    // العلاقة مع العميل
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function details()
{
    return $this->hasMany(JournalEntryDetail::class, 'journal_entry_id');
}

    // العلاقة مع الموظف
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // العلاقة مع الفاتورة
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    public function payment()
{
    return $this->hasOne(PaymentsProcess::class);
}

    // العلاقة مع مركز التكلفة
    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    // العلاقة مع الموظف الذي أنشأ القيد
    public function createdByEmployee()
    {
        return $this->belongsTo(User::class, 'created_by_employee');
    }

    // العلاقة مع الموظف الذي اعتمد القيد
    public function approvedByEmployee()
    {
        return $this->belongsTo(User::class, 'approved_by_employee');
    }

    // العلاقة مع المدفوعات
    public function payments()
    {
        return $this->hasMany(PaymentsProcess::class);
    }

    public function account()//+
    {//+
        return $this->belongsTo(Account::class, 'account_id'); // Adjust the foreign key if necessary//+
    }//+



    // دالة مساعدة لحالة القيد
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            0 => 'معلق',
            1 => 'معتمد',
            2 => 'مرفوض',
            default => 'غير معروف'
        };
    }

    // دالة للتحقق من توازن القيد
    public function isBalanced()
    {
        $totalDebit = $this->details()->sum('debit');
        $totalCredit = $this->details()->sum('credit');
        return $totalDebit == $totalCredit;
    }
    public function branch(){
        return $this->belongsTo(Branch::class);
    }
}
