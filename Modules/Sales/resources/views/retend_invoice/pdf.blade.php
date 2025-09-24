<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>فاتورة مرتجع #{{ $return_invoice->id }}</title>
      <style>
        /* أنماط الصفحة الرئيسية - جميع النصوص عريضة */
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            background: white;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-size: 14px !important; /* زيادة حجم الخط العام */
            font-weight: bold !important;
        }

        .invoice-main-container {
            width: 80mm;
            padding: 8px; /* زيادة الحشوة قليلاً */
            margin: 0 auto;
            text-align: center;
        }

        /* أنماط الرأس */
        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #333;
        }

        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 20px; /* زيادة حجم العنوان الرئيسي */
            font-weight: bold;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 16px; /* زيادة حجم النص الثانوي */
        }

        .header p, .header h4 {
            margin: 4px 0;
            font-size: 14px; /* زيادة حجم النص */
        }

        /* أنماط معلومات العميل */
        .client-info {
            margin: 10px 0;
            text-align: right;
            padding-bottom: 8px;
            border-bottom: 1px dashed #333;
        }

        .client-info h3 {
            margin: 8px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .qrcode {
            text-align: center;
            margin-top: 10px;
        }

        .qrcode img {
            max-width: 100px;
            height: auto;
        }
        .client-info p {
            margin: 5px 0;
            font-size: 14px;
        }

        .invoice-meta {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #ccc;
            font-size: 14px;
        }

        /* أنماط جدول العناصر */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px auto;
            font-size: 14px; /* زيادة حجم خط الجدول */
        }

        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            padding: 8px 5px; /* زيادة الحشوة */
            border-bottom: 1px solid #333;
            text-align: center;
        }

        .items-table td {
            padding: 8px 5px;
            border-bottom: 1px dashed #ddd;
            text-align: center;
            font-weight: bold;
        }

        /* أنماط قسم المجموع */
        .total-section {
            margin: 15px auto 0;
            padding-top: 8px;
            border-top: 1px dashed #333;
            width: 100%;
            font-size: 14px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 6px 0;
            padding: 4px 0;
        }

        .total-row:last-child {
            border-top: 1px dashed #333;
            padding-top: 8px;
        }

        /* أنماط التوقيع والباركود */
        .signature {
            margin: 15px auto 0;
            padding-top: 10px;
            border-top: 1px dashed #333;
            width: 90%;
            text-align: center;
            font-size: 14px;
        }

        .barcode {
            text-align: center;
            margin: 10px auto;
            padding: 8px 0;
        }

        .thank-you {
            font-style: italic;
            margin-top: 5px;
            font-size: 14px;
        }

        /* أنماط الطباعة */
        @media print {
            body {
                display: block !important;
                width: 80mm !important;
                font-size: 14px !important;
                background: white !important;
                font-weight: bold !important;
            }

            .invoice-main-container {
                box-shadow: none !important;
                margin: 0 auto !important;
                padding: 5px !important;
            }

            .barcode svg {
                width: 70px !important;
                height: 70px !important;
            }
        }
    </style>
</head>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
<body>
     <div class="invoice-main-container">
        <div class="invoice-content">
        مؤسسة أعمال خاصة للتجارة<br>
       
        0509992803 | الرياض
    </div>

    <div class="header">
        <h1>فاتورة مرتجع</h1>
        <p>رقم: {{ str_pad($return_invoice->id, 5, '0', STR_PAD_LEFT) }}</p>
        <p>التاريخ: {{ $return_invoice->created_at->format('Y/m/d') }}</p>
    </div>

    <div class="divider"></div>

    <table class="info-grid" cellpadding="0" cellspacing="0">
        <tr>
            <td class="info-label">العميل:</td>
            <td class="info-value" colspan="3">{{ $return_invoice->client->trade_name ?? $return_invoice->client->first_name . ' ' . $return_invoice->client->last_name }}</td>
        </tr>
        <tr>
            <td class="info-label">الهاتف:</td>
            <td class="info-value">{{ $return_invoice->client->mobile ?? 'غير متوفر' }}</td>
            <td class="info-label">الفاتورة الأصلية:</td>
            <td class="info-value">{{ $return_invoice->id ?? "" }}</td>
        </tr>
        <tr>
            <td class="info-label">الرقم الضريبي:</td>
            <td class="info-value" colspan="3">{{ $return_invoice->client->tax_number ?? 'غير متوفر' }}</td>
        </tr>
    </table>
    <div class="return-reason">
        <strong>سبب المرتجع:</strong> {{ $return_invoice->return_reason ?? 'لم يتم تحديد سبب' }}
    </div>
    @php
    $currency = $account_setting->currency ?? 'SAR';
    $currencySymbol = $currency == 'SAR' || empty($currency) ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">' : $currency;
@endphp
    <div class="section-title">تفاصيل المرتجع</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="10%">#</th>
                <th width="40%">الصنف</th>
                <th width="10%">الكمية</th>
                <th width="20%">السعر</th>
                <th width="20%">المجموع</th>
            </tr>
        </thead>
        <tbody>
            @foreach($return_invoice->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->item }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="totals">
        {{-- <p>الإجمالي قبل الضريبة: {{ number_format($return_invoice->subtotal ?? 0, 2) }} {!! $currencySymbol !!}</p>
        <p>ضريبة القيمة المضافة: {{ number_format($return_invoice->tax_total ?? 0, 2) }} {!! $currencySymbol !!}</p>
        @if(($return_invoice->total_discount ?? 0) > 0)
            <p>الخصم: {{ number_format($return_invoice->total_discount, 2) }} {!! $currencySymbol !!}</p>
        @endif --}}
        <p><strong>إجمالي المرتجع:</strong> {{ number_format($return_invoice->grand_total ?? 0, 2) }} {!! $currencySymbol !!}</p>
    </div>

    <div class="divider"></div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">توقيع العميل</div>
        </div>
       
        <br>
        <div class="signature-box">
            <div class="signature-line">ختم المحل</div>
        </div>
    </div>
    <br>
    <div class="footer">
        شكراً لتعاملكم معنا<br>
         <br>
        {{ date('Y/m/d H:i') }}
        <br>
        <div class="qrcode">
            <canvas id="qrcode"></canvas>
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
<script>
    const returnData = `
        رقم المرتجع: {{ $return_invoice->id }}
        التاريخ: {{ $return_invoice->created_at->format('Y/m/d') }}
        العميل: {{ $return_invoice->client->trade_name ?? $return_invoice->client->first_name . ' ' . $return_invoice->client->last_name }}
        الإجمالي: {{ number_format($return_invoice->grand_total, 2) }} ر.س
        رقم الفاتورة الأصلية: {{ $return_invoice->original_invoice_id }}
    `;

    QRCode.toCanvas(document.getElementById('qrcode'), returnData, {
        width: 150,
        margin: 1
    }, function (error) {
        if (error) console.error(error);
        console.log('QR Code generated successfully!');
    });
</script>
</body>
</html>