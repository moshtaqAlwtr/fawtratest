@extends('master')

@section('title')
    سياسة الإجازات - {{ $leave_policy->name }}
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
    <style>
        .stats-card {
            transition: transform 0.2s ease-in-out;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .employee-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            color: white;
            border-radius: 50%;
        }
        .leave-type-card {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            transition: all 0.2s ease;
        }
        .leave-type-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }
        .timeline-item {
            position: relative;
            padding-left: 60px;
            margin-bottom: 25px;
        }
        .timeline-icon {
            position: absolute;
            left: 0;
            top: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }
        .timeline-content {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">سياسة الإجازات #{{ $leave_policy->id }}</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('leave_policy.index') }}">سياسات الإجازات</a></li>
                            <li class="breadcrumb-item active">{{ $leave_policy->name }}</li>
                            <li class="breadcrumb-item active">
                                @if($leave_policy->status == 0)
                                    <span class="badge badge-success">نشط</span>
                                @else
                                    <span class="badge badge-danger">غير نشط</span>
                                @endif
                            </li>
                            <li class="breadcrumb-item active">
                                <span class="badge badge-primary">{{ $leave_policy->leaveType()->count() }} نوع إجازة</span>
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
                data-target="#modal_DELETE{{ $leave_policy->id }}">حذف السياسة <i class="fa fa-trash"></i></a>
            <a href="{{ route('leave_policy.edit', $leave_policy->id) }}"
                class="btn btn-outline-primary btn-sm waves-effect waves-light">تعديل <i class="fa fa-edit"></i></a>
            <a href="{{ route('leave_policy.leave_policy_employees', $leave_policy->id) }}"
                class="btn btn-outline-success btn-sm waves-effect waves-light">إدارة الموظفين <i class="fa fa-users"></i></a>
            <a href="{{ route('leave_policy.updateStatus', $leave_policy->id) }}"
                class="btn btn-outline-{{ $leave_policy->status == 0 ? 'danger' : 'success' }} btn-sm waves-effect waves-light">
                {{ $leave_policy->status == 0 ? 'تعطيل' : 'تفعيل' }} <i class="fa {{ $leave_policy->status == 0 ? 'fa-ban' : 'fa-check' }}"></i>
            </a>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                        aria-controls="details" aria-selected="true">تفاصيل السياسة</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="leave-types-tab" data-toggle="tab" href="#leave-types" role="tab"
                        aria-controls="leave-types" aria-selected="false">أنواع الإجازات</a>
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
                <!-- تفاصيل السياسة -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="row">
                                <table class="table">
                                    <thead style="background: #f8f8f8">
                                        <tr>
                                            <th style="width: 70%">معلومات السياسة</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p><small>رقم السياسة</small>: </p>
                                                <strong>#{{ $leave_policy->id }}</strong>
                                            </td>
                                            <td>
                                                <p><small>اسم السياسة</small>: </p>
                                                <strong>{{ $leave_policy->name }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><small>الوصف</small>: </p>
                                                <strong>{{ $leave_policy->description ?: 'لا يوجد وصف' }}</strong>
                                            </td>
                                            <td>
                                                <p><small>الحالة</small>: </p>
                                                @if($leave_policy->status == 0)
                                                    <span class="badge badge-success">نشط</span>
                                                @else
                                                    <span class="badge badge-danger">غير نشط</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><small>عدد أنواع الإجازات</small>: </p>
                                                <strong>{{ $leave_policy->leaveType()->count() }} نوع</strong>
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
                                                <strong>{{ $leave_policy->created_at->locale('ar')->translatedFormat('l، d F Y - H:i') }}</strong>
                                            </td>
                                            <td>
                                                <p><small>آخر تحديث</small>: </p>
                                                <strong>{{ $leave_policy->updated_at->locale('ar')->translatedFormat('l، d F Y - H:i') }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- الإحصائيات السريعة -->
                    <div class="row mt-3" id="statisticsCards">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white stats-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-8">
                                            <h4 class="mb-0" id="totalLeaveTypes">{{ $leave_policy->leaveType()->count() }}</h4>
                                            <p class="mb-0">أنواع الإجازات</p>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="fa fa-list fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white stats-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-8">
                                            <h4 class="mb-0" id="totalEmployees">
                                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                            </h4>
                                            <p class="mb-0">إجمالي الموظفين</p>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="fa fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white stats-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-8">
                                            <h4 class="mb-0" id="totalDepartments">
                                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                            </h4>
                                            <p class="mb-0">الأقسام المتأثرة</p>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="fa fa-building fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white stats-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-8">
                                            <h4 class="mb-0">{{ $leave_policy->status == 0 ? 'نشط' : 'غير نشط' }}</h4>
                                            <p class="mb-0">حالة السياسة</p>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="fa fa-{{ $leave_policy->status == 0 ? 'check-circle' : 'times-circle' }} fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أنواع الإجازات -->
                <div class="tab-pane fade" id="leave-types" role="tabpanel" aria-labelledby="leave-types-tab">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="card-title">أنواع الإجازات المتاحة</h4>
                            <div class="card-tools">
                                <div class="form-inline">
                                    <div class="form-group mr-2">
                                        <input type="text" id="leave_type_search" class="form-control form-control-sm"
                                               placeholder="البحث في أنواع الإجازات...">
                                    </div>
                                    <button id="reset_leave_type_filters" class="btn btn-secondary btn-sm">
                                        <i class="fa fa-refresh"></i> إعادة تعيين
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($leave_policy->leaveType && $leave_policy->leaveType->count() > 0)
                                <div class="row" id="leaveTypesContainer">
                                    @foreach ($leave_policy->leaveType as $type)
                                        <div class="col-md-6 col-lg-4 leave-type-item" data-name="{{ strtolower($type->name) }}">
                                            <div class="leave-type-card">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <h5 class="mb-0">{{ $type->name }}</h5>
                                                    <span class="badge badge-primary">#{{ $type->id }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">الوصف:</small>
                                                    <p class="mb-0">{{ $type->description ?: 'لا يوجد وصف' }}</p>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="text-muted">مدة الإجازة:</small>
                                                        <span class="badge badge-info">{{ $type->duration ?: 'غير محدد' }}</span>
                                                    </div>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary btn-sm"
                                                                onclick="viewLeaveTypeDetails({{ $type->id }})"
                                                                title="عرض التفاصيل">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-warning btn-sm"
                                                                onclick="editLeaveType({{ $type->id }})"
                                                                title="تعديل">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info text-center" role="alert">
                                    <i class="fa fa-info-circle fa-2x mb-2"></i>
                                    <p class="mb-0">لا توجد أنواع إجازات مرتبطة بهذه السياسة!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- الموظفين المخصصين -->
                <div class="tab-pane fade" id="employees" role="tabpanel" aria-labelledby="employees-tab">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="card-title">الموظفين المخصصين لهذه السياسة</h4>
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
                                            <th width="10%">أنواع الإجازات</th>
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

                                    <div class="timeline">
                                        @foreach ($dayLogs as $log)
                                            @if ($log)
                                                <div class="timeline-item">
                                                    <div class="timeline-icon bg-primary">
                                                        <i class="fa fa-user"></i>
                                                    </div>
                                                    <div class="timeline-content">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <strong>{{ $log->user->name ?? 'مستخدم غير معروف' }}</strong>
                                                            <small class="text-muted">
                                                                <i class="far fa-clock"></i>
                                                                {{ $log->created_at->format('H:i:s') }}
                                                            </small>
                                                        </div>
                                                        <div class="mb-2">
                                                            {!! Str::markdown($log->description ?? 'لا يوجد وصف') !!}
                                                        </div>
                                                        <div class="text-muted">
                                                            <i class="fas fa-building"></i>
                                                            {{ $log->user->branch->name ?? 'فرع غير معروف' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

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

    <!-- Modal delete leave policy -->
    <div class="modal fade text-left" id="modal_DELETE{{ $leave_policy->id }}" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #EA5455 !important;">
                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف سياسة الإجازات</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: #DC3545">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <strong><i class="fa fa-exclamation-triangle"></i> تحذير!</strong>
                        <p>سيتم حذف سياسة الإجازات وجميع البيانات المرتبطة بها
                            ({{ $leave_policy->leaveType()->count() }} نوع إجازة).</p>
                        <p class="mb-0"><strong>هذا الإجراء لا يمكن التراجع عنه!</strong></p>
                    </div>
                    <p><strong>هل أنت متأكد من أنك تريد الحذف؟</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect waves-light"
                        data-dismiss="modal">إلغاء</button>
                    <a href="{{ route('leave_policy.delete', $leave_policy->id) }}"
                        class="btn btn-danger waves-effect waves-light">
                        <i class="fa fa-trash"></i> تأكيد الحذف
                    </a>
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
            loadStatistics();

            // التعامل مع فلاتر أنواع الإجازات
            $('#leave_type_search').on('input', function() {
                filterLeaveTypes();
            });

            // إعادة تعيين فلاتر أنواع الإجازات
            $('#reset_leave_type_filters').on('click', function() {
                $('#leave_type_search').val('');
                filterLeaveTypes();
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
        });

        // تحميل عدد الموظفين المخصصين
        function loadEmployeesCount() {
            $.ajax({
                url: '{{ route("leave_policy.employeesCount", $leave_policy->id) }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#assignedEmployeesCount').html(`<strong>${response.count} موظف</strong>`);
                        $('#totalEmployees').text(response.count);
                    } else {
                        $('#assignedEmployeesCount').html('<strong>0 موظف</strong>');
                        $('#totalEmployees').text('0');
                    }
                },
                error: function() {
                    $('#assignedEmployeesCount').html('<strong class="text-danger">خطأ في التحميل</strong>');
                    $('#totalEmployees').text('خطأ');
                }
            });
        }

        // تحميل الإحصائيات
        function loadStatistics() {
            $.ajax({
                url: '{{ route("leave_policy.getStatistics", $leave_policy->id) }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#totalDepartments').text(response.statistics.departments_count);
                    }
                },
                error: function() {
                    $('#totalDepartments').text('خطأ');
                }
            });
        }

        // فلترة أنواع الإجازات
        function filterLeaveTypes() {
            const searchTerm = $('#leave_type_search').val().toLowerCase();

            $('.leave-type-item').each(function() {
                const leaveTypeName = $(this).data('name');
                const matchesSearch = !searchTerm || leaveTypeName.includes(searchTerm);

                $(this).toggle(matchesSearch);
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
                url: '{{ route("leave_policy.employeesList", $leave_policy->id) }}',
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

                const leaveTypesCount = employee.leave_types_count || 0;
                const assignmentType = employee.assignment_type || 'غير محدد';

                html += `
                    <tr class="employee-row"
                        data-name="${fullName.toLowerCase()}"
                        data-department="${employee.department_id || ''}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="employee-avatar mr-2">
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
                            <span class="badge badge-success">${leaveTypesCount} نوع</span>
                        </td>
                        <td>
                            <span class="badge badge-${assignmentType === 'مباشر' ? 'primary' : 'secondary'}">${assignmentType}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary btn-sm"
                                        onclick="viewEmployeeLeaveTypes(${employee.id})"
                                        title="عرض أنواع إجازات الموظف">
                                    <i class="fa fa-list"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm"
                                        onclick="removeEmployeeFromPolicy(${employee.id}, '${fullName.replace(/'/g, "\\'")}')"
                                        title="إزالة من السياسة">
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
                            <p class="text-muted">لم يتم تخصيص أي موظفين لهذه السياسة حتى الآن</p>
                            <a href="{{ route('leave_policy.leave_policy_employees', $leave_policy->id) }}"
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
            const totalLeaveTypesAssigned = employees.reduce((sum, emp) =>
                sum + (emp.leave_types_count || 0), 0);

            $('#employeeStatsRow').html(`
                <div class="col-md-3">
                    <div class="card bg-primary text-white stats-card">
                        <div class="card-body text-center">
                            <h3 class="mb-1">${totalEmployees}</h3>
                            <p class="mb-0">إجمالي الموظفين</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white stats-card">
                        <div class="card-body text-center">
                            <h3 class="mb-1">${departments}</h3>
                            <p class="mb-0">عدد الأقسام</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white stats-card">
                        <div class="card-body text-center">
                            <h3 class="mb-1">${branches}</h3>
                            <p class="mb-0">عدد الفروع</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white stats-card">
                        <div class="card-body text-center">
                            <h3 class="mb-1">${totalLeaveTypesAssigned}</h3>
                            <p class="mb-0">إجمالي الأنواع المخصصة</p>
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

        // إزالة موظف من السياسة
        function removeEmployeeFromPolicy(employeeId, employeeName) {
            Swal.fire({
                title: 'إزالة الموظف من السياسة',
                text: `هل أنت متأكد من إزالة "${employeeName}" من هذه السياسة؟`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، أزل',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("leave_policy.removeEmployee", $leave_policy->id) }}',
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

        // عرض أنواع إجازات الموظف
        function viewEmployeeLeaveTypes(employeeId) {
            Swal.fire({
                title: 'أنواع إجازات الموظف',
                text: 'سيتم إضافة هذه الميزة قريباً',
                icon: 'info'
            });
        }

        // تعديل نوع الإجازة
        function editLeaveType(leaveTypeId) {
            Swal.fire({
                title: 'تعديل نوع الإجازة',
                text: 'سيتم إضافة هذه الميزة قريباً',
                icon: 'info'
            });
        }

        // عرض تفاصيل نوع الإجازة
        function viewLeaveTypeDetails(leaveTypeId) {
            Swal.fire({
                title: 'تفاصيل نوع الإجازة',
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