<?php

namespace Modules\Reports\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Branch;
use App\Models\Client;
use App\Models\CostCenter;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpensesCategory;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\PurchaseInvoice;
use App\Models\Receipt;
use App\Models\ReceiptCategory;
use App\Models\Supplier;
use App\Models\SupplyOrder;
use App\Models\TaxSitting;
use App\Models\Treasury;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneralAccountsController extends Controller
{
    // دالة لعرض الحسابات العامة
    public function index()
    {
        return view('reports::general_accounts.index'); // يعرض الملف الذي يحتوي على الحسابات العامة
    }
    public function taxReport(Request $request)
    {
        // جلب فواتير المبيعات
        $salesInvoices = Invoice::where('type', 'normal')->get();

        // جلب فواتير المرتجعات
        $returnInvoices = Invoice::where('type', 'returned')->get();

        // جلب فواتير المشتريات
        $purchaseInvoices = PurchaseInvoice::all();

        $taxData = [
            'sales' => $salesInvoices,
            'returns' => $returnInvoices,
            'purchases' => $purchaseInvoices,
        ];

        return view('reports::general_accounts.accountGeneral.tax_report', compact('taxData'));
    }




public function taxDeclaration(Request $request)
{
    $branches = Branch::all();
    $taxSettings = TaxSitting::all(); // جلب إعدادات الضرائب

    return view('reports::general_accounts.accountGeneral.tax_declaration', compact('branches', 'taxSettings'));
}

public function taxDeclarationAjax(Request $request)
{
    try {
        // تحديد الفترة الزمنية
        $fromDate = $request->from_date ?? now()->subDays(30)->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        // جلب إعدادات الضرائب
        $taxSettings = TaxSitting::all()->keyBy('name');

        // فلترة المبيعات مع ربطها بإعدادات الضرائب
        $salesQuery = Invoice::with(['client', 'branch', 'employee', 'invoiceItems'])
            ->whereBetween('invoice_date', [$fromDate, $toDate]);

        // تطبيق فلاتر المبيعات
        if ($request->filled('tax_type')) {
            $salesQuery->where('tax_type', $request->tax_type);
        }

        if ($request->filled('income_type')) {
            switch ($request->income_type) {
                case 'مستحقة':
                    $salesQuery->where('payment_status', 3); // غير مدفوع
                    break;
                case 'مدفوع بالكامل':
                    $salesQuery->where('payment_status', 1); // مدفوع بالكامل
                    break;
                case 'مدفوع جزئيا':
                    $salesQuery->where('payment_status', 2); // مدفوع جزئياً
                    break;
            }
        }

        if ($request->filled('branch')) {
            $salesQuery->where('branch_id', $request->branch);
        }

        // فلترة المشتريات مع ربطها بإعدادات الضرائب
        $purchasesQuery = PurchaseInvoice::with(['supplier', 'branch', 'purchaseItems'])
            ->whereBetween('date', [$fromDate, $toDate]);

        // تطبيق فلاتر المشتريات
        if ($request->filled('tax_type')) {
            $purchasesQuery->where('tax_type', $request->tax_type);
        }

        if ($request->filled('income_type')) {
            switch ($request->income_type) {
                case 'مستحقة':
                    $purchasesQuery->where('is_paid', false);
                    break;
                case 'مدفوع بالكامل':
                    $purchasesQuery->where('is_paid', true);
                    break;
                case 'مدفوع جزئيا':
                    $purchasesQuery->where('is_paid', false);
                    break;
            }
        }

        if ($request->filled('branch')) {
            $purchasesQuery->where('branch_id', $request->branch);
        }

        // جلب البيانات
        $salesInvoices = $salesQuery->get();
        $purchasesInvoices = $purchasesQuery->get();

        // حساب الاقرار الضريبي بناءً على إعدادات الضرائب
        $salesTaxDeclaration = $this->calculateSalesTaxDeclaration($salesInvoices, $taxSettings);
        $purchasesTaxDeclaration = $this->calculatePurchasesTaxDeclaration($purchasesInvoices, $taxSettings);

        // حساب الإجماليات
        $totalSales = $salesInvoices->sum('grand_total');
        $totalPurchases = $purchasesInvoices->sum('grand_total');
        $totalTaxOutput = $salesTaxDeclaration->sum('tax_amount'); // ضريبة المخرجات من المبيعات
        $totalTaxInput = $purchasesTaxDeclaration->sum('tax_amount'); // ضريبة المدخلات من المشتريات
        $netTaxDue = $totalTaxOutput - $totalTaxInput; // صافي الضريبة المستحقة
        $totalInvoices = $salesInvoices->count() + $purchasesInvoices->count();

        // بيانات الرسم البياني للمبيعات
        $salesTaxData = [
            'labels' => $salesTaxDeclaration->pluck('tax_name')->toArray(),
            'amounts' => $salesTaxDeclaration->pluck('tax_amount')->toArray(),
            'total' => $salesTaxDeclaration->sum('tax_amount')
        ];

        // بيانات الرسم البياني للمشتريات
        $purchasesTaxData = [
            'labels' => $purchasesTaxDeclaration->pluck('tax_name')->toArray(),
            'amounts' => $purchasesTaxDeclaration->pluck('tax_amount')->toArray(),
            'total' => $purchasesTaxDeclaration->sum('tax_amount')
        ];

        // إعداد البيانات للإرجاع
        $responseData = [
            'success' => true,
            'from_date' => Carbon::parse($fromDate)->format('d/m/Y'),
            'to_date' => Carbon::parse($toDate)->format('d/m/Y'),
            'totals' => [
                'total_sales' => $totalSales,
                'total_purchases' => $totalPurchases,
                'total_tax_output' => $totalTaxOutput,
                'total_tax_input' => $totalTaxInput,
                'net_tax_due' => $netTaxDue,
                'total_invoices' => $totalInvoices
            ],
            'sales_tax_declaration' => $salesTaxDeclaration,
            'purchases_tax_declaration' => $purchasesTaxDeclaration,
            'sales_tax_data' => $salesTaxData,
            'purchases_tax_data' => $purchasesTaxData,
            'tax_settings' => $taxSettings->values()
        ];

        return response()->json($responseData);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في جلب البيانات: ' . $e->getMessage()
        ], 500);
    }
}

// فنكشن حساب ضريبة المبيعات بناءً على إعدادات الضرائب
private function calculateSalesTaxDeclaration($salesInvoices, $taxSettings)
{
    $taxDeclaration = collect();

    foreach ($taxSettings as $taxSetting) {
        // فلترة الفواتير حسب نوع الضريبة
        $filteredInvoices = $salesInvoices->filter(function ($invoice) use ($taxSetting) {
            return $invoice->tax_type === $taxSetting->name ||
                   $invoice->tax_name === $taxSetting->name ||
                   $invoice->tax_id === $taxSetting->id;
        });

        if ($filteredInvoices->count() > 0) {
            $baseAmount = 0;
            $taxAmount = 0;
            $totalAmount = 0;

            // حساب المبالغ لكل فاتورة
            foreach ($filteredInvoices as $invoice) {
                $invoiceBaseAmount = $invoice->subtotal ?? ($invoice->grand_total - $invoice->tax_total);
                $invoiceTaxAmount = 0;

                // حساب الضريبة بناءً على النوع والنسبة
                if ($taxSetting->type === 'percentage') {
                    $invoiceTaxAmount = ($invoiceBaseAmount * $taxSetting->tax) / 100;
                } elseif ($taxSetting->type === 'fixed') {
                    $invoiceTaxAmount = $taxSetting->tax;
                } else {
                    // استخدام الضريبة المحفوظة في الفاتورة
                    $invoiceTaxAmount = $invoice->tax_total ?? 0;
                }

                $baseAmount += $invoiceBaseAmount;
                $taxAmount += $invoiceTaxAmount;
                $totalAmount += $invoiceBaseAmount + $invoiceTaxAmount;
            }

            $taxDeclaration->push([
                'tax_id' => $taxSetting->id,
                'tax_name' => $taxSetting->name,
                'tax_rate' => $taxSetting->tax,
                'tax_type' => $taxSetting->type,
                'base_amount' => $baseAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'invoices_count' => $filteredInvoices->count(),
                'average_tax_rate' => $baseAmount > 0 ? ($taxAmount / $baseAmount) * 100 : 0
            ]);
        }
    }

    // إضافة الفواتير التي لا تحتوي على ضريبة أو ضريبة غير محددة
    $unclassifiedInvoices = $salesInvoices->filter(function ($invoice) use ($taxSettings) {
        $hasMatchingTax = false;
        foreach ($taxSettings as $taxSetting) {
            if ($invoice->tax_type === $taxSetting->name ||
                $invoice->tax_name === $taxSetting->name ||
                $invoice->tax_id === $taxSetting->id) {
                $hasMatchingTax = true;
                break;
            }
        }
        return !$hasMatchingTax;
    });

    if ($unclassifiedInvoices->count() > 0) {
        $baseAmount = $unclassifiedInvoices->sum(function ($invoice) {
            return $invoice->subtotal ?? ($invoice->grand_total - ($invoice->tax_total ?? 0));
        });
        $taxAmount = $unclassifiedInvoices->sum('tax_total');
        $totalAmount = $unclassifiedInvoices->sum('grand_total');

        $taxDeclaration->push([
            'tax_id' => null,
            'tax_name' => 'غير مصنف',
            'tax_rate' => 0,
            'tax_type' => 'unclassified',
            'base_amount' => $baseAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'invoices_count' => $unclassifiedInvoices->count(),
            'average_tax_rate' => $baseAmount > 0 ? ($taxAmount / $baseAmount) * 100 : 0
        ]);
    }

    return $taxDeclaration;
}

