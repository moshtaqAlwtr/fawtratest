<?php

namespace App\Http\Controllers\TrackTime;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class TrackTimeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('tracktime.index', compact('employees'));
    }
    public function show()
    {
        $employees = Employee::all();
        return view('trackTime.view', compact('employees'));
    }

    public function create_invoice_time(){
        $employees = Employee::all();
        return view('trackTime.create_invoice_time', compact('employees'));

    }
}
