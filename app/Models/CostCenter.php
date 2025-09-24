<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CostCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'is_main',
        'parent_id',
        'created_by',
    ];


    // العلاقة مع المركز الأب
    public function parent()
    {
        return $this->belongsTo(CostCenter::class, 'parent_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    // العلاقة مع المراكز الفرعية
    public function children()
    {
        return $this->hasMany(CostCenter::class, 'parent_id');
    }

    // Accessors & Mutators
    public function getTypeTextAttribute()
    {
        return $this->is_main == 1 ? 'رئيسي' : 'فرعي';
    }

    // الحصول على كل المراكز الرئيسية
    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    // الحصول على المراكز النشطة
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // الحصول على المراكز الفرعية
    public function scopeSub($query)
    {
        return $query->where('is_main', false);
    }
}
