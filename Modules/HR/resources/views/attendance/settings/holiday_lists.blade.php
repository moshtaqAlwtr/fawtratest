@extends('master')

@section('title', 'قوائم العطلات')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">قوائم العطلات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item active">إضافة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
    <div class="card-body">
        <!-- السطر الأول: زر أضف وكلمة بحث -->
        <div class="row mb-3 align-items-right">
        <div class="col-md-6 text-md-start">
                <span class="h5">بحث</span>
            </div>
            <div class="col-md-6">
                <a href="{{ route('Attendance.attendance-days.create') }}" class="btn btn-outline-success">
                    <i class="fa fa-plus me-2"></i>أضف قائمة العطلات
                </a>
            </div>
        </div>

        <!-- السطر الثاني: حقل البحث -->
        <form id="search-form" method="GET">
            <div class="row">
                <div class="col-12">
                    <label for="employee-search" class="form-label">الموظف:</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="employee-search" 
                        name="employee" 
                        placeholder="البحث باسم الموظف أو المعرف أو البريد الإلكتروني">
                </div>
            </div>
            <div class="d-flex justify-content-start mt-3">
                <!-- زر البحث -->
                <button type="submit" class="btn btn-primary me-2 waves-effect waves-light">بحث</button>

                <!-- زر الإلغاء -->
                <button type="reset" class="btn btn-outline-warning waves-effect waves-light">إلغاء</button>
            </div>
        </form>
    </div>
</div>
<div class="card mt-5">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th scope="col">الاسم</th>
                    <th scope="col">إجمالي الأيام</th>
                    <th scope="col">ترتيب بواسطة</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>قائمة العطلات العامة #1</td>
                    <td>0</td>
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








    @endsection