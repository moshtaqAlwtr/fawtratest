<?php

namespace App\Http\Controllers;

use App\Models\CreditLimit;
use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\User;
use App\Models\Target;
use App\Models\EmployeeTarget;
use App\Models\Location;
use App\Models\Client;
use App\Models\Neighborhood;
use App\Models\Region_groub;
use App\Models\Statuses;
use Illuminate\Http\Request;
use App\Models\PaymentsProcess;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\Expense;
use App\Models\ClientEmployee;
use Carbon\Carbon;



class EmployeeTargetController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'employee')->get(); // يمكنك تخصيصهم حسب النوع إذا أردت
        return view('employee_targets.index', compact('employees'));
    }
    public function showGeneralTarget()
    {
        // جلب الهدف الأول أو إنشائه إذا لم يكن موجوداً
        $target = Target::firstOrCreate(['id' => 1], ['value' => 30000]);

        return view('employee_targets.general', compact('target'));
    }


    public function visitTarget()
    {
        // جلب الهدف الأول أو إنشائه إذا لم يكن موجوداً
        // الهدف العام للزيارات
        $target = Target::firstOrCreate(['id' => 3], ['value' => 1000]);

        return view('employee_targets.visitTarget', compact('target'));
    }
    public function client_target_store(Request $request)
    {
        $request->validate([
            'value' => 'required|numeric',
        ]);

        $target = Target::updateOrCreate(['id' => 2], $request->only(['value']));

        return redirect()->back()->with('success', 'تم تحديث الهدف بنجاح');
    }
    public function client_target(Request $request)
    {
        $user = auth()->user();

        $baseQuery = Client::with(['employee', 'status:id,name,color', 'locations', 'Neighborhoodname.Region', 'branch:id,name']);

        $noClients = false;

        // تحديد الصلاحيات حسب الدور
        if ($user->role === 'employee') {
            // الموظف يرى فقط العملاء المرتبطين بالمجموعات الخاصة به
            $employeeGroupIds = EmployeeGroup::where('employee_id', $user->employee_id)->pluck('group_id');

            if ($employeeGroupIds->isNotEmpty()) {
                $baseQuery->whereHas('Neighborhoodname.Region', function ($q) use ($employeeGroupIds) {
                    $q->whereIn('id', $employeeGroupIds);
                });
            } else {
                // لا توجد مجموعات → لا توجد عملاء
                $noClients = true;
            }
        } elseif ($user->role === 'manager') {
            // المدير يرى جميع العملاء → لا فلترة
        }

        // فلترة البحث حسب الطلب
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

        // استعلام الخريطة
        $mapQuery = clone $baseQuery;
        $allClients = $noClients ? collect() : $mapQuery->with(['status_client:id,name,color', 'locations:id,client_id,latitude,longitude', 'Neighborhoodname.Region', 'branch:id,name'])->get();

        // موقع الموظف
        $userLocation = Location::where('employee_id', $user->employee_id)->latest()->first();

        // تنفيذ الاستعلام مع التقسيم

        $target = Target::find(2)->value ?? 648; // الهدف العام لتحصيل كل عميل

        // الحصول على الشهر (أو NULL في البداية لجلب كل الشهور)
        $month = $request->input('month');
        $year = null;
        $monthNum = null;

        if ($month) {
            [$year, $monthNum] = explode('-', $month);
        }
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        // جلب جميع العملاء
        //  $clientsRaw = $noClients ? collect() : $baseQuery->get();
        $clients = Client::with(['employee', 'Neighborhoodname.Region', 'branch', 'locations'])
            ->whereNotIn('status_id', ['2', '6'])
            ->get();
        $clients = $clients
            ->map(function ($client) use ($target, $monthNum, $year, $dateFrom, $dateTo) {
                // مجموع المدفوعات

                $returnedInvoiceIds = Invoice::whereNotNull('reference_number')->pluck('reference_number')->toArray();

                // الفواتير الأصلية التي يجب استبعادها = كل فاتورة تم عمل راجع لها
                // بالإضافة إلى الفواتير التي تم تصنيفها صراحةً على أنها راجعة
                $excludedInvoiceIds = array_unique(array_merge($returnedInvoiceIds, Invoice::where('type', 'returned')->pluck('id')->toArray()));

                $invoiceIds = Invoice::where('client_id', $client->id)->where('type', 'normal')->whereNotIn('id', $excludedInvoiceIds)->pluck('id');

                $paymentsQuery = PaymentsProcess::whereIn('invoice_id', $invoiceIds);

                if ($monthNum && $year) {
                    $paymentsQuery->whereMonth('created_at', $monthNum)->whereYear('created_at', $year);
                }

                if ($dateFrom && $dateTo) {
                    $paymentsQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
                }
                $paymentsTotal = $paymentsQuery->sum('amount');

                // مجموع سندات القبض
                $receiptsTotal = Receipt::whereHas('account', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })->when($monthNum && $year, function ($query) use ($monthNum, $year) {
                    $query->whereMonth('created_at', $monthNum)->whereYear('created_at', $year);
                });

                if ($dateFrom && $dateTo) {
                    $receiptsTotal->whereBetween('created_at', [$dateFrom, $dateTo]);
                }
                $receipts = $receiptsTotal->sum('amount');

                $collected = $paymentsTotal + $receipts;
                $percentage = $target > 0 ? round(($collected / $target) * 100, 2) : 0;

                // تحديد المجموعة حسب النسبة
                if ($percentage > 100) {
                    $group = 'G';
                    $group_class = 'primary';
                } elseif ($percentage >= 60) {
                    $group = 'K';
                    $group_class = 'success';
                } elseif ($percentage >= 30) {
                    $group = 'B';
                    $group_class = 'warning';
                } elseif ($percentage >= 10) {
                    $group = 'C';
                    $group_class = 'danger';
                } else {
                    $group = 'D';
                    $group_class = 'secondary'; // أو dark إن أردت
                }

                // إضافة الخصائص للعميل
                $client->collected = $collected;
                $client->percentage = $percentage;
                $client->payments = $paymentsTotal;
                $client->receipts = $receipts;
                $client->group = $group;
                $client->group_class = $group_class;

                return $client;
            })
            ->sortByDesc('collected')
            ->values(); // ✅ الترتيب من الأعلى للأقل

        // بيانات إضافية للعرض
        return view('employee_targets.client', [
            'clients' => $clients, // يحتوي بالفعل على جميع الخصائص المطلوبة
            'allClients' => $allClients,
            'month' => $month,
            'target' => $target,
            'Neighborhoods' => Neighborhood::all(),
            'users' => User::all(),
            'employees' => Employee::all(),
            'creditLimit' => CreditLimit::first(),
            'statuses' => Statuses::select('id', 'name', 'color')->get(),
            'Region_groups' => Region_groub::all(),
            'userLocation' => $userLocation,
            // تم إزالة: 'percentage'، 'group'، 'group_class'، 'collected'
        ]);
    }



