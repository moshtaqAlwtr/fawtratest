<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    { // جلب جميع الموظفين
        return view('task.index');
    }

}
// تاتي هذه الدالة لتعرض جميع الموظفين في الصفحة الرئيسية للوحة التحكم
