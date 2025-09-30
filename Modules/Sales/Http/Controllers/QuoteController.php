<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\QuoteRequest;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Quote;
use App\Models\SerialSetting;
use App\Models\User;
use App\Models\TaxSitting;
use App\Models\TaxInvoice;
use App\Models\AccountSetting;
use Carbon\Carbon;
use BaconQrCode\Writer;
use TCPDF;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Barryvdh\DomPDF\Facade\Pdf;
use ArPHP\I18N\Arabic;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Auth;
use App\Mail\QuoteViewMail;
use App\Models\Tax;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index(Request $request)
{
    // بدء بناء الاستعلام الأساسي
    $query = Quote::with(['client', 'creator', 'items']);

    // تطبيق جميع شروط البحث
    $this->applySearchFilters($query, $request);

    // التحقق من كون الطلب AJAX
    if ($request->ajax()) {
        $quotes = $query->orderBy('created_at', 'desc')->paginate(15);
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

        return response()->json([
            'success' => true,
            'data' => view('sales::qoution.partials.table', compact('quotes', 'account_setting'))->render(),
            'current_page' => $quotes->currentPage(),
            'last_page' => $quotes->lastPage(),
            'total' => $quotes->total(),
            'from' => $quotes->firstItem(),
            'to' => $quotes->lastItem()
        ]);
    }

    // جلب البيانات الأخرى المطلوبة للواجهة
    $quotes_number = $this->generateInvoiceNumber();
    $clients = Client::all();
    $users = User::all();
    $employees = User::whereIn('role', ['employee', 'manager'])->get();
    $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

    return view('sales::qoution.index', compact(
        'account_setting',
        'quotes_number',
        'clients',
        'users',
        'employees'
    ));
}

protected function applySearchFilters($query, $request)
{
    // البحث حسب العميل
    if ($request->filled('client_id')) {
        $query->where('client_id', $request->client_id);
    }

    // البحث حسب رقم عرض السعر
    if ($request->filled('id')) {
        $query->where('id', 'LIKE', '%' . $request->id . '%');
    }

    // البحث حسب الحالة
    if ($request->filled('status')) {
        $query->where('status', intval($request->status));
    }

    // البحث حسب المبلغ الإجمالي (من)
    if ($request->filled('total_from')) {
        $query->where('grand_total', '>=', $request->total_from);
    }

    // البحث حسب المبلغ الإجمالي (إلى)
    if ($request->filled('total_to')) {
        $query->where('grand_total', '<=', $request->total_to);
    }

    // البحث حسب التاريخ
    if ($request->filled('from_date_1') && $request->filled('to_date_1')) {
        $from = Carbon::parse($request->from_date_1)->startOfDay();
        $to = Carbon::parse($request->to_date_1)->endOfDay();
        $query->whereBetween('created_at', [$from, $to]);
    }

    // البحث في البنود
    if ($request->filled('item_search')) {
        $query->whereHas('items', function ($q) use ($request) {
            $q->where('item', 'LIKE', '%' . $request->item_search . '%')
              ->orWhere('description', 'LIKE', '%' . $request->item_search . '%');
        });
    }

    // البحث حسب من أضاف عرض السعر
    if ($request->filled('created_by')) {
        $query->where('created_by', $request->created_by);
    }
}
    private function generateInvoiceNumber()
    {
        $lastQuote = Quote::latest()->first();
        return $lastQuote ? $lastQuote->id + 1 : 1;
    }

