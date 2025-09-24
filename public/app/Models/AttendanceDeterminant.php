<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceDeterminant extends Model
{
    use HasFactory;
    protected $table = 'attendance_determinants';
    protected $fillable =[
        'name', 'status', 'enable_ip_verification',
        'ip_investigation', 'allowed_ips', 'enable_location_verification',
        'location_investigation', 'radius',
        'radius_type', 'capture_employee_image', 'image_investigation'
    ];
}
