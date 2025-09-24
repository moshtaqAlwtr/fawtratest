<?php
namespace App\Imports;

use App\Models\InvoiceItem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InvoiceItemsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
{
    return new InvoiceItem([
        'invoice_id' => $row['invoice_id'] ?? null,
        'product_id' => $row['product_id'] ?? null,
        'packege_id' => $row['packege_id'] ?? null,
        'quotation_id' => $row['quotation_id'] ?? null,
        'credit_note_id' => $row['credit_note_id'] ?? null,
        'purchase_invoice_id' => $row['purchase_invoice_id'] ?? null,
        'store_house_id' => $row['store_house_id'] ?? null,
        'periodic_invoice_id' => $row['periodic_invoice_id'] ?? null,
        'item' => !empty($row['item']) ? $row['item'] : 'N/A',
        'quotes_purchase_order_id' => $row['quotes_purchase_order_id'] ?? null,
        'purchase_invoice_id_type' => $row['purchase_invoice_id_type'] ?? null,
        'description' => $row['description'] ?? null,
        'unit_price' => $row['unit_price'] ?? 0,
        'quantity' => $row['quantity'] ?? 1,
        'discount' => $row['discount'] ?? 0,
        'tax_1' => $row['tax_1'] ?? 0,
        'tax_2' => $row['tax_2'] ?? 0,
        'total' => $row['total'] ?? 0,
        'type' => isset($row['type']) && in_array($row['type'], ['product', 'service'])
            ? $row['type']
            : 'product', // ✅ تعيين قيمة افتراضية إذا لم تكن موجودة
    ]);
}
}
