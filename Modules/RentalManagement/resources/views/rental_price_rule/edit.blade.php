@extends('master')

@section('title')
    تعديل قواعد التسعير
@stop

@section('content')

    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تعديل قواعد التسعير </h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                        <li class="breadcrumb-item active">عرض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form   action="{{ route('rental_management.rental_price_rule.update', $pricingRule->id) }}" method="POST" id="products_form" enctype="multipart/form-data">
        @csrf <!-- إضافة CSRF token للحماية -->

        @method('PUT')
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
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>

                    <div>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i>الغاء
                        </a>
                        <button type="submit" form="products_form" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i>حفظ
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="card mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="contant-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="card mt-5">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">معلومات عن قاعدة التسعير</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="pricingName" class="form-label">اسم قاعدة التسعير <span class="text-danger">*</span></label>
                                                <input type="text" id="pricingName" name="pricingName" class="form-control" value="{{ old('pricingName', $pricingRule->pricing_name) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                                <div class="d-flex align-items-center">
                                                    <div class="form-check me-3">
                                                        <input type="radio" id="active" name="status" class="form-check-input" value="1" {{ old('status', $pricingRule->status) == 1 ? 'checked' : '' }}>
                                                        <label for="active" class="form-check-label">نشط</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="radio" id="inactive" name="status" class="form-check-input" value="2" {{ old('status', $pricingRule->status) == 2 ? 'checked' : '' }}>
                                                        <label for="inactive" class="form-check-label">غير نشط</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <x-form.select label="من العملة" name="currency" id="from_currency" col="6">
                                                <option value="">العملة</option>
                                                @foreach (\App\Helpers\CurrencyHelper::getAllCurrencies() as $code => $name)
                                                    <option value="{{ $code }}">{{ $code }} {{ $name }}</option>
                                                @endforeach
                                            </x-form.select>
                                            <div class="col-md-6">
                                                <label for="pricingMethod" class="form-label">طريقة
                                                    التسعير <span class="text-danger">*</span></label>
                                                <select id="pricingMethod" name="pricingMethod" class="form-control"
                                                    required>
                                                    <option value="1">بالساعات</option>
                                                    <option value="2" selected>بالأيام</option>
                                                    <option value="3">بالأسبوع</option>
                                                    <option value="4">بالشهر</option>
                                                    <option value="5">6 شهور</option>
                                                    <option value="6">بالسنة</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="dailyPrice" class="form-label">سعر اليوم <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="dailyPrice" name="dailyPrice" class="form-control"
                                                value="{{ old('dailyPrice', $pricingRule->daily_price) }}">
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>


                </div>
    </form>

@endsection
