<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission_Products extends Model
{
    protected $table = 'commission__products';
    protected $fillable = [
        'commission_id','product_id','commission_percentage'
    ];

    public function products()
	{
		return $this->belongsTo(Product::class,'product_id');
	}
}
