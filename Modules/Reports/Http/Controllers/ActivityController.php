<?php

namespace Modules\Reports\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        return view('reports::activity.index');
    }
}
