<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BalanceRecharge
 * 
 * @property int $recharge_id
 * @property int $user_id
 * @property int|null $client_id
 * @property float $recharge_amount
 * @property Carbon|null $recharge_date
 * @property string $payment_method
 * @property string|null $status
 * @property string|null $notes
 * 
 * @property User $user
 *
 * @package App\Models
 */
class BalanceRecharge extends Model
{
	protected $table = 'balance_recharges';
	protected $primaryKey = 'recharge_id';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'client_id' => 'int',
		'recharge_amount' => 'float',
		'recharge_date' => 'datetime'
	];

	protected $fillable = [
		'user_id',
		'client_id',
		'recharge_amount',
		'recharge_date',
		'payment_method',
		'status',
		'notes'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
