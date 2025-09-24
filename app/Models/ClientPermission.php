<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPermission extends Model
{
    protected $table = 'client_permissions';
    protected $fillable = ['is_active'];
    
}
