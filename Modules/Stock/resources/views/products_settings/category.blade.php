@extends('master')

@section('title')
التصنيفات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">التصنيفات</h2>
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

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-content">
                <div class="card-title p-2">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>بحث</div>
                        <div>
                            <a href="{{ route('category.create') }}" class="btn btn-outline-primary">
                                <i class="fa fa-plus me-2"></i>تصنيف جديد
                            </a>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="">بحث بالاسم</label>
                        <select name="category" class="form-control select2" id="">
                            <option value="">الكل</option>
                            {{-- @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="">بحث بالتصنيف</label>
                        <select name="category" class="form-control select2" id="">
                            <option value="">الكل</option>
                            <option value="1">تصنيف 1</option>
                            <option value="2">تصنيف 2</option>
                        </select>
                    </div>

                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                    <a href="{{ route('product_settings.category') }}" class="btn btn-outline-danger waves-effect waves-light">الغاء الفلترة</a>
                </div>

            </div>
        </div>

        @if (@isset($categories) && !@empty($categories) && count($categories) > 0)
        @foreach ($categories as $category)
            <div class="card">
                <table class="table">
                    <tr class="d-flex justify-content-between">
                        <td>
                            <p><strong>{{ $category->name }}</strong></p>
                            <small>{{ $category->discretion }}</small>
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"aria-expanded="false"></button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('category.edit',$category->id) }}">
                                                <i class="fa fa-edit me-2 text-success"></i>تعديل
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $category->id }}">
                                                <i class="fa fa-trash me-2"></i>حذف
                                            </a>
                                        </li>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Modal delete -->
                        <div class="modal fade text-left" id="modal_DELETE{{ $category->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #EA5455 !important;">
                                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $category->name }}</h4>
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
                                        <a href="{{ route('category.delete',$category->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end delete-->
                    </tr>
                </table>
            </div>
            @endforeach

        @else
            <div class="alert alert-danger text-xl-center" role="alert">
                <p class="mb-0">
                    لا توجد تصنيفات مضافه حتى الان !!
                </p>
            </div>
        @endif
        {{ $categories->links('pagination::bootstrap-5') }}
    </div>

@endsection


@section('scripts')



@endsection
