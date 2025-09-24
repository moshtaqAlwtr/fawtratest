@extends('master')

@section('title')
    تقرير ميزان مراجعة الأرصدة
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

        /* تنسيق صفوف الجدول */
        .account-row {
            background-color: rgba(13, 110, 253, 0.02) !important;
            transition: all 0.3s ease;
        }

        .account-row:hover {
            background-color: rgba(13, 110, 253, 0.08) !important;
        }

        .account-parent-row {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            font-weight: 600;
        }

        .account-child-row {
            background-color: rgba(102, 126, 234, 0.05) !important;
        }

        .account-total-row {
            background-color: rgba(102, 126, 234, 0.1) !important;
            font-weight: 600;
            border-top: 2px solid #667eea;
        }

        .table-grand-total {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
            color: white !important;
            font-weight: 700;
            font-size: 1.05em;
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

        /* أزرار التوسيع والطي */
        .btn-toggle-children {
            background: rgba(13, 110, 253, 0.1);
            border: 1px solid rgba(13, 110, 253, 0.3);
            border-radius: 8px;
            padding: 0.25rem 0.5rem;
            color: #0d6efd;
            transition: all 0.3s ease;
        }

        .btn-toggle-children:hover {
            background: rgba(13, 110, 253, 0.2);
            color: #0d6efd;
        }

        /* تحسين شجرة الحسابات */
        .account-tree {
            font-family: 'Cairo', sans-serif;
        }

        .account-level-0 {
            font-weight: 700;
            font-size: 1.1em;
        }

        .account-level-1 {
            font-weight: 600;
            font-size: 1.05em;
            padding-right: 30px !important;
        }

        .account-level-2 {
            font-weight: 500;
            padding-right: 50px !important;
        }

        .account-level-3 {
            font-weight: 400;
            padding-right: 70px !important;
        }

        /* تحسين الأرقام */
        .number-cell {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            text-align: left;
        }

        .debit-amount {
            color: #dc3545;
        }

        .credit-amount {
            color: #198754;
        }

        .zero-amount {
            color: #6c757d;
            opacity: 0.7;
        }

        /* Loading overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 0.5rem;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Modern form styles */
        .card-modern {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 2rem;
            border: none;
        }

        .card-body-modern {
            padding: 2rem;
        }

        .form-label-modern {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .btn-modern {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
        }

        .btn-warning-modern {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #212529;
        }

        .btn-info-modern {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }

        .btn-outline-modern {
            background: transparent;
            border: 2px solid #dee2e6;
            color: #6c757d;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Page header */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .breadcrumb-custom {
            background: none;
            padding: 0;
        }

        .breadcrumb-custom .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .breadcrumb-custom .breadcrumb-item.active {
            color: white;
        }

        /* Animations */
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
            animation: slideInRight 0.6s ease;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
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
                        تقرير ميزان مراجعة الأرصدة
                    </h1>
                    <nav class="breadcrumb-custom">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="fas fa-home me-2"></i>الرئيسية</a>
                            </li>
                            <li class="breadcrumb-item active">تقرير ميزان مراجعة الأرصدة</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-icon primary">
                        <i class="fas fa-calculator"></i>
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
                            <label class="form-label-modern"><i class="fas fa-calendar me-2"></i>الفترة الزمنية</label>
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
                                <option value="year_to_date" selected>من أول السنة حتى اليوم</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-sitemap me-2"></i>نوع الحسابات</label>
                            <select name="account_type" id="account_type" class="form-control select2">
                                <option value="">جميع الأنواع</option>
                                <option value="رئيسي">حساب رئيسي</option>
                                <option value="فرعي">حساب فرعي</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-list me-2"></i>حساب محدد</label>
                            <select name="account" id="account" class="form-control select2">
                                <option value="">جميع الحسابات</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-building me-2"></i>فرع الحسابات</label>
                            <select name="branch" id="branch" class="form-control select2">
                                <option value="">جميع الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-user-plus me-2"></i>أُضيفت بواسطة</label>
                            <select name="added_by" id="added_by" class="form-control select2">
                                <option value="">جميع المستخدمين</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-building-circle-check me-2"></i>فرع القيود</label>
                            <select name="journal_branch" id="journal_branch" class="form-control select2">
                                <option value="">جميع الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-eye me-2"></i>عرض الحسابات</label>
                            <select name="account_display" id="account_display" class="form-control select2">
                                <option value="">عرض جميع الحسابات</option>
                                <option value="1">عرض الحسابات التي عليها معاملات</option>
                                <option value="2">إخفاء الحسابات الصفرية</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-layer-group me-2"></i>المستويات</label>
                            <select name="level" id="level" class="form-control select2">
                                <option value="">جميع المستويات</option>
                                <option value="1">المستوى الأول</option>
                                <option value="2">المستوى الثاني</option>
                                <option value="3">المستوى الثالث</option>
                                <option value="4">المستوى الرابع</option>
                                <option value="5">المستوى الخامس</option>
                            </select>
                        </div>

                        <!-- Third Row -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label-modern"><i class="fas fa-bullseye me-2"></i>مركز التكلفة</label>
                            <select name="cost_center" id="cost_center" class="form-control select2">
                                <option value="">جميع مراكز التكلفة</option>
                                @foreach ($costCenters as $costCenter)
                                    <option value="{{ $costCenter->id }}">{{ $costCenter->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="col-lg-3 col-md-6" id="custom_dates">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>من تاريخ</label>
                            <input type="date" name="from_date" id="from_date" class="form-control"
                                value="{{ now()->startOfYear()->format('Y-m-d') }}">
                        </div>

                        <div class="col-lg-3 col-md-6" id="custom_to_date">
                            <label class="form-label-modern"><i class="fas fa-calendar-alt me-2"></i>إلى تاريخ</label>
                            <input type="date" name="to_date" id="to_date" class="form-control"
                                value="{{ now()->format('Y-m-d') }}">
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
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card primary">
                    <div class="stats-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="stats-value" id="totalDebit">0.00</div>
                    <div class="stats-label">إجمالي المدين (ريال)</div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-icon">
                        <i class="fas fa-minus-circle"></i>
                    </div>
                    <div class="stats-value" id="totalCredit">0.00</div>
                    <div class="stats-label">إجمالي الدائن (ريال)</div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="stats-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="stats-value" id="accountsCount">0</div>
                    <div class="stats-label">عدد الحسابات</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-modern fade-in mb-4" id="chartSection">
            <div class="card-header-modern">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الرسم البياني لأرصدة الحسابات
                </h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="balanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="card fade-in" id="reportContainer" style="position: relative;">
            <!-- Loading Overlay -->
            <div class="loading-overlay">
                <div class="spinner"></div>
            </div>

            <div class="card-header-modern">
                <h5 class="mb-0" id="reportTitle">
                    <i class="fas fa-table me-2"></i>
                    ميزان مراجعة الأرصدة
                </h5>
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0 account-tree">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user me-2"></i>اسم الحساب</th>
                                <th><i class="fas fa-barcode me-2"></i>الكود</th>
                                <th class="text-danger">مدين</th>
                                <th class="text-success">دائن</th>
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

    <style id="additionalTreeCSS">
        /* تحسينات البحث والتمييز */
        .search-highlight {
            background-color: rgba(255, 193, 7, 0.3) !important;
            border: 2px solid #ffc107 !important;
            animation: searchPulse 2s infinite;
        }

        @keyframes searchPulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
        }

        /* تحسينات صفوف الشجرة */
        .parent-expanded {
            background-color: rgba(13, 110, 253, 0.1) !important;
            border-left: 4px solid #0d6efd !important;
        }

        .fade-in-row {
            animation: fadeInSlideDown 0.3s ease-in-out;
        }

        @keyframes fadeInSlideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* تحسين أزرار التحكم */
        .tree-controls {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
        }

        .tree-controls .btn {
            transition: all 0.3s ease;
        }

        .tree-controls .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* تحسين خانة البحث */
        #searchAccountTree {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        #searchAccountTree:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        /* تحسين أزرار التوسيع */
        .btn-toggle-children {
            background: rgba(13, 110, 253, 0.1);
            border: 1px solid rgba(13, 110, 253, 0.3);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-toggle-children:hover {
            background: rgba(13, 110, 253, 0.2);
            transform: scale(1.1);
        }

        .btn-toggle-children i {
            font-size: 10px;
            transition: all 0.3s ease;
        }

        /* تحسين الانتقالات */
        .collapse {
            transition: all 0.4s ease-in-out;
        }

        .collapsing {
            transition: height 0.4s ease-in-out;
        }

        /* تحسين ألوان المبالغ */
        .debit-amount {
            color: #dc3545;
            font-weight: 600;
        }

        .credit-amount {
            color: #198754;
            font-weight: 600;
        }

        .zero-amount {
            color: #6c757d;
            opacity: 0.7;
            font-style: italic;
        }

        /* تحسين hover للصفوف */
        .account-row:hover {
            background-color: rgba(13, 110, 253, 0.08) !important;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        /* إخفاء أزرار التحكم في الطباعة */
        @media print {
            .tree-controls {
                display: none !important;
            }
        }
    </style>

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

            // Add animation classes on load
            setTimeout(() => {
                $('.fade-in').addClass('animate__animated animate__fadeInUp');
            }, 100);

            // التعامل مع تغيير نوع التاريخ
            $('#date_type').change(function() {
                handleDateTypeChange();
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
                $('#date_type').val('year_to_date').trigger('change');
                handleDateTypeChange();
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
                        case 'year_to_date':
                        default:
                            fromDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                            toDate = today.toISOString().split('T')[0];
                            break;
                    }

                    $('#from_date').val(fromDate);
                    $('#to_date').val(toDate);
                }
            }

            // دالة تحميل بيانات التقرير
            function loadReportData() {
                $('.loading-overlay').fadeIn();
                $('#filterBtn').prop('disabled', true);

                const formData = $('#reportForm').serialize();

                $.ajax({
                    url: '{{ route('GeneralAccountReports.accountBalanceReviewAjax') }}',
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
                animateValue('#totalDebit', 0, data.totals.total_debit, 1000);
                animateValue('#totalCredit', 0, data.totals.total_credit, 1000);
                animateValue('#accountsCount', 0, data.totals.accounts_count, 1000);

                // تحديث عنوان التقرير
                $('#reportTitle').html(`
                    <i class="fas fa-table me-2"></i>
                    ميزان مراجعة الأرصدة من ${data.from_date} إلى ${data.to_date}
                `);

                // تحديث الرسم البياني
                updateChart(data.chart_data);

                // تحديث جدول البيانات
                updateTableBody(data.account_tree, data.totals);
            }

            // دالة تحديث الرسم البياني
            function updateChart(chartData) {
                const ctx = document.getElementById('balanceChart').getContext('2d');

                if (balanceChart) {
                    balanceChart.destroy();
                }

                balanceChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'مدين',
                            data: chartData.debit_amounts,
                            backgroundColor: 'rgba(220, 53, 69, 0.8)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        }, {
                            label: 'دائن',
                            data: chartData.credit_amounts,
                            backgroundColor: 'rgba(25, 135, 84, 0.8)',
                            borderColor: 'rgba(25, 135, 84, 1)',
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

            // دالة عرض صف الحساب
            function renderAccountRow(account, level, parentId = '') {
                let html = '';
                const paddingRight = level * 20;
                const levelClass = `account-level-${level}`;
                const uniqueId = parentId ? `${parentId}-${account.id}` : account.id;

                // تحديد لون المبلغ
                const getAmountClass = (amount) => {
                    if (amount == 0) return 'zero-amount';
                    return amount > 0 ? 'debit-amount' : 'credit-amount';
                };

                html += `
                    <tr class="account-row ${levelClass}" data-level="${level}" data-account-id="${account.id}">
                        <td style="padding-right: ${paddingRight}px">
                            ${account.children && account.children.length > 0 ?
                                `<button class="btn btn-xs btn-toggle-children me-2"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#children-${uniqueId}"
                                        title="توسيع/طي العناصر الفرعية">
                                    <i class="fa fa-plus"></i>
                                </button>` : ''
                            }
                            <span class="account-name">${account.name}</span>
                        </td>
                        <td class="fw-bold account-code">${account.code}</td>
                        <td class="number-cell ${getAmountClass(account.total_debit)}">${formatNumber(account.total_debit)}</td>
                        <td class="number-cell ${getAmountClass(account.total_credit)}">${formatNumber(account.total_credit)}</td>
                    </tr>
                `;

                // إضافة الحسابات الفرعية
                if (account.children && account.children.length > 0) {
                    html += `
                        <tr class="child-container" data-parent-id="${account.id}">
                            <td colspan="4" class="p-0">
                                <div id="children-${uniqueId}" class="collapse">
                                    <table class="table table-borderless m-0">
                                        <tbody>
                    `;

                    account.children.forEach(child => {
                        html += renderAccountRow(child, level + 1, uniqueId);
                    });

                    html += `
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    `;
                }

                return html;
            }

            // دالة تحديث محتوى الجدول
            function updateTableBody(accountTree, totals) {
                let tableHtml = '';

                // عرض شجرة الحسابات
                accountTree.forEach(account => {
                    tableHtml += renderAccountRow(account, 0);
                });

                // صف الإجمالي العام
                tableHtml += `
                    <tr class="table-grand-total">
                        <td colspan="2">
                            <i class="fas fa-calculator me-2"></i>
                            <strong>المجموع الكلي</strong>
                        </td>
                        <td class="number-cell fw-bold">${formatNumber(totals.total_debit)}</td>
                        <td class="number-cell fw-bold">${formatNumber(totals.total_credit)}</td>
                    </tr>
                `;

                $('#reportTableBody').html(tableHtml);

                // إضافة أزرار التحكم إذا لم تكن موجودة
                if (!$('.tree-controls').length) {
                    addTreeControlButtons();
                }

                // إضافة وظائف التوسيع والطي
                addToggleFunctionality();
            }

            // إضافة أزرار التحكم في الشجرة
            function addTreeControlButtons() {
                const controlButtonsHtml = `
                    <div class="tree-controls mb-3" style="display: none;">
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="expandAllBtn">
                                <i class="fas fa-expand-arrows-alt me-1"></i>
                                فتح الكل
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAllBtn">
                                <i class="fas fa-compress-arrows-alt me-1"></i>
                                إغلاق الكل
                            </button>
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control form-control-sm" id="searchAccountTree"
                                       placeholder="البحث في الحسابات...">
                                <button class="btn btn-outline-secondary btn-sm" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <small class="text-muted ms-2">
                                <i class="fas fa-info-circle me-1"></i>
                                يمكنك البحث باسم الحساب أو الكود
                            </small>
                        </div>
                    </div>
                `;

                // إضافة الأزرار قبل الجدول
                $('#reportContainer .card-body-modern').prepend(controlButtonsHtml);

                // ربط الأحداث
                $('#expandAllBtn').click(() => toggleAllSections('expand'));
                $('#collapseAllBtn').click(() => toggleAllSections('collapse'));

                // البحث في الوقت الفعلي
                let searchTimeout;
                $('#searchAccountTree').on('input', function() {
                    clearTimeout(searchTimeout);
                    const searchTerm = $(this).val();

                    searchTimeout = setTimeout(() => {
                        searchInAccountTree(searchTerm);
                    }, 300);
                });

                // مسح البحث
                $('#clearSearch').click(function() {
                    $('#searchAccountTree').val('');
                    searchInAccountTree('');
                });
            }

            // دالة إضافة وظائف التوسيع والطي
            function addToggleFunctionality() {
                $('.btn-toggle-children').off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const $button = $(this);
                    const $icon = $button.find('i');
                    const targetId = $button.data('bs-target');
                    const $targetDiv = $(targetId);
                    const $currentRow = $button.closest('tr');

                    if (!$targetDiv.hasClass('show')) {
                        $icon.removeClass('fa-plus').addClass('fa-spinner fa-spin');

                        setTimeout(function() {
                            $icon.removeClass('fa-spinner fa-spin').addClass('fa-minus');
                            $targetDiv.collapse('show');
                            $currentRow.addClass('parent-expanded');
                        }, 300);
                    } else {
                        $icon.removeClass('fa-minus').addClass('fa-plus');
                        $targetDiv.collapse('hide');
                        $currentRow.removeClass('parent-expanded');
                    }
                });

                $('.tree-controls').fadeIn();
            }

            // دالة فتح/إغلاق جميع الأقسام
            function toggleAllSections(action) {
                const $allButtons = $('.btn-toggle-children');

                if (action === 'expand') {
                    $allButtons.each(function(index) {
                        const $button = $(this);
                        const $icon = $button.find('i');
                        const targetId = $button.data('bs-target');
                        const $targetDiv = $(targetId);

                        if (!$targetDiv.hasClass('show')) {
                            setTimeout(() => {
                                $icon.removeClass('fa-plus').addClass('fa-minus');
                                $targetDiv.collapse('show');
                                $button.closest('tr').addClass('parent-expanded');
                            }, index * 100);
                        }
                    });

                    showAlert('تم فتح جميع الأقسام', 'success');
                } else if (action === 'collapse') {
                    $allButtons.each(function(index) {
                        const $button = $(this);
                        const $icon = $button.find('i');
                        const targetId = $button.data('bs-target');
                        const $targetDiv = $(targetId);

                        if ($targetDiv.hasClass('show')) {
                            setTimeout(() => {
                                $icon.removeClass('fa-minus').addClass('fa-plus');
                                $targetDiv.collapse('hide');
                                $button.closest('tr').removeClass('parent-expanded');
                            }, index * 50);
                        }
                    });

                    showAlert('تم إغلاق جميع الأقسام', 'info');
                }
            }

            // دالة البحث في شجرة الحسابات
            function searchInAccountTree(searchTerm) {
                const $allRows = $('#reportTableBody tr.account-row');

                if (!searchTerm.trim()) {
                    $allRows.show().removeClass('search-highlight');
                    $('.child-container').show();
                    return;
                }

                $allRows.hide().removeClass('search-highlight');
                $('.child-container').hide();

                let foundResults = 0;

                $allRows.each(function() {
                    const $row = $(this);
                    const accountName = $row.find('.account-name').text().toLowerCase();
                    const accountCode = $row.find('.account-code').text().toLowerCase();
                    const searchTermLower = searchTerm.toLowerCase();

                    if (accountName.includes(searchTermLower) || accountCode.includes(searchTermLower)) {
                        $row.show().addClass('search-highlight');
                        foundResults++;

                        // إظهار الصفوف الأب والحاويات
                        showParentRowsAndContainers($row);
                    }
                });

                if (foundResults === 0) {
                    showAlert('لم يتم العثور على نتائج للبحث المطلوب', 'warning');
                } else {
                    showAlert(`تم العثور على ${foundResults} نتيجة`, 'success');
                }
            }

            // دالة إظهار الصفوف الأب والحاويات للعنصر المطابق للبحث
            function showParentRowsAndContainers($row) {
                $row.closest('.child-container').show();

                let $parentContainer = $row.closest('.child-container');
                while ($parentContainer.length > 0) {
                    $parentContainer.show();

                    const parentId = $parentContainer.data('parent-id');
                    if (parentId) {
                        const $parentRow = $(`tr[data-account-id="${parentId}"]`);
                        $parentRow.show();

                        const $toggleButton = $parentRow.find('.btn-toggle-children');
                        if ($toggleButton.length) {
                            const targetId = $toggleButton.data('bs-target');
                            const $targetDiv = $(targetId);

                            if (!$targetDiv.hasClass('show')) {
                                $toggleButton.find('i').removeClass('fa-plus').addClass('fa-minus');
                                $targetDiv.collapse('show');
                                $parentRow.addClass('parent-expanded');
                            }
                        }

                        $parentContainer = $parentRow.closest('.child-container');
                    } else {
                        break;
                    }
                }
            }

            // دالة تنسيق الأرقام
            function formatNumber(number) {
                const num = parseFloat(number) || 0;
                return num.toLocaleString('en-US', {
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
                const fileName = `ميزان_مراجعة_الأرصدة_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;

                XLSX.writeFile(wb, fileName);
                showAlert('تم تصدير الملف بنجاح!', 'success');
            }

            // دالة تصدير PDF
            function exportToPDF() {
                showAlert('جاري تصدير ملف PDF...', 'info');

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('l', 'mm', 'a4');

                doc.setFontSize(16);
                doc.text('ميزان مراجعة الأرصدة', 148, 20, { align: 'center' });

                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();
                doc.setFontSize(12);
                doc.text(`من ${fromDate} إلى ${toDate}`, 148, 30, { align: 'center' });

                const today = new Date();
                const fileName = `ميزان_مراجعة_الأرصدة_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.pdf`;

                doc.save(fileName);
                showAlert('تم تصدير ملف PDF بنجاح!', 'success');
            }

            // دالة عرض التنبيهات
            function showAlert(message, type) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show position-fixed"
                         style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : type === 'warning' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
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

            // تحسين UX - تلميحات الأدوات
            $('[data-bs-toggle="tooltip"]').tooltip();

            // تهيئة التواريخ عند التحميل
            handleDateTypeChange();
        });
    </script>
@endsection
