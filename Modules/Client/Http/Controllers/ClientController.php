<?php

namespace Modules\Client\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use App\Http\Controllers\Controller;

use App\Imports\ClientsImport;
use App\Models\Account;
use App\Models\Appointment;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use App\Models\AppointmentNote;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\CategoriesClient;
use App\Models\ClientRelation;
use App\Models\GeneralClientSetting;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Memberships;
use App\Models\Neighborhood;
use App\Models\AccountSetting;
use App\Models\Region_groub;
use App\Models\Package;
use App\Models\PaymentsProcess;
use App\Models\SerialSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use App\Mail\SendPasswordEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\notifications;
use App\Mail\TestMail;
use App\Models\ClientEmployee;
use App\Models\Statuses;
use App\Models\CreditLimit;
use App\Models\EmployeeClientVisit;
use App\Models\EmployeeGroup;
use App\Models\Expense;
use App\Models\HiddenClient;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Location;
use App\Models\Receipt;
use App\Models\Revenue;
use App\Models\Setting;
use App\Models\Target;
use Carbon\Carbon;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Client\Http\Requests\ClientRequest;

class ClientController extends Controller
{
    // ... existing methods ...

    public function getClientDetails($id)
    {
        $client = Client::with(['status', 'neighborhood', 'branch'])->find($id);
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        return response()->json(['client' => $client]);
    }

    public function getClientInvoices($id)
    {
        $invoices = Invoice::where('client_id', $id)
            ->with(['status', 'payments'])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json(['invoices' => $invoices]);
    }

