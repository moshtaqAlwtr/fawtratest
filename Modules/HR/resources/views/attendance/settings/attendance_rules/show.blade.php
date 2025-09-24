@extends('master')

@section('title', 'تفاصيل قاعدة الحضور')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تفاصيل قاعدة الحضور</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('attendance-rules.index') }}">قواعد الحضور</a></li>
                            <li class="breadcrumb-item active">{{ $attendanceRule->name }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="fa fa-cogs me-2"></i>
                {{ $attendanceRule->name }}
                <span class="badge {{ $attendanceRule->status == 'active' ? 'badge-success' : 'badge-secondary' }} ms-2">
                    {{ $attendanceRule->status_label }}
                </span>
            </h4>
            <div class="card-actions">
                <a href="#" class="btn btn-outline-danger btn-sm waves-effect waves-light"
                   data-toggle="modal" data-target="#modal_DELETE{{ $attendanceRule->id }}">
                    <i class="fa fa-trash me-1"></i>حذف
                </a>
                <a href="{{ route('attendance-rules.edit', $attendanceRule->id) }}"
                   class="btn btn-outline-primary btn-sm waves-effect waves-light">
                    <i class="fa fa-edit me-1"></i>تعديل
                </a>
                <a href="{{ route('attendance-rules.index') }}"
                   class="btn btn-outline-secondary btn-sm waves-effect waves-light">
                    <i class="fa fa-arrow-right me-1"></i>العودة للقائمة
                </a>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                        aria-controls="details" aria-selected="true">
                        <i class="fa fa-info-circle me-1"></i>تفاصيل القاعدة
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log" role="tab"
                        aria-controls="activity-log" aria-selected="false">
                        <i class="fa fa-history me-1"></i>سجل النشاطات
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- تفاصيل القاعدة -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fa fa-info me-2"></i>المعلومات الأساسية
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td width="40%"><strong>اسم القاعدة:</strong></td>
                                                <td>{{ $attendanceRule->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>الوصف:</strong></td>
                                                <td>{{ $attendanceRule->description ?? 'لا يوجد وصف' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>اللون:</strong></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div style="width: 24px; height: 24px; background-color: {{ $attendanceRule->color }};
                                                                    border-radius: 4px; margin-left: 8px; border: 1px solid #dee2e6;">
                                                        </div>
                                                        <code>{{ $attendanceRule->color }}</code>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>الوردية المرتبطة:</strong></td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        <i class="fa fa-clock-o me-1"></i>
                                                        {{ $attendanceRule->shift->name }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>الحالة:</strong></td>
                                                <td>
                                                    <span class="badge {{ $attendanceRule->status == 'active' ? 'badge-success' : 'badge-secondary' }} badge-lg">
                                                        <i class="fa fa-{{ $attendanceRule->status == 'active' ? 'check' : 'ban' }} me-1"></i>
                                                        {{ $attendanceRule->status_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>تاريخ الإنشاء:</strong></td>
                                                <td>
                                                    <i class="fa fa-calendar me-1"></i>
                                                    {{ $attendanceRule->created_at->format('d/m/Y H:i') }}
                                                    <br>
                                                    <small class="text-muted">{{ $attendanceRule->created_at->diffForHumans() }}</small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fa fa-cogs me-2"></i>إعدادات القاعدة
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td width="50%"><strong>دقائق التأخير المسموحة:</strong></td>
                                                <td>
                                                    <span class="badge badge-warning">
                                                        {{ $attendanceRule->late_minutes_allowed ?? 15 }} دقيقة
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>المغادرة المبكرة المسموحة:</strong></td>
                                                <td>
                                                    <span class="badge badge-warning">
                                                        {{ $attendanceRule->early_departure_minutes_allowed ?? 15 }} دقيقة
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>مدة الاستراحة:</strong></td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ $attendanceRule->break_duration_minutes ?? 60 }} دقيقة
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>فترة السماح:</strong></td>
                                                <td>
                                                    <span class="badge badge-primary">
                                                        {{ $attendanceRule->grace_period_minutes ?? 5 }} دقيقة
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>خصم لكل دقيقة تأخير:</strong></td>
                                                <td>
                                                    <span class="badge badge-danger">
                                                        {{ $attendanceRule->deduction_per_late_minute ?? 0 }} ريال
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>ساعات العمل الدنيا:</strong></td>
                                                <td>
                                                    <span class="badge badge-success">
                                                        {{ $attendanceRule->minimum_work_hours ?? 8 }} ساعة
                                                    </span>
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
                                        {{ $currentDate->locale('ar')->translatedFormat('l، d F Y') }}
                                    </div>

                                    <ul class="timeline">
                                        @foreach ($dayLogs as $log)
                                            @if ($log)
                                                <li class="timeline-item">
                                                    <div class="timeline-content">
                                                        <div class="timeline-header d-flex justify-content-between align-items-center">
                                                            <div class="time">
                                                                <i class="far fa-clock me-1"></i>
                                                                {{ $log->created_at->format('H:i:s') }}
                                                            </div>
                                                            <span class="badge badge-{{ $log->log_level ?? 'info' }}">
                                                                {{ $log->log_name ?? 'نشاط' }}
                                                            </span>
                                                        </div>
                                                        <div class="timeline-body mt-2">
                                                            <div class="user-info mb-2">
                                                                <strong>
                                                                    <i class="fa fa-user me-1"></i>
                                                                    {{ $log->user->name ?? 'مستخدم غير معروف' }}
                                                                </strong>
                                                                <span class="text-muted ms-2">
                                                                    <i class="fa fa-building me-1"></i>
                                                                    {{ $log->user->branch->name ?? 'فرع غير معروف' }}
                                                                </span>
                                                            </div>
                                                            <div class="activity-description">
                                                                {!! Str::markdown($log->description ?? 'لا يوجد وصف') !!}
                                                            </div>
                                                            @if($log->properties && count($log->properties) > 0)
                                                                <div class="activity-properties mt-2">
                                                                    <small class="text-muted">
                                                                        <i class="fa fa-info-circle me-1"></i>
                                                                        تفاصيل إضافية متاحة
                                                                    </small>
                                                                </div>
                                                            @endif
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
                                    <h5>لا توجد سجلات نشاط</h5>
                                    <p class="mb-0">لا توجد سجلات نشاط لهذه القاعدة حتى الآن!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal حذف -->
    <div class="modal fade text-left" id="modal_DELETE{{ $attendanceRule->id }}" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #EA5455 !important;">
                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">
                        <i class="fa fa-trash me-2"></i>حذف {{ $attendanceRule->name }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: #FFFFFF">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        <strong>تحذير!</strong>
                    </div>
                    <p class="mb-3">
                        هل أنت متأكد من حذف قاعدة الحضور
                        <strong>"{{ $attendanceRule->name }}"</strong>؟
                    </p>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary waves-effect waves-light" data-dismiss="modal">
                        <i class="fa fa-times me-1"></i>إلغاء
                    </button>

                </div>
            </div>
        </div>
    </div>

    <!-- نموذج الحذف المخفي -->
    <form id="delete-form-{{ $attendanceRule->id }}"
          action="{{ route('attendance-rules.destroy', $attendanceRule->id) }}"
          method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // تفعيل tooltips
    $('[title]').tooltip();

    // إضافة تأثيرات للتابات
    $('.nav-link').on('click', function() {
        const target = $(this).attr('href');
        $('.tab-pane').removeClass('show active');
        $(target).addClass('show active');
    });

    // تأثيرات hover للجداول
    $('.table tbody tr').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );

    // Display messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: '{{ session('error') }}',
            confirmButtonText: 'موافق'
        });
    @endif
});

// دالة تغيير حالة القاعدة من صفحة التفاصيل
function toggleRuleStatus(ruleId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const statusText = newStatus === 'active' ? 'تفعيل' : 'إلغاء تفعيل';

    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: `سيتم ${statusText} قاعدة الحضور`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: `نعم، ${statusText}`,
        cancelButtonText: 'إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/attendance-rules/${ruleId}/toggle-status`,
                method: 'PATCH',
                data: {
                    _token: $('input[name="_token"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم بنجاح!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // إعادة تحميل الصفحة لتحديث الحالة
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        Swal.fire('خطأ!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('خطأ!', 'حدث خطأ أثناء تغيير الحالة', 'error');
                }
            });
        }
    });
}
</script>
@endpush