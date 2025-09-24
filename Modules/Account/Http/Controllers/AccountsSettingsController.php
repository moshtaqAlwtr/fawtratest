<?php

namespace Modules\Account\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountsSettingsController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("Accounts.accounts_settings.index");
    }

    /**
     * Show the form for creating a new resource.
     */
     // الفترات المالية
    public function financial_years()
    {
        return view("Accounts.accounts_settings.financial_years");
    }

 public function closed_periods()
    {
        return view("Accounts.accounts_settings.closed_periods");
    }
    
    
    
     public function accounts_routing()
    {
        return view("Accounts.accounts_settings.accounts_routing");
    }
    
     public function accounting_general()
    {
        return view("Accounts.accounts_settings.accounting_general");
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
