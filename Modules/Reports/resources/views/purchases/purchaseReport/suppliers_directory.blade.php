@extends('master')

@section('title')
    تقرير دليل الموردين
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

        /* تحسين شريط التمرير */
        .select2-results::-webkit-scrollbar {
            width: 8px;
        }

        .select2-results::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .select2-results::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .select2-results::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
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

            .select2-container--open .select2-dropdown {
                border: 2px solid #007bff !important;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
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
                        <i class="fas fa-address-book me-3"></i>
                        تقرير دليل الموردين
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">دليل الموردين</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-truck"></i>
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
                    <div class="row g-4">
                        <!-- First Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-user-tie me-2"></i>المنشئ</label>
                            <select name="created_by" id="created_by" class="form-control select2">
                                <option value="">جميع الموظفين</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-map-marker-alt me-2"></i>المدينة</label>
                            <select name="city" id="city" class="form-control select2">
                                <option value="">جميع المدن</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city }}">{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-globe me-2"></i>البلد</label>
                            <select name="country" id="country" class="form-control select2">
                                <option value="">جميع البلدان</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country }}">{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-signature me-2"></i>اسم المورد</label>
                            <input type="text" name="supplier_name" id="supplier_name" class="form-control" placeholder="البحث بالاسم التجاري">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-barcode me-2"></i>رقم المورد</label>
                            <input type="text" name="supplier_code" id="supplier_code" class="form-control" placeholder="البحث برقم المورد">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-balance-scale me-2"></i>نوع الرصيد</label>
                            <select name="balance_type" id="balance_type" class="form-control select2">
                                <option value="all">جميع الأرصدة</option>
                                <option value="positive">رصيد موجب</option>
                                <option value="negative">رصيد سالب</option>
                                <option value="zero">رصيد صفر</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-layer-group me-2"></i>تجميع حسب</label>
                            <select name="group_by" id="group_by" class="form-control select2">
                                <option value="none">بدون تجميع</option>
                                <option value="branch">الفرع</option>
                                <option value="city">المدينة</option>
                                <option value="country">البلد</option>
                                <option value="creator">المنشئ</option>
                            </select>
                        </div>

                        <!-- Third Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-sort me-2"></i>ترتيب حسب</label>
                            <select name="sort_by" id="sort_by" class="form-control select2">
                                <option value="name">الاسم التجاري</option>
                                <option value="code">رقم المورد</option>
                                <option value="balance">الرصيد</option>
                                <option value="city">المدينة</option>
                                <option value="created_at">تاريخ الإنشاء</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-sort-amount-up me-2"></i>اتجاه الترتيب</label>
                            <select name="sort_direction" id="sort_direction" class="form-control select2">
                                <option value="asc">تصاعدي</option>
                                <option value="desc">تنازلي</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-lg-6 col-md-12 align-self-end">
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
                        <button class="btn-modern btn-danger-modern" id="exportPdf">
                            <i class="fas fa-file-pdf"></i>
                            تصدير PDF
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
            <div class="col-lg-2-4 col-md-6 mb-3">
                <div class="stats-card primary">
                    <div class="stats-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-value" id="totalSuppliers">{{ number_format($totals['total_suppliers'] ?? 0, 0) }}</div>
                    <div class="stats-label">إجمالي الموردين</div>
                </div>
            </div>

            <div class="col-lg-2-4 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div class="stats-value" id="totalBalance">{{ number_format($totals['total_balance'] ?? 0, 2) }}</div>
                    <div class="stats-label">إجمالي الأرصدة (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2-4 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stats-value" id="totalPurchases">{{ number_format($totals['total_purchases'] ?? 0, 2) }}</div>
                    <div class="stats-label">إجمالي المشتريات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2-4 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon danger">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="stats-value" id="totalReturns">{{ number_format($totals['total_returns'] ?? 0, 2) }}</div>
                    <div class="stats-label">إجمالي المرتجعات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2-4 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stats-value" id="totalPayments">{{ number_format($totals['total_payments'] ?? 0, 2) }}</div>
                    <div class="stats-label">إجمالي المدفوعات (ريال)</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني للموردين
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="row">
                    <div class="col-md-6">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="suppliersBalanceChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="topSuppliersChart"></canvas>
                        </div>
                    </div>
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
                    دليل الموردين
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-barcode me-2"></i>الكود</th>
                                <th><i class="fas fa-signature me-2"></i>الاسم التجاري</th>
                                <th><i class="fas fa-user me-2"></i>الاسم الكامل</th>
                                <th><i class="fas fa-phone me-2"></i>الهاتف</th>
                                <th><i class="fas fa-mobile-alt me-2"></i>الجوال</th>
                                <th><i class="fas fa-map-marker-alt me-2"></i>العنوان</th>
                                <th><i class="fas fa-balance-scale me-2"></i>الرصيد الحالي</th>
                                <th><i class="fas fa-shopping-cart me-2"></i>إجمالي المشتريات</th>
                                <th><i class="fas fa-shopping-cart me-2"></i>إجمالي   المرتجع</th>
                                <th><i class="fas fa-money-bill me-2"></i>إجمالي المدفوعات</th>
                                <th><i class="fas fa-building me-2"></i>الفرع</th>
                                <th><i class="fas fa-user-tie me-2"></i>المنشئ</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.24/jspdf.plugin.autotable.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        let suppliersBalanceChart;
        let topSuppliersChart;

        $(document).ready(function() {
            // تهيئة Select2 مع إعدادات محسنة للجوال
            $('.select2').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                language: {
                    noResults: function() {
                        return "لا توجد نتائج";
                    },
                    searching: function() {
                        return "جاري البحث...";
                    },
                    loadingMore: function() {
                        return "جاري تحميل المزيد...";
                    }
                },
                allowClear: true,
                width: '100%',
                dropdownParent: $('body'),
                minimumResultsForSearch: 0,
                placeholder: function() {
                    return $(this).data('placeholder') || 'اختر...';
                },
                dropdownCssClass: 'select2-dropdown-custom',
                closeOnSelect: true,
                selectOnClose: false
            });

            // تخصيص تصميم Select2
            $('.select2-container--bootstrap-5 .select2-selection--single').css({
                'border': '2px solid var(--gray-200)',
                'border-radius': '12px',
                'height': 'auto',
                'padding': '0.5rem 1rem',
                'min-height': '48px'
            });

            // إزالة أي عناصر إضافية قد تظهر تحت Select2
            $('.select2-container').each(function() {
                $(this).next('.select2-dropdown').remove();
                $(this).siblings('.select2-dropdown').remove();
            });

            // منع ظهور dropdown في مكان خاطئ
            $('.select2').on('select2:open', function() {
                $('.select2-dropdown').css({
                    'position': 'absolute',
                    'z-index': '9999'
                });
            });

            // تحميل البيانات الأولية
            loadReportData();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // التعامل مع تصفية التقرير
            $('#filterBtn').click(function() {
                $(this).addClass('loading');
                loadReportData();
            });

            // إعادة تعيين الفلاتر
            $('#resetBtn').click(function() {
                $('#reportForm')[0].reset();
                $('.select2').val(null).trigger('change');
                loadReportData();
            });

            // التعامل مع تصدير إكسل
            $('#exportExcel').click(function() {
                exportToExcel();
            });

            // التعامل مع تصدير PDF
            $('#exportPdf').click(function() {
                exportToPDF();
            });

            // التعامل مع الطباعة
            $('#printBtn').click(function() {
                window.print();
            });

            // تحديث البيانات عند تغيير أي فلتر
            $('.select2').on('change', function() {
                loadReportData();
            });

            // تحديث البيانات عند الكتابة في حقول البحث
            $('#supplier_name, #supplier_code').on('input', debounce(function() {
                loadReportData();
            }, 500));

            // دالة تحميل بيانات التقرير
            function loadReportData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#reportForm').serialize();

                $.ajax({
                    url: '{{ route('ReportsPurchases.SuppliersDirectoryAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateReportDisplay(response);
                        $('.loading-overlay').fadeOut();
                        $('#filterBtn').prop('disabled', false);
                        $('#filterBtn').removeClass('loading');

                        // Add success animation
                        $('#reportContainer').addClass('fade-in');
                    },
                    error: function(xhr, status, error) {
                        console.error('خطأ في تحميل البيانات:', error);
                        $('.loading-overlay').fadeOut();
                        $('#filterBtn').prop('disabled', false);
                        $('#filterBtn').removeClass('loading');

                        // Show error message
                        showAlert('حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.', 'danger');
                    }
                });
            }

            // دالة تحديث عرض التقرير
            function updateReportDisplay(data) {
                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#totalSuppliers', 0, data.totals.total_suppliers, 1000);
                animateValue('#totalBalance', 0, data.totals.total_balance, 1000);
                animateValue('#totalPurchases', 0, data.totals.total_purchases, 1000);
                animateValue('#totalReturns', 0, data.totals.total_returns, 1000);
                animateValue('#totalPayments', 0, data.totals.total_payments, 1000);

                // تحديث عنوان التقرير
                let title = 'دليل الموردين';
                if (data.group_by !== 'none') {
                    const groupNames = {
                        'branch': 'مجمع حسب الفرع',
                        'city': 'مجمع حسب المدينة',
                        'country': 'مجمع حسب البلد',
                        'creator': 'مجمع حسب المنشئ'
                    };
                    title += ' - ' + (groupNames[data.group_by] || '');
                }

                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    ${title}
                    <span class="badge bg-primary ms-2">${data.totals.total_suppliers} مورد</span>
                `);

                // تحديث الرسم البياني
                updateCharts(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.grouped_suppliers, data.totals, data.group_by);
            }

            // دالة تحديث الرسوم البيانية
            function updateCharts(chartData) {
                // رسم بياني لتوزيع الأرصدة
                const balanceCtx = document.getElementById('suppliersBalanceChart').getContext('2d');

                if (suppliersBalanceChart) {
                    suppliersBalanceChart.destroy();
                }

                suppliersBalanceChart = new Chart(balanceCtx, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.balance_chart.labels,
                        datasets: [{
                            data: chartData.balance_chart.values,
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(156, 163, 175, 0.8)'
                            ],
                            borderColor: [
                                'rgba(16, 185, 129, 1)',
                                'rgba(239, 68, 68, 1)',
                                'rgba(156, 163, 175, 1)'
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
                                    padding: 15,
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
                                        return context.label + ': ' + context.parsed + ' مورد';
                                    }
                                }
                            }
                        }
                    }
                });

                // رسم بياني لأكبر الموردين حسب الرصيد
                const topCtx = document.getElementById('topSuppliersChart').getContext('2d');

                if (topSuppliersChart) {
                    topSuppliersChart.destroy();
                }

                const topSuppliersData = chartData.top_suppliers || [];

                topSuppliersChart = new Chart(topCtx, {
                    type: 'bar',
                    data: {
                        labels: topSuppliersData.map(s => s.name.length > 15 ? s.name.substring(0, 15) + '...' : s.name),
                        datasets: [{
                            label: 'الرصيد (ريال)',
                            data: topSuppliersData.map(s => s.balance),
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
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
                                        return 'الرصيد: ' + formatNumber(context.parsed.y) + ' ريال';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return formatNumber(value) + ' ريال';
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
                                        family: 'Cairo',
                                        size: 10
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
            function updateTableBody(groupedData, totals, groupBy) {
                let tableHtml = '';
                let counter = 1;

                // تكرار عبر المجموعات
                Object.keys(groupedData).forEach(groupName => {
                    const groupData = groupedData[groupName];
                    const suppliers = groupData.suppliers;
                    const groupTotals = groupData.group_totals;

                    // إذا كان هناك تجميع، أضف رأس المجموعة
                    if (groupBy !== 'none') {
                        tableHtml += `
                            <tr class="table-group-header">
                                <td colspan="13">
                                    <i class="fas fa-layer-group me-2"></i>
                                    <strong>${groupName}</strong>
                                    <span class="ms-3">
                                        (عدد الموردين: ${groupTotals.count} |
                                        إجمالي الأرصدة: ${formatNumber(groupTotals.total_balance)} ريال |
                                        إجمالي المشتريات: ${formatNumber(groupTotals.total_purchases)} ريال |
                                        إجمالي المرتجعات: ${formatNumber(groupTotals.total_returns)} ريال)
                                    </span>
                                </td>
                            </tr>
                        `;
                    }

                    // عرض الموردين
                    suppliers.forEach(supplier => {
                        const balanceClass = supplier.current_balance > 0 ? 'text-success' :
                                           supplier.current_balance < 0 ? 'text-danger' : 'text-muted';

                        tableHtml += `<tr class="supplier-row">`;
                        tableHtml += `<td>${counter++}</td>`;
                        tableHtml += `<td><span class="badge bg-secondary">${supplier.number_suply || '--'}</span></td>`;
                        tableHtml += `<td><strong>${supplier.trade_name}</strong></td>`;
                        tableHtml += `<td>${supplier.full_name || '--'}</td>`;
                        tableHtml += `<td>${supplier.phone || '--'}</td>`;
                        tableHtml += `<td>${supplier.mobile || '--'}</td>`;
                        tableHtml += `<td>${supplier.full_address || '--'}</td>`;
                        tableHtml += `<td class="${balanceClass} fw-bold">${formatNumber(supplier.current_balance)}</td>`;
                        tableHtml += `<td class="text-primary">${formatNumber(supplier.total_purchases)}</td>`;
                        tableHtml += `<td class="text-warning">${formatNumber(supplier.total_returns)}</td>`;
                        tableHtml += `<td class="text-success">${formatNumber(supplier.total_payments)}</td>`;
                        tableHtml += `<td>${supplier.branch ? supplier.branch.name : '--'}</td>`;
                        tableHtml += `<td>${supplier.creator ? supplier.creator.name : '--'}</td>`;
                        tableHtml += `</tr>`;
                    });

                    // إذا كان هناك تجميع، أضف إجمالي المجموعة
                    if (groupBy !== 'none') {
                        tableHtml += `
                            <tr class="table-group-total">
                                <td colspan="8">
                                    <i class="fas fa-calculator me-2"></i>
                                    <strong>مجموع ${groupName}</strong>
                                </td>
                                <td class="fw-bold">${formatNumber(groupTotals.total_balance)}</td>
                                <td class="fw-bold">${formatNumber(groupTotals.total_purchases)}</td>
                                <td class="fw-bold">${formatNumber(groupTotals.total_returns)}</td>
                                <td class="fw-bold">${formatNumber(groupTotals.total_payments)}</td>
                                <td colspan="2">-</td>
                            </tr>
                        `;
                    }
                });

                // صف الإجمالي العام
                tableHtml += `
                    <tr class="table-grand-total">
                        <td colspan="8">
                            <i class="fas fa-chart-bar me-2"></i>
                            <strong>المجموع الكلي</strong>
                        </td>
                        <td class="fw-bold">${formatNumber(totals.total_balance)}</td>
                        <td class="fw-bold">${formatNumber(totals.total_purchases)}</td>
                        <td class="fw-bold">${formatNumber(totals.total_returns)}</td>
                        <td class="fw-bold">${formatNumber(totals.total_payments)}</td>
                        <td colspan="2">-</td>
                    </tr>
                `;

                $('#reportTableBody').html(tableHtml);
            }

            // دالة تنسيق الأرقام
            function formatNumber(number) {
                return parseFloat(number).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
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
                    if (element.includes('totalSuppliers')) {
                        obj.text(start);
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
                const fileName = `دليل_الموردين_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

                XLSX.writeFile(wb, fileName);
                showAlert('تم تصدير الملف بنجاح!', 'success');
            }

            // دالة تصدير PDF
            function exportToPDF() {
                showAlert('جاري تصدير ملف PDF...', 'info');

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('l', 'mm', 'a4'); // landscape orientation

                // إعداد الخط العربي
                doc.setFont('helvetica');

                // عنوان التقرير
                doc.setFontSize(16);
                doc.text('Suppliers Directory Report', 20, 20);

                // الحصول على بيانات الجدول
                const table = document.querySelector('#reportContainer table');

                doc.autoTable({
                    html: table,
                    startY: 30,
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                    },
                    headStyles: {
                        fillColor: [79, 70, 229],
                        textColor: 255,
                        fontSize: 9,
                    },
                    alternateRowStyles: {
                        fillColor: [245, 245, 245]
                    },
                    margin: { top: 30, right: 10, bottom: 10, left: 10 },
                });

                const today = new Date();
                const fileName = `suppliers_directory_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.pdf`;

                doc.save(fileName);
                showAlert('تم تصدير ملف PDF بنجاح!', 'success');
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

            // دالة debounce للبحث
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // التعامل مع أزرار العرض
            $('#summaryViewBtn, #detailViewBtn').click(function() {
                $('.btn-group .btn-modern').removeClass('btn-primary-modern active').addClass('btn-outline-modern');
                $(this).removeClass('btn-outline-modern').addClass('btn-primary-modern active');

                if ($(this).attr('id') === 'summaryViewBtn') {
                    $('#chartSection').show();
                    $('#totalsSection').show();
                    $('#reportContainer').hide();
                    showAlert('تم التبديل إلى عرض الملخص', 'info');
                } else {
                    $('#chartSection').hide();
                    $('#totalsSection').hide();
                    $('#reportContainer').show();
                    showAlert('تم التبديل إلى عرض التفاصيل', 'info');
                }
            });

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
