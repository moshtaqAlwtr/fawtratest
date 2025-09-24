@extends('master')

@section('title')
    الحركة التفصيلية للمخزون
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
                        <i class="fas fa-list-alt me-3"></i>
                        تقرير الحركة التفصيلية للمخزون
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">الحركة التفصيلية للمخزون</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-list-alt"></i>
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
                <form id="movementForm">
                    <div class="row g-4">
                        <!-- First Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-tags me-2"></i>التصنيف</label>
                            <select name="category" id="category" class="form-control select2">
                                <option value="">جميع التصنيفات</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-file-invoice me-2"></i>النوع</label>
                            <select name="type" id="type" class="form-control select2">
                                <option value="">جميع الأنواع</option>
                                <option value="sale">فاتورة بيع</option>
                                <option value="purchase">فاتورة شراء</option>
                                <option value="purchase_return">مرتجع شراء</option>
                                <option value="sale_return">مرتجع بيع</option>
                                <option value="transfer">نقل</option>
                                <option value="manual">يدوي</option>
                                <option value="in">إدخال</option>
                                <option value="out">إخراج</option>
                                <option value="adjustment">تعديل جرد</option>
                                <option value="manufacturing">تصنيع</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>من تاريخ</label>
                            <input type="date" name="start_date" id="start_date" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>إلى تاريخ</label>
                            <input type="date" name="end_date" id="end_date" class="form-control">
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-star me-2"></i>العلامة التجارية</label>
                            <select name="brand" id="brand" class="form-control select2">
                                <option value="">جميع العلامات</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand }}">{{ $brand }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-warehouse me-2"></i>المستودع</label>
                            <select name="warehouse" id="warehouse" class="form-control select2">
                                <option value="">جميع المستودعات</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-sort me-2"></i>ترتيب حسب</label>
                            <select name="sort_by" id="sort_by" class="form-control select2">
                                <option value="">اختر الترتيب</option>
                                <option value="date_desc">التاريخ (الأحدث أولاً)</option>
                                <option value="date_asc">التاريخ (الأقدم أولاً)</option>
                                <option value="quantity_desc">الكمية تنازلي</option>
                                <option value="quantity_asc">الكمية تصاعدي</option>
                                <option value="product_name">اسم المنتج</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-layer-group me-2"></i>تجميع حسب</label>
                            <select name="group_by" id="group_by" class="form-control select2">
                                <option value="">بدون تجميع</option>
                                <option value="product">المنتج</option>
                                <option value="category">التصنيف</option>
                                <option value="brand">العلامة التجارية</option>
                                <option value="warehouse">المستودع</option>
                                <option value="type">النوع</option>
                                <option value="date">التاريخ</option>
                            </select>
                        </div>

                        <!-- Third Row - Search and Options -->
                        <div class="col-lg-6 col-md-12">
                            <label class="form-label-modern"><i class="fas fa-search me-2"></i>البحث في المنتجات</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="ابحث بالاسم أو الكود أو الملاحظات...">
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-incoming" name="show_incoming" class="form-check-input" checked>
                                <label for="show-incoming" class="form-check-label">
                                    <i class="fas fa-arrow-down me-2 text-success"></i>إظهار الوارد
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-outgoing" name="show_outgoing" class="form-check-input" checked>
                                <label for="show-outgoing" class="form-check-label">
                                    <i class="fas fa-arrow-up me-2 text-danger"></i>إظهار المنصرف
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-lg-12 align-self-end">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn-modern btn-primary-modern" id="filterBtn">
                                    <i class="fas fa-search"></i>
                                    عرض التقرير
                                </button>
                                <button type="button" class="btn-modern btn-outline-modern" id="resetBtn">
                                    <i class="fas fa-refresh"></i>
                                    إلغاء الفلترة
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
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="stats-value" id="totalMovements">0</div>
                    <div class="stats-label">إجمالي الحركات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="stats-value" id="totalIncoming">0</div>
                    <div class="stats-label">إجمالي الوارد</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon danger">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="stats-value" id="totalOutgoing">0</div>
                    <div class="stats-label">إجمالي المنصرف</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="stats-value" id="totalProducts">0</div>
                    <div class="stats-label">عدد المنتجات</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    الرسم البياني لحركة المخزون
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="movementChart"></canvas>
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
                    الحركة التفصيلية للمخزون - مؤسسة أعمال خاصة للتجارة
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-clock me-2"></i>الوقت والتاريخ</th>
                                <th><i class="fas fa-tag me-2"></i>النوع</th>
                                <th><i class="fas fa-barcode me-2"></i>كود المنتج</th>
                                <th><i class="fas fa-cube me-2"></i>اسم المنتج</th>
                                <th><i class="fas fa-arrow-down me-2 text-success"></i>الوارد</th>
                                <th><i class="fas fa-arrow-up me-2 text-danger"></i>المنصرف</th>
                                <th><i class="fas fa-warehouse me-2"></i>المستودع</th>
                                <th><i class="fas fa-sticky-note me-2"></i>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody id="movementTableBody">
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
        let movementChart;

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
            loadMovementData();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // التعامل مع تصفية التقرير
            $('#filterBtn').click(function() {
                $(this).addClass('loading');
                loadMovementData();
            });

            // إعادة تعيين الفلاتر
            $('#resetBtn').click(function() {
                $('#movementForm')[0].reset();
                $('.select2').val(null).trigger('change');
                $('#show-incoming, #show-outgoing').prop('checked', true);
                loadMovementData();
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
            $('.select2, input[type="date"], #search').on('change keyup', function() {
                clearTimeout($(this).data('timeout'));
                $(this).data('timeout', setTimeout(function() {
                    loadMovementData();
                }, 500));
            });

            $('#show-incoming, #show-outgoing').on('change', function() {
                loadMovementData();
            });

            // دالة تحميل بيانات الحركة التفصيلية
            function loadMovementData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#movementForm').serialize();

                $.ajax({
                    url: '{{ route('StorHouseReport.detailedMovementInventoryAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateMovementDisplay(response);
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

            // دالة تحديث عرض الحركة التفصيلية
            function updateMovementDisplay(data) {
                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#totalMovements', 0, data.totals.total_movements, 1000);
                animateValue('#totalIncoming', 0, data.totals.total_incoming, 1000);
                animateValue('#totalOutgoing', 0, data.totals.total_outgoing, 1000);
                animateValue('#totalProducts', 0, data.totals.total_products, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    الحركة التفصيلية للمخزون - ${data.group_by ? 'تجميع حسب ' + data.group_by : 'جميع الحركات'}
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.movements, data.totals);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('movementChart').getContext('2d');

                if (movementChart) {
                    movementChart.destroy();
                }

                movementChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels || [],
                        datasets: [
                            {
                                label: 'الوارد',
                                data: chartData.incoming || [],
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 6,
                                pointHoverRadius: 8,
                            },
                            {
                                label: 'المنصرف',
                                data: chartData.outgoing || [],
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: 'rgba(239, 68, 68, 1)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 6,
                                pointHoverRadius: 8,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
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
                                        return context.dataset.label + ': ' + formatNumber(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return formatNumber(value);
                                    },
                                    font: {
                                        family: 'Cairo'
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 0,
                                    font: {
                                        family: 'Cairo'
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // دالة تحديث محتوى الجدول
            function updateTableBody(movements, totals) {
                let tableHtml = '';

                if (movements && movements.length > 0) {
                    let grandTotals = {
                        incoming: 0,
                        outgoing: 0
                    };

                    movements.forEach((movement, index) => {
                        const isIncoming = movement.direction === 'in' || movement.quantity > 0;
                        const quantity = Math.abs(movement.quantity || 0);

                        if (isIncoming) {
                            grandTotals.incoming += quantity;
                        } else {
                            grandTotals.outgoing += quantity;
                        }

                        const typeClass = getTypeClass(movement.type);
                        const directionIcon = isIncoming ?
                            '<i class="fas fa-arrow-down text-success"></i>' :
                            '<i class="fas fa-arrow-up text-danger"></i>';

                        tableHtml += `<tr>`;
                        tableHtml += `<td>${index + 1}</td>`;
                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock me-2 text-muted"></i>
                                <div>
                                    <div class="fw-bold">${formatDateTime(movement.created_at)}</div>
                                    <small class="text-muted">${formatDate(movement.created_at)}</small>
                                </div>
                            </div>
                        </td>`;
                        tableHtml += `<td>
                            <span class="badge ${typeClass}">
                                ${directionIcon}
                                ${getTypeLabel(movement.type)}
                            </span>
                        </td>`;
                        tableHtml += `<td><span class="badge bg-secondary">${movement.product_code || 'N/A'}</span></td>`;
                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-cube"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">${movement.product_name || 'N/A'}</div>
                                </div>
                            </div>
                        </td>`;
                        tableHtml += `<td class="text-center">
                            ${isIncoming ?
                                `<span class="fw-bold text-success">${formatNumber(quantity)}</span>` :
                                '<span class="text-muted">-</span>'
                            }
                        </td>`;
                        tableHtml += `<td class="text-center">
                            ${!isIncoming ?
                                `<span class="fw-bold text-danger">${formatNumber(quantity)}</span>` :
                                '<span class="text-muted">-</span>'
                            }
                        </td>`;
                        tableHtml += `<td>
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-warehouse me-1"></i>
                                ${movement.warehouse_name || 'غير محدد'}
                            </span>
                        </td>`;
                        tableHtml += `<td>
                            <div class="notes-cell">
                                ${movement.notes ?
                                    `<i class="fas fa-sticky-note me-1 text-info"></i>${movement.notes}` :
                                    '<span class="text-muted">لا توجد ملاحظات</span>'
                                }
                            </div>
                        </td>`;
                        tableHtml += `</tr>`;
                    });

                    // إضافة صف الإجمالي العام
                    tableHtml += `
                        <tr class="table-grand-total">
                            <td colspan="5" class="fw-bold">
                                <i class="fas fa-chart-bar me-2"></i>
                                المجموع الكلي
                            </td>
                            <td class="fw-bold text-center text-success">${formatNumber(grandTotals.incoming)}</td>
                            <td class="fw-bold text-center text-danger">${formatNumber(grandTotals.outgoing)}</td>
                            <td colspan="2" class="text-muted">-</td>
                        </tr>
                    `;
                } else {
                    tableHtml = `
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">لا توجد حركات مطابقة للفلاتر المحددة</p>
                            </td>
                        </tr>
                    `;
                }

                $('#movementTableBody').html(tableHtml);
            }

            // دالة تنسيق التاريخ والوقت
            function formatDateTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString('ar-SA', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('ar-SA');
            }

            // دالة الحصول على فئة النوع
            function getTypeClass(type) {
                const typeClasses = {
                    'sale': 'bg-danger',
                    'purchase': 'bg-success',
                    'purchase_return': 'bg-warning',
                    'sale_return': 'bg-info',
                    'transfer': 'bg-primary',
                    'manual': 'bg-secondary',
                    'in': 'bg-success',
                    'out': 'bg-danger',
                    'adjustment': 'bg-warning',
                    'manufacturing': 'bg-info'
                };
                return typeClasses[type] || 'bg-secondary';
            }

            // دالة الحصول على تسمية النوع
            function getTypeLabel(type) {
                const typeLabels = {
                    'sale': 'فاتورة بيع',
                    'purchase': 'فاتورة شراء',
                    'purchase_return': 'مرتجع شراء',
                    'sale_return': 'مرتجع بيع',
                    'transfer': 'نقل',
                    'manual': 'يدوي',
                    'in': 'إدخال',
                    'out': 'إخراج',
                    'adjustment': 'تعديل جرد',
                    'manufacturing': 'تصنيع'
                };
                return typeLabels[type] || type;
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
                const fileName = `الحركة_التفصيلية_للمخزون_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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
                doc.text('الحركة التفصيلية للمخزون', 150, 20, { align: 'center' });

                // إضافة التاريخ
                doc.setFontSize(10);
                doc.text(`تاريخ الطباعة: ${new Date().toLocaleDateString('ar-SA')}`, 20, 30);

                // تصدير الجدول
                doc.autoTable({
                    html: '#reportContainer table',
                    startY: 40,
                    styles: {
                        fontSize: 7,
                        cellPadding: 1
                    },
                    headStyles: {
                        fillColor: [59, 130, 246],
                        textColor: 255
                    },
                    columnStyles: {
                        0: { cellWidth: 15 }, // #
                        1: { cellWidth: 35 }, // الوقت والتاريخ
                        2: { cellWidth: 25 }, // النوع
                        3: { cellWidth: 25 }, // كود المنتج
                        4: { cellWidth: 40 }, // اسم المنتج
                        5: { cellWidth: 20 }, // الوارد
                        6: { cellWidth: 20 }, // المنصرف
                        7: { cellWidth: 30 }, // المستودع
                        8: { cellWidth: 50 }  // ملاحظات
                    }
                });

                const fileName = `الحركة_التفصيلية_للمخزون_${new Date().toISOString().split('T')[0]}.pdf`;
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
    </script>

    <style>
        /* إضافات CSS خاصة بالجدول */
        .notes-cell {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .notes-cell:hover {
            overflow: visible;
            white-space: normal;
            background-color: #f8f9fa;
            border-radius: 4px;
            padding: 4px;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }

        .table-grand-total {
            background-color: #f8f9fa !important;
            border-top: 2px solid #dee2e6 !important;
        }

        .table-grand-total td {
            border-top: 2px solid #dee2e6 !important;
            font-weight: bold !important;
        }

        /* تحسينات للجوال */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.85rem;
            }

            .avatar-sm {
                width: 24px;
                height: 24px;
                font-size: 0.6rem;
            }

            .notes-cell {
                max-width: 120px;
            }
        }
    </style>
@endsection