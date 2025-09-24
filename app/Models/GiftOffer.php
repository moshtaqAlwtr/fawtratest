<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GiftOffer extends Model
{
    protected $fillable = [
        'name',
        'target_product_id',
        'min_quantity',
        'gift_product_id',
        'gift_quantity',
        'start_date',
        'end_date',
        'is_for_all_clients',
        'is_for_all_employees',
        'is_active',
    ];

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_gift_offer');
    }
   
public function users()
{
    return $this->belongsToMany(User::class, 'employee_gift_offer', 'gift_id', 'user_id');
}

    public function targetProduct()
    {
        return $this->belongsTo(Product::class, 'target_product_id');
    }

    public function giftProduct()
    {
        return $this->belongsTo(Product::class, 'gift_product_id');
    }

    public function excludedClients()
{
    return $this->belongsToMany(Client::class, 'gift_offer_excluded_clients', 'offer_id', 'client_id');
}

}
