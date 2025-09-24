@extends('master')

@section('title')
    تقريرالموضفين
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
                    <h2 class="content-header-title float-left mb-0">تقارير الموظفين</h2>
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
                لوحة تقاريرالموظفين
            </h1>
            <p class="page-subtitle">تقارير شاملة ومفصلة لجميع عمليات    الموظفين</p>
        </div>
    </div>

    <div class="container">
        <div class="reports-container">

            <!-- تقارير الموظفين -->
            <div class="report-category">
                <div class="category-header sales">
                    <h3 class="category-title">
                        <i class="fas fa-users category-icon"></i>
                        تقارير الموظفين
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h4 class="report-title">تقرير حالات   اقامات الموظفين </h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn details">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- ملخص تقرير التحصيل -->
            <div class="report-category">
                <div class="category-header payments">
                    <h3 class="category-title">
                        <i class="fas fa-chart-bar category-icon"></i>
                        ملخص تقرير التحصيل
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h4 class="report-title">ملخص تقرير التحصيل - حسب الموظف</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn details">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <h4 class="report-title">ملخص تقرير التحصيل - حسب اليوم</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn details">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon brand">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                            <h4 class="report-title">ملخص تقرير التحصيل - حسب الأسبوع</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn details">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon payment">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h4 class="report-title">ملخص تقرير التحصيل - حسب الشهر</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn details">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon visit">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <h4 class="report-title">ملخص تقرير التحصيل - حسب السنة</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn details">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-users"></i>
                            </div>
                            <h4 class="report-title">ملخص تقرير التحصيل - حسب القسم</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn details">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-book"></i>
                            </div>
                            <h4 class="report-title">تقرير دفاتر التحصيل</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn details">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- تقرير ورديات التحصيل -->
            <div class="report-category">
                <div class="category-header items">
                    <h3 class="category-title">
                        <i class="fas fa-file-invoice-dollar category-icon"></i>
                        تقرير ورديات التحصيل
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon visit">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h4 class="report-title">تقرير ورديات التحصيل</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- تقرير التحصيل -->
            <div class="report-category">
                <div class="category-header visits">
                    <h3 class="category-title">
                        <i class="fas fa-money-check-alt category-icon"></i>
                        تقرير التحصيل
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h4 class="report-title">تقرير التحصيل تفصيلي (موظف واحد)</h4>
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
                                <i class="fas fa-users"></i>
                            </div>
                            <h4 class="report-title">تقرير التحصيل تفصيلي (عدة موظفين)</h4>
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
                                <i class="fas fa-users"></i>
                            </div>
                            <h4 class="report-title">تقرير رصيد إجازات الموظفين</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- تقارير المرتبات -->
            <div class="report-category">
                <div class="category-header salaries">
                    <h3 class="category-title">
                        <i class="fas fa-money-bill-wave category-icon"></i>
                        تقارير المرتبات
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h4 class="report-title">تقرير المرتبات حسب الموظف</h4>
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
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h4 class="report-title">تقرير المرتبات شهرين</h4>
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
                                <i class="fas fa-calendar"></i>
                            </div>
                            <h4 class="report-title">تقرير المرتبات سنوي</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon payment">
                                <i class="fas fa-users"></i>
                            </div>
                            <h4 class="report-title">تقرير المرتبات حسب القسم</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon visit">
                                <i class="fas fa-building"></i>
                            </div>
                            <h4 class="report-title">تقرير المرتبات حسب الفرع</h4>
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
                                <i class="fas fa-money-check-alt"></i>
                            </div>
                            <h4 class="report-title">تقرير سلف الموظفين</h4>
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
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <h4 class="report-title">تقرير عقود الموظفين</h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn view">
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
