<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class JournalModification extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'user_id',
        'action',
        'debit',
        'credit',
        'local_debit',
        'local_credit',
        'old_data',
        'new_data'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    // العلاقة مع القيد
    public function journal_entry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
