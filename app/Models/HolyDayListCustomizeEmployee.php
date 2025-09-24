<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HolyDayListCustomizeEmployee extends Model
{
    use HasFactory;
    protected $table = 'holy_day_list_customize_employees';
    protected $fillable = [ 'holyday_customizes_id', 'employee_id'];

}
