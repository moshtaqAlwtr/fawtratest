<?php

namespace App\Services\Invoices;

use App\Models\Invoice;
use App\Models\ChartOfAccount;
use App\Models\InvoiceItem;
use App\Services\Accounts\JournalEntryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class InvoiceService
{
    protected $journalEntryService;

    public function __construct(JournalEntryService $journalEntryService)
    {
        $this->journalEntryService = $journalEntryService;
    }

    /**
     * إنشاء فاتورة جديدة مع القيود المحاسبية
     */
    public function createInvoice(array $data)
    {
        DB::beginTransaction();
        try {
            // 1. إنشاء الفاتورة
            $invoice = Invoice::create([
                // 'invoice_number' => $this->generateInvoiceNumber(),
                'invoice_date' => $data['invoice_date'] ?? now(),
                'client_id' => $data['client_id'],
                'total_amount' => $data['total_amount'],
                'status' => 'pending',
                'type' => $data['type'] ?? 'invoice',
                'notes' => $data['notes'] ?? null,
            ]);

            // 2. إنشاء عناصر الفاتورة
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['quantity'] * $item['price'],
                    ]);
                }
            }

            // 3. إنشاء قيد محاسبي للفاتورة
            $journalData = [
                'date' => $invoice->invoice_date,
                'description' => 'فاتورة مبيعات رقم: ' . $invoice->invoice_number,
                'reference_number' => $invoice->invoice_number,
                'status' => 1,
                'client_id' => $invoice->client_id,
                'details' => []
            ];

            // البحث عن الحسابات المطلوبة
            $customersAccount = ChartOfAccount::where('type', 'asset')
                ->where('operation', 'sales_customers')
                ->first();

            if (!$customersAccount) {
                throw new \Exception('لم يتم العثور على حساب العملاء. الرجاء التأكد من وجود حساب من نوع asset وعملية sales_customers');
            }

            $salesAccount = ChartOfAccount::where('type', 'revenue')
                ->where('operation', 'sales')
                ->first();

            if (!$salesAccount) {
                throw new \Exception('لم يتم العثور على حساب المبيعات. الرجاء التأكد من وجود حساب من نوع revenue وعملية sales');
            }

            // حساب العملاء (مدين)
            $journalData['details'][] = [
                'account_id' => $customersAccount->id,
                'description' => 'مبلغ الفاتورة',
                'debit' => $invoice->total_amount,
                'credit' => 0
            ];

            // حساب المبيعات (دائن)
            $journalData['details'][] = [
                'account_id' => $salesAccount->id,
                'description' => 'قيمة المبيعات',
                'debit' => 0,
                'credit' => $invoice->total_amount
            ];

            // إنشاء القيد المحاسبي
            $this->journalEntryService->createJournalEntry($journalData);

            DB::commit();

            return Redirect::route('invoices.index')->with('success', 'تم إنشاء الفاتورة بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception('حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * الحصول على معرف الحساب حسب النوع والعملية
     */
    private function getAccountId($type, $operation)
    {
        $account = ChartOfAccount::where('type', $type)
            ->where('operation', $operation)
            ->first();

        if (!$account) {
            throw new \Exception("لم يتم العثور على الحساب من نوع: {$type} للعملية: {$operation}");
        }

        return $account->id;
    }
}
