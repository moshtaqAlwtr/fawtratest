<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Report
 * 
 * @property int $report_id
 * @property string $report_name
 * @property int $created_by
 * @property string|null $report_type
 * @property Carbon|null $created_at
 * @property string|null $data
 *
 * @package App\Models
 */
class Report extends Model
{
	protected $table = 'reports';
	protected $primaryKey = 'report_id';
	public $timestamps = false;

	protected $casts = [
		'created_by' => 'int'
	];

	protected $fillable = [
		'report_name',
		'created_by',
		'report_type',
		'data'
	];
}
