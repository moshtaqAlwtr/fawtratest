@extends('master')

@section('title')
سجلات الحضور
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">سجلات الحضور</h2>
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

<div class="content-body">

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div></div>
                <div>
                    <a href="{{ route('employee.export_view') }}" class="btn btn-outline-info waves-effect waves-light">
                        <i class="fa fa-share-square me-2"></i>  تصدير
                    </a>
                    <a href="{{ route('employee.create') }}" class="btn btn-outline-primary waves-effect waves-light">
                        <i class="fa fa-plus me-2"></i> اضافة موظف جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <form class="form" method="GET" action="#">
                    <div class="form-body row">
                        <div class="form-group col-md-6">
                            <select name="employee" class="form-control">
                                <option value="">البحث بواسطة اسم الموظف أو الرقم التعريفي</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <select name="status" class="form-control">
                                <option value="">أختر الحالة</option>
                                <option value="1">تسجيل خروج</option>
                                <option value="0">تسجيل خروج مكرر</option>
                            </select>
                        </div>
                    </div>
                    <!-- Hidden Div -->
                    <div class="collapse" id="advancedSearchForm" style="">
                        <div class="form-body row">
                            <div class="form-group col-md-4">
                                <label for="from_date">بواسطة التاريخ (من)</label>
                                <input type="date" id="from_date" class="form-control" name="from_date">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="to_date">بواسطة التاريخ (إلى)</label>
                                <input type="date" id="to_date" class="form-control" name="to_date">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="job_type">بواسطة نوع المصدر</label>
                                <select name="job_type" class="form-control">
                                    <option value="">أختر نوع المصدر</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <select name="department" class="form-control">
                                    <option value="">أختر القسم</option>
                                    <option value="1">الكل</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <select name="department" class="form-control">
                                    <option value="">أختر الفرع</option>
                                    <option value="1">الكل</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                        <a class="btn btn-outline-secondary ml-2 mr-2 waves-effect waves-light collapsed" data-toggle="collapse" data-target="#advancedSearchForm" aria-expanded="false">
                            <i class="bi bi-sliders"></i> بحث متقدم
                        </a>
                        <a href="#" class="btn btn-outline-danger waves-effect waves-light">الغاء الفلترة</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header" style="background: #f8f8f8">
            <h5 class="text-body text-center mb-1">سجلات الحضور</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">موظف</th>
                        <th scope="col">المصدر</th>
                        <th scope="col">رقم الجلسة</th>
                        <th scope="col">الحالة</th>
                        <th scope="col">سجل</th>
                        <th scope="col">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>راكب الغنيمي</td>
                        <td>محمد العتيبي</td>
                        <td>#1</td>
                        <td><span class="badge bg-warning text-dark">غير محسوبة</span></td>
                        <td>08:17 27/12/2024</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn bg-gradient-info fa fa-ellipsis-v" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#"><i class="fa fa-eye me-2 text-primary"></i>عرض</a>
                                    <a class="dropdown-item" href="#"><i class="fas fa-edit text-primary me-2"></i>تعديل</a>
                                    <div class="dropdown-divider"></div>
                                    <button class="dropdown-item text-danger delete-client">
                                        <i class="fas fa-trash me-2"></i>حذف
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
