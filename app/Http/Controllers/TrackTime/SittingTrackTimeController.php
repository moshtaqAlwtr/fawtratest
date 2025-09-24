<?php

namespace App\Http\Controllers\TrackTime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SittingTrackTimeController extends Controller
{
    public function index()
    {
        return view('trackTime.sitting.sitting.sitting');
    }
public function  create()
{
    return view('trackTime.sitting.sitting.create');
}
}
