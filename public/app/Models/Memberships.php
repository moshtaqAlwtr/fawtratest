<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Memberships extends Model
{
    protected $fillable = ['client_id', 'package_id', 'join_date', 'end_date', 'description'];

    
    public function client() {
        return $this->belongsTo(Client::class);
    }
    public function packege() {
        return $this->belongsTo(Package::class,'package_id');
    }
}
