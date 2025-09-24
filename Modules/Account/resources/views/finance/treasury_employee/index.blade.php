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
                            <li class="breadcrumb-item active">عرض
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
                    </div>

                    <div>
                        <a href="{{ route('treasury_employee.create') }}" class="btn btn-outline-primary">
                            <i class="fa fa-plus"></i> أضف الخزينة الافتراضية
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>بحث</div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form class="form" method="GET" action="">
                        <div class="form-body row">

                            <div class="form-group col-md-6">
                                <label for="amount">الموظف</label>
                                <select class="form-control" name="employee_id">
                                    <option selected value="">-- اختر الموظف --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->full_name??'' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="amount">الخزينة الافتراضية</label>
                                <select class="form-control" name="treasury_id">
                                    <option selected value="">-- اختر الخزينة --</option>
                                    @foreach($treasuries as $treasury)
                                        <option value="{{ $treasury->id }}">{{ $treasury->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                            <a href="{{ route('finance_settings.treasury_employee') }}" class="btn btn-outline-danger waves-effect waves-light">الغاء الفلترة</a>
                        </div>
                    </form>

                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header"></div>
            <div class="card-body">
                @if (@isset($treasury_employees) && !@empty($treasury_employees) && count($treasury_employees) > 0)
                    @php
                        // تصفية الموظفين الذين لديهم خزائن
                        $filteredEmployees = $treasury_employees->filter(function ($info) {
                            return isset($info->treasury) && !empty($info->treasury);
                        });
                    @endphp

                    @if ($filteredEmployees->count() > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>الخزينة</th>
                                    <th style="width: 10%">اجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($filteredEmployees as $info)
                                    <tr>
                                        <td>{{ $info->employee->full_name ?? 'غير محدد' }}</td>
                                        <td>{{ $info->treasury->name ?? 'غير محدد' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button" id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('treasury_employee.edit', $info->id) }}">
                                                                <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $info->id }}">
                                                                <i class="fa fa-trash me-2"></i>حذف
                                                            </a>
                                                        </li>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Modal delete -->
                                        <div class="modal fade text-left" id="modal_DELETE{{ $info->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #EA5455 !important;">
                                                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف خزينة موظف</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true" style="color: #DC3545">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <strong>
                                                            هل انت متاكد من انك تريد الحذف ؟
                                                        </strong>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">الغاء</button>
                                                        <a href="{{ route('treasury_employee.delete', $info->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end delete-->
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-danger text-xl-center" role="alert">
                            <p class="mb-0">
                                لا توجد خزائن موظفين مضافه حتى الان
                            </p>
                        </div>
                    @endif
                @else
                    <div class="alert alert-danger text-xl-center" role="alert">
                        <p class="mb-0">
                            لا توجد بيانات متاحة
                        </p>
                    </div>
                @endif
                {{ $treasury_employees->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div><!-- content-body -->
    @endsection
