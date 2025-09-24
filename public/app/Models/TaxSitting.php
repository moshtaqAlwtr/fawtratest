<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSitting extends Model
{
   protected $fillable = ['name', 'tax', 'type'];
}
