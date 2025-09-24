@extends('master')

@section('title')
تعديل قسم
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل قسم</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">تعديل
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="container-fluid">
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

            <form class="form-horizontal" action="{{ route('department.update',$department->id) }}" method="POST">
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
                            <h4 class="card-title">معلومات القسم </h4>
                        </div>

                        <div class="card-body">
                            <form class="form">
                                <div class="form-body row">

                                    <div class="form-group col-md-6">
                                        <label>الاسم <span style="color: red">*</span></label>
                                        <input type="text" id="feedback2" class="form-control" name="name" value="{{ old('name',$department->name) }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label> الاختصار </label>
                                        <input type="text" id="feedback2" class="form-control" name="short_name" value="{{ old('short_name',$department->short_name) }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">الحالة <span style="color: red">*</span></label>
                                        <select class="form-control" id="basicSelect" name="status">
                                            <option value="0" {{ old('status',$department->status) == 0 ? 'selected' : '' }}>نشط</option>
                                            <option value="1" {{ old('status',$department->status) == 1 ? 'selected' : '' }}>غير نشط</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">المديرون المعنيون</label>
                                        <select id="feedback2" class="form-control select2" name="employee_id[]" multiple="multiple">
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ in_array($employee->id, old('employee_id', $selectedEmployees ?? [])) ? 'selected' : '' }}>
                                                    {{ $employee->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="feedback2" class="sr-only">الوصف </label>
                                        <textarea id="feedback2" class="form-control" rows="3" placeholder="الوصف" name="description">{{ old('description',$department->description) }}</textarea>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
