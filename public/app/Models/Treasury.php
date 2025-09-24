<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treasury extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'name', 'type', 'status', 'description', 'bank_name', 'account_number', 'currency','is_main',
        'deposit_permissions', 'withdraw_permissions', 'value_of_deposit_permissions',
        'value_of_withdraw_permissions', 'created_at', 'updated_at'
    ];

    public function payments()
    {
        return $this->hasMany(PaymentsProcess::class, 'treasury_id'); // المفتاح الأساسي يجب أن يكون 'id'
    }
    public function account()
{
    return $this->belongsTo(Account::class, 'account_id');
}

}
