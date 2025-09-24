@extends('master')

@section('title')
    تقرير كشف حساب العملاء
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
                        <i class="fas fa-file-invoice-dollar me-3"></i>
                        تقرير كشف حساب العملاء
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">التقارير</a>
                            </li>
                            <li class="breadcrumb-item active">كشف حساب العملاء</li>
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
                    <div class="row g-4">
                        <!-- First Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">
                                <i class="fas fa-code-branch me-2"></i>فرع الحسابات
                            </label>
                            <select name="branch" id="branch" class="form-control select2">
                                <option value="">كل الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">
                                <i class="fas fa-file-invoice-dollar me-2"></i>حساب
                            </label>
                            <select name="account" id="account" class="form-control select2">
                                <option value="">الحساب الافتراضي</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">
                                <i class="far fa-calendar-alt me-2"></i>الفترة (أيام)
                            </label>
                            <input type="number" name="days" id="days" class="form-control" value="30" placeholder="عدد الأيام">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">
                                <i class="fas fa-calendar-check me-2"></i>السنة المالية
                            </label>
                            <select name="financial_year[]" id="financial_year" class="form-control select2" multiple>
                                <option value="current">السنة المفتوحة</option>
                                <option value="all">جميع السنوات</option>
                                @for ($year = date('Y'); $year >= date('Y') - 10; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">
                                <i class="fas fa-tags me-2"></i>تصنيف العميل
                            </label>
                            <select name="customer_type" id="customer_type" class="form-control select2">
                                <option value="">الكل</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">
                                <i class="fas fa-user-tie me-2"></i>العميل
                            </label>
                            <select name="customer" id="customer" class="form-control select2">
                                <option value="">الكل</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->trade_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">
                                <i class="fas fa-money-bill-wave me-2"></i>مركز التكلفة
                            </label>
                            <select name="cost_center" id="cost_center" class="form-control select2">
                                <option value="">اختر مركز التكلفة</option>
                                @foreach ($costCenters as $costCenter)
                                    <option value="{{ $costCenter->id }}">{{ $costCenter->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">
                                <i class="fas fa-user-cog me-2"></i>مسؤول مبيعات
                            </label>
                            <select name="sales_manager" id="sales_manager" class="form-control select2">
                                <option value="">الكل</option>
                                @foreach ($salesManagers as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->full_name }}</option>
                                @endforeach
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
                        <button class="btn-modern btn-info-modern" id="exportPdf">
                            <i class="fas fa-file-pdf"></i>
                            تصدير PDF
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
                    <div class="stats-value" id="totalCustomers">0</div>
                    <div class="stats-label">إجمالي العملاء</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stats-value" id="totalEntries">0</div>
                    <div class="stats-label">إجمالي القيود</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="stats-value" id="totalDebit">0</div>
                    <div class="stats-label">إجمالي المدين (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="stats-value" id="totalCredit">0</div>
                    <div class="stats-label">إجمالي الدائن (ريال)</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني للحسابات
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="accountsChart"></canvas>
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
                    كشف حساب العملاء
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0" id="accountStatementTable">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-user me-2"></i>العميل</th>
                                <th><i class="fas fa-bookmark me-2"></i>رقم الحساب</th>
                                <th><i class="fas fa-file-alt me-2"></i>رقم القيد</th>
                                <th><i class="fas fa-calendar me-2"></i>التاريخ</th>
                                <th><i class="fas fa-user-tie me-2"></i>الموظف</th>
                                <th><i class="fas fa-arrow-up me-2"></i>مدين</th>
                                <th><i class="fas fa-arrow-down me-2"></i>دائن</th>
                                <th><i class="fas fa-balance-scale me-2"></i>الرصيد</th>
                                <th><i class="fas fa-sticky-note me-2"></i>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <!-- سيتم تحديث البيانات هنا عبر AJAX -->
                        </tbody>
                        <tfoot id="reportTableFooter">
                            <!-- سيتم تحديث المجاميع هنا عبر AJAX -->
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- No Data Alert -->
        <div class="alert alert-modern alert-info-modern d-none" id="noDataAlert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fa-2x"></i>
                <div>
                    <h6 class="mb-1">لا توجد بيانات</h6>
                    <p class="mb-0">لا توجد قيود محاسبية مطابقة للفلاتر المحددة. جرب تعديل معايير البحث.</p>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        let accountsChart;

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
                adaptContainerCssClass: function(clazz) {
                    return clazz;
                },
                adaptDropdownCssClass: function(clazz) {
                    return clazz;
                },
                minimumInputLength: 0,
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

            // التعامل مع تصفية التقرير
            $('#filterBtn').click(function() {
                $(this).addClass('loading');
                loadReportData();
            });

            // إعادة تعيين الفلاتر
            $('#resetBtn').click(function() {
                $('#reportForm')[0].reset();
                $('.select2').val(null).trigger('change');
                $('#days').val(30);
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

            // التعامل مع تصدير PDF
            $('#exportPdf').click(function() {
                exportToPDF();
            });

            // تحديث البيانات عند تغيير أي فلتر
            $('.select2, #reportForm input').on('change', function() {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(function() {
                    loadReportData();
                }, 500);
            });

            // دالة تحميل بيانات التقرير
            function loadReportData() {
                $('.loading-overlay').fadeIn();
                $('#noDataAlert').addClass('d-none');
                $('#reportContainer').removeClass('d-none');
                $('#filterBtn').prop('disabled', true);

                const formData = $('#reportForm').serialize();

                $.ajax({
                    url: '{{ route('ClientReport.customerAccountStatementAjax') }}',
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

                        // إظهار رسالة عدم وجود بيانات
                        $('#reportContainer').addClass('d-none');
                        $('#noDataAlert').removeClass('d-none');
                    }
                });
            }

            // دالة تحديث عرض التقرير
            function updateReportDisplay(data) {
                console.log('Response data:', data); // للتشخيص
                
                // التحقق من وجود البيانات - يمكن أن تكون في journalEntries أو entries
                const entries = data.journalEntries || data.entries || [];
                
                if (!data || entries.length === 0) {
                    $('#reportContainer').addClass('d-none');
                    $('#noDataAlert').removeClass('d-none');
                    updateStatistics(0, 0, 0, 0);
                    return;
                }

                // تحديث الإجماليات مع تأثير العد التصاعدي
                // استخدام القيم الخام للأنيميشن والقيم المنسقة للعرض
                const totalDebitRaw = data.totals.total_debit_raw || data.totals.total_debit || 0;
                const totalCreditRaw = data.totals.total_credit_raw || data.totals.total_credit || 0;
                
                animateValue('#totalCustomers', 0, data.totals.total_customers || 0, 1000);
                animateValue('#totalEntries', 0, data.totals.total_entries || 0, 1000);
                animateValue('#totalDebit', 0, totalDebitRaw, 1000);
                animateValue('#totalCredit', 0, totalCreditRaw, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    كشف حساب العملاء - ${data.totals.total_customers || 0} عميل، ${data.totals.total_entries || 0} قيد
                `);

                // تحديث الرسم البياني
                if (data.chart_data) {
                    updateChart(data.chart_data);
                }

                // تحديث جدول البيانات
                updateTableBody(entries);
                updateTableFooter(data.totals);

                $('#reportContainer').removeClass('d-none');
                $('#noDataAlert').addClass('d-none');
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('accountsChart').getContext('2d');

                if (accountsChart) {
                    accountsChart.destroy();
                }

                accountsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                                label: 'المدين (ريال)',
                                data: chartData.debit,
                                backgroundColor: 'rgba(234, 84, 85, 0.7)',
                                borderColor: 'rgba(234, 84, 85, 1)',
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                            },
                            {
                                label: 'الدائن (ريال)',
                                data: chartData.credit,
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
            function updateTableBody(journalEntries) {
                let tableHtml = '';
                let counter = 1;
                let runningBalance = 0;
                let currentClient = null;

                if (journalEntries && journalEntries.length > 0) {
                    journalEntries.forEach((entry) => {
                        // التحقق من تغيير العميل
                        if (!currentClient || (entry.client && currentClient.id !== entry.client.id)) {
                            currentClient = entry.client;
                            
                            // صف رأس العميل
                            tableHtml += `
                                <tr class="table-employee-header">
                                    <td colspan="10">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle me-2"></i>
                                            <strong>${entry.client ? entry.client.trade_name : 'غير متوفر'}</strong>
                                            ${entry.client && entry.client.code ? `<span class="badge bg-secondary ms-2">${entry.client.code}</span>` : ''}
                                        </div>
                                    </td>
                                </tr>
                            `;

                            // صف الرصيد السابق
                            tableHtml += `
                                <tr class="table-employee-total">
                                    <td>${counter++}</td>
                                    <td colspan="4"><strong>الرصيد السابق</strong></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td class="${runningBalance >= 0 ? 'text-success' : 'text-danger'} fw-bold">
                                        ${formatNumber(Math.abs(runningBalance))} ${runningBalance >= 0 ? '(مدين)' : '(دائن)'}
                                    </td>
                                    <td>-</td>
                                </tr>
                            `;
                        }

                        // تفاصيل القيود
                        if (entry.details && entry.details.length > 0) {
                            entry.details.forEach((detail) => {
                                // استخدام القيم الخام للحسابات والقيم المنسقة للعرض
                                const debitRaw = detail.debit_raw || 0;
                                const creditRaw = detail.credit_raw || 0;
                                const debitFormatted = detail.debit || formatNumber(debitRaw);
                                const creditFormatted = detail.credit || formatNumber(creditRaw);
                                
                                runningBalance += debitRaw - creditRaw;

                                tableHtml += `<tr>`;
                                tableHtml += `<td>${counter++}</td>`;
                                tableHtml += `<td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user me-2 text-primary"></i>
                                        ${entry.client ? entry.client.trade_name : 'غير متوفر'}
                                    </div>
                                </td>`;
                                tableHtml += `<td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-bookmark me-2 text-info"></i>
                                        ${detail.account ? detail.account.name : 'غير متوفر'}
                                    </div>
                                </td>`;
                                tableHtml += `<td>
                                    <span class="badge bg-info">${entry.reference_number || 'غير متوفر'}</span>
                                </td>`;
                                tableHtml += `<td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt me-2 text-success"></i>
                                        ${formatDate(entry.date)}
                                    </div>
                                </td>`;
                                tableHtml += `<td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tie me-2 text-warning"></i>
                                        ${entry.created_by_employee ? entry.created_by_employee.name : 'غير متوفر'}
                                    </div>
                                </td>`;
                                tableHtml += `<td class="${debitRaw > 0 ? 'text-danger fw-bold' : ''}">
                                    ${debitRaw > 0 ? debitFormatted : '-'}
                                </td>`;
                                tableHtml += `<td class="${creditRaw > 0 ? 'text-success fw-bold' : ''}">
                                    ${creditRaw > 0 ? creditFormatted : '-'}
                                </td>`;
                                tableHtml += `<td class="${runningBalance >= 0 ? 'text-success' : 'text-danger'} fw-bold">
                                    ${formatNumber(Math.abs(runningBalance))} ${runningBalance >= 0 ? '(مدين)' : '(دائن)'}
                                </td>`;
                                tableHtml += `<td>${detail.description || detail.notes || '--'}</td>`;
                                tableHtml += `</tr>`;
                            });
                        }

                        // صف إجمالي العميل
                        const clientTotal = entry.total_debit - entry.total_credit;
                        tableHtml += `
                            <tr class="table-employee-total">
                                <td colspan="6">
                                    <i class="fas fa-calculator me-2"></i>
                                    <strong>مجموع ${entry.client ? entry.client.trade_name : 'العميل'}</strong>
                                </td>
                                <td class="text-danger fw-bold">${formatNumber(entry.total_debit)}</td>
                                <td class="text-success fw-bold">${formatNumber(entry.total_credit)}</td>
                                <td class="${clientTotal >= 0 ? 'text-success' : 'text-danger'} fw-bold">
                                    ${formatNumber(Math.abs(clientTotal))} ${clientTotal >= 0 ? '(مدين)' : '(دائن)'}
                                </td>
                                <td>-</td>
                            </tr>
                        `;

                        // صف فاصل بين العملاء
                        tableHtml += `
                            <tr style="background: var(--gray-100); height: 10px;">
                                <td colspan="10"></td>
                            </tr>
                        `;
                    });
                } else {
                    tableHtml = `
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">لا توجد بيانات</h5>
                                    <p class="text-muted mb-0">لا توجد قيود محاسبية مطابقة للفلاتر المحددة</p>
                                </div>
                            </td>
                        </tr>
                    `;
                }

                $('#reportTableBody').html(tableHtml);
            }

            // دالة تحديث تذييل الجدول
            function updateTableFooter(totals) {
                const finalBalance = totals.total_debit - totals.total_credit;
                const footerHtml = `
                    <tr class="table-grand-total">
                        <td colspan="6">
                            <i class="fas fa-chart-bar me-2"></i>
                            <strong>المجموع الكلي</strong>
                        </td>
                        <td class="text-danger fw-bold">${formatNumber(totals.total_debit)}</td>
                        <td class="text-success fw-bold">${formatNumber(totals.total_credit)}</td>
                        <td class="${finalBalance >= 0 ? 'text-success' : 'text-danger'} fw-bold">
                            ${formatNumber(Math.abs(finalBalance))} ${finalBalance >= 0 ? '(مدين)' : '(دائن)'}
                        </td>
                        <td>-</td>
                    </tr>
                `;

                $('#reportTableFooter').html(footerHtml);
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

            // دالة تحديث الإحصائيات
            function updateStatistics(customers, entries, debit, credit) {
                animateValue('#totalCustomers', 0, customers, 1000);
                animateValue('#totalEntries', 0, entries, 1000);
                animateValue('#totalDebit', 0, debit, 1000);
                animateValue('#totalCredit', 0, credit, 1000);
            }

            // دالة تصدير إكسل
            function exportToExcel() {
                showAlert('جاري تصدير الملف...', 'info');

                const table = document.querySelector('#accountStatementTable');
                const wb = XLSX.utils.table_to_book(table, {
                    raw: false,
                    cellDates: true
                });

                const today = new Date();
                const fileName = `كشف_حساب_العملاء_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

                XLSX.writeFile(wb, fileName);
                showAlert('تم تصدير الملف بنجاح!', 'success');
            }

            // دالة تصدير PDF
            function exportToPDF() {
                showAlert('جاري إنشاء ملف PDF...', 'info');

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('l', 'mm', 'a4');
                const title = "تقرير كشف حساب العملاء";
                const date = new Date().toLocaleDateString('ar-SA');

                // Add title and date
                doc.setFontSize(16);
                doc.setTextColor(40);
                doc.text(title, 140, 15, null, null, 'center');

                doc.setFontSize(10);
                doc.setTextColor(100);
                doc.text("تاريخ التقرير: " + date, 140, 22, null, null, 'center');

                // Add table
                html2canvas(document.getElementById('accountStatementTable')).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const imgWidth = 280;
                    const pageHeight = doc.internal.pageSize.height;
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    let heightLeft = imgHeight;
                    let position = 30;

                    doc.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;

                    while (heightLeft >= 0) {
                        position = heightLeft - imgHeight;
                        doc.addPage();
                        doc.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                        heightLeft -= pageHeight;
                    }

                    const fileName = `كشف_حساب_العملاء_${new Date().toISOString().slice(0, 10)}.pdf`;
                    doc.save(fileName);
                    showAlert('تم إنشاء ملف PDF بنجاح!', 'success');
                }).catch(() => {
                    showAlert('حدث خطأ في إنشاء ملف PDF', 'danger');
                });
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