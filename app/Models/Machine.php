<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Machine extends Model
{


    protected $fillable = [
        'name',
        'status',
        'serial_number',
        'host_name',
        'port_number',
        'connection_key',
        'machine_type'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
