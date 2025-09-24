@extends('master')

@section('title')
    تقرير مبيعات المنتجات
@stop

@section('css')
    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/report.css') }}">



@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="slide-in">
                        <i class="fas fa-box me-3"></i>
                        تقارير مبيعات المنتجات
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير مبيعات المنتجات</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-boxes"></i>
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
                            <label class="form-label-modern">المنتج</label>
                            <select name="product" id="product" class="form-control select2">
                                <option value="">جميع المنتجات</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} - {{ $product->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">فئة المنتج</label>
                            <select name="category" id="category" class="form-control select2">
                                <option value="">جميع الفئات</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">فئة العميل</label>
                            <select name="client_category" id="client_category" class="form-control select2">
                                <option value="">جميع فئات العملاء</option>
                                @foreach ($client_categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">العميل</label>
                            <select name="client" id="client" class="form-control select2">
                                <option value="">جميع العملاء</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->trade_name }}-{{ $client->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Second Row -->
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
                            <label class="form-label-modern">المخزن</label>
                            <select name="storehouse" id="storehouse" class="form-control select2">
                                <option value="">جميع المخازن</option>
                                @foreach ($storehouses as $storehouse)
                                    <option value="{{ $storehouse->id }}">{{ $storehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">حالة الدفع</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="">الكل</option>
                                <option value="1">مدفوعة</option>
                                <option value="3">غير مدفوعة</option>
                                <option value="2">مدفوعة جزئياً</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">نوع الفاتورة</label>
                            <select name="invoice_type" id="invoice_type" class="form-control select2">
                                <option value="">الكل</option>
                                <option value="normal">مبيعات</option>
                                <option value="returned">مرتجع</option>
                            </select>
                        </div>

                        <!-- Third Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">تمت الإضافة بواسطة</label>
                            <select name="added_by" id="added_by" class="form-control select2">
                                <option value="">الكل</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">نوع التاريخ</label>
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
                            <label class="form-label-modern">نوع التقرير</label>
                            <select name="report_type" id="report_type" class="form-control select2">
                                <option value="">الكل</option>
                                <option value="daily">يومي</option>
                                <option value="weekly">أسبوعي</option>
                                <option value="monthly">شهري</option>
                                <option value="yearly">سنوي</option>
                                <option value="sales_manager">مدير مبيعات</option>
                                <option value="employee">موظفين</option>
                                <option value="returns">مرتجعات</option>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="col-lg-3 col-md-6" id="custom_dates">
                            <label class="form-label-modern">من تاريخ</label>
                            <input type="date" name="from_date" id="from_date" class="form-control"
                                value="{{ $fromDate->format('Y-m-d') }}">
                        </div>

                        <div class="col-lg-3 col-md-6" id="custom_to_date">
                            <label class="form-label-modern">إلى تاريخ</label>
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
                <div class="stats-card">
                    <div class="stats-icon primary">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="stats-value" id="totalQuantity">{{ number_format($totals['total_quantity'] ?? 0, 0) }}</div>
                    <div class="stats-label">إجمالي الكمية</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stats-value" id="totalSales">{{ number_format($totals['total_sales'] ?? 0, 2) }}</div>
                    <div class="stats-label">إجمالي المبيعات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div class="stats-value" id="totalDiscount">{{ number_format($totals['total_discount'] ?? 0, 2) }}</div>
                    <div class="stats-label">إجمالي الخصومات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon danger">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="stats-value" id="totalReturns">{{ number_format($totals['total_returns'] ?? 0, 2) }}</div>
                    <div class="stats-label">إجمالي المرتجعات (ريال)</div>
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
                    تقرير مبيعات المنتجات من {{ $fromDate->format('d/m/Y') }} إلى {{ $toDate->format('d/m/Y') }}
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-box me-2"></i>المنتج</th>
                                <th><i class="fas fa-file-invoice me-2"></i>رقم الفاتورة</th>
                                <th><i class="fas fa-calendar me-2"></i>التاريخ</th>
                                <th><i class="fas fa-building me-2"></i>العميل</th>
                                <th><i class="fas fa-sort-numeric-up me-2"></i>الكمية</th>
                                <th><i class="fas fa-money-bill me-2"></i>سعر الوحدة (ريال)</th>
                                <th><i class="fas fa-percent me-2"></i>الخصم (ريال)</th>
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
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // تهيئة Select2 بدون بحث
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
                'height': '38px',
                'border': '1px solid #ced4da',
                'border-radius': '0.375rem'
            });

            // إزالة أي عناصر إضافية قد تظهر تحت Select2
            $('.select2-container').each(function() {
                $(this).next('.select2-dropdown').remove();
                $(this).siblings('.select2-dropdown').remove();
            });

            // تحسينات للجوال
            $('.select2').on('select2:open', function() {
                $('.select2-dropdown').css({
                    'z-index': '9999',
                    'max-height': '300px',
                    'overflow-y': 'auto'
                });

                // للجوال - تفعيل البحث
                if (window.innerWidth <= 768) {
                    $('.select2-search__field').attr('readonly', false);
                    $('.select2-search__field').focus();
                    $('.select2-search__field').click();
                }
            });

            // تحسين إضافي للجوال
            $('.select2').on('select2:opening', function() {
                if (window.innerWidth <= 768) {
                    $('.select2-search__field').attr('type', 'search');
                    $('.select2-search__field').attr('inputmode', 'search');
                    $('.select2-search__field').focus();
                }
            });

            // تخصيص تصميم Select2
            $('.select2-container--bootstrap-5 .select2-selection--single').css({
                'border': '2px solid var(--gray-200)',
                'border-radius': '12px',
                'height': 'auto',
                'padding': '0.5rem 1rem',
                'min-height': '48px'
            });

            $('.select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered').css({
                'padding': '0',
                'line-height': '1.5'
            });

            $('.select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow').css({
                'top': '50%',
                'transform': 'translateY(-50%)'
            });

            // تحميل البيانات الأولية
            loadReportData();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // التعامل مع تغيير نوع التاريخ
            $('#date_type').change(function() {
                const dateType = $(this).val();
                if (dateType === 'custom') {
                    $('#custom_dates, #custom_to_date').fadeIn();
                } else {
                    $('#custom_dates, #custom_to_date').fadeOut();
                    setDateRange(dateType);
                }
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
                $('.select2').val(null).trigger('change'); // إعادة تعيين Select2
                $('#date_type').val('custom').trigger('change');
                $('#from_date').val('{{ $fromDate->format('Y-m-d') }}');
                $('#to_date').val('{{ $toDate->format('Y-m-d') }}');
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

            $('#reportForm input[type="date"]').change(function() {
                if ($('#date_type').val() === 'custom') {
                    loadReportData();
                }
            });

            // دالة تحميل بيانات التقرير
            function loadReportData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#reportForm').serialize();

                $.ajax({
                    url: '{{ route('salesReports.byProductReportAjax') }}',
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
                animateValue('#totalQuantity', 0, data.totals.total_quantity, 1000);
                animateValue('#totalSales', 0, data.totals.total_sales, 1000);
                animateValue('#totalDiscount', 0, data.totals.total_discount, 1000);
                animateValue('#totalReturns', 0, data.totals.total_returns, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير مبيعات المنتجات من ${data.from_date} إلى ${data.to_date}
                `);

                // تحديث جدول البيانات
                updateTableBody(data.grouped_products, data.totals);
            }

            // دالة تحديث محتوى الجدول
            function updateTableBody(groupedProducts, totals) {
                let tableHtml = '';
                let grandQuantityTotal = 0;
                let grandDiscountTotal = 0;
                let grandAmountTotal = 0;

                // تكرار عبر المجموعات
                Object.keys(groupedProducts).forEach(productId => {
                    const items = groupedProducts[productId];
                    const productName = items[0].product.name;
                    const productCode = items[0].product.code;
                    const categoryName = items[0].product.category ? items[0].product.category.name : 'غير محدد';

                    // صف رأس المنتج
                    tableHtml += `
                        <tr class="table-product-header">
                            <td colspan="8">
                                <i class="fas fa-box me-2"></i>
                                <strong>${productName} (${productCode}) - ${categoryName}</strong>
                            </td>
                        </tr>
                    `;

                    let productQuantityTotal = 0;
                    let productDiscountTotal = 0;
                    let productAmountTotal = 0;

                    // تكرار عبر عناصر المنتج
                    items.forEach(item => {
                        const isReturn = ['return', 'returned'].includes(item.invoice.type);
                        const rowClass = isReturn ? 'table-return' : '';

                        tableHtml += `<tr class="${rowClass}">`;
                        tableHtml += `<td>${productName} (${productCode})</td>`;

                        // رقم الفاتورة
                        if (isReturn) {
                            tableHtml += `
                                <td>
                                    <span class="badge bg-danger">
                                        <i class="fas fa-undo me-1"></i>
                                        مرتجع #${String(item.invoice.code).padStart(5, '0')}
                                    </span>
                                </td>
                            `;
                        } else {
                            tableHtml += `
                                <td>
                                    <span class="badge bg-primary">
                                        #${String(item.invoice.code).padStart(5, '0')}
                                    </span>
                                </td>
                            `;
                        }

                        tableHtml += `<td>${formatDate(item.invoice.invoice_date)}</td>`;
                        tableHtml += `<td>${item.invoice.client ? item.invoice.client.trade_name : 'غير محدد'}</td>`;

                        if (isReturn) {
                            tableHtml += `<td class="text-danger fw-bold">-${formatNumber(item.quantity)}</td>`;
                            tableHtml += `<td class="text-danger fw-bold">${formatNumber(item.unit_price)}</td>`;
                            tableHtml += `<td class="text-danger fw-bold">${formatNumber(item.discount_amount || 0)}</td>`;
                            tableHtml += `<td class="text-danger fw-bold">-${formatNumber(item.total_amount)}</td>`;

                            productQuantityTotal -= parseFloat(item.quantity);
                            productDiscountTotal += parseFloat(item.discount_amount || 0);
                            productAmountTotal -= parseFloat(item.total_amount);
                        } else {
                            tableHtml += `<td class="fw-bold">${formatNumber(item.quantity)}</td>`;
                            tableHtml += `<td class="fw-bold">${formatNumber(item.unit_price)}</td>`;
                            tableHtml += `<td class="fw-bold">${formatNumber(item.discount_amount || 0)}</td>`;
                            tableHtml += `<td class="fw-bold">${formatNumber(item.total_amount)}</td>`;

                            productQuantityTotal += parseFloat(item.quantity);
                            productDiscountTotal += parseFloat(item.discount_amount || 0);
                            productAmountTotal += parseFloat(item.total_amount);
                        }

                        tableHtml += `</tr>`;
                    });

                    // صف إجمالي المنتج
                    tableHtml += `
                        <tr class="table-product-total">
                            <td colspan="4">
                                <i class="fas fa-calculator me-2"></i>
                                <strong>مجموع ${productName}</strong>
                            </td>
                            <td class="fw-bold">${formatNumber(productQuantityTotal)}</td>
                            <td class="fw-bold">-</td>
                            <td class="fw-bold">${formatNumber(productDiscountTotal)}</td>
                            <td class="fw-bold">${formatNumber(productAmountTotal)}</td>
                        </tr>
                    `;

                    grandQuantityTotal += productQuantityTotal;
                    grandDiscountTotal += productDiscountTotal;
                    grandAmountTotal += productAmountTotal;
                });

                // صف الإجمالي العام
                tableHtml += `
                    <tr class="table-grand-total">
                        <td colspan="4">
                            <i class="fas fa-chart-bar me-2"></i>
                            <strong>المجموع الكلي</strong>
                        </td>
                        <td class="fw-bold">${formatNumber(grandQuantityTotal)}</td>
                        <td class="fw-bold">-</td>
                        <td class="fw-bold">${formatNumber(grandDiscountTotal)}</td>
                        <td class="fw-bold">${formatNumber(grandAmountTotal)}</td>
                    </tr>
                `;

                $('#reportTableBody').html(tableHtml);
            }

            // دالة تحديد نطاق التاريخ
            function setDateRange(dateType) {
                const today = new Date();
                let fromDate, toDate;

                switch (dateType) {
                    case 'today':
                        fromDate = toDate = today;
                        break;
                    case 'yesterday':
                        fromDate = toDate = new Date(today.getTime() - 24 * 60 * 60 * 1000);
                        break;
                    case 'this_week':
                        const startOfWeek = new Date(today);
                        startOfWeek.setDate(today.getDate() - today.getDay());
                        fromDate = startOfWeek;
                        toDate = today;
                        break;
                    case 'last_week':
                        const lastWeekEnd = new Date(today);
                        lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
                        const lastWeekStart = new Date(lastWeekEnd);
                        lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
                        fromDate = lastWeekStart;
                        toDate = lastWeekEnd;
                        break;
                    case 'this_month':
                        fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                        toDate = today;
                        break;
                    case 'last_month':
                        fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        toDate = new Date(today.getFullYear(), today.getMonth(), 0);
                        break;
                    case 'this_year':
                        fromDate = new Date(today.getFullYear(), 0, 1);
                        toDate = today;
                        break;
                    case 'last_year':
                        fromDate = new Date(today.getFullYear() - 1, 0, 1);
                        toDate = new Date(today.getFullYear() - 1, 11, 31);
                        break;
                    default:
                        return;
                }

                $('#from_date').val(formatDateForInput(fromDate));
                $('#to_date').val(formatDateForInput(toDate));
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
                const fileName = `تقرير_مبيعات_المنتجات_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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

                // يمكن إضافة منطق تبديل العرض هنا
                if ($(this).attr('id') === 'summaryViewBtn') {
                    // عرض الملخص
                    showAlert('تم التبديل إلى عرض الملخص', 'info');
                } else {
                    // عرض التفاصيل
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

            // تحديث التوقيت الحقيقي (اختياري)
            setInterval(() => {
                const now = new Date();
                $('#current-time').text(now.toLocaleTimeString('ar-SA'));
            }, 1000);
        });

        // دوال إضافية للتحسينات

        // دالة التحقق من صحة النموذج
        function validateForm() {
            const fromDate = new Date($('#from_date').val());
            const toDate = new Date($('#to_date').val());

            if (fromDate > toDate) {
                showAlert('تاريخ البداية يجب أن يكون قبل تاريخ النهاية', 'danger');
                return false;
            }

            return true;
        }

        // دالة تحسين الأداء - تأخير التحديث
        let updateTimeout;

        function debounceUpdate() {
            clearTimeout(updateTimeout);
            updateTimeout = setTimeout(() => {
                if (validateForm()) {
                    loadReportData();
                }
            }, 500);
        }

        // تطبيق التأخير على التحديثات
        $('#reportForm input[type="date"]').on('input', debounceUpdate);
    </script>
@endsection