@extends('master')

@section('title')
    إعدادات الفاتورة الإلكترونية
@stop

@section('content')
@section('content')
<style>
    .card-setting, .card-toolbar {
        border-radius: 16px;
        box-shadow: 0 2px 8px #e8e8e8;
        background: #fff;
    }
    .card-toolbar {
        padding: 18px 25px 12px 25px;
        margin-bottom: 14px;
    }
    .card-setting {
        padding: 30px 25px 15px 25px;
    }
    .form-label {
        font-weight: bold;
        color: #15447a;
    }
    .form-control:focus {
        box-shadow: 0 0 0 0.2rem #b2d3ff;
        border-color: #4095d6;
    }
    .form-required {
        color: red;
        font-size: 1.1em;
        font-weight: bold;
    }
    .is-invalid {
        border-color: #ea3949;
        background-color: #fbeaea;
    }
    .invalid-feedback {
        color: #ea3949;
    }
    .page-title {
        color: #15447a;
        font-weight: 700;
        margin-bottom: 28px;
        font-size: 1.45rem;
    }
    .settings-toolbar {
        direction: ltr !important;
    }
    .settings-toolbar .btn {
        min-width: 90px;
        margin-left: 10px;
    }
    .btn-save {
        background-color: #1e72d0 !important;
        color: #fff !important;
        font-weight: bold;
        border-radius: 6px;
        box-shadow: 0 1px 4px rgba(30,114,208,.08);
    }
    .btn-save:hover {
        background-color: #155fa0 !important;
        color: #fff !important;
    }
</style>


<div class="container py-3">
    <h2 class="page-title">إعدادات الفاتورة الإلكترونية</h2>

    <form action="" method="POST">
        @csrf
        <div class="card card-toolbar mb-2">
            <div class="settings-toolbar d-flex justify-content-start">
                <button type="submit" class="btn btn-save">
                    <i class="fa fa-save"></i> حفظ
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    <i class="fa fa-times"></i> إلغاء
                </a>
            </div>
        </div>

        <div class="card card-setting">
            <div class="row">
                {{-- الاسم الشائع --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">الاسم الشائع <span class="form-required">*</span></label>
                    <input type="text" name="branch_name"
                           class="form-control @error('branch_name') is-invalid @enderror"
                           value="{{ old('branch_name', $settings['branch_name'] ?? '') }}">
                    @error('branch_name')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- اسم الفرع أو رقم المجموعة الضريبية --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم الفرع أو رقم المجموعة الضريبية <span class="form-required">*</span></label>
                    <input type="text" name="tax_group"
                           class="form-control @error('tax_group') is-invalid @enderror"
                           value="{{ old('tax_group', $settings['tax_group'] ?? '') }}">
                    @error('tax_group')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- السجل التجاري --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">سجل تجاري</label>
                    <input type="text" name="commercial_register"
                           class="form-control @error('commercial_register') is-invalid @enderror"
                           value="{{ old('commercial_register', $settings['commercial_register'] ?? '') }}">
                    @error('commercial_register')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- الرقم الضريبي --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">الرقم الضريبي <span class="form-required">*</span></label>
                    <input type="text" name="tax_number"
                           class="form-control @error('tax_number') is-invalid @enderror"
                           value="{{ old('tax_number', $settings['tax_number'] ?? '') }}">
                    @error('tax_number')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- اسم المؤسسة --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المؤسسة</label>
                    <input type="text" name="company_name"
                           class="form-control @error('company_name') is-invalid @enderror"
                           value="{{ old('company_name', $settings['company_name'] ?? '') }}">
                    @error('company_name')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- رمز الدولة --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">رمز الدولة <span class="form-required">*</span></label>
                    <input type="text" name="country_code"
                           class="form-control @error('country_code') is-invalid @enderror"
                           value="{{ old('country_code', $settings['country_code'] ?? 'SA') }}">
                    @error('country_code')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- تصنيف العمل --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">تصنيف العمل</label>
                    <input type="text" name="business_category"
                           class="form-control @error('business_category') is-invalid @enderror"
                           value="{{ old('business_category', $settings['business_category'] ?? '') }}">
                    @error('business_category')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- العنوان --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان <span class="form-required">*</span></label>
                    <input type="text" name="address"
                           class="form-control @error('address') is-invalid @enderror"
                           value="{{ old('address', $settings['address'] ?? '') }}">
                    @error('address')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
@endsection