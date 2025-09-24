<?php

namespace Modules\InsuranceAgents\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\InsuranceAgent;
use App\Models\InsuranceAgentCategory;
use Illuminate\Http\Request;
use App\Models\Log as ModelsLog;
class InsuranceAgentsController extends Controller
{
public function index(Request $request)
{
    // جلب وكلاء التأمين من قاعدة البيانات مع تطبيق البحث وحساب عدد الفئات
    $query = InsuranceAgent::withCount('categories');

    // تحقق مما إذا كان هناك اسم وكيل للبحث
    if ($request->filled('agent-name')) {
        $query->where('name', 'like', '%' . $request->input('agent-name') . '%');
    }

    // تحقق مما إذا كانت هناك حالة للبحث
    if ($request->filled('status')) {
        $status = $request->input('status') == 'active' ? 1 : 2;
        $query->where('status', $status);
    }

    $insuranceAgents = $query->get();

    return view('insuranceagents::index', compact('insuranceAgents'));
}
    public function create()
    {
        $categories = InsuranceAgentCategory::all(); // جلب جميع الفئات
        return view('insuranceagents::create', compact('categories'));
    }
    public function edit($id)
    {
        $insuranceAgent = InsuranceAgent::findOrFail($id);
        $categories = InsuranceAgentCategory::where('insurance_agent_id', $id)->get();
        return view('insuranceagents::edit', compact('insuranceAgent', 'categories'));
    }
    public function store(Request $request)
    {
        // التحقق من البيانات المدخلة
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'attachments' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // قيود المرفقات
            'categories' => 'array', // تأكد من أن الفئات عبارة عن مصفوفة
        ]);

        // تخزين البيانات في قاعدة البيانات
        $insuranceAgent = InsuranceAgent::create($request->except('attachments', 'categories')); // استبعاد المرفقات والفئات من التخزين المباشر

        // معالجة المرفقات
        if ($request->hasFile('attachments')) {
            $file = $request->file('attachments');
            if ($file->isValid()) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/insuranceAgents'), $filename);
                $insuranceAgent->attachments = $filename;
                $insuranceAgent->save(); // حفظ المرفقات في قاعدة البيانات
            }
        }

        // تخزين الفئات المرتبطة
        if ($request->filled('categories')) {
            foreach ($request->categories as $categoryId) {
                // تأكد من أن لديك علاقة بين InsuranceAgent و InsuranceAgentCategory
                $insuranceAgent->categories()->attach($categoryId);
            }
        }

 // تسجيل اشعار نظام جديد
            ModelsLog::create([
                'type' => 'insuranceAgent',
                'type_id' => $insuranceAgent->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'description' => 'تم اضافة  وكيل تأمين **' . $insuranceAgent->name. '**',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);
        // إعادة توجيه مع رسالة نجاح
        return redirect()->route('Insurance_Agents.index', ['insurance_agent_id' => $insuranceAgent->id])->with('success', 'تم إضافة وكيل التأمين بنجاح!');
    }

    // دالة لتحديث البيانات
    public function update(Request $request, $id)
    {
        // التحقق من البيانات المدخلة
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'attachments' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // مثال على قيود المرفقات
        ]);

        // العثور على وكيل التأمين وتحديثه
        $insuranceAgent = InsuranceAgent::findOrFail($id);
        $insuranceAgent->update($request->all());

        // إعادة توجيه مع رسالة نجاح
        return redirect()->route('Insurance_Agents.index')->with('success', 'تم تحديث وكيل التأمين بنجاح!');
    }

    public function show($id)
    {
        $insuranceAgent = InsuranceAgent::findOrFail($id);
        $categories = InsuranceAgentCategory::where('insurance_agent_id', $id)->get();
        return view('insuranceagents::show', compact('insuranceAgent', 'categories'));
    }
    // دالة لحذف الوكيل التأمين
    public function destroy($id)
    {
        $insuranceAgent = InsuranceAgent::findOrFail($id);
        $insuranceAgent->delete();

        return redirect()->route('Insurance_Agents.index')->with('success', 'تم حذف وكيل التأمين بنجاح!');
    }
}
