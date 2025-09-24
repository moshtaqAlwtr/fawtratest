<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryAdvance extends Model
{
    protected $fillable = ['employee_id', 'submission_date', 'amount', 'currency', 'installment_amount', 'total_installments', 'paid_installments', 'payment_rate', 'installment_start_date', 'treasury_id', 'pay_from_salary', 'tag', 'note'];

    protected $casts = [
        'submission_date' => 'date',
        'installment_start_date' => 'date',
        'amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'pay_from_salary' => 'boolean',
    ];

    // Currency constants
    const CURRENCY_SAR = 1;
    const CURRENCY_USD = 2;
    const CURRENCY_EUR = 3;
    const CURRENCY_GBP = 4;
    const CURRENCY_CNY = 5;

    // Payment rate constants
    const PAYMENT_MONTHLY = 1;
    const PAYMENT_WEEKLY = 2;
    const PAYMENT_DAILY = 3;

    /**
     * Get currency text
     */
    public function getCurrencyTextAttribute(): string
    {
        return match ($this->currency) {
            self::CURRENCY_SAR => 'SAR',
            self::CURRENCY_USD => 'USD',
            self::CURRENCY_EUR => 'EUR',
            self::CURRENCY_GBP => 'GBP',
            self::CURRENCY_CNY => 'CNY',
            default => 'Unknown',
        };
    }

    /**
     * Get payment rate text
     */
    public function getPaymentRateTextAttribute(): string
    {
        return match ($this->payment_rate) {
            self::PAYMENT_MONTHLY => 'شهري',
            self::PAYMENT_WEEKLY => 'أسبوعي',
            self::PAYMENT_DAILY => 'يومي',
            default => 'غير معروف',
        };
    }
public function payments()
{
    return $this->hasMany(InstallmentPayment::class);
}
public function account()
{
    return $this->belongsTo(Account::class);
}
    /**
     * Get the employee that owns the salary advance
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the treasury that owns the salary advance
     */
    public function treasury(): BelongsTo
    {
        return $this->belongsTo(Treasury::class);
    }
}
