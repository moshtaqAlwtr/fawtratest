<?php

namespace Modules\Sales\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ShippingOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShippingOptionsController extends Controller
{
    public function index(Request $request)
    {
        $query = ShippingOption::query();

        // البحث حسب الاسم
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // البحث حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $shippingOptions = $query->orderBy('display_order')->get();

        return view('sales::sitting.shipping_options.index', compact('shippingOptions'));
    }

    public function create()
    {
        $accounts = Account::all();
        $shippingOptions = ShippingOption::all();

        return view('sales::sitting.shipping_options.create', compact('accounts', 'shippingOptions'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'cost' => 'required|numeric|min:0',
                'tax' => 'required|numeric|min:0',
                'status' => 'required|integer|in:1,2',
                'display_order' => 'required|integer|min:0',
                'default_account_id' => 'required|exists:accounts,id',
            ]);

            // Get the last display order if not provided
            if (!$request->filled('display_order')) {
                $validatedData['display_order'] = ShippingOption::max('display_order') + 1;
            }

            ShippingOption::create($validatedData);

            DB::commit();

            session()->flash('success', 'تم إضافة خيار الشحن بنجاح');
            return redirect()->route('shippingOptions.index');
        } catch (\Exception $e) {
            DB::rollback();
            // إضافة تفاصيل الخطأ للتطوير فقط
            if (config('app.debug')) {
                session()->flash('error', 'حدث خطأ أثناء إضافة خيار الشحن: ' . $e->getMessage());
            } else {
                session()->flash('error', 'حدث خطأ أثناء إضافة خيار الشحن');
            }
            Log::error('خطأ في إضافة خيار الشحن: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $shippingOption = ShippingOption::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'cost' => 'required|numeric|min:0',
                'tax' => 'required|numeric|min:0',
                'status' => 'required|in:1,2',
                'display_order' => 'required|integer|min:0',
                'default_account_id' => 'required|exists:accounts,id',
            ]);

            $shippingOption->update($validatedData);

            DB::commit();

            session()->flash('success', 'تم تحديث خيار الشحن بنجاح');
            return redirect()->route('shippingOptions.index');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'حدث خطأ أثناء تحديث خيار الشحن');
            return redirect()->back()->withInput();
        }
    }

    public function updateStatus($id)
    {
        try {
            $shippingOption = ShippingOption::findOrFail($id);
            $shippingOption->status = $shippingOption->status == 1 ? 2 : 1;
            $shippingOption->save();

            session()->flash('success', 'تم تحديث حالة خيار الشحن بنجاح');
            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء تحديث حالة خيار الشحن');
            return redirect()->back();
        }
    }
    public function show($id)
    {
        $shippingOption = ShippingOption::findOrFail($id);
        return view('sales::sitting.shipping_options.show', compact('shippingOption'));
    }
    public function edit($id)
    {
        $shippingOption = ShippingOption::findOrFail($id);
        $accounts = Account::all();
        return view('sales::sitting.shipping_options.edit', compact('shippingOption', 'accounts'));
    }

    public function destroy($id)
    {
        $shippingOption = ShippingOption::findOrFail($id);
        $shippingOption->delete();

        session()->flash('success', 'تم حذف خيار الشحن بنجا��');
        return redirect()->route('shippingOptions.index');
    }
}
