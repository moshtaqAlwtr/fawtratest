<?php

namespace Modules\Pos\Http\Controllers;
use App\Models\PosSession;
use App\Models\PosSessionDetail;
use App\Models\CashierDevice;
use App\Models\shifts_pos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SessionsController extends Controller
{
   

// تحديث دالة index في SessionsController

public function index(Request $request)
{
    $user = Auth::user();
    
    // التحقق من وجود جلسة نشطة
    $activeSession = PosSession::active()->forUser($user->id)->first();
    
    if ($activeSession) {
        // إذا كانت هناك جلسة نشطة، عرضها مباشرة
        return redirect()->route('pos.sessions.show', $activeSession->id);
    }
    
    // بناء الاستعلام الأساسي
    $query = PosSession::with(['user', 'device.store', 'shift'])
        ->where('user_id', $user->id);
    
    // تطبيق الفلاتر
    if ($request->filled('device_id')) {
        $query->where('device_id', $request->device_id);
    }
    
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    if ($request->filled('date')) {
        $query->whereDate('started_at', $request->date);
    }
    
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('session_number', 'LIKE', "%{$search}%")
              ->orWhereHas('user', function($userQuery) use ($search) {
                  $userQuery->where('name', 'LIKE', "%{$search}%");
              });
        });
    }
    
    // الترتيب
    $sortField = $request->get('sort', 'started_at');
    $sortDirection = $request->get('direction', 'desc');
    
    // التحقق من صحة حقل الترتيب
    $allowedSortFields = ['session_number', 'started_at', 'ended_at', 'total_sales', 'status'];
    if (!in_array($sortField, $allowedSortFields)) {
        $sortField = 'started_at';
    }
    
    $query->orderBy($sortField, $sortDirection);
    
    // تطبيق التصدير إذا طُلب
    if ($request->has('export')) {
        return $this->exportSessions($query);
    }
    
    // الحصول على النتائج مع التصفح
    $sessions = $query->paginate(15)->withQueryString();
    
    // حساب الإحصائيات
    $stats = $this->calculateSessionsStats($user->id, $request);
    
    // الحصول على البيانات المساعدة للفلاتر
    $devices = CashierDevice::active()->with('store')->get();
    $shifts = shifts_pos::orderBy('name')->get();
    
    // إذا لم تكن هناك جلسة نشطة وكانت هناك جلسات سابقة، اعرض الفهرسة
    if ($sessions->count() > 0) {
        return view('pos.sessions.index', compact(
            'sessions', 
            'devices', 
            'shifts', 
            'stats'
        ));
    }
    
    // إذا لم تكن هناك جلسات على الإطلاق، اعرض صفحة إنشاء جلسة
    return view('pos.sessions.create', compact('devices', 'shifts'));
}

/**
 * حساب إحصائيات الجلسات
 */
