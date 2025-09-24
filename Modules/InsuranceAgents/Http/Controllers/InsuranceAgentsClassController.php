<?php

namespace Modules\InsuranceAgents\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InsuranceAgentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InsuranceAgentsClassController extends Controller
{
    public function create($insurance_agent_id)
    {
        $categories = Category::all(); // جلب جميع الفئات
        return view('insuranceagents::insurance_agent_classes.create', compact('categories', 'insurance_agent_id'));
    }
public function store(Request $request)
{
    try {
        // التحقق من البيانات المدخلة مع رسائل الخطأ المخصصة
        $validatedData = $request->validate([
            'insurance_agent_id' => 'required|exists:insurance_agents,id',
            'name' => 'required|string|max:255',
            'category_id' => 'required|array|min:1',
            'category_id.*' => 'required|exists:categories,id',
            'discount' => 'required|array|min:1',
            'discount.*' => 'required|numeric|min:0',
            'company_copayment' => 'required|array|min:1',
            'company_copayment.*' => 'required|numeric|min:0|max:100',
            'client_copayment' => 'required|array|min:1',
            'client_copayment.*' => 'required|numeric|min:0|max:100',
            'max_copayment' => 'required|array|min:1',
            'max_copayment.*' => 'required|numeric|min:0',
            'type' => 'required|array|min:1',
            'type.*' => 'required|in:1,2',
        ], [
            'insurance_agent_id.required' => 'معرف وكيل التأمين مطلوب',
            'insurance_agent_id.exists' => 'وكيل التأمين المختار غير موجود',
            'name.required' => 'اسم الفئة مطلوب',
            'name.string' => 'اسم الفئة يجب أن يكون نص',
            'name.max' => 'اسم الفئة يجب ألا يتجاوز 255 حرف',
            'category_id.required' => 'يجب اختيار تصنيف واحد على الأقل',
            'category_id.*.required' => 'يجب اختيار التصنيف',
            'category_id.*.exists' => 'التصنيف المختار غير موجود',
            'discount.*.required' => 'قيمة الخصم مطلوبة',
            'discount.*.numeric' => 'قيمة الخصم يجب أن تكون رقم',
            'discount.*.min' => 'قيمة الخصم يجب أن تكون أكبر من أو تساوي 0',
            'company_copayment.*.required' => 'نسبة الشركة مطلوبة',
            'company_copayment.*.numeric' => 'نسبة الشركة يجب أن تكون رقم',
            'company_copayment.*.min' => 'نسبة الشركة يجب أن تكون أكبر من أو تساوي 0',
            'company_copayment.*.max' => 'نسبة الشركة يجب أن تكون أقل من أو تساوي 100',
            'client_copayment.*.required' => 'نسبة العميل مطلوبة',
            'client_copayment.*.numeric' => 'نسبة العميل يجب أن تكون رقم',
            'client_copayment.*.min' => 'نسبة العميل يجب أن تكون أكبر من أو تساوي 0',
            'client_copayment.*.max' => 'نسبة العميل يجب أن تكون أقل من أو تساوي 100',
            'max_copayment.*.required' => 'الحد الأقصى للدفع المشترك مطلوب',
            'max_copayment.*.numeric' => 'الحد الأقصى للدفع المشترك يجب أن يكون رقم',
            'max_copayment.*.min' => 'الحد الأقصى للدفع المشترك يجب أن يكون أكبر من أو يساوي 0',
            'type.*.required' => 'نوع الدفع مطلوب',
            'type.*.in' => 'نوع الدفع يجب أن يكون 1 أو 2',
        ]);

        // التحقق من أن مجموع نسب الشركة والعميل = 100 لكل صف
        for ($i = 0; $i < count($request->category_id); $i++) {
            $companyPercent = floatval($request->company_copayment[$i]);
            $clientPercent = floatval($request->client_copayment[$i]);
            $total = $companyPercent + $clientPercent;

            if (abs($total - 100) > 0.01) {
                $errorMessage = "مجموع نسبة الشركة والعميل يجب أن يساوي 100% في الصف " . ($i + 1);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }
                return redirect()->back()->with('error', $errorMessage)->withInput();
            }
        }

        // حفظ البيانات
        for ($i = 0; $i < count($request->category_id); $i++) {
            InsuranceAgentCategory::create([
                'insurance_agent_id' => $validatedData['insurance_agent_id'],
                'name' => $validatedData['name'],
                'category_id' => $validatedData['category_id'][$i],
                'discount' => $validatedData['discount'][$i],
                'company_copayment' => $validatedData['company_copayment'][$i],
                'client_copayment' => $validatedData['client_copayment'][$i],
                'max_copayment' => $validatedData['max_copayment'][$i],
                'type' => $validatedData['type'][$i],
            ]);
        }


        $successMessage = 'تم حفظ البيانات بنجاح!';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'redirect' => route('Insurance_Agents.index')
            ]);
        }

        return redirect()->route('Insurance_Agents.index')->with('success', $successMessage);

    } catch (\Illuminate\Validation\ValidationException $e) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'يوجد أخطاء في البيانات المدخلة',
                'errors' => $e->validator->errors()
            ], 422);
        }
        return redirect()->back()->withErrors($e->validator)->withInput();
    } catch (\Exception $e) {
        Log::error('Error in InsuranceAgentCategory store: ' . $e->getMessage());

        $errorMessage = 'حدث خطأ أثناء حفظ البيانات، الرجاء المحاولة مرة أخرى';

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }

        return redirect()->back()->with('error', $errorMessage)->withInput();
    }
}

