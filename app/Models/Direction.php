<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'

    ];


public function groups()
{
    return $this->hasMany(Region_groub::class, 'directions_id');
}


}
