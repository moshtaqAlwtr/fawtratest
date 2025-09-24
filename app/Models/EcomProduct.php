<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EcomProduct
 * 
 * @property int $product_id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property int|null $stock_quantity
 * @property string|null $category
 * @property string|null $status
 * @property Carbon|null $created_at
 * 
 * @property Collection|EcomOrderItem[] $ecom_order_items
 * @property Collection|PosTransactionItem[] $pos_transaction_items
 *
 * @package App\Models
 */
class EcomProduct extends Model
{
	protected $table = 'ecom_products';
	protected $primaryKey = 'product_id';
	public $timestamps = false;

	protected $casts = [
		'price' => 'float',
		'stock_quantity' => 'int'
	];

	protected $fillable = [
		'name',
		'description',
		'price',
		'stock_quantity',
		'category',
		'status'
	];

	public function ecom_order_items()
	{
		return $this->hasMany(EcomOrderItem::class, 'product_id');
	}

	public function pos_transaction_items()
	{
		return $this->hasMany(PosTransactionItem::class, 'product_id');
	}
}
