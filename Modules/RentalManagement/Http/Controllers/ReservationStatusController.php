<?php

namespace Modules\RentalManagement\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReservationStatusController extends Controller
{
    public function index(){
        return view('rentalmanagement::Settings.reservation-status.index');
    }
    public function create(){
        return view('rentalmanagement::Settings.reservation-status.create');
    }
}
