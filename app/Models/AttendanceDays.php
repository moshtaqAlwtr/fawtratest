<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceDays extends Model
{
    use HasFactory;

    // اسم الجدول
    protected $table = 'attendance_days';

    // الأعمدة المسموح بملئها
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'status',
        'start_shift',
        'end_shift',
        'check_in_time',
        'check_out_time',
        'login_time',
        'logout_time',
        'absence_type',
        'absence_balance',
        'check_in_method',
        'check_out_method',
        'scanned_via_barcode',
        'ip_address',
        'user_agent',
        'notes',
    ];

    /**
     * علاقة الحضور بالموظف
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
     public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', Carbon::today());
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    // Accessors
    public function getWorkingHoursAttribute()
    {
        if ($this->check_in_time && $this->check_out_time) {
            $checkIn = Carbon::parse($this->check_in_time);
            $checkOut = Carbon::parse($this->check_out_time);
            return $checkOut->diffInMinutes($checkIn);
        }
        return 0;
    }

    public function getIsCheckedInAttribute()
    {
        return !is_null($this->check_in_time) && is_null($this->check_out_time);
    }

    public function getIsCompleteAttribute()
    {
        return !is_null($this->check_in_time) && !is_null($this->check_out_time);
    }

    // Helper Methods
    public static function getTodayAttendance($employeeId)
    {
        return self::where('employee_id', $employeeId)
                   ->whereDate('attendance_date', Carbon::today())
                   ->first();
    }

    public static function checkIfAlreadyCheckedIn($employeeId)
    {
        $attendance = self::getTodayAttendance($employeeId);
        return $attendance && $attendance->check_in_time && !$attendance->check_out_time;
    }

    public static function checkIfAlreadyCheckedOut($employeeId)
    {
        $attendance = self::getTodayAttendance($employeeId);
        return $attendance && $attendance->check_in_time && $attendance->check_out_time;
    }
}
