<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
        protected $fillable = [
        'name',
        'region_id',
        'client_id',
    ];

   public function Region()
{
    return $this->belongsTo(Region_groub::class, 'region_id');
}
public function client()
{
    return $this->belongsTo(Client::class, 'client_id');
}
}