public function sendQuoteLink($id)
{
    $quote = Quote::with('client')->findOrFail($id);

    if (!$quote->client || !$quote->client->email || !filter_var($quote->client->email, FILTER_VALIDATE_EMAIL)) {
        return redirect()->back()->with('error', 'هذا العميل لا يملك  بريد إلكتروني صالح.');
    }

    // إنشاء الرابط بناءً على اسم الـ Route
    $viewUrl = route('questions.print', $quote->id);

    Mail::to($quote->client->email)->send(new QuoteViewMail($quote, $viewUrl));

    return redirect()->back()->with('success', 'تم إرسال رابط عرض السعر إلى بريد العميل.');
}
    public function create()
    {
        $quotes_number = $this->generateInvoiceNumber();
        $items = Product::all();
        $clients = Client::all();
        $users = User::all();
        $taxs = TaxSitting::all();
          $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        return view('sales::qoution.create', compact('clients','taxs', 'users','account_setting', 'items', 'quotes_number'));
    }

    public function store(Request $request)
    {
        // التحقق من صحة البيانات باستخدام helper function
        $validated = validator($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'quote_date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.discount_type' => 'nullable|in:amount,percentage',
            'items.*.tax_1' => 'nullable|numeric|min:0',
            'items.*.tax_2' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:amount,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_type' => 'required|in:1,2,3', // 1=vat, 2=zero, 3=exempt
            'tax_rate' => 'nullable|numeric|min:0', // نسبة الضريبة التي يدخلها المستخدم
            'notes' => 'nullable|string',
        ])->validate();

        // بدء العملية داخل ترانزاكشن
        DB::beginTransaction();

        try {
            // ** الخطوة الأولى: إنشاء كود للعرض باستخدام الرقم التسلسلي الحالي **
            $serialSetting = SerialSetting::where('section', 'quotation')->first(); // الحصول على الرقم التسلسلي الحالي
            $currentNumber = $serialSetting ? $serialSetting->current_number : 1; // إذا لم يتم العثور على إعدادات، نستخدم 1 كقيمة افتراضية

            // التحقق من أن الرقم فريد
            while (Quote::where('id', $currentNumber)->exists()) {
                $currentNumber++;
            }

            // تعيين الرقم التسلسلي
            $quotes_number = $currentNumber;

            // زيادة الرقم التسلسلي في جدول serial_settings
            if ($serialSetting) {
                $serialSetting->update(['current_number' => $currentNumber + 1]);
            } else {
                // إذا لم يتم العثور على إعدادات، يتم إنشاء سجل جديد
                SerialSetting::create([
                    'section' => 'quotation',
                    'current_number' => $currentNumber + 1,
                ]);
            }

            // ** تجهيز المتغيرات الرئيسية لحساب العرض **
            $total_amount = 0; // إجمالي المبلغ قبل الخصومات
            $total_discount = 0; // إجمالي الخصومات على البنود
            $items_data = []; // تجميع بيانات البنود

            // ** الخطوة الثانية: معالجة البنود (items) **
            foreach ($validated['items'] as $item) {
                // جلب المنتج
                $product = Product::findOrFail($item['product_id']);

                // حساب تفاصيل الكمية والأسعار
                $quantity = floatval($item['quantity']);
                $unit_price = floatval($item['unit_price']);
                $item_total = $quantity * $unit_price;

                // حساب الخصم للبند
                $item_discount = 0; // قيمة الخصم المبدئية
                if (isset($item['discount']) && $item['discount'] > 0) {
                    $discountType = $item['discount_type'] ?? 'amount';
                    if ($discountType === 'percentage') {
                        $item_discount = ($item_total * floatval($item['discount'])) / 100;
                    } else {
                        $item_discount = floatval($item['discount']);
                    }
                }

                // تحديث الإجماليات
                $total_amount += $item_total;
                $total_discount += $item_discount;

                // تجهيز بيانات البند
                $items_data[] = [
                    'quotation_id' => null, // سيتم تعيينه لاحقًا بعد إنشاء العرض
                    'product_id' => $item['product_id'],
                    'item' => $product->name,
                    'description' => $item['description'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'discount' => $item_discount,
                    'discount_type' => isset($item['discount_type']) && $item['discount_type'] === 'percentage' ? 2 : 1,
                    'tax_1' => floatval($item['tax_1'] ?? 0),
                    'tax_2' => floatval($item['tax_2'] ?? 0),
                    'total' => $item_total - $item_discount,
                ];
            }

            // ** الخطوة الثالثة: حساب الخصم الإضافي للعرض ككل **
            $quote_discount = floatval($validated['discount_amount'] ?? 0);
            $discountType = $validated['discount_type'] ?? 'amount';
            if ($discountType === 'percentage') {
                $quote_discount = ($total_amount * $quote_discount) / 100;
            }

            // الخصومات الإجمالية
            $final_total_discount = $total_discount + $quote_discount;

            // حساب المبلغ بعد الخصم
            $amount_after_discount = $total_amount - $final_total_discount;

            // ** حساب الضرائب بناءً على القيمة التي يدخلها المستخدم **
            $tax_total = 0;
            $tax_type = $validated['tax_type'];
            // if ($tax_type == 1) { // إذا كانت الضريبة مفعلة
            //     $tax_rate = $validated['tax_rate'] ?? 0; // نسبة الضريبة التي يدخلها المستخدم (افتراضيًا 0 إذا لم يتم تقديمها)
            //     $tax_total = ($amount_after_discount * $tax_rate) / 100; // حساب الضريبة
            // }

 foreach ($request->items as $item) {
                $tax_1 = floatval($item['tax_1'] ?? 0); // الضريبة الأولى
                $tax_2 = floatval($item['tax_2'] ?? 0); // الضريبة الثانية

                // حساب الضريبة لكل بند
                $item_total = floatval($item['quantity']) * floatval($item['unit_price']);
                $item_tax = ($item_total * $tax_1) / 100 + ($item_total * $tax_2) / 100;

                // إضافة الضريبة إلى الإجمالي
                $tax_total += $item_tax;
            }
            // ** إضافة تكلفة الشحن (إذا وجدت) **
            $shipping_cost = floatval($validated['shipping_cost'] ?? 0);

            // ** حساب ضريبة الشحن (إذا كانت الضريبة مفعلة) **
            $shipping_tax = 0;
            if ($tax_type == 1) { // إذا كانت الضريبة مفعلة
                $tax_rate = $validated['tax_rate'] ?? 0; // نسبة الضريبة التي يدخلها المستخدم (افتراضيًا 0 إذا لم يتم تقديمها)
                $shipping_tax = ($shipping_cost * $tax_rate) / 100; // ضريبة الشحن بناءً على نسبة الضريبة
            }

            // ** إضافة ضريبة الشحن إلى tax_total **
            $tax_total += $shipping_tax;

            // ** الحساب النهائي للمجموع الكلي **
            $total_with_tax = $amount_after_discount + $tax_total + $shipping_cost;

            // ** الخطوة الرابعة: إنشاء عرض السعر **
            $quote = Quote::create([
                'id' => $quotes_number, // استخدام الرقم التسلسلي كـ id
                'client_id' => $validated['client_id'],
                'quotes_number' => $quotes_number,
                'quote_date' => $validated['quote_date'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
                'discount_amount' => $quote_discount,
                'discount_type' => $discountType === 'percentage' ? 2 : 1,
                'shipping_cost' => $shipping_cost,
                'shipping_tax' => $shipping_tax,
                'tax_type' => $validated['tax_type'] ?? $quote->tax_type,
                'tax_rate' => $tax_type == 1 ? ($validated['tax_rate'] ?? 0) : null, // نحفظ نسبة الضريبة إذا كانت مفعلة
                'subtotal' => $total_amount,
                'total_discount' => $final_total_discount,
                'tax_total' => $tax_total,
                'grand_total' => $total_with_tax,
                'status' => 1, // حالة العرض (1: Draft)
            ]);




foreach ($request->items as $item) {
    // حساب الإجمالي لكل منتج (السعر × الكمية)
    $item_subtotal = $item['unit_price'] * $item['quantity'];

    // حساب الضرائب بناءً على البيانات القادمة من `request`
    $tax_ids = ['tax_1_id', 'tax_2_id'];
    foreach ($tax_ids as $tax_id) {
        if (!empty($item[$tax_id])) { // التحقق مما إذا كان هناك ضريبة
            $tax = TaxSitting::find($item[$tax_id]);

            if ($tax) {
                $tax_value = ($tax->tax / 100) * $item_subtotal; // حساب قيمة الضريبة

                // حفظ الضريبة في جدول TaxInvoice
                TaxInvoice::create([
                    'name' => $tax->name,
                    'invoice_id' => $quote->id,
                    'type' => $tax->type,
                    'rate' => $tax->tax,
                    'value' => $tax_value,
                    'type_invoice' => 'quote',
                ]);
            }
        }
    }
}

// ** بعد حفظ الضرائب، نقوم بحفظ المنتجات **
foreach ($items_data as $item) {
    $item['quotation_id'] = $quote->id;
    InvoiceItem::create($item);
}


            DB::commit();
            return redirect()->route('questions.index')->with('success', 'تم إنشاء عرض السعر بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('حدث خطأ في دالة store: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء حفظ عرض السعر: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $quote = Quote::with(['client', 'employee', 'items'])->findOrFail($id);
        $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'quote')->get();
          $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        return view('sales::qoution.show', compact('quote','TaxsInvoice','account_setting'));
    }

    public function print($id)
    {

        $quote = Quote::with(['client', 'employee', 'items'])->findOrFail($id);
        $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'quote')->get();
        $account_setting = null;

        if (auth()->check()) {
            $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        }
        $client =  null;
        if (auth()->check()) {
                $client = Client::where('user_id', auth()->user()->id)->first();
        }
        return view('sales::qoution.pdf', compact('quote','TaxsInvoice','account_setting'));
    }


// public function downloadPdf($id)
// {
//     $quote = Quote::with(['client', 'employee', 'items'])->findOrFail($id);
//     $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'quote')->get();
//     $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

//     // إعدادات PDF
//     $pdf = PDF::loadView('sales.qoution.pdf', compact('quote','TaxsInvoice','account_setting'));

//     // إعدادات إضافية للغة العربية
//     $pdf->setPaper('A4', 'portrait');
//     $pdf->setOption([
//         'isHtml5ParserEnabled' => true,
//         'isRemoteEnabled' => true,
//         'isPhpEnabled' => true,
//         'defaultFont' => 'dejavusans', // أو أي خط يدعم العربية
//         'fontDir' => storage_path('fonts/'), // مسار الخطوط
//         'fontCache' => storage_path('fonts/'),
//         'tempDir' => storage_path('temp/'),
//         'chroot' => realpath(base_path()),
//         'dpi' => 300,
//     ]);

//     return $pdf->download('quote_'.$quote->id.'.pdf');
// }
public function downloadPdf($id)
{


    $quote = Quote::with(['client', 'employee', 'items'])->find($id);
    $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'quote')->get();
    // إنشاء بيانات QR Code
    $qrData = 'رقم الفاتورة: ' . $quote->id . "\n";
    $qrData .= 'التاريخ: ' . $quote->created_at->format('Y/m/d') . "\n";
    $qrData .= 'العميل: ' . ($quote->client->trade_name ?? $quote->client->first_name . ' ' . $quote->client->last_name) . "\n";
    $qrData .= 'الإجمالي: ' . number_format($quote->grand_total, 2) . ' ر.س';

    // إنشاء QR Code
    $qrOptions = new \chillerlan\QRCode\QROptions([
        'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L,
        'scale' => 5,
        'imageBase64' => true,
    ]);
    // composer require chillerlan/php-qrcode

    $qrCode = new \chillerlan\QRCode\QRCode($qrOptions);
    $barcodeImage = $qrCode->render($qrData);

    // Create new PDF document
    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('Fawtra');
    $pdf->SetAuthor('Fawtra System');
    $pdf->SetTitle('عرض سعر رقم ' . $quote->code);

    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(0);

    // Disable header and footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Add new page
    $pdf->AddPage();

    // Set RTL direction
    $pdf->setRTL(true);

    // Set font
    $pdf->SetFont('aealarabiya', '', 14);

    // Pass QR code image to view
    // $barcodeImage = $qrCode->render($qrData);

    // Generate

    $renderer = new ImageRenderer(
        new RendererStyle(150), // تحديد الحجم
        new SvgImageBackEnd(), // تحديد نوع الصورة (SVG)
    );

    $writer = new Writer($renderer);
    $qrText = urlencode("
    رقم عرض السعر: {$quote->quotes_number}
    التاريخ: {$quote->quote_date}
    العميل: ".($quote->client->trade_name ?? $quote->client->first_name.' '.$quote->client->last_name)."
    الإجمالي: ".number_format($quote->grand_total, 2)." ر.س
");

// استخدام خدمة QR Code خارجية
$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=".$qrText;

    $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
    $html = view('sales.qoution.pdf', compact('quote','TaxsInvoice','account_setting','qrCodeUrl'))->render();

    // Add content to PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Output file
    // return $pdf->Output('invoice-' . $invoice->code . '.pdf', 'I');
    return $pdf->Output('quote-' . $quote->id . '.pdf', 'I');

}





    public function edit($id)
    {
        $quote = Quote::with(['client', 'employee', 'items'])->findOrFail($id);
        $items = Product::all();
$taxs=Tax::all();
        $clients = Client::all();
        $quotes_number = $this->generateInvoiceNumber();
        $users = User::all();

        return view('sales::qoution.edit', compact('quote' ,'taxs','clients', 'users', 'items', 'quotes_number'));
    }

    public function update(Request $request, $id)
    {
        // التحقق من صحة البيانات باستخدام helper function
        $validated = validator($request->all(), [
            'client_id' => 'nullable|exists:clients,id',
            'quote_date' => 'nullable|date_format:Y-m-d',
            'items' => 'nullable|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id', // إضافة التحقق من المستودع
            'items.*.quantity' => 'nullable|numeric|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.discount_type' => 'nullable|in:amount,percentage',
            'items.*.tax_1' => 'nullable|numeric|min:0',
            'items.*.tax_2' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:amount,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_type' => 'nullable|in:1,2,3', // 1=vat, 2=zero, 3=exempt
            'notes' => 'nullable|string',
        ])->validate();

        // تحويل tax_type إلى النص المناسب
        $tax_type_map = [
            '1' => 'vat',
            '2' => 'zero',
            '3' => 'exempt',
        ];

        // بدء العملية داخل ترانزاكشن
        DB::beginTransaction();

        try {
            // البحث عن العرض الموجود
            $quote = Quote::findOrFail($id);

            // ** تجهيز المتغيرات الرئيسية لحساب العرض **
            $total_amount = 0; // إجمالي المبلغ قبل الخصومات
            $total_discount = 0; // إجمالي الخصومات على البنود
            $items_data = []; // تجميع بيانات البنود

            // ** الخطوة الثانية: معالجة البنود (items) **
            if (isset($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    // جلب المنتج مع التحقق من وجوده
                    $product = Product::find($item['product_id']); // استخدم find بدلاً من findOrFail

                    // إذا لم يتم العثور على المنتج، نستمر إلى العنصر التالي
                    if (!$product) {
                        continue; // أو يمكنك إرجاع رسالة خطأ هنا
                    }

                    // حساب تفاصيل الكمية والأسعار
                    $quantity = floatval($item['quantity']);
                    $unit_price = floatval($item['unit_price']);
                    $item_total = $quantity * $unit_price;

                    // حساب الخصم للبند
                    $item_discount = 0; // قيمة الخصم المبدئية
                    if (isset($item['discount']) && $item['discount'] > 0) {
                        $discountType = $item['discount_type'] ?? 'amount';
                        if ($discountType === 'percentage') {
                            $item_discount = ($item_total * floatval($item['discount'])) / 100;
                        } else {
                            $item_discount = floatval($item['discount']);
                        }
                    }

                    // تحديث الإجماليات
                    $total_amount += $item_total;
                    $total_discount += $item_discount;

                    // تجهيز بيانات البند
                    $items_data[] = [
                        'quotation_id' => $quote->id, // سيتم تعيينه لاحقًا بعد إنشاء العرض
                        'product_id' => $item['product_id'],
                        'item' => $product->name,
                        'description' => $item['description'] ?? null,
                        'quantity' => $quantity,
                        'unit_price' => $unit_price,
                        'discount' => $item_discount,
                        'discount_type' => isset($item['discount_type']) && $item['discount_type'] === 'percentage' ? 2 : 1,
                        'tax_1' => floatval($item['tax_1'] ?? 0),
                        'tax_2' => floatval($item['tax_2'] ?? 0),
                        'total' => $item_total - $item_discount,
                    ];
                }
            }

            // ** الخطوة الثالثة: حساب الخصم الإضافي للعرض ككل **
            $quote_discount = floatval($validated['discount_amount'] ?? 0);
            $discountType = $validated['discount_type'] ?? 'amount';
            if ($discountType === 'percentage') {
                $quote_discount = ($total_amount * $quote_discount) / 100;
            }

            // الخصومات الإجمالية
            $final_total_discount = $total_discount + $quote_discount;

            // حساب المبلغ بعد الخصم
            $amount_after_discount = $total_amount - $final_total_discount;

            // ** حساب الضرائب **
            $tax_total = 0;
            $tax_type = $tax_type_map[$validated['tax_type'] ?? $quote->tax_type]; // استخدام القيمة القديمة إذا لم يتم تقديم قيمة جديدة
            if ($tax_type === 'vat') {
                // حساب الضريبة على المبلغ بعد الخصم
                $tax_total = $amount_after_discount * 0.15; // نسبة الضريبة 15%
            }

            // ** إضافة تكلفة الشحن (إذا وجدت) **
            $shipping_cost = floatval($validated['shipping_cost'] ?? $quote->shipping_cost); // استخدام القيمة القديمة إذا لم يتم تقديم قيمة جديدة

            // ** حساب ضريبة الشحن (إذا كانت الضريبة مفعلة) **
            $shipping_tax = 0;
            if ($tax_type === 'vat') {
                $shipping_tax = $shipping_cost * 0.15; // ضريبة الشحن 15%
            }

            // ** إضافة ضريبة الشحن إلى tax_total **
            $tax_total += $shipping_tax;

            // ** الحساب النهائي للمجموع الكلي **
            $total_with_tax = $amount_after_discount + $tax_total + $shipping_cost;

            // ** الخطوة الرابعة: تحديث العرض في قاعدة البيانات **
            $quote->update([
                'client_id' => $validated['client_id'] ?? $quote->client_id, // استخدام القيمة القديمة إذا لم يتم تقديم قيمة جديدة
                'quote_date' => $validated['quote_date'] ?? $quote->quote_date, // استخدام القيمة القديمة إذا لم يتم تقديم قيمة جديدة
                'notes' => $validated['notes'] ?? $quote->notes, // استخدام القيمة القديمة إذا لم يتم تقديم قيمة جديدة
                'discount_amount' => $quote_discount,
                'discount_type' => $discountType === 'percentage' ? 2 : 1,
                'shipping_cost' => $shipping_cost,
                'shipping_tax' => $shipping_tax,
                'tax_type' => $validated['tax_type'] ?? $quote->tax_type, // استخدام القيمة القديمة إذا لم يتم تقديم قيمة جديدة
                'subtotal' => $total_amount,
                'total_discount' => $final_total_discount,
                'tax_total' => $tax_total,
                'grand_total' => $total_with_tax,
                'status' => 1, // حالة العرض (1: Draft)
            ]);

            // ** الخطوة الخامسة: حذف البنود القديمة وإنشاء سجلات البنود (items) الجديدة للعرض **
            if (isset($validated['items'])) {
                $quote->items()->delete(); // حذف البنود القديمة
                foreach ($items_data as $item) {
                    $quote->items()->create($item);
                }
            }

            DB::commit();
            return redirect()->route('questions.index')->with('success', 'تم تحديث عرض السعر بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('حدث خطأ في دالة update: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء تحديث عرض السعر: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $quote = Quote::findOrFail($id);
        $quote->delete();
        return redirect()->route('questions.index')->with('success', 'تم حذف عرض السعر بنجاح');
    }

 public function convertToInvoice($id)
{
    try {
        // جلب عرض الأسعار مع العلاقات
        $quote = Quote::with(['client', 'items'])->findOrFail($id);

        // تحضير البيانات لدالة store في InvoicesController
        $invoiceData = [
            'client_id' => $quote->client_id,
            'invoice_date' => now()->format('Y-m-d'),
            'type' => 'normal',
            'notes' => $quote->notes,
            'discount_amount' => $quote->discount_amount ?? 0,
            'discount_type' => $quote->discount_type ?? 1,
            'shipping_cost' => $quote->shipping_cost ?? 0,
            'shipping_tax' => $quote->shipping_tax ?? 0,
            'tax_type' => $quote->tax_type ?? 1,
            'tax_rate' => $quote->tax_rate ?? 0,
            'payment_type' => 'credit',
            'payment_amount' => 0,
            'items' => []
        ];

        // تحويل عناصر عرض السعر إلى صيغة مناسبة لدالة store
        foreach ($quote->items as $item) {
            $invoiceData['items'][] = [
                'product_id' => $item->product_id,
                'item' => $item->item,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount ?? 0,
                'discount_type' => $item->discount_type ?? 1,
                'tax_1' => $item->tax_1 ?? 0,
                'tax_2' => $item->tax_2 ?? 0,
                'total' => $item->total,
                'store_house_id' => 1 // افتراضي - يمكن تعديله حسب الحاجة
            ];
        }

        // نسخ الضرائب من عرض السعر
        $quoteTaxes = TaxInvoice::where('invoice_id', $quote->id)
            ->where('type_invoice', 'quote')
            ->get();

        $invoiceData['taxes'] = [];
        foreach ($quoteTaxes as $tax) {
            $invoiceData['taxes'][] = [
                'name' => $tax->name,
                'type' => $tax->type,
                'rate' => $tax->rate,
                'value' => $tax->value
            ];
        }

        // إنشاء request object مؤقت لتمريره لدالة store
        $request = new \Illuminate\Http\Request();
        $request->merge($invoiceData);

        // استخدام Laravel's Service Container لإنشاء InvoicesController مع dependencies
        $invoicesController = app(\Modules\Sales\Http\Controllers\InvoicesController::class);

        // استدعاء دالة store
        $result = $invoicesController->store($request);

        // إذا تم إنشاء الفاتورة بنجاح، تحديث حالة عرض السعر
        if ($result instanceof \Illuminate\Http\RedirectResponse) {
            // تحديث حالة عرض الأسعار إلى "تم التحويل"
            $quote->update(['status' => 4]); // 4: تم التحويل إلى فاتورة

            // تعديل رسالة النجاح لتشير إلى التحويل
            return $result->with('success', 'تم تحويل عرض الأسعار إلى فاتورة بنجاح.');
        }

        return $result;

    } catch (\Exception $e) {
        Log::error('حدث خطأ أثناء تحويل عرض الأسعار إلى فاتورة: ' . $e->getMessage());
        return redirect()
            ->back()
            ->with('error', 'حدث خطأ أثناء التحويل: ' . $e->getMessage());
    }
}

// إضافة دالة generateTlvContent في QuoteController
private function generateTlvContent($timestamp, $totalAmount, $vatAmount)
{
    $tlvContent = $this->getTlv(1, 'مؤسسة اعمال خاصة للتجارة')
        . $this->getTlv(2, '000000000000000')
        . $this->getTlv(3, $timestamp)
        . $this->getTlv(4, number_format($totalAmount, 2, '.', ''))
        . $this->getTlv(5, number_format($vatAmount, 2, '.', ''));

    return base64_encode($tlvContent);
}

private function getTlv($tag, $value)
{
    $value = (string) $value;
    return pack('C', $tag) . pack('C', strlen($value)) . $value;
}
    public  function   logsaction(Request $request){
        return view('test');
    }

}
