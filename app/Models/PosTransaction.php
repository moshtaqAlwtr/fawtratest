<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PosTransaction
 * 
 * @property int $transaction_id
 * @property Carbon|null $transaction_date
 * @property int $cashier_id
 * @property float $total_amount
 * @property string|null $payment_method
 * @property string|null $status
 * @property int $employee_id
 *
 * @package App\Models
 */
class PosTransaction extends Model
{
	protected $table = 'pos_transactions';
	protected $primaryKey = 'transaction_id';
	public $timestamps = false;

	protected $casts = [
		'transaction_date' => 'datetime',
		'cashier_id' => 'int',
		'total_amount' => 'float',
		'employee_id' => 'int'
	];

	protected $fillable = [
		'transaction_date',
		'cashier_id',
		'total_amount',
		'payment_method',
		'status',
		'employee_id'
	];
}
