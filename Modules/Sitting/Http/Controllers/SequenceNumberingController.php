<?php

namespace App\Http\Controllers\Sitting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SerialSetting;
use Illuminate\Support\Facades\DB;

class SequenceNumberingController extends Controller
{
    /**
     * عرض صفحة إعدادات الترقيم المتسلسل
     */
    public function index($section = 'invoice') // Default value added
    {
        // جلب الإعدادات الحالية للقسم
        $serialSetting = SerialSetting::where('section', $section)->first();

        // إذا لم يتم العثور على إعدادات، نستخدم القيم الافتراضية
        $currentNumber = $serialSetting ? $serialSetting->current_number : 0;
        $numberOfDigits = $serialSetting ? $serialSetting->number_of_digits : 5;
        $prefix = $serialSetting ? $serialSetting->prefix : '';
        $mode = $serialSetting ? $serialSetting->mode : 0;

        return view('sitting::sequence_numbering.index', [
            'currentNumber' => $currentNumber,
            'numberOfDigits' => $numberOfDigits,
            'prefix' => $prefix,
            'mode' => $mode,
            'section' => $section,
        ]);
    }
    // في Controller الخاص بك

    // public function getCurrentNumber($section)
    // {
    //     // استرجاع الرقم الحالي من الجدول المرتبط بالقسم
    //     $currentNumber = DB::table($section)->orderBy('id', 'desc')->value('next_number');

    //     return response()->json(['current_number' => $currentNumber]);
    // }

