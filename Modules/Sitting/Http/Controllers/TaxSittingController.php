<?php

namespace Modules\Sitting\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\TaxSitting;
use Illuminate\Http\Request;
use DB;
class TaxSittingController extends Controller
{
    public function index()
    {
        $tax = TaxSitting::all();
        return view('sitting::tax_sitting.index',compact('tax'));
    }

public function updateAll(Request $request)
{
    $ids = $request->id;
    $names = $request->name;
    $taxes = $request->tax;
    $types = $request->type;

    foreach ($ids as $index => $id) {
        if ($id) {
            // تحديث الضريبة الموجودة
            $tax = TaxSitting::find($id);
            if ($tax) {
                $tax->update([
                    'name' => $names[$index],
                    'tax' => $taxes[$index],
                    'type' => $types[$index],
                ]);
            }
        } else {
            // إضافة ضريبة جديدة
            TaxSitting::create([
                'name' => $names[$index],
                'tax' => $taxes[$index],
                'type' => $types[$index],
            ]);
        }
    }

    return redirect()->back()->with('success', 'تم تحديث الضرائب بنجاح');
}
public function destroy($id)
{
    try {
        $tax = TaxSitting::findOrFail($id);
        $tax->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الضريبة بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()
        ], 500);
    }
}








}
