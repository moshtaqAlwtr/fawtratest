@extends('master')

@section('title')
    تقرير المخزن
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
                    <h2 class="content-header-title float-left mb-0">تقرير المخزن</h2>
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
                <i class="fas fa-warehouse"></i>
                لوحة تقارير المخزون
            </h1>
            <p class="page-subtitle">تقارير شاملة ومفصلة لجميع عمليات المخزون والمنتجات</p>
        </div>
    </div>

    <div class="container">
        <div class="reports-container">

            <!-- تقارير المخزون الأساسية -->
            <div class="report-category">
                <div class="category-header sales">
                    <h3 class="category-title">
                        <i class="fas fa-boxes category-icon"></i>
                        تقارير المخزون الأساسية
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-clipboard"></i>
                            </div>
                            <h4 class="report-title">ورقة الجرد</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('StorHouseReport.inventorySheet') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-book"></i>
                            </div>
                            <h4 class="report-title">ملخص عمليات المخزون</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('StorHouseReport.summaryInventory') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon brand">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <h4 class="report-title">الحركة التفصيلية للمخزون</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('StorHouseReport.detailedMovementInventory') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon sales-rep">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h4 class="report-title">قيمة المخزون</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('StorHouseReport.valueInventory') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- تقارير الأرصدة والمراجعة -->
            <div class="report-category">
                <div class="category-header payments">
                    <h3 class="category-title">
                        <i class="fas fa-balance-scale category-icon"></i>
                        تقارير الأرصدة والمراجعة
                    </h3>
                </div>
                <ul class="report-list">
                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h4 class="report-title">ملخص رصيد المخازن</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('StorHouseReport.inventoryBlance') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon brand">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                            <h4 class="report-title">ميزان مراجعة المنتجات</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('StorHouseReport.trialBalance') }}" class="report-btn view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </div>
                    </li>

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h4 class="report-title">تفاصيل حركة المخزون لكل منتج</h4>
                        </div>
                        <div class="report-actions">
                            <a href="{{ route('StorHouseReport.Inventory_mov_det_product') }}" class="report-btn view">
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
