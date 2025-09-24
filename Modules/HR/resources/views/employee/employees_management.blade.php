@extends('master')

@section('title')
    أدارة أدوار الموظفين
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أدارة أداوار الموظفين</h2>
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
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <!-- زر "فاتورة جديدة" -->
                    <div class="form-group col-outdo">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                    </div>
                    <div class="btn-group">
                        <div class="dropdown">
                            <button class="btn  dropdown-toggle mr-1 mb-1" type="button" id="dropdownMenuButton302"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton302">
                                <a class="dropdown-item" href="#">Option 1</a>
                                <a class="dropdown-item" href="#">Option 2</a>
                                <a class="dropdown-item" href="#">Option 3</a>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group col-md-5">
                        <div class="dropdown">
                            <button class="btn bg-gradient-info dropdown-toggle mr-1 mb-1" type="button"
                                id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                الاجراءات
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                <a class="dropdown-item" href="#">Option 1</a>
                                <a class="dropdown-item" href="#">Option 2</a>
                                <a class="dropdown-item" href="#">Option 3</a>
                            </div>
                        </div>
                    </div>
                    <!-- مربع اختيار -->

                    <!-- الجزء الخاص بالتصفح -->
                    <div class="d-flex align-items-center">
                        <!-- زر الصفحة السابقة -->
                        <button class="btn btn-outline-secondary btn-sm" aria-label="الصفحة السابقة">
                            <i class="fa fa-angle-right"></i>
                        </button>

                        <!-- أرقام الصفحات -->
                        <nav class="mx-2">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item active"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">4</a></li>
                                <li class="page-item"><a class="page-link" href="#">5</a></li>
                            </ul>
                        </nav>

                        <!-- زر الصفحة التالية -->
                        <button class="btn btn-outline-secondary btn-sm" aria-label="الصفحة التالية">
                            <i class="fa fa-angle-left"></i>
                        </button>
                    </div>

                    <!-- قائمة الإجراءات -->

                    <a href="{{ route('employee.employee_role_management.add_new_role') }}" class="btn btn-success btn-sm d-flex align-items-center ">
                        <i class="fa fa-plus me-2"></i>دور جديد
                    </a>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">

                    <!-- جدول عرض الأدوار -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center table-striped">
                            <thead>
                                <tr>
                                    <th scope="col"><input type="checkbox" class="form-check-input"></th>
                                    <th scope="col">المعرف</th>
                                    <th scope="col">الدور الوظيفي</th>
                                    <th scope="col">النوع</th>
                                    <th scope="col">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- البيانات الثابتة -->
                                <tr>
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td>1</td>
                                    <td>Manager</td>
                                    <td>مستخدم</td>
                                    <td>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-pencil-square"></i> تعديل
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-files"></i> نسخ
                                        </button>
                                        <button class="btn btn-outline-warning btn-sm">
                                            <i class="bi bi-lock"></i> صفحات محظورة
                                        </button>
                                        <button class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td>2</td>
                                    <td>Staff</td>
                                    <td>مستخدم</td>
                                    <td>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-pencil-square"></i> تعديل
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-files"></i> نسخ
                                        </button>
                                        <button class="btn btn-outline-warning btn-sm">
                                            <i class="bi bi-lock"></i> صفحات محظورة
                                        </button>
                                        <button class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td>3</td>
                                    <td>الإمام</td>
                                    <td>مستخدم</td>
                                    <td>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-pencil-square"></i> تعديل
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-files"></i> نسخ
                                        </button>
                                        <button class="btn btn-outline-warning btn-sm">
                                            <i class="bi bi-lock"></i> صفحات محظورة
                                        </button>
                                        <button class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button class="btn btn-danger">
                        <i class="bi bi-trash"></i> حذف للمحدد
                    </button>
                </div>

                <!-- رسالة نجاح عند تنفيذ الإجراء -->
                <div class="alert alert-success mt-3" style="display: none;">
                    تم تنفيذ الإجراء بنجاح.
                </div>
            </div>
        </div>

    </div>

@endsection
