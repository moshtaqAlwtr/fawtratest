<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderSource;
use App\Models\settingsOrderSource;

class OrderSourceController extends Controller
{
    public function index()
    {
          $sources = OrderSource::orderBy('sort_order')->get();
        // الإعدادات الافتراضية
        $default_id = settingsOrderSource::get('default_order_source_id');
        $is_mandatory = settingsOrderSource::get('order_source_mandatory', '0');
        
        
       
        return view('sales.sitting.order_sources.index', compact('sources', 'default_id', 'is_mandatory'));
    }

  public function storeOrUpdate(Request $request)
{
    // حفظ وتحديث مصادر الطلب
    $ids = $request->input('ids', []);
    $names = $request->input('name', []);
    $actives = $request->input('active', []);
    $orders = $request->input('sort_order', []);

    foreach ($names as $i => $name) {
        if ($ids[$i]) {
            $src = OrderSource::find($ids[$i]);
            if ($src) {
                $src->update([
                    'name' => $name,
                    'active' => isset($actives[$i]) && $actives[$i] == 1,
                    'sort_order' => $orders[$i] ?? $i,
                ]);
            }
        } else {
            OrderSource::create([
                'name' => $name,
                'active' => isset($actives[$i]) && $actives[$i] == 1,
                'sort_order' => $orders[$i] ?? $i,
            ]);
        }
    }

    // ⬅️ هنا تضع أسطر إعدادات النظام (بعد الحفظ وقبل التحويل)
    settingsOrderSource::set('default_order_source_id', $request->default_order_source_id);
    settingsOrderSource::set('order_source_mandatory', $request->order_source_mandatory ? '1' : '0');

   return back()->with('success', 'تم حفظ التغييرات بنجاح');

}


    public function updateAll(Request $request)
    {
        $ids = $request->input('ids', []);
        $names = $request->input('name', []);
        $actives = $request->input('active', []);

        foreach ($ids as $index => $id) {
            $source = OrderSource::find($id);
            if ($source) {
                $source->name = $names[$index];
                $source->active = isset($actives[$index]) ? 1 : 0;
                $source->save();
            }
        }
        return redirect()->route('order_sources.index')->with('success', 'تم تحديث مصادر الطلب بنجاح');
    }

     public function destroy($id)
    {
        OrderSource::destroy($id);
        return response()->json(['status' => 'deleted']);
    }

    // ترتيب السحب
    public function sort(Request $request)
    {
        foreach ($request->order as $i => $id) {
            OrderSource::where('id', $id)->update(['sort_order' => $i]);
        }
        return response()->json(['status' => 'ok']);
    }
}



