<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosSessionDetail extends Model
{
    protected $table = 'pos_session_details';

    protected $fillable = [
        'session_id',
        'transaction_type',
        'reference_number',
        'amount',
        'payment_method',
        'cash_amount',
        'card_amount',
        'description',
        'metadata',
        'transaction_time'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'card_amount' => 'decimal:2',
        'metadata' => 'array',
        'transaction_time' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(PosSession::class, 'session_id');
    }
}