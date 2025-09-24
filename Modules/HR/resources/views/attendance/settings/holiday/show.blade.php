@extends('master')

@section('title')
    عرض قائمة العطلات
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">قائمة العطلات #{{ $holiday_list->id }}</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('holiday_lists.index') }}">قوائم العطلات</a></li>
                            <li class="breadcrumb-item active">{{ $holiday_list->name }}</li>
                            <li class="breadcrumb-item active">
                                <span class="badge badge-primary">{{ $holiday_list->holidays()->count() }} عطلة</span>
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
                data-target="#modal_DELETE{{ $holiday_list->id }}">حذف القائمة <i class="fa fa-trash"></i></a>
            <a href="{{ route('holiday_lists.edit', $holiday_list->id) }}"
                class="btn btn-outline-primary btn-sm waves-effect waves-light">تعديل <i class="fa fa-edit"></i></a>
            <a href="{{ route('holiday_lists.holyday_employees', $holiday_list->id) }}"
                class="btn btn-outline-success btn-sm waves-effect waves-light">إدارة الموظفين <i class="fa fa-users"></i></a>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                        aria-controls="details" aria-selected="true">تفاصيل القائمة</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="holidays-tab" data-toggle="tab" href="#holidays" role="tab"
                        aria-controls="holidays" aria-selected="false">أيام العطلات</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="employees-tab" data-toggle="tab" href="#employees" role="tab"
                        aria-controls="employees" aria-selected="false">الموظفين المخصصين</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log" role="tab"
                        aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- تفاصيل القائمة -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="row">
                                <table class="table">
                                    <thead style="background: #f8f8f8">
                                        <tr>
                                            <th style="width: 70%">معلومات القائمة</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p><small>رقم القائمة</small>: </p>
                                                <strong>#{{ $holiday_list->id }}</strong>
                                            </td>
                                            <td>
                                                <p><small>اسم القائمة</small>: </p>
                                                <strong>{{ $holiday_list->name }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><small>عدد العطلات</small>: </p>
                                                <strong>{{ $holiday_list->holidays()->count() }} عطلة</strong>
                                            </td>
                                            <td>
                                                <p><small>عدد الموظفين المخصصين</small>: </p>
                                                <span id="assignedEmployeesCount">
                                                    <div class="spinner-border spinner-border-sm" role="status">
                                                        <span class="sr-only">جاري التحميل...</span>
                                                    </div>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><small>تاريخ الإنشاء</small>: </p>
                                                <strong>{{ $holiday_list->created_at->locale('ar')->translatedFormat('l، d F Y - H:i') }}</strong>
                                            </td>
                                            <td>
                                                <p><small>آخر تحديث</small>: </p>
                                                <strong>{{ $holiday_list->updated_at->locale('ar')->translatedFormat('l، d F Y - H:i') }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أيام العطلات -->
                <div class="tab-pane fade" id="holidays" role="tabpanel" aria-labelledby="holidays-tab">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="card-title">أيام العطلات التفصيلية</h4>
                            <div class="card-tools">
                                <div class="form-inline">
                                    <div class="form-group mr-2">
                                        <input type="text" id="holiday_search" class="form-control form-control-sm"
                                               placeholder="البحث في العطلات...">
                                    </div>
                                    <div class="form-group mr-2">
                                        <select id="month_filter" class="form-control form-control-sm">
                                            <option value="">جميع الشهور</option>
                                            <option value="1">يناير</option>
                                            <option value="2">فبراير</option>
                                            <option value="3">مارس</option>
                                            <option value="4">أبريل</option>
                                            <option value="5">مايو</option>
                                            <option value="6">يونيو</option>
                                            <option value="7">يوليو</option>
                                            <option value="8">أغسطس</option>
                                            <option value="9">سبتمبر</option>
                                            <option value="10">أكتوبر</option>
                                            <option value="11">نوفمبر</option>
                                            <option value="12">ديسمبر</option>
                                        </select>
                                    </div>
                                    <button id="reset_holiday_filters" class="btn btn-secondary btn-sm">
                                        <i class="fa fa-refresh"></i> إعادة تعيين
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($holiday_list->holidays()->count() > 0)
                                <!-- الإحصائيات السريعة -->
                                <div id="holiday_statistics_cards">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-8">
                                                            <h4 class="mb-0">{{ $holiday_list->holidays()->count() }}</h4>
                                                            <p class="mb-0">إجمالي العطلات</p>
                                                        </div>
                                                        <div class="col-4 text-right">
                                                            <i class="fa fa-calendar-alt fa-2x"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-8">
                                                            <h4 class="mb-0" id="upcoming_holidays">
                                                                @php
                                                                    $upcomingCount = $holiday_list->holidays()->whereDate('holiday_date', '>', now())->count();
                                                                @endphp
                                                                {{ $upcomingCount }}
                                                            </h4>
                                                            <p class="mb-0">عطلات قادمة</p>
                                                        </div>
                                                        <div class="col-4 text-right">
                                                            <i class="fa fa-calendar-plus fa-2x"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-8">
                                                            <h4 class="mb-0" id="past_holidays">
                                                                @php
                                                                    $pastCount = $holiday_list->holidays()->whereDate('holiday_date', '<', now())->count();
                                                                @endphp
                                                                {{ $pastCount }}
                                                            </h4>
                                                            <p class="mb-0">عطلات سابقة</p>
                                                        </div>
                                                        <div class="col-4 text-right">
                                                            <i class="fa fa-calendar-check fa-2x"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-8">
                                                            <h4 class="mb-0" id="this_month_holidays">
                                                                @php
                                                                    $thisMonthCount = $holiday_list->holidays()->whereMonth('holiday_date', now()->month)->count();
                                                                @endphp
                                                                {{ $thisMonthCount }}
                                                            </h4>
                                                            <p class="mb-0">عطلات هذا الشهر</p>
                                                        </div>
                                                        <div class="col-4 text-right">
                                                            <i class="fa fa-calendar-day fa-2x"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- جدول العطلات -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead style="background: #f8f8f8">
                                            <tr>
                                                <th width="25%">اسم العطلة</th>
                                                <th width="15%">التاريخ</th>
                                                <th width="10%">اليوم</th>
                                                <th width="10%">الحالة</th>
                                                <th width="15%">المدة المتبقية</th>
                                                <th width="10%">النوع</th>
                                                <th width="15%">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody id="holidays_table">
                                            @foreach($holiday_list->holidays as $holiday)
                                            <tr class="holiday-row" data-name="{{ strtolower($holiday->named) }}"
                                                data-month="{{ \Carbon\Carbon::parse($holiday->holiday_date)->month }}">
                                                <td>
                                                    <strong>{{ $holiday->named }}</strong>
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($holiday->holiday_date)->locale('ar')->translatedFormat('d/m/Y') }}
                                                </td>
                                                <td>
                                                    <span class="badge badge-light">
                                                        {{ \Carbon\Carbon::parse($holiday->holiday_date)->locale('ar')->translatedFormat('l') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $holidayDate = \Carbon\Carbon::parse($holiday->holiday_date);
                                                        $now = \Carbon\Carbon::now();
                                                        $isToday = $holidayDate->isToday();
                                                        $isPast = $holidayDate->isPast();
                                                        $isFuture = $holidayDate->isFuture();
                                                    @endphp

                                                    @if($isToday)
                                                        <span class="badge badge-success">اليوم</span>
                                                    @elseif($isPast)
                                                        <span class="badge badge-secondary">انتهت</span>
                                                    @else
                                                        <span class="badge badge-primary">قادمة</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($isToday)
                                                        <span class="text-success"><i class="fa fa-calendar-day"></i> اليوم</span>
                                                    @elseif($isPast)
                                                        <span class="text-muted">
                                                            منذ {{ $holidayDate->diffForHumans() }}
                                                        </span>
                                                    @else
                                                        <span class="text-info">
                                                            {{ $holidayDate->diffForHumans() }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">عطلة رسمية</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary btn-sm"
                                                                onclick="viewHolidayDetails({{ $holiday->id }})"
                                                                title="عرض التفاصيل">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-warning btn-sm"
                                                                onclick="editHoliday({{ $holiday->id }})"
                                                                title="تعديل">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm delete-holiday"
                                                                data-holiday-id="{{ $holiday->id }}"
                                                                data-holiday-name="{{ $holiday->named }}"
                                                                data-holiday-date="{{ \Carbon\Carbon::parse($holiday->holiday_date)->locale('ar')->translatedFormat('d F Y') }}"
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
                            @else
                                <div class="alert alert-info text-center" role="alert">
                                    <i class="fa fa-info-circle fa-2x mb-2"></i>
                                    <p class="mb-0">لا توجد عطلات في هذه القائمة حتى الآن!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- الموظفين المخصصين -->
                <div class="tab-pane fade" id="employees" role="tabpanel" aria-labelledby="employees-tab">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="card-title">الموظفين المخصصين لهذه القائمة</h4>
                            <div class="card-tools">
                                <div class="form-inline">
                                    <div class="form-group mr-2">
                                        <input type="text" id="employee_search" class="form-control form-control-sm"
                                               placeholder="البحث في الموظفين...">
                                    </div>
                                    <div class="form-group mr-2">
                                        <select id="department_filter" class="form-control form-control-sm">
                                            <option value="">جميع الأقسام</option>
                                        </select>
                                    </div>
                                    <button id="reset_employee_filters" class="btn btn-secondary btn-sm">
                                        <i class="fa fa-refresh"></i> إعادة تعيين
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- الإحصائيات السريعة للموظفين -->
                            <div id="employee_statistics_cards">
                                <div class="row mb-3" id="employeeStatsRow">
                                    <!-- سيتم ملؤها عبر JavaScript -->
                                </div>
                            </div>

                            <!-- جدول الموظفين -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead style="background: #f8f8f8">
                                        <tr>
                                            <th width="25%">الموظف</th>
                                            <th width="15%">القسم</th>
                                            <th width="15%">المسمى الوظيفي</th>
                                            <th width="10%">الفرع</th>
                                            <th width="10%">العطلات المخصصة</th>
                                            <th width="15%">طريقة التخصيص</th>
                                            <th width="10%">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employees_table">
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <div class="p-4">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="sr-only">جاري تحميل بيانات الموظفين...</span>
                                                    </div>
                                                    <p class="mt-2 text-muted">يرجى الانتظار...</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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

    <!-- Modal delete holiday list -->
    <div class="modal fade text-left" id="modal_DELETE{{ $holiday_list->id }}" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #EA5455 !important;">
                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف قائمة العطلات</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: #DC3545">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <strong><i class="fa fa-exclamation-triangle"></i> تحذير!</strong>
                        <p>سيتم حذف قائمة العطلات وجميع العطلات المرتبطة بها
                            ({{ $holiday_list->holidays()->count() }} عطلة).</p>
                        <p class="mb-0"><strong>هذا الإجراء لا يمكن التراجع عنه!</strong></p>
                    </div>
                    <p><strong>هل أنت متأكد من أنك تريد الحذف؟</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect waves-light"
                        data-dismiss="modal">إلغاء</button>
                    <a href="{{ route('holiday_lists.delete', $holiday_list->id) }}"
                        class="btn btn-danger waves-effect waves-light">
                        <i class="fa fa-trash"></i> تأكيد الحذف
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // تحميل البيانات الأولية
            loadEmployeesCount();
            calculateHolidayStatistics();

            // التعامل مع فلاتر العطلات
            $('#holiday_search, #month_filter').on('input change', function() {
                filterHolidays();
            });

            // إعادة تعيين فلاتر العطلات
            $('#reset_holiday_filters').on('click', function() {
                $('#holiday_search').val('');
                $('#month_filter').val('');
                filterHolidays();
            });

            // التعامل مع فلاتر الموظفين
            $('#employee_search, #department_filter').on('input change', function() {
                filterEmployees();
            });

            // إعادة تعيين فلاتر الموظفين
            $('#reset_employee_filters').on('click', function() {
                $('#employee_search').val('');
                $('#department_filter').val('');
                filterEmployees();
            });

            // تحميل بيانات الموظفين عند النقر على التبويبة
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var targetTab = $(e.target).attr("href");
                var url = window.location.href;
                var newUrl = url.split('#')[0] + targetTab;
                window.history.replaceState(null, null, newUrl);

                // تحميل بيانات الموظفين عند تفعيل التبويبة
                if (targetTab === '#employees') {
                    loadEmployees();
                }
            });

            // تفعيل التبويبة من URL إذا كانت موجودة
            var hash = window.location.hash;
            if (hash) {
                $('.nav-tabs a[href="' + hash + '"]').tab('show');
                // إذا كانت تبويبة الموظفين، قم بتحميل البيانات
                if (hash === '#employees') {
                    loadEmployees();
                }
            }

            // التعامل مع حذف العطلات
            $(document).on('click', '.delete-holiday', function(e) {
                e.preventDefault();

                const holidayId = $(this).data('holiday-id');
                const holidayName = $(this).data('holiday-name');
                const holidayDate = $(this).data('holiday-date');

                Swal.fire({
                    title: 'حذف العطلة',
                    html: `
                        <p><strong>اسم العطلة:</strong> ${holidayName}</p>
                        <p><strong>التاريخ:</strong> ${holidayDate}</p>
                        <hr>
                        <strong>هل أنت متأكد من أنك تريد حذف هذه العطلة؟</strong>
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
                        deleteHoliday(holidayId);
                    }
                });
            });

            // إضافة SweetAlert2 إذا لم يكن موجوداً
            if (typeof Swal === 'undefined') {
                var script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                document.head.appendChild(script);
            }
        });

        // تحميل عدد الموظفين المخصصين
        function loadEmployeesCount() {
            $.ajax({
                url: '{{ route("holiday_lists.employees_count", $holiday_list->id) }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#assignedEmployeesCount').html(`<strong>${response.count} موظف</strong>`);
                    } else {
                        $('#assignedEmployeesCount').html('<strong>0 موظف</strong>');
                    }
                },
                error: function() {
                    $('#assignedEmployeesCount').html('<strong class="text-danger">خطأ في التحميل</strong>');
                }
            });
        }

        // حساب إحصائيات العطلات
        function calculateHolidayStatistics() {
            const now = new Date();
            const currentMonth = now.getMonth() + 1;
            const currentYear = now.getFullYear();

            let upcomingCount = 0;
            let pastCount = 0;
            let thisMonthCount = 0;

            $('.holiday-row').each(function() {
                const dateText = $(this).find('td:eq(1)').text().trim();
                const dateParts = dateText.split('/');
                const holidayDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

                if (holidayDate > now) {
                    upcomingCount++;
                } else if (holidayDate < now) {
                    pastCount++;
                }

                if (holidayDate.getMonth() + 1 === currentMonth && holidayDate.getFullYear() === currentYear) {
                    thisMonthCount++;
                }
            });

            $('#upcoming_holidays').text(upcomingCount);
            $('#past_holidays').text(pastCount);
            $('#this_month_holidays').text(thisMonthCount);
        }

        // فلترة العطلات
        function filterHolidays() {
            const searchTerm = $('#holiday_search').val().toLowerCase();
            const selectedMonth = $('#month_filter').val();

            $('.holiday-row').each(function() {
                const holidayName = $(this).data('name');
                const holidayMonth = $(this).data('month');

                const matchesSearch = !searchTerm || holidayName.includes(searchTerm);
                const matchesMonth = !selectedMonth || holidayMonth == selectedMonth;

                $(this).toggle(matchesSearch && matchesMonth);
            });

            // إعادة حساب الإحصائيات للعطلات المعروضة
            updateVisibleHolidayStats();
        }

        // تحديث إحصائيات العطلات المعروضة
        function updateVisibleHolidayStats() {
            const now = new Date();
            let visibleUpcoming = 0;
            let visiblePast = 0;
            let visibleThisMonth = 0;

            $('.holiday-row:visible').each(function() {
                const dateText = $(this).find('td:eq(1)').text().trim();
                const dateParts = dateText.split('/');
                const holidayDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

                if (holidayDate > now) {
                    visibleUpcoming++;
                } else if (holidayDate < now) {
                    visiblePast++;
                }

                if (holidayDate.getMonth() + 1 === now.getMonth() + 1 &&
                    holidayDate.getFullYear() === now.getFullYear()) {
                    visibleThisMonth++;
                }
            });

            $('#upcoming_holidays').text(visibleUpcoming);
            $('#past_holidays').text(visiblePast);
            $('#this_month_holidays').text(visibleThisMonth);
        }

        // تحميل بيانات الموظفين
        function loadEmployees() {
            // تحقق من أن البيانات لم يتم تحميلها مسبقاً
            if ($('#employees_table').data('loaded')) {
                return;
            }

            console.log('بدء تحميل بيانات الموظفين...');

            $.ajax({
                url: '{{ route("holiday_lists.employees_list", $holiday_list->id) }}',
                method: 'GET',
                timeout: 30000, // 30 ثانية
                beforeSend: function() {
                    // إظهار رسالة التحميل
                    $('#employees_table').html(`
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="p-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">جاري تحميل بيانات الموظفين...</span>
                                    </div>
                                    <p class="mt-2 text-muted">جاري جلب البيانات، يرجى الانتظار...</p>
                                </div>
                            </td>
                        </tr>
                    `);
                },
                success: function(response) {
                    console.log('تم استلام الاستجابة:', response);

                    if (response.success) {
                        if (response.employees && response.employees.length > 0) {
                            displayEmployees(response.employees);
                            populateDepartmentFilter(response.departments || []);
                            updateEmployeeStatistics(response.employees);
                        } else {
                            showNoEmployeesMessage();
                        }
                        // وضع علامة على أن البيانات تم تحميلها
                        $('#employees_table').data('loaded', true);
                    } else {
                        showErrorMessage('لم يتم العثور على بيانات الموظفين');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('خطأ في تحميل البيانات:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        error: error,
                        response: xhr.responseText
                    });

                    let errorMessage = 'خطأ في تحميل البيانات';
                    if (xhr.status === 404) {
                        errorMessage = 'الرابط غير موجود';
                    } else if (xhr.status === 500) {
                        errorMessage = 'خطأ في الخادم';
                    } else if (status === 'timeout') {
                        errorMessage = 'انتهت مهلة الطلب';
                    }

                    showErrorMessage(errorMessage);
                }
            });
        }

        // عرض رسالة خطأ
        function showErrorMessage(message) {
            $('#employees_table').html(`
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="p-4">
                            <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                            <h5 class="text-danger">${message}</h5>
                            <button class="btn btn-primary mt-2" onclick="retryLoadEmployees()">
                                <i class="fa fa-refresh"></i> إعادة المحاولة
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        }

        // إعادة محاولة تحميل الموظفين
        function retryLoadEmployees() {
            $('#employees_table').removeData('loaded');
            loadEmployees();
        }

        // عرض بيانات الموظفين
        function displayEmployees(employees) {
            if (!employees || employees.length === 0) {
                showNoEmployeesMessage();
                return;
            }

            let html = '';

            employees.forEach(function(employee) {
                // طباعة البيانات للتشخيص
                console.log('Employee data:', employee);

                // التأكد من وجود البيانات المطلوبة مع تجربة عدة احتمالات
                const fullName = employee.full_name || employee.name || employee.first_name || employee.ar_name || 'غير محدد';
                const employeeId = employee.employee_id || employee.emp_id || employee.id || 'غير محدد';

                // محاولة الحصول على اسم القسم بطرق مختلفة
                let departmentName = 'غير محدد';
                if (employee.department && employee.department.name) {
                    departmentName = employee.department.name;
                } else if (employee.department && employee.department.ar_name) {
                    departmentName = employee.department.ar_name;
                } else if (employee.department_name) {
                    departmentName = employee.department_name;
                }

                // محاولة الحصول على المسمى الوظيفي بطرق مختلفة
                let jobTitleName = 'غير محدد';
                if (employee.job_title && employee.job_title.name) {
                    jobTitleName = employee.job_title.name;
                } else if (employee.job_Title && employee.job_Title.name) {
                    jobTitleName = employee.job_Title.name;
                } else if (employee.job_title && employee.job_title.ar_name) {
                    jobTitleName = employee.job_title.ar_name;
                } else if (employee.job_Title && employee.job_Title.ar_name) {
                    jobTitleName = employee.job_Title.ar_name;
                } else if (employee.job_title_name) {
                    jobTitleName = employee.job_title_name;
                } else if (employee.position) {
                    jobTitleName = employee.position;
                }

                // محاولة الحصول على اسم الفرع بطرق مختلفة
                let branchName = 'غير محدد';
                if (employee.branch && employee.branch.name) {
                    branchName = employee.branch.name;
                } else if (employee.branch && employee.branch.ar_name) {
                    branchName = employee.branch.ar_name;
                } else if (employee.branch_name) {
                    branchName = employee.branch_name;
                }

                const holidaysCount = employee.holidays_count || 0;
                const assignmentType = employee.assignment_type || 'غير محدد';

                html += `
                    <tr class="employee-row"
                        data-name="${fullName.toLowerCase()}"
                        data-department="${employee.department_id || ''}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2"
                                     style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                                    ${fullName.substr(0, 2).toUpperCase()}
                                </div>
                                <div>
                                    <h6 class="mb-0">${fullName}</h6>
                                    <small class="text-muted">رقم الموظف: ${employeeId}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light">${departmentName}</span>
                        </td>
                        <td>
                            <span class="badge badge-light">${jobTitleName}</span>
                        </td>
                        <td>
                            <span class="badge badge-info">${branchName}</span>
                        </td>
                        <td>
                            <span class="badge badge-success">${holidaysCount} عطلة</span>
                        </td>
                        <td>
                            <span class="badge badge-${assignmentType === 'مباشر' ? 'primary' : 'secondary'}">${assignmentType}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary btn-sm"
                                        onclick="viewEmployeeHolidays(${employee.id})"
                                        title="عرض عطلات الموظف">
                                    <i class="fa fa-calendar"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm"
                                        onclick="removeEmployeeFromList(${employee.id}, '${fullName.replace(/'/g, "\\'")}')"
                                        title="إزالة من القائمة">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            $('#employees_table').html(html);
            console.log('تم عرض الموظفين بنجاح');
        }

        // إظهار رسالة عدم وجود موظفين
        function showNoEmployeesMessage() {
            $('#employees_table').html(`
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="p-4">
                            <i class="fa fa-users fa-3x text-muted mb-3"></i>
                            <h5>لا توجد موظفين مخصصين</h5>
                            <p class="text-muted">لم يتم تخصيص أي موظفين لهذه القائمة حتى الآن</p>
                            <a href="{{ route('holiday_lists.holyday_employees', $holiday_list->id) }}"
                               class="btn btn-primary">
                                <i class="fa fa-plus"></i> إضافة موظفين
                            </a>
                        </div>
                    </td>
                </tr>
            `);

            // تحديث إحصائيات الموظفين
            $('#employeeStatsRow').html(`
                <div class="col-md-12">
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle"></i>
                        لا توجد موظفين مخصصين لعرض الإحصائيات
                    </div>
                </div>
            `);
        }

        // تحديث إحصائيات الموظفين
        function updateEmployeeStatistics(employees) {
            if (!employees || employees.length === 0) {
                showNoEmployeesMessage();
                return;
            }

            const totalEmployees = employees.length;
            const departments = [...new Set(employees.map(emp =>
                emp.department && emp.department.name ? emp.department.name : null
            ).filter(Boolean))].length;
            const branches = [...new Set(employees.map(emp =>
                emp.branch && emp.branch.name ? emp.branch.name : null
            ).filter(Boolean))].length;
            const totalHolidaysAssigned = employees.reduce((sum, emp) =>
                sum + (emp.holidays_count || 0), 0);

            $('#employeeStatsRow').html(`
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1">${totalEmployees}</h3>
                            <p class="mb-0">إجمالي الموظفين</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1">${departments}</h3>
                            <p class="mb-0">عدد الأقسام</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1">${branches}</h3>
                            <p class="mb-0">عدد الفروع</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1">${totalHolidaysAssigned}</h3>
                            <p class="mb-0">إجمالي العطلات المخصصة</p>
                        </div>
                    </div>
                </div>
            `);
        }

        // ملء فلتر الأقسام
        function populateDepartmentFilter(departments) {
            let options = '<option value="">جميع الأقسام</option>';
            if (departments && departments.length > 0) {
                departments.forEach(function(dept) {
                    if (dept && dept.id && dept.name) {
                        options += `<option value="${dept.id}">${dept.name}</option>`;
                    }
                });
            }
            $('#department_filter').html(options);
        }

        // فلترة الموظفين
        function filterEmployees() {
            const searchTerm = $('#employee_search').val().toLowerCase();
            const selectedDept = $('#department_filter').val();

            $('.employee-row').each(function() {
                const employeeName = $(this).data('name');
                const employeeDept = $(this).data('department');

                const matchesSearch = !searchTerm || employeeName.includes(searchTerm);
                const matchesDept = !selectedDept || employeeDept == selectedDept;

                $(this).toggle(matchesSearch && matchesDept);
            });
        }

        // حذف عطلة
        function deleteHoliday(holidayId) {
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
                url: `/holidays/${holidayId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'تم الحذف!',
                            text: response.message || 'تم حذف العطلة بنجاح',
                            icon: 'success',
                            timer: 2000,
                            timerProgressBar: true
                        });

                        // إزالة الصف من الجدول
                        $(`.delete-holiday[data-holiday-id="${holidayId}"]`).closest('tr').fadeOut(300, function() {
                            $(this).remove();
                            calculateHolidayStatistics(); // إعادة حساب الإحصائيات
                        });

                        // تحديث العداد في الرأس
                        loadEmployeesCount();
                    } else {
                        Swal.fire({
                            title: 'خطأ!',
                            text: response.message || 'حدث خطأ أثناء الحذف',
                            icon: 'error'
                        });
                    }
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

        // إزالة موظف من القائمة
        function removeEmployeeFromList(employeeId, employeeName) {
            Swal.fire({
                title: 'إزالة الموظف من القائمة',
                text: `هل أنت متأكد من إزالة "${employeeName}" من هذه القائمة؟`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، أزل',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("holiday_lists.remove_employee", $holiday_list->id) }}',
                        method: 'POST',
                        data: {
                            employee_id: employeeId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'تمت الإزالة!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    timerProgressBar: true
                                });

                                // إزالة الصف وإعادة تحميل البيانات
                                $(`.employee-row[data-name*="${employeeName.toLowerCase()}"]`).fadeOut(300, function() {
                                    $(this).remove();
                                    loadEmployeesCount();
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'خطأ!',
                                text: 'حدث خطأ أثناء إزالة الموظف',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

        // عرض عطلات الموظف
        function viewEmployeeHolidays(employeeId) {
            Swal.fire({
                title: 'عطلات الموظف',
                text: 'سيتم إضافة هذه الميزة قريباً',
                icon: 'info'
            });
        }

        // تعديل عطلة
        function editHoliday(holidayId) {
            Swal.fire({
                title: 'تعديل العطلة',
                text: 'سيتم إضافة هذه الميزة قريباً',
                icon: 'info'
            });
        }

        // عرض تفاصيل العطلة
        function viewHolidayDetails(holidayId) {
            Swal.fire({
                title: 'تفاصيل العطلة',
                text: 'سيتم إضافة هذه الميزة قريباً',
                icon: 'info'
            });
        }

        // تحسينات إضافية
        $(document).on('mouseenter', '.table-hover tbody tr', function() {
            $(this).addClass('table-active');
        }).on('mouseleave', '.table-hover tbody tr', function() {
            $(this).removeClass('table-active');
        });

        // Tooltips للأزرار
        $('[title]').tooltip();

        // معالجة الأخطاء العامة
        $(document).ajaxError(function(event, xhr, settings) {
            if (xhr.status === 403) {
                Swal.fire({
                    title: 'غير مصرح!',
                    text: 'ليس لديك صلاحية للقيام بهذا الإجراء',
                    icon: 'error'
                });
            } else if (xhr.status === 404) {
                Swal.fire({
                    title: 'غير موجود!',
                    text: 'البيانات المطلوبة غير موجودة',
                    icon: 'error'
                });
            }
        });
    </script>
@endsection