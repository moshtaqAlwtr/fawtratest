<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class BalanceConsumption extends Model
{
    use HasFactory;

    protected $table = 'balance_consumptions';

    protected $fillable = [
        'client_id',
        'balance_type_id',
        'invoice_id',
        'consumption_date',
        'status',
        'used_balance',
        'description',
        'contract_type',
    ];

    // Cast consumption_date to a Carbon date object
    protected $casts = [
        'consumption_date' => 'date',
    ];

    // Define relationships
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function balanceType()
    {
        return $this->belongsTo(BalanceType::class, 'balance_type_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