// فنكشن حساب ضريبة المشتريات بناءً على إعدادات الضرائب
private function calculatePurchasesTaxDeclaration($purchasesInvoices, $taxSettings)
{
    $taxDeclaration = collect();

    foreach ($taxSettings as $taxSetting) {
        // فلترة الفواتير حسب نوع الضريبة
        $filteredInvoices = $purchasesInvoices->filter(function ($invoice) use ($taxSetting) {
            return $invoice->tax_type === $taxSetting->name ||
                   $invoice->tax_name === $taxSetting->name ||
                   $invoice->tax_id === $taxSetting->id;
        });

        if ($filteredInvoices->count() > 0) {
            $baseAmount = 0;
            $taxAmount = 0;
            $totalAmount = 0;

            // حساب المبالغ لكل فاتورة
            foreach ($filteredInvoices as $invoice) {
                $invoiceBaseAmount = $invoice->subtotal ?? ($invoice->grand_total - $invoice->tax_total);
                $invoiceTaxAmount = 0;

                // حساب الضريبة بناءً على النوع والنسبة
                if ($taxSetting->type === 'percentage') {
                    $invoiceTaxAmount = ($invoiceBaseAmount * $taxSetting->tax) / 100;
                } elseif ($taxSetting->type === 'fixed') {
                    $invoiceTaxAmount = $taxSetting->tax;
                } else {
                    // استخدام الضريبة المحفوظة في الفاتورة
                    $invoiceTaxAmount = $invoice->tax_total ?? 0;
                }

                $baseAmount += $invoiceBaseAmount;
                $taxAmount += $invoiceTaxAmount;
                $totalAmount += $invoiceBaseAmount + $invoiceTaxAmount;
            }

            $taxDeclaration->push([
                'tax_id' => $taxSetting->id,
                'tax_name' => $taxSetting->name,
                'tax_rate' => $taxSetting->tax,
                'tax_type' => $taxSetting->type,
                'base_amount' => $baseAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'invoices_count' => $filteredInvoices->count(),
                'average_tax_rate' => $baseAmount > 0 ? ($taxAmount / $baseAmount) * 100 : 0
            ]);
        }
    }

    // إضافة الفواتير التي لا تحتوي على ضريبة أو ضريبة غير محددة
    $unclassifiedInvoices = $purchasesInvoices->filter(function ($invoice) use ($taxSettings) {
        $hasMatchingTax = false;
        foreach ($taxSettings as $taxSetting) {
            if ($invoice->tax_type === $taxSetting->name ||
                $invoice->tax_name === $taxSetting->name ||
                $invoice->tax_id === $taxSetting->id) {
                $hasMatchingTax = true;
                break;
            }
        }
        return !$hasMatchingTax;
    });

    if ($unclassifiedInvoices->count() > 0) {
        $baseAmount = $unclassifiedInvoices->sum(function ($invoice) {
            return $invoice->subtotal ?? ($invoice->grand_total - ($invoice->tax_total ?? 0));
        });
        $taxAmount = $unclassifiedInvoices->sum('tax_total');
        $totalAmount = $unclassifiedInvoices->sum('grand_total');

        $taxDeclaration->push([
            'tax_id' => null,
            'tax_name' => 'غير مصنف',
            'tax_rate' => 0,
            'tax_type' => 'unclassified',
            'base_amount' => $baseAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'invoices_count' => $unclassifiedInvoices->count(),
            'average_tax_rate' => $baseAmount > 0 ? ($taxAmount / $baseAmount) * 100 : 0
        ]);
    }

    return $taxDeclaration;
}



public function incomeStatement(Request $request)
{
    $branches = Branch::all();
    $costCenters = CostCenter::all();

    return view('reports::general_accounts.accountGeneral.income_statement', compact('branches', 'costCenters'));
}

/**
 * جلب بيانات قائمة الدخل عبر AJAX
 */
