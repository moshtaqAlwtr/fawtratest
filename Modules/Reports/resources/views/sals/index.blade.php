@extends('master')

@section('title')
    تقرير المبيعات
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
                    <h2 class="content-header-title float-left mb-0">تقارير المبيعات</h2>
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
                <i class="fas fa-chart-line"></i>
                لوحة تقارير المبيعات
            </h1>
            <p class="page-subtitle">تقارير شاملة ومفصلة لجميع عمليات المبيعات والأرباح</p>
        </div>
    </div>

    <div class="container">
        <div class="reports-container">

            <!-- تقارير متابعة الفواتير المقسمة -->
            <div class="report-category">
                <div class="category-header sales">
                    <h3 class="category-title">
                        <i class="fas fa-chart-line category-icon"></i>
                        تقارير متابعة المبيعات
                    </h3>
                </div>
                <ul class="report-list">

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h4 class="report-title">المبيعات حسب الموظف</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('salesReports.byEmployee') }}" class="report-btn details">
                                <i class="fas fa-file-lines"></i> التفاصيل
                            </a>
                            <a href="{{ route('salesReports.byEmployee') }}" class="report-btn summary">
                                <i class="fas fa-clipboard"></i> الملخص
                            </a>
                        </div>
                    </li>
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h4 class="report-title">المبيعات حسب المنتج</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('salesReports.byProduct') }}" class="report-btn details">
                                <i class="fas fa-file-lines"></i> التفاصيل
                            </a>
                            <a href="{{ route('salesReports.byProduct') }}" class="report-btn summary">
                                <i class="fas fa-clipboard"></i> الملخص
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon brand">
                                <i class="fas fa-tag"></i>
                            </div>
                            <h4 class="report-title">مبيعات البنود </h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('salesReports.byItem', ['filter' => 'brand']) }}" class="report-btn details">
                                <i class="fas fa-file-lines"></i> التفاصيل
                            </a>
                            <a href="{{ route('salesReports.byItem', ['filter' => 'brand', 'summary' => true]) }}"
                                class="report-btn summary">
                                <i class="fas fa-clipboard"></i> الملخص
                            </a>
                        </div>
                    </li>
                </ul>
            </div>



            <!-- تقارير المدفوعات المقسمة -->
            <div class="report-category">
                <div class="category-header payments">
                    <h3 class="category-title">
                        <i class="fas fa-money-check-alt category-icon"></i>
                        تقارير المدفوعات المقسمة
                    </h3>
                </div>
                <ul class="report-list">

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h4 class="report-title">المدفوعات حسب الموظف</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('salesReports.employeePaymentsReceiptsReport') }}" class="report-btn details">
                                <i class="fas fa-file-lines"></i> التفاصيل
                            </a>
                            <a href="{{ route('salesReports.employeePaymentsReceiptsReport') }}" class="report-btn summary">
                                <i class="fas fa-clipboard"></i> الملخص
                            </a>
                        </div>
                    </li>
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon payment">
                                <i class="fas fa-cash-register"></i>
                            </div>
                            <h4 class="report-title">المدفوعات حسب طريقة الدفع</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('salesReports.paymentMethodReport') }}" class="report-btn details">
                                <i class="fas fa-file-lines"></i> التفاصيل
                            </a>
                            <a href="{{ route('salesReports.paymentMethodReport') }}" class="report-btn summary">
                                <i class="fas fa-clipboard"></i> الملخص
                            </a>
                        </div>
                    </li>
                </ul>
            </div>


            <!-- أرباح مبيعات الأصناف -->
            <div class="report-category">
                <div class="category-header items">
                    <h3 class="category-title">
                        <i class="fas fa-chart-pie category-icon"></i>
                        أرباح مبيعات الأصناف
                    </h3>
                </div>
                <ul class="report-list">

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h4 class="report-title">أرباح مبيعات </h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('salesReports.employeeProfits') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                </ul>
            </div>


            <!-- تقارير تتبع الزيارات -->
            <div class="report-category">
                <div class="category-header visits">
                    <h3 class="category-title">
                        <i class="fas fa-map-marker-alt category-icon"></i>
                        تقارير تتبع الزيارات
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon visit">
                                <i class="fas fa-route"></i>
                            </div>
                            <h4 class="report-title">تتبع الزيارات</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('visits.tracktaff') }}" class="report-btn view">
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
