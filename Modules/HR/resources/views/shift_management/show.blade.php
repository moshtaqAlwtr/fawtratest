@extends('master')

@section('title')
    الورديات
@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الورديات</h2>
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

        <div class="container" style="max-width: 1200px">
            <div class="card">
                <div class="card-title p-2">
                    <a href="{{ route('shift_management.edit', $shift->id) }}" class="btn btn-outline-primary btn-sm">تعديل
                        <i class="fa fa-edit"></i></a>
                    <a href="#" class="btn btn-outline-danger btn-sm" data-toggle="modal"
                        data-target="#modal_DELETE{{ $shift->id }}">حذف <i class="fa fa-trash"></i></a>
                    <a href="" class="btn btn-outline-success btn-sm">نقل <i class="fa fa-reply-all"></i></a>
                    <a href="" class="btn btn-outline-info btn-sm">اضف عمليه <i class="fa fa-plus"></i></a>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" aria-controls="home"
                                role="tab" aria-selected="false">التفاصيل</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="activate-tab" data-toggle="tab" href="#activate" aria-controls="about"
                                role="tab" aria-selected="true">سجل النشاطات</a>
                        </li>

                    </ul>
                    <div class="tab-content">

                        <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">

                            <div class="card">
                                <div class="card-header">
                                    <strong>ايام العمل :</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @php
                                            $arabicDays = [
                                                'sunday' => 'الأحد',
                                                'monday' => 'الإثنين',
                                                'tuesday' => 'الثلاثاء',
                                                'wednesday' => 'الأربعاء',
                                                'thursday' => 'الخميس',
                                                'friday' => 'الجمعة',
                                                'saturday' => 'السبت',
                                            ];
                                        @endphp
                                        @foreach ($shift->days as $key)
                                            <table class="table">
                                                <thead style="background: #f8f8f8">
                                                    <tr>
                                                        <th>يوم : <small>( {{ $arabicDays[$key->day] ?? $key->day }}
                                                                )</small></th>
                                                        <th>نوع اليوم : <small>( يوم عمل )</small></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <p><small>بداية الوردية </small>:
                                                                <strong>{{ $key->start_time }}</strong></p>
                                                            <p><small>بداية تسجيل الدخول </small>:
                                                                <strong>{{ $key->login_start_time }}</strong></p>
                                                            <p><small>نهاية تسجيل الدخول </small>:
                                                                <strong>{{ $key->login_end_time }}</strong></p>
                                                            <p><small>فترة السماح </small>: <strong>{{ $key->grace_period }}
                                                                    دقيقة</strong></p>
                                                        </td>
                                                        <td>
                                                            <p><small>نهاية الوردية </small>:
                                                                <strong>{{ $key->end_time }}</strong></p>
                                                            <p><small>بداية تسجيل الخروج </small>:
                                                                <strong>{{ $key->logout_start_time }}</strong></p>
                                                            <p><small>نهاية تسجيل الخروج </small>:
                                                                <strong>{{ $key->logout_end_time }}</strong></p>
                                                            <p><small>حساي التأخير </small>: <strong>
                                                                    @if ($key->delay_calculation == 1)
                                                                        بعد موعد بداية الوردية + مهلة التأخير
                                                                    @else
                                                                        من موعد بداية الوردية
                                                                    @endif
                                                                </strong></p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane" id="activate" aria-labelledby="activate-tab" role="tabpanel">

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
        </div>
    </div>
    </div>

    <!-- Modal delete -->
    <div class="modal fade text-left" id="modal_DELETE{{ $shift->id }}" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #EA5455 !important;">
                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $shift->name }}</h4>
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
                    <button type="button" class="btn btn-light waves-effect waves-light"
                        data-dismiss="modal">الغاء</button>
                    <a href="{{ route('shift_management.delete', $shift->id) }}"
                        class="btn btn-danger waves-effect waves-light">تأكيد</a>
                </div>
            </div>
        </div>
    </div>
    <!--end delete-->

    </div><!-- content-body -->
@endsection
