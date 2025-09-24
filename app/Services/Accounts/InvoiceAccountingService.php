<?php

namespace App\Services\Accounts;

use App\Models\ChartOfAccount;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceAccountingService
{
    protected $journalEntryService;

    public function __construct(JournalEntryService $journalEntryService)
    {
        $this->journalEntryService = $journalEntryService;
    }

    /**
     * تسجيل القيود المحاسبية للفاتورة
     */
    public function recordInvoiceEntries(Invoice $invoice)
    {
        try {
            Log::info('بدء تسجيل القيود المحاسبية للفاتورة رقم: ' . $invoice->invoice_number);

            // البحث عن حساب المبيعات
            $salesAccount = ChartOfAccount::where('code', '4.1')->first();
            Log::info('نتيجة البحث عن حساب المبيعات:', [
                'found' => $salesAccount ? 'نعم' : 'لا',
                'account' => $salesAccount
            ]);

            // البحث عن حساب الذمم المدينة
            $accountsReceivable = ChartOfAccount::where('code', '1.2')->first();
            Log::info('نتيجة البحث عن حساب الذمم المدينة:', [
                'found' => $accountsReceivable ? 'نعم' : 'لا',
                'account' => $accountsReceivable
            ]);

            if (!$salesAccount) {
                Log::error('حساب المبيعات غير موجود');
                Log::error('الحسابات المتوفرة: ');
                ChartOfAccount::all()->each(function($account) {
                    Log::error("الحساب: {$account->name}, الكود: {$account->code}");
                });
                throw new \Exception('لم يتم العثور على حساب المبيعات');
            }

            if (!$accountsReceivable) {
                Log::error('حساب الذمم المدينة غير موجود');
                Log::error('الحسابات المتوفرة: ');
                ChartOfAccount::all()->each(function($account) {
                    Log::error("الحساب: {$account->name}, الكود: {$account->code}");
                });
                throw new \Exception('لم يتم العثور على حساب الذمم المدينة');
            }

            // إنشاء بيانات القيد
            $entryData = [
                'date' => $invoice->invoice_date,
                'description' => 'قيد تلقائي - فاتورة رقم: ' . $invoice->invoice_number,
                'reference_number' => $invoice->invoice_number,
                'client_id' => $invoice->client_id,
                'status' => 1,
                'details' => []
            ];

            // إضافة الطرف المدين (الذمم المدينة)
            $entryData['details'][] = [
                'account_id' => $accountsReceivable->id,
                'description' => 'مبلغ الفاتورة - ' . $invoice->invoice_number,
                'debit' => $invoice->total_amount,
                'credit' => 0
            ];

            // إضافة الطرف الدائن (المبيعات)
            $entryData['details'][] = [
                'account_id' => $salesAccount->id,
                'description' => 'إيراد مبيعات - فاتورة رقم: ' . $invoice->invoice_number,
                'debit' => 0,
                'credit' => $invoice->total_amount
            ];

            Log::info('بيانات القيد المحاسبي:', $entryData);

            // إنشاء القيد المحاسبي
            $journalEntry = $this->journalEntryService->createJournalEntry($entryData);
            Log::info('تم إنشاء القيد المحاسبي بنجاح:', ['journal_entry_id' => $journalEntry->id]);

            // تحديث الفاتورة بمعرف القيد
            $invoice->journal_entry_id = $journalEntry->id;
            $invoice->save();

            Log::info('تم تحديث الفاتورة بمعرف القيد المحاسبي');

            return $journalEntry;

        } catch (\Exception $e) {
            Log::error('خطأ في تسجيل القيود المحاسبية: ' . $e->getMessage());
            throw new \Exception('فشل في تسجيل القيود المحاسبية: ' . $e->getMessage());
        }
    }
}
