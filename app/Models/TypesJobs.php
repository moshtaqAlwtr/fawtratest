<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypesJobs extends Model
{
    use HasFactory;
    protected $table = 'types_jobs';
    protected $fillable = ['name', 'description', 'status' ,'created_at', 'updated_at'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'job_type_id');
    }

}
