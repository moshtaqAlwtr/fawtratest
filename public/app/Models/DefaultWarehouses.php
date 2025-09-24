<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultWarehouses extends Model
{
    use HasFactory;
    protected $table = 'default_warehouses';
    protected $fillable = ['storehouse_id','employee_id'];

    public function storehouse()
    {
        return $this->belongsTo(StoreHouse::class, 'storehouse_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
