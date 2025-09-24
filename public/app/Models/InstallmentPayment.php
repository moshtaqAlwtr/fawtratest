<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentPayment extends Model
{
   protected $fillable = ['amount', 'payment_date', 'status','created_by','account_id','salary_advance_id','installment_number','due_date'];
    
    public function salaryAdvance()
    {
        return $this->belongsTo(SalaryAdvance::class);
    }
    
    public function account()
{
    return $this->belongsTo(Account::class);
}
}
