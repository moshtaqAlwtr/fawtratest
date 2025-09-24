<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'type_id',
        'description',
        'created_by',
        'type_log',
        'old_value',
        'icon',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function Product()
    {
        return $this->belongsTo(Product::class, 'type_id');
    }


    public function expense()
    {
        return $this->belongsTo(Expense::class, 'type_id');
    }
public  function  supplier()
{
    return $this->belongsTo(Supplier::class, 'type_id');
}

public   function OrdersPurchases()
{
    return $this->belongsTo(PurchaseOrder::class, 'type_id');
}

public  function  purchase_quotation()
{
    return $this->belongsTo(PurchaseQuotation::class, 'type_id');
}
public function  quotation_view()
{
    return $this->belongsTo(PurchaseQuotationView::class, 'type_id');
}
public function  purchase_request()
{
    return $this->belongsTo(PurchaseInvoice::class, 'type_id');
}

public function  purchase_invoice()
{
    return $this->belongsTo(PurchaseInvoice::class, 'type_id');
}
public function  purchase_return_log()
{
    return $this->belongsTo(PurchaseInvoice::class, 'type_id');
}
public function  attendance_days_log()
{
    return $this->belongsTo(AttendanceDays::class, 'type_id');
}
 public function attendanceSheet()
    {
        return $this->belongsTo(AttendanceSheets::class, 'type_id');

    }

    public function  holiday_list()
    {
        return $this->belongsTo(HolidayList::class, 'type_id');
    }
        public function  attendance_rules_log()
    {
        return $this->belongsTo(AttendanceRule::class, 'type_id');
    }
            public function  warehouse_log()
    {
        return $this->belongsTo(WarehousePermits::class, 'type_id');
    }

                public function  pathMan()
    {
        return $this->belongsTo(ProductionPath::class, 'type_id');
    }

                    public function  work_station()
    {
        return $this->belongsTo(WorkStations::class, 'type_id');
    }

                    public function  production_material()
    {
        return $this->belongsTo(ProductionMaterials::class, 'type_id');
    }
               public function  manufacturing_order()
    {
        return $this->belongsTo(ManufacturOrders::class, 'type_id');
    }


               public function  indirect_cost()
    {
        return $this->belongsTo(IndirectCost::class, 'type_id');
    }




}
