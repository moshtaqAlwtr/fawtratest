@extends('master')

@section('title')
قوالب الوحدات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة قوالب الوحدات</h2>
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
                            <a href="{{ route('template_unit.create') }}" class="btn btn-outline-primary">
                                <i class="fa fa-plus me-2"></i>أضف قالب الوحدة
                            </a>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="">بحث بالماركة</label>
                        <select name="category" class="form-control" id="">
                            <option value="">الكل</option>
                            <option value="1">ماركة 1</option>
                            <option value="2">ماركة 2</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if (@isset($template_units) && !@empty($template_units) && count($template_units) > 0)
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>القالب</th>
                        <th>الحالة</th>
                        <th>اجراء</th>
                    </tr>
                </thead>
                @foreach ($template_units as $template_unit)
                <tr>
                    <td>{{ $template_unit->template }} <small class="">({{ $template_unit->base_unit_name }})</small></td>
                    <td>
                        @if ($template_unit->status == 1)
                            <span class="badge badge-pill badge badge-success">نشط</span>
                        @else
                            <span class="badge badge-pill badge badge-danger">معطل</span>
                        @endif
                    </td>
                    <td style="width: 10%">
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm" type="button"id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"aria-expanded="false"></button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('template_unit.show',$template_unit->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('template_unit.edit',$template_unit->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $template_unit->id }}">
                                            <i class="fa fa-trash me-2"></i>حذف
                                        </a>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Modal delete -->
                    <div class="modal fade text-left" id="modal_DELETE{{ $template_unit->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #EA5455 !important;">
                                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $template_unit->name }}</h4>
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
                                    <a href="{{ route('template_unit.delete',$template_unit->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end delete-->
                </tr>
                @endforeach
            </table>

        @else
            <div class="alert alert-danger text-xl-center" role="alert">
                <p class="mb-0">
                    لا توجد قوالب وحدات مضافه حتى الان !!
                </p>
            </div>
        @endif
        {{-- {{ $template_units->links('pagination::bootstrap-5') }} --}}
    </div>

@endsection


@section('scripts')
@endsection
