<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قسيمة الراتب</title>
    <style>
        @font-face {
        font-family: 'Cairo';
        font-style: normal;
        font-weight: normal;
        src: url("{{ asset('fonts/Cairo-Regular.ttf') }}") format('truetype');
    }

    body {
        font-family: 'Cairo', Arial, sans-serif;
        direction: rtl;
        text-align: right;
    }
        .container {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
        }
        .signature {
            margin-top: 30px;
            text-align: left;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>قسيمة الراتب</h2>
        <p><strong>اسم الموظف:</strong> {{ $details['name'] }}</p>
        <p><strong>تاريخ النشر:</strong> {{ \Carbon\Carbon::parse($details['create'])->format('Y-m-d') }}</p>
        <p><strong>الفترة:</strong> من {{ \Carbon\Carbon::parse($details['from'])->format('Y-m-d') }} إلى {{ \Carbon\Carbon::parse($details['to'])->format('Y-m-d') }}</p>

        <table>
            <tr>
                <th>المبلغ المستحق</th>
                <th>الخصومات</th>
                <th>صافي الراتب</th>
            </tr>
            <tr>
                <td>{{ number_format($details['total_salary'], 2) }}</td>
                <td>{{ number_format($details['total_deductions'], 2) }}</td>
                <td>{{ number_format($details['net_salary'], 2) }}</td>
            </tr>
        </table>

        <div class="signature">
            <p>________________________</p>
            <p>التوقيع</p>
        </div>

        <div class="footer">
            <p>شكراً لجهودك المستمرة!</p>
        </div>
    </div>
</body>
</html>
