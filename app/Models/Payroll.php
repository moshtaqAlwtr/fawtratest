<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'registration_date',
        'start_date',
        'end_date',
        'select_emp_role',
        'receiving_cycle',
        'attendance_check',
        'department_id',
        'jop_title_id',
        'branch_id',
    ];

    /**
     * Relationships
     */

    // علاقة مع قسم
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // علاقة مع المسمى الوظيفي
    public function jobTitle()
{
    return $this->belongsTo(JopTitle::class, 'job_title_id');
}

    // علاقة مع الفرع
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'payroll_employee', 'payroll_id', 'employee_id');
    }

}
