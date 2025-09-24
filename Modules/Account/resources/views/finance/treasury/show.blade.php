@extends('master')

@section('title')
    خزائن وحسابات بنكية
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">خزائن وحسابات بنكية</h2>
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
        <div class="card-title p-2">
            <a href="{{ route('treasury.transferCreate') }}" class="btn btn-outline-success btn-sm">
                تحويل <i class="fa fa-reply-all"></i>
            </a>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <strong>
                                @if ($treasury->type_accont == 0)
                                    <i class="fa fa-archive"></i>
                                @else
                                    <i class="fa fa-bank"></i>
                                @endif
                                {{ $treasury->name }}
                            </strong>
                        </div>

                        <div>
                            @if ($treasury->is_active == 0)
                                <div class="badge badge-pill badge-success">نشط</div>
                            @else
                                <div class="badge badge-pill badge-danger">غير نشط</div>
                            @endif
                        </div>

                        <div>
                            <small>SAR </small> <strong>{{ number_format($treasury->balance, 2, '.', ',') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            @include('layouts.alerts.error')
            @include('layouts.alerts.success')

            <div class="card">
                <div class="card-body">
                    <!-- 🔹 التبويبات -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home"
                                role="tab">التفاصيل</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="transactions-tab" data-toggle="tab" href="#transactions"
                                role="tab">معاملات النظام</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="transfers-tab" data-toggle="tab" href="#transfers"
                                role="tab">التحويلات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="activate-tab" data-toggle="tab" href="#activate" role="tab">سجل
                                النشاطات</a>
                        </li>
                    </ul>


                    <div class="tab-content">
                        <!-- 🔹 تبويب التفاصيل -->
                        <div class="tab-pane fade show active" id="home" role="tabpanel">
                            <div class="card">
                                <div class="card-header"><strong>معلومات الحساب</strong></div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <td><small>الاسم</small> : <strong>{{ $treasury->name }}</strong></td>
                                            @if ($treasury->type_accont == 1)
                                                <td><small>اسم الحساب البنكي</small> :
                                                    <strong>{{ $treasury->name }}</strong>
                                                </td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td><small>النوع</small> : <strong>
                                                    @if ($treasury->type_accont == 0)
                                                        خزينة
                                                    @else
                                                        حساب بنكي
                                                    @endif
                                                </strong></td>
                                            <td><small>الحالة</small> :
                                                @if ($treasury->is_active == 0)
                                                    <div class="badge badge-pill badge-success">نشط</div>
                                                @else
                                                    <div class="badge badge-pill badge-danger">غير نشط</div>
                                                @endif
                                            </td>
                                            <td><small>المبلغ</small> : <strong
                                                    style="color: #00CFE8">{{ number_format($treasury->balance, 2) }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>الوصف</strong> : <small>{{ $treasury->description ?? '' }}</small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="transactions" role="tabpanel">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center p-2">
                                    <div class="d-flex gap-2">
                                        <span class="hide-button-text">بحث وتصفية</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button class="btn btn-outline-secondary btn-sm" onclick="toggleSearchFields(this)">
                                            <i class="fa fa-times"></i>
                                            <span class="hide-button-text">اخفاء</span>
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                                            data-bs-target="#advancedSearchForm" onclick="toggleSearchText(this)">
                                            <i class="fa fa-filter"></i>
                                            <span class="button-text">متقدم</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form id="operationsSearchForm" class="form">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label for="from_date">التاريخ من</label>
                                                <input type="date" class="form-control" name="from_date" id="from_date">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="to_date">التاريخ إلى</label>
                                                <input type="date" class="form-control" name="to_date" id="to_date">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="operation_type">نوع العملية</label>
                                                <select name="operation_type" class="form-control" id="operation_type">
                                                    <option value="">جميع العمليات</option>
                                                    <option value="payment">عمليات الدفع</option>
                                                    <option value="receipt">سندات القبض</option>
                                                    <option value="transfer">التحويلات</option>
                                                    <option value="expense">سندات الصرف</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>&nbsp;</label>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-primary"
                                                        onclick="loadOperations()">
                                                        <i class="fa fa-search"></i> بحث
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning"
                                                        onclick="resetFilters()">
                                                        <i class="fa fa-refresh"></i> إلغاء
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- البحث المتقدم -->
                                        <div class="collapse" id="advancedSearchForm">
                                            <div class="row g-3 mt-2">
                                                <div class="col-md-4">
                                                    <label for="amount_from">المبلغ من</label>
                                                    <input type="number" class="form-control" name="amount_from"
                                                        id="amount_from" step="0.01" placeholder="المبلغ الأدنى">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="amount_to">المبلغ إلى</label>
                                                    <input type="number" class="form-control" name="amount_to"
                                                        id="amount_to" step="0.01" placeholder="المبلغ الأعلى">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="branch_filter">الفرع</label>
                                                    <select name="branch_id" class="form-control" id="branch_filter">
                                                        <option value="">جميع الفروع</option>
                                                        @foreach ($branches as $branch)
                                                            <option value="{{ $branch->id }}">{{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Loading indicator -->
                                <div id="operationsLoading" class="text-center p-3" style="display: none;">
                                    <i class="fa fa-spinner fa-spin"></i> جاري التحميل...
                                </div>

                                <!-- Operations table -->
                                <div id="operationsTableContainer">
                                    <table class="table table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%"><i class="fa fa-list"></i></th>
                                                <th>العملية</th>
                                                <th>الوصف</th>
                                                <th>الإيداع</th>
                                                <th>السحب</th>
                                                <th>الرصيد بعد العملية</th>
                                                <th>التاريخ</th>
                                                <th width="10%">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody id="operationsTableBody">
                                            <!-- سيتم تحميل البيانات هنا عبر AJAX -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <nav aria-label="Page navigation" id="operationsPagination">
                                    <!-- سيتم إنشاء التنقل هنا عبر JavaScript -->
                                </nav>
                            </div>
                        </div>

                        <div class="tab-pane" id="transfers" role="tabpanel">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center p-2">
                                    <div class="d-flex gap-2">
                                        <span class="hide-button-text">بحث وتصفية</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button class="btn btn-outline-secondary btn-sm"
                                            onclick="toggleSearchFields(this)">
                                            <i class="fa fa-times"></i>
                                            <span class="hide-button-text">اخفاء</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form class="form" id="searchForm" method="GET"
                                        action="{{ route('invoices.index') }}">
                                        <div class="row g-3">
                                            <!-- 1. التاريخ (من) -->
                                            <div class="col-md-4">
                                                <label for="from_date">form date</label>
                                                <input type="date" id="from_date" class="form-control"
                                                    name="from_date" value="{{ request('from_date') }}">
                                            </div>

                                            <!-- 2. التاريخ (إلى) -->
                                            <div class="col-md-4">
                                                <label for="to_date">التاريخ من</label>
                                                <input type="date" id="to_date" class="form-control" name="to_date"
                                                    value="{{ request('to_date') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="to_date">التاريخ إلى</label>
                                                <input type="date" id="to_date" class="form-control" name="to_date"
                                                    value="{{ request('to_date') }}">
                                            </div>
                                        </div>

                                        <!-- الأزرار -->
                                        <div class="form-actions mt-2">
                                            <button type="submit" class="btn btn-primary">بحث</button>
                                            <a href="" type="reset" class="btn btn-outline-warning">إلغاء</a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- 🔹 الجدول لعرض التحويلات -->
                            <div id="transfersTableContainer">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>رقم القيد</th>
                                            <th>التاريخ</th>
                                            <th>من خزينة</th>
                                            <th>إلى خزينة</th>
                                            <th>المبلغ</th>
                                            <th style="width: 10%">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transfersTableBody">
                                        <!-- سيتم تحميل البيانات هنا عبر AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- 🔹 تبويب سجل النشاطات -->

                        <div class="tab-pane fade" id="activate" role="tabpanel">
                            <p>سجل النشاطات هنا...</p>
                        </div>

                    </div> <!-- tab-content -->
                </div> <!-- card-body -->
            </div> <!-- card -->
        </div> <!-- content-body -->
    </div> <!-- card -->

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/search.js') }}"></script>
    <script>
        let currentPage = 1;
        const treasuryId = {{ $treasury->id }};

        // تحميل العمليات عند تحميل الصفحة
        $(document).ready(function() {
            // تحميل العمليات عند تحميل الصفحة
            $(document).ready(function() {
                // تحميل العمليات تلقائياً عند تحميل الصفحة
                loadOperations();

                // تحميل العمليات عند النقر على تبويب معاملات النظام
                $('#transactions-tab').on('shown.bs.tab', function() {
                    loadOperations();
                });

                // تحميل التحويلات عند النقر على تبويب التحويلات
                $('#transfers-tab').on('shown.bs.tab', function() {
                    loadTransfers();
                });
            });
        });

        // دالة تحميل العمليات
        function loadOperations(page = 1) {
            currentPage = page;
            showLoading();

            const formData = new FormData(document.getElementById('operationsSearchForm'));
            formData.append('page', page);

            $.ajax({
                url: `{{ route('treasury.show', $treasury->id) }}`,
                method: 'GET',
                data: Object.fromEntries(formData),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    hideLoading();
                    renderOperationsTable(response.operations);
                    renderPagination(response.pagination);
                },
                error: function(xhr, status, error) {
                    hideLoading();
                    console.error('خطأ في تحميل البيانات:', error);
                    showError('حدث خطأ في تحميل البيانات');
                }
            });
        }

        // عرض مؤشر التحميل
        function showLoading() {
            $('#operationsLoading').show();
            $('#operationsTableContainer').hide();
        }

        // إخفاء مؤشر التحميل
        function hideLoading() {
            $('#operationsLoading').hide();
            $('#operationsTableContainer').show();
        }

        // رسم جدول العمليات
        function renderOperationsTable(operations) {
            const tbody = $('#operationsTableBody');
            tbody.empty();

            if (operations.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fa fa-info-circle"></i> لا توجد عمليات للعرض
                        </td>
                    </tr>
                `);
                return;
            }

            operations.forEach(function(operation) {
                const row = createOperationRow(operation);
                tbody.append(row);
            });
        }

        // إنشاء صف العملية
        function createOperationRow(operation) {
            const operationIcon = getOperationIcon(operation.type);
            const operationColor = getOperationColor(operation.type);
            const depositAmount = operation.deposit > 0 ? formatNumber(operation.deposit) : '-';
            const withdrawAmount = operation.withdraw > 0 ? formatNumber(operation.withdraw) : '-';
            const balanceAfter = formatNumber(operation.balance_after);
            const formattedDate = formatDate(operation.date);

            // إنشاء زر عرض القيد المحاسبي
            let actionButton = '';
            if (operation.journal_entry_id) {
                actionButton = `
                    <a href="{{ route('journal.show', '') }}/${operation.journal_entry_id}"
                       class="btn btn-sm btn-outline-primary"
                       title="عرض القيد المحاسبي">
                        <i class="fa fa-eye"></i>
                    </a>
                `;
            } else {
                actionButton = '<span class="text-muted">-</span>';
            }

            return `
                <tr>
                    <td>
                        <i class="fa ${operationIcon} ${operationColor}"></i>
                    </td>
                    <td>
                        <span class="font-weight-bold ${operationColor}">
                            ${operation.operation}
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">${operation.description || '-'}</small>
                    </td>
                    <td>
                        <span class="text-success font-weight-bold">
                            ${depositAmount}
                        </span>
                    </td>
                    <td>
                        <span class="text-danger font-weight-bold">
                            ${withdrawAmount}
                        </span>
                    </td>
                    <td>
                        <span class="font-weight-bold text-primary">
                            ${balanceAfter}
                        </span>
                        ${operation.balance_change ? `<br><small class="text-muted">(${operation.balance_change})</small>` : ''}
                    </td>
                    <td>
                        <small>${formattedDate}</small>
                    </td>
                    <td>
                        ${actionButton}
                    </td>
                </tr>
            `;
        }

        // الحصول على أيقونة العملية
        function getOperationIcon(type) {
            const icons = {
                'payment': 'fa-credit-card',
                'receipt': 'fa-file-invoice',
                'transfer': 'fa-exchange-alt',
                'expense': 'fa-minus-circle',
                'revenue': 'fa-plus-circle'
            };
            return icons[type] || 'fa-circle';
        }

        // الحصول على لون العملية
        function getOperationColor(type) {
            const colors = {
                'payment': 'text-primary',
                'receipt': 'text-success',
                'transfer': 'text-warning',
                'expense': 'text-danger',
                'revenue': 'text-success'
            };
            return colors[type] || 'text-muted';
        }

        // رسم التنقل بين الصفحات
        function renderPagination(pagination) {
            const container = $('#operationsPagination');
            container.empty();

            if (pagination.last_page <= 1) {
                return;
            }

            let paginationHtml = '<ul class="pagination pagination-sm mb-0">';

            // زر الصفحة الأولى
            if (pagination.current_page > 1) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link border-0 rounded-pill" href="#" onclick="loadOperations(1)">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                `;
            } else {
                paginationHtml += `
                    <li class="page-item disabled">
                        <span class="page-link border-0 rounded-pill">
                            <i class="fas fa-angle-double-right"></i>
                        </span>
                    </li>
                `;
            }

            // زر الصفحة السابقة
            if (pagination.current_page > 1) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link border-0 rounded-pill" href="#" onclick="loadOperations(${pagination.current_page - 1})">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </li>
                `;
            } else {
                paginationHtml += `
                    <li class="page-item disabled">
                        <span class="page-link border-0 rounded-pill">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </li>
                `;
            }

            // رقم الصفحة الحالية
            paginationHtml += `
                <li class="page-item">
                    <span class="page-link border-0 bg-light rounded-pill px-3">
                        صفحة ${pagination.current_page} من ${pagination.last_page}
                    </span>
                </li>
            `;

            // زر الصفحة التالية
            if (pagination.current_page < pagination.last_page) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link border-0 rounded-pill" href="#" onclick="loadOperations(${pagination.current_page + 1})">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </li>
                `;
            } else {
                paginationHtml += `
                    <li class="page-item disabled">
                        <span class="page-link border-0 rounded-pill">
                            <i class="fas fa-angle-left"></i>
                        </span>
                    </li>
                `;
            }

            // زر الصفحة الأخيرة
            if (pagination.current_page < pagination.last_page) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link border-0 rounded-pill" href="#" onclick="loadOperations(${pagination.last_page})">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                `;
            } else {
                paginationHtml += `
                    <li class="page-item disabled">
                        <span class="page-link border-0 rounded-pill">
                            <i class="fas fa-angle-double-left"></i>
                        </span>
                    </li>
                `;
            }

            paginationHtml += '</ul>';
            container.html(paginationHtml);
        }

        // إعادة تعيين الفلاتر
        function resetFilters() {
            document.getElementById('operationsSearchForm').reset();
            loadOperations();
        }

        // تنسيق الأرقام
        function formatNumber(number) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(number);
        }

        // تنسيق التاريخ
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-GB'); // صيغة dd/mm/yyyy
        }

        // عرض رسالة خطأ
        function showError(message) {
            const tbody = $('#operationsTableBody');
            tbody.html(`
                <tr>
                    <td colspan="7" class="text-center text-danger py-4">
                        <i class="fa fa-exclamation-triangle"></i> ${message}
                    </td>
                </tr>
            `);
        }

        // دوال للبحث المتقدم
        function toggleSearchFields(button) {
            // تنفيذ إخفاء/إظهار حقول البحث
        }

        function toggleSearchText(button) {
            const text = button.querySelector('.button-text');
            if (text.textContent === 'متقدم') {
                text.textContent = 'بسيط';
            } else {
                text.textContent = 'متقدم';
            }
        }
    </script>

    <script>
        // تحميل التحويلات عند النقر على تبويب التحويلات
        $('#transfers-tab').on('shown.bs.tab', function() {
            loadTransfers();
        });

        // دالة تحميل التحويلات
        function loadTransfers() {
            showTransfersLoading();

            $.ajax({
                url: `{{ route('treasury.transfers', $treasury->id) }}`,
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    hideTransfersLoading();
                    renderTransfersTable(response.transfers);
                },
                error: function(xhr, status, error) {
                    hideTransfersLoading();
                    console.error('خطأ في تحميل البيانات:', error);
                    showTransfersError('حدث خطأ في تحميل البيانات');
                }
            });
        }

        // عرض مؤشر التحميل للتحويلات
        function showTransfersLoading() {
            $('#transfersLoading').show();
            $('#transfersTableContainer').hide();
        }

        // إخفاء مؤشر التحميل للتحويلات
        function hideTransfersLoading() {
            $('#transfersLoading').hide();
            $('#transfersTableContainer').show();
        }

        // رسم جدول التحويلات
        function renderTransfersTable(transfers) {
            const tbody = $('#transfersTableBody');
            tbody.empty();

            if (transfers.length === 0) {
                tbody.append(`
<tr>
    <td colspan="6" class="text-center text-muted py-4">
        <i class="fa fa-info-circle"></i> لا توجد تحويلات للعرض
    </td>
</tr>
`);
                return;
            }

            transfers.forEach(function(transfer) {
                const row = createTransferRow(transfer);
                tbody.append(row);
            });
        }

        // إنشاء صف التحويل
        function createTransferRow(transfer) {
            const formattedDate = formatDate(transfer.date);
            const formattedAmount = formatNumber(transfer.amount);

            return `
<tr>
    <td>${transfer.reference_number || '-'}</td>
    <td><small>${formattedDate}</small></td>
    <td>${transfer.from_account ? transfer.from_account.name : '-'}</td>
    <td>${transfer.to_account ? transfer.to_account.name : '-'}</td>
    <td>
        <span class="font-weight-bold text-primary">
            ${formattedAmount}
        </span>
    </td>
    <td>
        <a href="{{ route('treasury.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="fa fa-eye"></i>
        </a>
    </td>
</tr>
`;
        }

        // عرض رسالة خطأ للتحويلات
        function showTransfersError(message) {
            const tbody = $('#transfersTableBody');
            tbody.html(`
<tr>
    <td colspan="6" class="text-center text-danger py-4">
        <i class="fa fa-exclamation-triangle"></i> ${message}
    </td>
</tr>
`);
        }
    </script>
@endsection
