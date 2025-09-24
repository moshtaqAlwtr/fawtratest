@extends('master')

@section('title')
    تقرير الأرباح الشامل للموظفين
@stop

@section('css')
    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
    <style>
        /* تحسينات Select2 لإزالة العناصر غير المرغوبة */
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

        .select2-results__options {
            max-height: 300px !important;
            overflow-y: auto !important;
        }

        .select2-results__option {
            padding: 0.5rem 1rem !important;
        }

        .select2-results__option--highlighted {
            background-color: #0d6efd !important;
        }

        /* إخفاء أي عناصر إضافية قد تظهر */
        .select2-container + .select2-container,
        .select2-container ~ .select2-dropdown {
            display: none !important;
        }

        /* تحسين المظهر العام */
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

         /* CSS مخصص للـ dropdown */
         .select2-dropdown-custom {
             max-height: 300px !important;
             overflow-y: auto !important;
         }

         .select2-dropdown-custom .select2-results {
             max-height: 300px !important;
             overflow-y: auto !important;
         }

         .select2-dropdown-custom .select2-results__options {
             max-height: 300px !important;
             overflow-y: auto !important;
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

         /* إخفاء رسائل التحديد غير المرغوبة */
         .select2-selection__limit,
         .select2-selection__choice__remove {
             display: none !important;
         }

         .select2-container--bootstrap-5 .select2-selection__choice {
             display: none !important;
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
                 font-size: 16px !important; /* منع التكبير في iOS */
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
                        <i class="fas fa-chart-line me-3"></i>
                        تقرير الأرباح الشامل للموظفين
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير الأرباح الشامل للموظفين</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-coins"></i>
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
                            <label class="form-label-modern"><i class="fas fa-user-tie me-2"></i>الموظف</label>
                            <select name="employee" id="employee" class="form-control select2">
                                <option value="">جميع الموظفين</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-user me-2"></i>العميل</label>
                            <select name="client" id="client" class="form-control select2">
                                <option value="">جميع العملاء</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->trade_name }}-{{ $client->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-building me-2"></i>الفرع</label>
                            <select name="branch" id="branch" class="form-control select2">
                                <option value="">جميع الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-box me-2"></i>المنتج</label>
                            <select name="product" id="product" class="form-control select2">
                                <option value="">جميع المنتجات</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-tags me-2"></i>التصنيف</label>
                            <select name="category" id="category" class="form-control select2">
                                <option value="">جميع التصنيفات</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-trademark me-2"></i>الماركة</label>
                            <select name="brand" id="brand" class="form-control select2">
                                <option value="">جميع الماركات</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand }}">{{ $brand }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-file-alt me-2"></i>نوع التقرير</label>
                            <select name="report_type" id="report_type" class="form-control select2">
                                <option value="comprehensive">التقرير الشامل</option>
                                <option value="employee_profits">أرباح الموظفين</option>
                                <option value="client_profits">أرباح العملاء</option>
                                <option value="product_profits">أرباح المنتجات</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar me-2"></i>نوع التاريخ</label>
                            <select name="date_type" id="date_type" class="form-control select2">
                                <option value="custom">مخصص</option>
                                <option value="today">اليوم</option>
                                <option value="yesterday">أمس</option>
                                <option value="this_week">هذا الأسبوع</option>
                                <option value="last_week">الأسبوع الماضي</option>
                                <option value="this_month">هذا الشهر</option>
                                <option value="last_month">الشهر الماضي</option>
                                <option value="this_year">هذا العام</option>
                                <option value="last_year">العام الماضي</option>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="col-lg-3 col-md-6" id="custom_dates">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>من تاريخ</label>
                            <input type="date" name="from_date" id="from_date" class="form-control"
                                value="{{ $fromDate->format('Y-m-d') }}">
                        </div>

                        <div class="col-lg-3 col-md-6" id="custom_to_date">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>إلى تاريخ</label>
                            <input type="date" name="to_date" id="to_date" class="form-control"
                                value="{{ $toDate->format('Y-m-d') }}">
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
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stats-value" id="totalRevenue">{{ number_format($totals['total_revenue'] ?? 0, 2) }}
                    </div>
                    <div class="stats-label">إجمالي الإيرادات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon danger">
                        <i class="fas fa-minus-circle"></i>
                    </div>
                    <div class="stats-value" id="totalCost">{{ number_format($totals['total_cost'] ?? 0, 2) }}
                    </div>
                    <div class="stats-label">إجمالي التكلفة (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card primary">
                    <div class="stats-icon primary">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stats-value" id="totalProfit">{{ number_format($totals['total_profit'] ?? 0, 2) }}</div>
                    <div class="stats-label">صافي الربح (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stats-value" id="profitMargin">{{ number_format($totals['total_profit_margin'] ?? 0, 2) }}%</div>
                    <div class="stats-label">هامش الربح</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني للأرباح والإيرادات
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="profitsChart"></canvas>
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
                    تقرير الأرباح الشامل من {{ $fromDate->format('d/m/Y') }} إلى {{ $toDate->format('d/m/Y') }}
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-user-tie me-2"></i>الموظف</th>
                                <th><i class="fas fa-calendar me-2"></i>التاريخ</th>
                                <th><i class="fas fa-user me-2"></i>العميل</th>
                                <th><i class="fas fa-box me-2"></i>المنتج</th>
                                <th><i class="fas fa-sort-numeric-up me-2"></i>الكمية</th>
                                <th><i class="fas fa-dollar-sign me-2"></i>الإيراد (ريال)</th>
                                <th><i class="fas fa-minus-circle me-2"></i>التكلفة (ريال)</th>
                                <th><i class="fas fa-chart-line me-2"></i>الربح (ريال)</th>
                                <th><i class="fas fa-percentage me-2"></i>هامش الربح</th>
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
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        let profitsChart;

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
                // إعدادات خاصة للجوال
                adaptContainerCssClass: function(clazz) {
                    return clazz;
                },
                adaptDropdownCssClass: function(clazz) {
                    return clazz;
                },
                // تفعيل البحث دائماً حتى في الجوال
                minimumInputLength: 0,
                // إعدادات إضافية للجوال
                closeOnSelect: true,
                selectOnClose: false
            });

            // تخصيص تصميم Select2 وإزالة العناصر غير المرغوبة
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

            // التعامل مع تغيير نوع التاريخ
            $('#date_type').change(function() {
                loadReportData();
            });

            // التعامل مع تصفية التقرير
            $('#filterBtn').click(function() {
                $(this).addClass('loading');
                loadReportData();
            });

            // إعادة تعيين الفلاتر
            $('#resetBtn').click(function() {
                $('#reportForm')[0].reset();
                $('.select2').val(null).trigger('change');
                $('#date_type').val('custom').trigger('change');
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
            $('.select2').on('change', function() {
                if ($('#date_type').val() === 'custom' || $(this).attr('id') !== 'date_type') {
                    loadReportData();
                }
            });

            // دالة تحميل بيانات التقرير
            function loadReportData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#reportForm').serialize();

                $.ajax({
                    url: '{{ route('salesReports.employeeComprehensiveProfitsReportAjax') }}',
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
                animateValue('#totalRevenue', 0, data.totals.total_revenue, 1000);
                animateValue('#totalCost', 0, data.totals.total_cost, 1000);
                animateValue('#totalProfit', 0, data.totals.total_profit, 1000);
                animateValue('#profitMargin', 0, data.totals.total_profit_margin, 1000, '%');

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير الأرباح الشامل من ${data.from_date} إلى ${data.to_date}
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.grouped_data, data.totals, data.report_type);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('profitsChart').getContext('2d');

                if (profitsChart) {
                    profitsChart.destroy();
                }

                profitsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                                label: 'الإيرادات (ريال)',
                                data: chartData.revenues,
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                            },
                            {
                                label: 'التكلفة (ريال)',
                                data: chartData.costs,
                                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                            },
                            {
                                label: 'الأرباح (ريال)',
                                data: chartData.profits,
                                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                                borderColor: 'rgba(16, 185, 129, 1)',
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
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
                                        return context.dataset.label + ': ' + formatNumber(context
                                            .parsed.y) + ' ريال';
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
                                        family: 'Cairo'
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            }

            // دالة تحديث محتوى الجدول
            function updateTableBody(groupedData, totals, reportType) {
                let tableHtml = '';
                let counter = 1;
                let grandRevenueTotal = 0;
                let grandCostTotal = 0;
                let grandProfitTotal = 0;

                if (reportType === 'comprehensive') {
                    // عرض البيانات الشاملة مقسمة حسب الأقسام
                    if (groupedData.employees && groupedData.employees.length > 0) {
                        tableHtml += `
                            <tr class="table-section-header">
                                <td colspan="10">
                                    <i class="fas fa-users me-2"></i>
                                    <strong>أرباح الموظفين</strong>
                                </td>
                            </tr>
                        `;

                        groupedData.employees.forEach(employee => {
                            tableHtml += createEmployeeRow(employee, counter++);
                            grandRevenueTotal += employee.total_revenue;
                            grandCostTotal += employee.total_cost;
                            grandProfitTotal += employee.total_profit;
                        });
                    }

                    if (groupedData.clients && groupedData.clients.length > 0) {
                        tableHtml += `
                            <tr class="table-section-header">
                                <td colspan="10">
                                    <i class="fas fa-user-friends me-2"></i>
                                    <strong>أرباح العملاء</strong>
                                </td>
                            </tr>
                        `;

                        groupedData.clients.forEach(client => {
                            tableHtml += createClientRow(client, counter++);
                        });
                    }

                    if (groupedData.products && groupedData.products.length > 0) {
                        tableHtml += `
                            <tr class="table-section-header">
                                <td colspan="10">
                                    <i class="fas fa-boxes me-2"></i>
                                    <strong>أرباح المنتجات</strong>
                                </td>
                            </tr>
                        `;

                        groupedData.products.forEach(product => {
                            tableHtml += createProductRow(product, counter++);
                        });
                    }
                } else {
                    // عرض البيانات حسب نوع التقرير المحدد
                    if (Array.isArray(groupedData)) {
                        groupedData.forEach(item => {
                            if (reportType === 'employee_profits') {
                                tableHtml += createEmployeeRow(item, counter++);
                            } else if (reportType === 'client_profits') {
                                tableHtml += createClientRow(item, counter++);
                            } else if (reportType === 'product_profits') {
                                tableHtml += createProductRow(item, counter++);
                            }
                            grandRevenueTotal += item.total_revenue;
                            grandCostTotal += item.total_cost;
                            grandProfitTotal += item.total_profit;
                        });
                    }
                }

                // صف الإجمالي العام
                tableHtml += `
                    <tr class="table-grand-total">
                        <td colspan="6">
                            <i class="fas fa-chart-bar me-2"></i>
                            <strong>المجموع الكلي</strong>
                        </td>
                        <td class="fw-bold">${formatNumber(totals.total_revenue)}</td>
                        <td class="fw-bold">${formatNumber(totals.total_cost)}</td>
                        <td class="fw-bold">${formatNumber(totals.total_profit)}</td>
                        <td class="fw-bold">${formatNumber(totals.total_profit_margin)}%</td>
                    </tr>
                `;

                $('#reportTableBody').html(tableHtml);
            }

            // دالة إنشاء صف الموظف
            function createEmployeeRow(employee, counter) {
                const profitClass = employee.total_profit >= 0 ? 'text-success' : 'text-danger';
                const profitMarginClass = employee.profit_margin >= 0 ? 'text-success' : 'text-danger';

                return `
                    <tr class="employee-row">
                        <td>${counter}</td>
                        <td>
                            <span class="badge bg-primary">
                                <i class="fas fa-user-tie me-1"></i>${employee.name}
                            </span>
                        </td>
                        <td>-</td>
                        <td>متعدد</td>
                        <td>متعدد</td>
                        <td>${formatNumber(employee.total_quantity)}</td>
                        <td class="text-primary fw-bold">${formatNumber(employee.total_revenue)}</td>
                        <td class="text-danger fw-bold">${formatNumber(employee.total_cost)}</td>
                        <td class="${profitClass} fw-bold">${formatNumber(employee.total_profit)}</td>
                        <td class="${profitMarginClass} fw-bold">${formatNumber(employee.profit_margin)}%</td>
                    </tr>
                `;
            }

            // دالة إنشاء صف العميل
            function createClientRow(client, counter) {
                const profitClass = client.total_profit >= 0 ? 'text-success' : 'text-danger';
                const profitMarginClass = client.profit_margin >= 0 ? 'text-success' : 'text-danger';

                return `
                    <tr class="client-row">
                        <td>${counter}</td>
                        <td>متعدد</td>
                        <td>-</td>
                        <td>
                            <span class="badge bg-info">
                                <i class="fas fa-user me-1"></i>${client.name}
                            </span>
                        </td>
                        <td>متعدد</td>
                        <td>${formatNumber(client.total_quantity)}</td>
                        <td class="text-primary fw-bold">${formatNumber(client.total_revenue)}</td>
                        <td class="text-danger fw-bold">${formatNumber(client.total_cost)}</td>
                        <td class="${profitClass} fw-bold">${formatNumber(client.total_profit)}</td>
                        <td class="${profitMarginClass} fw-bold">${formatNumber(client.profit_margin)}%</td>
                    </tr>
                `;
            }

            // دالة إنشاء صف المنتج
            function createProductRow(product, counter) {
                const profitClass = product.total_profit >= 0 ? 'text-success' : 'text-danger';
                const profitMarginClass = product.profit_margin >= 0 ? 'text-success' : 'text-danger';

                return `
                    <tr class="product-row">
                        <td>${counter}</td>
                        <td>متعدد</td>
                        <td>-</td>
                        <td>متعدد</td>
                        <td>
                            <span class="badge bg-warning">
                                <i class="fas fa-box me-1"></i>${product.name}
                            </span>
                            <br><small class="text-muted">${product.category} | ${product.brand}</small>
                        </td>
                        <td>${formatNumber(product.total_quantity)}</td>
                        <td class="text-primary fw-bold">${formatNumber(product.total_revenue)}</td>
                        <td class="text-danger fw-bold">${formatNumber(product.total_cost)}</td>
                        <td class="${profitClass} fw-bold">${formatNumber(product.total_profit)}</td>
                        <td class="${profitMarginClass} fw-bold">${formatNumber(product.profit_margin)}%</td>
                    </tr>
                `;
            }

            // دالة تنسيق التاريخ للعرض
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('ar-SA', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                });
            }

            // دالة تنسيق الأرقام
            function formatNumber(number) {
                return parseFloat(number).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // دالة الرسوم المتحركة للأرقام
            function animateValue(element, start, end, duration, suffix = '') {
                const obj = $(element);
                const range = end - start;
                const increment = end > start ? 1 : -1;
                const stepTime = Math.abs(Math.floor(duration / range));
                const timer = setInterval(function() {
                    start += increment;
                    obj.text(formatNumber(start) + suffix);
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
                const fileName =
                    `تقرير_الأرباح_الشامل_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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
                $('.btn-group .btn-modern').removeClass('btn-primary-modern active').addClass(
                    'btn-outline-modern');
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