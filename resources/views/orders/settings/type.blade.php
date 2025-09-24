@extends('master')

@section('title')
    أنواع الطلبات
@stop

@section('content')

    <div class="card">

    </div>
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> أنواع الطلبات</h2>
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
  <!-- بطاقة البحث -->
       <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="card-title">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>بحث </div>
                        <div>
                            <a href="{{ route('orders.Settings.create') }}" class="btn btn-outline-success">
                                <i class="fa fa-plus me-2"></i>أضف نوع الطلب
                            </a>
                        </div>
                    </div>
                </div>
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="device" class="form-label">أسم نوع الطلب أو المعرف</label>
                            <select id="device" class="form-control">
                                <option>أي </option>
                            </select>
                        </div>
                   
                        <div class="col-md-6">
                            <label for="status" class="form-label">الحالة</label>
                            <select id="status" class="form-control">
                                <option>جميع الحالات </option>
                            </select>
                        </div>
                      
                    </div>
                    <div class="d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary me-2">بحث</button>
                        <button type="reset" class="btn btn-secondary">إلغاء الفلتر</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <table class="table table-bordered text-center">
        <thead class="thead-light">
            <tr>
                <th>الاسم</th>
                <th>الحالة</th>
                <th>ترتيب بواسطة</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>طلب مشتريات #2</td>
                <td><span class="text-success">● نشط</span></td>
                <td>
                    <div class="btn-group">
                        <div class="dropdown">
                            <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                type="button"id="dropdownMenuButton303" data-toggle="dropdown"
                                aria-haspopup="true"aria-expanded="false"></button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                    <li>
                                        <a class="dropdown-item" href="">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="">
                                            <i class="fa fa-pencil-alt me-2 text-success"></i>تعديل
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item text-danger" href="#">
                                            <i class="fa fa-trash-alt me-2"></i>حذف
                                        </a>
                                    </li>
                                  
                                </div>
                        </div>
                    </div>
                </td>
            </tr>
       
        </tbody>
    </table>
    
    @endsection