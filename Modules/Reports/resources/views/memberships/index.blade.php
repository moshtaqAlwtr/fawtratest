@extends('master')

@section('title')
العضويات
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">العضويات</h2>
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

<div class="container mt-4">
    <div class="row">
        <!-- كرت تقرير العضويات -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">
                        <i class="bi bi-people-fill"></i> العضويات
                    </h5>
                    <ul class="list-unstyled">
                        <!-- العضويات المنتهية -->
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-x-circle-fill text-danger"></i> العضويات المنتهية
                            </span>
                            <div>
                                <a href="{{ route('reports.Memberships.Expired') }}" class="btn btn-link">عرض</a>
                                <a href="#" class="btn btn-link text-secondary">تفاصيل</a>
                            </div>
                        </li>
                        <!-- تجديد العضويات -->
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-arrow-repeat text-success"></i> تجديد العضويات
                            </span>
                            <div>
                                <a href="{{ route('reports.Memberships.Renewals') }}" class="btn btn-link">عرض</a>
                                <a href="#" class="btn btn-link text-secondary">تفاصيل</a>
                            </div>
                        </li>
                        <!-- الاشتراكات الجديدة -->
                        <li class="mb-3 d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-plus-circle-fill text-primary"></i> الأشتراكات الجديدة 
                            </span>
                            <div>
                                <a href="{{ route('reports.Memberships.New_Subscriptions') }}" class="btn btn-link">عرض</a>
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
