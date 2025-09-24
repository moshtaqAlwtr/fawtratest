@extends('master')

@section('title')
    اضافة خيار الشحن
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اضافة خيار شحن </h2>
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
    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <div class="content-body">
        <div class="container-fluid">
            <form class="form-horizontal" action="{{ route('shippingOptions.store') }}" method="POST">
                @csrf

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
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa fa-save"></i>حفظ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title"> تفاصيل العرض </h4>
                        </div>

                        <div class="card-body">
                            <div class="form-body row">

                                <!-- اسم خيار الشحن -->
                                <div class="form-group col-md-6">
                                    <label for="name">الاسم <span style="color: red">*</span></label>
                                    <input type="text" id="name" class="form-control" placeholder="الاسم"
                                        name="name" value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger" id="basic-default-name-error" class="error">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <!-- الحالة -->
                                <div class="form-group col-md-6">
                                    <label for="status">الحالة</label>
                                    <select id="status" name="status" class="form-control select2">
                                        <option value="">اختر الحالة</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                </div>

                                <!-- الضرائب -->
                                <div class="form-group col-md-6">
                                    <label for="tax"> الضرائب <span style="color: red">*</span></label>
                                    <select class="form-control" id="tax" name="tax">
                                        <option value="">اختر الضريبة </option>
                                        <option value="1" {{ old('tax') == 1 ? 'selected' : '' }}> القيمة المضافة</option>
                                        <option value="2" {{ old('tax') == 2 ? 'selected' : '' }}> القيمة الصفرية</option>
                                    </select>
                                </div>

                                <!-- الرسوم -->
                                <div class="form-group col-md-6">
                                    <label for="cost">الرسوم</label>
                                    <input type="text" id="cost" name="cost" class="form-control">
                                </div>

                                <!-- ترتيب العرض -->
                                <div class="form-group col-md-6">
                                    <label for="display_order">ترتيب العرض <span style="color: red">*</span></label>
                                    <input type="text" id="display_order" name="display_order" class="form-control">
                                </div>

                                <!-- الحساب الافتراضي -->
                                <div class="form-group col-md-6">
                                    <label for="default_account_id">الحساب الافتراضي </label>
                                    <select class="form-control select2" id="default_account_id" name="default_account_id">
                                        <option value="">اختر الحساب الافتراضي </option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- الوصف -->
                                <div class="form-group col-md-6">
                                    <label for="description">الوصف <span style="color: red">*</span></label>
                                    <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
