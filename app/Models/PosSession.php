<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class PosSession extends Model
{
    use SoftDeletes;

    protected $table = 'pos_sessions';

    protected $fillable = [
        'session_number',
        'user_id',
        'device_id', 
        'shift_id',
        'started_at',
        'ended_at',
        'opening_balance',
        'closing_balance',
        'actual_closing_balance',
        'difference',
        'total_transactions',
        'total_sales',
        'total_cash',
        'total_card',
        'total_returns',
        'status',
        'closing_notes'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'actual_closing_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_cash' => 'decimal:2',
        'total_card' => 'decimal:2',
        'total_returns' => 'decimal:2',
    ];

    // العلاقات
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(CashierDevice::class, 'device_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(shifts_pos::class, 'shift_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PosSessionDetail::class, 'session_id');
    }

    // دوال مساعدة
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function getDurationAttribute(): string
    {
        $start = $this->started_at;
        $end = $this->ended_at ?? now();
        
        return $start->diffForHumans($end, true);
    }

    public function getExpectedCashAttribute()
    {
        return $this->opening_balance + $this->total_cash - $this->total_returns;
    }

    // إنشاء رقم جلسة تلقائي
    public static function generateSessionNumber(): string
    {
        $date = now()->format('Ymd');
        $lastSession = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastSession ? (intval(substr($lastSession->session_number, -3)) + 1) : 1;
        
        return $date . sprintf('%03d', $sequence);
    }

    // نطاقات
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
public function scopeForDevice($query, $deviceId)
{
    return $query->where('device_id', $deviceId);
}
    public function scopeToday($query)
    {
        return $query->whereDate('started_at', today());
    }

        // التحقق من صلاحيات العمليات
    public function canBeResumed()
    {
        return $this->status === 'suspended';
    }

    public function canBeClosed()
    {
        return $this->status === 'active';
    }

    public function canBeDeleted()
    {
        return $this->status === 'active' && $this->total_transactions === 0;
    }

}

