@extends('master')

@section('title')
    قواعد ولاء العملاء
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">قواعد ولاء العملاء</h2>
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


    @include('layouts.alerts.success')
    @include('layouts.alerts.error')

    <div class="content-body">

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-rtl flex-wrap">
                    <div></div>
                    <div>
                        <a href="{{ route('loyalty_points.create') }}" class="btn btn-outline-success">
                            <i class="fa fa-plus me-2"></i>أضف قاعدة ولاء
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <div class="card">
            <div class="card-body">
                <form class="form" method="GET" action="{{ route('loyalty_points.index') }}">
                    <div class="form-body row">
                        <div class="form-group col-md-3">
                            <label for="" class=""> الاسم </label>
                            <input type="text" id="name" class="form-control" placeholder="الاسم" name="name">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="balance_name" class=""> تصنيف العميل </label>
                            <input type="text" id="balance_name" class="form-control" placeholder="تصنيف العميل" name="balance_name">
                        </div>
                        <div class="form-group col-md-3">
                            <label>العملة </label>
                            <select class="form-control" id="currency" name="currency">
                                <option value="">كل العملات</option>
                                <option value="3">ريال سعودي</option>
                                <option value="1">الدولار الأمريكي</option>
                                <option value="2">الدرهم الإماراتي</option>
                                <option value="3">اليورو</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label> من الحد الادنى للصرف </label>
                            <input type="number" class="form-control" name="min_spent">
                        </div>
                    </div>
                    <div class="form-body row">
                        <div class="form-group col-md-3">
                            <label> الى الحد الادنى للصرف </label>
                            <input type="number" class="form-control" name="max_spent">
                        </div>
                        <div class="form-group col-md-3">
                            <label> الحالة </label>
                            <select class="form-control" id="status" name="status">
                                <option value="">الحالة</option>
                                <option value="1"> نشيط</option>
                                <option value="2">غير نشيط</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
                        <a href="{{ route('loyalty_points.index') }}" class="btn btn-outline-warning waves-effect waves-light">الغاء الفلتر</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table" style="font-size: 1.1rem;">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>تصنيف العميل</th>
                            <th>درجة الاولوية</th>
                            <th>معامل جمع الرصيد</th>
                            <th>الحد الادنى للصرف</th>
                            <th>الحالة</th>
                            <th>ترتيب بواسطة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($loyaltyRules->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center">لا توجد بيانات لعرضها</td>
                            </tr>
                        @else
                            @foreach($loyaltyRules as $rule)
                                <tr>
                                    <td>{{ $rule->name }}</td>
                                    <td>
                                        @foreach($rule->clients as $client)
                                            {{ $client->trade_name }}@if (!$loop->last), @endif
                                        @endforeach
                                    </td>
                                    <td>{{ $rule->priority_level }}</td>
                                    <td>{{ $rule->collection_factor }}</td>
                                    <td>{{ $rule->minimum_total_spent }}</td>
                                    <td>{{ $rule->status == 1 ? 'نشيط' : 'غير نشيط' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                    type="button" id="dropdownMenuButton303" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false"></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('loyalty_points.show', $rule->id) }}">
                                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('loyalty_points.edit', $rule->id) }}">
                                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE {{ $rule->id }}">
                                                            <i class="fa fa-trash me-2"></i>حذف
                                                        </a>
                                                    </li>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Modal لحذف القسم -->
                                <div class="modal fade" id="modal_DELETE {{ $rule->id }}" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document"></div>
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myModalLabel1">حذف</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                هل انت متاكد من انك تريد الحذف ��
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">الغاء</button>
                                                <a href="{{ route('loyalty_points.destroy', $rule->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                        </div>
                                    </div>
                                </div>
                                <!--end delete-->
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endsection

