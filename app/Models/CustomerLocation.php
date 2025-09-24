<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerLocation
 * 
 * @property int $location_id
 * @property int|null $client_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $address
 * @property Carbon|null $created_at
 *
 * @package App\Models
 */
class CustomerLocation extends Model
{
	protected $table = 'customer_locations';
	protected $primaryKey = 'location_id';
	public $timestamps = false;

	protected $casts = [
		'client_id' => 'int',
		'latitude' => 'float',
		'longitude' => 'float'
	];

	protected $fillable = [
		'client_id',
		'latitude',
		'longitude',
		'address'
	];
}
