<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region_groub extends Model
{
    protected $table = 'region_groubs';
    protected $fillable = [ 'name'  , 'branch_id','directions_id' ];


     public function neighborhoods()
     {
         return $this->hasMany(Neighborhood::class, 'region_id');
     }

public function clients()
{
    return $this->belongsTo(Client::class);

}
public function branch()
{
    return $this->belongsTo(Branch::class , 'branch_id');
}
public function direction()
{
    return $this->belongsTo(Direction::class, 'directions_id');
    // لاحظ تغيير اسم الدالة إلى direction (مفرد) لأنها علاقة belongsTo
}
// App\Models\Region.php
public function region_groubs()
{
    return $this->belongsToMany(Region_groub::class); // اسم الجدول الوسيط حسب مشروعك
}

}
