<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountSetting extends Model
{
    //
    protected $fillable = [
        'business_type', // Add this field
        'currency',
        'timezone',
      
    ];


}
