<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JopTitle extends Model
{
    use HasFactory;
    protected $table = 'jop_titles';
    protected $fillable = ['id','name', 'description', 'status', 'department_id', 'created_at', 'updated_at'];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class,'job_title_id');
    }

}
