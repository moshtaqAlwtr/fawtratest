<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChequeBook extends Model
{
    use HasFactory;

    protected $table = 'cheque_books';

    protected $fillable = ['id', 'bank_id', 'cheque_book_number', 'currency', 'start_serial_number', 'end_serial_number', 'status', 'notes', 'created_at', 'updated_at'];

    public function bank()
    {
        return $this->belongsTo(Treasury::class, 'bank_id');
    }

    public function cheques()
    {
        return $this->hasMany(PayableCheque::class, 'cheque_book_id');
    }

}
