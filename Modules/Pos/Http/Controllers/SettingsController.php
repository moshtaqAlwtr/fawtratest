<?php

namespace Modules\Pos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\PosGeneralSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('pos.settings.index');
    }
    public function general()
    {
        $settings = PosGeneralSetting::getSettings();
        
        // جلب البيانات للمتحكمات (Select Options)
        $customers = Client::select('id', 'trade_name')->get();
        $paymentMethods = PaymentMethod::select('id', 'name')->where('status', 'active')->get();
        $categories = Category::select('id', 'name')->get();
        $accounts = Account::select('id', 'name', 'code')->get();
        
        return view('pos.settings.general', compact(
            'settings',
            'customers', 
            'paymentMethods', 
            'categories', 
            'accounts'
        ));
    }

  
public function store(Request $request)
{
    $request->validate([
        'default_customer_id' => 'nullable|exists:clients,id',
        'invoice_template' => 'required|in:thermal,electronic',
        'active_payment_method_ids' => 'nullable|array',
        'active_payment_method_ids.*' => 'exists:payment_methods,id',
        'default_payment_method_id' => 'nullable|exists:payment_methods,id',
       
        'allowed_categories_ids' => 'nullable|array',
        'allowed_categories_ids.*' => 'exists:categories,id',
        'profit_account_id' => 'nullable|exists:accounts,id',
        'loss_account_id' => 'nullable|exists:accounts,id',
    ]);

    // التحقق من أن طريقة الدفع الافتراضية ضمن المفعلة
    if ($request->default_payment_method_id && $request->active_payment_method_ids) {
        if (!in_array($request->default_payment_method_id, $request->active_payment_method_ids)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['default_payment_method_id' => 'طريقة الدفع الافتراضية يجب أن تكون من ضمن طرق الدفع المفعلة']);
        }
    }

    // تحضير البيانات
    $data = $request->only([
        'default_customer_id',
        'invoice_template',
        'active_payment_method_ids',
        'default_payment_method_id',
        'allowed_categories_type',
        'allowed_categories_ids',
        'profit_account_id',
        'loss_account_id',
    ]);

    // معالجة الـ checkboxes
    $data['enable_departments'] = $request->has('enable_departments');
    $data['apply_custom_fields_validation'] = $request->has('apply_custom_fields_validation');
    $data['show_product_images'] = $request->has('show_product_images');
    $data['show_print_window_after_confirm'] = $request->has('show_print_window_after_confirm');
    $data['accounting_system_per_invoice'] = $request->has('accounting_system_per_invoice');
    $data['enable_auto_settlement'] = $request->has('enable_auto_settlement');
    $data['enable_sales_settlement'] = $request->has('enable_sales_settlement');

    // تنظيف البيانات
    if (empty($data['active_payment_method_ids'])) {
        $data['active_payment_method_ids'] = null;
        $data['default_payment_method_id'] = null; // إذا لم تكن هناك طرق مفعلة، فلا يمكن أن تكون هناك افتراضية
    }

    if ($data['allowed_categories_type'] === 'all') {
        $data['allowed_categories_ids'] = null;
    }

    // حفظ أو تحديث الإعدادات
    $settings = PosGeneralSetting::first();
    
    if ($settings) {
        $settings->update($data);
    } else {
        PosGeneralSetting::create($data);
    }

    return redirect()->back()->with('success', 'تم حفظ الإعدادات بنجاح');
}

/**
 * الحصول على طرق الدفع المفعلة للـ AJAX
 */
public function getActivePaymentMethods(Request $request)
{
    $activeIds = $request->get('active_ids', []);
    
    if (empty($activeIds)) {
        return response()->json([]);
    }

    $paymentMethods = PaymentMethod::whereIn('id', $activeIds)
        ->where('status', 'active')
        ->select('id', 'name')
        ->get();

    return response()->json($paymentMethods);
}


    public function getCategoriesByType(Request $request)
    {
        $type = $request->get('type', 'all');
        $selectedIds = $request->get('selected_ids', []);
        
        if ($type === 'all') {
            $categories = Category::select('id', 'name')->get();
        } elseif ($type === 'only' && !empty($selectedIds)) {
            $categories = Category::select('id', 'name')
                ->whereIn('id', $selectedIds)
                ->get();
        } elseif ($type === 'except' && !empty($selectedIds)) {
            $categories = Category::select('id', 'name')
                ->whereNotIn('id', $selectedIds)
                ->get();
        } else {
            $categories = collect();
        }
        
        return response()->json($categories);
    }
    public function Shift()
    {
        return view('pos.settings.shift.index');
    }
    public function Create()
    {
        return view('pos.settings.shift.create');
    }

}

