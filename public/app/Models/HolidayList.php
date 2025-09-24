<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HolidayList extends Model
{
    use HasFactory;
    protected $table = 'holiday_lists';
    protected $fillable = ['name'];

    public function holidays()
    {
        return $this->hasMany(Holiday::class, 'holiday_list_id');
    }

}
