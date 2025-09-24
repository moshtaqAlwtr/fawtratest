<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة #{{ $invoice->id }}</title>
    <!-- Import Arabic font -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap');

        /* Global Styles */
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
            direction: rtl;
            font-weight: bold;
        }

        /* Receipt Container */
        .receipt-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .receipt {
            width: 80mm;
            /* Standard thermal receipt width */
            max-width: 100%;
            background-color: white;
            padding: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        /* Receipt Header */
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

        /* Invoice To Section */
        .invoice-to {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }

        /* Invoice Details Section */
        .invoice-details {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }

        /* Invoice Items Table */
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

        /* Invoice Summary */
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

        /* QR Code */
        .qr-code {
            margin: 15px 0;
            text-align: center;
        }

        /* Signature */
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

        /* Print Styles */
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
        }

        /* Responsive Styles */
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
                    <h1 class="receipt-title">فاتورة</h1>
                    <p class="mb-0">مؤسسة أعمال خاصة للتجارة</p>
                    <p class="mb-0">الرياض - الرياض</p>
                    <p>رقم المسؤول: 0509992803</p>
                </div>

                <!-- Invoice To -->
                <div class="invoice-to">
                    <p class="mb-0">فاتورة الى: {{ $invoice->client->trade_name ?? $invoice->client->first_name . ' ' . $invoice->client->last_name }}</p>
                    <p class="mb-0">{{ $invoice->client->street1 ?? 'غير متوفر' }}</p>
                    <p class="mb-0">كود العميل: {{ $invoice->client->code ?? 'غير متوفر' }}</p>
                    <p class="mb-0">الرقم الضريبي: {{ $invoice->client->tax_number ?? 'غير متوفر' }}</p>
                    @if($invoice->client->phone)
                    <p class="mb-0">رقم جوال العميل: {{ $invoice->client->phone }}</p>
                    @endif
                </div>

                <!-- Invoice Details -->
                <div class="invoice-details">
                    <div class="summary-row">
                        <span>رقم الفاتورة:</span>
                        <span>{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>تاريخ الفاتورة:</span>
                        <span>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="invoice-items">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="40%">البند</th>
                                <th width="15%">الكمية</th>
                                <th width="20%">السعر</th>
                                <th width="25%">المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr>
                                <td style="text-align: right;">{{ $item->item }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Invoice Summary -->
                <div class="invoice-summary">
                    <div class="summary-row">
                        <span>المجموع الكلي:</span>
                        <span>{{ number_format($invoice->grand_total, 2) }} ر.س</span>
                    </div>

                    @if($invoice->total_discount > 0)
                    <div class="summary-row">
                        <span>الخصم:</span>
                        <span>{{ number_format($invoice->total_discount, 2) }} ر.س</span>
                    </div>
                    @endif

                    @if($invoice->shipping_cost > 0)
                    <div class="summary-row">
                        <span>تكلفة الشحن:</span>
                        <span>{{ number_format($invoice->shipping_cost, 2) }} ر.س</span>
                    </div>
                    @endif

                    @if($invoice->advance_payment > 0)
                    <div class="summary-row">
                        <span>الدفعة المقدمة:</span>
                        <span>{{ number_format($invoice->advance_payment, 2) }} ر.س</span>
                    </div>
                    @endif

                    <div class="summary-row">
                        <span>المبلغ المستحق:</span>
                        <span>{{ number_format($invoice->due_value, 2) }} ر.س</span>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="qr-code">
                    {!! $qrCodeSvg !!}
                </div>

                <!-- Signature -->
                <div class="signature">
                    <p>الاسم: ________________</p>
                    <p>التوقيع: _______________</p>
                    <p class="thank-you">شكراً لتعاملكم معنا</p>
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
