<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{
    protected $table = 'notifications';
    protected $fillable = [
        'user_id',       // يجب إضافته
        'title',
'receiver_id',
        'description',
        'message',      // يجب إضافته
        'read',
        'type',
        'data'          // يجب إضافته لاحتواء البيانات الإضافية
    ];

    protected $casts = [
        'data' => 'array' // لتحويل حقل البيانات إلى array تلقائياً
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
public function receiver(){
    return $this->belongsTo(User::class);
}
}
