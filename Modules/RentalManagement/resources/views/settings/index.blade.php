@extends('master')

@section('title')
    الأعدادات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الأعدادات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container py-5">
        <div class="row g-4">
        <div class="col-md-6">
    <a href="{{ route('rental_management.Settings.general.index') }}" class="text-decoration-none">
        <div class="card text-center p-5">
            <div class="mb-3"><i class="bi bi-gear-fill" style="font-size: 3rem; color: #0d6efd;"></i></div>
            <div class="text-dark">الإعدادات العامة</div>
        </div>
    </a>
</div>

            <div class="col-md-6">
                <a href="{{ route('rental_management.Settings.Add_Type.index') }}" class="text-decoration-none">
                <div class="card text-center p-5">
                    <div class="mb-3"><i class="bi bi-layers-fill" style="font-size: 3rem; color: #0d6efd;"></i></div>
                    <div>أنواع الوحدات</div>
                </div>
                </a>
            </div>
            <div class="col-md-6">
                <div class="card text-center p-5">
                    <a href="{{ route('rental_management.Settings.reservation-status.index') }}" class="text-decoration-none">
                    <div class="mb-3"><i class="bi bi-list-stars" style="font-size: 3rem; color: #0d6efd;"></i></div>
                    <div>حالات أوامر الحجوزات</div>
                </div>
                </a>
            </div>
            <div class="col-md-6">
                <div class="card text-center p-5">
                    <div class="mb-3"><i class="bi bi-table" style="font-size: 3rem; color: #0d6efd;"></i></div>
                    <div>الحقول الإضافية للحجوزات</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center p-5">
                    <div class="mb-3"><i class="bi bi-card-text" style="font-size: 3rem; color: #0d6efd;"></i></div>
                    <div>الحقول الإضافية للوحدات</div>
                </div>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
   



@endsection
