<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSheets extends Model
{
    use HasFactory;
    protected $table = 'attendance_sheets';
    protected $fillable = ['id', 'from_date', 'to_date', 'use_rules', 'status', 'branch_id', 'department_id', 'job_title_id', 'shift_id', 'created_at', 'updated_at'];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'attendance_sheets_employees', 'attendance_sheets_id', 'employee_id');
    }
public function attendanceDays()
{
    return $this->hasMany(AttendanceDays::class);
}
}
