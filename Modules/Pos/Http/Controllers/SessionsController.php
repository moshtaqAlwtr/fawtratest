<?php

namespace Modules\Pos\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SessionsController extends Controller
{
    public function index()
    {
        return view('pos::sessions.index');
    }
}
