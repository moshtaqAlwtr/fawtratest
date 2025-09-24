<?php

namespace Modules\PointsAndBlances\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SittingController extends Controller
{
public function index()
{
    return view('pointsandblances::sitting.index');
}
}
