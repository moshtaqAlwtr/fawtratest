<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'signer_name',
        'signer_role',
        'signature_data',
        'amount_paid'
    ];

    // علاقة مع الفاتورة (Invoice)
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
