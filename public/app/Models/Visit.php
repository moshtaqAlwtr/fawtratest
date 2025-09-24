<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'client_id',
        'visit_date',
        'status',
        'employee_latitude',
        'employee_longitude',
        'arrival_time',
        'departure_time',
        'notes',
        'visit_order',
        'client_latitude',
        'client_longitude',
        'distance',
        'recording_method',
        'is_approved',
        'approved_by'
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'arrival_time' => 'datetime',
        'departure_time' => 'datetime',
        'is_approved' => 'boolean',
        'distance' => 'decimal:2',
        'employee_latitude' => 'decimal:8',
        'employee_longitude' => 'decimal:8',
        'client_latitude' => 'decimal:8',
        'client_longitude' => 'decimal:8'
    ];

    // علاقة كثير إلى واحد مع جدول الموظفين (employees)
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // علاقة كثير إلى واحد مع جدول العملاء (clients)
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // علاقة كثير إلى واحد مع المسؤول الذي وافق على الزيارة
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // حساب المسافة بين موظف والعميل
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // نصف قطر الأرض بالمتر

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }

    // scope للزيارات القريبة
    public function scopeNearby($query, $latitude, $longitude, $radius = 1000)
    {
        return $query->whereRaw("
            ST_Distance_Sphere(
                point(client_longitude, client_latitude),
                point(?, ?)
            ) <= ?
        ", [$longitude, $latitude, $radius]);
    }
}
