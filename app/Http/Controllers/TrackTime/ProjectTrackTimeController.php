<?php

namespace App\Http\Controllers\TrackTime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectTrackTimeController extends Controller
{
public function index(){
    return view('trackTime.sitting.project_sitting.index');
}

public function create(){
    return view('trackTime.sitting.project_sitting.create');
}}

