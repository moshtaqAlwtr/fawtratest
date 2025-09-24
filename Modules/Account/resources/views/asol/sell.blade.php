@extends('master')

@section('title')
    بيع الأصل - {{ $asset->name }}
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-start mb-0">بيع الأصل</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('Assets.index') }}">الأصول</a></li>
                            <li class="breadcrumb-item active">بيع الأصل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">معلومات البيع</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('Assets.sell', $asset->id) }}" method="POST" class="form form-vertical">
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
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <div>
                                            <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                                        </div>

                                        <div>
                                            <a href="{{ route('employee.index') }}" class="btn btn-outline-danger">
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
                                <div class="card-header bg-gradient-success text-white font-weight-bold p-1">
                                    معلومات البيع
                                </div>
                                <div class="card-body">

                                    <div class="form-row">

                                        <div class="form-group col-md-6">
                                            <label for="sale_date">التاريخ <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="sale_date" name="sale_date"
                                                value="{{ old('sale_date', date('Y-m-d')) }}" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="sale_price">سعر البيع <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control" id="sale_price"
                                                name="sale_price" value="{{ old('sale_price') }}" required>
                                        </div>


                                    </div>

                                    <div class="form-group row">
                                        <div class="form-group col-md-4">
                                            <label for="cash_account">حساب النقدية <span class="text-danger">*</span></label>
                                            <select name="cash_account" id="cash_account" class="form-control select2" required>
                                                <option value="">اختر الحساب</option>
                                                @foreach($accounts_all as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ old('cash_account') == $account->id ? 'selected' : '' }}>
                                                        {{ $account->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                            <div class="form-group col-md-4">
                                                <label for="tax1">الضريبة 1</label>
                                                <select name="tax1" id="tax1" class="form-control">
                                                    <option value="">بدون ضريبة</option>
                                                    <option value="1" {{ old('tax1') == '1' ? 'selected' : '' }}>القيمة المضافة</option>
                                                    <option value="2" {{ old('tax1') == '2' ? 'selected' : '' }}>صفرية</option>
                                                    <option value="3" {{ old('tax1') == '3' ? 'selected' : '' }}>قيمة مضافة</option>
                                                </select>
                                            </div>


                                            <div class="form-group col-md-4">
                                                <label for="tax2">الضريبة 2</label>
                                                <select name="tax2" id="tax2" class="form-control">
                                                    <option value="">بدون ضريبة</option>
                                                    <option value="1" {{ old('tax2') == '1' ? 'selected' : '' }}>القيمة المضافة</option>
                                                    <option value="2" {{ old('tax2') == '2' ? 'selected' : '' }}>صفرية</option>
                                                    <option value="3" {{ old('tax2') == '3' ? 'selected' : '' }}>قيمة مضافة</option>
                                                </select>
                                            </div>

                                    </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
