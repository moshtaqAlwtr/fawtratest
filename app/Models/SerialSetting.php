<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerialSetting extends Model
{
    protected $fillable = ['section', 'current_number', 'number_of_digits', 'prefix', 'mode'];

    /**
     * توليد الرقم التسلسلي لقسم معين
     */
    public static function generateSerial($section)
    {
        $setting = self::firstOrCreate(
            ['section' => $section],
            ['current_number' => 0, 'number_of_digits' => 5, 'prefix' => '', 'mode' => 'auto']
        );

        // زيادة الرقم الحالي
        $setting->current_number++;
        $setting->save();

        // توليد الرقم التسلسلي مع البادئة والأصفار
        $serialNumber = str_pad($setting->current_number, $setting->number_of_digits, '0', STR_PAD_LEFT);
        return $setting->prefix . $serialNumber;
    }
}
