<?php

namespace Modules\Sitting\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrencyRatesController extends Controller
{
    public function index()
    {
        return view('sitting::currency_rates.index');
    }

    public function create()
    {
        return view('sitting::currency_rates.create');
    }
    public function edit()
    {
        return view('sitting::currency_rates.edit');
    }
}
