<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesCommission extends Model
{
    
    public function employee()
	{
		return $this->belongsTo(User::class,'employee_id');
	}

    public function commission()
	{
		return $this->belongsTo(Commission::class);
	}
	public function products()
	{
		return $this->belongsTo(Product::class,'product_id');
	}
}
