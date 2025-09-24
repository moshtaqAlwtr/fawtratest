<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryItem extends Model
{
    protected $fillable = ['name', 'type', 'status', 'description', 'salary_item_value', 'amount', 'calculation_formula', 'condition', 'account_id', 'salary_slips_id'];

    protected $casts = [
        'type' => 'integer',
        'status' => 'integer',
        'salary_item_value' => 'integer',
        'amount' => 'decimal:2',
    ];

    // Constants for type
    const TYPE_DEDUCTION = 1;
    const TYPE_ADDITION = 2;

    // Constants for status
    const STATUS_ACTIVE = 1;
    const STATUS_STOPPED = 2;
    const STATUS_NOT_ACTIVE = 3;

    // Constants for salary_item_value
    const VALUE_TYPE_AMOUNT = 1;
    const VALUE_TYPE_CALCULATION_FORMULA = 2;

    /**
     * Get the chart of account that owns the salary item
     */
    public function Account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public function salaryTemplate()
    {
        return $this->belongsTo(SalaryTemplate::class, 'salary_template_id');
    }
    public function salarySlips()
    {
        return $this->belongsTo(SalarySlip::class, 'salary_slips_id');
    }
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
}
