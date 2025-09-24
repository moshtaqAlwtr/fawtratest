<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeClientVisit extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'client_id', 'week_number', 'year', 'day_of_week','status'];

    // العلاقة مع الموظف
     public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id')->with(['locations', 'status_client']);
    }
}