    public function getClientNotes($id)
    {
        $notes = ClientRelation::where('client_id', $id)
            ->with(['createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['notes' => $notes]);
    }

    public function searchClients(Request $request)
    {
        $term = $request->term;

        $clients = Client::where(function ($query) use ($term) {
            $query
                ->where('trade_name', 'like', '%' . $term . '%')
                ->orWhere('email', 'like', '%' . $term . '%')
                ->orWhere('code', 'like', '%' . $term . '%')
                ->orWhere('phone', 'like', '%' . $term . '%');
        })
            ->with(['status', 'neighborhood'])
            ->limit(10)
            ->get();

        return response()->json(['clients' => $clients]);
    }
    public function needsModification(Request $request)
    {
        $employeeId = $request->employee_id;
        $year = $request->year;
        $week = $request->week;

        $clients = Client::whereHas('employeeClientVisit', function ($query) use ($employeeId, $year, $week) {
            $query->where('employee_id', $employeeId)->where('year', $year)->where('week_number', $week)->where('needs_modification', true);
        })
            ->with(['neighborhood', 'employeeClientVisit'])
            ->get();

        return response()->json(['data' => $clients]);
    }

    public function currentRoute(Request $request)
    {
        $employeeId = $request->employee_id;
        $year = $request->year;
        $week = $request->week;

        $clients = Client::whereHas('employeeClientVisit', function ($query) use ($employeeId, $year, $week) {
            $query->where('employee_id', $employeeId)->where('year', $year)->where('week_number', $week);
        })
            ->with(['neighborhood', 'employeeClientVisit'])
            ->get();

        return response()->json(['data' => $clients]);
    }

   private function applyFilters($baseQuery, $request)
    {
        if ($request->filled('client')) {
            $baseQuery->where('id', $request->client);
        }

        if ($request->filled('name')) {
            $baseQuery->where('trade_name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('status')) {
            $baseQuery->where('status_id', $request->status);
        }

        if ($request->filled('region')) {
            $baseQuery->whereHas('Neighborhoodname.Region', function ($q) use ($request) {
                $q->where('id', $request->region);
            });
        }

        if ($request->filled('neighborhood')) {
            $baseQuery->whereHas('Neighborhoodname', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->neighborhood . '%')->orWhere('id', $request->neighborhood);
            });
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $baseQuery->whereBetween('created_at', [$request->date_from . ' 00:00:00', $request->date_to . ' 23:59:59']);
        } elseif ($request->filled('date_from')) {
            $baseQuery->where('created_at', '>=', $request->date_from . ' 00:00:00');
        } elseif ($request->filled('date_to')) {
            $baseQuery->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('categories')) {
            $baseQuery->where('category_id', $request->categories);
        }

        if ($request->filled('user')) {
            $baseQuery->where('created_by', $request->user);
        }

        if ($request->filled('type')) {
            $baseQuery->where('type', $request->type);
        }

        if ($request->filled('employee')) {
            $baseQuery->where('employee_id', $request->employee);
        }
    }


    private function calculateClientData($clients, $currentYear)
    {
        if ($clients->isEmpty()) {
            return [];
        }

        $clientIds = $clients->pluck('id');
        $monthlyTarget = 648;

        // جلب البيانات المالية
        $returnedInvoiceIds = Invoice::whereNotNull('reference_number')->pluck('reference_number')->toArray();
        $excludedInvoiceIds = array_unique(array_merge($returnedInvoiceIds, Invoice::where('type', 'returned')->pluck('id')->toArray()));

        $invoices = Invoice::whereIn('client_id', $clientIds)->where('type', 'normal')->whereNotIn('id', $excludedInvoiceIds)->get();

        $invoiceIdsByClient = $invoices->groupBy('client_id')->map->pluck('id');

        $payments = PaymentsProcess::whereIn('invoice_id', $invoices->pluck('id'))->whereYear('created_at', $currentYear)->get()->groupBy('invoice_id');

        $receipts = Receipt::with('account')->whereHas('account', fn($q) => $q->whereIn('client_id', $clientIds))->whereYear('created_at', $currentYear)->get()->groupBy(fn($receipt) => $receipt->account->client_id);

        $months = [
            'يناير' => 1,
            'فبراير' => 2,
            'مارس' => 3,
            'أبريل' => 4,
            'مايو' => 5,
            'يونيو' => 6,
            'يوليو' => 7,
            'أغسطس' => 8,
            'سبتمبر' => 9,
            'أكتوبر' => 10,
            'نوفمبر' => 11,
            'ديسمبر' => 12,
        ];

        $getClassification = function ($percentage, $collected = 0) {
            if ($collected == 0) {
                return ['group' => 'D', 'class' => 'secondary'];
            }
            if ($percentage > 100) {
                return ['group' => 'A++', 'class' => 'primary'];
            }
            if ($percentage >= 60) {
                return ['group' => 'A', 'class' => 'success'];
            }
            if ($percentage >= 30) {
                return ['group' => 'B', 'class' => 'warning'];
            }
            return ['group' => 'C', 'class' => 'danger'];
        };

        return $clients
            ->map(function ($client) use ($invoiceIdsByClient, $payments, $receipts, $months, $monthlyTarget, $getClassification, $currentYear) {
                $invoiceIds = $invoiceIdsByClient[$client->id] ?? collect();

                $clientData = [
                    'id' => $client->id,
                    'monthly' => [],
                    'invoices_count' => $invoiceIds->count(),
                    'payments_count' => $invoiceIds->sum(fn($id) => isset($payments[$id]) ? $payments[$id]->count() : 0),
                    'receipts_count' => isset($receipts[$client->id]) ? $receipts[$client->id]->count() : 0,
                    'total_collected' => 0,
                ];

                $totalYearlyCollected = 0;

                foreach ($months as $monthName => $monthNumber) {
                    $paymentsTotal = 0;
                    if ($invoiceIds->isNotEmpty()) {
                        foreach ($invoiceIds as $invoiceId) {
                            if (isset($payments[$invoiceId])) {
                                $paymentsTotal += $payments[$invoiceId]->filter(fn($payment) => Carbon::parse($payment->created_at)->year == $currentYear && Carbon::parse($payment->created_at)->month == $monthNumber)->sum('amount');
                            }
                        }
                    }

                    $receiptsTotal = isset($receipts[$client->id]) ? $receipts[$client->id]->filter(fn($receipt) => Carbon::parse($receipt->created_at)->year == $currentYear && Carbon::parse($receipt->created_at)->month == $monthNumber)->sum('amount') : 0;

                    $monthlyCollected = $paymentsTotal + $receiptsTotal;
                    $totalYearlyCollected += $monthlyCollected;

                    $percentage = $monthlyTarget > 0 ? round(($monthlyCollected / $monthlyTarget) * 100, 2) : 0;
                    $classification = $getClassification($percentage, $monthlyCollected);

                    $clientData['monthly'][$monthName] = [
                        'collected' => $monthlyCollected,
                        'payments_total' => $paymentsTotal,
                        'receipts_total' => $receiptsTotal,
                        'target' => $monthlyTarget,
                        'percentage' => $percentage,
                        'group' => $classification['group'],
                        'group_class' => $classification['class'],
                        'month_number' => $monthNumber,
                    ];
                }

                $clientData['total_collected'] = $totalYearlyCollected;
                return $clientData;
            })
            ->keyBy('id');
    }

    private function getClientDueBalances($clients)
    {
        if ($clients->isEmpty()) {
            return [];
        }

        $clientIds = $clients->pluck('id');
        return Account::whereIn('client_id', $clientIds)->selectRaw('client_id, SUM(balance) as total_due')->groupBy('client_id')->pluck('total_due', 'client_id');
    }

    private function getClientTotalSales($clients)
    {
        if ($clients->isEmpty()) {
            return [];
        }

        $clientIds = $clients->pluck('id');
        $returnedInvoiceIds = Invoice::whereNotNull('reference_number')->pluck('reference_number')->toArray();
        $excludedInvoiceIds = array_unique(array_merge($returnedInvoiceIds, Invoice::where('type', 'returned')->pluck('id')->toArray()));

        return Invoice::whereIn('client_id', $clientIds)->where('type', 'normal')->whereNotIn('id', $excludedInvoiceIds)->groupBy('client_id')->selectRaw('client_id, SUM(grand_total) as total_sales')->pluck('total_sales', 'client_id');
    }



    private function calculateClientDistances($clients, $user)
{
    $clientDistances = [];
    $userLocation = Location::where('employee_id', $user->id)->latest()->first();

    // إذا لم يكن هناك موقع للمستخدم، إرجاع بيانات فارغة
    if (!$userLocation) {
        foreach ($clients as $client) {
            $clientDistances[$client->id] = [
                'distance' => null,
                'message' => 'موقعك غير معروف',
                'within_range' => false,
            ];
        }
        return $clientDistances;
    }

    foreach ($clients as $client) {
        // البحث عن آخر موقع للعميل
        $clientLocation = $client->locations()->latest()->first();

        if ($clientLocation && $clientLocation->latitude && $clientLocation->longitude) {
            $distanceKm = $this->calculateDistance(
                $userLocation->latitude,
                $userLocation->longitude,
                $clientLocation->latitude,
                $clientLocation->longitude
            );

            $clientDistances[$client->id] = [
                'distance' => $distanceKm,
                'message' => $distanceKm !== null ? 'تم الحساب بنجاح' : 'خطأ في حساب المسافة',
                'within_range' => $distanceKm !== null && $distanceKm <= 0.3,
                'distance_text' => $distanceKm !== null ?
                    ($distanceKm < 1 ?
                        round($distanceKm * 1000) . ' متر' :
                        round($distanceKm, 2) . ' كم'
                    ) : 'غير معروف'
            ];
        } else {
            $clientDistances[$client->id] = [
                'distance' => null,
                'message' => 'موقع العميل غير معروف',
                'within_range' => false,
                'distance_text' => 'غير معروف'
            ];
        }
    }

    return $clientDistances;
}


/**
 * إظهار العميل في الخريطة والكارد
 */


/**
 * الحصول على قائمة العملاء المخفيين
 */

/**
 * الحصول على قائمة العملاء المخفيين
 */

// تحسين ترتيب العملاء في getAjaxResponse

    // إضافة route جديد لجلب بيانات الخريطة - النسخة المُصلحة
    // أولاً: تحديث دالة getMapData
public function getMapData(Request $request)
{
    $user = auth()->user();

    // تأكد من استخدام نفس العلاقة المستخدمة في index
    $baseQuery = Client::with([
        'employee',
        'status_client:id,name,color',  // استخدام نفس العلاقة
        'locations',
        'neighborhood.region',
        'branch:id,name'
    ]);

    $noClients = false;

    $currentDate = now();
    $currentDayOfWeek = $currentDate->dayOfWeek;
    $adjustedDayOfWeek = ($currentDayOfWeek + 1) % 7;
    $englishDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    $currentDayNameEn = $englishDays[$adjustedDayOfWeek];

    $startOfYear = now()->copy()->startOfYear();
    $startOfYearDay = ($startOfYear->dayOfWeek + 1) % 7;
    $daysSinceStart = $startOfYear->diffInDays($currentDate);
    $currentWeek = (int) ceil(($daysSinceStart + $startOfYearDay + 1) / 7);
    $currentYear = now()->year;

    // تطبيق نفس منطق الصلاحيات
    if ($user->role === 'employee') {
        $clientVisits = EmployeeClientVisit::with('client')
            ->where('employee_id', $user->id)
            ->where('day_of_week', $currentDayNameEn)
            ->where('year', $currentYear)
            ->where('week_number', $currentWeek)
            ->get();

        if ($clientVisits->isNotEmpty()) {
            $clientIds = $clientVisits->pluck('client_id');
            $baseQuery->whereIn('id', $clientIds);
        } else {
            $noClients = true;
        }
    } elseif ($user->branch_id) {
        $mainBranchName = Branch::where('is_main', true)->value('name');
        $currentBranchName = Branch::find($user->branch_id)->name;

        if ($currentBranchName !== $mainBranchName) {
            $baseQuery->where('branch_id', $user->branch_id);
        }
    }

    // **الإضافة المهمة: تطبيق فلتر العملاء المخفيين قبل تطبيق الفلاتر الأخرى**
    $hiddenClientIds = HiddenClient::getHiddenClientsForUser($user->id);
    if (!empty($hiddenClientIds)) {
        $baseQuery->whereNotIn('id', $hiddenClientIds);
    }

    // تطبيق الفلاتر
    $this->applyFilters($baseQuery, $request);

    if ($noClients) {
        return response()->json(['clients' => []]);
    }

    // جلب العملاء مع المواقع فقط
    $clients = $baseQuery
        ->whereHas('locations', function ($query) {
            $query->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('latitude', '!=', '')
                ->where('longitude', '!=', '')
                ->where('latitude', '!=', 0)
                ->where('longitude', '!=', 0);
        })
        ->get();

    // تنسيق البيانات للخريطة
    $clientsForMap = [];

    foreach ($clients as $client) {
        $location = $client->locations;

        if ($location && $location->latitude && $location->longitude && $location->latitude != 0 && $location->longitude != 0) {
            // استخدام نفس المنطق من الصفحة الرئيسية
            $statusColor = '#4361ee'; // اللون الافتراضي
            $statusName = 'غير محدد';

            // التحقق من وجود status_client (نفس ما في الصفحة الرئيسية)
            if ($client->status_client) {
                $statusColor = $client->status_client->color ?: '#4361ee';
                $statusName = $client->status_client->name ?: 'غير محدد';
            }

            $clientsForMap[] = [
                'id' => $client->id,
                'lat' => (float) $location->latitude,
                'lng' => (float) $location->longitude,
                'trade_name' => $client->trade_name ?? 'غير محدد',
                'code' => $client->code ?? 'غير محدد',
                'phone' => $client->phone ?? 'غير متوفر',
                'address' => $location->address ?? 'غير محدد',
                'status' => $statusName,
                'statusColor' => $statusColor, // تأكد من إرسال اللون الصحيح
                'branch' => optional($client->branch)->name ?? 'غير محدد',
                'employee' => optional($client->employee)->name ?? 'غير محدد',
            ];
        }
    }

    return response()->json([
        'clients' => $clientsForMap,
        'total' => count($clientsForMap),
        'debug' => [
            'user_role' => $user->role,
            'total_clients_found' => $clients->count(),
            'clients_with_location' => count($clientsForMap),
            'hidden_clients_count' => count($hiddenClientIds),
            'hidden_client_ids' => $hiddenClientIds
        ]
    ]);
}


public function index(Request $request)
{
    $user = auth()->user();
    $baseQuery = Client::with(['employee', 'status:id,name,color', 'locations', 'neighborhood.region', 'branch:id,name', 'account', 'categoriesClient']);
    $noClients = false;

    $currentDate = now();
    $currentDayOfWeek = $currentDate->dayOfWeek;

    // نجعل السبت هو أول يوم في الأسبوع (0 = السبت)
    $adjustedDayOfWeek = ($currentDayOfWeek + 1) % 7;

    $arabicDays = ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
    $englishDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

    $currentDayName = $arabicDays[$adjustedDayOfWeek];
    $currentDayNameEn = $englishDays[$adjustedDayOfWeek];

    // نحسب الأسبوع يدويًا من بداية السنة مع اعتبار بداية الأسبوع من السبت
    $startOfYear = now()->copy()->startOfYear();
    $startOfYearDay = ($startOfYear->dayOfWeek + 1) % 7;
    $daysSinceStart = $startOfYear->diffInDays($currentDate);
    $currentWeek = (int) ceil(($daysSinceStart + $startOfYearDay + 1) / 7);

    $currentYear = now()->year;

    // إنشاء query منفصل لجلب جميع العملاء للـ select (بدون فلاتر)
    $allClientsQuery = Client::select(['id', 'trade_name', 'code']);

    // تطبيق صلاحيات المستخدم فقط على allClients
    if ($user->role === 'employee') {
        // للموظف: إظهار العملاء المخصصين له في هذا اليوم من الأسبوع
        $clientVisits = EmployeeClientVisit::with('client')
            ->where('employee_id', $user->id)
            ->where('day_of_week', $currentDayNameEn)
            ->where('year', $currentYear)
            ->where('week_number', $currentWeek)
            ->get();

        if ($clientVisits->isNotEmpty()) {
            $clientIds = $clientVisits->pluck('client_id');
            $allClientsQuery->whereIn('id', $clientIds);
        } else {
            // إذا لم يكن هناك عملاء مخصصين، إرجاع مجموعة فارغة
            $allClientsQuery = $allClientsQuery->whereRaw('1 = 0');
        }
    } elseif ($user->branch_id) {
        // للمستخدم المرتبط بفرع معين
        $mainBranchName = Branch::where('is_main', true)->value('name');
        $currentBranchName = Branch::find($user->branch_id)->name;

        if ($currentBranchName !== $mainBranchName) {
            $allClientsQuery->where('branch_id', $user->branch_id);
        }
    }

    // جلب جميع العملاء للـ select بناءً على صلاحيات المستخدم
    $allClients = $allClientsQuery->orderBy('trade_name')->get();

    // تطبيق فلترة الموظف على البيانات المعروضة
    if ($user->role === 'employee') {
        $clientVisits = EmployeeClientVisit::with('client')->where('employee_id', $user->id)->where('day_of_week', $currentDayNameEn)->where('year', $currentYear)->where('week_number', $currentWeek)->get();

        if ($clientVisits->isNotEmpty()) {
            $clientIds = $clientVisits->pluck('client_id');
            $baseQuery->whereIn('id', $clientIds);
        } else {
            $noClients = true;
        }
    } elseif ($user->branch_id) {
        $mainBranchName = Branch::where('is_main', true)->value('name');
        $currentBranchName = Branch::find($user->branch_id)->name;

        if ($currentBranchName !== $mainBranchName) {
            $baseQuery->where('branch_id', $user->branch_id);
        }
    }

    // تطبيق الفلاتر على البيانات المعروضة فقط
    $this->applyFilters($baseQuery, $request);

    // إذا كان طلب AJAX، نرجع البيانات فقط
    if ($request->ajax()) {
        return $this->getAjaxResponse($baseQuery, $request, $noClients, $user);
    }

    // للطلب العادي، نرجع البيانات الأساسية للعرض الأولي
    $perPage = (int) $request->get('perPage', 50);
    $clients = $noClients ? collect() : $baseQuery->paginate($perPage)->appends($request->query());

    // جلب بيانات مبسطة للخريطة فقط (مع الفلاتر المطبقة)
    $mapQuery = clone $baseQuery;
    $mapClients = $noClients
        ? collect()
        : $mapQuery
            ->select(['id', 'trade_name', 'code', 'phone', 'status_id', 'branch_id'])
            ->with(['status_client:id,name,color', 'locations:id,client_id,latitude,longitude', 'branch:id,name'])
            ->get();

    // حساب المسافات للعملاء المعروضين فقط
    $clientDistances = $this->calculateClientDistances($clients, $user);

    // حساب البيانات المالية للعملاء المعروضين فقط
    $clientsData = $this->calculateClientData($clients, $currentYear);
    $clientDueBalances = $this->getClientDueBalances($clients);

    $regionGroups = $user->role === 'employee' ? $user->regionGroups()->get() : Region_groub::all();

    // جلب العملاء المخفيين للمستخدم الحالي من قاعدة البيانات
    $hiddenClientIds = HiddenClient::getHiddenClientsForUser($user->id);

    return view('client::index', [
        'clients' => $clients,
        'allClients' => $allClients,
        'mapClients' => $mapClients,
        'clientsData' => $clientsData,
        'clientDueBalances' => $clientDueBalances,
        'clientDistances' => $clientDistances,
        'Neighborhoods' => Neighborhood::all(),
        'users' => User::all(),
        'categories' => CategoriesClient::all(),
        'employees' => Employee::all(),
        'creditLimit' => CreditLimit::first(),
        'statuses' => Statuses::all()->keyBy('id'),
        'Region_groups' => $regionGroups,
        'target' => Target::find(2)->value ?? 648,
        'monthlyTarget' => 648,
        'months' => ['يناير' => 1, 'فبراير' => 2, 'مارس' => 3, 'أبريل' => 4, 'مايو' => 5, 'يونيو' => 6, 'يوليو' => 7, 'أغسطس' => 8, 'سبتمبر' => 9, 'أكتوبر' => 10, 'نوفمبر' => 11, 'ديسمبر' => 12],
        'currentYear' => $currentYear,
        'currentDayName' => $currentDayName,
        'hiddenClients' => $hiddenClientIds, // العملاء المخفيين من قاعدة البيانات
    ]);
}

public function hideFromMap(Request $request, $clientId)
{
    try {
        $client = Client::findOrFail($clientId);
        $userId = auth()->id();

        // التحقق من وجود العميل مسبقاً
        $existingHidden = HiddenClient::where('user_id', $userId)
            ->where('client_id', $clientId)
            ->where('expires_at', '>', now())
            ->first();

        if ($existingHidden) {
            return response()->json([
                'success' => false,
                'message' => 'العميل مخفي بالفعل'
            ]);
        }

        // إنشاء سجل جديد في قاعدة البيانات
        HiddenClient::create([
            'user_id' => $userId,
            'client_id' => $clientId,
            'hidden_at' => now(),
            'expires_at' => now()->addHours(24)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إخفاء العميل بنجاح لمدة 24 ساعة',
            'client_id' => $clientId,
            'client_name' => $client->trade_name,
            'expires_at' => now()->addHours(24)->toDateTimeString(),
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في إخفاء العميل: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء إخفاء العميل'
        ], 500);
    }
}

public function showInMap(Request $request, $clientId)
{
    try {
        $client = Client::findOrFail($clientId);
        $userId = auth()->id();

        // حذف السجل من قاعدة البيانات
        HiddenClient::where('user_id', $userId)
            ->where('client_id', $clientId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم إظهار العميل بنجاح',
            'client_id' => $clientId,
            'client_name' => $client->trade_name
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في إظهار العميل: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء إظهار العميل'
        ], 500);
    }
}

public function getHiddenClients(Request $request)
{
    try {
        $userId = auth()->id();

        // جلب العملاء المخفيين من قاعدة البيانات مع معلومات العميل
        $hiddenClients = HiddenClient::where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->with('client:id,trade_name,code')
            ->get()
            ->map(function ($hidden) {
                return [
                    'id' => $hidden->client_id,
                    'name' => $hidden->client->trade_name,
                    'code' => $hidden->client->code,
                    'hidden_at' => $hidden->hidden_at->format('Y-m-d H:i:s'),
                    'expires_at' => $hidden->expires_at->format('Y-m-d H:i:s')
                ];
            });

        return response()->json([
            'success' => true,
            'hidden_clients' => $hiddenClients
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء جلب العملاء المخفيين'
        ], 500);
    }
}

// تعديل دالة getAjaxResponse لاستبعاد العملاء المخفيين من قاعدة البيانات
private function getAjaxResponse($baseQuery, $request, $noClients, $user)
{
    $perPage = (int) $request->get('perPage', 50);
    $clients = $noClients ? collect() : $baseQuery->get();

    // جلب العملاء المخفيين للمستخدم الحالي من قاعدة البيانات
    $hiddenClientIds = HiddenClient::getHiddenClientsForUser($user->id);

    // استبعاد العملاء المخفيين من النتائج
    if (!empty($hiddenClientIds)) {
        $clients = $clients->reject(function ($client) use ($hiddenClientIds) {
            return in_array($client->id, $hiddenClientIds);
        });
    }

    if ($clients->isEmpty()) {
        return response()->json([
            'success' => true,
            'html' => '',
            'pagination' => []
        ]);
    }

    // حساب المسافات
    $clientDistances = $this->calculateClientDistances($clients, $user);

    // ترتيب العملاء حسب المسافة
    $clients = $clients->sortBy(function ($client) use ($clientDistances) {
        $distance = $clientDistances[$client->id]['distance'] ?? null;
        // العملاء بدون مسافة معروفة يذهبون إلى النهاية
        return $distance === null ? PHP_INT_MAX : $distance;
    })->values();

    // إنشاء الترقيم
    $page = $request->get('page', 1);
    $pagedClients = new \Illuminate\Pagination\LengthAwarePaginator(
        $clients->forPage($page, $perPage),
        $clients->count(),
        $perPage,
        $page,
        [
            'path' => $request->url(),
            'query' => $request->query(),
        ]
    );
 
    // حساب البيانات المالية للصفحة الحالية فقط
    $clientsData = $this->calculateClientData($pagedClients, now()->year);
    $clientDueBalances = $this->getClientDueBalances($pagedClients);

    return response()->json([
        'success' => true,
        'html' => view('client::partials.client_cards', [
            'clients' => $pagedClients,
            'clientsData' => $clientsData,
            'clientDueBalances' => $clientDueBalances,
            'clientDistances' => $clientDistances,
            'statuses' => Statuses::all()->keyBy('id'),
            'months' => [
                'يناير' => 1, 'فبراير' => 2, 'مارس' => 3, 'أبريل' => 4,
                'مايو' => 5, 'يونيو' => 6, 'يوليو' => 7, 'أغسطس' => 8,
                'سبتمبر' => 9, 'أكتوبر' => 10, 'نوفمبر' => 11, 'ديسمبر' => 12
            ],
            'currentYear' => now()->year,
            'clientTotalSales' => $this->getClientTotalSales($pagedClients),
        ])->render(),
        'pagination' => [
            'current_page' => $pagedClients->currentPage(),
            'last_page' => $pagedClients->lastPage(),
            'has_more_pages' => $pagedClients->hasMorePages(),
            'on_first_page' => $pagedClients->onFirstPage(),
            'per_page' => $pagedClients->perPage(),
            'total' => $pagedClients->total(),
            'from' => $pagedClients->firstItem(),
            'to' => $pagedClients->lastItem(),
        ],
    ]);
}
    public function updateCreditLimit(Request $request)
    {
        $request->validate([
            'value' => 'required|numeric|min:0',
        ]);

        // تحديث أو إنشاء الحد الائتماني إذا لم يكن موجودًا
        $creditLimit = CreditLimit::first(); // يجلب أول حد ائتماني
        if ($creditLimit) {
            $creditLimit->value = $request->value;
            $creditLimit->save();
        } else {
            CreditLimit::create([
                'value' => $request->value,
            ]);
        }

        return redirect()->back()->with('success', 'تم تحديث الحد الائتماني بنجاح!');
    }

