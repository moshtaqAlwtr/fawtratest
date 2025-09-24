@extends('master')

@section('title')
    بيانات الحساب 
@stop

@section('content')
    <div style="font-size: 1.1rem;">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0"> اضافة عضوية </h2>
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

        <form id="clientForm" action="{{ route('AccountInfo.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="content-body">
                <div class="card mb-5">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                            <div>
                                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                            </div>
                            <div>
                                <a href="" class="btn btn-outline-danger">
                                    <i class="fa fa-ban"></i> الغاء
                                </a>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa fa-save"></i> حفظ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" style="max-width: 90%; margin: 0 auto;">
                    <div class="card-header">
                        <h1>بيانات العمل</h1>
                    </div>
                    <div class="card-body">
                        <div class="form-body row mb-5 align-items-center">
                            <div class="form-group col-md-4 mb-3">
                                <label for="business_name" class="">الاسم التجاري <span class="text-danger">*</span></label>
                                <input type="name" class="form-control @error('business_name') is-invalid @enderror" 
                                       name="business_name" value="{{ old('business_name') }}">
                                @error('business_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label for="first_name" class="">الاسم الأول <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       name="first_name" value="{{ old('first_name') }}">
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label for="last_name" class="">الاسم الأخير <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       name="last_name" value="{{ old('last_name') }}">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-body row mb-5 align-items-center">
                            <div class="form-group col-md-6 mb-3">
                                <label for="phone" class="">الهاتف <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="mobile" class="">الجوال <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('mobile') is-invalid @enderror" 
                                       name="mobile" value="{{ old('mobile') }}">
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-body row mb-5 align-items-center">
                            <div class="form-group col-md-6 mb-3">
                                <label for="address1" class="">عنوان الشارع الأول <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address1') is-invalid @enderror" 
                                       name="address1" value="{{ old('address1') }}">
                                @error('address1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="address2" class="">عنوان الشارع الثاني</label>
                                <input type="text" class="form-control @error('address2') is-invalid @enderror" 
                                       name="address2" value="{{ old('address2') }}">
                                @error('address2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-body row mb-5 align-items-center">
                            <div class="form-group col-md-6 mb-3">
                                <label for="city" class="">المدينة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       name="city" value="{{ old('city') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="postal_code" class="">الرمز البريدي <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                       name="postal_code" value="{{ old('postal_code') }}">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group col-md-6 mb-3">
                            <label for="country" class="">البلد <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                   name="country" value="{{ old('country') }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                    </div>
                </div>

           

            <div>
            </br>
        </hr>
            </div>
           
            <div class="card" style="max-width: 90%; margin: 0 auto;">
                <div class="card-header">
                    <h1>إعدادات الحساب</h1>
                </div>
                <div class="card-body">
                    <div class="form-body row mb-5 align-items-center">
                        <div class="form-group col-md-6 mb-3">
                            <label for="currency" class="">العملة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('currency') is-invalid @enderror" 
                                   name="currency" value="{{ old('currency') }}">
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="timezone" class="">المنطقة الزمنية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('timezone') is-invalid @enderror" 
                                   name="timezone" value="{{ old('timezone') }}">
                            @error('timezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
            
                    <div class="form-group col-md-6 mb-3">
                        <label for="business_type" class="">نوع العمل <span class="text-danger">*</span></label>
                        <select name="business_type" class="form-control @error('business_type') is-invalid @enderror">
                            <option value="products" {{ old('business_type') == 'products' ? 'selected' : '' }}>منتجات</option>
                            <option value="services" {{ old('business_type') == 'services' ? 'selected' : '' }}>خدمات</option>
                            <option value="products_services" {{ old('business_type') == 'products_services' ? 'selected' : '' }}>منتجات وخدمات</option>
                        </select>
                        @error('business_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
        </form>
    </div>
@endsection
