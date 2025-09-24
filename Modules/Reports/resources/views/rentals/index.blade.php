@extends('master')

@section('title')
الإيجارات
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">الإيجارات</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">الرئيسية</a>
                        </li>
                        <li class="breadcrumb-item active">عرض
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="row">
        <!-- بطاقة تقرير الوحدات -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">
                        <i class="bi bi-building"></i> تقارير الوحدات
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-house-door-fill text-warning"></i> الوحدات المتاحة
                            </span>
                            <div>
                                <a href="{{ route('reports.Rentals.Available_Units') }}" class="btn btn-link">عرض</a>
                                <a href="#" class="btn btn-link text-secondary">تفاصيل</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-currency-exchange text-success"></i> تسعير الوحدات
                            </span>
                            <div>
                                <a href="{{ route('reports.Rentals.Unit_Pricing') }}" class="btn btn-link">عرض</a>
                                <a href="#" class="btn btn-link text-secondary">تفاصيل</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- بطاقة الإيرادات والمصروفات -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-4">
                        <i class="bi bi-graph-up"></i> إيرادات ومصروفات الوحدات المؤجرة
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-diagram-3-fill text-danger"></i> حسب نوع الوحدة
                            </span>
                            <div>
                                <a href="{{ route('reports.Rentals.Unit_Type') }}" class="btn btn-link">عرض</a>
                                <a href="#" class="btn btn-link text-secondary">تفاصيل</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-tree-fill text-info"></i> حسب اسم الوحدة الأكبر
                            </span>
                            <div>
                                <a href="{{ route('reports.Rentals.Main_Unit_Name') }}" class="btn btn-link">عرض</a>
                                <a href="#" class="btn btn-link text-secondary">تفاصيل</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-calendar-day text-primary"></i> الإيرادات والمصروفات اليومية
                            </span>
                            <div>
                                <a href="{{ route('reports.Rentals.Daily_for_Units') }}" class="btn btn-link">عرض</a>
                                <a href="#" class="btn btn-link text-secondary">تفاصيل</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-calendar-week text-primary"></i> الإيرادات والمصروفات الأسبوعية
                            </span>
                            <div>
                                <a href="{{ route('reports.Rentals.Weekly_for_Units') }}" class="btn btn-link">عرض</a>
                                <a href="#" class="btn btn-link text-secondary">تفاصيل</a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-calendar-month text-primary"></i> الإيرادات والمصروفات الشهرية
                            </span>
                            <div>
                                <a href="{{ route('reports.Rentals.Monthly_for_Units') }}" class="btn btn-link">عرض</a>
                                <a href="#" class="btn btn-link text-secondary">تفاصيل</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
