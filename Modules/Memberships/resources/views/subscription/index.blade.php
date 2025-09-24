@extends('master')

@section('title')
    أدارة الأشتراكات
@stop

@section('content')
    <div style="font-size: 1.1rem;">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0"> أدارة الأشتراكات </h2>
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


            <div class="container mt-4">
                <div class="card p-3">
                    <h5>تصفية البيانات</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="بحث بواسطة العميل">
                        </div>
                        <div class="col-md-6">
                            <select class="form-control">
                                <option selected>الباقات</option>
                                <option>الباقة البروزية</option>
                                <option>الباقة الفضية</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>تاريخ البدء من</label>
                            <input type="date" class="form-control" placeholder="تاريخ البدء - من">
                        </div>
                        <div class="col-md-3">
                            <label>تاريخ البدء إلى</label>
                            <input type="date" class="form-control" placeholder="تاريخ البدء - إلى">
                        </div>
                        <div class="col-md-3">
                            <label>تاريخ الانتهاء من</label>
                            <input type="date" class="form-control" placeholder="تاريخ الانتهاء - من">
                        </div>
                        <div class="col-md-3">
                            <label>تاريخ الانتهاء إلى</label>
                            <input type="date" class="form-control" placeholder="تاريخ الانتهاء - إلى">
                        </div>
                    </div>
                    <div class="row g-2 mt-3 text-start">
                        <div class="col-md-2 ms-auto">
                            <button class="btn btn-primary w-100">بحث</button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-secondary w-100">إلغاء الفلتر</button>
                        </div>
                    </div>
                </div>
        
                <div class="card p-3 mt-4">
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>المعرف</th>
                                <th>بيانات العميل</th>
                                <th>باقة</th>
                                <th>تاريخ البدء</th>
                                <th>تاريخ الانتهاء</th>
                                <th>رقم الفاتورة</th>
                                <th>المبلغ</th>
                                <th>ترتيب بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>POS Client #2</td>
                                <td>الباقة البروزية</td>
                                <td>15/02/2025</td>
                                <td>14/05/2025</td>
                                <td>#00012</td>
                                <td>300.00 ر.س</td>
                                <td>
                                    <div class="btn-group">
                                        <div class="dropdown">
                                            <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                type="button"id="dropdownMenuButton303" data-toggle="dropdown"
                                                aria-haspopup="true"aria-expanded="false"></button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('Memberships.show', 1) }}">
                                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('Memberships.edit', 1) }}">
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
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
   
     
        
        @endsection