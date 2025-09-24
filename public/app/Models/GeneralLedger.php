<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GeneralLedger
 * 
 * @property int $ledger_id
 * @property string $account_name
 * @property string $account_type
 * @property float|null $balance
 * @property Carbon|null $created_at
 *
 * @package App\Models
 */
class GeneralLedger extends Model
{
	protected $table = 'general_ledger';
	protected $primaryKey = 'ledger_id';
	public $timestamps = false;

	protected $casts = [
		'balance' => 'float'
	];

	protected $fillable = [
		'account_name',
		'account_type',
		'balance'
	];
}
