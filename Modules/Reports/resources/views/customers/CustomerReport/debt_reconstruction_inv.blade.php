@extends('master')

@section('title')
    تقرير أعمار ديون الفواتير
@stop

@section('css')
    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/report.css') }}">
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">

    <style>
        /* نفس CSS الأستاذ العام */
        .pagination-custom {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }

        .pagination-custom .page-btn {
            width: 45px;
            height: 45px;
            border: 2px solid #e0e0e0;
            border-radius: 50%;
            background: white;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            text-decoration: none;
        }

        .pagination-custom .page-btn:hover {
            border-color: #007bff;
            color: #007bff;
            background: #f8f9ff;
            transform: translateY(-2px);
        }

        .pagination-custom .page-btn.active {
            background: #007bff;
            border-color: #007bff;
            color: white;
        }

        .pagination-custom .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .pagination-info {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin: 10px 0;
        }

        .table-optimized {
            font-size: 13px;
        }

        .table-optimized th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            padding: 12px 8px;
            border: none;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-optimized td {
            padding: 8px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-optimized tbody tr:hover {
            background-color: #f8f9ff;
            transform: scale(1.001);
            transition: all 0.2s ease;
        }

        .group-info {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 8px 12px;
            margin: 2px 0;
            font-size: 12px;
        }

        .group-badge {
            background: #007bff;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin: 0 2px;
        }

        @media (max-width: 768px) {
            .table-optimized {
                font-size: 11px;
            }

            .table-optimized th,
            .table-optimized td {
                padding: 6px 4px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="slide-in">
                        <i class="fas fa-file-invoice-dollar me-3"></i>
                        تقرير أعمار ديون الفواتير
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">التقارير</a>
                            </li>
                            <li class="breadcrumb-item active">أعمار ديون الفواتير</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Filters Section -->
        <div class="card-modern fade-in">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>
                    فلاتر التقرير
                </h5>
            </div>
            <div class="card-body-modern">
                <form id="reportForm">
                    <div class="row g-3">
                        <!-- First Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">العميل</label>
                            <select name="customer" id="customer" class="form-control select2-ajax">
                                <option value="">جميع العملاء</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->trade_name }} ({{ $customer->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">الفرع</label>
                            <select name="branch" id="branch" class="form-control select2">
                                <option value="">جميع الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">تصنيف العميل</label>
                            <select name="customer_type" id="customer_type" class="form-control select2">
                                <option value="">جميع التصنيفات</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">المجموعة</label>
                            <select name="region_group" id="region_group" class="form-control select2">
                                <option value="">جميع المجموعات</option>
                                @foreach ($regionGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">مسؤول المبيعات</label>
                            <select name="sales_manager" id="sales_manager" class="form-control select2">
                                <option value="">جميع المسؤولين</option>
                                @foreach ($salesManagers as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                @endforeach
                            </select>
                            <div id="employeeGroupsInfo" class="mt-2" style="display: none;">
                                <small class="text-muted">مجموعات الموظف:</small>
                                <div id="employeeGroups"></div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">تمت الإضافة بواسطة</label>
                            <select name="added_by" id="added_by" class="form-control select2">
                                <option value="">الكل</option>
                                @foreach ($salesManagers as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">السنة المالية</label>
                            <select id="financial-year" name="financial_year[]" class="form-control select2" multiple>
                                <option value="current" selected>السنة المفتوحة</option>
                                <option value="all">جميع السنوات</option>
                                @for ($year = date('Y'); $year >= date('Y') - 10; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">فلتر حسب عمر الدين</label>
                            <select name="aging_filter" id="aging_filter" class="form-control">
                                <option value="">جميع الأعمار</option>
                                <option value="today">اليوم</option>
                                <option value="1-30">1-30 يوم</option>
                                <option value="31-60">31-60 يوم</option>
                                <option value="61-90">61-90 يوم</option>
                                <option value="91-120">91-120 يوم</option>
                                <option value="120+">أكثر من 120 يوم</option>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">من تاريخ</label>
                            <input type="date" name="from_date" id="from_date" class="form-control"
                                value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">إلى تاريخ</label>
                            <input type="date" name="to_date" id="to_date" class="form-control"
                                value="{{ now()->format('Y-m-d') }}">
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-lg-6 align-self-end">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn-modern btn-primary-modern" id="filterBtn">
                                    <i class="fas fa-search"></i>
                                    عرض التقرير
                                </button>
                                <button type="button" class="btn-modern btn-outline-modern" id="resetBtn">
                                    <i class="fas fa-refresh"></i>
                                    إلغاء الفلتر
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card-modern no-print fade-in">
            <div class="card-body-modern">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn-modern btn-success-modern" id="exportExcel">
                            <i class="fas fa-file-excel"></i>
                            تصدير إكسل
                        </button>
                        <button class="btn-modern btn-warning-modern" id="printBtn">
                            <i class="fas fa-print"></i>
                            طباعة
                        </button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn-modern btn-primary-modern active" id="summaryViewBtn">
                            <i class="fas fa-chart-pie"></i>
                            ملخص
                        </button>
                        <button type="button" class="btn-modern btn-outline-modern" id="detailViewBtn">
                            <i class="fas fa-list"></i>
                            تفاصيل
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4 fade-in" id="totalsSection">
            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-icon primary">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-value" id="todayAmount">0.00</div>
                    <div class="stats-label">اليوم (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stats-value" id="days1to30">0.00</div>
                    <div class="stats-label">1-30 يوم (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stats-value" id="days31to60">0.00</div>
                    <div class="stats-label">31-60 يوم (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stats-value" id="days61to90">0.00</div>
                    <div class="stats-label">61-90 يوم (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stats-value" id="days91to120">0.00</div>
                    <div class="stats-label">91-120 يوم (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card dark">
                    <div class="stats-icon dark">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stats-value" id="daysOver120">0.00</div>
                    <div class="stats-label">+120 يوم (ريال)</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني لأعمار ديون الفواتير
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container">
                    <canvas class="chart bg-light" id="agingChart" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="card-modern fade-in" id="reportContainer">
            <div class="card-header-modern">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" id="reportTitle">
                        <i class="fas fa-table me-2"></i>
                        تقرير أعمار ديون الفواتير
                    </h5>
                    <div class="pagination-info">
                        <small class="text-muted" id="recordsInfo">
                            عدد السجلات: <span id="recordCount">0</span> |
                            عدد العملاء: <span id="clientCount">0</span> |
                            الصفحة: <span id="currentPage">1</span> من <span id="totalPages">1</span>
                        </small>
                    </div>
                </div>
            </div>

            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-optimized mb-0" id="reportTable">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>رقم الفاتورة</th>
                                <th><i class="fas fa-code me-1"></i>كود العميل</th>
                                <th><i class="fas fa-user-tie me-1"></i>اسم العميل</th>
                                <th><i class="fas fa-building me-1"></i>الفرع</th>
                                <th><i class="fas fa-tags me-1"></i>التصنيف</th>
                                <th><i class="fas fa-layer-group me-1"></i>المجموعة</th>
                                <th><i class="fas fa-user-cog me-1"></i>مسؤول المبيعات</th>
                                <th><i class="fas fa-calendar me-1"></i>تاريخ الفاتورة</th>
                                <th><i class="fas fa-hourglass-half me-1"></i>عدد الأيام</th>
                                <th><i class="fas fa-clock me-1"></i>اليوم</th>
                                <th><i class="fas fa-calendar-day me-1"></i>1-30 يوم</th>
                                <th><i class="fas fa-calendar-week me-1"></i>31-60 يوم</th>
                                <th><i class="fas fa-calendar-alt me-1"></i>61-90 يوم</th>
                                <th><i class="fas fa-exclamation-triangle me-1"></i>91-120 يوم</th>
                                <th><i class="fas fa-times-circle me-1"></i>+120 يوم</th>
                                <th><i class="fas fa-calculator me-1"></i>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <!-- سيتم تحديث البيانات هنا عبر AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Custom Pagination -->
            <div class="card-footer-modern">
                <div class="pagination-custom" id="customPagination">
                    <!-- سيتم إضافة أزرار الـ pagination هنا -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        let agingChart;
        let currentPage = 1;
        let totalPages = 1;
        let isLoading = false;

        $(document).ready(function() {
            // تهيئة Select2
            initializeSelect2();

            // تحميل البيانات الأولية
            loadReportData(1);
            initializeChart();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // Event handlers
            $('#filterBtn').click(function() {
                if (!isLoading) {
                    currentPage = 1;
                    loadReportData(1);
                }
            });

            $('#resetBtn').click(function() {
                resetFilters();
            });

            $('#exportExcel').click(function() {
                exportToExcel();
            });

            $('#printBtn').click(function() {
                window.print();
            });

            // View toggle
            $('#summaryViewBtn, #detailViewBtn').click(function() {
                toggleView($(this).attr('id'));
            });

            // Event handler for employee selection
            $('#sales_manager').change(function() {
                const employeeId = $(this).val();
                if (employeeId) {
                    loadEmployeeGroups(employeeId);
                } else {
                    $('#employeeGroupsInfo').hide();
                }
            });

            // Event handler for group selection
            $('#region_group').change(function() {
                const groupId = $(this).val();
                if (groupId) {
                    loadGroupClients(groupId);
                }
            });
        });

        // تهيئة Select2
        function initializeSelect2() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                language: {
                    noResults: function() { return "لا توجد نتائج"; },
                    searching: function() { return "جاري البحث..."; }
                },
                allowClear: true,
                width: '100%',
                placeholder: 'اختر...'
            });

            // تهيئة Select2 مع AJAX للعملاء
            $('#customer').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                allowClear: true,
                width: '100%',
                placeholder: 'ابحث عن عميل...',
                minimumInputLength: 1,
                ajax: {
                    url: '{{ route('ClientReport.searchClients') }}',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: data.results,
                            pagination: { more: data.pagination.more }
                        };
                    }
                }
            });
        }

        // تحميل مجموعات الموظف
        function loadEmployeeGroups(employeeId) {
            $.ajax({
                url: '{{ route('ClientReport.getEmployeeGroups') }}',
                method: 'GET',
                data: { employee_id: employeeId },
                success: function(response) {
                    if (response.success && response.groups.length > 0) {
                        let groupsHtml = '';
                        response.groups.forEach(function(group) {
                            groupsHtml += `
                                <div class="group-info">
                                    <i class="fas fa-layer-group me-1"></i>
                                    <strong>${group.name}</strong>
                                    <span class="group-badge">${group.branch}</span>
                                    <span class="group-badge">${group.direction}</span>
                                </div>
                            `;
                        });
                        $('#employeeGroups').html(groupsHtml);
                        $('#employeeGroupsInfo').show();
                    } else {
                        $('#employeeGroupsInfo').hide();
                    }
                },
                error: function() {
                    $('#employeeGroupsInfo').hide();
                }
            });
        }

        // تحميل عملاء المجموعة
        function loadGroupClients(groupId) {
            $.ajax({
                url: '{{ route('ClientReport.getGroupClients') }}',
                method: 'GET',
                data: { group_id: groupId },
                success: function(response) {
                    if (response.success) {
                        const customerSelect = $('#customer');
                        customerSelect.empty();
                        customerSelect.append('<option value="">جميع العملاء</option>');

                        response.clients.forEach(function(client) {
                            customerSelect.append(`<option value="${client.id}">${client.text}</option>`);
                        });

                        customerSelect.trigger('change');
                        showAlert(`تم تحديث قائمة العملاء (${response.clients.length} عميل)`, 'info');
                    }
                },
                error: function() {
                    showAlert('حدث خطأ في تحميل عملاء المجموعة', 'warning');
                }
            });
        }

        // دالة تحميل بيانات التقرير
        function loadReportData(page = 1) {
            if (isLoading) return;

            isLoading = true;
            showTableLoading(true);
            $('#filterBtn').prop('disabled', true).addClass('loading');

            const formData = {
                customer: $('#customer').val(),
                branch: $('#branch').val(),
                customer_type: $('#customer_type').val(),
                sales_manager: $('#sales_manager').val(),
                region_group: $('#region_group').val(),
                added_by: $('#added_by').val(),
                financial_year: $('#financial-year').val(),
                aging_filter: $('#aging_filter').val(),
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                page: page,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: '{{ route('ClientReport.invoiceDebtAgingAjax') }}',
                method: 'GET',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        updateReportDisplay(response);
                        currentPage = response.pagination.current_page;
                        totalPages = response.pagination.last_page;
                        updatePagination(response.pagination);
                    } else {
                        showAlert('حدث خطأ في تحميل البيانات', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('خطأ في تحميل البيانات:', error);
                    showAlert('حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.', 'danger');
                },
                complete: function() {
                    isLoading = false;
                    showTableLoading(false);
                    $('#filterBtn').prop('disabled', false).removeClass('loading');
                }
            });
        }

        // تحديث عرض التقرير
        function updateReportDisplay(data) {
            // تحديث الإجماليات مع تأثير العد التصاعدي
            animateValue('#todayAmount', 0, data.totals.today, 1000);
            animateValue('#days1to30', 0, data.totals.days1to30, 1000);
            animateValue('#days31to60', 0, data.totals.days31to60, 1000);
            animateValue('#days61to90', 0, data.totals.days61to90, 1000);
            animateValue('#days91to120', 0, data.totals.days91to120, 1000);
            animateValue('#daysOver120', 0, data.totals.daysOver120, 1000);

            // تحديث معلومات العد
            $('#recordCount').text(data.pagination.total || 0);
            $('#clientCount').text(data.customers_count || 0);
            $('#currentPage').text(data.pagination.current_page || 1);
            $('#totalPages').text(data.pagination.last_page || 1);

            // تحديث عنوان التقرير
            $('#reportTitle').html(`
                <i class="fas fa-table me-2"></i>
                تقرير أعمار ديون الفواتير من ${data.from_date} إلى ${data.to_date}
            `);

            // تحديث جدول البيانات
            updateTableBody(data.grouped_customers);

            // تحديث الرسم البياني
            updateChart(data.chart_data);
        }
// استبدال دالة updateTableBody بالكود التالي:

// تحديث محتوى الجدول - معدلة لإظهار كل العملاء مع فواتيرهم
function updateTableBody(groupedCustomers) {
    let tableHtml = '';
    let grandTotals = {
        today: 0,
        days1to30: 0,
        days31to60: 0,
        days61to90: 0,
        days91to120: 0,
        daysOver120: 0,
        total_due: 0
    };

    // التحقق من وجود بيانات
    if (!groupedCustomers || Object.keys(groupedCustomers).length === 0) {
        tableHtml = `
            <tr>
                <td colspan="16" class="text-center py-4">
                    <i class="fas fa-info-circle me-2"></i>
                    لا توجد بيانات لعرضها
                </td>
            </tr>
        `;
    } else {
        // تكرار عبر المجموعات (العملاء)
        Object.keys(groupedCustomers).forEach((customerName, customerIndex) => {
            const customerGroup = groupedCustomers[customerName];
            const customerData = customerGroup.data;
            const customerTotals = customerGroup.customer_totals;

            // صف رأس العميل
            tableHtml += `
                <tr class="table-client-header" style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border-left: 4px solid #2196f3;">
                    <td colspan="16" style="padding: 12px; font-weight: bold; font-size: 14px;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-tie me-3 text-primary" style="font-size: 18px;"></i>
                                <div>
                                    <span style="color: #1976d2; font-size: 16px;">${customerName}</span>
                                    <span class="badge bg-primary ms-3">${customerData.length} فاتورة</span>
                                    ${customerData[0].client_code ? `<span class="badge bg-secondary ms-2">كود: ${customerData[0].client_code}</span>` : ''}
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted me-3">إجمالي المدين: </small>
                                <strong class="text-danger" style="font-size: 14px;">${formatNumber(customerTotals.total_due)} ريال</strong>
                            </div>
                        </div>
                    </td>
                </tr>
            `;

            // تكرار عبر فواتير العميل
            customerData.forEach((item, index) => {
                const isOverdue = (item.days91to120 > 0 || item.daysOver120 > 0);
                const rowClass = isOverdue ? 'table-warning' : 'table-invoice-row';

                tableHtml += `<tr class="${rowClass}" style="background-color: ${isOverdue ? '#fff3cd' : '#fafafa'}; border-left: 3px solid ${isOverdue ? '#ffc107' : '#e0e0e0'};">`;

                // رقم الفاتورة مع أيقونة
                tableHtml += `<td style="padding-left: 20px;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-invoice text-primary me-2"></i>
                        <span class="fw-bold text-primary">${item.invoice_number}</span>
                    </div>
                </td>`;

                // كود العميل
                tableHtml += `<td>
                    <span class="text-muted">${item.client_code || 'غير محدد'}</span>
                </td>`;

                // اسم العميل (فارغ لأنه في الهيدر)
                tableHtml += `<td>
                    <span class="text-muted" style="font-size: 12px;">└ فرعي</span>
                </td>`;

                // الفرع
                tableHtml += `<td>
                    <span class="badge bg-light text-dark">${item.branch || 'غير محدد'}</span>
                </td>`;

                // التصنيف
                tableHtml += `<td>
                    <span class="badge bg-info text-white">${item.category || 'غير محدد'}</span>
                </td>`;

                // المجموعة
                tableHtml += `<td>
                    <span class="badge bg-secondary">${item.region_group || 'غير محدد'}</span>
                </td>`;

                // مسؤول المبيعات
                tableHtml += `<td>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-cog me-1 text-success"></i>
                        <span style="font-size: 12px;">${item.sales_manager || 'غير محدد'}</span>
                    </div>
                </td>`;

                // تاريخ الفاتورة
                tableHtml += `<td>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar me-1 text-info"></i>
                        <span class="badge bg-light text-dark" style="font-size: 11px;">${item.invoice_date}</span>
                    </div>
                </td>`;

                // عدد الأيام
                tableHtml += `<td>
                    <span class="badge ${getDaysLateBadgeClass(item.days_late)}">${item.days_late || 0} يوم</span>
                </td>`;

                // الأعمدة المالية مع ألوان وأيقونات
                tableHtml += `<td class="text-center">
                    <div class="fw-bold ${item.today > 0 ? 'text-primary' : 'text-muted'}">
                        ${item.today > 0 ? '<i class="fas fa-clock me-1"></i>' : ''}
                        ${formatNumber(item.today)}
                    </div>
                </td>`;

                tableHtml += `<td class="text-center">
                    <div class="fw-bold ${item.days1to30 > 0 ? 'text-warning' : 'text-muted'}">
                        ${item.days1to30 > 0 ? '<i class="fas fa-calendar-day me-1"></i>' : ''}
                        ${formatNumber(item.days1to30)}
                    </div>
                </td>`;

                tableHtml += `<td class="text-center">
                    <div class="fw-bold ${item.days31to60 > 0 ? 'text-info' : 'text-muted'}">
                        ${item.days31to60 > 0 ? '<i class="fas fa-calendar-week me-1"></i>' : ''}
                        ${formatNumber(item.days31to60)}
                    </div>
                </td>`;

                tableHtml += `<td class="text-center">
                    <div class="fw-bold ${item.days61to90 > 0 ? 'text-success' : 'text-muted'}">
                        ${item.days61to90 > 0 ? '<i class="fas fa-calendar-alt me-1"></i>' : ''}
                        ${formatNumber(item.days61to90)}
                    </div>
                </td>`;

                tableHtml += `<td class="text-center">
                    <div class="fw-bold ${item.days91to120 > 0 ? 'text-danger' : 'text-muted'}">
                        ${item.days91to120 > 0 ? '<i class="fas fa-exclamation-triangle me-1"></i>' : ''}
                        ${formatNumber(item.days91to120)}
                    </div>
                </td>`;

                tableHtml += `<td class="text-center">
                    <div class="fw-bold ${item.daysOver120 > 0 ? 'text-dark' : 'text-muted'}">
                        ${item.daysOver120 > 0 ? '<i class="fas fa-times-circle me-1"></i>' : ''}
                        ${formatNumber(item.daysOver120)}
                    </div>
                </td>`;

                // الإجمالي
                tableHtml += `<td class="text-center">
                    <div class="fw-bold text-primary" style="font-size: 13px;">
                        <i class="fas fa-calculator me-1"></i>
                        ${formatNumber(item.total_due)}
                    </div>
                </td>`;

                tableHtml += `</tr>`;

                // تجميع الإجماليات الكبرى
                grandTotals.today += parseFloat(item.today);
                grandTotals.days1to30 += parseFloat(item.days1to30);
                grandTotals.days31to60 += parseFloat(item.days31to60);
                grandTotals.days61to90 += parseFloat(item.days61to90);
                grandTotals.days91to120 += parseFloat(item.days91to120);
                grandTotals.daysOver120 += parseFloat(item.daysOver120);
                grandTotals.total_due += parseFloat(item.total_due);
            });

            // صف إجمالي العميل
            tableHtml += `
                <tr class="table-client-total" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 4px solid #28a745; font-weight: bold;">
                    <td colspan="9" style="padding: 10px 20px;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calculator me-2 text-success"></i>
                            <span style="color: #155724; font-size: 14px;">إجمالي ${customerName}</span>
                            <span class="badge bg-success ms-2">${customerData.length} فاتورة</span>
                        </div>
                    </td>
                    <td class="text-center fw-bold text-primary">${formatNumber(customerTotals.today)}</td>
                    <td class="text-center fw-bold text-warning">${formatNumber(customerTotals.days1to30)}</td>
                    <td class="text-center fw-bold text-info">${formatNumber(customerTotals.days31to60)}</td>
                    <td class="text-center fw-bold text-success">${formatNumber(customerTotals.days61to90)}</td>
                    <td class="text-center fw-bold text-danger">${formatNumber(customerTotals.days91to120)}</td>
                    <td class="text-center fw-bold text-dark">${formatNumber(customerTotals.daysOver120)}</td>
                    <td class="text-center fw-bold text-primary" style="font-size: 14px; background-color: #e3f2fd;">
                        ${formatNumber(customerTotals.total_due)}
                    </td>
                </tr>
            `;

            // إضافة فاصل بين العملاء
            if (customerIndex < Object.keys(groupedCustomers).length - 1) {
                tableHtml += `
                    <tr class="table-separator" style="height: 10px; background: linear-gradient(90deg, transparent 0%, rgba(33, 150, 243, 0.1) 50%, transparent 100%);">
                        <td colspan="16" style="border: none; padding: 0;"></td>
                    </tr>
                `;
            }
        });

        // صف الإجمالي العام
        if (Object.keys(groupedCustomers).length > 0) {
            tableHtml += `
                <tr class="table-grand-total" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; font-weight: bold; font-size: 14px;">
                    <td colspan="9" style="padding: 15px 20px;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-bar me-2"></i>
                            <span style="font-size: 16px;">المجموع الكلي لجميع العملاء</span>
                            <span class="badge bg-light text-dark ms-3">${Object.keys(groupedCustomers).length} عميل</span>
                        </div>
                    </td>
                    <td class="text-center fw-bold" style="background-color: rgba(255,255,255,0.1);">${formatNumber(grandTotals.today)}</td>
                    <td class="text-center fw-bold" style="background-color: rgba(255,255,255,0.1);">${formatNumber(grandTotals.days1to30)}</td>
                    <td class="text-center fw-bold" style="background-color: rgba(255,255,255,0.1);">${formatNumber(grandTotals.days31to60)}</td>
                    <td class="text-center fw-bold" style="background-color: rgba(255,255,255,0.1);">${formatNumber(grandTotals.days61to90)}</td>
                    <td class="text-center fw-bold" style="background-color: rgba(255,255,255,0.1);">${formatNumber(grandTotals.days91to120)}</td>
                    <td class="text-center fw-bold" style="background-color: rgba(255,255,255,0.1);">${formatNumber(grandTotals.daysOver120)}</td>
                    <td class="text-center fw-bold" style="background-color: rgba(255,255,255,0.2); font-size: 16px;">
                        <i class="fas fa-coins me-1"></i>
                        ${formatNumber(grandTotals.total_due)}
                    </td>
                </tr>
            `;
        }
    }

    $('#reportTableBody').html(tableHtml);

    // إضافة تأثيرات hover للصفوف الجديدة
    addTableRowEffects();
}

// دالة إضافة تأثيرات hover للصفوف
function addTableRowEffects() {
    // تأثير hover لصفوف الفواتير
    $('.table-invoice-row').hover(
        function() {
            $(this).css({
                'background-color': '#f0f8ff',
                'transform': 'translateX(-5px)',
                'box-shadow': '3px 0 8px rgba(33, 150, 243, 0.2)',
                'border-left': '3px solid #2196f3',
                'transition': 'all 0.3s ease'
            });
        },
        function() {
            $(this).css({
                'background-color': '#fafafa',
                'transform': 'translateX(0)',
                'box-shadow': 'none',
                'border-left': '3px solid #e0e0e0',
                'transition': 'all 0.3s ease'
            });
        }
    );

    // تأثير hover لهيدر العميل
    $('.table-client-header').hover(
        function() {
            $(this).css({
                'background': 'linear-gradient(135deg, #bbdefb 0%, #e1bee7 100%)',
                'transform': 'scale(1.002)',
                'box-shadow': '0 4px 12px rgba(33, 150, 243, 0.3)',
                'transition': 'all 0.3s ease'
            });
        },
        function() {
            $(this).css({
                'background': 'linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%)',
                'transform': 'scale(1)',
                'box-shadow': 'none',
                'transition': 'all 0.3s ease'
            });
        }
    );

    // تأثير hover لإجمالي العميل
    $('.table-client-total').hover(
        function() {
            $(this).css({
                'background': 'linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%)',
                'transform': 'scale(1.001)',
                'box-shadow': '0 3px 10px rgba(40, 167, 69, 0.3)',
                'transition': 'all 0.3s ease'
            });
        },
        function() {
            $(this).css({
                'background': 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)',
                'transform': 'scale(1)',
                'box-shadow': 'none',
                'transition': 'all 0.3s ease'
            });
        }
    );

    // تأثير hover للإجمالي العام
    $('.table-grand-total').hover(
        function() {
            $(this).css({
                'background': 'linear-gradient(135deg, #c82333 0%, #bd2130 100%)',
                'transform': 'scale(1.002)',
                'box-shadow': '0 5px 15px rgba(220, 53, 69, 0.4)',
                'transition': 'all 0.3s ease'
            });
        },
        function() {
            $(this).css({
                'background': 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)',
                'transform': 'scale(1)',
                'box-shadow': 'none',
                'transition': 'all 0.3s ease'
            });
        }
    );
}
        // نفس باقي الدوال من ملف الأستاذ العام مع التعديلات المناسبة
        function updatePagination(paginationData) {
            const container = $('#customPagination');
            let paginationHtml = '';

            const current = paginationData.current_page;
            const total = paginationData.last_page;

            // First page button
            paginationHtml += `
                <a href="#" class="page-btn ${current === 1 ? 'disabled' : ''}" data-page="1" title="الصفحة الأولى">
                    <i class="fas fa-angle-double-right"></i>
                </a>
            `;

            // Previous page button
            paginationHtml += `
                <a href="#" class="page-btn ${current === 1 ? 'disabled' : ''}" data-page="${current - 1}" title="السابق">
                    <i class="fas fa-angle-right"></i>
                </a>
            `;

            // Page numbers
            let startPage = Math.max(1, current - 2);
            let endPage = Math.min(total, current + 2);

            for (let i = startPage; i <= endPage; i++) {
                paginationHtml += `
                    <a href="#" class="page-btn ${i === current ? 'active' : ''}" data-page="${i}">
                        ${i}
                    </a>
                `;
            }

            // Next page button
            paginationHtml += `
                <a href="#" class="page-btn ${current === total ? 'disabled' : ''}" data-page="${current + 1}" title="التالي">
                    <i class="fas fa-angle-left"></i>
                </a>
            `;

            // Last page button
            paginationHtml += `
                <a href="#" class="page-btn ${current === total ? 'disabled' : ''}" data-page="${total}" title="الصفحة الأخيرة">
                    <i class="fas fa-angle-double-left"></i>
                </a>
            `;

            container.html(paginationHtml);

            // Bind pagination click events
            container.find('.page-btn:not(.disabled)').click(function(e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'));
                if (page !== current && page >= 1 && page <= total) {
                    loadReportData(page);
                }
            });
        }

        // إظهار/إخفاء loading للجدول
        function showTableLoading(show) {
            if (show) {
                $('#reportTableBody').html(`
                    <tr>
                        <td colspan="16" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            <div class="mt-2">جاري تحميل البيانات...</div>
                        </td>
                    </tr>
                `);
            }
        }

        // إعادة تعيين الفلاتر
        function resetFilters() {
            $('#reportForm')[0].reset();
            $('.select2').val(null).trigger('change');
            $('#financial-year').val(['current']).trigger('change');
            $('#from_date').val('{{ now()->startOfMonth()->format('Y-m-d') }}');
            $('#to_date').val('{{ now()->format('Y-m-d') }}');
            $('#employeeGroupsInfo').hide();
            currentPage = 1;
            loadReportData(1);
        }

        // تبديل العرض
        function toggleView(viewId) {
            $('.btn-group .btn-modern').removeClass('btn-primary-modern active').addClass('btn-outline-modern');
            $('#' + viewId).removeClass('btn-outline-modern').addClass('btn-primary-modern active');

            if (viewId === 'summaryViewBtn') {
                $('#chartSection').fadeIn();
                showAlert('تم التبديل إلى عرض الملخص', 'info');
            } else {
                $('#chartSection').fadeOut();
                showAlert('تم التبديل إلى عرض التفاصيل', 'info');
            }
        }

        // باقي الدوال المساعدة نفس الأستاذ العام
        function getDaysLateBadgeClass(daysLate) {
            if (daysLate === 0) return 'bg-primary';
            if (daysLate <= 30) return 'bg-warning';
            if (daysLate <= 60) return 'bg-info';
            if (daysLate <= 90) return 'bg-success';
            if (daysLate <= 120) return 'bg-danger';
            return 'bg-dark';
        }

        function initializeChart() {
            const ctx = document.getElementById('agingChart').getContext('2d');

            agingChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['اليوم', '1-30 يوم', '31-60 يوم', '61-90 يوم', '91-120 يوم', '+120 يوم'],
                    datasets: [{
                        label: 'أعمار ديون الفواتير (ريال)',
                        data: [0, 0, 0, 0, 0, 0],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(23, 162, 184, 0.8)',
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(220, 53, 69, 0.8)',
                            'rgba(52, 58, 64, 0.8)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(23, 162, 184, 1)',
                            'rgba(40, 167, 69, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(52, 58, 64, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'توزيع أعمار ديون الفواتير',
                            font: {
                                size: 18,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            callbacks: {
                                label: function(context) {
                                    return 'المبلغ: ' + formatNumber(context.parsed.y) + ' ريال';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return (value / 1000000).toFixed(1) + 'M';
                                    } else if (value >= 1000) {
                                        return (value / 1000).toFixed(0) + 'K';
                                    }
                                    return value;
                                },
                                font: {
                                    size: 12
                                }
                            },
                            title: {
                                display: true,
                                text: 'المبلغ (ريال)',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        function updateChart(chartData) {
            if (agingChart && chartData.aging_values) {
                agingChart.data.datasets[0].data = chartData.aging_values;
                agingChart.update('active');
            }
        }

        function formatNumber(number) {
            return parseFloat(number || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function animateValue(element, start, end, duration) {
            const obj = $(element);
            const range = Math.abs(end - start);

            if (range < 1) {
                obj.text(formatNumber(end));
                return;
            }

            const startTime = Date.now();
            const timer = setInterval(function() {
                const elapsed = Date.now() - startTime;
                const progress = Math.min(elapsed / duration, 1);

                const current = start + (end - start) * progress;
                obj.text(formatNumber(Math.round(current)));

                if (progress >= 1) {
                    obj.text(formatNumber(end));
                    clearInterval(timer);
                }
            }, 16);
        }

        function exportToExcel() {
            showAlert('جاري تصدير الملف...', 'info');

            const table = document.querySelector('#reportContainer table');
            const wb = XLSX.utils.table_to_book(table, {
                raw: false,
                cellDates: true
            });

            const today = new Date();
            const fileName = `تقرير_أعمار_ديون_الفواتير_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

            XLSX.writeFile(wb, fileName);
            showAlert('تم تصدير الملف بنجاح!', 'success');
        }

        function showAlert(message, type) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show position-fixed"
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            $('body').append(alertHtml);

            setTimeout(() => {
                $('.alert').alert('close');
            }, 3000);
        }

        // تأثيرات hover للكروت
        $(document).on('mouseenter', '.stats-card', function() {
            $(this).css({
                'transform': 'translateY(-8px) scale(1.02)',
                'box-shadow': '0 15px 35px rgba(0,0,0,0.1)',
                'transition': 'all 0.3s ease'
            });
        }).on('mouseleave', '.stats-card', function() {
            $(this).css({
                'transform': 'translateY(0) scale(1)',
                'box-shadow': '0 5px 15px rgba(0,0,0,0.08)',
                'transition': 'all 0.3s ease'
            });
        });

        // تأثيرات hover للأزرار
        $(document).on('mouseenter', '.btn-modern:not(.active)', function() {
            $(this).css({
                'transform': 'translateY(-2px)',
                'box-shadow': '0 8px 25px rgba(0,0,0,0.15)',
                'transition': 'all 0.3s ease'
            });
        }).on('mouseleave', '.btn-modern:not(.active)', function() {
            $(this).css({
                'transform': 'translateY(0)',
                'box-shadow': 'none',
                'transition': 'all 0.3s ease'
            });
        });

        // تأثيرات hover لصفوف الجدول
        $(document).on('mouseenter', '.table-optimized tbody tr', function() {
            if (!$(this).hasClass('table-loading')) {
                $(this).css({
                    'background-color': '#f8f9ff',
                    'transform': 'scale(1.002)',
                    'box-shadow': '0 3px 10px rgba(0,0,0,0.1)',
                    'transition': 'all 0.2s ease'
                });
            }
        }).on('mouseleave', '.table-optimized tbody tr', function() {
            if (!$(this).hasClass('table-loading')) {
                $(this).css({
                    'background-color': '',
                    'transform': 'scale(1)',
                    'box-shadow': 'none',
                    'transition': 'all 0.2s ease'
                });
            }
        });

        // تحسين تجربة المستخدم للفلاتر
        $('.form-control, .select2-selection').on('focus', function() {
            $(this).closest('.col-lg-3, .col-md-6').addClass('filter-focused');
        }).on('blur', function() {
            $(this).closest('.col-lg-3, .col-md-6').removeClass('filter-focused');
        });

        // إضافة CSS إضافي للتحسينات

        // إضافة CSS إضافي للرأس
        $('head').append(additionalCSS);

        // إضافة تلميحات الأدوات
        $('[title]').tooltip({
            placement: 'top',
            trigger: 'hover'
        });

        // تحسين أداء الجدول عند التمرير
        let scrollTimeout;
        $('.table-responsive').on('scroll', function() {
            clearTimeout(scrollTimeout);
            $(this).addClass('scrolling');

            scrollTimeout = setTimeout(() => {
                $(this).removeClass('scrolling');
            }, 150);
        });

        // إضافة مؤشر التقدم للتحميل
        function showProgressBar() {
            if (!$('.progress-bar-container').length) {
                $('body').append(`
                    <div class="progress-bar-container position-fixed" style="top: 0; left: 0; width: 100%; z-index: 9999; height: 3px;">
                        <div class="progress-bar bg-primary" style="height: 100%; width: 0%; transition: width 0.3s ease;"></div>
                    </div>
                `);
            }

            $('.progress-bar').css('width', '0%');
            $('.progress-bar-container').show();

            // محاكاة التقدم
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 90) progress = 90;
                $('.progress-bar').css('width', progress + '%');

                if (progress >= 90) {
                    clearInterval(interval);
                }
            }, 200);
        }

        function hideProgressBar() {
            $('.progress-bar').css('width', '100%');
            setTimeout(() => {
                $('.progress-bar-container').fadeOut();
            }, 300);
        }

        // تحديث دالة loadReportData لاستخدام شريط التقدم
        const originalLoadReportData = loadReportData;
        loadReportData = function(page = 1) {
            showProgressBar();
            return originalLoadReportData(page).always(() => {
                hideProgressBar();
            });
        };

        // إضافة اختصارات لوحة المفاتيح
        $(document).keydown(function(e) {
            // Ctrl + F للبحث السريع
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                $('#customer').select2('open');
            }

            // Ctrl + R لإعادة التحميل
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                loadReportData(currentPage);
            }

            // Ctrl + P للطباعة
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                $('#printBtn').click();
            }

            // Ctrl + E للتصدير
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                $('#exportExcel').click();
            }

            // مفاتيح الأسهم للتنقل بين الصفحات
            if (e.key === 'ArrowRight' && currentPage > 1) {
                loadReportData(currentPage - 1);
            }

            if (e.key === 'ArrowLeft' && currentPage < totalPages) {
                loadReportData(currentPage + 1);
            }
        });

        // حفظ إعدادات الفلاتر في localStorage
        function saveFiltersState() {
            const filtersState = {
                customer: $('#customer').val(),
                branch: $('#branch').val(),
                customer_type: $('#customer_type').val(),
                sales_manager: $('#sales_manager').val(),
                region_group: $('#region_group').val(),
                added_by: $('#added_by').val(),
                financial_year: $('#financial-year').val(),
                aging_filter: $('#aging_filter').val(),
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val()
            };
            localStorage.setItem('invoice_aging_filters', JSON.stringify(filtersState));
        }

        // استرداد إعدادات الفلاتر من localStorage
        function loadFiltersState() {
            const saved = localStorage.getItem('invoice_aging_filters');
            if (saved) {
                try {
                    const filtersState = JSON.parse(saved);
                    Object.keys(filtersState).forEach(key => {
                        const element = $('#' + key.replace('_', '-'));
                        if (element.length && filtersState[key]) {
                            element.val(filtersState[key]).trigger('change');
                        }
                    });
                } catch (e) {
                    console.log('خطأ في استرداد إعدادات الفلاتر:', e);
                }
            }
        }

        // حفظ الفلاتر عند التغيير
        $('#reportForm').on('change', 'select, input', function() {
            saveFiltersState();
        });

        // استرداد الفلاتر عند التحميل
        setTimeout(() => {
            loadFiltersState();
        }, 1000);

        // إضافة زر مسح الفلاتر المحفوظة
        $('.card-body-modern').append(`
            <div class="text-center mt-3">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSavedFilters">
                    <i class="fas fa-trash-alt me-1"></i>
                    مسح الفلاتر المحفوظة
                </button>
            </div>
        `);

        $('#clearSavedFilters').click(function() {
            localStorage.removeItem('invoice_aging_filters');
            showAlert('تم مسح الفلاتر المحفوظة', 'success');
        });

        console.log('تم تحميل تقرير أعمار ديون الفواتير بنجاح! 🎉');
    </script>
@endsection