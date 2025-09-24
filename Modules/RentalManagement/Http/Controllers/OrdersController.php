<?php

namespace Modules\RentalManagement\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\UnitType;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index()
    {
        return view('rentalmanagement::orders.index');
    }


        public function create()
        {
            $unitTypes = UnitType::with('pricingRule')->get(); // جلب البيانات مع قواعد التسعير
            return view('rentalmanagement::orders.create', compact('unitTypes'));
        }

    }


