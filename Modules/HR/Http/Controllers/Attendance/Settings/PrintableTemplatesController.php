<?php

namespace Modules\HR\Http\Controllers\Attendance\Settings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrintableTemplatesController extends Controller
{
    public function index()
    {
        return view('hr::attendance.settings.printable-templates.index');
    }
    public function create()
    {
        return view('hr::attendance.settings.printable-templates.create');
    }
}
