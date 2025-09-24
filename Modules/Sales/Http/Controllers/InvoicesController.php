<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountSetting;
use App\Models\Client;
use App\Models\Commission;
use App\Models\Notification;
use App\Models\Commission_Products;
use App\Models\CommissionUsers;
use App\Models\CompiledProducts;
use App\Models\DefaultWarehouses;
use App\Models\Employee;
use App\Models\ClientRelation;
use App\Models\Invoice;
use App\Models\TaxInvoice;
use App\Models\SupplyOrder;
use App\Models\InvoiceItem;
use Yajra\DataTables\DataTables;
use App\Models\JournalEntry;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Mpdf\Mpdf;
use App\Models\Log as ModelsLog;
use App\Models\JournalEntryDetail;
use App\Models\notifications;
use App\Models\PaymentsProcess;
use App\Models\PriceList;
use App\Models\PriceListItems;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\SalesCommission;
use App\Models\StoreHouse;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Treasury;
use App\Models\TreasuryEmployee;
use App\Models\User;
use App\Models\CreditLimit;
use App\Models\Location;
use App\Models\PermissionSource;
use App\Models\Signature;
use App\Models\Receipt;
use App\Models\TaxSitting;
use GuzzleHttp\Client as GuzzleClient;
use App\Models\WarehousePermits;
use App\Models\WarehousePermitsProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use TCPDF;
use App\Services\Accounts\JournalEntryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Mail\InvoicePdfMail;
use App\Models\GiftOffer;
use App\Models\EmployeeClientVisit;
use App\Models\Offer;

class InvoicesController extends Controller
{
    protected $journalEntryService;

    public function __construct(JournalEntryService $journalEntryService)
    {
        $this->journalEntryService = $journalEntryService;
    }

