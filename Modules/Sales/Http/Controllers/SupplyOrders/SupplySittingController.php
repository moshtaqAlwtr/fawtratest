<?php

namespace Modules\Sales\Http\Controllers\SupplyOrders;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplySittingController extends Controller
{

    public function index()
    {
        return view('sales::supplyOrders.sitting.sitting');
    }
    public function edit_procedures()
    {
        return view('sales::supplyOrders.sitting.edit_procedures');
    }

    public function edit_supply_number()
    {
        return view('sales::supplyOrders.sitting.edit_supply_number');
    }
    public function sitting_serial_number()
    {
        return view('sales::supplyOrders.sitting.sitting_serial_number');
    }

}
