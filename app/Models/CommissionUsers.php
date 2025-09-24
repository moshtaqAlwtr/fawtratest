<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionUsers extends Model
{
    protected $table = 'commission_users';
    protected $fillable = [
        'commission_id','employee_id'
    ];

    public function employee()
	{
		return $this->belongsTo(User::class,'employee_id');
	}
	public function commission()
{
    return $this->belongsTo(Commission::class, 'commission_id');
}

}
