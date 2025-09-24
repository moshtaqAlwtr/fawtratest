<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientGiftOffer extends Model
{
    use HasFactory;

    protected $table = 'client_gift_offer';

    protected $fillable = [
        'client_id',
        'gift_offer_id',
    ];

    // العلاقة مع العميل
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // العلاقة مع عرض الهدية
    public function giftOffer()
    {
        return $this->belongsTo(GiftOffer::class);
    }
}
