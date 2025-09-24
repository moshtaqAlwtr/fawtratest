<?php

namespace Modules\Reports\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    public function index()
    {
        return view('reports::employees.index');
    }
    public function Residences()
    {
        return view('reports::employees.Residences');
    }
    public function attendance()
    {
        return view('reports::employees.attendance');
    }
    public function attendancemultiple()
    {
        return view('reports::employees.attendance_multiple');
    }
    public function Leaves()
    {
        return view('reports::employees.Leaves');
    }
    public function shift()
    {
        return view('reports::employees.shift');
    }
    public function AttendancebyEmployee()
    {
        return view('reports::employees.Attendance_by_Employee');
    }
    public function AttendancebyDay()
    {
        return view('reports::employees.Attendance-by-Day');
    }
    public function AttendancebyWeek()
    {
        return view('reports::employees.attendance-by-week');
    }
    public function AttendancebyMonth()
    {
        return view('reports::employees.attendance-by-month');
    }
    public function AttendancebyYear()
    {
        return view('reports::employees.attendance-by-year');

    }
    public function AttendancebyDepartment()
    {
        return view('reports::employees.attendance-by-department');
    }
    public function byBranch()
    {
        return view('reports::employees.Salaries_by_Branch');
    }
    public function Advances()
    {
        return view('reports::employees.Advances');
    }
    public function Contracts()
    {
        return view('reports::employees.Contracts');
    }

}
