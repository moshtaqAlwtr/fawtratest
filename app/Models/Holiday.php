<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;
    protected $table = 'holidays';
    protected $fillable = ['holiday_list_id', 'holiday_date', 'named'];

    public function holidayList()
    {
        return $this->belongsTo(HolidayList::class);
    }

}
