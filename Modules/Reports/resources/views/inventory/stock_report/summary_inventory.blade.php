@extends('master')

@section('title')
    تقرير ملخص المخزون
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
                        <i class="fas fa-chart-pie me-3"></i>
                        تقرير ملخص المخزون
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير ملخص المخزون</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-chart-pie"></i>
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
                <form id="summaryForm">
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
                            <label class="form-label-modern"><i class="fas fa-sort me-2"></i>ترتيب حسب المنتج</label>
                            <select name="sort_by" id="sort_by" class="form-control select2">
                                <option value="">اختر الترتيب</option>
                                <option value="quantity_asc">كمية المخزون تصاعدي</option>
                                <option value="quantity_desc">كمية المخزون تنازلي</option>
                                <option value="name_asc">الاسم أبجدياً</option>
                                <option value="name_desc">الاسم عكس أبجدياً</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-layer-group me-2"></i>تجميع حسب</label>
                            <select name="group_by" id="group_by" class="form-control select2">
                                <option value="">بدون تجميع</option>
                                <option value="category">التصنيف</option>
                                <option value="brand">العلامة التجارية</option>
                                <option value="warehouse">المستودع</option>
                            </select>
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
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="stats-value" id="totalMovement">0</div>
                    <div class="stats-label">إجمالي الحركة</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني لحركة المخزون
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="summaryChart"></canvas>
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
                    ملخص المخزون - مؤسسة أعمال خاصة للتجارة
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle"><i class="fas fa-cube me-2"></i>اسم المنتج</th>
                                <th colspan="5" class="text-center bg-success-subtle">
                                    <i class="fas fa-arrow-down me-2"></i>الوارد
                                </th>
                                <th colspan="6" class="text-center bg-danger-subtle">
                                    <i class="fas fa-arrow-up me-2"></i>المنصرف
                                </th>
                                <th rowspan="2" class="align-middle text-center">
                                    <i class="fas fa-exchange-alt me-2"></i>إجمالي الحركة
                                </th>
                            </tr>
                            <tr>
                                <!-- الوارد -->
                                <th class="text-center">فواتير الشراء</th>
                                <th class="text-center">مرتجع المبيعات</th>
                                <th class="text-center">التحويل</th>
                                <th class="text-center">يدوي</th>
                                <th class="text-center bg-success text-white">الإجمالي</th>

                                <!-- المنصرف -->
                                <th class="text-center">فواتير البيع</th>
                                <th class="text-center">مرتجع المشتريات</th>
                                <th class="text-center">التحويل</th>
                                <th class="text-center">يدوي</th>
                                <th class="text-center bg-danger text-white">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody id="summaryTableBody">
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
        let summaryChart;

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
            loadSummaryData();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // التعامل مع تصفية التقرير
            $('#filterBtn').click(function() {
                $(this).addClass('loading');
                loadSummaryData();
            });

            // إعادة تعيين الفلاتر
            $('#resetBtn').click(function() {
                $('#summaryForm')[0].reset();
                $('.select2').val(null).trigger('change');
                loadSummaryData();
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
            $('.select2, input[type="date"]').on('change', function() {
                loadSummaryData();
            });

            // دالة تحميل بيانات ملخص المخزون
            function loadSummaryData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#summaryForm').serialize();

                $.ajax({
                    url: '{{ route('StorHouseReport.summaryInventoryAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateSummaryDisplay(response);
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

            // دالة تحديث عرض ملخص المخزون
            function updateSummaryDisplay(data) {
                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#totalProducts', 0, data.totals.total_products, 1000);
                animateValue('#totalIncoming', 0, data.totals.total_incoming, 1000);
                animateValue('#totalOutgoing', 0, data.totals.total_outgoing, 1000);
                animateValue('#totalMovement', 0, data.totals.total_movement, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    ملخص المخزون - ${data.group_by ? 'تجميع حسب ' + data.group_by : 'جميع المنتجات'}
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.products, data.totals);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('summaryChart').getContext('2d');

                if (summaryChart) {
                    summaryChart.destroy();
                }

                summaryChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels || [],
                        datasets: [
                            {
                                label: 'الوارد',
                                data: chartData.incoming || [],
                                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                            },
                            {
                                label: 'المنصرف',
                                data: chartData.outgoing || [],
                                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                borderColor: 'rgba(239, 68, 68, 1)',
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
                                        return context.dataset.label + ': ' + formatNumber(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                stacked: false,
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
                                stacked: false,
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
                        purchase: 0,
                        purchase_return: 0,
                        transfer_in: 0,
                        manual_in: 0,
                        total_in: 0,
                        sale: 0,
                        sale_return: 0,
                        purchase_return_out: 0,
                        transfer_out: 0,
                        manual_out: 0,
                        total_out: 0,
                        total_movement: 0
                    };

                    products.forEach((product, index) => {
                        // حساب الوارد
                        const purchase = parseFloat(product.purchase || 0);
                        const saleReturn = parseFloat(product.sale_return || 0);
                        const transferIn = parseFloat(product.transfer_in || 0);
                        const manualIn = parseFloat(product.manual_in || 0);
                        const totalIn = purchase + saleReturn + transferIn + manualIn;

                        // حساب المنصرف
                        const sale = parseFloat(product.sale || 0);
                        const purchaseReturnOut = parseFloat(product.purchase_return_out || 0);
                        const transferOut = parseFloat(product.transfer_out || 0);
                        const manualOut = parseFloat(product.manual_out || 0);
                        const totalOut = sale + purchaseReturnOut + transferOut + manualOut;

                        // إجمالي الحركة
                        const totalMovement = totalIn + totalOut;

                        // تحديث الإجماليات الكبرى
                        grandTotals.purchase += purchase;
                        grandTotals.sale_return += saleReturn;
                        grandTotals.transfer_in += transferIn;
                        grandTotals.manual_in += manualIn;
                        grandTotals.total_in += totalIn;
                        grandTotals.sale += sale;
                        grandTotals.purchase_return_out += purchaseReturnOut;
                        grandTotals.transfer_out += transferOut;
                        grandTotals.manual_out += manualOut;
                        grandTotals.total_out += totalOut;
                        grandTotals.total_movement += totalMovement;

                        tableHtml += `<tr>`;
                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-cube"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">${product.name}</div>
                                    <small class="text-muted">${product.code || product.id}</small>
                                </div>
                            </div>
                        </td>`;

                        // الوارد
                        tableHtml += `<td class="text-center">${formatNumber(purchase)}</td>`;
                        tableHtml += `<td class="text-center">${formatNumber(saleReturn)}</td>`;
                        tableHtml += `<td class="text-center">${formatNumber(transferIn)}</td>`;
                        tableHtml += `<td class="text-center">${formatNumber(manualIn)}</td>`;
                        tableHtml += `<td class="text-center fw-bold text-success">${formatNumber(totalIn)}</td>`;

                        // المنصرف
                        tableHtml += `<td class="text-center">${formatNumber(sale)}</td>`;
                        tableHtml += `<td class="text-center">${formatNumber(purchaseReturnOut)}</td>`;
                        tableHtml += `<td class="text-center">${formatNumber(transferOut)}</td>`;
                        tableHtml += `<td class="text-center">${formatNumber(manualOut)}</td>`;
                        tableHtml += `<td class="text-center fw-bold text-danger">${formatNumber(totalOut)}</td>`;

                        // إجمالي الحركة
                        tableHtml += `<td class="text-center fw-bold text-info">${formatNumber(totalMovement)}</td>`;
                        tableHtml += `</tr>`;
                    });

                    // إضافة صف الإجمالي العام
                    tableHtml += `
                        <tr class="table-grand-total">
                            <td class="fw-bold">
                                <i class="fas fa-chart-bar me-2"></i>
                                المجموع الكلي
                            </td>
                            <td class="fw-bold text-center">${formatNumber(grandTotals.purchase)}</td>
                            <td class="fw-bold text-center">${formatNumber(grandTotals.sale_return)}</td>
                            <td class="fw-bold text-center">${formatNumber(grandTotals.transfer_in)}</td>
                            <td class="fw-bold text-center">${formatNumber(grandTotals.manual_in)}</td>
                            <td class="fw-bold text-center text-success">${formatNumber(grandTotals.total_in)}</td>
                            <td class="fw-bold text-center">${formatNumber(grandTotals.sale)}</td>
                            <td class="fw-bold text-center">${formatNumber(grandTotals.purchase_return_out)}</td>
                            <td class="fw-bold text-center">${formatNumber(grandTotals.transfer_out)}</td>
                            <td class="fw-bold text-center">${formatNumber(grandTotals.manual_out)}</td>
                            <td class="fw-bold text-center text-danger">${formatNumber(grandTotals.total_out)}</td>
                            <td class="fw-bold text-center text-info">${formatNumber(grandTotals.total_movement)}</td>
                        </tr>
                    `;
                } else {
                    tableHtml = `
                        <tr>
                            <td colspan="13" class="text-center py-4">
                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">لا توجد منتجات مطابقة للفلاتر المحددة</p>
                            </td>
                        </tr>
                    `;
                }

                $('#summaryTableBody').html(tableHtml);
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
                const fileName = `ملخص_المخزون_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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
                doc.text('ملخص المخزون', 150, 20, { align: 'center' });

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
                        0: { cellWidth: 30 }, // اسم المنتج
                        1: { cellWidth: 15 }, // فواتير الشراء
                        2: { cellWidth: 15 }, // الفواتير المرتجعة
                        3: { cellWidth: 15 }, // التحويل
                        4: { cellWidth: 15 }, // يدوي
                        5: { cellWidth: 20 }, // إجمالي الوارد
                        6: { cellWidth: 15 }, // فواتير البيع
                        7: { cellWidth: 15 }, // مرتجع مشتريات
                        8: { cellWidth: 15 }, // الفواتير المرتجعة
                        9: { cellWidth: 15 }, // التحويل
                        10: { cellWidth: 15 }, // يدوي
                        11: { cellWidth: 20 }, // إجمالي المنصرف
                        12: { cellWidth: 20 }  // إجمالي الحركة
                    }
                });

                const fileName = `ملخص_المخزون_${new Date().toISOString().split('T')[0]}.pdf`;
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
@endsection