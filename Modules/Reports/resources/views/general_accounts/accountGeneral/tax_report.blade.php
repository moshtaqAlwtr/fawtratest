@extends('master')

@section('title')
    تقرير الضرائب
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>تقرير الضرائب</title>
    <style>
        .gradient-bg {
            background: linear-gradient(90deg, #007bff, #6610f2);
            color: white;
        }

        body {
            background-color: #f8f9fa;
            direction: rtl;
        }

        .card-header {
            background-color: #007bff;
            color: white;
        }

        .btn-custom {
            background-color: #28a745;
            color: white;
        }

        .table-header {
            background-color: #e9ecef;
        }

        .hidden {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="content-header row mb-3">
        <div class="content-header-left col-md-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="content-header-title float-start mb-0">تقارير الضرائب</h2>
                </div>
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper float-start">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <!-- Filter Section -->
        <div class="card p-4 mb-3">
            <form action="{{ route('GeneralAccountReports.taxReport') }}" method="GET">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <label for="inputTaxType" class="form-label">الضرائب:</label>
                        <select class="form-control" id="inputTaxType" name="tax_type">
                            <option value="all" {{ request('tax_type') == 'all' ? 'selected' : '' }}>كل الضرائب</option>
                            <option value="vat" {{ request('tax_type') == 'vat' ? 'selected' : '' }}>ضريبة مضافة</option>
                            <option value="income" {{ request('tax_type') == 'income' ? 'selected' : '' }}>ضريبة دخل</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="inputDateFrom" class="form-label">الفترة من / إلى:</label>
                        <input type="date" class="form-control" id="inputDateFrom" name="date_from" value="{{ request('date_from') }}">
                        <input type="date" class="form-control mt-2" id="inputDateTo" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="inputGroupBy" class="form-label">تجميع حسب:</label>
                        <select class="form-control" id="inputGroupBy" name="group_by">
                            <option value="item" {{ request('group_by') == 'item' ? 'selected' : '' }}>البند</option>
                            <option value="branch" {{ request('group_by') == 'branch' ? 'selected' : '' }}>الفرع</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="inputCurrency" class="form-label">العملة:</label>
                        <select class="form-control" id="inputCurrency" name="currency">
                            <option value="SAR" {{ request('currency') == 'SAR' ? 'selected' : '' }}>SAR</option>
                            <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="inputBranch" class="form-label">الفرع:</label>
                        <select class="form-control" id="inputBranch" name="branch">
                            <option value="all" {{ request('branch') == 'all' ? 'selected' : '' }}>كل الفروع</option>
                            <option value="1" {{ request('branch') == '1' ? 'selected' : '' }}>الفرع الرئيسي</option>
                            <option value="2" {{ request('branch') == '2' ? 'selected' : '' }}>الفرع الفرعي</option>
                        </select>
                    </div>
                </div>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="showNonTaxable" name="show_non_taxable" {{ request('show_non_taxable') ? 'checked' : '' }}>
                    <label class="form-check-label" for="showNonTaxable">إظهار البنود غير الخاضعة للضريبة</label>
                </div>
                <button type="submit" class="btn btn-custom mt-3">عرض التقرير</button>
            </form>
        </div>

        <!-- Report Summary -->
        <div class="card p-4 mb-3">
            <h5>الجميع إلى ({{ request('currency', 'SAR') }})</h5>
            <p>من {{ request('date_from', 'بداية التاريخ') }} إلى {{ request('date_to', 'نهاية التاريخ') }}</p>
            <p>مؤسسة أعمال خاصة للتجارة<br>الرياض<br>الرياض</p>
        </div>

        <!-- Table Section -->
        <div class="card p-4 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <button class="btn btn-outline-primary" id="summaryButton">الملخص</button>
                    <button class="btn btn-outline-primary" id="detailsButton">التفاصيل</button>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        خيارات التصدير
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportCSV()">تصدير إلى CSV</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportExcel()">تصدير إلى Excel</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportPDF()">تصدير إلى PDF</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printTable()">طباعة</a></li>
                    </ul>
                </div>
            </div>

            <!-- Summary Table -->
            <div id="summaryTable">
                <table class="table table-bordered table-striped">
                    <thead class="table-header">
                        <tr>
                            <th>رقم</th>
                            <th>الممول</th>
                            <th>الرقم الضريبي</th>
                            <th>السجل التجاري</th>
                            <th>التاريخ</th>
                            <th>البند</th>
                            <th>الوصف</th>
                            <th>المبلغ الخاضع للضريبة</th>
                            <th>الضرائب</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- فواتير المبيعات -->
                        <tr class="table-info">
                            <td colspan="9" class="text-center fw-bold">فواتير المبيعات</td>
                        </tr>
                        @foreach ($taxData['sales'] as $invoice)
                            <tr>
                                <td>{{ $invoice->code }}</td>
                                <td>{{ $invoice->client->trade_name ?? 'غير محدد' }}</td>
                                <td>{{ $invoice->client->tax_number ?? 'غير محدد' }}</td>
                                <td>{{ $invoice->client->commercial_registration ?? 'غير محدد' }}</td>
                                <td>{{ $invoice->invoice_date }}</td>
                                <td>{{ $invoice->items->first()->name ?? 'غير محدد' }}</td>
                                <td>{{ $invoice->items->first()->description ?? 'غير محدد' }}</td>
                                <td>{{ number_format($invoice->total, 2) }}</td>
                                <td>{{ number_format($invoice->tax_total, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-success">
                            <td colspan="7" class="text-end fw-bold">إجمالي المبيعات:</td>
                            <td>{{ number_format($taxData['sales']->sum('total'), 2) }}</td>
                            <td>{{ number_format($taxData['sales']->sum('tax_total'), 2) }}</td>
                        </tr>

                        <!-- فواتير المرتجعات -->
                        <tr class="table-warning">
                            <td colspan="9" class="text-center fw-bold">فواتير المرتجعات</td>
                        </tr>
                        @foreach ($taxData['returns'] as $return)
                            <tr>
                                <td>{{ $return->code }}</td>
                                <td>{{ $return->client->trade_name ?? 'غير محدد' }}</td>
                                <td>{{ $return->client->tax_number ?? 'غير محدد' }}</td>
                                <td>{{ $return->client->commercial_registration ?? 'غير محدد' }}</td>
                                <td>{{ $return->invoice_date }}</td>
                                <td>{{ $return->items->first()->name ?? 'غير محدد' }}</td>
                                <td>{{ $return->items->first()->description ?? 'غير محدد' }}</td>
                                <td>{{ number_format($return->total, 2) }}</td>
                                <td>{{ number_format($return->tax_total, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-success">
                            <td colspan="7" class="text-end fw-bold">إجمالي المرتجعات:</td>
                            <td>{{ number_format($taxData['returns']->sum('total'), 2) }}</td>
                            <td>{{ number_format($taxData['returns']->sum('tax_total'), 2) }}</td>
                        </tr>

                        <!-- فواتير المشتريات -->
                        <tr class="table-danger">
                            <td colspan="9" class="text-center fw-bold">فواتير المشتريات</td>
                        </tr>
                        @foreach ($taxData['purchases'] as $purchase)
                            <tr>
                                <td>{{ $purchase->code }}</td>
                                <td>{{ $purchase->supplier->trade_name ?? 'غير محدد' }}</td>
                                <td>{{ $purchase->supplier->tax_number ?? 'غير محدد' }}</td>
                                <td>{{ $purchase->supplier->commercial_registration ?? 'غير محدد' }}</td>
                                <td>{{ $purchase->date }}</td>
                                <td>{{ $purchase->items->first()->product->name ?? 'غير محدد' }}</td>
                                <td>{{ $purchase->items->first()->description ?? 'غير محدد' }}</td>
                                <td>{{ number_format($purchase->subtotal, 2) }}</td>
                                <td>{{ number_format($purchase->total_tax, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-success">
                            <td colspan="7" class="text-end fw-bold">إجمالي المشتريات:</td>
                            <td>{{ number_format($taxData['purchases']->sum('subtotal'), 2) }}</td>
                            <td>{{ number_format($taxData['purchases']->sum('total_tax'), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="detailsTable" class="hidden">
                <table class="table table-bordered table-striped">
                    <thead class="table-header">
                        <tr>
                            <th>الضرائب</th>
                            <th>المبلغ الخاضع للضريبة</th>
                            <th>الضرائب</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taxData['sales'] as $invoice)
                            <tr>
                                <td>{{ $invoice->tax_type }}</td>
                                <td>{{ number_format($invoice->total, 2) }}</td>
                                <td>{{ number_format($invoice->tax_total, 2) }}</td>
                            </tr>
                        @endforeach
                        @foreach ($taxData['purchases'] as $purchase)
                            <tr>
                                <td>{{ $purchase->tax_type }}</td>
                                <td>{{ number_format($purchase->subtotal, 2) }}</td>
                                <td>{{ number_format($purchase->total_tax, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle between summary and details table visibility
        document.getElementById('summaryButton').addEventListener('click', function () {
            document.getElementById('summaryTable').classList.remove('hidden');
            document.getElementById('detailsTable').classList.add('hidden');
        });

        document.getElementById('detailsButton').addEventListener('click', function () {
            document.getElementById('detailsTable').classList.remove('hidden');
            document.getElementById('summaryTable').classList.add('hidden');
        });

        // Functions for export and printing
        function exportCSV() {
            alert("تصدير إلى CSV تم تنفيذه");
        }

        function exportExcel() {
            alert("تصدير إلى Excel تم تنفيذه");
        }

        function exportPDF() {
            alert("تصدير إلى PDF تم تنفيذه");
        }

        function printTable() {
            window.print();
        }
    </script>
@endsection
