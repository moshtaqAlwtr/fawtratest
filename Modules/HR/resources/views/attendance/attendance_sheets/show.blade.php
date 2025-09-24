@extends('master')

@section('title')
    عرض دفتر الحضور
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">دفتر الحضور #{{ $attendanceSheet->id }}</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('attendance_sheets.index') }}">دفاتر الحضور</a>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ \Carbon\Carbon::parse($attendanceSheet->from_date)->locale('ar')->translatedFormat('d/m/Y') }}
                                -
                                {{ \Carbon\Carbon::parse($attendanceSheet->to_date)->locale('ar')->translatedFormat('d/m/Y') }}
                            </li>
                            <li class="breadcrumb-item active">
                                @if ($attendanceSheet->use_rules == 1)
                                    <span class="badge badge-primary">قائم على القواعد</span>
                                @else
                                    <span class="badge badge-secondary">موظفين محددين</span>
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
                data-target="#modal_DELETE{{ $attendanceSheet->id }}">حذف الدفتر <i class="fa fa-trash"></i></a>
            <a href="{{ route('attendance_sheets.edit', $attendanceSheet->id) }}"
                class="btn btn-outline-primary btn-sm waves-effect waves-light">تعديل <i class="fa fa-edit"></i></a>

        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                        aria-controls="details" aria-selected="true">تفاصيل الدفتر</a>
                </li>

                <!-- تبويبة أيام الحضور -->
                <li class="nav-item">
                    <a class="nav-link" id="attendance-days-tab" data-toggle="tab" href="#attendance-days" role="tab"
                        aria-controls="attendance-days" aria-selected="false">أيام الحضور</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="statistics-tab" data-toggle="tab" href="#statistics" role="tab"
                        aria-controls="statistics" aria-selected="false">الإحصائيات</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log" role="tab"
                        aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- تفاصيل الدفتر -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="row">
                                <table class="table">
                                    <thead style="background: #f8f8f8">
                                        <tr>
                                            <th style="width: 70%">معلومات الدفتر</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p><small>رقم الدفتر</small>: </p>
                                                <strong>#{{ $attendanceSheet->id }}</strong>
                                            </td>
                                            <td>
                                                <p><small>نوع الدفتر</small>: </p>
                                                <strong>
                                                    @if ($attendanceSheet->use_rules == 1)
                                                        <span class="badge badge-primary">قائم على القواعد</span>
                                                    @else
                                                        <span class="badge badge-secondary">موظفين محددين</span>
                                                    @endif
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><small>من تاريخ</small>: </p>
                                                <strong>{{ \Carbon\Carbon::parse($attendanceSheet->from_date)->locale('ar')->translatedFormat('l، d F Y') }}</strong>
                                            </td>
                                            <td>
                                                <p><small>إلى تاريخ</small>: </p>
                                                <strong>{{ \Carbon\Carbon::parse($attendanceSheet->to_date)->locale('ar')->translatedFormat('l، d F Y') }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><small>تاريخ الإنشاء</small>: </p>
                                                <strong>{{ $attendanceSheet->created_at->locale('ar')->translatedFormat('l، d F Y - H:i') }}</strong>
                                            </td>
                                            <td>
                                                <p><small>عدد الموظفين</small>: </p>
                                                <strong>{{ $attendanceSheet->employees->count() }} موظف</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                @if ($attendanceSheet->use_rules == 1)
                                    <table class="table">
                                        <thead style="background: #f8f8f8">
                                            <tr>
                                                <th colspan="2">القواعد المطبقة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p><small>الفرع</small>: </p>
                                                    <strong>{{ $attendanceSheet->branch->name ?? 'غير محدد' }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>القسم</small>: </p>
                                                    <strong>{{ $attendanceSheet->department->name ?? 'غير محدد' }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><small>المسمى الوظيفي</small>: </p>
                                                    <strong>{{ $attendanceSheet->jobTitle->name ?? 'غير محدد' }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>الوردية</small>: </p>
                                                    <strong>{{ $attendanceSheet->shift->name ?? 'غير محدد' }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @endif

                                <!-- قائمة الموظفين -->
                                <table class="table">
                                    <thead style="background: #f8f8f8">
                                        <tr>
                                            <th colspan="2">الموظفين في الدفتر</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attendanceSheet->employees as $employee)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar mr-2">
                                                            <span
                                                                class="avatar-content">{{ substr($employee->full_name, 0, 2) }}</span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $employee->full_name }}</h6>
                                                            <small
                                                                class="text-muted">{{ $employee->branch->name ?? 'غير محدد' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-right">
                                                    <span
                                                        class="badge badge-light">{{ $employee->department->name ?? 'غير محدد' }}</span>
                                                    <span
                                                        class="badge badge-light">{{ $employee->job_title->name ?? 'غير محدد' }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">لا يوجد موظفين في هذا
                                                    الدفتر</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويبة أيام الحضور -->
                <div class="tab-pane fade" id="attendance-days" role="tabpanel" aria-labelledby="attendance-days-tab">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="card-title">أيام الحضور التفصيلية</h4>
                            <div class="card-tools">
                                <div class="form-inline">
                                    <div class="form-group mr-2">
                                        <select id="employee_filter" class="form-control form-control-sm">
                                            <option value="">جميع الموظفين</option>
                                            @foreach ($attendanceSheet->employees as $emp)
                                                <option value="{{ $emp->id }}">
                                                    {{ $emp->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <select id="status_filter" class="form-control form-control-sm">
                                            <option value="">جميع الحالات</option>
                                            <option value="present">حاضر</option>
                                            <option value="absent">غائب</option>
                                            <option value="leave">إجازة</option>
                                            <option value="late">متأخر</option>
                                        </select>
                                    </div>
                                    <button id="reset_filters" class="btn btn-secondary btn-sm">
                                        <i class="fa fa-refresh"></i> إعادة تعيين
                                    </button>
                                    <div class="ml-2">
                                        <div class="spinner-border spinner-border-sm d-none" id="loading_spinner"
                                            role="status">
                                            <span class="sr-only">جاري التحميل...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (isset($attendanceDays) && count($attendanceDays) > 0)

                                <!-- الإحصائيات السريعة -->
                                @php
                                    $totalDays = count($attendanceDays);
                                    $presentDays = $attendanceDays->where('status', 'present')->count();
                                    $absentDays = $attendanceDays->where('status', 'absent')->count();
                                    $leaveDays = $attendanceDays->where('status', 'leave')->count();
                                    $lateDays = $attendanceDays->where('status', 'late')->count();
                                @endphp

                                <div class="row mb-4">
                                    <div class="col-md-2 col-sm-4">
                                        <div class="card bg-primary text-white text-center">
                                            <div class="card-body">
                                                <h3>{{ $totalDays }}</h3>
                                                <p class="mb-0">إجمالي الأيام</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4">
                                        <div class="card bg-success text-white text-center">
                                            <div class="card-body">
                                                <h3>{{ $presentDays }}</h3>
                                                <p class="mb-0">حضور</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4">
                                        <div class="card bg-danger text-white text-center">
                                            <div class="card-body">
                                                <h3>{{ $absentDays }}</h3>
                                                <p class="mb-0">غياب</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4">
                                        <div class="card bg-secondary text-white text-center">
                                            <div class="card-body">
                                                <h3>{{ $leaveDays }}</h3>
                                                <p class="mb-0">إجازات</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4">
                                        <div class="card bg-warning text-white text-center">
                                            <div class="card-body">
                                                <h3>{{ $lateDays }}</h3>
                                                <p class="mb-0">تأخير</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4">
                                        <div class="card bg-info text-white text-center">
                                            <div class="card-body">
                                                <h3>{{ number_format(($presentDays / max($totalDays, 1)) * 100, 1) }}%</h3>
                                                <p class="mb-0">نسبة الحضور</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- جدول أيام الحضور -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead style="background: #f8f8f8">
                                            <tr>
                                                <th width="15%">الموظف</th>
                                                <th width="12%">التاريخ</th>
                                                <th width="8%">اليوم</th>
                                                <th width="10%">الحالة</th>
                                                <th width="10%">وقت الدخول</th>
                                                <th width="10%">وقت الخروج</th>
                                                <th width="10%">ساعات العمل</th>
                                                <th width="10%">التأخير</th>
                                                <th width="15%">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($attendanceDays as $day)
                                                @php
                                                    $attendanceDate = \Carbon\Carbon::parse($day->attendance_date);
                                                    $workedHours = 0;
                                                    $lateMinutes = 0;

                                                    if (
                                                        $day->status == 'present' &&
                                                        $day->login_time &&
                                                        $day->logout_time
                                                    ) {
                                                        $login = \Carbon\Carbon::parse($day->login_time);
                                                        $logout = \Carbon\Carbon::parse($day->logout_time);
                                                        $workedHours = $login->diffInMinutes($logout) / 60;

                                                        // حساب التأخير
                                                        if ($day->start_shift) {
                                                            $shiftStart = \Carbon\Carbon::parse($day->start_shift);
                                                            if ($login->isAfter($shiftStart)) {
                                                                $lateMinutes = $shiftStart->diffInMinutes($login);
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-sm mr-2">
                                                                <span
                                                                    class="avatar-content">{{ substr($day->employee->full_name, 0, 2) }}</span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">{{ $day->employee->full_name }}</h6>
                                                                <small
                                                                    class="text-muted">{{ $day->employee->employee_number ?? 'غير محدد' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $attendanceDate->locale('ar')->translatedFormat('d/m/Y') }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ $attendanceDate->locale('ar')->translatedFormat('l') }}
                                                    </td>
                                                    <td>
                                                        @if ($day->status == 'present')
                                                            <span class="badge badge-success">
                                                                <i class="fa fa-check"></i> حاضر
                                                            </span>
                                                        @elseif($day->status == 'absent')
                                                            <span class="badge badge-secondary">
                                                                <i class="fa fa-calendar"></i> إجازة
                                                            </span>
                                                        @elseif($day->status == 'late')
                                                            <span class="badge badge-warning">
                                                                <i class="fa fa-clock"></i> متأخر
                                                            </span>
                                                        @else
                                                            <span class="badge badge-danger">
                                                                <i class="fa fa-times"></i> غياب
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($day->login_time)
                                                            <span class="text-success">
                                                                <i class="fa fa-sign-in-alt"></i>
                                                                {{ \Carbon\Carbon::parse($day->login_time)->format('H:i') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">--</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($day->logout_time)
                                                            <span class="text-danger">
                                                                <i class="fa fa-sign-out-alt"></i>
                                                                {{ \Carbon\Carbon::parse($day->logout_time)->format('H:i') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">--</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($workedHours > 0)
                                                            <span class="badge badge-info">
                                                                {{ number_format($workedHours, 1) }} ساعة
                                                            </span>
                                                        @else
                                                            <span class="text-muted">--</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($lateMinutes > 0)
                                                            <span class="badge badge-warning">
                                                                <i class="fa fa-clock"></i>
                                                                {{ $lateMinutes }} دقيقة
                                                            </span>
                                                        @elseif($day->status == 'present')
                                                            <span class="badge badge-success">
                                                                <i class="fa fa-thumbs-up"></i> في الوقت
                                                            </span>
                                                        @else
                                                            <span class="text-muted">--</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('attendanceDays.show', $day->id) }}"
                                                                class="btn btn-sm btn-outline-primary"
                                                                title="عرض التفاصيل">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('attendanceDays.edit', $day->id) }}"
                                                                class="btn btn-sm btn-outline-secondary" title="تعديل">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                data-toggle="modal"
                                                                data-target="#modal_DELETE_DAY{{ $day->id }}"
                                                                title="حذف">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                @if (method_exists($attendanceDays, 'links'))
                                    <div class="d-flex justify-content-center">
                                        {{ $attendanceDays->appends(request()->query())->links() }}
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info text-center" role="alert">
                                    <i class="fa fa-info-circle fa-2x mb-2"></i>
                                    <p class="mb-0">لا توجد أيام حضور لهذا الدفتر حتى الآن!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- تبويبة الإحصائيات -->
                <div class="tab-pane fade" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="card-title">إحصائيات الحضور</h4>
                        </div>
                        <div class="card-body">
                            @if (isset($attendanceDays) && count($attendanceDays) > 0)
                                @php
                                    $employeeStats = [];
                                    foreach ($attendanceSheet->employees as $employee) {
                                        $employeeDays = $attendanceDays->where('employee_id', $employee->id);
                                        $employeeStats[$employee->id] = [
                                            'name' => $employee->full_name,
                                            'total' => $employeeDays->count(),
                                            'present' => $employeeDays->where('status', 'present')->count(),
                                            'absent' => $employeeDays->where('status', 'absent')->count(),
                                            'leave' => $employeeDays->where('status', 'leave')->count(),
                                            'late' => $employeeDays->where('status', 'late')->count(),
                                        ];
                                    }
                                @endphp

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead style="background: #f8f8f8">
                                            <tr>
                                                <th>الموظف</th>
                                                <th>إجمالي الأيام</th>
                                                <th>أيام الحضور</th>
                                                <th>أيام الغياب</th>
                                                <th>أيام الإجازة</th>
                                                <th>أيام التأخير</th>
                                                <th>نسبة الحضور</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($employeeStats as $stat)
                                                <tr>
                                                    <td><strong>{{ $stat['name'] }}</strong></td>
                                                    <td>{{ $stat['total'] }}</td>
                                                    <td>
                                                        <span class="badge badge-success">{{ $stat['present'] }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-danger">{{ $stat['absent'] }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-secondary">{{ $stat['leave'] }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-warning">{{ $stat['late'] }}</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $attendanceRate =
                                                                $stat['total'] > 0
                                                                    ? ($stat['present'] / $stat['total']) * 100
                                                                    : 0;
                                                        @endphp
                                                        <span
                                                            class="badge {{ $attendanceRate >= 90 ? 'badge-success' : ($attendanceRate >= 70 ? 'badge-warning' : 'badge-danger') }}">
                                                            {{ number_format($attendanceRate, 1) }}%
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info text-center" role="alert">
                                    <i class="fa fa-chart-bar fa-2x mb-2"></i>
                                    <p class="mb-0">لا توجد بيانات لعرض الإحصائيات!</p>
                                </div>
                            @endif
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
                                <div class="alert alert-danger text-center" role="alert">
                                    <p class="mb-0">لا توجد سجلات نشاط حتى الآن!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal delete attendance sheet -->
    <div class="modal fade text-left" id="modal_DELETE{{ $attendanceSheet->id }}" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #EA5455 !important;">
                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف دفتر الحضور</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: #DC3545">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <strong><i class="fa fa-exclamation-triangle"></i> تحذير!</strong>
                        <p>سيتم حذف دفتر الحضور وجميع أيام الحضور المرتبطة به
                            ({{ $attendanceSheet->attendanceDays->count() ?? 0 }} سجل).</p>
                        <p class="mb-0"><strong>هذا الإجراء لا يمكن التراجع عنه!</strong></p>
                    </div>
                    <p><strong>هل أنت متأكد من أنك تريد الحذف؟</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect waves-light"
                        data-dismiss="modal">إلغاء</button>
                    <a href="{{ route('attendance_sheets.delete', $attendanceSheet->id) }}"
                        class="btn btn-danger waves-effect waves-light">
                        <i class="fa fa-trash"></i> تأكيد الحذف
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal delete individual attendance days -->
    @if (isset($attendanceDays))
        @foreach ($attendanceDays as $day)
            <div class="modal fade text-left" id="modal_DELETE_DAY{{ $day->id }}" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel{{ $day->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #EA5455 !important;">
                            <h4 class="modal-title" id="myModalLabel{{ $day->id }}" style="color: #FFFFFF">حذف يوم
                                الحضور</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" style="color: #DC3545">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>الموظف:</strong> {{ $day->employee->full_name }}</p>
                            <p><strong>التاريخ:</strong>
                                {{ \Carbon\Carbon::parse($day->attendance_date)->locale('ar')->translatedFormat('l، d F Y') }}
                            </p>
                            <hr>
                            <strong>هل أنت متأكد من أنك تريد حذف سجل هذا اليوم؟</strong>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light waves-effect waves-light"
                                data-dismiss="modal">إلغاء</button>
                            <a href="{{ route('attendanceDays.delete', $day->id) }}"
                                class="btn btn-danger waves-effect waves-light">
                                <i class="fa fa-trash"></i> تأكيد الحذف
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let currentFilters = {
                employee_filter: '',
                status_filter: ''
            };

            // تحميل البيانات الأولية
            loadAttendanceDays();

            // التعامل مع الفلاتر
            $('#employee_filter, #status_filter').on('change', function() {
                currentFilters = {
                    employee_filter: $('#employee_filter').val(),
                    status_filter: $('#status_filter').val()
                };
                currentPage = 1; // إعادة تعيين الصفحة للأولى
                loadAttendanceDays();
            });

            // إعادة تعيين الفلاتر
            $('#reset_filters').on('click', function() {
                $('#employee_filter').val('');
                $('#status_filter').val('');
                currentFilters = {
                    employee_filter: '',
                    status_filter: ''
                };
                currentPage = 1;
                loadAttendanceDays();
            });

            // دالة تحميل أيام الحضور
            function loadAttendanceDays(page = 1) {
                // إظهار loading
                $('#loading_spinner').removeClass('d-none');

                $.ajax({
                    url: '',
                    method: 'GET',
                    data: {
                        employee_filter: currentFilters.employee_filter,
                        status_filter: currentFilters.status_filter,
                        page: page
                    },
                    success: function(response) {
                        // تحديث الإحصائيات
                        $('#statistics_cards').html(response.statistics);

                        // تحديث الجدول
                        $('#attendance_days_table').html(response.table);

                        // تحديث pagination
                        $('#pagination_container').html(response.pagination);

                        // إضافة tooltips للأزرار الجديدة
                        $('[title]').tooltip();

                        // إخفاء loading
                        $('#loading_spinner').addClass('d-none');

                        // إعادة ربط أحداث الحذف للعناصر الجديدة
                        bindDeleteEvents();

                        // إعادة ربط أحداث pagination
                        bindPaginationEvents();
                    },
                    error: function(xhr) {
                        $('#loading_spinner').addClass('d-none');
                        console.error('خطأ في تحميل البيانات:', xhr);

                        // عرض رسالة خطأ
                        $('#attendance_days_table').html(
                            '<tr><td colspan="9" class="text-center text-danger">' +
                            '<i class="fa fa-exclamation-triangle"></i> حدث خطأ في تحميل البيانات' +
                            '</td></tr>'
                        );
                    }
                });
            }

            // ربط أحداث الحذف
            function bindDeleteEvents() {
                $('.delete-attendance-day').off('click').on('click', function(e) {
                    e.preventDefault();

                    const dayId = $(this).data('day-id');
                    const employeeName = $(this).data('employee-name');
                    const attendanceDate = $(this).data('attendance-date');

                    Swal.fire({
                        title: 'حذف يوم الحضور',
                        html: `
                            <p><strong>الموظف:</strong> ${employeeName}</p>
                            <p><strong>التاريخ:</strong> ${attendanceDate}</p>
                            <hr>
                            <strong>هل أنت متأكد من أنك تريد حذف سجل هذا اليوم؟</strong>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteAttendanceDay(dayId);
                        }
                    });
                });
            }

            // ربط أحداث pagination
            function bindPaginationEvents() {
                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();
                    const url = $(this).attr('href');
                    const page = new URL(url).searchParams.get('page');
                    if (page) {
                        currentPage = page;
                        loadAttendanceDays(page);
                    }
                });
            }

            // حذف يوم الحضور
            function deleteAttendanceDay(dayId) {
                Swal.fire({
                    title: 'جاري الحذف...',
                    text: 'يرجى الانتظار',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `{{ route('attendanceDays.delete', ':id') }}`.replace(':id', dayId),
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'تم الحذف!',
                            text: 'تم حذف سجل الحضور بنجاح',
                            icon: 'success',
                            timer: 2000,
                            timerProgressBar: true
                        });

                        // إعادة تحميل البيانات
                        loadAttendanceDays(currentPage);
                    },
                    error: function(xhr) {
                        let errorMessage = 'حدث خطأ أثناء الحذف';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'خطأ!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    }
                });
            }

            // التعامل مع التبويبات وحفظ الحالة في URL
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var targetTab = $(e.target).attr("href");
                var url = window.location.href;
                var newUrl = url.split('#')[0] + targetTab;
                window.history.replaceState(null, null, newUrl);

                // إذا تم تفعيل تبويبة أيام الحضور، تأكد من تحميل البيانات
                if (targetTab === '#attendance-days') {
                    setTimeout(function() {
                        if ($('#attendance_days_table').children().length === 0) {
                            loadAttendanceDays();
                        }
                    }, 100);
                }
            });

            // تفعيل التبويبة من URL إذا كانت موجودة
            var hash = window.location.hash;
            if (hash) {
                $('.nav-tabs a[href="' + hash + '"]').tab('show');
            }

            // إذا كان هناك معامل tab في URL
            var urlParams = new URLSearchParams(window.location.search);
            var tabParam = urlParams.get('tab');
            if (tabParam) {
                $('.nav-tabs a[href="#' + tabParam + '"]').tab('show');
            }

            // إضافة تأثيرات للجداول
            $(document).on('mouseenter', '.table-hover tbody tr', function() {
                $(this).addClass('table-active');
            }).on('mouseleave', '.table-hover tbody tr', function() {
                $(this).removeClass('table-active');
            });

            // تأكيد إضافي للحذف
            $('.btn-outline-danger').on('click', function(e) {
                var modalTarget = $(this).data('target');
                if (!modalTarget && !$(this).hasClass('delete-attendance-day')) {
                    e.preventDefault();
                    var result = confirm('هل أنت متأكد من أنك تريد المتابعة؟');
                    if (!result) {
                        return false;
                    }
                }
            });

            // تحسين عرض الإحصائيات
            $(document).on('mouseenter',
                '.card.bg-primary, .card.bg-success, .card.bg-danger, .card.bg-secondary, .card.bg-warning, .card.bg-info',
                function() {
                    $(this).addClass('shadow-lg');
                }).on('mouseleave',
                '.card.bg-primary, .card.bg-success, .card.bg-danger, .card.bg-secondary, .card.bg-warning, .card.bg-info',
                function() {
                    $(this).removeClass('shadow-lg');
                });

            // إضافة loading للأزرار عند النقر
            $('.btn').on('click', function() {
                var $btn = $(this);
                if (!$btn.hasClass('btn-outline-danger') && !$btn.hasClass('delete-attendance-day')) {
                    $btn.addClass('disabled').append(' <i class="fa fa-spinner fa-spin"></i>');
                }
            });

            // إضافة SweetAlert2 إذا لم يكن موجوداً
            if (typeof Swal === 'undefined') {
                var script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                document.head.appendChild(script);
            }
        });
    </script>
@endsection
