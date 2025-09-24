@extends('master')

@section('title')
    تقرير أوامر التوريد
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

        /* تحسينات خاصة للتواريخ */
        .date-inputs .form-control {
            font-size: 0.9rem;
        }

        /* أنماط إضافية للحالات */
        .status-pending { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .status-approved { background: linear-gradient(135deg, #10b981, #059669); }
        .status-in_progress { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .status-shipped { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .status-delivered { background: linear-gradient(135deg, #10b981, #059669); }
        .status-cancelled { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .status-completed { background: linear-gradient(135deg, #6b7280, #4b5563); }

        /* تحسينات الجدول */
        .table-modern tbody tr.expired {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
        }

        .table-modern tbody tr.urgent {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
        }

        /* أنماط للبطاقات */
        .order-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .order-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .order-card.expired {
            border-left: 4px solid #ef4444;
            background: linear-gradient(90deg, #fef2f2 0%, white 10%);
        }

        .order-card.urgent {
            border-left: 4px solid #f59e0b;
            background: linear-gradient(90deg, #fffbeb 0%, white 10%);
        }

        /* أنماط للإحصائيات */
        .stats-card.danger {
            border-top: 4px solid #ef4444;
        }

        .stats-card.danger .stats-icon {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .progress-ring {
            transform: rotate(-90deg);
        }

        .progress-ring-circle {
            transition: stroke-dashoffset 0.35s;
            transform-origin: 50% 50%;
        }

        /* تحسينات responsive */
        @media (max-width: 768px) {
            .card-body-modern {
                padding: 1rem;
            }

            .stats-card {
                margin-bottom: 1rem;
            }

            .btn-modern {
                width: 100%;
                margin-bottom: 0.5rem;
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
                        تقرير أوامر التوريد
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">التقارير</a>
                            </li>
                            <li class="breadcrumb-item active">أوامر التوريد</li>
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
                <form id="supplyOrdersForm">
                    <div class="row g-4">
                        <!-- First Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-user-tie me-2"></i>العميل</label>
                            <select name="client_id" id="client_id" class="form-control select2">
                                <option value="">جميع العملاء</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->trade_name }}-{{ $client->code ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-user me-2"></i>الموظف المسؤول</label>
                            <select name="employee_id" id="employee_id" class="form-control select2">
                                <option value="">جميع الموظفين</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-tasks me-2"></i>حالة الأمر</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="">جميع الحالات</option>
                                @foreach($statuses as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-coins me-2"></i>العملة</label>
                            <select name="currency" id="currency" class="form-control select2">
                                <option value="">جميع العملات</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency }}">{{ $currency }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-tag me-2"></i>العلامة</label>
                            <select name="tag" id="tag" class="form-control select2">
                                <option value="">جميع العلامات</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag }}">{{ $tag }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-sort me-2"></i>ترتيب حسب</label>
                            <select name="sort_by" id="sort_by" class="form-control select2">
                                <option value="">اختر الترتيب</option>
                                <option value="budget_desc">الميزانية الأعلى</option>
                                <option value="budget_asc">الميزانية الأقل</option>
                                <option value="start_date_desc">تاريخ البداية الأحدث</option>
                                <option value="start_date_asc">تاريخ البداية الأقدم</option>
                                <option value="end_date_desc">تاريخ الانتهاء الأحدث</option>
                                <option value="end_date_asc">تاريخ الانتهاء الأقدم</option>
                                <option value="name_asc">الاسم أبجدياً</option>
                                <option value="name_desc">الاسم عكس أبجدياً</option>
                                <option value="order_number_asc">رقم الأمر تصاعدي</option>
                                <option value="order_number_desc">رقم الأمر تنازلي</option>
                                <option value="created_at_desc">تاريخ الإنشاء الأحدث</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-layer-group me-2"></i>تجميع حسب</label>
                            <select name="group_by" id="group_by" class="form-control select2">
                                <option value="">بدون تجميع</option>
                                <option value="client">العميل</option>
                                <option value="employee">الموظف</option>
                                <option value="status">الحالة</option>
                                <option value="currency">العملة</option>
                                <option value="tag">العلامة</option>
                                <option value="month">الشهر</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-search me-2"></i>البحث في الأوامر</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="ابحث بالاسم، رقم الأمر، أو رقم التتبع...">
                        </div>

                        <!-- Third Row - Date Filters -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar me-2"></i>تاريخ البداية من</label>
                            <input type="date" name="start_date_from" id="start_date_from" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar me-2"></i>تاريخ البداية إلى</label>
                            <input type="date" name="start_date_to" id="start_date_to" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar-check me-2"></i>تاريخ الانتهاء من</label>
                            <input type="date" name="end_date_from" id="end_date_from" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar-check me-2"></i>تاريخ الانتهاء إلى</label>
                            <input type="date" name="end_date_to" id="end_date_to" class="form-control">
                        </div>

                        <!-- Fourth Row - Budget Filters -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-dollar-sign me-2"></i>الميزانية من</label>
                            <input type="number" name="budget_from" id="budget_from" class="form-control" placeholder="0" min="0" step="0.01">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-dollar-sign me-2"></i>الميزانية إلى</label>
                            <input type="number" name="budget_to" id="budget_to" class="form-control" placeholder="0" min="0" step="0.01">
                        </div>

                        <!-- Options Row -->
                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-employee-only" name="show_employee_only" class="form-check-input">
                                <label for="show-employee-only" class="form-check-label">
                                    <i class="fas fa-user-check me-2"></i>إظهار للموظفين فقط
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-expired-only" name="show_expired_only" class="form-check-input">
                                <label for="show-expired-only" class="form-check-label">
                                    <i class="fas fa-exclamation-triangle me-2"></i>الأوامر المنتهية الصلاحية
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-active-only" name="show_active_only" class="form-check-input">
                                <label for="show-active-only" class="form-check-label">
                                    <i class="fas fa-play me-2"></i>الأوامر النشطة فقط
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-totals" name="show_totals" class="form-check-input" checked>
                                <label for="show-totals" class="form-check-label">
                                    <i class="fas fa-calculator me-2"></i>إظهار الإجماليات
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-lg-12 align-self-end">
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
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="stats-value" id="totalOrders">0</div>
                    <div class="stats-label">إجمالي الأوامر</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stats-value" id="totalBudget">0</div>
                    <div class="stats-label">إجمالي الميزانية</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-value" id="totalClients">0</div>
                    <div class="stats-label">عدد العملاء</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon info">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stats-value" id="totalEmployees">0</div>
                    <div class="stats-label">عدد الموظفين</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stats-value" id="expiredOrders">0</div>
                    <div class="stats-label">أوامر منتهية الصلاحية</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-value" id="urgentOrders">0</div>
                    <div class="stats-label">أوامر عاجلة</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني لأوامر التوريد
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="supplyOrdersChart"></canvas>
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
                    تقرير أوامر التوريد - مؤسسة أعمال خاصة للتجارة
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-file-alt me-2"></i>رقم الأمر</th>
                                <th><i class="fas fa-truck me-2"></i>اسم الأمر</th>
                                <th><i class="fas fa-user-tie me-2"></i>العميل</th>
                                <th><i class="fas fa-user me-2"></i>الموظف المسؤول</th>
                                <th><i class="fas fa-tasks me-2"></i>الحالة</th>
                                <th><i class="fas fa-calendar me-2"></i>تاريخ البداية</th>
                                <th><i class="fas fa-calendar-check me-2"></i>تاريخ الانتهاء</th>
                                <th><i class="fas fa-dollar-sign me-2"></i>الميزانية</th>
                                <th><i class="fas fa-coins me-2"></i>العملة</th>
                                <th><i class="fas fa-tag me-2"></i>العلامة</th>
                                <th><i class="fas fa-shipping-fast me-2"></i>رقم التتبع</th>
                                <th><i class="fas fa-sticky-note me-2"></i>الوصف</th>
                            </tr>
                        </thead>
                        <tbody id="supplyOrdersTableBody">
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
        let supplyOrdersChart;

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
            loadSupplyOrdersData();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // التعامل مع تصفية التقرير
            $('#filterBtn').click(function() {
                $(this).addClass('loading');
                loadSupplyOrdersData();
            });

            // إعادة تعيين الفلاتر
            $('#resetBtn').click(function() {
                $('#supplyOrdersForm')[0].reset();
                $('.select2').val(null).trigger('change');
                loadSupplyOrdersData();
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
            $('.select2, #search, input[type="date"], input[type="number"]').on('change keyup', function() {
                clearTimeout($(this).data('timeout'));
                $(this).data('timeout', setTimeout(function() {
                    loadSupplyOrdersData();
                }, 500));
            });

            $('input[type="checkbox"]').on('change', function() {
                loadSupplyOrdersData();
            });

            // دالة تحميل بيانات أوامر التوريد
            function loadSupplyOrdersData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#supplyOrdersForm').serialize();

                $.ajax({
                    url: '{{ route('supply-orders.supplyOrdersReportAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateSupplyOrdersDisplay(response);
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

            // دالة تحديث عرض أوامر التوريد
            function updateSupplyOrdersDisplay(data) {
                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#totalOrders', 0, data.totals.total_orders, 1000);
                animateValue('#totalBudget', 0, data.totals.total_budget, 1000);
                animateValue('#totalClients', 0, data.totals.total_clients, 1000);
                animateValue('#totalEmployees', 0, data.totals.total_employees, 1000);
                animateValue('#expiredOrders', 0, data.totals.expired_orders, 1000);
                animateValue('#urgentOrders', 0, data.totals.urgent_orders, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير أوامر التوريد - ${data.group_by ? 'تجميع حسب ' + data.group_by : 'جميع الأوامر'}
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.orders, data.totals);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('supplyOrdersChart').getContext('2d');

                if (supplyOrdersChart) {
                    supplyOrdersChart.destroy();
                }

                let datasets = [];

                if (chartData.budgets && chartData.budgets.length > 0) {
                    datasets.push({
                        label: 'الميزانية',
                        data: chartData.budgets,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    });
                }

                if (chartData.counts && chartData.counts.length > 0) {
                    datasets.push({
                        label: 'عدد الأوامر',
                        data: chartData.counts,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    });
                }

                supplyOrdersChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels || [],
                        datasets: datasets
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
                                        if (context.datasetIndex === 0 && chartData.budgets) {
                                            return 'الميزانية: ' + formatNumber(context.parsed.y);
                                        }
                                        return 'عدد الأوامر: ' + formatNumber(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
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
            function updateTableBody(orders, totals) {
                let tableHtml = '';

                if (orders && orders.length > 0) {
                    let grandTotals = {
                        budget: 0
                    };

                    orders.forEach((order, index) => {
                        grandTotals.budget += parseFloat(order.budget || 0);

                        const statusClass = getStatusClass(order.status);
                        const rowClass = getRowClass(order);
                        const urgentBadge = order.is_urgent ? '<span class="badge bg-warning ms-1"><i class="fas fa-clock"></i></span>' : '';
                        const expiredBadge = order.is_expired ? '<span class="badge bg-danger ms-1"><i class="fas fa-exclamation-triangle"></i></span>' : '';

                        tableHtml += `<tr class="${rowClass}">`;
                        tableHtml += `<td>${index + 1}</td>`;
                        tableHtml += `<td><span class="badge bg-secondary">${order.order_number || 'غير محدد'}</span></td>`;
                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">${order.name}</div>
                                    ${urgentBadge}${expiredBadge}
                                </div>
                            </div>
                        </td>`;
                        tableHtml += `<td>${order.client ? `<span class="badge bg-info">${order.client}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${order.employee ? `<span class="badge bg-success">${order.employee}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td><span class="badge ${statusClass}">${order.status_label}</span></td>`;
                        tableHtml += `<td>${order.start_date || '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${order.end_date || '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td><span class="fw-bold text-success">${formatNumber(order.budget || 0)}</span></td>`;
                        tableHtml += `<td>${order.currency ? `<span class="badge bg-warning">${order.currency}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${order.tag ? `<span class="badge bg-primary">${order.tag}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${order.tracking_number || '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${order.description ? `<small>${order.description.substring(0, 50)}${order.description.length > 50 ? '...' : ''}</small>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `</tr>`;
                    });

                    // إضافة صف الإجمالي العام
                    if ($('#show-totals').is(':checked')) {
                        tableHtml += `
                            <tr class="table-grand-total">
                                <td colspan="8">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    <strong>المجموع الكلي</strong>
                                </td>
                                <td class="fw-bold">${formatNumber(grandTotals.budget)}</td>
                                <td colspan="4" class="text-muted">-</td>
                            </tr>
                        `;
                    }
                } else {
                    tableHtml = `
                        <tr>
                            <td colspan="13" class="text-center py-4">
                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">لا توجد أوامر مطابقة للفلاتر المحددة</p>
                            </td>
                        </tr>
                    `;
                }

                $('#supplyOrdersTableBody').html(tableHtml);
            }

            // دالة الحصول على فئة CSS للحالة
            function getStatusClass(status) {
                const statusClasses = {
                    'pending': 'bg-warning',
                    'approved': 'bg-success',
                    'in_progress': 'bg-primary',
                    'shipped': 'bg-info',
                    'delivered': 'bg-success',
                    'cancelled': 'bg-danger',
                    'completed': 'bg-secondary'
                };
                return statusClasses[status] || 'bg-secondary';
            }

            // دالة الحصول على فئة CSS للصف
            function getRowClass(order) {
                if (order.is_expired) return 'expired';
                if (order.is_urgent) return 'urgent';
                return '';
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
                const fileName = `تقرير_أوامر_التوريد_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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
                doc.text('تقرير أوامر التوريد', 148, 20, { align: 'center' });

                // إضافة التاريخ
                doc.setFontSize(10);
                doc.text(`تاريخ الطباعة: ${new Date().toLocaleDateString('ar-SA')}`, 20, 30);

                // تصدير الجدول
                doc.autoTable({
                    html: '#reportContainer table',
                    startY: 40,
                    styles: {
                        fontSize: 7,
                        cellPadding: 1.5
                    },
                    headStyles: {
                        fillColor: [102, 126, 234],
                        textColor: 255
                    },
                    columnStyles: {
                        0: { cellWidth: 15 },  // #
                        1: { cellWidth: 25 },  // رقم الأمر
                        2: { cellWidth: 30 },  // اسم الأمر
                        3: { cellWidth: 25 },  // العميل
                        4: { cellWidth: 25 },  // الموظف
                        5: { cellWidth: 20 },  // الحالة
                        6: { cellWidth: 25 },  // تاريخ البداية
                        7: { cellWidth: 25 },  // تاريخ الانتهاء
                        8: { cellWidth: 20 },  // الميزانية
                        9: { cellWidth: 15 },  // العملة
                        10: { cellWidth: 20 }, // العلامة
                        11: { cellWidth: 25 }, // رقم التتبع
                        12: { cellWidth: 40 }  // الوصف
                    }
                });

                const fileName = `تقرير_أوامر_التوريد_${new Date().toISOString().split('T')[0]}.pdf`;
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

            // تحديث التواريخ التلقائي
            const today = new Date().toISOString().split('T')[0];
            $('#start_date_from').attr('max', today);
            $('#start_date_to').attr('max', today);
            $('#end_date_from').attr('min', today);
            $('#end_date_to').attr('min', today);

            // التحقق من صحة التواريخ
            $('#start_date_from, #start_date_to').on('change', function() {
                const startFrom = $('#start_date_from').val();
                const startTo = $('#start_date_to').val();

                if (startFrom && startTo && startFrom > startTo) {
                    showAlert('تاريخ البداية "من" يجب أن يكون قبل تاريخ البداية "إلى"', 'warning');
                    $(this).val('');
                }
            });

            $('#end_date_from, #end_date_to').on('change', function() {
                const endFrom = $('#end_date_from').val();
                const endTo = $('#end_date_to').val();

                if (endFrom && endTo && endFrom > endTo) {
                    showAlert('تاريخ الانتهاء "من" يجب أن يكون قبل تاريخ الانتهاء "إلى"', 'warning');
                    $(this).val('');
                }
            });

            // التحقق من صحة الميزانية
            $('#budget_from, #budget_to').on('change', function() {
                const budgetFrom = parseFloat($('#budget_from').val()) || 0;
                const budgetTo = parseFloat($('#budget_to').val()) || 0;

                if (budgetFrom > 0 && budgetTo > 0 && budgetFrom > budgetTo) {
                    showAlert('الميزانية "من" يجب أن تكون أقل من أو تساوي الميزانية "إلى"', 'warning');
                    $(this).val('');
                }
            });
        });

        // دوال JavaScript عامة
        function viewOrderDetails(orderId) {
            // يمكن إضافة modal هنا لعرض تفاصيل الأمر
            console.log(`عرض تفاصيل الأمر: ${orderId}`);

            // مثال على طلب AJAX لجلب التفاصيل
            /*
            $.ajax({
                url: `/supply-orders/${orderId}`,
                method: 'GET',
                success: function(response) {
                    // عرض التفاصيل في modal
                },
                error: function() {
                    showAlert('حدث خطأ في تحميل تفاصيل الأمر', 'danger');
                }
            });
            */
        }

        function updateOrderStatus(orderId, newStatus) {
            // يمكن إضافة AJAX هنا لتحديث حالة الأمر
            console.log(`تحديث حالة الأمر ${orderId} إلى: ${newStatus}`);

            // مثال على طلب AJAX (اختياري)
            /*
            $.ajax({
                url: `/supply-orders/${orderId}/status`,
                method: 'PUT',
                data: {
                    status: newStatus,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showAlert('تم تحديث حالة الأمر بنجاح', 'success');
                    loadSupplyOrdersData(); // إعادة تحميل البيانات
                },
                error: function() {
                    showAlert('حدث خطأ في تحديث حالة الأمر', 'danger');
                }
            });
            */
        }

        function sendNotification(orderId, type) {
            // إرسال إشعار للعميل أو الموظف
            console.log(`إرسال إشعار ${type} للأمر: ${orderId}`);

            // مثال على طلب AJAX
            /*
            $.ajax({
                url: `/supply-orders/${orderId}/notify`,
                method: 'POST',
                data: {
                    type: type,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showAlert('تم إرسال الإشعار بنجاح', 'success');
                },
                error: function() {
                    showAlert('حدث خطأ في إرسال الإشعار', 'danger');
                }
            });
            */
        }

        // دالة للتحقق من الأوامر المنتهية الصلاحية والعاجلة
        function checkOrdersStatus() {
            // يمكن استدعاء هذه الدالة بشكل دوري للتحقق من حالة الأوامر
            $.ajax({
                url: '{{ route("supply-orders.checkOrdersStatus") }}',
                method: 'GET',
                success: function(response) {
                    if (response.urgent_orders > 0) {
                        showAlert(`لديك ${response.urgent_orders} أمر عاجل يحتاج متابعة!`, 'warning');
                    }
                    if (response.expired_orders > 0) {
                        showAlert(`لديك ${response.expired_orders} أمر منتهي الصلاحية!`, 'danger');
                    }
                }
            });
        }

        // تشغيل فحص الأوامر كل 5 دقائق
        setInterval(checkOrdersStatus, 300000);

        // تشغيل فحص أولي بعد تحميل الصفحة
        setTimeout(checkOrdersStatus, 2000);
    </script>
@endsection