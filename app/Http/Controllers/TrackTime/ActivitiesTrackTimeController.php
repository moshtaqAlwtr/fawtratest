<?php

namespace App\Http\Controllers\TrackTime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivitiesTrackTimeController extends Controller
{
    public function index()
    {
        return view('trackTime.sitting.activities.index');
    }
    public function create()
    {
        return view('trackTime.sitting.activities.create');
    }

}
