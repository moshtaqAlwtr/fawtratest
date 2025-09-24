<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';
    protected $fillable = ['name', 'code', 'parent_id', 'type', 'category', 'balance', 'is_active', 'branch_id', 'client_id'];

    // العلاقة مع الحسابات الفرعية
    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }
    public function client()
{
    return $this->belongsTo(Client::class, 'client_id');
}

    public function updateBalance($amount, $operation = 'add')
    {
        if ($operation === 'add') {
            $this->balance += $amount;
        } elseif ($operation === 'subtract') {
            $this->balance -= $amount;
        }
        $this->save();
    }

    // العلاقة مع الحساب الرئيسي
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function ancestors()
    {
        $path = [];
        $account = $this;

        while ($account) {
            $path[] = $account;
            $account = $account->parent;
        }

        return array_reverse($path); // لعرض الهيكل من الجذر إلى الفرع الأخير
    }
    // العلاقة مع المعاملات
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    // العلاقة مع الحسابات التابعة للفرع
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // العلاقة مع الموظفين
    public function salesManager()
    {
        return $this->belongsToMany(Employee::class);
    }

    // العلاقة مع الحسابات التابعة للموظفين الذين يحصلون عليها صلاحية التصريح

    // في موديل Account
    public function customer()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function journalEntries()
    {
        return $this->hasMany(JournalEntryDetail::class, 'account_id');
    }
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
    public function treasury()
    {
        return $this->hasOne(Treasury::class, 'account_id');
    }
    public function getAvailableCredit()
    {
        return $this->credit_limit - $this->current_balance;
    }
public function receipts()
{
    return $this->hasMany(Receipt::class, 'account_id');
}
}
