@extends('master')

@section('title')
    تقرير أعمار ديون الموردين
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
                        <i class="fas fa-clock me-3"></i>
                        تقرير أعمار ديون الموردين
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">التقارير</a>
                            </li>
                            <li class="breadcrumb-item active">أعمار ديون الموردين</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-clock"></i>
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
                            <label class="form-label-modern">المورد</label>
                            <select name="supplier" id="supplier" class="form-control select2">
                                <option value="">جميع الموردين</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->trade_name }}</option>
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
                            <label class="form-label-modern">الفترة (أيام)</label>
                            <input type="number" id="days" name="days" class="form-control" value="30" min="1" max="365">
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">تمت الإضافة بواسطة</label>
                            <select name="added_by" id="added_by" class="form-control select2">
                                <option value="">الكل</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">السنة المالية</label>
                            <select id="financial-year" name="financial_year[]" class="form-control select2" multiple>
                                <option value="current">السنة المفتوحة</option>
                                <option value="all">جميع السنوات</option>
                                @for ($year = date('Y'); $year >= date('Y') - 10; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">من تاريخ</label>
                            <input type="date" name="from_date" id="from_date" class="form-control"
                                value="{{ $fromDate->format('Y-m-d') }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">إلى تاريخ</label>
                            <input type="date" name="to_date" id="to_date" class="form-control"
                                value="{{ $toDate->format('Y-m-d') }}">
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
                    <div class="stats-value" id="todayAmount">{{ number_format($totals['today'] ?? 0, 2) }}</div>
                    <div class="stats-label">اليوم (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stats-value" id="days1to30">{{ number_format($totals['days1to30'] ?? 0, 2) }}</div>
                    <div class="stats-label">1-30 يوم (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stats-value" id="days31to60">{{ number_format($totals['days31to60'] ?? 0, 2) }}</div>
                    <div class="stats-label">31-60 يوم (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stats-value" id="days61to90">{{ number_format($totals['days61to90'] ?? 0, 2) }}</div>
                    <div class="stats-label">61-90 يوم (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stats-value" id="days91to120">{{ number_format($totals['days91to120'] ?? 0, 2) }}</div>
                    <div class="stats-label">91-120 يوم (ريال)</div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-3">
                <div class="stats-card dark">
                    <div class="stats-icon dark">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stats-value" id="daysOver120">{{ number_format($totals['daysOver120'] ?? 0, 2) }}</div>
                    <div class="stats-label">+120 يوم (ريال)</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني لأعمار الديون
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container">
                    <canvas class="chart bg-light" id="agingChart" style="height: 400px;"></canvas>
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
                    تقرير أعمار ديون الموردين من {{ $fromDate->format('d/m/Y') }} إلى {{ $toDate->format('d/m/Y') }}
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-code me-2"></i>كود المورد</th>
                                <th><i class="fas fa-hashtag me-2"></i>رقم الحساب</th>
                                <th><i class="fas fa-user-tie me-2"></i>اسم المورد</th>
                                <th><i class="fas fa-building me-2"></i>الفرع</th>
                                <th><i class="fas fa-phone me-2"></i>الهاتف</th>
                                <th><i class="fas fa-clock me-2"></i>اليوم (ريال)</th>
                                <th><i class="fas fa-calendar-day me-2"></i>1-30 يوم (ريال)</th>
                                <th><i class="fas fa-calendar-week me-2"></i>31-60 يوم (ريال)</th>
                                <th><i class="fas fa-calendar-alt me-2"></i>61-90 يوم (ريال)</th>
                                <th><i class="fas fa-exclamation-triangle me-2"></i>91-120 يوم (ريال)</th>
                                <th><i class="fas fa-times-circle me-2"></i>+120 يوم (ريال)</th>
                                <th><i class="fas fa-calculator me-2"></i>الإجمالي (ريال)</th>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        let agingChart;

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
                minimumInputLength: 0,
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

            // تحميل البيانات الأولية
            loadReportData();
            initializeChart();

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
                $('#from_date').val('{{ $fromDate->format('Y-m-d') }}');
                $('#to_date').val('{{ $toDate->format('Y-m-d') }}');
                $('#days').val('30');
                loadReportData();
            });

            // التعامل مع تصدير إكسل
            $('#exportExcel').click(function() {
                exportToExcel();
            });

            // التعامل مع الطباعة
            $('#printBtn').click(function() {
                window.print();
            });

            // تحديث البيانات عند تغيير أي فلتر
            $('.select2, #reportForm input').on('change', function() {
                loadReportData();
            });

            // دالة تحميل بيانات التقرير
            function loadReportData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#reportForm').serialize();

                $.ajax({
                    url: '{{ route('ReportsPurchases.supplierDebtAgingAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateReportDisplay(response);
                        $('.loading-overlay').fadeOut();
                        $('#filterBtn').prop('disabled', false);
                        $('#reportContainer').addClass('fade-in');
                    },
                    error: function(xhr, status, error) {
                        console.error('خطأ في تحميل البيانات:', error);
                        $('.loading-overlay').fadeOut();
                        $('#filterBtn').prop('disabled', false);
                        showAlert('حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.', 'danger');
                    }
                });
            }

            // دالة تحديث عرض التقرير
            function updateReportDisplay(data) {
                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#todayAmount', 0, data.totals.today, 1000);
                animateValue('#days1to30', 0, data.totals.days1to30, 1000);
                animateValue('#days31to60', 0, data.totals.days31to60, 1000);
                animateValue('#days61to90', 0, data.totals.days61to90, 1000);
                animateValue('#days91to120', 0, data.totals.days91to120, 1000);
                animateValue('#daysOver120', 0, data.totals.daysOver120, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير أعمار ديون الموردين من ${data.from_date} إلى ${data.to_date}
                `);

                // تحديث جدول البيانات
                updateTableBody(data.grouped_suppliers, data.totals);

                // تحديث الرسم البياني
                updateChart(data.chart_data);
            }

            // دالة تحديث محتوى الجدول
            function updateTableBody(groupedSuppliers, totals) {
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

                // تكرار عبر المجموعات
                Object.keys(groupedSuppliers).forEach(supplierName => {
                    const supplierGroup = groupedSuppliers[supplierName];
                    const supplierData = supplierGroup.data;

                    // صف رأس المورد
                    if (supplierData.length > 1) {
                        tableHtml += `
                            <tr class="table-employee-header">
                                <td colspan="12">
                                    <i class="fas fa-user-tie me-2"></i>
                                    <strong>${supplierName}</strong>
                                </td>
                            </tr>
                        `;
                    }

                    // تكرار عبر بيانات المورد
                    supplierData.forEach(item => {
                        const isOverdue = (item.days91to120 > 0 || item.daysOver120 > 0);
                        const rowClass = isOverdue ? 'table-warning' : '';

                        tableHtml += `<tr class="${rowClass}">`;
                        tableHtml += `<td>${item.supplier_code}</td>`;
                        tableHtml += `<td>${item.account_number}</td>`;
                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-tie me-2 text-primary"></i>
                                <div>
                                    <div class="fw-bold">${item.supplier_name}</div>
                                    <small class="text-muted">${item.supplier_email}</small>
                                </div>
                            </div>
                        </td>`;
                        tableHtml += `<td>${item.branch}</td>`;
                        tableHtml += `<td>${item.supplier_phone}</td>`;

                        // الأعمدة المالية مع الألوان
                        tableHtml += `<td class="fw-bold ${item.today > 0 ? 'text-primary' : ''}">${formatNumber(item.today)}</td>`;
                        tableHtml += `<td class="fw-bold ${item.days1to30 > 0 ? 'text-warning' : ''}">${formatNumber(item.days1to30)}</td>`;
                        tableHtml += `<td class="fw-bold ${item.days31to60 > 0 ? 'text-info' : ''}">${formatNumber(item.days31to60)}</td>`;
                        tableHtml += `<td class="fw-bold ${item.days61to90 > 0 ? 'text-success' : ''}">${formatNumber(item.days61to90)}</td>`;
                        tableHtml += `<td class="fw-bold ${item.days91to120 > 0 ? 'text-danger' : ''}">${formatNumber(item.days91to120)}</td>`;
                        tableHtml += `<td class="fw-bold ${item.daysOver120 > 0 ? 'text-dark' : ''}">${formatNumber(item.daysOver120)}</td>`;
                        tableHtml += `<td class="fw-bold text-primary">${formatNumber(item.total_due)}</td>`;
                        tableHtml += `</tr>`;

                        // تجميع الإجماليات
                        grandTotals.today += parseFloat(item.today);
                        grandTotals.days1to30 += parseFloat(item.days1to30);
                        grandTotals.days31to60 += parseFloat(item.days31to60);
                        grandTotals.days61to90 += parseFloat(item.days61to90);
                        grandTotals.days91to120 += parseFloat(item.days91to120);
                        grandTotals.daysOver120 += parseFloat(item.daysOver120);
                        grandTotals.total_due += parseFloat(item.total_due);
                    });

                    // صف إجمالي المورد (إذا كان هناك أكثر من سجل)
                    if (supplierData.length > 1) {
                        const supplierTotals = supplierGroup.supplier_totals;
                        tableHtml += `
                            <tr class="table-employee-total">
                                <td colspan="5">
                                    <i class="fas fa-calculator me-2"></i>
                                    <strong>مجموع ${supplierName}</strong>
                                </td>
                                <td class="fw-bold">${formatNumber(supplierTotals.today)}</td>
                                <td class="fw-bold">${formatNumber(supplierTotals.days1to30)}</td>
                                <td class="fw-bold">${formatNumber(supplierTotals.days31to60)}</td>
                                <td class="fw-bold">${formatNumber(supplierTotals.days61to90)}</td>
                                <td class="fw-bold">${formatNumber(supplierTotals.days91to120)}</td>
                                <td class="fw-bold">${formatNumber(supplierTotals.daysOver120)}</td>
                                <td class="fw-bold">${formatNumber(supplierTotals.total_due)}</td>
                            </tr>
                        `;
                    }
                });

                // صف الإجمالي العام
                tableHtml += `
                    <tr class="table-grand-total">
                        <td colspan="5">
                            <i class="fas fa-chart-bar me-2"></i>
                            <strong>المجموع الكلي</strong>
                        </td>
                        <td class="fw-bold">${formatNumber(grandTotals.today)}</td>
                        <td class="fw-bold">${formatNumber(grandTotals.days1to30)}</td>
                        <td class="fw-bold">${formatNumber(grandTotals.days31to60)}</td>
                        <td class="fw-bold">${formatNumber(grandTotals.days61to90)}</td>
                        <td class="fw-bold">${formatNumber(grandTotals.days91to120)}</td>
                        <td class="fw-bold">${formatNumber(grandTotals.daysOver120)}</td>
                        <td class="fw-bold">${formatNumber(grandTotals.total_due)}</td>
                    </tr>
                `;

                $('#reportTableBody').html(tableHtml);
            }

            // دالة تهيئة الرسم البياني
            function initializeChart() {
                const ctx = document.getElementById('agingChart').getContext('2d');

                agingChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['اليوم', '1-30 يوم', '31-60 يوم', '61-90 يوم', '91-120 يوم', '+120 يوم'],
                        datasets: [{
                            label: 'أعمار الديون (ريال)',
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
                                text: 'توزيع أعمار ديون الموردين',
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

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                if (agingChart && chartData.aging_values) {
                    agingChart.data.datasets[0].data = chartData.aging_values;
                    agingChart.update('active');
                }
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
                const range = Math.abs(end - start);
                
                // إذا كان الفرق صغير جداً أو صفر، اعرض القيمة النهائية مباشرة
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
                    
                    // توقف عند اكتمال الرسوم المتحركة
                    if (progress >= 1) {
                        obj.text(formatNumber(end));
                        clearInterval(timer);
                    }
                }, 16); // 60 FPS تقريباً
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
                const fileName = `تقرير_أعمار_ديون_الموردين_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

                XLSX.writeFile(wb, fileName);
                showAlert('تم تصدير الملف بنجاح!', 'success');
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

            // التعامل مع أزرار العرض
            $('#summaryViewBtn, #detailViewBtn').click(function() {
                $('.btn-group .btn-modern').removeClass('btn-primary-modern active').addClass('btn-outline-modern');
                $(this).removeClass('btn-outline-modern').addClass('btn-primary-modern active');

                if ($(this).attr('id') === 'summaryViewBtn') {
                    $('#chartSection').fadeIn();
                    showAlert('تم التبديل إلى عرض الملخص', 'info');
                } else {
                    $('#chartSection').fadeOut();
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

        // CSS إضافي للتحسينات
        const additionalCSS = `
            <style>
                .table-employee-header {
                    background: linear-gradient(45deg, #007bff, #0056b3) !important;
                    color: white !important;
                }

                .table-employee-total {
                    background: linear-gradient(45deg, #28a745, #1e7e34) !important;
                    color: white !important;
                }

                .table-grand-total {
                    background: linear-gradient(45deg, #dc3545, #bd2130) !important;
                    color: white !important;
                    font-weight: bold !important;
                }

                .table-warning {
                    background-color: rgba(255, 193, 7, 0.1) !important;
                }

                .stats-card {
                    transition: all 0.3s ease;
                }

                .btn-modern {
                    transition: all 0.3s ease;
                }

                .loading-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8);
                    display: none;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                }

                .spinner {
                    width: 40px;
                    height: 40px;
                    border: 4px solid #f3f3f3;
                    border-top: 4px solid #007bff;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }

                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }

                .chart-container {
                    position: relative;
                    height: 400px;
                    padding: 20px;
                }

                .stats-card .stats-icon.dark {
                    background: linear-gradient(45deg, #343a40, #23272b);
                    color: white;
                }

                .stats-card .stats-icon.info {
                    background: linear-gradient(45deg, #17a2b8, #117a8b);
                    color: white;
                }
            </style>
        `;

        $('head').append(additionalCSS);
    </script>

@endsection
