@extends('master')

@section('title')
    ملخص رصيد المخزون
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

        /* مؤشرات الحالة */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-available {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .status-low {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .status-out {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .status-inactive {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
        }

        /* تحسين بطاقات الإحصائيات */
        .balance-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 20px;
            padding: 2rem;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .balance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            pointer-events: none;
        }

        .balance-card.available {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }

        .balance-card.low-stock {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
        }

        .balance-card.out-of-stock {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            box-shadow: 0 10px 30px rgba(239, 68, 68, 0.3);
        }

        .balance-card.inactive {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            box-shadow: 0 10px 30px rgba(107, 114, 128, 0.3);
        }

        .balance-icon {
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

        .balance-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .balance-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* تحسين صفوف الجدول */
        .product-row {
            transition: all 0.3s ease;
        }

        .product-row:hover {
            background-color: rgba(102, 126, 234, 0.05);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-left: 12px;
        }

        .quantity-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            text-align: center;
            min-width: 80px;
        }

        .quantity-high {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .quantity-medium {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .quantity-low {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .quantity-zero {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }

        /* تحسين المؤشرات البصرية */
        .value-display {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            font-size: 1.1rem;
            direction: ltr;
            text-align: left;
        }

        .value-positive {
            color: #10b981;
        }

        .value-negative {
            color: #ef4444;
        }

        .value-zero {
            color: #6b7280;
        }

        /* رسوم متحركة */
        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(102, 126, 234, 0); }
            100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
        }

        /* تحسينات للجوال */
        @media (max-width: 768px) {
            .balance-card {
                padding: 1.5rem;
            }

            .balance-value {
                font-size: 1.5rem;
            }

            .product-avatar {
                width: 32px;
                height: 32px;
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
                        <i class="fas fa-chart-bar me-3"></i>
                        ملخص رصيد المخزون
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">ملخص رصيد المخزون</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-chart-bar"></i>
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
                <form id="balanceForm">
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
                            <label class="form-label-modern"><i class="fas fa-star me-2"></i>العلامة التجارية</label>
                            <select name="brand" id="brand" class="form-control select2">
                                <option value="">جميع العلامات</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand }}">{{ $brand }}</option>
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

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-info-circle me-2"></i>الحالة</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="">جميع الحالات</option>
                                <option value="1">متاح</option>
                                <option value="2">مخزون منخفض</option>
                                <option value="3">مخزون نفد</option>
                                <option value="4">غير نشط</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-sort me-2"></i>ترتيب حسب</label>
                            <select name="sort_by" id="sort_by" class="form-control select2">
                                <option value="">اختر الترتيب</option>
                                <option value="quantity_desc">الكمية تنازلي</option>
                                <option value="quantity_asc">الكمية تصاعدي</option>
                                <option value="value_desc">القيمة تنازلي</option>
                                <option value="value_asc">القيمة تصاعدي</option>
                                <option value="name_asc">الاسم أبجدياً</option>
                                <option value="name_desc">الاسم عكس أبجدياً</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-layer-group me-2"></i>تجميع حسب</label>
                            <select name="group_by" id="group_by" class="form-control select2">
                                <option value="">بدون تجميع</option>
                                <option value="category">التصنيف</option>
                                <option value="brand">العلامة التجارية</option>
                                <option value="warehouse">المستودع</option>
                                <option value="status">الحالة</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-search me-2"></i>البحث</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="ابحث بالاسم أو الكود...">
                        </div>

                        <!-- Third Row - Options -->
                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="hide-zero-balance" name="hide_zero_balance" class="form-check-input">
                                <label for="hide-zero-balance" class="form-check-label">
                                    <i class="fas fa-eye-slash me-2"></i>إخفاء الرصيد الصفري
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="show-values" name="show_values" class="form-check-input" checked>
                                <label for="show-values" class="form-check-label">
                                    <i class="fas fa-dollar-sign me-2"></i>إظهار القيم المالية
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-lg-6 col-md-12 align-self-end">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn-modern btn-primary-modern" id="filterBtn">
                                    <i class="fas fa-search"></i>
                                    عرض التقرير
                                </button>
                                <button type="button" class="btn-modern btn-outline-modern" id="resetBtn">
                                    <i class="fas fa-refresh"></i>
                                    إلغاء الفلترة
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
                <div class="balance-card available">
                    <div class="balance-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="balance-value" id="availableCount">0</div>
                    <div class="balance-label">منتجات متاحة</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="balance-card low-stock">
                    <div class="balance-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="balance-value" id="lowStockCount">0</div>
                    <div class="balance-label">مخزون منخفض</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="balance-card out-of-stock">
                    <div class="balance-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="balance-value" id="outOfStockCount">0</div>
                    <div class="balance-label">مخزون نفد</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="balance-card inactive">
                    <div class="balance-icon">
                        <i class="fas fa-pause-circle"></i>
                    </div>
                    <div class="balance-value" id="inactiveCount">0</div>
                    <div class="balance-label">غير نشط</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    توزيع المخزون حسب الحالة
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="balanceChart"></canvas>
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
                    ملخص رصيد المخزون - مؤسسة أعمال خاصة للتجارة
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-barcode me-2"></i>الكود</th>
                                <th><i class="fas fa-cube me-2"></i>اسم المنتج</th>
                                <th><i class="fas fa-tags me-2"></i>التصنيف</th>
                                <th><i class="fas fa-star me-2"></i>العلامة التجارية</th>
                                <th><i class="fas fa-warehouse me-2"></i>المستودع</th>
                                <th><i class="fas fa-boxes me-2"></i>الكمية الإجمالية</th>
                                <th><i class="fas fa-dollar-sign me-2"></i>القيمة الإجمالية</th>
                                <th><i class="fas fa-info-circle me-2"></i>الحالة</th>
                            </tr>
                        </thead>
                        <tbody id="balanceTableBody">
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
        let balanceChart;

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
            loadBalanceData();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // التعامل مع تصفية التقرير
            $('#filterBtn').click(function() {
                $(this).addClass('loading');
                loadBalanceData();
            });

            // إعادة تعيين الفلاتر
            $('#resetBtn').click(function() {
                $('#balanceForm')[0].reset();
                $('.select2').val(null).trigger('change');
                $('#hide-zero-balance, #show-values').prop('checked', function(i, val) {
                    return i === 1; // keep show-values checked
                });
                loadBalanceData();
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
            $('.select2, #search').on('change keyup', function() {
                clearTimeout($(this).data('timeout'));
                $(this).data('timeout', setTimeout(function() {
                    loadBalanceData();
                }, 500));
            });

            $('#hide-zero-balance, #show-values').on('change', function() {
                loadBalanceData();
            });

            // دالة تحميل بيانات رصيد المخزون
            function loadBalanceData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#balanceForm').serialize();

                $.ajax({
                    url: '{{ route('StorHouseReport.inventoryBlanceAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateBalanceDisplay(response);
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

            // دالة تحديث عرض رصيد المخزون
            function updateBalanceDisplay(data) {
                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#availableCount', 0, data.totals.available_count, 1000);
                animateValue('#lowStockCount', 0, data.totals.low_stock_count, 1000);
                animateValue('#outOfStockCount', 0, data.totals.out_of_stock_count, 1000);
                animateValue('#inactiveCount', 0, data.totals.inactive_count, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    ملخص رصيد المخزون - ${data.group_by ? 'تجميع حسب ' + data.group_by : 'جميع المنتجات'}
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.products, data.totals);

                // إضافة تأثير النبض للبطاقات التي تحتوي على تحديثات
                $('.balance-card').removeClass('pulse-animation');
                if (data.totals.low_stock_count > 0) {
                    $('.balance-card.low-stock').addClass('pulse-animation');
                }
                if (data.totals.out_of_stock_count > 0) {
                    $('.balance-card.out-of-stock').addClass('pulse-animation');
                }
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('balanceChart').getContext('2d');

                if (balanceChart) {
                    balanceChart.destroy();
                }

                balanceChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['متاح', 'مخزون منخفض', 'مخزون نفد', 'غير نشط'],
                        datasets: [{
                            data: [
                                chartData.available || 0,
                                chartData.low_stock || 0,
                                chartData.out_of_stock || 0,
                                chartData.inactive || 0
                            ],
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(107, 114, 128, 0.8)'
                            ],
                            borderColor: [
                                'rgba(16, 185, 129, 1)',
                                'rgba(245, 158, 11, 1)',
                                'rgba(239, 68, 68, 1)',
                                'rgba(107, 114, 128, 1)'
                            ],
                            borderWidth: 3,
                            hoverOffset: 10
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
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                        return context.label + ': ' + context.parsed + ' منتج (' + percentage + '%)';
                                    }
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
                        quantity: 0,
                        value: 0
                    };

                    products.forEach((product, index) => {
                        const quantity = parseFloat(product.total_quantity || 0);
                        const value = parseFloat(product.total_value || 0);
                        const status = product.status || 'غير محدد';

                        grandTotals.quantity += quantity;
                        grandTotals.value += value;

                        // تحديد حالة المنتج وألوانه
                        const statusInfo = getStatusInfo(status, quantity);
                        const quantityBadge = getQuantityBadge(quantity);

                        tableHtml += `<tr class="product-row">`;
                        tableHtml += `<td>${index + 1}</td>`;
                        tableHtml += `<td><span class="badge bg-secondary">${product.code || product.id}</span></td>`;
                        tableHtml += `<td>
                            <div class="d-flex align-items-center">
                                <div class="product-avatar">
                                    ${product.name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <div class="fw-bold">${product.name}</div>
                                    <small class="text-muted">${product.brand || 'غير محدد'}</small>
                                </div>
                            </div>
                        </td>`;
                        tableHtml += `<td>${product.category ? `<span class="badge bg-info">${product.category}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${product.brand ? `<span class="badge bg-success">${product.brand}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td>${product.warehouse ? `<span class="badge bg-warning">${product.warehouse}</span>` : '<span class="text-muted">غير محدد</span>'}</td>`;
                        tableHtml += `<td class="text-center">
                            <span class="${quantityBadge.class}">${formatNumber(quantity)}</span>
                        </td>`;
                        tableHtml += `<td class="text-center">
                            <span class="value-display ${getValueClass(value)}">${formatCurrency(value)}</span>
                        </td>`;
                        tableHtml += `<td class="text-center">
                            <span class="${statusInfo.class}">
                                <i class="${statusInfo.icon} me-1"></i>
                                ${statusInfo.label}
                            </span>
                        </td>`;
                        tableHtml += `</tr>`;
                    });

                    // إضافة صف الإجمالي العام
                    tableHtml += `
                        <tr class="table-grand-total">
                            <td colspan="6" class="fw-bold">
                                <i class="fas fa-calculator me-2"></i>
                                المجموع الكلي
                            </td>
                            <td class="text-center fw-bold">${formatNumber(grandTotals.quantity)}</td>
                            <td class="text-center fw-bold">${formatCurrency(grandTotals.value)}</td>
                            <td class="text-center">-</td>
                        </tr>
                    `;
                } else {
                    tableHtml = `
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">لا توجد منتجات مطابقة للفلاتر المحددة</p>
                            </td>
                        </tr>
                    `;
                }

                $('#balanceTableBody').html(tableHtml);
            }

            // دالة الحصول على معلومات الحالة
            function getStatusInfo(status, quantity) {
                if (status === 'متاح' || quantity > 10) {
                    return {
                        class: 'status-indicator status-available',
                        icon: 'fas fa-check-circle',
                        label: 'متاح'
                    };
                } else if (status === 'مخزون منخفض' || (quantity > 0 && quantity <= 10)) {
                    return {
                        class: 'status-indicator status-low',
                        icon: 'fas fa-exclamation-triangle',
                        label: 'مخزون منخفض'
                    };
                } else if (status === 'مخزون نفد' || quantity === 0) {
                    return {
                        class: 'status-indicator status-out',
                        icon: 'fas fa-times-circle',
                        label: 'مخزون نفد'
                    };
                } else {
                    return {
                        class: 'status-indicator status-inactive',
                        icon: 'fas fa-pause-circle',
                        label: 'غير نشط'
                    };
                }
            }

            // دالة الحصول على شارة الكمية
            function getQuantityBadge(quantity) {
                if (quantity > 50) {
                    return { class: 'quantity-badge quantity-high' };
                } else if (quantity > 10) {
                    return { class: 'quantity-badge quantity-medium' };
                } else if (quantity > 0) {
                    return { class: 'quantity-badge quantity-low' };
                } else {
                    return { class: 'quantity-badge quantity-zero' };
                }
            }

            // دالة تحديد لون القيمة
            function getValueClass(value) {
                if (value > 0) return 'value-positive';
                if (value < 0) return 'value-negative';
                return 'value-zero';
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
                const fileName = `ملخص_رصيد_المخزون_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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
                doc.text('ملخص رصيد المخزون', 150, 20, { align: 'center' });

                // إضافة التاريخ
                doc.setFontSize(10);
                doc.text(`تاريخ الطباعة: ${new Date().toLocaleDateString('ar-SA')}`, 20, 30);

                // تصدير الجدول
                doc.autoTable({
                    html: '#reportContainer table',
                    startY: 40,
                    styles: {
                        fontSize: 7,
                        cellPadding: 1
                    },
                    headStyles: {
                        fillColor: [102, 126, 234],
                        textColor: 255
                    }
                });

                const fileName = `ملخص_رصيد_المخزون_${new Date().toISOString().split('T')[0]}.pdf`;
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
            $('.balance-card').hover(
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

    <style>
        /* إضافات CSS خاصة بالجدول */
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

            .balance-card {
                background: #f8f9fa !important;
                color: black !important;
                -webkit-print-color-adjust: exact;
            }

            .status-indicator {
                background: #f8f9fa !important;
                color: black !important;
                border: 1px solid #dee2e6 !important;
            }
        }
    </style>
@endsection