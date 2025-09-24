<?php

namespace App\Services\Accounts;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JournalEntryService
{
    /**
     * إنشاء قيد محاسبي تلقائي
     */
    public function createJournalEntry($data)
    {
        try {
            DB::beginTransaction();

            // إنشاء القيد الرئيسي
            $journalEntry = new JournalEntry();
            $journalEntry->date = $data['date'] ?? now();
            $journalEntry->description = $data['description'];
            $journalEntry->reference_number = $data['reference_number'];
            $journalEntry->status = $data['status'] ?? 1;
            $journalEntry->client_id = $data['client_id'] ?? null;
            $journalEntry->currency = $data['currency'] ?? 'SAR';
            $journalEntry->created_by_employee = auth()->id();
            $journalEntry->save();

            // إضافة تفاصيل القيد
            foreach ($data['details'] as $detail) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $detail['account_id'],
                    'description' => $detail['description'],
                    'debit' => $detail['debit'] ?? 0,
                    'credit' => $detail['credit'] ?? 0,
                    'cost_center_id' => $detail['cost_center_id'] ?? null
                ]);
            }

            DB::commit();
            return $journalEntry;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * البحث عن الحساب حسب النوع والعملية
     */
    private function findAccount($type, $operation)
    {
        $account = ChartOfAccount::where('type', $type)
            ->where('operation', $operation)
            ->first();

        if (!$account) {
            throw new \Exception("لم يتم العثور على الحساب من نوع: {$type} للعملية: {$operation}");
        }

        return $account;
    }

    /**
     * إنشاء قيد محاسبي لفاتورة المبيعات
     */
    public function createSalesInvoiceEntry($invoice, $total, $totalTax)
    {
        try {
            // البحث عن حساب المبيعات الرئيسي
            $salesAccount = ChartOfAccount::where('name', 'LIKE', '%مبيعات%')
                ->whereNull('parent_id')
                ->where('status', 1)
                ->first();

            if (!$salesAccount) {
                throw new \Exception('لم يتم العثور على حساب المبيعات الرئيسي');
            }

            // إنشاء حساب فرعي للفاتورة
            $invoiceAccount = new ChartOfAccount();
            $invoiceAccount->name = "فاتورة مبيعات رقم: " . $invoice->invoice_number;
            $invoiceAccount->code = $salesAccount->code . '.' . $invoice->id;
            $invoiceAccount->parent_id = $salesAccount->id;
            $invoiceAccount->type = 'invoice';
            $invoiceAccount->nature = 'credit';
            $invoiceAccount->status = 1;
            $invoiceAccount->save();

            // البحث عن حساب الضريبة إذا كان هناك ضريبة
            $taxAccount = null;
            if ($totalTax > 0) {
                $taxAccount = $this->findAccount('liability', 'sales_tax');
            }

            // إنشاء بيانات القيد
            $data = [
                'date' => $invoice->invoice_date,
                'description' => 'قيد تلقائي - فاتورة مبيعات رقم: ' . $invoice->invoice_number,
                'reference_number' => $invoice->invoice_number,
                'status' => 1,
                'client_id' => $invoice->client_id,
                'currency' => $invoice->currency,
                'details' => [
                    [
                        'account_id' => $salesAccount->id,
                        'description' => 'مدين - قيمة المبيعات',
                        'debit' => $total + $totalTax,
                        'credit' => 0
                    ],
                    [
                        'account_id' => $invoiceAccount->id,
                        'description' => 'دائن - قيمة الفاتورة',
                        'debit' => 0,
                        'credit' => $total
                    ]
                ]
            ];

            // إضافة قيد الضريبة إذا وجدت
            if ($totalTax > 0 && $taxAccount) {
                $data['details'][] = [
                    'account_id' => $taxAccount->id,
                    'description' => 'دائن - ضريبة القيمة المضافة',
                    'debit' => 0,
                    'credit' => $totalTax
                ];
            }

            $journalEntry = $this->createJournalEntry($data);
            
            // تحديث الفاتورة بمعرف الحساب
            $invoice->account_id = $invoiceAccount->id;
            $invoice->save();

            return [
                'journal_entry' => $journalEntry,
                'invoice_account' => $invoiceAccount
            ];

        } catch (\Exception $e) {
            Log::error('خطأ في إنشاء القيد المحاسبي: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * إنشاء قيد محاسبي لفاتورة المرتجعات
     */
    public function createReturnInvoiceEntry($invoice, $total, $totalTax)
    {
        // البحث عن الحسابات المطلوبة للمرتجعات
        $customersAccount = $this->findAccount('asset', 'sales_customers');
        $salesAccount = $this->findAccount('revenue', 'sales_returns');
        $taxAccount = $this->findAccount('liability', 'sales_tax');

        $data = [
            'date' => $invoice->invoice_date,
            'description' => 'قيد تلقائي - فاتورة مرتجعة رقم: ' . $invoice->invoice_number,
            'reference_number' => $invoice->invoice_number,
            'status' => 1,
            'client_id' => $invoice->client_id,
            'currency' => $invoice->currency,
            'details' => [
                [
                    'account_id' => $salesAccount->id,
                    'description' => 'مدين - قيمة مرتجع المبيعات',
                    'debit' => $total,
                    'credit' => 0
                ],
                [
                    'account_id' => $customersAccount->id,
                    'description' => 'دائن - قيمة فاتورة المرتجعات',
                    'debit' => 0,
                    'credit' => $total + $totalTax
                ]
            ]
        ];

        // إضافة قيد الضريبة إذا وجدت
        if ($totalTax > 0) {
            $data['details'][] = [
                'account_id' => $taxAccount->id,
                'description' => 'مدين - ضريبة القيمة المضافة المرتجعة',
                'debit' => $totalTax,
                'credit' => 0
            ];
        }

        return $this->createJournalEntry($data);
    }

    /**
     * إنشاء قيد محاسبي للمشتريات
     */
    public function createPurchaseEntry($purchase, $total, $totalTax)
    {
        $suppliersAccount = $this->findAccount('liability', 'purchases_suppliers');
        $purchasesAccount = $this->findAccount('expense', 'purchases');
        $taxAccount = $this->findAccount('liability', 'purchases_tax');

        $data = [
            'date' => $purchase->purchase_date,
            'description' => 'قيد تلقائي - فاتورة مشتريات رقم: ' . $purchase->purchase_number,
            'reference_number' => $purchase->purchase_number,
            'status' => 1,
            'supplier_id' => $purchase->supplier_id,
            'currency' => $purchase->currency,
            'details' => [
                [
                    'account_id' => $purchasesAccount->id,
                    'description' => 'مدين - قيمة المشتريات',
                    'debit' => $total,
                    'credit' => 0
                ],
                [
                    'account_id' => $suppliersAccount->id,
                    'description' => 'دائن - قيمة فاتورة المشتريات',
                    'debit' => 0,
                    'credit' => $total + $totalTax
                ]
            ]
        ];

        // إضافة قيد الضريبة إذا وجدت
        if ($totalTax > 0) {
            $data['details'][] = [
                'account_id' => $taxAccount->id,
                'description' => 'مدين - ضريبة القيمة المضافة',
                'debit' => $totalTax,
                'credit' => 0
            ];
        }

        return $this->createJournalEntry($data);
    }

    /**
     * إنشاء قيد محاسبي للمصروفات
     */
    public function createExpenseEntry($expense)
    {
        $cashAccount = $this->findAccount('asset', 'cash');
        $expenseAccount = $this->findAccount('expense', $expense->expense_account_operation);

        $data = [
            'date' => $expense->date,
            'description' => 'قيد تلقائي - مصروف: ' . $expense->description,
            'reference_number' => $expense->reference_number,
            'status' => 1,
            'currency' => $expense->currency,
            'details' => [
                [
                    'account_id' => $expenseAccount->id,
                    'description' => 'مدين - ' . $expense->description,
                    'debit' => $expense->amount,
                    'credit' => 0
                ],
                [
                    'account_id' => $cashAccount->id,
                    'description' => 'دائن - سداد مصروف',
                    'debit' => 0,
                    'credit' => $expense->amount
                ]
            ]
        ];

        return $this->createJournalEntry($data);
    }

    /**
     * إنشاء قيد محاسبي للإيرادات
     */
    public function createRevenueEntry($revenue)
    {
        $cashAccount = $this->findAccount('asset', 'cash');
        $revenueAccount = $this->findAccount('revenue', $revenue->revenue_account_operation);

        $data = [
            'date' => $revenue->date,
            'description' => 'قيد تلقائي - إيراد: ' . $revenue->description,
            'reference_number' => $revenue->reference_number,
            'status' => 1,
            'currency' => $revenue->currency,
            'details' => [
                [
                    'account_id' => $cashAccount->id,
                    'description' => 'مدين - تحصيل إيراد',
                    'debit' => $revenue->amount,
                    'credit' => 0
                ],
                [
                    'account_id' => $revenueAccount->id,
                    'description' => 'دائن - ' . $revenue->description,
                    'debit' => 0,
                    'credit' => $revenue->amount
                ]
            ]
        ];

        return $this->createJournalEntry($data);
    }

    /**
     * إنشاء قيد محاسبي لسداد العملاء
     */
    public function createCustomerPaymentEntry($payment)
    {
        $cashAccount = $this->findAccount('asset', 'cash');
        $customersAccount = $this->findAccount('asset', 'sales_customers');

        $data = [
            'date' => $payment->date,
            'description' => 'قيد تلقائي - سداد من العميل: ' . $payment->client->name,
            'reference_number' => $payment->reference_number,
            'status' => 1,
            'client_id' => $payment->client_id,
            'currency' => $payment->currency,
            'details' => [
                [
                    'account_id' => $cashAccount->id,
                    'description' => 'مدين - تحصيل من العميل',
                    'debit' => $payment->amount,
                    'credit' => 0
                ],
                [
                    'account_id' => $customersAccount->id,
                    'description' => 'دائن - سداد العميل',
                    'debit' => 0,
                    'credit' => $payment->amount
                ]
            ]
        ];

        return $this->createJournalEntry($data);
    }

    /**
     * إنشاء قيد محاسبي لسداد الموردين
     */
    public function createSupplierPaymentEntry($payment)
    {
        $cashAccount = $this->findAccount('asset', 'cash');
        $suppliersAccount = $this->findAccount('liability', 'purchases_suppliers');

        $data = [
            'date' => $payment->date,
            'description' => 'قيد تلقائي - سداد للمورد: ' . $payment->supplier->name,
            'reference_number' => $payment->reference_number,
            'status' => 1,
            'supplier_id' => $payment->supplier_id,
            'currency' => $payment->currency,
            'details' => [
                [
                    'account_id' => $suppliersAccount->id,
                    'description' => 'مدين - سداد للمورد',
                    'debit' => $payment->amount,
                    'credit' => 0
                ],
                [
                    'account_id' => $cashAccount->id,
                    'description' => 'دائن - سداد للمورد',
                    'debit' => 0,
                    'credit' => $payment->amount
                ]
            ]
        ];

        return $this->createJournalEntry($data);
    }

    /**
     * إنشاء حساب فرعي للفاتورة تحت حساب المبيعات
     */
    protected function createInvoiceAccount($invoice)
    {
        try {
            // البحث عن حساب المبيعات الرئيسي
            $salesAccount = ChartOfAccount::where('name', 'LIKE', '%مبيعات%')
                ->whereNull('parent_id')
                ->first();

            if (!$salesAccount) {
                throw new \Exception('لم يتم العثور على حساب المبيعات الرئيسي');
            }

            // إنشاء حساب فرعي للفاتورة
            $invoiceAccount = new ChartOfAccount();
            $invoiceAccount->name = "فاتورة مبيعات رقم: " . $invoice->invoice_number;
            $invoiceAccount->code = $salesAccount->code . '.' . $invoice->id; // تنسيق الكود: كود_المبيعات.رقم_الفاتورة
            $invoiceAccount->parent_id = $salesAccount->id;
            $invoiceAccount->type = 'invoice';
            $invoiceAccount->nature = 'credit'; // طبيعة حساب المبيعات دائن
            $invoiceAccount->status = 1;
            $invoiceAccount->save();

            return $invoiceAccount;
        } catch (\Exception $e) {
            Log::error('خطأ في إنشاء حساب الفاتورة: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * إنشاء قيد محاسبي للفاتورة مع إنشاء حساب فرعي
     */
    public function createInvoiceJournalEntry($invoice)
    {
        try {
            DB::beginTransaction();

            // إنشاء حساب فرعي للفاتورة
            $invoiceAccount = $this->createInvoiceAccount($invoice);

            // إنشاء بيانات القيد
            $entryData = [
                'date' => $invoice->invoice_date,
                'description' => 'قيد تلقائي - فاتورة مبيعات رقم: ' . $invoice->invoice_number,
                'reference_number' => $invoice->invoice_number,
                'status' => 1,
                'client_id' => $invoice->client_id,
                'details' => [
                    [
                        'account_id' => $invoiceAccount->id,
                        'description' => 'مبيعات - ' . $invoice->invoice_number,
                        'credit' => $invoice->total_amount,
                        'debit' => 0
                    ],
                    [
                        'account_id' => $invoice->customer_account_id, // حساب العميل
                        'description' => 'ذمم مدينة - ' . $invoice->invoice_number,
                        'debit' => $invoice->total_amount,
                        'credit' => 0
                    ]
                ]
            ];

            // إنشاء القيد المحاسبي
            $journalEntry = $this->createJournalEntry($entryData);

            DB::commit();
            return [
                'journal_entry' => $journalEntry,
                'invoice_account' => $invoiceAccount
            ];

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
