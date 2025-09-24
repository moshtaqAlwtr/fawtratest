@extends('master')

@section('title')
سجل جلسات الحضور
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">سجل جلسات الحضور</h2>
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

<div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-title">

                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>بحث </div>
                            <div>
                                <a href="{{ route('products.create') }}" class="btn btn-outline-success">
                                    <i class="fa fa-plus me-2"></i>سجل حضور الموظفين
                                </a>


                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form class="form" method="GET" action="{{ route('products.search') }}">
                        <div class="form-body row">
                            <div class="form-group col-md-6">
                                <label for="">الحالة</label>
                                <input type="text" class="form-control" placeholder="البحث بواسطة الحالة"name="keywords">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">المصدر</label>
                                <input type="text" class="form-control" placeholder="البحث بواسطة المصدر"name="keywords">
                            </div>
</div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-danger waves-effect waves-light">الغاء الفلترة</a>
                        </div>
                    </form>

                </div>

            </div>
            <div class="card mt-4">
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered" dir="rtl">
            <thead class="table-light">
                <tr>
                    <th scope="col">المعرف </th>
                    <th scope="col">وقت الفتح</th>
                    <th scope="col">وقت الاغلاق</th>
                    <th scope="col">المصدر</th>
                    <th scope="col">عدد التسجيلات</th>
                    <th scope="col">الحالة</th>
                    <th scope="col">ترتيب بواسطة</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#1</td>
                    <td> 	05/12/2023 12:20</td>
                    <td> 	05/12/2023 12:20</td>
                    <td> محمد العتيبي</td>
                    <td> 1</td>
                    <td><span class="badge bg-green text-dark">مفتوح  </span></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn bg-gradient-info fa fa-ellipsis-v" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#"><i class="fa fa-eye me-2 text-primary"></i>عرض</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-edit text-primary me-2"></i>تعديل</a>
                                <div class="dropdown-divider"></div>
                                <button class="dropdown-item text-danger delete-client">
                                    <i class="fas fa-trash me-2"></i>حذف
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>#1</td>
                    <td> 	05/12/2023 12:20</td>
                    <td> 	05/12/2023 12:20</td>
                    <td> محمد العتيبي</td>
                    <td> 1</td>
                    <td><span class="badge bg-green text-dark">مفتوح  </span></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn bg-gradient-info fa fa-ellipsis-v" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#"><i class="fa fa-eye me-2 text-primary"></i>عرض</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-edit text-primary me-2"></i>تعديل</a>
                                <div class="dropdown-divider"></div>
                                <button class="dropdown-item text-danger delete-client">
                                    <i class="fas fa-trash me-2"></i>حذف
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>#1</td>
                    <td> 	05/12/2023 12:20</td>
                    <td> 	05/12/2023 12:20</td>
                    <td> محمد العتيبي</td>
                    <td> 1</td>
                    <td><span class="badge bg-green text-dark">مفتوح  </span></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn bg-gradient-info fa fa-ellipsis-v" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#"><i class="fa fa-eye me-2 text-primary"></i>عرض</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-edit text-primary me-2"></i>تعديل</a>
                                <div class="dropdown-divider"></div>
                                <button class="dropdown-item text-danger delete-client">
                                    <i class="fas fa-trash me-2"></i>حذف
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                <!-- أضف المزيد من الصفوف حسب الحاجة -->
            </tbody>
        </table>
    </div>
</div>

        </div>

@endsection
