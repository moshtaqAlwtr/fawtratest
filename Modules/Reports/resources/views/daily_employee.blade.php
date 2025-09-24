<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>التقرير اليومي للموظف {{ $user->name }}</title>
    <style>
        body {
            font-family: DejaVu Sans;
            direction: rtl;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            font-size: 20px;
            margin: 5px 0;
            color: #2c3e50;
        }
        .header .subtitle {
            font-size: 14px;
            color: #7f8c8d;
        }
        .employee-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .info-item {
            flex: 1;
            min-width: 150px;
        }
        .info-label {
            font-weight: bold;
            color: #3498db;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 13px;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #3498db;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .section-count {
            background-color: #2980b9;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
            direction: ltr;
            font-family: 'Courier New', monospace;
        }
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .no-data {
            color: #999;
            font-style: italic;
            text-align: center;
            padding: 15px;
            border: 1px dashed #ddd;
            margin: 10px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            color: white;
        }
        .status-paid {
            background-color: #28a745;
        }
        .status-partial {
            background-color: #17a2b8;
        }
        .status-unpaid {
            background-color: #dc3545;
        }
        .summary-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .summary-card {
            flex: 1;
            min-width: 120px;
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            border: 1px solid #eee;
        }
        .summary-label {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #6c757d;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>التقرير اليومي لأداء الموظف</h1>
        <div class="subtitle">تاريخ التقرير: {{ $date }}</div>
    </div>

    <div class="employee-info">
        <div class="info-item">
            <div class="info-label">اسم الموظف</div>
            <div class="info-value">{{ $user->name }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">رقم الموظف</div>
            <div class="info-value">{{ $user->id }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">البريد الإلكتروني</div>
            <div class="info-value">{{ $user->email }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">تاريخ الإنضمام</div>
            <div class="info-value">{{ $user->created_at->format('Y-m-d') }}</div>
        </div>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-label">إجمالي الفواتير</div>
            <div class="summary-value">{{ number_format($total_invoices, 2) }} ر.س</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">إجمالي المدفوعات</div>
            <div class="summary-value">{{ number_format($total_payments, 2) }} ر.س</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">عدد الزيارات</div>
            <div class="summary-value">{{ $visits->count() }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">إجمالي الإيصالات</div>
            <div class="summary-value">{{ number_format($receipts->sum('amount'), 2) }} ر.س</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">إجمالي المصروفات</div>
            <div class="summary-value">{{ number_format($expenses->sum('amount'), 2) }} ر.س</div>
        </div>
    </div>

    {{-- الفواتير --}}
    <div class="section">
        <div class="section-title">
            <span>الفواتير الصادرة</span>
            <span class="section-count">{{ $invoices->count() }}</span>
        </div>
        @if ($invoices->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th width="10%">رقم الفاتورة</th>
                        <th width="25%">العميل</th>
                        <th width="15%">المجموع</th>
                        <th width="15%">الحالة</th>
                        <th width="15%">التاريخ</th>
                        <th width="20%">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td>#{{ $invoice->id }}</td>
                            <td>{{ $invoice->client->trade_name ?? 'غير محدد' }}</td>
                            <td class="text-left">{{ number_format($invoice->grand_total, 2) }} ر.س</td>
                            <td>
                                @if($invoice->payment_status == 1)
                                    <span class="status-badge status-paid">مدفوعة</span>
                                @elseif ($invoice->payment_status == 2)
                                    <span class="status-badge status-partial">جزئي</span>
                                @elseif ($invoice->payment_status == 3)
                                    <span class="status-badge status-unpaid">غير مدفوعة</span>
                                @endif
                            </td>
                            <td>{{ $invoice->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $invoice->notes ?? '--' }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2">المجموع</td>
                        <td class="text-left">{{ $invoices->sum('grand_total'), 2 }} ر.س</td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-data">لا يوجد فواتير مسجلة لهذا اليوم</div>
        @endif
    </div>

    {{-- المدفوعات --}}
    <div class="section">
        <div class="section-title">
            <span>المدفوعات المستلمة</span>
            <span class="section-count">{{ $payments->count() }}</span>
        </div>
        @if ($payments->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th width="15%">رقم العملية</th>
                        <th width="25%">العميل</th>
                        <th width="15%">المبلغ</th>
                        <th width="15%">طريقة الدفع</th>
                        <th width="15%">التاريخ</th>
                        <th width="15%">رقم الفاتورة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>#{{ $payment->id }}</td>
                            <td>{{ $payment->client->trade_name ?? 'غير محدد' }}</td>
                            <td class="text-left">{{ number_format($payment->amount, 2) }} ر.س</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>{{ $payment->payment_date }}</td>
                            <td>#{{ $payment->invoice_id }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2">المجموع</td>
                        <td class="text-left">{{ $payments->sum('amount'), 2 }} ر.س</td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-data">لا يوجد مدفوعات مسجلة لهذا اليوم</div>
        @endif
    </div>

    {{-- زيارات العملاء --}}
    <div class="section">
        <div class="section-title">
            <span>زيارات العملاء</span>
            <span class="section-count">{{ $visits->count() }}</span>
        </div>
        @if ($visits->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th width="25%">العميل</th>
                        <th width="20%">العنوان</th>
                        <th width="10%">الوصول</th>
                        <th width="10%">الانصراف</th>
                        <th width="15%">التاريخ</th>
                        <th width="20%">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($visits as $visit)
                        <tr>
                            <td>{{ $visit->client->trade_name ?? 'غير محدد' }}</td>
                            <td>{{ $visit->client->street1 ?? 'غير محدد' }}</td>
                            <td>{{ $visit->arrival_time ?? '--' }}</td>
                            <td>{{ $visit->departure_time ?? '--' }}</td>
                            <td>{{ $visit->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ Str::limit($visit->notes ?? 'لا توجد ملاحظات', 30) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="6">إجمالي عدد الزيارات: {{ $visits->count() }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-data">لا يوجد زيارات مسجلة لهذا اليوم</div>
        @endif
    </div>

    {{-- سندات القبض --}}
    <div class="section">
        <div class="section-title">
            <span>سندات القبض</span>
            <span class="section-count">{{ $receipts->count() }}</span>
        </div>
        @if ($receipts->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th width="20%">رقم السند</th>
                        <th width="25%">من</th>
                        <th width="15%">المبلغ</th>
                        <th width="20%">التاريخ</th>
                        <th width="20%">الوصف</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($receipts as $receipt)
                        <tr>
                            <td>#{{ $receipt->id }}</td>
                            <td>{{ $receipt->account->name ?? 'غير محدد' }}</td>
                            <td class="text-left">{{ number_format($receipt->amount, 2) }} ر.س</td>
                            <td>{{ $receipt->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ Str::limit($receipt->description ?? '--', 30) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2">المجموع</td>
                        <td class="text-left">{{$receipts->sum('amount'), 2 }} ر.س</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-data">لا يوجد سندات قبض مسجلة لهذا اليوم</div>
        @endif
    </div>

    {{-- سندات الصرف --}}
    <div class="section">
        <div class="section-title">
            <span>سندات الصرف</span>
            <span class="section-count">{{ $expenses->count() }}</span>
        </div>
        @if ($expenses->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th width="20%">رقم السند</th>
                        <th width="25%">إلى</th>
                        <th width="15%">المبلغ</th>
                        <th width="20%">التاريخ</th>
                        <th width="20%">الوصف</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $expense)
                        <tr>
                            <td>#{{ $expense->id }}</td>
                            <td>{{ $expense->name }}</td>
                            <td class="text-left">{{ number_format($expense->amount, 2) }} ر.س</td>
                            <td>{{ $expense->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ Str::limit($expense->description ?? '--', 30) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2">المجموع</td>
                        <td class="text-left">{{ $expenses->sum('amount'), 2 }} ر.س</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-data">لا يوجد سندات صرف مسجلة لهذا اليوم</div>
        @endif
    </div>

    <div class="footer">
        تم إنشاء هذا التقرير تلقائياً بتاريخ {{ date('Y-m-d H:i') }} - نظام فوترة سمارت
    </div>

</body>
</html>
