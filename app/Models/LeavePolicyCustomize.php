<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeavePolicyCustomize extends Model
{
    use HasFactory;
    protected $fillable = ['use_rules', 'leave_policy_id', 'branch_id', 'department_id', 'job_title_id'];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'leave_policy_customize_employees', 'policy_customize_id', 'employee_id');
    }

}
