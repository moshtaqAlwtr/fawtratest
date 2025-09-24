@extends('master')

@section('title')
    تقرير الشيكات المدفوعة
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

         /* Badge styles for cheque status */
         .cheque-status-badge {
             padding: 0.375rem 0.75rem;
             border-radius: 8px;
             font-weight: 500;
             font-size: 0.75rem;
             text-transform: uppercase;
             letter-spacing: 0.5px;
         }

         .status-paid {
             background: linear-gradient(135deg, #10b981, #059669);
             color: white;
         }

         .status-pending {
             background: linear-gradient(135deg, #f59e0b, #d97706);
             color: white;
         }

         .status-cancelled {
             background: linear-gradient(135deg, #ef4444, #dc2626);
             color: white;
         }

         .status-returned {
             background: linear-gradient(135deg, #8b5cf6, #7c3aed);
             color: white;
         }

         /* Cheque amount styling */
         .cheque-amount {
             font-weight: 700;
             font-size: 1.1rem;
             color: #059669;
         }

         /* Progress bar for due dates */
         .due-progress {
             height: 4px;
             border-radius: 2px;
             background: #e5e7eb;
             overflow: hidden;
         }

         .due-progress-bar {
             height: 100%;
             border-radius: 2px;
             transition: width 0.3s ease;
         }

         .due-today { background: #f59e0b; }
         .due-soon { background: #ef4444; }
         .due-normal { background: #10b981; }
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
                        <i class="fas fa-money-check me-3"></i>
                        تقرير الشيكات المدفوعة
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير الشيكات المدفوعة</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-receipt"></i>
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
                            <label class="form-label-modern"><i class="fas fa-user-tie me-2"></i>الحساب المستلم</label>
                            <select name="recipient_account" id="recipient_account" class="form-control select2">
                                <option value="">جميع الحسابات</option>
                                @foreach ($recipient_accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }} - {{ $account->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-university me-2"></i>اسم البنك</label>
                            <select name="bank_id" id="bank_id" class="form-control select2">
                                <option value="">جميع البنوك</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-book me-2"></i>دفتر الشيكات</label>
                            <select name="cheque_book_id" id="cheque_book_id" class="form-control select2">
                                <option value="">جميع دفاتر الشيكات</option>
                                @foreach ($cheque_books as $book)
                                    <option value="{{ $book->id }}">{{ $book->book_number }} - {{ $book->bank->name ?? 'غير محدد' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-toggle-on me-2"></i>الحالة</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="">جميع الحالات</option>
                                <option value="paid">مدفوع</option>
                                <option value="pending">معلق</option>
                                <option value="cancelled">ملغي</option>
                                <option value="returned">مرتجع</option>
                            </select>
                        </div>

                        <!-- Second Row - Date Filters -->
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

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>الإصدار من تاريخ</label>
                            <input type="date" name="issue_date_from" id="issue_date_from" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>الإصدار إلى تاريخ</label>
                            <input type="date" name="issue_date_to" id="issue_date_to" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar-check me-2"></i>الاستحقاق من تاريخ</label>
                            <input type="date" name="due_date_from" id="due_date_from" class="form-control">
                        </div>

                        <!-- Third Row - More Date Filters -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar-check me-2"></i>الاستحقاق إلى تاريخ</label>
                            <input type="date" name="due_date_to" id="due_date_to" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-hashtag me-2"></i>رقم الشيك</label>
                            <input type="text" name="cheque_number" id="cheque_number" class="form-control" placeholder="رقم الشيك">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-layer-group me-2"></i>تجميع حسب</label>
                            <select name="group_by" id="group_by" class="form-control select2">
                                <option value="">بدون تجميع</option>
                                <option value="bank">البنك</option>
                                <option value="cheque_book">دفتر الشيكات</option>
                                <option value="recipient">الحساب المستلم</option>
                                <option value="status">الحالة</option>
                                <option value="month">الشهر</option>
                            </select>
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
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div class="stats-value" id="totalAmount">0</div>
                    <div class="stats-label">إجمالي قيمة الشيكات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card primary">
                    <div class="stats-icon primary">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-value" id="paidCount">0</div>
                    <div class="stats-label">الشيكات المدفوعة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-value" id="pendingCount">0</div>
                    <div class="stats-label">الشيكات المعلقة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="stats-value" id="totalCount">0</div>
                    <div class="stats-label">إجمالي عدد الشيكات</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني للشيكات
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="amountChart"></canvas>
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
                    تقرير الشيكات المدفوعة
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-receipt me-2"></i>رقم الشيك</th>
                                <th><i class="fas fa-money-bill me-2"></i>المبلغ (ريال)</th>
                                <th><i class="fas fa-user-tie me-2"></i>المستفيد</th>
                                <th><i class="fas fa-university me-2"></i>البنك</th>
                                <th><i class="fas fa-book me-2"></i>دفتر الشيكات</th>
                                <th><i class="fas fa-calendar-alt me-2"></i>تاريخ الإصدار</th>
                                <th><i class="fas fa-calendar-check me-2"></i>تاريخ الاستحقاق</th>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        let statusChart, amountChart;

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
                dropdownCssClass: 'select2-dropdown-custom'
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

            // التعامل مع تصدير PDF
            $('#exportPdf').click(function() {
                exportToPdf();
            });

            // تحديث البيانات عند تغيير أي فلتر
            $('.select2, input[type="date"], input[type="text"]').on('change', function() {
                if ($('#date_type').val() === 'custom' || $(this).attr('id') !== 'date_type') {
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
                        return;
                }

                if (fromDate && toDate) {
                    $('#issue_date_from').val(formatDateForInput(fromDate));
                    $('#issue_date_to').val(formatDateForInput(toDate));
                }
            }

            // دالة تحميل بيانات التقرير
            function loadReportData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#reportForm').serialize();

                $.ajax({
                    url: '{{ route('checksReports.deliveredChequesReportAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateReportDisplay(response);
                        $('.loading-overlay').fadeOut();
                        $('#filterBtn').prop('disabled', false);
                        $('#filterBtn').removeClass('loading');
                        $('#reportContainer').addClass('fade-in');
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

            // دالة تحديث عرض التقرير
            function updateReportDisplay(data) {
                // تحديث الإجماليات
                animateValue('#totalAmount', 0, data.totals.total_amount, 1000);
                animateValue('#paidCount', 0, data.totals.paid_count, 1000);
                animateValue('#pendingCount', 0, data.totals.pending_count, 1000);
                animateValue('#totalCount', 0, data.totals.total_count, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير الشيكات المدفوعة من ${data.from_date} إلى ${data.to_date}
                `);

                // تحديث الرسوم البيانية
                updateCharts(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.cheques);
            }

            // دالة تحديث الرسوم البيانية
            function updateCharts(chartData) {
                // رسم بياني لحالة الشيكات
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                if (statusChart) {
                    statusChart.destroy();
                }

                statusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.status.labels,
                        datasets: [{
                            label: 'حالة الشيكات',
                            data: chartData.status.values,
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(139, 92, 246, 0.8)'
                            ],
                            borderColor: [
                                'rgba(16, 185, 129, 1)',
                                'rgba(245, 158, 11, 1)',
                                'rgba(239, 68, 68, 1)',
                                'rgba(139, 92, 246, 1)'
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
                            title: {
                                display: true,
                                text: 'توزيع الشيكات حسب الحالة',
                                font: {
                                    family: 'Cairo',
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                });

                // رسم بياني للمبالغ
                const amountCtx = document.getElementById('amountChart').getContext('2d');
                if (amountChart) {
                    amountChart.destroy();
                }

                amountChart = new Chart(amountCtx, {
                    type: 'bar',
                    data: {
                        labels: chartData.amounts.labels,
                        datasets: [{
                            label: 'المبلغ (ريال)',
                            data: chartData.amounts.values,
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'توزيع المبالغ حسب البنك',
                                font: {
                                    family: 'Cairo',
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return formatNumber(value) + ' ريال';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // دالة تحديث محتوى الجدول
            function updateTableBody(cheques) {
                let tableHtml = '';

                if (cheques.length === 0) {
                    tableHtml = `
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد بيانات متاحة</h5>
                                <p class="text-muted">لا توجد شيكات تطابق المعايير المحددة</p>
                            </td>
                        </tr>
                    `;
                } else {
                    cheques.forEach((cheque, index) => {
                        const statusInfo = getStatusInfo(cheque.status);
                        const dueStatus = getDueStatus(cheque.due_date);

                        tableHtml += `
                            <tr class="cheque-row">
                                <td>${index + 1}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary-subtle rounded-circle me-2">
                                            <i class="fas fa-receipt text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">${cheque.cheque_number}</div>
                                            <small class="text-muted">${cheque.cheque_book ? cheque.cheque_book.book_number : '--'}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="cheque-amount">${formatNumber(cheque.amount)}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-info-subtle rounded-circle me-2">
                                            <i class="fas fa-user-tie text-info"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">${cheque.payee_name || 'غير محدد'}</div>
                                            <small class="text-muted">المستفيد</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="fas fa-university me-1"></i>
                                        ${cheque.bank ? cheque.bank.name : 'غير محدد'}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="fas fa-book me-1"></i>
                                        ${cheque.cheque_book ? cheque.cheque_book.book_number : 'غير محدد'}
                                    </span>
                                </td>
                                <td>${cheque.issue_date ? formatDate(cheque.issue_date) : '--'}</td>
                                <td>
                                    <div>
                                        <div>${cheque.due_date ? formatDate(cheque.due_date) : '--'}</div>
                                        ${dueStatus.html}
                                    </div>
                                </td>
                                <td>
                                    <span class="cheque-status-badge ${statusInfo.class}">
                                        <i class="${statusInfo.icon} me-1"></i>
                                        ${statusInfo.text}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted" title="${cheque.description || '--'}">
                                        ${cheque.description ? (cheque.description.length > 30 ? cheque.description.substring(0, 30) + '...' : cheque.description) : '--'}
                                    </span>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#reportTableBody').html(tableHtml);
            }

            // دالة الحصول على معلومات الحالة
            function getStatusInfo(status) {
                const statusMap = {
                    'paid': {
                        class: 'status-paid',
                        icon: 'fas fa-check-circle',
                        text: 'مدفوع'
                    },
                    'pending': {
                        class: 'status-pending',
                        icon: 'fas fa-clock',
                        text: 'معلق'
                    },
                    'cancelled': {
                        class: 'status-cancelled',
                        icon: 'fas fa-times-circle',
                        text: 'ملغي'
                    },
                    'returned': {
                        class: 'status-returned',
                        icon: 'fas fa-undo',
                        text: 'مرتجع'
                    }
                };

                return statusMap[status] || {
                    class: 'status-pending',
                    icon: 'fas fa-question-circle',
                    text: 'غير محدد'
                };
            }

            // دالة الحصول على حالة تاريخ الاستحقاق
            function getDueStatus(dueDate) {
                if (!dueDate) return { html: '', class: '' };

                const today = new Date();
                const due = new Date(dueDate);
                const diffTime = due - today;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays < 0) {
                    return {
                        html: `<div class="due-progress"><div class="due-progress-bar due-soon" style="width: 100%"></div></div>
                               <small class="text-danger">متأخر ${Math.abs(diffDays)} يوم</small>`,
                        class: 'due-overdue'
                    };
                } else if (diffDays === 0) {
                    return {
                        html: `<div class="due-progress"><div class="due-progress-bar due-today" style="width: 100%"></div></div>
                               <small class="text-warning">يستحق اليوم</small>`,
                        class: 'due-today'
                    };
                } else if (diffDays <= 7) {
                    const progress = ((7 - diffDays) / 7) * 100;
                    return {
                        html: `<div class="due-progress"><div class="due-progress-bar due-soon" style="width: ${progress}%"></div></div>
                               <small class="text-warning">يستحق خلال ${diffDays} يوم</small>`,
                        class: 'due-soon'
                    };
                } else {
                    return {
                        html: `<div class="due-progress"><div class="due-progress-bar due-normal" style="width: 20%"></div></div>
                               <small class="text-success">يستحق خلال ${diffDays} يوم</small>`,
                        class: 'due-normal'
                    };
                }
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

                    if (progress >= 1) {
                        obj.text(formatNumber(end));
                        clearInterval(timer);
                    }
                }, 16);
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
                const fileName = `تقرير_الشيكات_المدفوعة_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

                XLSX.writeFile(wb, fileName);
                showAlert('تم تصدير الملف بنجاح!', 'success');
            }

            // دالة تصدير PDF
            function exportToPdf() {
                showAlert('جاري تصدير ملف PDF...', 'info');

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('l', 'mm', 'a4');

                // إضافة العنوان
                doc.setFontSize(16);
                doc.text('تقرير الشيكات المدفوعة', 20, 20);

                // إضافة التاريخ
                const today = new Date().toLocaleDateString('ar-SA');
                doc.setFontSize(12);
                doc.text(`تاريخ التقرير: ${today}`, 20, 30);

                // حفظ الملف
                const fileName = `تقرير_الشيكات_المدفوعة_${today.replace(/\//g, '-')}.pdf`;
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

            // تحديث نطاق التاريخ الأولي
            updateDateRange();
        });
    </script>
@endsection
