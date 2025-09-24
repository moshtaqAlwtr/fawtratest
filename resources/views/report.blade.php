<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير الموظفين اليومي</title>
<style>
    @font-face {
        font-family: 'cairo';
        src: url("{{ public_path('fonts/Cairo-Regular.ttf') }}") format("truetype");
    }

    body {
        font-family: 'cairo', sans-serif;
        direction: rtl;
        text-align: right;
        font-size: 14px;
        line-height: 1.6;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        border: 1px solid #000;
        padding: 8px;
    }

    th {
        background-color: #f2f2f2;
    }
</style>


</head>
<body>
    <h2>📄 تقرير الموظفين اليومي - {{ \Carbon\Carbon::today()->format('Y-m-d') }}</h2>
    <table>
        <thead>
            <tr>
                <th>الموظف</th>
                <th>عدد الفواتير</th>
                <th>مجموع الفواتير</th>
                <th>عدد المدفوعات</th>
                <th>مجموع المدفوعات</th>
                <th>عدد سندات القبض</th>
                <th>مجموع سندات القبض</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $row)
                <tr>
                    <td>{{ $row['user'] }}</td>
                    <td>{{ $row['invoice_count'] }}</td>
                    <td>{{ number_format($row['invoice_total'], 2) }}</td>
                    <td>{{ $row['payment_count'] }}</td>
                    <td>{{ number_format($row['payment_total'], 2) }}</td>
                    <td>{{ $row['receipt_count'] }}</td>
                    <td>{{ number_format($row['receipt_total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
