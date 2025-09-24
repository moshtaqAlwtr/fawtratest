@extends('master')

@section('title')
    إدارة قائمة جهات الاتصال
@stop

@section('css')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --success-color: #4facfe;
            --warning-color: #ffeaa7;
            --danger-color: #fd79a8;
            --dark-color: #2d3748;
            --light-color: #f8f9ff;

            --gradient-warning: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
            --gradient-danger: linear-gradient(135deg, #fd79a8 0%, #fdcb6e 100%);
            --shadow-light: 0 4px 15px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .content-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-medium);
        }

        .content-header-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .breadcrumb {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 0.5rem 1rem;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: white;
        }

        .breadcrumb-item.active {
            color: white;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--shadow-light);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .card-header {
            background: var(--gradient-primary);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 1.5rem;
            border: none;
        }

        .btn {
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: var(--shadow-light);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
        }

        .btn-success {
            background: var(--gradient-success);
            color: white;
        }

        .btn-warning {
            background: var(--gradient-warning);
            color: white;
        }

        .btn-danger {
            background: var(--gradient-danger);
            color: white;
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--gradient-primary);
            color: white;
        }

        .form-control {
            border-radius: 15px;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }

        .table thead th {
            background: var(--gradient-primary);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: scale(1.02);
        }

        .table tbody td {
            padding: 1rem;
            border: none;
            vertical-align: middle;
        }

        .pagination {
            border-radius: 50px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }

        .page-link {
            border: none;
            border-radius: 50px;
            margin: 0 0.25rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: var(--gradient-primary);
            color: white;
            transform: translateY(-2px);
        }

        .page-item.active .page-link {
            background: var(--gradient-primary);
            color: white;
        }

        .alert {
            border-radius: 15px;
            border: none;
            box-shadow: var(--shadow-light);
        }

        .alert-success {
            background: var(--gradient-success);
            color: white;
        }

        .alert-danger {
            background: var(--gradient-danger);
            color: white;
        }

        .dropdown-menu {
            border: none;
            border-radius: 15px;
            box-shadow: var(--shadow-medium);
            padding: 0.5rem;
        }

        .dropdown-item {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: var(--gradient-primary);
            color: white;
            transform: translateX(5px);
        }

        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 0.375rem;
            border: 2px solid var(--primary-color);
        }

        .form-check-input:checked {
            background: var(--gradient-primary);
            border-color: var(--primary-color);
        }

        .search-section {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-light);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .export-btn {
            background: var(--gradient-success);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .stats-card {
            background: var(--gradient-primary);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--shadow-light);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 animate-fade-in" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-4"></i>
                <strong>{{ session('success') }}</strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="content-header animate-fade-in">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="content-header-title">
                    <i class="fas fa-address-book me-3"></i>
                    إدارة قائمة جهات الاتصال
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home me-1"></i>الرئيسية</a></li>
                        <li class="breadcrumb-item active" aria-current="page">جهات الاتصال</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number">{{ isset($clients) ? $clients->total() : 0 }}</div>
                    <div>إجمالي العملاء</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- قسم البحث -->
        <div class="search-section animate-fade-in">
            <form class="form" method="GET" action="{{ route('clients.contacts') }}" id="searchForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="search" class="form-label fw-bold">
                                <i class="fas fa-search me-2"></i>البحث الأساسي
                            </label>
                            <input type="text" id="search" class="form-control"
                                   placeholder="ابحث بالاسم أو الكود..." name="search"
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="advanced_search" class="form-label fw-bold">
                                <i class="fas fa-filter me-2"></i>البحث المتقدم
                            </label>
                            <input type="text" id="advanced_search" class="form-control"
                                   placeholder="بريد إلكتروني، هاتف، أو جوال..." name="advanced_search"
                                   value="{{ request('advanced_search') }}">
                        </div>
                    </div>
                </div>
                <div class="form-actions mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>بحث
                    </button>
                    <button type="reset" class="btn btn-outline-primary" onclick="clearSearch()">
                        <i class="fas fa-times me-2"></i>إلغاء
                    </button>
                </div>
            </form>
        </div>

        <!-- قسم الإجراءات -->
        <div class="card animate-fade-in">
            <div class="card-body">
                <div class="action-buttons">


                    <a href="{{ route('clients.create') }}" class="btn btn-success">
                        <i class="fas fa-plus-circle me-2"></i>إضافة عميل جديد
                    </a>

                    <button class="btn btn-outline-primary" onclick="importData()">
                        <i class="fas fa-cloud-upload-alt me-2"></i>استيراد البيانات
                    </button>

                    <button class="export-btn" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-2"></i>تصدير إلى Excel
                    </button>

                    <button class="btn btn-warning" onclick="exportSelected()" style="display: none;" id="exportSelectedBtn">
                        <i class="fas fa-download me-2"></i>تصدير المحدد
                    </button>
                </div>

                <!-- شريط التحميل -->
                <div class="loading" id="loadingBar">
                    <div class="spinner mx-auto"></div>
                    <p class="mt-2">جاري معالجة البيانات...</p>
                </div>
            </div>
        </div>

        <!-- جدول البيانات -->
        <div class="card animate-fade-in">
            <div class="card-body p-0">
                @if (isset($clients) && !empty($clients) && count($clients) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="clientsTable">
                            <thead>
                                <tr>

                                    <th width="20%">
                                        <i class="fas fa-barcode me-2"></i>الكود
                                    </th>
                                    <th width="30%">
                                        <i class="fas fa-building me-2"></i>الاسم التجاري
                                    </th>
                                    <th width="20%" class="text-center">
                                        <i class="fas fa-phone me-2"></i>الهاتف
                                    </th>
                                    <th width="15%" class="text-center">
                                        <i class="fas fa-cogs me-2"></i>الإجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $client)
                                    <tr class="client-row" data-client-id="{{ $client->id }}">
                                        <td class="text-center">
                                            <div class="form-check">
                                                <input class="form-check-input client-checkbox" type="checkbox"
                                                       value="{{ $client->id }}"
                                                       data-client-code="{{ $client->code }}"
                                                       data-client-name="{{ $client->trade_name }}"
                                                       data-client-phone="{{ $client->phone }}">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary fs-6">{{ $client->code }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-3">
                                                    <i class="fas fa-user-tie"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $client->trade_name }}</h6>
                                                    <small class="text-muted">عميل نشط</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="tel:{{ $client->phone }}" class="text-decoration-none">
                                                <span class="badge bg-primary fs-6">
                                                    <i class="fas fa-phone me-1"></i>{{ $client->phone }}
                                                </span>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary"
                                                        type="button" id="dropdownMenuButton{{ $client->id }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $client->id }}">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('clients.show', $client->id) }}">
                                                            <i class="fas fa-eye me-2 text-success"></i>عرض التفاصيل
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#">
                                                            <i class="fas fa-edit me-2 text-warning"></i>تعديل
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="deleteClient({{ $client->id }})">
                                                            <i class="fas fa-trash me-2"></i>حذف
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- شريط التنقل -->
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted">
                                    عرض {{ $clients->firstItem() }} إلى {{ $clients->lastItem() }}
                                    من إجمالي {{ $clients->total() }} عميل
                                </span>
                            </div>
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm mb-0">
                                    @if ($clients->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fas fa-angle-double-right"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $clients->url(1) }}">
                                                <i class="fas fa-angle-double-right"></i>
                                            </a>
                                        </li>
                                    @endif

                                    @if ($clients->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fas fa-angle-right"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $clients->previousPageUrl() }}">
                                                <i class="fas fa-angle-right"></i>
                                            </a>
                                        </li>
                                    @endif

                                    <li class="page-item active">
                                        <span class="page-link">
                                            {{ $clients->currentPage() }} من {{ $clients->lastPage() }}
                                        </span>
                                    </li>

                                    @if ($clients->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $clients->nextPageUrl() }}">
                                                <i class="fas fa-angle-left"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fas fa-angle-left"></i>
                                            </span>
                                        </li>
                                    @endif

                                    @if ($clients->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $clients->url($clients->lastPage()) }}">
                                                <i class="fas fa-angle-double-left"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fas fa-angle-double-left"></i>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h4 class="mt-3 text-muted">لا توجد بيانات</h4>
                        <p class="text-muted">لم يتم العثور على أي عملاء، يرجى إضافة عملاء جدد أو تعديل معايير البحث.</p>
                        <a href="{{ route('clients.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus-circle me-2"></i>إضافة عميل جديد
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // تحديد الكل
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const selectAllTableCheckbox = document.getElementById('select-all');
            const clientCheckboxes = document.querySelectorAll('.client-checkbox');
            const exportSelectedBtn = document.getElementById('exportSelectedBtn');

            if (selectAllCheckbox) {
                selectAllTableCheckbox.checked = selectAllCheckbox.checked;
            }

            clientCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox ? selectAllCheckbox.checked : selectAllTableCheckbox.checked;
            });

            updateExportSelectedButton();
        }

        // ربط checkbox الجدول بالـ checkbox الرئيسي
        document.getElementById('select-all').addEventListener('change', function() {
            const clientCheckboxes = document.querySelectorAll('.client-checkbox');
            const selectAllCheckbox = document.getElementById('selectAll');

            clientCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = this.checked;
            }

            updateExportSelectedButton();
        });

        // تحديث زر التصدير المحدد
        function updateExportSelectedButton() {
            const checkedBoxes = document.querySelectorAll('.client-checkbox:checked');
            const exportSelectedBtn = document.getElementById('exportSelectedBtn');

            if (checkedBoxes.length > 0) {
                exportSelectedBtn.style.display = 'inline-block';
                exportSelectedBtn.innerHTML = `<i class="fas fa-download me-2"></i>تصدير المحدد (${checkedBoxes.length})`;
            } else {
                exportSelectedBtn.style.display = 'none';
            }
        }

        // إضافة مستمع لكل checkbox
        document.querySelectorAll('.client-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateExportSelectedButton);
        });

        // مسح البحث
        function clearSearch() {
            document.getElementById('search').value = '';
            document.getElementById('advanced_search').value = '';
            document.getElementById('searchForm').submit();
        }

        // تصدير جميع البيانات إلى Excel
        function exportToExcel() {
            showLoading();

            // جمع بيانات الجدول
            const table = document.getElementById('clientsTable');
            const data = [];

            // إضافة الرؤوس
            const headers = ['الكود', 'الاسم التجاري', 'الهاتف'];
            data.push(headers);

            // إضافة البيانات
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            for (let i = 0; i <scpit rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                const rowData = [
                    cells[1].textContent.trim(), // الكود
                    cells[2].querySelector('h6').textContent.trim(), // الاسم التجاري
                    cells[3].textContent.trim().replace(/[^\d+]/g, '') // الهاتف
                ];
                data.push(rowData);
            }

            // إنشاء ملف Excel
            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'قائمة العملاء');

            // تحديد عرض الأعمدة
            const wscols = [
                { wch: 15 }, // الكود
                { wch: 30 }, // الاسم التجاري
                { wch: 15 }  // الهاتف
            ];
            ws['!cols'] = wscols;

            // تصدير الملف
            const fileName = `قائمة_العملاء_${new Date().toISOString().split('T')[0]}.xlsx`;
            XLSX.writeFile(wb, fileName);

            hideLoading();
            showNotification('تم تصدير البيانات بنجاح!', 'success');
        }

        // تصدير البيانات المحددة
        function exportSelected() {
            const checkedBoxes = document.querySelectorAll('.client-checkbox:checked');

            if (checkedBoxes.length === 0) {
                showNotification('يرجى تحديد عملاء للتصدير!', 'warning');
                return;
            }

            showLoading();

            const data = [];
            const headers = ['الكود', 'الاسم التجاري', 'الهاتف'];
            data.push(headers);

            checkedBoxes.forEach(checkbox => {
                const rowData = [
                    checkbox.dataset.clientCode,
                    checkbox.dataset.clientName,
                    checkbox.dataset.clientPhone
                ];
                data.push(rowData);
            });

            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'العملاء المحددين');

            const wscols = [
                { wch: 15 },
                { wch: 30 },
                { wch: 15 }
            ];
            ws['!cols'] = wscols;

            const fileName = `العملاء_المحددين_${new Date().toISOString().split('T')[0]}.xlsx`;
            XLSX.writeFile(wb, fileName);

            hideLoading();
            showNotification(`تم تصدير ${checkedBoxes.length} عميل بنجاح!`, 'success');
        }

        // إظهار التحميل
        function showLoading() {
            document.getElementById('loading').style.display = 'block';
        }

        // اخفاء التحميل
        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
        }

        // استدعاء الوظيفة عند التحميل
        window.addEventListener('load', function() {
            updateExportSelectedButton();
        });



    </scpit>
    @endsection
