<?php

namespace Modules\Api\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;

class AccountingService
{
    /**
     * Create journal entries for invoice
     */
    public function createJournalEntries($invoice, array $calculationData): void
    {
        // Get required accounts
        $clientAccount = $this->getClientAccount($invoice->client_id);
        $vatAccount = $this->getVatAccount();
        $salesAccount = $this->getSalesAccount();

        // Create main invoice journal entry
        $journalEntry = $this->createMainJournalEntry($invoice);

        // Create journal entry details
        $this->createJournalEntryDetails($journalEntry, $clientAccount, $salesAccount, $vatAccount, $calculationData);

        // Update account balances
        $this->updateAccountBalances($clientAccount, $salesAccount, $vatAccount, $calculationData);
    }

    /**
     * Get client account
     */
    protected function getClientAccount(int $clientId): Account
    {
        $clientAccount = Account::where('client_id', $clientId)->first();
        
        if (!$clientAccount) {
            throw new \Exception('حساب العميل غير موجود');
        }

        return $clientAccount;
    }

    /**
     * Get VAT account
     */
    protected function getVatAccount(): Account
    {
        $vatAccount = Account::where('name', 'القيمة المضافة المحصلة')->first();
        
        if (!$vatAccount) {
            throw new \Exception('حساب القيمة المضافة المحصلة غير موجود');
        }

        return $vatAccount;
    }

    /**
     * Get sales account
     */
    protected function getSalesAccount(): Account
    {
        $salesAccount = Account::where('name', 'المبيعات')->first();
        
        if (!$salesAccount) {
            throw new \Exception('حساب المبيعات غير موجود');
        }

        return $salesAccount;
    }

    /**
     * Create main journal entry
     */
    protected function createMainJournalEntry($invoice): JournalEntry
    {
        return JournalEntry::create([
            'reference_number' => $invoice->code,
            'date' => now(),
            'description' => 'فاتورة مبيعات رقم ' . $invoice->code,
            'status' => 1,
            'currency' => 'SAR',
            'client_id' => $invoice->client_id,
            'invoice_id' => $invoice->id,
        ]);
    }

    /**
     * Create journal entry details
     */
    protected function createJournalEntryDetails(
        JournalEntry $journalEntry,
        Account $clientAccount,
        Account $salesAccount,
        Account $vatAccount,
        array $calculationData
    ): void {
        // Client account (debit)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $clientAccount->id,
            'description' => 'فاتورة مبيعات رقم ' . $journalEntry->reference_number,
            'debit' => $calculationData['total_with_tax'],
            'credit' => 0,
            'is_debit' => true,
        ]);

        // Sales account (credit)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $salesAccount->id,
            'description' => 'إيرادات مبيعات',
            'debit' => 0,
            'credit' => $calculationData['amount_after_discount'],
            'is_debit' => false,
        ]);

        // VAT account (credit)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $vatAccount->id,
            'description' => 'ضريبة القيمة المضافة',
            'debit' => 0,
            'credit' => $calculationData['tax_total'],
            'is_debit' => false,
        ]);
    }

    /**
     * Update account balances
     */
    protected function updateAccountBalances(
        Account $clientAccount,
        Account $salesAccount,
        Account $vatAccount,
        array $calculationData
    ): void {
        // Update sales account balance
        $salesAccount->balance += $calculationData['amount_after_discount'];
        $salesAccount->save();

        // Update revenue account
        $revenueAccount = Account::where('name', 'الإيرادات')->first();
        if ($revenueAccount) {
            $revenueAccount->balance += $calculationData['amount_after_discount'];
            $revenueAccount->save();
        }

        // Update VAT account balance
        $vatAccount->balance += $calculationData['tax_total'];
        $vatAccount->save();
        $this->updateParentBalance($vatAccount->parent_id, $calculationData['tax_total']);

        // Update assets account
        $assetsAccount = Account::where('name', 'الأصول')->first();
        if ($assetsAccount) {
            $assetsAccount->balance += $calculationData['total_with_tax'];
            $assetsAccount->save();
        }

        // Update client account balance
        $clientAccount->balance += $calculationData['total_with_tax'];
        $clientAccount->save();
    }

    /**
     * Update parent account balances recursively
     */
    protected function updateParentBalance(?int $parentId, float $amount): void
    {
        if (!$parentId) {
            return;
        }

        $parentAccount = Account::find($parentId);
        if ($parentAccount) {
            $parentAccount->balance += $amount;
            $parentAccount->save();
            
            // Recursively update parent's parent
            $this->updateParentBalance($parentAccount->parent_id, $amount);
        }
    }
}