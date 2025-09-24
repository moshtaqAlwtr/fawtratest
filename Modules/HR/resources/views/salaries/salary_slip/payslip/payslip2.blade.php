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
            margin-bottom: 40px;
        }

        .company-info {
            text-align: right;
        }

        .payslip-title {
            font-weight: bold;
            color: #333;
        }

        .branch {
            margin: 20px 0;
        }

        .info-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .info-table th {
            background: #000;
            color: white;
            padding: 8px;
            text-align: right;
        }

        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .earnings-table,
        .deductions-table {
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

        .table-header th:last-child {
            text-align: left;
        }

        .amount-column {
            text-align: left !important;
        }

        .total-row {
            text-align: left;
            color: #666;
            padding: 10px 0;
        }

        .net-salary {
            background: #000;
            color: white;
            padding: 15px;
            text-align: left;
            margin-top: 30px;
            width: 300px;
            margin-left: 0;
            margin-right: auto;
        }

        .net-amount {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .amount-in-words {
            font-size: 12px;
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
            <div class="payslip-title">PAYSLIP</div>
            <div class="company-info">
                <div>مؤسسة أعمال خاصة للتجارة</div>
                <div>الرياض</div>
            </div>
        </div>

        <div class="branch">Main Branch</div>

        <table class="info-table">
            <tr>
                <th>Posting Date</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
            <tr>
                <td>{{ $salarySlip->slip_date->format('d/m/Y') }}</td>
                <td>{{ $salarySlip->from_date->format('d/m/Y') }}</td>
                <td>{{ $salarySlip->to_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Attendance Sheet</th>
                <th>Contract ID</th>
                <th>Expense ID</th>
            </tr>
            <tr>
                <td>#{{ $salarySlip->id }}</td>
                <td>#{{ $salarySlip->employee->contract_id ?? '' }}</td>
                <td>#{{ $salarySlip->id }}</td>
            </tr>
        </table>

        <table class="earnings-table">
            <tr class="table-header">
                <th>Earnings</th>
                <th class="amount-column">Amount</th>
            </tr>
            @foreach ($additionItems as $item)
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

        <table class="deductions-table">
            <tr class="table-header">
                <th>Deductions</th>
                <th class="amount-column">Amount</th>
            </tr>
            @foreach ($deductionItems as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td class="amount-column">{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" class="total-row">
                    Total Deductions: {{ number_format($salarySlip->total_deductions, 2) }}
                    {{ $salarySlip->currency }}
                </td>
            </tr>
        </table>

        <div class="net-salary">
            <div class="net-amount">{{ number_format($salarySlip->net_salary, 2) }} {{ $salarySlip->currency }}</div>
            <div class="amount-in-words">{{ NumberToWords::convert($salarySlip->net_salary) }}
                {{ $salarySlip->currency }}</div>
        </div>
    </div>
</body>

</html>