public function daily_closing_entry(Request $request)
{
    $isRangeSearch = false;

    // تحديد نوع البحث
    if ($request->from_date && $request->to_date) {
        $isRangeSearch = true;
        $fromDate = Carbon::parse($request->from_date)->startOfDay();
        $toDate = Carbon::parse($request->to_date)->endOfDay();
        $selectedDate = null;
    } else {
        $selectedDate = $request->date ? Carbon::parse($request->date) : now();
        $fromDate = $selectedDate->copy()->startOfDay();
        $toDate = $selectedDate->copy()->endOfDay();
    }

    $employeeIds = User::whereHas('employee')->pluck('id');
    $defaultTarget = 10000;

    $cards = $employeeIds->map(function ($userId) use ($fromDate, $toDate, $defaultTarget) {
        $user = User::find($userId);

        // استبعاد الفواتير المرجعة
        $returnedInvoiceIds = Invoice::whereNotNull('reference_number')
            ->pluck('reference_number')->toArray();

        $excludedInvoiceIds = array_unique(array_merge(
            $returnedInvoiceIds,
            Invoice::where('type', 'returned')->pluck('id')->toArray()
        ));

        $invoiceIds = Invoice::where('created_by', $userId)
            ->where('type', 'normal')
            ->whereNotIn('id', $excludedInvoiceIds)
            ->pluck('id');

        // المبالغ المحصلة (من المدفوعات)
        $paymentsTotal = PaymentsProcess::whereIn('invoice_id', $invoiceIds)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->sum('amount');

        // سندات القبض
        $receiptsTotal = Receipt::where('created_by', $userId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->sum('amount');

        // سندات الصرف
        $expensesTotal = Expense::where('created_by', $userId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->sum('amount');

        $totalCollected = $paymentsTotal + $receiptsTotal - $expensesTotal;

        $target = $user->target?->monthly_target ?? $defaultTarget;
        $percentage = $target > 0 ? round(($totalCollected / $target) * 100, 2) : 0;

        return [
            'name' => $user?->name ?? 'غير معروف',
            'payments' => $paymentsTotal,
            'receipts' => $receiptsTotal,
            'expenses' => $expensesTotal,
            'total' => $totalCollected,
            'target' => $target,
            'percentage' => $percentage,
        ];
    });

    // ترتيب تنازلي حسب المحصل
    $cards = $cards->sortByDesc('total')->values();

    return view('daily_closing_entry', [
        'cards' => $cards,
        'selectedDate' => $selectedDate ? $selectedDate->toDateString() : null,
        'fromDate' => $request->from_date,
        'toDate' => $request->to_date,
        'isRangeSearch' => $isRangeSearch,
    ]);
}

 public function updatevisitTarget(Request $request)
    {
        $request->validate([
            'value' => 'required|numeric',
        ]);

        $target = Target::updateOrCreate(['id' => 3], $request->only(['value']));

        return redirect()->back()->with('success', 'تم تحديث الهدف بنجاح');
    }
    public function updateGeneralTarget(Request $request)
    {
        $request->validate([
            'value' => 'required|numeric',
        ]);

        $target = Target::updateOrCreate(['id' => 1], $request->only(['value']));

        return redirect()->back()->with('success', 'تم تحديث الهدف بنجاح');
    }
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'targets' => 'required|array',
            'targets.*.user_id' => 'required|exists:users,id',
            'targets.*.monthly_target' => 'nullable|numeric|min:0',
        ]);

        foreach ($request->targets as $targetData) {
            // تجاهل الحقول الفارغة
            if (!is_numeric($targetData['monthly_target'])) {
                continue;
            }

            EmployeeTarget::updateOrCreate(['user_id' => $targetData['user_id']], ['monthly_target' => $targetData['monthly_target']]);
        }

        return redirect()->route('employee_targets.index')->with('success', 'تم تحديث التارقت بنجاح!');
    }
}
