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
    @include('layouts.alerts.success')
    @include('layouts.alerts.error')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('sittingLoyalty.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-rtl flex-wrap">
                            <div></div>
                            <div>
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fa fa-plus me-2"></i>الغاء
                                </button>
                                <button type="submit" class="btn btn-outline-success">
                                    <i class="fa fa-plus me-2"></i>حفظ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="form-body row">
                            <div class="form-group col-md-6">
                                <label for="minimum_import_points" class=""> الحد الادنى من نقاط الاستيراد </label>
                                <input type="text" id="minimum_import_points" class="form-control" placeholder="" name="minimum_import_points" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label> </label>
                                <select class="form-control" id="client_credit_type_id" name="client_credit_type_id" required>
                                    <option value="">اختر Client Credit Type</option>
                                    @foreach ($balanceTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="form-label">معامل التحويل</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <span>1 نقطة <i class="far fa-equals mr-1"></i></span>
                                    </div>
                                    <input id="client_loyalty_conversion_factor" name="client_loyalty_conversion_factor" class="form-control" required>

                                    <div class="invalid-message filled backend-error">
                                        <span></span>
                                    </div>

                                    <div class="input-group-text">
                                        <span>SAR</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">1 نقطة الولاء = مقدار العملة الأساسية؟</small>
                                <div id="factor-errors" class="invalid-messages"></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="position-relative" style="margin-top: 2rem;">
                                    <div class="input-group form-group">
                                        <div class="input-group-prepend w-100">
                                            <div class="input-group-text w-100">
                                                <div class="custom-control custom-Checkbox d-flex justify-content-start align-items-center w-100">
                                                    <input id="allow_decimal" name="allow_decimal" class="custom-control-input" type="checkbox" value="1">
                                                    <label for="allow_decimal" class="custom-control-label">اتاحة الارقام العشرية <span class="text-danger">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
