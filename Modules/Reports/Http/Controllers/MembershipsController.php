<?php

namespace Modules\Reports\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MembershipsController extends Controller
{
    public function index()
    {
        return view('reports::memberships.index');
    }
    public function Expired()
    {
        return view('reports::memberships.Expired');
    }
    public function Renewals()
    {
        return view('reports::memberships.Renewals');
    }
    public function Subscriptions()
    {
        return view('reports::memberships.New_Subscriptions');
    }

}
