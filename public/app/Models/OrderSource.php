<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSource extends Model
{
    protected $fillable = ['name', 'active', 'sort_order'];
}