    public function create()
    {
        $employees = Employee::all();
        $categories = CategoriesClient::all();
        $Regions_groub = Region_groub::all();
        $branches = Branch::all();
        $lastClient = Client::orderBy('code', 'desc')->first();

        $newCode = $lastClient ? $lastClient->code + 1 : 3000;

        $GeneralClientSettings = GeneralClientSetting::all();
        // إذا كان الجدول فارغًا، قم بإنشاء قيم افتراضية (مفعلة بالكامل)
        if ($GeneralClientSettings->isEmpty()) {
            $defaultSettings = [['key' => 'image', 'name' => 'صورة', 'is_active' => true], ['key' => 'type', 'name' => 'النوع', 'is_active' => true], ['key' => 'birth_date', 'name' => 'تاريخ الميلاد', 'is_active' => true], ['key' => 'location', 'name' => 'الموقع على الخريطة', 'is_active' => true], ['key' => 'opening_balance', 'name' => 'الرصيد الافتتاحي', 'is_active' => true], ['key' => 'credit_limit', 'name' => 'الحد الائتماني', 'is_active' => true], ['key' => 'credit_duration', 'name' => 'المدة الائتمانية', 'is_active' => true], ['key' => 'national_id', 'name' => 'رقم الهوية الوطنية', 'is_active' => true], ['key' => 'addresses', 'name' => 'عناوين متعددة', 'is_active' => true], ['key' => 'link', 'name' => 'الرابط', 'is_active' => true]];

            // تحويل المصفوفة إلى مجموعة (Collection)
            $GeneralClientSettings = collect($defaultSettings)->map(function ($item) {
                return (object) $item; // تحويل المصفوفة إلى كائن
            });
        }
        return view('client::create', compact('employees', 'branches', 'newCode', 'categories', 'GeneralClientSettings', 'Regions_groub'));
    }
    private function getNeighborhoodFromGoogle($latitude, $longitude)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY'); // احصل على API Key من .env
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=$apiKey&language=ar";

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (!empty($data['results'])) {
            foreach ($data['results'][0]['address_components'] as $component) {
                if (in_array('sublocality', $component['types']) || in_array('neighborhood', $component['types'])) {
                    return $component['long_name']; // اسم الحي
                }
            }
        }
        return 'لم يتم العثور على الحي';
    }
    public function store(ClientRequest $request)
    {
        try {
            $data_request = $request->except('_token');
            $rules = [
                'region_id' => ['required'],
            ];

            $messages = [
                'region_id.required' => 'حقل المجموعة مطلوب.',
            ];

            $validator = Validator::make($data_request, $rules, $messages);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'success' => false,
                        'errors' => $validator->errors(),
                        'message' => 'يرجى التحقق من البيانات المدخلة',
                    ],
                    422,
                );
            }

            if ($request->has('latitude') && $request->has('longitude')) {
                $latitude = $request->latitude;
                $longitude = $request->longitude;
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'الإحداثيات غير موجودة',
                    ],
                    400,
                );
            }

            DB::beginTransaction();

            $client = new Client();
            $client->status_id = 3;

            // الحصول على الرقم الحالي لقسم العملاء من جدول serial_settings
            $serialSetting = SerialSetting::where('section', 'customer')->first();
            $currentNumber = $serialSetting ? $serialSetting->current_number : 1;

            // تعيين id للعميل الجديد
            $client->code = $currentNumber;
            $client->fill($data_request);

            // معالجة الصورة
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $client->attachments = $filename;
                }
            }

            // حفظ العميل أولاً
            $client->save();

            // حفظ جهات الاتصال الأساسية
            $mainContact = [
                'first_name' => $client->trade_name,
                'phone' => $client->phone,
                'mobile' => $client->mobile,
                'email' => $client->email,
                'is_primary' => true,
            ];

            $client->contacts()->create($mainContact);

            // حفظ الموظفين المرتبطين وجمع معرفاتهم
            $employeeIds = [];
            if (auth()->user()->role === 'manager') {
                if ($request->has('employee_client_id')) {
                    foreach ($request->employee_client_id as $employee_id) {
                        $client_employee = new ClientEmployee();
                        $client_employee->client_id = $client->id;
                        $client_employee->employee_id = $employee_id;
                        $client_employee->save();
                        $employeeIds[] = $employee_id;
                    }
                }
            } elseif (auth()->user()->role === 'employee') {
                $employeeId = auth()->user()->employee_id;
                ClientEmployee::create([
                    'client_id' => $client->id,
                    'employee_id' => $employeeId,
                ]);
                $employeeIds[] = $employeeId;
            }

            // تسجيل الإحداثيات
            $client->locations()->create([
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            $neighborhoodName = $this->getNeighborhoodFromGoogle($latitude, $longitude);
            $Neighborhood = new Neighborhood();
            $Neighborhood->name = $neighborhoodName ?? 'غير محدد';
            $Neighborhood->region_id = $request->region_id;
            $Neighborhood->client_id = $client->id;
            $Neighborhood->save();

            // إنشاء مستخدم جديد إذا تم توفير البريد الإلكتروني
            $password = Str::random(10);
            $full_name = $client->trade_name . ' ' . $client->first_name . ' ' . $client->last_name;
            if ($request->email != null) {
                User::create([
                    'name' => $full_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'role' => 'client',
                    'client_id' => $client->id,
                    'password' => Hash::make($password),
                ]);
            }

            // تسجيل إشعار نظام جديد
            ModelsLog::create([
                'type' => 'client',
                'type_id' => $client->id,
                'type_log' => 'log',
                'description' => 'تم إضافة عميل **' . $client->trade_name . '**',
                'created_by' => auth()->id(),
            ]);

            // زيادة الرقم الحالي بمقدار 1
            if ($serialSetting) {
                $serialSetting->update(['current_number' => $currentNumber + 1]);
            }

            // إنشاء حساب فرعي باستخدام trade_name
            $customers = Account::where('name', 'العملاء')->first();
            if ($customers) {
                $customerAccount = new Account();
                $customerAccount->name = $client->trade_name;
                $customerAccount->client_id = $client->id;
                $customerAccount->balance += $client->opening_balance ?? 0;

                $lastChild = Account::where('parent_id', $customers->id)->orderBy('code', 'desc')->first();
                $newCode = $lastChild ? $this->generateNextCode($lastChild->code) : $customers->code . '1';

                while (\App\Models\Account::where('code', $newCode)->exists()) {
                    $newCode = $this->generateNextCode($newCode);
                }

                $customerAccount->code = $newCode;
                $customerAccount->balance_type = 'debit';
                $customerAccount->parent_id = $customers->id;
                $customerAccount->is_active = false;
                $customerAccount->save();

                if ($client->opening_balance > 0) {
                    $journalEntry = JournalEntry::create([
                        'reference_number' => $client->code,
                        'date' => now(),
                        'description' => 'رصيد افتتاحي للعميل : ' . $client->trade_name,
                        'status' => 1,
                        'currency' => 'SAR',
                        'client_id' => $client->id,
                    ]);

                    JournalEntryDetail::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $customerAccount->id,
                        'description' => 'رصيد افتتاحي للعميل : ' . $client->trade_name,
                        'debit' => $client->opening_balance ?? 0,
                        'credit' => 0,
                        'is_debit' => true,
                    ]);
                }
            }

            // حفظ جهات الاتصال الإضافية
            if ($request->has('contacts') && is_array($request->contacts)) {
                foreach ($request->contacts as $contact) {
                    $client->contacts()->create($contact);
                }
            }

            // إضافة العميل إلى خط سير الموظفين المسؤولين عنه (اليوم الحالي فقط)
            if (!empty($employeeIds)) {
                $now = now();
                $currentDate = $now->copy();
                $currentYear = $now->year;

                // نحسب أول سبت في السنة
                $firstSaturday = Carbon::createFromDate($currentYear, 1, 1)->startOfWeek(Carbon::SATURDAY);

                // إذا أول يوم في السنة كان سبت، نستخدمه
                if (Carbon::createFromDate($currentYear, 1, 1)->dayOfWeek === Carbon::SATURDAY) {
                    $firstSaturday = Carbon::createFromDate($currentYear, 1, 1);
                }

                // نحسب الفرق بالأسابيع
                $daysDiff = $firstSaturday->diffInDays($currentDate);
                $currentWeek = (int) floor($daysDiff / 7) + 1;

                // اليوم الحالي بعد تعديل الترتيب بحيث السبت هو 0
                $adjustedDayOfWeek = ($now->dayOfWeek + 1) % 7;
                $englishDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                $dayOfWeek = strtolower($englishDays[$adjustedDayOfWeek]);

                foreach ($employeeIds as $employeeId) {
                    EmployeeClientVisit::updateOrCreate(
                        [
                            'employee_id' => $employeeId,
                            'client_id' => $client->id,
                            'day_of_week' => $dayOfWeek,
                            'year' => $currentYear,
                            'week_number' => $currentWeek,
                            'status' => 'active',
                        ],
                        [
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✨ تم إضافة العميل بنجاح!',
                'redirect_url' => route('clients.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    protected function getCorrectWeekNumber(Carbon $date)
    {
        // تاريخ السبت الماضي كبداية للأسبوع
        $weekStart = $date->copy()->startOfWeek(Carbon::SATURDAY);

        // تاريخ 1 يناير من نفس السنة
        $janFirst = Carbon::create($weekStart->year, 1, 1);

        // إذا كان 1 يناير ليس سبتاً، نبدأ العد من السبت التالي
        if ($janFirst->dayOfWeek != Carbon::SATURDAY) {
            $janFirst->next(Carbon::SATURDAY);
        }

        // حساب الفرق بالأسابيع + 1
        $weekNumber = $janFirst->diffInWeeks($weekStart) + 1;

        // التأكد من أن رقم الأسبوع لا يتجاوز 52
        return min($weekNumber, 52);
    }

    public function send_email($id)
    {
        $employee = User::where('client_id', $id)->first();

        if (!$employee || empty($employee->email)) {
            return redirect()->back()->with('error', 'العميل لا يمتلك بريدًا إلكترونيًا للدخول.');
        }

        // توليد كلمة مرور جديدة عشوائية
        $newPassword = $this->generateRandomPassword();

        // تحديث كلمة المرور في قاعدة البيانات بعد تشفيرها
        $employee->password = Hash::make($newPassword);
        $employee->save();

        // إعداد بيانات البريد
        $details = [
            'name' => $employee->name,
            'email' => $employee->email,
            'password' => $newPassword, // إرسال كلمة المرور الجديدة مباشرة
        ];

        // إرسال البريد
        Mail::to($employee->email)->send(new TestMail($details));
        ModelsLog::create([
            'type' => 'hr_log',
            'type_id' => $employee->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم ارسال بيانات الدخول **' . $employee->name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        // return back()->with('message', 'تم إرسال البريد بنجاح!');
        return redirect()
            ->back()
            ->with(['success' => 'تم  ارسال البريد بنجاح .']);
    }
    private function generateRandomPassword($length = 10)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }

    protected function generateUniqueAccountCode($parentId, $parentCode)
    {
        $lastChild = Account::where('parent_id', $parentId)->orderBy('code', 'desc')->first();

        $baseCode = $lastChild ? (int) $lastChild->code + 1 : $parentCode . '001';

        $counter = 1;
        $newCode = $baseCode;

        while (Account::where('code', $newCode)->exists()) {
            $newCode = $baseCode . '_' . $counter;
            $counter++;

            if ($counter > 100) {
                throw new \RuntimeException('فشل توليد كود فريد');
            }
        }

        return $newCode;
    }
    // إضافة هذه الدالة في نفس وحدة التحكم
    private function generateNextCode(string $lastChildCode): string
    {
        // استخراج الرقم الأخير من الكود
        $lastNumber = intval(substr($lastChildCode, -1));
        // زيادة الرقم الأخير بمقدار 1
        $newNumber = $lastNumber + 1;
        // إعادة بناء الكود مع الرقم الجديد
        return substr($lastChildCode, 0, -1) . $newNumber;
    }
    public function update(ClientRequest $request, $id)
    {
        $rules = [
            'region_id' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];

        $messages = [
            'region_id.required' => 'حقل المجموعة مطلوب.',
            'latitude.required' => 'العميل ليس لديه موقع مسجل الرجاء تحديد الموقع على الخريطة',
            'longitude.required' => 'العميل ليس لديه موقع مسجل الرجاء تحديد الموقع على الخريطة',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'يرجى التحقق من البيانات المدخلة',
                ],
                422,
            );
        }

        // بدء المعاملة لضمان سلامة البيانات
        DB::beginTransaction();

        try {
            $data_request = $request->except('_token', 'contacts');
            $client = Client::findOrFail($id);
            $oldData = $client->getOriginal();

            $latitude = $request->latitude ?? $client->latitude;
            $longitude = $request->longitude ?? $client->longitude;

            $data_request = $request->except('_token', 'contacts', 'latitude', 'longitude');

            // حذف الموظفين السابقين فقط إذا كان المستخدم مدير
            if (auth()->user()->role === 'manager') {
                ClientEmployee::where('client_id', $client->id)->delete();

                if ($request->has('employee_client_id')) {
                    foreach ($request->employee_client_id as $employee_id) {
                        ClientEmployee::create([
                            'client_id' => $client->id,
                            'employee_id' => $employee_id,
                        ]);
                    }
                }
            } elseif (auth()->user()->role === 'employee') {
                $employee_id = auth()->user()->employee_id;

                // التحقق إذا هو أصلاً مسؤول
                $alreadyExists = ClientEmployee::where('client_id', $client->id)->where('employee_id', $employee_id)->exists();

                if (!$alreadyExists) {
                    ClientEmployee::create([
                        'client_id' => $client->id,
                        'employee_id' => $employee_id,
                    ]);
                }
            }

            // 1. معالجة المرفقات
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    // حذف الملف القديم إن وجد
                    if ($client->attachments) {
                        $oldFilePath = public_path('assets/uploads/') . $client->attachments;
                        if (File::exists($oldFilePath)) {
                            File::delete($oldFilePath);
                        }
                    }

                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $data_request['attachments'] = $filename;
                }
            }

            // 2. تحديث بيانات العميل الأساسية
            $client->update($data_request);

            // تحديث اسم الحساب إذا تغير الاسم التجاري
            if ($client->wasChanged('trade_name')) {
                Account::where('client_id', $client->id)->update(['name' => $client->trade_name]);
            }

            // 3. معالجة الإحداثيات - الطريقة المؤكدة
            $client->locations()->delete(); // حذف جميع المواقع القديمة

            $client->locations()->create([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'client_id' => $client->id,
            ]);

            $neighborhoodName = $this->getNeighborhoodFromGoogle($request->latitude, $request->longitude);

            // البحث عن الحي الحالي للعميل
            $Neighborhood = Neighborhood::where('client_id', $client->id)->first();

            if ($Neighborhood) {
                // إذا كان لديه حي، قم بتحديثه
                $Neighborhood->name = $neighborhoodName ?? 'غير محدد';
                $Neighborhood->region_id = $request->region_id;
                $Neighborhood->save();
            } else {
                // إذا لم يكن لديه حي، أضف حيًا جديدًا
                $Neighborhood = new Neighborhood();
                $Neighborhood->name = $neighborhoodName ?? 'غير محدد';
                $Neighborhood->region_id = $request->region_id;
                $Neighborhood->client_id = $client->id;
                $Neighborhood->save();
            }

            // 4. تحديث بيانات المستخدم
            if ($request->email) {
                $full_name = implode(' ', array_filter([$client->trade_name, $client->first_name, $client->last_name]));

                $userData = [
                    'name' => $full_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ];

                $user = User::where('client_id', $client->id)->first();

                if ($user) {
                    $user->update($userData);
                } else {
                    $userData['password'] = Hash::make(Str::random(10));
                    $userData['role'] = 'client';
                    $userData['client_id'] = $client->id;
                    User::create($userData);
                }
            }

            // 6. معالجة جهات الاتصال
            if ($request->has('contacts')) {
                $existingContacts = $client->contacts->keyBy('id');
                $newContacts = collect($request->contacts);

                // الحذف
                $contactsToDelete = $existingContacts->diffKeys($newContacts->whereNotNull('id')->keyBy('id'));
                $client->contacts()->whereIn('id', $contactsToDelete->keys())->delete();

                // التحديث والإضافة
                foreach ($request->contacts as $contact) {
                    if (isset($contact['id']) && $existingContacts->has($contact['id'])) {
                        $existingContacts[$contact['id']]->update($contact);
                    } else {
                        $client->contacts()->create($contact);
                    }
                }
            }

            // 7. تسجيل العملية في السجل
            ModelsLog::create([
                'type' => 'client',
                'type_id' => $client->id,
                'type_log' => 'update',
                'description' => 'تم تحديث بيانات العميل: ' . $client->trade_name,
                'created_by' => auth()->id(),
                'old_data' => json_encode($oldData),
                'new_data' => json_encode($client->getAttributes()),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✨ تم تحديث بيانات العميل بنجاح!',
                'redirect_url' => route('clients.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function edit_question($id)
    {
        $client = Client::findOrFail($id);
        $employees = Employee::all();
        $branches = Branch::all();
        $location = Location::where('client_id', $id)->first();

        // جلب جميع المجموعات المتاحة
        $Regions_groub = Region_groub::all();
        $GeneralClientSettings = GeneralClientSetting::all();
        // إذا كان الجدول فارغًا، قم بإنشاء قيم افتراضية (مفعلة بالكامل)
        if ($GeneralClientSettings->isEmpty()) {
            $defaultSettings = [['key' => 'image', 'name' => 'صورة', 'is_active' => true], ['key' => 'type', 'name' => 'النوع', 'is_active' => true], ['key' => 'birth_date', 'name' => 'تاريخ الميلاد', 'is_active' => true], ['key' => 'location', 'name' => 'الموقع على الخريطة', 'is_active' => true], ['key' => 'opening_balance', 'name' => 'الرصيد الافتتاحي', 'is_active' => true], ['key' => 'credit_limit', 'name' => 'الحد الائتماني', 'is_active' => true], ['key' => 'credit_duration', 'name' => 'المدة الائتمانية', 'is_active' => true], ['key' => 'national_id', 'name' => 'رقم الهوية الوطنية', 'is_active' => true], ['key' => 'addresses', 'name' => 'عناوين متعددة', 'is_active' => true], ['key' => 'link', 'name' => 'الرابط', 'is_active' => true]];

            // تحويل المصفوفة إلى مجموعة (Collection)
            $GeneralClientSettings = collect($defaultSettings)->map(function ($item) {
                return (object) $item; // تحويل المصفوفة إلى كائن
            });
        }
        $categories = CategoriesClient::all();

        return view('client::edit', compact('client', 'branches', 'employees', 'categories', 'Regions_groub', 'location', 'GeneralClientSettings'));
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);

        // التحقق من وجود فواتير مرتبطة بالعميل
        if ($client->invoices()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف العميل لأنه يحتوي على فواتير مرتبطة.');
        }

        // حذف المدفوعات المرتبطة
        if ($client->payments()->exists()) {
            $client->payments()->delete();
        }

        // حذف إشعارات الائتمان المرتبطة
        if ($client->creditNotifications()->exists()) {
            $client->creditNotifications()->delete();
        }

        // حذف مدخلات المجلة المرتبطة
        if ($client->journalEntries()->exists()) {
            $client->journalEntries()->delete();
        }

        // حذف المرفقات إذا وجدت
        if ($client->attachments) {
            $attachments = explode(',', $client->attachments);
            foreach ($attachments as $attachment) {
                $path = public_path('uploads/clients/' . trim($attachment));
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        // حذف العميل
        $client->delete();

        return redirect()->back()->with('success', 'تم حذف العميل وجميع البيانات المرتبطة به بنجاح');
    }
    public function show($id)
{
    // تحميل العميل المحدد مع جميع العلاقات الضرورية (مع استبعاد الفواتير الreturnedة)
    $client = Client::with([
               'invoices' => function ($query) {
    $query->where('type', '!=', 'returned')
          ->whereNotIn('id', function($subQuery) {
              $subQuery->select('reference_number')
                       ->from('invoices')
                       ->whereNotNull('reference_number');
          })
          ->orderBy('created_at', 'desc');
},
        'invoices.payments',
        'appointments' => function ($query) {
            $query->orderBy('created_at', 'desc');
        },
        'employee',
        'account',
        'payments' => function ($query) {
            $query->orderBy('created_at', 'desc');
        },
        'appointmentNotes' => function ($query) {
            $query->orderBy('created_at', 'desc');
        },
        'visits.employee' => function ($query) {
            $query->orderBy('created_at', 'desc');
        },
    ])->findOrFail($id);

    // تحميل البيانات الإضافية
    // $installment = Installment::with('invoice.client')->get();
    $employees = Employee::all();
    $account = Account::all();
    $statuses = Statuses::all();

    // تحميل الحجوزات والعضويات
    $bookings = Booking::where('client_id', $id)->get();
    $packages = Package::all();
    $memberships = Memberships::where('client_id', $id)->get();

    // تحميل الفواتير (مع التصفية الإضافية للتأكد)
    $invoices = $client->invoices->filter(function($invoice) {
        return $invoice->type != 'returned' &&
               ($invoice->reference_number === null ||
                ($invoice->referenceInvoice && $invoice->referenceInvoice->type != 'returned'));
    });

    $invoice_due = $invoices->sum('due_value');
    $due = Account::where('client_id', $id)->sum('balance');

    $payments = $client->payments()->orderBy('payment_date', 'desc')->get();

    // تحميل الملاحظات
    $appointmentNotes = $client->appointmentNotes;

    // تحميل الفئات والعلاقات الأخرى
    $categories = CategoriesClient::all();
    $ClientRelations = ClientRelation::where('client_id', $id)->get();
    $visits = $client->visits()->orderBy('created_at', 'desc')->get();

    // إنشاء كود جديد للعميل (إن وجد)
    do {
        $lastClient = Client::orderBy('code', 'desc')->first();
        $newCode = $lastClient ? $lastClient->code + 1 : 1;
    } while (Client::where('code', $newCode)->exists());

    $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

    $account = Account::where('client_id', $id)->first();

    if (!$account) {
        return redirect()->back()->with('error', 'لا يوجد حساب مرتبط بهذا العميل.');
    }

    $accountId = $account->id;
    // جلب بيانات الخزينة
    $treasury = $this->getTreasury($accountId);
    $branches = $this->getBranches();

    // جلب العمليات المالية
    $transactions = $this->getTransactions($accountId);
    $transfers = $this->getTransfers($accountId);
    $expenses = $this->getExpenses($accountId);
    $revenues = $this->getRevenues($accountId);

    // معالجة العمليات وحساب الرصيد
    $allOperations = $this->processOperations($transactions, $transfers, $expenses, $revenues, $treasury);

    // ترتيب العمليات حسب التاريخ
    usort($allOperations, function ($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    // تقسيم العمليات إلى صفحات
    $operationsPaginator = $this->paginateOperations($allOperations);

    // إرسال البيانات إلى الواجهة
    return view('client::show', compact(
        'client', 'treasury', 'account', 'operationsPaginator', 'branches',
        'ClientRelations', 'visits', 'due', 'invoice_due', 'statuses',
        'account', 'employees', 'bookings', 'packages',
        'memberships', 'invoices', 'payments', 'appointmentNotes', 'account_setting'
    ));
}
    public function updateStatus(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->notes = $request->notes; // تحديث الملاحظات بالحالة الجديدة
        $client->save();

        return response()->json(['success' => true]);
    }

    public function contact()
    {
        $clients = Client::all();

        return view('client::contacts.contact_mang', compact('clients'));
    }

    public function contacts(Request $request)
    {
        $query = Client::query()->with(['employee', 'status']);

        // البحث الأساسي (يشمل جميع الحقول المهمة)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('trade_name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhereHas('employee', function ($q) use ($search) {
                        $q->where('trade_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('status', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // البحث المتقدم (حسب الحقول المحددة)
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }

        if ($request->filled('mobile')) {
            $query->where('mobile', 'like', '%' . $request->input('mobile') . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->input('status_id'));
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->input('city') . '%');
        }

        if ($request->filled('region')) {
            $query->where('region', 'like', '%' . $request->input('region') . '%');
        }

        $clients = $query->paginate(25)->withQueryString();

        $employees = Employee::all(); // لاستخدامها في dropdown الموظفين
        $statuses = Statuses::all(); // لاستخدامها في dropdown الحالات

        return view('client::contacts.contact_mang', compact('clients', 'employees', 'statuses'));
    }

    public function show_contant($id)
    {
        $client = Client::with(['appointments.notes', 'appointments.client'])->findOrFail($id);
        $notes = AppointmentNote::with(['appointment', 'user'])
            ->whereHas('appointment', function ($query) use ($id) {
                $query->where('client_id', $id);
            })
            ->latest()
            ->get();

        return view('client.contacts.show_contant', compact('client', 'notes'));
    }
    public function mang_client(Request $request)
    {
        $clientGroups=Region_groub::all();
        $invoices=Invoice::all();
        $notes=ClientRelation::all();
        $clients = Client::with([
            'invoices',
            'appointmentNotes.employee',
            'clientRelations' => function ($query) {
                $query->with(['employee', 'location'])->orderBy('date', 'desc');
            },
        ])
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->full_name,
                    'phone' => $client->phone,
                    'balance' => $client->balance,
                    'invoices' => $client->invoices->map(function ($invoice) {
                        return [
                            'id' => $invoice->id,
                            'number' => $invoice->code,
                            'date' => $invoice->invoice_date->format('Y-m-d'),
                            'amount' => $invoice->grand_total,
                            'status' => $invoice->payment_status,
                            'remaining' => $invoice->remaining_amount,
                            'paymentMethod' => $invoice->payment_method,
                        ];
                    }),
                    'notes' => $client->appointmentNotes->map(function ($note) {
                        return [
                            'id' => $note->id,
                            'date' => $note->date,
                            'employee' => $note->employee->name ?? 'غير محدد',
                            'content' => $note->description,
                            'status' => $note->status,
                        ];
                    }),
                    'relations' => $client->clientRelations->map(function ($relation) {
                        return [
                            'id' => $relation->id,
                            'status' => $relation->status,
                            'process' => $relation->process,
                            'time' => $relation->time,
                            'date' => $relation->date,
                            'employee' => $relation->employee->name ?? 'غير محدد',
                            'description' => $relation->description,
                            'location' => $relation->location
                                ? [
                                    'id' => $relation->location->id,
                                    'address' => $relation->location->address,
                                    'coordinates' => $relation->location->coordinates,
                                ]
                                : null,
                            'site_type' => $relation->site_type,
                            'competitor_documents' => $relation->competitor_documents,
                            'additional_data' => $relation->additional_data,
                        ];
                    }),
                ];
            });

        return view('client::relestion_mang_client', [
            'clients' => $clients,
            'invoices'=>$invoices,
            'notes'=>$notes,
            'clientGroups'=>$clientGroups
        ]);
    }
    public function getClientData($clientId)
    {
        $client = Client::find($clientId);

        if (!$client) {
            return response()->json(['error' => 'العميل غير موجود'], 404);
        }

        // جلب الفواتير الخاصة بهذا العميل فقط
        $invoices = Invoice::where('client_id', $clientId)
            ->with(['client', 'createdByUser', 'employee'])
            ->orderBy('created_at', 'desc')
            ->get();

        // جلب الملاحظات الخاصة بهذا العميل
        $notes = ClientRelation::where('client_id', $clientId)->with('employee')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'client' => $client,
            'invoices' => $invoices,
            'notes' => $notes,
        ]);
    }
    // public function getClientDetails($id)
    // {
    //     $client = Client::findOrFail($id);
    //     $ClientRelations = ClientRelation::where('client_id', $id)->get();
    //     $categories = CategoriesClient::all();
    //     $employees = Employee::all();
    //     $notes = AppointmentNote::with(['user'])
    //         ->latest()
    //         ->get();
    //     $appointments = Appointment::all();

    //     return response()->json([
    //         'success' => true,
    //         'html' => view('client.partials.client_details', [
    //             'client' => $client,
    //             'ClientRelations' => $ClientRelations,
    //             'categories' => $categories,
    //             'employees' => $employees,
    //             'notes' => $notes,
    //             'appointments' => $appointments,
    //         ])->render(),
    //     ]);
    // }
    public function getAllClients()
    {
        $clients = Client::with('latestStatus')->orderBy('created_at', 'desc')->get();
        return response()->json($clients);
    }
    // public function getClientNotes($client_id)
    // {
    //     try {
    //         $ClientRelations = ClientRelation::where('client_id', $client_id)->get();

    //         // التحقق من وجود ملاحظات
    //         if ($ClientRelations->isEmpty()) {
    //             return response()->json(['message' => 'لا توجد ملاحظات لهذا العميل.'], 200);
    //         }

    //         return response()->json($ClientRelations, 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'حدث خطأ أثناء جلب البيانات', 'details' => $e->getMessage()], 500);
    //     }
    // }
    public function getNextClient(Request $request)
    {
        $currentClientId = $request->query('currentClientId');
        $nextClient = Client::where('id', '>', $currentClientId)->orderBy('id', 'asc')->first();

        if ($nextClient) {
            $nextClient->load('notes'); // تحميل الملاحظات المرتبطة
            return response()->json(['client' => $nextClient]);
        }

        return response()->json(['client' => null]);
    }
    public function updateOpeningBalance(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->opening_balance = $request->opening_balance;
        $client->save();

        $Account = Account::where('client_id', $id)->first();
        if ($Account) {
            $Account->balance += $client->opening_balance;
            $Account->save(); // حفظ التعديل في قاعدة البيانات
        }
        if ($client->opening_balance > 0) {
            $journalEntry = JournalEntry::create([
                'reference_number' => $client->code,
                'date' => now(),
                'description' => 'رصيد افتتاحي للعميل : ' . $client->trade_name,
                'status' => 1,
                'currency' => 'SAR',
                'client_id' => $client->id,
                // 'invoice_id' => $$client->id,
                // 'created_by_employee' => Auth::id(),
            ]);

            // // 1. حساب العميل (مدين)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $Account->id, // حساب العميل
                'description' => 'رصيد افتتاحي للعميل : ' . $client->trade_name,
                'debit' => $client->opening_balance ?? 0, // المبلغ الكلي للفاتورة (مدين)
                'credit' => 0,
                'is_debit' => true,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function getPreviousClient(Request $request)
    {
        $currentClientId = $request->query('currentClientId');
        $previousClient = Client::where('id', '<', $currentClientId)->orderBy('id', 'desc')->first();

        if ($previousClient) {
            $previousClient->load('notes'); // تحميل الملاحظات المرتبطة
            return response()->json(['client' => $previousClient]);
        }

        return response()->json(['client' => null]);
    }
    public function getFirstClient()
    {
        $firstClient = Client::orderBy('id', 'asc')->first();
        if ($firstClient) {
            $firstClient->load('notes');
            return response()->json(['client' => $firstClient]);
        }
        return response()->json(['client' => null]);
    }

    public function mang_client_store(ClientRequest $request)
    {
        $data_request = $request->except('_token');

        // إنشاء العميل
        $client = new Client();

        // الحصول على الرقم الحالي لقسم العملاء من جدول serial_settings
        $serialSetting = SerialSetting::where('section', 'customer')->first();

        // إذا لم يتم العثور على إعدادات، نستخدم 1 كقيمة افتراضية
        $currentNumber = $serialSetting ? $serialSetting->current_number : 1;

        // تعيين id للعميل الجديد باستخدام الرقم الحالي
        // $client->id = $currentNumber;

        // تعيين الكود للعميل الجديد (إذا كان الكود مطلوبًا أيضًا)
        $client->code = $currentNumber;

        // تعبئة البيانات الأخرى
        $client->fill($data_request);

        // معالجة الصورة
        if ($request->hasFile('attachments')) {
            $file = $request->file('attachments');
            if ($file->isValid()) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/'), $filename);
                $client->attachments = $filename;
            }
        }

        // حفظ العميل
        $client->save();

        // تسجيل اشعار نظام جديد
        ModelsLog::create([
            'type' => 'client',
            'type_id' => $client->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم اضافة  عميل **' . $client->trade_name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        // زيادة الرقم الحالي بمقدار 1
        if ($serialSetting) {
            $serialSetting->update(['current_number' => $currentNumber + 1]);
        }

        // إنشاء حساب فرعي باستخدام trade_name
        $customers = Account::where('name', 'العملاء')->first(); // الحصول على حساب العملاء الرئيسي
        if ($customers) {
            $customerAccount = new Account();
            $customerAccount->name = $client->trade_name; // استخدام trade_name كاسم الحساب
            $customerAccount->client_id = $client->id;

            // تعيين كود الحساب الفرعي بناءً على كود الحسابات
            $lastChild = Account::where('parent_id', $customers->id)->orderBy('code', 'desc')->first();
            $newCode = $lastChild ? $this->generateNextCode($lastChild->code) : $customers->code . '1'; // استخدام نفس منطق توليد الكود
            $customerAccount->code = $newCode; // تعيين الكود الجديد للحساب الفرعي

            $customerAccount->balance_type = 'debit'; // أو 'credit' حسب الحاجة
            $customerAccount->parent_id = $customers->id; // ربط الحساب الفرعي بحساب العملاء
            $customerAccount->is_active = false;
            $customerAccount->save();
        }

        // حفظ جهات الاتصال المرتبطة بالعميل
        if ($request->has('contacts') && is_array($request->contacts)) {
            foreach ($request->contacts as $contact) {
                $client->contacts()->create($contact);
            }
        }

        return redirect()->route('clients.mang_client')->with('success', '✨ تم إضافة العميل بنجاح!');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (is_null($lat1) || is_null($lon1) || is_null($lat2) || is_null($lon2)) {
            return 0;
        }

        if (abs($lat1) > 90 || abs($lon1) > 180 || abs($lat2) > 90 || abs($lon2) > 180) {
            return 0;
        }

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
        $c = 2 * asin(sqrt($a));
        $distance = 6371000 * $c; // بالمتر

        return $distance / 1000; // تحويل إلى كيلومتر
    }

    public function forceShow(Client $client)
    {
        if (auth()->user()->role !== 'manager') {
            abort(403, 'أنت لا تملك الصلاحية لتنفيذ هذا الإجراء.');
        }

        $client->update([
            'force_show' => true,
            'last_note_at' => null,
        ]);

        ModelsLog::create([
            'type' => 'client',
            'type_log' => 'update',
            'description' => 'تم إظهار العميل ' . $client->trade_name . ' في الخريطة قبل انتهاء المدة',
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إظهار العميل في الخريطة بنجاح');
    }

    // دالة مساعدة لحساب المسافة

    /**
     * حساب المسافة باستخدام Haversine formula
     */

    public function mang_client_details($id)
    {
        try {
            // Find the client
            $client = Client::with(['employee'])->findOrFail($id);

            // Get all clients for the sidebar
            $clients = Client::orderBy('created_at', 'desc')->get();

            // Get notes and appointments
            $notes = AppointmentNote::with(['user'])
                ->latest()
                ->get();
            $appointments = Appointment::all();
            $employees = Employee::all();

            // Get previous and next client IDs
            $previousClient = Client::where('id', '<', $id)->orderBy('id', 'desc')->first();
            $nextClient = Client::where('id', '>', $id)->orderBy('id', 'asc')->first();

            // If it's an AJAX request, return JSON
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'client' => $client,
                        'trade_name' => $client->trade_name,
                        'phone' => $client->phone,
                        'email' => $client->email,
                        'status' => $client->status,
                        'employee' => $client->employee
                            ? [
                                'name' => $client->employee->name,
                                'department' => $client->employee->department,
                                'role' => $client->employee->role,
                            ]
                            : null,
                    ],
                ]);
            }

            // For regular requests, return the view
            return view('client.relestion_mang_client', compact('clients', 'client', 'employees', 'notes', 'appointments', 'previousClient', 'nextClient'));
        } catch (\Exception $e) {
            // If it's an AJAX request, return error response
            if (request()->ajax()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'حدث خطأ أثناء تحميل بيانات العميل',
                    ],
                    422,
                );
            }

            // For regular requests, redirect with error
            return redirect()->route('clients.mang_client')->with('error', 'حدث خطأ أثناء تحميل بيانات العميل');
        }
    }
    public function search(Request $request)
    {
        try {
            $searchTerm = $request->query('query'); // تغيير اسم المتغير هنا

            $clients = Client::with(['latestStatus', 'employee'])
                ->where(function ($queryBuilder) use ($searchTerm) {
                    // استخدام اسم مختلف للـ Query Builder
                    $queryBuilder
                        ->where('trade_name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('code', 'like', '%' . $searchTerm . '%')
                        ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $clients->items(),
                'pagination' => [
                    'total' => $clients->total(),
                    'current_page' => $clients->currentPage(),
                    'last_page' => $clients->lastPage(),
                    'per_page' => $clients->perPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'حدث خطأ أثناء البحث',
                ],
                500,
            );
        }
    }

    public function assignEmployees(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'employee_id' => 'required|array',
            'employee_id.*' => 'exists:employees,id',
        ]);

        try {
            // البحث عن العميل
            $client = Client::findOrFail($request->client_id);

            // بدء المعاملة
            DB::beginTransaction();

            // مزامنة الموظفين (إضافة وإزالة)
            $client->employees()->sync($request->employee_id);

            // إنهاء المعاملة
            DB::commit();

            // إعادة التوجيه مع رسالة نجاح
            return redirect()->back()->with('success', 'تم تعيين الموظفين بنجاح');
        } catch (\Exception $e) {
            // إلغاء المعاملة في حالة الخطأ
            DB::rollBack();

            // تسجيل الخطأ
            Log::error('خطأ في تعيين الموظفين: ' . $e->getMessage());

            // إعادة التوجيه مع رسالة خطأ
            return redirect()->back()->with('error', 'حدث خطأ أثناء تعيين الموظفين');
        }
    }

    /**
     * إزالة موظف محدد من عميل
     */
    public function removeEmployee(Request $request, $clientId)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->with('error', 'خطأ في البيانات المدخلة');
        }

        try {
            // البحث عن العميل
            $client = Client::findOrFail($clientId);

            // إزالة الموظف
            $client->employees()->detach($request->employee_id);

            // تسجيل عملية الإزالة
            Log::info('تمت إزالة الموظف', [
                'client_id' => $clientId,
                'employee_id' => $request->employee_id,
            ]);

            // إعادة التوجيه مع رسالة نجاح
            return redirect()->back()->with('success', 'تم إزالة الموظف بنجاح');
        } catch (\Exception $e) {
            // تسجيل الخطأ
            Log::error('خطأ في إزالة الموظف', [
                'message' => $e->getMessage(),
                'client_id' => $clientId,
                'employee_id' => $request->employee_id,
            ]);

            // إعادة التوجيه مع رسالة خطأ
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء إزالة الموظف: ' . $e->getMessage());
        }
    }

    /**
     * جلب الموظفين المعينين لعميل
     */
    public function getAssignedEmployees($clientId)
    {
        try {
            // البحث عن العميل مع الموظفين المرتبطين
            $client = Client::with('employees')->findOrFail($clientId);

            // إرجاع استجابة JSON
            return response()->json([
                'success' => true,
                'employees' => $client->employees->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->full_name,
                        'department' => $employee->department,
                        'role' => $employee->role,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            // تسجيل الخطأ
            Log::error('خطأ في جلب الموظفين المعينين: ' . $e->getMessage());

            // إرجاع استجابة خطأ
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء جلب الموظفين',
                ],
                500,
            );
        }
    }
    public function import(Request $request)
    {
        set_time_limit(500);
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        Excel::import(new ClientsImport(), $request->file('file'));

        return redirect()->back()->with('success', 'تم استيراد العملاء بنجاح!');
    }

    public function updateStatusClient(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'status_id' => 'required|exists:statuses,id',
        ]);

        DB::beginTransaction();

        try {
            $client = Client::findOrFail($request->client_id);
            $client->status_id = $request->status_id;
            $client->save();

            // الحصول على حالة "موقوف" و "تحت المراجعة"
            $suspendedStatus = Statuses::where('name', 'موقوف')->first();
            $underReviewStatus = Statuses::where('name', 'تحت المراجعة')->first();
            $currentUserId = auth()->id();

            // إذا كانت الحالة الجديدة هي "موقوف"
            if ($suspendedStatus && $request->status_id == $suspendedStatus->id) {
                $suspendedGroup = Region_groub::where('name', 'عملاء موقوفون')->first();

                if ($suspendedGroup) {
                    $neighborhood = Neighborhood::firstOrNew(['client_id' => $client->id]);
                    $neighborhood->region_id = $suspendedGroup->id;
                    $neighborhood->save();
                }
            }
            // إذا كانت الحالة الجديدة هي "تحت المراجعة"
            elseif ($underReviewStatus && $request->status_id == $underReviewStatus->id) {
                $neighborhood = Neighborhood::where('client_id', $client->id)->first();

                if ($neighborhood && ($regionGroup = Region_groub::find($neighborhood->region_id))) {
                    // الحصول على الموظف المرتبط بالمجموعة (على فرض أن كل مجموعة لها موظف واحد فقط)
                    $employeeGroup = EmployeeGroup::where('group_id', $regionGroup->id)->first();

                    if ($employeeGroup && $employeeGroup->employee) {
                        notifications::create([
                            'user_id' => $currentUserId,
                            'receiver_id' => $employeeGroup->employee->id,
                            'title' => 'مراجعة عميل',
                            'description' => 'تم تحويل العميل "' . $client->trade_name . '" إلى تحت المراجعة.',
                            'read' => 0,
                            'type' => 'مراجعة عميل',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم تغيير حالة العميل بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in updateStatusClient: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function statement($id)
{
    $client = Client::find($id);

    $account = Account::where('client_id', $id)->first();

    if (!$account) {
        return redirect()->back()->with('error', 'لا يوجد حساب مرتبط بهذا العميل.');
    }

    $accountId = $account->id;

    // جلب بيانات الخزينة
    $treasury = $this->getTreasury($accountId);
    $branches = $this->getBranches();

    // جلب العمليات المالية
    $transactions = $this->getTransactions($accountId);
    $transfers = $this->getTransfers($accountId);
    $expenses = $this->getExpenses($accountId);
    $revenues = $this->getRevenues($accountId);

    // معالجة العمليات وحساب الرصيد
    $allOperations = $this->processOperations($transactions, $transfers, $expenses, $revenues, $treasury);

    // ترتيب العمليات حسب التاريخ (من الأقدم للأحدث)
    usort($allOperations, function ($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']);
    });

    // حساب الرصيد بعد كل عملية
    $runningBalance = $client->opening_balance ?? 0;

    foreach ($allOperations as &$operation) {
        // إضافة الإيداعات
        if ($operation['deposit']) {
            $runningBalance += $operation['deposit'];
        }

        // طرح المسحوبات
        if ($operation['withdraw']) {
            $runningBalance -= $operation['withdraw'];
        }

        // تحديث الرصيد بعد العملية
        $operation['balance_after'] = $runningBalance;
    }
    unset($operation); // كسر المرجع

    // ترتيب العمليات من الأحدث للأقدم للعرض
    $allOperations = array_reverse($allOperations);

    // تقسيم العمليات إلى صفحات
    $operationsPaginator = $this->paginateOperations($allOperations);

    // جلب معلومات الشركة من الإعدادات
    // $company_name = Setting::get('company_name', 'موسسة اعمال خاصة');
    // $company_logo = Setting::get('company_logo');
    // $company_description = Setting::get('company_description');
    // $company_address = Setting::get('company_address');
    // $company_phone = Setting::get('company_phone');
    // $company_email = Setting::get('company_email');

    // إرسال البيانات إلى الواجهة
    return view('client::statement', compact(
        'treasury',
        'account',
        'operationsPaginator',
        'branches',
        'client',

    ));
}
    private function getTreasury($id)
    {
        return Account::findOrFail($id);
    }

    private function getBranches()
    {
        return Branch::all();
    }

    private function getTransactions($id)
    {
        return JournalEntryDetail::where('account_id', $id)
            ->with([
                'journalEntry' => function ($query) {
                    $query->with('invoice', 'client');
                },
            ])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getTransfers($id)
    {
        return JournalEntry::whereHas('details', function ($query) use ($id) {
            $query->where('account_id', $id);
        })
            ->with(['details.account'])
            ->where('description', 'تحويل المالية')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getExpenses($id)
    {
        return Expense::where('treasury_id', $id)
            ->with(['expenses_category', 'vendor', 'employee', 'branch', 'client'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getRevenues($id)
    {
        return Revenue::where('treasury_id', $id)
            ->with(['account', 'paymentVoucher', 'treasury', 'bankAccount', 'journalEntry'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function processOperations($transactions, $transfers, $expenses, $revenues, $treasury)
    {
        $currentBalance = 0;
        $allOperations = [];

        // معالجة المدفوعات
        foreach ($transactions as $transaction) {
            $amount = $transaction->debit > 0 ? $transaction->debit : $transaction->credit;
            $type = $transaction->debit > 0 ? 'إيداع' : 'سحب';

            $currentBalance = $this->updateBalance($currentBalance, $amount, $type);

            $allOperations[] = [
                'operation' => $transaction->description,
                'deposit' => $type === 'إيداع' ? $amount : 0,
                'withdraw' => $type === 'سحب' ? $amount : 0,
                'balance_after' => $currentBalance,

                'journalEntry' => $transaction->journalEntry->id,
                'date' => $transaction->journalEntry->date,
                'invoice' => $transaction->journalEntry->invoice,
                'client' => $transaction->journalEntry->client,
                'type' => 'transaction',
            ];
        }

        foreach ($expenses as $expense) {
            $currentBalance -= $expense->amount;

            $allOperations[] = [
                'operation' => 'سند صرف: ' . $expense->description,
                'deposit' => 0,
                'withdraw' => $expense->amount,
                'balance_after' => $currentBalance,
                'date' => $expense->date,
                'invoice' => null,
                'client' => $expense->client,
                'type' => 'expense',
            ];
        }

        // معالجة سندات القبض
        foreach ($revenues as $revenue) {
            $currentBalance += $revenue->amount;

            $allOperations[] = [
                'operation' => 'سند قبض: ' . $revenue->description,
                'deposit' => $revenue->amount,
                'withdraw' => 0,
                'balance_after' => $currentBalance,
                'date' => $revenue->date,
                'invoice' => null,
                'client' => null,
                'type' => 'revenue',
            ];
        }

        return $allOperations;
    }

    private function updateBalance($currentBalance, $amount, $type)
    {
        return $type === 'إيداع' ? $currentBalance + $amount : $currentBalance - $amount;
    }

    private function paginateOperations($allOperations)
    {
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedOperations = array_slice($allOperations, $offset, $perPage);

        return new \Illuminate\Pagination\LengthAwarePaginator($paginatedOperations, count($allOperations), $perPage, $currentPage, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }

    public function addnotes(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'process' => 'required|string|max:255',
            'description' => 'required|string',
            'deposit_count' => 'nullable|integer|min:0',
            'competitor_documents' => 'nullable|integer|min:0',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,txt,mp4,webm,ogg|max:102400',
            'current_latitude' => 'nullable|numeric',
            'current_longitude' => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try {
            // التحقق من الموقع للموظفين فقط
            if (auth()->user()->role === 'employee') {
                $employeeLocation = Location::where('employee_id', auth()->id())
                    ->latest()
                    ->firstOrFail();

                $clientLocation = Location::where('client_id', $request->client_id)->latest()->firstOrFail();

                $distance = $this->calculateDistance($employeeLocation->latitude, $employeeLocation->longitude, $clientLocation->latitude, $clientLocation->longitude);

                if ($distance > 0.3) {
                    throw new \Exception('يجب أن تكون ضمن نطاق 0.3 كيلومتر من العميل! المسافة الحالية: ' . round($distance, 2) . ' كم');
                }
            }

            // تحديث حالة الزيارة إلى
            EmployeeClientVisit::where('employee_id', auth()->id())
                ->where('client_id', $request->client_id)
                ->update(['status' => 'active']);

            // الحصول على بيانات العميل والحالات
            $client = Client::findOrFail($request->client_id);
            $underReviewStatus = Statuses::where('name', 'تحت المراجعة')->first();
            $activeStatus = Statuses::where('name', 'نشط')->first();
            $followUpStatus = Statuses::where('name', 'متابعة')->first();

            // إنشاء سجل الملاحظة
            $clientRelation = ClientRelation::create([
                'employee_id' => auth()->id(),
                'client_id' => $request->client_id,
                'status' => $request->status ?? 'pending',
                'process' => $request->process,
                'description' => $request->description,
                'deposit_count' => $request->deposit_count,
                'competitor_documents' => $request->competitor_documents,
                'additional_data' => json_encode([
                    'deposit_count' => $request->deposit_count,
                    'competitor_documents' => $request->competitor_documents,
                    'latitude' => $request->current_latitude,
                    'longitude' => $request->current_longitude,
                ]),
            ]);

            // إنشاء إشعار واحد فقط بناء على نوع الإجراء
            $notificationData = [
                'user_id' => auth()->id(),
                'receiver_id' => auth()->id(), // افتراضيًا للموظف نفسه
                'type' => 'client_note',
                'title' => 'تم إضافة ملاحظة للعميل ' . $client->trade_name,
                'description' => 'نوع الإجراء: ' . $request->description,
                'read' => false,
            ];

            // إذا كان الإجراء هو إبلاغ المشرف
            if (in_array($request->process, ['إبلاغ المشرف', 'متابعة'])) {
                $supervisor = null;
                if ($request->process === 'إبلاغ المشرف') {
                    $supervisor = User::where('role', 'manager')
                        ->where(function ($query) {
                            $query->where('id', auth()->user()->supervisor_id)->orWhere('role', 'manager');
                        })
                        ->first();
                }

                if ($followUpStatus && ($request->process === 'متابعة' || $supervisor)) {
                    // حفظ الحالة الحالية للموظف داخل الملاحظة
                    $currentStatusForEmployee = $client->status_id;

                    // تحديث حالة العميل إلى "متابعة"
                    $client->status_id = $followUpStatus->id;
                    $client->save();

                    // تحقق إن الموظف ما سبق له عمل إبلاغ مشرف لنفس العميل
                    $previousNote = ClientRelation::where('client_id', $client->id)
                        ->where('employee_id', auth()->id())
                        ->where('process', 'إبلاغ المشرف')
                        ->whereNotNull('employee_view_status')
                        ->first();

                    if (!$previousNote) {
                        // أول مرة يرسل إبلاغ مشرف → خزّن له الحالة الأصلية
                        $clientRelation->update([
                            'employee_view_status' => $currentStatusForEmployee,
                        ]);
                    }

                    // تسجيل اللوج
                    ModelsLog::create([
                        'type' => 'status_change',
                        'type_log' => 'log',
                        'description' => 'تم تغيير حالة العميل إلى "متابعة" بسبب: ' . $request->process,
                        'created_by' => auth()->id(),
                        'related_id' => $client->id,
                        'related_type' => Client::class,
                    ]);

                    // تحديث الإشعار إذا كان "إبلاغ مشرف"
                    if ($request->process === 'إبلاغ المشرف' && $supervisor) {
                        $notificationData['receiver_id'] = $supervisor->id;
                        $notificationData['type'] = 'supervisor_alert';
                        $notificationData['title'] = 'إبلاغ عن مشكلة عميل - ' . $client->trade_name;
                        $notificationData['description'] = 'يوجد مشكلة تحتاج متابعة مع العميل ' . $client->trade_name . ' -  ' . $client->code . ': ' . $request->description;
                    }
                }
            }

            // إنشاء الإشعار
            notifications::create($notificationData);

            // تغيير حالة العميل إذا كان تحت المراجعة وأصبح نشط
            if ($underReviewStatus && $activeStatus && $client->status_id == $underReviewStatus->id) {
                $client->status_id = $activeStatus->id;
                $client->save();

                ModelsLog::create([
                    'type' => 'status_change',
                    'type_log' => 'log',
                    'description' => 'تم تغيير حالة العميل من "تحت المراجعة" إلى "نشط" تلقائياً',
                    'created_by' => auth()->id(),
                    'related_id' => $client->id,
                    'related_type' => Client::class,
                ]);
            }

            // تحديث وقت آخر ملاحظة للعميل
            $client->last_note_at = now();
            $client->save();

            // حفظ المرفقات إذا وجدت
            if ($request->hasFile('attachments')) {
                $attachments = [];
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('assets/uploads/notes'), $filename);
                        $attachments[] = $filename;
                    }
                }
                $clientRelation->attachments = json_encode($attachments);
                $clientRelation->save();
            }

            // تحديث موقع الموظف (إذا كان employee)
            if (auth()->user()->role === 'employee') {
                Location::where('employee_id', auth()->id())
                    ->latest()
                    ->first()
                    ->update([
                        'client_relation_id' => $clientRelation->id,
                        'client_id' => $request->client_id,
                        'latitude' => $request->current_latitude,
                        'longitude' => $request->current_longitude,
                    ]);
            }

            // تسجيل السجل العام
            ModelsLog::create([
                'type' => 'client_note',
                'type_log' => 'log',
                'description' => 'تم إضافة ملاحظة للعميل: ' . $request->description,
                'created_by' => auth()->id(),
                'related_id' => $client->id,
                'related_type' => Client::class,
            ]);

            DB::commit();

            return redirect()
                ->route('clients.show', $request->client_id)
                ->with('success', 'تم إضافة الملاحظة بنجاح' . ($client->wasChanged('status_id') ? ' وتغيير حالة العميل إلى متابعة!' : '!'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'فشل إضافة الملاحظة: ' . $e->getMessage())
                ->withInput();
        }
    }
}