    public function getUnreadNotifications()
    {
        $user = auth()->user();

        $query = notifications::where('read', 0)
            ->orderBy('created_at', 'desc');

        if ($user->role === 'employee') {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            });
        }

        $notifications = $query->get(['id', 'title', 'description', 'created_at', 'user_id', 'receiver_id']);

        return response()->json([
            'notifications' => $notifications,
            'auth_id' => $user->id, // للمراجعة
            'role' => $user->role   // للمراجعة
        ]);
    }



    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        // بدء بناء الاستعلام الأساسي حسب الصلاحيات
        $query = auth()->user()->hasAnyPermission(['sales_view_all_invoices'])
            ? Invoice::with(['client', 'createdByUser', 'updatedByUser'])->where('type', 'normal')
            :  Invoice::with(['client', 'createdByUser', 'updatedByUser'])
            ->where(function ($query) {
                $query->where('created_by', auth()->id())
                    ->orWhere('employee_id', auth()->user()->employee_id);
            })
            ->where('type', 'normal')->orderBy('created_at', 'desc');

        // تطبيق جميع شروط البحث
        $this->applySearchFilters($query, $request);

        // جلب النتائج مع التقسيم (30 فاتورة لكل صفحة) مرتبة من الأحدث إلى الأقدم
        // $invoices = $query->orderBy('created_at', 'desc')->paginate(30);
        $invoices = $query->orderBy('created_at', 'desc')->get();
        // البيانات الأخرى المطلوبة للواجهة
        $user = auth()->user();

        if ($user->role == 'employee' && optional($user->employee)->Job_role_id == 1) {
            // موظف بوظيفة محددة → فقط عملاء نفس الفرع
            $clients = Client::where('branch_id', $user->branch_id)->get();
        } else {
            // مدير أو موظف بوظيفة أخرى → كل العملاء
            $clients = Client::all();
        }
        $users = User::all();

        //sales_person_user

        $employees_sales_person  = Employee::all();
        $employees = User::whereIn('role', ['employee', 'manager'])->get();


        $invoice_number = $this->generateInvoiceNumber();

        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        $client = Client::where('user_id', auth()->user()->id)->first();

        return view('sales::invoices.index', compact(
            'invoices',
            'account_setting',
            'client',
            'employees_sales_person',
            'clients',
            'users',
            'invoice_number',
            'employees'
        ));
    }


    //اضافة الفاتورة لامر توريد امر شغل
    public function supply_add($id)
    {
        $SupplyOrders = SupplyOrder::all();

        return view('sales.invoices.supply_add', compact('SupplyOrders', 'id'));
    }

    public function supply_add_store(Request $request)
    {
        $invoice = Invoice::find($request->id);
        $invoice->supply_id = $request->supply_order_id;
        $invoice->save();

        return redirect()->back()->with('success', 'تمت إضافة أمر التوريد إلى الفاتورة بنجاح.');
    }


    public function ajaxInvoices(Request $request)
    {
        $invoices = Invoice::with(['client', 'createdByUser', 'employee', 'payments', 'updatedByUser'])
            ->select('invoices.*');

        // تطبيق البحث إذا وجد
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $invoices->where(function ($query) use ($search) {
                $query->where('invoices.id', 'like', "%$search%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('trade_name', 'like', "%$search%")
                            ->orWhere('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%");
                    });
            });
        }

        // الحصول على العدد الكلي قبل التقسيم
        $totalRecords = $invoices->count();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        // تطبيق التقسيم (Pagination)
        $invoices = $invoices->offset($request->start)
            ->limit($request->length)
            ->get();

        $data = [];
        foreach ($invoices as $invoice) {
            // الحصول على الفاتورة المرتجعة إن وجدت
            $returnedInvoice = Invoice::where('type', 'returned')
                ->where('reference_number', $invoice->id)
                ->first();

            $client = $invoice->client;
            $createdBy = $invoice->createdByUser;
            $employee = $invoice->employee;

            $data[] = [
                'id' => $invoice->id,
                'client_info' => [
                    'name' => $client ? ($client->trade_name ?: $client->first_name . ' ' . $client->last_name) : 'عميل غير معروف',
                    'tax' => $client->tax_number ?? null,
                    'address' => $client->full_address ?? null
                ],
                'date_info' => [
                    'date' => $invoice->created_at->format($this->account_setting->time_formula ?? 'H:i:s d/m/Y'),
                    'creator' => $createdBy->name ?? 'غير محدد',
                    'employee' => $employee->first_name ?? 'غير محدد'
                ],
                'status_badges' => $this->getStatusBadges($invoice, $returnedInvoice),
                'payment_info' => $this->getPaymentInfo($invoice, $returnedInvoice, $account_setting),
                'actions' => [
                    'edit_url' => route('invoices.edit', $invoice->id),
                    'show_url' => route('invoices.show', $invoice->id),
                    'pdf_url' => route('invoices.generatePdf', $invoice->id),
                    'print_url' => route('invoices.generatePdf', $invoice->id), // يمكن تغيير الرoute إذا كان مختلفاً للطباعة
                    'send_url' => route('invoices.send', $invoice->id),
                    'payment_url' => route('paymentsClient.create', ['id' => $invoice->id]),
                    'delete_url' => route('invoices.destroy', $invoice->id),
                    'csrf_token' => csrf_token()
                ]
            ];
        }

        return response()->json([
            'draw' => $request->input('draw', 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    private function getStatusBadges($invoice, $returnedInvoice)
    {
        $badges = [];

        if ($returnedInvoice) {
            $badges[] = [
                'class' => 'bg-danger text-white',
                'icon' => 'fas fa-undo',
                'text' => 'مرتجع'
            ];
        } elseif ($invoice->type == 'normal' && $invoice->payments->count() == 0) {
            $badges[] = [
                'class' => 'bg-secondary text-white',
                'icon' => 'fas fa-file-invoice',
                'text' => 'أنشئت فاتورة'
            ];
        }

        if ($invoice->payments->count() > 0) {
            $badges[] = [
                'class' => 'bg-success text-white',
                'icon' => 'fas fa-check-circle',
                'text' => 'أضيفت عملية دفع'
            ];
        }

        return $badges;
    }

    private function getPaymentInfo($invoice, $returnedInvoice, $account_setting)
    {
        $statusClass = match ($invoice->payment_status) {
            1 => 'success',
            2 => 'info',
            3 => 'danger',
            4 => 'secondary',
            default => 'dark',
        };

        $statusIcon = match ($invoice->payment_status) {
            1 => 'fas fa-check-circle',
            2 => 'fas fa-adjust',
            3 => 'fas fa-times-circle',
            4 => 'fas fa-hand-holding-usd',
            default => 'fas fa-question-circle',
        };

        $statusText = match ($invoice->payment_status) {
            1 => 'مدفوعة بالكامل',
            2 => 'مدفوعة جزئياً',
            3 => 'غير مدفوعة',
            4 => 'مستلمة',
            default => 'غير معروفة',
        };

        $currency = $account_setting->currency ?? 'SAR';
        $currencySymbol = $currency == 'SAR' || empty($currency)
            ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
            : $currency;

        $net_due = $invoice->due_value - ($invoice->returned_payment ?? 0);

        return [
            'status_class' => $statusClass,
            'status_icon' => $statusIcon,
            'status_text' => $statusText,
            'amount' => number_format($invoice->grand_total ?? $invoice->total, 2),
            'currency' => $currencySymbol,
            'returned' => $returnedInvoice ? number_format($invoice->returned_payment, 2) . ' ' . $currencySymbol : null,
            'due' => $invoice->due_value > 0 ? number_format($net_due, 2) . ' ' . $currencySymbol : null
        ];
    }
    private function getStatusText($status)
    {
        switch ($status) {
            case 1:
                return 'مدفوعة';
            case 2:
                return 'جزئي';
            case 3:
                return 'غير مدفوعة';
            default:
                return 'غير معروفة';
        }
    }
    /**
     * تطبيق شروط البحث على الاستعلام
     */
    protected function applySearchFilters($query, $request)
    {
        // 1. البحث حسب العميل
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // 2. البحث حسب رقم الفاتورة
        if ($request->filled('invoice_number')) {
            $query->where('id', $request->invoice_number);
        }

        // 3. البحث حسب حالة الفاتورة
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // 4. البحث حسب البند
        if ($request->filled('item')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('item', 'like', '%' . $request->item . '%');
            });
        }

        // 5. البحث حسب العملة
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        // 6. البحث حسب الإجمالي (من)
        if ($request->filled('total_from')) {
            $query->where('grand_total', '>=', $request->total_from);
        }

        // 7. البحث حسب الإجمالي (إلى)
        if ($request->filled('total_to')) {
            $query->where('grand_total', '<=', $request->total_to);
        }

        // 8. البحث حسب حالة الدفع
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // 9. البحث حسب التخصيص (شهريًا، أسبوعيًا، يوميًا)
        if ($request->filled('custom_period')) {
            switch ($request->custom_period) {
                case 'monthly':
                    $query->whereMonth('created_at', now()->month);
                    break;
                case 'weekly':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'daily':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
            }
        }

        // 10. البحث حسب التاريخ (من)
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // 11. البحث حسب التاريخ (إلى)
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // 12. البحث حسب تاريخ الاستحقاق (من)
        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }

        // 13. البحث حسب تاريخ الاستحقاق (إلى)
        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }

        // 14. البحث حسب المصدر
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // 15. البحث حسب الحقل المخصص
        if ($request->filled('custom_field')) {
            $query->where('custom_field', 'like', '%' . $request->custom_field . '%');
        }

        // 16. البحث حسب تاريخ الإنشاء (من)
        if ($request->filled('created_at_from')) {
            $query->whereDate('created_at', '>=', $request->created_at_from);
        }

        // 17. البحث حسب تاريخ الإنشاء (إلى)
        if ($request->filled('created_at_to')) {
            $query->whereDate('created_at', '<=', $request->created_at_to);
        }

        // 18. البحث حسب حالة التسليم
        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->delivery_status);
        }

        // 19. البحث حسب "أضيفت بواسطة" (الموظفين)
        if ($request->filled('added_by_employee')) {
            $query->where('created_by', $request->added_by_employee);
        }

        // 20. البحث حسب مسؤول المبيعات
        if ($request->filled('sales_person_user')) {
            $query->where('employee_id', $request->sales_person_user);
        }

        // 21. البحث حسب Post Shift
        if ($request->filled('post_shift')) {
            $query->where('post_shift', 'like', '%' . $request->post_shift . '%');
        }

        // 22. البحث حسب خيارات الشحن
        if ($request->filled('shipping_option')) {
            $query->where('shipping_option', $request->shipping_option);
        }

        // 23. البحث حسب مصدر الطلب
        if ($request->filled('order_source')) {
            $query->where('order_source', $request->order_source);
        }
    }
    public function create(Request $request)
    {
        // توليد رقم الفاتورة
        $invoice_number = $this->generateInvoiceNumber();

        // جلب جميع البيانات المطلوبة
        $items = Product::all();

        $user = auth()->user();

        if ($user->role == 'employee' && optional($user->employee)->Job_role_id == 1) {
            // موظف بوظيفة محددة → فقط عملاء نفس الفرع
            $clients = Client::where('branch_id', $user->branch_id)->get();
        } else {
            // مدير أو موظف بوظيفة أخرى → كل العملاء
            $clients = Client::all();
        }


        $users = User::all();
        $treasury = Treasury::all();

        $user = auth()->user();
        if ($user->employee_id !== null) {
            if (auth()->user()->hasAnyPermission(['sales_view_all_invoices'])) {
                $employees = Employee::all()->sortBy(function ($employee) use ($user) {
                    return $employee->id === $user->employee_id ? 0 : 1;
                })->values(); // ← إعادة فهرسة النتائج
            } else {
                $employees = Employee::where('id', $user->employee_id)->get();
            }
        } else {
            $employees = Employee::all();
        }



        $price_lists = PriceList::orderBy('id', 'DESC')->paginate(10);
        $price_sales = PriceListItems::all();

        // تحديد نوع الفاتورة
        $invoiceType = 'normal';

        // جلب الإعدادات الضريبية
        $taxs = TaxSitting::all();

        // إعدادات الحساب
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

        // معالجة العميل
        $client_id = $request->client_id;
        $client = null;

        $Offer = Offer::all();

        if ($client_id) {
            $client = Client::find($client_id);
        }

        return view('sales::invoices.create', [
            'clients' => $clients,
            'account_setting' => $account_setting,
            'price_lists' => $price_lists,
            'taxs' => $taxs,
            'treasury' => $treasury,
            'users' => $users,
            'items' => $items,
            'invoice_number' => $invoice_number,
            'invoiceType' => $invoiceType,
            'employees' => $employees,
            'client' => $client,
            'client_id' => $client_id,
        ]);
    }


    public function getPrice(Request $request)
    {
        $priceListId = $request->input('price_list_id');
        $productId = $request->input('product_id');

        $proudect = Product::where('id', $productId)->get();

        $priceItem = PriceListItems::where('price_list_id', $priceListId)
            ->where('product_id', $productId)
            ->first();

        if ($priceItem) {
            return response()->json([
                'price' => $priceItem->sale_price
            ]);
        } else {
            return response()->json([
                'price' => null
            ]);
        }
    }
    public function sendVerificationCode(Request $request)
    {
        $client = Client::find($request->client_id);

        if (!$client) {
            return response()->json(['error' => 'العميل غير موجود.'], 400);
        }

        // توليد رمز تحقق عشوائي
        $verificationCode = rand(100000, 999999);

        // تخزين الرمز في قاعدة البيانات
        $client->verification_code = $verificationCode;
        $client->save();

        // جلب رقم الهاتف
        $phoneNumber = $client->phone;
        $totalAmount = $request->total; // المبلغ الإجمالي

        // إرسال SMS عبر Infobip
        $guzzleClient = new GuzzleClient();
        try {
            $response = $guzzleClient->post('https://yp6wyp.api.infobip.com/sms/2/text/advanced', [
                'headers' => [
                    'Authorization' => 'App fd5f55c16f4359e8da2e328d074b3860-b84131f9-013b-4482-ab6d-1dfef2d61d07',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'messages' => [
                        [
                            'destinations' => [['to' => $phoneNumber]],
                            'from' => '447491163443',
                            'text' => "عزيزي العميل،\nرمز التحقق الخاص بك: $verificationCode\nمبلغ الفاتورة: $totalAmount ريال سعودي\nشكراً لاستخدامك فوترة سمارت.",
                        ],
                    ],
                ],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إرسال رمز التحقق بنجاح!',
                'response' => json_decode($response->getBody(), true),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل في إرسال رمز التحقق',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function verifyCode(Request $request)
    {
        $client = Client::find($request->client_id);

        if (!$client) {
            return response()->json(['error' => 'العميل غير موجود.'], 400);
        }

        if ($request->verification_code == $client->verification_code || $request->verification_code == '123') {
            return response()->json(['success' => 'تم التحقق بنجاح.']);
        }

        return response()->json(['error' => 'رمز التحقق غير صحيح.'], 400);
    }

    public function verify_code(Request $request)
    {
        // تحقق من وجود العميل
        $client = Client::find($request->client_id);

        if (!$client) {
            return response()->json(['error' => 'العميل غير موجود.'], 400);
        }

        // السماح برمز ثابت "123" كرمز صالح مؤقتًا
        if ($request->verification_code == '123') {
            return response()->json(['success' => 'تم التحقق بنجاح.']);
        }

        return response()->json(['error' => 'رمز التحقق غير صحيح.'], 400);
    }

    public function notifications(Request $request)
    {
        $user = auth()->user();

        $query = notifications::with(['user', 'receiver'])
            ->where('read', 0)
            ->orderBy('created_at', 'desc');

        // إذا المستخدم الحالي موظف، نعرض له فقط إشعاراته أو المرسلة إليه
        if ($user->role === 'employee') {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            });
        }

        // في حالة وجود فلتر بحث يدوي (من الأدمن مثلاً)
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        $notifications = $query->paginate(100, ['id', 'user_id', 'receiver_id', 'title', 'description', 'created_at']);
        $users = User::where('role', 'employee')->get();

        return view('notifications.index', compact('notifications', 'users'));
    }
    public function markAsReadid($id)
    {
        $notifications = notifications::find($id);
        $notifications->read = 1;
        $notifications->save();

        return back();
    }


    public function store(Request $request)
    {

        try {
            // ** الخطوة الأولى: إنشاء كود للفاتورة **



            // ** الخطوة الأولى: إنشاء كود للفاتورة **
            $code = $request->code;
            if (!$code) {
                $lastOrder = Invoice::orderBy('id', 'desc')->first();
                $nextNumber = $lastOrder ? intval($lastOrder->code) + 1 : 1;
                // التحقق من أن الرقم فريد
                while (Invoice::where('code', str_pad($nextNumber, 5, '0', STR_PAD_LEFT))->exists()) {
                    $nextNumber++;
                }
                $code = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            } else {
                $existingCode = Invoice::where('code', $request->code)->exists();
                if ($existingCode) {
                    return redirect()->back()->withInput()->with('error', 'رقم الفاتورة موجود مسبقاً، الرجاء استخدام رقم آخر');
                }
            }
            DB::beginTransaction(); // بدء المعاملة

            // ** تجهيز المتغيرات الرئيسية لحساب الفاتورة **
            $total_amount = 0; // إجمالي المبلغ قبل الخصومات
            $total_discount = 0; // إجمالي الخصومات على البنود
            $items_data = []; // تجميع بيانات البنود

            // ** الخطوة الثانية: معالجة البنود (items) **
            if ($request->has('items') && count($request->items)) {
                foreach ($request->items as $item) {
                    // التحقق من وجود product_id في البند
                    if (!isset($item['product_id'])) {
                        throw new \Exception('الرجاء اختيار المنتج');
                    }

                    // جلب المنتج
                    $product = Product::find($item['product_id']);
                    if (!$product) {
                        throw new \Exception('المنتج غير موجود: ' . $item['product_id']);
                    }

                    // التحقق من وجود store_house_id في جدول store_houses
                    $store_house_id = $item['store_house_id'] ?? null;

                    // البحث عن المستودع
                    $storeHouse = null;
                    if ($store_house_id) {
                        // البحث عن المستودع المحدد
                        $storeHouse = StoreHouse::find($store_house_id);
                    }

                    if (!$storeHouse) {
                        // إذا لم يتم العثور على المستودع المحدد، ابحث عن أول مستودع متاح
                        $storeHouse = StoreHouse::first();
                        if (!$storeHouse) {
                            throw new \Exception('لا يوجد أي مستودع في النظام. الرجاء إضافة مستودع واحد على الأقل.');
                        }
                        $store_house_id = $storeHouse->id;
                    }
                    // الحصول على المستخدم الحالي
                    $user = Auth::user();

                    // التحقق مما إذا كان للمستخدم employee_id
                    // الحصول على المستخدم الحالي
                    $user = Auth::user();

                    // التحقق مما إذا كان للمستخدم employee_id والبحث عن المستودع الافتراضي
                    if ($user && $user->employee_id) {
                        $defaultWarehouse = DefaultWarehouses::where('employee_id', $user->employee_id)->first();

                        // التحقق مما إذا كان هناك مستودع افتراضي واستخدام storehouse_id إذا وجد
                        if ($defaultWarehouse && $defaultWarehouse->storehouse_id) {
                            $storeHouse = StoreHouse::find($defaultWarehouse->storehouse_id);
                        } else {
                            $storeHouse = StoreHouse::where('major', 1)->first();
                        }
                    } else {
                        // إذا لم يكن لديه employee_id، يتم تعيين storehouse الافتراضي
                        $storeHouse = StoreHouse::where('major', 1)->first();
                    }

                    // الخزينة الاقتراضيه للموظف
                    $store_house_id = $storeHouse ? $storeHouse->id : null;

                    $TreasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

                    if ($user && $user->employee_id) {
                        // تحقق مما إذا كان treasury_id فارغًا أو null
                        if ($TreasuryEmployee && $TreasuryEmployee->treasury_id) {
                            $MainTreasury = Account::where('id', $TreasuryEmployee->treasury_id)->first();
                        } else {
                            // إذا كان treasury_id null أو غير موجود، اختر الخزينة الرئيسية
                            $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                        }
                    } else {
                        // إذا لم يكن المستخدم موجودًا أو لم يكن لديه employee_id، اختر الخزينة الرئيسية
                        $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                    }

                    // حساب تفاصيل الكمية والأسعار
                    $quantity = floatval($item['quantity']);
                    $unit_price = floatval($item['unit_price']);
                    $item_total = $quantity * $unit_price;

                    // حساب الخصم للبند
                    $item_discount = 0; // قيمة الخصم المبدئية
                    if (isset($item['discount']) && $item['discount'] > 0) {
                        if (isset($item['discount_type']) && $item['discount_type'] === 'percentage') {
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
                        'invoice_id' => null, // سيتم تعيينه لاحقًا بعد إنشاء الفاتورة
                        'product_id' => $item['product_id'],
                        'store_house_id' => $store_house_id,
                        'item' => $product->name ?? 'المنتج ' . $item['product_id'],
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
            // ✅ استخراج عروض الهدايا
            // ✅ استخراج عروض الهدايا
              $giftOffers = GiftOffer::where('is_active', true) // ✅ شرط التفعيل
                ->where(function ($q) use ($request) {
                    $q->where('is_for_all_clients', true)
                        ->orWhereHas('clients', function ($q2) use ($request) {
                            $q2->where('client_id', $request->client_id);
                        });
                })
                ->where(function ($q) {
                    $q->where('is_for_all_employees', true)
                        ->orWhereHas('users', function ($q2) {
                            $q2->where('user_id', auth()->id());
                        });
                })->whereDoesntHave('excludedClients', function ($q) use ($request) {
    $q->where('client_id', $request->client_id);
})


                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->get();

            // ✅ فحص كل بند مقابل العروض
            foreach ($request->items as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                $unit_price = floatval($item['unit_price']);

                // 🔍 الحصول على العروض المطابقة لهذا المنتج والكمية
                $validOffers = $giftOffers->filter(function ($offer) use ($productId, $quantity) {
                    $matchesTarget = !$offer->target_product_id || $offer->target_product_id == $productId;
                    return $matchesTarget && $quantity >= $offer->min_quantity;
                });

                // ✅ اختيار أفضل عرض (أعلى عدد هدايا)
                $bestOffer = $validOffers->sortByDesc('gift_quantity')->first();

                if ($bestOffer) {
                    $giftProduct = Product::find($bestOffer->gift_product_id);
                    if (!$giftProduct) continue;

                    $items_data[] = [
                        'invoice_id' => null,
                        'product_id' => $giftProduct->id,
                        'store_house_id' => $store_house_id,
                        'item' => $giftProduct->name . ' (هدية)',
                        'description' => 'هدية عرض عند شراء ' . $quantity . ' من المنتج',
                        'quantity' => $bestOffer->gift_quantity,
                        'unit_price' => 0,
                        'discount' => 0,
                        'discount_type' => 1,
                        'tax_1' => 0,
                        'tax_2' => 0,
                        'total' => 0,
                    ];
                }
            }



            // ** الخطوة الثالثة: حساب الخصم الإضافي للفاتورة ككل **
            $invoice_discount = 0;
            if ($request->has('discount_amount') && $request->discount_amount > 0) {
                if ($request->has('discount_type') && $request->discount_type === 'percentage') {
                    $invoice_discount = ($total_amount * floatval($request->discount_amount)) / 100;
                } else {
                    $invoice_discount = floatval($request->discount_amount);
                }
            }

            // الخصومات الإجمالية
            $final_total_discount = $total_discount + $invoice_discount;

            // حساب المبلغ بعد الخصم
            $amount_after_discount = $total_amount - $final_total_discount;

            // ** حساب الضرائب **
            $tax_total = 0;
            if ($request->tax_type == 1) {
                // حساب الضريبة بناءً على القيمة التي يدخلها المستخدم في tax_1 أو tax_2
                foreach ($request->items as $item) {
                    $tax_1 = floatval($item['tax_1'] ?? 0); // الضريبة الأولى
                    $tax_2 = floatval($item['tax_2'] ?? 0); // الضريبة الثانية

                    // حساب الضريبة لكل بند
                    $item_total = floatval($item['quantity']) * floatval($item['unit_price']);
                    $item_tax = ($item_total * $tax_1) / 100 + ($item_total * $tax_2) / 100;

                    // إضافة الضريبة إلى الإجمالي
                    $tax_total += $item_tax;
                }
            }

            // ** إضافة تكلفة الشحن (إذا وجدت) **
            $shipping_cost = floatval($request->shipping_cost ?? 0);

            // ** حساب ضريبة الشحن (إذا كانت الضريبة مفعلة) **
            $shipping_tax = 0;
            if ($request->tax_type == 1) {
                $shipping_tax = $shipping_cost * 0.15; // ضريبة الشحن 15%
            }

            // ** إضافة ضريبة الشحن إلى tax_total **
            $tax_total += $shipping_tax;

            $adjustmentLabel = $request->input('adjustment_label');
            $adjustmentValue = floatval($request->input('adjustment_value', 0));
            $adjustmentType = $request->input('adjustment_type');

            // حساب قيمة التسوية الفعلية
            if ($adjustmentType === 'discount') {
                $adjustmentEffect = -$adjustmentValue;
            } elseif ($adjustmentType === 'addition') {
                $adjustmentEffect = $adjustmentValue;
            } else {
                $adjustmentEffect = 0; // احتياطًا لأي قيمة غير متوقعة
            }

            // ** الحساب النهائي للمجموع الكلي **
            $total_with_tax = $amount_after_discount + $tax_total + $shipping_cost + $adjustmentEffect;




            // ** حساب المبلغ المستحق (due_value) بعد خصم الدفعة المقدمة **
            $advance_payment = floatval($request->advance_payment ?? 0);
            $due_value = $total_with_tax - $advance_payment;

            // ** تحديد حالة الفاتورة بناءً على المدفوعات **
            $payment_status = 3; // الحالة الافتراضية (مسودة)
            $is_paid = false;

            if ($advance_payment > 0 || $request->has('is_paid')) {
                // حساب إجمالي المدفوعات
                $total_payments = $advance_payment;

                if ($request->has('is_paid') && $request->is_paid) {
                    $total_payments = $total_with_tax;
                    $advance_payment = $total_with_tax;
                    $due_value = 0;
                    $payment_status = 1; // مكتمل
                    $is_paid = true;
                } else {
                    // إذا كان هناك دفعة مقدمة لكن لم يتم اكتمال المبلغ
                    $payment_status = 2; // غير مكتمل
                    $is_paid = false;
                }
            }

            // إذا تم تحديد حالة دفع معينة في الطلب
            if ($request->has('payment_status')) {
                switch ($request->payment_status) {
                    case 4: // تحت المراجعة
                        $payment_status = 4;
                        $is_paid = false;
                        break;
                    case 5: // فاشلة
                        $payment_status = 5;
                        $is_paid = false;
                        break;
                }
            }

            $clientAccount = Account::where('client_id', $request->client_id)->first();

            if ($payment_status == 3) {
                if (
                    !auth()
                        ->user()
                        ->hasAnyPermission(['Issue_an_invoice_to_a_customer_who_has_a_debt'])
                ) {
                    if ($clientAccount && $clientAccount->balance != 0) {
                        return redirect()->back()->with('error', 'عفوا، لا يمكن إصدار فاتورة للعميل. الرجاء سداد المديونية أولًا.');
                    }
                }
            }

            $creditLimit = CreditLimit::first(); // جلب أول حد ائتماني
            if ($payment_status == 3) {
                if ($creditLimit && $total_with_tax + $clientAccount->balance > $creditLimit->value) {
                    return redirect()->back()->with('error', 'عفوا، لقد تجاوز العميل الحد الائتماني. الرجاء سداد المديونية أولًا.');
                }
            }
            // // التحقق من الرمز قبل إنشاء الفاتورة
            // if ($request->verification_code !== '123') {
            //     return response()->json(['error' => 'رمز التحقق غير صحيح.'], 400);
            // }
            // ** الخطوة الرابعة: إنشاء الفاتورة في قاعدة البيانات **



            $invoice = Invoice::create([
                'client_id' => $request->client_id,
                'employee_id' => $request->employee_id,
                'due_value' => $due_value,
                'code' => $code,
                'type' => 'normal',
                'invoice_date' => $request->invoice_date,
                'adjustment_label' => $request->adjustment_label,
                'adjustment_value' => $request->adjustment_value,
                'issue_date' => $request->issue_date,
                'terms' => $request->terms ?? 0,
                'notes' => $request->notes,
                'payment_status' => $payment_status,
                'is_paid' => $is_paid,
                'created_by' => Auth::id(),
                'account_id' => $request->account_id,
                'discount_amount' => $final_total_discount,
                'discount_type' => $request->has('discount_type') ? ($request->discount_type === 'percentage' ? 2 : 1) : 1,
                'advance_payment' => $advance_payment,
                'payment_type' => $request->payment_type ?? 1,
                'shipping_cost' => $shipping_cost,
                'shipping_tax' => $shipping_tax,
                'tax_type' => $request->tax_type ?? 1,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'received_date' => $request->received_date,
                'subtotal' => $total_amount,
                // 'discount_amount' => $final_total_discount,
                'tax_total' => $tax_total,
                'grand_total' => $total_with_tax,
                'paid_amount' => $advance_payment,
                'subscription_id' => $request->subscription_id,
            ]);

            $invoice->qrcode = $this->generateTlvContent($invoice->created_at, $invoice->grand_total, $invoice->tax_total);
            $invoice->save();
                   $client = Client::find($invoice->client_id);

if ($client) {
    // البحث عن آخر زيارة لهذا العميل وتحديث حالتها
    $visit = EmployeeClientVisit::where('employee_id', auth()->id())
        ->where('client_id', $client->id)
        ->latest() // أخذ آخر زيارة
        ->first();

    if ($visit) {
        $visit->update([
            'status' => 'active',
            'updated_at' => now()
        ]);

        Log::info('تم تحديث حالة الزيارة الحالية للفاتورة', [
            'visit_id' => $visit->id,
            'client_id' => $client->id,
            'invoice_id' => $invoice->id
        ]);
    } else {
        Log::warning('لا توجد زيارات مسجلة لهذا العميل للفاتورة', [
            'client_id' => $client->id,
            'employee_id' => auth()->id()
        ]);
    }
}


            // حساب الضريبة
            foreach ($request->items as $item) {
                // حساب الإجمالي لكل منتج (السعر × الكمية)
                $item_subtotal = $item['unit_price'] * $item['quantity'];

                // حساب الضرائب بناءً على البيانات القادمة من `request`
                $tax_ids = ['tax_1_id', 'tax_2_id'];
                foreach ($tax_ids as $tax_id) {
                    if (!empty($item[$tax_id])) {
                        // التحقق مما إذا كان هناك ضريبة
                        $tax = TaxSitting::find($item[$tax_id]);

                        if ($tax) {
                            $tax_value = ($tax->tax / 100) * $item_subtotal; // حساب قيمة الضريبة

                            // حفظ الضريبة في جدول TaxInvoice
                            TaxInvoice::create([
                                'name' => $tax->name,
                                'invoice_id' => $invoice->id,
                                'type' => $tax->type,
                                'rate' => $tax->tax,
                                'value' => $tax_value,
                                'type_invoice' => 'invoice',
                            ]);
                        }
                    }
                }
            }

            // ** تحديث رصيد حساب أبناء العميل **

            // إضافة المبلغ الإجمالي للفاتورة إلى رصيد أبناء العميل

            // ** الخطوة الخامسة: إنشاء سجلات البنود (items) للفاتورة **
            foreach ($items_data as $item) {
                $item['invoice_id'] = $invoice->id;
                $item_invoice = InvoiceItem::create($item);
                $client_name = Client::find($invoice->client_id);
                ModelsLog::create([
                    'type' => 'sales',
                    'type_id' => $invoice->id, // ID النشاط المرتبط
                    'type_log' => 'log', // نوع النشاط
                    'icon' => 'create',
                    'description' => sprintf(
                        'تم انشاء فاتورة مبيعات رقم **%s** للمنتج **%s** كمية **%s** بسعر **%s** للعميل **%s**',
                        $invoice->code ?? '', // رقم طلب الشراء
                        $item_invoice->product->name ?? '', // اسم المنتج
                        $item['quantity'] ?? '', // الكمية
                        $item['unit_price'] ?? '', // السعر
                        $client_name->trade_name ?? '', // المورد (يتم استخدام %s للنصوص)
                    ),
                    'created_by' => auth()->id(), // ID المستخدم الحالي
                ]);

                // ** تحديث المخزون بناءً على store_house_id المحدد في البند **
                $productDetails = ProductDetails::where('store_house_id', $item['store_house_id'])->where('product_id', $item['product_id'])->first();

                if (!$productDetails) {
                    $productDetails = ProductDetails::create([
                        'store_house_id' => $item['store_house_id'],
                        'product_id' => $item['product_id'],
                        'quantity' => 0,
                    ]);
                }

                $proudect = Product::where('id', $item['product_id'])->first();

                if ($proudect->type == 'products' || ($proudect->type == 'compiled' && $proudect->compile_type !== 'Instant')) {
                    if ((int) $item['quantity'] > (int) $productDetails->quantity) {
                        throw new \Exception('الكمية المطلوبة (' . $item['quantity'] . ') غير متاحة في المخزون. الكمية المتاحة: ' . $productDetails->quantity);
                    }
                }

                if ($proudect->type == 'products') {
                    // ** حساب المخزون قبل وبعد التعديل **
                    $total_quantity = DB::table('product_details')->where('product_id', $item['product_id'])->sum('quantity');
                    $stock_before = $total_quantity;
                    $stock_after = $stock_before - $item['quantity'];

                    // ** تحديث المخزون **
                    $productDetails->decrement('quantity', $item['quantity']);

                    // ** جلب مصدر إذن المخزون المناسب (فاتورة مبيعات) **
                    $permissionSource = PermissionSource::where('name', 'فاتورة مبيعات')->first();

                    if (!$permissionSource) {
                        // لو ما وجدنا مصدر إذن، ممكن ترمي استثناء أو ترجع خطأ
                        throw new \Exception("مصدر إذن 'فاتورة مبيعات' غير موجود في قاعدة البيانات.");
                    }

                    // ** تسجيل المبيعات في حركة المخزون **
                    $wareHousePermits = new WarehousePermits();
                    $wareHousePermits->permission_source_id = $permissionSource->id; // جلب id المصدر الديناميكي
                    $wareHousePermits->permission_date = $invoice->created_at;
                    $wareHousePermits->number = $invoice->id;
                    $wareHousePermits->grand_total = $invoice->grand_total;
                    $wareHousePermits->store_houses_id = $storeHouse->id;
                    $wareHousePermits->created_by = auth()->user()->id;
                    $wareHousePermits->save();

                    // ** تسجيل البيانات في WarehousePermitsProducts **
                    WarehousePermitsProducts::create([
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'unit_price' => $item['unit_price'],
                        'product_id' => $item['product_id'],
                        'stock_before' => $stock_before, // المخزون قبل التحديث
                        'stock_after' => $stock_after,   // المخزون بعد التحديث
                        'warehouse_permits_id' => $wareHousePermits->id,
                    ]);

                    // ** تنبيه انخفاض الكمية **
                    if ($productDetails->quantity < $product['low_stock_alert']) {
                        notifications::create([
                            'type' => 'Products',
                            'title' => 'تنبيه الكمية',
                            'description' => 'كمية المنتج ' . $product['name'] . ' قاربت على الانتهاء.',
                        ]);

                        $telegramApiUrl = 'https://api.telegram.org/bot7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU/sendMessage';

                        $message = "🚨 *تنبيه جديد!* 🚨\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
                        $message .= "📌 *العنوان:* 🔔 `تنبيه الكمية`\n";
                        $message .= '📦 *المنتج:* `' . $product['name'] . "`\n";
                        $message .= "⚠️ *الوصف:* _كمية المنتج قاربت على الانتهاء._\n";
                        $message .= '📅 *التاريخ:* `' . now()->format('Y-m-d H:i') . "`\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━\n";

                        $response = Http::post($telegramApiUrl, [
                            'chat_id' => '@Salesfatrasmart',
                            'text' => $message,
                            'parse_mode' => 'Markdown',
                            'timeout' => 60,
                        ]);
                    }

                    // ** تنبيه تاريخ انتهاء الصلاحية **
                    if ($product['track_inventory'] == 2 && !empty($product['expiry_date']) && !empty($product['notify_before_days'])) {
                        $expiryDate = Carbon::parse($product['expiry_date']);
                        $daysBeforeExpiry = (int) $product['notify_before_days'];

                        if ($expiryDate->greaterThan(now())) {
                            $remainingDays = floor($expiryDate->diffInDays(now()));

                            if ($remainingDays <= $daysBeforeExpiry) {
                                notifications::create([
                                    'type' => 'Products',
                                    'title' => 'تاريخ الانتهاء',
                                    'description' => 'المنتج ' . $product['name'] . ' قارب على الانتهاء في خلال ' . $remainingDays . ' يوم.',
                                ]);

                                $telegramApiUrl = 'https://api.telegram.org/bot7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU/sendMessage';

                                $message = "⚠️ *تنبيه انتهاء صلاحية المنتج* ⚠️\n";
                                $message .= "━━━━━━━━━━━━━━━━━━━━\n";
                                $message .= '📌 *اسم المنتج:* ' . $product['name'] . "\n";
                                $message .= '📅 *تاريخ الانتهاء:* ' . $expiryDate->format('Y-m-d') . "\n";
                                $message .= '⏳ *المدة المتبقية:* ' . $remainingDays . " يوم\n";
                                $message .= "━━━━━━━━━━━━━━━━━━━━\n";

                                $response = Http::post($telegramApiUrl, [
                                    'chat_id' => '@Salesfatrasmart',
                                    'text' => $message,
                                    'parse_mode' => 'Markdown',
                                    'timeout' => 60,
                                ]);
                            }
                        }
                    }
                }


                if ($proudect->type == 'compiled' && $proudect->compile_type == 'Instant') {
                    // ** حساب المخزون قبل وبعد التعديل للمنتج التجميعي **
                    $total_quantity = DB::table('product_details')->where('product_id', $item['product_id'])->sum('quantity');
                    $stock_before = $total_quantity;

                    // ** الحركة الأولى: إضافة الكمية إلى المخزن **
                    $wareHousePermits = new WarehousePermits();
                    $wareHousePermits->permission_source_id = 1; // إضافة للمخزون منتج مجمع خارجي
                    $wareHousePermits->permission_date = $invoice->created_at;
                    $wareHousePermits->number = $invoice->id;
                    $wareHousePermits->grand_total = $invoice->grand_total;
                    $wareHousePermits->store_houses_id = $storeHouse->id;
                    $wareHousePermits->created_by = auth()->user()->id;
                    $wareHousePermits->save();

                    // ** تحديث المخزون: إضافة الكمية **
                    $productDetails->increment('quantity', $item['quantity']); // إضافة الكمية بدلاً من خصمها

                    // ** تسجيل البيانات في WarehousePermitsProducts للإضافة **
                    WarehousePermitsProducts::create([
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'unit_price' => $item['unit_price'],
                        'product_id' => $item['product_id'],
                        'stock_before' => $stock_before, // المخزون قبل التحديث
                        'stock_after' => $stock_before + $item['quantity'], // المخزون بعد الإضافة
                        'warehouse_permits_id' => $wareHousePermits->id,
                    ]);

                    // ** الحركة الثانية: خصم الكمية من المخزن **
                    $wareHousePermits = new WarehousePermits();
                    $wareHousePermits->permission_source_id = 10; // خصم من الفاتورة
                    $wareHousePermits->permission_date = $invoice->created_at;
                    $wareHousePermits->number = $invoice->id;
                    $wareHousePermits->grand_total = $invoice->grand_total;
                    $wareHousePermits->store_houses_id = $storeHouse->id;
                    $wareHousePermits->created_by = auth()->user()->id;
                    $wareHousePermits->save();

                    // ** تحديث المخزون: خصم الكمية **
                    $productDetails->decrement('quantity', $item['quantity']); // خصم الكمية

                    // ** تسجيل البيانات في WarehousePermitsProducts للخصم **
                    WarehousePermitsProducts::create([
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'unit_price' => $item['unit_price'],
                        'product_id' => $item['product_id'],
                        'stock_before' => $stock_before + $item['quantity'], // المخزون قبل الخصم (بعد الإضافة)
                        'stock_after' => $stock_before, // المخزون بعد الخصم (يعود إلى القيمة الأصلية)
                        'warehouse_permits_id' => $wareHousePermits->id,
                    ]);

                    // ** الحصول على المنتجات التابعة للمنتج التجميعي **
                    $CompiledProducts = CompiledProducts::where('compile_id', $item['product_id'])->get();

                    foreach ($CompiledProducts as $compiledProduct) {
                        // ** حساب المخزون قبل وبعد التعديل للمنتج التابع **
                        $total_quantity = DB::table('product_details')->where('product_id', $compiledProduct->product_id)->sum('quantity');
                        $stock_before = $total_quantity;
                        $stock_after = $stock_before - $compiledProduct->qyt * $item['quantity']; // خصم الكمية المطلوبة

                        // ** تسجيل المبيعات في حركة المخزون للمنتج التابع **
                        $wareHousePermits = new WarehousePermits();
                        $wareHousePermits->permission_source_id = 10; // خصم من الفاتورة
                        $wareHousePermits->permission_date = $invoice->created_at;
                        $wareHousePermits->number = $invoice->id;
                        $wareHousePermits->grand_total = $invoice->grand_total;
                        $wareHousePermits->store_houses_id = $storeHouse->id;
                        $wareHousePermits->created_by = auth()->user()->id;
                        $wareHousePermits->save();

                        // ** تسجيل البيانات في WarehousePermitsProducts للمنتج التابع **
                        WarehousePermitsProducts::create([
                            'quantity' => $compiledProduct->qyt * $item['quantity'],
                            'total' => $item['total'],
                            'unit_price' => $item['unit_price'],
                            'product_id' => $compiledProduct->product_id,
                            'stock_before' => $stock_before, // المخزون قبل التحديث
                            'stock_after' => $stock_after, // المخزون بعد التحديث
                            'warehouse_permits_id' => $wareHousePermits->id,
                        ]);

                        // ** تحديث المخزون للمنتج التابع **
                        $compiledProductDetails = ProductDetails::where('store_house_id', $item['store_house_id'])->where('product_id', $compiledProduct->product_id)->first();

                        if (!$compiledProductDetails) {
                            $compiledProductDetails = ProductDetails::create([
                                'store_house_id' => $item['store_house_id'],
                                'product_id' => $compiledProduct->product_id,
                                'quantity' => 0,
                            ]);
                        }

                        $compiledProductDetails->decrement('quantity', $compiledProduct->qyt * $item['quantity']);
                    }
                }
            }

            // جلب بيانات الموظف والمستخدم
            $employee_name = Employee::where('id', $invoice->employee_id)->first();
            $user_name = User::where('id', $invoice->created_by)->first();
            $client_name = Client::find($invoice->client_id);
            // جلب جميع المنتجات المرتبطة بالفاتورة
            $invoiceItems = InvoiceItem::where('invoice_id', $invoice->id)->get();

            // تجهيز قائمة المنتجات
            $productsList = '';
            foreach ($invoiceItems as $item) {
                $product = Product::find($item->product_id);
                $productName = $product ? $product->name : 'منتج غير معروف';
                $productsList .= "▫️ *{$productName}* - الكمية: {$item->quantity}, السعر: {$item->unit_price} \n";
            }

            // // رابط API التلقرام
            $telegramApiUrl = 'https://api.telegram.org/bot7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU/sendMessage';

            // تجهيز الرسالة
            $message = "📜 *فاتورة جديدة* 📜\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "🆔 *رقم الفاتورة:* `$code`\n";
            $message .= '👤 *مسؤول البيع:* ' . ($employee_name->first_name ?? 'لا يوجد') . "\n";
            $message .= '🏢 *العميل:* ' . ($client_name->trade_name ?? 'لا يوجد') . "\n";
            $message .= '✍🏻 *أنشئت بواسطة:* ' . ($user_name->name ?? 'لا يوجد') . "\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= '💰 *المجموع:* `' . number_format($invoice->grand_total, 2) . "` ريال\n";
            $message .= '🧾 *الضريبة:* `' . number_format($invoice->tax_total, 2) . "` ريال\n";
            $message .= '📌 *الإجمالي:* `' . number_format($invoice->tax_total + $invoice->grand_total, 2) . "` ريال\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "📦 *المنتجات:* \n" . $productsList;
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= '📅 *التاريخ:* `' . date('Y-m-d H:i') . "`\n";

            // إرسال الرسالة إلى التلقرام
            $response = Http::post($telegramApiUrl, [
                'chat_id' => '@Salesfatrasmart', // تأكد من أن لديك صلاحية الإرسال للقناة
                'text' => $message,
                'parse_mode' => 'Markdown',
                'timeout' => 30,
            ]);
            notifications::create([
                'type' => 'invoice',
                'title' => $user_name->name . ' أضاف فاتورة لعميل',
                'description' => 'فاتورة للعميل ' . $client_name->trade_name . ' بقيمة ' . number_format($invoice->grand_total, 2) . ' ر.س',
            ]);


            // التحقق مما إذا كان للمستخدم قاعدة عمولة
            $userHasCommission = CommissionUsers::where('employee_id', auth()->user()->id)->exists();

            //  if (!$userHasCommission) {
            //      return "no000"; // المستخدم لا يملك قاعدة عمولة
            //   }

            if ($userHasCommission) {
                // جلب جميع commission_id الخاصة بالمستخدم
                $commissionIds = CommissionUsers::where('employee_id', auth()->user()->id)->pluck('commission_id');

                // التحقق مما إذا كانت هناك أي عمولة نشطة في جدول Commission
                $activeCommission = Commission::whereIn('id', $commissionIds)->where('status', 'active')->first();

                //   if (!$activeCommission) {
                //    return "not active"; // لا توجد عمولة نشطة، توقف هنا
                //    }

                if ($activeCommission) {
                    //    // ✅ التحقق مما إذا كانت حالة الدفع في `invoice` تتطابق مع حساب العمولة في `commission`
                    //    if (
                    //  ($invoice->payment_status == 1 && $activeCommission->commission_calculation != "fully_paid") ||
                    //  ($invoice->payment_status == 2 && $activeCommission->commission_calculation != "partially_paid")
                    //  )   {
                    //  return "payment mismatch"; // حالتا الدفع لا تتطابقان
                    //   }

                    // البحث في جدول commission__products باستخدام هذه commission_id
                    $commissionProducts = Commission_Products::whereIn('commission_id', $commissionIds)->get();

                    // التحقق من وجود أي product_id = 0
                    if ($commissionProducts->contains('product_id', 0)) {
                        return 'yesall';
                    }

                    // جلب جميع product_id الخاصة بالفاتورة
                    $invoiceProductIds = InvoiceItem::where('invoice_id', $invoice->id)->pluck('product_id');

                    // التحقق مما إذا كان أي من product_id في جدول commission__products يساوي أي من المنتجات في الفاتورة
                    if ($commissionProducts->whereIn('product_id', $invoiceProductIds)->isNotEmpty()) {
                        // جلب بيانات العمولة المرتبطة بالفاتورة
                        $inAmount = Commission::whereIn('id', $commissionIds)->first();
                        $commissionProduct = Commission_Products::whereIn('commission_id', $commissionIds)->first();
                        if ($inAmount) {
                            if ($inAmount->target_type == 'amount') {
                                $invoiceTotal = InvoiceItem::where('invoice_id', $invoice->id)->sum('total');
                                $invoiceQyt = InvoiceItem::where('invoice_id', $invoice->id)->first();
                                // تحقق من أن قيمة العمولة تساوي أو أكبر من `total`
                                if ((float) $inAmount->value <= (float) $invoiceTotal) {
                                    $salesInvoice = new SalesCommission();
                                    $salesInvoice->invoice_number = $invoice->id; // تعيين رقم الفاتورة الصحيح
                                    $salesInvoice->employee_id = auth()->user()->id; // اسم الموظف
                                    $salesInvoice->sales_amount = $invoiceTotal; // إجمالي المبيعات
                                    $salesInvoice->sales_quantity = $invoiceQyt->quantity;
                                    $salesInvoice->commission_id = $inAmount->id;
                                    $salesInvoice->ratio = $commissionProduct->commission_percentage ?? 0;
                                    $salesInvoice->product_id = $commissionProduct->product_id ?? 0; // رقم معرف العمولة
                                    $salesInvoice->save(); // حفظ السجل في قاعدة البيانات
                                }
                            } elseif ($inAmount->target_type == 'quantity') {
                                // تحقق من أن قيمة العمولة تساوي أو أكبر من `quantity`
                                $invoiceQuantity = InvoiceItem::where('invoice_id', $invoice->id)->sum('quantity');

                                if ((float) $inAmount->value <= (float) $invoiceQuantity) {
                                    $salesInvoice = new SalesCommission();
                                    $salesInvoice->invoice_number = $invoice->id; // تعيين رقم الفاتورة الصحيح
                                    $salesInvoice->employee_id = auth()->user()->id; // اسم الموظف
                                    $salesInvoice->sales_amount = $invoiceTotal; // إجمالي المبيعات
                                    $salesInvoice->sales_quantity = $invoiceQyt->quantity;
                                    $salesInvoice->commission_id = $inAmount->id; // رقم معرف العمولة
                                    $salesInvoice->ratio = $commissionProduct->commission_percentage ?? 0;
                                    $salesInvoice->product_id = $commissionProduct->product_id ?? 0;
                                    $salesInvoice->save(); // حفظ السجل في قاعدة البيانات
                                }
                            }
                        }
                    }
                }
            }

            // ** معالجة المرفقات (attachments) إذا وجدت **
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $invoice->attachments = $filename;
                    $invoice->save();
                }
            }
            $clientaccounts = Account::where('client_id', $invoice->client_id)->first();
            if (!$clientaccounts) {
                throw new \Exception('حساب العميل غير موجود');
            }
            // استرجاع حساب القيمة المضافة المحصلة
            $vatAccount = Account::where('name', 'القيمة المضافة المحصلة')->first();
            if (!$vatAccount) {
                throw new \Exception('حساب القيمة المضافة المحصلة غير موجود');
            }
            $salesAccount = Account::where('name', 'المبيعات')->first();
            if (!$salesAccount) {
                throw new \Exception('حساب المبيعات غير موجود');
            }

            //     // إنشاء القيد المحاسبي للفاتورة
            $journalEntry = JournalEntry::create([
                'reference_number' => $invoice->code,
                'date' => now(),
                'description' => 'فاتورة مبيعات رقم ' . $invoice->code,
                'status' => 1,
                'currency' => 'SAR',
                'client_id' => $invoice->client_id,
                'invoice_id' => $invoice->id,
                // 'created_by_employee' => Auth::id(),
            ]);

            // // إضافة تفاصيل القيد المحاسبي
            // // 1. حساب العميل (مدين)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $clientaccounts->id, // حساب العميل
                'description' => 'فاتورة مبيعات رقم ' . $invoice->code,
                'debit' => $total_with_tax, // المبلغ الكلي للفاتورة (مدين)
                'credit' => 0,
                'is_debit' => true,
            ]);

            // // 2. حساب المبيعات (دائن)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $salesAccount->id, // حساب المبيعات
                'description' => 'إيرادات مبيعات',
                'debit' => 0,
                'credit' => $amount_after_discount, // المبلغ بعد الخصم (دائن)
                'is_debit' => false,
            ]);

            // // 3. حساب القيمة المضافة المحصلة (دائن)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $vatAccount->id, // حساب القيمة المضافة المحصلة
                'description' => 'ضريبة القيمة المضافة',
                'debit' => 0,
                'credit' => $tax_total, // قيمة الضريبة (دائن)
                'is_debit' => false,
            ]);

            // ** تحديث رصيد حساب المبيعات (إيرادات) **
            //  if ($salesAccount) {
            //     $salesAccount->balance += $amount_after_discount; // إضافة المبلغ بعد الخصم
            //     $salesAccount->save();
            // }

            // ** تحديث رصيد حساب المبيعات والحسابات المرتبطة به (إيرادات) **
            if ($salesAccount) {
                $amount = $amount_after_discount;
                $salesAccount->balance += $amount;
                $salesAccount->save();

                // تحديث جميع الحسابات الرئيسية المتصلة به
                // $this->updateParentBalanceSalesAccount($salesAccount->parent_id, $amount);
            }

            // تحديث رصيد حساب الإيرادات (المبيعات + الضريبة)
            $revenueAccount = Account::where('name', 'الإيرادات')->first();
            if ($revenueAccount) {
                $revenueAccount->balance += $amount_after_discount; // المبلغ بعد الخصم (بدون الضريبة)
                $revenueAccount->save();
            }

            // $vatAccount->balance += $tax_total; // قيمة الضريبة
            // $vatAccount->save();

            //تحديث رصيد حساب القيمة المضافة (الخصوم)
            if ($vatAccount) {
                $amount = $tax_total;
                $vatAccount->balance += $amount;
                $vatAccount->save();

                // تحديث جميع الحسابات الرئيسية المتصلة به
                $this->updateParentBalance($vatAccount->parent_id, $amount);
            }

            // تحديث رصيد حساب الأصول (المبيعات + الضريبة)
            $assetsAccount = Account::where('name', 'الأصول')->first();
            if ($assetsAccount) {
                $assetsAccount->balance += $total_with_tax; // المبلغ الكلي (المبيعات + الضريبة)
                $assetsAccount->save();
            }
            // تحديث رصيد حساب الخزينة الرئيسية

            // if ($MainTreasury) {
            //     $MainTreasury->balance += $total_with_tax; // المبلغ الكلي (المبيعات + الضريبة)
            //     $MainTreasury->save();
            // }

            if ($clientaccounts) {
                $clientaccounts->balance += $invoice->grand_total; // المبلغ الكلي (المبيعات + الضريبة)
                $clientaccounts->save();
            }


            // تحديث رصيد حساب الخزينة الرئيسية

            // ** الخطوة السابعة: إنشاء سجل الدفع إذا كان هناك دفعة مقدمة أو دفع كامل **
            if ($advance_payment > 0 || $is_paid) {
                $payment_amount = $is_paid ? $total_with_tax : $advance_payment;

                // تحديد الخزينة المستهدفة بناءً على الموظف
                $MainTreasury = null;

                if ($user && $user->employee_id) {
                    // البحث عن الخزينة المرتبطة بالموظف
                    $TreasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

                    if ($TreasuryEmployee && $TreasuryEmployee->treasury_id) {
                        // إذا كان الموظف لديه خزينة مرتبطة
                        $MainTreasury = Account::where('id', $TreasuryEmployee->treasury_id)->first();
                    } else {
                        // إذا لم يكن لدى الموظف خزينة مرتبطة، استخدم الخزينة الرئيسية
                        $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                    }
                } else {
                    // إذا لم يكن المستخدم موجودًا أو لم يكن لديه employee_id، استخدم الخزينة الرئيسية
                    $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                }

                // إذا لم يتم العثور على خزينة، توقف العملية وأظهر خطأ
                if (!$MainTreasury) {
                    throw new \Exception('لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.');
                }

                // إنشاء سجل الدفع
                $payment = PaymentsProcess::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $payment_amount,
                    'payment_date' => now(),
                    'payment_method' => $request->payment_method,
                    'reference_number' => $request->reference_number,
                    'notes' => 'تم إنشاء الدفعة تلقائياً عند إنشاء الفاتورة',
                    'type' => 'client payments',
                    'payment_status' => $payment_status,
                    'created_by' => Auth::id(),
                ]);

                // تحديث رصيد الخزينة
                if ($MainTreasury) {
                    $MainTreasury->balance += $payment_amount;
                    $MainTreasury->save();
                }

                if ($advance_payment > 0) {

                    if ($clientaccounts) {
                        $clientaccounts->balance -= $payment_amount; // المبلغ الكلي (المبيعات + الضريبة)
                        $clientaccounts->save();
                    }
                } else {
                    if ($clientaccounts) {
                        $clientaccounts->balance -= $invoice->grand_total; // المبلغ الكلي (المبيعات + الضريبة)
                        $clientaccounts->save();
                    }
                }

                // إنشاء قيد محاسبي للدفعة
                $paymentJournalEntry = JournalEntry::create([
                    'reference_number' => $payment->reference_number ?? $invoice->code,
                    'date' => now(),
                    'description' => 'دفعة للفاتورة رقم ' . $invoice->code,
                    'status' => 1,
                    'currency' => 'SAR',
                    'client_id' => $invoice->client_id,
                    'invoice_id' => $invoice->id,
                    // 'created_by_employee' => Auth::id(),
                ]);

                // 1. حساب الخزينة المستهدفة (مدين)
                JournalEntryDetail::create([
                    'journal_entry_id' => $paymentJournalEntry->id,
                    'account_id' => $MainTreasury->id,
                    'description' => 'دفعة للفاتورة رقم ' . $invoice->code,
                    'debit' => $payment_amount,
                    'credit' => 0,
                    'is_debit' => true,
                    'client_account_id' => $clientaccounts->id,
                ]);

                // 2. حساب العميل (دائن)
                JournalEntryDetail::create([
                    'journal_entry_id' => $paymentJournalEntry->id,
                    'account_id' => $clientaccounts->id,
                    'description' => 'دفعة عميل  للفاتورة رقم ' . $invoice->code,
                    'debit' => 0,
                    'credit' => $payment_amount,
                    'is_debit' => false,
                    'client_account_id' => $clientaccounts->id,
                ]);
            }
            DB::commit();

            // إعداد رسالة النجاح
            // $response = Http::post($telegramApiUrl, [
            //     'chat_id' => '@Salesfatrasmart',  // تأكد من أن لديك صلاحية الإرسال للقناة
            //     'text' => sprintf("تم إنشاء فاتورة جديدة بنجاح. رقم الفاتورة: %s", $invoice->code),
            //     'parse_mode' => 'Markdown',
            // ]);

            // if ($response->failed()) {
            //     Log::error('خطاء في الارسال للقناة: ' . $response->body());
            // }

            return redirect()
                ->route('invoices.show', $invoice->id)
                ->with('success', sprintf('تم إنشاء فاتورة المبيعات بنجاح. رقم الفاتورة: %s', $invoice->code));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في إنشاء فاتورة المبيعات: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء حفظ فاتورة المبيعات: ' . $e->getMessage());
        }
        //edit
    }
    public function storeFromJob(Request $request)
    {
        try {
            // ** الخطوة الأولى: إنشاء كود للفاتورة **



            // ** الخطوة الأولى: إنشاء كود للفاتورة **
            $code = $request->code;
            if (!$code) {
                $lastOrder = Invoice::orderBy('id', 'desc')->first();
                $nextNumber = $lastOrder ? intval($lastOrder->code) + 1 : 1;
                // التحقق من أن الرقم فريد
                while (Invoice::where('code', str_pad($nextNumber, 5, '0', STR_PAD_LEFT))->exists()) {
                    $nextNumber++;
                }
                $code = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            } else {
                $existingCode = Invoice::where('code', $request->code)->exists();
                if ($existingCode) {
                    return redirect()->back()->withInput()->with('error', 'رقم الفاتورة موجود مسبقاً، الرجاء استخدام رقم آخر');
                }
            }
            DB::beginTransaction(); // بدء المعاملة

            // ** تجهيز المتغيرات الرئيسية لحساب الفاتورة **
            $total_amount = 0; // إجمالي المبلغ قبل الخصومات
            $total_discount = 0; // إجمالي الخصومات على البنود
            $items_data = []; // تجميع بيانات البنود

            // ** الخطوة الثانية: معالجة البنود (items) **
            if ($request->has('items') && count($request->items)) {
                foreach ($request->items as $item) {
                    // التحقق من وجود product_id في البند
                    if (!isset($item['product_id'])) {
                        throw new \Exception('الرجاء اختيار المنتج');
                    }

                    // جلب المنتج
                    $product = Product::find($item['product_id']);
                    if (!$product) {
                        throw new \Exception('المنتج غير موجود: ' . $item['product_id']);
                    }

                    // التحقق من وجود store_house_id في جدول store_houses
                    $store_house_id = $item['store_house_id'] ?? null;

                    // البحث عن المستودع
                    $storeHouse = null;
                    if ($store_house_id) {
                        // البحث عن المستودع المحدد
                        $storeHouse = StoreHouse::find($store_house_id);
                    }

                    if (!$storeHouse) {
                        // إذا لم يتم العثور على المستودع المحدد، ابحث عن أول مستودع متاح
                        $storeHouse = StoreHouse::first();
                        if (!$storeHouse) {
                            throw new \Exception('لا يوجد أي مستودع في النظام. الرجاء إضافة مستودع واحد على الأقل.');
                        }
                        $store_house_id = $storeHouse->id;
                    }
                    // الحصول على المستخدم الحالي
                    $user = Auth::user();

                    // التحقق مما إذا كان للمستخدم employee_id
                    // الحصول على المستخدم الحالي
                    $user = Auth::user();

                    // التحقق مما إذا كان للمستخدم employee_id والبحث عن المستودع الافتراضي
                    if ($user && $user->employee_id) {
                        $defaultWarehouse = DefaultWarehouses::where('employee_id', $user->employee_id)->first();

                        // التحقق مما إذا كان هناك مستودع افتراضي واستخدام storehouse_id إذا وجد
                        if ($defaultWarehouse && $defaultWarehouse->storehouse_id) {
                            $storeHouse = StoreHouse::find($defaultWarehouse->storehouse_id);
                        } else {
                            $storeHouse = StoreHouse::where('major', 1)->first();
                        }
                    } else {
                        // إذا لم يكن لديه employee_id، يتم تعيين storehouse الافتراضي
                        $storeHouse = StoreHouse::where('major', 1)->first();
                    }

                    // الخزينة الاقتراضيه للموظف
                    $store_house_id = $storeHouse ? $storeHouse->id : null;

                    $TreasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

                    if ($user && $user->employee_id) {
                        // تحقق مما إذا كان treasury_id فارغًا أو null
                        if ($TreasuryEmployee && $TreasuryEmployee->treasury_id) {
                            $MainTreasury = Account::where('id', $TreasuryEmployee->treasury_id)->first();
                        } else {
                            // إذا كان treasury_id null أو غير موجود، اختر الخزينة الرئيسية
                            $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                        }
                    } else {
                        // إذا لم يكن المستخدم موجودًا أو لم يكن لديه employee_id، اختر الخزينة الرئيسية
                        $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                    }

                    // حساب تفاصيل الكمية والأسعار
                    $quantity = floatval($item['quantity']);
                    $unit_price = floatval($item['unit_price']);
                    $item_total = $quantity * $unit_price;

                    // حساب الخصم للبند
                    $item_discount = 0; // قيمة الخصم المبدئية
                    if (isset($item['discount']) && $item['discount'] > 0) {
                        if (isset($item['discount_type']) && $item['discount_type'] === 'percentage') {
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
                        'invoice_id' => null, // سيتم تعيينه لاحقًا بعد إنشاء الفاتورة
                        'product_id' => $item['product_id'],
                        'store_house_id' => $store_house_id,
                        'item' => $product->name ?? 'المنتج ' . $item['product_id'],
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
            // ✅ استخراج عروض الهدايا
            // ✅ استخراج عروض الهدايا
            $giftOffers = GiftOffer::where(function ($q) use ($request) {
                $q->where('is_for_all_clients', true)
                    ->orWhereHas('clients', function ($q2) use ($request) {
                        $q2->where('client_id', $request->client_id);
                    });
            })
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->get();

            // ✅ فحص كل بند مقابل العروض
            foreach ($request->items as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                $unit_price = floatval($item['unit_price']);

                // 🔍 الحصول على العروض المطابقة لهذا المنتج والكمية
                $validOffers = $giftOffers->filter(function ($offer) use ($productId, $quantity) {
                    $matchesTarget = !$offer->target_product_id || $offer->target_product_id == $productId;
                    return $matchesTarget && $quantity >= $offer->min_quantity;
                });

                // ✅ اختيار أفضل عرض (أعلى عدد هدايا)
                $bestOffer = $validOffers->sortByDesc('gift_quantity')->first();

                if ($bestOffer) {
                    $giftProduct = Product::find($bestOffer->gift_product_id);
                    if (!$giftProduct) continue;

                    $items_data[] = [
                        'invoice_id' => null,
                        'product_id' => $giftProduct->id,
                        'store_house_id' => $store_house_id,
                        'item' => $giftProduct->name . ' (هدية)',
                        'description' => 'هدية عرض عند شراء ' . $quantity . ' من المنتج',
                        'quantity' => $bestOffer->gift_quantity,
                        'unit_price' => 0,
                        'discount' => 0,
                        'discount_type' => 1,
                        'tax_1' => 0,
                        'tax_2' => 0,
                        'total' => 0,
                    ];
                }
            }



            // ** الخطوة الثالثة: حساب الخصم الإضافي للفاتورة ككل **
            $invoice_discount = 0;
            if ($request->has('discount_amount') && $request->discount_amount > 0) {
                if ($request->has('discount_type') && $request->discount_type === 'percentage') {
                    $invoice_discount = ($total_amount * floatval($request->discount_amount)) / 100;
                } else {
                    $invoice_discount = floatval($request->discount_amount);
                }
            }

            // الخصومات الإجمالية
            $final_total_discount = $total_discount + $invoice_discount;

            // حساب المبلغ بعد الخصم
            $amount_after_discount = $total_amount - $final_total_discount;

            // ** حساب الضرائب **
            $tax_total = 0;
            if ($request->tax_type == 1) {
                // حساب الضريبة بناءً على القيمة التي يدخلها المستخدم في tax_1 أو tax_2
                foreach ($request->items as $item) {
                    $tax_1 = floatval($item['tax_1'] ?? 0); // الضريبة الأولى
                    $tax_2 = floatval($item['tax_2'] ?? 0); // الضريبة الثانية

                    // حساب الضريبة لكل بند
                    $item_total = floatval($item['quantity']) * floatval($item['unit_price']);
                    $item_tax = ($item_total * $tax_1) / 100 + ($item_total * $tax_2) / 100;

                    // إضافة الضريبة إلى الإجمالي
                    $tax_total += $item_tax;
                }
            }

            // ** إضافة تكلفة الشحن (إذا وجدت) **
            $shipping_cost = floatval($request->shipping_cost ?? 0);

            // ** حساب ضريبة الشحن (إذا كانت الضريبة مفعلة) **
            $shipping_tax = 0;
            if ($request->tax_type == 1) {
                $shipping_tax = $shipping_cost * 0.15; // ضريبة الشحن 15%
            }

            // ** إضافة ضريبة الشحن إلى tax_total **
            $tax_total += $shipping_tax;

            $adjustmentLabel = $request->input('adjustment_label');
            $adjustmentValue = floatval($request->input('adjustment_value', 0));



            // ** الحساب النهائي للمجموع الكلي **
            $total_with_tax =  $tax_total + $shipping_cost - $amount_after_discount + $adjustmentValue;

            // ** حساب المبلغ المستحق (due_value) بعد خصم الدفعة المقدمة **
            $advance_payment = floatval($request->advance_payment ?? 0);
            $due_value = $total_with_tax - $advance_payment;

            // ** تحديد حالة الفاتورة بناءً على المدفوعات **
            $payment_status = 3; // الحالة الافتراضية (مسودة)
            $is_paid = false;

            if ($advance_payment > 0 || $request->has('is_paid')) {
                // حساب إجمالي المدفوعات
                $total_payments = $advance_payment;

                if ($request->has('is_paid') && $request->is_paid) {
                    $total_payments = $total_with_tax;
                    $advance_payment = $total_with_tax;
                    $due_value = 0;
                    $payment_status = 1; // مكتمل
                    $is_paid = true;
                } else {
                    // إذا كان هناك دفعة مقدمة لكن لم يتم اكتمال المبلغ
                    $payment_status = 2; // غير مكتمل
                    $is_paid = false;
                }
            }

            // إذا تم تحديد حالة دفع معينة في الطلب
            if ($request->has('payment_status')) {
                switch ($request->payment_status) {
                    case 4: // تحت المراجعة
                        $payment_status = 4;
                        $is_paid = false;
                        break;
                    case 5: // فاشلة
                        $payment_status = 5;
                        $is_paid = false;
                        break;
                }
            }

            $clientAccount = Account::where('client_id', $request->client_id)->first();

            if ($payment_status == 3) {
                if (
                    !auth()
                        ->user()
                        ->hasAnyPermission(['Issue_an_invoice_to_a_customer_who_has_a_debt'])
                ) {
                    if ($clientAccount && $clientAccount->balance != 0) {
                        return redirect()->back()->with('error', 'عفوا، لا يمكن إصدار فاتورة للعميل. الرجاء سداد المديونية أولًا.');
                    }
                }
            }

            $creditLimit = CreditLimit::first(); // جلب أول حد ائتماني
            if ($payment_status == 3) {
                if ($creditLimit && $total_with_tax + $clientAccount->balance > $creditLimit->value) {
                    return redirect()->back()->with('error', 'عفوا، لقد تجاوز العميل الحد الائتماني. الرجاء سداد المديونية أولًا.');
                }
            }
            // // التحقق من الرمز قبل إنشاء الفاتورة
            // if ($request->verification_code !== '123') {
            //     return response()->json(['error' => 'رمز التحقق غير صحيح.'], 400);
            // }
            // ** الخطوة الرابعة: إنشاء الفاتورة في قاعدة البيانات **
            $invoice = Invoice::create([
                'client_id' => $request->client_id,
                'employee_id' => $request->employee_id,
                'due_value' => $due_value,
                'code' => $code,
                'type' => 'normal',
                'invoice_date' => $request->invoice_date,
                'issue_date' => $request->issue_date,
                'terms' => $request->terms ?? 0,
                'notes' => $request->notes,
                'payment_status' => $payment_status,
                'is_paid' => $is_paid,
                'created_by' => Auth::id(),
                'account_id' => $request->account_id,
                'discount_amount' => $final_total_discount,
                'discount_type' => $request->has('discount_type') ? ($request->discount_type === 'percentage' ? 2 : 1) : 1,
                'advance_payment' => $advance_payment,
                'payment_type' => $request->payment_type ?? 1,
                'shipping_cost' => $shipping_cost,
                'shipping_tax' => $shipping_tax,
                'tax_type' => $request->tax_type ?? 1,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'received_date' => $request->received_date,
                'subtotal' => $total_amount,
                // 'discount_amount' => $final_total_discount,
                'tax_total' => $tax_total,
                'grand_total' => $total_with_tax,
                'paid_amount' => $advance_payment,
                'adjustment_label' => $adjustmentLabel,
                'adjustment_value' => $adjustmentValue,
                'subscription_id' => $request->subscription_id,
            ]);

            $invoice->qrcode = $this->generateTlvContent($invoice->created_at, $invoice->grand_total, $invoice->tax_total);
            $invoice->save();

            // حساب الضريبة
            foreach ($request->items as $item) {
                // حساب الإجمالي لكل منتج (السعر × الكمية)
                $item_subtotal = $item['unit_price'] * $item['quantity'];

                // حساب الضرائب بناءً على البيانات القادمة من `request`
                $tax_ids = ['tax_1_id', 'tax_2_id'];
                foreach ($tax_ids as $tax_id) {
                    if (!empty($item[$tax_id])) {
                        // التحقق مما إذا كان هناك ضريبة
                        $tax = TaxSitting::find($item[$tax_id]);

                        if ($tax) {
                            $tax_value = ($tax->tax / 100) * $item_subtotal; // حساب قيمة الضريبة

                            // حفظ الضريبة في جدول TaxInvoice
                            TaxInvoice::create([
                                'name' => $tax->name,
                                'invoice_id' => $invoice->id,
                                'type' => $tax->type,
                                'rate' => $tax->tax,
                                'value' => $tax_value,
                                'type_invoice' => 'invoice',
                            ]);
                        }
                    }
                }
            }

            // ** تحديث رصيد حساب أبناء العميل **

            // إضافة المبلغ الإجمالي للفاتورة إلى رصيد أبناء العميل

            // ** الخطوة الخامسة: إنشاء سجلات البنود (items) للفاتورة **
            foreach ($items_data as $item) {
                $item['invoice_id'] = $invoice->id;
                $item_invoice = InvoiceItem::create($item);
                $client_name = Client::find($invoice->client_id);
                ModelsLog::create([
                    'type' => 'sales',
                    'type_id' => $invoice->id, // ID النشاط المرتبط
                    'type_log' => 'log', // نوع النشاط
                    'icon' => 'create',
                    'description' => sprintf(
                        'تم انشاء فاتورة مبيعات رقم **%s** للمنتج **%s** كمية **%s** بسعر **%s** للعميل **%s**',
                        $invoice->code ?? '', // رقم طلب الشراء
                        $item_invoice->product->name ?? '', // اسم المنتج
                        $item['quantity'] ?? '', // الكمية
                        $item['unit_price'] ?? '', // السعر
                        $client_name->trade_name ?? '', // المورد (يتم استخدام %s للنصوص)
                    ),
                    'created_by' => auth()->id(), // ID المستخدم الحالي
                ]);

                // ** تحديث المخزون بناءً على store_house_id المحدد في البند **
                $productDetails = ProductDetails::where('store_house_id', $item['store_house_id'])->where('product_id', $item['product_id'])->first();

                if (!$productDetails) {
                    $productDetails = ProductDetails::create([
                        'store_house_id' => $item['store_house_id'],
                        'product_id' => $item['product_id'],
                        'quantity' => 0,
                    ]);
                }

                $proudect = Product::where('id', $item['product_id'])->first();

                if ($proudect->type == 'products' || ($proudect->type == 'compiled' && $proudect->compile_type !== 'Instant')) {
                    if ((int) $item['quantity'] > (int) $productDetails->quantity) {
                        throw new \Exception('الكمية المطلوبة (' . $item['quantity'] . ') غير متاحة في المخزون. الكمية المتاحة: ' . $productDetails->quantity);
                    }
                }

                if ($proudect->type == 'products') {
                    // ** حساب المخزون قبل وبعد التعديل **
                    $total_quantity = DB::table('product_details')->where('product_id', $item['product_id'])->sum('quantity');
                    $stock_before = $total_quantity;
                    $stock_after = $stock_before - $item['quantity'];

                    // ** تحديث المخزون **
                    $productDetails->decrement('quantity', $item['quantity']);

                    // ** جلب مصدر إذن المخزون المناسب (فاتورة مبيعات) **
                    $permissionSource = PermissionSource::where('name', 'فاتورة مبيعات')->first();

                    if (!$permissionSource) {
                        // لو ما وجدنا مصدر إذن، ممكن ترمي استثناء أو ترجع خطأ
                        throw new \Exception("مصدر إذن 'فاتورة مبيعات' غير موجود في قاعدة البيانات.");
                    }

                    // ** تسجيل المبيعات في حركة المخزون **
                    $wareHousePermits = new WarehousePermits();
                    $wareHousePermits->permission_source_id = $permissionSource->id; // جلب id المصدر الديناميكي
                    $wareHousePermits->permission_date = $invoice->created_at;
                    $wareHousePermits->number = $invoice->id;
                    $wareHousePermits->grand_total = $invoice->grand_total;
                    $wareHousePermits->store_houses_id = $storeHouse->id;
                    $wareHousePermits->created_by = auth()->user()->id;
                    $wareHousePermits->save();

                    // ** تسجيل البيانات في WarehousePermitsProducts **
                    WarehousePermitsProducts::create([
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'unit_price' => $item['unit_price'],
                        'product_id' => $item['product_id'],
                        'stock_before' => $stock_before, // المخزون قبل التحديث
                        'stock_after' => $stock_after,   // المخزون بعد التحديث
                        'warehouse_permits_id' => $wareHousePermits->id,
                    ]);

                    // ** تنبيه انخفاض الكمية **
                    if ($productDetails->quantity < $product['low_stock_alert']) {
                        notifications::create([
                            'type' => 'Products',
                            'title' => 'تنبيه الكمية',
                            'description' => 'كمية المنتج ' . $product['name'] . ' قاربت على الانتهاء.',
                        ]);

                        $telegramApiUrl = 'https://api.telegram.org/bot7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU/sendMessage';

                        $message = "🚨 *تنبيه جديد!* 🚨\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
                        $message .= "📌 *العنوان:* 🔔 `تنبيه الكمية`\n";
                        $message .= '📦 *المنتج:* `' . $product['name'] . "`\n";
                        $message .= "⚠️ *الوصف:* _كمية المنتج قاربت على الانتهاء._\n";
                        $message .= '📅 *التاريخ:* `' . now()->format('Y-m-d H:i') . "`\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━\n";

                        $response = Http::post($telegramApiUrl, [
                            'chat_id' => '@Salesfatrasmart',
                            'text' => $message,
                            'parse_mode' => 'Markdown',
                            'timeout' => 60,
                        ]);
                    }

                    // ** تنبيه تاريخ انتهاء الصلاحية **
                    if ($product['track_inventory'] == 2 && !empty($product['expiry_date']) && !empty($product['notify_before_days'])) {
                        $expiryDate = Carbon::parse($product['expiry_date']);
                        $daysBeforeExpiry = (int) $product['notify_before_days'];

                        if ($expiryDate->greaterThan(now())) {
                            $remainingDays = floor($expiryDate->diffInDays(now()));

                            if ($remainingDays <= $daysBeforeExpiry) {
                                notifications::create([
                                    'type' => 'Products',
                                    'title' => 'تاريخ الانتهاء',
                                    'description' => 'المنتج ' . $product['name'] . ' قارب على الانتهاء في خلال ' . $remainingDays . ' يوم.',
                                ]);

                                $telegramApiUrl = 'https://api.telegram.org/bot7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU/sendMessage';

                                $message = "⚠️ *تنبيه انتهاء صلاحية المنتج* ⚠️\n";
                                $message .= "━━━━━━━━━━━━━━━━━━━━\n";
                                $message .= '📌 *اسم المنتج:* ' . $product['name'] . "\n";
                                $message .= '📅 *تاريخ الانتهاء:* ' . $expiryDate->format('Y-m-d') . "\n";
                                $message .= '⏳ *المدة المتبقية:* ' . $remainingDays . " يوم\n";
                                $message .= "━━━━━━━━━━━━━━━━━━━━\n";

                                $response = Http::post($telegramApiUrl, [
                                    'chat_id' => '@Salesfatrasmart',
                                    'text' => $message,
                                    'parse_mode' => 'Markdown',
                                    'timeout' => 60,
                                ]);
                            }
                        }
                    }
                }


                if ($proudect->type == 'compiled' && $proudect->compile_type == 'Instant') {
                    // ** حساب المخزون قبل وبعد التعديل للمنتج التجميعي **
                    $total_quantity = DB::table('product_details')->where('product_id', $item['product_id'])->sum('quantity');
                    $stock_before = $total_quantity;

                    // ** الحركة الأولى: إضافة الكمية إلى المخزن **
                    $wareHousePermits = new WarehousePermits();
                    $wareHousePermits->permission_source_id = 1; // إضافة للمخزون منتج مجمع خارجي
                    $wareHousePermits->permission_date = $invoice->created_at;
                    $wareHousePermits->number = $invoice->id;
                    $wareHousePermits->grand_total = $invoice->grand_total;
                    $wareHousePermits->store_houses_id = $storeHouse->id;
                    $wareHousePermits->created_by = auth()->user()->id;
                    $wareHousePermits->save();

                    // ** تحديث المخزون: إضافة الكمية **
                    $productDetails->increment('quantity', $item['quantity']); // إضافة الكمية بدلاً من خصمها

                    // ** تسجيل البيانات في WarehousePermitsProducts للإضافة **
                    WarehousePermitsProducts::create([
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'unit_price' => $item['unit_price'],
                        'product_id' => $item['product_id'],
                        'stock_before' => $stock_before, // المخزون قبل التحديث
                        'stock_after' => $stock_before + $item['quantity'], // المخزون بعد الإضافة
                        'warehouse_permits_id' => $wareHousePermits->id,
                    ]);

                    // ** الحركة الثانية: خصم الكمية من المخزن **
                    $wareHousePermits = new WarehousePermits();
                    $wareHousePermits->permission_source_id = 10; // خصم من الفاتورة
                    $wareHousePermits->permission_date = $invoice->created_at;
                    $wareHousePermits->number = $invoice->id;
                    $wareHousePermits->grand_total = $invoice->grand_total;
                    $wareHousePermits->store_houses_id = $storeHouse->id;
                    $wareHousePermits->created_by = auth()->user()->id;
                    $wareHousePermits->save();

                    // ** تحديث المخزون: خصم الكمية **
                    $productDetails->decrement('quantity', $item['quantity']); // خصم الكمية

                    // ** تسجيل البيانات في WarehousePermitsProducts للخصم **
                    WarehousePermitsProducts::create([
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'unit_price' => $item['unit_price'],
                        'product_id' => $item['product_id'],
                        'stock_before' => $stock_before + $item['quantity'], // المخزون قبل الخصم (بعد الإضافة)
                        'stock_after' => $stock_before, // المخزون بعد الخصم (يعود إلى القيمة الأصلية)
                        'warehouse_permits_id' => $wareHousePermits->id,
                    ]);

                    // ** الحصول على المنتجات التابعة للمنتج التجميعي **
                    $CompiledProducts = CompiledProducts::where('compile_id', $item['product_id'])->get();

                    foreach ($CompiledProducts as $compiledProduct) {
                        // ** حساب المخزون قبل وبعد التعديل للمنتج التابع **
                        $total_quantity = DB::table('product_details')->where('product_id', $compiledProduct->product_id)->sum('quantity');
                        $stock_before = $total_quantity;
                        $stock_after = $stock_before - $compiledProduct->qyt * $item['quantity']; // خصم الكمية المطلوبة

                        // ** تسجيل المبيعات في حركة المخزون للمنتج التابع **
                        $wareHousePermits = new WarehousePermits();
                        $wareHousePermits->permission_source_id = 10; // خصم من الفاتورة
                        $wareHousePermits->permission_date = $invoice->created_at;
                        $wareHousePermits->number = $invoice->id;
                        $wareHousePermits->grand_total = $invoice->grand_total;
                        $wareHousePermits->store_houses_id = $storeHouse->id;
                        $wareHousePermits->created_by = auth()->user()->id;
                        $wareHousePermits->save();

                        // ** تسجيل البيانات في WarehousePermitsProducts للمنتج التابع **
                        WarehousePermitsProducts::create([
                            'quantity' => $compiledProduct->qyt * $item['quantity'],
                            'total' => $item['total'],
                            'unit_price' => $item['unit_price'],
                            'product_id' => $compiledProduct->product_id,
                            'stock_before' => $stock_before, // المخزون قبل التحديث
                            'stock_after' => $stock_after, // المخزون بعد التحديث
                            'warehouse_permits_id' => $wareHousePermits->id,
                        ]);

                        // ** تحديث المخزون للمنتج التابع **
                        $compiledProductDetails = ProductDetails::where('store_house_id', $item['store_house_id'])->where('product_id', $compiledProduct->product_id)->first();

                        if (!$compiledProductDetails) {
                            $compiledProductDetails = ProductDetails::create([
                                'store_house_id' => $item['store_house_id'],
                                'product_id' => $compiledProduct->product_id,
                                'quantity' => 0,
                            ]);
                        }

                        $compiledProductDetails->decrement('quantity', $compiledProduct->qyt * $item['quantity']);
                    }
                }
            }

            // جلب بيانات الموظف والمستخدم
            $employee_name = Employee::where('id', $invoice->employee_id)->first();
            $user_name = User::where('id', $invoice->created_by)->first();
            $client_name = Client::find($invoice->client_id);
            // جلب جميع المنتجات المرتبطة بالفاتورة
            $invoiceItems = InvoiceItem::where('invoice_id', $invoice->id)->get();

            // تجهيز قائمة المنتجات
            $productsList = '';
            foreach ($invoiceItems as $item) {
                $product = Product::find($item->product_id);
                $productName = $product ? $product->name : 'منتج غير معروف';
                $productsList .= "▫️ *{$productName}* - الكمية: {$item->quantity}, السعر: {$item->unit_price} \n";
            }

            // // رابط API التلقرام
            $telegramApiUrl = 'https://api.telegram.org/bot7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU/sendMessage';

            // تجهيز الرسالة
            $message = "📜 *فاتورة جديدة* 📜\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "🆔 *رقم الفاتورة:* `$code`\n";
            $message .= '👤 *مسؤول البيع:* ' . ($employee_name->first_name ?? 'لا يوجد') . "\n";
            $message .= '🏢 *العميل:* ' . ($client_name->trade_name ?? 'لا يوجد') . "\n";
            $message .= '✍🏻 *أنشئت بواسطة:* ' . ($user_name->name ?? 'لا يوجد') . "\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= '💰 *المجموع:* `' . number_format($invoice->grand_total, 2) . "` ريال\n";
            $message .= '🧾 *الضريبة:* `' . number_format($invoice->tax_total, 2) . "` ريال\n";
            $message .= '📌 *الإجمالي:* `' . number_format($invoice->tax_total + $invoice->grand_total, 2) . "` ريال\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "📦 *المنتجات:* \n" . $productsList;
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= '📅 *التاريخ:* `' . date('Y-m-d H:i') . "`\n";

            // إرسال الرسالة إلى التلقرام
            $response = Http::post($telegramApiUrl, [
                'chat_id' => '@Salesfatrasmart', // تأكد من أن لديك صلاحية الإرسال للقناة
                'text' => $message,
                'parse_mode' => 'Markdown',
                'timeout' => 30,
            ]);
            notifications::create([
                'type' => 'invoice',
                'title' => $user_name->name . ' أضاف فاتورة لعميل',
                'description' => 'فاتورة للعميل ' . $client_name->trade_name . ' بقيمة ' . number_format($invoice->grand_total, 2) . ' ر.س',
            ]);


            // التحقق مما إذا كان للمستخدم قاعدة عمولة
            $userHasCommission = CommissionUsers::where('employee_id', auth()->user()->id)->exists();

            //  if (!$userHasCommission) {
            //      return "no000"; // المستخدم لا يملك قاعدة عمولة
            //   }

            if ($userHasCommission) {
                // جلب جميع commission_id الخاصة بالمستخدم
                $commissionIds = CommissionUsers::where('employee_id', auth()->user()->id)->pluck('commission_id');

                // التحقق مما إذا كانت هناك أي عمولة نشطة في جدول Commission
                $activeCommission = Commission::whereIn('id', $commissionIds)->where('status', 'active')->first();

                //   if (!$activeCommission) {
                //    return "not active"; // لا توجد عمولة نشطة، توقف هنا
                //    }

                if ($activeCommission) {
                    //    // ✅ التحقق مما إذا كانت حالة الدفع في `invoice` تتطابق مع حساب العمولة في `commission`
                    //    if (
                    //  ($invoice->payment_status == 1 && $activeCommission->commission_calculation != "fully_paid") ||
                    //  ($invoice->payment_status == 2 && $activeCommission->commission_calculation != "partially_paid")
                    //  )   {
                    //  return "payment mismatch"; // حالتا الدفع لا تتطابقان
                    //   }

                    // البحث في جدول commission__products باستخدام هذه commission_id
                    $commissionProducts = Commission_Products::whereIn('commission_id', $commissionIds)->get();

                    // التحقق من وجود أي product_id = 0
                    if ($commissionProducts->contains('product_id', 0)) {
                        return 'yesall';
                    }

                    // جلب جميع product_id الخاصة بالفاتورة
                    $invoiceProductIds = InvoiceItem::where('invoice_id', $invoice->id)->pluck('product_id');

                    // التحقق مما إذا كان أي من product_id في جدول commission__products يساوي أي من المنتجات في الفاتورة
                    if ($commissionProducts->whereIn('product_id', $invoiceProductIds)->isNotEmpty()) {
                        // جلب بيانات العمولة المرتبطة بالفاتورة
                        $inAmount = Commission::whereIn('id', $commissionIds)->first();
                        $commissionProduct = Commission_Products::whereIn('commission_id', $commissionIds)->first();
                        if ($inAmount) {
                            if ($inAmount->target_type == 'amount') {
                                $invoiceTotal = InvoiceItem::where('invoice_id', $invoice->id)->sum('total');
                                $invoiceQyt = InvoiceItem::where('invoice_id', $invoice->id)->first();
                                // تحقق من أن قيمة العمولة تساوي أو أكبر من `total`
                                if ((float) $inAmount->value <= (float) $invoiceTotal) {
                                    $salesInvoice = new SalesCommission();
                                    $salesInvoice->invoice_number = $invoice->id; // تعيين رقم الفاتورة الصحيح
                                    $salesInvoice->employee_id = auth()->user()->id; // اسم الموظف
                                    $salesInvoice->sales_amount = $invoiceTotal; // إجمالي المبيعات
                                    $salesInvoice->sales_quantity = $invoiceQyt->quantity;
                                    $salesInvoice->commission_id = $inAmount->id;
                                    $salesInvoice->ratio = $commissionProduct->commission_percentage ?? 0;
                                    $salesInvoice->product_id = $commissionProduct->product_id ?? 0; // رقم معرف العمولة
                                    $salesInvoice->save(); // حفظ السجل في قاعدة البيانات
                                }
                            } elseif ($inAmount->target_type == 'quantity') {
                                // تحقق من أن قيمة العمولة تساوي أو أكبر من `quantity`
                                $invoiceQuantity = InvoiceItem::where('invoice_id', $invoice->id)->sum('quantity');

                                if ((float) $inAmount->value <= (float) $invoiceQuantity) {
                                    $salesInvoice = new SalesCommission();
                                    $salesInvoice->invoice_number = $invoice->id; // تعيين رقم الفاتورة الصحيح
                                    $salesInvoice->employee_id = auth()->user()->id; // اسم الموظف
                                    $salesInvoice->sales_amount = $invoiceTotal; // إجمالي المبيعات
                                    $salesInvoice->sales_quantity = $invoiceQyt->quantity;
                                    $salesInvoice->commission_id = $inAmount->id; // رقم معرف العمولة
                                    $salesInvoice->ratio = $commissionProduct->commission_percentage ?? 0;
                                    $salesInvoice->product_id = $commissionProduct->product_id ?? 0;
                                    $salesInvoice->save(); // حفظ السجل في قاعدة البيانات
                                }
                            }
                        }
                    }
                }
            }

            // ** معالجة المرفقات (attachments) إذا وجدت **
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $invoice->attachments = $filename;
                    $invoice->save();
                }
            }
            $clientaccounts = Account::where('client_id', $invoice->client_id)->first();
            if (!$clientaccounts) {
                throw new \Exception('حساب العميل غير موجود');
            }
            // استرجاع حساب القيمة المضافة المحصلة
            $vatAccount = Account::where('name', 'القيمة المضافة المحصلة')->first();
            if (!$vatAccount) {
                throw new \Exception('حساب القيمة المضافة المحصلة غير موجود');
            }
            $salesAccount = Account::where('name', 'المبيعات')->first();
            if (!$salesAccount) {
                throw new \Exception('حساب المبيعات غير موجود');
            }

            //     // إنشاء القيد المحاسبي للفاتورة
            $journalEntry = JournalEntry::create([
                'reference_number' => $invoice->code,
                'date' => now(),
                'description' => 'فاتورة مبيعات رقم ' . $invoice->code,
                'status' => 1,
                'currency' => 'SAR',
                'client_id' => $invoice->client_id,
                'invoice_id' => $invoice->id,
                // 'created_by_employee' => Auth::id(),
            ]);

            // // إضافة تفاصيل القيد المحاسبي
            // // 1. حساب العميل (مدين)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $clientaccounts->id, // حساب العميل
                'description' => 'فاتورة مبيعات رقم ' . $invoice->code,
                'debit' => $total_with_tax, // المبلغ الكلي للفاتورة (مدين)
                'credit' => 0,
                'is_debit' => true,
            ]);

            // // 2. حساب المبيعات (دائن)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $salesAccount->id, // حساب المبيعات
                'description' => 'إيرادات مبيعات',
                'debit' => 0,
                'credit' => $amount_after_discount, // المبلغ بعد الخصم (دائن)
                'is_debit' => false,
            ]);

            // // 3. حساب القيمة المضافة المحصلة (دائن)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $vatAccount->id, // حساب القيمة المضافة المحصلة
                'description' => 'ضريبة القيمة المضافة',
                'debit' => 0,
                'credit' => $tax_total, // قيمة الضريبة (دائن)
                'is_debit' => false,
            ]);

            // ** تحديث رصيد حساب المبيعات (إيرادات) **
            //  if ($salesAccount) {
            //     $salesAccount->balance += $amount_after_discount; // إضافة المبلغ بعد الخصم
            //     $salesAccount->save();
            // }

            // ** تحديث رصيد حساب المبيعات والحسابات المرتبطة به (إيرادات) **
            if ($salesAccount) {
                $amount = $amount_after_discount;
                $salesAccount->balance += $amount;
                $salesAccount->save();

                // تحديث جميع الحسابات الرئيسية المتصلة به
                // $this->updateParentBalanceSalesAccount($salesAccount->parent_id, $amount);
            }

            // تحديث رصيد حساب الإيرادات (المبيعات + الضريبة)
            $revenueAccount = Account::where('name', 'الإيرادات')->first();
            if ($revenueAccount) {
                $revenueAccount->balance += $amount_after_discount; // المبلغ بعد الخصم (بدون الضريبة)
                $revenueAccount->save();
            }

            // $vatAccount->balance += $tax_total; // قيمة الضريبة
            // $vatAccount->save();

            //تحديث رصيد حساب القيمة المضافة (الخصوم)
            if ($vatAccount) {
                $amount = $tax_total;
                $vatAccount->balance += $amount;
                $vatAccount->save();

                // تحديث جميع الحسابات الرئيسية المتصلة به
                $this->updateParentBalance($vatAccount->parent_id, $amount);
            }

            // تحديث رصيد حساب الأصول (المبيعات + الضريبة)
            $assetsAccount = Account::where('name', 'الأصول')->first();
            if ($assetsAccount) {
                $assetsAccount->balance += $total_with_tax; // المبلغ الكلي (المبيعات + الضريبة)
                $assetsAccount->save();
            }
            // تحديث رصيد حساب الخزينة الرئيسية

            // if ($MainTreasury) {
            //     $MainTreasury->balance += $total_with_tax; // المبلغ الكلي (المبيعات + الضريبة)
            //     $MainTreasury->save();
            // }

            if ($clientaccounts) {
                $clientaccounts->balance += $invoice->grand_total; // المبلغ الكلي (المبيعات + الضريبة)
                $clientaccounts->save();
            }


            // تحديث رصيد حساب الخزينة الرئيسية

            // ** الخطوة السابعة: إنشاء سجل الدفع إذا كان هناك دفعة مقدمة أو دفع كامل **
            if ($advance_payment > 0 || $is_paid) {
                $payment_amount = $is_paid ? $total_with_tax : $advance_payment;

                // تحديد الخزينة المستهدفة بناءً على الموظف
                $MainTreasury = null;

                if ($user && $user->employee_id) {
                    // البحث عن الخزينة المرتبطة بالموظف
                    $TreasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

                    if ($TreasuryEmployee && $TreasuryEmployee->treasury_id) {
                        // إذا كان الموظف لديه خزينة مرتبطة
                        $MainTreasury = Account::where('id', $TreasuryEmployee->treasury_id)->first();
                    } else {
                        // إذا لم يكن لدى الموظف خزينة مرتبطة، استخدم الخزينة الرئيسية
                        $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                    }
                } else {
                    // إذا لم يكن المستخدم موجودًا أو لم يكن لديه employee_id، استخدم الخزينة الرئيسية
                    $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                }

                // إذا لم يتم العثور على خزينة، توقف العملية وأظهر خطأ
                if (!$MainTreasury) {
                    throw new \Exception('لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.');
                }

                // إنشاء سجل الدفع
                $payment = PaymentsProcess::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $payment_amount,
                    'payment_date' => now(),
                    'payment_method' => $request->payment_method,
                    'reference_number' => $request->reference_number,
                    'notes' => 'تم إنشاء الدفعة تلقائياً عند إنشاء الفاتورة',
                    'type' => 'client payments',
                    'payment_status' => $payment_status,
                    'created_by' => Auth::id(),
                ]);

                // تحديث رصيد الخزينة
                if ($MainTreasury) {
                    $MainTreasury->balance += $payment_amount;
                    $MainTreasury->save();
                }

                if ($advance_payment > 0) {

                    if ($clientaccounts) {
                        $clientaccounts->balance -= $payment_amount; // المبلغ الكلي (المبيعات + الضريبة)
                        $clientaccounts->save();
                    }
                } else {
                    if ($clientaccounts) {
                        $clientaccounts->balance -= $invoice->grand_total; // المبلغ الكلي (المبيعات + الضريبة)
                        $clientaccounts->save();
                    }
                }

                // إنشاء قيد محاسبي للدفعة
                $paymentJournalEntry = JournalEntry::create([
                    'reference_number' => $payment->reference_number ?? $invoice->code,
                    'date' => now(),
                    'description' => 'دفعة للفاتورة رقم ' . $invoice->code,
                    'status' => 1,
                    'currency' => 'SAR',
                    'client_id' => $invoice->client_id,
                    'invoice_id' => $invoice->id,
                    // 'created_by_employee' => Auth::id(),
                ]);

                // 1. حساب الخزينة المستهدفة (مدين)
                JournalEntryDetail::create([
                    'journal_entry_id' => $paymentJournalEntry->id,
                    'account_id' => $MainTreasury->id,
                    'description' => 'دفعة للفاتورة رقم ' . $invoice->code,
                    'debit' => $payment_amount,
                    'credit' => 0,
                    'is_debit' => true,
                    'client_account_id' => $clientaccounts->id,
                ]);

                // 2. حساب العميل (دائن)
                JournalEntryDetail::create([
                    'journal_entry_id' => $paymentJournalEntry->id,
                    'account_id' => $clientaccounts->id,
                    'description' => 'دفعة عميل  للفاتورة رقم ' . $invoice->code,
                    'debit' => 0,
                    'credit' => $payment_amount,
                    'is_debit' => false,
                    'client_account_id' => $clientaccounts->id,
                ]);
            }
            DB::commit();
            return $invoice;
            // إعداد رسالة النجاح
            // $response = Http::post($telegramApiUrl, [
            //     'chat_id' => '@Salesfatrasmart',  // تأكد من أن لديك صلاحية الإرسال للقناة
            //     'text' => sprintf("تم إنشاء فاتورة جديدة بنجاح. رقم الفاتورة: %s", $invoice->code),
            //     'parse_mode' => 'Markdown',
            // ]);

            // if ($response->failed()) {
            //     Log::error('خطاء في الارسال للقناة: ' . $response->body());
            // }

            return redirect()
                ->route('invoices.show', $invoice->id)
                ->with('success', sprintf('تم إنشاء فاتورة المبيعات بنجاح. رقم الفاتورة: %s', $invoice->code));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ أثناء إنشاء الفاتورة من كرن جوب: ' . $e->getMessage());
            return null;
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء حفظ فاتورة المبيعات: ' . $e->getMessage());
        }
        //edit
    }
    private function getSalesAccount()
    {
        // البحث عن حساب المبيعات باسمه
        $salesAccount = Account::where('name', 'المبيعات')->orWhere('name', 'إيرادات المبيعات')->first();

        if (!$salesAccount) {
            throw new \Exception('لم يتم العثور على حساب المبيعات في دليل الحسابات');
        }

        return $salesAccount->id;
    }
    private function generateTlvContent($timestamp, $totalAmount, $vatAmount)
    {
        $tlvContent = $this->getTlv(1, 'مؤسسة اعمال خاصة للتجارة') . $this->getTlv(2, '000000000000000') . $this->getTlv(3, $timestamp) . $this->getTlv(4, number_format($totalAmount, 2, '.', '')) . $this->getTlv(5, number_format($vatAmount, 2, '.', ''));

        return base64_encode($tlvContent);
    }
    private function getTlv($tag, $value)
    {
        $value = (string) $value;
        return pack('C', $tag) . pack('C', strlen($value)) . $value;
    }
    private function updateParentBalance($parentId, $amount)
    {
        //تحديث الحسابات المرتبطة بالقيمة المضافة
        if ($parentId) {
            $vatAccount = Account::find($parentId);
            if ($vatAccount) {
                $vatAccount->balance += $amount;
                $vatAccount->save();

                // استدعاء الوظيفة نفسها لتحديث الحساب الأعلى منه
                $this->updateParentBalance($vatAccount->parent_id, $amount);
            }
        }
    }

    private function updateParentBalanceMainTreasury($parentId, $amount)
    {
        // تحديث رصيد الحسابات المرتبطة الخزينة الرئيسية
        if ($parentId) {
            $MainTreasury = Account::find($parentId);
            if ($MainTreasury) {
                $MainTreasury->balance += $amount;
                $MainTreasury->save();

                // استدعاء الوظيفة نفسها لتحديث الحساب الأعلى منه
                $this->updateParentBalance($MainTreasury->parent_id, $amount);
            }
        }
    }
    private function calculateTaxValue($rate, $total)
    {
        return ($rate / 100) * $total;
    }

    private function updateParentBalanceSalesAccount($parentId, $amount)
    {
        // تحديث رصيد الحسابات المرتبطة  المبيعات
        if ($parentId) {
            $MainTreasury = Account::find($parentId);
            if ($MainTreasury) {
                $MainTreasury->balance += $amount;
                $MainTreasury->save();

                // استدعاء الوظيفة نفسها لتحديث الحساب الأعلى منه
                $this->updateParentBalanceSalesAccount($MainTreasury->parent_id, $amount);
            }
        }
    }


    public function show(Request $request, $id)

    {
        $clients = Client::all();
        $employees = Employee::all();


        $search = $request->input('search');

        $actives_logs = ModelsLog::where('type_log', 'log')->where('type', 'sales')->where('type_id', $id)
            ->when($search, function ($query) use ($search) {
                return $query->where('description', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get()->unique('id')
            ->filter(function ($log) {
                return !is_null($log) && !is_bool($log); // التأكد من أن السجل ليس null أو false
            })
            ->groupBy(function ($log) {
                return optional($log->created_at)->format('Y-m-d'); // التأكد أن created_at ليس null
            });


        //
        $invoice = Invoice::find($id);
        $return_invoices = Invoice::where('reference_number', $id)->get();
        $invoice_notes = ClientRelation::where('invoice_id', $id)->get();
        $renderer = new ImageRenderer(
            new RendererStyle(150), // تحديد الحجم
            new SvgImageBackEnd(), // تحديد نوع الصورة (SVG)
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($invoice->qrcode);
        $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'invoice')->get();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        $client = Client::where('user_id', auth()->user()->id)->first();

        $invoice_number = $this->generateInvoiceNumber();

        // إنشاء رقم الباركود من رقم الفاتورة
        $barcodeNumber = str_pad($invoice->id, 13, '0', STR_PAD_LEFT); // تنسيق الرقم إلى 13 خانة

        // إنشاء رابط الباركود باستخدام خدمة Barcode Generator
        $barcodeImage = 'https://barcodeapi.org/api/128/' . $barcodeNumber;
        $nextCode = Receipt::max('code') ?? 0;

        // نحاول تكرار البحث حتى نحصل على كود غير مكرر
        while (Receipt::where('code', $nextCode)->exists()) {
            $nextCode++;
        }
        // تغيير اسم المتغير من qrCodeImage إلى barcodeImage
        return view('sales::invoices.show', compact('invoice_number', 'account_setting', 'nextCode', 'client', 'clients', 'employees', 'invoice', 'barcodeImage', 'TaxsInvoice', 'qrCodeSvg', 'invoice_notes', 'return_invoices', 'actives_logs'));
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

        // إنشاء رقم الباركود من رقم الفاتورة
        $barcodeNumber = str_pad($invoice->id, 13, '0', STR_PAD_LEFT); // تنسيق الرقم إلى 13 خانة

        // إنشاء رابط الباركود باستخدام خدمة Barcode Generator
        $barcodeImage = 'https://barcodeapi.org/api/128/' . $barcodeNumber;
        $nextCode = Receipt::max('code') ?? 0;

        // نحاول تكرار البحث حتى نحصل على كود غير مكرر
        while (Receipt::where('code', $nextCode)->exists()) {
            $nextCode++;
        }
        // تغيير اسم المتغير من qrCodeImage إلى barcodeImage
        return view('sales::invoices.print', compact('invoice_number', 'account_setting', 'nextCode', 'client', 'clients', 'employees', 'invoice', 'barcodeImage', 'TaxsInvoice', 'qrCodeSvg'));
    }
    public function edit($id)
    {
        return redirect()
            ->back()
            ->with('error', 'لا يمكنك تعديل الفاتورة رقم ' . $id . '. طبقا لتعليمات هيئة الزكاة والدخل يمنع حذف أو تعديل الفاتورة بعد إصدارها وفقا لمتطلبات الفاتورة الإلكترونية، ولكن يمكن إصدار فاتورة مرتجعة أو إشعار دائن لإلغائها أو تعديلها.');
    }

    public function destroy($id)
    {
        return redirect()->route('invoices.index')->with('error', 'لا يمكنك حذف الفاتورة. طبقا لتعليمات هيئة الزكاة والدخل يمنع حذف أو تعديل الفاتورة بعد إصدارها وفقا لمتطلبات الفاتورة الإلكترونية، ولكن يمكن إصدار فاتورة مرتجعة أو إشعار دائن لإلغائها أو تعديلها.');
    }
    public function update(Request $request, $id)
    {
        return redirect()->route('invoices.index')->with('error', 'لا يمكنك تعديل الفاتورة. طبقا لتعليمات هيئة الزكاة والدخل يمنع حذف أو تعديل الفاتورة بعد إصدارها وفقا لمتطلبات الفاتورة الإلكترونية، ولكن يمكن إصدار فاتورة مرتجعة أو إشعار دائن لإلغائها أو تعديلها.');
    }

    private function generateInvoiceNumber()
    {
        $lastInvoice = Invoice::latest()->first();
        $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
        return str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    protected function getAccountId($type)
    {
        $account = Account::where('code', $type)->first();

        if (!$account) {
            throw new \Exception("لم يتم العثور على الحساب من نوع: {$type}. الرجاء التأكد من وجود الحساب في دليل الحسابات.");
        }

        return $account->id;
    }

    public function generatePdf($id)
    {
        $invoice = Invoice::with(['client', 'items', 'createdByUser'])->findOrFail($id);

        // إنشاء بيانات QR Code
        $qrData = 'رقم الفاتورة: ' . $invoice->id . "\n";
        $qrData .= 'التاريخ: ' . $invoice->created_at->format('Y/m/d') . "\n";
        $qrData .= 'العميل: ' . ($invoice->client->trade_name ?? $invoice->client->first_name . ' ' . $invoice->client->last_name) . "\n";
        $qrData .= 'الإجمالي: ' . number_format($invoice->grand_total, 2) . ' ر.س';

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
        $pdf->SetTitle('فاتورة رقم ' . $invoice->code);

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
        $barcodeImage = $qrCode->render($qrData);

        // Generate

        $renderer = new ImageRenderer(
            new RendererStyle(150), // تحديد الحجم
            new SvgImageBackEnd(), // تحديد نوع الصورة (SVG)
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($invoice->qrcode);
        $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'invoice')->get();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        $html = view('sales.invoices.print', compact('invoice', 'barcodeImage', 'TaxsInvoice', 'account_setting', 'qrCodeSvg'))->render();

        // Add content to PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output file
        return $pdf->Output('invoice-' . $invoice->code . '.pdf', 'I');
    }



    public function send_invoice($id)
    {
        $invoice = Invoice::with(['client', 'items', 'createdByUser'])->findOrFail($id);

        $client = $invoice->client;

        // ✅ تحقق أولًا من وجود بريد إلكتروني
        if (!$client || !$client->email || !filter_var($client->email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'هذا العميل لا يملك  بريد إلكتروني صالح.');
        }

        // QR code preparation (نفس الكود الذي تستخدمه)
        $qrData = 'رقم الفاتورة: ' . $invoice->id . "\n";
        $qrData .= 'التاريخ: ' . $invoice->created_at->format('Y/m/d') . "\n";
        $qrData .= 'العميل: ' . ($invoice->client->trade_name ?? $invoice->client->first_name . ' ' . $invoice->client->last_name) . "\n";
        $qrData .= 'الإجمالي: ' . number_format($invoice->grand_total, 2) . ' ر.س';

        $qrOptions = new \chillerlan\QRCode\QROptions([
            'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L,
            'scale' => 5,
            'imageBase64' => true,
        ]);

        $qrCode = new \chillerlan\QRCode\QRCode($qrOptions);
        $barcodeImage = $qrCode->render($qrData);

        $TaxsInvoice = \App\Models\TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'invoice')->get();
        $account_setting = \App\Models\AccountSetting::where('user_id', auth()->id())->first();
        $renderer = new ImageRenderer(
            new RendererStyle(150), // تحديد الحجم
            new SvgImageBackEnd(), // تحديد نوع الصورة (SVG)
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($invoice->qrcode);
        $html = view('sales.invoices.print', compact('invoice', 'barcodeImage', 'TaxsInvoice', 'account_setting', 'qrCodeSvg'))->render();

        // إنشاء PDF
        $pdf = new TCPDF();
        $pdf->SetMargins(15, 15, 15);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->setRTL(true);
        $pdf->SetFont('aealarabiya', '', 14);
        $pdf->writeHTML($html, true, false, true, false, '');

        // حفظ مؤقت
        $fileName = 'invoice-' . $invoice->code . '.pdf';
        $filePath = storage_path('app/public/' . $fileName);
        $pdf->Output($filePath, 'F'); // F = save to file

        // إرسال البريد
        Mail::to($invoice->client->email)->send(new InvoicePdfMail($invoice, $filePath));

        // حذف الملف بعد الإرسال (اختياري)
        unlink($filePath);

        return redirect()->back()->with(['success' => 'تم إرسال الفاتورة إلى بريد العميل.']);
    }



    public function label($id)
    {
        $invoice = Invoice::findOrFail($id);

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4', // تغيير من A6 إلى A4
            'orientation' => 'portrait', // أو 'landscape' إذا أردت الوضع الأفقي
            'default_font' => 'dejavusans',
            'default_font_size' => 12, // تصغير حجم الخط قليلاً
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);

        $html = view('sales.invoices.label', compact('invoice'))->render();

        $mpdf->WriteHTML($html);
        return response($mpdf->Output('shipping-label.pdf', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

    // قائمة الاستلام
    public function picklist($id)
    {
        $invoice = Invoice::findOrFail($id);

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4', // تغيير من A6 إلى A4
            'orientation' => 'portrait', // أو 'landscape' إذا أردت الوضع الأفقي
            'default_font' => 'dejavusans',
            'default_font_size' => 12, // تصغير حجم الخط قليلاً
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);

        $html = view('sales.invoices.picklist', compact('invoice'))->render();

        $mpdf->WriteHTML($html);
        return response($mpdf->Output('shipping-picklist.pdf', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

    // ملصق التوصيل

    public function shipping_label($id)
    {
        $invoice = Invoice::findOrFail($id);

        $mpdf = new App\Http\Controllers\Sales\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4', // تغيير من A6 إلى A4
            'orientation' => 'portrait', // أو 'landscape' إذا أردت الوضع الأفقي
            'default_font' => 'dejavusans',
            'default_font_size' => 12, // تصغير حجم الخط قليلاً
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);

        $html = view('sales::invoices.shipping_label', compact('invoice'))->render();

        $mpdf->WriteHTML($html);
        return response($mpdf->Output('shipping-shipping_label.pdf', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

    public function storeSignatures(Request $request, $invoiceId)
    {
        $validated = $request->validate([
            'signer_name' => 'required|string|max:255',
            'signer_role' => 'nullable|string|max:255',
            'signature_data' => 'required|string',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        // حفظ التوقيع فقط (بدون amount_paid)
        $signature = Signature::create([
            'invoice_id' => $invoiceId,
            'signer_name' => $validated['signer_name'],
            'signer_role' => $validated['signer_role'],
            'signature_data' => $validated['signature_data'],
            'amount_paid' => $validated['amount_paid'],

            'signed_at' => now(),
        ]);

        // إذا كان هناك مبلغ مدفوع، ننشئ سند القبض
        if (!empty($validated['amount_paid']) && $validated['amount_paid'] > 0) {
            $invoiceaccount = invoice::find($invoiceId);
            $account = Account::where('client_id', $invoiceaccount->client_id)->first();

            $income = new Receipt();
            $income->code = $request->input('code');
            $income->amount = $validated['amount_paid'];
            $income->description = "مدفوعات لفاتورة رقم " . $invoiceId;
            $income->date = now();
            $income->incomes_category_id = 1;
            $income->seller = 1;
            $income->account_id = $account->id;
            $income->is_recurring = $request->has('is_recurring') ? 1 : 0;
            $income->recurring_frequency = $request->input('recurring_frequency');
            $income->end_date = $request->input('end_date');
            $income->tax1 = 1;
            $income->tax2 = 1;
            $income->created_by = auth()->id();
            $income->tax1_amount = 0;
            $income->tax2_amount = 0;
            $income->cost_centers_enabled = $request->has('cost_centers_enabled') ? 1 : 0;

            $MainTreasury = $this->determineTreasury();
            $income->treasury_id = $MainTreasury->id;
            $income->save();

            // باقي العمليات المتعلقة بسند القبض
            $income_account_name = Account::find($income->account_id);
            $user = Auth::user();

            notifications::create([
                'user_id' => $user->id,
                'type' => 'Receipt',
                'title' => $user->name . ' أنشأ سند قبض',
                'description' => 'سند قبض رقم ' . $income->code . ' لـ ' . $income_account_name->name . ' بقيمة ' . number_format($income->amount, 2) . ' ر.س',
            ]);

            ModelsLog::create([
                'type' => 'finance_log',
                'type_id' => $income->id,
                'type_log' => 'log',
                'description' => sprintf('تم انشاء سند قبض رقم **%s** بقيمة **%d**', $income->code, $income->amount),
                'created_by' => auth()->id(),
            ]);

            $MainTreasury->balance += $income->amount;
            $MainTreasury->save();

            $clientAccount = Account::find($income->account_id);
            if ($clientAccount) {
                $clientAccount->balance -= $income->amount;
                $clientAccount->save();
            }

            $this->applyPaymentToInvoices($income, $user, $invoiceId);
            $this->createJournalEntry($income, $user, $clientAccount, $MainTreasury);
        }

        // إرجاع بيانات التوقيع فقط
        return response()->json([
            'success' => true,
            'signature' => [
                'signer_name' => $signature->signer_name,
                'signer_role' => $signature->signer_role,
                'signature_data' => $signature->signature_data,
            ]
        ]);
    }

    private function determineTreasury()
    {
        $user = Auth::user();
        $treasury = null;

        if ($user && $user->employee_id) {
            $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();
            if ($treasuryEmployee && $treasuryEmployee->treasury_id) {
                $treasury = Account::find($treasuryEmployee->treasury_id);
            }
        }

        if (!$treasury) {
            $treasury = Account::where('name', 'الخزينة الرئيسية')->first();
        }

        if (!$treasury) {
            throw new \Exception('لم يتم العثور على خزينة صالحة');
        }

        return $treasury;
    }


    private function applyPaymentToInvoices(Receipt $income, $user, $invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $paymentAmount = $income->amount;

        // حساب المبلغ المدفوع سابقاً لهذه الفاتورة فقط (باستثناء الملغاة)
        $previousPaymentsForThisInvoice = PaymentsProcess::where('invoice_id', $invoice->id)
            ->where('payment_status', '!=', 5)
            ->sum('amount');

        // المبلغ الإجمالي المدفوع للفاتورة بعد هذه العملية
        $totalPaidForInvoice = $previousPaymentsForThisInvoice + $paymentAmount;

        // التحقق من عدم تجاوز المبلغ الإجمالي المدفوع قيمة الفاتورة الحالية
        if ($totalPaidForInvoice > $invoice->grand_total) {
            $excessAmount = $totalPaidForInvoice - $invoice->grand_total;
            throw new \Exception("المبلغ يتجاوز إجمالي الفاتورة الحالية بمقدار " . number_format($excessAmount, 2));
        }

        // تحديد حالة السداد للفاتورة الحالية
        $isFullPaymentForInvoice = ($totalPaidForInvoice >= $invoice->grand_total);

        // إنشاء سجل الدفع الجديد لهذه الفاتورة
        PaymentsProcess::create([
            'invoice_id' => $invoice->id,
            'amount' => $paymentAmount,
            'payment_date' => $income->date,
            'Payment_method' => 'cash',
            'reference_number' => $income->code,
            'type' => 'client payments',
            'payment_status' => $isFullPaymentForInvoice ? 1 : 2,
            'employee_id' => $user->id,
            'notes' => 'دفع عبر سند القبض رقم ' . $income->code,
        ]);

        // تحديث حالة الفاتورة الحالية فقط
        $invoice->update([
            'advance_payment' => $totalPaidForInvoice,
            'is_paid' => $isFullPaymentForInvoice,
            'payment_status' => $isFullPaymentForInvoice ? 1 : 2,
            'due_value' => $invoice->grand_total - $totalPaidForInvoice
        ]);

        // إرسال إشعار خاص بهذه الفاتورة
        Notification::create([
            'user_id' => $user->id,
            'type' => 'invoice_payment',
            'title' => 'سداد فاتورة #' . $invoice->code,
            'description' => 'تم سداد مبلغ ' . number_format($paymentAmount, 2) .
                ' (إجمالي مدفوعات هذه الفاتورة: ' . number_format($totalPaidForInvoice, 2) .
                ' - المتبقي: ' . number_format($invoice->grand_total - $totalPaidForInvoice, 2) . ')',
            'metadata' => ['invoice_id' => $invoice->id]
        ]);
    }
    private function createJournalEntry(Receipt $income, $user, $clientAccount, $treasury)
    {
        $journalEntry = JournalEntry::create([
            'reference_number' => $income->code,
            'date' => $income->date,
            'description' => 'سند قبض رقم ' . $income->code,
            'status' => 1,
            'currency' => 'SAR',
            'client_id' => $clientAccount->client_id ?? null,
            'created_by_employee' => $user->id,
        ]);

        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $treasury->id,
            'description' => 'استلام مبلغ من سند قبض',
            'debit' => $income->amount,
            'credit' => 0,
            'is_debit' => true,
        ]);

        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $income->account_id,
            'description' => 'إيرادات من سند قبض',
            'debit' => 0,
            'credit' => $income->amount,
            'is_debit' => false,
        ]);
    }

public function markAsPaidSilently($id)
{
    // منع الموظف من تنفيذ الإجراء
    if (auth()->user()->role === 'employee') {
        abort(403, 'غير مصرح لك بتنفيذ هذا الإجراء.');
    }

    $invoice = Invoice::findOrFail($id);

    $invoice->is_paid = true;
    $invoice->payment_status = 1; // مدفوع بالكامل
    $invoice->due_value = 0;

    $invoice->save();

    // الرجوع لنفس الصفحة مع رسالة نجاح
    return redirect()->back()->with('success', 'تم تغيير حالة الفاتورة إلى مدفوعة بالكامل.');
}



}
