@extends('master')

@section('title')
    تقرير القيود المحاسبية
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
                        <i class="fas fa-book me-3"></i>
                        تقرير القيود المحاسبية
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير القيود المحاسبية</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-journal-whills"></i>
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
                            <label class="form-label-modern"><i class="fas fa-user me-2"></i>العميل</label>
                            <select name="client" id="client" class="form-control select2">
                                <option value="">جميع العملاء</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->trade_name }}-{{ $client->code }}</option>
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
                            <label class="form-label-modern"><i class="fas fa-bullseye me-2"></i>مركز التكلفة</label>
                            <select name="cost_center" id="cost_center" class="form-control select2">
                                <option value="">جميع مراكز التكلفة</option>
                                @foreach ($costCenters as $costCenter)
                                    <option value="{{ $costCenter->id }}">{{ $costCenter->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-check-circle me-2"></i>الحالة</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="">جميع الحالات</option>
                                <option value="0">معلق</option>
                                <option value="1">معتمد</option>
                                <option value="2">مرفوض</option>
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
                <div class="stats-card primary">
                    <div class="stats-icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="stats-value" id="totalDebits">{{ number_format($totals['total_debits'] ?? 0, 2) }}</div>
                    <div class="stats-label">إجمالي المدين (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="stats-value" id="totalCredits">{{ number_format($totals['total_credits'] ?? 0, 2) }}</div>
                    <div class="stats-label">إجمالي الدائن (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stats-value" id="totalCount">{{ number_format($totals['total_count'] ?? 0, 0) }}</div>
                    <div class="stats-label">عدد القيود</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-value" id="employeesCount">{{ number_format($totals['employees_count'] ?? 0, 0) }}</div>
                    <div class="stats-label">عدد الموظفين</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني للقيود المحاسبية حسب الموظف
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="journalEntriesChart"></canvas>
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
                    تقرير القيود المحاسبية من {{ $fromDate->format('d/m/Y') }} إلى {{ $toDate->format('d/m/Y') }}
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-barcode me-2"></i>رقم القيد</th>
                                <th><i class="fas fa-calendar me-2"></i>التاريخ</th>
                                <th><i class="fas fa-sticky-note me-2"></i>الوصف</th>
                                <th><i class="fas fa-user-tie me-2"></i>الموظف</th>
                                <th><i class="fas fa-user me-2"></i>العميل</th>
                                <th><i class="fas fa-building me-2"></i>الفرع</th>
                                <th><i class="fas fa-arrow-up me-2 text-success"></i>مدين (ريال)</th>
                                <th><i class="fas fa-arrow-down me-2 text-danger"></i>دائن (ريال)</th>
                                <th><i class="fas fa-balance-scale me-2"></i>متوازن</th>
                                <th><i class="fas fa-check-circle me-2"></i>الحالة</th>
                                <th><i class="fas fa-eye me-2"></i>تفاصيل</th>
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
        let journalEntriesChart;

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
                }
            });

            // تحميل البيانات الأولية
            loadReportData();

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

            // التعامل مع تصدير PDF
            $('#exportPdf').click(function() {
                exportToPDF();
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
                    url: '{{ route('GeneralAccountReports.JournalEntriesByEmployeeAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateReportDisplay(response);
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

            // دالة تحديث عرض التقرير
            function updateReportDisplay(data) {
                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#totalDebits', 0, data.totals.total_debits, 1000);
                animateValue('#totalCredits', 0, data.totals.total_credits, 1000);
                animateValue('#totalCount', 0, data.totals.total_count, 1000);
                animateValue('#employeesCount', 0, data.totals.employees_count, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تقرير القيود المحاسبية من ${data.from_date} إلى ${data.to_date}
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.grouped_data, data.totals);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('journalEntriesChart').getContext('2d');

                if (journalEntriesChart) {
                    journalEntriesChart.destroy();
                }

                journalEntriesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'المدين (ريال)',
                            data: chartData.debits,
                            backgroundColor: 'rgba(25, 135, 84, 0.8)',
                            borderColor: 'rgba(25, 135, 84, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        }, {
                            label: 'الدائن (ريال)',
                            data: chartData.credits,
                            backgroundColor: 'rgba(220, 53, 69, 0.8)',
                            borderColor: 'rgba(220, 53, 69, 1)',
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
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // دالة تحديث محتوى الجدول
            function updateTableBody(groupedData, totals) {
                let tableHtml = '';
                let counter = 1;
                let grandTotalDebits = 0;
                let grandTotalCredits = 0;

                // تكرار عبر المجموعات (الموظفين)
                Object.keys(groupedData).forEach(employeeId => {
                    const employeeData = groupedData[employeeId];
                    const employee = employeeData.employee;

                    // صف رأس الموظف
                    tableHtml += `
                        <tr class="table-employee-header">
                            <td colspan="12">
                                <i class="fas fa-user-tie me-2"></i>
                                <strong>${employee.name} - ${employee.code || 'لا يوجد رمز'}</strong>
                                <span class="ms-3">
                                    (عدد القيود: ${employeeData.entries_count} |
                                    مدين: ${formatNumber(employeeData.total_debits)} ريال |
                                    دائن: ${formatNumber(employeeData.total_credits)} ريال)
                                </span>
                            </td>
                        </tr>
                    `;

                    // عرض القيود للموظف
                    if (employeeData.entries && employeeData.entries.length > 0) {
                        employeeData.entries.forEach(entry => {
                            tableHtml += `<tr class="journal-entry-row journal-details-toggle" data-entry-id="${entry.id}">`;
                            tableHtml += `<td>${counter++}</td>`;
                            tableHtml += `<td><strong>${entry.reference_number}</strong></td>`;
                            tableHtml += `<td>${formatDate(entry.date)}</td>`;
                            tableHtml += `<td>${entry.description || '--'}</td>`;
                            tableHtml += `<td>${employee.name}</td>`;
                            tableHtml += `<td>${entry.client ? entry.client.name : 'غير محدد'}</td>`;
                            tableHtml += `<td>${entry.branch ? entry.branch.name : 'غير محدد'}</td>`;
                            tableHtml += `<td class="debit-amount">${formatNumber(entry.total_debits)}</td>`;
                            tableHtml += `<td class="credit-amount">${formatNumber(entry.total_credits)}</td>`;
                            tableHtml += `<td>${getBalanceBadge(entry.is_balanced)}</td>`;
                            tableHtml += `<td>${getStatusBadge(entry.status)}</td>`;
                            tableHtml += `<td><i class="fas fa-chevron-right expand-icon" style="cursor: pointer;"></i></td>`;
                            tableHtml += `</tr>`;

                            // تفاصيل القيد (مخفية افتراضياً)
                            if (entry.details && entry.details.length > 0) {
                                entry.details.forEach(detail => {
                                    tableHtml += `<tr class="journal-detail-row journal-details-collapsed" data-parent-entry="${entry.id}">`;
                                    tableHtml += `<td></td>`;
                                    tableHtml += `<td colspan="3"><i class="fas fa-angle-right me-2"></i>${detail.account_name}</td>`;
                                    tableHtml += `<td colspan="3">${detail.description || '--'}</td>`;
                                    tableHtml += `<td class="debit-amount">${formatNumber(detail.debit)}</td>`;
                                    tableHtml += `<td class="credit-amount">${formatNumber(detail.credit)}</td>`;
                                    tableHtml += `<td colspan="3">-</td>`;
                                    tableHtml += `</tr>`;
                                });
                            }
                        });
                    }

                    // صف إجمالي الموظف
                    tableHtml += `
                        <tr class="table-employee-total">
                            <td colspan="7">
                                <i class="fas fa-calculator me-2"></i>
                                <strong>مجموع ${employee.name}</strong>
                            </td>
                            <td class="fw-bold debit-amount">${formatNumber(employeeData.total_debits)}</td>
                            <td class="fw-bold credit-amount">${formatNumber(employeeData.total_credits)}</td>
                            <td colspan="3">-</td>
                        </tr>
                    `;

                    grandTotalDebits += employeeData.total_debits;
                    grandTotalCredits += employeeData.total_credits;
                });

                // صف الإجمالي العام
                tableHtml += `
                    <tr class="table-grand-total">
                        <td colspan="7">
                            <i class="fas fa-chart-bar me-2"></i>
                            <strong>المجموع الكلي</strong>
                        </td>
                        <td class="fw-bold">${formatNumber(grandTotalDebits)}</td>
                        <td class="fw-bold">${formatNumber(grandTotalCredits)}</td>
                        <td colspan="3">-</td>
                    </tr>
                `;

                $('#reportTableBody').html(tableHtml);

                // إضافة أحداث توسيع/طي التفاصيل
                $('.journal-details-toggle').click(function() {
                    const entryId = $(this).data('entry-id');
                    const detailRows = $(`.journal-details-collapsed[data-parent-entry="${entryId}"]`);
                    const expandIcon = $(this).find('.expand-icon');

                    if (detailRows.is(':visible')) {
                        detailRows.hide();
                        expandIcon.removeClass('expanded');
                    } else {
                        detailRows.show();
                        expandIcon.addClass('expanded');
                    }
                });
            }

            // دالة إرجاع شارة التوازن
            function getBalanceBadge(isBalanced) {
                if (isBalanced) {
                    return '<span class="badge bg-success balance-badge"><i class="fas fa-check me-1"></i>متوازن</span>';
                } else {
                    return '<span class="badge bg-danger balance-badge"><i class="fas fa-times me-1"></i>غير متوازن</span>';
                }
            }

            // دالة إرجاع شارة الحالة
            function getStatusBadge(status) {
                switch(status) {
                    case 1:
                        return '<span class="badge bg-success"><i class="fas fa-check me-1"></i>معتمد</span>';
                    case 0:
                        return '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>معلق</span>';
                    case 2:
                        return '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>مرفوض</span>';
                    default:
                        return '<span class="badge bg-secondary"><i class="fas fa-question me-1"></i>غير محدد</span>';
                }
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

                if (range === 0) {
                    obj.text(formatNumber(end));
                    return;
                }

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
                const fileName = `تقرير_القيود_المحاسبية_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

                XLSX.writeFile(wb, fileName);
                showAlert('تم تصدير الملف بنجاح!', 'success');
            }

            // دالة تصدير PDF
            function exportToPDF() {
                showAlert('جاري تصدير ملف PDF...', 'info');

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('p', 'mm', 'a4');

                // إضافة العنوان
                doc.setFontSize(16);
                doc.text('تقرير القيود المحاسبية', 105, 20, { align: 'center' });

                // إضافة التاريخ
                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();
                doc.setFontSize(12);
                doc.text(`من ${fromDate} إلى ${toDate}`, 105, 30, { align: 'center' });

                // حفظ الملف
                const today = new Date();
                const fileName = `تقرير_القيود_المحاسبية_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.pdf`;

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

            // Add animations on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);
        });
    </script>
@endsection
