<?php

namespace Modules\HR\Http\Controllers\Attendance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceSessionsRecordController extends Controller
{
    public function index()
    {
        return view('hr::attendance.attendance-sessions-record.index');
}
}
