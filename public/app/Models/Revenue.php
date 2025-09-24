<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    // اسم incomincom
    protected $table = 'income';

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'date',
        'source',
        'amount',
        'description',
        'account_id',
        'payment_voucher_id',
        'treasury_id',
        'bank_account_id',
        'journal_entry_id',
        'created_by',
    ];

    // العلاقات
    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function paymentVoucher()
    {
        return $this->belongsTo(PaymentVoucher::class, 'payment_voucher_id');
    }

    public function treasury()
    {
        return $this->belongsTo(Treasury::class, 'treasury_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
