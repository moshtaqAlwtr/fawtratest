<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreasuryEmployee extends Model
{
    use HasFactory;

    protected $table = 'treasury_employees';
    protected $fillable = ['treasury_id', 'employee_id'];

    public function treasury()
    {
        return $this->belongsTo(Account::class, 'treasury_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
