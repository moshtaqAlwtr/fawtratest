<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings_sales';
    protected $fillable = ['setting_key', 'value'];

    // جلب قيمة إعداد
    public static function get($key, $default = null) {
        $row = static::where('setting_key', $key)->first();
        return $row ? $row->value : $default;
    }

    // حفظ قيمة إعداد
    public static function set($key, $value) {
        return static::updateOrCreate(['setting_key' => $key], ['value' => $value]);
    }
}
