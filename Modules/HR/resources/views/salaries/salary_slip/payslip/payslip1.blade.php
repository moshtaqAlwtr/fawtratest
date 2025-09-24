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
            max-width: 210mm; /* A4 width */
            min-height: 297mm; /* A4 height */
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
        .print-button:hover {
            background: #0056b3;
        }
        @media screen {
            body {
                background: #f0f0f0;
            }
            .payslip-container {
                background: white;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                padding: 40px;
                margin: 40px auto;
                border-radius: 8px;
            }
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }
        .company-info {
            text-align: right;
            font-size: 14px;
        }
        .payslip-title {
            font-size: 16px;
        }
        .employee-name {
            font-size: 16px;
            margin-bottom: 5px;
            text-align: right;
        }
        .branch {
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .info-item {
            text-align: right;
        }
        .info-label {
            color: #666;
            font-size: 12px;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 14px;
        }
        .earnings-section, .deductions-section {
            margin-bottom: 20px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .section-header span:last-child {
            text-align: left;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .item-row span:last-child {
            text-align: left;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            color: #666;
            margin-top: 5px;
            font-size: 14px;
            text-align: left;
        }
        .total-row span:first-child {
            margin-left: 10px;
        }
        .net-salary {
            text-align: left;
            margin-top: 30px;
        }
        .net-amount {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .amount-in-words {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .signature {
            text-align: left;
            color: #666;
            font-size: 14px;
            margin-top: 30px;
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
            <div class="company-info">
                <div>مؤسسة أعمال خاصة للتجارة</div>
                <div>الرياض</div>
            </div>
            <div class="payslip-title">Payslip</div>
        </div>

        <div class="employee-name">{{ $salarySlip->employee->full_name }}</div>
        <div class="branch">{{ $salarySlip->employee->branch->name ?? 'Main Branch' }}</div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Posting Date</div>
                <div class="info-value">{{ $salarySlip->slip_date->format('d/m/Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Start Date</div>
                <div class="info-value">{{ $salarySlip->from_date->format('d/m/Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">End Date</div>
                <div class="info-value">{{ $salarySlip->to_date->format('d/m/Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Attendance Sheet</div>
                <div class="info-value">#{{ $salarySlip->id }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Contract ID</div>
                <div class="info-value">#{{ $salarySlip->employee->contract_id ?? '' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Expense ID</div>
                <div class="info-value">#{{ $salarySlip->id }}</div>
            </div>
        </div>

        <div class="earnings-section">
            <div class="section-header">
                <span>Earnings</span>
                <span>Amount</span>
            </div>
            @foreach($additionItems as $item)
            <div class="item-row">
                <span>{{ $item->name }}</span>
                <span>{{ number_format($item->amount, 2) }}</span>
            </div>
            @endforeach
            <div class="total-row">
                <span>Gross Pay:</span>
                <span>{{ number_format($salarySlip->total_salary, 2) }} {{ $salarySlip->currency }}</span>
            </div>
        </div>

        <div class="deductions-section">
            <div class="section-header">
                <span>Deductions</span>
                <span>Amount</span>
            </div>
            @foreach($deductionItems as $item)
            <div class="item-row">
                <span>{{ $item->name }}</span>
                <span>{{ number_format($item->amount, 2) }}</span>
            </div>
            @endforeach
            <div class="total-row">
                <span>Total Deductions:</span>
                <span>{{ number_format($salarySlip->total_deductions, 2) }} {{ $salarySlip->currency }}</span>
            </div>
        </div>

        <div class="net-salary">
            <div class="net-amount">{{ number_format($salarySlip->net_salary, 2) }} {{ $salarySlip->currency }}</div>
            <div class="amount-in-words">{{ NumberToWords::convert($salarySlip->net_salary) }} {{ $salarySlip->currency }}</div>
        </div>

        <div class="signature">Signature</div>
    </div>
</body>
</html>
