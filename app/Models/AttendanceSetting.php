<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;
    protected $table = 'attendance_settings';
    protected $fillable = ['start_month', 'start_day', 'allow_second_shift', 'allow_backdated_requests', 'direct_manager_approval', 'department_manager_approval', 'employees_approval'];
    
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'attendance_settings_employees', 'attendance_settings_id', 'employee_id');
    }

}
