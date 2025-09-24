<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>عرض سعر #{{ $quote->quotes_number }}</title>
    <style>
        body {
            direction: rtl;
            font-family: 'Cairo', sans-serif;
            padding: 10px;
            margin: 0;
            font-size: 12px;
        }
      
    @media print {
        body {
            background: white !important;
            color: black !important;
            font-size: 12pt;
        }
        .no-print {
            display: none !important;
        }
        .print-only {
            display: block !important;
        }
        /* أي أنماط إضافية تحتاجها للطباعة */
    }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 16px;
        }

        .header p {
            margin: 3px 0;
        }

        .info-grid {
            width: 100%;
            margin: 10px 0;
        }

        .info-grid td {
            padding: 3px;
        }

        .info-label {
            font-weight: bold;
            width: 100px;
        }

        .info-value {
            border-bottom: 1px dotted #000;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .data-table th {
            background-color: #f5f5f5;
        }

        .section-title {
            margin: 10px 0 5px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            font-size: 14px;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
        }

        .signatures {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 150px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 20px;
            padding-top: 3px;
        }

        .qrcode {
            text-align: center;
            margin-top: 10px;
        }

        .qrcode img {
            max-width: 100px;
            height: auto;
        }

        .totals {
            margin: 15px 0;
            text-align: left;
        }

        .totals p {
            margin: 5px 0;
            font-weight: bold;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
</head>
<body>
    <div class="header">
        <h1>عرض سعر</h1>
        <p>{{ $quote->client->trade_name ?? $quote->client->first_name . ' ' . $quote->client->last_name }}</p>
        <p>{{ $quote->client->street1 ?? 'غير متوفر' }}</p>
        <p>{{ $quote->client->mobile ?? 'غير متوفر' }}</p>
    </div>

    <table class="info-grid" cellpadding="0" cellspacing="0">
        <tr>
            <td class="info-label">رقم عرض السعر:</td>
            <td class="info-value">{{ str_pad($quote->quotes_number, 5, '0', STR_PAD_LEFT) }}</td>
            <td class="info-label">تاريخ العرض:</td>
            <td class="info-value">{{ $quote->quote_date }}</td>
        </tr>
        <tr>
            <td class="info-label">العميل:</td>
            <td class="info-value">{{ $quote->client->trade_name ?? $quote->client->first_name . ' ' . $quote->client->last_name }}</td>
            <td class="info-label">العنوان:</td>
            <td class="info-value">{{ $quote->client->street2 ?? 'غير متوفر' }}</td>
        </tr>
    </table>

    <h3 class="section-title">تفاصيل عرض السعر:</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>البند</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>الخصم</th>
                <th>المجموع</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->item }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->discount, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
         @php
        $currency = $account_setting->currency ?? 'SAR';
        $currencySymbol =
            $currency == 'SAR' || empty($currency)
                ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15">'
                : $currency;
    @endphp
        <!-- الضريبة -->
        @if($TaxsInvoice->isNotEmpty())
    @foreach($TaxsInvoice as $TaxInvoice)
        <p> {{ $TaxInvoice->name }} ({{ $TaxInvoice->rate }}%): 
            {{ number_format($TaxInvoice->value ?? 0, 2) }} {!! $currencySymbol !!}
        </p>
    @endforeach
@else
    {{-- <p>الضريبة: 0.00 {!! $currencySymbol !!}</p> --}}
@endif

        <!-- الشحن -->
        @if(($quote->shipping_cost ?? 0) > 0)
            <p>تكلفة الشحن: {{ number_format($quote->shipping_cost, 2) }} {!! $currencySymbol !!}</p>
        @endif

        <!-- الخصم -->
        @if(($quote->total_discount ?? 0) > 0)
            <p>الخصم: {{ number_format($quote->total_discount, 2) }} {!! $currencySymbol !!}</p>
        @endif

        <!-- المجموع الكلي -->
        <p>المجموع الكلي: {{ number_format($quote->grand_total ?? 0, 2) }} {!! $currencySymbol !!}</p>
    </div>

    <!-- قسم QR Code -->
    <div class="qrcode">
        <canvas id="qrcode"></canvas>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">توقيع البائع</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">توقيع العميل</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">ختم الشركة</div>
        </div>
    </div>

    <script>
        const quoteData = `
            رقم عرض السعر: {{ $quote->quotes_number }}
            التاريخ: {{ $quote->quote_date }}
            العميل: {{ $quote->client->trade_name ?? $quote->client->first_name . ' ' . $quote->client->last_name }}
            الإجمالي: {{ number_format($quote->grand_total, 2) }} ر.س
        `;

        QRCode.toCanvas(document.getElementById('qrcode'), quoteData, {
            width: 150,
            margin: 1
        }, function (error) {
            if (error) console.error(error);
            console.log('QR Code generated successfully!');
        });
    </script>
</body>
</html>