    public function getCurrentNumber($section)
{
    // هنا يمكنك جلب الرقم الحالي من قاعدة البيانات بناءً على القسم
    $currentNumber = $this->getCurrentNumberFromDatabase($section);

    return view('sitting::sequence_numbering.index', [
        'currentNumber' => $currentNumber,
        'section' => $section,
    ]);
}

private function getCurrentNumberFromDatabase($section)
{
    // البحث عن إعدادات التسلسل للقسم المحدد
    $serialSetting = SerialSetting::where('section', $section)->first();

    // إذا تم العثور على إعدادات، نستخدم الرقم الحالي
    if ($serialSetting) {
        return $serialSetting->current_number;
    }

    // إذا لم يتم العثور على إعدادات، نستخدم آخر id من الجدول المقابل للقسم
    $tables = [
        'invoice' => 'invoices',
        'customer' => 'clients',
        'quotation' => 'quotes',
        'return-invoice' => 'invoices',
        'credit-note' => 'credit_notifications',
        'reservation' => 'reservations',
        'purchase-invoice' => 'purchase_invoices',
        'purchase-return' => 'purchase_invoices',
        'supply-order' => 'supply_orders',
         'supplier' => 'suppliers',
         'entry' => '	journal_entries',
        'expense' => 'expenses',
        'receipt-voucher' => 'receipt_vouchers',
        'warehouse-add' => 'warehouse_permits',
        'warehouse-dispose' => 'warehouse_permits_products',
        // 'transfer-request' => 'transfer_requests',
        'branch' => 'branches',
        // 'inventory-report' => 'inventory_reports',
        // 'products' => 'products',
        // 'contracts' => 'contracts',
        // 'quotation-request' => 'quotation_requests',
        // 'purchase-quotation' => 'purchase_quotations',
        // 'purchase-order' => 'purchase_orders',
        // 'origin' => 'origins',
        // 'invoice-payment' => 'invoice_payments',
        // 'payment-return' => 'payment_returns',
        // // 'purchase-refund' => 'purchase_refunds',
        // 'sales-debit' => 'sales_debits',
        // 'products-custom' => 'products_custom',
        // 'purchase-refund-payment' => 'purchase_refund_payments',
        // 'sales-debit-notes' => 'sales_debit_notes',
        // 'purchase-credit-notes' => 'purchase_credit_notes',
        // 'production-routes' => 'production_routes',
        // 'workstations' => 'workstations',
        // 'production-material-lists' => 'production_material_lists',
        // 'manufacturing-orders' => 'manufacturing_orders',
        // 'production-plan' => 'production_plans',
    ];

    // التحقق من وجود القسم في المصفوفة
    if (!array_key_exists($section, $tables)) {
        return 1; // قيمة افتراضية إذا لم يتم العثور على القسم
    }

    // الحصول على اسم الجدول
    $table = $tables[$section];

    // جلب آخر id من الجدول
    $lastId = DB::table($table)->orderBy('id', 'desc')->value('id');

    // إذا كان الجدول فارغًا، نرجع 1
    return $lastId ? $lastId : 1;
}

public function store(Request $request)
{
    // التحقق من صحة البيانات المرسلة من النموذج
    $validatedData = $request->validate([
        'section' => 'required|string|max:50', // القسم مطلوب ويجب أن يكون نصًا ولا يتجاوز 50 حرفًا
        'current_number' => 'nullable|integer|min:0|max:9999999', // الرقم الحالي (اختياري)
        'number_of_digits' => 'nullable|integer|min:1|max:10', // عدد الأرقام (اختياري)
        'prefix' => 'nullable|string|max:10', // البادئة (اختياري)
        'mode' => 'nullable|integer|between:0,6', // النمط (اختياري)
    ]);

    try {
        // إنشاء سجل جديد في قاعدة البيانات باستخدام البيانات المصدق عليها
        $serialSetting = SerialSetting::create([
            'section' => $validatedData['section'],
            'current_number' => $validatedData['current_number'] ?? 0, // إذا لم يتم إدخال قيمة، نستخدم 0 كقيمة افتراضية
            'number_of_digits' => $validatedData['number_of_digits'] ?? 5, // إذا لم يتم إدخال قيمة، نستخدم 5 كقيمة افتراضية
            'prefix' => $validatedData['prefix'] ?? '', // إذا لم يتم إدخال قيمة، نستخدم سلسلة فارغة كقيمة افتراضية
            'mode' => $validatedData['mode'] ?? 0, // إذا لم يتم إدخال قيمة، نستخدم 0 كقيمة افتراضية
        ]);

        // إعادة توجيه المستخدم مع رسالة نجاح
        return redirect()->back()->with('success', 'تم إنشاء الإعدادات بنجاح!');
    } catch (\Exception $e) {
        // في حالة حدوث خطأ، إعادة توجيه المستخدم مع رسالة خطأ
        return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الإعدادات: ' . $e->getMessage());
    }
}
    /**
     * تحديث إعدادات الترقيم
     */

     public function update(Request $request)
{
    // التحقق من صحة البيانات
    $validatedData = $request->validate([
        'section' => 'required|string|max:50', // القسم مطلوب
        'current_number' => 'nullable|integer|min:0|max:9999999',
        'number_of_digits' => 'nullable|integer|min:1|max:10',
        'prefix' => 'nullable|string|max:10',
        'mode' => 'nullable|integer|between:0,6',
    ]);

    try {
        // البحث عن السجل في قاعدة البيانات
        $serialSetting = SerialSetting::where('section', $validatedData['section'])->first();

        // إذا لم يتم العثور على السجل، إرجاع رسالة خطأ
        if (!$serialSetting) {
            return redirect()->back()->with('error', 'لم يتم العثور على الإعدادات لهذا القسم!');
        }

        // تحديث القيم فقط إذا كان السجل موجودًا
        $serialSetting->update([
            'current_number' => $validatedData['current_number'] ?? $serialSetting->current_number,
            'number_of_digits' => $validatedData['number_of_digits'] ?? $serialSetting->number_of_digits,
            'prefix' => $validatedData['prefix'] ?? $serialSetting->prefix,
            'mode' => $validatedData['mode'] ?? $serialSetting->mode,
        ]);

        return redirect()->back()->with('success', 'تم تحديث الإعدادات بنجاح!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
    }
}


}
