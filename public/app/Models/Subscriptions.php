<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    public function invoice()
    {
      
            return $this->belongsTo(Invoice::class);
      
    
    }

    public function packege() {
        return $this->belongsTo(Package::class,'package_id');
    }
}
