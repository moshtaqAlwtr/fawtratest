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
                        <strong>{{ $storehouse->name }} </strong> |
                        @if ($storehouse->status == 0)
                            <div class="badge badge-pill badge badge-success">نشط</div>
                        @elseif ($storehouse->status == 1)
                            <div class="badge badge-pill badge badge-danger">غير نشط</div>
                        @else
                            <div class="badge badge-pill badge badge-warning">متوقف</div>
                        @endif
                    </div>

                    <div>
                        <a href="{{ route('storehouse.edit',$storehouse->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-edit"></i>تعديل
                        </a>
                        <a href="" class="btn btn-outline-danger btn-sm">
                            <i class="fa fa-trash"></i>حذف
                        </a>
                    </div>

                </div>
            </div>
        </div>

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="false">معلومات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="activate-tab" data-toggle="tab" href="#activate" aria-controls="about" role="tab" aria-selected="true">سجل النشاطات</a>
                    </li>

                </ul>
                <div class="tab-content">

                    <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <strong>معلومات</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th><small>الاسم</small></th>
                                                <th><small>عنوان الشحن</small></th>
                                                <th><small>الحاله</small></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    {{ $storehouse->name }}
                                                </td>
                                                <td>
                                                    {{ $storehouse->shipping_address }}
                                                </td>
                                                <td>
                                                    @if ($storehouse->status == 0)
                                                        <div class="badge badge-pill badge badge-success">نشط</div>
                                                    @elseif ($storehouse->status == 1)
                                                        <div class="badge badge-pill badge badge-danger">غير نشط</div>
                                                    @else
                                                        <div class="badge badge-pill badge badge-warning">متوقف</div>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <strong>الصلاحيات</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th><small>عرض</small></th>
                                                <th><small>انشاء فاتورة</small></th>
                                                <th><small>تعديل المخازن</small></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    @if($storehouse->view_permissions !== 0)
                                                        @if ($storehouse->view_permissions == 1)
                                                        <!---employee_id--->
                                                        {{ App\Models\Employee::find($storehouse->value_of_view_permissions)->full_name }}
                                                        @elseif ($storehouse->view_permissions == 2)
                                                        <!---functional_role_id--->
                                                            {{ $storehouse->value_of_view_permissions }} functional_role_id
                                                        @else
                                                        <!---branch_id--->
                                                            {{ $storehouse->value_of_view_permissions }} branch_id
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($storehouse->crate_invoices_permissions !== 0)
                                                        @if ($storehouse->crate_invoices_permissions == 1)
                                                        <!---employee_id--->
                                                            {{ $storehouse->value_of_crate_invoices_permissions }} employee_id
                                                        @elseif ($storehouse->crate_invoices_permissions == 2)
                                                        <!---functional_role_id--->
                                                            {{ $storehouse->value_of_crate_invoices_permissions }} functional_role_id
                                                        @else
                                                        <!---branch_id--->
                                                            {{ $storehouse->value_of_crate_invoices_permissions }} branch_id
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($storehouse->edit_stock_permissions !== 0)
                                                        @if ($storehouse->edit_stock_permissions == 1)
                                                        <!---employee_id--->
                                                            {{ $storehouse->value_of_edit_stock_permissions }}
                                                        @elseif ($storehouse->edit_stock_permissions == 2)
                                                        <!---functional_role_id--->
                                                            {{ $storehouse->value_of_edit_stock_permissions }} functional_role_id
                                                        @else
                                                        <!---branch_id--->
                                                            {{ $storehouse->value_of_edit_stock_permissions }} branch_id
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane" id="activate" aria-labelledby="activate-tab" role="tabpanel">
                           <style>
    .timeline {
        position: relative;
        margin: 20px 0;
        padding: 0;
        list-style: none;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 50px;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #28a745 0%, #218838 100%);
        right: 50px;
        margin-right: -2px;
    }
    .timeline-item {
        margin: 0 0 20px;
        padding-right: 100px;
        position: relative;
        text-align: right;
    }
    .timeline-item::before {
        content: "\f067";
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 30px;
        top: 15px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(145deg, #28a745, #218838);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ffffff;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }
    .timeline-content {
        background: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    .timeline-content .time {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 10px;
    }

    .filter-bar {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    .timeline-day {
        background-color: #ffffff;
        padding: 10px 20px;
        border-radius: 30px;
        text-align: center;
        margin-bottom: 20px;
        font-weight: bold;
        color: #333;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        display: inline-block;
        position: relative;
        top: 0;
        right: 50px;
        transform: translateX(50%);
    }
    .filter-bar .form-control {
        border-radius: 8px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }
    .filter-bar .btn-outline-secondary {
        border-radius: 8px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }
    .timeline-date {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin: 20px 0;
        color: #333;
    }
</style>
<div class="card">
    <div class="card">
        <div class="container">
            <div class="row mt-4">
                <div class="col-12">
                    <!-- شريط التصفية -->
                    <div class="filter-bar d-flex justify-content-between align-items-center">
                        <div>
                            <button class="btn btn-outline-secondary"><i class="fas fa-th"></i></button>
                            <button class="btn btn-outline-secondary"><i class="fas fa-list"></i></button>
                        </div>
                        <div class="d-flex">
                            <form action="{{ route('logs.index') }}" method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="ابحث في الأحداث..." value="{{ $search ?? '' }}">
                                <button class="btn btn-primary" type="submit">بحث</button>
                            </form>
                        </div>
                    </div>

                    <!-- الجدول الزمني -->
                    @if(isset($actives_logs) && $actives_logs->count() > 0)
                        @php
                            $previousDate = null;
                        @endphp

                        @foreach($actives_logs as $date => $dayLogs)
                            @php
                                $currentDate = \Carbon\Carbon::parse($date);
                                $diffInDays = $previousDate ? $previousDate->diffInDays($currentDate) : 0;
                            @endphp

                            @if($diffInDays > 7)
                                <div class="timeline-date">
                                    <h4>{{ $currentDate->format('Y-m-d') }}</h4>
                                </div>
                            @endif

                            <div class="timeline-day">{{ $currentDate->translatedFormat('l') }}</div>

                            <ul class="timeline">
                                @foreach($dayLogs as $log)
                                    @if ($log)
                                        <li class="timeline-item">
                                            <div class="timeline-content">
                                                <div class="time">
                                                    <i class="far fa-clock"></i> {{ $log->created_at->format('H:i:s') }}
                                                </div>
                                                <div>
                                                    <strong>{{ $log->user->name ?? 'مستخدم غير معروف' }}</strong>
                                                    {!! Str::markdown($log->description ?? 'لا يوجد وصف') !!}
                                                    <div class="text-muted">{{ $log->user->branch->name ?? 'فرع غير معروف' }}</div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>

                            @php
                                $previousDate = $currentDate;
                            @endphp
                        @endforeach
                    @else
                        <div class="alert alert-danger text-xl-center" role="alert">
                            <p class="mb-0">لا توجد عمليات مضافه حتى الان !!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection


@section('scripts')



@endsection
