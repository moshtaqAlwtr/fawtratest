<?php

namespace Modules\Sales\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RevolvingInvoicesController extends Controller
{
    public function index()
    {
        return view('sales::revolving_invoices.index');
    }

    public function create ()
    {
        return view('sales::revolving_invoices.create');


    }

    public function show ()
    {
        return view('sales::revolving_invoices.show');
    }
}