private function calculateSessionsStats($userId, $request)
{
    $baseQuery = PosSession::where('user_id', $userId);
    
    // تطبيق نفس الفلاتر على الإحصائيات
    if ($request->filled('device_id')) {
        $baseQuery->where('device_id', $request->device_id);
    }
    
    if ($request->filled('status')) {
        $baseQuery->where('status', $request->status);
    }
    
    if ($request->filled('date')) {
        $baseQuery->whereDate('started_at', $request->date);
    }
    
    if ($request->filled('search')) {
        $search = $request->search;
        $baseQuery->where(function($q) use ($search) {
            $q->where('session_number', 'LIKE', "%{$search}%")
              ->orWhereHas('user', function($userQuery) use ($search) {
                  $userQuery->where('name', 'LIKE', "%{$search}%");
              });
        });
    }
    
    return [
        'total_sessions' => $baseQuery->count(),
        'active_sessions' => (clone $baseQuery)->where('status', 'active')->count(),
        'closed_sessions' => (clone $baseQuery)->where('status', 'closed')->count(),
        'total_sales' => (clone $baseQuery)->sum('total_sales'),
        'total_transactions' => (clone $baseQuery)->sum('total_transactions'),
        'avg_sales' => (clone $baseQuery)->avg('total_sales'),
        'today_sessions' => (clone $baseQuery)->whereDate('started_at', today())->count(),
        'this_week_sessions' => (clone $baseQuery)->whereBetween('started_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count(),
        'this_month_sessions' => (clone $baseQuery)->whereMonth('started_at', now()->month)
                                                   ->whereYear('started_at', now()->year)
                                                   ->count()
    ];
}

/**
 * تصدير الجلسات
 */
private function exportSessions($query)
{
    $sessions = $query->get();
    
    $filename = 'sessions_' . now()->format('Y_m_d_H_i_s') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];
    
    $callback = function() use ($sessions) {
        $file = fopen('php://output', 'w');
        
        // إضافة BOM للـ UTF-8
        fwrite($file, "\xEF\xBB\xBF");
        
        // رؤوس الأعمدة
        fputcsv($file, [
            'رقم الجلسة',
            'الموظف',
            'الجهاز',
            'الوردية',
            'تاريخ البداية',
            'وقت البداية',
            'تاريخ النهاية',
            'وقت النهاية',
            'المدة (دقيقة)',
            'الحالة',
            'الرصيد الافتتاحي',
            'إجمالي المبيعات',
            'إجمالي النقدي',
            'إجمالي البطاقات',
            'عدد المعاملات',
            'الرصيد المتوقع',
            'الرصيد الفعلي',
            'الفرق',
            'ملاحظات الإغلاق'
        ]);
        
        // البيانات
        foreach ($sessions as $session) {
            $duration = $session->ended_at 
                ? $session->started_at->diffInMinutes($session->ended_at)
                : $session->started_at->diffInMinutes(now());
                
            fputcsv($file, [
                $session->session_number,
                $session->user->name,
                $session->device->device_name ?? '',
                $session->shift->name ?? '',
                $session->started_at->format('Y-m-d'),
                $session->started_at->format('H:i:s'),
                $session->ended_at ? $session->ended_at->format('Y-m-d') : '',
                $session->ended_at ? $session->ended_at->format('H:i:s') : '',
                $duration,
                $this->getStatusText($session->status),
                number_format($session->opening_balance, 2),
                number_format($session->total_sales, 2),
                number_format($session->total_cash, 2),
                number_format($session->total_card, 2),
                $session->total_transactions,
                number_format($session->closing_balance ?? 0, 2),
                number_format($session->actual_closing_balance ?? 0, 2),
                number_format($session->difference ?? 0, 2),
                $session->closing_notes ?? ''
            ]);
        }
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
}

/**
 * الحصول على نص الحالة
 */
private function getStatusText($status)
{
    return match($status) {
        'active' => 'نشطة',
        'closed' => 'مغلقة',
        'suspended' => 'معلقة',
        default => $status
    };
}

/**
 * تعليق الجلسة
 */
public function suspend(Request $request, $id)
{
    try {
        $session = PosSession::where('user_id', Auth::id())
            ->where('status', 'active')
            ->findOrFail($id);

        $session->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspend_reason' => $request->input('reason', 'تعليق يدوي')
        ]);

        // تسجيل العملية
        Log::info('تم تعليق الجلسة', [
            'session_id' => $session->id,
            'session_number' => $session->session_number,
            'user_id' => Auth::id(),
            'reason' => $request->input('reason')
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تعليق الجلسة بنجاح'
            ]);
        }

        return back()->with('success', 'تم تعليق الجلسة بنجاح');

    } catch (\Exception $e) {
        Log::error('خطأ في تعليق الجلسة', [
            'session_id' => $id,
            'user_id' => Auth::id(),
            'error' => $e->getMessage()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تعليق الجلسة'
            ], 500);
        }

        return back()->with('error', 'حدث خطأ أثناء تعليق الجلسة');
    }
}

/**
 * استكمال الجلسة المعلقة
 */
