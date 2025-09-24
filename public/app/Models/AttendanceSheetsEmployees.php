<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSheetsEmployees extends Model
{
    use HasFactory;

    protected $table = 'attendance_sheets_employees';

    protected $fillable = [
        'attendance_sheets_id',
        'employee_id',
    ];
}
