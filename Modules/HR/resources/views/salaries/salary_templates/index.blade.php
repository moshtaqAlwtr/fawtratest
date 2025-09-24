@extends('master')

@section('title')
    قالب الراتب
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <strong>{{ session('success') }}</strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">قالب الراتب</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">


                    <div class="d-flex align-items-center gap-3">
                        <div class="btn-group">
                            <button class="btn btn-light border">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                            <button class="btn btn-light border">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                        </div>
                        <span class="mx-2">1 - 1 من 1</span>
                        <div class="input-group" style="width: 150px">
                            <input type="text" class="form-control text-center" value="صفحة 1 من 1">
                        </div>


                    </div>
                    <div class="d-flex" style="gap: 15px">
                        <a href="{{ route('SalaryTemplates.create') }}" class="btn btn-success">
                            <i class="fa fa-plus me-2"></i>
                            أضف قالب الراتب
                        </a>

                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <h4 class="card-title">بحث</h4>
                </div>

                <div class="card-body">
                    <form class="form" method="GET" action="{{ route('SalaryTemplates.index') }}">
                        <div class="form-body row">
                            <div class="form-group col-md-8">
                                <label for="template_name">البحث بواسطة اسم قالب الراتب</label>
                                <input type="text" id="template_name" class="form-control"
                                    placeholder="البحث بواسطة اسم قالب الراتب" name="name" value="{{ request('name') }}">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="item_search">البحث بواسطة البنود</label>
                                <input type="text" id="item_search" class="form-control"
                                    placeholder="البحث بواسطة البنود" name="item_search"
                                    value="{{ request('item_search') }}">
                            </div>

                            <div class="form-group col-md-4">
                                <label>نوع البند</label>
                                <select class="form-control" name="type">
                                    @foreach ($types as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ request('type') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">
                                <i class="fa fa-search me-2"></i>
                                بحث
                            </button>

                            <a href="{{ route('SalaryTemplates.index') }}"
                                class="btn btn-outline-warning waves-effect waves-light">
                                <i class="fa fa-times me-2"></i>
                                الغاء الفلتر
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-body">
                @if (isset($SalaryTemplates) && $SalaryTemplates->count() > 0)



                    <table class="table">
                        <thead>
                            <tr>

                                <th>اسم </th>


                                <th> الحالة</th>
                                <th style="width: 10%">الترتيب</th>
                            </tr>
                        </thead>
                        @foreach ($SalaryTemplates as $temp)
                            <tbody>
                                <tr>
                                    <td>{{ $temp->name }}</td>
                                    <td>
                                        @if ($temp->status == 1)
                                            <span class="badge badge-success">نشط</span>
                                        @else
                                            <span class="badge badge-danger">غير نشط</span>
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
                                                            href="{{ route('SalaryTemplates.show', 1) }}">
                                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('SalaryTemplates.edit', 1) }}">
                                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            data-toggle="modal" data-target="#modal_DELETE">
                                                            <i class="fa fa-trash me-2"></i>حذف
                                                        </a>
                                                    </li>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Modal delete -->
                                    <div class="modal fade text-left" id="modal_DELETE{{ $temp->id }}"
                                        tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color: #EA5455 !important;">
                                                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف
                                                        {{ $temp->name }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true" style="color: #DC3545">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <strong>
                                                        هل انت متاكد من انك تريد الحذف ؟
                                                    </strong>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light waves-effect waves-light"
                                                        data-dismiss="modal">الغاء</button>
                                                    <a href="{{ route('SalaryTemplates.destroy', $temp->id) }}"
                                                        class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end delete-->
                                </tr>

                            </tbody>
                        @endforeach
                    </table>
                @else
                    <div class="alert alert-danger text-xl-center" role="alert">
                        <p class="mb-0">
                            لا توجد مسميات وظيفية مضافة حتى الان !!
                        </p>
                    </div>
                @endif
            </div>
        </div>




    @endsection
