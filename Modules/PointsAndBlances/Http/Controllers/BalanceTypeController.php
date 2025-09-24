<?php

namespace Modules\PointsAndBlances\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\BalanceType;
use Illuminate\Http\Request;

class BalanceTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = BalanceType::query();

        // Filter by name if provided
        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $balanceTypes = $query->get(); // Retrieve filtered balance types

        return view('pointsandblances::sitting.balanceType.create', compact('balanceTypes'));
    }
    public function create()
    {
        $balanceTypes = BalanceType::all();
        return view('pointsandblances.sitting.balanceType.create', compact('balanceTypes'));
    }
    public function store(Request $request)
{
    // Validate the incoming request data
    $request->validate([
        'name' => 'required|string|max:255',
        'status' => 'required|integer|in:1,2',
        'unit' => 'required|string|max:255',
        'allow_decimal' => 'boolean',
        'description' => 'required|string',
    ]);

    // Create a new balance type record
    BalanceType::create([
        'name' => $request->name,
        'status' => $request->status,
        'unit' => $request->unit,
        'allow_decimal' => $request->has('allow_decimal'), // Checkbox will return true if checked
        'description' => $request->description,
    ]);

    // Redirect to the index page with a success message
    return redirect()->route('BalanceType.index')->with('success', 'Balance type created successfully.');
}

public function show($id)
{
    // Retrieve the specific balance type record
    $balanceType = BalanceType::findOrFail($id);

    // Return the view with the balance type data
    return view('pointsandblances.sitting.balanceType.show', compact('balanceType'));
}
    public function edit($id)
    {
        $balanceType = BalanceType::findOrFail($id);
        return view('pointsandblances.sitting.balanceType.edit',compact('balanceType'));
    }
    public function update(Request $request, $id)
{
    // Validate the incoming request data
    $request->validate([
        'name' => 'required|string|max:255',
        'status' => 'required|integer',
        'unit' => 'required|string|max:255',
        'allow_decimal' => 'boolean',
        'description' => 'required|string',
    ]);

    // Find the existing balance type record
    $balanceType = BalanceType::findOrFail($id);

    // Update the balance type record
    $balanceType->update([
        'name' => $request->name,
        'status' => $request->status,
        'unit' => $request->unit,
        'allow_decimal' => $request->has('allow_decimal'), // Checkbox will return true if checked
        'description' => $request->description,
    ]);

    // Redirect to the index page with a success message
    return redirect()->route('BalanceType.show',$balanceType->id)->with('success', 'Balance type updated successfully.');
}
public function destroy($id)
{
    $balanceType = BalanceType::find($id);
    $balanceType->delete();
    return redirect()->route('BalanceType.index')->with('success', 'Balance type deleted successfully.');
}
public function updateStatus($id)
{
    $balanceType = BalanceType::find($id);

    if (!$balanceType) {
        return redirect()->route('BalanceType.show',$id)->with(['error' => ' نوع الرصيد غير موجود!']);
    }

    $balanceType->update(['status' => !$balanceType->status]);

    return redirect()->route('BalanceType.show',$id)->with(['success' => 'تم تحديث حالة نوع الرصيد بنجاح!']);
}
}
