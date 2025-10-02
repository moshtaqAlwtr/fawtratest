<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosGeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'default_customer_id',
        'invoice_template',
        'active_payment_method_id',
        'default_payment_method_id',
        'allowed_categories_type',
        'allowed_categories_ids',
        'enable_departments',
        'apply_custom_fields_validation',
        'show_product_images',
        'show_print_window_after_confirm',
        'accounting_system_per_invoice',
        'enable_auto_settlement',
        'enable_sales_settlement',
        'profit_account_id',
        'loss_account_id',
    ];

    protected $casts = [
        'allowed_categories_ids' => 'array',
        'enable_departments' => 'boolean',
        'apply_custom_fields_validation' => 'boolean',
        'show_product_images' => 'boolean',
        'show_print_window_after_confirm' => 'boolean',
        'accounting_system_per_invoice' => 'boolean',
        'enable_auto_settlement' => 'boolean',
        'enable_sales_settlement' => 'boolean',
    ];

    // العلاقات
    public function defaultCustomer()
    {
        return $this->belongsTo(Client::class, 'default_customer_id');
    }

    public function activePaymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'active_payment_method_id');
    }

    public function defaultPaymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'default_payment_method_id');
    }

    public function profitAccount()
    {
        return $this->belongsTo(Account::class, 'profit_account_id');
    }

    public function lossAccount()
    {
        return $this->belongsTo(Account::class, 'loss_account_id');
    }

    // Helper method to get settings (Singleton pattern)
    public static function getSettings()
    {
        return self::first() ?: new self();
    }

    // Helper method to get active payment methods
    public function getActivePaymentMethods()
    {
        if (!$this->active_payment_method_ids) {
            return collect();
        }
        
        return PaymentMethod::whereIn('id', $this->active_payment_method_ids)
            ->where('is_active', true)
            ->get();
    }
    public function getSelectedCategories()
    {
        if ($this->allowed_categories_type === 'all') {
            return Category::all();
        } elseif ($this->allowed_categories_type === 'only' && $this->allowed_categories_ids) {
            return Category::whereIn('id', $this->allowed_categories_ids)->get();
        } elseif ($this->allowed_categories_type === 'except' && $this->allowed_categories_ids) {
            return Category::whereNotIn('id', $this->allowed_categories_ids)->get();
        }
        
        return collect();
    }
}