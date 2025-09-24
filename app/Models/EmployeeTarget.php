<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTarget extends Model
{
    protected $fillable = ['user_id','monthly_target'];
    public function user()
{
    return $this->belongsTo(User::class);
}

}
