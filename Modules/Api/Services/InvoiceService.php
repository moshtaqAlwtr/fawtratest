<?php

namespace Modules\Api\Services;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Branch;
use App\Models\EmployeeClientVisit;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Auth;
use App\Models\StoreHouse;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\PermissionSource;
use App\Models\WarehousePermits;
use App\Models\WarehousePermitsProducts;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\PaymentsProcess;
use App\Models\TreasuryEmployee;
use App\Models\GiftOffer;
use App\Models\notifications;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


// App\Services\InvoiceService.php
class InvoiceService
{
     public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $code = $this->generateUniqueCode($data['code'] ?? null);
            $summary = $this->calculateInvoiceTotals($data['items'], $data);
            $invoice = $this->storeInvoiceRecord($data, $summary, $code);
            $giftItems = $this->generateGiftItems($data['items'], $data['client_id']);
            $allItems = array_merge($data['items'], $giftItems);
            $this->storeInvoiceItems($invoice, $allItems);
            $this->handleStockAndPermits($invoice, $allItems);
            $this->storeJournalEntry($invoice, $summary);
            $this->handlePaymentIfRequired($invoice, $data, $summary);
            $this->sendTelegramNotification($invoice);
            $this->storeInternalNotification($invoice);
            return $invoice;
        });
    }

    public function generateUniqueCode($code = null)
    {
        if ($code) return $code;
        $last = Invoice::orderBy('id', 'desc')->first();
        $next = $last ? intval($last->code) + 1 : 1;
        while (Invoice::where('code', str_pad($next, 5, '0', STR_PAD_LEFT))->exists()) {
            $next++;
        }
        return str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function calculateInvoiceTotals(array $items, array $data): array
    {
        $subTotal = 0;
        $taxTotal = 0;
        foreach ($items as $item) {
            $lineTotal = $item['unit_price'] * $item['quantity'];
            $subTotal += $lineTotal;
            $tax1 = $item['tax_1'] ?? 0;
            $tax2 = $item['tax_2'] ?? 0;
            $taxTotal += ($lineTotal * ($tax1 + $tax2)) / 100;
        }

        $discountAmount = floatval($data['discount_amount'] ?? 0);
        if (($data['discount_type'] ?? null) === 'percentage') {
            $discountAmount = ($subTotal * $discountAmount) / 100;
        }

        $grandTotal = $subTotal + $taxTotal - $discountAmount;

        return [
            'sub_total' => $subTotal,
            'tax_total' => $taxTotal,
            'discount' => $discountAmount,
            'grand_total' => $grandTotal,
        ];
    }

    public function storeInvoiceRecord(array $data, array $summary, string $code): Invoice
    {
        return Invoice::create([
            'client_id' => $data['client_id'],
            'code' => $code,
            'invoice_date' => $data['invoice_date'] ?? now(),
            'sub_total' => $summary['sub_total'],
            'tax_total' => $summary['tax_total'],
            'discount_amount' => $summary['discount'],
            'grand_total' => $summary['grand_total'],
            'notes' => $data['notes'] ?? null,
            'is_paid' => $data['is_paid'] ?? 0,
            'payment_status' => 1,
            'created_by' => auth()->id(),
        ]);
    }

    public function generateGiftItems(array $items, int $clientId): array
    {
        $giftItems = [];
        foreach ($items as $item) {
          $offers = GiftOffer::where('target_product_id', $item['product_id'])
                   ->where('is_active', 1)
                   ->get();

          foreach ($offers as $offer) {
    if ($offer->buy_quantity > 0) {
        $eligibleQty = floor($item['quantity'] / $offer->buy_quantity);
        if ($eligibleQty > 0) {
            $giftItems[] = [
                'product_id' => $offer->gift_product_id,
                'quantity' => $eligibleQty * $offer->gift_quantity,
                'unit_price' => 0,
                'discount' => 0,
                'discount_type' => null,
                'tax_1' => 0,
                'tax_2' => 0,
                'type' => 'gift',
                'item' => $offer->giftProduct?->name ?? 'هدية',
            ];
        }
    }
}

        }
        return $giftItems;
    }

   public function storeInvoiceItems(Invoice $invoice, array $items): void
{
    foreach ($items as $item) {
        $product = Product::find($item['product_id']);
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $item['product_id'] ?? null,
            'item' => $item['item'] ?? $product?->name ?? 'غير محدد',
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'discount' => $item['discount'] ?? 0,
            'discount_type' => $item['discount_type'] ?? null,
            'tax_1' => $item['tax_1'] ?? 0,
            'tax_2' => $item['tax_2'] ?? 0,
            'type' => $item['type'] ?? 'product',
        ]);
    }
}


    // Placeholder for remaining methods...

 public function handleStockAndPermits(Invoice $invoice, array $items): void
    {
        foreach ($items as $item) {
            if (($item['type'] ?? 'normal') === 'normal') {
                ProductDetails::where('product_id', $item['product_id'])

                    ->decrement('quantity', $item['quantity']);
            }
        }
        $user = Auth::user();

                    // التحقق مما إذا كان للمستخدم employee_id والبحث عن المستودع الافتراضي
                    if ($user && $user->employee_id) {
                        $defaultWarehouse = DefaultWarehouses::where('employee_id', $user->employee_id)->first();

                        // التحقق مما إذا كان هناك مستودع افتراضي واستخدام storehouse_id إذا وجد
                        if ($defaultWarehouse && $defaultWarehouse->storehouse_id) {
                            $storeHouse = StoreHouse::find($defaultWarehouse->storehouse_id);
                        } else {
                            $storeHouse = StoreHouse::where('major', 1)->first();
                        }
                    } else {
                        // إذا لم يكن لديه employee_id، يتم تعيين storehouse الافتراضي
                        $storeHouse = StoreHouse::where('major', 1)->first();
                    }

                    // الخزينة الاقتراضيه للموظف
                    $store_house_id = $storeHouse ? $storeHouse->id : null;


        $permissionSource = PermissionSource::where('name', 'فاتورة مبيعات')->first();

                    if (!$permissionSource) {
                        // لو ما وجدنا مصدر إذن، ممكن ترمي استثناء أو ترجع خطأ
                        throw new \Exception("مصدر إذن 'فاتورة مبيعات' غير موجود في قاعدة البيانات.");
                    }

          $permit = new WarehousePermits();
                     $permit->permission_source_id = $permissionSource->id; // جلب id المصدر الديناميكي
                     $permit->permission_date = $invoice->created_at;
                     $permit->number = $invoice->id;
                     $permit->grand_total = $invoice->grand_total;
                     $permit->store_houses_id = $storeHouse->id;
                     $permit->created_by = auth()->user()->id;
                     $permit->save();

$total_quantity = DB::table('product_details')->where('product_id', $item['product_id'])->sum('quantity');
                    $stock_before = $total_quantity;
                    $stock_after = $stock_before - $item['quantity'];

        foreach ($items as $item) {
            if (($item['type'] ?? 'product') === 'product') {
                  // ** تسجيل البيانات في WarehousePermitsProducts **
                    WarehousePermitsProducts::create([
                        'quantity' => $item['quantity'],
                        'total' => 30,
                        'unit_price' => $item['unit_price'],
                        'product_id' => $item['product_id'],
                        'stock_before' => $stock_before, // المخزون قبل التحديث
                        'stock_after' => $stock_after,   // المخزون بعد التحديث
                        'warehouse_permits_id' => $permit->id,
                    ]);
            }
        }
    }

    public function storeJournalEntry(Invoice $invoice, array $summary): void
    {
        $entry = JournalEntry::create([
            'date' => now(),
            'description' => 'فاتورة مبيعات #' . $invoice->code,
            'source_type' => Invoice::class,
            'source_id' => $invoice->id,
        ]);

        JournalEntryDetail::insert([
            [
                'journal_entry_id' => $entry->id,
                'account_id' => Account::where('type', 'sales')->value('id'),
                'debit' => 0,
                'credit' => $summary['sub_total'],
            ],
            [
                'journal_entry_id' => $entry->id,
                'account_id' => Account::where('type', 'tax')->value('id'),
                'debit' => 0,
                'credit' => $summary['tax_total'],
            ],
            [
                'journal_entry_id' => $entry->id,
                'account_id' => Account::where('type', 'client')->value('id'),
                'debit' => $summary['grand_total'],
                'credit' => 0,
            ],
        ]);
    }

    public function handlePaymentIfRequired(Invoice $invoice, array $data, array $summary): void
    {
        $amount = $data['advance_payment'] ?? 0;
        if ($amount > 0) {
            $process = PaymentsProcess::create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'client_id' => $invoice->client_id,
                'employee_id' => auth()->id(),
                'type' => 'receipt',
                'notes' => 'تم إنشاء الدفعة تلقائياً عند إنشاء الفاتورة',
            ]);

            JournalEntry::create([
                'date' => now(),
                'description' => 'دفعة على فاتورة #' . $invoice->code,
                'source_type' => PaymentsProcess::class,
                'source_id' => $process->id,
            ]);
        }
    }
     public function sendTelegramNotification(Invoice $invoice): void
    {
        $client = $invoice->client;
        $employee = $invoice->employee;
        $creator = $invoice->creator;
        $items = $invoice->items;

        $message = "\xF0\x9F\x93\x9C *فاتورة جديدة* \n";
        $message .= "رقم الفاتورة: `{$invoice->code}`\n";
        $message .= "العميل: {$client?->trade_name}\n";
        $message .= "الموظف: {$employee?->first_name}\n";
        $message .= "أنشأها: {$creator?->name}\n";
        $message .= "المجموع: `" . number_format($invoice->grand_total, 2) . "` ر.س\n";
        $message .= "الضريبة: `" . number_format($invoice->tax_total, 2) . "` ر.س\n";
        $message .= "\xF0\x9F\x93\xA6 *المنتجات:*\n";

        foreach ($items as $item) {
            $message .= "\xE2\x97\x8D {$item->item} - الكمية: {$item->quantity}, السعر: {$item->unit_price}\n";
        }

        $message .= "\xF0\x9F\x93\x85 التاريخ: `" . now()->format('Y-m-d H:i') . "`\n";

        Http::post('https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendMessage', [
            'chat_id' => '@Salesfatrasmart',
            'text' => $message,
            'parse_mode' => 'Markdown',
            'timeout' => 20,
        ]);
    }

    public function storeInternalNotification(Invoice $invoice): void
    {
        $creator = $invoice->creator;
        $client = $invoice->client;

        notifications::create([
            'type' => 'invoice',
            'title' => ($creator?->name ?? 'مستخدم') . ' أضاف فاتورة لعميل',
            'description' => 'فاتورة للعميل ' . ($client?->trade_name ?? 'غير معروف') .
                ' بقيمة ' . number_format($invoice->grand_total, 2) . ' ر.س',
        ]);
    }
    // تابع باقي الدوال مثل generateUniqueCode, calculateInvoiceTotals, storeInvoiceItems ...
}


