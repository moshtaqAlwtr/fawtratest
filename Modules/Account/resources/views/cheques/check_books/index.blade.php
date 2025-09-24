@extends('master')

@section('title')
دفاتر الشيكات
@stop

@section('content')


    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">دفاتر الشيكات</h2>
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

        @include('layouts.alerts.success')
        @include('layouts.alerts.error')

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>بحث </div>
                            <div>
                                <a href="{{ route('check_books.create') }}" class="btn btn-outline-primary">
                                    <i class="fa fa-plus me-2"></i>أضف دفتر الشيكات
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form class="form" method="GET" action="#">
                        <div class="form-body row">
                            <div class="form-group col-md-4">
                                <label for="">البحث بواسطة الموظف</label>
                                <input type="text" class="form-control" placeholder="ادخل الإسم او الكود"name="keywords">
                            </div>
                            <div class="form-group col-4">
                                    <label for="">من تاريخ</label>
                                    <input type="date" class="form-control" name="from_date">
                            </div>
                            <div class="form-group col-4">
                                <label for="">الي تاريخ</label>
                                <input type="date" class="form-control"  name="to_date">
                            </div>
                        </div>
                        <!-- Hidden Div -->
                        <div class="collapse" id="advancedSearchForm">
                            <div class="form-body row">
                                <div class="form-group col-4">
                                    <label for="">جميع الحالات</label>
                                    <input type="text" class="form-control" name="product_code">
                                </div>
                                <div class="col-md-4">
                                    <label for="searchDepartment" class="form-label">أختر قسم</label>
                                    <input type="text" class="form-control" id="searchDepartment" placeholder="البحث بواسطة القسم">
                                </div>
                                <div class="col-md-4">
                                    <label for="searchDepartment" class="form-label">أختر فرع</label>
                                    <input type="text" class="form-control" id="searchDepartment" placeholder="البحث بواسطة الفرع">
                                </div>
                            </div>

                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                            <a class="btn btn-outline-secondary ml-2 mr-2" data-toggle="collapse" data-target="#advancedSearchForm">
                                <i class="bi bi-sliders"></i> بحث متقدم
                            </a>
                            <a href="#" class="btn btn-outline-danger waves-effect waves-light">الغاء الفلترة</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive">
                        @if(isset($check_books) && !@empty($check_books) && $check_books->count() > 0)
                            <table class="table table-striped" dir="rtl">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">رقم دفتر الشيكات</th>
                                        <th scope="col">اسم البنك</th>
                                        <th scope="col">الشيكات المستخدمه</th>
                                        <th scope="col">الحالة</th>
                                        <th scope="col">اجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($check_books as $check_book)
                                        <tr>
                                            <td>{{ $check_book->cheque_book_number }}</td>
                                            <td>{{ $check_book->bank->bank_name }}</td>
                                            <td>{{ $check_book->end_serial_number - $check_book->start_serial_number + 1 }}/0</td>
                                            <td>
                                                @if($check_book->status == 0)
                                                    <span class="mr-1 bullet bullet-success bullet-sm"></span><span class="mail-date">نشط</span>
                                                @else
                                                    <span class="mr-1 bullet bullet-danger bullet-sm"></span><span class="mail-date">غير نشط</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <div class="dropdown">
                                                        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm" type="button"id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"aria-expanded="false"></button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('check_books.show', $check_book->id) }}">
                                                                    <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('check_books.edit', $check_book->id) }}">
                                                                    <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $check_book->id }}">
                                                                    <i class="fa fa-trash me-2"></i>حذف
                                                                </a>
                                                            </li>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Modal delete -->
                                            <div class="modal fade text-left" id="modal_DELETE{{ $check_book->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="background-color: #EA5455 !important;">
                                                            <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف دفتر الشيكات</h4>
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
                                                            <a href="{{ route('check_books.delete',$check_book->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
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
                                    لا توجد دفاتر شيكات مضافة حتى الان !!
                                </p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
@endsection
