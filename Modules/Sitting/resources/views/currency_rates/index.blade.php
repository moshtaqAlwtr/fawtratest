@extends('master')

@section('title')
    اسعار العملات
@stop
@section('content')
    <x-layout.breadcrumb title="اسعار العملات " :items="[['title' => 'عرض']]" />
    <div class="content-body">
        <x-layout.card>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>
                <div>
                    <a href="{{ route('CurrencyRates.create') }}" class="btn btn-outline-primary">
                        <i class="fa fa-plus'"></i> اضف سعر العملة
                    </a>
                </div>
            </div>
        </x-layout.card>

        <x-layout.card title="بحث">
            <form class="form" method="GET" action="">
                <div class="form-body row">
                    <x-form.select label="العملة" name="currency" col="4">
                        <option value="">اختر العملة</option>
                        @foreach (\App\Helpers\CurrencyHelper::getAllCurrencies() as $code => $name)
                            <option value="{{ $code }}">{{ $code }} {{ $name }}</option>
                        @endforeach
                    </x-form.select>
                </div>

                <!-- Hidden Div -->
                <div class="collapse" id="advancedSearchForm">
                    <div class="form-body row">
                        <x-form.select label="تخصيص" name="seller" col="2">
                            <option value="">اي بائع</option>
                            <option value="1">بائع 1</option>
                            <option value="2">بائع 2</option>
                        </x-form.select>

                        <x-form.input label="من (تاريخ الانشاء)" name="created_from" type="date" col="2" />
                        <x-form.input label="الى (تاريخ الانشاء)" name="created_to" type="date" col="2" />
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                    <a class="btn btn-outline-secondary ml-2 mr-2" data-toggle="collapse" data-target="#advancedSearchForm">
                        <i class="bi bi-sliders"></i> بحث متقدم
                    </a>
                </div>
            </form>
        </x-layout.card>
        <x-layout.card>
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 20px">
                                    <input type="checkbox" class="form-check-input">
                                </th>
                                <th>الرقم التعريفي</th>
                                <th>من العملة </th>
                                <th>من العملة </th>
                                <th>السعر</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input">
                                </td>
                                <td>1</td>
                                <td>SAR</td>
                                <td>USD</td>
                                <td>3.50</td>
                                <td>2023-01-01</td>
                                <td>

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('CurrencyRates.edit', 1) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                            تعديل
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                            حذف
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>


                </div>
            </div>
        </x-layout.card>
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-auto">
                    <button class="btn btn-sm btn-outline-danger me-2">
                        <i class="fas fa-trash"></i>
                        حذف للمحدد
                    </button>
                    <button class="btn btn-sm btn-outline-secondary me-2">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center">
                        <span>1-1 من 1 النتائج المعروضة</span>
                    </div>
                </div>
            </div>


        </div>
    </div>
    </div>
    </div>
@endsection
