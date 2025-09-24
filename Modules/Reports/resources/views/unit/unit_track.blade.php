@extends('master')

@section('title')
    تقرير تتبع الوحدات
@stop

@section('css')
    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
    <style>
        /* تحسينات Select2 */
        .select2-container {
            display: block !important;
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: auto !important;
            min-height: 48px !important;
            border: 2px solid #e9ecef !important;
            border-radius: 12px !important;
            padding: 0.5rem 1rem !important;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
        }

        .select2-dropdown {
            border: 2px solid #e9ecef !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            max-height: 300px !important;
            overflow-y: auto !important;
        }

        .select2-results {
            max-height: 300px !important;
            overflow-y: auto !important;
        }

        .select2-results__option {
            padding: 0.5rem 1rem !important;
        }

        .select2-results__option--highlighted {
            background-color: #0d6efd !important;
        }

        .select2-selection__rendered {
            color: #495057 !important;
            line-height: 1.5 !important;
        }

        .select2-selection__placeholder {
            color: #6c757d !important;
        }

        .select2-selection__arrow {
            height: 46px !important;
            right: 10px !important;
        }

        /* إعدادات خاصة للجوال */
        @media (max-width: 768px) {
            .select2-search--dropdown {
                display: block !important;
            }

            .select2-search__field {
                display: block !important;
                width: 100% !important;
                padding: 8px 12px !important;
                border: 1px solid #ddd !important;
                border-radius: 4px !important;
                font-size: 16px !important;
                -webkit-appearance: none !important;
                appearance: none !important;
            }

            .select2-dropdown {
                position: fixed !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                width: 90% !important;
                max-width: 400px !important;
                z-index: 99999 !important;
            }
        }

        /* أنماط خاصة للحالات */
        .status-active { background: linear-gradient(135deg, #10b981, #059669); }
        .status-inactive { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .status-maintenance { background: linear-gradient(135deg, #f59e0b, #d97706); }

        /* تحسينات الجدول */
        .table-modern tbody tr.high-priority {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
        }

        .table-modern tbody tr.medium-priority {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
        }

        .table-modern tbody tr.low-priority {
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
        }

        /* أنماط للبطاقات */
        .unit-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .unit-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .unit-card.high-priority {
            border-left: 4px solid #ef4444;
            background: linear-gradient(90deg, #fef2f2 0%, white 10%);
        }

        .unit-card.medium-priority {
            border-left: 4px solid #f59e0b;
            background: linear-gradient(90deg, #fffbeb 0%, white 10%);
        }

        .unit-card.low-priority {
            border-left: 4px solid #10b981;
            background: linear-gradient(90deg, #f0fdf4 0%, white 10%);
        }

        /* أنماط للإحصائيات */
        .stats-card.danger {
            border-top: 4px solid #ef4444;
        }

        .stats-card.danger .stats-icon {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .progress-ring {
            transform: rotate(-90deg);
        }

        .progress-ring-circle {
            transition: stroke-dashoffset 0.35s;
            transform-origin: 50% 50%;
        }

        /* Priority Colors */
        .priority-high { color: #dc2626; font-weight: bold; }
        .priority-medium { color: #d97706; font-weight: bold; }
        .priority-low { color: #059669; font-weight: bold; }

        /* تحسينات responsive */
        @media (max-width: 768px) {
            .card-body-modern {
                padding: 1rem;
            }

            .stats-card {
                margin-bottom: 1rem;
            }

            .btn-modern {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>


    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="slide-in">
                        <i class="fas fa-building me-3"></i>
                        تقرير تتبع الوحدات
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">التقارير</a>
                            </li>
                            <li class="breadcrumb-item active">تتبع الوحدات</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-building"></i>
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
                <form id="unitsForm">
                    <div class="row g-4">
                        <!-- First Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-layer-group me-2"></i>نوع الوحدة</label>
                            <select name="unit_type_id" id="unit_type_id" class="form-control select2">
                                <option value="">جميع الأنواع</option>
                                @foreach($unitTypes as $unitType)
                                    <option value="{{ $unitType->id }}">{{ $unitType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-tasks me-2"></i>حالة الوحدة</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="">جميع الحالات</option>
                                @foreach($statuses as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-sort-numeric-up me-2"></i>الأولوية</label>
                            <select name="priority" id="priority" class="form-control select2">
                                <option value="">جميع الأولويات</option>
                                @foreach($priorities as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-sort me-2"></i>ترتيب حسب</label>
                            <select name="sort_by" id="sort_by" class="form-control select2">
                                <option value="">اختر الترتيب</option>
                                <option value="name_asc">الاسم أبجدياً</option>
                                <option value="name_desc">الاسم عكس أبجدياً</option>
                                <option value="priority_desc">الأولوية الأعلى</option>
                                <option value="priority_asc">الأولوية الأقل</option>
                                <option value="created_at_desc">تاريخ الإنشاء الأحدث</option>
                                <option value="created_at_asc">تاريخ الإنشاء الأقدم</option>
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-layer-group me-2"></i>تجميع حسب</label>
                            <select name="group_by" id="group_by" class="form-control select2">
                                <option value="">بدون تجميع</option>
                                <option value="unit_type">نوع الوحدة</option>
                                <option value="status">الحالة</option>
                                <option value="priority">الأولوية</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-search me-2"></i>البحث في الوحدات</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="ابحث بالاسم أو الوصف...">
                        </div>

                        <!-- Options Row -->
                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-active-only" name="show_active_only" class="form-check-input">
                                <label for="show-active-only" class="form-check-label">
                                    <i class="fas fa-play me-2"></i>الوحدات النشطة فقط
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-totals" name="show_totals" class="form-check-input" checked>
                                <label for="show-totals" class="form-check-label">
                                    <i class="fas fa-calculator me-2"></i>إظهار الإحصائيات
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-lg-12 align-self-end">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn-modern btn-primary-modern" id="filterBtn">
                                    <i class="fas fa-search"></i>
                                    تصفية التقرير
                                </button>
                                <button type="button" class="btn-modern btn-outline-modern" id="resetBtn">
                                    <i class="fas fa-refresh"></i>
                                    إعادة تعيين
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
                    </div>

                    <div class="d-flex align-items-center">
                        <span class="me-2 text-muted">تاريخ ووقت الطباعة:</span>
                        <span class="fw-bold">{{ now()->format('H:i d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4 fade-in" id="totalsSection">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card primary">
                    <div class="stats-icon primary">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stats-value" id="totalUnits">0</div>
                    <div class="stats-label">إجمالي الوحدات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-value" id="activeUnits">0</div>
                    <div class="stats-label">وحدات نشطة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="stats-value" id="maintenanceUnits">0</div>
                    <div class="stats-label">قيد الصيانة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="stats-value" id="totalUnitTypes">0</div>
                    <div class="stats-label">أنواع الوحدات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stats-value" id="highPriorityUnits">0</div>
                    <div class="stats-label">أولوية عالية</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-pause-circle"></i>
                    </div>
                    <div class="stats-value" id="inactiveUnits">0</div>
                    <div class="stats-label">وحدات غير نشطة</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    الرسم البياني للوحدات
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="unitsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="card-modern fade-in" id="reportContainer" style="position: relative;">
            <!-- Loading Overlay -->
            <div class="loading-overlay">
                <div class="spinner"></div>
            </div>

            <div class="card-header-modern">
                <h5 class="mb-0" id="reportTitle">
                    <i class="fas fa-table me-2"></i>
                    تقرير تتبع الوحدات - مؤسسة أعمال خاصة للتجارة
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-building me-2"></i>اسم الوحدة</th>
                                <th><i class="fas fa-layer-group me-2"></i>نوع الوحدة</th>
                                <th><i class="fas fa-tasks me-2"></i>الحالة</th>
                                <th><i class="fas fa-sort-numeric-up me-2"></i>الأولوية</th>
                                <th><i class="fas fa-sticky-note me-2"></i>الوصف</th>
                                <th><i class="fas fa-calendar me-2"></i>تاريخ الإنشاء</th>
                                <th><i class="fas fa-calendar-alt me-2"></i>آخر تحديث</th>
                            </tr>
                        </thead>
                        <tbody id="unitsTableBody">
                            <!-- سيتم تحديث البيانات هنا عبر AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        let unitsChart;

        $(document).ready(function() {
            // تهيئة Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                language: {
                    noResults: function() {
                        return "لا توجد نتائج";
                    },
                    searching: function() {
                        return "جاري البحث...";
                    }
                },
                allowClear: true,
                width: '100%',
                dropdownParent: $('body'),
                minimumResultsForSearch: 0,
                placeholder: function() {
                    return $(this).data('placeholder') || 'اختر...';
                }
            });

            // تحميل البيانات الأولية
            loadUnitsData();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // التعامل مع تصفية التقرير
            $('#filterBtn').click(function() {
                $(this).addClass('loading');
                loadUnitsData();
            });

            // إعادة تعيين الفلاتر
            $('#resetBtn').click(function() {
                $('#unitsForm')[0].reset();
                $('.select2').val(null).trigger('change');
                loadUnitsData();
            });

            // التعامل مع تصدير إكسل
            $('#exportExcel').click(function() {
                exportToExcel();
            });

            // التعامل مع الطباعة
            $('#printBtn').click(function() {
                window.print();
            });

            // التعامل مع تصدير PDF
            $('#exportPDF').click(function() {
                exportToPDF();
            });

            // تحديث البيانات عند تغيير أي فلتر
            $('.select2, #search').on('change keyup', function() {
                clearTimeout($(this).data('timeout'));
                $(this).data('timeout', setTimeout(function() {
                    loadUnitsData();
                }, 500));
            });

            $('input[type="checkbox"]').on('change', function() {
                loadUnitsData();
            });

            // دالة تحميل بيانات الوحدات
            function loadUnitsData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#unitsForm').serialize();

                $.ajax({
                    url: '{{ route('unit-track.unitsReportAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateUnitsDisplay(response);
                        $('.loading-overlay').fadeOut();
                        $('#filterBtn').prop('disabled', false);
                        $('#filterBtn').removeClass('loading');
                    },
                    error: function(xhr, status, error) {
                        console.error('خطأ في تحميل البيانات:', error);
                        $('.loading-overlay').fadeOut();
                        $('#filterBtn').prop('disabled', false);
                        $('#filterBtn').removeClass('loading');
                        showAlert('حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.', 'danger');
                    }
                });
            }

            // دالة تحديث عرض الوحدات
            function updateUnitsDisplay(data) {
                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#totalUnits', 0, data.totals.total_units, 1000);
                animateValue('#activeUnits', 0, data.totals.active_units, 1000);
                animateValue('#maintenanceUnits', 0, data.totals.maintenance_units, 1000);
                animateValue('#totalUnitTypes', 0, data.totals.total_unit_types, 1000);
                animateValue('#highPriorityUnits', 0, data.totals.high_priority_units, 1000);
                animateValue('#inactiveUnits', 0, data.totals.inactive_units, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير تتبع الوحدات - ${data.group_by ? 'تجميع حسب ' + data.group_by : 'جميع الوحدات'}
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.units, data.totals);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('unitsChart').getContext('2d');

                if (unitsChart) {
                    unitsChart.destroy();
                }

                unitsChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.labels || [],
                        datasets: [{
                            data: chartData.values || [],
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(139, 92, 246, 0.8)'
                            ],
                            borderColor: [
                                'rgba(16, 185, 129, 1)',
                                'rgba(239, 68, 68, 1)',
                                'rgba(245, 158, 11, 1)',
                                'rgba(59, 130, 246, 1)',
                                'rgba(139, 92, 246, 1)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        family: 'Cairo',
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.2)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // دالة تحديث محتوى الجدول
            function updateTableBody(units, totals) {
                let tableHtml = '';

                if (units && units.length > 0) {
                    units.forEach((unit, index) => {
                        const statusClass = getStatusClass(unit.status);
                        const rowClass = getPriorityRowClass(unit.priority);
                        const priorityClass = getPriorityClass(unit.priority);

                        tableHtml += `<tr class="${rowClass}">`;
                        tableHtml += `<td>${index + 1}</td>`;
                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">${unit.name}</div>
                                </div>
                            </div>
                        </td>`;
                        tableHtml += `<td>${unit.unit_type ? `<span class="badge bg-info">${unit.unit_type}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td><span class="badge ${statusClass}">${unit.status_label}</span></td>`;
                        tableHtml += `<td><span class="${priorityClass}">${unit.priority_label}</span></td>`;
                        tableHtml += `<td>${unit.description ? `<small>${unit.description.substring(0, 50)}${unit.description.length > 50 ? '...' : ''}</small>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${unit.created_at || '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${unit.updated_at || '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `</tr>`;
                    });

                    // إضافة صف الإجمالي العام
                    if ($('#show-totals').is(':checked')) {
                        tableHtml += `
                            <tr class="table-grand-total">
                                <td colspan="6">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    <strong>المجموع الكلي</strong>
                                </td>
                                <td class="fw-bold">${totals.total_units} وحدة</td>
                                <td class="text-muted">-</td>
                            </tr>
                        `;
                    }
                } else {
                    tableHtml = `
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">لا توجد وحدات مطابقة للفلاتر المحددة</p>
                            </td>
                        </tr>
                    `;
                }

                $('#unitsTableBody').html(tableHtml);
            }

            // دالة الحصول على فئة CSS للحالة
            function getStatusClass(status) {
                const statusClasses = {
                    'active': 'status-active',
                    'inactive': 'status-inactive',
                    'maintenance': 'status-maintenance'
                };
                return statusClasses[status] || 'bg-secondary';
            }

            // دالة الحصول على فئة CSS للأولوية
            function getPriorityClass(priority) {
                const priorityClasses = {
                    'high': 'priority-high',
                    'medium': 'priority-medium',
                    'low': 'priority-low'
                };
                return priorityClasses[priority] || 'text-muted';
            }

            // دالة الحصول على فئة CSS لصف الأولوية
            function getPriorityRowClass(priority) {
                const rowClasses = {
                    'high': 'high-priority',
                    'medium': 'medium-priority',
                    'low': 'low-priority'
                };
                return rowClasses[priority] || '';
            }

            // دالة تنسيق الأرقام
            function formatNumber(number) {
                return parseFloat(number).toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
            }

            // دالة الرسوم المتحركة للأرقام
            function animateValue(element, start, end, duration) {
                const obj = $(element);
                const range = end - start;
                const increment = end > start ? 1 : -1;
                const stepTime = Math.abs(Math.floor(duration / range));
                const timer = setInterval(function() {
                    start += increment;
                    obj.text(formatNumber(start));
                    if (start == end) {
                        clearInterval(timer);
                    }
                }, stepTime);
            }

            // دالة تصدير إكسل
            function exportToExcel() {
                showAlert('جاري تصدير الملف...', 'info');

                const table = document.querySelector('#reportContainer table');
                const wb = XLSX.utils.table_to_book(table, {
                    raw: false,
                    cellDates: true
                });

                const today = new Date();
                const fileName = `تقرير_تتبع_الوحدات_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

                XLSX.writeFile(wb, fileName);
                showAlert('تم تصدير الملف بنجاح!', 'success');
            }

            // دالة تصدير PDF
            function exportToPDF() {
                showAlert('جاري تصدير PDF...', 'info');

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('l', 'mm', 'a4'); // landscape orientation

                // إضافة العنوان
                doc.setFontSize(16);
                doc.text('تقرير تتبع الوحدات', 148, 20, { align: 'center' });

                // إضافة التاريخ
                doc.setFontSize(10);
                doc.text(`تاريخ الطباعة: ${new Date().toLocaleDateString('ar-SA')}`, 20, 30);

                // تصدير الجدول
                doc.autoTable({
                    html: '#reportContainer table',
                    startY: 40,
                    styles: {
                        fontSize: 8,
                        cellPadding: 2
                    },
                    headStyles: {
                        fillColor: [102, 126, 234],
                        textColor: 255
                    },
                    columnStyles: {
                        0: { cellWidth: 15 },  // #
                        1: { cellWidth: 40 },  // اسم الوحدة
                        2: { cellWidth: 30 },  // نوع الوحدة
                        3: { cellWidth: 25 },  // الحالة
                        4: { cellWidth: 25 },  // الأولوية
                        5: { cellWidth: 50 },  // الوصف
                        6: { cellWidth: 30 },  // تاريخ الإنشاء
                        7: { cellWidth: 30 }   // آخر تحديث
                    }
                });

                const fileName = `تقرير_تتبع_الوحدات_${new Date().toISOString().split('T')[0]}.pdf`;
                doc.save(fileName);
                showAlert('تم تصدير PDF بنجاح!', 'success');
            }

            // دالة عرض التنبيهات
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

                // إزالة التنبيه تلقائياً بعد 3 ثوان
                setTimeout(() => {
                    $('.alert').alert('close');
                }, 3000);
            }

            // تأثيرات hover للكروت
            $('.stats-card').hover(
                function() {
                    $(this).css('transform', 'translateY(-8px) scale(1.02)');
                },
                function() {
                    $(this).css('transform', 'translateY(0) scale(1)');
                }
            );

            // تأثيرات hover للأزرار
            $('.btn-modern').hover(
                function() {
                    if (!$(this).hasClass('active')) {
                        $(this).css('transform', 'translateY(-2px)');
                    }
                },
                function() {
                    if (!$(this).hasClass('active')) {
                        $(this).css('transform', 'translateY(0)');
                    }
                }
            );

            // تحسين UX - تلميحات الأدوات
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // دوال JavaScript عامة
        function viewUnitDetails(unitId) {
            console.log(`عرض تفاصيل الوحدة: ${unitId}`);
        }

        function updateUnitStatus(unitId, newStatus) {
            console.log(`تحديث حالة الوحدة ${unitId} إلى: ${newStatus}`);
        }

        function updateUnitPriority(unitId, newPriority) {
            console.log(`تحديث أولوية الوحدة ${unitId} إلى: ${newPriority}`);
        }
    </script>
@endsection