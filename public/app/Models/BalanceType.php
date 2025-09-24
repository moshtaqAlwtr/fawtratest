<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceType extends Model
{
    use HasFactory;

    protected $table = 'balance_types'; // Specify the table name

    protected $fillable = ['name', 'status', 'unit', 'allow_decimal', 'description', 'package_id', 'balance_value'];

    public function packages()
{
    return $this->belongsToMany(Package::class, 'balance_type_package')
                ->withPivot('balance_value')
                ->withTimestamps();
}
}
