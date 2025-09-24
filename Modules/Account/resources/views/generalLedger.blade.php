@extends('master')

@section('title')
    الأستاذ العام
@stop

@section('css')
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .page-title {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
        }

        .page-title h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .page-title p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        .filter-card {
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
        }

        .filter-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .filter-header i {
            font-size: 1.5rem;
            color: #3498db;
            margin-left: 10px;
        }

        .filter-header h4 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }

        .form-filter {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .form-group {
            position: relative;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #495057;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            transform: translateY(-1px);
        }

        .btn-search {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }

        .results-card {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .results-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .results-header h2 {
            margin: 0 0 10px 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .results-header .period {
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            margin: 5px;
            font-size: 0.9rem;
        }

        .account-info {
            background: rgba(255,255,255,0.15);
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 25px;
            background: #f8f9fa;
        }

        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
        }

        .summary-card .icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .summary-card.debit .icon {
            color: #e74c3c;
        }

        .summary-card.credit .icon {
            color: #27ae60;
        }

        .summary-card.balance .icon {
            color: #3498db;
        }

        .summary-card h5 {
            margin: 0 0 5px 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .summary-card .amount {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
        }

        .summary-card.debit .amount {
            color: #e74c3c;
        }

        .summary-card.credit .amount {
            color: #27ae60;
        }

        .summary-card.balance .amount {
            color: #3498db;
        }

        .table-container {
            padding: 0;
            overflow-x: auto;
        }

        .ledger-table {
            width: 100%;
            margin: 0;
            font-size: 0.9rem;
        }

        .ledger-table thead {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
        }

        .ledger-table thead th {
            padding: 15px 12px;
            font-weight: 600;
            text-align: center;
            border: none;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .ledger-table tbody tr {
            transition: all 0.3s ease;
        }

        .ledger-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .ledger-table tbody tr:hover {
            background-color: #e3f2fd;
            transform: scale(1.01);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .ledger-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .ledger-table .date-cell {
            background: #e8f5e8;
            font-weight: 600;
            color: #2d5a2d;
        }

        .ledger-table .description-cell {
            text-align: right;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ledger-table .debit-cell {
            color: #e74c3c;
            font-weight: 600;
        }

        .ledger-table .credit-cell {
            color: #27ae60;
            font-weight: 600;
        }

        .ledger-table .balance-cell {
            color: #3498db;
            font-weight: 600;
            background: rgba(52, 152, 219, 0.1);
        }

        .ledger-table tfoot {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            color: white;
            font-weight: 700;
        }

        .ledger-table tfoot td {
            padding: 15px 12px;
            border: none;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .no-data i {
            font-size: 4rem;
            color: #bdc3c7;
            margin-bottom: 20px;
        }

        .no-data h4 {
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .no-data p {
            color: #95a5a6;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-filter {
                grid-template-columns: 1fr;
            }
            
            .summary-cards {
                grid-template-columns: 1fr;
            }
            
            .page-title h1 {
                font-size: 2rem;
            }
            
            .ledger-table {
                font-size: 0.8rem;
            }
            
            .ledger-table th,
            .ledger-table td {
                padding: 8px 6px;
            }
        }

        /* Loading Animation */
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Print Styles */
        @media print {
            body {
                background: white !important;
            }
            
            .filter-card {
                display: none;
            }
            
            .results-card {
                box-shadow: none;
                border: 1px solid #333;
            }
        }
    </style>
@endsection

@section('content')
<div class="container" style="direction: rtl">
    
    <!-- Page Title -->
    <div class="page-title">
        <h1><i class="fas fa-book"></i> الأستاذ العام</h1>
        <p>تقرير مفصل لحركة الحسابات</p>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <div class="filter-header">
            <i class="fas fa-filter"></i>
            <h4>فلترة البيانات</h4>
        </div>
        
        <form action="{{ route('journal.generalLedger') }}" method="GET" class="form-filter" id="filterForm">
            <div class="form-group">
                <label><i class="fas fa-calendar-alt"></i> من تاريخ:</label>
                <input type="date" name="fromDate" value="{{ request('fromDate') }}" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-calendar-check"></i> إلى تاريخ:</label>
                <input type="date" name="toDate" value="{{ request('toDate') }}" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-chart-line"></i> الحساب:</label>
                <select name="account_id" required>
                    <option value="">-- اختر حساب --</option>
                    @foreach ($accounts as $accountOption)
                        <option value="{{ $accountOption->id }}" {{ request('account_id') == $accountOption->id ? 'selected' : '' }}>
                            {{ $accountOption->name }} ({{ $accountOption->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> عرض التقرير
                </button>
            </div>
        </form>
        
        <!-- Loading Spinner -->
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner"></div>
            <p>جاري تحميل البيانات...</p>
        </div>
    </div>

    @if(isset($entries) && $entries->count())
        <div class="results-card">
            <!-- Results Header -->
            <div class="results-header">
                <h2><i class="fas fa-chart-bar"></i> نتائج الأستاذ العام</h2>
                <div>
                    <span class="period">
                        <i class="fas fa-calendar"></i>
                        من {{ $from_date }} إلى {{ $to_date }}
                    </span>
                </div>
                <div class="account-info">
                    <i class="fas fa-user-circle"></i>
                    <strong>{{ $account->name ?? '' }} ({{ $account->code ?? '' }})</strong>
                </div>
            </div>

            <!-- Summary Cards -->
            @php 
                $debitTotal = 0;
                $creditTotal = 0;
                $balanceTotal = 0;
                
                foreach($entries as $entry) {
                    $debitTotal += $entry->debit ?? 0;
                    $creditTotal += $entry->credit ?? 0;
                }
                $balanceTotal = $debitTotal - $creditTotal;
            @endphp
            
            <div class="summary-cards">
                <div class="summary-card debit">
                    <div class="icon"><i class="fas fa-arrow-up"></i></div>
                    <h5>إجمالي المدين</h5>
                    <p class="amount">{{ number_format($debitTotal, 2) }}</p>
                </div>
                
                <div class="summary-card credit">
                    <div class="icon"><i class="fas fa-arrow-down"></i></div>
                    <h5>إجمالي الدائن</h5>
                    <p class="amount">{{ number_format($creditTotal, 2) }}</p>
                </div>
                
                <div class="summary-card balance">
                    <div class="icon"><i class="fas fa-balance-scale"></i></div>
                    <h5>صافي الرصيد</h5>
                    <p class="amount">{{ number_format($balanceTotal, 2) }}</p>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <table class="ledger-table table-bordered">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar"></i> التاريخ</th>
                            <th><i class="fas fa-file-alt"></i> الوصف</th>
                            <th><i class="fas fa-hashtag"></i> المرجع</th>
                            <th><i class="fas fa-plus-circle"></i> مدين</th>
                            <th><i class="fas fa-minus-circle"></i> دائن</th>
                            <th><i class="fas fa-chart-line"></i> رصيد مدين</th>
                            <th><i class="fas fa-chart-line"></i> رصيد دائن</th>
                            <th><i class="fas fa-calculator"></i> إجمالي الرصيد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $runningDebitTotal = 0;
                            $runningCreditTotal = 0;
                            $runningBalanceTotal = 0;
                        @endphp
                        @foreach($entries as $entry)
                            @php
                                $debit = $entry->debit ?? 0;
                                $credit = $entry->credit ?? 0;
                                $balance = $debit - $credit;

                                $runningDebitTotal += $debit;
                                $runningCreditTotal += $credit;
                                $runningBalanceTotal += $balance;
                            @endphp
                            <tr>
                                <td class="date-cell">
                                    {{ \Carbon\Carbon::parse($entry->created_at)->format('Y-m-d') }}
                                </td>
                                <td class="description-cell" title="{{ $entry->description }}">
                                    {{ $entry->description }}
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $entry->reference }}</span>
                                </td>
                                <td class="debit-cell">
                                    {{ $debit > 0 ? number_format($debit, 2) : '-' }}
                                </td>
                                <td class="credit-cell">
                                    {{ $credit > 0 ? number_format($credit, 2) : '-' }}
                                </td>
                                <td>{{ number_format($runningDebitTotal, 2) }}</td>
                                <td>{{ number_format($runningCreditTotal, 2) }}</td>
                                <td class="balance-cell">{{ number_format($runningBalanceTotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><i class="fas fa-calculator"></i> الإجمالي النهائي</td>
                            <td>{{ number_format($debitTotal, 2) }}</td>
                            <td>{{ number_format($creditTotal, 2) }}</td>
                            <td colspan="2">-</td>
                            <td>{{ number_format($balanceTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-inbox"></i>
            <h4>لا توجد بيانات</h4>
            <p>لم يتم العثور على أي قيود للفترة والحساب المحددين</p>
            <p>يرجى تعديل معايير البحث والمحاولة مرة أخرى</p>
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const loadingSpinner = document.getElementById('loadingSpinner');
    
    // Show loading spinner on form submit
    form.addEventListener('submit', function() {
        loadingSpinner.style.display = 'block';
    });
    
    // Auto-fill today's date if fields are empty
    const fromDate = document.querySelector('input[name="fromDate"]');
    const toDate = document.querySelector('input[name="toDate"]');
    
    if (!fromDate.value) {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        fromDate.value = firstDay.toISOString().split('T')[0];
    }
    
    if (!toDate.value) {
        const today = new Date();
        toDate.value = today.toISOString().split('T')[0];
    }
    
    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('.ledger-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.background = 'linear-gradient(45deg, #e3f2fd, #f3e5f5)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.background = '';
        });
    });
});

// Print function
function printReport() {
    window.print();
}

// Export to CSV function
function exportToCSV() {
    const table = document.querySelector('.ledger-table');
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const csvRow = [];
        cols.forEach(col => {
            csvRow.push(col.textContent.trim());
        });
        csv.push(csvRow.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'general_ledger.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>
@endsection