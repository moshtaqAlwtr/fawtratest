<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Chat
 * 
 * @property int $chat_id
 * @property int|null $sender_id
 * @property int|null $receiver_id
 * @property string|null $message_content
 * @property Carbon|null $sent_at
 * @property bool|null $is_read
 *
 * @package App\Models
 */
class Chat extends Model
{
	protected $table = 'chat';
	protected $primaryKey = 'chat_id';
	public $timestamps = false;

	protected $casts = [
		'sender_id' => 'int',
		'receiver_id' => 'int',
		'sent_at' => 'datetime',
		'is_read' => 'bool'
	];

	protected $fillable = [
		'sender_id',
		'receiver_id',
		'message_content',
		'sent_at',
		'is_read'
	];
}
