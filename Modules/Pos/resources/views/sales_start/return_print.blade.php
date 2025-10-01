<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة إرجاع #{{ $invoice->id }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap');

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
            direction: rtl;
            font-weight: bold;
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

            .qr-code svg {
                width: 70px !important;
                height: 70px !important;
            }

            .no-print {
                display: none !important;
            }
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
            border-bottom: 2px dashed #ccc;
            margin-bottom: 15px;
            text-align: center;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #dc3545;
        }

        .return-badge {
            background-color: #dc3545;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 10px;
            display: inline-block;
        }

        .original-invoice-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            border-left: 4px solid #17a2b8;
        }

        .invoice-to {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
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
            border-top: 2px dashed #dc3545;
            padding-top: 10px;
            margin-bottom: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 16px;
            color: #dc3545;
            border-top: 1px solid #dc3545;
            padding-top: 5px;
            margin-top: 10px;
        }

        .negative-amount {
            color: #dc3545;
            font-weight: bold;
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
            color: #666;
        }

        .return-notice {
            background-color: #fff3cd;
            border: 1px dashed #ffc107;
            padding: 10px;
            margin: 10px 0;
            text-align: center;
            border-radius: 5px;
        }

        @media (max-width: 576px) {
            .receipt {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="receipt-container">
            <div class="receipt">
                <!-- Receipt Header -->
                <div class="receipt-header">
                    <div class="return-badge">فاتورة إرجاع</div>
                    <h1 class="receipt-title">فاتورة إرجاع ضريبية</h1>
                    <p class="mb-0">مؤسسة الطيب الافضل للتجارة</p>
                    <p class="mb-0">الرقم الضريبي: 310213567700003</p>
                    <p class="mb-0">الرياض - الرياض</p>
                    <p>رقم المسؤول: 0509992803</p>
                </div>

                <!-- Original Invoice Info -->
                @if($originalInvoice)
                <div class="original-invoice-info">
                    <strong>معلومات الفاتورة الأصلية:</strong><br>
                    <small>رقم الفاتورة: {{ $originalInvoice->code }}</small><br>
                    <small>تاريخ الفاتورة: {{ $originalInvoice->invoice_date }}</small><br>
                    <small>قيمة الفاتورة الأصلية: {{ number_format($originalInvoice->grand_total, 2) }} ر.س</small>
                </div>
                @endif

                <!-- Invoice To -->
                <div class="invoice-to">
                    <p class="mb-0">إرجاع إلى:
                        {{ $invoice->client->trade_name ?? $invoice->client->first_name . ' ' . $invoice->client->last_name }}
                    </p>
                    <p class="mb-0">{{ $invoice->client->street1 ?? 'غير متوفر' }}</p>
                    <p class="mb-0">كود العميل: {{ $invoice->client->code ?? 'غير متوفر' }}</p>
                    <p class="mb-0">الرقم الضريبي: {{ $invoice->client->tax_number ?? 'غير متوفر' }}</p>
                    @if ($invoice->client->phone)
                        <p class="mb-0">رقم جوال العميل: {{ $invoice->client->phone }}</p>
                    @endif
                </div>

                <!-- Invoice Details -->
                <div class="invoice-details">
                    <div class="summary-row">
                        <span>رقم فاتورة الإرجاع:</span>
                        <span>{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>تاريخ الإرجاع:</span>
                        <span>{{ $invoice->invoice_date ?? $invoice->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>رقم الفاتورة الأصلية:</span>
                        <span>{{ $originalInvoice->code ?? $invoice->reference_number }}</span>
                    </div>
                </div>

                <!-- Return Notice -->
                <div class="return-notice">
                    <strong>تنبيه:</strong> هذه فاتورة إرجاع - المبالغ ستُخصم من إجمالي المبيعات
                </div>

                <!-- Invoice Items -->
                <div class="invoice-items">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="35%">المنتج</th>
                                <th width="10%">الكمية</th>
                                <th width="15%">السعر</th>
                                <th width="10%">الضريبة</th>
                                <th width="25%">المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $index => $item)
                                @php
                                    $unit_price = $item->unit_price; // السعر بدون ضريبة
                                    $quantity = $item->quantity;
                                    $subtotal = $unit_price * $quantity;
                                    $tax = $item->tax_1 ?? 0; // الضريبة المحفوظة
                                    $total = $item->total; // الإجمالي شامل الضريبة
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td style="text-align: right;">{{ $item->item }}</td>
                                    <td>-{{ $item->quantity }}</td>
                                    <td>{{ number_format($unit_price, 2) }}</td>
                                    <td>{{ number_format($tax, 2) }}</td>
                                    <td class="negative-amount">-{{ number_format($total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Invoice Summary -->
                <div class="invoice-summary">
                    <div class="summary-row">
                        <span>المجموع الفرعي:</span>
                        <span class="negative-amount">-{{ number_format($invoice->subtotal ?? 0, 2) }} ر.س</span>
                    </div>

                    <div class="summary-row">
                        <span>ضريبة القيمة المضافة (15%):</span>
                        <span class="negative-amount">-{{ number_format($invoice->tax_total ?? 0, 2) }} ر.س</span>
                    </div>

                    <div class="summary-row total">
                        <span>إجمالي الإرجاع:</span>
                        <span class="negative-amount">-{{ number_format($invoice->grand_total, 2) }} ر.س</span>
                    </div>

                    <div class="summary-row">
                        <span>المبلغ المسترد للعميل:</span>
                        <span class="negative-amount">{{ number_format($invoice->grand_total, 2) }} ر.س</span>
                    </div>
                </div>

                <div class="signature">
                    <p>الاسم: ________________</p>
                    <p>التوقيع: _______________</p>
                    <p class="thank-you">نعتذر عن أي إزعاج - شكراً لتفهمكم</p>
                </div>

                <div class="qr-code">
                    {!! $qrCodeSvg !!}
                </div>
            </div>
        </div>
    </div>

    <script>
        // طباعة تلقائية عند تحميل الصفحة
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };

        // إعادة الطباعة عند محاولة الإغلاق
        window.onbeforeunload = function() {
            window.print();
        };
    </script>
</body>
</html>