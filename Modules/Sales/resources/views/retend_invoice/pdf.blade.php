<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة مرتجع #{{ $return_invoice->id }}</title>
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

        .return-reason {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #fff3cd;
            border-radius: 4px;
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
                <!-- Receipt Header -->
                <div class="receipt-header">
                    <h1 class="receipt-title">فاتورة مرتجع</h1>
                    <p class="mb-0">مؤسسة أعمال خاصة للتجارة</p>
                    <p class="mb-0">الرياض - الرياض</p>
                    <p>رقم المسؤول: 0509992803</p>
                </div>

                <!-- Invoice To -->
                <div class="invoice-to">
                    <p class="mb-0">العميل: {{ $return_invoice->client->trade_name ?? $return_invoice->client->first_name . ' ' . $return_invoice->client->last_name }}</p>
                    <p class="mb-0">{{ $return_invoice->client->street1 ?? 'غير متوفر' }}</p>
                    <h1 class="mb-0">{{ $return_invoice->client->code ?? 'غير متوفر' }}</h1>
                    <p class="mb-0">الرقم الضريبي: {{ $return_invoice->client->tax_number ?? 'غير متوفر' }}</p>
                    @if($return_invoice->client->mobile)
                        <p class="mb-0">الهاتف: {{ $return_invoice->client->mobile }}</p>
                    @endif
                </div>

                <!-- Invoice Details -->
                <div class="invoice-details">
                    <div class="summary-row">
                        <span>رقم المرتجع:</span>
                        <span>{{ str_pad($return_invoice->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>التاريخ:</span>
                        <span>{{ $return_invoice->created_at->format('Y/m/d') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>الفاتورة الأصلية:</span>
                        <span>{{ $return_invoice->original_invoice_id ?? 'غير متوفر' }}</span>
                    </div>
                </div>

                <!-- Return Reason -->
                <div class="return-reason">
                    <strong>سبب المرتجع:</strong> {{ $return_invoice->return_reason ?? 'لم يتم تحديد سبب' }}
                </div>

                <!-- Invoice Items -->
                <div class="invoice-items">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="45%">الصنف</th>
                                <th width="15%">الكمية</th>
                                <th width="15%">السعر</th>
                                <th width="20%">المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($return_invoice->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
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
                        <span><strong>إجمالي المرتجع:</strong></span>
                        <span><strong>{{ number_format($return_invoice->grand_total ?? 0, 2) }} ر.س</strong></span>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="qr-code">
                    <canvas id="qrcode"></canvas>
                </div>

                <!-- Signature -->
                <div class="signature">
                    <p>توقيع العميل: _______________</p>
                    <p>ختم المحل: _______________</p>
                    <p class="thank-you">شكراً لتعاملكم معنا</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const returnData = `رقم المرتجع: {{ $return_invoice->id }}
التاريخ: {{ $return_invoice->created_at->format('Y/m/d') }}
العميل: {{ $return_invoice->client->trade_name ?? $return_invoice->client->first_name . ' ' . $return_invoice->client->last_name }}
الإجمالي: {{ number_format($return_invoice->grand_total, 2) }} ر.س
رقم الفاتورة الأصلية: {{ $return_invoice->original_invoice_id ?? 'غير متوفر' }}`;

        QRCode.toCanvas(document.getElementById('qrcode'), returnData, {
            width: 150,
            margin: 1
        }, function (error) {
            if (error) console.error(error);
        });

        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>