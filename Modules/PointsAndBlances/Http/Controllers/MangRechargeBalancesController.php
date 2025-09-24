<?php

namespace Modules\PointsAndBlances\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\BalanceCharge;
use App\Models\BalanceType;
use App\Models\Client;

use Illuminate\Http\Request;

class MangRechargeBalancesController extends Controller
{
    public function index(Request $request)
    {
        // Initialize the query
        $query = BalanceCharge::with(['client', 'balanceType']);

        // Apply filters based on the request
        if ($request->has('client_or_id') && $request->input('client_or_id') != '') {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->input('client_or_id') . '%')->orWhere('id', $request->input('client_or_id'));
            });
        }

        if ($request->has('balance_name_or_id') && $request->input('balance_name_or_id') != '') {
            $query->whereHas('balanceType', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->input('balance_name_or_id') . '%')->orWhere('id', $request->input('balance_name_or_id'));
            });
        }

        if ($request->has('status') && $request->input('status') != '') {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('start_date_from') && $request->input('start_date_from') != '') {
            $query->where('start_date', '>=', $request->input('start_date_from'));
        }

        if ($request->has('start_date_to') && $request->input('start_date_to') != '') {
            $query->where('start_date', '<=', $request->input('start_date_to'));
        }

        if ($request->has('end_date_from') && $request->input('end_date_from') != '') {
            $query->where('end_date', '>=', $request->input('end_date_from'));
        }

        if ($request->has('end_date_to') && $request->input('end_date_to') != '') {
            $query->where('end_date', '<=', $request->input('end_date_to'));
        }

        // Fetch the filtered results
        $balances = $query->get();

        // Return the view with the fetched data
        return view('pointsandbalances::mangRechargeBalances.index', compact('balances'));
    }
    public function create()
    {
        $clients = Client::all();
        $balanceTypes = BalanceType::all();
        return view('pointsandblances::mangRechargeBalances.create', compact('clients', 'balanceTypes'));
    }
    public function store(Request $request)
    {
        // Validate the incoming request data
        try {
            $validatedData = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'balance_type_id' => 'required|exists:balance_types,id',
                'value' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'required|string',
                'contract_type' => 'boolean',
            ]);

            // Create a new BalanceCharge record
            BalanceCharge::create($validatedData);

            // Redirect back with a success message
            return redirect()->route('MangRechargeBalances.index')->with('success', 'تم اضافه شحن رصيد ب بنجاح !!');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ ما اثناء الاضافه !!');
        }
    }

    public function update(Request $request, $id)
    {
        // Find the BalanceCharge record by ID
        try {
            $balanceCharge = BalanceCharge::findOrFail($id);

            // Validate the incoming request data
            $validatedData = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'balance_type_id' => 'required|exists:balance_types,id',
                'value' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'required|string',
                'contract_type' => 'boolean',
            ]);

            // Update the BalanceCharge record
            $balanceCharge->update($validatedData);

            // Redirect back with a success message
            return redirect()->route('MangRechargeBalances.index')->with('success', 'تم تعديل معامله بنجاح !.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثنا�� تعديل معامله: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function show($id)
{
    // Fetch the balance charge by ID with related client and balance type
    $balanceCharge = BalanceCharge::with(['client', 'balanceType'])->findOrFail($id);

    // Return the view with the fetched data
    return view('pointsandblances::mangRechargeBalances.show', compact('balanceCharge'));
}
    public function edit($id)
    {
        $clients = Client::all();
        $balanceCharge = BalanceCharge::findOrFail($id);

        $balanceTypes = BalanceType::all();
        return view('pointsandblances::mangRechargeBalances.edit', compact('clients', 'balanceCharge', 'balanceTypes'));
    }
    public function destroy($id)
    {
        // Find the BalanceCharge record by ID
        $balanceCharge = BalanceCharge::findOrFail($id);

        // Delete the BalanceCharge record
        $balanceCharge->delete();

        // Redirect back with a success message
        return redirect()->route('MangRechargeBalances.index')->with('success', 'تم حذف معامله بنجاح.');
    }
    public function updateStatus($id)
    {
        $balanceCharge = BalanceCharge::find($id);

        if (!$balanceCharge) {
            return redirect()
                ->route('MangRechargeBalances.show', $id)
                ->with(['error' => ' نوع الرصيد غير موجود!']);
        }

        $balanceCharge->update(['status' => !$balanceCharge->status]);

        return redirect()
            ->route('MangRechargeBalances.show', $id)
            ->with(['success' => 'تم تحديث حالة نوع الرصيد بنجاح!']);
    }
}
