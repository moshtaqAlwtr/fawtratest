<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriesClient extends Model
{
    use HasFactory;

    protected $table = 'categories_clients';

    protected $fillable = [
        'name',
        'description',
        'active'
    ];

    public function clients()
    {
        return $this->hasMany(Client::class, 'category_id');
    }
}
