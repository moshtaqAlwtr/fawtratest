<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessData extends Model
{
    protected $fillable = [
        'business_name', // Add this field
        'business_email',
        'first_name',
        'last_name',
        'phone',
        'mobile',
        'street_address1',
        'street_address2',
        'city',
        'postal_code',
        'country',
    ];
}
