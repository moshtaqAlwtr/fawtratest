@extends('master')

@section('title')
    تقرير العملاء
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
                    <h2 class="content-header-title float-left mb-0">تقرير العملاء</h2>
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
                <i class="fas fa-users"></i>
                لوحة تقارير العملاء
            </h1>
            <p class="page-subtitle">تقارير شاملة ومفصلة لجميع عمليات العملاء والمبيعات</p>
        </div>
    </div>

    <div class="container">
        <div class="reports-container">

            <!-- تقارير أعمار الديون -->
            <div class="report-category">
                <div class="category-header sales">
                    <h3 class="category-title">
                        <i class="fas fa-clock category-icon"></i>
                        تقارير أعمار الديون
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <h4 class="report-title">أعمار الديون (الفواتير)</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('ClientReport.debtReconstructionInv') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <h4 class="report-title">أعمار الديون (حساب الأستاذ)</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('ClientReport.debtAgingGeneralLedger') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- تقارير بيانات العملاء -->
            <div class="report-category">
                <div class="category-header payments">
                    <h3 class="category-title">
                        <i class="fas fa-address-book category-icon"></i>
                        تقارير بيانات العملاء
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-address-book"></i>
                            </div>
                            <h4 class="report-title">دليل العملاء</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('ClientReport.customerGuide') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon brand">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <h4 class="report-title">أرصدة العملاء</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('ClientReport.customerBalance') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-search"></i>
                            </div>
                            <h4 class="report-title">كشف حساب العملاء</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('ClientReport.customerAccountStatement') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                </ul>
            </div>


            <!-- تقارير مدفوعات ومواعيد العملاء -->
            <div class="report-category">
                <div class="category-header payments">
                    <h3 class="category-title">
                        <i class="fas fa-credit-card category-icon"></i>
                        تقارير المدفوعات والمواعيد
                    </h3>
                </div>
                <ul class="report-list">


                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon brand">
                                <i class="fas fa-users"></i>
                            </div>
                            <h4 class="report-title">مواعيد العملاء</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('ClientReport.customerAppointments') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h4 class="report-title">أقساط العملاء</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('ClientReport.customerInstallments') }}" class="report-btn view">
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