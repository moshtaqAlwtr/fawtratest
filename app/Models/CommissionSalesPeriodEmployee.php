<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSalesPeriodEmployee extends Model
{
    protected $table = 'commission_sales_period_employee';

    protected $fillable = [
        'sales_period_id',
        'employee_id',
        'sales_amount',
        'commission_id',
        // ...أضف باقي الحقول لو فيه
    ];


public function employee() { return $this->belongsTo(User::class, 'employee_id'); }
public function commission() { return $this->belongsTo(Commission::class, 'commission_id'); }



public function salesPeriod()
{
    return $this->belongsTo(\App\Models\SalesPeriod::class, 'sales_period_id');
}

}
