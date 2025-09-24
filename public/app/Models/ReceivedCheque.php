<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivedCheque extends Model
{
    use HasFactory;
    protected $table = 'received_cheques';
    protected $fillable = ['id', 'amount', 'issue_date', 'due_date', 'cheque_number', 'recipient_account_id', 'collection_account_id', 'payee_name', 'endorsement', 'name', 'description', 'attachment', 'created_at', 'updated_at'];

}
