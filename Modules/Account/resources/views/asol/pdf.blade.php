<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>تقرير الأصل</title>
    <style>
        body {
            direction: rtl;
            font-family: 'aealarabiya', sans-serif;
            padding: 30px;
            background-color: #fff;
            color: #000;
        }

        .header {
            text-align: center;
            padding: 10px;
            border: 2px solid #000;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 4px 0;
            font-size: 14px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .info-grid td {
            padding: 8px;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            width: 25%;
            color: #333;
        }

        .info-value {
            border-bottom: 1px dotted #666;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .data-table th {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>تقرير الأسل التفصيلي</h1>
        <p>{{ $asset->created_at->format('Y-m-d') }}</p>
        <p>مؤسسة الطيب الأفضل للتجارة</p>
        <p> اسم الأصل : {{ $asset->name }}</p>
    </div>

    <table class="info-grid">
        <tr>
            <td class="info-label">طريقة الإهلاك:</td>
            <td class="info-value">
                @switch($asset->dep_method)
                    @case(1) القسط الثابت @break
                    @case(2) القسط المتناقص @break
                    @case(3) وحدات الإنتاج @break
                    @case(4) بدون إهلاك @break
                    @default بدون إهلاك
                @endswitch
            </td>
            <td class="info-label">الرقم المعرف:</td>
            <td class="info-value">{{ $asset->id }}</td>
        </tr>
        <tr>
            <td class="info-label">الكود:</td>
            <td class="info-value">{{ $asset->code }}</td>
            <td class="info-label">الكمية:</td>
            <td class="info-value">{{ $asset->quantity }}</td>
        </tr>
        <tr>
            <td class="info-label">العمر الإنتاجي:</td>
            <td class="info-value">{{ $asset->region_age }} سنة</td>
            <td class="info-label">الموقع:</td>
            <td class="info-value">{{ $asset->place ?: 'غير محدد' }}</td>
        </tr>
        <tr>
            <td class="info-label">الوصف:</td>
            <td class="info-value">{{ $asset->description ?: 'لا يوجد وصف' }}</td>
            <td class="info-label">الموظف:</td>
            <td class="info-value">{{ $asset->employee->full_name ?? 'غير محدد' }}</td>
        </tr>
        <tr>
            <td class="info-label">الحساب الرئيسي:</td>
            <td class="info-value">{{ $asset->account->name ?? 'غير محدد' }}</td>
            <td class="info-label">حساب الإهلاك:</td>
            <td class="info-value">{{ $asset->depreciation_account->name ?? 'غير محدد' }}</td>
        </tr>
        <tr>
            <td class="info-label">قيمة الشراء:</td>
            <td class="info-value">{{ number_format($asset->purchase_value, 2) }} {{ $asset->currency == 1 ? 'ريال' : 'دولار' }}</td>
            <td class="info-label">القيمة الحالية:</td>
            <td class="info-value">{{ number_format($asset->current_value, 2) }} {{ $asset->currency == 1 ? 'ريال' : 'دولار' }}</td>
        </tr>
        <tr>
            <td class="info-label">تاريخ بداية الخدمة:</td>
            <td class="info-value">{{ $asset->date_service }}</td>
            <td class="info-label">تاريخ الشراء:</td>
            <td class="info-value">{{ $asset->date_price }}</td>
        </tr>
    </table>

    <h3 class="section-title">سجل الحركات:</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>العملية</th>
                <th>المبلغ</th>
                <th>الرصيد</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $asset->created_at->format('Y-m-d') }}</td>
                <td>شراء</td>
                <td>{{ number_format($asset->purchase_value, 2) }}</td>
                <td>{{ number_format($asset->purchase_value, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3"><strong>القيمة الحالية</strong></td>
                <td>{{ number_format($asset->purchase_value, 2) }}</td>
            </tr>
        </tbody>
    </table>

</body>

</html>
