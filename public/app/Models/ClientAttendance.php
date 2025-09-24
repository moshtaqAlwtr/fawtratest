<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ClientAttendance
 * 
 * @property int $attendance_id
 * @property int $client_id
 * @property string $event_name
 * @property Carbon $date
 * @property string|null $status
 * @property string|null $notes
 *
 * @package App\Models
 */
class ClientAttendance extends Model
{
	protected $table = 'client_attendance';
	protected $primaryKey = 'attendance_id';
	public $timestamps = false;

	protected $casts = [
		'client_id' => 'int',
		'date' => 'datetime'
	];

	protected $fillable = [
		'client_id',
		'event_name',
		'date',
		'status',
		'notes'
	];
}
