@extends('master')

@section('title')
    النماذج ذات الصلة
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">النماذج ذات الصلة</h2>
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
                        <a href="{{ route('RelatedModels.create') }}" class="btn btn-success">
                            <i class="fa fa-plus me-2"></i>
                            أضف نموذج ذات صلة
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
                    <form class="form">
                        <div class="form-body row">
                            <div class="form-group col-md-8">
                                <label for="feedback1" class="">البحث بواسطة اسم النموذج الذات الصلة </label>
                                <input type="text" id="feedback1" class="form-control"
                                    placeholder=" البحث  بواسطة اسم النموذج الذات الصلة" name="name">
                            </div>



                            <div class="form-group col-md-4">
                                <label for="feedback2" class=""> جميع الحالات </label>
                                <select id="feedback2" class="form-control">
                                    <option value="">جميع الحالات</option>

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

                <table class="table">
                    <thead>
                        <tr>

                            <th>العنوان </th>

                            <th> الحالة</th>
                            <th style="width: 10%">الترتيب</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>

                            <td>اب </td>
                            <td>نشط</td>

                            <td>
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                            type="button"id="dropdownMenuButton303" data-toggle="dropdown"
                                            aria-haspopup="true"aria-expanded="false"></button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                            <li>
                                                <a class="dropdown-item" href="">
                                                    <i class="fa fa-book-open me-2 text-success"></i>اداة السجلات
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="">
                                                    <i class="fa fa-pencil-ruler me-2 text-warning"></i>مصمم النماذج
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="">
                                                    <i class="fa fa-print me-2 text-danger"></i>ادارة قوالب الطباعة
                                                </a>
                                            </li>

                                            <li>
                                                <a class="dropdown-item" href="{{ route('SalaryItems.edit', 1) }}">
                                                    <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                </a>
                                            </li>

                                            <li>
                                                <a class="dropdown-item text-danger" href="#" data-toggle="modal"
                                                    data-target="#modal_DELETE">
                                                    <i class="fa fa-trash me-2"></i>حذف
                                                </a>
                                            </li>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Modal delete -->
                            {{-- <div class="modal fade text-left" id="modal_DELETE{{ $title->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #EA5455 !important;">
                                                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $title->name }}</h4>
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
                                                        <a href="{{ route('JobTitles.delete', $title->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                            <!--end delete-->
                        </tr>

                    </tbody>
                </table>
                {{-- @else
                        <div class="alert alert-danger text-xl-center" role="alert">
                            <p class="mb-0">
                                لا توجد مسميات وظيفية مضافة حتى الان !!
                            </p>
                        </div>
                    @endif --}}

            </div>
        </div>




    @endsection
