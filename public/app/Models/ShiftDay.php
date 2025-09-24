<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftDay extends Model
{
    use HasFactory;
    protected $table = 'shift_days';
    protected $guarded;

    public function shift()
    {
        return $this->belongsTo(Shift::class,'shift_id');
    }
}
