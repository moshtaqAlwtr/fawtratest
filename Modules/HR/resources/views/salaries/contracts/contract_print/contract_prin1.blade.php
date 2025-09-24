<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عقد عمل</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 16px;
            color: #000;
        }
        .contract-title {
            font-size: 16px;
            color: #000;
        }
        .employee-name {
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .info-item {
            margin-bottom: 15px;
        }
        .info-label {
            color: #666;
            font-size: 12px;
        }
        .info-value {
            color: #000;
            font-size: 14px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: right;
            font-size: 12px;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #000;
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">
            مؤسسة اعمال خاصة للتجارة<br>
            الرياض
        </div>
        <div class="contract-title">Contract</div>
    </div>

    <div class="employee-name">{{ $contract->employee->full_name }}</div>

    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Contract ID</div>
            <div class="info-value">#{{ $contract->id }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Master Contract</div>
            <div class="info-value">{{ $contract->parent_contract ? $contract->parent_contract->contract_number : '-' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Code</div>
            <div class="info-value">{{ $contract->code ?? '1' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Status</div>
            <div class="info-value">{{ ucfirst($contract->status) }}</div>
        </div>
    </div>

    <div class="info-item">
        <div class="info-label">Description</div>
        <div class="info-value">{{ $contract->description ?? '-' }}</div>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Designation</div>
            <div class="info-value">{{ optional($contract->jobTitle)->name ?? '-' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Employment Level</div>
            <div class="info-value">{{ optional($contract->jobLevel)->name ?? '-' }}</div>
        </div>
    </div>

    <div class="section-title">Contract Information</div>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Contract Start Date</div>
            <div class="info-value">{{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') : '-' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Duration</div>
            <div class="info-value">-</div>
        </div>
        <div class="info-item">
            <div class="info-label">Probation Period End Date</div>
            <div class="info-value">{{ $contract->probation_end_date ? \Carbon\Carbon::parse($contract->probation_end_date)->format('d/m/Y') : '-' }}</div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Join Date</div>
            <div class="info-value">{{ $contract->join_date ? \Carbon\Carbon::parse($contract->join_date)->format('d/m/Y') : '-' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Contract Sign Date</div>
            <div class="info-value">{{ $contract->contract_date ? \Carbon\Carbon::parse($contract->contract_date)->format('d/m/Y') : '-' }}</div>
        </div>
    </div>

    <div class="section-title">Salary Information</div>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Salary Structure</div>
            <div class="info-value">{{ optional($contract->salaryTemplate)->name ?? '-' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Payroll Frequency</div>
            <div class="info-value">{{ $contract->payment_cycle ?? 'Monthly' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Currency</div>
            <div class="info-value">{{ $contract->currency ?? 'SAR' }}</div>
        </div>
    </div>

    <table>
        <tr>
            <th>Earnings</th>
            <th>Formula</th>
            <th>Amount</th>
        </tr>
        @foreach($contract->salaryItems->where('type', 1) as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->calculation_formula ?? '--' }}</td>
            <td>{{ number_format($item->amount, 2) }}</td>
        </tr>
        @endforeach
    </table>

    <table>
        <tr>
            <th>Deductions</th>
            <th>Formula</th>
            <th>Amount</th>
        </tr>
        @foreach($contract->salaryItems->where('type', 2) as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->calculation_formula ?? '--' }}</td>
            <td>{{ number_format($item->amount, 2) }}</td>
        </tr>
        @endforeach
    </table>

    <div class="signature-line">
        <div class="info-label">Signature</div>
    </div>
</body>
</html>
