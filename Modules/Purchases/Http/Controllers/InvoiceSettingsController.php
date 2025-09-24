<?php

namespace Modules\Purchases\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceSettingsController extends Controller
{
    public function index()
    {
        return view('purchases::purchases.invoice_settings.index');
    }
    public function create()
    {
        return view('purchases::purchases.invoice_settings.create');
    }
}
