<?php

namespace App\Imports;

use App\Models\Invoice;
use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InvoicesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // التحقق مما إذا كان `client_id` موجودًا في جدول `clients`
        $clientId = $row['client_id'] ?? null;
        if ($clientId && !Client::where('id', $clientId)->exists()) {
            return null; // تجاوز السطر إذا لم يكن العميل موجودًا
        }

        return new Invoice([
            'client_id' => $clientId,
            'treasury_id' => $row['treasury_id'] ?? null,
            'payment' => $row['payment'] ?? null,
            'invoice_date' => $row['invoice_date'] ?? null,
            'issue_date' => $row['issue_date'] ?? null,
            'payment_terms' => $row['payment_terms'] ?? null,
            'payment_status' => $row['payment_status'] ?? 0,
            'currency' => $row['currency'] ?? null,
            'total' => $this->parseDecimal($row['total'] ?? 0),
            'grand_total' => $this->parseDecimal($row['grand_total'] ?? 0),
            'due_value' => $this->parseDecimal($row['due_value'] ?? 0),
            'employee_id' => $row['employee_id'] ?? null,
            'advance_payment' => $this->parseDecimal($row['advance_payment'] ?? 0),
            'remaining_amount' => $this->parseDecimal($row['remaining_amount'] ?? 0),
            'is_paid' => filter_var($row['is_paid'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'payment_method' => $row['payment_method'] ?? null,
            'reference_number' => $row['reference_number'] ?? null,
            'notes' => $row['notes'] ?? null,
            'code' => $row['code'] ?? null,
            'discount_amount' => $this->parseDecimal($row['discount_amount'] ?? 0),
            'discount_type' => $row['discount_type'] ?? null,
            'shipping_cost' => $this->parseDecimal($row['shipping_cost'] ?? 0),
            'shipping_tax' => $this->parseDecimal($row['shipping_tax'] ?? 0),
            'tax_type' => $row['tax_type'] ?? null,
            'tax_total' => $this->parseDecimal($row['tax_total'] ?? 0),
            'attachments' => $row['attachments'] ?? null,
            'type' => $row['type'] ?? null,
            'created_by' => $row['created_by'] ?? null,
            'updated_by' => $row['updated_by'] ?? null,
        ]);
    }

    /**
     * Parse a decimal value from the input.
     *
     * @param mixed $value
     * @return float
     */
    private function parseDecimal($value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }
}
