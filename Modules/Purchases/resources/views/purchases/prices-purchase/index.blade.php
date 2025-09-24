@extends('master')

@section('title')
طلبات الشراء
@stop

@section('content')

    <div class="card">

    </div>
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة طلبات الشراء</h2>
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

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">

                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item">
                                    <button class="btn btn-sm btn-outline-secondary px-2" aria-label="Previous">
                                        <i class="fa fa-angle-right"></i>
                                    </button>
                                </li>
                                <li class="page-item mx-2">
                                    <span class="text-muted">صفحة 1 من 1</span>
                                </li>
                                <li class="page-item">
                                    <button class="btn btn-sm btn-outline-secondary px-2" aria-label="Next">
                                        <i class="fa fa-angle-left"></i>
                                    </button>
                                </li>
                            </ul>
                        </nav>

                        <span class="text-muted mx-2">1-1 من 1</span>

                        <a href="{{ route('OrdersPurchases.create') }}" class="btn btn-success">
                            <i class="fa fa-plus me-1"></i>
                            أضف طلب شراء
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form class="form" method="GET" action="{{ route('OrdersPurchases.index') }}">
                    <div class="form-body row">
                        <div class="form-group col-md-3">
                            <label for="follow_status">حالة المتابعة</label>
                            <select name="follow_status" class="form-control" id="follow_status">
                                <option value=""> جميع حالات المتابعة</option>
                                <option value="1" {{ request('follow_status') == '1' ? 'selected' : '' }}>متابع
                                </option>
                                <option value="2" {{ request('follow_status') == '2' ? 'selected' : '' }}>غير متابع
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="employee_id">موظف</label>
                            <select name="employee_id" class="form-control" id="employee_id">
                                <option value=""> اختر الموظف </option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="code">الكود</label>
                            <input type="text" class="form-control" name="code" id="code"
                                value="{{ request('code') }}" placeholder="ادخل الكود">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="status">الحالة</label>
                            <select class="form-control" name="status" id="status">
                                <option value="">الحالة</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>متوقف</option>
                                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="order_date_from">تاريخ الطلب (من)</label>
                            <input type="date" class="form-control" name="order_date_from" id="order_date_from"
                                value="{{ request('order_date_from') }}">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="order_date_to">تاريخ الطلب (إلى)</label>
                            <input type="date" class="form-control" name="order_date_to" id="order_date_to"
                                value="{{ request('order_date_to') }}">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="due_date_from">تاريخ الاستحقاق (من)</label>
                            <input type="date" class="form-control" name="due_date_from" id="due_date_from"
                                value="{{ request('due_date_from') }}">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="due_date_to">تاريخ الاستحقاق (إلى)</label>
                            <input type="date" class="form-control" name="due_date_to" id="due_date_to"
                                value="{{ request('due_date_to') }}">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary mr-1">بحث</button>
                        <a href="{{ route('OrdersPurchases.index') }}" class="btn btn-outline-danger">إلغاء الفلترة</a>
                    </div>
                </form>
            </div>
        </div>


        <div class="card">
            <div class="card-body">
                @if ($purchaseOrders->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 5%">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>رقم الطلب</th>
                                <th>المسمى</th>
                                <th>تاريخ الطلب</th>
                                <th>تاريخ الاستحقاق</th>

                                <th>الحالة</th>
                                <th style="width: 10%">خيارات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseOrders as $order)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input order-checkbox"
                                            value="{{ $order->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2" style="background-color: #4B6584">
                                                <span class="avatar-content">{{ substr($order->code, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                {{ $order->code }}
                                                <div class="text-muted small">#{{ $order->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $order->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') }}</td>
                                    <td>
                                        @if ($order->due_date)
                                            {{ \Carbon\Carbon::parse($order->due_date)->format('Y-m-d') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($order->status == 1)
                                            <span class="badge bg-warning">تحت المراجعة </span>
                                        @elseif ($order->status == 2)
                                            <span class="badge bg-success">تم الموافقة علية </span>
                                        @elseif ($order->status == 3)
                                            <span class="badge bg-danger">مرفوض</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                    type="button" id="dropdownMenuButton{{ $order->id }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="dropdownMenuButton{{ $order->id }}">
                                                    <a class="dropdown-item" href="{{ route('OrdersPurchases.show', $order->id) }}">
                                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('OrdersPurchases.edit', $order->id) }}">
                                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                    </a>
                                                    <a class="dropdown-item text-danger" href="#"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $order->id }}">
                                                        <i class="fa fa-trash me-2"></i>حذف
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal delete -->
                                        <div class="modal fade" id="deleteModal{{ $order->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">حذف طلب الشراء</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>هل أنت متأكد من حذف طلب الشراء رقم "{{ $order->code }}"؟
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light"
                                                            data-bs-dismiss="modal">إلغاء</button>
                                                        <form action="{{ route('OrdersPurchases.destroy', $order->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">حذف</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info text-center" role="alert">
                        <p class="mb-0">لا يوجد طلبات شراء مضافة حتى الآن</p>
                    </div>
                @endif
            </div>
        </div>

    </div>




@endsection


@section('scripts')



@endsection
