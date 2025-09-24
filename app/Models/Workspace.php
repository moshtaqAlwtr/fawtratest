<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'admin_id',  // تأكد من أن هذا يطابق اسم العمود في قاعدة البيانات
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * العلاقة مع المالك/المنشئ
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id'); // استخدم admin_id بدلاً من user_id
    }

    /**
     * العلاقة مع الأعضاء (Many to Many)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workspace_user')
                    ->withTimestamps();
    }

    /**
     * العلاقة مع المشاريع
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * الحصول على المساحة الرئيسية للمستخدم الحالي
     */
    public static function primary($userId = null)
    {
        $userId = $userId ?: auth()->id();

        return static::where('admin_id', $userId) // استخدم admin_id
                    ->where('is_primary', true)
                    ->first();
    }

    /**
     * التحقق من أن المستخدم عضو في هذه المساحة
     */
    public function hasMember($userId)
    {
        return $this->users()->where('user_id', $userId)->exists(); // استخدم user_id للجدول المحوري
    }

    /**
     * التحقق من أن المستخدم مالك هذه المساحة
     */
    public function isOwner($userId)
    {
        return $this->admin_id == $userId; // استخدم admin_id
    }
}
