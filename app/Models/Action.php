<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'actions';

    // Define the fillable properties
    protected $fillable = [
        'name',
        'color',
    ];
}
