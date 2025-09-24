<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قسيمة راتب</title>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                size: A4;
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            margin: 0 auto;
            max-width: 210mm;
            min-height: 297mm;
            direction: rtl;
            background: white;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1000;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .company-info {
            text-align: left;
        }
        .payslip-title {
            font-size: 18px;
            font-weight: bold;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-item {
            margin-bottom: 10px;
            color: #666;
        }
        .info-value {
            color: #000;
            margin-top: 3px;
        }
        .employee-section {
            margin: 30px 0;
        }
        .employee-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .branch {
            color: #666;
        }
        .dates-table {
            width: 300px;
            margin: 20px 0;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        .dates-table th, .dates-table td {
            padding: 8px;
            text-align: center;
            border: 1px solid #000;
        }
        .dates-table th {
            font-weight: normal;
        }
        .main-tables {
            display: flex;
            gap: 20px;
            margin: 30px 0;
        }
        .table-container {
            flex: 1;
        }
        .earnings-table, .deductions-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        .table-header {
            border-bottom: 1px solid #000;
        }
        .table-header th {
            padding: 8px;
            text-align: right;
            font-weight: normal;
        }
        .amount-column {
            text-align: right;
            width: 120px;
        }
        tr.data-row td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .total-row {
            border-top: 1px solid #000;
        }
        .total-row td {
            padding: 8px;
            color: #666;
        }
        .net-salary {
            margin-top: 30px;
        }
        .net-label {
            color: #666;
            margin-bottom: 5px;
        }
        .net-amount {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .amount-in-words {
            color: #666;
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</head>
<body>
    @php
        use App\Helpers\NumberToWords;
    @endphp

    <button onclick="window.print()" class="print-button no-print">طباعة</button>

    <div class="payslip-container">
        <div class="header">
            <div class="payslip-title">قسيمة راتب</div>
            <div class="company-info">
                <div>مؤسسة أعمال خاصة للتجارة</div>
                <div>الرياض</div>
            </div>
        </div>

        <div class="info-section">
            <div class="info-item">
                <div>تاريخ النشر</div>
                <div class="info-value">{{ $salarySlip->slip_date->format('d/m/Y') }}</div>
            </div>
            <div class="info-item">
                <div>رقم المصروف</div>
                <div class="info-value">#{{ $salarySlip->id }}</div>
            </div>
            <div class="info-item">
                <div>رقم العقد</div>
                <div class="info-value">#{{ $salarySlip->employee->contract_id ?? '' }}</div>
            </div>
            <div class="info-item">
                <div>دفتر الحضور</div>
                <div class="info-value">#{{ $salarySlip->id }}</div>
            </div>
        </div>

        <div class="employee-section">
            <div class="employee-name">{{ $salarySlip->employee->full_name }}</div>
            <div class="branch">{{ $salarySlip->employee->branch->name ?? 'Main Branch' }}</div>
        </div>

        <table class="dates-table">
            <tr>
                <th>تاريخ النهاية</th>
                <th>تاريخ البداية</th>
            </tr>
            <tr>
                <td>{{ $salarySlip->to_date->format('d/m/Y') }}</td>
                <td>{{ $salarySlip->from_date->format('d/m/Y') }}</td>
            </tr>
        </table>

        <div class="main-tables">
            <div class="table-container">
                <table class="earnings-table">
                    <tr class="table-header">
                        <th>المستحقات</th>
                        <th class="amount-column">المبلغ</th>
                    </tr>
                    @foreach($additionItems as $item)
                    <tr class="data-row">
                        <td>{{ $item->name }}</td>
                        <td class="amount-column">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2">إجمالي المستحقات: SR {{ number_format($salarySlip->total_salary, 2) }}</td>
                    </tr>
                </table>
            </div>

            <div class="table-container">
                <table class="deductions-table">
                    <tr class="table-header">
                        <th>الإستقطاعات</th>
                        <th class="amount-column">المبلغ</th>
                    </tr>
                    @foreach($deductionItems as $item)
                    <tr class="data-row">
                        <td>{{ $item->name }}</td>
                        <td class="amount-column">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2">إجمالي الإستقطاعات: SR {{ number_format($salarySlip->total_deductions, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="net-salary">
            <div class="net-label">صافي الراتب</div>
            <div class="net-amount">SR {{ number_format($salarySlip->net_salary, 2) }}</div>
            <div class="amount-in-words">{{ NumberToWords::convert($salarySlip->net_salary) }} <img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;"></div>
        </div>
    </div>
</body>
</html>
