<?php

namespace Modules\Purchases\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoiceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceSettingsController extends Controller
{

public function index()
{
    $settings = PurchaseInvoiceSetting::ordered()->get();
    return view('purchases::purchases.invoice_settings.create', compact('settings'));
}


    public function update(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'next_invoice_number' => 'required|integer|min:1',
            'settings' => 'sometimes|array',
            'settings.*' => 'string|exists:purchase_invoice_settings,setting_key'
        ], [
            'next_invoice_number.required' => 'رقم الفاتورة التالي مطلوب',
            'next_invoice_number.integer' => 'رقم الفاتورة يجب أن يكون رقماً صحيحاً',
            'next_invoice_number.min' => 'رقم الفاتورة يجب أن يكون أكبر من صفر',
            'settings.array' => 'الإعدادات يجب أن تكون مصفوفة',
            'settings.*.exists' => 'إعداد غير صحيح تم اختياره'
        ]);

        DB::beginTransaction();

        try {
            // حفظ رقم الفاتورة التالي

            // إعادة تعيين جميع الإعدادات إلى غير مفعل
            PurchaseInvoiceSetting::query()->update(['is_active' => false]);

            // تفعيل الإعدادات المحددة
            if ($request->has('settings') && is_array($request->settings)) {
                PurchaseInvoiceSetting::whereIn('setting_key', $request->settings)
                                    ->update(['is_active' => true]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'تم تحديث إعدادات فواتير الشراء بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء تحديث الإعدادات: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * تبديل حالة إعداد واحد (AJAX)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'setting_key' => 'required|string|exists:purchase_invoice_settings,setting_key'
        ]);

        try {
            $newStatus = PurchaseInvoiceSetting::toggleSetting($request->setting_key);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الإعداد بنجاح',
                'is_active' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الإعداد: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إعادة تعيين الإعدادات للحالة الافتراضية
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset()
    {
        DB::beginTransaction();

        try {
            // إعادة تعيين جميع الإعدادات
            PurchaseInvoiceSetting::query()->update(['is_active' => false]);

            // تفعيل الإعدادات الافتراضية
            $defaultActiveSettings = [
                'auto_payment',
                'default_received_invoices',
                'enable_settlement'
            ];

            PurchaseInvoiceSetting::whereIn('setting_key', $defaultActiveSettings)
                                 ->update(['is_active' => true]);


            DB::commit();

            return redirect()->back()->with('success', 'تم إعادة تعيين الإعدادات للحالة الافتراضية بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء إعادة تعيين الإعدادات: ' . $e->getMessage());
        }
    }

    /**
     * Helper method للتحقق من تفعيل إعداد معين
     *
     * @param string $settingKey
     * @return bool
     */
    public static function isEnabled($settingKey)
    {
        return PurchaseInvoiceSetting::isSettingActive($settingKey);
    }

    /**
     * Helper method للحصول على رقم الفاتورة التالي
     *
     * @return int
     */
    // public static function getNextInvoiceNumber()
    // {
    //     return PurchaseInvoiceGeneralSetting::getNextInvoiceNumber();
    // }

    /**
     * Helper method لتحديث رقم الفاتورة التالي
     *
     * @return int الرقم المستخدم
     */
    // public static function incrementInvoiceNumber()
    // {
    //     return PurchaseInvoiceGeneralSetting::incrementInvoiceNumber();
    // }

    /**
     * الحصول على جميع الإعدادات المفعلة
     *
     * @return array
     */
    public static function getActiveSettings()
    {
        return PurchaseInvoiceSetting::getAllActiveSettings();
    }

    /**
     * تصدير الإعدادات (JSON)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function export()
    {
        try {
            $settings = PurchaseInvoiceSetting::all();


            $export = [
                'settings' => $settings->toArray(),

                'exported_at' => now()->toDateTimeString()
            ];

            return response()->json($export);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'حدث خطأ أثناء تصدير الإعدادات: ' . $e->getMessage()
            ], 500);
        }
    }
}
