<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientEmployee extends Model
{
    protected $table = 'client_employee';
    protected $fillable = ['client_id','employee_id'];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

}
