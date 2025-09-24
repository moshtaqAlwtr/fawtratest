@extends('master')

@section('title')
    أدارة الورديات
@stop

@section('content')       
<div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أدارة الورديات  </h2>
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
                            <div>بحث في الورديات</div>
                            <div>
                                <a href="{{ route('add_shift') }}" class="btn btn-outline-success">
                                    <i class="fa fa-plus me-2"></i>أضافة وردية
                                </a>

                           
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form class="form">
                        <div class="form-body row">
                            <div class="form-group col-md-8">
                                <select name="" class="form-control" id="">
                                    <option value="">أبحث بواسطة أسم الوردية </option>
                                    <a class="dropdown-item" href="#">Option 1</a>
                                </select>
                            </div>


                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>

                            <button type="reset"
                                class="btn btn-outline-warning waves-effect waves-light">ألغاء</button>
                        </div>
                    </form>

                </div>

            </div>

        </div>
<div class="card">
    <div class="card-body">
        <!-- عناوين الأعمدة -->
        <div class="row border-bottom py-2 bg-light font-weight-bold text-center">
            <div class="col-md-4">الاسم</div>
            <div class="col-md-4">أيام العطلات</div>
            <div class="col-md-4">الإجراءات</div>
        </div>

        <!-- بيانات الصف الأول -->
        <div class="row py-2 text-center">
            <div class="col-md-4">محمد أحمد</div>
            <div class="col-md-4">الجمعة</div>
            <div class="col-md-4">
                <div class="dropdown">
                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"
                        id="dropdownMenuButton1" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <a class="dropdown-item" href="#"><i class="fa fa-eye me-2 text-primary"></i>عرض</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-edit me-2 text-success"></i>تعديل</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-print me-2 text-dark"></i>تعطيل</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-envelope me-2 text-warning"></i>إرسال بيانات الدخول</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-credit-card me-2 text-info"></i>تغيير كلمة المرور</a>
                        <a class="dropdown-item text-danger" href="#"><i class="fa fa-trash me-2"></i>حذف</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- بيانات الصف الثاني -->
        <div class="row py-2 text-center">
            <div class="col-md-4">أحمد علي</div>
            <div class="col-md-4">الأحد</div>
            <div class="col-md-4">
                <div class="dropdown">
                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"
                        id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                        <a class="dropdown-item" href="#"><i class="fa fa-eye me-2 text-primary"></i>عرض</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-edit me-2 text-success"></i>تعديل</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-print me-2 text-dark"></i>تعطيل</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-envelope me-2 text-warning"></i>إرسال بيانات الدخول</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-credit-card me-2 text-info"></i>تغيير كلمة المرور</a>
                        <a class="dropdown-item text-danger" href="#"><i class="fa fa-trash me-2"></i>حذف</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- بيانات الصف الثالث -->
        <div class="row py-2 text-center">
            <div class="col-md-4">سارة محمود</div>
            <div class="col-md-4">السبت</div>
            <div class="col-md-4">
                <div class="dropdown">
                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"
                        id="dropdownMenuButton3" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                        <a class="dropdown-item" href="#"><i class="fa fa-eye me-2 text-primary"></i>عرض</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-edit me-2 text-success"></i>تعديل</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-print me-2 text-dark"></i>تعطيل</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-envelope me-2 text-warning"></i>إرسال بيانات الدخول</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-credit-card me-2 text-info"></i>تغيير كلمة المرور</a>
                        <a class="dropdown-item text-danger" href="#"><i class="fa fa-trash me-2"></i>حذف</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    
        @endsection