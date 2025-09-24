@extends('master')

@section('title')
ادارة الباقات
@stop

@section('content')
    <div style="font-size: 1.2rem;">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0" style="font-size: 1.2rem;"> ادارة الباقات </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" style="font-size: 1.2rem;">الرئيسية</a></li>
                                <li class="breadcrumb-item active" style="font-size: 1.2rem;">عرض</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.alerts.success')
        @include('layouts.alerts.error')

        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">


                        <div class="d-flex align-items-center gap-3">
                            <div class="btn-group">
                                <button class="btn btn-light border" style="font-size: 1.2rem;">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                                <button class="btn btn-light border" style="font-size: 1.2rem;">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                            </div>
                            <span class="mx-2" style="font-size: 1.2rem;">1 - 1 من 1</span>
                            <div class="input-group" style="width: 150px">
                                <input type="text" class="form-control text-center" value="صفحة 1 من 1" style="font-size: 1.2rem;">
                            </div>

                        </div>
                        <div class="d-flex" style="gap: 15px">
                            <a href="{{ route('PackageManagement.create') }}" class="btn btn-success" style="font-size: 1.2rem;">
                                <i class="fa fa-plus me-2"></i>
                                اضافة  باقة
                            </a>
                            <a href="" class="btn btn-tumblr" style="font-size: 1.2rem;">
                                <i class="fa fa-plus me-2"></i>
                                طلب   باقة
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <h4 class="card-title" style="font-size: 1.2rem;">بحث</h4>
                    </div>

                    <div class="card-body">
                        <form class="form" method="GET" action="{{ route('PackageManagement.index') }}">
                            <div class="form-body row">
                                <div class="form-group col-md-6">
                                    <label for="feedback1" class="" style="font-size: 1.2rem;"> البحث بواسطة اسم الباقة الرقم التعريفي </label>
                                    <input type="text" id="feedback1" class="form-control" style="font-size: 1.2rem;"
                                        placeholder="البحث بواسطة اسم الباقة الرقم التعريفي" name="name">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="status" class="" style="font-size: 1.2rem;"> الحالة </label>
                                    <select id="status" name="status" class="form-control" style="font-size: 1.2rem;">
                                        <option value="" style="font-size: 1.2rem;">اختر الحالة</option>
                                        <option value="1" style="font-size: 1.2rem;">نشط</option>
                                        <option value="2" style="font-size: 1.2rem;">غير نشط</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-body row">
                                <div class="form-group col-md-6">
                                    <label for="period" class="" style="font-size: 1.2rem;"> الفترات </label>
                                    <select id="period" name="period" class="form-control" style="font-size: 1.2rem;">
                                        <option value="" style="font-size: 1.2rem;"> كل الفترات </option>
                                        <option value="yearly" style="font-size: 1.2rem;"> سنويا </option>
                                        <option value="monthly" style="font-size: 1.2rem;"> شهريا </option>
                                        <option value="weekly" style="font-size: 1.2rem;"> اسبوعي </option>
                                        <option value="daily" style="font-size: 1.2rem;"> يومي </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="type" class="" style="font-size: 1.2rem;"> النوع </label>
                                    <select id="type" name="type" class="form-control" style="font-size: 1.2rem;">
                                        <option value="" style="font-size: 1.2rem;"> كل الأنواع </option>
                                        <option value="membership" style="font-size: 1.2rem;"> العضوية </option>
                                        <option value="balance_recharge" style="font-size: 1.2rem;"> شحن الرصيد </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light" style="font-size: 1.2rem;">بحث</button>
                                <a href="{{ route('PackageManagement.index') }}" class="btn btn-outline-warning waves-effect waves-light" style="font-size: 1.2rem;">الغاء الفلتر</a>
                            </div>
                        </form>
                    </div>

                </div>

            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table text-center" style="font-size: 1.2rem;">
                        <thead>
                            <tr>
                                <th style="font-size: 1.2rem;">اسم الباقة</th>
                                <th style="font-size: 1.2rem;">النوع</th>
                                <th style="font-size: 1.2rem;">السعر</th>
                                <th style="font-size: 1.2rem;">الفترة</th>
                                <th style="font-size: 1.2rem;">الحالة</th>
                                <th style="font-size: 1.2rem;">ترتيب بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packages as $package)
                                <tr>
                                    <td style="font-size: 1.2rem;">{{ $package->commission_name }}</td>
                                    <td style="font-size: 1.2rem;">{{ $package->members == 1 ? 'العضوية' : 'شحن الرصيد' }}</td>
                                    <td style="font-size: 1.2rem;">{{ $package->price }}</td>
                                    <td style="font-size: 1.2rem;">{{ $package->duration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle {{ $package->status == 1 ? 'bg-success' : 'bg-secondary' }}" style="width: 10px; height: 10px;"></div>
                                            <span class="text-muted" style="font-size: 1.2rem;">{{ $package->status == 1 ? 'نشط' : 'غير نشط' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                    style="font-size: 1.2rem;"
                                                    type="button" id="dropdownMenuButton{{ $package->id }}" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false"></button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $package->id }}">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('PackageManagement.show', $package->id) }}" style="font-size: 1.2rem;">
                                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('PackageManagement.edit', $package->id) }}" style="font-size: 1.2rem;">
                                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $package->id }}">
                                                        <i class="fa fa-trash me-2"></i>حذف
                                                    </a>
                                                </li>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                                <div class="modal fade text-left" id="modal_DELETE{{ $package->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #EA5455 !important;">
                                                <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $package->name }}</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true" style="color: #DC3545">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <strong>هل انت متاكد من انك تريد الحذف ؟</strong>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">الغاء</button>
                                                <form action="{{ route('PackageManagement.destroy', $package->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger waves-effect waves-light">تأكيد</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>




        @endsection
