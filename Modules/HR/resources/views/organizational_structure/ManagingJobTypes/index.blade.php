@extends('master')

@section('title')
    انواع الوظائف
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">انواع الوظائف</h2>
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
                            <a href="{{ route('ManagingJobTypes.create') }}"
                                class="btn btn-outline-success">
                                <i class="fa fa-plus me-2"></i> اضافة نوع وظيفة
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
                        <h4 class="card-title">بحث</h4>
                    </div>

                    <div class="card-body">
                        <form class="form">
                            <div class="form-body row">

                                <div class="form-group col-md-8">
                                    <label for="feedback2" class="sr-only"> نوع وظيفي</label>
                                    <input type="email" id="feedback2" class="form-control"
                                        placeholder="البحث بواسطة نوع  وظيفي " name="email">
                                </div>

                                <div class="form-group col-md-4">
                                    <select id="feedback2" class="form-control">
                                        <option value="">جميع الحالات</option>
                                        <option value="1">نشط </option>
                                        <option value="0">غير نشط</option>
                                    </select>
                                </div>

                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>


                                <button type="reset" class="btn btn-outline-warning waves-effect waves-light">الغاء الفلتر
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    @if (@isset($types) && !@empty($types) && count($types) > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>عدد الموظفين</th>
                                    <th>الحالة</th>
                                    <th style="width: 10%">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($types as $type)
                                <tr>
                                    <td>{{ $type->name }}</td>
                                    <td>{{ $type->employees->where('employee_type',1)->count() }}</td>
                                    <td>
                                        @if ($type->status == 0)
                                            <div class="badge badge-pill badge badge-success">نشط</div>
                                        @else
                                            <div class="badge badge-pill badge badge-danger">غير نشط</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                    type="button"id="dropdownMenuButton303" data-toggle="dropdown"
                                                    aria-haspopup="true"aria-expanded="false"></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('ManagingJobTypes.show',$type->id) }}">
                                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('ManagingJobTypes.edit',$type->id) }}">
                                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $type->id }}">
                                                            <i class="fa fa-trash me-2"></i>حذف
                                                        </a>
                                                    </li>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Modal delete -->
                                    <div class="modal fade text-left" id="modal_DELETE{{ $type->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="background-color: #EA5455 !important;">
                                                            <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $type->name }}</h4>
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
                                                            <a href="{{ route('ManagingJobTypes.delete', $type->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
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
                                لا توجد نوع وظيفي مضافة حتى الان !!
                            </p>
                        </div>
                    @endif

                    {{-- {{ $types->links('pagination::bootstrap-5') }} --}}
                </div>
            </div>

        </div>
    </div>

@endsection
