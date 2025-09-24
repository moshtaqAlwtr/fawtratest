@extends('master')

@section('title')
أعدادات عامة
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">أعدادات عامة </h2>
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

@include('layouts.alerts.success')
@include('layouts.alerts.error')

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
            </div>

            <div>
                <a href="" class="btn btn-outline-danger">
                    <i class="fa fa-ban"></i>الغاء
                </a>
                <button type="submit" form="general_form" class="btn btn-outline-primary">
                    <i class="fa fa-save"></i>حفظ
                </button>
            </div>

        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card">
        <div class="card-header" style="background-color: #f8f8f8">
            <strong class="mb-1">الإعدادات العامة</strong>
        </div>
            <form id="general_form" action="{{ route('Manufacturing.general_settings.update') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="custom-control custom-switch custom-switch-success custom-control-inline">
                        <input type="checkbox" class="custom-control-input" id="customSwitch1" name="quantity_exceeded" value="1" {{ optional($general_settings)->quantity_exceeded == 1 ? 'checked' : '' }}>
                        <label class="custom-control-label" for="customSwitch1"></label>
                        <span class="switch-label">تجاوز الكمية المطلوبة في أمر التصنيع</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
