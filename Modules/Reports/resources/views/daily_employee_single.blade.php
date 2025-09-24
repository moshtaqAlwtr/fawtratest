<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>التقرير اليومي للموظف {{ $user->name }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/dejavu-sans/DejaVuSans.ttf') }}') format('truetype');
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            font-size: 9pt;
            margin: 0;
            padding: 5mm;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
            padding-bottom: 2mm;
            border-bottom: 0.5pt solid #333;
        }
        .header h1 {
            font-size: 12pt;
            margin: 2pt 0;
            color: #2c3e50;
        }
        .header .subtitle {
            font-size: 9pt;
            color: #7f8c8d;
        }
        .employee-info {
            margin-bottom: 5mm;
        }
        .info-table {
            width: 100%;
            margin-bottom: 3mm;
            font-size: 9pt;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2mm;
            border: 0.5pt solid #eee;
        }
        .payment-summary {
            margin-bottom: 8mm;
            border: 0.5pt solid #ddd;
            padding: 4mm;
            border-radius: 3pt;
        }
        .payment-summary-title {
            font-weight: bold;
            margin-bottom: 4mm;
            font-size: 10pt;
            text-align: center;
            color: #2c3e50;
        }
        .payment-summary-grid {
            display: table;
            width: 100%;
        }
        .payment-summary-item {
            display: table-row;
        }
        .payment-summary-label, .payment-summary-value {
            display: table-cell;
            padding: 2mm 1mm;
            vertical-align: middle;
        }
        .payment-summary-label {
            font-size: 9pt;
            color: #495057;
            text-align: right;
            width: 70%;
        }
        .payment-summary-value {
            font-size: 9pt;
            font-weight: bold;
            text-align: left;
            width: 30%;
            direction: ltr;
        }
        .grand-total {
            background-color: #e9f7ef;
            border-top: 0.5pt solid #28a745;
        }
        .section {
            margin-bottom: 8mm;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #3498db;
            color: white;
            padding: 2mm 3mm;
            border-radius: 1.5pt;
            font-size: 10pt;
            margin-bottom: 3mm;
            display: flex;
            justify-content: space-between;
        }
        .section-count {
            background-color: #2980b9;
            padding: 1pt 3pt;
            border-radius: 1.5pt;
            font-size: 9pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
            font-size: 8pt;
            table-layout: fixed;
        }
        th, td {
            border: 0.5pt solid #ddd;
            padding: 2mm;
            text-align: right;
            direction: rtl;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .currency {
            text-align: left;
            direction: ltr;
            font-family: 'DejaVu Sans', sans-serif;
        }
        .time {
            text-align: center;
            direction: rtl;
        }
        .no-data {
            color: #999;
            font-style: italic;
            text-align: center;
            padding: 5mm;
            border: 0.5pt dashed #ddd;
            margin: 3mm 0;
        }
        .status-badge {
            display: inline-block;
            padding: 1.5pt 3pt;
            border-radius: 2pt;
            font-size: 7pt;
            color: white;
            min-width: 40px;
            text-align: center;
        }
        .activity-icon {
            font-size: 10pt;
            text-align: center;
            width: 20px;
        }
        /* ألوان حسب نوع النشاط (تم حذف الزيارات) */
        .activity-invoice { background-color: #e3f2fd; border-left: 3pt solid #2196f3; }
        .activity-payment { background-color: #e8f5e8; border-left: 3pt solid #4caf50; }
        .activity-receipt { background-color: #f3e5f5; border-left: 3pt solid #9c27b0; }
        .activity-expense { background-color: #ffebee; border-left: 3pt solid #f44336; }
        .activity-note { background-color: #f5f5f5; border-left: 3pt solid #607d8b; }

        .status-paid { background-color: #28a745; }
        .status-partial { background-color: #17a2b8; }
        .status-unpaid { background-color: #dc3545; }
        .status-completed { background-color: #28a745; }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-cancelled { background-color: #dc3545; }
        .status-returned { background-color: #6f42c1; }
        .status-receipt { background-color: #6610f2; }
        .status-expense { background-color: #dc3545; }
        .status-note { background-color: #6c757d; }

        .footer {
            text-align: center;
            margin-top: 8mm;
            padding-top: 3mm;
            border-top: 0.5pt solid #eee;
            font-size: 8pt;
            color: #6c757d;
        }
        .col-5 { width: 5%; }
        .col-8 { width: 8%; }
        .col-10 { width: 10%; }
        .col-12 { width: 12%; }
        .col-15 { width: 15%; }
        .col-18 { width: 18%; }
        .col-20 { width: 20%; }
        .col-25 { width: 25%; }
        .col-30 { width: 30%; }
        .nowrap { white-space: nowrap; }
        .wrap-text {
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h1>التقرير اليومي لأداء الموظف</h1>
        <div class="subtitle">تاريخ التقرير: {{ $date }}</div>
    </div>

    <!-- معلومات الموظف -->
    <div class="employee-info">
        <table class="info-table">
            <tr>
                <td style="width: 30%; font-weight: bold;">اسم الموظف</td>
                <td style="width: 70%;">{{ $user->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">رقم الموظف</td>
                <td>{{ $user->id }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">البريد الإلكتروني</td>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">تاريخ الإنضمام</td>
                <td>{{ $user->created_at->format('Y-m-d') }}</td>
            </tr>
        </table>
    </div>

    <!-- ملخص المدفوعات -->
    <div class="payment-summary">
        <div class="payment-summary-title">ملخص المدفوعات</div>
        <div class="payment-summary-grid">
            <div class="payment-summary-item">
                <div class="payment-summary-label">إجمالي المدفوعات المستلمة</div>
                <div class="payment-summary-value">
                    {{ number_format($payments->sum('amount'), 2, '.', ',') }} ر.س
                </div>
            </div>
            <div class="payment-summary-item">
                <div class="payment-summary-label">إجمالي سندات القبض</div>
                <div class="payment-summary-value">
                    {{ number_format($receipts->sum('amount'), 2, '.', ',') }} ر.س
                </div>
            </div>
            <div class="payment-summary-item">
                <div class="payment-summary-label">إجمالي سندات الصرف</div>
                <div class="payment-summary-value">
                    {{ number_format($expenses->sum('amount'), 2, '.', ',') }} ر.س
                </div>
            </div>
            <div class="payment-summary-item grand-total">
                <div class="payment-summary-label">صافي التحصيل النقدي</div>
                <div class="payment-summary-value">
                    @php
                        $totalCollection = $payments->sum('amount') + $receipts->sum('amount') - $expenses->sum('amount');
                    @endphp
                    {{ number_format($totalCollection, 2, '.', ',') }} ر.س
                </div>
            </div>
        </div>
    </div>

    <!-- الأنشطة اليومية -->
    <div class="section">
        <div class="section-title">
            <span>الأنشطة اليومية (مرتبة حسب الوقت)</span>
            <span class="section-count">{{ $allActivities->count() }}</span>
        </div>
        @if ($allActivities->count() > 0)
            <table>
                <thead>
                    <tr>

                        <th class="col-10">النوع</th>
                        <th class="col-20">العميل/الاسم</th>
                        <th class="col-12">المبلغ</th>
                        <th class="col-12">الحالة</th>
                        <th class="col-10">الوقت</th>
                        <th class="col-12">المرجع</th>
                        <th class="col-19">التفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allActivities as $activity)
                        <tr class="activity-{{ $activity['type'] }}">

                            <td class="text-center">
                                @switch($activity['type'])
                                    @case('invoice')
                                        <span class="status-badge {{ $activity['status'] == 'مرتجع' ? 'status-returned' : 'status-completed' }}">
                                            {{ $activity['status'] == 'مرتجع' ? 'مرتجع' : 'فاتورة' }}
                                        </span>
                                        @break
                                    @case('payment')
                                        <span class="status-badge status-paid">دفعة</span>
                                        @break
                                    @case('receipt')
                                        <span class="status-badge status-receipt">قبض</span>
                                        @break
                                    @case('expense')
                                        <span class="status-badge status-expense">صرف</span>
                                        @break
                                    @case('note')
                                        <span class="status-badge status-note">ملاحظة</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="wrap-text">{{ $activity['client_name'] }}</td>
                            <td class="currency">
                                @if($activity['amount'] > 0)
                                    @if($activity['type'] == 'expense')
                                        -{{ number_format($activity['amount'], 2, '.', ',') }} ر.س
                                    @else
                                        {{ number_format($activity['amount'], 2, '.', ',') }} ر.س
                                    @endif
                                @else
                                    --
                                @endif
                            </td>
                            <td class="text-center">
                                @if($activity['type'] == 'invoice' && $activity['payment_status'])
                                    @if($activity['payment_status'] == 1)
                                        <span class="status-badge status-paid">مدفوعة</span>
                                    @elseif ($activity['payment_status'] == 2)
                                        <span class="status-badge status-partial">جزئي</span>
                                    @elseif ($activity['payment_status'] == 3)
                                        <span class="status-badge status-unpaid">غير مدفوعة</span>
                                    @endif
                                @else
                                    {{ $activity['status'] }}
                                @endif
                            </td>
                            <td class="time nowrap">{{ \Carbon\Carbon::parse($activity['time'])->format('H:i') }}</td>
                            <td class="wrap-text nowrap">{{ $activity['reference'] }}</td>
                            <td class="wrap-text">{{ $activity['description'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">لا يوجد أنشطة مسجلة</div>
        @endif
    </div>

    <!-- إحصائيات سريعة (تم حذف الزيارات) -->
    <div class="section">
        <div class="section-title">
            <span>إحصائيات سريعة</span>
        </div>
        <table class="info-table">
            <tr>
                <td style="font-weight: bold;">إجمالي الفواتير</td>
                <td>{{ $invoices->count() }} فاتورة</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">إجمالي المدفوعات</td>
                <td>{{ $payments->count() }} عملية دفع</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">إجمالي سندات القبض</td>
                <td>{{ $receipts->count() }} سند</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">إجمالي سندات الصرف</td>
                <td>{{ $expenses->count() }} سند</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">إجمالي الملاحظات</td>
                <td>{{ $notes->count() }} ملاحظة</td>
            </tr>
            <tr style="background-color: #e9f7ef; font-weight: bold;">
                <td>إجمالي الأنشطة</td>
                <td>{{ $allActivities->count() }} نشاط</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        تم إنشاء التقرير تلقائياً بتاريخ {{ date('Y-m-d H:i') }} - نظام فوترة سمارت
    </div>

</body>
</html>