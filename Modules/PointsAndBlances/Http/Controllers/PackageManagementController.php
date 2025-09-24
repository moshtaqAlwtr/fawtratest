<?php

namespace Modules\PointsAndBlances\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\BalanceType;
use App\Models\BalanceTypePackage;
use App\Models\Employee;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PackageManagementController extends Controller
{

    public function index(Request $request)
    {
        // إعداد الاستعلام الأساسي
        $query = Package::with('balanceTypes');

        // تطبيق الفلاتر
        if ($request->has('name') && $request->name != '') {
            $query->where('commission_name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('period') && $request->period != '') {
            $query->where('duration', $request->period); // تأكد من أن لديك علاقة صحيحة
        }

        if ($request->has('type') && $request->type != '') {
            if ($request->type == 'membership') {
                $query->where('members', 1);
            } elseif ($request->type == 'balance_recharge') {
                $query->where('members', 2);
            }
        }

        // جلب البيانات
        $packages = $query->get();

        return view('pointsandbalances::package_management.index', compact('packages'));
    }
    public function create()
    {
        $employees = Employee::all();
        $balanceTypes = BalanceType::all();
        return view('', compact('employees', 'balanceTypes'));
    }

    public function store(Request $request)
{
    try {
        // Validate the incoming request data
        $request->validate([
            'commission_name' => 'required|string|max:255',
            'status' => 'nullable|integer|in:1,2',
            'price' => 'nullable|numeric',

            'duration' => 'nullable|string|max:255',
            'payment_rate' => 'nullable|integer|in:1,2',
            'description' => 'nullable|string',
            'balance_types.*.id' => 'nullable|exists:balance_types,id', // تأكد من وجود id
            'balance_types.*.balance_value' => 'nullable|numeric', // اجعلها مطلوبة
        ]);

        // Debugging: Check if data is coming through
        Log::info('Request data:', $request->all());

        // Create a new package instance
        $package = new Package();
        $package->commission_name = $request->commission_name;
        $package->status = $request->status;
        $package->price = $request->price;
        $package->period = $request->period;
        $package->duration = $request->duration;
        $package->payment_rate = $request->payment_rate;
        $package->description = $request->description;

        // Save the package
        $package->save();

        // Log success
        Log::info('Package saved successfully:', $package->toArray());

        // Handle balance types
        if ($request->has('balance_types')) {
            foreach ($request->balance_types as $balanceType) {
                if (isset($balanceType['id']) && isset($balanceType['balance_value'])) {
                    // إضافة سجل جديد إلى الجدول الوسيط
                    BalanceTypePackage::create([
                        'balance_type_id' => $balanceType['id'], // استخدم id لإضافة السجل
                        'package_id' => $package->id,
                        'balance_value' => $balanceType['balance_value'],
                    ]);
                }
            }
        }

        // Redirect or return a response
        return redirect()->route('PackageManagement.index')->with('success', 'تم حفظ الباقة بنجاح.');

    } catch (\Exception $e) {
        // Log the error
        Log::error('Error saving package: ' . $e->getMessage());

        // Optionally, return back with an error message
        return back()
            ->withErrors(['error' => 'حدث خطأ أثناء حفظ الباقة: ' . $e->getMessage()])
            ->withInput();
    }
}

    public function edit($id)
    {
        // Get the package and related balance types
        $package = Package::with('balanceTypes')->findOrFail($id);
        $balanceTypes = BalanceType::all();
        $employees = Employee::all();

        return view('pointsAndBalances.package_management.edit', compact('package', 'balanceTypes', 'employees'));
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate the incoming request data
            $request->validate([
                'commission_name' => 'required|string|max:255',
                'status' => 'nullable|integer|in:1,2',
                'price' => 'nullable|numeric',
                'duration' => 'nullable|string|max:255',
                'payment_rate' => 'nullable|integer|in:1,2',
                'description' => 'nullable|string',
                'balance_types.*.id' => 'nullable|exists:balance_types,id', // تأكد من وجود id
                'balance_types.*.balance_value' => 'nullable|numeric', // اجعلها مطلوبة
            ]);

            // Debugging: Check if data is coming through
            Log::info('Request data:', $request->all());

            // Find the package to update
            $package = Package::findOrFail($id);
            $package->commission_name = $request->commission_name;
            $package->status = $request->status;
            $package->price = $request->price;
            $package->duration = $request->duration;
            $package->payment_rate = $request->payment_rate;
            $package->description = $request->description;

            // Save the updated package
            $package->save();

            // Log success
            Log::info('Package updated successfully:', $package->toArray());

            // Handle balance types
            if ($request->has('balance_types')) {
                // First, delete existing balance types for this package
                BalanceTypePackage::where('package_id', $package->id)->delete();

                foreach ($request->balance_types as $balanceType) {
                    if (isset($balanceType['id']) && isset($balanceType['balance_value'])) {
                        // إضافة سجل جديد إلى الجدول الوسيط
                        BalanceTypePackage::create([
                            'balance_type_id' => $balanceType['id'], // استخدم id لإضافة السجل
                            'package_id' => $package->id,
                            'balance_value' => $balanceType['balance_value'],
                        ]);
                    }
                }
            }

            // Redirect or return a response
            return redirect()->route('PackageManagement.index')->with('success', 'تم تحديث الباقة بنجاح.');

        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating package: ' . $e->getMessage());

            // Optionally, return back with an error message
            return back()
                ->withErrors(['error' => 'مشكلة في تحديث الباقة: ' . $e->getMessage()])
                ->withInput();
        }
    }
    public function show($id)
    {
        // جلب الباقة المحددة من قاعدة البيانات مع أنواع الرصيد
        $package = Package::with('balanceTypes')->findOrFail($id); // تأكد من وجود العلاقة في النموذج

        return view('pointsAndBalances.package_management.show', compact('package'));
    }
    public function destroy($id)

    {
        // Find the package by ID
        $package = Package::findOrFail($id);

        // Delete the package
        $package->delete();

        // Log success
        Log::info('Package deleted successfully:', $package->toArray());

        // Redirect or return a response
        return redirect()->route('PackageManagement.index')->with('success', 'Package deleted successfully.');
    }



    public function updateStatus($id)
{
    $package = Package::find($id);

    if (!$package) {
        return redirect()->route('PackageManagement.show',$id)->with(['error' => ' نوع الرصيد غير موجود!']);
    }

    $package->update(['status' => !$package->status]);

    return redirect()->route('PackageManagement.show',$id)->with(['success' => 'تم تحديث حالة نوع الرصيد بنجاح!']);
}
}
