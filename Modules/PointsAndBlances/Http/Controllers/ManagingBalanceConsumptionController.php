<?php

namespace Modules\PointsAndBlances\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\BalanceConsumption; // Import the BalanceConsumption model
use App\Models\BalanceType;
use App\Models\Client;
use Illuminate\Http\Request;

class ManagingBalanceConsumptionController extends Controller
{
    public function index(Request $request)
    {
        $query = BalanceConsumption::with(['client', 'balanceType']);

        // Search by client name
        if ($request->filled('client_name')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('first_name', 'LIKE', '%' . $request->client_name . '%')
                  ->orWhere('last_name', 'LIKE', '%' . $request->client_name . '%');
            });
        }

        // Search by balance type name or ID
        if ($request->filled('balance_name')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('balanceType', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->balance_name . '%');
                })
                ->orWhere('balance_type_id', $request->balance_name); // Assuming balance_name can also be an ID
            });
        }

        // Filter by consumption date range
        if ($request->filled('date_from')) {
            $query->where('consumption_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('consumption_date', '<=', $request->date_to);
        }

        $balanceConsumptions = $query->get();

        return view('pointsAndBalances.Managing_balance_consumption.index', compact('balanceConsumptions'));
    }
    public function create()
    {
        $balanceTypes = BalanceType::all();
        $clients = Client::all();
        return view('pointsAndBalances.Managing_balance_consumption.create', compact('clients', 'balanceTypes'));
    }

    public function show($id)
    {
        $balanceConsumption = BalanceConsumption::with(['client', 'balanceType'])->findOrFail($id);
        return view('pointsAndBalances.Managing_balance_consumption.show', compact('balanceConsumption'));
    }

    public function edit($id)
{
    $balanceConsumption = BalanceConsumption::findOrFail($id);
    $balanceTypes = BalanceType::all();
    $clients = Client::all();
    return view('pointsAndBalances.Managing_balance_consumption.edit', compact('balanceConsumption', 'clients', 'balanceTypes'));
}

    public function store(Request $request)
    {
        // Validate the request data
        try {
            $request->validate([
                'client_id' => 'nullable|exists:clients,id',
                'balance_type_id' => 'nullable|exists:balance_types,id',
                'consumption_date' => 'nullable|date',
                'status' => 'nullable|integer',
                'used_balance' => 'nullable|numeric',
                'description' => 'nullable|string',
                'contract_type' => 'nullable|string',
            ]);

            // Create a new balance consumption record
            BalanceConsumption::create($request->all());

            // Redirect to the index page with a success message
            return redirect()->route('ManagingBalanceConsumption.index')->with('success', 'تم اضافه معامله بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ ما ا��نا�� الا��افه!!');
        }
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'balance_type_id' => 'nullable|exists:balance_types,id',
            'consumption_date' => 'nullable|date',
            'status' => 'nullable|integer',
            'used_balance' => 'nullable|numeric',
            'description' => 'nullable|string',
            'contract_type' => 'nullable|string',
        ]);

        // Find the balance consumption record
        $balanceConsumption = BalanceConsumption::findOrFail($id);

        // Update the record with the validated data
        $balanceConsumption->update($request->all());

        // Redirect to the index page with a success message
        return redirect()->route('ManagingBalanceConsumption.index')->with('success', 'تم تعديل المعامله بنجاح.');
    }

    public function destroy($id)
    {
        // Find the balance consumption record
        $balanceConsumption = BalanceConsumption::findOrFail($id);

        // Delete the record
        $balanceConsumption->delete();

        // Redirect to the index page with a success message
        return redirect()->route('ManagingBalanceConsumption.index')->with('success', 'تم حذف المعامله بنجاح.');
    }

    public function updateStatus($id)
    {
        // Find the balance consumption record
        $balanceConsumption = BalanceConsumption::findOrFail($id);

        // Toggle the status
        $balanceConsumption->contract_type = !$balanceConsumption->contract_type;
        $balanceConsumption->save();

        // Redirect to the index page with a success message
        return redirect()->route('ManagingBalanceConsumption.index')->with('success', 'تم تغيير حالة المعامله بنجاح.');
    }
}
