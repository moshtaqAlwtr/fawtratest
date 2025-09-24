@extends('master')

@section('title')
    تقرير الحسابات العامة
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/report.css') }}">
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تقرير الحسابات العامة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- رأس الصفحة -->
    <div class="page-header">
        <div class="container">
            <h1 class="page-title">
                <i class="fas fa-chart-bar"></i>
                لوحة تقارير الحسابات العامة
            </h1>
            <p class="page-subtitle">تقارير شاملة ومفصلة لجميع العمليات المحاسبية والمالية</p>
        </div>
    </div>

    <div class="container">
        <div class="reports-container">

            <!-- تقارير الحسابات العامة -->
            <div class="report-category">
                <div class="category-header sales">
                    <h3 class="category-title">
                        <i class="fas fa-file-alt category-icon"></i>
                        تقارير الحسابات العامة
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <h4 class="report-title">تقرير الضرائب</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('GeneralAccountReports.declaration') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-file-signature"></i>
                            </div>
                            <h4 class="report-title">اقرار ضرائب</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('GeneralAccountReports.taxDeclaration') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon brand">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4 class="report-title">قائمة الدخل</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('GeneralAccountReports.incomeStatement') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                            <h4 class="report-title">الميزانية العمومية</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('GeneralAccountReports.BalanceSheet') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-coins"></i>
                            </div>
                            <h4 class="report-title">الربح و الخسارة</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon brand">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <h4 class="report-title">الحركات المالية</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <h4 class="report-title">تقرير التدفقات النقدية</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-building"></i>
                            </div>
                            <h4 class="report-title">الاصول</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- تقارير القيود اليومية -->
            <div class="report-category">
                <div class="category-header payments">
                    <h3 class="category-title">
                        <i class="fas fa-journal-whills category-icon"></i>
                        تقارير القيود اليومية
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-address-book"></i>
                            </div>
                            <h4 class="report-title">تقرير ميزان المراجعه مجاميع الارصدة</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('GeneralAccountReports.trialBalance', ['report_type' => 'balances_summary']) }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon brand">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                            <h4 class="report-title">تقرير حساب المراجعة</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('GeneralAccountReports.trialBalance', ['report_type' => 'account_review']) }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h4 class="report-title">تقرير حساب مراجعة الارصدة</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('GeneralAccountReports.accountBalanceReview') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h4 class="report-title">حساب الاستاذ العام</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('GeneralAccountReports.generalLedger') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon brand">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <h4 class="report-title">مراكز التكلفة</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{route('GeneralAccountReports.CostCentersReport')}}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <h4 class="report-title">تقرير القيود</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{route('GeneralAccountReports.JournalEntriesByEmployee')}}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <h4 class="report-title">دليل الحسابات العامة</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{route('GeneralAccountReports.ChartOfAccounts')}}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- تقارير المصاريف المقسمة -->


            <!-- تقارير المصروفات بالمدة الزمنية -->

            <!-- تقرير سندات القبض المقسمة -->
            <div class="report-category">
                <div class="category-header items">
                    <h3 class="category-title">
                        <i class="fas fa-receipt category-icon"></i>
                        تقرير  السندات
                    </h3>
                </div>
                <ul class="report-list">



                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4 class="report-title"> تقرير سندات   القبض </h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('GeneralAccountReports.ReceiptByEmployee') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4 class="report-title"> تقرير  سندات الصرف</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('GeneralAccountReports.splitExpensesByEmployee') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
@endsection
