<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Region_groub;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function groupClient(Request $request)
{
    try {
        $query = Region_groub::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('directions_id')) {
            $query->where('directions_id', $request->directions_id);
        }

        $groups = $query->with(['branch', 'direction', 'neighborhoods.client:id,trade_name,code'])
                        ->orderBy('id', 'desc')
                        ->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المجموعات بنجاح',
            'data' => $groups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'branch' => $group->branch?->name,
                    'direction' => $group->direction?->name,
                    //  'clients_count' => $group->neighborhoods->pluck('client')->filter()->unique('id')->count(),
                    //  'clients' => $group->neighborhoods->pluck('client')->filter()->unique('id')->map(function ($client) {
                    //      return [
                    //          'id' => $client->id,
                    //          'name' => $client->trade_name,
                    //          'code' => $client->code,
                    //         'neighborhood_id' => $client->neighborhood_id,
                    //     ];
                    // })->values(),
                ];
            }),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء جلب المجموعات',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function storeGroupClient(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|unique:region_groubs,name',
            'branch_id' => 'required|exists:branches,id',
            'directions_id' => 'required|exists:directions,id',
        ]);

        $regionGroup = Region_groub::create([
            'name' => $validated['name'],
            'branch_id' => $validated['branch_id'],
            'directions_id' => $validated['directions_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المجموعة بنجاح',
            'data' => [
                'id' => $regionGroup->id,
                'name' => $regionGroup->name,
                'branch_id' => $regionGroup->branch_id,
                'directions_id' => $regionGroup->directions_id,
            ],
        ], 201);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء إنشاء المجموعة',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('api::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('api::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('api::edit');
    }

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|unique:region_groubs,name,' . $id,
        'branch_id' => 'required|exists:branches,id',
        'directions_id' => 'required|exists:directions,id',
    ]);

    $group = Region_groub::findOrFail($id);
    $group->update([
        'name' => $request->name,
        'branch_id' => $request->branch_id,
        'directions_id' => $request->directions_id,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'تم تحديث المجموعة بنجاح',
        'data' => $group
    ]);
}

   
 

    /**
     * Remove the specified resource from storage.
     */
public function destroy($id)
{
    $group = Region_groub::findOrFail($id);
    $group->delete();

    return response()->json([
        'success' => true,
        'message' => 'تم حذف المجموعة بنجاح'
    ]);
}

}
