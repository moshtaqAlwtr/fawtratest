@extends('master')

@section('title')
    تقرير المصروفات حسب المدة الزمنية
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            direction: rtl;
            background-color: #f8f9fa;
        }

        .card-header {
            background-color: #007bff;
            color: white;
        }

        .btn-custom {
            background-color: #28a745;
            color: white;
        }

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }

        .hidden {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid mt-4">
        <div class="card mb-4">
            <div class="card-header">
                <h4>تقرير المصروفات حسب المدة الزمنية</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('GeneralAccountReports.splitExpensesByTimePeriod') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">الخزينة</label>
                            <select name="treasury" class="form-control">
                                <option value="">كل الخزائن</option>
                                @foreach ($treasuries as $treasury)
                                    <option value="{{ $treasury->id }}"
                                        {{ request('treasury') == $treasury->id ? 'selected' : '' }}>
                                        {{ $treasury->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">الموظف</label>
                            <select name="employee" class="form-control">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ request('employee') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">الفرع</label>
                            <select name="branch" class="form-control">
                                <option value="all">كل الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">العملة</label>
                            <select name="currency" class="form-control">
                                <option value="all">كل العملات</option>
                                <option value="SAR" {{ request('currency') == 'SAR' ? 'selected' : '' }}>SAR</option>
                                <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ request('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">نوع التقرير</label>
                            <select name="report_type" class="form-control">
                                <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>شهري
                                </option>
                                <option value="quarterly" {{ request('report_type') == 'quarterly' ? 'selected' : '' }}>ربع
                                    سنوي</option>
                                <option value="yearly" {{ request('report_type') == 'yearly' ? 'selected' : '' }}>سنوي
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">نوع التقرير</label>
                            <select name="report_type" class="form-control">
                                <option value="daily" {{ request('report_type') == 'daily' ? 'selected' : '' }}>يومي
                                </option>
                                <option value="weekly" {{ request('report_type') == 'weekly' ? 'selected' : '' }}>أسبوعي
                                </option>
                                <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>شهري
                                </option>
                                <option value="quarterly" {{ request('report_type') == 'quarterly' ? 'selected' : '' }}>ربع
                                    سنوي</option>
                                <option value="yearly" {{ request('report_type') == 'yearly' ? 'selected' : '' }}>سنوي
                                </option>
                            </select>

                        </div>


                    </div>
                    <button type="submit" class="btn btn-custom mt-3">عرض التقرير</button>
                    <a href="{{ route('GeneralAccountReports.splitExpensesByTimePeriod') }}"
                        class="btn btn-custom mt-3">الغاء الفلتر</a>
                </form>
            </div>
        </div>

        @if (!empty($groupedExpenses))
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>تفاصيل المصروفات</h5>
                    <div>
                        <button id="summaryViewBtn" class="btn btn-sm btn-outline-primary active">عرض ملخص</button>
                        <button id="detailedViewBtn" class="btn btn-sm btn-outline-secondary">عرض تفصيلي</button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="expensesChart" width="400" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>ملخص الإجماليات</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>إجمالي المصروفات</th>
                                            <td>{{ number_format($expenses->sum('amount'), 2) }} ريال</td>
                                        </tr>
                                        <tr>
                                            <th>إجمالي الضرائب</th>
                                            <td>{{ number_format($expenses->sum('tax1_amount') + $expenses->sum('tax2_amount'), 2) }}
                                                ريال</td>
                                        </tr>
                                        <tr>
                                            <th>المجموع الكلي</th>
                                            <td>{{ number_format($expenses->sum('amount') + $expenses->sum('tax1_amount') + $expenses->sum('tax2_amount'), 2) }}
                                                ريال</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">

                <div class="card-body">

                    <!-- In your summary table section, modify the grouping -->
                    @if(!empty($expenses))
                    <div id="summaryTable">
                        <table class="table table-bordered table-striped">
                            <thead class="table-header">
                                <tr>
                                    <th>الفترة الزمنية</th>
                                    <th>الكود</th>
                                    <th>التاريخ</th>
                                    <th>خزينة</th>
                                    <th>التصنيف</th>
                                    <th>البائع</th>
                                    <th>الحساب الفرعي</th>
                                    <th>موظف</th>
                                    <th>ملاحظة</th>
                                    <th>فرع</th>
                                    <th>المبلغ</th>
                                    <th>الضرائب</th>
                                    <th>الإجمالي مع الضريبة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupedExpenses as $periodKey => $periodExpenses)
                                    <!-- Period Header -->
                                    <tr style="background-color: #f8f9fa;">
                                        <td colspan="13"><strong>{{ $period }} - {{ $periodKey }}</strong></td>
                                    </tr>

                                    <!-- Expenses in this period -->
                                    @foreach ($periodExpenses as $expense)
                                        <tr>
                                            <td>{{ $periodKey }}</td>
                                            <td>{{ $expense->code }}</td>
                                            <td>{{ $expense->date }}</td>
                                            <td>{{ $expense->treasury->name ?? 'N/A' }}</td>
                                            <td>{{ $expense->expenses_category->name ?? 'N/A' }}</td>
                                            <td>{{ $expense->vendor_id }}</td>
                                            <td>{{ $expense->sup_account }}</td>
                                            <td>{{ $expense->seller }}</td>
                                            <td>{{ $expense->description }}</td>
                                            <td>{{ $expense->branch->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($expense->amount, 2) }}</td>
                                            <td>{{ number_format($expense->tax1_amount + $expense->tax2_amount, 2) }}</td>
                                            <td>{{ number_format($expense->amount + $expense->tax1_amount + $expense->tax2_amount, 2) }}</td>
                                        </tr>
                                    @endforeach

                                    <!-- Period Total -->
                                    <tr style="background-color: #e9ecef;">
                                        <td colspan="10"><strong>مجموع الفترة ({{ $periodKey }})</strong></td>
                                        <td><strong>{{ number_format($periodExpenses->sum('amount'), 2) }}</strong></td>
                                        <td><strong>{{ number_format($periodExpenses->sum('tax1_amount') + $periodExpenses->sum('tax2_amount'), 2) }}</strong></td>
                                        <td><strong>{{ number_format($periodExpenses->sum('amount') + $periodExpenses->sum('tax1_amount') + $periodExpenses->sum('tax2_amount'), 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                    <div id="detailedView" class="mt-4 hidden">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>الفترة الزمنية</th>
                                        <th>رقم المصروف</th>
                                        <th>التاريخ</th>
                                        <th>الخزينة</th>
                                        <th>الموظف</th>
                                        <th>المبلغ</th>
                                        <th>الضريبة</th>
                                        <th>المجموع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupedExpenses as $period => $periodExpenses)
                                        @foreach ($periodExpenses as $expense)
                                            <tr>
                                                <td>{{ $period }}</td>
                                                <td>{{ $expense->code }}</td>
                                                <td>{{ $expense->date }}</td>
                                                <td>{{ $expense->treasury->name ?? 'غير محدد' }}</td>
                                                <td>{{ $expense->employee->full_name ?? 'غير محدد' }}</td>
                                                <td>{{ number_format($expense->amount, 2) }}</td>
                                                <td>{{ number_format($expense->tax1_amount + $expense->tax2_amount, 2) }}
                                                </td>
                                                <td>{{ number_format($expense->amount + $expense->tax1_amount + $expense->tax2_amount, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        // Chart Initialization
        @if (!empty($chartLabels) && !empty($chartData))
            const ctx = document.getElementById('expensesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'المصروفات',
                        data: @json($chartData),
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'المصروفات حسب الفترة الزمنية'
                        }
                    }
                }
            });
        @endif

        // View Toggle
        document.getElementById('summaryViewBtn').addEventListener('click', function() {
            document.getElementById('summaryView').classList.remove('hidden');
            document.getElementById('detailedView').classList.add('hidden');
            this.classList.add('active');
            document.getElementById('detailedViewBtn').classList.remove('active');
        });

        document.getElementById('detailedViewBtn').addEventListener('click', function() {
            document.getElementById('detailedView').classList.remove('hidden');
            document.getElementById('summaryView').classList.add('hidden');
            this.classList.add('active');
            document.getElementById('summaryViewBtn').classList.remove('active');
        });

        // Export Functions
        function exportToExcel() {
            const table = document.getElementById('summaryView').querySelector('table');
            const wb = XLSX.utils.table_to_book(table);
            XLSX.writeFile(wb, 'expenses_report.xlsx');
        }

        function exportToPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();
            doc.text('تقرير المصروفات', 10, 10);
            doc.autoTable({
                html: '#summaryView table'
            });
            doc.save('expenses_report.pdf');
        }
    </script>
@endsection
