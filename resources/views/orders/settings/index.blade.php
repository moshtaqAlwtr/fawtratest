@extends('master')

@section('title')
    
@stop

@section('content')

    <div class="card">

    </div>
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> أنواع الطلبات</h2>
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



    <div class="card text-center shadow-sm p-4" style="width: 300px;">
        <a href="{{ route('orders.Settings.type') }}" class="text-decoration-none">
            <div class="card-body">
                <i class="bi bi-gear-fill text-primary" style="font-size: 50px;"></i>
                <h6 class="mt-3 text-primary">نوع الطلب</h6>
            </div>
        </a>
    </div>
    


    @endsection