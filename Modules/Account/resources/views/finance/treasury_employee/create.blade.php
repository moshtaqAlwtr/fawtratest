@extends('master')

@section('title')
خزائن الموظفين
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">خزائن الموظفين</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">اضافه
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>

                    <div>
                        <a href="{{ route('finance_settings.treasury_employee') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i>الغاء
                        </a>

                        <button type="submit" form="expenses_form" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i>حفظ
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="expenses_form" action="{{ route('treasury_employee.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">

                        <div class="form-group col-md-6">
                            <label for="amount">الموظف <span style="color: red">*</span></label>
                            <select class="form-control" name="employee_id">
                                <option selected value="">-- اختر الموظف --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                            @error('employee_id')
                            <span class="text-danger" id="basic-default-name-error" class="error">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="amount">الخزينة الافتراضية <span style="color: red">*</span></label>
                            <select class="form-control" name="treasury_id">
                                <option selected value="">-- اختر الخزينة --</option>
                                @foreach($treasuries as $treasury)
                                    <option value="{{ $treasury->id }}" {{ old('treasury_id') == $treasury->id ? 'selected' : '' }}>{{ $treasury->name }}</option>
                                @endforeach
                            </select>
                            @error('treasury_id')
                            <span class="text-danger" id="basic-default-name-error" class="error">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                    </div>

                </form>
            </div>
        </div>

    </div>

@endsection
