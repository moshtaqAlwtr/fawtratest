@extends('master')

@section('title')
    أوامر الحجز
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أوامر الحجز</h2>
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



<div class="card mt-4">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">أوامر الحجز</h5>
        <div class="dropdown">
            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                إضافة أمر حجز
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="#">إضافة أمر حجز يدوي</a></li>
                <li><a class="dropdown-item" href="{{ route('rental_management.orders.create') }}">إضافة أمر حجز تلقائي</a></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#">مفتوح</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">مغلق</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">الكل</a>
            </li>
        </ul>
    </div>
</div>
@endsection
