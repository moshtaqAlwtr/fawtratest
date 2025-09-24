@extends('master')

@section('title')
    اضافة نوع  الرصيد
@stop

@section('content')
    <div class="fs-5">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0 fs-4"> اضافة نوع الرصيد </h2>
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

        <div class="content-body">


            <form class="form" action="{{route('BalanceType.store')}}"  method="post" enctype="multipart/form-data">
                @csrf
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                            </div>

                            <div>
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
                <div class="card" style="max-width: 90%; margin: 0 auto;">
                    <div class="card-header">
                        <h4 class="mb-0">تفاصيل نوع الرصيد</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-body row mb-2">
                            <div class="form-group col-md-6 mb-2">
                                <label for="feedback2" class="mb-1">اسم الرصيد <span class="text-danger">*</span></label>
                                <input type="text" id="feedback2" name="name" class="form-control" placeholder="اسم الرصيد">
                            </div>
                            <div class="form-group col-md-6 mb-2">
                                <label for="feedback1" class="mb-1">الحالة <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" id="">
                                    <option value="">اختر الحالة </option>
                                    <option value="1">نشط</option>
                                    <option value="0">غير نشط</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-body row mb-2">
                            <div class="form-group col-md-6 mb-2">
                                <label for="feedback1" class="mb-1">الوحدة <span class="text-danger">*</span></label>
                                <input type="text" name="unit" class="form-control">
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="position-relative" style="margin-top: 2rem;">
                                    <div class="input-group form-group">
                                        <div class="input-group-prepend w-100">
                                            <div class="input-group-text w-100">
                                                <div class="custom-control custom-Checkbox d-flex justify-content-start align-items-center w-100">
                                                    <input id="duration_checkbox" name="allow_decimal" class="custom-control-input" type="checkbox" value="1">
                                                    <label for="duration_checkbox" class="custom-control-label">اتاحة الارقام العشرية <span class="text-danger">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-body row">
                            <div class="form-group col-md-6">
                                <label for="feedback1" class="mb-1">الوصف <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
