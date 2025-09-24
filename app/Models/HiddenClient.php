<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HiddenClient extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'hidden_at',
        'expires_at',
    ];

    protected $casts = [
        'hidden_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع العميل
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * التحقق من انتهاء صلاحية الإخفاء
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * حذف العملاء المخفيين المنتهية الصلاحية
     */
    public static function cleanupExpired(): void
    {
        static::where('expires_at', '<', now())->delete();
    }

    /**
     * الحصول على العملاء المخفيين للمستخدم
     */
    public static function getHiddenClientsForUser($userId): array
    {
        // تنظيف العملاء المنتهية الصلاحية أولاً
        static::cleanupExpired();

        return static::where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->pluck('client_id')
            ->toArray();
    }
}