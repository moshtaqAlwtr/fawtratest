<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_date',
        'borrower_name',
        'amount',
        'description',
        'account_id',
        'treasury_id',
        'attachment',
        'status',
        'repayment_date',
        'interest_rate',
        'loan_type'
    ];

    protected $dates = [
        'voucher_date',
        'repayment_date'
    ];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function treasury()
    {
        return $this->belongsTo(Treasury::class, 'treasury_id');
    }

    public function details()
    {
        return $this->hasMany(LoanVoucherDetail::class);
    }
}
