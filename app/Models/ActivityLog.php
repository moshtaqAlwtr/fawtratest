<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ActivityLog
 * 
 * @property int $log_id
 * @property int|null $user_id
 * @property string $activity_type
 * @property string $activity_description
 * @property Carbon|null $activity_date
 * @property string|null $ip_address
 * @property string|null $module_affected
 *
 * @package App\Models
 */
class ActivityLog extends Model
{
	protected $table = 'activity_logs';
	protected $primaryKey = 'log_id';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'activity_date' => 'datetime'
	];

	protected $fillable = [
		'user_id',
		'activity_type',
		'activity_description',
		'activity_date',
		'ip_address',
		'module_affected'
	];
}
