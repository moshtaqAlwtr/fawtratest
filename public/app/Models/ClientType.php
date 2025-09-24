<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientType extends Model
{
    protected $table = 'type_client';
    protected $fillable = ['type'];
}
