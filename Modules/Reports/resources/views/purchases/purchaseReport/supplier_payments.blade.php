@extends('master')

@section('title')
    تقرير مدفوعات الموردين
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
                        <i class="fas fa-truck me-3"></i>
                        تقرير مدفوعات الموردين
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير مدفوعات الموردين</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon warning">
                        <i class="fas fa-hand-holding-usd"></i>
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
                            <label class="form-label-modern"><i class="fas fa-truck me-2"></i>المورد</label>
                            <select name="supplier" id="supplier" class="form-control select2">
                                <option value="">جميع الموردين</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->trade_name }}-{{ $supplier->code }}</option>
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
                            <label class="form-label-modern"><i class="fas fa-university me-2"></i>الحساب</label>
                            <select name="account" id="account" class="form-control select2">
                                <option value="">جميع الحسابات</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-vault me-2"></i>الخزينة</label>
                            <select name="treasury" id="treasury" class="form-control select2">
                                <option value="">جميع الخزائن</option>
                                @foreach ($treasuries as $treasury)
                                    <option value="{{ $treasury->id }}">{{ $treasury->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-credit-card me-2"></i>وسيلة الدفع</label>
                            <select name="payment_method" id="payment_method" class="form-control select2">
                                <option value="">جميع وسائل الدفع</option>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method['id'] }}">{{ $method['name'] }}</option>
                                @endforeach
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
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stats-value" id="totalPayments">{{ number_format($totals['total_payments'] ?? 0, 2) }}
                    </div>
                    <div class="stats-label">إجمالي مدفوعات الموردين (ريال)</div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="stats-value" id="totalCount">{{ number_format($totals['total_count'] ?? 0, 0) }}</div>
                    <div class="stats-label">عدد العمليات</div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card primary">
                    <div class="stats-icon primary">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="stats-value" id="totalAmount">{{ number_format($totals['total_amount'] ?? 0, 2) }}</div>
                    <div class="stats-label">المجموع الإجمالي (ريال)</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني لمدفوعات الموردين
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="supplierPaymentsChart"></canvas>
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
                    تقرير مدفوعات الموردين من {{ $fromDate->format('d/m/Y') }} إلى {{ $toDate->format('d/m/Y') }}
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
                                <th><i class="fas fa-truck me-2"></i>المورد</th>
                                <th><i class="fas fa-barcode me-2"></i>المرجع</th>
                                <th><i class="fas fa-credit-card me-2"></i>وسيلة الدفع</th>
                                <th><i class="fas fa-money-bill me-2"></i>المبلغ (ريال)</th>
                                <th><i class="fas fa-sticky-note me-2"></i>ملاحظات</th>
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
        let supplierPaymentsChart;

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
                    url: '{{ route('ReportsPurchases.supplierPaymentsData') }}',
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
                animateValue('#totalPayments', 0, data.totals.total_payments, 1000);
                animateValue('#totalAmount', 0, data.totals.total_amount, 1000);
                animateValue('#totalCount', 0, data.totals.total_count, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير مدفوعات الموردين من ${data.from_date} إلى ${data.to_date}
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.grouped_data, data.totals);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('supplierPaymentsChart').getContext('2d');

                if (supplierPaymentsChart) {
                    supplierPaymentsChart.destroy();
                }

                supplierPaymentsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'مدفوعات الموردين (ريال)',
                            data: chartData.payments,
                            backgroundColor: 'rgba(255, 193, 7, 0.7)',
                            borderColor: 'rgba(255, 193, 7, 1)',
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
                                        return context.dataset.label + ': ' + formatNumber(context.parsed.y) + ' ريال';
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
            function updateTableBody(groupedData, totals) {
                let tableHtml = '';
                let counter = 1;
                let grandPaymentsTotal = 0;
                let grandAmountTotal = 0;

                // تكرار عبر المجموعات
                Object.keys(groupedData).forEach(employeeId => {
                    const employeeData = groupedData[employeeId];
                    const employee = employeeData.employee;

                    // صف رأس الموظف
                    tableHtml += `
                        <tr class="table-employee-header">
                            <td colspan="8">
                                <i class="fas fa-user-tie me-2"></i>
                                <strong>${employee.name}</strong>
                                <span class="ms-3">
                                    (مدفوعات الموردين: ${formatNumber(employeeData.total_payments)} ريال |
                                    الإجمالي: ${formatNumber(employeeData.total_amount)} ريال)
                                </span>
                            </td>
                        </tr>
                    `;

                    // عرض المدفوعات
                    if (employeeData.payments && employeeData.payments.length > 0) {
                        employeeData.payments.forEach(payment => {
                            tableHtml += `<tr class="payment-row">`;
                            tableHtml += `<td>${counter++}</td>`;
                            tableHtml += `<td>${employee.name}</td>`;
                            tableHtml += `<td>${formatDate(payment.payment_date)}</td>`;
                            tableHtml += `<td>${payment.purchase_invoice && payment.purchase_invoice.supplier ? payment.purchase_invoice.supplier.trade_name : 'غير محدد'}</td>`;
                            tableHtml += `<td>${payment.reference_number || '--'}</td>`;
                            tableHtml += `<td>${getPaymentMethodName(payment.payment_method)}</td>`;
                            tableHtml += `<td class="text-warning fw-bold">${formatNumber(payment.amount)}</td>`;
                            tableHtml += `<td>${payment.notes || '--'}</td>`;
                            tableHtml += `</tr>`;
                        });
                    }

                    // صف إجمالي الموظف
                    tableHtml += `
                        <tr class="table-employee-total">
                            <td colspan="6">
                                <i class="fas fa-calculator me-2"></i>
                                <strong>مجموع ${employee.name}</strong>
                            </td>
                            <td class="fw-bold">${formatNumber(employeeData.total_amount)}</td>
                            <td>-</td>
                        </tr>
                    `;

                    grandPaymentsTotal += employeeData.total_payments;
                    grandAmountTotal += employeeData.total_amount;
                });

                // صف الإجمالي العام
                tableHtml += `
                    <tr class="table-grand-total">
                        <td colspan="6">
                            <i class="fas fa-chart-bar me-2"></i>
                            <strong>المجموع الكلي</strong>
                        </td>
                        <td class="fw-bold">${formatNumber(grandAmountTotal)}</td>
                        <td>-</td>
                    </tr>
                `;

                $('#reportTableBody').html(tableHtml);
            }

            // دالة الحصول على اسم وسيلة الدفع
            function getPaymentMethodName(method) {
                const methods = {
                    1: 'نقدي',
                    2: 'شيك',
                    3: 'تحويل بنكي',
                    4: 'بطاقة ائتمان'
                };
                return methods[method] || 'غير محدد';
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
                const fileName = `تقرير_مدفوعات_الموردين_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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
        });
    </script>
@endsection
