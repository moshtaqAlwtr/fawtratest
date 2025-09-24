@extends('master')

@section('title')
الضرائب الافتراضيه
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الضرائب الافتراضيه  </h2>
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
                        <a href="{{ route('storehouse.index') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fa fa-ban"></i>الغاء
                        </a>
                        <button type="submit" form="products_form" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-save"></i>حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-header">إعدادات الضرائب</div>
            <div class="card-body">
                <div class="row">


                    <div class="form-group col-md-6">
                        <label for="">الضريبة 1</label>
                        <select name="category" class="form-control select2" id="">
                            <option value="">اختار اسم الضريبه</option>
                            <option value="1">القيمة المضافة</option>
                            <option value="2">صفريه</option>
                            <option value="3">قيمة مضافة</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="">الضريبة 2</label>
                        <select name="category" class="form-control select2" id="">
                            <option value="">اختار اسم الضريبه</option>
                            <option value="1">القيمة المضافة</option>
                            <option value="2">صفريه</option>
                            <option value="3">قيمة مضافة</option>
                        </select>
                    </div>

                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                    <a href="" class="btn btn-outline-danger waves-effect waves-light">الغاء الفلترة</a>
                </div>

            </div>
        </div>

        {{-- @if (@isset($price_lists) && !@empty($price_lists) && count($price_lists) > 0) --}}
            <div class="card">
                <div class="card-header"></div>
                <table class="table">
                    {{-- @foreach ($price_lists as $price_list) --}}
                    <tr class="d-flex justify-content-between">
                        <td>
                            منتج
                            {{-- @if ($price_list->status == 0)
                                <span class="badge badge-pill badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-pill badge badge-danger">معطل</span>
                            @endif --}}
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"aria-expanded="false"></button>
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
                                            <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $price_list->id }}">
                                                <i class="fa fa-trash me-2"></i>حذف
                                            </a>
                                        </li> --}}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Modal delete -->
                        {{-- <div class="modal fade text-left" id="modal_DELETE{{ $price_list->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #EA5455 !important;">
                                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $price_list->name }}</h4>
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
                                        <a href="" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <!--end delete-->
                    </tr>
                    {{-- @endforeach --}}
                </table>
            </div>

        {{-- @else
            <div class="alert alert-danger text-xl-center" role="alert">
                <p class="mb-0">
                    لا توجد قوائم اسعار مضافه حتى الان !!
                </p>
            </div>
        @endif
        {{ $price_lists->links('pagination::bootstrap-5') }} --}}
    </div>

@endsection


@section('scripts')



@endsection
