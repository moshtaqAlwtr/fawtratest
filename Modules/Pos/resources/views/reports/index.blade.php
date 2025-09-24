@extends('master')

@section('title')
تقارير نقاط البيع
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تقارير نقاط البيع</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">الرئيسية</li>
                        <li class="breadcrumb-item active">عرض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-4">
                        <i class="bi bi-cart"></i> الحركات - مبيعات نقاط البيع
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-bar-chart text-danger"></i> أجمالي مبيعات التصنيفات
                            </span>
                            <div>
                                <a href="{{ route('pos_reports.Category') }}" class="btn btn-link">عرض</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-box-seam text-info"></i> أجمالي مبيعات المنتجات
                            </span>
                            <div>
                                <a href="{{ route('pos_reports.Product') }}" class="btn btn-link">عرض</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-clock-history text-primary"></i> مبيعات الورديات
                            </span>
                            <div>
                                <a href="{{ route('pos_reports.Shift') }}" class="btn btn-link">عرض</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-graph-up text-primary"></i> حركة الورديات تفصيلي
                            </span>
                            <div>
                                <a href="{{ route('pos_reports.Detailed') }}" class="btn btn-link">عرض</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-4">
                        <i class="bi bi-currency-exchange"></i> الأرباح - نقاط البيع
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-cash-coin text-primary"></i> ربحية الورديات
                            </span>
                            <div>
                                <a href="{{ route('pos_reports.Shift_Profitability') }}" class="btn btn-link">عرض</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-pie-chart text-primary"></i> ربحية التصنيفات
                            </span>
                            <div>
                                <a href="{{ route('pos_reports.Category_Profitability') }}" class="btn btn-link">عرض</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-bar-chart-line text-primary"></i> ربحية المنتجات
                            </span>
                            <div>
                                <a href="{{ route('pos_reports.Product_Profitabilit') }}" class="btn btn-link">عرض</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
