<?php

namespace App\Http\Controllers\TrackTime;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class AverageHoursTrackTimeController extends Controller
{
    public function index(){
        return view('trackTime.sitting.average_hours.index');

    }
public function create(){
    $employees = Employee::all();
    return view('trackTime.sitting.average_hours.create',compact('employees'));
}

public function edit(){
    $employees = Employee::all();
    return view('trackTime.sitting.average_hours.edit',compact('employees'));
}
}
