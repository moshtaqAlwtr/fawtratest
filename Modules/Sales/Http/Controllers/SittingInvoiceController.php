<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccountSetting;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\TaxInvoice;
use App\Models\Template;
use App\Models\User;
use Illuminate\Http\Request;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Blade;
class SittingInvoiceController extends Controller
{
    public function index()
    {
        return view('sales::sitting.index');
    }

    public function invoice()
    {

        return view('sales::sitting.invoice');
    }
    public function bill_designs()
{
    $templates = Template::where('type', 'invoice')->get();
    return view('templates.index', compact('templates'));
}
public function test_print(Request $request)
    {
        // بدء بناء الاستعلام
        if (auth()->user()->hasAnyPermission(['sales_view_all_invoices'])) {
            // عنده صلاحية، يشوف كل الفواتير
            $invoices = Invoice::with(['client', 'createdByUser', 'updatedByUser'])
                        ->where('type','normal')->orderBy('created_at', 'desc');
        } else {
            // ما عنده صلاحية، يشوف فقط فواتيره
            $invoices = Invoice::with(['client', 'createdByUser', 'updatedByUser'])
                        ->where('created_by', auth()->user()->id)
                        ->where('type','normal')->orderBy('created_at', 'desc');
        }

        // 1. البحث حسب العميل
        if ($request->has('client_id') && $request->client_id) {
            $invoices->where('client_id', $request->client_id);
        }

        // 2. البحث حسب رقم الفاتورة
        if ($request->has('invoice_number') && $request->invoice_number) {
            $invoices->where('id', $request->invoice_number);
        }

        // 3. البحث حسب حالة الفاتورة
        if ($request->has('status') && $request->status) {
            $invoices->where('payment_status', $request->status);
        }

        // 4. البحث حسب البند
        if ($request->has('item') && $request->item) {
            $invoices->whereHas('items', function ($query) use ($request) {
                $query->where('item', 'like', '%' . $request->item . '%');
            });
        }

        // 5. البحث حسب العملة
        if ($request->has('currency') && $request->currency) {
            $invoices->where('currency', $request->currency);
        }

        // 6. البحث حسب الإجمالي (من)
        if ($request->has('total_from') && $request->total_from) {
            $invoices->where('grand_total', '>=', $request->total_from);
        }

        // 7. البحث حسب الإجمالي (إلى)
        if ($request->has('total_to') && $request->total_to) {
            $invoices->where('grand_total', '<=', $request->total_to);
        }

        // 8. البحث حسب حالة الدفع
        if ($request->has('payment_status') && $request->payment_status) {
            $invoices->where('payment_status', $request->payment_status);
        }

        // 9. البحث حسب التخصيص (شهريًا، أسبوعيًا، يوميًا)
        if ($request->has('custom_period') && $request->custom_period) {
            if ($request->custom_period == 'monthly') {
                $invoices->whereMonth('created_at', now()->month);
            } elseif ($request->custom_period == 'weekly') {
                $invoices->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($request->custom_period == 'daily') {
                $invoices->whereDate('created_at', now()->toDateString());
            }
        }

        // 10. البحث حسب التاريخ (من)
        if ($request->has('from_date') && $request->from_date) {
            $invoices->whereDate('created_at', '>=', $request->from_date);
        }

        // 11. البحث حسب التاريخ (إلى)
        if ($request->has('to_date') && $request->to_date) {
            $invoices->whereDate('created_at', '<=', $request->to_date);
        }

        // 12. البحث حسب تاريخ الاستحقاق (من)
        if ($request->has('due_date_from') && $request->due_date_from) {
            $invoices->whereDate('due_date', '>=', $request->due_date_from);
        }

        // 13. البحث حسب تاريخ الاستحقاق (إلى)
        if ($request->has('due_date_to') && $request->due_date_to) {
            $invoices->whereDate('due_date', '<=', $request->due_date_to);
        }

        // 14. البحث حسب المصدر
        if ($request->has('source') && $request->source) {
            $invoices->where('source', $request->source);
        }

        // 15. البحث حسب الحقل المخصص
        if ($request->has('custom_field') && $request->custom_field) {
            $invoices->where('custom_field', 'like', '%' . $request->custom_field . '%');
        }

        // 16. البحث حسب تاريخ الإنشاء (من)
        if ($request->has('created_at_from') && $request->created_at_from) {
            $invoices->whereDate('created_at', '>=', $request->created_at_from);
        }

        // 17. البحث حسب تاريخ الإنشاء (إلى)
        if ($request->has('created_at_to') && $request->created_at_to) {
            $invoices->whereDate('created_at', '<=', $request->created_at_to);
        }

        // 18. البحث حسب حالة التسليم
        if ($request->has('delivery_status') && $request->delivery_status) {
            $invoices->where('delivery_status', $request->delivery_status);
        }

        // 19. البحث حسب "أضيفت بواسطة" (الموظفين)
        if ($request->has('added_by_employee') && $request->added_by_employee) {
            $invoices->where('created_by', $request->added_by_employee);
        }

        // 20. البحث حسب مسؤول المبيعات (المستخدمين)
        if ($request->has('sales_person_user') && $request->sales_person_user) {
            $invoices->where('created_by', $request->sales_person_user);
        }

        // 21. البحث حسب Post Shift
        if ($request->has('post_shift') && $request->post_shift) {
            $invoices->where('post_shift', 'like', '%' . $request->post_shift . '%');
        }

        // 22. البحث حسب خيارات الشحن
        if ($request->has('shipping_option') && $request->shipping_option) {
            $invoices->where('shipping_option', $request->shipping_option);
        }

        // 23. البحث حسب مصدر الطلب
        if ($request->has('order_source') && $request->order_source) {
            $invoices->where('order_source', $request->order_source);
        }

        // جلب النتائج مع التقسيم (Pagination)
        $invoices = $invoices->get();

        // البيانات الأخرى المطلوبة للواجهة
        $clients = Client::all();
        $users = User::all();
        $employees = Employee::all();
        $invoice_number = $this->generateInvoiceNumber();

        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        $client = Client::where('user_id', auth()->user()->id)->first();

        return view('templates.test_index', compact('invoices', 'account_setting', 'client', 'clients', 'users', 'invoice_number', 'employees'));
    }
    private function generateInvoiceNumber()
    {
        $lastInvoice = Invoice::latest()->first();
        $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
        return str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
    public function print($id)
    {
        $clients = Client::all();
        $employees = Employee::all();
        $invoice = Invoice::find($id);
        // $qrCodeSvg = QrCode::encoding('UTF-8')->size(150)->generate($invoice->qrcode);
        $renderer = new ImageRenderer(
            new RendererStyle(150), // تحديد الحجم
            new SvgImageBackEnd(), // تحديد نوع الصورة (SVG)
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($invoice->qrcode);
        $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'invoice')->get();
        $account_setting = null;

if (auth()->check()) {
    $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
}
$client =  null;
if (auth()->check()) {
        $client = Client::where('user_id', auth()->user()->id)->first();
}
        $invoice_number = $this->generateInvoiceNumber();
        $template = Template::where('type', 'invoice')->firstOrFail();
        // إنشاء رقم الباركود من رقم الفاتورة
        $barcodeNumber = str_pad($invoice->id, 13, '0', STR_PAD_LEFT); // تنسيق الرقم إلى 13 خانة

        // إنشاء رابط الباركود باستخدام خدمة Barcode Generator
        $barcodeImage = 'https://barcodeapi.org/api/128/' . $barcodeNumber;
// إعداد البيانات للقالب
$data = [
    'invoice' => $invoice,
    'TaxsInvoice' => $TaxsInvoice,
    'qrCodeSvg' => $qrCodeSvg,
    'barcodeImage' => $barcodeImage,
    'account_setting' => AccountSetting::where('user_id', auth()->id())->first(),
    'client' => Client::where('user_id', auth()->id())->first(),
    'today' => now()->format('Y-m-d')
];
$decodedContent = html_entity_decode($template->content);
// عرض الفاتورة باستخدام القالب
try {
    $html = Blade::render($decodedContent, $data);
} catch (\Throwable $e) {
    dd($e->getMessage(), $decodedContent, $data);
}

// $html = Blade::render($decodedContent, $data);

return view('templates.test_print', ['html' => $html]);
        // تغيير اسم المتغير من qrCodeImage إلى barcodeImage
        return view('templates.test_print', compact('invoice_number', 'account_setting', 'client', 'clients', 'employees', 'invoice', 'barcodeImage', 'TaxsInvoice', 'qrCodeSvg'));
    }
public function preview(Request $request)
{
    try {
        $content = $request->input('content');

        // تنظيف المحتوى من أي أكواد غير مرغوب فيها
        $content = $this->sanitizeTemplateContent($content);

        $dummyData = $this->generateDummyData();

        // استبدال &lt; و &gt; بالرموز الحقيقية قبل التصيير
        $content = htmlspecialchars_decode($content);

        // معالجة القالب يدويًا قبل تمريره لـ Blade
        $processedContent = $this->preprocessTemplate($content);

        $html = Blade::render($processedContent, $dummyData);

        return response()->json(['html' => $html]);

    } catch (\Throwable $e) {
        return response()->json([
            'error' => 'خطأ في المعاينة: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
}

private function sanitizeTemplateContent($content)
{
    // إزالة أي أكواد PHP خطيرة
    $content = preg_replace('/<\?php.*?\?>/s', '', $content);

    // استبدال HTML entities
    $content = str_replace(
        ['&lt;?', '?&gt;', '&amp;'],
        ['<?', '?>', '&'],
        $content
    );

    return $content;
}

private function preprocessTemplate($content)
{
    // استبدال المتغيرات المعقدة بعلامات مؤقتة
    $replacements = [
        '/@php(.*?)@endphp/s' => '<?php $1 ?>',
        '/\{\!!(.*?)!!\}/' => '<?php echo $1 ?>'
    ];

    return preg_replace(
        array_keys($replacements),
        array_values($replacements),
        $content
    );
}

private function generateDummyData()
{
    return [
        'invoice' => (object)[
            'id' => rand(1000, 9999),
            'invoice_date' => now(),
            'discount_amount' => 50,
            'advance_payment' => 100,
            'due_value' => 450,
            'shipping_cost' => 30,
            'grand_total' => 500,
            'payment_status' => 0,
            'returned_payment' => 0,
            'items' => collect([
                (object)['item' => 'منتج تجريبي 1', 'quantity' => 2, 'unit_price' => 100, 'discount' => 10, 'total' => 190],
                (object)['item' => 'منتج تجريبي 2', 'quantity' => 1, 'unit_price' => 200, 'discount' => 0, 'total' => 200]
            ]),
            'client' => (object)[
                'trade_name' => 'شركة تجريبية',
                'first_name' => 'محمد',
                'last_name' => 'علي',
                'street1' => 'شارع الملك فهد',
                'street2' => 'الرياض',
                'tax_number' => '1234567890',
                'phone' => '0501234567',
                'code' => 'CL001',
                'mobile' => '0501234567'
            ]
        ],
        'account_setting' => (object)[
            'currency' => 'SAR',
            'user_id' => auth()->id()
        ],
        'qrCodeSvg' => '<svg width="100" height="100"><rect width="100" height="100" fill="#000"/></svg>',
        'TaxsInvoice' => collect([])
    ];
}

public function edit(Template $template)
{
    return view('templates.edit', compact('template'));
}

public function update(Request $request, Template $template)
{
    $request->validate(['content' => 'required']);

    $template->update(['content' => $request->content,'name'=> $request->name]);

    return redirect()->route('SittingInvoice.bill_designs')->with('success', 'تم تحديث القالب');
}

public function reset(Template $template)
{
    $template->update(['content' => $template->default_content]);

    return back()->with('success', 'تم استعادة القالب الافتراضي');
}

}