public function resume(Request $request, $id)
{
    try {
        $session = PosSession::where('user_id', Auth::id())
            ->where('status', 'suspended')
            ->findOrFail($id);

        // التحقق من عدم وجود جلسة نشطة أخرى
        $activeSession = PosSession::active()->forUser(Auth::id())->first();
        if ($activeSession) {
            return back()->with('error', 'لديك جلسة نشطة بالفعل. يجب إغلاقها أولاً');
        }

        $session->update([
            'status' => 'active',
            'resumed_at' => now(),
            'suspended_at' => null,
            'suspend_reason' => null
        ]);

        // تسجيل العملية
        Log::info('تم استكمال الجلسة', [
            'session_id' => $session->id,
            'session_number' => $session->session_number,
            'user_id' => Auth::id()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم استكمال الجلسة بنجاح',
                'redirect' => route('pos.sessions.show', $session->id)
            ]);
        }

        return redirect()->route('pos.sessions.show', $session->id)
            ->with('success', 'تم استكمال الجلسة بنجاح');

    } catch (\Exception $e) {
        Log::error('خطأ في استكمال الجلسة', [
            'session_id' => $id,
            'user_id' => Auth::id(),
            'error' => $e->getMessage()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استكمال الجلسة'
            ], 500);
        }

        return back()->with('error', 'حدث خطأ أثناء استكمال الجلسة');
    }
}

/**
 * طباعة تقرير الجلسة
 */
