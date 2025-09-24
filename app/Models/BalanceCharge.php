<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceCharge extends Model
{
    use HasFactory;

    protected $table = 'balance_charges';

    protected $fillable = [
        'client_id',
        'balance_type_id',
        'value',
        'start_date',
        'end_date',
        'description',
        'status',
        'remaining',
        'consumer',
        'contract_type',
    ];

    // Define relationships if needed
    public function client()
{
    return $this->belongsTo(Client::class);
}

public function balanceType()
{
    return $this->belongsTo(BalanceType::class);
}
}
