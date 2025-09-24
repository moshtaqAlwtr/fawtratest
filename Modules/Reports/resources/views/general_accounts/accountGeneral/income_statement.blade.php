@extends('master')

@section('title')
    تقرير قائمة الدخل
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

         /* إعدادات خاصة للجوال */
         @media (max-width: 768px) {
             .select2-search__field {
                 font-size: 16px !important; /* منع التكبير في iOS */
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

         /* تنسيق الجداول */
         .revenue-row {
             background-color: rgba(25, 135, 84, 0.05) !important;
         }

         .revenue-row:hover {
             background-color: rgba(25, 135, 84, 0.1) !important;
         }

         .expense-row {
             background-color: rgba(220, 53, 69, 0.05) !important;
         }

         .expense-row:hover {
             background-color: rgba(220, 53, 69, 0.1) !important;
         }

         .table-revenue-header {
             background: linear-gradient(135deg, #198754 0%, #20c997 100%) !important;
             color: white !important;
             font-weight: 600;
         }

         .table-expense-header {
             background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%) !important;
             color: white !important;
             font-weight: 600;
         }

         .table-net-income {
             background: linear-gradient(135deg, #6f42c1 0%, #0d6efd 100%) !important;
             color: white !important;
             font-weight: 700;
             font-size: 1.2em;
         }

         /* تحسين تصميم البطاقات */
         .stats-card {
             background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
             border: none;
             border-radius: 20px;
             padding: 2rem;
             color: white;
             transition: all 0.3s ease;
             box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
         }

         .stats-card.revenue {
             background: linear-gradient(135deg, #198754 0%, #20c997 100%);
         }

         .stats-card.expense {
             background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
         }

         .stats-card.net-income {
             background: linear-gradient(135deg, #6f42c1 0%, #0d6efd 100%);
         }

         .stats-card.profit-margin {
             background: linear-gradient(135deg, #4481eb 0%, #04befe 100%);
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
             font-size: 2rem;
             font-weight: 700;
             margin-bottom: 0.5rem;
         }

         .stats-label {
             font-size: 0.9rem;
             opacity: 0.9;
         }

         /* تحسين الأرقام السالبة */
         .negative-amount {
             color: #dc3545 !important;
         }

         .positive-amount {
             color: #198754 !important;
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
                        <i class="fas fa-chart-line me-3"></i>
                        تقرير قائمة الدخل
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير قائمة الدخل</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-chart-pie"></i>
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
                            <label class="form-label-modern"><i class="fas fa-calendar me-2"></i>السنة المالية</label>
                            <select name="financial_year[]" id="financial_year" class="form-control select2" multiple>
                                <option value="current">السنة المفتوحة</option>
                                <option value="all">جميع السنوات</option>
                                @for ($year = date('Y'); $year >= date('Y') - 10; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-eye me-2"></i>عرض الحسابات</label>
                            <select name="account" id="account" class="form-control select2">
                                <option value="">عرض جميع الحسابات</option>
                                <option value="1">عرض الحسابات التي عليها معاملات</option>
                                <option value="2">إخفاء الحسابات الصفرية</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-layer-group me-2"></i>المستويات</label>
                            <select name="level" id="level" class="form-control select2">
                                <option value="">المستويات الافتراضية</option>
                                <option value="1">مستوى 1</option>
                                <option value="2">مستوى 2</option>
                                <option value="3">مستوى 3</option>
                                <option value="4">مستوى 4</option>
                                <option value="5">مستوى 5</option>
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
                            <label class="form-label-modern"><i class="fas fa-bullseye me-2"></i>مركز التكلفة</label>
                            <select name="cost_center" id="cost_center" class="form-control select2">
                                <option value="">جميع مراكز التكلفة</option>
                                @foreach ($costCenters as $costCenter)
                                    <option value="{{ $costCenter->id }}">{{ $costCenter->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-lg-9 col-md-12 align-self-end">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn-modern btn-primary-modern" id="filterBtn">
                                    <i class="fas fa-search"></i>
                                    عرض التقرير
                                </button>
                                <button type="button" class="btn-modern btn-outline-modern" id="resetBtn">
                                    <i class="fas fa-refresh"></i>
                                    إلغاء الفلتر
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
                <div class="stats-card revenue">
                    <div class="stats-icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="stats-value" id="totalRevenues">0.00</div>
                    <div class="stats-label">إجمالي الإيرادات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card expense">
                    <div class="stats-icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="stats-value" id="totalExpenses">0.00</div>
                    <div class="stats-label">إجمالي المصروفات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card net-income">
                    <div class="stats-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="stats-value" id="netIncome">0.00</div>
                    <div class="stats-label">صافي الدخل (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card profit-margin">
                    <div class="stats-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stats-value" id="profitMargin">0.00%</div>
                    <div class="stats-label">هامش الربح</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    مقارنة الإيرادات والمصروفات
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="incomeChart"></canvas>
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
                    تقرير قائمة الدخل
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <!-- جدول الإيرادات -->
                    <table class="table table-modern mb-0" id="revenuesTable">
                        <thead>
                            <tr class="table-revenue-header">
                                <th colspan="3" class="text-center">
                                    <h4 class="mb-0"><i class="fas fa-arrow-up me-2"></i>الإيرادات</h4>
                                </th>
                            </tr>
                            <tr class="table-revenue-header">
                                <th><i class="fas fa-user me-2"></i>اسم الحساب</th>
                                <th class="text-center"><i class="fas fa-barcode me-2"></i>الكود</th>
                                <th class="text-right"><i class="fas fa-money-bill me-2"></i>المبلغ (ريال)</th>
                            </tr>
                        </thead>
                        <tbody id="revenuesTableBody">
                            <!-- سيتم تحديث البيانات هنا عبر AJAX -->
                        </tbody>
                        <tfoot>
                            <tr class="table-success">
                                <th colspan="2">
                                    <span class="h5"><i class="fas fa-calculator me-2"></i>إجمالي الإيراد</span>
                                </th>
                                <td class="text-right text-success h5 font-weight-bold" id="revenueTotal">
                                    0.00 ر.س
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- جدول المصروفات -->
                    <table class="table table-modern mt-4 mb-0" id="expensesTable">
                        <thead>
                            <tr class="table-expense-header">
                                <th colspan="3" class="text-center">
                                    <h4 class="mb-0"><i class="fas fa-arrow-down me-2"></i>المصروفات</h4>
                                </th>
                            </tr>
                            <tr class="table-expense-header">
                                <th><i class="fas fa-user me-2"></i>اسم الحساب</th>
                                <th class="text-center"><i class="fas fa-barcode me-2"></i>الكود</th>
                                <th class="text-right"><i class="fas fa-money-bill me-2"></i>المبلغ (ريال)</th>
                            </tr>
                        </thead>
                        <tbody id="expensesTableBody">
                            <!-- سيتم تحديث البيانات هنا عبر AJAX -->
                        </tbody>
                        <tfoot>
                            <tr class="table-danger">
                                <th colspan="2">
                                    <span class="h5"><i class="fas fa-calculator me-2"></i>إجمالي المصروفات</span>
                                </th>
                                <td class="text-right text-danger h5 font-weight-bold" id="expenseTotal">
                                    0.00 ر.س
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- جدول صافي الدخل -->
                    <table class="table table-modern mt-4 mb-0" id="netIncomeTable">
                        <thead>
                            <tr class="table-net-income">
                                <th colspan="3" class="text-center">
                                    <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>صافي الدخل</h4>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-primary">
                                <th colspan="2">
                                    <span class="h4"><i class="fas fa-equals me-2"></i>صافي الدخل</span>
                                </th>
                                <td class="text-right h3 font-weight-bold" id="netIncomeAmount">
                                    0.00 ر.س
                                </td>
                            </tr>
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
        let incomeChart;

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
                placeholder: 'اختر...'
            });

            // تحميل البيانات الأولية
            loadIncomeStatement();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // التعامل مع تصفية التقرير
            $('#filterBtn').click(function() {
                $(this).addClass('loading');
                loadIncomeStatement();
            });

            // إعادة تعيين الفلاتر
            $('#resetBtn').click(function() {
                $('#reportForm')[0].reset();
                $('.select2').val(null).trigger('change');
                loadIncomeStatement();
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

            // دالة تحميل بيانات قائمة الدخل
            function loadIncomeStatement() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#reportForm').serialize();

                $.ajax({
                    url: '{{ route('GeneralAccountReports.incomeStatementAjax') }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        updateIncomeStatementDisplay(response);
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

            // دالة تحديث عرض قائمة الدخل
            function updateIncomeStatementDisplay(data) {
                // حساب البيانات
                const totalRevenues = data.revenues ? data.revenues.reduce((sum, item) => sum + parseFloat(item.balance || 0), 0) : 0;
                const totalExpenses = data.expenses ? data.expenses.reduce((sum, item) => sum + parseFloat(item.balance || 0), 0) : 0;
                const netIncome = totalRevenues - totalExpenses;
                const profitMargin = totalRevenues > 0 ? ((netIncome / totalRevenues) * 100) : 0;

                // تحديث الإجماليات مع تأثير العد التصاعدي
                animateValue('#totalRevenues', 0, totalRevenues, 1000);
                animateValue('#totalExpenses', 0, totalExpenses, 1000);
                animateValue('#netIncome', 0, netIncome, 1000);
                animatePercentage('#profitMargin', 0, profitMargin, 1000);

                // تحديث الرسم البياني
                updateChart(totalRevenues, totalExpenses, netIncome);

                // تحديث جداول البيانات
                updateRevenuesTable(data.revenues || [], totalRevenues);
                updateExpensesTable(data.expenses || [], totalExpenses);
                updateNetIncomeTable(netIncome);
            }

            // دالة تحديث الرسم البياني
            function updateChart(revenues, expenses, netIncome) {
                const ctx = document.getElementById('incomeChart').getContext('2d');

                if (incomeChart) {
                    incomeChart.destroy();
                }

                incomeChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['الإيرادات', 'المصروفات', 'صافي الدخل'],
                        datasets: [{
                            label: 'المبلغ (ريال)',
                            data: [revenues, expenses, netIncome],
                            backgroundColor: [
                                'rgba(25, 135, 84, 0.8)',
                                'rgba(220, 53, 69, 0.8)',
                                netIncome >= 0 ? 'rgba(111, 66, 193, 0.8)' : 'rgba(220, 53, 69, 0.8)'
                            ],
                            borderColor: [
                                'rgba(25, 135, 84, 1)',
                                'rgba(220, 53, 69, 1)',
                                netIncome >= 0 ? 'rgba(111, 66, 193, 1)' : 'rgba(220, 53, 69, 1)'
                            ],
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

            // دالة تحديث جدول الإيرادات
            function updateRevenuesTable(revenues, total) {
                let tableHtml = '';

                if (revenues.length > 0) {
                    revenues.forEach(revenue => {
                        tableHtml += `
                            <tr class="revenue-row">
                                <td>
                                    <i class="fas fa-circle me-2 text-success" style="font-size: 0.5rem;"></i>
                                    ${revenue.name}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">${revenue.code || '--'}</span>
                                </td>
                                <td class="text-right font-weight-bold positive-amount">
                                    ${formatNumber(revenue.balance || 0)} ر.س
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    tableHtml = `
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                <i class="fas fa-info-circle me-2"></i>
                                لا توجد إيرادات للعرض
                            </td>
                        </tr>
                    `;
                }

                $('#revenuesTableBody').html(tableHtml);
                $('#revenueTotal').html(formatNumber(total) + ' ر.س');
            }

            // دالة تحديث جدول المصروفات
            function updateExpensesTable(expenses, total) {
                let tableHtml = '';

                if (expenses.length > 0) {
                    expenses.forEach(expense => {
                        tableHtml += `
                            <tr class="expense-row">
                                <td>
                                    <i class="fas fa-circle me-2 text-danger" style="font-size: 0.5rem;"></i>
                                    ${expense.name}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger">${expense.code || '--'}</span>
                                </td>
                                <td class="text-right font-weight-bold negative-amount">
                                    ${formatNumber(expense.balance || 0)} ر.س
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    tableHtml = `
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                <i class="fas fa-info-circle me-2"></i>
                                لا توجد مصروفات للعرض
                            </td>
                        </tr>
                    `;
                }

                $('#expensesTableBody').html(tableHtml);
                $('#expenseTotal').html(formatNumber(total) + ' ر.س');
            }

            // دالة تحديث جدول صافي الدخل
            function updateNetIncomeTable(netIncome) {
                const isProfit = netIncome >= 0;
                const colorClass = isProfit ? 'text-primary positive-amount' : 'text-danger negative-amount';
                const icon = isProfit ? 'fa-arrow-up' : 'fa-arrow-down';

                $('#netIncomeAmount').html(`
                    <i class="fas ${icon} me-2"></i>
                    ${formatNumber(netIncome)} ر.س
                `).removeClass('text-primary text-danger positive-amount negative-amount').addClass(colorClass);
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
                const increment = end > start ? 1 : -1;
                const stepTime = Math.abs(Math.floor(duration / range));
                let current = start;

                const timer = setInterval(function() {
                    current += increment * (range / 100);
                    if ((increment === 1 && current >= end) || (increment === -1 && current <= end)) {
                        current = end;
                        clearInterval(timer);
                    }
                    obj.text(formatNumber(current));
                }, stepTime / 10);
            }

            // دالة الرسوم المتحركة للنسب المئوية
            function animatePercentage(element, start, end, duration) {
                const obj = $(element);
                const range = Math.abs(end - start);
                const increment = end > start ? 1 : -1;
                const stepTime = Math.abs(Math.floor(duration / range));
                let current = start;

                const timer = setInterval(function() {
                    current += increment * (range / 100);
                    if ((increment === 1 && current >= end) || (increment === -1 && current <= end)) {
                        current = end;
                        clearInterval(timer);
                    }
                    obj.text(formatNumber(current) + '%');
                }, stepTime / 10);
            }

            // دالة تصدير إكسل
            function exportToExcel() {
                showAlert('جاري تصدير الملف...', 'info');

                // إنشاء workbook جديد
                const wb = XLSX.utils.book_new();

                // إنشاء worksheet للإيرادات
                const revenuesData = [];
                revenuesData.push(['اسم الحساب', 'الكود', 'المبلغ (ريال)']);

                $('#revenuesTableBody tr').each(function() {
                    const row = [];
                    $(this).find('td').each(function(index) {
                        if (index === 0) {
                            row.push($(this).text().trim());
                        } else if (index === 1) {
                            row.push($(this).find('.badge').text().trim());
                        } else {
                            row.push($(this).text().replace(' ر.س', '').trim());
                        }
                    });
                    if (row.length === 3) revenuesData.push(row);
                });

                // إضافة إجمالي الإيرادات
                revenuesData.push(['', 'إجمالي الإيرادات', $('#revenueTotal').text().replace(' ر.س', '').trim()]);

                const revenuesWS = XLSX.utils.aoa_to_sheet(revenuesData);
                XLSX.utils.book_append_sheet(wb, revenuesWS, 'الإيرادات');

                // إنشاء worksheet للمصروفات
                const expensesData = [];
                expensesData.push(['اسم الحساب', 'الكود', 'المبلغ (ريال)']);

                $('#expensesTableBody tr').each(function() {
                    const row = [];
                    $(this).find('td').each(function(index) {
                        if (index === 0) {
                            row.push($(this).text().trim());
                        } else if (index === 1) {
                            row.push($(this).find('.badge').text().trim());
                        } else {
                            row.push($(this).text().replace(' ر.س', '').trim());
                        }
                    });
                    if (row.length === 3) expensesData.push(row);
                });

                // إضافة إجمالي المصروفات
                expensesData.push(['', 'إجمالي المصروفات', $('#expenseTotal').text().replace(' ر.س', '').trim()]);

                const expensesWS = XLSX.utils.aoa_to_sheet(expensesData);
                XLSX.utils.book_append_sheet(wb, expensesWS, 'المصروفات');

                // إنشاء worksheet للملخص
                const summaryData = [
                    ['البيان', 'المبلغ (ريال)'],
                    ['إجمالي الإيرادات', $('#totalRevenues').text()],
                    ['إجمالي المصروفات', $('#totalExpenses').text()],
                    ['صافي الدخل', $('#netIncome').text()],
                    ['هامش الربح', $('#profitMargin').text()]
                ];

                const summaryWS = XLSX.utils.aoa_to_sheet(summaryData);
                XLSX.utils.book_append_sheet(wb, summaryWS, 'الملخص');

                const today = new Date();
                const fileName = `قائمة_الدخل_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

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
                doc.text('قائمة الدخل', 105, 20, { align: 'center' });

                // إضافة التاريخ
                const today = new Date();
                doc.setFontSize(12);
                doc.text(`تاريخ التقرير: ${today.toLocaleDateString('ar-SA')}`, 105, 30, { align: 'center' });

                // إضافة الملخص
                let yPosition = 50;
                doc.setFontSize(14);
                doc.text('ملخص قائمة الدخل:', 20, yPosition);

                yPosition += 10;
                doc.setFontSize(11);
                doc.text(`إجمالي الإيرادات: ${$('#totalRevenues').text()} ريال`, 20, yPosition);

                yPosition += 8;
                doc.text(`إجمالي المصروفات: ${$('#totalExpenses').text()} ريال`, 20, yPosition);

                yPosition += 8;
                doc.text(`صافي الدخل: ${$('#netIncome').text()} ريال`, 20, yPosition);

                yPosition += 8;
                doc.text(`هامش الربح: ${$('#profitMargin').text()}`, 20, yPosition);

                // حفظ الملف
                const fileName = `قائمة_الدخل_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.pdf`;

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
        });
    </script>
@endsection