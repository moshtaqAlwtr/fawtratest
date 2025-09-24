<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryDetail extends Model
{
    use HasFactory;

    protected $table = 'journal_entry_details';
    protected $guarded = [];

    protected $fillable = ['journal_entry_id', 'account_id', 'description', 'debit', 'credit', 'reference', 'currency', 'is_debit'];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'is_debit' => 'boolean',
    ];

    // العلاقة مع القيد الرئيسي



    public function details()
    {
        return $this->hasMany(JournalEntryDetail::class, 'journal_id');
    }

    // العلاقة مع الحساب
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    // دالة مساعدة لحساب الرصيد
    public function getBalanceAttribute()
    {
        return $this->debit - $this->credit;
    }
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

public function invoice()
{
    return $this->belongsTo(Invoice::class, 'reference', 'invoice_number');
}
}
