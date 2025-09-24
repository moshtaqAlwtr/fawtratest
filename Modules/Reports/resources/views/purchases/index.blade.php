@extends('master')

@section('title')
    تقارير المشتريات
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
                    <h2 class="content-header-title float-left mb-0">تقارير المشتريات</h2>
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
                <i class="fas fa-shopping-cart"></i>
                لوحة تقارير المشتريات
            </h1>
            <p class="page-subtitle">تقارير شاملة ومفصلة لجميع عمليات المشتريات والموردين</p>
        </div>
    </div>

    <div class="container">
        <div class="reports-container">

            <!-- تقارير متابعة المشتريات المقسمة -->
            <div class="report-category">
                <div class="category-header sales">
                    <h3 class="category-title">
                        <i class="fas fa-chart-line category-icon"></i>
                        تقارير متابعة المشتريات المقسمة
                    </h3>
                </div>
                <ul class="report-list">

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h4 class="report-title">  تقارير فواتير المشتريات </h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('ReportsPurchases.purchaseByEmployee') }}" class="report-btn details">
                                <i class="fas fa-file-lines"></i> التفاصيل
                            </a>
                            <a href="{{ route('ReportsPurchases.purchaseByEmployee') }}" class="report-btn summary">
                                <i class="fas fa-clipboard"></i> الملخص
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- تقارير الموردين -->
            <div class="report-category">
                <div class="category-header payments">
                    <h3 class="category-title">
                        <i class="fas fa-users category-icon"></i>
                        تقارير الموردين
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-address-book"></i>
                            </div>
                            <h4 class="report-title">دليل الموردين</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('ReportsPurchases.SuppliersDirectory') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon brand">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h4 class="report-title">أعمار المدين حساب الاستاذ العام</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('ReportsPurchases.supplierDebtAging') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                </ul>
            </div>

            <!-- تقارير مشتريات المنتجات -->
            <div class="report-category">
                <div class="category-header items">
                    <h3 class="category-title">
                        <i class="fas fa-box category-icon"></i>
                        تقارير مشتريات المنتجات
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-box"></i>
                            </div>
                            <h4 class="report-title">مشتريات المنتجات حسب المنتج</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('ReportsPurchases.byProduct') }}" class="report-btn details">
                                <i class="fas fa-file-lines"></i> التفاصيل
                            </a>
                            <a href="{{ route('ReportsPurchases.byProduct') }}" class="report-btn summary">
                                <i class="fas fa-clipboard"></i> الملخص
                            </a>
                        </div>
                    </li>

                </ul>
            </div>

            <!-- تقارير مدفوعات المشتريات -->
            <div class="report-category">
                <div class="category-header payments">
                    <h3 class="category-title">
                        <i class="fas fa-money-bill-wave category-icon"></i>
                        تقارير مدفوعات المشتريات
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-money-bill"></i>
                            </div>
                            <h4 class="report-title">مدفوعات مشتريات الموردين</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('ReportsPurchases.employeeSupplierPayments') }}" class="report-btn view">
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
