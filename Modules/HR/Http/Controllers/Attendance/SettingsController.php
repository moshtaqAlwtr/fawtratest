<?php

namespace Modules\HR\Http\Controllers\Attendance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('hr::attendance.settings.index');
    }

}
