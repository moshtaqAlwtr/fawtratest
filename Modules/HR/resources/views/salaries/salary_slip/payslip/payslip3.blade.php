<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip</title>
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
            margin-bottom: 60px;
        }
        .company-info {
            text-align: right;
            font-size: 16px;
        }
        .right-info {
            text-align: right;
        }
        .payslip-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: right;
        }
        .info-grid {
            text-align: right;
            display: grid;
            gap: 10px;
        }
        .info-item {
            color: #666;
            font-size: 14px;
        }
        .info-value {
            color: #000;
            margin-right: 5px;
        }
        .employee-name {
            font-size: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .branch {
            margin-bottom: 40px;
            color: #666;
        }
        .date-table {
            width: 100%;
            margin-bottom: 40px;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        .date-table th {
            padding: 8px;
            text-align: right;
            border: 1px solid #000;
            background: white;
        }
        .date-table td {
            padding: 8px;
            text-align: right;
            border: 1px solid #000;
        }
        .main-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        .main-table th {
            padding: 8px;
            text-align: right;
            border: 1px solid #000;
            font-weight: normal;
        }
        .main-table td {
            padding: 8px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        .amount-column {
            text-align: left !important;
        }
        .total-row {
            border-top: 1px solid #000;
            text-align: left;
            padding: 8px;
        }
        .net-salary {
            text-align: right;
            margin-top: 40px;
        }
        .net-salary-label {
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

    <button onclick="window.print()" class="print-button no-print">Print</button>

    <div class="payslip-container">
        <div class="header">
            <div class="right-info">
                <div class="company-info">
                    <div>مؤسسة أعمال خاصة للتجارة</div>
                    <div>الرياض</div>
                </div>
            </div>
            <div class="info-grid">
                <div class="payslip-title">PAYSLIP</div>
                <div class="info-item">
                    Posting Date: {{ $salarySlip->slip_date->format('d/m/Y') }}
                </div>
                <div class="info-item">
                    Expense ID: #{{ $salarySlip->id }}
                </div>
                <div class="info-item">
                    Contract ID: #{{ $salarySlip->employee->contract_id ?? '' }}
                </div>
                <div class="info-item">
                    Attendance Sheet: #{{ $salarySlip->id }}
                </div>
            </div>
        </div>

        <div class="employee-name">{{ $salarySlip->employee->full_name }}</div>
        <div class="branch">Main Branch</div>

        <table class="date-table">
            <tr>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
            <tr>
                <td>{{ $salarySlip->from_date->format('d/m/Y') }}</td>
                <td>{{ $salarySlip->to_date->format('d/m/Y') }}</td>
            </tr>
        </table>

        <table class="main-table">
            <tr>
                <th>Earnings</th>
                <th class="amount-column">Amount</th>
            </tr>
            @foreach($additionItems as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td class="amount-column">{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" class="total-row">
                    Gross Pay: {{ number_format($salarySlip->total_salary, 2) }} {{ $salarySlip->currency }}
                </td>
            </tr>
        </table>

        <table class="main-table">
            <tr>
                <th>Deductions</th>
                <th class="amount-column">Amount</th>
            </tr>
            @foreach($deductionItems as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td class="amount-column">{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" class="total-row">
                    Total Deductions: {{ number_format($salarySlip->total_deductions, 2) }} {{ $salarySlip->currency }}
                </td>
            </tr>
        </table>

        <div class="net-salary">
            <div class="net-salary-label">Net Salary</div>
            <div class="net-amount">{{ number_format($salarySlip->net_salary, 2) }} {{ $salarySlip->currency }}</div>
            <div class="amount-in-words">{{ NumberToWords::convert($salarySlip->net_salary) }} {{ $salarySlip->currency }}</div>
        </div>
    </div>
</body>
</html>
