<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    public function users()
{
    return $this->belongsToMany(User::class, 'commission_users', 'commission_id', 'employee_id');
}

}
