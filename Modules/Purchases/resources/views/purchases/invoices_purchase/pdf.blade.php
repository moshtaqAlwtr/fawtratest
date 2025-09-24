<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>فاتورة مشتريات</title>
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

        .info-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px;
            border-bottom: 1px dotted #999;
        }

        .products-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .products-table th,
        .products-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .totals-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 5px;
            border: 1px solid #000;
        }

        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>فاتورة مشتريات</h2>
        <p>{{ $purchaseInvoice->date }}</p>
        <p>مؤسسة اعمال عتمة للتجارة</p>
        <p>رقم الفاتورة: {{ $purchaseInvoice->code }}</p>
        <p>الحالة:
            {{ $purchaseInvoice->status == 1 ? ' مدفوعة' : ($purchaseInvoice->status == 2 ? 'مدفوعة' : 'قيد المراجعة') }}
        </p>
    </div>

    <table class="info-table">
        <tr>
            <td>المورد:</td>
            <td>{{ optional($purchaseInvoice->supplier)->trade_name }}</td>
            <td>رقم الفاتورة:</td>
            <td>{{ $purchaseInvoice->code }}</td>
        </tr>
        <tr>
            <td>شروط الدفع:</td>
            <td>{{ $purchaseInvoice->payment_terms }} يوم</td>
            <td>تاريخ الفاتورة:</td>
            <td>{{ $purchaseInvoice->date }}</td>
        </tr>
        <tr>
            <td>طريقة الدفع:</td>
            <td>{{ $purchaseInvoice->payment_method }}</td>
            <td>الحساب:</td>
            <td>{{ optional($purchaseInvoice->account)->name }}</td>
        </tr>
        <tr>
            <td>تاريخ الاستلام:</td>
            <td>{{ $purchaseInvoice->received_date }}</td>
            <td>الرقم المرجعي:</td>
            <td>{{ $purchaseInvoice->reference_number }}</td>
        </tr>
    </table>

    <h3>تفاصيل المنتجات:</h3>
    <table class="products-table">
        <thead>
            <tr>
                <th>#</th>
                <th>المنتج</th>
                <th>سعر الوحدة</th>
                <th>الخصم</th>
                <th>الكمية</th>
                <th>المجموع قبل الضريبة</th>
                <th>الضريبة</th>
                <th>المجموع شامل الضريبة</th>
            </tr>
        </thead>
        <tbody>
            @if ($purchaseInvoice->items && $purchaseInvoice->items->count() > 0)
                @php
                    $totalTaxAmount = 0;
                @endphp
                @foreach ($purchaseInvoice->items as $item)
                    @php
                        // حساب المجموع قبل الضريبة (بعد الخصم)
                        $subtotalBeforeTax = ($item->unit_price * $item->quantity) - (($item->unit_price * $item->quantity * $item->discount) / 100);

                        // حساب مبلغ الضريبة للصف
                        $taxRate = $item->tax_rate ?? 15; // نسبة الضريبة (افتراضي 15%)
                        $itemTaxAmount = ($subtotalBeforeTax * $taxRate) / 100;

                        // المجموع شامل الضريبة
                        $totalWithTax = $subtotalBeforeTax + $itemTaxAmount;

                        // إضافة إلى إجمالي الضريبة
                        $totalTaxAmount += $itemTaxAmount;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ optional($item->product)->name }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ number_format($item->discount, 2) }}%</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($subtotalBeforeTax, 2) }}</td>
                        <td>{{ number_format($itemTaxAmount, 2) }} ({{ $taxRate }}%)</td>
                        <td>{{ number_format($totalWithTax, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8">لا توجد منتجات مضافة</td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>المجموع الفرعي (قبل الضريبة):</td>
            <td>{{ number_format($purchaseInvoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>إجمالي الخصم:</td>
            <td>{{ number_format($purchaseInvoice->total_discount, 2) }}</td>
        </tr>

        @php
            $currency = $account_setting->currency ?? 'SAR';
            $currencySymbol =
                $currency == 'SAR' || empty($currency)
                    ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15">'
                    : $currency;
        @endphp

        <tr>
            <td>إجمالي الضريبة:</td>
            <td>{{ number_format($totalTaxAmount ?? 0, 2) }} {!! $currencySymbol !!}</td>
        </tr>

        <tr>
            <td>الشحن:</td>
            <td>{{ number_format($purchaseInvoice->shipping_cost, 2) }}</td>
        </tr>
        <tr>
            <td>الدفعة المقدمة:</td>
            <td>{{ number_format($purchaseInvoice->advance_payment, 2) }}</td>
        </tr>
        <tr>
            <td>المجموع النهائي:</td>
            <td>{{ number_format($purchaseInvoice->grand_total, 2) }}</td>
        </tr>
        <tr>
            <td>المبلغ المستحق:</td>
            <td>{{ number_format($purchaseInvoice->due_amount, 2) }}</td>
        </tr>
    </table>

    @if ($purchaseInvoice->notes)
        <div class="notes">
            <strong>ملاحظات:</strong> {{ $purchaseInvoice->notes }}
        </div>
    @endif

    <div class="signatures">
        <div>
            <p>توقيع المورد</p>
            <div style="margin-top: 40px; border-top: 1px solid #000; width: 150px;"></div>
        </div>
        <div>
            <p>توقيع المستلم</p>
            <div style="margin-top: 40px; border-top: 1px solid #000; width: 150px;"></div>
        </div>
        <div>
            <p>ختم الشركة</p>
            <div style="margin-top: 40px; border-top: 1px solid #000; width: 150px;"></div>
        </div>
    </div>
</body>

</html>