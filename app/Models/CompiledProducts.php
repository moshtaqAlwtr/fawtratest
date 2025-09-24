<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompiledProducts extends Model
{
    protected $table = 'compiled_products';
    

    public function Product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
   
}
