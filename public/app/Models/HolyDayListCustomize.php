<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HolyDayListCustomize extends Model
{
    use HasFactory;
    protected $table = 'holy_day_list_customizes';
    protected $fillable = ['id', 'use_rules', 'holiday_list_id', 'branch_id', 'department_id', 'job_title_id', 'created_at', 'updated_at'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function jobTitle()
    {
        return $this->belongsTo(JopTitle::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'holy_day_list_customize_employees', 'holyday_customizes_id', 'employee_id');
    }
}
