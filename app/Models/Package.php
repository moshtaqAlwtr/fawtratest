<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        'commission_name',
        'members',
        'status',
        'price',
        'period',
        'duration',
        'payment_rate',
        'description',
    ];

    public function balanceType()
    {
        return $this->hasMany(BalanceType::class, 'package_id');
    }
    public function balanceTypes()
{
    return $this->belongsToMany(BalanceType::class, 'balance_type_package')
                ->withPivot('balance_value')
                ->withTimestamps();
}
}
