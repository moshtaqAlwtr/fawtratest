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

    // تحويل الحالة النصية إلى رقم
    public static $statusMap = [
        'pending' => self::STATUS_PENDING,
        'completed' => self::STATUS_COMPLETED,
        'ignored' => self::STATUS_IGNORED,
        'rescheduled' => self::STATUS_RESCHEDULED,
    ];

    // تحويل الرقم إلى نص الحالة
    public static $statusTextMap = [
        self::STATUS_PENDING => 'pending',
        self::STATUS_COMPLETED => 'completed',
        self::STATUS_IGNORED => 'ignored',
        self::STATUS_RESCHEDULED => 'rescheduled',
    ];

    // تحويل الرقم إلى النص العربي
    public static $statusArabicMap = [
        self::STATUS_PENDING => 'تم جدولته',
        self::STATUS_COMPLETED => 'تم',
        self::STATUS_IGNORED => 'صرف النظر عنه',
        self::STATUS_RESCHEDULED => 'تم جدولته مجددا',
    ];

    // إضافة نطاق الفرز حسب التاريخ
    public function scopeOrderByAppointmentDate($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // الحصول على النص العربي للحالة
    public function getStatusTextAttribute()
    {
        return self::$statusArabicMap[$this->status] ?? 'غير معروف';
    }

    // الحصول على لون الحالة
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-warning text-dark',
            self::STATUS_COMPLETED => 'bg-success text-white',
            self::STATUS_IGNORED => 'bg-danger text-white',
            self::STATUS_RESCHEDULED => 'bg-info text-white',
            default => 'bg-secondary text-white',
        };
    }

    // تحديد الحقول القابلة للتعبئة
    protected $fillable = ['client_id', 'title', 'description', 'date', 'status','created_by'];

    // تحديد الحقول التي يتم تحويلها إلى أنواع معينة
    protected $casts = [
        'date' => 'datetime',
        'status' => 'integer',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function notes()
    {
        return $this->hasMany(AppointmentNote::class);
    }
    // العلاقة مع العميل
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

}
