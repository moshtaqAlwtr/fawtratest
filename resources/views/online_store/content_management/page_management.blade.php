@extends('master')

@section('title')
إدارة صفحات المحتوي
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إدارة صفحات المحتوي</h2>
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
        <div class="container-fluid">
            <div class="card">

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div></div>
                        <div>
                            <a href="{{ route('content_management.page_management_create') }}" class="btn btn-outline-primary">
                                <i class="fa fa-plus me-2"></i> أضف المحتوى
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @include('layouts.alerts.error')
            @include('layouts.alerts.success')

            {{-- @if (@isset($departments) && !@empty($departments) && count($departments) > 0) --}}
                <div class="card">
                    <div class="card-body">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>المعرف</th>
                                    <th>اسم المحتوى</th>
                                    <th>نوع المحتوى</th>
                                    <th style="width: 10%">اجراء</th>
                                </tr>
                            </thead>

                            {{-- @foreach ($departments as $department) --}}
                                <tbody>
                                    <tr>
                                        <td>12</td>
                                        <td>Contact Us Page</td>
                                        <td>
                                            صفحة
                                            {{-- @if ($department->status == 0)
                                                <div class="badge badge-pill badge badge-success">نشط</div>
                                            @else
                                                <div class="badge badge-pill badge badge-danger">غير نشط</div>
                                            @endif --}}
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                        type="button"id="dropdownMenuButton303" data-toggle="dropdown"
                                                        aria-haspopup="true"aria-expanded="false"></button>
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

                                                        {{-- <li>
                                                            <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $department->id }}">
                                                                <i class="fa fa-trash me-2"></i>حذف
                                                            </a>
                                                        </li> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    <!-- Modal delete -->
                                    {{-- <div class="modal fade text-left" id="modal_DELETE{{ $department->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color: #EA5455 !important;">
                                                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $department->name }}</h4>
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
                                                    <a href="{{ route('department.delete', $department->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <!--end delete-->
                                    </tr>
                                </tbody>
                            {{-- @endforeach --}}
                        </table>

                    </div>
                {{-- @else
                    <div class="alert alert-danger text-xl-center" role="alert">
                        <p class="mb-0">
                            لا توجد اقسام مضافة حتى الان !!
                        </p>
                    </div>
                @endif --}}

                {{-- {{ $shifts->links('pagination::bootstrap-5') }} --}}
            </div>
        </div>
    </div>

@endsection
