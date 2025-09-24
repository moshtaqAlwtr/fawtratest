@extends('master')

@section('title')
    تقرير تفاصيل حركة المخزون لكل منتج
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

@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="slide-in">
                        <i class="fas fa-boxes me-3"></i>
                        تقرير تفاصيل حركة المخزون لكل منتج
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">التقارير</a>
                            </li>
                            <li class="breadcrumb-item active">حركة المخزون</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Quick Date Buttons -->
        <div class="quick-date-buttons d-block mb-3">
            <button class="btn btn-sm btn-outline-primary quick-date-range" data-range="today">
                <i class="fas fa-calendar-day me-1"></i>اليوم
            </button>
            <button class="btn btn-sm btn-outline-primary quick-date-range" data-range="week">
                <i class="fas fa-calendar-week me-1"></i>آخر 7 أيام
            </button>
            <button class="btn btn-sm btn-outline-primary quick-date-range" data-range="month">
                <i class="fas fa-calendar-alt me-1"></i>هذا الشهر
            </button>
            <button class="btn btn-sm btn-outline-primary quick-date-range" data-range="quarter">
                <i class="fas fa-calendar me-1"></i>هذا الربع
            </button>
        </div>

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
                            <label class="form-label-modern">من تاريخ</label>
                            <input type="date" name="start_date" id="start_date" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">إلى تاريخ</label>
                            <input type="date" name="end_date" id="end_date" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">المنتج</label>
                            <select name="product" id="product" class="form-control select2-ajax">
                                <option value="">جميع المنتجات</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} {{ $product->code ? '(' . $product->code . ')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">التصنيف</label>
                            <select name="category" id="category" class="form-control select2">
                                <option value="">جميع التصنيفات</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">المصدر</label>
                            <select name="source_type" id="source_type" class="form-control select2">
                                <option value="">جميع المصادر</option>
                                <option value="6">فاتورة مرتجعة</option>
                                <option value="4">إشعار دائن</option>
                                <option value="3">فاتورة شراء</option>
                                <option value="7">مرتجع مشتريات</option>
                                <option value="14">اشعار مدين المشتريات</option>
                                <option value="2">فاتورة</option>
                                <option value="1">تعديل يدوي</option>
                                <option value="8">منتج مجمع</option>
                                <option value="9">إذن مخزن</option>
                                <option value="10">منتج مجمع الخارجي</option>
                                <option value="101">اذن مخزني اليدوي الداخلي</option>
                                <option value="102">اذن مخزني اليدوي الخارجي</option>
                                <option value="103">اذن مخزني فاتورة</option>
                                <option value="104">اذن مخزني مرتجع مبيعات</option>
                                <option value="105">اذن مخزني إشعار دائن</option>
                                <option value="106">اذن مخزني فاتورة الشراء</option>
                                <option value="107">اذن مخزني مرتجع شراء</option>
                                <option value="115">اذن مخزني إشعار مدين</option>
                                <option value="108">نقل اذن مخزني</option>
                                <option value="109">نقل اذن مخزني داخلي</option>
                                <option value="110">نقل اذن مخزني خارجي</option>
                                <option value="111">اذن مخزني نقطة البيع داخلي</option>
                                <option value="112">اذن مخزني نقطة البيع خارجي</option>
                                <option value="113">جرد المخزون الخارجي</option>
                                <option value="114">جرد المخزون الداخلي</option>
                                <option value="5">نقل المخزون</option>
                                <option value="116">طلب التصنيع</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">العلامة التجارية</label>
                            <select name="brand" id="brand" class="form-control select2">
                                <option value="">جميع العلامات</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand }}">{{ $brand }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">المستودع</label>
                            <select name="warehouse" id="warehouse" class="form-control select2">
                                <option value="">جميع المستودعات</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">العملة</label>
                            <select name="currency" id="currency" class="form-control select2">
                                <option value="">الكل</option>
                                <option value="All" selected>الجميع إلى (SAR)</option>
                                <option value="Separated">كل على حده</option>
                                <option value="SAR">SAR</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-lg-12 align-self-end">
                            <div class="d-flex gap-2 flex-wrap justify-content-center">
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
                        <button class="btn-modern btn-info-modern" id="exportPDF">
                            <i class="fas fa-file-pdf"></i>
                            تصدير PDF
                        </button>
                        <button class="btn-modern btn-outline-modern" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-cogs"></i>
                            تصدير متقدم
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
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stats-value" id="totalProducts">0</div>
                    <div class="stats-label">عدد المنتجات</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="stats-value" id="totalIn">0.00</div>
                    <div class="stats-label">إجمالي الداخل (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon danger">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="stats-value" id="totalOut">0.00</div>
                    <div class="stats-label">إجمالي الخارج (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div class="stats-value" id="netMovement">0.00</div>
                    <div class="stats-label">صافي الحركة (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div class="stats-value" id="totalStock">0.00</div>
                    <div class="stats-label">قيمة المخزون الحالي</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card dark">
                    <div class="stats-icon dark">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="stats-value" id="totalMovements">0</div>
                    <div class="stats-label">عدد الحركات</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني لحركة المخزون
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container">
                    <canvas class="chart bg-light" id="movementChart" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="card-modern fade-in" id="reportContainer">
            <div class="card-header-modern">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" id="reportTitle">
                        <i class="fas fa-table me-2"></i>
                        تقرير تفاصيل حركة المخزون لكل منتج
                    </h5>
                    <div class="pagination-info">
                        <small class="text-muted" id="recordsInfo">
                            عدد السجلات: <span id="recordCount">0</span> |
                            عدد المنتجات: <span id="productCount">0</span> |
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
                                <th><i class="fas fa-calendar me-1"></i>التاريخ</th>
                                <th><i class="fas fa-cog me-1"></i>العملية</th>
                                <th><i class="fas fa-source me-1"></i>المصدر</th>
                                <th><i class="fas fa-warehouse me-1"></i>المستودع</th>
                                <th><i class="fas fa-sort-numeric-up me-1"></i>الكمية</th>
                                <th><i class="fas fa-dollar-sign me-1"></i>سعر الوحدة</th>
                                <th><i class="fas fa-inventory me-1"></i>المخزون بعد</th>
                                <th><i class="fas fa-calculator me-1"></i>متوسط التكلفة</th>
                                <th><i class="fas fa-money-bill me-1"></i>قيمة الحركة</th>
                                <th><i class="fas fa-coins me-1"></i>السعر الكلي</th>
                                <th><i class="fas fa-chart-line me-1"></i>قيمة المخزون بعد</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            @php $currentProduct = null; @endphp
                            @foreach ($movements as $movement)
                                @if ($currentProduct !== $movement->product_id)
                                    @php $currentProduct = $movement->product_id; @endphp
                                    <tr>
                                        <td colspan="11" class="product-row">
                                            <i class="fas fa-box me-2"></i>
                                            {{ $movement->product->name ?? 'منتج غير محدد' }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>{{ $movement->created_at ? $movement->created_at->format('d/m/Y') : 'غير محدد' }}</td>
                                    <td>
                                        <span class="movement-badge {{ $movement->quantity > 0 ? 'movement-in' : 'movement-out' }}">
                                            {{ $movement->type ?? 'غير محدد' }}
                                        </span>
                                    </td>
                                    <td>{{ $movement->source_type ?? 'غير محدد' }}</td>
                                    <td>
                                        <i class="fas fa-warehouse me-1"></i>
                                        {{ $movement->storeHouse->name ?? 'غير محدد' }}
                                    </td>
                                    <td class="{{ $movement->quantity > 0 ? 'quantity-positive' : 'quantity-negative' }}">
                                        <i class="fas fa-{{ $movement->quantity > 0 ? 'plus' : 'minus' }} me-1"></i>
                                        {{ number_format(abs($movement->quantity ?? 0), 2) }}
                                    </td>
                                    <td class="value-cell">{{ number_format($movement->unit_price ?? 0, 2) }}</td>
                                    <td class="fw-bold">{{ number_format($movement->stock_after ?? 0, 2) }}</td>
                                    <td class="value-cell">{{ number_format($movement->purchase_price ?? 0, 2) }}</td>
                                    <td class="value-cell">{{ number_format($movement->total ?? 0, 2) }}</td>
                                    <td class="value-cell">{{ number_format($movement->total ?? 0, 2) }}</td>
                                    <td class="value-cell fw-bold">{{ number_format($movement->stock_value_after ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
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

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">خيارات التصدير المتقدم</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exportFormat" class="form-label">تنسيق الملف</label>
                        <select class="form-control" id="exportFormat">
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="pdf">PDF (.pdf)</option>
                            <option value="csv">CSV (.csv)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeSummary" checked>
                            <label class="form-check-label" for="includeSummary">
                                تضمين ملخص الإحصائيات
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeChart">
                            <label class="form-check-label" for="includeChart">
                                تضمين الرسم البياني (PDF فقط)
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="autoRefresh">
                            <label class="form-check-label" for="autoRefresh">
                                تحديث تلقائي كل 5 دقائق
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" id="exportAdvanced">تصدير</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        let movementChart;
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

            $('#exportPDF').click(function() {
                exportToPDF();
            });

            $('#printBtn').click(function() {
                window.print();
            });

            // View toggle
            $('#summaryViewBtn, #detailViewBtn').click(function() {
                toggleView($(this).attr('id'));
            });

            // معالج للفترات الزمنية السريعة
            $('.quick-date-range').click(function() {
                const range = $(this).data('range');
                setQuickDateRange(range);
                loadReportData(1);
            });

            // معالج للتصدير المتقدم
            $('#exportAdvanced').click(function() {
                const format = $('#exportFormat').val();
                const options = {
                    includeChart: $('#includeChart').is(':checked'),
                    includeSummary: $('#includeSummary').is(':checked'),
                    dateRange: `${$('#start_date').val()} - ${$('#end_date').val()}`
                };

                switch(format) {
                    case 'excel':
                        exportToAdvancedExcel(options);
                        break;
                    case 'pdf':
                        exportToAdvancedPDF(options);
                        break;
                    case 'csv':
                        exportToCSV();
                        break;
                }

                $('#exportModal').modal('hide');
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

            // تهيئة Select2 مع AJAX للمنتجات
            $('#product').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                allowClear: true,
                width: '100%',
                placeholder: 'ابحث عن منتج...',
                minimumInputLength: 1,
                ajax: {
                    url: '{{ route('StorHouseReport.searchProducts') }}',
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
                    },
                    cache: true
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
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                product: $('#product').val(),
                category: $('#category').val(),
                source_type: $('#source_type').val(),
                brand: $('#brand').val(),
                warehouse: $('#warehouse').val(),
                currency: $('#currency').val(),
                page: page,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: '{{ route('StorHouseReport.Inventory_mov_det_product_ajax') }}',
                method: 'GET',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        updateReportDisplay(response);
                        currentPage = response.pagination.current_page;
                        totalPages = response.pagination.last_page;
                        updatePagination(response.pagination);
                        showAlert('تم تحديث التقرير بنجاح!', 'success');
                    } else {
                        showAlert(response.message || 'حدث خطأ في تحميل البيانات', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('خطأ في تحميل البيانات:', error);
                    let errorMessage = 'حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    showAlert(errorMessage, 'danger');
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
            animateValue('#totalProducts', 0, data.totals.total_products || 0, 1000);
            animateValue('#totalIn', 0, data.totals.total_in || 0, 1000);
            animateValue('#totalOut', 0, data.totals.total_out || 0, 1000);
            animateValue('#netMovement', 0, data.totals.net_movement || 0, 1000);
            animateValue('#totalStock', 0, data.totals.total_stock_value || 0, 1000);
            animateValue('#totalMovements', 0, data.totals.total_movements || 0, 1000);

            // تحديث معلومات العد
            $('#recordCount').text(data.pagination.total || 0);
            $('#productCount').text(data.products_count || 0);
            $('#currentPage').text(data.pagination.current_page || 1);
            $('#totalPages').text(data.pagination.last_page || 1);

            // تحديث جدول البيانات
            updateTableBody(data.grouped_movements || {});

            // تحديث الرسم البياني
            updateChart(data.chart_data);
        }

        // تحديث محتوى الجدول
        function updateTableBody(groupedMovements) {
            let tableHtml = '';
            let previousProductId = null;
            let previousStockAfter = 0;

            if (!groupedMovements || Object.keys(groupedMovements).length === 0) {
                tableHtml = `
                    <tr>
                        <td colspan="11" class="text-center py-4">
                            <i class="fas fa-info-circle me-2"></i>
                            لا توجد بيانات متاحة
                        </td>
                    </tr>`;
            } else {
                // Sort products alphabetically
                const sortedProducts = Object.entries(groupedMovements).sort((a, b) => a[0].localeCompare(b[0]));

                sortedProducts.forEach(([productName, movements]) => {
                    // Add product header row
                    tableHtml += `
                        <tr class="product-header">
                            <td colspan="11" class="bg-light">
                                <i class="fas fa-box me-2"></i>
                                ${productName}
                            </td>
                        </tr>`;

                    // Sort movements by date (newest first)
                    const sortedMovements = [...movements].sort((a, b) => {
                        return new Date(b.created_at) - new Date(a.created_at);
                    });

                    // Add movement rows
                    sortedMovements.forEach((movement, index) => {
                        const movementClass = movement.quantity > 0 ? 'movement-in' : 'movement-out';
                        const movementIcon = movement.quantity > 0 ? 'plus' : 'minus';

                        // Get the current stock from product_details
                        let stockAfter = movement.stock_after || 0;
                        // For display purposes, we'll show the stock_after value directly from the database
                        // which represents the actual quantity in product_details

                        // Get source name from permission source if available
                        let sourceName = movement.source_type || 'غير محدد';
                        if (movement.permission_source) {
                            if (typeof movement.permission_source === 'object' && movement.permission_source !== null) {
                                sourceName = movement.permission_source.name || sourceName;
                                if (movement.permission_source.category) {
                                    sourceName += ` (${movement.permission_source.category})`;
                                }
                            } else if (typeof movement.permission_source === 'string') {
                                sourceName = movement.permission_source;
                            }
                        }

                        // Format dates in Gregorian (Miladi) format
                        const formattedDate = new Date(movement.created_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                        const formattedQuantity = Math.abs(movement.quantity || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        const formattedUnitPrice = (movement.unit_price || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        const formattedStockAfter = stockAfter.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        const formattedPurchasePrice = (movement.purchase_price || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        const formattedTotal = (movement.total || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        const stockValueAfter = (stockAfter * (movement.unit_price || 0)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                        tableHtml += `
                        <tr>
                            <td>${formattedDate}</td>
                            <td><span class="movement-badge ${movementClass}">${movement.type || 'غير محدد'}</span></td>
                            <td>${sourceName}</td>
                            <td><i class="fas fa-warehouse me-1"></i> ${movement.store_house?.name || 'غير محدد'}</td>
                            <td class="${movementClass}">
                                <i class="fas fa-${movementIcon} me-1"></i>
                                ${formattedQuantity}
                            </td>
                            <td class="value-cell">${formattedUnitPrice}</td>
                            <td class="fw-bold">${formattedStockAfter}</td>
                            <td class="value-cell">${formattedPurchasePrice}</td>
                            <td class="value-cell">${formattedTotal}</td>
                            <td class="value-cell">${formattedTotal}</td>
                            <td class="value-cell fw-bold">${stockValueAfter}</td>
                        </tr>`;
                    });
                });
            }

            $('#reportTableBody').html(tableHtml);

            // Apply any additional formatting or event handlers
            applyTableEnhancements();
        }

        function applyTableEnhancements() {
            // Add hover effects and tooltips
            $('#reportTable tbody tr:not(.product-header)').hover(
                function() { $(this).addClass('table-active'); },
                function() { $(this).removeClass('table-active'); }
            );

            // Add click handler for movement details
            $('#reportTable tbody tr:not(.product-header)').on('click', function() {
                // Add your click handler logic here
                // For example, show a modal with movement details
            });
        }

        // تحديث الـ pagination
        function updatePagination(paginationData) {
            const container = $('#customPagination');
            let paginationHtml = '';

            const current = paginationData.current_page;
            const total = paginationData.last_page;

            if (total <= 1) {
                container.html('');
                return;
            }

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
                        <td colspan="11" class="text-center py-5">
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
            $('#currency').val('All').trigger('change');
            currentPage = 1;
            loadReportData(1);
            showAlert('تم إعادة تعيين الفلاتر', 'info');
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

        // تحديد نطاقات التاريخ السريعة
        function setQuickDateRange(range) {
            const today = new Date();
            let startDate, endDate;

            switch(range) {
                case 'today':
                    startDate = endDate = today.toISOString().split('T')[0];
                    break;
                case 'week':
                    startDate = new Date(today.setDate(today.getDate() - 7)).toISOString().split('T')[0];
                    endDate = new Date().toISOString().split('T')[0];
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                    endDate = new Date().toISOString().split('T')[0];
                    break;
                case 'quarter':
                    const quarter = Math.floor((today.getMonth() + 3) / 3);
                    startDate = new Date(today.getFullYear(), (quarter - 1) * 3, 1).toISOString().split('T')[0];
                    endDate = new Date().toISOString().split('T')[0];
                    break;
            }

            $('#start_date').val(startDate);
            $('#end_date').val(endDate);

            // تحديث الأزرار
            $('.quick-date-range').removeClass('btn-primary').addClass('btn-outline-primary');
            $(`.quick-date-range[data-range="${range}"]`).removeClass('btn-outline-primary').addClass('btn-primary');
        }

        // دالة للحصول على كلاس القيمة
        function getValueClass(value) {
            const numValue = parseFloat(value);
            if (numValue > 10000) return 'value-high';
            if (numValue > 1000) return 'value-medium';
            return 'value-low';
        }

        // تهيئة الرسم البياني
        function initializeChart() {
            const ctx = document.getElementById('movementChart').getContext('2d');

            movementChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'حركة الداخل (ريال)',
                        data: [],
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'حركة الخارج (ريال)',
                        data: [],
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'حركة المخزون خلال الفترة المحددة',
                            font: {
                                size: 18,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
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
                                    return context.dataset.label + ': ' + formatNumber(context.parsed.y) + ' ريال';
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
                                    size: 12
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

        // تحديث الرسم البياني
        function updateChart(chartData) {
            if (movementChart && chartData) {
                movementChart.data.labels = chartData.labels || [];
                movementChart.data.datasets[0].data = chartData.in_values || [];
                movementChart.data.datasets[1].data = chartData.out_values || [];
                movementChart.update('active');
            }
        }

        // تنسيق التاريخ
        function formatDate(dateString) {
            if (!dateString) return 'غير محدد';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            } catch (e) {
                console.error('Error formatting date:', e);
                return dateString;
            }
        }

        // تنسيق الأرقام
        function formatNumber(number) {
            return parseFloat(number || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // الرسوم المتحركة للأرقام
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

        // تصدير إكسل بسيط
        function exportToExcel() {
            showAlert('جاري تصدير الملف...', 'info');

            const table = document.querySelector('#reportContainer table');
            const wb = XLSX.utils.table_to_book(table, {
                raw: false,
                cellDates: true
            });

            const today = new Date();
            const fileName = `تقرير_حركة_المخزون_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

            XLSX.writeFile(wb, fileName);
            showAlert('تم تصدير الملف بنجاح!', 'success');
        }

        // تصدير PDF بسيط
        function exportToPDF() {
            showAlert('جاري تصدير ملف PDF...', 'info');

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // إضافة عنوان
            doc.setFontSize(16);
            doc.text('تقرير تفاصيل حركة المخزون لكل منتج', 20, 20);

            // إضافة الجدول
            doc.autoTable({
                html: '#reportTable',
                startY: 30,
                theme: 'grid',
                styles: {
                    font: 'helvetica',
                    fontSize: 8
                },
                headStyles: {
                    fillColor: [102, 126, 234],
                    textColor: 255
                }
            });

            const today = new Date();
            const fileName = `تقرير_حركة_المخزون_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.pdf`;

            doc.save(fileName);
            showAlert('تم تصدير ملف PDF بنجاح!', 'success');
        }

        // تصدير Excel متقدم
        function exportToAdvancedExcel(options) {
            showAlert('جاري إعداد ملف Excel المتقدم...', 'info');

            // إنشاء workbook متعدد الأوراق
            const wb = XLSX.utils.book_new();

            // ورقة البيانات الرئيسية
            const ws1 = XLSX.utils.table_to_sheet(document.querySelector('#reportTable'));
            XLSX.utils.book_append_sheet(wb, ws1, "حركة المخزون");

            // ورقة الملخص إذا كانت مطلوبة
            if (options.includeSummary) {
                const summaryData = [
                    ['الإحصائية', 'القيمة'],
                    ['عدد المنتجات', $('#totalProducts').text()],
                    ['إجمالي الداخل', $('#totalIn').text()],
                    ['إجمالي الخارج', $('#totalOut').text()],
                    ['صافي الحركة', $('#netMovement').text()],
                    ['قيمة المخزون الحالي', $('#totalStock').text()],
                    ['عدد الحركات', $('#totalMovements').text()],
                    ['فترة التقرير', options.dateRange]
                ];
                const ws2 = XLSX.utils.aoa_to_sheet(summaryData);
                XLSX.utils.book_append_sheet(wb, ws2, "الملخص");
            }

            const fileName = `تقرير_حركة_المخزون_متقدم_${new Date().toISOString().split('T')[0]}.xlsx`;
            XLSX.writeFile(wb, fileName);

            showAlert('تم تصدير ملف Excel المتقدم بنجاح!', 'success');
        }

        // تصدير PDF متقدم
        function exportToAdvancedPDF(options) {
            showAlert('جاري إعداد ملف PDF المتقدم...', 'info');

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // إضافة header
            doc.setFontSize(18);
            doc.text('تقرير تفاصيل حركة المخزون لكل منتج', 105, 20, { align: 'center' });

            if (options.dateRange) {
                doc.setFontSize(12);
                doc.text(`الفترة: ${options.dateRange}`, 105, 35, { align: 'center' });
            }

            let yPosition = 50;

            // إضافة الملخص إذا كان مطلوباً
            if (options.includeSummary) {
                doc.setFontSize(14);
                doc.text('ملخص الإحصائيات:', 20, yPosition);
                yPosition += 10;

                doc.setFontSize(10);
                doc.text(`عدد المنتجات: ${$('#totalProducts').text()}`, 20, yPosition);
                yPosition += 7;
                doc.text(`إجمالي الداخل: ${$('#totalIn').text()} ريال`, 20, yPosition);
                yPosition += 7;
                doc.text(`إجمالي الخارج: ${$('#totalOut').text()} ريال`, 20, yPosition);
                yPosition += 7;
                doc.text(`صافي الحركة: ${$('#netMovement').text()} ريال`, 20, yPosition);
                yPosition += 7;
                doc.text(`قيمة المخزون الحالي: ${$('#totalStock').text()} ريال`, 20, yPosition);
                yPosition += 15;
            }

            // إضافة الجدول
            doc.autoTable({
                html: '#reportTable',
                startY: yPosition,
                theme: 'grid',
                styles: {
                    font: 'helvetica',
                    fontSize: 8,
                    cellPadding: 2
                },
                headStyles: {
                    fillColor: [102, 126, 234],
                    textColor: 255,
                    fontStyle: 'bold'
                },
                alternateRowStyles: {
                    fillColor: [245, 245, 245]
                }
            });

            const fileName = `تقرير_حركة_المخزون_متقدم_${new Date().toISOString().split('T')[0]}.pdf`;
            doc.save(fileName);

            showAlert('تم تصدير ملف PDF المتقدم بنجاح!', 'success');
        }

        // تصدير CSV
        function exportToCSV() {
            showAlert('جاري تصدير ملف CSV...', 'info');

            const csv = XLSX.utils.table_to_book(document.querySelector('#reportTable'), {sheet: "حركة المخزون"});
            const csvOutput = XLSX.write(csv, {bookType:'csv', type:'string'});

            const blob = new Blob([csvOutput], {type: 'text/csv;charset=utf-8;'});
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            link.setAttribute('href', url);
            link.setAttribute('download', `تقرير_حركة_المخزون_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            showAlert('تم تصدير ملف CSV بنجاح!', 'success');
        }

        // عرض التنبيهات
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

        // تأثيرات hover
        $(document).on('mouseenter', '.stats-card', function() {
            $(this).css('transform', 'translateY(-8px) scale(1.02)');
        }).on('mouseleave', '.stats-card', function() {
            $(this).css('transform', 'translateY(0) scale(1)');
        });

        $(document).on('mouseenter', '.btn-modern:not(.active)', function() {
            $(this).css('transform', 'translateY(-2px)');
        }).on('mouseleave', '.btn-modern:not(.active)', function() {
            $(this).css('transform', 'translateY(0)');
        });

        // تأثيرات إضافية لتحسين التفاعل
        $(document).on('click', '.product-row', function() {
            const nextRows = $(this).nextUntil('.product-row');
            nextRows.fadeToggle();
        });

        // Auto-refresh كل 5 دقائق للبيانات الحية
        setInterval(function() {
            if (!isLoading && $('#autoRefresh').is(':checked')) {
                loadReportData(currentPage);
            }
        }, 300000); // 5 دقائق

        // دعم الطباعة المتقدمة
        window.onbeforeprint = function() {
            // إخفاء العناصر غير المرغوب بطباعتها
            $('.no-print, .btn-modern, .pagination-custom').hide();
            // تكبير الجدول للطباعة
            $('.table-optimized').css('font-size', '10px');
        };

        window.onafterprint = function() {
            // إظهار العناصر مرة أخرى
            $('.no-print, .btn-modern, .pagination-custom').show();
            $('.table-optimized').css('font-size', '13px');
        };

        // دعم اختصارات لوحة المفاتيح
        $(document).keydown(function(e) {
            // Ctrl+F للبحث السريع
            if (e.ctrlKey && e.keyCode === 70) {
                e.preventDefault();
                $('#product').focus();
                return false;
            }
            // Ctrl+R لتحديث البيانات
            if (e.ctrlKey && e.keyCode === 82) {
                e.preventDefault();
                loadReportData(currentPage);
                return false;
            }
            // Ctrl+P للطباعة
            if (e.ctrlKey && e.keyCode === 80) {
                e.preventDefault();
                window.print();
                return false;
            }
            // Ctrl+E للتصدير
            if (e.ctrlKey && e.keyCode === 69) {
                e.preventDefault();
                $('#exportModal').modal('show');
                return false;
            }
        });

        // تحسين الاستجابة للشاشات الصغيرة
        function adjustForMobile() {
            if ($(window).width() < 768) {
                // تصغير الجدول أكثر للموبايل
                $('.table-optimized').css('font-size', '10px');
                // إخفاء بعض الأعمدة غير الضرورية في الموبايل
                $('.table-optimized th:nth-child(n+8), .table-optimized td:nth-child(n+8)').hide();
                // تصغير البطاقات
                $('.stats-value').css('font-size', '1.8rem');
            } else {
                $('.table-optimized').css('font-size', '13px');
                $('.table-optimized th, .table-optimized td').show();
                $('.stats-value').css('font-size', '2.5rem');
            }
        }

        // تطبيق التعديلات عند تغيير حجم الشاشة
        $(window).resize(adjustForMobile);
        adjustForMobile();

        // تفعيل tooltips للمساعدة
        $('[data-bs-toggle="tooltip"]').tooltip();

        // معالجة الأخطاء العامة
        window.onerror = function(msg, url, line, col, error) {
            console.error('خطأ في الصفحة:', { msg, url, line, col, error });
            showAlert('حدث خطأ غير متوقع. يرجى تحديث الصفحة.', 'warning');
        };

        // إضافة مؤشر التحميل المتقدم
        function showAdvancedLoader() {
            const loader = `
                <div id="advanced-loader" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.9); z-index: 9999;">
                    <div class="text-center">
                        <div class="spinner-grow text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
                        <h5>جاري تحميل البيانات...</h5>
                        <p class="text-muted">يرجى الانتظار</p>
                    </div>
                </div>
            `;
            if (!$('#advanced-loader').length) {
                $('body').append(loader);
            }
        }

        function hideAdvancedLoader() {
            $('#advanced-loader').fadeOut(300, function() {
                $(this).remove();
            });
        }

        // معالج AJAX العام
        $(document).ajaxStart(function() {
            showAdvancedLoader();
        }).ajaxStop(function() {
            hideAdvancedLoader();
        });

        // إضافة تلميحات مساعدة
        $('.form-label-modern').attr('title', 'انقر للمساعدة').tooltip();

        // معالج لحفظ حالة الفلاتر (بدون localStorage)
        let savedFilters = {};

        function saveFilterSettings() {
            savedFilters = {
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                product: $('#product').val(),
                category: $('#category').val(),
                source_type: $('#source_type').val(),
                brand: $('#brand').val(),
                warehouse: $('#warehouse').val(),
                currency: $('#currency').val()
            };
        }

        function loadFilterSettings() {
            if (Object.keys(savedFilters).length > 0) {
                Object.keys(savedFilters).forEach(key => {
                    $(`#${key}`).val(savedFilters[key]).trigger('change');
                });
            }
        }

        // تطبيق الفلاتر عند تغيير القيم
        $('.form-control, .select2').on('change', function() {
            saveFilterSettings();
        });

        // تحسينات نهائية للأداء
        console.log('تم تحميل تقرير حركة المخزون بنجاح!');
        console.log('الإصدار: 2.0.0');
        console.log('آخر تحديث: ' + new Date().toLocaleDateString('ar-SA'));

        // إضافة معلومات المطور
        console.log('%cتم تطوير هذا التقرير بواسطة فريق التطوير', 'color: #667eea; font-weight: bold; font-size: 14px;');
        console.log('%cجميع الحقوق محفوظة © 2024', 'color: #6c757d; font-size: 12px;');
    </script>
@endsection
