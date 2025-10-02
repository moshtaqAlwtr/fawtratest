<?php

namespace Modules\Pos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashierDevice;
use App\Models\StoreHouse;
use Illuminate\Http\Request;

class DevicesController extends Controller

{

 public function index(Request $request)
{
    $query = CashierDevice::with(['store']);
    
    // البحث بالاسم
    if ($request->filled('search')) {
    }
    
    // التصفية حسب المخزن
    if ($request->filled('store_id')) {
        $query->byStore($request->store_id);
    }
    
    // التصفية حسب الحالة
    if ($request->filled('status')) {
        $query->byStatus($request->status);
    }
    
    $devices = $query->latest()->paginate(15);
    
    // جلب البيانات للفلاتر
    $stores = StoreHouse::orderBy('name')->get();
    $statusOptions = CashierDevice::getStatusOptions();
    
    return view('pos.settings.devices.index', compact('devices', 'stores', 'statusOptions'));
}
    public function create()
    {
        
        $storehouses = StoreHouse::orderBy('id')->get();
        $devices     = CashierDevice::all();
        $statusOptions = CashierDevice::getStatusOptions();
        return view('pos.settings.devices.create',compact('storehouses','devices','statusOptions'));
    }
 public function edit($id)
{
    try {
        $device = CashierDevice::findOrFail($id);
        $storehouses = StoreHouse::orderBy('name')->get();
        $devices = CashierDevice::where('id', '!=', $id)->get(); // استبعاد الجهاز الحالي
        $statusOptions = CashierDevice::getStatusOptions();
        
        return view('pos.settings.devices.edit', compact('device', 'storehouses', 'devices', 'statusOptions'));
    } catch (\Exception $e) {
        return redirect()->route('pos.settings.devices.index')
            ->with('error', 'الجهاز المطلوب غير موجود');
    }
}

// دالة تحديث البيانات
public function update(Request $request, $id)
{
    $device = CashierDevice::findOrFail($id);
    
    $validatedData = $request->validate([
        'device_name' => 'required|string|max:255|min:3',
        'store_id' => 'required|exists:store_houses,id',
        'main_category_id' => 'nullable|exists:cashier_devices,id',
        'device_status' => 'required|in:active,inactive,maintenance,damaged',
        'device_image' => 'nullable|image|mimes:jpeg,jpg,png|max:20480',
        'description' => 'nullable|string|max:1000',
    ], [
        'device_name.required' => 'اسم الجهاز مطلوب',
        'device_name.string' => 'اسم الجهاز يجب أن يكون نص',
        'device_name.max' => 'اسم الجهاز يجب ألا يتجاوز 255 حرف',
        'device_name.min' => 'اسم الجهاز يجب أن يكون على الأقل 3 أحرف',
        
        'store_id.required' => 'المخزن مطلوب',
        'store_id.exists' => 'المخزن المختار غير موجود',
        
        'main_category_id.exists' => 'التصنيف المختار غير موجود',
        
        'device_status.required' => 'حالة الجهاز مطلوبة',
        'device_status.in' => 'حالة الجهاز غير صحيحة',
        
        'device_image.image' => 'الملف يجب أن يكون صورة',
        'device_image.mimes' => 'صيغة الصورة يجب أن تكون jpeg, jpg, أو png',
        'device_image.max' => 'حجم الصورة يجب ألا يتجاوز 20MB',
        
        'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف',
    ]);

    try {
        // التعامل مع رفع الصورة الجديدة
        if ($request->hasFile('device_image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($device->device_image && \Storage::disk('public')->exists($device->device_image)) {
                \Storage::disk('public')->delete($device->device_image);
            }
            
            // رفع الصورة الجديدة
            $validatedData['device_image'] = $request->file('device_image')
                ->store('cashier-devices', 'public');
        }

        // تحديث البيانات
        $device->update($validatedData);

        return redirect()->route('pos.settings.devices.index')
            ->with('success', 'تم تحديث الجهاز بنجاح');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'حدث خطأ أثناء تحديث البيانات. يرجى المحاولة مرة أخرى.');
    }
}

// دالة الحذف
public function destroy($id)
{
    
    // try {
        $device = CashierDevice::findOrFail($id);
        
        // حذف الصورة المرتبطة بالجهاز
        if ($device->device_image && \Storage::disk('public')->exists($device->device_image)) {
            \Storage::disk('public')->delete($device->device_image);
        }
        
        // حذف الجهاز
        $deviceName = $device->device_name;
        $device->delete(); // سيستخدم SoftDeletes إذا كان مفعلاً
        
        return redirect()->route('pos.settings.devices.index')
            ->with('success', "تم حذف الجهاز '{$deviceName}' بنجاح");
            
    // } catch (\Exception $e) {
    //     return redirect()->route('pos.settings.devices.index')
    //         ->with('error', 'حدث خطأ أثناء حذف الجهاز. يرجى المحاولة مرة أخرى.');
    // }
}

public function store(Request $request)
{
    $validatedData = $request->validate([
        'device_name' => 'required|string|max:255|min:3',
        'store_id' => 'required|exists:store_houses,id',
       'main_category_id' => 'nullable|exists:cashier_devices,id',

        'device_status' => 'required|in:active,inactive,maintenance,damaged',
        'device_image' => 'nullable|image|mimes:jpeg,jpg,png|max:20480',
        'description' => 'nullable|string|max:1000',
    ], [
        'device_name.required' => 'اسم الجهاز مطلوب',
        'device_name.string' => 'اسم الجهاز يجب أن يكون نص',
        'device_name.max' => 'اسم الجهاز يجب ألا يتجاوز 255 حرف',
        'device_name.min' => 'اسم الجهاز يجب أن يكون على الأقل 3 أحرف',
        
        'store_id.required' => 'المخزن مطلوب',
        'store_id.exists' => 'المخزن المختار غير موجود',
        
        'main_category_id.exists' => 'التصنيف المختار غير موجود',
        
        'device_status.required' => 'حالة الجهاز مطلوبة',
        'device_status.in' => 'حالة الجهاز غير صحيحة',
        
        'device_image.image' => 'الملف يجب أن يكون صورة',
        'device_image.mimes' => 'صيغة الصورة يجب أن تكون jpeg, jpg, أو png',
        'device_image.max' => 'حجم الصورة يجب ألا يتجاوز 20MB',
        
        'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف',
    ]);

    try {
        // رفع الصورة إذا تم اختيارها
        if ($request->hasFile('device_image')) {
            $validatedData['device_image'] = $request->file('device_image')
                ->store('cashier-devices', 'public');
        }

        $device = CashierDevice::create($validatedData);

        return redirect()->route('pos.settings.devices.index')
            ->with('success', 'تم إضافة الجهاز بنجاح');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'حدث خطأ أثناء حفظ البيانات. يرجى المحاولة مرة أخرى.');
    }
}

 public function toggleStatus(Request $request, $id)
    {
        $device = CashierDevice::findOrFail($id);
        
        $request->validate([
            'device_status' => 'required|in:active,inactive,maintenance,damaged'
        ], [
            'device_status.required' => 'حالة الجهاز مطلوبة',
            'device_status.in' => 'حالة الجهاز غير صحيحة'
        ]);

        try {
            $oldStatus = $device->status_text;
            $device->update(['device_status' => $request->device_status]);
            $newStatus = $device->fresh()->status_text;

            return redirect()->route('pos.settings.devices.index')
                ->with('success', "تم تغيير حالة الجهاز '{$device->device_name}' من {$oldStatus} إلى {$newStatus}");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تغيير حالة الجهاز: ' . $e->getMessage());
        }
    }
}

