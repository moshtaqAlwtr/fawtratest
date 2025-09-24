<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAttendance extends Model
{
    use HasFactory;

    // اسم الجدول (اختياري إذا كان نفس اسم الجمع)
    protected $table = 'client_attendances';

    // الأعمدة القابلة للتعبئة
    protected $fillable = [
        'client_id',
        'created_by',
        'date',
    ];

    // العلاقة مع العميل
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // العلاقة مع المستخدم (الذي أضاف الحضور)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // يمكنك إضافة وظائف أو Scope حسب الحاجة
}
