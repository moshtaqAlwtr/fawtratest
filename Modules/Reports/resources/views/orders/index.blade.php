@extends('master')

@section('title')
تقارير أوامر التوريد
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تقارير أوامر التوريد</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">الرئيسية</></li>
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
                        <i class="bi bi-graph-up"></i> تقارير أوامر التوريد
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-diagram-3-fill text-danger"></i> أمر التوريد
                            </span>
                            <div>
                                <a href="{{ route('reports.orders.supplyOrder') }}" class="btn btn-link">عرض</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-tree-fill text-info"></i> أوامر التوريد بالوسوم
                            </span>
                            <div>
                                <a href="{{ route('reports.orders.taggedSupplyOrders') }}" class="btn btn-link">عرض</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-calendar-day text-primary"></i> مواعيد أوامر التوريد
                            </span>
                            <div>
                                <a href="{{ route('reports.orders.supplyOrdersSchedule') }}" class="btn btn-link">عرض</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-calendar-week text-primary"></i> أرباح أوامر التوريد - الملخص
                            </span>
                            <div>
                                <a href="{{ route('reports.orders.supplyOrdersProfitSummary') }}" class="btn btn-link">عرض</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-calendar-month text-primary"></i> أرباح أوامر التوريد - التفاصيل
                            </span>
                            <div>
                                <a href="{{ route('reports.orders.supplyOrdersProfitDetails') }}" class="btn btn-link">عرض</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
