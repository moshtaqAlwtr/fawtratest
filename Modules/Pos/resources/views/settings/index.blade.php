@extends('master')

@section('title')
الإعدادات
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">الإعدادات</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">الرئيسية</li>
                        <li class="breadcrumb-item active">الإعدادات</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="row mt-4">
    <div class="col-md-6 mb-4">
        <a href="{{route('pos.settings.general')}}" class="text-decoration-none">
            <div class="card shadow text-center" style="padding: 100px;">
                <div class="card-body">
                    <i class="bi bi-gear display-3 text-primary mb-3"></i>
                    <h5 class="card-title">عام</h5>
                </div>
                </a>
            </div>
        </div>
     
        <div class="col-md-6 mb-4">
            <a href="{{route('pos.settings.shift.index')}}" class="text-decoration-none">
            <div class="card shadow text-center" style="padding: 100px;">
                <div class="card-body">
                    <i class="bi bi-clock display-3 text-primary mb-3"></i>
                    <h5 class="card-title">ورديات نقاط البيع</h5>
                </div>
                </a>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <a href="{{route('pos.settings.devices.index')}}" class="text-decoration-none">
            <div class="card shadow text-center" style="padding: 100px;">
                <div class="card-body">
                    <i class="bi bi-cpu display-3 text-primary mb-3"></i>
                    <h5 class="card-title">أجهزة نقاط البيع</h5>
                </div>
                </a>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow text-center" style="padding: 100px;">
                <div class="card-body">
                    <i class="bi bi-download display-3 text-primary mb-3"></i>
                    <h5 class="card-title">تطبيق سطح المكتب</h5>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
