@extends('master')

@section('title')
    ادارة الشحن والارصدة
@stop

@section('content')
    <div style="font-size: 1.1rem;">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0"> ادارة الشحن والارصدة </h2>
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
        @include('layouts.alerts.success')
        @include('layouts.alerts.error')

        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">


                        <div class="d-flex align-items-center gap-3">
                            <div class="btn-group">
                                <button class="btn btn-light border">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                                <button class="btn btn-light border">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                            </div>
                            <span class="mx-2">1 - 1 من 1</span>
                            <div class="input-group" style="width: 150px">
                                <input type="text" class="form-control text-center" value="صفحة 1 من 1">
                            </div>

                        </div>
                        <div class="d-flex" style="gap: 15px">
                            <a href="{{ route('MangRechargeBalances.create') }}" class="btn btn-success">
                                <i class="fa fa-plus me-2"></i>
                                اضافة شحن ارصدة
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <h4 class="card-title">بحث</h4>
                    </div>

                    <div class="card-body">
                        <form class="form" method="GET" action="{{ route('MangRechargeBalances.index') }}">
                            <div class="form-body row">
                                <div class="form-group col-md-4">
                                    <label for="client_or_id" class=""> البحث بواسطة العميل أو الرقم التعريفي </label>
                                    <input type="text" id="client_or_id" class="form-control"
                                        placeholder="البحث بواسطة العميل أو الرقم التعريفي" name="client_or_id">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="balance_name_or_id" class=""> البحث بواسطة اسم الرصيد أو الرقم
                                        التعريفي </label>
                                    <input type="text" id="balance_name_or_id" class="form-control"
                                        placeholder="البحث بواسطة اسم الرصيد أو الرقم التعريفي" name="balance_name_or_id">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="status" class=""> الحالة </label>
                                    <select id="status" class="form-control" name="status">
                                        <option value="">اختر الحالة</option>
                                        <option value="active">نشط</option>
                                        <option value="inactive">موقوف</option>
                                        <!-- Add more options as needed -->
                                    </select>
                                </div>
                            </div>
                            <div class="form-body row">
                                <div class="form-group col-md-3">
                                    <label> تاريخ البدء (من) </label>
                                    <input type="date" class="form-control" name="start_date_from">
                                </div>
                                <div class="form-group col-md-3">
                                    <label> تاريخ البدء (إلى) </label>
                                    <input type="date" class="form-control" name="start_date_to">
                                </div>
                                <div class="form-group col-md-3">
                                    <label> تاريخ الانتهاء (من) </label>
                                    <input type="date" class="form-control" name="end_date_from">
                                </div>
                                <div class="form-group col-md-3">
                                    <label> تاريخ الانتهاء (إلى) </label>
                                    <input type="date" class="form-control" name="end_date_to">
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                                <a href="{{ route('MangRechargeBalances.index') }}"
                                    class="btn btn-outline-warning waves-effect waves-light">الغاء الفلتر</a>
                            </div>
                        </form>

                    </div>

                </div>

            </div>


            <div class="card">
                <div class="card-body">

                    <table class="table" style="font-size: 1.1rem;">
                        <thead>
                            <tr>
                                <th>المعرف</th>
                                <th>اسم العميل</th>
                                <th>نوع الرصيد</th>
                                <th>فاتورة</th>
                                <th>رصيد الشحن</th>
                                <th>الاستهلاك</th>
                                <th>الحالة</th>
                                <th>ترتيب بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($balances as $balance)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-sm bg-danger">
                                                <span class="avatar-content">{{ $balance->client->initial ?? 'N/A' }}</span>
                                            </div>
                                            <div>
                                                {{ $balance->client->trade_name ?? 'N/A' }}
                                                <br>
                                                <small class="text-muted">#{{ $balance->client->id ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $balance->balanceType->name ?? 'N/A' }}<br><small
                                            class="text-muted">#{{ $balance->balance_type_id ?? 'N/A' }}</small></td>
                                    <td>-</td>
                                    <td>{{ $balance->value }}<br><small class="text-muted">نقطة</small></td>
                                    <td>
                                        <div class="text-muted">{{ $balance->consumption ?? 0 }} مستهلك</div>
                                        <div class="border-top mt-1 pt-1">{{ $balance->remaining ?? 0 }} متبقي</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-info" style="width: 8px; height: 8px;"></div>
                                            @if ($balance->status)
                                                <span class="badge badge-success">نشط</span>
                                            @else
                                                <span class="badge badge-danger">موقوف</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                    type="button" id="dropdownMenuButton303" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false"></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('MangRechargeBalances.show', $balance->id) }}">
                                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('MangRechargeBalances.edit', $balance->id) }}">
                                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $balance->id }}">
                                                            <i class="fa fa-trash me-2"></i>حذف
                                                        </a>
                                                    </li>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <div class="modal fade text-left" id="modal_DELETE{{ $balance->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #EA5455 !important;">
                                                <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $balance->name }}</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true" style="color: #DC3545">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <strong>هل انت متاكد من انك تريد الحذف ؟</strong>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">الغاء</button>
                                                <form action="{{ route('MangRechargeBalances.destroy', $balance->id) }}" method="POST" style="display:inline;">
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
