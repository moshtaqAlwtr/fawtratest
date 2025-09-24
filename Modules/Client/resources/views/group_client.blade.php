@extends('master')

@section('title')
   اعدادات المجموعات
@stop

@section('css')
    <style>
        #map {
            height: 500px;
            width: 100%;
            margin-bottom: 20px;
        }
    </style>
@stop

@section('content')
   
 @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> اعدادات  المجموعات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">


        <!-- بطاقة الإجراءات -->
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body">
                <div class="row align-items-center gy-3">
                    <!-- القسم الأيمن -->
                    <div
                        class="col-md-6 d-flex flex-wrap align-items-center gap-2 justify-content-center justify-content-md-start">
                        <!-- زر إضافة عميل -->
                        <a href="{{ route('clients.group_client_create') }}"
                            class="btn btn-success btn-sm rounded-pill px-4 text-center">
                            <i class="fas fa-plus-circle me-1"></i>
                            إضافة مجموعة
                        </a>

                       
                </div>
            </div>
        </div>
     
        <!-- بطاقة البحث -->
        <div class="card">
          
            <div class="card-body">
                <form class="form" id="searchForm" method="GET" action="{{ route('clients.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4 col-12">
                            
                        </div>
                      
                    </div>

                    <div class="collapse" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                         
                          
                           
                     
                        </div>
                    </div>

                    
                </form>
            </div>
        </div>

        <!-- جدول العملاء -->
     
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                      <table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>المجموعة</th>
            <th>عدد الأحياء</th>
            <th style="width: 10%">الإجراءات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($Regions_groub as $Region_groub)
            <tr>
                <td>{{ $loop->iteration }}</td> <!-- ترقيم تلقائي -->
                <td>{{ $Region_groub->name ?? "" }}</td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>

                    </div>
                </div>
            </div>
     
      

@endsection
