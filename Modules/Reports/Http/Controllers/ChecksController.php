<?php

namespace Modules\Reports\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ChequeBook;
use App\Models\PayableCheque;
use App\Models\ReceivedCheque;
use App\Models\Treasury;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChecksController extends Controller
{
    public function index()
    {
        return view('reports::checks.index');
    }
    public function deliveredChecks()
    {
        // جلب البيانات المطلوبة للفلاتر
        $banks = Treasury::where('type', 'bank')->orderBy('name')->get();
        $cheque_books = ChequeBook::with('bank')->orderBy('id')->get();
        $recipient_accounts = Account::orderBy('name')->get();

        return view('reports::checks.delivered-checks', compact(
            'banks',
            'cheque_books',
            'recipient_accounts'
        ));
    }

    /**
     * جلب بيانات تقرير الشيكات المدفوعة عبر AJAX
     */
    public function deliveredChequesReportAjax(Request $request)
    {
        try {
            // بناء الاستعلام الأساسي
            $query = PayableCheque::with(['bank', 'cheque_book', 'recipient_account']);

            // تطبيق الفلاتر
            $this->applyFilters($query, $request);

            // جلب البيانات
            $cheques = $query->orderBy('issue_date', 'desc')->get();

            // حساب الإجماليات
            $totals = $this->calculateTotals($cheques);

            // إعداد بيانات الرسوم البيانية
            $chartData = $this->prepareChartData($cheques);

            // تحديد نطاق التاريخ للعرض
            $dateRange = $this->getDateRangeReceived($request);

            return response()->json([
                'success' => true,
                'cheques' => $this->formatChequesForDisplay($cheques),
                'totals' => $totals,
                'chart_data' => $chartData,
                'from_date' => $dateRange['from'],
                'to_date' => $dateRange['to'],
                'filters_applied' => $this->getAppliedFilters($request)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تطبيق الفلاتر على الاستعلام
     */
    private function applyFilters($query, Request $request)
    {
        // فلتر الحساب المستلم
        if ($request->filled('recipient_account')) {
            $query->where('recipient_account_id', $request->recipient_account);
        }

        // فلتر البنك
        if ($request->filled('bank_id')) {
            $query->where('bank_id', $request->bank_id);
        }

        // فلتر دفتر الشيكات
        if ($request->filled('cheque_book_id')) {
            $query->where('cheque_book_id', $request->cheque_book_id);
        }

        // فلتر الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلتر رقم الشيك
        if ($request->filled('cheque_number')) {
            $query->where('cheque_number', 'like', '%' . $request->cheque_number . '%');
        }

        // فلاتر التاريخ
        $this->applyDateFilters($query, $request);
    }

    /**
     * تطبيق فلاتر التاريخ
     */
    private function applyDateFilters($query, Request $request)
    {
        $dateType = $request->get('date_type', 'custom');

        if ($dateType !== 'custom') {
            $dates = $this->getPresetDates($dateType);
            $fromDate = $dates['from'];
            $toDate = $dates['to'];
        } else {
            $fromDate = $request->issue_date_from;
            $toDate = $request->issue_date_to;
        }

        // فلتر تاريخ الإصدار
        if ($fromDate) {
            $query->whereDate('issue_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('issue_date', '<=', $toDate);
        }

        // فلتر تاريخ الاستحقاق
        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }
        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }
    }

    /**
     * الحصول على التواريخ المحددة مسبقاً
     */
    private function getPresetDates($dateType)
    {
        $today = Carbon::today();

        switch ($dateType) {
            case 'today':
                return ['from' => $today, 'to' => $today];

            case 'yesterday':
                $yesterday = $today->copy()->subDay();
                return ['from' => $yesterday, 'to' => $yesterday];

            case 'this_week':
                return [
                    'from' => $today->copy()->startOfWeek(),
                    'to' => $today
                ];

            case 'last_week':
                return [
                    'from' => $today->copy()->subWeek()->startOfWeek(),
                    'to' => $today->copy()->subWeek()->endOfWeek()
                ];

            case 'this_month':
                return [
                    'from' => $today->copy()->startOfMonth(),
                    'to' => $today
                ];

            case 'last_month':
                return [
                    'from' => $today->copy()->subMonth()->startOfMonth(),
                    'to' => $today->copy()->subMonth()->endOfMonth()
                ];

            case 'this_year':
                return [
                    'from' => $today->copy()->startOfYear(),
                    'to' => $today
                ];

            case 'last_year':
                return [
                    'from' => $today->copy()->subYear()->startOfYear(),
                    'to' => $today->copy()->subYear()->endOfYear()
                ];

            default:
                return ['from' => null, 'to' => null];
        }
    }

    /**
     * حساب الإجماليات
     */
    private function calculateTotals($cheques)
    {
        $totalAmount = $cheques->sum('amount');
        $totalCount = $cheques->count();
        $paidCount = $cheques->where('status', 'paid')->count();
        $pendingCount = $cheques->where('status', 'pending')->count();
        $cancelledCount = $cheques->where('status', 'cancelled')->count();
        $returnedCount = $cheques->where('status', 'returned')->count();

        return [
            'total_amount' => $totalAmount,
            'total_count' => $totalCount,
            'paid_count' => $paidCount,
            'pending_count' => $pendingCount,
            'cancelled_count' => $cancelledCount,
            'returned_count' => $returnedCount,
            'paid_amount' => $cheques->where('status', 'paid')->sum('amount'),
            'pending_amount' => $cheques->where('status', 'pending')->sum('amount')
        ];
    }

    /**
     * إعداد بيانات الرسوم البيانية
     */
    private function prepareChartData($cheques)
    {
        // بيانات الرسم البياني للحالات
        $statusData = $cheques->groupBy('status')->map(function ($group) {
            return $group->count();
        });

        $statusLabels = [];
        $statusValues = [];
        foreach ($statusData as $status => $count) {
            $statusLabels[] = $this->getStatusLabel($status);
            $statusValues[] = $count;
        }

        // بيانات الرسم البياني للمبالغ حسب البنك
        $bankData = $cheques->groupBy('bank.name')->map(function ($group) {
            return $group->sum('amount');
        });

        $bankLabels = [];
        $bankValues = [];
        foreach ($bankData as $bankName => $amount) {
            $bankLabels[] = $bankName ?: 'غير محدد';
            $bankValues[] = $amount;
        }

        return [
            'status' => [
                'labels' => $statusLabels,
                'values' => $statusValues
            ],
            'amounts' => [
                'labels' => $bankLabels,
                'values' => $bankValues
            ]
        ];
    }

    /**
     * تحويل رمز الحالة إلى نص عربي
     */
    private function getStatusLabel($status)
    {
        $statusMap = [
            'paid' => 'مدفوع',
            'pending' => 'معلق',
            'cancelled' => 'ملغي',
            'returned' => 'مرتجع'
        ];

        return $statusMap[$status] ?? 'غير محدد';
    }

    /**
     * تنسيق بيانات الشيكات للعرض
     */
    private function formatChequesForDisplay($cheques)
    {
        return $cheques->map(function ($cheque) {
            return [
                'id' => $cheque->id,
                'cheque_number' => $cheque->cheque_number,
                'amount' => $cheque->amount,
                'payee_name' => $cheque->payee_name,
                'issue_date' => $cheque->issue_date,
                'due_date' => $cheque->due_date,
                'status' => $cheque->status,
                'description' => $cheque->description,
                'bank' => $cheque->bank ? [
                    'id' => $cheque->bank->id,
                    'name' => $cheque->bank->name
                ] : null,
                'cheque_book' => $cheque->cheque_book ? [
                    'id' => $cheque->cheque_book->id,
                    'book_number' => $cheque->cheque_book->book_number
                ] : null,
                'recipient_account' => $cheque->recipient_account ? [
                    'id' => $cheque->recipient_account->id,
                    'name' => $cheque->recipient_account->name
                ] : null
            ];
        });
    }

    /**
     * تحديد نطاق التاريخ للعرض
     */
    private function getDateRange(Request $request)
    {
        $dateType = $request->get('date_type', 'custom');

        if ($dateType !== 'custom') {
            $dates = $this->getPresetDates($dateType);
            return [
                'from' => $dates['from'] ? $dates['from']->format('Y-m-d') : null,
                'to' => $dates['to'] ? $dates['to']->format('Y-m-d') : null
            ];
        }

        return [
            'from' => $request->issue_date_from ?: 'غير محدد',
            'to' => $request->issue_date_to ?: 'غير محدد'
        ];
    }

    /**
     * الحصول على الفلاتر المطبقة
     */
    private function getAppliedFilters(Request $request)
    {
        $filters = [];

        if ($request->filled('recipient_account')) {
            $account = Account::find($request->recipient_account);
            $filters['recipient_account'] = $account ? $account->name : 'غير موجود';
        }

        if ($request->filled('bank_id')) {
            $bank = Treasury::find($request->bank_id);
            $filters['bank'] = $bank ? $bank->name : 'غير موجود';
        }

        if ($request->filled('cheque_book_id')) {
            $book = ChequeBook::find($request->cheque_book_id);
            $filters['cheque_book'] = $book ? $book->book_number : 'غير موجود';
        }

        if ($request->filled('status')) {
            $filters['status'] = $this->getStatusLabel($request->status);
        }

        if ($request->filled('cheque_number')) {
            $filters['cheque_number'] = $request->cheque_number;
        }

        return $filters;
    }

    /**
     * تصدير التقرير إلى Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            // جلب البيانات
            $query = PayableCheque::with(['bank', 'cheque_book', 'recipient_account']);
            $this->applyFilters($query, $request);
            $cheques = $query->orderBy('issue_date', 'desc')->get();

            // إعداد البيانات للتصدير
            $exportData = $cheques->map(function ($cheque, $index) {
                return [
                    '#' => $index + 1,
                    'رقم الشيك' => $cheque->cheque_number,
                    'المبلغ (ريال)' => number_format($cheque->amount, 2),
                    'المستفيد' => $cheque->payee_name ?: 'غير محدد',
                    'البنك' => $cheque->bank ? $cheque->bank->name : 'غير محدد',
                    'دفتر الشيكات' => $cheque->cheque_book ? $cheque->cheque_book->book_number : 'غير محدد',
                    'تاريخ الإصدار' => $cheque->issue_date ? Carbon::parse($cheque->issue_date)->format('Y-m-d') : '--',
                    'تاريخ الاستحقاق' => $cheque->due_date ? Carbon::parse($cheque->due_date)->format('Y-m-d') : '--',
                    'الحالة' => $this->getStatusLabel($cheque->status),
                    'الوصف' => $cheque->description ?: '--'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $exportData->toArray(),
                'filename' => 'تقرير_الشيكات_المدفوعة_' . date('Y-m-d') . '.xlsx'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تصدير البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تجميع البيانات حسب المعايير المحددة
     */
    public function getGroupedData(Request $request)
    {
        try {
            $groupBy = $request->get('group_by');

            if (!$groupBy) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تحديد معيار التجميع'
                ]);
            }

            $query = PayableCheque::with(['bank', 'cheque_book', 'recipient_account']);
            $this->applyFilters($query, $request);

            $groupedData = [];

            switch ($groupBy) {
                case 'bank':
                    $groupedData = $this->groupByBank($query);
                    break;
                case 'cheque_book':
                    $groupedData = $this->groupByChequeBook($query);
                    break;
                case 'recipient':
                    $groupedData = $this->groupByRecipient($query);
                    break;
                case 'status':
                    $groupedData = $this->groupByStatus($query);
                    break;
                case 'month':
                    $groupedData = $this->groupByMonth($query);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'معيار التجميع غير مدعوم'
                    ]);
            }

            return response()->json([
                'success' => true,
                'grouped_data' => $groupedData,
                'group_by' => $groupBy
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تجميع البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تجميع حسب البنك
     */
    private function groupByBank($query)
    {
        return $query->get()->groupBy('bank.name')->map(function ($cheques, $bankName) {
            return [
                'group_name' => $bankName ?: 'غير محدد',
                'count' => $cheques->count(),
                'total_amount' => $cheques->sum('amount'),
                'paid_count' => $cheques->where('status', 'paid')->count(),
                'pending_count' => $cheques->where('status', 'pending')->count(),
                'cheques' => $cheques->values()
            ];
        })->values();
    }

    /**
     * تجميع حسب دفتر الشيكات
     */
    private function groupByChequeBook($query)
    {
        return $query->get()->groupBy('cheque_book.book_number')->map(function ($cheques, $bookNumber) {
            return [
                'group_name' => $bookNumber ?: 'غير محدد',
                'count' => $cheques->count(),
                'total_amount' => $cheques->sum('amount'),
                'paid_count' => $cheques->where('status', 'paid')->count(),
                'pending_count' => $cheques->where('status', 'pending')->count(),
                'cheques' => $cheques->values()
            ];
        })->values();
    }

    /**
     * تجميع حسب المستفيد
     */
    private function groupByRecipient($query)
    {
        return $query->get()->groupBy('payee_name')->map(function ($cheques, $payeeName) {
            return [
                'group_name' => $payeeName ?: 'غير محدد',
                'count' => $cheques->count(),
                'total_amount' => $cheques->sum('amount'),
                'paid_count' => $cheques->where('status', 'paid')->count(),
                'pending_count' => $cheques->where('status', 'pending')->count(),
                'cheques' => $cheques->values()
            ];
        })->values();
    }

    /**
     * تجميع حسب الحالة
     */
    private function groupByStatus($query)
    {
        return $query->get()->groupBy('status')->map(function ($cheques, $status) {
            return [
                'group_name' => $this->getStatusLabel($status),
                'count' => $cheques->count(),
                'total_amount' => $cheques->sum('amount'),
                'paid_count' => $cheques->where('status', 'paid')->count(),
                'pending_count' => $cheques->where('status', 'pending')->count(),
                'cheques' => $cheques->values()
            ];
        })->values();
    }

    /**
     * تجميع حسب الشهر
     */
    private function groupByMonth($query)
    {
        return $query->get()->groupBy(function ($cheque) {
            return Carbon::parse($cheque->issue_date)->format('Y-m');
        })->map(function ($cheques, $month) {
            return [
                'group_name' => Carbon::createFromFormat('Y-m', $month)->format('Y/m'),
                'count' => $cheques->count(),
                'total_amount' => $cheques->sum('amount'),
                'paid_count' => $cheques->where('status', 'paid')->count(),
                'pending_count' => $cheques->where('status', 'pending')->count(),
                'cheques' => $cheques->values()
            ];
        })->values();
    }

    /**
     * إحصائيات مفصلة للشيكات
     */
    public function getDetailedStats(Request $request)
    {
        try {
            $query = PayableCheque::with(['bank', 'cheque_book']);
            $this->applyFilters($query, $request);
            $cheques = $query->get();

            // إحصائيات التواريخ
            $today = Carbon::today();
            $overdueCheques = $cheques->filter(function ($cheque) use ($today) {
                return $cheque->due_date && Carbon::parse($cheque->due_date)->lt($today);
            });

            $dueTodayCheques = $cheques->filter(function ($cheque) use ($today) {
                return $cheque->due_date && Carbon::parse($cheque->due_date)->eq($today);
            });

            $dueThisWeekCheques = $cheques->filter(function ($cheque) use ($today) {
                return $cheque->due_date &&
                       Carbon::parse($cheque->due_date)->between($today, $today->copy()->addWeek());
            });

            // إحصائيات المبالغ
            $largestCheque = $cheques->max('amount');
            $smallestCheque = $cheques->min('amount');
            $averageAmount = $cheques->avg('amount');

            // إحصائيات البنوك
            $bankStats = $cheques->groupBy('bank.name')->map(function ($group, $bankName) {
                return [
                    'bank_name' => $bankName ?: 'غير محدد',
                    'count' => $group->count(),
                    'total_amount' => $group->sum('amount'),
                    'percentage' => 0 // سيتم حسابها لاحقاً
                ];
            });

            $totalCount = $cheques->count();
            $bankStats = $bankStats->map(function ($stat) use ($totalCount) {
                $stat['percentage'] = $totalCount > 0 ? round(($stat['count'] / $totalCount) * 100, 2) : 0;
                return $stat;
            });

            return response()->json([
                'success' => true,
                'stats' => [
                    'overdue' => [
                        'count' => $overdueCheques->count(),
                        'amount' => $overdueCheques->sum('amount')
                    ],
                    'due_today' => [
                        'count' => $dueTodayCheques->count(),
                        'amount' => $dueTodayCheques->sum('amount')
                    ],
                    'due_this_week' => [
                        'count' => $dueThisWeekCheques->count(),
                        'amount' => $dueThisWeekCheques->sum('amount')
                    ],
                    'amount_stats' => [
                        'largest' => $largestCheque ?: 0,
                        'smallest' => $smallestCheque ?: 0,
                        'average' => round($averageAmount ?: 0, 2)
                    ],
                    'bank_stats' => $bankStats->values()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الإحصائيات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض تقرير الشيكات المستلمة
     */
    public function received()
    {
        // جلب البيانات المطلوبة للفلاتر
        $recipient_accounts = Account::orderBy('name')->get();
        $collection_accounts = Account::orderBy('name')->get();

        return view('reports::checks.received-checks', compact(
            'recipient_accounts',
            'collection_accounts'
        ));
    }

    /**
     * جلب بيانات تقرير الشيكات المستلمة عبر AJAX
     */
    public function receivedChequesReportAjax(Request $request)
    {
        try {
            // بناء الاستعلام الأساسي
            $query = ReceivedCheque::with(['recipient_account', 'collection_account']);

            // تطبيق الفلاتر
            $this->applyFiltersReceived($query, $request);

            // جلب البيانات
            $cheques = $query->orderBy('issue_date', 'desc')->get();

            // حساب الإجماليات
            $totals = $this->calculateTotalsReceived($cheques);

            // إعداد بيانات الرسوم البيانية
            $chartData = $this->prepareChartDataReceived($cheques);

            // تحديد نطاق التاريخ للعرض
            $dateRange = $this->getDateRangeReceived($request);

            return response()->json([
                'success' => true,
                'cheques' => $this->formatChequesForDisplayR($cheques),
                'totals' => $totals,
                'chart_data' => $chartData,
                'from_date' => $dateRange['from'],
                'to_date' => $dateRange['to'],
                'filters_applied' => $this->getAppliedFilters($request)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تطبيق الفلاتر على الاستعلام
     */
    private function applyFiltersReceived($query, Request $request)
    {
        // فلتر الحساب المستلم
        if ($request->filled('recipient_account')) {
            $query->where('recipient_account_id', $request->recipient_account);
        }

        // فلتر حساب التحصيل
        if ($request->filled('collection_account')) {
            $query->where('collection_account_id', $request->collection_account);
        }

        // فلتر رقم الشيك
        if ($request->filled('cheque_number')) {
            $query->where('cheque_number', 'like', '%' . $request->cheque_number . '%');
        }

        // فلتر اسم المدفوع له
        if ($request->filled('payee_name')) {
            $query->where('payee_name', 'like', '%' . $request->payee_name . '%');
        }

        // فلتر الاسم
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // فلتر نوع التظهير
        if ($request->filled('endorsement')) {
            $query->where('endorsement', $request->endorsement);
        }

        // فلاتر التاريخ
        $this->applyDateFiltersReceived($query, $request);
    }

    /**
     * تطبيق فلاتر التاريخ
     */
    private function applyDateFiltersReceived($query, Request $request)
    {
        $dateType = $request->get('date_type', 'custom');

        if ($dateType !== 'custom') {
            $dates = $this->getPresetDatesReceived($dateType);
            $fromDate = $dates['from'];
            $toDate = $dates['to'];
        } else {
            $fromDate = $request->issue_date_from;
            $toDate = $request->issue_date_to;
        }

        // فلتر تاريخ الإصدار
        if ($fromDate) {
            $query->whereDate('issue_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('issue_date', '<=', $toDate);
        }

        // فلتر تاريخ الاستحقاق
        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }
        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }
    }

    /**
     * الحصول على التواريخ المحددة مسبقاً
     */
    private function getPresetDatesReceived($dateType)
    {
        $today = Carbon::today();

        switch ($dateType) {
            case 'today':
                return ['from' => $today, 'to' => $today];

            case 'yesterday':
                $yesterday = $today->copy()->subDay();
                return ['from' => $yesterday, 'to' => $yesterday];

            case 'this_week':
                return [
                    'from' => $today->copy()->startOfWeek(),
                    'to' => $today
                ];

            case 'last_week':
                return [
                    'from' => $today->copy()->subWeek()->startOfWeek(),
                    'to' => $today->copy()->subWeek()->endOfWeek()
                ];

            case 'this_month':
                return [
                    'from' => $today->copy()->startOfMonth(),
                    'to' => $today
                ];

            case 'last_month':
                return [
                    'from' => $today->copy()->subMonth()->startOfMonth(),
                    'to' => $today->copy()->subMonth()->endOfMonth()
                ];

            case 'this_year':
                return [
                    'from' => $today->copy()->startOfYear(),
                    'to' => $today
                ];

            case 'last_year':
                return [
                    'from' => $today->copy()->subYear()->startOfYear(),
                    'to' => $today->copy()->subYear()->endOfYear()
                ];

            default:
                return ['from' => null, 'to' => null];
        }
    }

    /**
     * حساب الإجماليات
     */
    private function calculateTotalsReceived($cheques)
    {
        $totalAmount = $cheques->sum('amount');
        $totalCount = $cheques->count();

        // تصنيف الشيكات حسب تاريخ الاستحقاق
        $today = Carbon::today();
        $dueToday = $cheques->filter(function ($cheque) use ($today) {
            return $cheque->due_date && Carbon::parse($cheque->due_date)->eq($today);
        })->count();

        $overdue = $cheques->filter(function ($cheque) use ($today) {
            return $cheque->due_date && Carbon::parse($cheque->due_date)->lt($today);
        })->count();

        $upcoming = $cheques->filter(function ($cheque) use ($today) {
            return $cheque->due_date && Carbon::parse($cheque->due_date)->gt($today);
        })->count();

        return [
            'total_amount' => $totalAmount,
            'total_count' => $totalCount,
            'due_today_count' => $dueToday,
            'overdue_count' => $overdue,
            'upcoming_count' => $upcoming,
            'average_amount' => $totalCount > 0 ? $totalAmount / $totalCount : 0
        ];
    }

    /**
     * إعداد بيانات الرسوم البيانية
     */
    private function prepareChartDataReceived($cheques)
    {
        // بيانات الرسم البياني لحالات الاستحقاق
        $today = Carbon::today();
        $dueStatusData = [
            'overdue' => $cheques->filter(function ($cheque) use ($today) {
                return $cheque->due_date && Carbon::parse($cheque->due_date)->lt($today);
            })->count(),
            'due_today' => $cheques->filter(function ($cheque) use ($today) {
                return $cheque->due_date && Carbon::parse($cheque->due_date)->eq($today);
            })->count(),
            'upcoming' => $cheques->filter(function ($cheque) use ($today) {
                return $cheque->due_date && Carbon::parse($cheque->due_date)->gt($today);
            })->count()
        ];

        $statusLabels = ['متأخر', 'يستحق اليوم', 'قادم'];
        $statusValues = [$dueStatusData['overdue'], $dueStatusData['due_today'], $dueStatusData['upcoming']];

        // بيانات الرسم البياني للمبالغ حسب الحساب المستلم
        $accountData = $cheques->groupBy('recipient_account.name')->map(function ($group) {
            return $group->sum('amount');
        });

        $accountLabels = [];
        $accountValues = [];
        foreach ($accountData as $accountName => $amount) {
            $accountLabels[] = $accountName ?: 'غير محدد';
            $accountValues[] = $amount;
        }

        return [
            'status' => [
                'labels' => $statusLabels,
                'values' => $statusValues
            ],
            'amounts' => [
                'labels' => $accountLabels,
                'values' => $accountValues
            ]
        ];
    }

    /**
     * تنسيق بيانات الشيكات للعرض
     */
    private function formatChequesForDisplayR($cheques)
    {
        return $cheques->map(function ($cheque) {
            return [
                'id' => $cheque->id,
                'cheque_number' => $cheque->cheque_number,
                'amount' => $cheque->amount,
                'payee_name' => $cheque->payee_name,
                'name' => $cheque->name,
                'issue_date' => $cheque->issue_date,
                'due_date' => $cheque->due_date,
                'endorsement' => $cheque->endorsement,
                'description' => $cheque->description,
                'attachment' => $cheque->attachment,
                'recipient_account' => $cheque->recipient_account ? [
                    'id' => $cheque->recipient_account->id,
                    'name' => $cheque->recipient_account->name
                ] : null,
                'collection_account' => $cheque->collection_account ? [
                    'id' => $cheque->collection_account->id,
                    'name' => $cheque->collection_account->name
                ] : null
            ];
        });
    }

    /**
     * تحديد نطاق التاريخ للعرض
     */
    private function getDateRangeReceived(Request $request)
    {
        $dateType = $request->get('date_type', 'custom');

        if ($dateType !== 'custom') {
            $dates = $this->getPresetDatesReceived($dateType);
            return [
                'from' => $dates['from'] ? $dates['from']->format('Y-m-d') : null,
                'to' => $dates['to'] ? $dates['to']->format('Y-m-d') : null
            ];
        }

        return [
            'from' => $request->issue_date_from ?: 'غير محدد',
            'to' => $request->issue_date_to ?: 'غير محدد'
        ];
    }

    /**
     * الحصول على الفلاتر المطبقة
     */
    private function getAppliedFiltersReceived(Request $request)
    {
        $filters = [];

        if ($request->filled('recipient_account')) {
            $account = Account::find($request->recipient_account);
            $filters['recipient_account'] = $account ? $account->name : 'غير موجود';
        }

        if ($request->filled('collection_account')) {
            $account = Account::find($request->collection_account);
            $filters['collection_account'] = $account ? $account->name : 'غير موجود';
        }

        if ($request->filled('endorsement')) {
            $filters['endorsement'] = $this->getEndorsementLabel($request->endorsement);
        }

        if ($request->filled('cheque_number')) {
            $filters['cheque_number'] = $request->cheque_number;
        }

        if ($request->filled('payee_name')) {
            $filters['payee_name'] = $request->payee_name;
        }

        if ($request->filled('name')) {
            $filters['name'] = $request->name;
        }

        return $filters;
    }

    /**
     * تحويل رمز التظهير إلى نص عربي
     */
    private function getEndorsementLabel($endorsement)
    {
        $endorsementMap = [
            'cash' => 'نقدي',
            'bank_deposit' => 'إيداع بنكي',
            'transfer' => 'تحويل',
            'other' => 'أخرى'
        ];

        return $endorsementMap[$endorsement] ?? 'غير محدد';
    }

    /**
     * تصدير التقرير إلى Excel
     */


    /**
     * تجميع البيانات حسب المعايير المحددة
     */
    public function getGroupedDataReceived(Request $request)
    {
        try {
            $groupBy = $request->get('group_by');

            if (!$groupBy) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تحديد معيار التجميع'
                ]);
            }

            $query = ReceivedCheque::with(['recipient_account', 'collection_account']);
            $this->applyFiltersReceived($query, $request);

            $groupedData = [];

            switch ($groupBy) {
                case 'recipient_account':
                    $groupedData = $this->groupByRecipientAccount($query);
                    break;
                case 'collection_account':
                    $groupedData = $this->groupByCollectionAccount($query);
                    break;
                case 'endorsement':
                    $groupedData = $this->groupByEndorsement($query);
                    break;
                case 'month':
                    $groupedData = $this->groupByMonthReceived($query);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'معيار التجميع غير مدعوم'
                    ]);
            }

            return response()->json([
                'success' => true,
                'grouped_data' => $groupedData,
                'group_by' => $groupBy
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تجميع البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تجميع حسب الحساب المستلم
     */
    private function groupByRecipientAccount($query)
    {
        return $query->get()->groupBy('recipient_account.name')->map(function ($cheques, $accountName) {
            return [
                'group_name' => $accountName ?: 'غير محدد',
                'count' => $cheques->count(),
                'total_amount' => $cheques->sum('amount'),
                'cheques' => $cheques->values()
            ];
        })->values();
    }

    /**
     * تجميع حسب حساب التحصيل
     */
    private function groupByCollectionAccount($query)
    {
        return $query->get()->groupBy('collection_account.name')->map(function ($cheques, $accountName) {
            return [
                'group_name' => $accountName ?: 'غير محدد',
                'count' => $cheques->count(),
                'total_amount' => $cheques->sum('amount'),
                'cheques' => $cheques->values()
            ];
        })->values();
    }

    /**
     * تجميع حسب نوع التظهير
     */
    private function groupByEndorsement($query)
    {
        return $query->get()->groupBy('endorsement')->map(function ($cheques, $endorsement) {
            return [
                'group_name' => $this->getEndorsementLabel($endorsement),
                'count' => $cheques->count(),
                'total_amount' => $cheques->sum('amount'),
                'cheques' => $cheques->values()
            ];
        })->values();
    }

    /**
     * تجميع حسب الشهر
     */
    private function groupByMonthReceived($query)
    {
        return $query->get()->groupBy(function ($cheque) {
            return Carbon::parse($cheque->issue_date)->format('Y-m');
        })->map(function ($cheques, $month) {
            return [
                'group_name' => Carbon::createFromFormat('Y-m', $month)->format('Y/m'),
                'count' => $cheques->count(),
                'total_amount' => $cheques->sum('amount'),
                'cheques' => $cheques->values()
            ];
        })->values();
    }

    /**
     * إحصائيات مفصلة للشيكات المستلمة
     */


}
