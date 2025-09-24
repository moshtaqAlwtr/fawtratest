<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Department extends Model
{
    use HasFactory;
    protected $table = 'departments';
    protected $fillable = ['name','short_name','status','description','created_at','updated_at'];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'department_employee', 'department_id', 'employee_id');
    }

}
