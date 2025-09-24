<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>طلب شراء - {{ $purchaseOrder->code ?? $purchaseOrder->id }}</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
            size: A4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
            direction: rtl;
        }

        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
        }

        .company-details {
            font-size: 11px;
            color: #666;
            line-height: 1.5;
        }

        .document-title {
            text-align: center;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
        }

        .order-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .order-info-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .info-title {
            font-weight: bold;
            color: #1e40af;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            align-items: center;
        }

        .info-label {
            font-weight: 600;
            color: #475569;
            min-width: 100px;
        }

        .info-value {
            color: #1e293b;
            flex: 1;
            text-align: left;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .items-table thead {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
        }

        .items-table th {
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
        }

        .items-table td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .items-table tbody tr:hover {
            background: #f1f5f9;
        }

        .total-section {
            margin-top: 20px;
            text-align: left;
            direction: ltr;
        }

        .total-box {
            background: #f8fafc;
            border: 2px solid #2563eb;
            border-radius: 8px;
            padding: 20px;
            width: 300px;
            margin-left: auto;
            direction: rtl;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .total-final {
            border-top: 2px solid #2563eb;
            padding-top: 10px;
            font-weight: bold;
            font-size: 16px;
            color: #1e40af;
        }

        .notes-section {
            margin-top: 30px;
        }

        .notes-title {
            font-weight: bold;
            color: #1e40af;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .notes-content {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            min-height: 60px;
            line-height: 1.6;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 11px;
            color: #666;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 50px;
        }

        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 40px;
            color: #1e40af;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 10px;
            height: 40px;
        }

        .page-break {
            page-break-after: always;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(37, 99, 235, 0.05);
            z-index: -1;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="watermark">طلب شراء</div>

    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <div class="company-name">{{ $company['name'] }}</div>
            <div class="company-details">
                {{ $company['address'] }}<br>
                هاتف: {{ $company['phone'] }} | بريد إلكتروني: {{ $company['email'] }}
            </div>
        </div>

        <div class="document-title">
            طلب شراء
        </div>
    </div>

    <!-- Order Information -->
    <div class="order-info">
        <div class="order-info-section">
            <div class="info-box">
                <div class="info-title">معلومات الطلب</div>
                <div class="info-row">
                    <span class="info-label">رقم الطلب:</span>
                    <span class="info-value">{{ $purchaseOrder->code ?? 'غير محدد' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">عنوان الطلب:</span>
                    <span class="info-value">{{ $purchaseOrder->title }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">تاريخ الطلب:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('Y-m-d') }}</span>
                </div>
                @if($purchaseOrder->due_date)
                <div class="info-row">
                    <span class="info-label">تاريخ الاستحقاق:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($purchaseOrder->due_date)->format('Y-m-d') }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="order-info-section">
            <div class="info-box">
                <div class="info-title">معلومات إضافية</div>
                <div class="info-row">
                    <span class="info-label">تم الإنشاء بواسطة:</span>
                    <span class="info-value">{{ $purchaseOrder->creator->name ?? 'غير محدد' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">تاريخ الإنشاء:</span>
                    <span class="info-value">{{ $purchaseOrder->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">حالة الطلب:</span>
                    <span class="info-value">{{ $purchaseOrder->status ?? 'جديد' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 8%">#</th>
                <th style="width: 30%">المنتج</th>
                <th style="width: 15%">الكمية</th>
                <th style="width: 15%">سعر الوحدة</th>
                <th style="width: 17%">المجموع</th>
                <th style="width: 15%">ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: right;">{{ $item->item }}</td>
                <td>{{ number_format($item->quantity, 0) }}</td>
                <td>{{ number_format($item->price, 2) }} ر.س</td>
                <td>{{ number_format($item->total, 2) }} ر.س</td>
                <td>-</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total Section -->
    <div class="total-section">
        <div class="total-box">
            <div class="total-row">
                <span>المجموع الفرعي:</span>
                <span>{{ number_format($totalAmount, 2) }} ر.س</span>
            </div>
            <div class="total-row">
                <span>ضريبة القيمة المضافة (15%):</span>
                <span>{{ number_format($totalAmount * 0.15, 2) }} ر.س</span>
            </div>
            <div class="total-row total-final">
                <span>المجموع الإجمالي:</span>
                <span>{{ number_format($totalAmount * 1.15, 2) }} ر.س</span>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    @if($purchaseOrder->notes)
    <div class="notes-section">
        <div class="notes-title">ملاحظات:</div>
        <div class="notes-content">
            {{ $purchaseOrder->notes }}
        </div>
    </div>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">إعداد الطلب</div>
            <div class="signature-line"></div>
            <div>التوقيع والتاريخ</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">مراجعة الطلب</div>
            <div class="signature-line"></div>
            <div>التوقيع والتاريخ</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">اعتماد الطلب</div>
            <div class="signature-line"></div>
            <div>التوقيع والتاريخ</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>تم إنشاء هذا التقرير تلقائياً في {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>{{ $company['name'] }} - جميع الحقوق محفوظة</p>
    </div>
</body>
</html>