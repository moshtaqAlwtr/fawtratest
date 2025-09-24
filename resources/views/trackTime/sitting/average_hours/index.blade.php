@extends('master')

@section('title')
    مدة الساعة للموظفين
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> مدة الساعة للموظفين</h2>
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
                        <div>
                            <label> مدة الساعة <span style="color: red"></span> </label>
                        </div>

                        <div>

                            <a href="{{ route('AverageHours.create') }}" class="btn btn-outline-primary">
                                <i class="fa fa-save"></i>اضف معدل ساعة
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

                                <div class="form-group col-md-4">
                                    <label for="feedback2" class=""> بحث بواسطة الموضف </label>
                                    <input type="email" id="feedback2" class="form-control" placeholder=" الاسم   "
                                        name="email">

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

                                <th> الموظف</th>
                                <th>معدل الساعة</th>
                                <th class="text-end">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>


                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>#1</span>
                                        <small class="text-muted">بواسطة: عدنان العوقلي</small>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-success">10.0</span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <div class="dropdown">
                                            <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                type="button"id="dropdownMenuButton303" data-toggle="dropdown"
                                                aria-haspopup="true"aria-expanded="false"></button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">

                                                <li>
                                                    <a class="dropdown-item" href="{{ route('AverageHours.edit', 1) }}">
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
                                {{-- <div class="modal fade text-left" id="modal_DELETE{{ $shift->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #EA5455 !important;">
                                                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $shift->name }}</h4>
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

                        </tbody>
                    </table>


                    <div class="alert alert-danger text-xl-center" role="alert">
                        <p class="mb-0">
                            لا توجد مدة ساعة مضافة حتى الان !!
                        </p>
                    </div>

                    {{-- {{ $shifts->links('pagination::bootstrap-5') }} --}}
                </div>
            </div>

        </div>
    </div>
    </div>
@endsection
