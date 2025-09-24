@extends('master')

@section('title')
    تقرير شحن الأرصدة
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
                        <i class="fas fa-wallet me-3"></i>
                        تقرير شحن الأرصدة
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير شحن الأرصدة</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-credit-card"></i>
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
                            <label class="form-label-modern"><i class="fas fa-user me-2"></i>العميل</label>
                            <select name="customer" id="customer" class="form-control select2">
                                <option value="">جميع العملاء</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->trade_name }} - {{ $client->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-tags me-2"></i>نوع الرصيد</label>
                            <select name="income_type" id="income_type" class="form-control select2">
                                <option value="">جميع الأنواع</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
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
                            <label class="form-label-modern"><i class="fas fa-toggle-on me-2"></i>الحالة</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="">جميع الحالات</option>
                                <option value="1">نشط</option>
                                <option value="0">غير نشط</option>
                            </select>
                        </div>

                        <!-- Second Row -->
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
                            <input type="date" name="date_from" id="date_from" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6" id="custom_to_date">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>إلى تاريخ</label>
                            <input type="date" name="date_to" id="date_to" class="form-control">
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-lg-3 col-md-12 align-self-end">
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
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stats-value" id="totalCharges">0</div>
                    <div class="stats-label">إجمالي شحن الأرصدة (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card primary">
                    <div class="stats-icon primary">
                        <i class="fas fa-battery-three-quarters"></i>
                    </div>
                    <div class="stats-value" id="totalRemaining">0</div>
                    <div class="stats-label">إجمالي الرصيد المتبقي (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-value" id="activeCharges">0</div>
                    <div class="stats-label">الأرصدة النشطة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="stats-value" id="totalCount">0</div>
                    <div class="stats-label">عدد عمليات الشحن</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني لشحن الأرصدة
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="chargesChart"></canvas>
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
                    تقرير شحن الأرصدة
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-user me-2"></i>العميل</th>
                                <th><i class="fas fa-tags me-2"></i>نوع الرصيد</th>
                                <th><i class="fas fa-money-bill me-2"></i>القيمة (ريال)</th>
                                <th><i class="fas fa-battery-quarter me-2"></i>المتبقي (ريال)</th>
                                <th><i class="fas fa-calendar-check me-2"></i>تاريخ البدء</th>
                                <th><i class="fas fa-calendar-times me-2"></i>تاريخ الانتهاء</th>
                                <th><i class="fas fa-building me-2"></i>الفرع</th>
                                <th><i class="fas fa-toggle-on me-2"></i>الحالة</th>
                                <th><i class="fas fa-sticky-note me-2"></i>الوصف</th>
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
        let chargesChart;

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

            // التعامل مع تغيير نوع التاريخ
            $('#date_type').change(function() {
                updateDateRange();
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
                updateDateRange();
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

            // تحديث البيانات عند تغيير التواريخ
            $('#date_from, #date_to').on('change', function() {
                if ($('#date_type').val() === 'custom') {
                    loadReportData();
                }
            });

            // دالة تحديث نطاق التاريخ
            function updateDateRange() {
                const dateType = $('#date_type').val();
                const today = new Date();
                let fromDate, toDate;

                switch(dateType) {
                    case 'today':
                        fromDate = toDate = new Date();
                        break;
                    case 'yesterday':
                        fromDate = toDate = new Date(today.getTime() - 24 * 60 * 60 * 1000);
                        break;
                    case 'this_week':
                        const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
                        fromDate = startOfWeek;
                        toDate = new Date();
                        break;
                    case 'last_week':
                        const lastWeekStart = new Date(today.setDate(today.getDate() - today.getDay() - 7));
                        const lastWeekEnd = new Date(today.setDate(today.getDate() - today.getDay() - 1));
                        fromDate = lastWeekStart;
                        toDate = lastWeekEnd;
                        break;
                    case 'this_month':
                        fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                        toDate = new Date();
                        break;
                    case 'last_month':
                        fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        toDate = new Date(today.getFullYear(), today.getMonth(), 0);
                        break;
                    case 'this_year':
                        fromDate = new Date(today.getFullYear(), 0, 1);
                        toDate = new Date();
                        break;
                    case 'last_year':
                        fromDate = new Date(today.getFullYear() - 1, 0, 1);
                        toDate = new Date(today.getFullYear() - 1, 11, 31);
                        break;
                    default:
                        return; // custom - لا نغير التواريخ
                }

                if (fromDate && toDate) {
                    $('#date_from').val(formatDateForInput(fromDate));
                    $('#date_to').val(formatDateForInput(toDate));
                }
            }

            // دالة تحميل بيانات التقرير
            function loadReportData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#reportForm').serialize();

                $.ajax({
                    url: '{{ route('ClientReport.rechargeBalancesReportAjax') }}',
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
                animateValue('#totalCharges', 0, data.totals.total_charges, 1000);
                animateValue('#totalRemaining', 0, data.totals.total_remaining, 1000);
                animateValue('#activeCharges', 0, data.totals.active_charges, 1000);
                animateValue('#totalCount', 0, data.totals.total_count, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير شحن الأرصدة من ${data.from_date} إلى ${data.to_date}
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.charges);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('chargesChart').getContext('2d');

                if (chargesChart) {
                    chargesChart.destroy();
                }

                chargesChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'شحن الأرصدة',
                            data: chartData.values,
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(139, 92, 246, 0.8)',
                                'rgba(236, 72, 153, 0.8)'
                            ],
                            borderColor: [
                                'rgba(16, 185, 129, 1)',
                                'rgba(59, 130, 246, 1)',
                                'rgba(245, 158, 11, 1)',
                                'rgba(239, 68, 68, 1)',
                                'rgba(139, 92, 246, 1)',
                                'rgba(236, 72, 153, 1)'
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
                                        const percentage = ((context.parsed / chartData.total) * 100).toFixed(1);
                                        return context.label + ': ' + formatNumber(context.parsed) + ' ريال (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // دالة تحديث محتوى الجدول
            function updateTableBody(charges) {
                let tableHtml = '';

                if (charges.length === 0) {
                    tableHtml = `
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد بيانات متاحة</h5>
                                <p class="text-muted">لا توجد عمليات شحن أرصدة تطابق المعايير المحددة</p>
                            </td>
                        </tr>
                    `;
                } else {
                    charges.forEach((charge, index) => {
                        const statusBadge = charge.status == 1
                            ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>نشط</span>'
                            : '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>غير نشط</span>';

                        const progressPercentage = charge.value > 0 ? ((charge.remaining / charge.value) * 100).toFixed(1) : 0;
                        const progressColor = progressPercentage > 50 ? 'success' : progressPercentage > 25 ? 'warning' : 'danger';

                        tableHtml += `
                            <tr class="charge-row">
                                <td>${index + 1}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary-subtle rounded-circle me-2">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">${charge.client ? charge.client.trade_name : 'غير محدد'}</div>
                                            <small class="text-muted">${charge.client ? charge.client.code : '--'}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="fas fa-tag me-1"></i>
                                        ${charge.balance_type ? charge.balance_type.name : 'غير محدد'}
                                    </span>
                                </td>
                                <td class="fw-bold text-success">${formatNumber(charge.value)}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 me-2">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-${progressColor}"
                                                     style="width: ${progressPercentage}%"></div>
                                            </div>
                                        </div>
                                        <span class="fw-bold text-${progressColor}">${formatNumber(charge.remaining)}</span>
                                    </div>
                                </td>
                                <td>${charge.start_date ? formatDate(charge.start_date) : '--'}</td>
                                <td>${charge.end_date ? formatDate(charge.end_date) : '--'}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="fas fa-building me-1"></i>
                                        ${charge.client && charge.client.branch ? charge.client.branch.name : 'غير محدد'}
                                    </span>
                                </td>
                                <td>${statusBadge}</td>
                                <td>
                                    <span class="text-muted" title="${charge.description || '--'}">
                                        ${charge.description ? (charge.description.length > 30 ? charge.description.substring(0, 30) + '...' : charge.description) : '--'}
                                    </span>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#reportTableBody').html(tableHtml);
            }

            // دالة تنسيق التاريخ للإدخال
            function formatDateForInput(date) {
                return date.toISOString().split('T')[0];
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
                const fileName = `تقرير_شحن_الأرصدة_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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

            // تحديث نطاق التاريخ الأولي
            updateDateRange();
        });
    </script>
@endsection