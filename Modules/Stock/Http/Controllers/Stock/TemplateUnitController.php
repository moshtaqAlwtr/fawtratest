<?php


namespace Modules\Stock\Http\Controllers\Stock;
use App\Http\Controllers\Controller;
use App\Models\SubUnit;
use App\Models\TemplateUnit;
use Illuminate\Http\Request;

class TemplateUnitController extends Controller
{
    public function index()
    {
        $template_units = TemplateUnit::select()->orderBy('id', 'DESC')->get();
        return view('stock::template_unit.index', compact('template_units'));
    }

    public function create()
    {
        return view('stock::template_unit.create');
    }

    public function store(Request $request)
    {
        $templateUnit = new TemplateUnit();

        $templateUnit->base_unit_name = $request->base_unit_name;
        $templateUnit->discrimination = $request->discrimination;
        $templateUnit->template = $request->template;
        $templateUnit->status =  $request->has('status') ? 1 : 0;

        $templateUnit->save();

        foreach ($request['larger_unit_name'] as $index => $largerUnitName) {
            SubUnit::create([
                'larger_unit_name' => $largerUnitName,
                'conversion_factor' => $request['conversion_factor'][$index],
                'sub_discrimination' => $request['sub_discrimination'][$index],
                'template_unit_id' => $templateUnit->id,
            ]);
        }

        return redirect()->route('template_unit.index')->with(['success' => 'تم حفظ قالب الوحدة بنجاح']);
    }

    public function edit($id)
    {
        $templateUnit = TemplateUnit::find($id);
        return view('stock::template_unit.edit', compact('templateUnit'));
    }

    public function update(Request $request, $id)
    {
        $templateUnit = TemplateUnit::findOrFail($id);

        $templateUnit->base_unit_name = $request->base_unit_name;
        $templateUnit->discrimination = $request->discrimination;
        $templateUnit->template = $request->template;
        $templateUnit->status = $request->has('status') ? 1 : 0;

        $templateUnit->save();

        SubUnit::where('template_unit_id', $id)->delete();

        foreach ($request['larger_unit_name'] as $index => $largerUnitName) {
            SubUnit::create([
                'larger_unit_name' => $largerUnitName,
                'conversion_factor' => $request['conversion_factor'][$index],
                'sub_discrimination' => $request['sub_discrimination'][$index],
                'template_unit_id' => $templateUnit->id,
            ]);
        }

        return redirect()->route('template_unit.index')->with(['success' => 'تم تحديث قالب الوحدة بنجاح']);
    }

    public function show($id)
    {
        $templateUnit = TemplateUnit::with('sub_units')->find($id);
        return view('stock::template_unit.show', compact('templateUnit'));
    }

    public function delete($id)
    {
        $templateUnit = TemplateUnit::find($id);
        $templateUnit->sub_units()->delete();
        $templateUnit->delete();
        return redirect()->route('template_unit.index')->with(['error' => 'تم حذف قالب الوحدة بنجاح']);
    }

    public function updateStatus($id)
    {
        $templateUnit = TemplateUnit::find($id);
        $templateUnit->update(['status' => !$templateUnit->status]);
        return redirect()->route('template_unit.show',$id)->with(['success' => 'تم تحديث حالة قالب الوحدة بنجاح!']);
    }

}
