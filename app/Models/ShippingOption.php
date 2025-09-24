<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingOption extends Model
{
    protected $table = 'shipping_options';

    protected $fillable = ['name', 'description', 'cost', 'tax', 'status', 'display_order', 'default_account_id'];

    protected $casts = [
        'cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'status' => 'integer',
        'display_order' => 'integer',
    ];

    public function defaultAccount()
    {
        return $this->belongsTo(Account::class, 'default_account_id');
    }

    public function getStatusTextAttribute()
    {
        return $this->status == 1 ? 'نشط' : 'غير نشط';
    }

    public function isActive()
    {
        return $this->status == 1;
    }
}