public function print($id)
{
    try {
        $session = PosSession::with(['user', 'device', 'shift', 'details'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('pos.sessions.print', compact('session'));

    } catch (\Exception $e) {
        Log::error('خطأ في طباعة الجلسة', [
            'session_id' => $id,
            'user_id' => Auth::id(),
            'error' => $e->getMessage()
        ]);

        return back()->with('error', 'حدث خطأ أثناء طباعة التقرير');
    }
}

/**
 * تفاصيل الجلسة (AJAX)
 */
public function details($id)
{
    try {
        $session = PosSession::with(['user', 'device', 'shift', 'details'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $sessionDetails = [
            'session_number' => $session->session_number,
            'status' => $session->status,
            'status_text' => $this->getStatusText($session->status),
            'user_name' => $session->user->name,
            'device_name' => $session->device->device_name ?? 'غير محدد',
            'shift_name' => $session->shift->name ?? 'غير محدد',
            'started_at' => $session->started_at->format('d/m/Y H:i:s'),
            'ended_at' => $session->ended_at?->format('d/m/Y H:i:s'),
            'duration' => $session->ended_at 
                ? $session->started_at->diff($session->ended_at)->format('%H:%I:%S')
                : $session->started_at->diffForHumans(),
            'opening_balance' => number_format($session->opening_balance, 2),
            'total_sales' => number_format($session->total_sales, 2),
            'total_transactions' => $session->total_transactions,
            'total_cash' => number_format($session->total_cash, 2),
            'total_card' => number_format($session->total_card, 2),
            'closing_balance' => number_format($session->closing_balance ?? 0, 2),
            'actual_closing_balance' => number_format($session->actual_closing_balance ?? 0, 2),
            'difference' => number_format($session->difference ?? 0, 2),
            'closing_notes' => $session->closing_notes,
            'transactions_count' => $session->details->count(),
            'last_transaction' => $session->details->sortByDesc('transaction_time')->first()?->transaction_time?->diffForHumans()
        ];

        return response()->json([
            'success' => true,
            'session' => $sessionDetails
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء جلب تفاصيل الجلسة'
        ], 500);
    }
}
    // عرض الجلسة النشطة أو صفحة إنشاء جلسة جديدة
    public function create()
    {
        $user = Auth::user();
        $activeSession = PosSession::active()->forUser($user->id)->first();
        
        if ($activeSession) {
            return redirect()->route('pos.sessions.show', $activeSession->id);
        }
        
        // إذا لم تكن هناك جلسة نشطة، اعرض صفحة إنشاء جلسة
        $devices = CashierDevice::active()->with('store')->get();
        $shifts = shifts_pos::orderBy('name')->get();
        
        return view('pos::sessions.create', compact('devices', 'shifts'));
    }

    // إنشاء جلسة جديدة
    public function store(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'device_id' => 'required|exists:cashier_devices,id',
        'shift_id' => 'required|exists:shifts_pos,id',
        'opening_balance' => 'required|numeric|min:0'
    ], [
        'device_id.required' => 'يجب اختيار جهاز',
        'shift_id.required' => 'يجب اختيار وردية',
        'opening_balance.required' => 'الرصيد الافتتاحي مطلوب'
    ]);

    // التحقق من وجود جلسة نشطة للمستخدم
    if (PosSession::active()->forUser($user->id)->exists()) {
        return back()->with('error', 'لديك جلسة نشطة بالفعل. يجب إغلاقها أولاً');
    }

    // التحقق من وجود جلسة نشطة للجهاز
    if (PosSession::active()->forDevice($validated['device_id'])->exists()) {
        return back()->with('error', 'الجهاز المحدد لديه جلسة نشطة بالفعل. يجب إغلاقها أولاً');
    }

    try {
        DB::beginTransaction();

        $session = PosSession::create([
            'session_number'   => PosSession::generateSessionNumber(),
            'user_id'          => $user->id,
            'device_id'        => $validated['device_id'],
            'shift_id'         => $validated['shift_id'],
            'opening_balance'  => $validated['opening_balance'],
            'started_at'       => now(),
            'status'           => 'active'
        ]);

        // إضافة سجل في التفاصيل للرصيد الافتتاحي
        if ($validated['opening_balance'] > 0) {
            PosSessionDetail::create([
                'session_id'       => $session->id,
                'transaction_type' => 'opening_balance',
                'amount'           => $validated['opening_balance'],
                'payment_method'   => 'cash',
                'cash_amount'      => $validated['opening_balance'],
                'card_amount'      => 0,
                'description'      => 'الرصيد الافتتاحي',
                'transaction_time' => now()
            ]);
        }

        DB::commit();

        return redirect()->route('POS.sales_start.index', $session->id)
            ->with('success', 'تم إنشاء الجلسة بنجاح');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()
            ->with('error', 'حدث خطأ أثناء إنشاء الجلسة');
    }
}

    // عرض تفاصيل الجلسة
    // public function show($id)
    // {
    //     $session = PosSession::with(['user', 'device', 'shift', 'details'])
    //         ->where('user_id', Auth::id())
    //         ->findOrFail($id);

    //     // حساب الإحصائيات المحدثة
    //     $this->updateSessionStatistics($session);

    //     return view('pos.sessions.show', compact('session'));
    // }

    // عرض نموذج إغلاق الجلسة
    public function closeForm($id)
    {
        $session = PosSession::where('user_id', Auth::id())
            ->where('status', 'active')
            ->findOrFail($id);

        // حساب الإحصائيات النهائية
        $this->updateSessionStatistics($session);
        
        $expectedCash = $session->opening_balance + $session->total_cash - $session->total_returns;

        return view('pos.sessions.close', compact('session', 'expectedCash'));
    }

    // // إغلاق الجلسة
    // public function close(Request $request, $id)
    // {
    //     $session = PosSession::where('user_id', Auth::id())
    //         ->where('status', 'active')
    //         ->findOrFail($id);

    //     $validated = $request->validate([
    //         'actual_closing_balance' => 'required|numeric|min:0',
    //         'closing_notes' => 'nullable|string|max:1000'
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         // حساب الإحصائيات النهائية
    //         $this->updateSessionStatistics($session);
            
    //         $expectedCash = $session->opening_balance + $session->total_cash - $session->total_returns;
    //         $difference = $validated['actual_closing_balance'] - $expectedCash;

    //         $session->update([
    //             'status' => 'closed',
    //             'ended_at' => now(),
    //             'closing_balance' => $expectedCash,
    //             'actual_closing_balance' => $validated['actual_closing_balance'],
    //             'difference' => $difference,
    //             'closing_notes' => $validated['closing_notes']
    //         ]);

    //         // إضافة سجل الإغلاق في التفاصيل
    //         PosSessionDetail::create([
    //             'session_id' => $session->id,
    //             'transaction_type' => 'closing_balance',
    //             'amount' => $validated['actual_closing_balance'],
    //             'payment_method' => 'cash',
    //             'cash_amount' => $validated['actual_closing_balance'],
    //             'card_amount' => 0,
    //             'description' => 'الرصيد الختامي الفعلي',
    //             'transaction_time' => now()
    //         ]);

    //         // إذا كان هناك فرق، أضف سجل للفرق
    //         if ($difference != 0) {
    //             PosSessionDetail::create([
    //                 'session_id' => $session->id,
    //                 'transaction_type' => 'cash_adjustment',
    //                 'amount' => abs($difference),
    //                 'payment_method' => 'cash',
    //                 'cash_amount' => $difference,
    //                 'card_amount' => 0,
    //                 'description' => $difference > 0 ? 'زيادة في الصندوق' : 'نقص في الصندوق',
    //                 'transaction_time' => now()
    //             ]);
    //         }

    //         DB::commit();

    //         return redirect()->route('pos.sessions.summary', $session->id)
    //             ->with('success', 'تم إغلاق الجلسة بنجاح');

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return back()->withInput()
    //             ->with('error', 'حدث خطأ أثناء إغلاق الجلسة');
    //     }
    // }

    // عرض ملخص الجلسة المغلقة
    public function summary($id)
    {
        $session = PosSession::with(['user', 'device', 'shift', 'details'])
            ->where('user_id', Auth::id())
            ->where('status', 'closed')
            ->findOrFail($id);

        return view('pos.sessions.summary', compact('session'));
    }

    // دالة مساعدة لتحديث إحصائيات الجلسة
    private function updateSessionStatistics(PosSession $session)
    {
        $stats = $session->details()
            ->selectRaw('
                COUNT(*) as transaction_count,
                SUM(CASE WHEN transaction_type = "sale" THEN amount ELSE 0 END) as total_sales,
                SUM(CASE WHEN transaction_type = "return" THEN amount ELSE 0 END) as total_returns,
                SUM(cash_amount) as total_cash,
                SUM(card_amount) as total_card
            ')
            ->first();

        $session->update([
            'total_transactions' => $stats->transaction_count ?? 0,
            'total_sales' => $stats->total_sales ?? 0,
            'total_returns' => $stats->total_returns ?? 0,
            'total_cash' => $stats->total_cash ?? 0,
            'total_card' => $stats->total_card ?? 0
        ]);

        return $session->fresh();
    }

    // إضافة معاملة للجلسة (يتم استدعاؤها من نظام نقطة البيع)
    public function addTransaction($sessionId, $data)
    {
        $session = PosSession::active()->findOrFail($sessionId);
        
        PosSessionDetail::create([
            'session_id' => $session->id,
            'transaction_type' => $data['type'], // sale, return, etc
            'reference_number' => $data['reference'] ?? null,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'cash_amount' => $data['cash_amount'] ?? 0,
            'card_amount' => $data['card_amount'] ?? 0,
            'description' => $data['description'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'transaction_time' => now()
        ]);

        // تحديث إحصائيات الجلسة
        $this->updateSessionStatistics($session);

        return $session;
    }
    
    public function getTransactions($id)
{
    try {
        $session = PosSession::with(['details' => function($query) {
            $query->orderBy('transaction_time', 'desc')->limit(10);
        }])
        ->where('user_id', Auth::id())
        ->findOrFail($id);

        $transactions = $session->details->map(function ($detail) {
            return [
                'time' => $detail->transaction_time->format('H:i:s'),
                'type' => $detail->transaction_type,
                'reference' => $detail->reference_number,
                'amount' => number_format($detail->amount, 2),
                'cash_amount' => number_format($detail->cash_amount, 2),
                'card_amount' => number_format($detail->card_amount, 2),
                'payment_method' => $detail->payment_method,
                'description' => $detail->description
            ];
        });

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'خطأ في جلب المعاملات'
        ], 500);
    }
}

/**
 * تحديث إحصائيات الجلسة (AJAX)
 */
public function refreshStats($id)
{
    try {
        $session = PosSession::where('user_id', Auth::id())->findOrFail($id);
        
        // تحديث الإحصائيات
        $this->updateSessionStatistics($session);
        
        // إعادة تحميل البيانات المحدثة
        $session = $session->fresh();

        return response()->json([
            'success' => true,
            'stats' => [
                'total_transactions' => $session->total_transactions,
                'total_sales' => number_format($session->total_sales, 2),
                'total_cash' => number_format($session->total_cash, 2),
                'total_card' => number_format($session->total_card, 2),
                'total_returns' => number_format($session->total_returns, 2),
                'expected_cash' => number_format($session->opening_balance + $session->total_cash - $session->total_returns, 2),
                'last_transaction' => $session->details()->orderBy('transaction_time', 'desc')->first()?->transaction_time?->diffForHumans(),
                'duration' => $session->duration
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'خطأ في تحديث الإحصائيات'
        ], 500);
    }
}

/**
 * تحديث دوال Controller الموجودة
 */

// تحديث دالة show لدعم طلبات AJAX
public function show($id)
{
    $session = PosSession::with(['user', 'device', 'shift', 'details'])
        ->where('user_id', Auth::id())
        ->findOrFail($id);

    // حساب الإحصائيات المحدثة
    $this->updateSessionStatistics($session);

    // إذا كان طلب AJAX، أرجع JSON فقط
    if (request()->ajax() || request()->has('ajax')) {
        return response()->json([
            'success' => true,
            'session' => $session,
            'stats' => [
                'total_transactions' => $session->total_transactions,
                'total_sales' => $session->total_sales,
                'total_cash' => $session->total_cash,
                'total_card' => $session->total_card
            ]
        ]);
    }

    return view('pos.sessions.show', compact('session'));
}

// تحديث دالة close لتحسين المعالجة
public function close(Request $request, $id)
{
    $session = PosSession::where('user_id', Auth::id())
        ->where('status', 'active')
        ->findOrFail($id);

    $validated = $request->validate([
        'actual_closing_balance' => 'required|numeric|min:0',
        'closing_notes' => 'nullable|string|max:1000'
    ], [
        'actual_closing_balance.required' => 'الرصيد الفعلي مطلوب',
        'actual_closing_balance.numeric' => 'الرصيد يجب أن يكون رقماً',
        'actual_closing_balance.min' => 'الرصيد لا يمكن أن يكون أقل من صفر',
        'closing_notes.max' => 'الملاحظات لا يمكن أن تتجاوز 1000 حرف'
    ]);

    try {
        DB::beginTransaction();

        // حساب الإحصائيات النهائية
        $this->updateSessionStatistics($session);
        
        $expectedCash = $session->opening_balance + $session->total_cash - $session->total_returns;
        $difference = $validated['actual_closing_balance'] - $expectedCash;

        $session->update([
            'status' => 'closed',
            'ended_at' => now(),
            'closing_balance' => $expectedCash,
            'actual_closing_balance' => $validated['actual_closing_balance'],
            'difference' => $difference,
            'closing_notes' => $validated['closing_notes']
        ]);

        // إضافة سجل الإغلاق في التفاصيل
        PosSessionDetail::create([
            'session_id' => $session->id,
            'transaction_type' => 'closing_balance',
            'amount' => $validated['actual_closing_balance'],
            'payment_method' => 'cash',
            'cash_amount' => $validated['actual_closing_balance'],
            'card_amount' => 0,
            'description' => 'الرصيد الختامي الفعلي',
            'transaction_time' => now()
        ]);

        // إذا كان هناك فرق، أضف سجل للفرق
        if (abs($difference) > 0.01) {
            PosSessionDetail::create([
                'session_id' => $session->id,
                'transaction_type' => 'cash_adjustment',
                'amount' => abs($difference),
                'payment_method' => 'cash',
                'cash_amount' => $difference,
                'card_amount' => 0,
                'description' => $difference > 0 ? 'زيادة في الصندوق' : 'نقص في الصندوق',
                'transaction_time' => now()
            ]);
        }

        // تسجيل إغلاق الجلسة في السجلات
        // Log::info('تم إغلاق جلسة نقطة البيع', [
        //     'session_id' => $session->id,
        //     'session_number' => $session->session_number,
        //     'user_id' => Auth::id(),
        //     'expected_cash' => $expectedCash,
        //     'actual_cash' => $validated['actual_closing_balance'],
        //     'difference' => $difference,
        //     'total_sales' => $session->total_sales,
        //     'total_transactions' => $session->total_transactions
        // ]);

        DB::commit();

        // إذا كان طلب AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إغلاق الجلسة بنجاح',
                'redirect' => route('pos.sessions.summary', $session->id)
            ]);
        }

        return redirect()->route('pos.sessions.summary', $session->id)
            ->with('success', 'تم إغلاق الجلسة بنجاح');

    } catch (\Exception $e) {
        DB::rollback();
        
        Log::error('خطأ في إغلاق الجلسة', [
            'session_id' => $session->id,
            'user_id' => Auth::id(),
            'error' => $e->getMessage(),
            'request_data' => $request->all()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إغلاق الجلسة: ' . $e->getMessage()
            ], 500);
        }

        return back()->withInput()
            ->with('error', 'حدث خطأ أثناء إغلاق الجلسة: ' . $e->getMessage());
    }
}

