<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;
    protected $table = 'shifts';
    protected $fillable = ['id', 'name', 'type', 'created_at', 'updated_at'];

    public function days()
    {
        return $this->hasMany(ShiftDay::class,'shift_id');
    }
}
