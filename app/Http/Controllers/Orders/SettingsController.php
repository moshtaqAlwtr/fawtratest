<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function Settings()
    {
        return view('orders.Settings.index');
    }
    public function type()
    {
        return view('orders.Settings.type');
    }
    public function create()
    {
        $employees = Employee::all();
        return view('orders.Settings.create', compact('employees'));
    }
}
