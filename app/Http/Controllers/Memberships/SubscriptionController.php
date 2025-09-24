<?php

namespace App\Http\Controllers\Memberships;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        return view('memberships.subscription.index');
    }
    public function create()
    {
        return view('memberships.subscription.cteate');
    }
}
