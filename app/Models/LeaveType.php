<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $table = 'leave_types';

    protected $fillable = [
        'name',
        'description',
        'color',
        'max_days_per_year',
        'max_consecutive_days',
        'applicable_after',
        'replace_weekends',
        'requires_approval',
    ];

}
