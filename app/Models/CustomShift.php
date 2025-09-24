<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomShift extends Model
{
    use HasFactory;
    protected $table = 'custom_shifts';
    protected $guarded = [];

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'custom_shifts_employees', 'custom_shifts_id', 'employee_id');
    }


}
