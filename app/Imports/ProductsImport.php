<?php
namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // تنظيف وتحويل القيم إلى أرقام لضمان عدم وجود مسافات أو نصوص غير صحيحة
        $categoryId = isset($row['category_id']) ? trim($row['category_id']) : null;

        // التأكد أن category_id هو رقم صالح وليس فارغًا
        if (!is_numeric($categoryId) || !Category::where('id', $categoryId)->exists()) {
            return null; // تجاوز السطر إذا لم يكن التصنيف صالحًا
        }

        return new Product([
            'name' => $row['name'] ?? null,
            'description' => $row['description'] ?? null,
            'category_id' => (int) $categoryId, // تحويل القيمة إلى رقم صحيح
            'serial_number' => $row['serial_number'] ?? null,
            'brand' => $row['brand'] ?? null,
            'supplier_id' => $row['supplier_id'] ?? null,
            'low_stock_thershold' => $this->parseNumeric($row['low_stock_thershold'] ?? 0),
            'barcode' => $row['barcode'] ?? null,
            'sales_cost_account' => $row['sales_cost_account'] ?? null,
            'sales_account' => $row['sales_account'] ?? null,
            'available_online' => $this->parseNumeric($row['available_online'] ?? 0),
            'featured_product' => $this->parseNumeric($row['featured_product'] ?? 0),
            'track_inventory' => $this->parseNumeric($row['track_inventory'] ?? 0),
            'inventory_type' => $row['inventory_type'] ?? null,
            'low_stock_alert' => $this->parseNumeric($row['low_stock_alert'] ?? 0),
            'Internal_notes' => $row['internal_notes'] ?? null,
            'tags' => $row['tags'] ?? null,
            'images' => $row['images'] ?? null,
            'status' => $row['status'] ?? 'active',
            'purchase_price' => $this->parseNumeric($row['purchase_price'] ?? 0),
            'sale_price' => $this->parseNumeric($row['sale_price'] ?? 0),
            'tax1' => $this->parseNumeric($row['tax1'] ?? 0),
            'tax2' => $this->parseNumeric($row['tax2'] ?? 0),
            'min_sale_price' => $this->parseNumeric($row['min_sale_price'] ?? 0),
            'discount' => $this->parseNumeric($row['discount'] ?? 0),
           'discount_type' => $this->parseNumeric($row['discount_type'] ?? null),

            'profit_margin' => $this->parseNumeric($row['profit_margin'] ?? 0),
            'created_by' => auth()->id() ?? null,
        ]);
    }

    /**
     * تحويل القيم إلى أرقام والتأكد من صحتها.
     */
    private function parseNumeric($value): float
    {
        $value = trim($value); // إزالة المسافات الفارغة
        return is_numeric($value) ? (float) $value : 0; // إذا كانت القيمة رقمية، يتم تحويلها وإلا تعيينها 0
    }
}

