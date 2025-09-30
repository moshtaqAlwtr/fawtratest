<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إشعار دائن #{{ $credit->credit_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap');

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
            direction: rtl;
            font-weight: bold;
        }

        .receipt-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .receipt {
            width: 80mm;
            max-width: 100%;
            background-color: white;
            padding: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        .receipt-header {
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
            margin-bottom: 10px;
            text-align: center;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .invoice-to {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }

        .invoice-to h1 {
            font-size: 28px;
            text-align: center;
            margin: 8px 0;
        }

        .invoice-details {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }

        .invoice-items {
            margin-bottom: 10px;
        }

        .table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 12px;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            text-align: center;
            padding: 5px;
            border-bottom: 1px dashed #ddd;
        }

        .table th {
            background-color: #f5f5f5;
            border-bottom: 1px solid #333;
        }

        .invoice-summary {
            border-top: 1px dashed #ccc;
            padding-top: 10px;
            margin-bottom: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .notes {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 12px;
        }

        .qr-code {
            margin: 15px 0;
            text-align: center;
        }

        .signature {
            margin: 15px auto 0;
            padding-top: 10px;
            border-top: 1px dashed #333;
            width: 90%;
            text-align: center;
        }

        .thank-you {
            font-style: italic;
            margin-top: 5px;
        }

        @media print {
            body {
                background-color: white;
                padding: 0;
                margin: 0;
                display: block !important;
                width: 80mm !important;
                font-weight: bold !important;
            }

            .receipt {
                width: 100%;
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0 auto !important;
            }

            .receipt-container {
                min-height: auto;
            }

            .qr-code canvas {
                width: 70px !important;
                height: 70px !important;
            }
        }

        @media (max-width: 576px) {
            .receipt {
                width: 100%;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="receipt-container">
            <div class="receipt">
                <div class="receipt-header">
                    <h1 class="receipt-title">إشعار دائن</h1>
                    <p class="mb-0">مؤسسة أعمال خاصة للتجارة</p>
                    <p class="mb-0">الرياض - الرياض</p>
                    <p>رقم المسؤول: 0509992803</p>
                </div>

                <div class="invoice-to">
                    <p class="mb-0">فاتورة الى: {{ $credit->client->trade_name ?? $credit->client->first_name . ' ' . $credit->client->last_name }}</p>
                    <p class="mb-0">{{ $credit->client->street1 ?? 'غير متوفر' }}</p>
                    <h1 class="mb-0">{{ $credit->client->code ?? 'غير متوفر' }}</h1>
                    <p class="mb-0">الرقم الضريبي: {{ $credit->client->tax_number ?? 'غير متوفر' }}</p>
                    @if($credit->client->mobile)
                        <p class="mb-0">رقم جوال العميل: {{ $credit->client->mobile }}</p>
                    @endif
                </div>

                <div class="invoice-details">
                    <div class="summary-row">
                        <span>رقم الإشعار:</span>
                        <span>{{ $credit->credit_number }}</span>
                    </div>
                    <div class="summary-row">
                        <span>تاريخ الإشعار:</span>
                        <span>{{ $credit->created_at->format('Y/m/d') }}</span>
                    </div>
                    @if($credit->reference_invoice_number)
                    <div class="summary-row">
                        <span>الفاتورة المرجعية:</span>
                        <span>{{ $credit->reference_invoice_number }}</span>
                    </div>
                    @endif
                </div>

                <div class="invoice-items">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="45%">المنتج</th>
                                <th width="15%">الكمية</th>
                                <th width="15%">السعر</th>
                                <th width="20%">المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($credit->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td style="text-align: right;">{{ $item->item }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @php
                    $currency = $account_setting->currency ?? 'SAR';
                    $currencySymbol = $currency == 'SAR' || empty($currency) ? 'ر.س' : $currency;
                @endphp

                <div class="invoice-summary">
                    <div class="summary-row">
                        <span>المجموع الكلي:</span>
                        <span>{{ number_format($credit->subtotal ?? 0, 2) }} {{ $currencySymbol }}</span>
                    </div>

                    @if(($credit->total_discount ?? 0) > 0)
                    <div class="summary-row">
                        <span>الخصم:</span>
                        <span>{{ number_format($credit->total_discount, 2) }} {{ $currencySymbol }}</span>
                    </div>
                    @endif

                    @if($TaxsInvoice->isNotEmpty())
                        @foreach($TaxsInvoice as $TaxInvoice)
                        <div class="summary-row">
                            <span>{{ $TaxInvoice->name }} ({{ $TaxInvoice->rate }}%):</span>
                            <span>{{ number_format($TaxInvoice->value ?? 0, 2) }} {{ $currencySymbol }}</span>
                        </div>
                        @endforeach
                    @endif

                    <div class="summary-row">
                        <span><strong>المبلغ المستحق:</strong></span>
                        <span><strong>{{ number_format($credit->grand_total ?? 0, 2) }} {{ $currencySymbol }}</strong></span>
                    </div>
{{--
                    @if($amount_in_words)
                    <div style="margin-top: 8px; font-size: 11px; text-align: center;">
                        {{ $amount_in_words }}
                    </div>
                    @endif --}}
                </div>

                @if($credit->notes)
                <div class="notes">
                    <strong>ملاحظات:</strong><br>
                    {{ $credit->notes }}
                </div>
                @endif

                <div class="signature">
                    <p>الاسم: ________________</p>
                    <p>التوقيع: _______________</p>
                    <p class="thank-you">شكراً لتعاملكم معنا</p>
                </div>

                <div class="qr-code">
                    <canvas id="qrcode"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const creditData = `رقم الإشعار الدائن: {{ $credit->credit_number }}
التاريخ: {{ $credit->created_at->format('Y/m/d') }}
العميل: {{ $credit->client->trade_name ?? $credit->client->first_name . ' ' . $credit->client->last_name }}
الإجمالي: {{ number_format($credit->grand_total, 2) }} ر.س
رقم الفاتورة المرجعية: {{ $credit->reference_invoice_number ?? 'غير متوفر' }}
الرقم الضريبي: {{ $credit->client->tax_number ?? 'غير متوفر' }}`;


    </script>
</body>
</html>