public function update(Request $request, $id)
{
    try {
        // التحقق من البيانات المدخلة مع رسائل الخطأ المخصصة
        $validatedData = $request->validate([
            'insurance_agent_id' => 'required|exists:insurance_agents,id',
            'name' => 'required|string|max:255',
            'category_id' => 'required|array|min:1',
            'category_id.*' => 'required|exists:categories,id',
            'discount' => 'required|array|min:1',
            'discount.*' => 'required|numeric|min:0',
            'company_copayment' => 'required|array|min:1',
            'company_copayment.*' => 'required|numeric|min:0|max:100',
            'client_copayment' => 'required|array|min:1',
            'client_copayment.*' => 'required|numeric|min:0|max:100',
            'max_copayment' => 'required|array|min:1',
            'max_copayment.*' => 'required|numeric|min:0',
            'type' => 'required|array|min:1',
            'type.*' => 'required|in:1,2',
        ], [
            'insurance_agent_id.required' => 'معرف وكيل التأمين مطلوب',
            'insurance_agent_id.exists' => 'وكيل التأمين المختار غير موجود',
            'name.required' => 'اسم الفئة مطلوب',
            'name.string' => 'اسم الفئة يجب أن يكون نص',
            'name.max' => 'اسم الفئة يجب ألا يتجاوز 255 حرف',
            'category_id.required' => 'يجب اختيار تصنيف واحد على الأقل',
            'category_id.*.required' => 'يجب اختيار التصنيف',
            'category_id.*.exists' => 'التصنيف المختار غير موجود',
            'discount.*.required' => 'قيمة الخصم مطلوبة',
            'discount.*.numeric' => 'قيمة الخصم يجب أن تكون رقم',
            'discount.*.min' => 'قيمة الخصم يجب أن تكون أكبر من أو تساوي 0',
            'company_copayment.*.required' => 'نسبة الشركة مطلوبة',
            'company_copayment.*.numeric' => 'نسبة الشركة يجب أن تكون رقم',
            'company_copayment.*.min' => 'نسبة الشركة يجب أن تكون أكبر من أو تساوي 0',
            'company_copayment.*.max' => 'نسبة الشركة يجب أن تكون أقل من أو تساوي 100',
            'client_copayment.*.required' => 'نسبة العميل مطلوبة',
            'client_copayment.*.numeric' => 'نسبة العميل يجب أن تكون رقم',
            'client_copayment.*.min' => 'نسبة العميل يجب أن تكون أكبر من أو تساوي 0',
            'client_copayment.*.max' => 'نسبة العميل يجب أن تكون أقل من أو تساوي 100',
            'max_copayment.*.required' => 'الحد الأقصى للدفع المشترك مطلوب',
            'max_copayment.*.numeric' => 'الحد الأقصى للدفع المشترك يجب أن يكون رقم',
            'max_copayment.*.min' => 'الحد الأقصى للدفع المشترك يجب أن يكون أكبر من أو يساوي 0',
            'type.*.required' => 'نوع الدفع مطلوب',
            'type.*.in' => 'نوع الدفع يجب أن يكون 1 أو 2',
        ]);

        // التحقق من أن مجموع نسب الشركة والعميل = 100 لكل صف
        for ($i = 0; $i < count($request->category_id); $i++) {
            $companyPercent = floatval($request->company_copayment[$i]);
            $clientPercent = floatval($request->client_copayment[$i]);
            $total = $companyPercent + $clientPercent;

            if (abs($total - 100) > 0.01) {
                $errorMessage = "مجموع نسبة الشركة والعميل يجب أن يساوي 100% في الصف " . ($i + 1);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }
                return redirect()->back()->with('error', $errorMessage)->withInput();
            }
        }

        // حذف البيانات القديمة
        InsuranceAgentCategory::where('insurance_agent_id', $validatedData['insurance_agent_id'])->delete();

        // حفظ البيانات الجديدة
        for ($i = 0; $i < count($request->category_id); $i++) {
            InsuranceAgentCategory::create([
                'insurance_agent_id' => $validatedData['insurance_agent_id'],
                'name' => $validatedData['name'],
                'category_id' => $validatedData['category_id'][$i],
                'discount' => $validatedData['discount'][$i],
                'company_copayment' => $validatedData['company_copayment'][$i],
                'client_copayment' => $validatedData['client_copayment'][$i],
                'max_copayment' => $validatedData['max_copayment'][$i],
                'type' => $validatedData['type'][$i],
            ]);
        }



        $successMessage = 'تم تحديث البيانات بنجاح!';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'redirect' => route('Insurance_Agents.show', $validatedData['insurance_agent_id'])
            ]);
        }

        return redirect()->route('Insurance_Agents.show', $validatedData['insurance_agent_id'])->with('success', $successMessage);

    } catch (\Illuminate\Validation\ValidationException $e) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'يوجد أخطاء في البيانات المدخلة',
                'errors' => $e->validator->errors()
            ], 422);
        }
        return redirect()->back()->withErrors($e->validator)->withInput();
    } catch (\Exception $e) {
        Log::error('Error in InsuranceAgentCategory update: ' . $e->getMessage());

        $errorMessage = 'حدث خطأ أثناء تحديث البيانات، الرجاء المحاولة مرة أخرى';

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }

        return redirect()->back()->with('error', $errorMessage)->withInput();
    }
}
    public function edit($id)
{
    // Fetch the insurance agent category by ID
    $insuranceAgentCategory = InsuranceAgentCategory::findOrFail($id);

    // Fetch all categories
    $categories = Category::all(); // جلب جميع الفئات

    // Fetch the insurance agent ID from the insurance agent category
    $insurance_agent_id = $insuranceAgentCategory->insurance_agent_id; // Assuming you have this field

    // Pass both the insurance agent category, categories, and insurance agent ID to the view
    return view('insuranceagents::insurance_agent_classes.edit', compact('insuranceAgentCategory', 'categories', 'insurance_agent_id'));
}

    public function show($id)
    {
        // Fetch the insurance agent category by ID
        $insuranceAgentCategory = InsuranceAgentCategory::findOrFail($id);

        // Fetch the associated insurance agent
        $insuranceAgent = $insuranceAgentCategory->insuranceAgent; // Assuming you have a relationship defined

        // Fetch all categories related to the insurance agent
        $categories = InsuranceAgentCategory::where('insurance_agent_id', $insuranceAgent->id)->get();

        // Pass both variables to the view
        return view('insuranceagents::insurance_agent_classes.show', compact('insuranceAgentCategory', 'insuranceAgent', 'categories'));
    }

public    function destroy($id)
{
    $category = InsuranceAgentCategory::findOrFail($id);
    $category->delete();
    return redirect()->route('Insurance_Agents.show', $category->insurance_agent_id)->with('success', 'تم حذف البيانات بنجاح');
}
}
