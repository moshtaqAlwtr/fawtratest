@extends('master')

@section('title')
    أدارة أدوار الموظفين
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أدارة أداوار الموظفين</h2>
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

                        <a href="{{ route('managing_employee_roles.create') }}" class="btn btn-outline-primary">
                            <i class="fa fa-plus"></i> دور جديد
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

                    <!-- جدول عرض الأدوار -->
                    @if (@isset($roles) && !@empty($roles) && count($roles) > 0)
                    <table class="table table-hover align-middle text-center table-striped">
                        <thead>
                            <tr>
                                <th scope="col">المعرف</th>
                                <th scope="col">الدور الوظيفي</th>
                                <th scope="col">النوع</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $index=>$role)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $role->role_name }}</td>
                                <td>@if($role->role_type == 1) مستخدم @else موظف @endif</td>
                                <td>
                                    <a href="{{ route('managing_employee_roles.edit',$role->id) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fa fa-pencil-square"></i> تعديل
                                    </a>
                                    <button class="btn btn-outline-secondary btn-sm">
                                        <i class="fa fa-file"></i> نسخ
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm">
                                        <i class="fa fa-lock"></i> صفحات محظورة
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#modal_DELETE{{ $role->id }}">
                                        <i class="fa fa-trash"></i> حذف
                                    </button>
                                </td>

                                <!-- Modal delete -->
                                <div class="modal fade text-left" id="modal_DELETE{{ $role->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #EA5455 !important;">
                                                <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $role->role_name }}</h4>
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
                                                <a href="{{ route('managing_employee_roles.delete',$role->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
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
                                لا توجد ادوار موظفين مضافه حتى الان !!
                            </p>
                        </div>
                    @endif

                </div>

                <!-- رسالة نجاح عند تنفيذ الإجراء -->
                <div class="alert alert-success mt-3" style="display: none;">
                    تم تنفيذ الإجراء بنجاح.
                </div>
            </div>
        </div>

    </div>

@endsection