/**
 * دالة مساعدة لحساب ملخص الجلسة
 */
private function calculateSessionSummary($session)
{
    $totalSales = $session->details()->where('transaction_type', 'sale')->sum('amount');
    $totalReturns = $session->details()->where('transaction_type', 'return')->sum('amount');
    $totalCash = $session->details()->sum('cash_amount');
    $totalCard = $session->details()->sum('card_amount');
    $transactionCount = $session->details()->whereIn('transaction_type', ['sale', 'return'])->count();

    return [
        'total_sales' => $totalSales,
        'total_returns' => $totalReturns,
        'total_cash' => $totalCash,
        'total_card' => $totalCard,
        'transaction_count' => $transactionCount,
        'average_transaction' => $transactionCount > 0 ? $totalSales / $transactionCount : 0,
        'expected_cash' => $session->opening_balance + $totalCash - $totalReturns
    ];
}

/**
 * التحقق من صحة بيانات الإغلاق
 */
private function validateClosingData($session, $actualBalance)
{
    $expectedCash = $session->opening_balance + $session->total_cash - $session->total_returns;
    $difference = $actualBalance - $expectedCash;
    $warnings = [];

    // تحذير إذا كان الفرق كبيراً
    if (abs($difference) > 100) {
        $warnings[] = [
            'type' => 'large_difference',
            'message' => 'الفرق في الصندوق كبير جداً: ' . number_format(abs($difference), 2) . ' ر.س',
            'severity' => 'high'
        ];
    }

    // تحذير إذا لم تكن هناك معاملات
    if ($session->total_transactions == 0) {
        $warnings[] = [
            'type' => 'no_transactions',
            'message' => 'لا توجد معاملات في هذه الجلسة',
            'severity' => 'medium'
        ];
    }

    // تحذير إذا كانت مدة الجلسة قصيرة جداً
    if ($session->started_at->diffInMinutes(now()) < 30) {
        $warnings[] = [
            'type' => 'short_duration',
            'message' => 'مدة الجلسة قصيرة جداً (أقل من 30 دقيقة)',
            'severity' => 'low'
        ];
    }

    return [
        'is_valid' => count($warnings) == 0,
        'warnings' => $warnings,
        'difference' => $difference,
        'expected_cash' => $expectedCash
    ];
}
}
