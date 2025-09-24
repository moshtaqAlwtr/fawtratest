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
            text-align: right;
            font-size: 16px;
        }
        .payslip-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 30px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .employee-info {
            margin-bottom: 40px;
        }
        .employee-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .branch {
            color: #666;
            margin-bottom: 20px;
        }
        .dates-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
            text-align: right;
        }
        .date-item {
            color: #666;
        }
        .date-value {
            color: #000;
            margin-top: 5px;
        }
        .ids-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
            text-align: right;
        }
        .id-item {
            color: #666;
        }
        .id-value {
            color: #000;
            margin-top: 5px;
        }
        .table-section {
            margin-bottom: 20px;
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .table-content {
            margin-bottom: 10px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .total-row {
            display: flex;
            justify-content: flex-start;
            color: #666;
            margin-top: 10px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .total-row span:first-child {
            margin-left: 10px;
        }
        .net-salary {
            margin-top: 40px;
            text-align: right;
        }
        .net-salary-title {
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
            margin-bottom: 40px;
        }
        .signature {
            margin-top: 60px;
            text-align: right;
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
        </div>

        <div class="payslip-title">قسيمة راتب</div>

        <div class="employee-info">
            <div class="employee-name">{{ $salarySlip->employee->full_name }}</div>
            <div class="branch">{{ $salarySlip->employee->branch->name ?? 'الفرع الرئيسي' }}</div>
        </div>

        <div class="dates-info">
            <div class="date-item">
                <div>تاريخ النشر</div>
                <div class="date-value">{{ $salarySlip->slip_date->format('d/m/Y') }}</div>
            </div>
            <div class="date-item">
                <div>تاريخ البداية</div>
                <div class="date-value">{{ $salarySlip->from_date->format('d/m/Y') }}</div>
            </div>
            <div class="date-item">
                <div>تاريخ النهاية</div>
                <div class="date-value">{{ $salarySlip->to_date->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="ids-info">
            <div class="id-item">
                <div>رقم المصروف</div>
                <div class="id-value">#{{ $salarySlip->id }}</div>
            </div>
            <div class="id-item">
                <div>رقم العقد</div>
                <div class="id-value">#{{ $salarySlip->employee->contract_id ?? '' }}</div>
            </div>
            <div class="id-item">
                <div>دفتر الحضور</div>
                <div class="id-value">#{{ $salarySlip->id }}</div>
            </div>
        </div>

        <div class="table-section">
            <div class="table-header">
                <span>المستحقات</span>
                <span>المبلغ</span>
            </div>
            <div class="table-content">
                @foreach($additionItems as $item)
                <div class="row">
                    <span>{{ $item->name }}</span>
                    <span>{{ number_format($item->amount, 2) }}</span>
                </div>
                @endforeach
            </div>
            <div class="total-row">
                <span>إجمالي المستحقات:</span>
                <span>{{ number_format($salarySlip->total_salary, 2) }} {{ $salarySlip->currency }}</span>
            </div>
        </div>

        <div class="table-section">
            <div class="table-header">
                <span>الإستقطاعات</span>
                <span>المبلغ</span>
            </div>
            <div class="table-content">
                @foreach($deductionItems as $item)
                <div class="row">
                    <span>{{ $item->name }}</span>
                    <span>{{ number_format($item->amount, 2) }}</span>
                </div>
                @endforeach
            </div>
            <div class="total-row">
                <span>إجمالي الإستقطاعات:</span>
                <span>{{ number_format($salarySlip->total_deductions, 2) }} {{ $salarySlip->currency }}</span>
            </div>
        </div>

        <div class="net-salary">
            <div class="net-salary-title">صافي الراتب</div>
            <div class="net-amount">{{ number_format($salarySlip->net_salary, 2) }} {{ $salarySlip->currency }}</div>
            <div class="amount-in-words">{{ NumberToWords::convert($salarySlip->net_salary) }} {{ $salarySlip->currency }}</div>
        </div>

        <div class="signature">التوقيع</div>
    </div>
</body>
</html>
