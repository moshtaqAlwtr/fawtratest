@extends('master')

@section('title', 'عرض محدد حضور')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
@endsection

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">محدد الحضور #{{ $attendance_determinants->id }}</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('attendance_determinants.index') }}">محددات الحضور</a></li>
                            <li class="breadcrumb-item active">{{ $attendance_determinants->name }}</li>
                            <li class="breadcrumb-item active">
                                <span class="badge badge-primary" id="employeeCountBadge">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="sr-only">جاري التحميل...</span>
                                    </div>
                                </span>
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
                data-target="#modal_DELETE{{ $attendance_determinants->id }}">حذف المحدد <i class="fa fa-trash"></i></a>
            <a href="{{ route('attendance_determinants.edit', $attendance_determinants->id) }}"
                class="btn btn-outline-primary btn-sm waves-effect waves-light">تعديل <i class="fa fa-edit"></i></a>
            <a href="{{ route('attendance_determinants.manage_employees', $attendance_determinants->id) }}"
                class="btn btn-outline-success btn-sm waves-effect waves-light">إدارة الموظفين <i class="fa fa-users"></i></a>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                        aria-controls="details" aria-selected="true">تفاصيل المحدد</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="settings-tab" data-toggle="tab" href="#settings" role="tab"
                        aria-controls="settings" aria-selected="false">الإعدادات</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="employees-tab" data-toggle="tab" href="#employees" role="tab"
                        aria-controls="employees" aria-selected="false">الموظفين المخصصين</a>
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
                <!-- تفاصيل المحدد -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="row">
                                <table class="table">
                                    <thead style="background: #f8f8f8">
                                        <tr>
                                            <th style="width: 70%">معلومات المحدد</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p><small>رقم المحدد</small>: </p>
                                                <strong>#{{ $attendance_determinants->id }}</strong>
                                            </td>
                                            <td>
                                                <p><small>اسم المحدد</small>: </p>
                                                <strong>{{ $attendance_determinants->name }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><small>الحالة</small>: </p>
                                                @if($attendance_determinants->status == 0)
                                                    <span class="badge badge-success">نشط</span>
                                                @else
                                                    <span class="badge badge-danger">غير نشط</span>
                                                @endif
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
                                                <strong>{{ $attendance_determinants->created_at->locale('ar')->translatedFormat('l، d F Y - H:i') }}</strong>
                                            </td>
                                            <td>
                                                <p><small>آخر تحديث</small>: </p>
                                                <strong>{{ $attendance_determinants->updated_at->locale('ar')->translatedFormat('l، d F Y - H:i') }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإعدادات -->
                <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="row">
                                <!-- إعدادات التقاط الصورة -->
                                <div class="col-md-12 mb-4">
                                    <h6 class="mb-3 p-2" style="background: #f8f8f8; border-radius: 4px;">إعدادات التقاط الصورة</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">التقاط صورة الموظف</label>
                                            <div>
                                                @if($attendance_determinants->capture_employee_image)
                                                    <span class="badge badge-success">مفعل</span>
                                                @else
                                                    <span class="badge badge-secondary">معطل</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">نوع التحقق من الصورة</label>
                                            <div>
                                                @if($attendance_determinants->image_investigation == 1)
                                                    <span class="badge badge-warning">مطلوب</span>
                                                @else
                                                    <span class="badge badge-info">اختياري</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- إعدادات التحقق من IP -->
                                <div class="col-md-12 mb-4">
                                    <h6 class="mb-3 p-2" style="background: #f8f8f8; border-radius: 4px;">إعدادات التحقق من IP</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">التحقق من عنوان IP</label>
                                            <div>
                                                @if($attendance_determinants->enable_ip_verification)
                                                    <span class="badge badge-success">مفعل</span>
                                                @else
                                                    <span class="badge badge-secondary">معطل</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">نوع التحقق من IP</label>
                                            <div>
                                                @if($attendance_determinants->ip_investigation == 1)
                                                    <span class="badge badge-warning">مطلوب</span>
                                                @else
                                                    <span class="badge badge-info">اختياري</span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($attendance_determinants->enable_ip_verification && $attendance_determinants->allowed_ips)
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label text-muted">عناوين IP المسموحة</label>
                                            <div class="bg-light p-3 rounded">
                                                @if(is_array($attendance_determinants->allowed_ips))
                                                    @foreach($attendance_determinants->allowed_ips as $ip)
                                                        <span class="badge badge-primary me-1 mb-1">{{ $ip }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">{{ $attendance_determinants->allowed_ips }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- إعدادات التحقق من الموقع -->
                                <div class="col-md-12 mb-4">
                                    <h6 class="mb-3 p-2" style="background: #f8f8f8; border-radius: 4px;">إعدادات التحقق من الموقع</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">التحقق من الموقع</label>
                                            <div>
                                                @if($attendance_determinants->enable_location_verification)
                                                    <span class="badge badge-success">مفعل</span>
                                                @else
                                                    <span class="badge badge-secondary">معطل</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">نوع التحقق من الموقع</label>
                                            <div>
                                                @if($attendance_determinants->location_investigation == 1)
                                                    <span class="badge badge-warning">مطلوب</span>
                                                @else
                                                    <span class="badge badge-info">اختياري</span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($attendance_determinants->enable_location_verification)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">نطاق الموقع</label>
                                            <div>
                                                {{ $attendance_determinants->radius ?? 'غير محدد' }}
                                                @if($attendance_determinants->radius_type == 1)
                                                    متر
                                                @elseif($attendance_determinants->radius_type == 2)
                                                    كيلومتر
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">الإحداثيات</label>
                                            <div>
                                                @if($attendance_determinants->latitude && $attendance_determinants->longitude)
                                                    {{ number_format($attendance_determinants->latitude, 6) }}, {{ number_format($attendance_determinants->longitude, 6) }}
                                                @else
                                                    غير محددة
                                                @endif
                                            </div>
                                        </div>

                                        @if($attendance_determinants->latitude && $attendance_determinants->longitude)
                                        <div class="col-md-12">
                                            <label class="form-label text-muted">الموقع على الخريطة</label>
                                            <div id="map" style="width: 100%; height: 400px; border-radius: 8px; border: 1px solid #dee2e6;"></div>
                                        </div>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الموظفين المخصصين -->
                <div class="tab-pane fade" id="employees" role="tabpanel" aria-labelledby="employees-tab">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="card-title">الموظفين المخصصين لهذا المحدد</h4>
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
                                            <th width="10%">المحدد المخصص</th>
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

                <!-- الإحصائيات -->
                <div class="tab-pane fade" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="mb-3 p-2" style="background: #f8f8f8; border-radius: 4px;">إحصائيات الاستخدام</h6>

                            <div class="row text-center">
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-8">
                                                    <h4 class="mb-0" id="total_employees">0</h4>
                                                    <p class="mb-0">إجمالي الموظفين</p>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <i class="fa fa-users fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-8">
                                                    <h4 class="mb-0" id="attendance_records">{{ $attendance_determinants->attendances_count ?? 0 }}</h4>
                                                    <p class="mb-0">تسجيلات الحضور</p>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <i class="fa fa-clock fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-8">
                                                    <h4 class="mb-0" id="departments_count">0</h4>
                                                    <p class="mb-0">عدد الأقسام</p>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <i class="fa fa-building fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-8">
                                                    <h4 class="mb-0">{{ $attendance_determinants->created_at->format('Y/m/d') }}</h4>
                                                    <p class="mb-0">تاريخ الإنشاء</p>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <i class="fa fa-calendar fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

    <!-- Modal delete attendance determinant -->
    <div class="modal fade text-left" id="modal_DELETE{{ $attendance_determinants->id }}" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #EA5455 !important;">
                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف محدد الحضور</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: #DC3545">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <strong><i class="fa fa-exclamation-triangle"></i> تحذير!</strong>
                        <p>سيتم حذف محدد الحضور وجميع البيانات المرتبطة به.</p>
                        <p class="mb-0"><strong>هذا الإجراء لا يمكن التراجع عنه!</strong></p>
                    </div>
                    <p><strong>هل أنت متأكد من أنك تريد الحذف؟</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect waves-light"
                        data-dismiss="modal">إلغاء</button>
                    <form method="POST" action="{{ route('attendance_determinants.destroy', $attendance_determinants->id) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger waves-effect waves-light">
                            <i class="fa fa-trash"></i> تأكيد الحذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // تحميل البيانات الأولية
            loadEmployeesCount();

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
                } else if (targetTab === '#statistics') {
                    loadStatistics();
                }
            });

            // تفعيل التبويبة من URL إذا كانت موجودة
            var hash = window.location.hash;
            if (hash) {
                $('.nav-tabs a[href="' + hash + '"]').tab('show');
                // إذا كانت تبويبة الموظفين، قم بتحميل البيانات
                if (hash === '#employees') {
                    loadEmployees();
                } else if (hash === '#statistics') {
                    loadStatistics();
                }
            }

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
                url: '{{ route("attendance_determinants.employees_count", $attendance_determinants->id) }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#assignedEmployeesCount').html(`<strong>${response.count} موظف</strong>`);
                        $('#employeeCountBadge').html(`${response.count} موظف`);
                    } else {
                        $('#assignedEmployeesCount').html('<strong>0 موظف</strong>');
                        $('#employeeCountBadge').html('0 موظف');
                    }
                },
                error: function() {
                    $('#assignedEmployeesCount').html('<strong class="text-danger">خطأ في التحميل</strong>');
                    $('#employeeCountBadge').html('<span class="text-danger">خطأ</span>');
                }
            });
        }

        // تحميل بيانات الموظفين
        function loadEmployees() {
            // تحقق من أن البيانات لم يتم تحميلها مسبقاً
            if ($('#employees_table').data('loaded')) {
                return;
            }

            console.log('بدء تحميل بيانات الموظفين...');

            $.ajax({
                url: '{{ route("attendance_determinants.employees_list", $attendance_determinants->id) }}',
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

        // تحميل الإحصائيات
        function loadStatistics() {
            $.ajax({
                url: '{{ route("attendance_determinants.statistics", $attendance_determinants->id) }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#total_employees').text(response.statistics.employees_count || 0);
                        $('#departments_count').text(response.statistics.departments_count || 0);
                    }
                },
                error: function() {
                    console.error('خطأ في تحميل الإحصائيات');
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

                const determinantName = employee.determinant_name || '{{ $attendance_determinants->name }}';
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
                            <span class="badge badge-primary">${determinantName}</span>
                        </td>
                        <td>
                            <span class="badge badge-${assignmentType === 'مباشر' ? 'primary' : 'secondary'}">${assignmentType}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary btn-sm"
                                        onclick="viewEmployeeAttendance(${employee.id})"
                                        title="عرض حضور الموظف">
                                    <i class="fa fa-clock"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm"
                                        onclick="removeEmployeeFromDeterminant(${employee.id}, '${fullName.replace(/'/g, "\\'")}')"
                                        title="إزالة من المحدد">
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
                            <p class="text-muted">لم يتم تخصيص أي موظفين لهذا المحدد حتى الآن</p>
                            <a href="{{ route('attendance_determinants.manage_employees', $attendance_determinants->id) }}"
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
            const totalAssignments = employees.length;

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
                            <h3 class="mb-1">${totalAssignments}</h3>
                            <p class="mb-0">إجمالي التخصيصات</p>
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

        // إزالة موظف من المحدد
        function removeEmployeeFromDeterminant(employeeId, employeeName) {
            Swal.fire({
                title: 'إزالة الموظف من المحدد',
                text: `هل أنت متأكد من إزالة "${employeeName}" من هذا المحدد؟`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، أزل',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("attendance_determinants.remove_employee", $attendance_determinants->id) }}',
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

        // عرض حضور الموظف
        function viewEmployeeAttendance(employeeId) {
            Swal.fire({
                title: 'حضور الموظف',
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

        // Initialize map if location data exists
        @if($attendance_determinants->enable_location_verification && $attendance_determinants->latitude && $attendance_determinants->longitude)
        function initMap() {
            const latitude = {{ $attendance_determinants->latitude }};
            const longitude = {{ $attendance_determinants->longitude }};
            const center = { lat: latitude, lng: longitude };

            // Create map
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: center,
                mapTypeId: 'roadmap'
            });

            // Add marker
            const marker = new google.maps.Marker({
                position: center,
                map: map,
                title: '{{ $attendance_determinants->name }}'
            });

            // Add circle to show radius
            @if($attendance_determinants->radius)
            const radiusInMeters = {{ $attendance_determinants->radius_type == 2 ? $attendance_determinants->radius * 1000 : $attendance_determinants->radius }};

            const circle = new google.maps.Circle({
                map: map,
                center: center,
                radius: radiusInMeters,
                fillColor: '#007bff',
                fillOpacity: 0.2,
                strokeColor: '#007bff',
                strokeOpacity: 0.8,
                strokeWeight: 2
            });

            // Adjust map bounds to include circle
            map.fitBounds(circle.getBounds());
            @endif
        }

        // Load Google Maps - تأخير تحميل الخريطة حتى تصبح التبويبة نشطة
        $('a[href="#settings"]').on('shown.bs.tab', function() {
            if (!window.mapInitialized) {
                const script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyB6Hsnt5MiyjXtrGT5q-5KUj09XmLPV5So&callback=initMap';
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
                window.mapInitialized = true;
            }
        });
        @endif
    </script>
