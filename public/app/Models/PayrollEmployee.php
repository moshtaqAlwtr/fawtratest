<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PayrollEmployee extends Pivot
{
    protected $table = 'payroll_employee';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['payroll_id', 'employee_id'];



}
