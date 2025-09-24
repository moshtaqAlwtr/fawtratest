@extends('master')

@section('title')
الشيكات المستلمة
@stop

@section('content')


    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الشيكات المستلمة</h2>
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
                                <a href="{{ route('received_cheques.create') }}" class="btn btn-outline-primary">
                                    <i class="fa fa-plus me-2"></i>استلم شيك
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
                        @if(isset($received_cheques) && !@empty($received_cheques) && $received_cheques->count() > 0)
                            <table class="table table-striped" dir="rtl">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">شيك #</th>
                                        <th scope="col">الحساب المستلم</th>
                                        <th scope="col">تاريخ الإصدار</th>
                                        <th scope="col">تاريخ الاستحقاق</th>
                                        <th scope="col">المبلغ</th>
                                        <th scope="col">الحالة</th>
                                        <th scope="col">اجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($received_cheques as $cheque)
                                        <tr>
                                            <td>{{ $cheque->cheque_number }}</td>
                                            <td>{{ $cheque->recipient_account_id }}</td>
                                            <td>{{ $cheque->issue_date }}</td>
                                            <td>{{ $cheque->due_date }}</td>
                                            <td>{{ $cheque->amount }}</td>
                                            <td>
                                                {{-- @if($cheque->status == 0) --}}
                                                    <span class="mr-1 bullet bullet-secondary bullet-sm"></span><span class="mail-date">مستلم</span>
                                                {{-- @else
                                                    <span class="mr-1 bullet bullet-danger bullet-sm"></span><span class="mail-date">غير نشط</span>
                                                @endif --}}
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <div class="dropdown">
                                                        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm" type="button"id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"aria-expanded="false"></button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('received_cheques.show', $cheque->id) }}">
                                                                    <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('received_cheques.edit', $cheque->id) }}">
                                                                    <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                                </a>
                                                            </li>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-danger text-xl-center" role="alert">
                                <p class="mb-0">
                                    لا توجد شيكات مضافة حتى الان !!
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
