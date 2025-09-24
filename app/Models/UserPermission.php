<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserPermission
 * 
 * @property int $user_id
 * @property string $user_name
 * @property string $status
 * @property bool|null $allow_approve_reject
 * @property bool|null $permission_add_new_order
 * @property bool|null $permission_approve_reject_orders
 * @property bool|null $permission_view_orders
 * @property bool|null $permission_manage_inventory
 * @property bool|null $permission_manage_products
 *
 * @package App\Models
 */
class UserPermission extends Model
{
	protected $table = 'user_permissions';
	protected $primaryKey = 'user_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'allow_approve_reject' => 'bool',
		'permission_add_new_order' => 'bool',
		'permission_approve_reject_orders' => 'bool',
		'permission_view_orders' => 'bool',
		'permission_manage_inventory' => 'bool',
		'permission_manage_products' => 'bool'
	];

	protected $fillable = [
		'user_name',
		'status',
		'allow_approve_reject',
		'permission_add_new_order',
		'permission_approve_reject_orders',
		'permission_view_orders',
		'permission_manage_inventory',
		'permission_manage_products'
	];
}
