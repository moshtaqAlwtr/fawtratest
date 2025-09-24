@extends('master')

@section('title')
    دليل الحسابات
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/report.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تقرير دليل الحسابات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('GeneralAccountReports.ChartOfAccounts') }}" method="GET">
                <div class="row g-3 align-items-center">
                    <!-- فلترة حسب مستوى الحساب -->
                    <div class="col-md-3">
                        <label for="account_level" class="form-label">مستوى الحساب:</label>
                        <select class="form-control" id="account_level" name="account_level">
                            <option value="">حساب رئيسي</option>
                            <option value="main">حساب رئيسي</option>
                            <option value="sub">حساب فرعي</option>
                        </select>
                    </div>

                    <!-- فلترة حسب نوع الحساب -->
                    <div class="col-md-3">
                        <label for="account_type" class="form-label">نوع الحساب:</label>
                        <select class="form-control" id="account_type" name="account_type">
                            <option value="">الكل</option>
                            <option value="debit">مدين</option>
                            <option value="credit">دائن</option>
                        </select>
                    </div>

                    <!-- فلترة حسب نوع الحساب (عملاء/موردين) -->
                    <div class="col-md-3">
                        <label for="account_category" class="form-label">عرض كل الحسابات:</label>
                        <select class="form-control" id="account_category" name="account_category">
                            <option value="">الكل</option>
                            <option value="customer">حسابات العملاء</option>
                            <option value="supplier">حسابات الموردين</option>
                        </select>
                    </div>

                    <!-- فلترة حسب الفرع -->
                    <div class="col-md-3">
                        <label for="branch" class="form-label">الفرع:</label>
                        <select class="form-control" id="branch" name="branch">
                            <option value="all">كل الفروع</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- فلترة حسب ترتيب الكود -->
                    <div class="col-md-3">
                        <label for="order_by" class="form-label">ترتيب حسب:</label>
                        <select class="form-control" id="order_by" name="order_by">
                            <option value="">الكود تصاعدي</option>
                            <option value="asc">الكود تصاعدي</option>
                            <option value="desc">الكود تنازلي</option>
                        </select>
                    </div>

                    <!-- زر عرض التقرير -->
                    <div class="col-md-12 text-center mt-3">
                        <button type="submit" class="btn btn-custom">عرض التقرير</button>
                        <a href="{{ route('GeneralAccountReports.ChartOfAccounts') }}" class="btn btn-custom">الغاء
                            الفلتر</a>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-end mt-3">
                <button class="btn btn-outline-secondary mr-2" onclick="exportExcel()">
                    <i class="fas fa-file-excel"></i> تصدير إلى Excel
                </button>
                <button class="btn btn-outline-secondary mr-2" onclick="exportCSV()">
                    <i class="fas fa-file-csv"></i> تصدير إلى CSV
                </button>
                <button class="btn btn-outline-secondary" onclick="printTable()">
                    <i class="fas fa-print"></i> طباعة
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="accountsTable">
                    <thead>
                        <tr class="report-results-head">
                            <th class="code-column">كود الحساب</th>
                            <th class="name-column">اسم الحساب</th>
                            <th class="type-column">نوع الحساب</th>
                            <th class="level-column">مستوى الحساب</th>
                            <th class="cost_centers-column">مركز التكلفة</th>
                            <th class="branch_ids-column">فرع الحساب</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $account)
                            <tr>
                                <td>{{ $account->code }}</td>
                                <td>{{ $account->name }}</td>
                                <td>
                                    @if ($account->balance_type == 'debit')
                                        مدين
                                    @else
                                        دائن
                                    @endif
                                </td>
                                <td>{{ $account->parent_id ? 'فرعي' : 'رئيسي' }}</td>
                                <td>{{ optional($account->costCenter)->name ?? 'N/A' }}</td>
                                <td>{{ optional($account->branch)->name ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- أزرار التصدير -->

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script>
        // دالة لتصدير الجدول إلى Excel
        function exportExcel() {
            const table = document.getElementById("accountsTable");
            const workbook = XLSX.utils.table_to_book(table);
            XLSX.writeFile(workbook, "دليل_الحسابات.xlsx");
        }

        // دالة لتصدير الجدول إلى CSV
        function exportCSV() {
            const table = document.getElementById("accountsTable");
            const rows = table.querySelectorAll("tr");
            let csv = [];

            for (let i = 0; i < rows.length; i++) {
                const row = [],
                    cols = rows[i].querySelectorAll("td, th");
                for (let j = 0; j < cols.length; j++) {
                    row.push(cols[j].innerText);
                }
                csv.push(row.join(","));
            }

            const csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "دليل_الحسابات.csv");
            document.body.appendChild(link);
            link.click();
        }

        // دالة للطباعة
        function printTable() {
            window.print();
        }
    </script>
@endsection
