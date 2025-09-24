<?php

namespace Modules\Purchases\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierSettingsController extends Controller
{
    public function index()
    {
        return view('purchases::purchases.supplier_settings.index');
    }
}
