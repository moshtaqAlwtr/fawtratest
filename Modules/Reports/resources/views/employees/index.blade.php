@extends('master')

@section('title')
تقارير الموظفين
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تقارير الموظفين</h2>
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
<!DOCTYPE html>
<html lang="ar" dir="rtl">


    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-users text-primary"></i> تقارير الموظفين
                        </h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-id-card text-info me-3"></i>
                                تقرير حالة اقامات الموظفين
                                <a href="{{ route('reports.employees.Residences') }}" class="ms-auto">عرض</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-user-lock text-warning"></i> تقارير الحضور
                        </h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-user text-primary me-3"></i>
                                تقرير الحضور تفصيلي - موظف واحد
                                <a href="{{ route('reports.employees.attendance') }}" class="ms-auto">عرض</a>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-users text-danger me-3"></i>
                                تقرير الحضور تفصيلي - عدة موظفين
                                <a href="{{ route('reports.employees.attendance_multiple') }}" class="ms-auto">عرض</a>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-clipboard-list text-warning me-3"></i>
                                تقرير ترصيد إجازات الموظفين
                                <a href="{{ route('reports.employees.Leaves') }}" class="ms-auto">عرض</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-file-alt text-success"></i>  ملخص تقرير الحضور
                        </h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-user text-primary me-3"></i>
                                ملخص تقرير الحضور - حسب الموظف
                                <a href="{{ route('reports.employees.Attendance_by_Employee') }}" class="ms-auto">عرض</a>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-building text-secondary me-3"></i>
                                ملخص تقرير الحضور - حسب اليوم
                                <a href="#" class="ms-auto">عرض</a>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-layer-group text-info me-3"></i>
                                ملخص تقرير الحضور حسب الأسبوع
                                <a href="#" class="ms-auto">عرض</a>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-layer-group text-info me-3"></i>
                                ملخص تقرير الحضور حسب الشهر
                                <a href="#" class="ms-auto">عرض</a>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-layer-group text-info me-3"></i>
                                ملخص تقرير الحضور حسب السنه
                                <a href="#" class="ms-auto">عرض</a>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-layer-group text-info me-3"></i>
                                ملخص تقرير الحضور حسب القسم
                                <a href="#" class="ms-auto">عرض</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-file-alt text-success"></i>  ملخص تقرير المرتبات
                        </h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-user text-primary me-3"></i>
                                تقرير المرتبات - حسب الموظف
                                <a href="{{ route('reports.employees.Attendance_by_Employee') }}" class="ms-auto">عرض</a>
                            </li>
                            <!-- <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-building text-secondary me-3"></i>
                                ملخص المرتبات - حسب اليوم
                                <a href="{{ route('reports.employees.Attendance-by-Day') }}" class="ms-auto">عرض</a>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-layer-group text-info me-3"></i>
                                ملخص المرتبات حسب الأسبوع
                                <a href="{{ route('reports.employees.attendance-by-week') }}" class="ms-auto">عرض</a>
                            </li> -->
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-layer-group text-info me-3"></i>
                                تقرير المرتبات شهري 
                                <a href="{{ route('reports.employees.attendance-by-month') }}" class="ms-auto">عرض</a>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-layer-group text-info me-3"></i>
                                تقرير المرتبات سنوي 
                                <a href="{{ route('reports.employees.attendance-by-year') }}" class="ms-auto">عرض</a>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-layer-group text-info me-3"></i>
                                تقرير المرتبات حسب القسم
                                <a href="{{ route('reports.employees.attendance-by-department') }}" class="ms-auto">عرض</a>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-layer-group text-info me-3"></i>
                                تقرير المرتبات حسب الفرع
                                <a href="{{ route('reports.employees.Salaries_by_Branch') }}" class="ms-auto">عرض</a>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-layer-group text-info me-3"></i>
                                تقرير سلف الموظفين 
                                <a href="{{ route('reports.employees.Advances') }}" class="ms-auto">عرض</a>
                            </li>
                        </ul>    <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-layer-group text-info me-3"></i>
                                تقرير عقود الموظفين 
                                <a href="{{ route('reports.employees.Contracts') }}" class="ms-auto">عرض</a>
                            </li>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-calendar-check text-purple"></i> تقرير ورديات الحضور
                        </h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-clock text-danger me-3"></i>
                                تقرير ورديات الحضور
                                <a href="{{ route('reports.employees.shift') }}" class="ms-auto">عرض</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

