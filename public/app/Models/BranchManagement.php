<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BranchManagement
 * 
 * @property int $management_id
 * @property int|null $branch_id
 * @property int|null $employee_id
 * @property string|null $role_in_branch
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * 
 * @property Branch|null $branch
 * @property Employee|null $employee
 *
 * @package App\Models
 */
class BranchManagement extends Model
{
	protected $table = 'branch_management';
	protected $primaryKey = 'management_id';
	public $timestamps = false;

	protected $casts = [
		'branch_id' => 'int',
		'employee_id' => 'int',
		'start_date' => 'datetime',
		'end_date' => 'datetime'
	];

	protected $fillable = [
		'branch_id',
		'employee_id',
		'role_in_branch',
		'start_date',
		'end_date'
	];

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}
}
