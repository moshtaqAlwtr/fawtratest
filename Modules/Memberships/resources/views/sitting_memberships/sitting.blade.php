@extends('master')

@section('title')
    اعدادت العضويات
@stop

@section('content')
    <div class="content-header row fs-4">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0 fs-2"> اعدادت العضوية </h2>
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

    <div class="content-body fs-4">
        <div class="card mb-5">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>

                    <div>
                        <form action="{{ route('SittingMemberships.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                        <a href="" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i>الغاء
                        </a>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i>حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>
      
        @include('layouts.alerts.error')
        @include('layouts.alerts.success')
         
            <div class="card" style="max-width: 90%; margin: 0 auto;">
                <div class="card-header">
                    <h1 class="fs-2 fw-bold mb-0">
                        اعدادت العضوية
                    </h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('SittingMemberships.store') }}" method="POST">
                        @csrf
                        @php
                            $setting = \App\Models\MembershipsSetthing::first();
                        @endphp
                
                        <div class="form-body row mb-5 align-items-center">
                            <div class="form-group col-md-4 mb-3">
                                <label for="feedback2">ايام السماح ؟ <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="feedback2" name="days_allowed" 
                                    value="{{ $setting ? $setting->days_allowed : '' }}">
                            </div>
                        </div>
                
                        <div class="form-body row mb-5">
                            <div class="form-group col-md-6 mb-3">
                                <label for="feedback1"> السماح بتسجيل الحضور الي <span class="text-danger">*</span></label>
                                <select class="form-control" name="active_clients">
                                    <option value="all" {{ ($setting && $setting->active_clients == 'all') ? 'selected' : '' }}>كل العملاء</option>
                                    <option value="only_registered" {{ ($setting && $setting->active_clients == 'only_registered') ? 'selected' : '' }}>كل العملاء المشتركين</option>
                                    <option value="active_clients" {{ ($setting && $setting->active_clients == 'active_clients') ? 'selected' : '' }}>المشتركين النشيطين</option>
                                </select>
                            </div>
                        </div>
                
                      
                    </form>
                </div>
                

                    <div class="form-body row mb-5">
                        <div class="form-group col-md-6 mb-3">
                            <input type="checkbox" class="form-check-input" id="feedback1" style="transform: scale(1.7); margin-right: 10px">
                            <label for="feedback1" class="me-2" style="margin-right: 40px"> انشاء فواتير لمسودة ل اشكتراكات العضوية <span
                                    class="text-danger">*</span></label>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
