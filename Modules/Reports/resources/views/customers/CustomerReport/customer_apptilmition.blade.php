@extends('master')

@section('title')
    تقرير مواعيد العملاء
@stop

@section('css')
    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">

    <style>
        /* نفس الـ CSS من تقرير الأقساط مع تعديلات للمواعيد */

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

        /* تنسيق الصفوف الرئيسية للمواعيد */
        .appointment-main-row.has-details {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .appointment-main-row.has-details:hover {
            background-color: #f8f9fa;
            border-left-color: #0d6efd;
            transform: translateX(-2px);
        }

        /* تحسين الحالات */
        .status-badge {
            padding: 0.5rem 0.875rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
        }

        .status-completed {
            background: linear-gradient(135deg, #d1e7dd 0%, #a3cfbb 100%);
            color: #0f5132;
            border-color: #badbcc;
        }

        .status-pending {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #664d03;
            border-color: #ffdf7e;
        }

        .status-ignored {
            background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
            color: #721c24;
            border-color: #f5c2c7;
        }

        .status-rescheduled {
            background: linear-gradient(135deg, #d3edfa 0%, #9eeaf9 100%);
            color: #055160;
            border-color: #b8daff;
        }

        /* تنسيق ملاحظات المواعيد */
        .appointment-notes-section {
            margin-top: 1rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 0.75rem;
            border: 2px solid #dee2e6;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
        }

        .appointment-notes-title {
            color: #495057;
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* تحسين جدول الملاحظات */
        .appointment-notes-table {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid #e9ecef;
        }

        .appointment-notes-table thead th {
            background: linear-gradient(135deg, #495057 0%, #343a40 100%);
            color: white;
            font-weight: 700;
            font-size: 0.8rem;
            border: none;
            padding: 1rem 0.75rem;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .appointment-notes-table tbody td {
            font-size: 0.85rem;
            padding: 0.875rem 0.75rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
            transition: background-color 0.2s ease;
        }

        .appointment-notes-table tbody .note-row:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* تحسين عرض التاريخ والوقت */
        .datetime-display {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .date-part {
            font-weight: 700;
            color: #495057;
            font-size: 0.9rem;
        }

        .time-part {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        /* تحسين عرض الوصف */
        .description-display {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .description-full {
            white-space: normal;
            max-width: none;
        }

        /* تحسين النوتس */
        .note-content {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.75rem;
            margin: 0.5rem 0;
            position: relative;
        }

        .note-meta {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* تحسين الأيقونات */
        .appointment-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            margin-left: 0.75rem;
        }

        .icon-completed { background: #28a745; }
        .icon-pending { background: #ffc107; color: #000; }
        .icon-ignored { background: #dc3545; }
        .icon-rescheduled { background: #17a2b8; }

        /* تحسين الكروت */
        .appointment-details-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 0.5rem;
            margin: 0.5rem;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* تحسين العرض للجوال */
        @media (max-width: 768px) {
            .appointment-notes-section {
                padding: 1rem;
                margin-top: 0.5rem;
            }

            .appointment-notes-table thead th {
                padding: 0.75rem 0.5rem;
                font-size: 0.7rem;
            }

            .appointment-notes-table tbody td {
                padding: 0.75rem 0.5rem;
                font-size: 0.8rem;
            }

            .appointment-details-card {
                margin: 0.25rem;
            }

            .description-display {
                max-width: 150px;
            }
        }

        /* باقي الـ CSS من تقرير الأقساط */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        .fade-in.animate__animated {
            opacity: 1;
            transform: translateY(0);
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* استيراد باقي الـ CSS من ملف report.css */
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
                        <i class="fas fa-calendar-check me-3"></i>
                        تقرير مواعيد العملاء
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير مواعيد العملاء</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-calendar-check"></i>
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
                            <select name="client" id="client" class="form-control select2">
                                <option value="">جميع العملاء</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->trade_name ?? $client->first_name . ' ' . $client->last_name }}-{{ $client->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

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
                            <label class="form-label-modern"><i class="fas fa-user-plus me-2"></i>منشئ الموعد</label>
                            <select name="created_by" id="created_by" class="form-control select2">
                                <option value="">جميع المستخدمين</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-check-circle me-2"></i>حالة الموعد</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="">جميع الحالات</option>
                                <option value="pending">تم جدولته</option>
                                <option value="completed">تم</option>
                                <option value="ignored">صرف النظر عنه</option>
                                <option value="rescheduled">تم جدولته مجدداً</option>
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

                        <!-- Options -->
                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-upcoming-only" name="show_upcoming_only" class="form-check-input">
                                <label for="show-upcoming-only" class="form-check-label">
                                    <i class="fas fa-arrow-up me-2"></i>المواعيد القادمة فقط
                                </label>
                            </div>
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
                <div class="stats-card primary">
                    <div class="stats-icon primary">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stats-value" id="totalAppointments">0</div>
                    <div class="stats-label">إجمالي المواعيد</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-value" id="completedAppointments">0</div>
                    <div class="stats-label">المواعيد المكتملة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-value" id="pendingAppointments">0</div>
                    <div class="stats-label">المواعيد المنتظرة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stats-value" id="todayAppointments">0</div>
                    <div class="stats-label">مواعيد اليوم</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني لحالات المواعيد
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="appointmentChart"></canvas>
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
                    تقرير مواعيد العملاء
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-heading me-2"></i>عنوان الموعد</th>
                                <th><i class="fas fa-user me-2"></i>اسم العميل</th>
                                <th><i class="fas fa-user-tie me-2"></i>الموظف</th>
                                <th><i class="fas fa-calendar-alt me-2"></i>تاريخ الموعد</th>
                                <th><i class="fas fa-check-circle me-2"></i>حالة الموعد</th>
                                <th><i class="fas fa-align-left me-2"></i>الوصف</th>
                                <th><i class="fas fa-user-plus me-2"></i>منشئ الموعد</th>
                                <th><i class="fas fa-sticky-note me-2"></i>الملاحظات</th>
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
        let appointmentChart;

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
                minimumInputLength: 0,
                closeOnSelect: true,
                selectOnClose: false
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
                    url: '{{ route('ClientReport.customerAppointmentsAjax') }}',
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
                animateValue('#totalAppointments', 0, data.totals.total_appointments, 1000);
                animateValue('#completedAppointments', 0, data.totals.completed_appointments, 1000);
                animateValue('#pendingAppointments', 0, data.totals.pending_appointments, 1000);
                animateValue('#todayAppointments', 0, data.totals.today_appointments, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير مواعيد العملاء
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.appointments, data.totals);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('appointmentChart').getContext('2d');

                if (appointmentChart) {
                    appointmentChart.destroy();
                }

                appointmentChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['مكتمل', 'منتظر', 'ملغى', 'معاد جدولته'],
                        datasets: [{
                            data: [
                                chartData.completed || 0,
                                chartData.pending || 0,
                                chartData.ignored || 0,
                                chartData.rescheduled || 0
                            ],
                            backgroundColor: [
                                '#28a745',
                                '#ffc107',
                                '#dc3545',
                                '#17a2b8'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
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
                                        return context.label + ': ' + context.parsed + ' موعد';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // دالة تحديث محتوى الجدول
            function updateTableBody(appointments, totals) {
                let tableHtml = '';

                if (appointments && appointments.length > 0) {
                    appointments.forEach((appointment, index) => {
                        const statusClass = getStatusClass(appointment.status);
                        const statusText = appointment.status_text;
                        const hasNotes = appointment.notes && appointment.notes.length > 0;

                        // الصف الرئيسي للموعد
                        tableHtml += `<tr class="appointment-main-row ${hasNotes ? 'has-details' : ''}" ${hasNotes ? 'data-bs-toggle="collapse" data-bs-target="#notes-' + appointment.id + '"' : ''} style="${hasNotes ? 'cursor: pointer;' : ''}">`;

                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                ${hasNotes ? `<i class="fas fa-chevron-down me-2 text-primary collapse-icon" id="icon-${appointment.id}"></i>` : '<span class="me-4"></span>'}
                                ${index + 1}
                            </div>
                        </td>`;

                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <div class="appointment-icon ${getIconClass(appointment.status)}">
                                    <i class="${getStatusIcon(appointment.status)}"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">${appointment.title || 'موعد'}</div>
                                    <small class="text-muted">#${appointment.id}</small>
                                </div>
                            </div>
                        </td>`;

                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">${appointment.client_name || 'غير محدد'}</div>
                                    <small class="text-muted">${appointment.client_code || ''}</small>
                                </div>
                            </div>
                        </td>`;

                        tableHtml += `<td>${appointment.employee_name ? `<span class="badge bg-warning">${appointment.employee_name}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;

                        tableHtml += `<td>
                            <div class="datetime-display">
                                <div class="date-part">${formatDate(appointment.appointment_date)}</div>
                                <div class="time-part">${formatTime(appointment.appointment_date)}</div>
                            </div>
                        </td>`;

                        tableHtml += `<td><span class="status-badge ${statusClass}">${statusText}</span></td>`;

                        tableHtml += `<td>
                            <div class="description-display" title="${appointment.description || ''}">
                                ${appointment.description || 'لا يوجد وصف'}
                            </div>
                        </td>`;

                        tableHtml += `<td><span class="badge bg-info">${appointment.created_by || 'غير محدد'}</span></td>`;

                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                ${hasNotes ? `<span class="badge bg-secondary">${appointment.notes_count} ملاحظة</span>` : '<span class="text-muted">لا توجد</span>'}
                            </div>
                        </td>`;

                        tableHtml += `</tr>`;

                        // صف الملاحظات القابل للطي - فقط إذا كانت هناك ملاحظات
                        if (hasNotes) {
                            tableHtml += `<tr class="collapse" id="notes-${appointment.id}">`;
                            tableHtml += `<td colspan="9" class="p-0">`;
                            tableHtml += `<div class="appointment-details-card">`;

                            // معلومات الموعد الأساسية
                            tableHtml += `<div class="row g-3 p-3 mb-3">`;

                            // تفاصيل الموعد
                            tableHtml += `<div class="col-md-6">`;
                            tableHtml += `<div class="detail-section">`;
                            tableHtml += `<h6 class="detail-title"><i class="fas fa-calendar-check me-2"></i>تفاصيل الموعد</h6>`;
                            tableHtml += `<div class="detail-item"><strong>العنوان:</strong> ${appointment.title || 'غير محدد'}</div>`;
                            tableHtml += `<div class="detail-item"><strong>الوصف:</strong> ${appointment.description || 'لا يوجد وصف'}</div>`;
                            tableHtml += `<div class="detail-item"><strong>التاريخ والوقت:</strong> ${formatDateTime(appointment.appointment_date)}</div>`;
                            tableHtml += `<div class="detail-item"><strong>الحالة:</strong> <span class="status-badge ${statusClass}">${statusText}</span></div>`;
                            tableHtml += `<div class="detail-item"><strong>تاريخ الإنشاء:</strong> ${formatDate(appointment.created_at)}</div>`;
                            tableHtml += `</div>`;
                            tableHtml += `</div>`;

                            // معلومات أخرى
                            tableHtml += `<div class="col-md-6">`;
                            tableHtml += `<div class="detail-section">`;
                            tableHtml += `<h6 class="detail-title"><i class="fas fa-info-circle me-2"></i>معلومات إضافية</h6>`;
                            tableHtml += `<div class="detail-item"><strong>العميل:</strong> ${appointment.client_name || 'غير محدد'}</div>`;
                            tableHtml += `<div class="detail-item"><strong>الموظف:</strong> ${appointment.employee_name || 'غير محدد'}</div>`;
                            tableHtml += `<div class="detail-item"><strong>الفرع:</strong> ${appointment.branch || 'غير محدد'}</div>`;
                            tableHtml += `<div class="detail-item"><strong>منشئ الموعد:</strong> ${appointment.created_by || 'غير محدد'}</div>`;
                            tableHtml += `<div class="detail-item"><strong>عدد الملاحظات:</strong> ${appointment.notes_count}</div>`;
                            tableHtml += `</div>`;
                            tableHtml += `</div>`;

                            tableHtml += `</div>`; // end main info row

                            // عرض الملاحظات
                            tableHtml += `<div class="appointment-notes-section">`;
                            tableHtml += `<div class="d-flex justify-content-between align-items-center mb-3">`;
                            tableHtml += `<h6 class="appointment-notes-title mb-0">
                                <i class="fas fa-sticky-note me-2"></i>
                                ملاحظات الموعد (${appointment.notes.length})
                            </h6>`;
                            tableHtml += `</div>`;

                            tableHtml += `<div class="table-responsive">`;
                            tableHtml += `<table class="table table-sm appointment-notes-table">`;
                            tableHtml += `<thead>`;
                            tableHtml += `<tr>`;
                            tableHtml += `<th style="width: 60px;">#</th>`;
                            tableHtml += `<th>المحتوى</th>`;
                            tableHtml += `<th style="width: 150px;">كاتب الملاحظة</th>`;
                            tableHtml += `<th style="width: 150px;">تاريخ الإضافة</th>`;
                            tableHtml += `</tr>`;
                            tableHtml += `</thead>`;
                            tableHtml += `<tbody>`;

                            appointment.notes.forEach((note, noteIndex) => {
                                tableHtml += `<tr class="note-row">`;
                                tableHtml += `<td>
                                    <span class="badge bg-secondary">${noteIndex + 1}</span>
                                </td>`;
                                tableHtml += `<td>
                                    <div class="note-content">
                                        ${note.content || 'لا يوجد محتوى'}
                                    </div>
                                </td>`;
                                tableHtml += `<td>
                                    <span class="badge bg-info">${note.created_by || 'غير محدد'}</span>
                                </td>`;
                                tableHtml += `<td>
                                    <small class="text-muted">${formatDateTime(note.created_at)}</small>
                                </td>`;
                                tableHtml += `</tr>`;
                            });

                            tableHtml += `</tbody>`;
                            tableHtml += `</table>`;
                            tableHtml += `</div>`;
                            tableHtml += `</div>`; // end appointment-notes-section

                            tableHtml += `</div>`; // end appointment-details-card
                            tableHtml += `</td>`;
                            tableHtml += `</tr>`;
                        }
                    });

                    // إضافة صف الإجمالي العام
                    tableHtml += `
                        <tr class="table-grand-total">
                            <td colspan="7">
                                <i class="fas fa-chart-bar me-2"></i>
                                <strong>المجموع الكلي</strong>
                            </td>
                            <td class="fw-bold">${appointments.length} موعد</td>
                            <td></td>
                        </tr>
                    `;
                } else {
                    tableHtml = `
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">لا توجد مواعيد مطابقة للفلاتر المحددة</h5>
                                    <p class="text-muted mb-3">جرب تغيير الفلاتر أو إعادة تعيينها للحصول على نتائج</p>
                                    <button class="btn btn-outline-primary" onclick="$('#resetBtn').click();">
                                        <i class="fas fa-refresh me-2"></i>إعادة تعيين الفلاتر
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }

                $('#reportTableBody').html(tableHtml);

                // إضافة tooltip للعناصر التي تحتاج إليه
                $('[title]').tooltip();
            }

            // دالة الحصول على كلاس حالة الموعد
            function getStatusClass(status) {
                switch (status) {
                    case 2: // completed
                        return 'status-completed';
                    case 1: // pending
                        return 'status-pending';
                    case 3: // ignored
                        return 'status-ignored';
                    case 4: // rescheduled
                        return 'status-rescheduled';
                    default:
                        return 'status-pending';
                }
            }

            // دالة الحصول على كلاس أيقونة الحالة
            function getIconClass(status) {
                switch (status) {
                    case 2: // completed
                        return 'icon-completed';
                    case 1: // pending
                        return 'icon-pending';
                    case 3: // ignored
                        return 'icon-ignored';
                    case 4: // rescheduled
                        return 'icon-rescheduled';
                    default:
                        return 'icon-pending';
                }
            }

            // دالة الحصول على أيقونة الحالة
            function getStatusIcon(status) {
                switch (status) {
                    case 2: // completed
                        return 'fas fa-check';
                    case 1: // pending
                        return 'fas fa-clock';
                    case 3: // ignored
                        return 'fas fa-times';
                    case 4: // rescheduled
                        return 'fas fa-calendar-alt';
                    default:
                        return 'fas fa-clock';
                }
            }

            // دالة تنسيق التاريخ للعرض
            function formatDate(dateString) {
                if (!dateString) return 'غير محدد';
                const date = new Date(dateString);
                return date.toLocaleDateString('ar-SA', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                });
            }

            // دالة تنسيق الوقت للعرض
            function formatTime(dateString) {
                if (!dateString) return 'غير محدد';
                const date = new Date(dateString);
                return date.toLocaleTimeString('ar-SA', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // دالة تنسيق التاريخ والوقت معاً
            function formatDateTime(dateString) {
                if (!dateString) return 'غير محدد';
                const date = new Date(dateString);
                return date.toLocaleString('ar-SA', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
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
                    obj.text(start);
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
                const fileName = `تقرير_مواعيد_العملاء_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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

            // إضافة مستمع لتدوير أيقونة السهم عند فتح/إغلاق التفاصيل
            $(document).on('shown.bs.collapse', '[id^="notes-"]', function() {
                const appointmentId = $(this).attr('id').replace('notes-', '');
                $(`#icon-${appointmentId}`).addClass('rotated');
            });

            $(document).on('hidden.bs.collapse', '[id^="notes-"]', function() {
                const appointmentId = $(this).attr('id').replace('notes-', '');
                $(`#icon-${appointmentId}`).removeClass('rotated');
            });

            // إضافة معالج الأحداث للتاريخ
            $('#date_from, #date_to').on('change', function() {
                if ($('#date_type').val() === 'custom') {
                    loadReportData();
                }
            });

            // إضافة keyboard shortcuts
            $(document).keydown(function(e) {
                // Ctrl+P للطباعة
                if (e.ctrlKey && e.keyCode === 80) {
                    e.preventDefault();
                    $('#printBtn').click();
                }
                // Ctrl+E لتصدير Excel
                if (e.ctrlKey && e.keyCode === 69) {
                    e.preventDefault();
                    $('#exportExcel').click();
                }
                // Ctrl+R لإعادة تحميل البيانات
                if (e.ctrlKey && e.keyCode === 82) {
                    e.preventDefault();
                    $('#filterBtn').click();
                }
                // ESC لإعادة تعيين الفلاتر
                if (e.keyCode === 27) {
                    $('#resetBtn').click();
                }
            });

            // تحسين الطباعة
            window.addEventListener('beforeprint', function() {
                // إظهار جميع التفاصيل المطوية قبل الطباعة
                $('.collapse').addClass('show');
                $('.collapse-icon').addClass('rotated');
            });

            window.addEventListener('afterprint', function() {
                // إعادة إخفاء التفاصيل بعد الطباعة
                $('.collapse').removeClass('show');
                $('.collapse-icon').removeClass('rotated');
            });
        });
    </script>
@endsection