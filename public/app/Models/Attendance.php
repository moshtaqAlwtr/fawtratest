<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Attendance
 * 
 * @property int $attendance_id
 * @property int|null $employee_id
 * @property Carbon $date
 * @property Carbon|null $check_in_time
 * @property Carbon|null $check_out_time
 * @property Carbon|null $break_start_time
 * @property Carbon|null $break_end_time
 * @property string|null $status
 * @property string|null $location
 *
 * @package App\Models
 */
class Attendance extends Model
{
	protected $table = 'attendance';
	protected $primaryKey = 'attendance_id';
	public $timestamps = false;

	protected $casts = [
		'employee_id' => 'int',
		'date' => 'datetime',
		'check_in_time' => 'datetime',
		'check_out_time' => 'datetime',
		'break_start_time' => 'datetime',
		'break_end_time' => 'datetime'
	];

	protected $fillable = [
		'employee_id',
		'date',
		'check_in_time',
		'check_out_time',
		'break_start_time',
		'break_end_time',
		'status',
		'location'
	];
}
