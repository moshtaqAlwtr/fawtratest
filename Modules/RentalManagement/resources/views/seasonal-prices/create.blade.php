@extends('master')

@section('title')
أضف سعر موسمي
@stop

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">أضف سعر موسمي</h2>
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
<form   action="{{ route('rental_management.seasonal-prices.store') }}" method="POST" id="products_form" enctype="multipart/form-data">
    @csrf <!-- إضافة CSRF token للحماية -->

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
            <h5 class="card-title mb-4">بيانات السعر الموسمي</h5>
         
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="unitType" class="form-label">نوع الوحدة (الوحدات) <span
                                class="text-danger">*</span></label>
                                <select name="unit_type_id" id="unit_type_id" class="form-control" required>
                                    <option value="" disabled selected>إختر نوع الوحدة</option>
                                    @foreach ($unitTypes as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">التاريخ من <span class="text-danger">*</span></label>
                        <input type="date" name="date_from" id="date_from" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">التاريخ إلى <span class="text-danger">*</span></label>
                        <input type="date" name="date_to" id="date_to" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-10">
                        <label for="pricingRule" class="form-label">قاعدة التسعير <span
                                class="text-danger">*</span></label>
                                <select name="pricing_rule_id" id="pricing_rule_id" class="form-control" required>
                                    <option value="" disabled selected>إختر قاعدة التسعير</option>
                                    @foreach ($pricingRules as $rule)
                                        <option value="{{ $rule->id }}">{{ $rule->pricingName }}</option>
                                    @endforeach
                                </select>
                                
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-secondary w-100"
                            onclick="window.location.href='rental_management.rental_price_rule.create'">أضف
                            قاعدة</button>
                    </div>
                </div>


                <div id="basic">
                    <div class="card">
                        <div class="card-header p-1" style="background: #f8f8f8"><strong class="">أيام العمل</strong>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>يوم</th>
                                        <th style="width: 50%">يوم عمل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $days = ['sunday' => 'الأحد', 'monday' => 'الإثنين', 'tuesday' => 'الثلاثاء', 'wednesday' => 'الأربعاء', 'thursday' => 'الخميس', 'friday' => 'الجمعة', 'saturday' => 'السبت'];
                                    @endphp

                                    @foreach ($days as $dayKey => $dayName)
                                        <tr>
                                            <td>{{ $dayName }}</td>
                                            <td>
                                                <div class="custom-control custom-switch custom-switch-success mr-2 mb-1">
                                                    <input type="checkbox" class="custom-control-input" id="{{ $dayKey }}"
                                                        name="days[{{ $dayKey }}][working_day]" value="1" {{ in_array($dayKey, ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday']) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="{{ $dayKey }}"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>





@endsection