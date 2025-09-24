<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstallmentDetail extends Model
{
    use HasFactory;

    protected $table = 'installments_details';

    protected $fillable = [
        'amount',
        'installments_id',
        'status',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // علاقة مع القسط الأساسي
    public function installment()
    {
        return $this->belongsTo(Installment::class, 'installments_id');
    }
}
