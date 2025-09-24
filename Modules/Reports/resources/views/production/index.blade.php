



@extends('master')

@section('title')
    تقرير  التصنيع
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
                    <h2 class="content-header-title float-left mb-0">تقارير التصنيع</h2>
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
                لوحة تقارير  التصنيع
            </h1>
            <p class="page-subtitle">تقارير شاملة ومفصلة لجميع عمليات التصنيع</p>
        </div>
    </div>

    <div class="container">
        <div class="reports-container">
            <div class="report-category">
                <div class="category-header sales">
                    <h3 class="category-title">
                        <i class="fas fa-chart-line category-icon"></i>
                        تقاريرالتصنيع
                    </h3>
                </div>
                <ul class="report-list">

                    <li class="report-item">
                        <div class="report-info">
                            <div class="report-icon employee">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h4 class="report-title">  تكاليف طلبات التصنيع  </h4>
                        </div>
                        <div class="report-actions">
                            <a href="" class="report-btn details">
                                <i class="fas fa-file-lines"></i> التفاصيل
                            </a>
                            <a href="" class="report-btn summary">
                                <i class="fas fa-clipboard"></i> الملخص
                            </a>
                        </div>
                    </li>


                </ul>
            </div>



        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
@endsection
