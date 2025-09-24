<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeavePolicyCustomizeEmployee extends Model
{
    use HasFactory;
    protected $table = 'leave_policy_customizes_employees';
    protected $guarded = [];
}
