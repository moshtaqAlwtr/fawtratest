<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionSource extends Model
{
    use HasFactory;

    protected $table = 'permission_sources';

    // الحقول القابلة للتعبئة (fillable)
    protected $fillable = [
        'name',
        'description',
    ];

    // لو حبيت تربطه بأذونات المخزن (WarehousePermits) كمثال
    public function warehousePermits()
    {
        return $this->hasMany(WarehousePermits::class, 'permission_type', 'id');
    }
}
