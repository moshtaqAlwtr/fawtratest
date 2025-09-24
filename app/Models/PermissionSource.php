<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionSource extends Model
{
    use HasFactory;

    protected $table = 'permission_sources';

    protected $fillable = [
        'name',
        'category',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ربط مع أذونات المخزن
    public function warehousePermits()
    {
        return $this->hasMany(WarehousePermits::class, 'permission_type', 'id');
    }

    // ربط مع حركة المخزون
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'permission_source_id', 'id');
    }
}
