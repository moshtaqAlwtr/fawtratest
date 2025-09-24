<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeavePermissions extends Model
{
    use HasFactory;

    protected $table = 'leave_permissions';

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'type',
        'leave_type',
        'submission_date',
        'notes',
        'attachments'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'submission_date',
        'created_at',
        'updated_at'
    ];

    // العلاقات
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // الثوابت للتسهيل
    const LEAVE_TYPES = [
        1 => 'الوصول المتأخر',
        2 => 'الانصراف المبكر'
    ];

    const TYPES = [
        1 => 'إجازة اعتيادية',
        2 => 'إجازة عرضية'
    ];

    // Helper methods
    public function getLeaveTypeTextAttribute()
    {
        return self::LEAVE_TYPES[$this->leave_type] ?? '';
    }

    public function getTypeTextAttribute()
    {
        return self::TYPES[$this->type] ?? '';
    }
}
