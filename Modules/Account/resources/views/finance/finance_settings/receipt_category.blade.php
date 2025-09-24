@extends('master')

@section('title')
تصنيفات الايرادات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تصنيفات الايرادات</h2>
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
                        <a href="#" class="btn btn-outline-primary"  data-toggle="modal" data-target="#modal_Create">
                            <i class="fa fa-plus"></i>تصنيف جديد
                        </a>
                    </div>

                </div>
            </div>
        </div>

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-body">
                @if (@isset($receipt_categories) && !@empty($receipt_categories) && count($receipt_categories) > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الحاله</th>
                            <th>اجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($receipt_categories as $index=>$receipt_category)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $receipt_category->name }}</td>
                        <td>
                            @if ($receipt_category->status == 0)
                                <div class="badge badge-pill badge badge-success">نشط</div>
                            @else
                                <div class="badge badge-pill badge badge-danger">معطل</div>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"aria-expanded="false"></button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                        <li>
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal_Update{{ $receipt_category->id }}">
                                                <i class="fa fa-edit me-2 text-success"></i>تعديل
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $receipt_category->id }}">
                                                <i class="fa fa-trash me-2"></i>حذف
                                            </a>
                                        </li>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Modal Update -->
                        <div class="modal fade text-left" id="modal_Update{{ $receipt_category->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel1">تعديل تصنيف ايرادات</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="form" action="{{ route('finance_settings.receipt_category_update',$receipt_category->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-label-group">
                                                            <input type="text" class="form-control" placeholder="الاسم" name="name" value="{{ old('name',$receipt_category->name) }}">
                                                            <label for="first-name-floating">الاسم</label>
                                                            @error('name')
                                                            <span class="text-danger" id="basic-default-name-error" class="error">
                                                                {{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-label-group">
                                                            <textarea name="description" class="form-control" placeholder="الوصف" rows="2">{{ old('description',$receipt_category->description) }}</textarea>
                                                            <label for="first-name-floating">الوصف</label>
                                                            @error('description')
                                                            <span class="text-danger" id="basic-default-name-error" class="error">
                                                                {{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-label-group">
                                                            <fieldset>
                                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                                    <input type="checkbox" name="status" {{ $receipt_category->status == 1 ? 'checked' : '' }}>
                                                                    <span class="vs-checkbox">
                                                                        <span class="vs-checkbox--check">
                                                                            <i class="vs-icon feather icon-check"></i>
                                                                        </span>
                                                                    </span>
                                                                    <span class="">تعطيل</span>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-outline-primary btn-sm round mr-1 mb-1 waves-effect waves-light">تحديث</button>
                                            <button type="reset" class="btn btn-outline-warning btn-sm round mr-1 mb-1 waves-effect waves-light">تفريغ</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!--end Model-->

                        <!-- Modal delete -->
                        <div class="modal fade text-left" id="modal_DELETE{{ $receipt_category->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #EA5455 !important;">
                                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $receipt_category->name }}</h4>
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
                                        <a href="{{ route('finance_settings.receipt_category_delete',$receipt_category->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
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
                            لا توجد تصنيفات ايرادات مضافة حتى الان !
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Modal Create -->
    <div class="modal fade text-left" id="modal_Create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">اضافة تصنيف ايرادات</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form" action="{{ route('finance_settings.receipt_category_store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-label-group">
                                        <input type="text" class="form-control" placeholder="الاسم" name="name" value="{{ old('name') }}">
                                        <label for="first-name-floating">الاسم</label>
                                        @error('name')
                                        <span class="text-danger" id="basic-default-name-error" class="error">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-label-group">
                                        <textarea name="description" class="form-control" placeholder="الوصف" rows="2">{{ old('description') }}</textarea>
                                        <label for="first-name-floating">الوصف</label>
                                        @error('description')
                                        <span class="text-danger" id="basic-default-name-error" class="error">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-label-group">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" name="status">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">تعطيل</span>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-primary btn-sm round mr-1 mb-1 waves-effect waves-light">اضافة</button>
                        <button type="reset" class="btn btn-outline-warning btn-sm round mr-1 mb-1 waves-effect waves-light">تفريغ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end Model-->


@endsection


@section('scripts')
@endsection
