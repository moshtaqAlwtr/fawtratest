<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory;

    protected $table = 'price_lists';
    protected $fillable = ['id', 'name', 'status', 'created_at', 'updated_at'];
    public function price_list_products()
    {
        return $this->belongsToMany(Product::class, 'price_list_items');
    }
}
