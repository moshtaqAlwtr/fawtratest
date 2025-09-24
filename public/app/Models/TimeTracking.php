<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TimeTracking
 * 
 * @property int $time_tracking_id
 * @property int|null $employee_id
 * @property Carbon|null $start_time
 * @property Carbon|null $end_time
 * @property string|null $task_description
 * @property Carbon|null $created_at
 *
 * @package App\Models
 */
class TimeTracking extends Model
{
	protected $table = 'time_tracking';
	protected $primaryKey = 'time_tracking_id';
	public $timestamps = false;

	protected $casts = [
		'employee_id' => 'int',
		'start_time' => 'datetime',
		'end_time' => 'datetime'
	];

	protected $fillable = [
		'employee_id',
		'start_time',
		'end_time',
		'task_description'
	];
}
