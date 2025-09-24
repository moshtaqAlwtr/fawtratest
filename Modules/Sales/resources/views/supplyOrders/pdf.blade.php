<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>أوامر التوريد</title>
    <style>
        @page {
            margin: 20mm;
        }

        body {
            font-family: 'aealarabiya', Arial, sans-serif;
            direction: rtl;
            font-size: 10pt;
            line-height: 1.6;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18pt;
            color: #2c3e50;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 9pt;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 9pt;
        }

        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .summary {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
            text-align: right;
        }

        .footer {
            position: fixed;
            bottom: 20mm;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }

        .page-number:after {
            content: counter(page);
        }

        .status-open {
            color: green;
        }

        .status-closed {
            color: red;
        }

        .status-pending {
            color: orange;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>تقرير أوامر التوريد</h1>
            <div class="header-info">
                <div>
                    <strong>التاريخ:</strong> {{ now()->format('Y-m-d') }}
                    <strong>الوقت:</strong> {{ now()->format('H:i:s') }}
                </div>
                <div>
                    <strong>المستخدم:</strong> {{ auth()->user()->name ?? 'غير معروف' }}
                </div>
            </div>
        </div>
        <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>العميل</th>
                <th>تاريخ الإنشاء</th>
                <th>تاريخ التسليم</th>
                <th>الحالة</th>
                <th>المبلغ الإجمالي</th>
                <th>العملة</th>
            </tr>
        </thead>
        <tbody>
            @if($supplyOrders->count() > 0)
                @foreach($supplyOrders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ optional($order->client)->trade_name ?? 'غير محدد' }}</td>
                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        <td>{{ $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '-' }}</td>
                        <td>
                            @switch($order->status)
                                @case(1)
                                    <span class="status-open">مفتوح</span>
                                    @break
                                @case(2)
                                    <span class="status-closed">مغلق</span>
                                    @break
                                @case(3)
                                    <span class="status-pending">قيد التنفيذ</span>
                                    @break
                                @default
                                    <span>غير محدد</span>
                            @endswitch
                        </td>
                        <td>{{ number_format($order->budget, 2) }}</td>
                        <td>{{ $order->currency ?? 'ريال' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" style="text-align: center;">لا توجد أوامر توريد</td>
                </tr>
            @endif
        </tbody>
    </table>

        <div class="summary">
            <p><strong>ملخص التقرير:</strong></p>
            <ul>
                <li>إجمالي عدد أوامر التوريد: {{ $supplyOrders->count() }}</li>
                <li>إجمالي المبلغ: {{ number_format($supplyOrders->sum('budget'), 2) }} ريال</li>
                <li>عدد الأوامر المفتوحة: {{ $supplyOrders->where('status', 1)->count() }}</li>
                <li>عدد الأوامر المغلقة: {{ $supplyOrders->where('status', 2)->count() }}</li>
            </ul>
        </div>

        <div class="footer">
            <div>
                صفحة <span class="page-number"></span>
                | {{ config('app.name') }}
            </div>
        </div>

    </div>
</body>

</html>
