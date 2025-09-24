<?php

namespace  Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Account;
use App\Models\Branch;
use App\Models\User;
use App\Models\CostCenter;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;

class TrialBalanceController extends Controller
{

    public function trialBalanceAjax(Request $request)
    {
        try {
            // 1. معالجة نطاق التاريخ
            $dateType = $request->input('date_type', 'year_to_date');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            // تحديد التواريخ بناءً على النوع
            $dates = $this->calculateDateRange($dateType, $fromDate, $toDate);
            $startDate = $dates['start'];
            $endDate = $dates['end'];

            // 2. بناء استعلام الحسابات مع الفلترة
            $accountsQuery = Account::with(['parent', 'children']);

            // فلترة نوع الحساب
            $accountType = $request->input('account_type');
            if ($accountType == 'رئيسي') {
                $accountsQuery->whereNull('parent_id');
            } elseif ($accountType == 'فرعي') {
                $accountsQuery->whereNotNull('parent_id');
            }

            // فلترة الفرع
            $branchId = $request->input('branch');
            if ($branchId) {
                $accountsQuery->where('branch_id', $branchId);
            }

            // فلترة المستوى
            $level = $request->input('level');
            if ($level !== null && $level !== '') {
                $accountsQuery->where('level', $level);
            }

            // فلترة مركز التكلفة
            $costCenterId = $request->input('cost_center');

            // فلترة المستخدم المضيف
            $addedBy = $request->input('added_by');
            if ($addedBy) {
                $accountsQuery->where('created_by', $addedBy);
            }

            // 3. جلب الحسابات
            $accounts = $accountsQuery->get();

            // 4. حساب الأرصدة للحسابات
            $accountBalances = [];
            $accountDisplay = $request->input('account_display');
            $journalBranch = $request->input('journal_branch');

            foreach ($accounts as $account) {
                // بناء استعلام الرصيد الافتتاحي
                $openingBalanceQuery = JournalEntryDetail::join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
                    ->where('account_id', $account->id)
                    ->where('journal_entries.date', '<', $startDate)
                    ->where('journal_entries.status', 1);

                // فلترة فرع القيود
                if ($journalBranch) {
                    $openingBalanceQuery->where('journal_entries.branch_id', $journalBranch);
                }

                // فلترة مركز التكلفة
                if ($costCenterId) {
                    $openingBalanceQuery->where('journal_entry_details.cost_center_id', $costCenterId);
                }

                $openingDebit = $openingBalanceQuery->sum('debit') ?? 0;
                $openingCredit = $openingBalanceQuery->sum('credit') ?? 0;

                // حساب حركات الفترة
                $periodMovementQuery = JournalEntryDetail::join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
                    ->where('account_id', $account->id)
                    ->whereBetween('journal_entries.date', [$startDate, $endDate])
                    ->where('journal_entries.status', 1);

                // فلترة فرع القيود
                if ($journalBranch) {
                    $periodMovementQuery->where('journal_entries.branch_id', $journalBranch);
                }

                // فلترة مركز التكلفة
                if ($costCenterId) {
                    $periodMovementQuery->where('journal_entry_details.cost_center_id', $costCenterId);
                }

                $periodDebit = $periodMovementQuery->sum('debit') ?? 0;
                $periodCredit = $periodMovementQuery->sum('credit') ?? 0;

                // تحديد نوع الحساب (مدين أم دائن طبيعياً)
                $isDebitAccount = in_array($account->type, ['asset', 'expense', 'contra_liability', 'contra_equity']);

                // فلترة عرض الحسابات
                $skipAccount = false;
                switch ($accountDisplay) {
                    case '1': // عرض الحسابات التي عليها معاملات
                        $skipAccount = ($periodDebit == 0 && $periodCredit == 0);
                        break;
                    case '2': // إخفاء الحسابات الصفرية
                        $totalOpeningBalance = $openingDebit - $openingCredit;
                        $skipAccount = ($totalOpeningBalance == 0 && $periodDebit == 0 && $periodCredit == 0);
                        break;
                }

                if ($skipAccount) continue;

                // حساب الأرصدة
                $accountBalanceDetails = $this->calculateDetailedAccountBalance(
                    $account,
                    $isDebitAccount,
                    $openingDebit,
                    $openingCredit,
                    $periodDebit,
                    $periodCredit
                );

                $accountBalances[] = $accountBalanceDetails;
            }

            // 5. بناء شجرة الحسابات
            $accountTree = $this->buildAccountTree($accountBalances);

            // 6. حساب المجاميع
            $totals = $this->calculateTotals($accountTree);

            // 7. إعداد بيانات الرسم البياني
            $chartData = $this->prepareChartData($accountTree);

            // 8. تنسيق التواريخ للعرض
            $formattedDates = [
                'from_date' => $startDate->format('d/m/Y'),
                'to_date' => $endDate->format('d/m/Y')
            ];

            // 9. إرجاع الاستجابة
            return response()->json([
                'success' => true,
                'account_tree' => $accountTree,
                'totals' => $totals,
                'chart_data' => $chartData,
                'from_date' => $formattedDates['from_date'],
                'to_date' => $formattedDates['to_date'],
                'filters_applied' => $this->getAppliedFilters($request)
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في تقرير ميزان المراجعة: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حساب نطاق التاريخ بناءً على النوع المحدد
     */
    private function calculateDateRange($dateType, $fromDate = null, $toDate = null)
    {
        $today = now();

        switch ($dateType) {
            case 'today':
                return [
                    'start' => $today->copy()->startOfDay(),
                    'end' => $today->copy()->endOfDay()
                ];

            case 'yesterday':
                $yesterday = $today->copy()->subDay();
                return [
                    'start' => $yesterday->startOfDay(),
                    'end' => $yesterday->endOfDay()
                ];

            case 'this_week':
                return [
                    'start' => $today->copy()->startOfWeek(),
                    'end' => $today->copy()->endOfDay()
                ];

            case 'last_week':
                return [
                    'start' => $today->copy()->subWeek()->startOfWeek(),
                    'end' => $today->copy()->subWeek()->endOfWeek()
                ];

            case 'this_month':
                return [
                    'start' => $today->copy()->startOfMonth(),
                    'end' => $today->copy()->endOfDay()
                ];

            case 'last_month':
                return [
                    'start' => $today->copy()->subMonth()->startOfMonth(),
                    'end' => $today->copy()->subMonth()->endOfMonth()
                ];

            case 'this_year':
                return [
                    'start' => $today->copy()->startOfYear(),
                    'end' => $today->copy()->endOfDay()
                ];

            case 'last_year':
                return [
                    'start' => $today->copy()->subYear()->startOfYear(),
                    'end' => $today->copy()->subYear()->endOfYear()
                ];

            case 'year_to_date':
                return [
                    'start' => $today->copy()->startOfYear(),
                    'end' => $today->copy()->endOfDay()
                ];

            case 'custom':
            default:
                return [
                    'start' => $fromDate ? Carbon::parse($fromDate)->startOfDay() : $today->copy()->startOfYear(),
                    'end' => $toDate ? Carbon::parse($toDate)->endOfDay() : $today->copy()->endOfDay()
                ];
        }
    }

    /**
     * حساب تفاصيل رصيد الحساب
     */
    private function calculateDetailedAccountBalance($account, $isDebitAccount, $openingDebit, $openingCredit, $periodDebit, $periodCredit)
    {
        // حساب الرصيد الافتتاحي
        $openingBalance = $openingDebit - $openingCredit;

        // حساب الرصيد الختامي
        $closingBalance = $openingBalance + $periodDebit - $periodCredit;

        // تحديد المدين والدائن للرصيد الافتتاحي
        $openingBalanceDebit = $openingBalance > 0 ? $openingBalance : 0;
        $openingBalanceCredit = $openingBalance < 0 ? abs($openingBalance) : 0;

        // تحديد المدين والدائن للرصيد الختامي
        $closingBalanceDebit = $closingBalance > 0 ? $closingBalance : 0;
        $closingBalanceCredit = $closingBalance < 0 ? abs($closingBalance) : 0;

        // حساب إجمالي المدين والدائن
        $totalDebit = $openingBalanceDebit + $periodDebit;
        $totalCredit = $openingBalanceCredit + $periodCredit;

        return [
            'id' => $account->id,
            'name' => $account->name,
            'code' => $account->code,
            'type' => $account->type,
            'level' => $account->level ?? 0,
            'parent_id' => $account->parent_id,
            'opening_balance_debit' => $openingBalanceDebit,
            'opening_balance_credit' => $openingBalanceCredit,
            'period_debit' => $periodDebit,
            'period_credit' => $periodCredit,
            'closing_balance_debit' => $closingBalanceDebit,
            'closing_balance_credit' => $closingBalanceCredit,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'children' => []
        ];
    }

    /**
     * بناء شجرة الحسابات
     */
    private function buildAccountTree($accountBalances)
    {
        $accounts = collect($accountBalances)->keyBy('id');
        $tree = [];

        foreach ($accounts as $account) {
            if ($account['parent_id'] === null) {
                // حساب رئيسي
                $tree[] = $this->buildAccountBranch($account, $accounts);
            }
        }

        return $tree;
    }

    /**
     * بناء فرع الحساب مع أطفاله
     */
    private function buildAccountBranch($account, $allAccounts)
    {
        $children = [];

        foreach ($allAccounts as $child) {
            if ($child['parent_id'] === $account['id']) {
                $children[] = $this->buildAccountBranch($child, $allAccounts);
            }
        }

        $account['children'] = $children;
        return $account;
    }

    /**
     * حساب المجاميع الإجمالية
     */
    private function calculateTotals($accountTree)
    {
        $totals = [
            'opening_balance_debit' => 0,
            'opening_balance_credit' => 0,
            'period_debit' => 0,
            'period_credit' => 0,
            'closing_balance_debit' => 0,
            'closing_balance_credit' => 0,
            'total_debit' => 0,
            'total_credit' => 0,
            'accounts_count' => 0
        ];

        $this->sumAccountTreeTotals($accountTree, $totals);

        return $totals;
    }

    /**
     * جمع مجاميع شجرة الحسابات
     */
    private function sumAccountTreeTotals($accounts, &$totals)
    {
        foreach ($accounts as $account) {
            $totals['opening_balance_debit'] += $account['opening_balance_debit'];
            $totals['opening_balance_credit'] += $account['opening_balance_credit'];
            $totals['period_debit'] += $account['period_debit'];
            $totals['period_credit'] += $account['period_credit'];
            $totals['closing_balance_debit'] += $account['closing_balance_debit'];
            $totals['closing_balance_credit'] += $account['closing_balance_credit'];
            $totals['total_debit'] += $account['total_debit'];
            $totals['total_credit'] += $account['total_credit'];
            $totals['accounts_count']++;

            // جمع الحسابات الفرعية
            if (!empty($account['children'])) {
                $this->sumAccountTreeTotals($account['children'], $totals);
            }
        }
    }

    /**
     * إعداد بيانات الرسم البياني
     */
    private function prepareChartData($accountTree)
    {
        $chartData = [
            'labels' => [],
            'debit_amounts' => [],
            'credit_amounts' => []
        ];

        // أخذ أهم الحسابات (أعلى 10 حسابات بالقيمة)
        $flatAccounts = $this->flattenAccountTree($accountTree);

        // ترتيب الحسابات حسب إجمالي القيمة
        usort($flatAccounts, function($a, $b) {
            $totalA = $a['total_debit'] + $a['total_credit'];
            $totalB = $b['total_debit'] + $b['total_credit'];
            return $totalB <=> $totalA;
        });

        // أخذ أعلى 10 حسابات
        $topAccounts = array_slice($flatAccounts, 0, 10);

        foreach ($topAccounts as $account) {
            $chartData['labels'][] = $account['name'];
            $chartData['debit_amounts'][] = $account['total_debit'];
            $chartData['credit_amounts'][] = $account['total_credit'];
        }

        return $chartData;
    }

    /**
     * تحويل شجرة الحسابات إلى مصفوفة مسطحة
     */
    private function flattenAccountTree($accounts)
    {
        $flattened = [];

        foreach ($accounts as $account) {
            $flattened[] = $account;

            if (!empty($account['children'])) {
                $flattened = array_merge($flattened, $this->flattenAccountTree($account['children']));
            }
        }

        return $flattened;
    }

    /**
     * الحصول على الفلاتر المطبقة
     */
    private function getAppliedFilters($request)
    {
        $filters = [];

        if ($request->filled('account_type')) {
            $filters['نوع الحساب'] = $request->input('account_type');
        }

        if ($request->filled('branch')) {
            $branch = Branch::find($request->input('branch'));
            $filters['فرع الحسابات'] = $branch ? $branch->name : 'غير معروف';
        }

        if ($request->filled('journal_branch')) {
            $branch = Branch::find($request->input('journal_branch'));
            $filters['فرع القيود'] = $branch ? $branch->name : 'غير معروف';
        }

        if ($request->filled('level')) {
            $filters['المستوى'] = 'المستوى ' . $request->input('level');
        }

        if ($request->filled('cost_center')) {
            $costCenter = CostCenter::find($request->input('cost_center'));
            $filters['مركز التكلفة'] = $costCenter ? $costCenter->name : 'غير معروف';
        }

        if ($request->filled('added_by')) {
            $user = User::find($request->input('added_by'));
            $filters['أُضيفت بواسطة'] = $user ? $user->name : 'غير معروف';
        }

        if ($request->filled('account_display')) {
            $displayOptions = [
                '1' => 'عرض الحسابات التي عليها معاملات',
                '2' => 'إخفاء الحسابات الصفرية'
            ];
            $filters['عرض الحسابات'] = $displayOptions[$request->input('account_display')] ?? 'غير محدد';
        }

        return $filters;
    }

    /**
     * تحديث الدالة الأصلية لتقرير ميزان المراجعة لتتوافق مع الـ Ajax
     */
    public function trialBalance(Request $request)
    {
        // إعداد البيانات المساعدة للعرض الأولي
        $branches = Branch::all();
        $costCenters = CostCenter::all();
        $users = User::all();
        $accounts = Account::all();

        // البيانات الأولية الفارغة
        $initialData = [
            'accountTree' => [],
            'totals' => [
                'opening_balance_debit' => 0,
                'opening_balance_credit' => 0,
                'period_debit' => 0,
                'period_credit' => 0,
                'closing_balance_debit' => 0,
                'closing_balance_credit' => 0,
                'total_debit' => 0,
                'total_credit' => 0,
                'accounts_count' => 0
            ],
            'startDate' => now()->startOfYear(),
            'endDate' => now()
        ];

        return view('reports::general_accounts.daily_restrictions_reports.trial_balance', compact(
            'branches',
            'costCenters',
            'users',
            'accounts'
        ) + $initialData);
    }

    /**
 * دالة AJAX لتقرير ميزان مراجعة الأرصدة
 */
public function accountBalanceReviewAjax(Request $request)
{
    try {
        // 1. معالجة نطاق التاريخ
        $dateType = $request->input('date_type', 'year_to_date');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // تحديد التواريخ بناءً على النوع
        $dates = $this->calculateDateRange($dateType, $fromDate, $toDate);
        $startDate = $dates['start'];
        $endDate = $dates['end'];

        // 2. بناء استعلام الحسابات مع الفلترة
        $accountsQuery = Account::with(['parent', 'children']);

        // فلترة نوع الحساب
        $accountType = $request->input('account_type');
        if ($accountType == 'رئيسي') {
            $accountsQuery->whereNull('parent_id');
        } elseif ($accountType == 'فرعي') {
            $accountsQuery->whereNotNull('parent_id');
        }

        // فلترة الفرع
        $branchId = $request->input('branch');
        if ($branchId) {
            $accountsQuery->where('branch_id', $branchId);
        }

        // فلترة المستوى
        $level = $request->input('level');
        if ($level !== null && $level !== '') {
            $accountsQuery->where('level', $level);
        }

        // فلترة مركز التكلفة
        $costCenterId = $request->input('cost_center');

        // فلترة المستخدم المضيف
        $addedBy = $request->input('added_by');
        if ($addedBy) {
            $accountsQuery->where('created_by', $addedBy);
        }

        // فلترة الحساب المحدد
        $selectedAccount = $request->input('account');
        if ($selectedAccount) {
            $accountsQuery->where('id', $selectedAccount);
        }

        // 3. جلب الحسابات
        $accounts = $accountsQuery->get();

        // 4. حساب الأرصدة للحسابات
        $accountBalances = [];
        $accountDisplay = $request->input('account_display');
        $journalBranch = $request->input('journal_branch');

        foreach ($accounts as $account) {
            // حساب إجمالي الحركات للحساب
            $totalMovementQuery = JournalEntryDetail::join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
                ->where('account_id', $account->id)
                ->whereBetween('journal_entries.date', [$startDate, $endDate])
                ->where('journal_entries.status', 1);

            // فلترة فرع القيود
            if ($journalBranch) {
                $totalMovementQuery->where('journal_entries.branch_id', $journalBranch);
            }

            // فلترة مركز التكلفة
            if ($costCenterId) {
                $totalMovementQuery->where('journal_entry_details.cost_center_id', $costCenterId);
            }

            $totalDebit = $totalMovementQuery->sum('debit') ?? 0;
            $totalCredit = $totalMovementQuery->sum('credit') ?? 0;

            // فلترة عرض الحسابات
            $skipAccount = false;
            switch ($accountDisplay) {
                case '1': // عرض الحسابات التي عليها معاملات
                    $skipAccount = ($totalDebit == 0 && $totalCredit == 0);
                    break;
                case '2': // إخفاء الحسابات الصفرية
                    $skipAccount = ($totalDebit == 0 && $totalCredit == 0);
                    break;
            }

            if ($skipAccount) continue;

            // إعداد بيانات الحساب
            $accountBalanceDetails = [
                'id' => $account->id,
                'name' => $account->name,
                'code' => $account->code,
                'type' => $account->type,
                'level' => $account->level ?? 0,
                'parent_id' => $account->parent_id,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'children' => []
            ];

            $accountBalances[] = $accountBalanceDetails;
        }

        // 5. بناء شجرة الحسابات
        $accountTree = $this->buildAccountTree($accountBalances);

        // 6. حساب المجاميع
        $totals = [
            'total_debit' => array_sum(array_column($accountBalances, 'total_debit')),
            'total_credit' => array_sum(array_column($accountBalances, 'total_credit')),
            'accounts_count' => count($accountBalances)
        ];

        // 7. إعداد بيانات الرسم البياني
        $chartData = $this->prepareSimpleChartData($accountTree);

        // 8. تنسيق التواريخ للعرض
        $formattedDates = [
            'from_date' => $startDate->format('d/m/Y'),
            'to_date' => $endDate->format('d/m/Y')
        ];

        // 9. إرجاع الاستجابة
        return response()->json([
            'success' => true,
            'account_tree' => $accountTree,
            'totals' => $totals,
            'chart_data' => $chartData,
            'from_date' => $formattedDates['from_date'],
            'to_date' => $formattedDates['to_date'],
            'filters_applied' => $this->getAppliedFilters($request)
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في تقرير ميزان مراجعة الأرصدة: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * إعداد بيانات الرسم البياني المبسط لمراجعة الأرصدة
 */
private function prepareSimpleChartData($accountTree)
{
    $chartData = [
        'labels' => [],
        'debit_amounts' => [],
        'credit_amounts' => []
    ];

    // أخذ أهم الحسابات (أعلى 10 حسابات بالقيمة)
    $flatAccounts = $this->flattenAccountTree($accountTree);

    // ترتيب الحسابات حسب إجمالي القيمة
    usort($flatAccounts, function($a, $b) {
        $totalA = $a['total_debit'] + $a['total_credit'];
        $totalB = $b['total_debit'] + $b['total_credit'];
        return $totalB <=> $totalA;
    });

    // أخذ أعلى 10 حسابات
    $topAccounts = array_slice($flatAccounts, 0, 10);

    foreach ($topAccounts as $account) {
        $chartData['labels'][] = $account['name'];
        $chartData['debit_amounts'][] = $account['total_debit'];
        $chartData['credit_amounts'][] = $account['total_credit'];
    }

    return $chartData;
}

/**
 * تحديث الدالة الأصلية لتقرير ميزان مراجعة الأرصدة
 */
public function accountBalanceReview(Request $request)
{
    // إعداد البيانات المساعدة للعرض الأولي
    $branches = Branch::all();
    $costCenters = CostCenter::all();
    $users = User::all();
    $accounts = Account::all();

    // البيانات الأولية الفارغة
    $initialData = [
        'accountTree' => [],
        'totals' => [
            'total_debit' => 0,
            'total_credit' => 0,
            'accounts_count' => 0
        ],
        'startDate' => now()->startOfYear(),
        'endDate' => now(),
        'pageTitle' => 'تقرير ميزان مراجعة الأرصدة'
    ];

    return view('reports::general_accounts.daily_restrictions_reports.account_blance_review', compact(
        'branches',
        'costCenters',
        'users',
        'accounts'
    ) + $initialData);
}
/**
 * تقرير مراكز التكلفة - الصفحة الرئيسية
 */
public function CostCentersReport(Request $request)
{
    // إعداد البيانات المساعدة للعرض الأولي
    $accounts = Account::all();
    $costCenters = CostCenter::all();
    $branches = Branch::all();
    $users = User::all();

    // البيانات الأولية الفارغة
    $initialData = [
        'costCenterTree' => [],
        'totals' => [
            'total_debit' => 0,
            'total_credit' => 0,
            'total_balance' => 0,
            'cost_centers_count' => 0,
            'transactions_count' => 0
        ],
        'startDate' => now()->startOfYear(),
        'endDate' => now(),
        'pageTitle' => 'تقرير مراكز التكلفة'
    ];

    return view('reports::general_accounts.daily_restrictions_reports.cost_centers_report', compact(
        'accounts',
        'costCenters',
        'branches',
        'users'
    ) + $initialData);
}

/**
 * دالة AJAX لتقرير مراكز التكلفة
 */
public function CostCentersReportAjax(Request $request)
{
    try {
        // 1. معالجة نطاق التاريخ
        $dateType = $request->input('date_type', 'year_to_date');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // تحديد التواريخ بناءً على النوع
        $dates = $this->calculateDateRange($dateType, $fromDate, $toDate);
        $startDate = $dates['start'];
        $endDate = $dates['end'];

        // 2. بناء استعلام القيود مع الفلترة
        $journalEntriesQuery = JournalEntry::with(['details.account', 'costCenter', 'branch'])
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 1);

        // فلترة الحساب
        $accountId = $request->input('account');
        if ($accountId) {
            $journalEntriesQuery->whereHas('details', function($q) use ($accountId) {
                $q->where('account_id', $accountId);
            });
        }

        // فلترة الفرع
        $branchId = $request->input('branch');
        if ($branchId) {
            $journalEntriesQuery->where('branch_id', $branchId);
        }

        // فلترة مركز التكلفة
        $costCenterId = $request->input('cost_center');
        if ($costCenterId) {
            $journalEntriesQuery->whereHas('details', function($q) use ($costCenterId) {
                $q->where('cost_center_id', $costCenterId);
            });
        }

        // فلترة المستخدم المضيف
        $addedBy = $request->input('added_by');
        if ($addedBy) {
            $journalEntriesQuery->where('created_by', $addedBy);
        }

        // فلترة السنة المالية
        $financialYear = $request->input('financial_year');
        if ($financialYear && is_array($financialYear)) {
            $journalEntriesQuery->whereIn('financial_year', $financialYear);
        }

        // 3. جلب القيود
        $journalEntries = $journalEntriesQuery->get();

        // 4. تجميع البيانات حسب مراكز التكلفة
        $costCenterData = [];
        $totalTransactions = 0;

        foreach ($journalEntries as $entry) {
            foreach ($entry->details as $detail) {
                if ($detail->cost_center_id) {
                    $costCenterId = $detail->cost_center_id;

                    if (!isset($costCenterData[$costCenterId])) {
                        $costCenter = $detail->costCenter ?? CostCenter::find($costCenterId);
                        $costCenterData[$costCenterId] = [
                            'id' => $costCenterId,
                            'name' => $costCenter ? $costCenter->name : 'مركز تكلفة محذوف',
                            'code' => $costCenter ? $costCenter->code : 'N/A',
                            'is_main' => $costCenter ? $costCenter->is_main : 0,
                            'parent_id' => $costCenter ? $costCenter->parent_id : null,
                            'total_debit' => 0,
                            'total_credit' => 0,
                            'total_balance' => 0,
                            'transactions_count' => 0,
                            'children' => []
                        ];
                    }

                    $costCenterData[$costCenterId]['total_debit'] += $detail->debit;
                    $costCenterData[$costCenterId]['total_credit'] += $detail->credit;
                    $costCenterData[$costCenterId]['total_balance'] += ($detail->debit - $detail->credit);
                    $costCenterData[$costCenterId]['transactions_count']++;
                    $totalTransactions++;
                }
            }
        }

        // فلترة عرض مراكز التكلفة
        $displayOption = $request->input('display_option');
        if ($displayOption == '1') { // إظهار المراكز التي عليها معاملات فقط
            $costCenterData = array_filter($costCenterData, function($center) {
                return $center['transactions_count'] > 0;
            });
        } elseif ($displayOption == '2') { // إخفاء المراكز الصفرية
            $costCenterData = array_filter($costCenterData, function($center) {
                return $center['total_balance'] != 0;
            });
        }

        // 5. بناء شجرة مراكز التكلفة
        $costCenterTree = $this->buildCostCenterTree(array_values($costCenterData));

        // 6. حساب المجاميع
        $totals = [
            'total_debit' => array_sum(array_column($costCenterData, 'total_debit')),
            'total_credit' => array_sum(array_column($costCenterData, 'total_credit')),
            'total_balance' => array_sum(array_column($costCenterData, 'total_balance')),
            'cost_centers_count' => count($costCenterData),
            'transactions_count' => $totalTransactions
        ];

        // 7. إعداد بيانات الرسم البياني
        $chartData = $this->prepareCostCenterChartData($costCenterTree);

        // 8. تنسيق التواريخ للعرض
        $formattedDates = [
            'from_date' => $startDate->format('d/m/Y'),
            'to_date' => $endDate->format('d/m/Y')
        ];

        // 9. إرجاع الاستجابة
        return response()->json([
            'success' => true,
            'cost_center_tree' => $costCenterTree,
            'totals' => $totals,
            'chart_data' => $chartData,
            'from_date' => $formattedDates['from_date'],
            'to_date' => $formattedDates['to_date'],
            'filters_applied' => $this->getCostCenterAppliedFilters($request)
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في تقرير مراكز التكلفة: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * بناء شجرة مراكز التكلفة
 */
private function buildCostCenterTree($costCenterData)
{
    $costCenters = collect($costCenterData)->keyBy('id');
    $tree = [];

    foreach ($costCenters as $costCenter) {
        if ($costCenter['parent_id'] === null || $costCenter['is_main'] == 1) {
            // مركز تكلفة رئيسي
            $tree[] = $this->buildCostCenterBranch($costCenter, $costCenters);
        }
    }

    return $tree;
}

/**
 * بناء فرع مركز التكلفة مع أطفاله
 */
private function buildCostCenterBranch($costCenter, $allCostCenters)
{
    $children = [];

    foreach ($allCostCenters as $child) {
        if ($child['parent_id'] === $costCenter['id']) {
            $children[] = $this->buildCostCenterBranch($child, $allCostCenters);
        }
    }

    $costCenter['children'] = $children;
    return $costCenter;
}

/**
 * إعداد بيانات الرسم البياني لمراكز التكلفة
 */
private function prepareCostCenterChartData($costCenterTree)
{
    $chartData = [
        'labels' => [],
        'debit_amounts' => [],
        'credit_amounts' => [],
        'balance_amounts' => []
    ];

    // أخذ أهم مراكز التكلفة (أعلى 10 مراكز بالقيمة)
    $flatCostCenters = $this->flattenCostCenterTree($costCenterTree);

    // ترتيب مراكز التكلفة حسب إجمالي الحركة
    usort($flatCostCenters, function($a, $b) {
        $totalA = abs($a['total_debit']) + abs($a['total_credit']);
        $totalB = abs($b['total_debit']) + abs($b['total_credit']);
        return $totalB <=> $totalA;
    });

    // أخذ أعلى 10 مراكز
    $topCostCenters = array_slice($flatCostCenters, 0, 10);

    foreach ($topCostCenters as $costCenter) {
        $chartData['labels'][] = $costCenter['name'];
        $chartData['debit_amounts'][] = $costCenter['total_debit'];
        $chartData['credit_amounts'][] = $costCenter['total_credit'];
        $chartData['balance_amounts'][] = abs($costCenter['total_balance']);
    }

    return $chartData;
}

/**
 * تحويل شجرة مراكز التكلفة إلى مصفوفة مسطحة
 */
private function flattenCostCenterTree($costCenters)
{
    $flattened = [];

    foreach ($costCenters as $costCenter) {
        $flattened[] = $costCenter;

        if (!empty($costCenter['children'])) {
            $flattened = array_merge($flattened, $this->flattenCostCenterTree($costCenter['children']));
        }
    }

    return $flattened;
}

/**
 * الحصول على الفلاتر المطبقة لمراكز التكلفة
 */
private function getCostCenterAppliedFilters($request)
{
    $filters = [];

    if ($request->filled('account')) {
        $account = Account::find($request->input('account'));
        $filters['الحساب'] = $account ? $account->name : 'غير معروف';
    }

    if ($request->filled('branch')) {
        $branch = Branch::find($request->input('branch'));
        $filters['الفرع'] = $branch ? $branch->name : 'غير معروف';
    }

    if ($request->filled('cost_center')) {
        $costCenter = CostCenter::find($request->input('cost_center'));
        $filters['مركز التكلفة'] = $costCenter ? $costCenter->name : 'غير معروف';
    }

    if ($request->filled('added_by')) {
        $user = User::find($request->input('added_by'));
        $filters['أُضيفت بواسطة'] = $user ? $user->name : 'غير معروف';
    }

    if ($request->filled('financial_year')) {
        $years = is_array($request->input('financial_year'))
            ? implode(', ', $request->input('financial_year'))
            : $request->input('financial_year');
        $filters['السنة المالية'] = $years;
    }

    if ($request->filled('display_option')) {
        $displayOptions = [
            '1' => 'المراكز التي عليها معاملات',
            '2' => 'إخفاء المراكز الصفرية'
        ];
        $filters['خيار العرض'] = $displayOptions[$request->input('display_option')] ?? 'غير محدد';
    }

    return $filters;
}
}
