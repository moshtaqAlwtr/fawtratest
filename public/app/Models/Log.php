<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'type_id',
        'description',
        'created_by',
        'type_log',
        'old_value',
        'icon',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function Product()
    {
        return $this->belongsTo(Product::class, 'type_id');
    }
}
