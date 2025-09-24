@extends('master')

@section('title')
المستودعات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">المستودعات</h2>
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
                    <div>
                    </div>

                    <div>
                        <a href="{{ route('storehouse.create') }}" class="btn btn-outline-primary">
                            <i class="feather icon-plus"></i>مستودع جديد
                        </a>
                    </div>

                </div>
            </div>
        </div>

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-body">
                @if (@isset($storehouses) && !@empty($storehouses) && count($storehouses) > 0)
                <table class="table table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>الاسم</th>
                            <th>الحاله</th>
                            <th>اجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($storehouses as $index=>$storehouse)
                    <tr>
                        <td>
                            <strong>{{ $storehouse->name }}</strong>
                            <small>
                                @if ($storehouse->major == 1)
                                    <div class="badge badge-pill badge badge-success">رئيسي</div>
                                @endif
                            </small>
                        </td>
                        <td>
                            @if ($storehouse->status == 0)
                                    <span class="mr-1 bullet bullet-success bullet-sm"></span><span class="mail-date">نشط</span>
                            @elseif ($storehouse->status == 1)
                                <span class="mr-1 bullet bullet-danger bullet-sm"></span><span class="mail-date">غير نشط</span>
                            @else
                                <span class="mr-1 bullet bullet-secondary bullet-sm"></span><span class="mail-date">متوقف</span>
                            @endif
                        </td>
                        <td style="width: 10%">
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"aria-expanded="false"></button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('storehouse.show',$storehouse->id) }}">
                                                <i class="fa fa-eye me-2 text-primary"></i>عرض
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('storehouse.edit',$storehouse->id) }}">
                                                <i class="fa fa-edit me-2 text-success"></i>تعديل
                                            </a>
                                        </li>
                                        @if ($storehouse->major == 0)
                                            <li>
                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $storehouse->id }}">
                                                    <i class="fa fa-trash me-2 text-danger"></i>حذف
                                                </a>
                                            </li>
                                        @endif
                                        @if ($storehouse->status == 0)
                                            <li>
                                                <a class="dropdown-item" href="{{ route('storehouse.summary_inventory_operations', $storehouse->id) }}">
                                                    <i class="fa fa-bars me-2 text-info"></i>ملخص عمليات المخزون
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('storehouse.inventory_value', $storehouse->id) }}">
                                                    <i class="fa fa-bars me-2 text-info"></i>قيمة المخزون
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('storehouse.inventory_sheet', $storehouse->id) }}">
                                                    <i class="fa fa-bars me-2 text-info"></i>ورقة الجرد
                                                </a>
                                            </li>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Modal delete -->
                        <div class="modal fade text-left" id="modal_DELETE{{ $storehouse->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #EA5455 !important;">
                                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $storehouse->name }}</h4>
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
                                        <a href="{{ route('storehouse.delete', $storehouse->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
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
                            لا توجد مستودعات
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection


@section('scripts')



@endsection
