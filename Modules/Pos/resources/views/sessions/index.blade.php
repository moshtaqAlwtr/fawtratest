@extends('master')

@section('title')
الجلسات
@stop

@section('content')

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الجلسات</h2>
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

        @include('layouts.alerts.success')
        @include('layouts.alerts.error')

        <!-- بطاقة البحث -->
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>بحث </div>
                            <div>
                                <a href="{{ route('attendanceDays.create') }}" class="btn btn-outline-success">
                                    <i class="fa fa-plus me-2"></i>بدء الجلسة
                                </a>
                            </div>
                        </div>
                    </div>
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="device" class="form-label">جهاز</label>
                                <select id="device" class="form-control">
                                    <option>أي جهاز</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="shift" class="form-label">وردة</label>
                                <select id="shift" class="form-control">
                                    <option>أي وردة</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">الحالة</label>
                                <select id="status" class="form-control">
                                    <option>أي حالة</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="session-number" class="form-label">رقم الجلسة</label>
                                <input type="text" id="session-number" class="form-control">
                            </div>
                        </div>
                        <div class="d-flex justify-content-start">
                            <button type="submit" class="btn btn-primary me-2">بحث</button>
                            <button type="reset" class="btn btn-secondary">إلغاء الفلتر</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- بطاقة الجدول -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                            <th>رقم الجلسة</th>
                                <th>الجلسة/موظف الخزنة</th>
                                <th>المبيعات</th>
                                <th>فتح/إغلاق</th>
                                <th>الحالة</th>
                          
                               
                                <th>الترتيب حسب</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td>Main POS Device/2021/12/02/1</td>
                                <td>محمد العتيبي</td>
                                <td>69.00 ر.س</td>
                              
                                <td>11:22 02/12/2021</td>
                                <td><span class="badge bg-danger">مفتوحة</span></td>
                                <td>
                                                <div class="btn-group">
                                                    <div class="dropdown">
                                                        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm" type="button"id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"aria-expanded="false"></button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                            <li>
                                                                <a class="dropdown-item" href="">
                                                                    <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="">
                                                                    <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#">
                                                                    <i class="fa fa-trash me-2"></i>حذف
                                                                </a>
                                                            </li>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

@endsection
