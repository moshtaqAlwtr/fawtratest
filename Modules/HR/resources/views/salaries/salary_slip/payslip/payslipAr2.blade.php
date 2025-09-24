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
            margin-bottom: 30px;
        }
        .company-info {
            text-align: right;
        }
        .payslip-title {
            text-align: left;
            font-weight: bold;
        }
        .employee-section {
            margin-bottom: 30px;
        }
        .employee-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .branch {
            color: #666;
        }
        .dates-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 0;
        }
        .dates-table th {
            background: #000;
            color: white;
            padding: 8px;
            text-align: center;
        }
        .dates-table td {
            padding: 8px;
            text-align: center;
        }
        .ids-table {
            width: 100%;
            margin-bottom: 40px;
            border-collapse: separate;
            border-spacing: 0;
        }
        .ids-table th {
            background: #000;
            color: white;
            padding: 8px;
            text-align: center;
        }
        .ids-table td {
            padding: 8px;
            text-align: center;
        }
        .earnings-table, .deductions-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .table-header {
            background: #000;
            color: white;
        }
        .table-header th {
            padding: 8px;
            text-align: right;
        }
        .table-header th:first-child {
            text-align: right;
        }
        .table-header th:last-child {
            text-align: right;
        }
        .amount-column {
            text-align: right;
        }
        .total-row {
            color: #666;
            padding: 8px 0;
        }
        .net-salary {
            background: #000;
            color: white;
            padding: 15px;
            margin-top: 30px;
            width: fit-content;
        }
        .net-amount {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .amount-in-words {
            font-size: 14px;
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
            <div class="company-info">
                <div>مؤسسة أعمال خاصة للتجارة</div>
                <div>الرياض</div>
            </div>
            <div class="payslip-title">قسيمة راتب</div>
        </div>

        <div class="employee-section">
            <div class="employee-name">{{ $salarySlip->employee->full_name }}</div>
            <div class="branch">{{ $salarySlip->employee->branch->name ?? 'Main Branch' }}</div>
        </div>

        <table class="dates-table">
            <tr>
                <th>تاريخ النهاية</th>
                <th>تاريخ البداية</th>
                <th>تاريخ النشر</th>
            </tr>
            <tr>
                <td>{{ $salarySlip->to_date->format('d/m/Y') }}</td>
                <td>{{ $salarySlip->from_date->format('d/m/Y') }}</td>
                <td>{{ $salarySlip->slip_date->format('d/m/Y') }}</td>
            </tr>
        </table>

        <table class="ids-table">
            <tr>
                <th>رقم المصروف</th>
                <th>رقم العقد</th>
                <th>دفتر الحضور</th>
            </tr>
            <tr>
                <td>#{{ $salarySlip->id }}</td>
                <td>#{{ $salarySlip->employee->contract_id ?? '' }}</td>
                <td>#{{ $salarySlip->id }}</td>
            </tr>
        </table>

        <table class="earnings-table">
            <tr class="table-header">
                <th>المبلغ</th>
                <th>المستحقات</th>
            </tr>
            @foreach($additionItems as $item)
            <tr>
                <td class="amount-column">{{ number_format($item->amount, 2) }}</td>
                <td>{{ $item->name }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" class="total-row">
                    إجمالي المستحقات: SR {{ number_format($salarySlip->total_salary, 2) }}
                </td>
            </tr>
        </table>

        <table class="deductions-table">
            <tr class="table-header">
                <th>المبلغ</th>
                <th>الإستقطاعات</th>
            </tr>
            @foreach($deductionItems as $item)
            <tr>
                <td class="amount-column">{{ number_format($item->amount, 2) }}</td>
                <td>{{ $item->name }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" class="total-row">
                    إجمالي الإستقطاعات: SR {{ number_format($salarySlip->total_deductions, 2) }}
                </td>
            </tr>
        </table>

        <div class="net-salary">
            <div>صافي الراتب</div>
            <div class="net-amount">SR {{ number_format($salarySlip->net_salary, 2) }}</div>
            <div class="amount-in-words">{{ NumberToWords::convert($salarySlip->net_salary) }} ( <img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">)</div>
        </div>
    </div>
</body>
</html>
