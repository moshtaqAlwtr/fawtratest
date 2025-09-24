<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// SalesPeriod.php
class SalesPeriod extends Model
{
    protected $fillable = ['name', 'from_date', 'to_date', 'branch_id', 'department_id', 'job_title_id'];

public function employees()
{
    return $this->belongsToMany(User::class, 'commission_sales_period_employee', 'sales_period_id', 'employee_id')
        ->withPivot(['sales_amount', 'commission_id', 'commission_percentage'])
        ->withTimestamps();
}

// App\Models\SalesPeriod.php

public function periodEmployees()
{
    // لاحظ: Employee هنا هو User (الموظف)
    return $this->belongsToMany(\App\Models\User::class, 'commission_sales_period_employee', 'sales_period_id', 'employee_id')
        ->withPivot(['sales_amount', 'commission_id'])
        ->withTimestamps()
        ->using(\App\Models\CommissionSalesPeriodEmployee::class); // إذا لديك موديل Pivot مخصص
}

public function commissionSales()
{
    // هذا هو جدول الربط
    return $this->hasMany(\App\Models\CommissionSalesPeriodEmployee::class, 'sales_period_id');
}


    public function commission()
    {
        return $this->belongsTo(Commission::class, 'commission_id');
    }


      public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }

    public function jobTitle()
    {
        return $this->belongsTo(\App\Models\JopTitle::class, 'job_title_id');
    }
}
