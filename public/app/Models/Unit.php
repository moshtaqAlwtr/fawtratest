<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit_type_id',
        'priority',
        'status',
        'description',
    ];

    // العلاقة مع نوع الوحدة
    public function unitType()
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id');
    }
}
