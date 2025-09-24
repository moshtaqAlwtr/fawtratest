<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentNote extends Model
{


    protected $fillable = [
        'user_id',
        'client_id',
        'appointment_id',
        'date',
        'time',
        'action_type',
        'action_type',
        'notes',
        'attachments',

        'share_with_client',

    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'attachments' => 'array'
    ];

    // العلاقة مع الموعد
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
