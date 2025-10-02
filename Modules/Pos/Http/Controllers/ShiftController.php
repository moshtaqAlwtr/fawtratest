<?php

namespace Modules\Pos\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\shifts_pos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $query = shifts_pos::with(['parent']);
        
        // البحث بالاسم
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // التصفية حسب الوردية الأب
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }
        
        $shifts = $query->latest()->paginate(15);
        
        // جلب الورديات الرئيسية للفلتر
        $parentShifts = shifts_pos::whereNull('parent_id')->orderBy('name')->get();
        
        return view('pos.settings.shift.index', compact('shifts', 'parentShifts'));
    }

    public function create()
    {
        // جلب الورديات الرئيسية للتصنيف
        $parentShifts = shifts_pos::whereNull('parent_id')->orderBy('name')->get();
        
        return view('pos.settings.shift.create', compact('parentShifts'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|min:3',
            'parent_id' => 'nullable|exists:shifts_pos,id',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'اسم الوردية مطلوب',
            'name.string' => 'اسم الوردية يجب أن يكون نص',
            'name.max' => 'اسم الوردية يجب ألا يتجاوز 255 حرف',
            'name.min' => 'اسم الوردية يجب أن يكون على الأقل 3 أحرف',
            'parent_id.exists' => 'التصنيف المختار غير موجود',
            'attachment.file' => 'يجب أن يكون الملف صالحاً',
            'attachment.mimes' => 'صيغة الملف يجب أن تكون pdf, doc, docx, jpg, jpeg, أو png',
            'attachment.max' => 'حجم الملف يجب ألا يتجاوز 10MB',
            'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف',
        ]);

        try {
            // رفع المرفق إذا تم اختياره
            if ($request->hasFile('attachment')) {
                $validatedData['attachment'] = $request->file('attachment')
                    ->store('shifts-attachments', 'public');
            }

            $shift = shifts_pos::create($validatedData);

            return redirect()->route('pos.settings.shift.index')
                ->with('success', 'تم إضافة الوردية بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء حفظ البيانات. يرجى المحاولة مرة أخرى.');
        }
    }

    public function show($id)
    {
        try {
            $shift = shifts_pos::with(['parent', 'children'])->findOrFail($id);
            return view('pos.settings.shift.show', compact('shift'));
        } catch (\Exception $e) {
            return redirect()->route('pos.settings.shift.index')
                ->with('error', 'الوردية المطلوبة غير موجودة');
        }
    }

    public function edit($id)
    {
        try {
            $shift = shifts_pos::findOrFail($id);
            $parentShifts = shifts_pos::whereNull('parent_id')
                ->where('id', '!=', $id) // استبعاد الوردية الحالية
                ->orderBy('name')
                ->get();
            
            return view('pos.settings.shift.edit', compact('shift', 'parentShifts'));
        } catch (\Exception $e) {
            return redirect()->route('pos.settings.shift.index')
                ->with('error', 'الوردية المطلوبة غير موجودة');
        }
    }

    public function update(Request $request, $id)
    {
        $shift = shifts_pos::findOrFail($id);
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|min:3',
            'parent_id' => 'nullable|exists:shifts_pos,id',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'اسم الوردية مطلوب',
            'name.min' => 'اسم الوردية يجب أن يكون على الأقل 3 أحرف',
            'parent_id.exists' => 'التصنيف المختار غير موجود',
            'attachment.file' => 'يجب أن يكون الملف صالحاً',
            'attachment.mimes' => 'صيغة الملف غير مدعومة',
            'attachment.max' => 'حجم الملف يجب ألا يتجاوز 10MB',
        ]);

        try {
            // التعامل مع رفع المرفق الجديد
            if ($request->hasFile('attachment')) {
                // حذف المرفق القديم إذا كان موجوداً
                if ($shift->attachment && Storage::disk('public')->exists($shift->attachment)) {
                    Storage::disk('public')->delete($shift->attachment);
                }
                
                // رفع المرفق الجديد
                $validatedData['attachment'] = $request->file('attachment')
                    ->store('shifts-attachments', 'public');
            }

            $shift->update($validatedData);

            return redirect()->route('pos.settings.shift.index')
                ->with('success', 'تم تحديث الوردية بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث البيانات');
        }
    }

    public function destroy($id)
    {
        try {
            $shift = shifts_pos::findOrFail($id);
            
            // التحقق من وجود ورديات فرعية
            if ($shift->children()->count() > 0) {
                return redirect()->route('pos.settings.shift.index')
                    ->with('error', 'لا يمكن حذف هذه الوردية لأن لديها ورديات فرعية');
            }
            
            // حذف المرفق المرتبط بالوردية
            if ($shift->attachment && Storage::disk('public')->exists($shift->attachment)) {
                Storage::disk('public')->delete($shift->attachment);
            }
            
            $shiftName = $shift->name;
            $shift->delete();
            
            return redirect()->route('pos.settings.shift.index')
                ->with('success', "تم حذف الوردية '{$shiftName}' بنجاح");
                
        } catch (\Exception $e) {
            return redirect()->route('pos.settings.shift.index')
                ->with('error', 'حدث خطأ أثناء حذف الوردية');
        }
    }
}

