@extends('master')

@section('title')
    أدارة الطلبات
@stop

@section('content')

    <div class="card">

    </div>
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أدارة الطلبات</h2>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <a href="{{ route('orders.management.index') }}" class="text-decoration-none">
        <div class="card shadow-sm p-3" style="width: 280px; border-radius: 10px; cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
                <i class="bi bi-person-workspace text-primary" style="font-size: 40px;"></i>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <h6 class="text-primary mb-0">طلب إجازة</h6>
                <div class="d-flex align-items-center">
                    <span class="text-muted">تحت المراجعة</span>
                    <span class="badge bg-danger rounded-circle ms-2" style="font-size: 14px;">1</span>
                </div>
            </div>
        </div>
    </a>
    
    
    
    
    
    
    @endsection