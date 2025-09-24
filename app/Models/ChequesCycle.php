<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ChequesCycle
 * 
 * @property int $cheque_cycle_id
 * @property string|null $cheque_number
 * @property Carbon|null $issue_date
 * @property Carbon|null $due_date
 * @property float|null $amount
 * @property string|null $status
 * @property int|null $client_id
 * @property Carbon|null $created_at
 * 
 * @property Client|null $client
 *
 * @package App\Models
 */
class ChequesCycle extends Model
{
	protected $table = 'cheques_cycle';
	protected $primaryKey = 'cheque_cycle_id';
	public $timestamps = false;

	protected $casts = [
		'issue_date' => 'datetime',
		'due_date' => 'datetime',
		'amount' => 'float',
		'client_id' => 'int'
	];

	protected $fillable = [
		'cheque_number',
		'issue_date',
		'due_date',
		'amount',
		'status',
		'client_id'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}
}
