@extends('master')

@section('title')
المستودعات الأفتراضية للموظفين
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">المستودعات الأفتراضية للموظفين</h2>
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
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body">
                <form class="form form-vertical" action="{{ route('inventory_settings.employee_default_warehouse_update',$default_warehouse->id) }}" method="POST">
                    @csrf
                    <div class="form-body">

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="first-name-vertical">موظف <span class="text-danger">*</span></label>
                                    <select class="form-control" id="basicSelect" name="employee_id">
                                        <option value="" selected disabled>اختر الموظف</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id', $default_warehouse->employee_id) == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="email-id-vertical">المستودع الافتراضي للموظف <span class="text-danger">*</span></label>
                                    <select class="form-control" id="basicSelect" name="storehouse_id">
                                        <option value="" selected disabled>اختر المستودع الافتراضي للموظف</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('storehouse_id', $default_warehouse->storehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">تحديث</button>
                                <button type="reset" class="btn btn-outline-warning mr-1 mb-1 waves-effect waves-light">تفريغ</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
@endsection
