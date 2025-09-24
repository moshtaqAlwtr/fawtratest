<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveTypeLeaveType extends Model
{
    use HasFactory;
    protected $table = 'leave_type_leave_types';
    protected $fillable = ['leave_policy_id','leave_type_id'];
}
