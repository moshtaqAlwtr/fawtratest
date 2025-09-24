<?php
namespace Modules\Reports\Http\Controllers;
use App\Http\Controllers\Controller;

class RentalsController extends Controller
{
    public function index()
    {
        return view('reports::Rentals.index');
    }

    public function AvailableUnits()
    {
        return view('reports::rentals.Available_Units');
    }

    public function UnitPricing()
    {
        return view('reports::rentals.Unit_Pricing');
    }

    public function Subscriptions()
    {
        return view('reports::Rentals.Subscriptions');
    }


public  function  production(){
return view('reports::production.index');
}


}
