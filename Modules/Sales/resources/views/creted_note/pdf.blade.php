<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>إشعار دائن</title>
    <style>
        * {
            font-family: Arial, sans-serif !important;
            direction: rtl;
            unicode-bidi: embed;
        }

        body {
            font-family: Arial, sans-serif !important;
            direction: rtl;
            padding: 20px;
            font-size: 13px;
            line-height: 1.6;
            unicode-bidi: embed;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: right;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .company-info {
            margin-bottom: 20px;
            text-align: center;
        }

        .invoice-details {
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .section-title {
            background-color: #f5f5f5;
            padding: 5px;
            margin: 10px 0;
            font-weight: bold;
        }

        .numbers {
            font-family: Arial, sans-serif !important;
            direction: ltr;
            display: inline-block;
        }

        @page {
            margin: 1cm;
            size: A4 portrait;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
</head>
<body>
    <div class="company-info">
        @if(isset($company_logo))
            <img src="{{ $company_logo }}" alt="شعار الشركة">
        @endif
        <h2>مؤسسة اعمال خاصة للتجارة</h2>
        <p>الرياض</p>
        <p>هاتف: <span class="numbers">0509992803</span></p>
    </div>

    <div class="header">
        <h1>إشعار دائن</h1>
        <p>{{ $credit->client->trade_name ?? $credit->client->first_name . ' ' . $credit->client->last_name }}</p>
        <p>{{ $credit->client->street1 ?? 'غير متوفر' }}</p>
        <p>{{ $credit->client->mobile ?? 'غير متوفر' }}</p>
    </div>


    <h3 class="section-title">تفاصيل الإشعار الدائن:</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>البند</th>
                <th>الوصف</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>الخصم</th>
                <th>المجموع</th>
            </tr>
        </thead>
        <tbody>
            @foreach($credit->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->item }}</td>
                <td>{{ $item->description ?? '-' }}</td>
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
        <p>المجموع الفرعي: {{ number_format($credit->subtotal ?? 0, 2) }} {!! $currencySymbol !!}</p>

        @if(($credit->total_discount ?? 0) > 0)
            <p>الخصم: {{ number_format($credit->total_discount, 2) }} {!! $currencySymbol !!}</p>
        @endif

            @if($TaxsInvoice->isNotEmpty())
    @foreach($TaxsInvoice as $TaxInvoice)
        <p> {{ $TaxInvoice->name }} ({{ $TaxInvoice->rate }}%): 
            {{ number_format($TaxInvoice->value ?? 0, 2) }} {!! $currencySymbol !!}
        </p>
    @endforeach
@else
    {{-- <p>الضريبة: 0.00 {!! $currencySymbol !!}</p> --}}
@endif
        <p style="font-size: 14px; margin-top: 10px;">المجموع الكلي: {{ number_format($credit->grand_total ?? 0, 2) }} {!! $currencySymbol !!}</p>
        <p>{{ $amount_in_words ?? '' }}</p>
    </div>

    @if($credit->notes)
    <div class="notes">
        <h3 class="section-title">ملاحظات:</h3>
        <p>{{ $credit->notes }}</p>
    </div>
    @endif

    <div class="qrcode">
        <canvas id="qrcode"></canvas>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">توقيع المحاسب</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">توقيع المدير المالي</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">ختم الشركة</div>
        </div>
    </div>



    <script>
        const creditData = `
            رقم الإشعار الدائن: {{ $credit->credit_number }}
            التاريخ: {{ $credit->created_at->format('Y/m/d') }}
            العميل: {{ $credit->client->trade_name ?? $credit->client->first_name . ' ' . $credit->client->last_name }}
            الإجمالي: {{ number_format($credit->grand_total, 2) }} ر.س
            رقم الفاتورة المرجعية: {{ $credit->reference_invoice_number ?? 'غير متوفر' }}
            الرقم الضريبي: {{ $credit->client->tax_number ?? 'غير متوفر' }}
        `;

        QRCode.toCanvas(document.getElementById('qrcode'), creditData, {
            width: 150,
            margin: 1
        }, function (error) {
            if (error) console.error(error);
            console.log('QR Code generated successfully!');
        });
    </script>
</body>
</html>
