<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_plan_id',
        'client_id',
        'day_of_week',
        'scheduled_time',
        'visit_order',
        'estimated_distance',
        'priority',
        'status',
        'notes'
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'estimated_distance' => 'decimal:2',
        'priority' => 'integer',
        'visit_order' => 'integer'
    ];

    // علاقة مع الخطة الأسبوعية
    public function weeklyPlan()
    {
        return $this->belongsTo(WeeklyVisitPlan::class, 'weekly_plan_id');
    }

    // علاقة مع العميل
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // علاقة مع الزيارة الفعلية (إذا تمت)
    public function actualVisit()
    {
        return $this->hasOne(Visit::class, 'client_id', 'client_id')
            ->whereDate('visit_date', function($query) {
                // حساب التاريخ الفعلي للزيارة بناءً على يوم الأسبوع
                $query->selectRaw("DATE_ADD(?, INTERVAL CASE
                    WHEN ? = 'saturday' THEN 0
                    WHEN ? = 'sunday' THEN 1
                    WHEN ? = 'monday' THEN 2
                    WHEN ? = 'tuesday' THEN 3
                    WHEN ? = 'wednesday' THEN 4
                    WHEN ? = 'thursday' THEN 5
                    WHEN ? = 'friday' THEN 6
                END DAY)", [
                    $this->weeklyPlan->week_start_date,
                    $this->day_of_week, $this->day_of_week,
                    $this->day_of_week, $this->day_of_week,
                    $this->day_of_week, $this->day_of_week,
                    $this->day_of_week
                ]);
            });
    }

    // التحقق من اكتمال الزيارة
    public function getIsCompletedAttribute()
    {
        return $this->actualVisit()->exists();
    }

    // الحصول على التاريخ الفعلي للزيارة
    public function getVisitDateAttribute()
    {
        $weekStart = $this->weeklyPlan->week_start_date;
        $dayOffset = $this->getDayOffset();

        return $weekStart->addDays($dayOffset);
    }

    // حساب إزاحة اليوم
    private function getDayOffset()
    {
        $dayOffsets = [
            'saturday' => 0,
            'sunday' => 1,
            'monday' => 2,
            'tuesday' => 3,
            'wednesday' => 4,
            'thursday' => 5,
            'friday' => 6
        ];

        return $dayOffsets[$this->day_of_week] ?? 0;
    }

    // scope للزيارات حسب اليوم
    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    // scope للزيارات المرتبة حسب الأولوية والترتيب
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority')->orderBy('visit_order');
    }

    // scope للزيارات المكتملة
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // scope للزيارات المجدولة
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    // تحديث حالة الزيارة إلى مكتملة
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    // تحديث حالة الزيارة إلى ملغية
    public function markAsCancelled()
    {
        $this->update(['status' => 'cancelled']);
    }

    // إعادة جدولة الزيارة
    public function reschedule($newDay, $newTime = null)
    {
        $this->update([
            'day_of_week' => $newDay,
            'scheduled_time' => $newTime,
            'status' => 'rescheduled'
        ]);
    }
}
