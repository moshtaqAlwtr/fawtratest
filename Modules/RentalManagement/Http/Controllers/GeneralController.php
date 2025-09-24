<?php

namespace Modules\RentalManagement\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function index()
    {
        return view('rentalmanagement::Settings.general.index');
    }
}
