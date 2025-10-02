<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class shifts_pos extends Model
{
    use HasFactory, SoftDeletes;

    // ربط الموديل بالجدول الجديد
    protected $table = 'shifts_pos';

    protected $fillable = [
        'name',
        'parent_id',
        'attachment',
        'description',
    ];

    // العلاقة مع الورديات الأب
    public function parent()
    {
        return $this->belongsTo(shifts_pos::class, 'parent_id');
    }

    // العلاقة مع الورديات الأبناء
    public function children()
    {
        return $this->hasMany(shifts_pos::class, 'parent_id');
    }
}
