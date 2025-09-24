@extends('master')

@section('title')
    ادارة الرصيد
@stop

@section('content')
    <div style="font-size: 1.2rem;">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0" style="font-size: 1.2rem;"> ادارة الرصيد </h2>
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
                                <input type="text" class="form-control text-center" value="صفحة 1 من 1"
                                    style="font-size: 1.2rem;">
                            </div>

                        </div>
                        <div class="d-flex" style="gap: 15px">
                            <a href="{{ route('BalanceType.create') }}" class="btn btn-success" style="font-size: 1.2rem;">
                                <i class="fa fa-plus me-2"></i>
                                اضافة نوع الرصيد
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
                        <form method="GET" action="{{ route('BalanceType.index') }}" class="form">
                            <div class="form-body row">
                                <div class="form-group col-md-6">
                                    <label for="feedback1" class="" style="font-size: 1.2rem;"> البحث بواسطة اسم الرصيد الرقم التعريفي </label>
                                    <input type="text" id="feedback1" class="form-control" style="font-size: 1.2rem;"
                                           placeholder="البحث بواسطة اسم الرصيد الرقم التعريفي" name="name" value="{{ request()->get('name') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="feedback2" class="" style="font-size: 1.2rem;"> الحالة </label>
                                    <select id="feedback2" class="form-control" style="font-size: 1.2rem;" name="status">
                                        <option value="" style="font-size: 1.2rem;">اختر الحالة</option>
                                        <option value="1" {{ request()->get('status') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request()->get('status') == '2' ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-actions">
                                <a  href="{{ route('BalanceType.index') }}" class="btn btn-primary mr-1 waves-effect waves-light" style="font-size: 1.2rem;">بحث</a>
                                <button type="reset" class="btn btn-outline-warning waves-effect waves-light" style="font-size: 1.2rem;">الغاء الفلتر</button>
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
                                <th style="font-size: 1.2rem;">اسم</th>
                                <th style="font-size: 1.2rem;">الحالة</th>
                                <th style="font-size: 1.2rem;">ترتيب بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($balanceTypes as $balanceType)
                                <tr class="text-center">
                                    <td style="font-size: 1.2rem;">{{ $balanceType->name }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <div class="rounded-circle {{ $balanceType->status ? 'bg-success' : 'bg-secondary' }}" style="width: 10px; height: 10px;"></div>
                                            <span class="text-muted" style="font-size: 1.2rem;">
                                                {{ $balanceType->status ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                        type="button" id="dropdownMenuButton303" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false"></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('BalanceType.show', $balanceType->id) }}">
                                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('BalanceType.edit', $balanceType->id) }}">
                                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $balanceType->id }}">
                                                            <i class="fa fa-trash me-2"></i>حذف
                                                        </a>
                                                    </li>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal for Deletion -->
                                <div class="modal fade text-left" id="modal_DELETE{{ $balanceType->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #EA5455 !important;">
                                                <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $balanceType->name }}</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true" style="color: #DC3545">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <strong>هل انت متاكد من انك تريد الحذف ؟</strong>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">الغاء</button>
                                                <form action="{{ route('BalanceType.destroy', $balanceType->id) }}" method="POST" style="display:inline;">
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
