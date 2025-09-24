<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomShiftEmployee extends Model
{
    use HasFactory;

    protected $table = 'custom_shift_employees';

    protected $fillable = [
        'custom_shift_id',
        'employee_id',
    ];
}
