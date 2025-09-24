<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceDeterminant extends Model
{
    use HasFactory;

    protected $table = 'attendance_determinants';

    protected $fillable = [
        'name',
        'status',
        'enable_ip_verification',
        'ip_investigation',
        'allowed_ips',
        'enable_location_verification',
        'location_investigation',
        'latitude',
        'longitude',
        'radius',
        'radius_type',
        'capture_employee_image',
        'image_investigation'
    ];

    protected $casts = [
        'status' => 'boolean',
        'enable_ip_verification' => 'boolean',
        'enable_location_verification' => 'boolean',
        'capture_employee_image' => 'boolean',
        'allowed_ips' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius' => 'integer',
        'ip_investigation' => 'integer',
        'location_investigation' => 'integer',
        'image_investigation' => 'integer',
        'radius_type' => 'integer',
    ];

    // العلاقات
    public function locations()
    {
        return $this->hasMany(Location::class, 'attendance_determinant_id');
    }

    public function attendances()
    {
        return $this->hasMany(AttendanceDays::class, );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeWithLocationVerification($query)
    {
        return $query->where('enable_location_verification', true);
    }

    public function scopeWithIPVerification($query)
    {
        return $query->where('enable_ip_verification', true);
    }

    public function scopeWithImageCapture($query)
    {
        return $query->where('capture_employee_image', true);
    }

    // Helper Methods

    /**
     * فحص ما إذا كان الموظف يمكنه التسجيل
     */
    public function canEmployeeCheckIn($employeeId, $requestData = [])
    {
        $errors = [];
        $warnings = [];

        // فحص الموقع الجغرافي
        if ($this->enable_location_verification && isset($requestData['location'])) {
            if (!$this->isValidLocation($requestData['location'])) {
                if ($this->location_investigation == 1) {
                    $errors[] = 'خارج النطاق الجغرافي المحدد';
                } else {
                    $warnings[] = 'تم التسجيل من خارج النطاق المحدد';
                }
            }
        } elseif ($this->enable_location_verification && $this->location_investigation == 1) {
            $errors[] = 'بيانات الموقع الجغرافي مطلوبة';
        }

        // فحص عنوان IP
        if ($this->enable_ip_verification && isset($requestData['ip_address'])) {
            if (!$this->isValidIP($requestData['ip_address'])) {
                if ($this->ip_investigation == 1) {
                    $errors[] = 'عنوان IP غير مسموح';
                } else {
                    $warnings[] = 'تم التسجيل من عنوان IP غير مسجل';
                }
            }
        } elseif ($this->enable_ip_verification && $this->ip_investigation == 1) {
            $errors[] = 'عنوان IP مطلوب';
        }

        // فحص الصورة
        if ($this->capture_employee_image && $this->image_investigation == 1) {
            if (empty($requestData['image'])) {
                $errors[] = 'صورة الموظف مطلوبة';
            }
        }

        return [
            'allowed' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * فحص الموقع الجغرافي
     */
    public function isValidLocation($employeeLocation)
    {
        if (!$this->latitude || !$this->longitude || !$this->radius) {
            return true;
        }

        if (!isset($employeeLocation['latitude']) || !isset($employeeLocation['longitude'])) {
            return false;
        }

        $distance = $this->calculateDistance(
            $this->latitude,
            $this->longitude,
            $employeeLocation['latitude'],
            $employeeLocation['longitude']
        );

        $radiusInMeters = $this->radius_type == 2 ? $this->radius * 1000 : $this->radius;

        return $distance <= $radiusInMeters;
    }

    /**
     * فحص عنوان IP
     */
    public function isValidIP($ipAddress)
    {
        if (!$this->allowed_ips || empty($this->allowed_ips)) {
            return true;
        }

        return in_array($ipAddress, $this->allowed_ips);
    }

    /**
     * حساب المسافة بين نقطتين جغرافيتين (بالأمتار)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // بالأمتار

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    /**
     * تطبيق محددات الحضور على البيانات
     */
    public function validateAttendanceEntry($employeeId, $entryData)
    {
        $validation = $this->canEmployeeCheckIn($employeeId, $entryData);

        return [
            'allowed' => $validation['allowed'],
            'errors' => $validation['errors'],
            'warnings' => $validation['warnings'],
            'requires_approval' => false // يمكن تطويرها لاحقاً
        ];
    }

    /**
     * قواعد التحقق للنموذج
     */
    public static function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'enable_ip_verification' => 'nullable|boolean',
            'ip_investigation' => 'nullable|in:1,2',
            'allowed_ips' => 'nullable|array',
            'allowed_ips.*' => 'ip',
            'enable_location_verification' => 'nullable|boolean',
            'location_investigation' => 'nullable|in:1,2',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:1|max:10000',
            'radius_type' => 'nullable|in:1,2',
            'capture_employee_image' => 'nullable|boolean',
            'image_investigation' => 'nullable|in:1,2',
        ];
    }

    /**
     * Accessor لعرض الحالة
     */
    public function getStatusTextAttribute()
    {
        return $this->status == 0 ? 'نشط' : 'غير نشط';
    }

    /**
     * Accessor لعرض نوع التحقق من IP
     */
    public function getIpInvestigationTextAttribute()
    {
        if (!$this->enable_ip_verification) {
            return 'معطل';
        }
        return $this->ip_investigation == 1 ? 'مطلوب' : 'اختياري';
    }

    /**
     * Accessor لعرض نوع التحقق من الموقع
     */
    public function getLocationInvestigationTextAttribute()
    {
        if (!$this->enable_location_verification) {
            return 'معطل';
        }
        return $this->location_investigation == 1 ? 'مطلوب' : 'اختياري';
    }

    /**
     * Accessor لعرض نوع التحقق من الصورة
     */
    public function getImageInvestigationTextAttribute()
    {
        if (!$this->capture_employee_image) {
            return 'معطل';
        }
        return $this->image_investigation == 1 ? 'مطلوب' : 'اختياري';
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // تحويل القيم المنطقية إلى أعداد صحيحة للحفظ
            $model->status = $model->status ? 1 : 0;
            $model->enable_ip_verification = $model->enable_ip_verification ? 1 : 0;
            $model->enable_location_verification = $model->enable_location_verification ? 1 : 0;
            $model->capture_employee_image = $model->capture_employee_image ? 1 : 0;
        });

        static::updating(function ($model) {
            // تحويل القيم المنطقية إلى أعداد صحيحة للحفظ
            $model->status = $model->status ? 1 : 0;
            $model->enable_ip_verification = $model->enable_ip_verification ? 1 : 0;
            $model->enable_location_verification = $model->enable_location_verification ? 1 : 0;
            $model->capture_employee_image = $model->capture_employee_image ? 1 : 0;
        });
    }
}
