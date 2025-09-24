<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EcomOrder
 * 
 * @property int $order_id
 * @property int $client_id
 * @property Carbon|null $order_date
 * @property float|null $total_amount
 * @property string|null $status
 * @property string|null $payment_status
 * 
 * @property Client $client
 *
 * @package App\Models
 */
class EcomOrder extends Model
{
	protected $table = 'ecom_orders';
	protected $primaryKey = 'order_id';
	public $timestamps = false;

	protected $casts = [
		'client_id' => 'int',
		'order_date' => 'datetime',
		'total_amount' => 'float'
	];

	protected $fillable = [
		'client_id',
		'order_date',
		'total_amount',
		'status',
		'payment_status'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}
}
