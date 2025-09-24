@extends('master')

@section('title')
    تقرير دليل العملاء
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
        /* Custom Pagination Styles */
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

        /* Optimized table styles */
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

        /* Loading spinner */
        .table-loading {
            position: relative;
        }

        .table-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .table-optimized {
                font-size: 11px;
            }

            .table-optimized th,
            .table-optimized td {
                padding: 6px 4px;
            }
        }

        /* Group info styling */
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

        .avatar-sm {
            width: 35px;
            height: 35px;
            font-size: 14px;
        }

        .location-link {
            color: var(--primary-color, #007bff);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            background: rgba(0, 123, 255, 0.1);
            transition: all 0.3s ease;
        }

        .location-link:hover {
            background: #007bff;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .map-container {
            height: 500px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        #mapPlaceholder {
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            position: relative;
            overflow: hidden;
        }

        #mapPlaceholder::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                radial-gradient(circle at 25% 25%, rgba(0, 123, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(108, 117, 125, 0.1) 0%, transparent 50%);
        }

        .placeholder-content {
            text-align: center;
            z-index: 1;
            position: relative;
        }

        .placeholder-content i {
            font-size: 4rem;
            color: #007bff;
            margin-bottom: 1rem;
            opacity: 0.7;
        }

        .placeholder-content h5 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .placeholder-content p {
            color: #6c757d;
            margin: 0;
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
                        <i class="fas fa-address-book me-3"></i>
                        تقرير دليل العملاء
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">التقارير</a>
                            </li>
                            <li class="breadcrumb-item active">دليل العملاء</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-address-book"></i>
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
                                    <option value="{{ $group->id }}">{{ $group->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">الحي</label>
                            <select name="neighborhood" id="neighborhood" class="form-control select2">
                                <option value="">جميع الأحياء</option>
                                @foreach ($neighborhoods as $neighborhood)
                                    <option value="{{ $neighborhood->id }}">{{ $neighborhood->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">المدينة</label>
                            <input type="text" name="city" id="city" class="form-control" placeholder="أدخل اسم المدينة">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">البلد</label>
                            <input type="text" name="country" id="country" class="form-control" placeholder="أدخل اسم البلد">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">تجميع حسب</label>
                            <select id="group-by" name="group_by" class="form-control select2">
                                <option value="العميل">العميل</option>
                                <option value="الفرع">الفرع</option>
                                <option value="المدينة">المدينة</option>
                                <option value="المجموعة">المجموعة</option>
                                <option value="الحي">الحي</option>
                                <option value="التصنيف">التصنيف</option>
                            </select>
                        </div>

                        <!-- Third Row -->
                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <input type="checkbox" id="view-details" name="view_details" class="form-check-input me-2">
                            <label for="view-details" class="form-check-label">مشاهدة التفاصيل</label>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-lg-9 align-self-end">
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
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="stats-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-value" id="totalClients">0</div>
                    <div class="stats-label">إجمالي العملاء</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="stats-value" id="clientsWithLocations">0</div>
                    <div class="stats-label">عملاء بمواقع</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stats-value" id="totalBranches">0</div>
                    <div class="stats-label">عدد الفروع</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="stats-value" id="totalNeighborhoods">0</div>
                    <div class="stats-label">عدد الأحياء</div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="card-modern fade-in" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-map-marked-alt me-2"></i>
                    خريطة مواقع العملاء
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="map-container">
                    <!-- Placeholder -->
                    <div id="mapPlaceholder" class="text-center p-5">
                        <div class="placeholder-content">
                            <i class="fas fa-map-marked-alt"></i>
                            <h5>خريطة مواقع العملاء</h5>
                            <p>اختر عميلاً من الجدول لعرض موقعه على الخريطة</p>
                        </div>
                    </div>

                    <!-- Actual Map (hidden by default) -->
                    <div id="clientMap" style="height: 500px; display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="card-modern fade-in" id="reportContainer">
            <div class="card-header-modern">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" id="reportTitle">
                        <i class="fas fa-table me-2"></i>
                        تقرير دليل العملاء
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
                                <th><i class="fas fa-hashtag me-1"></i>#</th>
                                <th><i class="fas fa-code me-1"></i>الكود</th>
                                <th><i class="fas fa-user me-1"></i>اسم العميل</th>
                                <th><i class="fas fa-envelope me-1"></i>البريد الإلكتروني</th>
                                <th><i class="fas fa-phone me-1"></i>الهاتف</th>
                                <th><i class="fas fa-building me-1"></i>الفرع</th>
                                <th><i class="fas fa-tags me-1"></i>التصنيف</th>
                                <th><i class="fas fa-home me-1"></i>الحي</th>
                                <th><i class="fas fa-layer-group me-1"></i>المجموعة</th>
                                <th><i class="fas fa-city me-1"></i>المدينة</th>
                                <th><i class="fas fa-flag me-1"></i>البلد</th>
                                <th><i class="fas fa-map-marker-alt me-1"></i>الموقع</th>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap&libraries=places&v=weekly" async defer></script>

    <script>
        let map;
        const markers = [];
        let mapInitialized = false;
        let currentPage = 1;
        let totalPages = 1;
        let isLoading = false;

        $(document).ready(function() {
            // تهيئة Select2
            initializeSelect2();

            // تحميل البيانات الأولية
            loadReportData(1);

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

            // Event handlers for dependent dropdowns
            $('#region_group').on('change', function() {
                const regionGroupId = $(this).val();
                loadNeighborhoods(regionGroupId);
            });

            $('#branch').on('change', function() {
                const branchId = $(this).val();
                loadRegionGroups(branchId);
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

            // تهيئة Select2 مع AJAX للعملاء (إذا كان متوفراً)
            if ($('#customer').hasClass('select2-ajax')) {
                $('#customer').select2({
                    theme: 'bootstrap-5',
                    dir: 'rtl',
                    allowClear: true,
                    width: '100%',
                    placeholder: 'ابحث عن عميل...',
                    minimumInputLength: 1,
                    ajax: {
                        url: '{{ route('ClientReport.searchClients') ?? '' }}',
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
        }

        // دالة تحميل بيانات التقرير
        function loadReportData(page = 1) {
            if (isLoading) return;

            isLoading = true;
            showTableLoading(true);
            $('#filterBtn').prop('disabled', true).addClass('loading');

            const formData = $('#reportForm').serialize() + '&page=' + page;

            $.ajax({
                url: '{{ route('ClientReport.customerGuideAjax') }}',
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
            animateValue('#totalClients', 0, data.totals.total_clients, 1000);
            animateValue('#clientsWithLocations', 0, data.totals.clients_with_locations, 1000);
            animateValue('#totalBranches', 0, data.totals.total_branches, 1000);
            animateValue('#totalNeighborhoods', 0, data.totals.total_neighborhoods, 1000);

            // تحديث معلومات العد
            $('#recordCount').text(data.pagination.total || 0);
            $('#clientCount').text(data.clients_count || 0);
            $('#currentPage').text(data.pagination.current_page || 1);
            $('#totalPages').text(data.pagination.last_page || 1);

            // تحديث عنوان التقرير
            $('#reportTitle').html(`
                <i class="fas fa-table me-2"></i>
                تقرير دليل العملاء - تجميع حسب ${data.group_by || 'العميل'}
            `);

            // تحديث جدول البيانات
            updateTableBody(data.clients);

            // تحديث الخريطة إذا كان هناك عملاء بمواقع
            if (data.clients && data.clients.length > 0) {
                updateMapMarkers(data.clients);
            }
        }

        // تحديث محتوى الجدول
        function updateTableBody(clients) {
            let tableHtml = '';

            if (!clients || clients.length === 0) {
                tableHtml = `
                    <tr>
                        <td colspan="12" class="text-center py-4">
                            <i class="fas fa-info-circle me-2"></i>
                            لا توجد بيانات لعرضها
                        </td>
                    </tr>
                `;
            } else {
                clients.forEach((client, index) => {
                    const hasLocation = client.locations && client.locations.latitude && client.locations.longitude;
                    const globalIndex = ((currentPage - 1) * 50) + index + 1; // حساب الرقم العام

                    tableHtml += `<tr data-client-id="${client.id}">`;
                    tableHtml += `<td><span class="fw-bold text-primary">${globalIndex}</span></td>`;
                    tableHtml += `<td><span class="badge bg-secondary">${client.code || 'غير محدد'}</span></td>`;

                    tableHtml += `<td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div class="fw-bold">${client.trade_name || 'غير محدد'}</div>
                            </div>
                        </div>
                    </td>`;

                    tableHtml += `<td>${client.email ? `<small class="text-muted">${client.email}</small>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                    tableHtml += `<td>${client.phone || '<span class="text-muted">غير محدد</span>'}</td>`;
                    tableHtml += `<td>${client.branch ? `<span class="badge bg-info">${client.branch}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                    tableHtml += `<td>${client.category ? `<span class="badge bg-success">${client.category}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                    tableHtml += `<td>${client.neighborhood ? `<span class="badge bg-warning">${client.neighborhood}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                    tableHtml += `<td>${client.region_group ? `<span class="badge bg-primary">${client.region_group}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                    tableHtml += `<td>${client.city || '<span class="text-muted">غير محدد</span>'}</td>`;
                    tableHtml += `<td>${client.country || '<span class="text-muted">غير محدد</span>'}</td>`;

                    if (hasLocation) {
                        tableHtml += `<td>
                            <a href="#chartSection" class="location-link"
                                data-lat="${client.locations.latitude}"
                                data-lng="${client.locations.longitude}"
                                data-name="${client.trade_name}"
                                data-code="${client.code}">
                                <i class="fas fa-map-marker-alt"></i>
                                عرض على الخريطة
                            </a>
                        </td>`;
                    } else {
                        tableHtml += `<td><span class="text-muted"><i class="fas fa-times-circle me-1"></i>غير متوفر</span></td>`;
                    }

                    tableHtml += `</tr>`;
                });
            }

            $('#reportTableBody').html(tableHtml);

            // إعادة ربط معالجات النقر على روابط الموقع
            $('.location-link').off('click').on('click', function(e) {
                e.preventDefault();
                const lat = parseFloat($(this).data('lat'));
                const lng = parseFloat($(this).data('lng'));
                const clientId = $(this).closest('tr').data('client-id');
                const name = $(this).data('name');
                const code = $(this).data('code');

                showMapWithMarker(lat, lng, clientId, name, code);
            });
        }

        // تحديث الـ pagination
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
                        <td colspan="12" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            <div class="mt-2">جاري تحميل البيانات...</div>
                        </td>
                    </tr>
                `);
            }
        }

        // دالة تحميل الأحياء حسب المجموعة
        function loadNeighborhoods(regionGroupId) {
            if (!regionGroupId) {
                $('#neighborhood').empty().append('<option value="">جميع الأحياء</option>');
                return;
            }

            $.ajax({
                url: '{{ route('ClientReport.getNeighborhoods') }}',
                method: 'GET',
                data: { region_group_id: regionGroupId },
                success: function(neighborhoods) {
                    $('#neighborhood').empty().append('<option value="">جميع الأحياء</option>');
                    neighborhoods.forEach(function(neighborhood) {
                        $('#neighborhood').append(`<option value="${neighborhood.id}">${neighborhood.name}</option>`);
                    });
                },
                error: function() {
                    showAlert('خطأ في تحميل الأحياء', 'warning');
                }
            });
        }

        // دالة تحميل المجموعات حسب الفرع
        function loadRegionGroups(branchId) {
            if (!branchId) {
                $('#region_group').empty().append('<option value="">جميع المجموعات</option>');
                return;
            }

            $.ajax({
                url: '{{ route('ClientReport.getRegionGroups') }}',
                method: 'GET',
                data: { branch_id: branchId },
                success: function(groups) {
                    $('#region_group').empty().append('<option value="">جميع المجموعات</option>');
                    groups.forEach(function(group) {
                        $('#region_group').append(`<option value="${group.id}">${group.name}</option>`);
                    });
                },
                error: function() {
                    showAlert('خطأ في تحميل المجموعات', 'warning');
                }
            });
        }

        // إعادة تعيين الفلاتر
        function resetFilters() {
            $('#reportForm')[0].reset();
            $('.select2').val(null).trigger('change');
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

        // الرسوم المتحركة للأرقام
        function animateValue(element, start, end, duration) {
            const obj = $(element);
            const range = Math.abs(end - start);

            if (range < 1) {
                obj.text(end);
                return;
            }

            const startTime = Date.now();
            const timer = setInterval(function() {
                const elapsed = Date.now() - startTime;
                const progress = Math.min(elapsed / duration, 1);

                const current = start + (end - start) * progress;
                obj.text(Math.round(current));

                if (progress >= 1) {
                    obj.text(end);
                    clearInterval(timer);
                }
            }, 16);
        }

        // تصدير إكسل
        function exportToExcel() {
            showAlert('جاري تصدير الملف...', 'info');

            const table = document.querySelector('#reportContainer table');
            const wb = XLSX.utils.table_to_book(table, {
                raw: false,
                cellDates: true
            });

            const today = new Date();
            const fileName = `تقرير_دليل_العملاء_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

            XLSX.writeFile(wb, fileName);
            showAlert('تم تصدير الملف بنجاح!', 'success');
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

        // دوال الخريطة
        function initMap() {
            const mapElement = document.getElementById("clientMap");
            if (!mapElement) return;

            map = new google.maps.Map(mapElement, {
                center: { lat: 24.7136, lng: 46.6753 },
                zoom: 6,
                styles: [
                    {
                        "featureType": "poi",
                        "stylers": [{ "visibility": "off" }]
                    }
                ]
            });
            mapInitialized = true;
        }

        function showMapWithMarker(lat, lng, clientId, name, code) {
            // إظهار الخريطة
            $('#mapPlaceholder').hide();
            $('#clientMap').show();

            // التمرير إلى قسم الخريطة
            document.getElementById('chartSection').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // تهيئة الخريطة إذا لم تكن مهيأة
            if (!mapInitialized) {
                initMap();
            }

            // توسيط الخريطة على الموقع المحدد
            const position = { lat, lng };
            map.setCenter(position);
            map.setZoom(15);

            // إزالة العلامات السابقة
            markers.forEach(marker => marker.setMap(null));
            markers.length = 0;

            // إضافة علامة جديدة
            const marker = new google.maps.Marker({
                position,
                map,
                title: name,
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
                }
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="direction: rtl; text-align: right; min-width: 200px;">
                        ${name ? `<h6 style="margin-bottom: 5px; color: #2575fc;">${name}</h6>` : ''}
                        ${code ? `<p style="margin: 0; color: #6c757d;"><small>الكود: ${code}</small></p>` : ''}
                        <div style="margin-top: 10px;">
                            <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank"
                               style="color: #4caf50; text-decoration: none;">
                                <i class="fas fa-external-link-alt"></i> فتح في خرائط جوجل
                            </a>
                        </div>
                    </div>
                `,
            });

            marker.addListener("click", () => {
                infoWindow.open(map, marker);
            });

            markers.push(marker);

            // تحريك العلامة
            marker.setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(() => {
                marker.setAnimation(null);
            }, 1500);

            // فتح نافذة المعلومات
            infoWindow.open(map, marker);
        }

        function updateMapMarkers(clients) {
            if (!mapInitialized) return;

            // إزالة العلامات السابقة
            markers.forEach(marker => marker.setMap(null));
            markers.length = 0;

            // إضافة علامات جديدة
            clients.forEach(client => {
                if (client.locations && client.locations.latitude && client.locations.longitude) {
                    const marker = new google.maps.Marker({
                        position: {
                            lat: parseFloat(client.locations.latitude),
                            lng: parseFloat(client.locations.longitude)
                        },
                        map,
                        title: client.trade_name,
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
                        }
                    });

                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div style="direction: rtl; text-align: right; min-width: 200px;">
                                <h6 style="margin-bottom: 5px; color: #2575fc;">${client.trade_name}</h6>
                                <p style="margin: 0; color: #6c757d;"><small>الكود: ${client.code}</small></p>
                                <div style="margin-top: 10px;">
                                    <a href="https://www.google.com/maps?q=${client.locations.latitude},${client.locations.longitude}" target="_blank"
                                       style="color: #4caf50; text-decoration: none;">
                                        <i class="fas fa-external-link-alt"></i> فتح في خرائط جوجل
                                    </a>
                                </div>
                            </div>
                        `,
                    });

                    marker.addListener("click", () => {
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                }
            });
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
    </script>
@endsection
