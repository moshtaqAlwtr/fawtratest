<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    // تعريف ثوابت الحالة
    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_IGNORED = 3;
    const STATUS_RESCHEDULED = 4;

    // تحديد الحقول القابلة للتعبئة - تأكد من أن هذه الحقول موجودة في جدولك
    protected $fillable = [
        'client_id',
        'employee_id',
        'title',
        'description',
        'appointment_date', // تأكد من أن هذا هو اسم الحقل في جدولك
        'status',
        'created_by',
    ];

    // تحديد الحقول التي يتم تحويلها إلى أنواع معينة
    protected $casts = [
        'appointment_date' => 'datetime', // تأكد من أن هذا هو اسم الحقل في جدولك
        'status' => 'integer',
    ];

    // العلاقة مع المستخدم الذي أنشأ الموعد
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // العلاقة مع العميل
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    // العلاقة مع الموظف
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // الحصول على النص العربي للحالة
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'تم جدولته';
            case self::STATUS_COMPLETED:
                return 'تم';
            case self::STATUS_IGNORED:
                return 'صرف النظر عنه';
            case self::STATUS_RESCHEDULED:
                return 'تم جدولته مجدداً';
            default:
                return 'غير معروف';
        }
    }

    // الحصول على لون الحالة
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'bg-warning text-dark';
            case self::STATUS_COMPLETED:
                return 'bg-success text-white';
            case self::STATUS_IGNORED:
                return 'bg-danger text-white';
            case self::STATUS_RESCHEDULED:
                return 'bg-info text-white';
            default:
                return 'bg-secondary text-white';
        }
    }

    // نطاق للمواعيد المنتظرة
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // نطاق للمواعيد المكتملة
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // نطاق لمواعيد اليوم
    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }
}