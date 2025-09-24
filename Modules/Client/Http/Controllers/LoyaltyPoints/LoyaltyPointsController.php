<?php

namespace Modules\Client\Http\Controllers\LoyaltyPoints;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Log as ModelsLog;
use App\Models\LoyaltyRule; // Import the LoyaltyRule model
use Illuminate\Http\Request;

class LoyaltyPointsController extends Controller
{
    public function index(Request $request)
{
    // Initialize the query
    $query = LoyaltyRule::with('clients');

    // Apply filters based on the request parameters
    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->input('name') . '%');
    }

    if ($request->filled('balance_name')) {
        $query->whereHas('clients', function($q) use ($request) {
            $q->where('transactions', 'like', '%' . $request->input('balance_name') . '%');
        });
    }

    if ($request->filled('currency')) {
        $query->where('currency_type', $request->input('currency'));
    }

    if ($request->filled('min_spent')) {
        $query->where('minimum_total_spent', '>=', $request->input('min_spent'));
    }

    if ($request->filled('max_spent')) {
        $query->where('minimum_total_spent', '<=', $request->input('max_spent'));
    }

    if ($request->filled('status')) {
        $query->where('status', $request->input('status'));
    }

    // Execute the query and get the results
    $loyaltyRules = $query->get();

    return view('client::loyalty_points.index', compact('loyaltyRules'));
}
    public function create()
    {
        $clients = Client::all();
        return view('client::loyalty_points.create', compact('clients'));
    }



    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|integer',
            'priority_level' => 'nullable|string|max:255',
            'collection_factor' => 'nullable|numeric',
            'minimum_total_spent' => 'nullable|numeric',
            'currency_type' => 'nullable|integer',
            'period' => 'nullable|integer',
            'period_unit' => 'nullable|integer',
            'client_ids' => 'required|array', // Ensure client_ids is an array
        ]);


        // Create a new loyalty rule
        $loyaltyRule = LoyaltyRule::create($request->all());

 // تسجيل اشعار نظام جديد
            ModelsLog::create([
                'type' => 'loyaltyRule',
                'type_id' => $loyaltyRule->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
             'description' => 'تم اضافة قاعدة عملاء  جديد',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);

        // Attach the selected clients to the loyalty rule
        $loyaltyRule->clients()->attach($request->input('client_ids'));

        // Redirect to the index with a success message
        return redirect()->route('loyalty_points.index')->with('success', 'تم اضافة قوانين النقاط بنجاح.');
    }

    public function show($id)
    {
        $loyaltyRule = LoyaltyRule::with('clients')->findOrFail($id); // Fetch the loyalty rule with associated clients
        return view('client::loyalty_points.show', compact('loyaltyRule'));
    }
    public function updateStatus($id)
{
    $loyaltyRule = LoyaltyRule::findOrFail($id);

    if (!$loyaltyRule) {
        return redirect()->route('loyalty_points.show',$id)->with(['error' => ' قوانين النقاط غير موجود!']);
    }

    $loyaltyRule->update(['status' => !$loyaltyRule->status]);

    return redirect()->route('loyalty_points.show',$id)->with(['success' => 'تم تحديث حالة قوانين النقاط بنجاح!']);
}
    public function edit($id)
    {
        $loyaltyRule = LoyaltyRule::findOrFail($id);
        $clients = Client::all();
        return view('client::loyalty_points.edit', compact('loyaltyRule', 'clients'));
    }

    public function update(Request $request, $id)
{
    // Find the loyalty rule by ID
    $loyaltyRule = LoyaltyRule::findOrFail($id);

    // Validate the incoming request data
    $request->validate([
        'name' => 'required|string|max:255',
        'status' => 'required|integer',
        'priority_level' => 'nullable|string|max:255',
        'collection_factor' => 'nullable|numeric',
        'minimum_total_spent' => 'nullable|numeric',
        'currency_type' => 'nullable|integer',
        'period' => 'nullable|integer',
        'period_unit' => 'nullable|integer',
        'client_ids' => 'required|array', // Ensure client_ids is an array
    ]);

    // Update the loyalty rule
    $loyaltyRule->update($request->all());

    // Sync the selected clients with the loyalty rule
    $loyaltyRule->clients()->sync($request->input('client_ids'));

    // Redirect to the index with a success message
    return redirect()->route('loyalty_points.index')->with('success', 'تم تعديل قوانين النقاط بنجاح.');
}

    public function destroy($id)
    {
        // Find the loyalty rule by ID
        $loyaltyRule = LoyaltyRule::findOrFail($id);

        // Delete the loyalty rule
        $loyaltyRule->delete();

        // Redirect to the index with a success message
        return redirect()->route('loyalty_points.index')->with('success', 'تم حذف قوانين النقاط بنجاح.');
    }
}
