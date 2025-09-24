@extends('master')

@section('title')
    تقرير المشتريات
@stop

@section('css')
    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/report.css') }}">
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="slide-in">
                        <i class="fas fa-shopping-cart me-3"></i>
                        تقارير المشتريات
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير المشتريات</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-shopping-cart"></i>
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
                            <label class="form-label-modern">المورد</label>
                            <select name="supplier" id="supplier" class="form-control select2">
                                <option value="">جميع الموردين</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->trade_name }}</option>
                                @endforeach
                            </select>
                        </div>



                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">حالة الدفع</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="">الكل</option>
                                <option value="1">مدفوعة</option>
                                <option value="0">غير مدفوعة</option>
                                <option value="5">مرتجعة</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">نوع الفاتورة</label>
                            <select name="invoice_type" id="invoice_type" class="form-control select2">
                                <option value="">الكل</option>
                                <option value="invoice">فاتورة</option>
                                <option value="return">مرتجع</option>
                                <option value="requested">أمر شراء</option>
                                <option value="city_notice">إشعار دائن</option>
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern">تمت الإضافة بواسطة</label>
                            <select name="added_by" id="added_by" class="form-control select2">
                                <option value="">الكل</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
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
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stats-value" id="totalPurchases">{{ number_format($totals['total_purchases'] ?? 0, 2) }}</div>
                    <div class="stats-label">إجمالي المشتريات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-value" id="paidAmount">{{ number_format($totals['paid_amount'] ?? 0, 2) }}</div>
                    <div class="stats-label">المبالغ المدفوعة (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-value" id="unpaidAmount">{{ number_format($totals['unpaid_amount'] ?? 0, 2) }}</div>
                    <div class="stats-label">المبالغ غير المدفوعة (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon danger">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="stats-value" id="returnedAmount">{{ number_format($totals['total_returns'] ?? 0, 2) }}</div>
                    <div class="stats-label">المبالغ المرتجعة (ريال)</div>
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
                    تقرير المشتريات من {{ $fromDate->format('d/m/Y') }} إلى {{ $toDate->format('d/m/Y') }}
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user me-2"></i>الموظف</th>
                                <th><i class="fas fa-file-invoice me-2"></i>رقم الفاتورة</th>
                                <th><i class="fas fa-tag me-2"></i>النوع</th>
                                <th><i class="fas fa-calendar me-2"></i>التاريخ</th>
                                <th><i class="fas fa-truck me-2"></i>المورد</th>
                                <th><i class="fas fa-check me-2"></i>مدفوعة (ريال)</th>
                                <th><i class="fas fa-clock me-2"></i>غير مدفوعة (ريال)</th>
                                <th><i class="fas fa-undo me-2"></i>مرتجع (ريال)</th>
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
                dropdownCssClass: 'select2-dropdown-custom',
                minimumInputLength: 0,
                closeOnSelect: true,
                selectOnClose: false
            });

            // تخصيص تصميم Select2
            $('.select2-container--bootstrap-5 .select2-selection--single').css({
                'border': '2px solid var(--gray-200)',
                'border-radius': '12px',
                'height': 'auto',
                'padding': '0.5rem 1rem',
                'min-height': '48px'
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
                $('.select2').val(null).trigger('change');
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
                    url: '{{ route('ReportsPurchases.purchaseByEmployeeAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateReportDisplay(response);
                        $('.loading-overlay').fadeOut();
                        $('#filterBtn').prop('disabled', false);

                        $('#reportContainer').addClass('fade-in');
                    },
                    error: function(xhr, status, error) {
                        console.error('خطأ في تحميل البيانات:', error);
                        $('.loading-overlay').fadeOut();
                        $('#filterBtn').prop('disabled', false);

                        showAlert('حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.', 'danger');
                    }
                });
            }

            // دالة تحديث عرض التقرير
            function updateReportDisplay(data) {
                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#totalPurchases', 0, data.totals.total_purchases, 1000);
                animateValue('#paidAmount', 0, data.totals.paid_amount, 1000);
                animateValue('#unpaidAmount', 0, data.totals.unpaid_amount, 1000);
                animateValue('#returnedAmount', 0, data.totals.total_returns, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير المشتريات من ${data.from_date} إلى ${data.to_date}
                `);

                // تحديث جدول البيانات
                updateTableBody(data.grouped_invoices, data.totals);
            }

            // دالة تحديث محتوى الجدول
            function updateTableBody(groupedInvoices, totals) {
                let tableHtml = '';
                let grandPaidTotal = 0;
                let grandUnpaidTotal = 0;
                let grandReturnedTotal = 0;
                let grandOverallTotal = 0;

                // تكرار عبر المجموعات
                Object.keys(groupedInvoices).forEach(employeeId => {
                    const invoices = groupedInvoices[employeeId];
                    const employeeName = invoices[0].creator ? invoices[0].creator.name : 'موظف ' + employeeId;

                    // صف رأس الموظف
                    tableHtml += `
                        <tr class="table-employee-header">
                            <td colspan="8">
                                <i class="fas fa-user me-2"></i>
                                <strong>${employeeName}</strong>
                            </td>
                        </tr>
                    `;

                    let employeePaidTotal = 0;
                    let employeeUnpaidTotal = 0;
                    let employeeReturnedTotal = 0;
                    let employeeOverallTotal = 0;

                    // تكرار عبر فواتير الموظف
                    invoices.forEach(invoice => {
                        const isReturn = ['Return', 'returned'].includes(invoice.type);
                        const rowClass = isReturn ? 'table-return' : '';

                        tableHtml += `<tr class="${rowClass}">`;
                        tableHtml += `<td>${employeeName}</td>`;

                        // رقم الفاتورة
                        if (isReturn) {
                            tableHtml += `
                                <td>
                                    <span class="badge bg-danger">
                                        <i class="fas fa-undo me-1"></i>
                                        مرتجع #${String(invoice.code).padStart(5, '0')}
                                    </span>
                                </td>
                            `;
                        } else {
                            tableHtml += `
                                <td>
                                    <span class="badge bg-primary">
                                        #${String(invoice.code).padStart(5, '0')}
                                    </span>
                                </td>
                            `;
                        }

                        // عمود النوع
                        let typeHtml = '';
                        let typeClass = '';
                        let typeIcon = '';
                        let typeText = '';
                        
                        switch(invoice.type) {
                            case 'invoice':
                                typeClass = 'bg-primary';
                                typeIcon = 'fas fa-file-invoice';
                                typeText = 'فاتورة';
                                break;
                            case 'Return':
                            case 'returned':
                                typeClass = 'bg-danger';
                                typeIcon = 'fas fa-undo';
                                typeText = 'مرتجع';
                                break;
                            case 'Requested':
                                typeClass = 'bg-warning';
                                typeIcon = 'fas fa-shopping-cart';
                                typeText = 'أمر شراء';
                                break;
                            case 'City Notice':
                                typeClass = 'bg-info';
                                typeIcon = 'fas fa-credit-card';
                                typeText = 'إشعار دائن';
                                break;
                            default:
                                typeClass = 'bg-secondary';
                                typeIcon = 'fas fa-question';
                                typeText = 'غير محدد';
                        }
                        
                        typeHtml = `
                            <td>
                                <span class="badge ${typeClass}">
                                    <i class="${typeIcon} me-1"></i>
                                    ${typeText}
                                </span>
                            </td>
                        `;
                        tableHtml += typeHtml;

                        tableHtml += `<td>${formatDate(invoice.date)}</td>`;
                        tableHtml += `<td>${invoice.supplier ? invoice.supplier.trade_name : 'غير محدد'}</td>`;

                        if (isReturn) {
                            tableHtml += `<td>-</td>`;
                            tableHtml += `<td>-</td>`;
                            tableHtml += `<td class="text-danger fw-bold">${formatNumber(invoice.grand_total)}</td>`;
                            tableHtml += `<td class="text-danger fw-bold">-${formatNumber(invoice.grand_total)}</td>`;

                            employeeReturnedTotal += parseFloat(invoice.grand_total);
                            grandReturnedTotal += parseFloat(invoice.grand_total);
                            grandOverallTotal -= parseFloat(invoice.grand_total);
                            employeeOverallTotal -= parseFloat(invoice.grand_total);
                        } else {
                            const paidAmount = invoice.is_paid == 1 ? invoice.grand_total : invoice.paid_amount;
                            const unpaidAmount = invoice.is_paid == 1 ? 0 : invoice.due_value;

                            tableHtml += `<td class="text-success fw-bold">${formatNumber(paidAmount)}</td>`;
                            tableHtml += `<td class="text-warning fw-bold">${formatNumber(unpaidAmount)}</td>`;
                            tableHtml += `<td>-</td>`;
                            tableHtml += `<td class="fw-bold">${formatNumber(invoice.grand_total)}</td>`;

                            if (invoice.is_paid == 1) {
                                employeePaidTotal += parseFloat(invoice.grand_total);
                                grandPaidTotal += parseFloat(invoice.grand_total);
                            } else {
                                employeeUnpaidTotal += parseFloat(unpaidAmount);
                                grandUnpaidTotal += parseFloat(unpaidAmount);
                            }
                            employeeOverallTotal += parseFloat(invoice.grand_total);
                            grandOverallTotal += parseFloat(invoice.grand_total);
                        }

                        tableHtml += `</tr>`;
                    });

                    // صف إجمالي الموظف
                    tableHtml += `
                        <tr class="table-employee-total">
                            <td colspan="5">
                                <i class="fas fa-calculator me-2"></i>
                                <strong>مجموع ${employeeName}</strong>
                            </td>
                            <td class="fw-bold">${formatNumber(employeePaidTotal)}</td>
                            <td class="fw-bold">${formatNumber(employeeUnpaidTotal)}</td>
                            <td class="fw-bold">${formatNumber(employeeReturnedTotal)}</td>
                            <td class="fw-bold">${formatNumber(employeeOverallTotal)}</td>
                        </tr>
                    `;
                });

                // صف الإجمالي العام
                tableHtml += `
                    <tr class="table-grand-total">
                        <td colspan="5">
                            <i class="fas fa-chart-bar me-2"></i>
                            <strong>المجموع الكلي</strong>
                        </td>
                        <td class="fw-bold">${formatNumber(grandPaidTotal)}</td>
                        <td class="fw-bold">${formatNumber(grandUnpaidTotal)}</td>
                        <td class="fw-bold">${formatNumber(grandReturnedTotal)}</td>
                        <td class="fw-bold">${formatNumber(grandOverallTotal)}</td>
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
                const fileName = `تقرير_المشتريات_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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
                    showAlert('تم التبديل إلى عرض الملخص', 'info');
                } else {
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
