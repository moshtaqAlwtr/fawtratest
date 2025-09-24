<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    protected $fillable = ['client_id', 'treasury_id', 'payment', 'invoice_date', 'issue_date', 'payment_terms', 'payment_status', 'currency', 'total', 'grand_total', 'due_value', 'employee_id', 'advance_payment', 'remaining_amount', 'is_paid', 'payment_method', 'reference_number', 'notes', 'code', 'discount_amount', 'discount_type', 'shipping_cost', 'shipping_tax', 'tax_type', 'tax_total', 'attachments', 'type','subtotal', 'created_by', 'updated_by','InvoiceAdjustmentLabel','InvoiceAdjustmentValue','subscription_id','adjustment_label','adjustment_value','adjustment_type'];

    protected $casts = [
        'invoice_date' => 'date',
        'issue_date' => 'date',
        'is_paid' => 'boolean',
        'total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'shipping_tax' => 'decimal:2',
        'tax_total' => 'decimal:2',
    ];
// في Invoice Model
public function journalEntry()
{
    return $this->hasOne(JournalEntry::class, 'invoice_id', 'id');
}
    // العلاقة مع العميل
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    public function applyOffers()
    {
        $today = now()->format('Y-m-d');
        $clientId = $this->client_id;
        $invoiceItems = $this->items;
        $appliedOffers = [];

        // جلب جميع العروض النشطة في الفترة الحالية
        $offers = Offer::where('is_active', true)
            ->whereDate('valid_from', '<=', $today)
            ->whereDate('valid_to', '>=', $today)
            ->get();

        foreach ($invoiceItems as $item) {
            $productId = $item->product_id;
            $categoryId = $item->product->category_id ?? null;
            $quantity = $item->quantity;
            $originalPrice = $item->unit_price;

            foreach ($offers as $offer) {
                // التحقق من شروط العرض
                if ($this->checkOfferConditions($offer, $clientId, $productId, $categoryId, $quantity)) {
                    $discountValue = $this->calculateDiscount($offer, $originalPrice);
                    $appliedOffers[] = [
                        'product_id' => $productId,
                        'offer_id' => $offer->id,
                        'discount_value' => $discountValue,
                        'original_price' => $originalPrice,
                        'final_price' => $originalPrice - $discountValue
                    ];

                    // تطبيق الخصم على العنصر
                    $item->discount += $discountValue;
                    $item->save();
                }
            }
        }

        return $appliedOffers;
    }

    private function checkOfferConditions($offer, $clientId, $productId, $categoryId, $quantity)
    {
        // 1. التحقق من أن العميل مشمول بالعرض (إذا كان العرض محدد لعملاء معينين)
        if ($offer->clients->isNotEmpty() && !$offer->clients->contains('id', $clientId)) {
            return false;
        }

        // 2. التحقق من نوع الوحدة
        switch ($offer->unit_type) {
            case 1: // الكل
                break;

            case 2: // التصنيف
                if (!$offer->categories->contains('id', $categoryId)) {
                    return false;
                }
                break;

            case 3: // المنتجات
                if (!$offer->products->contains('id', $productId)) {
                    return false;
                }
                break;
        }

        // 3. التحقق من نوع العرض وكميته
        if ($offer->type == 2 && $quantity < $offer->quantity) {
            return false;
        }

        return true;
    }

    private function calculateDiscount($offer, $originalPrice)
    {
        if ($offer->discount_type == 1) { // خصم حقيقي
            return $offer->discount_value;
        } else { // خصم نسبي
            return ($originalPrice * $offer->discount_value) / 100;
        }
    }
    // العلاقة مع الخزينة
    public function treasury(): BelongsTo
    {
        return $this->belongsTo(Treasury::class);
    }
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    // العلاقة مع المستخدم الذي أنشأ الفاتورة
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // العلاقة مع المستخدم الذي قام بتحديث الفاتورة
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // العلاقة مع عناصر الفاتورة

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    // العلاقة مع جميع عمليات الدفع
    public function payments(): HasMany
    {
        return $this->hasMany(PaymentsProcess::class)->where('type', 'client payments');
    }

    // العلاقة مع آخر عملية دفع فقط
    public function lastPayment(): HasOne
    {
        return $this->hasOne(PaymentsProcess::class)->where('type', 'client payments')->latest();
    }

    // دالة لحساب المبلغ المتبقي
    public function calculateRemainingAmount(): float
    {
        $totalPaid = $this->payments()->sum('amount');
        return $this->grand_total - $totalPaid;
    }

    // دالة لتحديث حالة الدفع
    public function updatePaymentStatus(): void
    {
        $remainingAmount = $this->calculateRemainingAmount();

        if ($remainingAmount <= 0) {
            $this->payment_status = 1; // مدفوع بالكامل
            $this->is_paid = true;
        } elseif ($remainingAmount < $this->grand_total) {
            $this->payment_status = 2; // مدفوع جزئياً
            $this->is_paid = false;
        } else {
            $this->payment_status = 3; // غير مدفوع
            $this->is_paid = false;
        }

        $this->remaining_amount = max(0, $remainingAmount);
        $this->save();
    }
    public function payments_process()
    {
        return $this->hasMany(PaymentsProcess::class, 'invoice_id');
    }
    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'reference_id');
    }
    // في نموذج Invoice
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // في نموذج InvoiceItem
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function signatures()
{
    return $this->hasMany(Signature::class);
}
}
