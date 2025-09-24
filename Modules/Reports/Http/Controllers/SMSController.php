<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    public function index()
    {
        return view('reports.sms.index');
    }
    public function Campaigns()
    {
        return view('reports.sms.Campaigns');
    }
    public function log()
    {
        return view('reports.sms.log');
    }
}

