@extends('master')

@section('title')
ورديات المخصصة
@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
@endsection
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ورديات المخصصة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">{{ $custom_shift->name }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <div class="card">
        <div class="card-title p-2">
            <a href="#" class="btn btn-outline-danger btn-sm waves-effect waves-light" data-toggle="modal" data-target="#modal_DELETE{{ $custom_shift->id }}">حذف <i class="fa fa-trash"></i></a>
            <a href="{{ route('custom_shifts.edit',$custom_shift->id) }}" class="btn btn-outline-primary btn-sm waves-effect waves-light">تعديل <i class="fa fa-edit"></i></a>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                        aria-controls="details" aria-selected="true">تفاصيل </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log" role="tab"
                        aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- تفاصيل  -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="tab-content">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <table class="table">
                                        <thead style="background: #f8f8f8">
                                            <tr>
                                                <th style="width: 70%">معلومات وردية المخصصة</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p><small>تاريخ البدء</small>: </p><strong> {{ $custom_shift->from_date }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>تاريخ الانتهاء </small>: </p><strong> {{ $custom_shift->to_date }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><small>الوردية المعينه </small>: </p><strong>{{ $custom_shift->shift->name }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table">
                                        <thead style="background: #f8f8f8">
                                            <tr>
                                                <th style="width: 70%">التفاصيل مختارة</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p><small>اسم الموظف</small>:</p>
                                                    @foreach ($custom_shift->employees as $employee)
                                                        <strong class="mr-1">
                                                            <i class="fa fa-tag mr-1"></i>
                                                            <a href="{{ route('employee.show', $employee->id) }}" target="_blank">
                                                                {{ $employee->full_name }} #{{ $employee->id }}
                                                            </a>
                                                        </strong>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- سجل النشاطات -->
                <div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="activity-log-tab">


                            <div class="row mt-4">
                                <div class="col-12">
                                    @if ($logs && count($logs) > 0)
                                        @php
                                            $previousDate = null;
                                        @endphp

                                        @foreach ($logs as $date => $dayLogs)
                                            @php
                                                $currentDate = \Carbon\Carbon::parse($date);
                                                $diffInDays = $previousDate
                                                    ? $previousDate->diffInDays($currentDate)
                                                    : 0;
                                            @endphp

                                            @if ($diffInDays > 7)
                                                <div class="timeline-date">
                                                    <h4>{{ $currentDate->format('Y-m-d') }}</h4>
                                                </div>
                                            @endif

                                            <div class="timeline-day">
                                                {{ $currentDate->locale('ar')->translatedFormat('l') }}</div>

                                            <ul class="timeline">
                                                @foreach ($dayLogs as $log)
                                                    @if ($log)
                                                        <li class="timeline-item">
                                                            <div class="timeline-content">
                                                                <div class="time">
                                                                    <i class="far fa-clock"></i>
                                                                    {{ $log->created_at->format('H:i:s') }}
                                                                </div>
                                                                <div>
                                                                    <strong>{{ $log->user->name ?? 'مستخدم غير معروف' }}</strong>
                                                                    {!! Str::markdown($log->description ?? 'لا يوجد وصف') !!}
                                                                    <div class="text-muted">
                                                                        {{ $log->user->branch->name ?? 'فرع غير معروف' }}
                                                                    </div>
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
                                        <div class="alert alert-info text-center" role="alert">
                                            <i class="fa fa-info-circle fa-2x mb-2"></i>
                                            <p class="mb-0">لا توجد سجلات نشاط حتى الآن!</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>


            </div>
        </div>
    </div>


<!-- Modal delete -->
<div class="modal fade text-left" id="modal_DELETE{{ $custom_shift->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #EA5455 !important;">
                <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $custom_shift->name }}</h4>
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
                <a href="{{ route('custom_shifts.delete',$custom_shift->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
            </div>
        </div>
    </div>
</div>
<!--end delete-->

@endsection