public function incomeStatementAjax(Request $request)
{
    try {
        // جلب الإيرادات والمصروفات مع الحسابات الفرعية
        $revenuesQuery = Account::where('name', 'الإيرادات')->with('childrenRecursive');
        $expensesQuery = Account::where('name', 'المصروفات')->with('childrenRecursive');

        // تطبيق فلترة السنة المالية
        if ($request->has('financial_year') && !empty($request->financial_year)) {
            $years = $request->input('financial_year');

            if (in_array('current', $years)) {
                // السنة المفتوحة (السنة الحالية)
                $revenuesQuery->whereYear('created_at', date('Y'));
                $expensesQuery->whereYear('created_at', date('Y'));
            } elseif (in_array('all', $years)) {
                // جميع السنوات - لا نطبق فلترة
            } else {
                // سنوات محددة
                $revenuesQuery->whereIn(DB::raw('YEAR(created_at)'), $years);
                $expensesQuery->whereIn(DB::raw('YEAR(created_at)'), $years);
            }
        }

        // تطبيق فلترة عرض الحسابات
        if ($request->has('account') && !empty($request->account)) {
            $accountFilter = $request->input('account');

            if ($accountFilter == '1') {
                // عرض الحسابات التي عليها معاملات
                $revenuesQuery->has('transactions');
                $expensesQuery->has('transactions');
            } elseif ($accountFilter == '2') {
                // إخفاء الحسابات الصفرية
                $revenuesQuery->where('balance', '<>', 0);
                $expensesQuery->where('balance', '<>', 0);
            }
        }

        // تطبيق فلترة الفرع
        if ($request->has('branch') && !empty($request->branch)) {
            $branchFilter = $request->input('branch');
            $revenuesQuery->where('branch_id', $branchFilter);
            $expensesQuery->where('branch_id', $branchFilter);
        }

        // تطبيق فلترة مركز التكلفة
        if ($request->has('cost_center') && !empty($request->cost_center)) {
            $costCenterFilter = $request->input('cost_center');
            $revenuesQuery->where('cost_center_id', $costCenterFilter);
            $expensesQuery->where('cost_center_id', $costCenterFilter);
        }

        // تطبيق فلترة المستويات
        if ($request->has('level') && !empty($request->level)) {
            $levelFilter = $request->input('level');
            $revenuesQuery->where('level', '<=', $levelFilter);
            $expensesQuery->where('level', '<=', $levelFilter);
        }

        // جلب البيانات
        $revenues = $revenuesQuery->first();
        $expenses = $expensesQuery->first();

        // تحضير بيانات الإيرادات
        $revenuesData = [];
        if ($revenues && $revenues->childrenRecursive) {
            foreach ($revenues->childrenRecursive as $revenue) {
                $revenuesData[] = [
                    'id' => $revenue->id,
                    'name' => $revenue->name,
                    'code' => $revenue->code,
                    'balance' => $revenue->balance ?? 0,
                    'level' => $revenue->level ?? 1
                ];
            }
        }

        // تحضير بيانات المصروفات
        $expensesData = [];
        if ($expenses && $expenses->childrenRecursive) {
            foreach ($expenses->childrenRecursive as $expense) {
                $expensesData[] = [
                    'id' => $expense->id,
                    'name' => $expense->name,
                    'code' => $expense->code,
                    'balance' => $expense->balance ?? 0,
                    'level' => $expense->level ?? 1
                ];
            }
        }

        // حساب الإجماليات
        $totalRevenues = collect($revenuesData)->sum('balance');
        $totalExpenses = collect($expensesData)->sum('balance');
        $netIncome = $totalRevenues - $totalExpenses;
        $profitMargin = $totalRevenues > 0 ? (($netIncome / $totalRevenues) * 100) : 0;

        // تحضير بيانات الرسم البياني
        $chartData = [
            'labels' => ['الإيرادات', 'المصروفات', 'صافي الدخل'],
            'amounts' => [$totalRevenues, $totalExpenses, $netIncome]
        ];

        return response()->json([
            'success' => true,
            'revenues' => $revenuesData,
            'expenses' => $expensesData,
            'totals' => [
                'total_revenues' => $totalRevenues,
                'total_expenses' => $totalExpenses,
                'net_income' => $netIncome,
                'profit_margin' => $profitMargin
            ],
            'chart_data' => $chartData,
            'filters_applied' => $request->all()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في جلب البيانات: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * تصدير قائمة الدخل إلى Excel
 */
public function exportIncomeStatementExcel(Request $request)
{
    try {
        // استخدام نفس منطق الفلترة من incomeStatementAjax
        $data = $this->incomeStatementAjax($request);
        $responseData = json_decode($data->getContent(), true);

        if (!$responseData['success']) {
            return response()->json(['error' => 'فشل في جلب البيانات'], 500);
        }

        // إنشاء ملف Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // إعداد العنوان
        $sheet->setCellValue('A1', 'قائمة الدخل');
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // تاريخ التقرير
        $sheet->setCellValue('A2', 'تاريخ التقرير: ' . date('Y-m-d H:i:s'));
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        $row = 4;

        // قسم الإيرادات
        $sheet->setCellValue('A' . $row, 'الإيرادات');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('198754');
        $row++;

        $sheet->setCellValue('A' . $row, 'اسم الحساب');
        $sheet->setCellValue('B' . $row, 'الكود');
        $sheet->setCellValue('C' . $row, 'المبلغ (ريال)');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $row++;

        foreach ($responseData['revenues'] as $revenue) {
            $sheet->setCellValue('A' . $row, $revenue['name']);
            $sheet->setCellValue('B' . $row, $revenue['code']);
            $sheet->setCellValue('C' . $row, number_format($revenue['balance'], 2));
            $row++;
        }

        // إجمالي الإيرادات
        $sheet->setCellValue('A' . $row, 'إجمالي الإيرادات');
        $sheet->setCellValue('C' . $row, number_format($responseData['totals']['total_revenues'], 2));
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $row += 2;

        // قسم المصروفات
        $sheet->setCellValue('A' . $row, 'المصروفات');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('dc3545');
        $row++;

        $sheet->setCellValue('A' . $row, 'اسم الحساب');
        $sheet->setCellValue('B' . $row, 'الكود');
        $sheet->setCellValue('C' . $row, 'المبلغ (ريال)');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $row++;

        foreach ($responseData['expenses'] as $expense) {
            $sheet->setCellValue('A' . $row, $expense['name']);
            $sheet->setCellValue('B' . $row, $expense['code']);
            $sheet->setCellValue('C' . $row, number_format($expense['balance'], 2));
            $row++;
        }

        // إجمالي المصروفات
        $sheet->setCellValue('A' . $row, 'إجمالي المصروفات');
        $sheet->setCellValue('C' . $row, number_format($responseData['totals']['total_expenses'], 2));
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $row += 2;

        // صافي الدخل
        $sheet->setCellValue('A' . $row, 'صافي الدخل');
        $sheet->setCellValue('C' . $row, number_format($responseData['totals']['net_income'], 2));
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('6f42c1');

        // تنسيق الأعمدة
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        // إنشاء ملف Excel وتحميله
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'قائمة_الدخل_' . date('Y-m-d') . '.xlsx';

        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        return response()->json(['error' => 'فشل في تصدير الملف: ' . $e->getMessage()], 500);
    }
}

/**
 * تصدير قائمة الدخل إلى PDF
 */
public function exportIncomeStatementPdf(Request $request)
{
    try {
        // استخدام نفس منطق الفلترة من incomeStatementAjax
        $data = $this->incomeStatementAjax($request);
        $responseData = json_decode($data->getContent(), true);

        if (!$responseData['success']) {
            return response()->json(['error' => 'فشل في جلب البيانات'], 500);
        }

        // إنشاء PDF باستخدام DomPDF أو أي مكتبة PDF أخرى
        $pdf = app('dompdf.wrapper');

        $html = view('reports::general_accounts.accountGeneral.income_statement_pdf', [
            'revenues' => $responseData['revenues'],
            'expenses' => $responseData['expenses'],
            'totals' => $responseData['totals'],
            'date' => date('Y-m-d H:i:s')
        ])->render();

        $pdf->loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'قائمة_الدخل_' . date('Y-m-d') . '.pdf';

        return $pdf->download($fileName);

    } catch (\Exception $e) {
        return response()->json(['error' => 'فشل في تصدير الملف: ' . $e->getMessage()], 500);
    }
}

/**
 * طباعة قائمة الدخل
 */
public function printIncomeStatement(Request $request)
{
    try {
        // استخدام نفس منطق الفلترة من incomeStatementAjax
        $data = $this->incomeStatementAjax($request);
        $responseData = json_decode($data->getContent(), true);

        if (!$responseData['success']) {
            return response()->json(['error' => 'فشل في جلب البيانات'], 500);
        }

        return view('reports::general_accounts.accountGeneral.income_statement_print', [
            'revenues' => $responseData['revenues'],
            'expenses' => $responseData['expenses'],
            'totals' => $responseData['totals'],
            'date' => date('Y-m-d H:i:s')
        ]);

    } catch (\Exception $e) {
        return back()->with('error', 'فشل في تحضير التقرير للطباعة: ' . $e->getMessage());
    }

}

    // المصروفات
    public function splitExpensesByCategory(Request $request)
    {
        // جلب البيانات للفلاتر
        $branches = Branch::all();
        $treasuries = Treasury::all();
        $expensesCategory = ExpensesCategory::all();
        $accounts = Account::all();
        $employees = Employee::all();

        // فلترة البيانات بناءً على الطلب
        $query = Expense::query();

        if ($request->has('treasury') && $request->treasury != '') {
            $query->where('treasury_id', $request->treasury);
        }

        if ($request->has('employee') && $request->employee != '') {
            $query->where('seller', $request->employee);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->has('group_by') && $request->group_by != '') {
            $query->where('expenses_category_id', $request->group_by);
        }

        if ($request->has('branch') && $request->branch != '') {
            $query->where('branch_id', $request->branch);
        }

        if ($request->has('currency') && $request->currency != 'all') {
            $query->where('currency', $request->currency);
        }

        // جلب البيانات مع العلاقات
        $expenses = $query->with(['expenses_category', 'treasury', 'employee', 'branch'])->get();

        // تجميع البيانات حسب التصنيف
        $groupedExpenses = $expenses->groupBy('expenses_category_id');

        // إعداد بيانات المخطط
        $chartLabels = [];
        $chartData = [];

        foreach ($groupedExpenses as $categoryId => $expensesInCategory) {
            $category = $expensesInCategory->first()->expenses_category;
            $chartLabels[] = $category->name ?? 'غير مصنف';
            $chartData[] = $expensesInCategory->sum('amount') + $expensesInCategory->sum('tax1_amount') + $expensesInCategory->sum('tax2_amount');
        }

        return view('reports::general_accounts.split_expenses.expenses_by_category', compact('branches', 'expensesCategory', 'treasuries', 'accounts', 'employees', 'expenses', 'groupedExpenses', 'chartLabels', 'chartData'));
    }
    public function splitExpensesBySeller(Request $request)
    {
        // جلب البيانات للفلاتر
        $branches = Branch::all();
        $treasuries = Treasury::all();
        $expensesCategory = ExpensesCategory::all();
        $accounts = Account::all();
        $employees = Employee::all();

        // فلترة البيانات بناءً على الطلب
        $query = Expense::query();

        if ($request->has('treasury') && $request->treasury != '') {
            $query->where('treasury_id', $request->treasury);
        }

        if ($request->has('employee') && $request->employee != '') {
            $query->where('seller', $request->employee);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->has('branch') && $request->branch != '') {
            $query->where('branch_id', $request->branch);
        }

        if ($request->has('currency') && $request->currency != 'all') {
            $query->where('currency', $request->currency);
        }

        // جلب البيانات مع العلاقات
        $expenses = $query->with(['expenses_category', 'treasury', 'employee', 'branch'])->get();

        // تجميع البيانات حسب البائع
        $groupedExpenses = $expenses->groupBy('seller');

        // إعداد بيانات المخطط
        $chartLabels = [];
        $chartData = [];

        foreach ($groupedExpenses as $sellerId => $expensesInSeller) {
            $seller = $expensesInSeller->first()->employee;
            $chartLabels[] = $seller->full_name ?? 'غير معروف';
            $chartData[] = $expensesInSeller->sum('amount') + $expensesInSeller->sum('tax1_amount') + $expensesInSeller->sum('tax2_amount');
        }

        return view('reports::general_accounts.split_expenses.expenses_by_seller', compact('branches', 'expensesCategory', 'treasuries', 'accounts', 'employees', 'expenses', 'groupedExpenses', 'chartLabels', 'chartData'));
    }



    public function splitExpensesByClient(Request $request)
    {
        // جلب البيانات للفلاتر
        $branches = Branch::all();
        $treasuries = Treasury::all();
        $expensesCategory = ExpensesCategory::all();
        $accounts = Account::all();
        $employees = Employee::all();
        $clients = Client::all(); // جلب جميع العملاء

        // فلترة البيانات بناءً على الطلب
        $query = Expense::query();

        if ($request->has('treasury') && $request->treasury != '') {
            $query->where('treasury_id', $request->treasury);
        }

        if ($request->has('client') && $request->client != '') {
            $query->where('client_id', $request->client);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->has('branch') && $request->branch != '') {
            $query->where('branch_id', $request->branch);
        }

        if ($request->has('currency') && $request->currency != 'all') {
            $query->where('currency', $request->currency);
        }

        // جلب البيانات مع العلاقات
        $expenses = $query->with(['expenses_category', 'treasury', 'client', 'branch'])->get();

        // تجميع البيانات حسب العميل
        $groupedExpenses = $expenses->groupBy('client_id');

        // إعداد بيانات المخطط
        $chartLabels = [];
        $chartData = [];

        foreach ($groupedExpenses as $clientId => $expensesInClient) {
            $client = $expensesInClient->first()->client;
            $chartLabels[] = $client->name ?? 'غير معروف';
            $chartData[] = $expensesInClient->sum('amount') + $expensesInClient->sum('tax1_amount') + $expensesInClient->sum('tax2_amount');
        }

        return view('reports::general_accounts.split_expenses.expenses_by_client', compact('branches', 'expensesCategory', 'treasuries', 'accounts', 'employees', 'clients', 'expenses', 'groupedExpenses', 'chartLabels', 'chartData'));
    }


    public function splitExpensesByEmployee(Request $request)
{
    // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
    $employees = User::where('role', 'employee')->get();
    $branches = Branch::orderBy('name')->get();
    $treasuries = Treasury::orderBy('name')->get();
    $expensesCategories = ExpensesCategory::orderBy('name')->get();
    $accounts = Account::orderBy('name')->get();
    $suppliers = Supplier::orderBy('trade_name')->get();

    // 2. تحديد التواريخ الافتراضية
    $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : now()->subMonth();
    $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : now();

    // 3. إعداد البيانات الافتراضية الفارغة
    $totals = [
        'total_expenses' => 0,
        'total_taxes' => 0,
        'total_with_tax' => 0,
        'total_count' => 0,
        'average_amount' => 0,
        'employees_count' => 0,
    ];

    $groupedData = collect();
    $chartData = ['labels' => [], 'amounts' => [], 'taxes' => []];

    // 4. إرجاع العرض
    return view('reports::general_accounts.split_expenses.expenses_by_employee', compact(
        'employees',
        'branches',
        'treasuries',
        'expensesCategories',
        'accounts',
        'suppliers',
        'totals',
        'chartData',
        'fromDate',
        'toDate',
        'groupedData'
    ));
}

/**
 * الحصول على بيانات تقرير المصروفات حسب الموظف عبر AJAX
 */
public function expensesByEmployeeAjax(Request $request)
{
    try {
        // 1. التحقق من صحة البيانات المدخلة
        $validatedData = $request->validate([
            'employee' => 'nullable|exists:users,id',
            'supplier' => 'nullable|exists:suppliers,id',
            'branch' => 'nullable|exists:branches,id',
            'account' => 'nullable|exists:accounts,id',
            'treasury' => 'nullable|exists:treasuries,id',
            'expense_category' => 'nullable|exists:expenses_categories,id',
            'status' => 'nullable|in:approved,pending,rejected',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'date_type' => 'nullable|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year,custom',
        ]);

        // 2. تحديد نطاق التاريخ بناءً على نوع التاريخ المحدد
        $fromDate = now()->subMonth();
        $toDate = now();

        if ($request->filled('date_type') && $request->date_type !== 'custom') {
            $dateRange = $this->getDateRange($request->date_type);
            $fromDate = $dateRange['from'];
            $toDate = $dateRange['to'];
        } elseif ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = Carbon::parse($request->from_date);
            $toDate = Carbon::parse($request->to_date);
        } elseif ($request->filled('from_date')) {
            $fromDate = Carbon::parse($request->from_date);
        } elseif ($request->filled('to_date')) {
            $toDate = Carbon::parse($request->to_date);
        }

        // 3. الحصول على account_id للمورد والخزينة المحددين (إن وجدا)
        $supplierAccountId = null;
        $treasuryAccountId = null;

        if ($request->filled('supplier')) {
            $supplier = Supplier::find($request->supplier);
            $supplierAccountId = $supplier ? $supplier->account_id : null;
        }

        if ($request->filled('treasury')) {
            $treasury = Treasury::find($request->treasury);
            $treasuryAccountId = $treasury ? $treasury->account_id : null;
        }

        // 4. إنشاء استعلام المصروفات مع العلاقات
        $expensesQuery = Expense::with([
            'createdBy',
            'treasury.account', // إضافة علاقة الحساب للخزينة
            'branch',
            'expenses_category',
            'account',
            'Supplier.account' // إضافة علاقة الحساب للمورد
        ])
        ->whereBetween('date', [$fromDate, $toDate])
        ->whereNotNull('created_by');

        // 5. تطبيق الفلاتر على المصروفات
        if ($request->filled('employee')) {
            $expensesQuery->where('created_by', $request->employee);
        }

        // فلتر المورد بناءً على account_id
        if ($request->filled('supplier') && $supplierAccountId) {
            $expensesQuery->whereHas('Supplier', function ($query) use ($supplierAccountId) {
                $query->where('account_id', $supplierAccountId);
            });
            // أو بدلاً من ذلك يمكنك البحث مباشرة في جدول المصروفات
            // $expensesQuery->where('account_id', $supplierAccountId);
        }

        // فلتر الخزينة بناءً على account_id
        if ($request->filled('treasury') && $treasuryAccountId) {
            $expensesQuery->whereHas('treasury', function ($query) use ($treasuryAccountId) {
                $query->where('account_id', $treasuryAccountId);
            });
            // أو بدلاً من ذلك يمكنك البحث مباشرة في جدول المصروفات
            // $expensesQuery->where('account_id', $treasuryAccountId);
        }

        if ($request->filled('branch')) {
            $expensesQuery->where('branch_id', $request->branch);
        }

        if ($request->filled('account')) {
            $expensesQuery->where('account_id', $request->account);
        }

        if ($request->filled('expense_category')) {
            $expensesQuery->where('expenses_category_id', $request->expense_category);
        }

        if ($request->filled('status')) {
            $expensesQuery->where('status', $request->status);
        }

        // 6. جلب البيانات مع ترتيبها
        $expenses = $expensesQuery->orderBy('date', 'desc')->get();

        // 7. معالجة البيانات وتجميعها حسب الموظف
        $groupedData = collect();

        if ($expenses->isNotEmpty()) {
            $groupedExpenses = $expenses->groupBy(function ($expense) {
                return $expense->createdBy ? $expense->createdBy->id : 'unknown';
            });

            foreach ($groupedExpenses as $employeeId => $employeeExpenses) {
                if ($employeeId !== 'unknown' && $employeeExpenses->isNotEmpty()) {
                    $employee = $employeeExpenses->first()->createdBy;

                    $groupedData->put($employeeId, [
                        'employee' => $employee,
                        'expenses' => $employeeExpenses,
                        'expenses_count' => $employeeExpenses->count(),
                        'total_amount' => $employeeExpenses->sum('amount'),
                        'total_taxes' => $employeeExpenses->sum('tax1_amount') + $employeeExpenses->sum('tax2_amount'),
                        'total_with_tax' => $employeeExpenses->sum('amount') + $employeeExpenses->sum('tax1_amount') + $employeeExpenses->sum('tax2_amount'),
                    ]);
                }
            }
        }

        // 8. حساب الإجماليات العامة
        $uniqueEmployees = $expenses
            ->filter(function ($expense) {
                return $expense->createdBy;
            })
            ->map(function ($expense) {
                return $expense->createdBy->id;
            })
            ->unique()
            ->count();

        $totalAmount = $expenses->sum('amount');
        $totalTaxes = $expenses->sum('tax1_amount') + $expenses->sum('tax2_amount');
        $totalWithTax = $totalAmount + $totalTaxes;
        $expensesCount = $expenses->count();

        $totals = [
            'total_expenses' => $totalAmount,
            'total_taxes' => $totalTaxes,
            'total_with_tax' => $totalWithTax,
            'total_count' => $expensesCount,
            'average_amount' => $expensesCount > 0 ? $totalAmount / $expensesCount : 0,
            'employees_count' => $uniqueEmployees,
        ];

        // 9. إعداد بيانات الرسم البياني (أفضل 10 موظفين)
        $topEmployees = $groupedData
            ->sortByDesc('total_with_tax')
            ->take(10);

        $chartData = [
            'labels' => $topEmployees
                ->map(function ($data) {
                    return $data['employee']->name ?? 'غير محدد';
                })
                ->values()
                ->toArray(),
            'amounts' => $topEmployees
                ->map(function ($data) {
                    return $data['total_amount'];
                })
                ->values()
                ->toArray(),
            'taxes' => $topEmployees
                ->map(function ($data) {
                    return $data['total_taxes'];
                })
                ->values()
                ->toArray(),
        ];

        // 10. تحويل البيانات للإرسال عبر JSON
        $groupedDataArray = [];
        foreach ($groupedData as $employeeId => $data) {
            $groupedDataArray[$employeeId] = [
                'employee' => [
                    'id' => $data['employee']->id,
                    'name' => $data['employee']->name,
                    'code' => $data['employee']->code ?? '',
                ],
                'expenses' => $data['expenses']
                    ->map(function ($expense) {
                        return [
                            'id' => $expense->id,
                            'code' => $expense->code,
                            'amount' => $expense->amount,
                            'date' => $expense->date,
                            'description' => $expense->description,
                            'status' => $expense->status ?? 'approved',
                            'tax1_amount' => $expense->tax1_amount ?? 0,
                            'tax2_amount' => $expense->tax2_amount ?? 0,
                            'total_taxes' => ($expense->tax1_amount ?? 0) + ($expense->tax2_amount ?? 0),
                            'total_with_tax' => $expense->amount + ($expense->tax1_amount ?? 0) + ($expense->tax2_amount ?? 0),
                            'employee' => $expense->createdBy
                                ? [
                                    'id' => $expense->createdBy->id,
                                    'name' => $expense->createdBy->name,
                                ]
                                : null,
                            'account' => $expense->account
                                ? [
                                    'id' => $expense->account->id,
                                    'name' => $expense->account->name,
                                ]
                                : null,
                            'treasury' => $expense->treasury
                                ? [
                                    'id' => $expense->treasury->id,
                                    'name' => $expense->treasury->name,
                                    'account' => $expense->treasury->account
                                        ? [
                                            'id' => $expense->treasury->account->id,
                'employees_count' => $uniqueEmployees,
                                        ]
                                        : null,
                                ]
                                : null,
                            'supplier' => $expense->Supplier
                                ? [
                                    'id' => $expense->Supplier->id,
                                    'name' => $expense->Supplier->name,
                                    'account' => $expense->Supplier->account
                                        ? [
                                            'id' => $expense->Supplier->account->id,
                                            'name' => $expense->Supplier->account->name,
                                        ]
                                        : null,
                                ]
                                : null,
                            'category' => $expense->expenses_category
                                ? [
                                    'id' => $expense->expenses_category->id,
                                    'name' => $expense->expenses_category->name,
                                ]
                                : null,
                            'branch' => $expense->branch
                                ? [
                                    'id' => $expense->branch->id,
                                    'name' => $expense->branch->name,
                                ]
                                : null,
                        ];
                    })
                    ->toArray(),
                'expenses_count' => $data['expenses_count'],
                'total_amount' => $data['total_amount'],
                'total_taxes' => $data['total_taxes'],
                'total_with_tax' => $data['total_with_tax'],
            ];
        }

        // 11. إرجاع البيانات كـ JSON
        return response()->json([
            'success' => true,
            'grouped_data' => $groupedDataArray,
            'totals' => $totals,
            'chart_data' => $chartData,
            'from_date' => $fromDate->format('d/m/Y'),
            'to_date' => $toDate->format('d/m/Y'),
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في تقرير المصروفات حسب الموظف: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
        ], 500);
    }
}


public function journalEntriesByEmployee(Request $request)
    {
        // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
        $employees = User::where('role', 'employee')->get();
        $branches = Branch::orderBy('name')->get();
        $accounts = Account::orderBy('name')->get();
        $clients = Client::orderBy('trade_name')->get();
        $costCenters = CostCenter::orderBy('name')->get();

        // 2. تحديد التواريخ الافتراضية
        $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : now()->subMonth();
        $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : now();

        // 3. إعداد البيانات الافتراضية الفارغة
        $totals = [
            'total_debits' => 0,
            'total_credits' => 0,
            'total_entries' => 0,
            'total_count' => 0,
            'average_amount' => 0,
            'employees_count' => 0,
        ];

        $groupedData = collect();
        $chartData = ['labels' => [], 'debits' => [], 'credits' => []];

        // 4. إرجاع العرض
        return view('reports::general_accounts.daily_restrictions_reports.report_journal', compact(
            'employees',
            'branches',
            'accounts',
            'clients',
            'costCenters',
            'totals',
            'chartData',
            'fromDate',
            'toDate',
            'groupedData'
        ));
    }

    /**
     * الحصول على بيانات تقرير القيود حسب الموظف عبر AJAX
     */
    public function journalEntriesByEmployeeAjax(Request $request)
    {
        try {
            // 1. التحقق من صحة البيانات المدخلة
            $validatedData = $request->validate([
                'employee' => 'nullable|exists:users,id',
                'client' => 'nullable|exists:clients,id',
                'branch' => 'nullable|exists:branches,id',
                'account' => 'nullable|exists:accounts,id',
                'cost_center' => 'nullable|exists:cost_centers,id',
                'status' => 'nullable|in:0,1,2', // 0=معلق, 1=معتمد, 2=مرفوض
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'date_type' => 'nullable|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year,custom',
            ]);

            // 2. تحديد نطاق التاريخ بناءً على نوع التاريخ المحدد
            $fromDate = now()->subMonth();
            $toDate = now();

            if ($request->filled('date_type') && $request->date_type !== 'custom') {
                $dateRange = $this->getDateRange($request->date_type);
                $fromDate = $dateRange['from'];
                $toDate = $dateRange['to'];
            } elseif ($request->filled('from_date') && $request->filled('to_date')) {
                $fromDate = Carbon::parse($request->from_date);
                $toDate = Carbon::parse($request->to_date);
            } elseif ($request->filled('from_date')) {
                $fromDate = Carbon::parse($request->from_date);
            } elseif ($request->filled('to_date')) {
                $toDate = Carbon::parse($request->to_date);
            }

            // 3. إنشاء استعلام القيود مع العلاقات
            $journalEntriesQuery = JournalEntry::with([
                'createdByEmployee',
                'approvedByEmployee',
                'branch',
                'client',
                'costCenter',
                'details.account',
                'account'
            ])
            ->whereBetween('date', [$fromDate, $toDate])
            ->whereNotNull('created_by_employee');

            // 4. تطبيق الفلاتر على القيود
            if ($request->filled('employee')) {
                $journalEntriesQuery->where('created_by_employee', $request->employee);
            }

            if ($request->filled('client')) {
                $journalEntriesQuery->where('client_id', $request->client);
            }

            if ($request->filled('branch')) {
                $journalEntriesQuery->where('branch_id', $request->branch);
            }

            if ($request->filled('cost_center')) {
                $journalEntriesQuery->where('cost_center_id', $request->cost_center);
            }

            if ($request->filled('status')) {
                $journalEntriesQuery->where('status', $request->status);
            }

            // فلتر الحساب من خلال تفاصيل القيد
            if ($request->filled('account')) {
                $journalEntriesQuery->whereHas('details', function ($query) use ($request) {
                    $query->where('account_id', $request->account);
                });
            }

            // 5. جلب البيانات مع ترتيبها
            $journalEntries = $journalEntriesQuery->orderBy('date', 'desc')->get();

            // 6. معالجة البيانات وتجميعها حسب الموظف
            $groupedData = collect();

            if ($journalEntries->isNotEmpty()) {
                $groupedEntries = $journalEntries->groupBy(function ($entry) {
                    return $entry->createdByEmployee ? $entry->createdByEmployee->id : 'unknown';
                });

                foreach ($groupedEntries as $employeeId => $employeeEntries) {
                    if ($employeeId !== 'unknown' && $employeeEntries->isNotEmpty()) {
                        $employee = $employeeEntries->first()->createdByEmployee;

                        // حساب إجماليات المدين والدائن
                        $totalDebits = 0;
                        $totalCredits = 0;

                        foreach ($employeeEntries as $entry) {
                            $totalDebits += $entry->details->sum('debit');
                            $totalCredits += $entry->details->sum('credit');
                        }

                        $groupedData->put($employeeId, [
                            'employee' => $employee,
                            'entries' => $employeeEntries,
                            'entries_count' => $employeeEntries->count(),
                            'total_debits' => $totalDebits,
                            'total_credits' => $totalCredits,
                            'balance' => $totalDebits - $totalCredits,
                        ]);
                    }
                }
            }

            // 7. حساب الإجماليات العامة
            $uniqueEmployees = $journalEntries
                ->filter(function ($entry) {
                    return $entry->createdByEmployee;
                })
                ->map(function ($entry) {
                    return $entry->createdByEmployee->id;
                })
                ->unique()
                ->count();

            $totalDebits = 0;
            $totalCredits = 0;
            $entriesCount = $journalEntries->count();

            foreach ($journalEntries as $entry) {
                $totalDebits += $entry->details->sum('debit');
                $totalCredits += $entry->details->sum('credit');
            }

            $totals = [
                'total_debits' => $totalDebits,
                'total_credits' => $totalCredits,
                'total_entries' => $totalDebits, // أو يمكن استخدام المدين كإجمالي
                'total_count' => $entriesCount,
                'average_amount' => $entriesCount > 0 ? $totalDebits / $entriesCount : 0,
                'employees_count' => $uniqueEmployees,
            ];

            // 8. إعداد بيانات الرسم البياني (أفضل 10 موظفين)
            $topEmployees = $groupedData
                ->sortByDesc('total_debits')
                ->take(10);

            $chartData = [
                'labels' => $topEmployees
                    ->map(function ($data) {
                        return $data['employee']->name ?? 'غير محدد';
                    })
                    ->values()
                    ->toArray(),
                'debits' => $topEmployees
                    ->map(function ($data) {
                        return $data['total_debits'];
                    })
                    ->values()
                    ->toArray(),
                'credits' => $topEmployees
                    ->map(function ($data) {
                        return $data['total_credits'];
                    })
                    ->values()
                    ->toArray(),
            ];

            // 9. تحويل البيانات للإرسال عبر JSON
            $groupedDataArray = [];
            foreach ($groupedData as $employeeId => $data) {
                $groupedDataArray[$employeeId] = [
                    'employee' => [
                        'id' => $data['employee']->id,
                        'name' => $data['employee']->name,
                        'code' => $data['employee']->code ?? '',
                    ],
                    'entries' => $data['entries']
                        ->map(function ($entry) {
                            return [
                                'id' => $entry->id,
                                'reference_number' => $entry->reference_number,
                                'date' => $entry->date,
                                'description' => $entry->description,
                                'status' => $entry->status,
                                'status_text' => $entry->getStatusTextAttribute(),
                                'total_debits' => $entry->details->sum('debit'),
                                'total_credits' => $entry->details->sum('credit'),
                                'is_balanced' => $entry->isBalanced(),
                                'employee' => $entry->createdByEmployee
                                    ? [
                                        'id' => $entry->createdByEmployee->id,
                                        'name' => $entry->createdByEmployee->name,
                                    ]
                                    : null,
                                'client' => $entry->client
                                    ? [
                                        'id' => $entry->client->id,
                                        'name' => $entry->client->trade_name,
                                    ]
                                    : null,
                                'branch' => $entry->branch
                                    ? [
                                        'id' => $entry->branch->id,
                                        'name' => $entry->branch->name,
                                    ]
                                    : null,
                                'cost_center' => $entry->costCenter
                                    ? [
                                        'id' => $entry->costCenter->id,
                                        'name' => $entry->costCenter->name,
                                    ]
                                    : null,
                                'details' => $entry->details->map(function ($detail) {
                                    return [
                                        'id' => $detail->id,
                                        'account_name' => $detail->account ? $detail->account->name : 'غير محدد',
                                        'description' => $detail->description,
                                        'debit' => $detail->debit,
                                        'credit' => $detail->credit,
                                        'reference' => $detail->reference,
                                    ];
                                })->toArray(),
                            ];
                        })
                        ->toArray(),
                    'entries_count' => $data['entries_count'],
                    'total_debits' => $data['total_debits'],
                    'total_credits' => $data['total_credits'],
                    'balance' => $data['balance'],
                ];
            }

            // 10. إرجاع البيانات كـ JSON
            return response()->json([
                'success' => true,
                'grouped_data' => $groupedDataArray,
                'totals' => $totals,
                'chart_data' => $chartData,
                'from_date' => $fromDate->format('d/m/Y'),
                'to_date' => $toDate->format('d/m/Y'),
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في تقرير القيود حسب الموظف: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
            ], 500);
        }
    }

/**
 * الحصول على إحصائيات المصروفات
 */
public function getExpensesStats(Request $request)
{
    try {
        $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : now()->subMonth();
        $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : now();

        $expenses = Expense::whereBetween('date', [$fromDate, $toDate])
            ->whereNotNull('created_by');

        $stats = [
            'total_expenses' => $expenses->sum('amount'),
            'total_count' => $expenses->count(),
            'today_expenses' => Expense::whereDate('date', now())->sum('amount'),
            'this_month_expenses' => Expense::whereMonth('date', now()->month)
                ->whereYear('date', now()->year)->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في جلب الإحصائيات: ' . $e->getMessage(),
        ], 500);
    }
}
    public function splitExpensesByTimePeriod(Request $request)
    {
        // استخراج الفترة الزمنية من الرابط
        $period = $request->route('period');

        // Fetch necessary dropdown data
        $branches = Branch::all();
        $treasuries = Treasury::all();
        $employees = Employee::all();
        $expensesCategories = ExpensesCategory::all();

        // Base query for expenses
        $query = Expense::query();

        // Apply filters
        if ($request->filled('treasury')) {
            $query->where('treasury_id', $request->treasury);
        }

        if ($request->filled('employee')) {
            $query->where('employee_id', $request->employee);
        }

        // Date range filtering with flexible parsing
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = Carbon::parse($request->date_from);
            $dateTo = Carbon::parse($request->date_to);
            $query->whereBetween('date', [$dateFrom, $dateTo]);
        }

        if ($request->filled('branch') && $request->branch != 'all') {
            $query->where('branch_id', $request->branch);
        }

        if ($request->filled('currency') && $request->currency != 'all') {
            $query->where('currency', $request->currency);
        }

        // Determine grouping method based on route parameter or request input
        $reportType = $period ?? $request->input('report_type', 'monthly');

        // Fetch expenses with all related data
        $expenses = $query->with(['treasury', 'expenses_category', 'branch', 'employee'])->get();

        // Group expenses by time periods
        $groupedExpenses = $expenses->groupBy(function ($expense) use ($reportType) {
            $date = Carbon::parse($expense->date);

            switch ($reportType) {
                case 'daily':
                    return $date->format('Y-m-d');
                case 'weekly':
                    return $date->year . ' Week ' . $date->weekOfYear;
                case 'monthly':
                    return $date->format('Y-m');
                case 'quarterly':
                    return $date->year . ' Q' . ceil($date->month / 3);
                case 'yearly':
                    return $date->year;
                default:
                    return $date->format('Y-m');
            }
        });

        // Prepare period display names
        $periodDisplayNames = [
            'daily' => 'المصروفات اليومية',
            'weekly' => 'المصروفات الأسبوعية',
            'monthly' => 'المصروفات الشهرية',
            'quarterly' => 'المصروفات الربع سنوية',
            'yearly' => 'المصروفات السنوية',
        ];
        $periodDisplayName = $periodDisplayNames[$reportType] ?? $reportType;

        // Prepare chart data
        $chartLabels = array_keys($groupedExpenses->toArray());
        $chartData = $groupedExpenses
            ->map(function ($group) {
                return $group->sum('amount') + $group->sum('tax1_amount') + $group->sum('tax2_amount');
            })
            ->toArray();

        return view('reports::general_accounts.split_expenses.expenses_time_period', [
            'branches' => $branches,
            'treasuries' => $treasuries,
            'employees' => $employees,
            'expensesCategories' => $expensesCategories,
            'expenses' => $expenses,
            'groupedExpenses' => $groupedExpenses,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'reportType' => $reportType,
            'period' => $periodDisplayName,
        ]);
    }
    public function ReceiptByCategory(Request $request)
    {
        try {
            // Default to current month if no date range specified
            $fromDate = $request->input('from_date', Carbon::now()->startOfMonth());
            $toDate = $request->input('to_date', Carbon::now()->endOfMonth());

            // Fetch necessary dropdown data
            $branches = Branch::all();
            $treasuries = Treasury::all();
            $accounts = Account::all();
            $employees = Employee::all();
            $receiptsCategory = ReceiptCategory::all();
            $clients = Client::all();

            // Base query for receipts
            $query = Receipt::with(['incomes_category', 'treasury', 'account', 'client'])->whereBetween('date', [$fromDate, $toDate]);

            // Apply filters
            if ($request->filled('branch')) {
                $query->where('branch_id', $request->branch);
            }

            if ($request->filled('treasury')) {
                $query->where('treasury_id', $request->treasury);
            }

            if ($request->filled('employee')) {
                $query->where('employee_id', $request->employee);
            }

            if ($request->filled('client')) {
                $query->where('client_id', $request->client);
            }

            if ($request->filled('incomes_category')) {
                $query->where('incomes_category_id', $request->incomes_category);
            }

            // Fetch receipts
            $receipts = $query->get();

            // Group receipts by category
            $groupedReceipts = $receipts->groupBy('incomes_category_id');

            // Prepare chart data
            $chartData = [
                'labels' => $groupedReceipts
                    ->keys()
                    ->map(function ($categoryId) use ($receiptsCategory) {
                        return $receiptsCategory->find($categoryId)->name ?? 'غير مصنف';
                    })
                    ->toArray(),
                'values' => $groupedReceipts
                    ->map(function ($categoryReceipts) {
                        return $categoryReceipts->sum('amount');
                    })
                    ->values()
                    ->toArray(),
            ];

            return view('reports::general_accounts.receipt_bonds.receipt_by_category', [
                'branches' => $branches,
                'treasuries' => $treasuries,
                'accounts' => $accounts,
                'employees' => $employees,
                'receiptsCategory' => $receiptsCategory,
                'clients' => $clients,
                'receipts' => $receipts,
                'groupedReceipts' => $groupedReceipts,
                'chartData' => $chartData,
                'fromDate' => Carbon::parse($fromDate),
                'toDate' => Carbon::parse($toDate),
            ]);
        } catch (\Exception $e) {
            // Log the full error
            Log::error('Error in receipts by category report', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Optionally, return an error view or redirect with a message
            return back()->with('error', 'حدث خطأ أثناء إنشاء التقرير: ' . $e->getMessage());
        }
    }
    public function ReceiptBySeller(Request $request)
    {
        try {
            // Default to current month if no date range specified
            $fromDate = $request->input('from_date', Carbon::now()->startOfMonth());
            $toDate = $request->input('to_date', Carbon::now()->endOfMonth());

            // Fetch necessary dropdown data
            $branches = Branch::all();
            $treasuries = Treasury::all();
            $accounts = Account::all();
            $employees = Employee::all();
            $receiptsCategory = ReceiptCategory::all();
            $clients = Client::all();

            // Base query for receipts
            $query = Receipt::with(['incomes_category', 'treasury', 'account', 'client'])->whereBetween('date', [$fromDate, $toDate]);

            // Apply filters
            if ($request->filled('branch')) {
                $query->where('branch_id', $request->branch);
            }

            if ($request->filled('treasury')) {
                $query->where('treasury_id', $request->treasury);
            }

            if ($request->filled('employee')) {
                $query->where('employee_id', $request->employee);
            }

            if ($request->filled('client')) {
                $query->where('client_id', $request->client);
            }

            if ($request->filled('seller')) {
                $query->where('seller', $request->seller);
            }

            // Fetch receipts
            $receipts = $query->get();

            // Group receipts by seller
            $groupedReceipts = $receipts->groupBy('seller');

            // Prepare chart data
            $chartData = [
                'labels' => $groupedReceipts->keys()->toArray(),
                'values' => $groupedReceipts
                    ->map(function ($sellerReceipts) {
                        return $sellerReceipts->sum('amount');
                    })
                    ->values()
                    ->toArray(),
            ];

            return view('reports::general_accounts.receipt_bonds.receipt_by_seller', [
                'branches' => $branches,
                'treasuries' => $treasuries,
                'accounts' => $accounts,
                'employees' => $employees,
                'receiptsCategory' => $receiptsCategory,
                'clients' => $clients,
                'receipts' => $receipts,
                'groupedReceipts' => $groupedReceipts,
                'chartData' => $chartData,
                'fromDate' => Carbon::parse($fromDate),
                'toDate' => Carbon::parse($toDate),
            ]);
        } catch (\Exception $e) {
            // Log the full error
            Log::error('Error in receipts by seller report', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Optionally, return an error view or redirect with a message
            return back()->with('error', 'حدث خطأ أثناء إنشاء التقرير: ' . $e->getMessage());
        }
    }
 public function receiptsReport(Request $request)
    {
        // 1. الحصول على البيانات الأساسية للقوائم المنسدلة
        $clients = Client::orderBy('trade_name')->get();
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        $accounts = Account::orderBy('name')->get();
        $treasuries = Treasury::orderBy('name')->get();

        // 2. تحديد التواريخ الافتراضية
        $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : now()->subMonth();
        $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : now();

        // 3. إعداد البيانات الافتراضية الفارغة
        $totals = [
            'total_receipts' => 0,
            'total_count' => 0,
            'average_amount' => 0,
            'clients_count' => 0,
        ];

        $groupedData = collect();
        $chartData = ['labels' => [], 'amounts' => []];

        // 4. إرجاع العرض
        return view('reports::general_accounts.receipt_bonds.receipt_by_employee', compact(
            'clients',
            'employees',
            'branches',
            'accounts',
            'treasuries',
            'totals',
            'chartData',
            'fromDate',
            'toDate',
            'groupedData'
        ));
    }

    /**
     * الحصول على بيانات تقرير سندات القبض عبر AJAX
     */
    public function receiptsReportAjax(Request $request)
    {
        try {
            // 1. التحقق من صحة البيانات المدخلة
            $validatedData = $request->validate([
                'client' => 'nullable|exists:clients,id',
                'employee' => 'nullable|exists:users,id',
                'branch' => 'nullable|exists:branches,id',
                'account' => 'nullable|exists:accounts,id',
                'treasury' => 'nullable|exists:treasuries,id',
                'receipt_type' => 'nullable|in:client_payment,refund,other',
                'status' => 'nullable|in:confirmed,pending,cancelled',
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'date_type' => 'nullable|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year,custom',
            ]);

            // 2. تحديد نطاق التاريخ بناءً على نوع التاريخ المحدد
            $fromDate = now()->subMonth();
            $toDate = now();

            if ($request->filled('date_type') && $request->date_type !== 'custom') {
                $dateRange = $this->getDateRange($request->date_type);
                $fromDate = $dateRange['from'];
                $toDate = $dateRange['to'];
            } elseif ($request->filled('from_date') && $request->filled('to_date')) {
                $fromDate = Carbon::parse($request->from_date);
                $toDate = Carbon::parse($request->to_date);
            } elseif ($request->filled('from_date')) {
                $fromDate = Carbon::parse($request->from_date);
            } elseif ($request->filled('to_date')) {
                $toDate = Carbon::parse($request->to_date);
            }

            // 3. إنشاء استعلام سندات القبض مع العلاقات
            $receiptsQuery = Receipt::with([
                'client',
                'account.client',
                'user',
                'treasury',
                'account'
            ])
            ->whereBetween('date', [$fromDate, $toDate])
            ->whereHas('account', function ($q) {
                $q->whereNotNull('client_id');
            });

            // 4. تطبيق الفلاتر على سندات القبض
            if ($request->filled('client')) {
                $receiptsQuery->whereHas('account', function ($q) use ($request) {
                    $q->where('client_id', $request->client);
                });
            }

            if ($request->filled('employee')) {
                $receiptsQuery->where('created_by', $request->employee);
            }

            if ($request->filled('branch')) {
                $receiptsQuery->whereHas('account.client', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch);
                });
            }

            if ($request->filled('account')) {
                $receiptsQuery->where('account_id', $request->account);
            }

            if ($request->filled('treasury')) {
                $receiptsQuery->where('treasury_id', $request->treasury);
            }

            if ($request->filled('receipt_type')) {
                $receiptsQuery->where('type', $request->receipt_type);
            }

            if ($request->filled('status')) {
                $receiptsQuery->where('status', $request->status);
            }

            // 5. جلب البيانات مع ترتيبها
            $receipts = $receiptsQuery->orderBy('date', 'desc')->get();

            // 6. معالجة البيانات وتجميعها حسب العميل
            $groupedData = collect();

            if ($receipts->isNotEmpty()) {
                $groupedReceipts = $receipts->groupBy(function ($receipt) {
                    return $receipt->account && $receipt->account->client ? $receipt->account->client->id : 'unknown';
                });

                foreach ($groupedReceipts as $clientId => $clientReceipts) {
                    if ($clientId !== 'unknown' && $clientReceipts->isNotEmpty()) {
                        $client = $clientReceipts->first()->account->client;

                        $groupedData->put($clientId, [
                            'client' => $client,
                            'receipts' => $clientReceipts,
                            'receipts_count' => $clientReceipts->count(),
                            'total_amount' => $clientReceipts->sum('amount'),
                        ]);
                    }
                }
            }

            // 7. حساب الإجماليات العامة
            $uniqueClients = $receipts
                ->filter(function ($receipt) {
                    return $receipt->account && $receipt->account->client;
                })
                ->map(function ($receipt) {
                    return $receipt->account->client->id;
                })
                ->unique()
                ->count();

            $totalAmount = $receipts->sum('amount');
            $receiptsCount = $receipts->count();

            $totals = [
                'total_receipts' => $totalAmount,
                'total_count' => $receiptsCount,
                'average_amount' => $receiptsCount > 0 ? $totalAmount / $receiptsCount : 0,
                'clients_count' => $uniqueClients,
            ];

            // 8. إعداد بيانات الرسم البياني (أفضل 10 عملاء)
            $topClients = $groupedData
                ->sortByDesc('total_amount')
                ->take(10);

            $chartData = [
                'labels' => $topClients
                    ->map(function ($data) {
                        return $data['client']->trade_name ?? 'غير محدد';
                    })
                    ->values()
                    ->toArray(),
                'amounts' => $topClients
                    ->map(function ($data) {
                        return $data['total_amount'];
                    })
                    ->values()
                    ->toArray(),
            ];

            // 9. تحويل البيانات للإرسال عبر JSON
            $groupedDataArray = [];
            foreach ($groupedData as $clientId => $data) {
                $groupedDataArray[$clientId] = [
                    'client' => [
                        'id' => $data['client']->id,
                        'trade_name' => $data['client']->trade_name,
                        'code' => $data['client']->code,
                    ],
                    'receipts' => $data['receipts']
                        ->map(function ($receipt) {
                            return [
                                'id' => $receipt->id,
                                'code' => $receipt->code,
                                'amount' => $receipt->amount,
                                'date' => $receipt->date,
                                'description' => $receipt->description,
                                'status' => $receipt->status ?? 'confirmed',
                                'type' => $receipt->type ?? 'client_payment',
                                'employee' => $receipt->user
                                    ? [
                                        'id' => $receipt->user->id,
                                        'name' => $receipt->user->name,
                                    ]
                                    : null,
                                'account' => $receipt->account
                                    ? [
                                        'id' => $receipt->account->id,
                                        'name' => $receipt->account->name,
                                        'client' => $receipt->account->client
                                            ? [
                                                'id' => $receipt->account->client->id,
                                                'trade_name' => $receipt->account->client->trade_name,
                                                'code' => $receipt->account->client->code,
                                            ]
                                            : null,
                                    ]
                                    : null,
                                'treasury' => $receipt->treasury
                                    ? [
                                        'id' => $receipt->treasury->id,
                                        'name' => $receipt->treasury->name,
                                    ]
                                    : null,
                            ];
                        })
                        ->toArray(),
                    'receipts_count' => $data['receipts_count'],
                    'total_amount' => $data['total_amount'],
                ];
            }

            // 10. إرجاع البيانات كـ JSON
            return response()->json([
                'success' => true,
                'grouped_data' => $groupedDataArray,
                'totals' => $totals,
                'chart_data' => $chartData,
                'from_date' => $fromDate->format('d/m/Y'),
                'to_date' => $toDate->format('d/m/Y'),
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في تقرير سندات القبض: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getReceiptsStats(Request $request)
    {
        try {
            $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date')) : now()->subMonth();
            $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date')) : now();

            $receipts = Receipt::whereBetween('date', [$fromDate, $toDate])
                ->whereHas('account', function ($q) {
                    $q->whereNotNull('client_id');
                });

            $stats = [
                'total_receipts' => $receipts->sum('amount'),
                'total_count' => $receipts->count(),
                'today_receipts' => Receipt::whereDate('date', now())->sum('amount'),
                'this_month_receipts' => Receipt::whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)->sum('amount'),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الإحصائيات: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ReceiptByClient(Request $request)
{
    $fromDate = $request->input('from_date', Carbon::now()->startOfMonth());
    $toDate = $request->input('to_date', Carbon::now()->endOfMonth());

    $branches = Branch::all();
    $treasuries = Treasury::all();
    $accounts = Account::all(); // فيه أسماء العملاء
    $employees = Employee::all();
    $receiptsCategory = ReceiptCategory::all();

    $query = Receipt::with(['incomes_category', 'treasury', 'account', 'employee', 'branch'])
                    ->whereBetween('date', [$fromDate, $toDate]);

    // الفلاتر
    // if ($request->filled('branch')) {
    //     $query->where('branch_id', $request->branch);
    // }

    if ($request->filled('treasury')) {
        $query->where('treasury_id', $request->treasury);
    }

    if ($request->filled('employee')) {
        $query->where('employee_id', $request->employee);
    }

    if ($request->filled('account')) {
        $query->where('account_id', $request->account);
    }

    $receipts = $query->get();

    // تجميع حسب الحساب (العميل)
    $groupedReceipts = $receipts->groupBy('account_id');

    // الإجماليات
    $totalAmount = $receipts->sum('amount');
    $totalTax = $receipts->sum('tax1_amount') + $receipts->sum('tax2_amount');
    $totalWithTax = $totalAmount + $totalTax;
    $chartData = [
        'labels' => $groupedReceipts->keys()->toArray(),
        'values' => $groupedReceipts
            ->map(function ($userReceipts) {
                return $userReceipts->sum('amount');
            })
            ->values()
            ->toArray(),
    ];

    return view('reports::general_accounts.receipt_bonds.receipt_by_client', [
        'branches' => $branches,
        'treasuries' => $treasuries,
        'accounts' => $accounts,
        'employees' => $employees,
        'receiptsCategory' => $receiptsCategory,
        'receipts' => $receipts,
        'groupedReceipts' => $groupedReceipts,
        'totalAmount' => $totalAmount,
        'chartData' => $chartData,
        'totalTax' => $totalTax,
        'totalWithTax' => $totalWithTax,
        'fromDate' => Carbon::parse($fromDate),
        'toDate' => Carbon::parse($toDate),
    ]);
}

    public function ReceiptByTimePeriod(Request $request)
    {
        // Default to current month if no date range specified
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth());
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth());

        // Fetch necessary dropdown data
        $branches = Branch::all();
        $treasuries = Treasury::all();
        $accounts = Account::all();
        $employees = Employee::all();
        $receiptsCategory = ReceiptCategory::all();
        $clients = Client::all();

        // Base query for receipts
        $query = Receipt::with(['incomes_category', 'treasury', 'account', 'client', 'employee'])->whereBetween('date', [$fromDate, $toDate]);

        // Apply filters
        if ($request->filled('branch')) {
            $query->where('branch_id', $request->branch);
        }

        if ($request->filled('treasury')) {
            $query->where('treasury_id', $request->treasury);
        }

        if ($request->filled('employee')) {
            $query->where('employee_id', $request->employee);
        }

        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }

        if ($request->filled('incomes_category')) {
            $query->where('incomes_category_id', $request->incomes_category);
        }

        $reportPeriod = $request->input('report_period', 'monthly');
        // Fetch receipts
        $receipts = $query->get();

        // Group receipts by client
        $groupedReceipts = $receipts->groupBy('client_id');

        $groupedReceipts = $receipts->groupBy(function ($receipt) use ($reportPeriod) {
            $date = Carbon::parse($receipt->date);

            switch ($reportPeriod) {
                case 'daily':
                    return $date->format('Y-m-d');
                case 'weekly':
                    return $date->year . ' Week ' . $date->weekOfYear;
                case 'monthly':
                    return $date->format('Y-m');
                case 'quarterly':
                    return $date->year . ' Q' . ceil($date->month / 3);
                case 'yearly':
                    return $date->year;
                default:
                    return $date->format('Y-m-d');
            }
        });
        // Prepare chart data
        $chartData = [
            'labels' => $groupedReceipts
                ->keys()
                ->map(function ($clientId) use ($clients) {
                    return $clients->find($clientId)->name ?? 'غير معروف';
                })
                ->toArray(),
            'values' => $groupedReceipts
                ->map(function ($clientReceipts) {
                    return $clientReceipts->sum('amount');
                })
                ->values()
                ->toArray(),
        ];

        return view('reports::general_accounts.receipt_bonds.receipt_time_period', [
            'branches' => $branches,
            'treasuries' => $treasuries,
            'accounts' => $accounts,
            'employees' => $employees,
            'receiptsCategory' => $receiptsCategory,
            'clients' => $clients,
            'receipts' => $receipts,
            'groupedReceipts' => $groupedReceipts,
            'chartData' => $chartData,
            'fromDate' => Carbon::parse($fromDate),
            'toDate' => Carbon::parse($toDate),
            'reportPeriod' => $reportPeriod,
        ]);
    }


    protected function calculateAccountBalanceDetails($account, $isDebitAccount, $openingBalance, $periodDebit, $periodCredit)
{
    // تحديد نوع الحساب بشكل أكثر دقة
    $accountType = $account->type;

    // حساب الرصيد الافتتاحي بشكل مفصل
    $openingBalanceDebit = 0;
    $openingBalanceCredit = 0;

    // تحديد اتجاه الحساب بناءً على نوع الحساب
    $isDebitNormalBalance = in_array($accountType, [
        'asset', 'expense', 'contra_liability', 'contra_equity'
    ]);

    // معالجة الرصيد الافتتاحي
    if ($isDebitNormalBalance) {
        // للحسابات المدينة الطبيعية (الأصول، المصروفات)
        if ($openingBalance > 0) {
            $openingBalanceDebit = abs($openingBalance);
        } else {
            $openingBalanceCredit = abs($openingBalance);
        }
    } else {
        // للحسابات الدائنة الطبيعية (الالتزامات، الإيرادات، حقوق الملكية)
        if ($openingBalance > 0) {
            $openingBalanceCredit = abs($openingBalance);
        } else {
            $openingBalanceDebit = abs($openingBalance);
        }
    }

    // حساب الرصيد الختامي
    $closingBalance = $openingBalance + $periodDebit - $periodCredit;
    $closingBalanceDebit = 0;
    $closingBalanceCredit = 0;

    if ($isDebitNormalBalance) {
        // للحسابات المدينة الطبيعية
        if ($closingBalance > 0) {
            $closingBalanceDebit = abs($closingBalance);
        } else {
            $closingBalanceCredit = abs($closingBalance);
        }
    } else {
        // للحسابات الدائنة الطبيعية
        if ($closingBalance > 0) {
            $closingBalanceCredit = abs($closingBalance);
        } else {
            $closingBalanceDebit = abs($closingBalance);
        }
    }

    // حساب الإجماليات
    $totalDebit = $openingBalanceDebit + $periodDebit;
    $totalCredit = $openingBalanceCredit + $periodCredit;

    return [
        'id' => $account->id,
        'name' => $account->name,
        'code' => $account->code,
        'parent_id' => $account->parent_id,
        'account_type' => $accountType,
        'account_category' => $account->category,
        'opening_balance_debit' => $openingBalanceDebit,
        'opening_balance_credit' => $openingBalanceCredit,
        'period_debit' => $periodDebit,
        'period_credit' => $periodCredit,
        'closing_balance_debit' => $closingBalanceDebit,
        'closing_balance_credit' => $closingBalanceCredit,
        'total_debit' => $totalDebit,
        'total_credit' => $totalCredit
    ];
}
public function generalLedger(Request $request)
{
    // استرجاع الحسابات والفروع ومراكز التكلفة والمستخدمين
    $accounts = Account::all();
    $branches = Branch::all();
    $costCenters = CostCenter::all();
    $users = User::all();

    // استرجاع القيود المحاسبية بناءً على الفلتر
    $journalEntries = JournalEntry::query();

    if ($request->has('dateRange')) {
        $journalEntries->whereBetween('date', $this->getDateRange($request->dateRange));
    }

    if ($request->has('account')) {
        $journalEntries->whereHas('details', function ($query) use ($request) {
            $query->where('account_id', $request->account);
        });
    }

    if ($request->has('branch')) {
        $journalEntries->where('branch_id', $request->branch);
    }

    if ($request->has('cost_center')) {
        $journalEntries->where('cost_center_id', $request->cost_center);
    }

    // استرجاع القيود مع التفاصيل والحسابات المرتبطة
    $journalEntries = $journalEntries->with(['details.account', 'employee'])->get();

    // حساب الإجماليات
    $totalDebit = 0;
    $totalCredit = 0;
    $totalBalanceDebit = 0;
    $totalBalanceCredit = 0;

    foreach ($journalEntries as $entry) {
        foreach ($entry->details as $detail) {
            $totalDebit += $detail->debit;
            $totalCredit += $detail->credit;
            $totalBalanceDebit += $detail->account->balance;
            $totalBalanceCredit += $detail->account->balance;
        }
    }

    // استرجاع حساب "المبيعات" من جدول الحسابات
    $salesAccount = Account::where('name', 'المبيعات')->first();

    return view('reports::general_accounts.accountGeneral.general_ledger_account', compact(
        'accounts', 'users', 'branches', 'costCenters', 'journalEntries',
        'totalDebit', 'totalCredit', 'totalBalanceDebit', 'totalBalanceCredit',
        'salesAccount'
    ));
}


private function getDateRange($dateRange)
{
    // دالة لتحويل الفترة المحددة إلى نطاق تاريخي
    switch ($dateRange) {
        case 'الأسبوع الماضي':
            return [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()];
        case 'الشهر الأخير':
            return [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()];
        case 'من أول الشهر حتى اليوم':
            return [now()->startOfMonth(), now()];
        case 'السنة الماضية':
            return [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()];
        case 'من أول السنة حتى اليوم':
            return [now()->startOfYear(), now()];
        default:
            return [now()->subMonth(), now()];
    }
}


public function ReportJournal(Request $request)
{
    // فلترة البيانات بناءً على المدخلات
    $query = JournalEntry::query();

    // فلترة حسب المصدر (نوع القيد)
    if ($request->filled('treasury')) {
        $query->where('entity_type', $request->treasury);
    }

    // فلترة حسب الحساب الفرعي
    if ($request->filled('account')) {
        $query->whereHas('details', function($q) use ($request) {
            $q->where('account_id', $request->account);
        });
    }

    // فلترة حسب الفترة من
    if ($request->filled('from_date')) {
        $query->whereDate('date', '>=', $request->from_date);
    }

    // فلترة حسب الفترة إلى
    if ($request->filled('to_date')) {
        $query->whereDate('date', '<=', $request->to_date);
    }

    // فلترة حسب أمر التوريد
    if ($request->filled('supply')) {
        $query->where('supply_order_id', $request->supply);
    }

    // فلترة حسب الفرع
    if ($request->filled('branch')) {
        $query->where('branch_id', $request->branch);
    }

    // جلب البيانات مع التفاصيل والحسابات المرتبطة
    $journalEntries = $query->with(['details.account', 'branch'])->get();

    // حساب الإجمالي لكل قيد
    $journalEntries->each(function ($entry) {
        $entry->total_debit = $entry->details->sum('debit');
        $entry->total_credit = $entry->details->sum('credit');
    });

    // تحضير البيانات للعرض
    $accounts = Account::all();
    $branches = Branch::all();
    $supplyOrders = SupplyOrder::all();

    // إرجاع العرض مع البيانات
    return view('reports::general_accounts.daily_restrictions_reports.report_journal',
        compact('journalEntries', 'accounts', 'branches', 'supplyOrders')
    );
}
 public function ChartOfAccounts(Request $request)
    {
        // إذا كان الطلب AJAX، نعيد البيانات فقط
        if ($request->ajax()) {
            try {
                // فلترة البيانات بناءً على المدخلات
                $query = Account::with(['branch', 'parent', 'costCenter']);

                // فلترة حسب مستوى الحساب
                if ($request->filled('account_level')) {
                    if ($request->account_level == 'main') {
                        $query->whereNull('parent_id'); // حسابات رئيسية
                    } elseif ($request->account_level == 'sub') {
                        $query->whereNotNull('parent_id'); // حسابات فرعية
                    }
                }

                // فلترة حسب نوع الحساب (مدين/دائن)
                if ($request->filled('account_type')) {
                    $query->where('balance_type', $request->account_type);
                }

                // فلترة حسب نوع الحساب (عملاء/موردين)
                if ($request->filled('account_category')) {
                    $query->where('category', $request->account_category);
                }

                // فلترة حسب الفرع
                if ($request->filled('branch') && $request->branch !== 'all') {
                    $query->where('branch_id', $request->branch);
                }

                // البحث في الكود والاسم
                if ($request->filled('search')) {
                    $searchTerm = $request->search;
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('code', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('name', 'LIKE', "%{$searchTerm}%");
                    });
                }

                // ترتيب حسب الكود أو الاسم
                if ($request->filled('order_by')) {
                    switch ($request->order_by) {
                        case 'desc':
                            $query->orderBy('code', 'desc');
                            break;
                        case 'name_asc':
                            $query->orderBy('name', 'asc');
                            break;
                        case 'name_desc':
                            $query->orderBy('name', 'desc');
                            break;
                        default:
                            $query->orderBy('code', 'asc');
                            break;
                    }
                } else {
                    $query->orderBy('code', 'asc');
                }

                // جلب البيانات
                $accounts = $query->get();

                // حساب الإحصائيات
                $statistics = [
                    'total_accounts' => $accounts->count(),
                    'main_accounts' => $accounts->where('parent_id', null)->count(),
                    'sub_accounts' => $accounts->where('parent_id', '!=', null)->count(),
                    'debit_accounts' => $accounts->where('balance_type', 'debit')->count(),
                    'credit_accounts' => $accounts->where('balance_type', 'credit')->count()
                ];

                // إضافة معلومات إضافية للحسابات
                $accountsData = $accounts->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'code' => $account->code,
                        'name' => $account->name,
                        'balance_type' => $account->balance_type,
                        'parent_id' => $account->parent_id,
                        'branch' => $account->branch ? [
                            'id' => $account->branch->id,
                            'name' => $account->branch->name
                        ] : null,
                        'cost_center' => $account->costCenter ? [
                            'id' => $account->costCenter->id,
                            'name' => $account->costCenter->name
                        ] : null,
                        'parent' => $account->parent ? [
                            'id' => $account->parent->id,
                            'name' => $account->parent->name,
                            'code' => $account->parent->code
                        ] : null
                    ];
                });

                return response()->json([
                    'success' => true,
                    'accounts' => $accountsData,
                    'statistics' => $statistics,
                    'total_filtered' => $accounts->count(),
                    'filters_applied' => [
                        'account_level' => $request->account_level,
                        'account_type' => $request->account_type,
                        'account_category' => $request->account_category,
                        'branch' => $request->branch,
                        'search' => $request->search,
                        'order_by' => $request->order_by ?? 'asc'
                    ]
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء جلب البيانات: ' . $e->getMessage(),
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        // للعرض العادي، نعيد الـ view مع البيانات الأساسية
        try {
            $branches = Branch::orderBy('name')->get();

            // جلب عينة من الحسابات للعرض الأولي
            $accounts = Account::with(['branch', 'parent', 'costCenter'])
                ->orderBy('code')
                ->limit(50)
                ->get();

            return view('reports::general_accounts.daily_restrictions_reports.chart_of_account_report',
                compact('accounts', 'branches')
            );

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحميل الصفحة: ' . $e->getMessage());
        }
    }

}
