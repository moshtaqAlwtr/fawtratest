<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunctionalLevels extends Model
{
    use HasFactory;
    protected $table = 'functional_levels';
    protected $fillable = ['name', 'description', 'status'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'job_level_id');
    }

}
