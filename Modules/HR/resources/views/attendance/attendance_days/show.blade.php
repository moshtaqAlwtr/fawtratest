@extends('master')

@section('title')
    عرض ايام الحضور

@stop
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
@endsection
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">{{ $attendance_day->employee->full_name }}</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">
                                {{ \Carbon\Carbon::parse($attendance_day->attendance_date)->locale('ar')->translatedFormat('l, d/m/Y') }}
                            </li>
                            <li class="breadcrumb-item active">
                                @if ($attendance_day->status == 'present')
                                    <span class="mr-1 bullet bullet-success bullet-sm"></span><span
                                        class="mail-date">حاضر</span>
                                @elseif($attendance_day->status == 'absent')
                                    <span class="mr-1 bullet bullet-secondary bullet-sm"></span><span class="mail-date">يوم
                                        اجازة (No Shift)</span>
                                @else
                                    <span class="mr-1 bullet bullet-danger bullet-sm"></span><span
                                        class="mail-date">غياب</span>
                                @endif
                            </li>
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
            <a href="#" class="btn btn-outline-danger btn-sm waves-effect waves-light" data-toggle="modal"
                data-target="#modal_DELETE{{ $attendance_day->id }}">حذف <i class="fa fa-trash"></i></a>
            <a href="{{ route('attendanceDays.edit', $attendance_day->id) }}"
                class="btn btn-outline-primary btn-sm waves-effect waves-light">تعديل <i class="fa fa-edit"></i></a>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                        aria-controls="details" aria-selected="true">تفاصيل </a>
                </li>

                <!-- تبويبة سجل الحضور الجديدة -->
                <li class="nav-item">
                    <a class="nav-link" id="attendance-record-tab" data-toggle="tab" href="#attendance-record" role="tab"
                        aria-controls="attendance-record" aria-selected="false">سجل الحضور</a>
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
                                                <th style="width: 70%">معلومات الموظف</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p><small>اسم الموظف</small>: </p>
                                                    <strong>{{ $attendance_day->employee->full_name }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>الرقم التعريفي للموظف</small>: </p><strong>#
                                                        {{ $attendance_day->employee->id }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><small>الحالة</small>: </p><strong>
                                                        @if ($attendance_day->status == 'present')
                                                            <span class="mr-1 bullet bullet-success bullet-sm"></span><span
                                                                class="mail-date">حاضر</span>
                                                        @elseif($attendance_day->status == 'absent')
                                                            <span
                                                                class="mr-1 bullet bullet-secondary bullet-sm"></span><span
                                                                class="mail-date">يوم اجازة (No Shift)</span>
                                                        @else
                                                            <span class="mr-1 bullet bullet-danger bullet-sm"></span><span
                                                                class="mail-date">غياب</span>
                                                        @endif
                                                    </strong>
                                                </td>
                                                <td>
                                                    <p><small>رقم دفتر الحضور</small>: </p><strong>#
                                                        {{ $attendance_day->id }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    @if ($attendance_day->status == 'present')
                                        <table class="table">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>بياتات الوردية</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p><small>بداية الوردية </small>: </p>
                                                        <strong>{{ $attendance_day->start_shift }}</strong>
                                                    </td>
                                                    <td>
                                                        <p><small>نهاية الوردية</small>: </p>
                                                        <strong>{{ $attendance_day->end_shift }}</strong>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <table class="table">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>بياتات الحضور</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p><small>تسجيل الدخول </small>: </p>
                                                        <span>{{ $attendance_day->login_time }}</span>
                                                    </td>
                                                    <td>
                                                        <p><small>تسجيل الخروج </small>: </p>
                                                        <strong>{{ $attendance_day->logout_time }}</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    @php
                                                        $loginTime = \Carbon\Carbon::parse($attendance_day->login_time);
                                                        $logoutTime = \Carbon\Carbon::parse(
                                                            $attendance_day->logout_time,
                                                        );
                                                        $StartShift = \Carbon\Carbon::parse(
                                                            $attendance_day->start_shift,
                                                        );
                                                        $EndShift = \Carbon\Carbon::parse($attendance_day->end_shift);
                                                        $totalDuration = $loginTime->diff($logoutTime);
                                                        $totalDurationDefault = $StartShift->diff($EndShift);
                                                    @endphp
                                                    <td>
                                                        <p><small>ساعات العمل المتوقعة </small>: </p>
                                                        <strong>{{ $totalDurationDefault->h }} ساعة و
                                                            {{ $totalDurationDefault->i }} دقيقة</strong>
                                                    </td>
                                                    <td>
                                                        <p><small>ساعات العمل الفعلية </small>: </p>
                                                        <strong>{{ $totalDuration->h }} ساعة و {{ $totalDuration->i }}
                                                            دقيقة</strong>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويبة سجل الحضور الجديدة -->
                <div class="tab-pane fade" id="attendance-record" role="tabpanel" aria-labelledby="attendance-record-tab">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="card-title">سجل الحضور الشامل</h4>
                            <div class="card-tools">
                                <form method="GET" class="form-inline">
                                    <div class="form-group mr-2">
                                        <label for="filter_month" class="mr-1">الشهر:</label>
                                        <select name="filter_month" id="filter_month" class="form-control form-control-sm">
                                            <option value="">جميع الأشهر</option>
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}" {{ request('filter_month') == $i ? 'selected' : '' }}>
                                                    {{ \Carbon\Carbon::create()->month($i)->locale('ar')->translatedFormat('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <label for="filter_year" class="mr-1">السنة:</label>
                                        <select name="filter_year" id="filter_year" class="form-control form-control-sm">
                                            @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                                <option value="{{ $year }}" {{ request('filter_year', date('Y')) == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">تصفية</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(isset($attendanceRecords) && count($attendanceRecords) > 0)
                                <!-- إحصائيات سريعة -->
                                @php
                                    $totalDays = count($attendanceRecords);
                                    $presentDays = $attendanceRecords->where('status', 'present')->count();
                                    $absentDays = $attendanceRecords->where('status', 'absent')->count();
                                    $leaveDays = $attendanceRecords->where('status', 'leave')->count();
                                    $totalWorkedHours = 0;

                                    foreach($attendanceRecords->where('status', 'present') as $record) {
                                        if($record->login_time && $record->logout_time) {
                                            $login = \Carbon\Carbon::parse($record->login_time);
                                            $logout = \Carbon\Carbon::parse($record->logout_time);
                                            $totalWorkedHours += $login->diffInHours($logout, false);
                                        }
                                    }
                                @endphp

                                <div class="row mb-4">
                                    <div class="col-md-3 col-sm-6">
                                        <div class="card bg-success text-white text-center">
                                            <div class="card-body">
                                                <h3>{{ $presentDays }}</h3>
                                                <p class="mb-0">أيام الحضور</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="card bg-danger text-white text-center">
                                            <div class="card-body">
                                                <h3>{{ $absentDays }}</h3>
                                                <p class="mb-0">أيام الغياب</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="card bg-secondary text-white text-center">
                                            <div class="card-body">
                                                <h3>{{ $leaveDays }}</h3>
                                                <p class="mb-0">أيام الإجازة</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="card bg-info text-white text-center">
                                            <div class="card-body">
                                                <h3>{{ number_format($totalWorkedHours, 1) }}</h3>
                                                <p class="mb-0">إجمالي ساعات العمل</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- جدول سجل الحضور -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead style="background: #f8f8f8">
                                            <tr>
                                                <th width="15%">التاريخ</th>
                                                <th width="10%">اليوم</th>
                                                <th width="12%">الحالة</th>
                                                <th width="12%">وقت الدخول</th>
                                                <th width="12%">وقت الخروج</th>
                                                <th width="12%">ساعات العمل</th>
                                                <th width="12%">التأخير</th>
                                                <th width="15%">ملاحظات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($attendanceRecords as $record)
                                                @php
                                                    $attendanceDate = \Carbon\Carbon::parse($record->attendance_date);
                                                    $workedHours = 0;
                                                    $lateMinutes = 0;

                                                    if($record->status == 'present' && $record->login_time && $record->logout_time) {
                                                        $login = \Carbon\Carbon::parse($record->login_time);
                                                        $logout = \Carbon\Carbon::parse($record->logout_time);
                                                        $workedHours = $login->diffInMinutes($logout) / 60;

                                                        // حساب التأخير إذا كان هناك وقت بداية وردية
                                                        if($record->start_shift) {
                                                            $shiftStart = \Carbon\Carbon::parse($record->start_shift);
                                                            if($login->isAfter($shiftStart)) {
                                                                $lateMinutes = $shiftStart->diffInMinutes($login);
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $attendanceDate->locale('ar')->translatedFormat('d/m/Y') }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ $attendanceDate->locale('ar')->translatedFormat('l') }}
                                                    </td>
                                                    <td>
                                                        @if ($record->status == 'present')
                                                            <span class="badge badge-success">
                                                                <i class="fa fa-check"></i> حاضر
                                                            </span>
                                                        @elseif($record->status == 'leave')
                                                            <span class="badge badge-secondary">
                                                                <i class="fa fa-calendar"></i> إجازة
                                                            </span>
                                                        @else
                                                            <span class="badge badge-danger">
                                                                <i class="fa fa-times"></i> غياب
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($record->login_time)
                                                            <span class="text-success">
                                                                <i class="fa fa-sign-in-alt"></i>
                                                                {{ \Carbon\Carbon::parse($record->login_time)->format('H:i') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">--</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($record->logout_time)
                                                            <span class="text-danger">
                                                                <i class="fa fa-sign-out-alt"></i>
                                                                {{ \Carbon\Carbon::parse($record->logout_time)->format('H:i') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">--</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($workedHours > 0)
                                                            <span class="badge badge-info">
                                                                {{ number_format($workedHours, 1) }} ساعة
                                                            </span>
                                                        @else
                                                            <span class="text-muted">--</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($lateMinutes > 0)
                                                            <span class="badge badge-warning">
                                                                <i class="fa fa-clock"></i>
                                                                {{ $lateMinutes }} دقيقة
                                                            </span>
                                                        @elseif($record->status == 'present')
                                                            <span class="badge badge-success">
                                                                <i class="fa fa-thumbs-up"></i> في الوقت
                                                            </span>
                                                        @else
                                                            <span class="text-muted">--</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($record->notes)
                                                            <small class="text-muted">{{ Str::limit($record->notes, 50) }}</small>
                                                        @else
                                                            <span class="text-muted">لا توجد ملاحظات</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination إذا كانت مطلوبة -->
                                @if(method_exists($attendanceRecords, 'links'))
                                    <div class="d-flex justify-content-center">
                                        {{ $attendanceRecords->links() }}
                                    </div>
                                @endif

                            @else
                                <div class="alert alert-info text-center" role="alert">
                                    <i class="fa fa-info-circle fa-2x mb-2"></i>
                                    <p class="mb-0">لا توجد سجلات حضور لهذا الموظف حتى الآن!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- سجل النشاطات -->
                <div class="tab-pane" id="activity-log" role="tabpanel">
                    <div class="row mt-4">
                        <div class="col-12">
                            @if ($logs && count($logs) > 0)
                                @php
                                    $previousDate = null;
                                @endphp

                                @foreach ($logs as $date => $dayLogs)
                                    @php
                                        $currentDate = \Carbon\Carbon::parse($date);
                                        $diffInDays = $previousDate ? $previousDate->diffInDays($currentDate) : 0;
                                    @endphp

                                    @if ($diffInDays > 7)
                                        <div class="timeline-date">
                                            <h4>{{ $currentDate->format('Y-m-d') }}</h4>
                                        </div>
                                    @endif

                                    <div class="timeline-day">{{ $currentDate->translatedFormat('l') }}</div>

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
                                <div class="alert alert-danger text-xl-center" role="alert">
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
    <div class="modal fade text-left" id="modal_DELETE{{ $attendance_day->id }}" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #EA5455 !important;">
                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف يوم الحضور</h4>
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
                    <a href="{{ route('attendanceDays.delete', $attendance_day->id) }}"
                        class="btn btn-danger waves-effect waves-light">تأكيد</a>
                </div>
            </div>
        </div>
    </div>
    <!--end delete-->

@endsection
