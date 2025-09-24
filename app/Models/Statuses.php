<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statuses extends Model
{
    use HasFactory;

    protected $table = 'statuses';

    protected $fillable = [
        'name',
        'color',
        'state',
        'client_id', // Include the foreign key in fillable properties
        'supply_order_id', // Include the foreign key in fillable properties
    ];

    // Define the relationship to the Order model
    public function supplyOrder()
    {
        return $this->belongsTo(SupplyOrder::class);
    }
public function client()
{
    return $this->belongsTo(Client::class);
}
public function clients()
{
    return $this->hasMany(Client::class, 'status_id', 'id');
}

    // Define the relationship to the Client model
  
}
