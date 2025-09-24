<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayableCheque extends Model
{
    use HasFactory;
    protected $table = 'payable_cheques';
    protected $fillable = ['id', 'amount', 'issue_date', 'due_date', 'bank_id', 'cheque_book_id', 'cheque_number', 'recipient_account_id', 'payee_name', 'description', 'attachment', 'created_at', 'updated_at'];

    public function bank()
    {
        return $this->belongsTo(Treasury::class, 'bank_id');
    }

    public function cheque_book()
    {
        return $this->belongsTo(ChequeBook::class, 'cheque_book_id');
    }
}
