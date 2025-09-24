@extends('master')

@section('title')
    تقرير تقييم المخزون
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
                        <i class="fas fa-dollar-sign me-3"></i>
                        تقرير تقييم المخزون
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير تقييم المخزون</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-dollar-sign"></i>
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
                <form id="valueForm">
                    <div class="row g-4">
                        <!-- First Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-truck me-2"></i>المورد</label>
                            <select name="supplier" id="supplier" class="form-control select2">
                                <option value="">جميع الموردين</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>

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

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>من تاريخ</label>
                            <input type="date" name="start_date" id="start_date" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>إلى تاريخ</label>
                            <input type="date" name="end_date" id="end_date" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-sort me-2"></i>ترتيب حسب</label>
                            <select name="sort_by" id="sort_by" class="form-control select2">
                                <option value="">اختر الترتيب</option>
                                <option value="value_desc">القيمة الإجمالية تنازلي</option>
                                <option value="value_asc">القيمة الإجمالية تصاعدي</option>
                                <option value="profit_desc">الربح المتوقع تنازلي</option>
                                <option value="profit_asc">الربح المتوقع تصاعدي</option>
                                <option value="quantity_desc">الكمية تنازلي</option>
                                <option value="quantity_asc">الكمية تصاعدي</option>
                                <option value="name_asc">الاسم أبجدياً</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-layer-group me-2"></i>تجميع حسب</label>
                            <select name="group_by" id="group_by" class="form-control select2">
                                <option value="">بدون تجميع</option>
                                <option value="category">التصنيف</option>
                                <option value="brand">العلامة التجارية</option>
                                <option value="supplier">المورد</option>
                                <option value="warehouse">المستودع</option>
                            </select>
                        </div>

                        <!-- Third Row - Search and Options -->
                        <div class="col-lg-6 col-md-12">
                            <label class="form-label-modern"><i class="fas fa-search me-2"></i>البحث في المنتجات</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="ابحث بالاسم أو الكود...">
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="hide-zero-value" name="hide_zero_value" class="form-check-input">
                                <label for="hide-zero-value" class="form-check-label">
                                    <i class="fas fa-eye-slash me-2"></i>إخفاء المنتجات بدون قيمة
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-profit-only" name="show_profit_only" class="form-check-input">
                                <label for="show-profit-only" class="form-check-label">
                                    <i class="fas fa-chart-line me-2"></i>إظهار المربحة فقط
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
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="stats-value" id="totalProducts">0</div>
                    <div class="stats-label">إجمالي المنتجات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stats-value" id="totalPurchaseValue">0</div>
                    <div class="stats-label">إجمالي قيمة الشراء</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="stats-value" id="totalSaleValue">0</div>
                    <div class="stats-label">إجمالي قيمة البيع</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stats-value" id="totalProfit">0</div>
                    <div class="stats-label">إجمالي الربح المتوقع</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني لتقييم المخزون
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="valueChart"></canvas>
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
                    تقرير تقييم المخزون - مؤسسة أعمال خاصة للتجارة
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-barcode me-2"></i>كود المنتج</th>
                                <th><i class="fas fa-cube me-2"></i>اسم المنتج</th>
                                <th><i class="fas fa-tags me-2"></i>التصنيف</th>
                                <th><i class="fas fa-star me-2"></i>العلامة التجارية</th>
                                <th><i class="fas fa-warehouse me-2"></i>المستودع</th>
                                <th><i class="fas fa-boxes me-2"></i>الكمية</th>
                                <th><i class="fas fa-dollar-sign me-2"></i>سعر الشراء</th>
                                <th><i class="fas fa-tag me-2"></i>سعر البيع</th>
                                <th><i class="fas fa-shopping-cart me-2"></i>قيمة الشراء</th>
                                <th><i class="fas fa-money-bill me-2"></i>قيمة البيع</th>
                                <th><i class="fas fa-chart-line me-2"></i>الربح المتوقع</th>
                            </tr>
                        </thead>
                        <tbody id="valueTableBody">
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
        let valueChart;

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
            loadValueData();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // التعامل مع تصفية التقرير
            $('#filterBtn').click(function() {
                $(this).addClass('loading');
                loadValueData();
            });

            // إعادة تعيين الفلاتر
            $('#resetBtn').click(function() {
                $('#valueForm')[0].reset();
                $('.select2').val(null).trigger('change');
                $('#hide-zero-value, #show-profit-only').prop('checked', false);
                loadValueData();
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
                    loadValueData();
                }, 500));
            });

            $('#hide-zero-value, #show-profit-only').on('change', function() {
                loadValueData();
            });

            // دالة تحميل بيانات تقييم المخزون
            function loadValueData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#valueForm').serialize();

                $.ajax({
                    url: '{{ route('StorHouseReport.valueInventoryAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateValueDisplay(response);
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

            // دالة تحديث عرض تقييم المخزون
            function updateValueDisplay(data) {
                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#totalProducts', 0, data.totals.total_products, 1000);
                animateValue('#totalPurchaseValue', 0, data.totals.total_purchase_value, 1000, true);
                animateValue('#totalSaleValue', 0, data.totals.total_sale_value, 1000, true);
                animateValue('#totalProfit', 0, data.totals.total_profit, 1000, true);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير تقييم المخزون - ${data.group_by ? 'تجميع حسب ' + data.group_by : 'جميع المنتجات'}
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.products, data.totals);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('valueChart').getContext('2d');

                if (valueChart) {
                    valueChart.destroy();
                }

                valueChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels || [],
                        datasets: [
                            {
                                label: 'قيمة الشراء',
                                data: chartData.purchase_values || [],
                                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                            },
                            {
                                label: 'قيمة البيع',
                                data: chartData.sale_values || [],
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                            },
                            {
                                label: 'الربح المتوقع',
                                data: chartData.profit_values || [],
                                backgroundColor: 'rgba(168, 85, 247, 0.7)',
                                borderColor: 'rgba(168, 85, 247, 1)',
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
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
                                        return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return formatCurrency(value);
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
            function updateTableBody(products, totals) {
                let tableHtml = '';

                if (products && products.length > 0) {
                    let grandTotals = {
                        quantity: 0,
                        purchase_value: 0,
                        sale_value: 0,
                        profit: 0
                    };

                    products.forEach((product, index) => {
                        const quantity = parseFloat(product.total_quantity || 0);
                        const purchasePrice = parseFloat(product.purchase_price || 0);
                        const salePrice = parseFloat(product.sale_price || 0);
                        const purchaseValue = parseFloat(product.total_purchase_value || 0);
                        const saleValue = parseFloat(product.total_sale_value || 0);
                        const profit = parseFloat(product.expected_profit || 0);

                        grandTotals.quantity += quantity;
                        grandTotals.purchase_value += purchaseValue;
                        grandTotals.sale_value += saleValue;
                        grandTotals.profit += profit;

                        const profitClass = profit > 0 ? 'text-success' : profit < 0 ? 'text-danger' : 'text-warning';
                        const quantityClass = quantity > 0 ? 'text-success' : 'text-danger';

                        tableHtml += `<tr>`;
                        tableHtml += `<td>${index + 1}</td>`;
                        tableHtml += `<td><span class="badge bg-secondary">${product.code || product.id}</span></td>`;
                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-cube"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">${product.name}</div>
                                </div>
                            </div>
                        </td>`;
                        tableHtml += `<td>${product.category ? `<span class="badge bg-info">${product.category}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${product.brand ? `<span class="badge bg-success">${product.brand}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${product.warehouse ? `<span class="badge bg-warning">${product.warehouse}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td class="text-center"><span class="${quantityClass} fw-bold">${formatNumber(quantity)}</span></td>`;
                        tableHtml += `<td class="text-center">${formatCurrency(purchasePrice)}</td>`;
                        tableHtml += `<td class="text-center">${formatCurrency(salePrice)}</td>`;
                        tableHtml += `<td class="text-center fw-bold text-success">${formatCurrency(purchaseValue)}</td>`;
                        tableHtml += `<td class="text-center fw-bold text-primary">${formatCurrency(saleValue)}</td>`;
                        tableHtml += `<td class="text-center fw-bold ${profitClass}">${formatCurrency(profit)}</td>`;
                        tableHtml += `</tr>`;
                    });

                    // إضافة صف الإجمالي العام
                    tableHtml += `
                        <tr class="table-grand-total">
                            <td colspan="6" class="fw-bold">
                                <i class="fas fa-chart-bar me-2"></i>
                                المجموع الكلي
                            </td>
                            <td class="fw-bold text-center">${formatNumber(grandTotals.quantity)}</td>
                            <td class="text-muted text-center">-</td>
                            <td class="text-muted text-center">-</td>
                            <td class="fw-bold text-center text-success">${formatCurrency(grandTotals.purchase_value)}</td>
                            <td class="fw-bold text-center text-primary">${formatCurrency(grandTotals.sale_value)}</td>
                            <td class="fw-bold text-center ${grandTotals.profit > 0 ? 'text-success' : grandTotals.profit < 0 ? 'text-danger' : 'text-warning'}">${formatCurrency(grandTotals.profit)}</td>
                        </tr>
                    `;
                } else {
                    tableHtml = `
                        <tr>
                            <td colspan="12" class="text-center py-4">
                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">لا توجد منتجات مطابقة للفلاتر المحددة</p>
                            </td>
                        </tr>
                    `;
                }

                $('#valueTableBody').html(tableHtml);
            }

            // دالة تنسيق العملة
            function formatCurrency(number) {
                return parseFloat(number).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' ر.س';
            }

            // دالة تنسيق الأرقام
            function formatNumber(number) {
                return parseFloat(number).toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
            }

            // دالة الرسوم المتحركة للأرقام
            function animateValue(element, start, end, duration, isCurrency = false) {
                const obj = $(element);
                const range = end - start;
                const increment = end > start ? 1 : -1;
                const stepTime = Math.abs(Math.floor(duration / range));
                const timer = setInterval(function() {
                    start += increment;
                    if (isCurrency) {
                        obj.text(formatCurrency(start));
                    } else {
                        obj.text(formatNumber(start));
                    }
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
                const fileName = `تقرير_تقييم_المخزون_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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
                doc.text('تقرير تقييم المخزون', 150, 20, { align: 'center' });

                // إضافة التاريخ
                doc.setFontSize(10);
                doc.text(`تاريخ الطباعة: ${new Date().toLocaleDateString('ar-SA')}`, 20, 30);

                // تصدير الجدول
                doc.autoTable({
                    html: '#reportContainer table',
                    startY: 40,
                    styles: {
                        fontSize: 6,
                        cellPadding: 1
                    },
                    headStyles: {
                        fillColor: [59, 130, 246],
                        textColor: 255
                    },
                    columnStyles: {
                        0: { cellWidth: 10 }, // #
                        1: { cellWidth: 20 }, // كود المنتج
                        2: { cellWidth: 35 }, // اسم المنتج
                        3: { cellWidth: 20 }, // التصنيف
                        4: { cellWidth: 20 }, // العلامة التجارية
                        5: { cellWidth: 20 }, // المستودع
                        6: { cellWidth: 15 }, // الكمية
                        7: { cellWidth: 20 }, // سعر الشراء
                        8: { cellWidth: 20 }, // سعر البيع
                        9: { cellWidth: 25 }, // قيمة الشراء
                        10: { cellWidth: 25 }, // قيمة البيع
                        11: { cellWidth: 25 }  // الربح المتوقع
                    }
                });

                const fileName = `تقرير_تقييم_المخزون_${new Date().toISOString().split('T')[0]}.pdf`;
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
        }

        /* تحسينات للطباعة */
        @media print {
            .no-print {
                display: none !important;
            }

            .page-header {
                background: none !important;
                color: black !important;
                -webkit-print-color-adjust: exact;
            }

            .card-modern {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
            }
        }
    </style>
@endsection