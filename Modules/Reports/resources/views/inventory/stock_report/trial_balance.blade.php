@extends('master')

@section('title')
    ميزان المراجعة للمخزون
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

        /* تحسين تصميم الجدول */
        .trial-balance-table {
            font-family: 'Cairo', sans-serif;
        }

        .trial-balance-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-align: center;
            border: none;
            padding: 1rem 0.5rem;
        }

        .trial-balance-table .table-section-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            font-weight: 700;
            text-align: center;
        }

        .trial-balance-table .initial-section {
            background-color: rgba(34, 197, 94, 0.1);
            border-left: 4px solid #22c55e;
        }

        .trial-balance-table .movement-section {
            background-color: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
        }

        .trial-balance-table .final-section {
            background-color: rgba(168, 85, 247, 0.1);
            border-left: 4px solid #a855f7;
        }

        .trial-balance-table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.08);
            transition: all 0.3s ease;
        }

        .trial-balance-table .number-cell {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            text-align: left;
            direction: ltr;
        }

        .trial-balance-table .positive-amount {
            color: #22c55e;
        }

        .trial-balance-table .negative-amount {
            color: #ef4444;
        }

        .trial-balance-table .zero-amount {
            color: #6b7280;
            opacity: 0.7;
        }

        .table-grand-total {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: white !important;
            font-weight: 700 !important;
            font-size: 1.05em !important;
        }

        .table-grand-total td {
            border: none !important;
            padding: 1rem 0.5rem !important;
        }

        /* تحسين البطاقات الإحصائية */
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 20px;
            padding: 2rem;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .stats-card.success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stats-card.info {
            background: linear-gradient(135deg, #4481eb 0%, #04befe 100%);
        }

        .stats-card.danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5722 100%);
        }

        .stats-icon {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stats-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* تحسين المؤشرات */
        .balance-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-left: 8px;
        }

        .balance-indicator.positive {
            background-color: #22c55e;
            box-shadow: 0 0 8px rgba(34, 197, 94, 0.5);
        }

        .balance-indicator.negative {
            background-color: #ef4444;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.5);
        }

        .balance-indicator.zero {
            background-color: #6b7280;
        }

        /* تحسينات للجوال */
        @media (max-width: 768px) {
            .trial-balance-table {
                font-size: 0.85rem;
            }

            .trial-balance-table th,
            .trial-balance-table td {
                padding: 0.5rem 0.25rem;
            }

            .stats-card {
                padding: 1.5rem;
            }

            .stats-value {
                font-size: 1.5rem;
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
                        <i class="fas fa-balance-scale me-3"></i>
                        ميزان المراجعة للمخزون
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">ميزان المراجعة للمخزون</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-balance-scale"></i>
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
                <form id="trialBalanceForm">
                    <div class="row g-4">
                        <!-- First Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-cube me-2"></i>المنتج</label>
                            <select name="product" id="product" class="form-control select2">
                                <option value="">جميع المنتجات</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

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
                            <label class="form-label-modern"><i class="fas fa-warehouse me-2"></i>المستودع</label>
                            <select name="warehouse" id="warehouse" class="form-control select2">
                                <option value="">جميع المستودعات</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-calendar me-2"></i>الفترة الزمنية</label>
                            <select name="date_type" id="date_type" class="form-control select2">
                                <option value="custom">مخصص</option>
                                <option value="today">اليوم</option>
                                <option value="yesterday">أمس</option>
                                <option value="this_week">هذا الأسبوع</option>
                                <option value="last_week">الأسبوع الماضي</option>
                                <option value="this_month" selected>هذا الشهر</option>
                                <option value="last_month">الشهر الماضي</option>
                                <option value="this_year">هذا العام</option>
                                <option value="last_year">العام الماضي</option>
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6" id="custom_dates">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>من تاريخ</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>

                        <div class="col-lg-3 col-md-6" id="custom_to_date">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>إلى تاريخ</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ now()->format('Y-m-d') }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-sort me-2"></i>ترتيب حسب</label>
                            <select name="sort_by" id="sort_by" class="form-control select2">
                                <option value="">اختر الترتيب</option>
                                <option value="name_asc">الاسم أبجدياً</option>
                                <option value="name_desc">الاسم عكس أبجدياً</option>
                                <option value="quantity_desc">الكمية النهائية تنازلي</option>
                                <option value="quantity_asc">الكمية النهائية تصاعدي</option>
                                <option value="value_desc">القيمة النهائية تنازلي</option>
                                <option value="value_asc">القيمة النهائية تصاعدي</option>
                                <option value="movement_desc">حجم الحركة تنازلي</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-layer-group me-2"></i>تجميع حسب</label>
                            <select name="group_by" id="group_by" class="form-control select2">
                                <option value="">بدون تجميع</option>
                                <option value="category">التصنيف</option>
                                <option value="warehouse">المستودع</option>
                                <option value="movement_type">نوع الحركة</option>
                            </select>
                        </div>

                        <!-- Third Row - Options -->
                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="hide-zero-balance" name="hide_zero_balance" class="form-check-input">
                                <label for="hide-zero-balance" class="form-check-label">
                                    <i class="fas fa-eye-slash me-2"></i>إخفاء الأرصدة الصفرية
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-movements-only" name="show_movements_only" class="form-check-input">
                                <label for="show-movements-only" class="form-check-label">
                                    <i class="fas fa-exchange-alt me-2"></i>إظهار المنتجات بحركة فقط
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12">
                            <label class="form-label-modern"><i class="fas fa-search me-2"></i>البحث في المنتجات</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="ابحث بالاسم أو الكود...">
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
                    <div class="stats-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="stats-value" id="totalProducts">0</div>
                    <div class="stats-label">إجمالي المنتجات</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="stats-value" id="totalQuantity">0</div>
                    <div class="stats-label">إجمالي الكمية النهائية</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stats-value" id="totalValue">0</div>
                    <div class="stats-label">إجمالي القيمة النهائية</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="stats-value" id="totalMovements">0</div>
                    <div class="stats-label">إجمالي الحركات</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني لميزان المخزون
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="trialBalanceChart"></canvas>
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
                    ميزان المراجعة للمخزون - مؤسسة أعمال خاصة للتجارة
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table trial-balance-table mb-0">
                        <thead>
                            <tr>
                                <th rowspan="2"><i class="fas fa-cube me-2"></i>المنتج</th>
                                <th rowspan="2"><i class="fas fa-barcode me-2"></i>الكود</th>
                                <th rowspan="2"><i class="fas fa-tags me-2"></i>التصنيف</th>
                                <th rowspan="2"><i class="fas fa-warehouse me-2"></i>المستودع</th>
                                <th colspan="2" class="initial-section">الرصيد الابتدائي</th>
                                <th colspan="4" class="movement-section">حركات الفترة</th>
                                <th colspan="2" class="final-section">الرصيد النهائي</th>
                            </tr>
                            <tr>
                                <th class="initial-section">الكمية</th>
                                <th class="initial-section">القيمة</th>
                                <th class="movement-section text-success">الوارد (كمية)</th>
                                <th class="movement-section text-success">الوارد (قيمة)</th>
                                <th class="movement-section text-danger">المنصرف (كمية)</th>
                                <th class="movement-section text-danger">المنصرف (قيمة)</th>
                                <th class="final-section">الكمية الصافية</th>
                                <th class="final-section">القيمة الصافية</th>
                            </tr>
                        </thead>
                        <tbody id="trialBalanceTableBody">
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
        let trialBalanceChart;

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
            loadTrialBalanceData();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // التعامل مع تغيير نوع التاريخ
            $('#date_type').change(function() {
                handleDateTypeChange();
                loadTrialBalanceData();
            });

            // التعامل مع تصفية التقرير
            $('#filterBtn').click(function() {
                $(this).addClass('loading');
                loadTrialBalanceData();
            });

            // إعادة تعيين الفلاتر
            $('#resetBtn').click(function() {
                $('#trialBalanceForm')[0].reset();
                $('.select2').val(null).trigger('change');
                $('#date_type').val('this_month').trigger('change');
                handleDateTypeChange();
                loadTrialBalanceData();
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
            $('.select2, input[type="date"], #search').on('change keyup', function() {
                clearTimeout($(this).data('timeout'));
                $(this).data('timeout', setTimeout(function() {
                    loadTrialBalanceData();
                }, 500));
            });

            $('#hide-zero-balance, #show-movements-only').on('change', function() {
                loadTrialBalanceData();
            });

            // دالة التعامل مع تغيير نوع التاريخ
            function handleDateTypeChange() {
                const dateType = $('#date_type').val();

                if (dateType === 'custom') {
                    $('#custom_dates, #custom_to_date').show();
                } else {
                    $('#custom_dates, #custom_to_date').hide();

                    // تحديد التواريخ بناءً على الاختيار
                    const today = new Date();
                    let fromDate, toDate;

                    switch (dateType) {
                        case 'today':
                            fromDate = toDate = today.toISOString().split('T')[0];
                            break;
                        case 'yesterday':
                            const yesterday = new Date(today);
                            yesterday.setDate(yesterday.getDate() - 1);
                            fromDate = toDate = yesterday.toISOString().split('T')[0];
                            break;
                        case 'this_week':
                            const startOfWeek = new Date(today);
                            startOfWeek.setDate(today.getDate() - today.getDay());
                            fromDate = startOfWeek.toISOString().split('T')[0];
                            toDate = today.toISOString().split('T')[0];
                            break;
                        case 'last_week':
                            const lastWeekEnd = new Date(today);
                            lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
                            const lastWeekStart = new Date(lastWeekEnd);
                            lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
                            fromDate = lastWeekStart.toISOString().split('T')[0];
                            toDate = lastWeekEnd.toISOString().split('T')[0];
                            break;
                        case 'this_month':
                            fromDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                            toDate = today.toISOString().split('T')[0];
                            break;
                        case 'last_month':
                            const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                            const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
                            fromDate = lastMonth.toISOString().split('T')[0];
                            toDate = lastMonthEnd.toISOString().split('T')[0];
                            break;
                        case 'this_year':
                            fromDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                            toDate = today.toISOString().split('T')[0];
                            break;
                        case 'last_year':
                            fromDate = new Date(today.getFullYear() - 1, 0, 1).toISOString().split('T')[0];
                            toDate = new Date(today.getFullYear() - 1, 11, 31).toISOString().split('T')[0];
                            break;
                        default:
                            fromDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                            toDate = today.toISOString().split('T')[0];
                            break;
                    }

                    $('#start_date').val(fromDate);
                    $('#end_date').val(toDate);
                }
            }

            // دالة تحميل بيانات ميزان المراجعة
            function loadTrialBalanceData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#trialBalanceForm').serialize();

                $.ajax({
                    url: '{{ route('StorHouseReport.trialBalanceAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateTrialBalanceDisplay(response);
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

            // دالة تحديث عرض ميزان المراجعة
            function updateTrialBalanceDisplay(data) {
                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#totalProducts', 0, data.totals.total_products, 1000);
                animateValue('#totalQuantity', 0, data.totals.total_quantity, 1000);
                animateValue('#totalValue', 0, data.totals.total_value, 1000, true);
                animateValue('#totalMovements', 0, data.totals.total_movements, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    ميزان المراجعة للمخزون - ${data.group_by ? 'تجميع حسب ' + data.group_by : 'جميع المنتجات'}
                    <small class="text-muted d-block">من ${data.from_date} إلى ${data.to_date}</small>
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.products, data.totals);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('trialBalanceChart').getContext('2d');

                if (trialBalanceChart) {
                    trialBalanceChart.destroy();
                }

                trialBalanceChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels || [],
                        datasets: [
                            {
                                label: 'الرصيد الابتدائي',
                                data: chartData.initial_values || [],
                                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                            },
                            {
                                label: 'الوارد',
                                data: chartData.incoming_values || [],
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                            },
                            {
                                label: 'المنصرف',
                                data: chartData.outgoing_values || [],
                                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                            },
                            {
                                label: 'الرصيد النهائي',
                                data: chartData.final_values || [],
                                backgroundColor: 'rgba(168, 85, 247, 0.7)',
                                borderColor: 'rgba(168, 85, 247, 1)',
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
                                        return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return formatCurrency(value);
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
            function updateTableBody(products, totals) {
                let tableHtml = '';

                if (products && products.length > 0) {
                    let grandTotals = {
                        initial_quantity: 0,
                        initial_amount: 0,
                        incoming_quantity: 0,
                        incoming_amount: 0,
                        outgoing_quantity: 0,
                        outgoing_amount: 0,
                        net_quantity: 0,
                        net_amount: 0
                    };

                    products.forEach((product, index) => {
                        const initialQuantity = parseFloat(product.initial_quantity || 0);
                        const initialAmount = parseFloat(product.initial_amount || 0);
                        const incomingQuantity = parseFloat(product.incoming_quantity || 0);
                        const incomingAmount = parseFloat(product.incoming_amount || 0);
                        const outgoingQuantity = parseFloat(product.outgoing_quantity || 0);
                        const outgoingAmount = parseFloat(product.outgoing_amount || 0);
                        const netQuantity = parseFloat(product.net_quantity || 0);
                        const netAmount = parseFloat(product.net_amount || 0);

                        // تحديث الإجماليات الكبرى
                        grandTotals.initial_quantity += initialQuantity;
                        grandTotals.initial_amount += initialAmount;
                        grandTotals.incoming_quantity += incomingQuantity;
                        grandTotals.incoming_amount += incomingAmount;
                        grandTotals.outgoing_quantity += outgoingQuantity;
                        grandTotals.outgoing_amount += outgoingAmount;
                        grandTotals.net_quantity += netQuantity;
                        grandTotals.net_amount += netAmount;

                        // تحديد المؤشرات
                        const quantityIndicator = getBalanceIndicator(netQuantity);
                        const amountIndicator = getBalanceIndicator(netAmount);

                        tableHtml += `<tr>`;
                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-cube"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">${product.name}</div>
                                    <small class="text-muted">${product.brand || 'غير محدد'}</small>
                                </div>
                            </div>
                        </td>`;
                        tableHtml += `<td><span class="badge bg-secondary">${product.code || product.id}</span></td>`;
                        tableHtml += `<td>${product.category ? `<span class="badge bg-info">${product.category}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${product.warehouse ? `<span class="badge bg-warning">${product.warehouse}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;

                        // الرصيد الابتدائي
                        tableHtml += `<td class="number-cell ${getAmountClass(initialQuantity)}">${formatNumber(initialQuantity)}</td>`;
                        tableHtml += `<td class="number-cell ${getAmountClass(initialAmount)}">${formatCurrency(initialAmount)}</td>`;

                        // حركات الفترة - الوارد
                        tableHtml += `<td class="number-cell text-success">${formatNumber(incomingQuantity)}</td>`;
                        tableHtml += `<td class="number-cell text-success">${formatCurrency(incomingAmount)}</td>`;

                        // حركات الفترة - المنصرف
                        tableHtml += `<td class="number-cell text-danger">${formatNumber(outgoingQuantity)}</td>`;
                        tableHtml += `<td class="number-cell text-danger">${formatCurrency(outgoingAmount)}</td>`;

                        // الرصيد النهائي
                        tableHtml += `<td class="number-cell fw-bold ${getAmountClass(netQuantity)}">
                            ${quantityIndicator}
                            ${formatNumber(netQuantity)}
                        </td>`;
                        tableHtml += `<td class="number-cell fw-bold ${getAmountClass(netAmount)}">
                            ${amountIndicator}
                            ${formatCurrency(netAmount)}
                        </td>`;
                        tableHtml += `</tr>`;
                    });

                    // إضافة صف الإجمالي العام
                    tableHtml += `
                        <tr class="table-grand-total">
                            <td colspan="4" class="fw-bold">
                                <i class="fas fa-calculator me-2"></i>
                                المجموع الكلي
                            </td>
                            <td class="number-cell">${formatNumber(grandTotals.initial_quantity)}</td>
                            <td class="number-cell">${formatCurrency(grandTotals.initial_amount)}</td>
                            <td class="number-cell">${formatNumber(grandTotals.incoming_quantity)}</td>
                            <td class="number-cell">${formatCurrency(grandTotals.incoming_amount)}</td>
                            <td class="number-cell">${formatNumber(grandTotals.outgoing_quantity)}</td>
                            <td class="number-cell">${formatCurrency(grandTotals.outgoing_amount)}</td>
                            <td class="number-cell fw-bold">${formatNumber(grandTotals.net_quantity)}</td>
                            <td class="number-cell fw-bold">${formatCurrency(grandTotals.net_amount)}</td>
                        </tr>
                    `;
                } else {
                    tableHtml = `
                        <tr>
                            <td colspan="12" class="text-center py-4">
                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">لا توجد منتجات مطابقة للفلاتر المحددة</p>
                            </td>
                        </tr>
                    `;
                }

                $('#trialBalanceTableBody').html(tableHtml);
            }

            // دالة الحصول على مؤشر الرصيد
            function getBalanceIndicator(amount) {
                const value = parseFloat(amount);
                if (value > 0) {
                    return '<span class="balance-indicator positive" title="رصيد موجب"></span>';
                } else if (value < 0) {
                    return '<span class="balance-indicator negative" title="رصيد سالب"></span>';
                } else {
                    return '<span class="balance-indicator zero" title="رصيد صفر"></span>';
                }
            }

            // دالة تحديد لون المبلغ
            function getAmountClass(amount) {
                const value = parseFloat(amount);
                if (value > 0) return 'positive-amount';
                if (value < 0) return 'negative-amount';
                return 'zero-amount';
            }

            // دالة تنسيق العملة
            function formatCurrency(number) {
                return parseFloat(number).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' ر.س';
            }

            // دالة تنسيق الأرقام
            function formatNumber(number) {
                return parseFloat(number).toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
            }

            // دالة الرسوم المتحركة للأرقام
            function animateValue(element, start, end, duration, isCurrency = false) {
                const obj = $(element);
                const range = end - start;
                const increment = end > start ? 1 : -1;
                const stepTime = Math.abs(Math.floor(duration / range));
                const timer = setInterval(function() {
                    start += increment;
                    if (isCurrency) {
                        obj.text(formatCurrency(start));
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
                const fileName = `ميزان_المراجعة_للمخزون_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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
                doc.text('ميزان المراجعة للمخزون', 150, 20, { align: 'center' });

                // إضافة التاريخ
                doc.setFontSize(10);
                doc.text(`تاريخ الطباعة: ${new Date().toLocaleDateString('ar-SA')}`, 20, 30);

                // تصدير الجدول
                doc.autoTable({
                    html: '#reportContainer table',
                    startY: 40,
                    styles: {
                        fontSize: 6,
                        cellPadding: 1
                    },
                    headStyles: {
                        fillColor: [102, 126, 234],
                        textColor: 255
                    }
                });

                const fileName = `ميزان_المراجعة_للمخزون_${new Date().toISOString().split('T')[0]}.pdf`;
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

            // تهيئة التواريخ عند التحميل
            handleDateTypeChange();
        });
    </script>

    <style>
        /* إضافات CSS خاصة بالجدول */
        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }

        /* تحسينات للجوال */
        @media (max-width: 768px) {
            .trial-balance-table {
                font-size: 0.75rem;
            }

            .avatar-sm {
                width: 24px;
                height: 24px;
                font-size: 0.6rem;
            }
        }

        /* تحسينات للطباعة */
        @media print {
            .no-print {
                display: none !important;
            }

            .page-header {
                background: none !important;
                color: black !important;
                -webkit-print-color-adjust: exact;
            }

            .card-modern {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
            }

            .trial-balance-table th {
                background: #f8f9fa !important;
                color: black !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
@endsection