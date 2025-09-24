<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>أمر شراء</title>
    <style>
        body {
            direction: rtl;
            font-family: aealarabiya;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #000;
            padding: 10px;
        }

        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0;
        }

        .info-grid {
            width: 100%;
            margin: 20px 0;
        }

        .info-grid td {
            padding: 5px;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
        }

        .info-value {
            border-bottom: 1px dotted #000;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .data-table th {
            background-color: #f5f5f5;
        }

        .section-title {
            margin: 20px 0 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
        }

        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        @php
            // التحقق من وجود العناصر وتهيئة المتغيرات
            $hasItems = isset($purchaseOrdersRequests->items) && $purchaseOrdersRequests->items && $purchaseOrdersRequests->items->count() > 0;
            $items = $hasItems ? $purchaseOrdersRequests->items : collect([]);

            // تهيئة المتغيرات الحسابية
            $subtotal = 0;
            $totalDiscount = 0;
            $totalTax1 = 0;
            $totalTax2 = 0;
            $shippingCost = $purchaseOrdersRequests->shipping_cost ?? 0;
            $additionalDiscount = $purchaseOrdersRequests->discount_amount ?? 0;
            $adjustmentAmount = $purchaseOrdersRequests->adjustment_value ?? 0;
            $adjustmentType = $purchaseOrdersRequests->adjustment_type ?? 'addition';

            // حساب إجماليات العناصر
            if ($hasItems) {
                foreach ($items as $item) {
                    $itemSubtotal = ($item->quantity ?? 0) * ($item->unit_price ?? 0);
                    $subtotal += $itemSubtotal;

                    // حساب خصم العنصر
                    $itemDiscount = 0;
                    if (isset($item->discount_type) && $item->discount_type === 'percentage') {
                        $itemDiscount = ($itemSubtotal * ($item->discount ?? 0)) / 100;
                    } else {
                        $itemDiscount = $item->discount ?? 0;
                    }
                    $totalDiscount += $itemDiscount;

                    // حساب الضرائب
                    $afterDiscount = $itemSubtotal - $itemDiscount;
                    $totalTax1 += ($afterDiscount * ($item->tax_1 ?? 0)) / 100;
                    $totalTax2 += ($afterDiscount * ($item->tax_2 ?? 0)) / 100;
                }
            }

            // حساب الخصم الإضافي
            if ($additionalDiscount > 0) {
                if (($purchaseOrdersRequests->discount_type ?? 'amount') === 'percentage') {
                    $additionalDiscount = ($subtotal * $additionalDiscount) / 100;
                }
            }

            // حساب التسوية
            if ($adjustmentType === 'discount') {
                $adjustmentAmount = -abs($adjustmentAmount);
            } else {
                $adjustmentAmount = abs($adjustmentAmount);
            }

            $totalTax = $totalTax1 + $totalTax2;
            $grandTotal = $subtotal - $totalDiscount - $additionalDiscount + $adjustmentAmount + $shippingCost + $totalTax;
        @endphp

        <!-- Header Section -->
        <div class="header">
            <div class="company-name">{{ config('', 'مؤسسة اعمال خاصة للتجارة') }}</div>
            <div class="document-title">أمر شراء</div>
            <div class="order-number">رقم الأمر: {{ $purchaseOrdersRequests->code ?? $purchaseOrdersRequests->purchase_order_number ?? 'غير محدد' }}</div>
            <div>تاريخ الإصدار: {{ now()->format('d/m/Y H:i') }}</div>

            @if(isset($purchaseOrdersRequests->status))
                <div style="margin-top: 10px;">
                    @switch($purchaseOrdersRequests->status)
                        @case('under_review')
                            <span class="status-badge status-under-review">تحت المراجعة</span>
                            @break
                        @case('approved')
                            <span class="status-badge status-approved">معتمد</span>
                            @break
                        @case('canceled')
                            <span class="status-badge status-canceled">ملغي</span>
                            @break
                        @case('converted')
                            <span class="status-badge status-converted">محول إلى فاتورة</span>
                            @break
                        @default
                            <span class="status-badge status-under-review">{{ $purchaseOrdersRequests->status }}</span>
                    @endswitch
                </div>
            @endif
        </div>

        <!-- Basic Information Section -->
        <div class="info-section">
            <div class="info-header">معلومات أساسية</div>
            <table class="info-grid">
                <tr>
                    <td class="info-label">المورد:</td>
                    <td class="info-value">{{ $purchaseOrdersRequests->supplier->trade_name ?? $purchaseOrdersRequests->supplier->name ?? 'غير محدد' }}</td>
                    <td class="info-label">رقم الأمر:</td>
                    <td class="info-value">{{ $purchaseOrdersRequests->code ?? $purchaseOrdersRequests->purchase_order_number ?? 'غير محدد' }}</td>
                </tr>
                <tr>
                    <td class="info-label">تاريخ الأمر:</td>
                    <td class="info-value">{{ $purchaseOrdersRequests->order_date ?? $purchaseOrdersRequests->date ?? 'غير محدد' }}</td>
                    <td class="info-label">تاريخ التسليم:</td>
                    <td class="info-value">{{ $purchaseOrdersRequests->delivery_date ?? 'غير محدد' }}</td>
                </tr>
                <tr>
                    <td class="info-label">الأولوية:</td>
                    <td class="info-value">
                        @switch($purchaseOrdersRequests->priority ?? 'normal')
                            @case('high')
                                <span class="text-warning">عالي</span>
                                @break
                            @case('urgent')
                                <span class="text-danger">عاجل</span>
                                @break
                            @default
                                <span class="text-info">عادي</span>
                        @endswitch
                    </td>
                    <td class="info-label">منشئ الأمر:</td>
                    <td class="info-value">{{ $purchaseOrdersRequests->creator->name ?? $purchaseOrdersRequests->user->name ?? 'غير محدد' }}</td>
                </tr>
            </table>
        </div>

        <!-- Additional Information Section -->
        @if(isset($purchaseOrdersRequests->account) || isset($purchaseOrdersRequests->currency) || isset($purchaseOrdersRequests->payment_terms) || isset($purchaseOrdersRequests->delivery_address))
        <div class="info-section">
            <div class="info-header">معلومات إضافية</div>
            <table class="info-grid">
                <tr>
                    <td class="info-label">الحساب:</td>
                    <td class="info-value">{{ $purchaseOrdersRequests->account->name ?? 'غير محدد' }}</td>
                    <td class="info-label">العملة:</td>
                    <td class="info-value">{{ $purchaseOrdersRequests->currency ?? 'ر.س SAR' }}</td>
                </tr>
                <tr>
                    <td class="info-label">شروط الدفع:</td>
                    <td class="info-value">{{ $purchaseOrdersRequests->payment_terms ?? 'غير محدد' }}</td>
                    <td class="info-label">عنوان التسليم:</td>
                    <td class="info-value">{{ $purchaseOrdersRequests->delivery_address ?? 'غير محدد' }}</td>
                </tr>
            </table>
        </div>
        @endif

        <!-- Items Section -->
        <div class="items-section">
            <div class="section-title">تفاصيل المنتجات والخدمات</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 20%;">المنتج</th>
                        <th style="width: 15%;">الوصف</th>
                        <th style="width: 8%;">الكمية</th>
                        <th style="width: 10%;">سعر الوحدة</th>
                        <th style="width: 8%;">الخصم</th>
                        <th style="width: 8%;">ضريبة 1</th>
                        <th style="width: 8%;">ضريبة 2</th>
                        <th style="width: 12%;">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @if($hasItems)
                        @foreach($items as $item)
                            @php
                                $itemSubtotal = ($item->quantity ?? 0) * ($item->unit_price ?? 0);

                                // حساب خصم العنصر
                                $itemDiscount = 0;
                                if (isset($item->discount_type) && $item->discount_type === 'percentage') {
                                    $itemDiscount = ($itemSubtotal * ($item->discount ?? 0)) / 100;
                                } else {
                                    $itemDiscount = $item->discount ?? 0;
                                }

                                $afterDiscount = $itemSubtotal - $itemDiscount;
                                $itemTax1 = ($afterDiscount * ($item->tax_1 ?? 0)) / 100;
                                $itemTax2 = ($afterDiscount * ($item->tax_2 ?? 0)) / 100;
                                $itemTotal = $afterDiscount + $itemTax1 + $itemTax2;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="product-name">{{ $item->product->name ?? $item->item ?? $item->product_name ?? 'غير محدد' }}</td>
                                <td class="description">{{ $item->description ?? '-' }}</td>
                                <td>{{ number_format($item->quantity ?? 0, 0) }}</td>
                                <td>{{ number_format($item->unit_price ?? 0, 2) }}</td>
                                <td>
                                    @if(isset($item->discount_type) && $item->discount_type === 'percentage')
                                        {{ number_format($item->discount ?? 0, 1) }}%
                                    @else
                                        {{ number_format($item->discount ?? 0, 2) }}
                                    @endif
                                </td>
                                <td>{{ number_format($item->tax_1 ?? 0, 1) }}%</td>
                                <td>{{ number_format($item->tax_2 ?? 0, 1) }}%</td>
                                <td class="font-bold">{{ number_format($itemTotal, 2) }}</td>
                            </tr>
                        @endforeach

                        <!-- Totals Section -->
                        <tr class="subtotal-row">
                            <td colspan="8" class="text-right font-bold">المجموع قبل الخصم والضريبة:</td>
                            <td class="font-bold">{{ number_format($subtotal, 2) }}</td>
                        </tr>

                        @if($totalDiscount > 0)
                            <tr class="discount-row">
                                <td colspan="8" class="text-right font-bold">إجمالي خصومات البنود:</td>
                                <td class="font-bold">-{{ number_format($totalDiscount, 2) }}</td>
                            </tr>
                        @endif

                        @if($additionalDiscount > 0)
                            <tr class="discount-row">
                                <td colspan="8" class="text-right font-bold">خصم إضافي:</td>
                                <td class="font-bold">-{{ number_format($additionalDiscount, 2) }}</td>
                            </tr>
                        @endif

                        @if($adjustmentAmount != 0)
                            <tr class="{{ $adjustmentAmount > 0 ? 'tax-row' : 'discount-row' }}">
                                <td colspan="8" class="text-right font-bold">{{ $purchaseOrdersRequests->adjustment_label ?? 'تسوية' }}:</td>
                                <td class="font-bold">{{ $adjustmentAmount > 0 ? '+' : '' }}{{ number_format($adjustmentAmount, 2) }}</td>
                            </tr>
                        @endif

                        @if($shippingCost > 0)
                            <tr class="shipping-row">
                                <td colspan="8" class="text-right font-bold">تكلفة الشحن:</td>
                                <td class="font-bold">{{ number_format($shippingCost, 2) }}</td>
                            </tr>
                        @endif

                        @if($totalTax1 > 0)
                            <tr class="tax-row">
                                <td colspan="8" class="text-right font-bold">إجمالي الضريبة الأولى:</td>
                                <td class="font-bold">{{ number_format($totalTax1, 2) }}</td>
                            </tr>
                        @endif

                        @if($totalTax2 > 0)
                            <tr class="tax-row">
                                <td colspan="8" class="text-right font-bold">إجمالي الضريبة الثانية:</td>
                                <td class="font-bold">{{ number_format($totalTax2, 2) }}</td>
                            </tr>
                        @endif

                        <tr class="final-total-row">
                            <td colspan="8" class="text-right font-bold">المجموع النهائي:</td>
                            <td class="font-bold">{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="9" class="no-items">
                                <strong>لا توجد منتجات مضافة لهذا الأمر</strong><br>
                                <small>يرجى إضافة منتجات لعرض التفاصيل</small>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Financial Summary -->
        @if($hasItems)
    <div class="financial-summary text-end" style="direction: rtl;">
        <div class="summary-title fw-bold mb-3">ملخص مالي</div>
        <div class="summary-grid">
            <div class="summary-row d-flex justify-content-end mb-2">
                <div class="summary-label me-2">عدد البنود:</div>
                <div class="summary-value">{{ $items->count() }} بند</div>
            </div>
            <div class="summary-row d-flex justify-content-end mb-2">
                <div class="summary-label me-2">إجمالي الكمية:</div>
                <div class="summary-value">{{ number_format($items->sum('quantity'), 0) }} وحدة</div>
            </div>
            <div class="summary-row d-flex justify-content-end mb-2">
                <div class="summary-label me-2">متوسط سعر الوحدة:</div>
                <div class="summary-value">{{ number_format($items->avg('unit_price'), 2) }}</div>
            </div>
            <div class="summary-row d-flex justify-content-end mb-2">
                <div class="summary-label me-2">نسبة الخصم الإجمالية:</div>
                <div class="summary-value">
                    {{ $subtotal > 0 ? number_format((($totalDiscount + $additionalDiscount) / $subtotal) * 100, 2) : 0 }}%
                </div>
            </div>
        </div>
    </div>
@endif

        <!-- Notes Section -->
        @if($purchaseOrdersRequests->notes)
            <div class="notes-section">
                <div class="notes-header">ملاحظات وشروط إضافية</div>
                <div class="notes-content">
                    {{ $purchaseOrdersRequests->notes }}
                </div>
            </div>
        @endif

        <!-- Attachments Section -->
        @if(isset($purchaseOrdersRequests->attachments) && $purchaseOrdersRequests->attachments)
            <div class="info-section">
                <div class="info-header">المرفقات</div>
                <div style="padding: 15px;">
                    <p><strong>عدد المرفقات:</strong> {{ is_array($purchaseOrdersRequests->attachments) ? count($purchaseOrdersRequests->attachments) : 1 }}</p>
                    <small class="text-info">يمكن الوصول للمرفقات من خلال النظام الإلكتروني</small>
                </div>
            </div>
        @endif

        <!-- Signatures Section -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">توقيع المورد</div>
                <small>{{ $purchaseOrdersRequests->supplier->trade_name ?? 'المورد' }}</small>
            </div>
            <div class="signature-box">
                <div class="signature-line">توقيع المشتري</div>
                <small>{{ $purchaseOrdersRequests->creator->name ?? 'المشتري' }}</small>
            </div>
            <div class="signature-box">
                <div class="signature-line">ختم الشركة</div>
                <small>{{ config( 'الشركة') }}</small>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #6c757d; border-top: 1px solid #dee2e6; padding-top: 15px;">
            <p>تم إنشاء هذا المستند تلقائياً في {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>{{ config( 'مؤسسة اعمال خاصة للتجارة') }} - جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>
</html>
