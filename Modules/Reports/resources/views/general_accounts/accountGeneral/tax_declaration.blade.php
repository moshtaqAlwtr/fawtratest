@extends('master')

@section('title')
    تقرير الاقرار الضريبي
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

        .stats-card.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%);
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

        /* تنسيق الجداول */
        .table-section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            font-weight: 600;
        }

        .table-sales-row {
            background-color: rgba(76, 175, 80, 0.05) !important;
        }

        .table-sales-row:hover {
            background-color: rgba(76, 175, 80, 0.1) !important;
        }

        .table-purchases-row {
            background-color: rgba(244, 67, 54, 0.05) !important;
        }

        .table-purchases-row:hover {
            background-color: rgba(244, 67, 54, 0.1) !important;
        }

        .table-total-row {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
            color: white !important;
            font-weight: 700;
            font-size: 1.1em;
        }

        /* تأثيرات الحركة */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        .fade-in.animate__animated {
            opacity: 1;
            transform: translateY(0);
        }

        .slide-in {
            animation: slideInFromRight 0.8s ease;
        }

        @keyframes slideInFromRight {
            from {
                transform: translateX(50px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* تحسينات responsive */
        @media (max-width: 768px) {
            .stats-card {
                padding: 1.5rem;
                margin-bottom: 1rem;
            }

            .stats-value {
                font-size: 1.5rem;
            }

            .table-responsive {
                font-size: 0.9rem;
            }
        }

        /* Loading overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* تحسين الأزرار */
        .btn-modern {
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            text-transform: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .btn-warning-modern {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .btn-info-modern {
            background: linear-gradient(135deg, #4481eb 0%, #04befe 100%);
            color: white;
        }

        .btn-outline-modern {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* تحسين البطاقات */
        .card-modern {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card-header-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1.5rem 2rem;
            font-weight: 600;
        }

        .card-body-modern {
            padding: 2rem;
        }

        /* تنسيق النموذج */
        .form-label-modern {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Page header */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 30px 30px;
        }

        .breadcrumb-custom {
            background: none;
            padding: 0;
        }

        .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.7);
        }

        .breadcrumb-custom .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .breadcrumb-custom .breadcrumb-item.active {
            color: white;
        }

        /* جدول البيانات */
        .table-modern {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .table-modern thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
            text-align: center;
        }

        .table-modern tbody td {
            padding: 1rem;
            border: none;
            border-bottom: 1px solid #f8f9fa;
            text-align: center;
            vertical-align: middle;
        }

        .table-modern tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        /* تحسين badges */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
        }

        /* إخفاء في الطباعة */
        @media print {
            .no-print {
                display: none !important;
            }

            .page-header {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
            }

            .stats-card {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
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
                        <i class="fas fa-file-invoice-dollar me-3"></i>
                        تقرير الاقرار الضريبي
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير الاقرار الضريبي</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-percentage"></i>
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
                            <label class="form-label-modern"><i class="fas fa-percentage me-2"></i>نوع الضريبة</label>
                            <select name="tax_type" id="tax_type" class="form-control select2">
                                <option value="">جميع أنواع الضرائب</option>
                                <option value="مضافة">ضريبة القيمة المضافة</option>
                                <option value="القيمة الصفرية">القيمة الصفرية</option>
                                <option value="معفاة">معفاة من الضريبة</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-money-check me-2"></i>نوعية الدخل</label>
                            <select name="income_type" id="income_type" class="form-control select2">
                                <option value="">جميع الأنواع</option>
                                <option value="مستحقة">تم إصدارها (مستحقة)</option>
                                <option value="مدفوع بالكامل">مدفوع بالكامل</option>
                                <option value="مدفوع جزئيا">مدفوع جزئياً (نقداً)</option>
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
                            <label class="form-label-modern"><i class="fas fa-calendar me-2"></i>نوع التاريخ</label>
                            <select name="date_type" id="date_type" class="form-control select2">
                                <option value="custom">مخصص</option>
                                <option value="today">اليوم</option>
                                <option value="yesterday">أمس</option>
                                <option value="this_week">هذا الأسبوع</option>
                                <option value="last_week">الأسبوع الماضي</option>
                                <option value="this_month">هذا الشهر</option>
                                <option value="last_month">الشهر الماضي</option>
                                <option value="this_quarter">هذا الربع</option>
                                <option value="last_quarter">الربع الماضي</option>
                                <option value="this_year">هذا العام</option>
                                <option value="last_year">العام الماضي</option>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="col-lg-3 col-md-6" id="custom_dates">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>من تاريخ</label>
                            <input type="date" name="from_date" id="from_date" class="form-control"
                                value="{{ now()->subDays(30)->format('Y-m-d') }}">
                        </div>

                        <div class="col-lg-3 col-md-6" id="custom_to_date">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>إلى تاريخ</label>
                            <input type="date" name="to_date" id="to_date" class="form-control"
                                value="{{ now()->format('Y-m-d') }}">
                        </div>

                        <!-- Currency -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-coins me-2"></i>العملة</label>
                            <select name="currency" id="currency" class="form-control select2">
                                <option value="الجميع">الجميع إلى (SAR)</option>
                                <option value="SAR" selected>SAR - ريال سعودي</option>
                                <option value="USD">USD - دولار أمريكي</option>
                                <option value="EUR">EUR - يورو</option>
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
                    <div class="stats-icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="stats-value" id="totalSales">0.00</div>
                    <div class="stats-label">إجمالي المبيعات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="stats-value" id="totalPurchases">0.00</div>
                    <div class="stats-label">إجمالي المشتريات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card primary">
                    <div class="stats-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stats-value" id="totalTaxOutput">0.00</div>
                    <div class="stats-label">ضريبة المخرجات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stats-value" id="totalTaxInput">0.00</div>
                    <div class="stats-label">ضريبة المدخلات (ريال)</div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="stats-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="stats-value" id="netTaxDue">0.00</div>
                    <div class="stats-label">صافي الضريبة المستحقة (ريال)</div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 mb-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <div class="stats-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stats-value" id="totalInvoices">0</div>
                    <div class="stats-label">إجمالي عدد الفواتير</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    التوزيع الضريبي للمبيعات والمشتريات
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-center mb-3">توزيع ضريبة المبيعات</h6>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="salesTaxChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-center mb-3">توزيع ضريبة المشتريات</h6>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="purchasesTaxChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="card-modern fade-in" id="reportContainer" style="position: relative; display: none;">
            <!-- Loading Overlay -->
            <div class="loading-overlay">
                <div class="spinner"></div>
            </div>

            <div class="card-header-modern">
                <h5 class="mb-0" id="reportTitle">
                    <i class="fas fa-table me-2"></i>
                    تفاصيل الاقرار الضريبي
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="row">
                    <!-- Sales Tax Table -->
                    <div class="col-md-6">
                        <h6 class="p-3 mb-0">
                            <i class="fas fa-arrow-up me-2 text-success"></i>
                            فواتير المبيعات
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th>نوع الضريبة</th>
                                        <th>المبيعات</th>
                                        <th>الضريبة</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody id="salesTaxTableBody">
                                    <!-- سيتم تحديث البيانات هنا عبر AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Purchases Tax Table -->
                    <div class="col-md-6">
                        <h6 class="p-3 mb-0">
                            <i class="fas fa-arrow-down me-2 text-danger"></i>
                            فواتير المشتريات
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th>نوع الضريبة</th>
                                        <th>المشتريات</th>
                                        <th>الضريبة</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody id="purchasesTaxTableBody">
                                    <!-- سيتم تحديث البيانات هنا عبر AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tax Declaration Summary -->
                <div class="p-4 mt-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                    <h5 class="text-center mb-4">
                        <i class="fas fa-file-contract me-2"></i>
                        ملخص الاقرار الضريبي
                    </h5>
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3 bg-white shadow-sm">
                                <h6 class="text-muted mb-1">ضريبة المخرجات</h6>
                                <h4 class="text-success mb-0" id="summaryTaxOutput">0.00</h4>
                                <small class="text-muted">ريال سعودي</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 bg-white shadow-sm">
                                <h6 class="text-muted mb-1">ضريبة المدخلات</h6>
                                <h4 class="text-warning mb-0" id="summaryTaxInput">0.00</h4>
                                <small class="text-muted">ريال سعودي</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 bg-white shadow-sm">
                                <h6 class="text-muted mb-1">صافي الضريبة</h6>
                                <h4 class="text-primary mb-0" id="summaryNetTax">0.00</h4>
                                <small class="text-muted">ريال سعودي</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 bg-white shadow-sm">
                                <h6 class="text-muted mb-1">الحالة</h6>
                                <h5 class="mb-0" id="summaryStatus">
                                    <span class="badge bg-info">مؤقت</span>
                                </h5>
                            </div>
                        </div>
                    </div>
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
        let salesTaxChart, purchasesTaxChart;

        $(document).ready(function() {
            // تهيئة Select2 مع إعدادات محسنة
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
                closeOnSelect: true
            });

            // تحميل البيانات الأولية
            loadReportData();

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // التعامل مع تغيير نوع التاريخ
            $('#date_type').change(function() {
                if ($(this).val() !== 'custom') {
                    setDateRange($(this).val());
                    loadReportData();
                }
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
                setDefaultDates();
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
            $('.select2, input[type="date"]').on('change', function() {
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
                    url: '{{ route('GeneralAccountReports.taxDeclarationAjax') }}',
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
                animateValue('#totalSales', 0, data.totals.total_sales, 1000);
                animateValue('#totalPurchases', 0, data.totals.total_purchases, 1000);
                animateValue('#totalTaxOutput', 0, data.totals.total_tax_output, 1000);
                animateValue('#totalTaxInput', 0, data.totals.total_tax_input, 1000);
                animateValue('#netTaxDue', 0, data.totals.net_tax_due, 1000);
                animateValue('#totalInvoices', 0, data.totals.total_invoices, 1000);

                // تحديث ملخص الاقرار
                $('#summaryTaxOutput').text(formatNumber(data.totals.total_tax_output));
                $('#summaryTaxInput').text(formatNumber(data.totals.total_tax_input));
                $('#summaryNetTax').text(formatNumber(data.totals.net_tax_due));

                // تحديث حالة صافي الضريبة
                const netTax = parseFloat(data.totals.net_tax_due);
                const statusElement = $('#summaryStatus');
                if (netTax > 0) {
                    statusElement.html('<span class="badge bg-danger">مستحقة للدفع</span>');
                } else if (netTax < 0) {
                    statusElement.html('<span class="badge bg-success">مبلغ مسترد</span>');
                } else {
                    statusElement.html('<span class="badge bg-info">متوازن</span>');
                }

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    تفاصيل الاقرار الضريبي من ${data.from_date} إلى ${data.to_date}
                `);

                // تحديث الرسوم البيانية
                updateCharts(data.sales_tax_data, data.purchases_tax_data);

                // تحديث جداول البيانات
                updateTaxTables(data.sales_tax_declaration, data.purchases_tax_declaration);
            }

            // دالة تحديث الرسوم البيانية
            function updateCharts(salesData, purchasesData) {
                // رسم بياني للمبيعات
                const salesCtx = document.getElementById('salesTaxChart').getContext('2d');
                if (salesTaxChart) {
                    salesTaxChart.destroy();
                }

                salesTaxChart = new Chart(salesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: salesData.labels,
                        datasets: [{
                            data: salesData.amounts,
                            backgroundColor: [
                                'rgba(76, 175, 80, 0.8)',
                                'rgba(33, 150, 243, 0.8)',
                                'rgba(255, 193, 7, 0.8)',
                                'rgba(156, 39, 176, 0.8)',
                                'rgba(255, 87, 34, 0.8)'
                            ],
                            borderColor: [
                                'rgba(76, 175, 80, 1)',
                                'rgba(33, 150, 243, 1)',
                                'rgba(255, 193, 7, 1)',
                                'rgba(156, 39, 176, 1)',
                                'rgba(255, 87, 34, 1)'
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
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.2)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = formatNumber(context.parsed);
                                        const percentage = ((context.parsed / salesData.total) * 100).toFixed(1);
                                        return `${label}: ${value} ريال (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // رسم بياني للمشتريات
                const purchasesCtx = document.getElementById('purchasesTaxChart').getContext('2d');
                if (purchasesTaxChart) {
                    purchasesTaxChart.destroy();
                }

                purchasesTaxChart = new Chart(purchasesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: purchasesData.labels,
                        datasets: [{
                            data: purchasesData.amounts,
                            backgroundColor: [
                                'rgba(244, 67, 54, 0.8)',
                                'rgba(255, 152, 0, 0.8)',
                                'rgba(96, 125, 139, 0.8)',
                                'rgba(121, 85, 72, 0.8)',
                                'rgba(158, 158, 158, 0.8)'
                            ],
                            borderColor: [
                                'rgba(244, 67, 54, 1)',
                                'rgba(255, 152, 0, 1)',
                                'rgba(96, 125, 139, 1)',
                                'rgba(121, 85, 72, 1)',
                                'rgba(158, 158, 158, 1)'
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
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.2)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = formatNumber(context.parsed);
                                        const percentage = ((context.parsed / purchasesData.total) * 100).toFixed(1);
                                        return `${label}: ${value} ريال (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // دالة تحديث جداول البيانات
            function updateTaxTables(salesDeclaration, purchasesDeclaration) {
                // تحديث جدول المبيعات
                let salesTableHtml = '';
                let salesTotalAmount = 0;
                let salesTotalTax = 0;

                salesDeclaration.forEach(item => {
                    salesTotalAmount += parseFloat(item.total_amount || 0);
                    salesTotalTax += parseFloat(item.tax_amount || 0);

                    salesTableHtml += `
                        <tr class="table-sales-row">
                            <td><strong>${item.tax_type || 'غير محدد'}</strong></td>
                            <td class="text-success fw-bold">${formatNumber(item.base_amount || 0)}</td>
                            <td class="text-primary fw-bold">${formatNumber(item.tax_amount || 0)}</td>
                            <td class="text-dark fw-bold">${formatNumber(item.total_amount || 0)}</td>
                        </tr>
                    `;
                });

                // صف الإجمالي للمبيعات
                salesTableHtml += `
                    <tr class="table-total-row">
                        <td><strong>الإجمالي</strong></td>
                        <td class="fw-bold">${formatNumber(salesTotalAmount - salesTotalTax)}</td>
                        <td class="fw-bold">${formatNumber(salesTotalTax)}</td>
                        <td class="fw-bold">${formatNumber(salesTotalAmount)}</td>
                    </tr>
                `;

                $('#salesTaxTableBody').html(salesTableHtml);

                // تحديث جدول المشتريات
                let purchasesTableHtml = '';
                let purchasesTotalAmount = 0;
                let purchasesTotalTax = 0;

                purchasesDeclaration.forEach(item => {
                    purchasesTotalAmount += parseFloat(item.total_amount || 0);
                    purchasesTotalTax += parseFloat(item.tax_amount || 0);

                    purchasesTableHtml += `
                        <tr class="table-purchases-row">
                            <td><strong>${item.tax_type || 'غير محدد'}</strong></td>
                            <td class="text-warning fw-bold">${formatNumber(item.base_amount || 0)}</td>
                            <td class="text-primary fw-bold">${formatNumber(item.tax_amount || 0)}</td>
                            <td class="text-dark fw-bold">${formatNumber(item.total_amount || 0)}</td>
                        </tr>
                    `;
                });

                // صف الإجمالي للمشتريات
                purchasesTableHtml += `
                    <tr class="table-total-row">
                        <td><strong>الإجمالي</strong></td>
                        <td class="fw-bold">${formatNumber(purchasesTotalAmount - purchasesTotalTax)}</td>
                        <td class="fw-bold">${formatNumber(purchasesTotalTax)}</td>
                        <td class="fw-bold">${formatNumber(purchasesTotalAmount)}</td>
                    </tr>
                `;

                $('#purchasesTaxTableBody').html(purchasesTableHtml);
            }

            // دالة تحديد نطاق التاريخ
            function setDateRange(type) {
                const today = new Date();
                let fromDate, toDate;

                switch(type) {
                    case 'today':
                        fromDate = toDate = today;
                        break;
                    case 'yesterday':
                        fromDate = toDate = new Date(today.setDate(today.getDate() - 1));
                        break;
                    case 'this_week':
                        const firstDayOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
                        fromDate = firstDayOfWeek;
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
                    case 'this_quarter':
                        const quarterStart = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3, 1);
                        fromDate = quarterStart;
                        toDate = new Date();
                        break;
                    case 'last_quarter':
                        const lastQuarterStart = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3 - 3, 1);
                        const lastQuarterEnd = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3, 0);
                        fromDate = lastQuarterStart;
                        toDate = lastQuarterEnd;
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

                $('#from_date').val(fromDate.toISOString().split('T')[0]);
                $('#to_date').val(toDate.toISOString().split('T')[0]);
            }

            // دالة تعيين التواريخ الافتراضية
            function setDefaultDates() {
                const today = new Date();
                const thirtyDaysAgo = new Date(today.setDate(today.getDate() - 30));

                $('#from_date').val(thirtyDaysAgo.toISOString().split('T')[0]);
                $('#to_date').val(new Date().toISOString().split('T')[0]);
            }

            // دالة تنسيق الأرقام
            function formatNumber(number) {
                return parseFloat(number || 0).toLocaleString('en-US', {
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

                // إنشاء workbook جديد
                const wb = XLSX.utils.book_new();

                // إنشاء ورقة الملخص
                const summaryData = [
                    ['تقرير الاقرار الضريبي'],
                    ['التاريخ:', `من ${$('#from_date').val()} إلى ${$('#to_date').val()}`],
                    [''],
                    ['البيان', 'المبلغ (ريال)'],
                    ['إجمالي المبيعات', $('#totalSales').text()],
                    ['إجمالي المشتريات', $('#totalPurchases').text()],
                    ['ضريبة المخرجات', $('#totalTaxOutput').text()],
                    ['ضريبة المدخلات', $('#totalTaxInput').text()],
                    ['صافي الضريبة المستحقة', $('#netTaxDue').text()],
                    [''],
                    ['إجمالي عدد الفواتير', $('#totalInvoices').text()]
                ];

                const summaryWs = XLSX.utils.aoa_to_sheet(summaryData);
                XLSX.utils.book_append_sheet(wb, summaryWs, 'ملخص الاقرار');

                // إنشاء ورقة تفاصيل المبيعات
                const salesTable = document.querySelector('#salesTaxTableBody').closest('table');
                if (salesTable) {
                    const salesWs = XLSX.utils.table_to_sheet(salesTable);
                    XLSX.utils.book_append_sheet(wb, salesWs, 'تفاصيل المبيعات');
                }

                // إنشاء ورقة تفاصيل المشتريات
                const purchasesTable = document.querySelector('#purchasesTaxTableBody').closest('table');
                if (purchasesTable) {
                    const purchasesWs = XLSX.utils.table_to_sheet(purchasesTable);
                    XLSX.utils.book_append_sheet(wb, purchasesWs, 'تفاصيل المشتريات');
                }

                // حفظ الملف
                const today = new Date();
                const fileName = `الاقرار_الضريبي_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

                XLSX.writeFile(wb, fileName);
                showAlert('تم تصدير الملف بنجاح!', 'success');
            }

            // دالة تصدير PDF
            function exportToPDF() {
                showAlert('جاري تصدير ملف PDF...', 'info');

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('p', 'mm', 'a4');

                // إضافة العنوان
                doc.setFontSize(18);
                doc.text('تقرير الاقرار الضريبي', 105, 20, { align: 'center' });

                // إضافة التاريخ
                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();
                doc.setFontSize(12);
                doc.text(`من ${fromDate} إلى ${toDate}`, 105, 30, { align: 'center' });

                // إضافة الملخص
                let yPosition = 50;
                doc.setFontSize(14);
                doc.text('ملخص الاقرار:', 20, yPosition);

                yPosition += 10;
                doc.setFontSize(10);
                doc.text(`إجمالي المبيعات: ${$('#totalSales').text()} ريال`, 20, yPosition);
                yPosition += 8;
                doc.text(`إجمالي المشتريات: ${$('#totalPurchases').text()} ريال`, 20, yPosition);
                yPosition += 8;
                doc.text(`ضريبة المخرجات: ${$('#totalTaxOutput').text()} ريال`, 20, yPosition);
                yPosition += 8;
                doc.text(`ضريبة المدخلات: ${$('#totalTaxInput').text()} ريال`, 20, yPosition);
                yPosition += 8;
                doc.text(`صافي الضريبة المستحقة: ${$('#netTaxDue').text()} ريال`, 20, yPosition);

                // حفظ الملف
                const today = new Date();
                const fileName = `الاقرار_الضريبي_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.pdf`;

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
