<?php

namespace Modules\Pos\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('pos::settings.index');
    }
    public function general()
    {
        return view('pos::settings.general');
    }
    public function Shift()
    {
        return view('pos::settings.shift.index');
    }
    public function Create()
    {
        return view('pos::settings.shift.create');
    }

}
