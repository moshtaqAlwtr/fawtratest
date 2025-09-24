@extends('master')

@section('title')
أعدادات التصنيع
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">أعدادات التصنيع</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                        <li class="breadcrumb-item active">عرض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- تضمين مكتبة Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

@include('layouts.alerts.error')
@include('layouts.alerts.success')

<div class="container my-5">
    <div class="row g-4">
        <!-- البوكس الأول -->
        <div class="col-md-4">
            <a href="{{ route('manufacturing.settings.general') }}" class="text-decoration-none">
            <div class="card text-center p-4 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-gear text-primary" style="font-size: 5rem;"></i>
                    <h5 class="card-title mt-3">الأعدادات العامة</h5>
                </div>
            </div>
            </a>
        </div>

        <!-- البوكس الثاني -->
        <div class="col-md-4">
            <a href="{{ route('manufacturing.paths.index') }}" class="text-decoration-none">
            <div class="card text-center p-4 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-diagram-3 text-success" style="font-size: 5rem;"></i>
                    <h5 class="card-title mt-3">مسارات الإنتاج</h5>
                </div>
            </div>
            </a>
        </div>

        <!-- البوكس الثالث -->
        <div class="col-md-4">
            <a href="{{ route('Manufacturing.settings.Manual') }}" class="text-decoration-none">
            <div class="card text-center p-4 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-hammer text-warning" style="font-size: 5rem;"></i>
                    <h5 class="card-title mt-3" style="font-size: 15px">الحالات اليدوية لطلبات التصنيع</h5>
                </div>
            </div>
        </div>
        </a>
    </div>
</div>

@endsection
