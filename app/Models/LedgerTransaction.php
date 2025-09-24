<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LedgerTransaction
 * 
 * @property int $transaction_id
 * @property int $ledger_id
 * @property Carbon $transaction_date
 * @property float $amount
 * @property string $transaction_type
 * @property string|null $description
 * @property Carbon|null $created_at
 *
 * @package App\Models
 */
class LedgerTransaction extends Model
{
	protected $table = 'ledger_transactions';
	protected $primaryKey = 'transaction_id';
	public $timestamps = false;

	protected $casts = [
		'ledger_id' => 'int',
		'transaction_date' => 'datetime',
		'amount' => 'float'
	];

	protected $fillable = [
		'ledger_id',
		'transaction_date',
		'amount',
		'transaction_type',
		'description'
	];
}
