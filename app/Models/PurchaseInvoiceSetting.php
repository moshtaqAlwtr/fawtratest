<?php

// File: app/Models/PurchaseInvoiceSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'setting_key',
        'setting_name',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * التحقق من تفعيل إعداد معين
     *
     * @param string $key
     * @return bool
     */
    public static function isSettingActive($key)
    {
        return self::where('setting_key', $key)
                  ->where('is_active', true)
                  ->exists();
    }

    /**
     * الحصول على إعداد مفعل
     *
     * @param string $key
     * @return PurchaseInvoiceSetting|null
     */
    public static function getActiveSetting($key)
    {
        return self::where('setting_key', $key)
                  ->where('is_active', true)
                  ->first();
    }

    /**
     * الحصول على جميع الإعدادات المفعلة
     *
     * @return array
     */
    public static function getAllActiveSettings()
    {
        return self::where('is_active', true)
                  ->pluck('setting_key')
                  ->toArray();
    }

    /**
     * تفعيل إعداد
     *
     * @param string $key
     * @return bool
     */
    public static function activateSetting($key)
    {
        return self::where('setting_key', $key)
                  ->update(['is_active' => true]);
    }

    /**
     * إلغاء تفعيل إعداد
     *
     * @param string $key
     * @return bool
     */
    public static function deactivateSetting($key)
    {
        return self::where('setting_key', $key)
                  ->update(['is_active' => false]);
    }

    /**
     * تبديل حالة إعداد
     *
     * @param string $key
     * @return bool
     */
    public static function toggleSetting($key)
    {
        $setting = self::where('setting_key', $key)->first();

        if ($setting) {
            $setting->update(['is_active' => !$setting->is_active]);
            return $setting->is_active;
        }

        return false;
    }

    /**
     * Scope للإعدادات المفعلة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للإعدادات غير المفعلة
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope للترتيب
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('id');
    }
}
