@extends('master')

@section('title')
    خزائن وحسابات بنكية
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">خزائن وحسابات بنكية</h2>
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
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div></div>

                    <div>
                        <a href="{{ route('treasury.create_account_bank') }}" class="btn btn-outline-success">
                            <i class="fa fa-bank"></i> إضافة حساب بنكي
                        </a>
                        <a href="{{ route('treasury.create') }}" class="btn btn-outline-primary">
                            <i class="fa fa-archive"></i> إضافة خزينة
                        </a>
                    </div>

                </div>
            </div>
        </div>

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-title">

                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>بحث</div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form class="form" method="GET" action="">
                        <div class="form-body row">
                            <div class="form-group col-md-3">
                                <label for="">البحث بكلمة مفتاحية</label>
                                <input type="text" class="form-control" placeholder="ادخل الإسم او الكود"
                                    name="keywords">
                            </div>

                            <div class="form-group col-md-3">
                                <label for="">النوع</label>
                                <select name="category" class="form-control" id="">
                                    <option value="">كل الانواع</option>
                                    <option value="1">حساب</option>
                                    <option value="2">خزينة</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="">الحالة</label>
                                <select class="form-control" name="status">
                                    <option value="">كل الحالات</option>
                                    <option value="1">نشط</option>
                                    <option value="2">غير نشط</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="">العمله</label>
                                <select class="form-control" name="status">
                                    <option value="">كل العملات</option>
                                    <option value="1">ريال</option>
                                    <option value="2">جنيه</option>
                                </select>
                            </div>

                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                            <a href="{{ route('treasury.index') }}"
                                class="btn btn-outline-danger waves-effect waves-light">الغاء الفلترة</a>
                        </div>
                    </form>

                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-body">
                    @if (@isset($treasuries) && !@empty($treasuries) && count($treasuries) > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>الوصف</th>
                                    <th>المبلغ</th>
                                    <th>الحاله</th>
                                    <th style="width: 10%">اجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($treasuries as $treasury)
                                    <tr>
                                        <td>
                                            <p><strong>
                                                    @if ($treasury->type_accont == 0)
                                                        <i class="fa fa-archive"></i>
                                                    @else
                                                        <i class="fa fa-bank"></i>
                                                    @endif {{ $treasury->name }}
                                                </strong></p>
                                            <small>
                                                @if ($treasury->type_accont == 0)
                                                    خزينة
                                                @else
                                                    حساب بنكي
                                                @endif
                                            </small>
                                        </td>
                                        <td>{{ $treasury->description }}</td>
                                        <td>{{ $treasury->balance }}</td>
                                        <td>
                                            @if ($treasury->is_active == 0)
                                                <div class="badge badge-pill badge-success">نشط</div>
                                            @else
                                                <div class="badge badge-pill badge-danger">غير نشط</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v mr-1 mb-1"
                                                        type="button" id="dropdownMenuButton303" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false"></button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                         <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('treasury.show', $treasury->id) }}">
                                                                    <i class="fa fa-eye me-2 text-primary"></i> عرض
                                                                </a>
                                                            </li>
                                                        @if (auth()->user()->role != 'employee')

                                                            <li>
                                                                @if ($treasury->type_accont == 1)
                                                                    <a class="dropdown-item" href="">
                                                                        <i class="fa fa-edit me-2 text-success"></i> تعديل
                                                                    </a>
                                                                @else
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('treasury.edit', $treasury->id) }}">
                                                                        <i class="fa fa-edit me-2 text-success"></i> تعديل
                                                                    </a>
                                                                @endif
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('treasury.updateStatus', $treasury->id) }}">
                                                                    @if ($treasury->is_active == 0)
                                                                        <i class="fa fa-ban me-2 text-danger"></i> تعطيل
                                                                    @else
                                                                        <i class="fa fa-check me-2 text-success"></i> تفعيل
                                                                    @endif
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('treasury.transferCreate', $treasury->id) }}">
                                                                    <i class="fa fa-exchange-alt me-2 text-info"></i> تحويل
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('treasury.updateType', $treasury->id) }}">
                                                                    @if ($treasury->type == 'main')
                                                                        <i class="fa fa-star me-2 text-secondary"></i>
                                                                        اجعله فرعي
                                                                    @else
                                                                        <i class="fa fa-star me-2 text-secondary"></i>
                                                                        اجعله رئيسي
                                                                    @endif
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#"
                                                                    data-toggle="modal"
                                                                    data-target="#modal_DELETE_{{ $treasury->id }}">
                                                                    <i class="fa fa-trash me-2"></i> حذف
                                                                </a>
                                                            </li>
                                                        @else
                                                            <!-- العناصر التي يراها الموظف فقط -->
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('treasury.show', $treasury->id) }}">
                                                                    <i class="fa fa-eye me-2 text-primary"></i> عرض
                                                                </a>
                                                            </li>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal delete -->
                                    <div class="modal fade text-left" id="modal_DELETE_{{ $treasury->id }}"
                                        tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color: #EA5455 !important;">
                                                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف
                                                    </h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true" style="color: #DC3545">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <strong>هل انت متاكد من انك تريد حذف الخزينة
                                                        "{{ $treasury->name }}"؟</strong>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light waves-effect waves-light"
                                                        data-dismiss="modal">الغاء</button>
                                                    <form action="{{ route('treasury.destroy', $treasury->id) }}"
                                                        method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-danger waves-effect waves-light">تأكيد</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end delete-->
                                @endforeach

                            </tbody>
                        </table>
                        {{ $treasuries->links('pagination::bootstrap-5') }}
                    @else
                        <div class="alert alert-danger text-xl-center" role="alert">
                            <p class="mb-0">
                                لا توجد خزائن مضافه حتى الان
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>


    </div>

@endsection

@section('scripts')

@endsection
