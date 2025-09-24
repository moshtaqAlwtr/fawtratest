<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosTransactionItem
 * 
 * @property int $item_id
 * @property int $transaction_id
 * @property int $product_id
 * @property int $quantity
 * @property float|null $unit_price
 * @property float|null $total_price
 * 
 * @property EcomProduct $ecom_product
 *
 * @package App\Models
 */
class PosTransactionItem extends Model
{
	protected $table = 'pos_transaction_items';
	protected $primaryKey = 'item_id';
	public $timestamps = false;

	protected $casts = [
		'transaction_id' => 'int',
		'product_id' => 'int',
		'quantity' => 'int',
		'unit_price' => 'float',
		'total_price' => 'float'
	];

	protected $fillable = [
		'transaction_id',
		'product_id',
		'quantity',
		'unit_price',
		'total_price'
	];

	public function ecom_product()
	{
		return $this->belongsTo(EcomProduct::class, 'product_id');
	}
}
