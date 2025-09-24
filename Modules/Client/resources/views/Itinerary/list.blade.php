@extends('master')

@section('title', 'عرض جميع خطط السير')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/listIntry.css') }}">
@endsection
@section('content')

    <!-- Header Section -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-title">
                        <i class="fas fa-route text-primary me-3"></i>
                        <h2 class="mb-0">عرض جميع خطط السير</h2>
                        <p class="text-muted mt-2">إدارة ومراقبة خطط السير الأسبوعية للمناديب</p>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('itinerary.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-plus me-2"></i>
                        أضف خط سير جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="container-fluid mb-4">
        <div class="row g-4">
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon bg-primary">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stats-content">
                        <h3>
                            @php
                                $totalWeeks = 0;
                                $weeklyData = [];

                                // تجميع البيانات حسب الأسبوع
                                foreach ($itineraries as $employeeId => $employeeData) {
                                    $employeeItineraries = $employeeData['weeks'] ?? [];
                                    foreach ($employeeItineraries as $weekIdentifier => $weekVisits) {
                                        if (!isset($weeklyData[$weekIdentifier])) {
                                            $weeklyData[$weekIdentifier] = [];
                                            $totalWeeks++;
                                        }
                                        $weeklyData[$weekIdentifier][$employeeId] = [
                                            'employee' => $employeeData['employee'],
                                            'visits' => $weekVisits
                                        ];
                                    }
                                }
                            @endphp
                            {{ $totalWeeks }}
                        </h3>
                        <p>إجمالي الأسابيع</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon bg-info">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-content">
                        <h3>{{ count($itineraries) }}</h3>
                        <p>إجمالي المناديب</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon bg-warning">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stats-content">
                        <h3>
                            @php
                                $totalPlannedVisits = 0;
                                foreach ($itineraries as $employeeId => $employeeData) {
                                    $employeeVisits = 0;
                                    $weeks = $employeeData['weeks'] ?? [];
                                    foreach ($weeks as $week) {
                                        foreach ($week as $dayData) {
                                            $employeeVisits += count($dayData['visits'] ?? []);
                                        }
                                    }
                                    $totalPlannedVisits += $employeeVisits;
                                }
                            @endphp
                            {{ $totalPlannedVisits }}
                        </h3>
                        <p>إجمالي الزيارات المخططة</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-content">
                        <h3>
                            @php
                                $totalCompletedVisits = 0;
                                foreach ($itineraries as $employeeId => $employeeData) {
                                    $weeks = $employeeData['weeks'] ?? [];
                                    foreach ($weeks as $week) {
                                        foreach ($week as $dayData) {
                                            foreach ($dayData['visits'] ?? [] as $visit) {
                                                if ($visit->status === 'active') {
                                                    $totalCompletedVisits++;
                                                }
                                            }
                                        }
                                    }
                                }
                            @endphp
                            {{ $totalCompletedVisits }}
                        </h3>
                        <p>الزيارات المكتملة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="main-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="card-title mb-1">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                    خطط السير الأسبوعية
                                </h4>
                                <p class="card-subtitle text-muted mb-0">استعراض وإدارة خطط السير مجمعة حسب الأسبوع</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="expandAll()">
                                    <i class="fas fa-expand-arrows-alt me-1"></i>
                                    توسيع الكل
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="collapseAll()">
                                    <i class="fas fa-compress-arrows-alt me-1"></i>
                                    طي الكل
                                </button>
                                <button class="btn btn-outline-info btn-sm" onclick="expandAllEmployees()">
                                    <i class="fas fa-users me-1"></i>
                                    عرض جميع الموظفين
                                </button>
                                <button class="btn btn-outline-warning btn-sm" onclick="collapseAllEmployees()">
                                    <i class="fas fa-user-minus me-1"></i>
                                    إخفاء جميع الموظفين
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (empty($weeklyData))
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-route"></i>
                                </div>
                                <h3>لا توجد خطط سير</h3>
                                <p>لم يتم إنشاء أي خطط سير بعد. ابدأ بإضافة خط سير جديد.</p>
                                <a href="{{ route('itinerary.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    أضف خط سير الآن
                                </a>
                            </div>
                        @else
                            <div class="accordion-modern" id="weeklyAccordion">
                                @php
                                    $accordionItemIndex = 0;
                                    $days = [
                                        'saturday' => 'السبت',
                                        'sunday' => 'الأحد',
                                        'monday' => 'الإثنين',
                                        'tuesday' => 'الثلاثاء',
                                        'wednesday' => 'الأربعاء',
                                        'thursday' => 'الخميس',
                                        'friday' => 'الجمعة',
                                    ];

                                    // ترتيب الأسابيع من الأحدث للأقدم
                                    krsort($weeklyData);
                                @endphp

                                @foreach ($weeklyData as $weekIdentifier => $weekEmployees)
                                    @php
                                        $yearWeek = explode('-W', $weekIdentifier);
                                        $year = $yearWeek[0] ?? date('Y');
                                        $weekNum = $yearWeek[1] ?? '00';
                                        $accordionItemIndex++;

                                        // حساب إحصائيات الأسبوع
                                        $weekTotalVisits = 0;
                                        $weekCompletedVisits = 0;
                                        $weekNewClients = 0;
                                        $weekEmployeesCount = count($weekEmployees);

                                        foreach ($weekEmployees as $employeeId => $employeeData) {
                                            $weekVisits = $employeeData['visits'] ?? [];
                                            foreach ($days as $dayKey => $dayName) {
                                                $dayVisits = count($weekVisits[$dayKey]['visits'] ?? []);
                                                $weekTotalVisits += $dayVisits;

                                                if (!empty($weekVisits[$dayKey]['visits'])) {
                                                    foreach ($weekVisits[$dayKey]['visits'] as $visit) {
                                                        if ($visit->status === 'active') {
                                                            $weekCompletedVisits++;
                                                        }
                                                    }
                                                }
                                                $weekNewClients += $weekVisits[$dayKey]['new_clients_count'] ?? 0;
                                            }
                                        }

                                        $weekIncompletedVisits = $weekTotalVisits - $weekCompletedVisits;

                                        // حساب تاريخ بداية ونهاية الأسبوع
                                        $weekStartDate = new DateTime();
                                        $weekStartDate->setISODate($year, $weekNum);
                                        $weekEndDate = clone $weekStartDate;
                                        $weekEndDate->modify('+6 days');
                                    @endphp

                                    <div class="accordion-item">
                                        <div class="accordion-header" id="weekHeading{{ $accordionItemIndex }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#weekCollapse{{ $accordionItemIndex }}"
                                                aria-expanded="false"
                                                aria-controls="weekCollapse{{ $accordionItemIndex }}">
                                                <div class="week-info-header">
                                                    <div class="week-avatar">
                                                        <i class="fas fa-calendar-week"></i>
                                                    </div>
                                                    <div class="week-details">
                                                        <h5 class="week-title">
                                                            الأسبوع {{ $weekNum }} - {{ $year }}
                                                        </h5>
                                                        <div class="week-date-range mb-2">
                                                            <span class="badge bg-info me-2">
                                                                <i class="far fa-calendar me-1"></i>
                                                                {{ $weekStartDate->format('d/m/Y') }} - {{ $weekEndDate->format('d/m/Y') }}
                                                            </span>
                                                        </div>
                                                        <div class="week-stats">
                                                            <span class="badge bg-primary me-2">
                                                                <i class="fas fa-users me-1"></i>
                                                                {{ $weekEmployeesCount }} مندوب
                                                            </span>
                                                            <span class="badge bg-secondary me-2">
                                                                <i class="fas fa-building me-1"></i>
                                                                {{ $weekTotalVisits }} زيارات
                                                            </span>
                                                            <span class="badge bg-success me-2">
                                                                <i class="fas fa-check-circle me-1"></i>
                                                                {{ $weekCompletedVisits }} مكتملة
                                                            </span>
                                                            @if ($weekIncompletedVisits > 0)
                                                                <span class="badge bg-warning me-2">
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    {{ $weekIncompletedVisits }} غير مكتملة
                                                                </span>
                                                            @endif
                                                            @if ($weekNewClients > 0)
                                                                <span class="badge bg-info">
                                                                    <i class="fas fa-user-plus me-1"></i>
                                                                    {{ $weekNewClients }} عملاء جدد
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <i class="fas fa-chevron-down accordion-arrow"></i>
                                            </button>
                                        </div>

                                        <div id="weekCollapse{{ $accordionItemIndex }}"
                                             class="accordion-collapse collapse"
                                             aria-labelledby="weekHeading{{ $accordionItemIndex }}"
                                             data-bs-parent="#weeklyAccordion">
                                            <div class="accordion-body">
                                                <!-- أزرار التحكم بالموظفين -->
                                                <div class="employees-controls mb-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0">
                                                            <i class="fas fa-users text-primary me-2"></i>
                                                            الموظفين في هذا الأسبوع ({{ $weekEmployeesCount }})
                                                        </h6>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-outline-primary btn-sm expand-employees-btn"
                                                                    data-week="{{ $accordionItemIndex }}">
                                                                <i class="fas fa-eye me-1"></i>
                                                                عرض الكل
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm collapse-employees-btn"
                                                                    data-week="{{ $accordionItemIndex }}">
                                                                <i class="fas fa-eye-slash me-1"></i>
                                                                إخفاء الكل
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="employees-in-week" id="employeesContainer{{ $accordionItemIndex }}">
                                                    @foreach ($weekEmployees as $employeeId => $employeeData)
                                                        @php
                                                            $employee = $employeeData['employee'];
                                                            $weekVisits = $employeeData['visits'] ?? [];
                                                            $employeeIndex = $loop->index;

                                                            // حساب إحصائيات الموظف في هذا الأسبوع
                                                            $employeeWeekTotalVisits = 0;
                                                            $employeeWeekCompletedVisits = 0;
                                                            $employeeWeekNewClients = 0;

                                                            foreach ($days as $dayKey => $dayName) {
                                                                $dayVisits = count($weekVisits[$dayKey]['visits'] ?? []);
                                                                $employeeWeekTotalVisits += $dayVisits;

                                                                if (!empty($weekVisits[$dayKey]['visits'])) {
                                                                    foreach ($weekVisits[$dayKey]['visits'] as $visit) {
                                                                        if ($visit->status === 'active') {
                                                                            $employeeWeekCompletedVisits++;
                                                                        }
                                                                    }
                                                                }
                                                                $employeeWeekNewClients += $weekVisits[$dayKey]['new_clients_count'] ?? 0;
                                                            }

                                                            $employeeWeekIncompletedVisits = $employeeWeekTotalVisits - $employeeWeekCompletedVisits;
                                                        @endphp

                                                        <div class="employee-section mb-4"
                                                             data-employee-id="{{ $employeeId }}"
                                                             data-week="{{ $accordionItemIndex }}">
                                                            <!-- رأس الموظف مع إمكانية النقر للتوسيع/الطي -->
                                                            <div class="employee-header cursor-pointer"
                                                                 onclick="toggleEmployee({{ $accordionItemIndex }}, {{ $employeeId }})">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div class="employee-info d-flex align-items-center">
                                                                        <div class="employee-toggle-icon me-2">
                                                                            <i class="fas fa-chevron-right transition-all"
                                                                               id="employeeToggle{{ $accordionItemIndex }}_{{ $employeeId }}"></i>
                                                                        </div>
                                                                        <div class="employee-avatar">
                                                                            <i class="fas fa-user"></i>
                                                                        </div>
                                                                        <div class="employee-details">
                                                                            <h6 class="employee-name">{{ $employee->name ?? 'غير معروف' }}</h6>
                                                                            <div class="employee-stats">
                                                                                <span class="badge bg-secondary me-2">
                                                                                    <i class="fas fa-building me-1"></i>
                                                                                    {{ $employeeWeekTotalVisits }} زيارات
                                                                                </span>
                                                                                <span class="badge bg-success me-2">
                                                                                    <i class="fas fa-check-circle me-1"></i>
                                                                                    {{ $employeeWeekCompletedVisits }} مكتملة
                                                                                </span>
                                                                                @if ($employeeWeekIncompletedVisits > 0)
                                                                                    <span class="badge bg-warning me-2">
                                                                                        <i class="fas fa-clock me-1"></i>
                                                                                        {{ $employeeWeekIncompletedVisits }} غير مكتملة
                                                                                    </span>
                                                                                @endif
                                                                                @if ($employeeWeekNewClients > 0)
                                                                                    <span class="badge bg-info">
                                                                                        <i class="fas fa-user-plus me-1"></i>
                                                                                        {{ $employeeWeekNewClients }} عملاء جدد
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="employee-actions">
                                                                        <a href="{{ route('itinerary.edit', $employee->id) }}"
                                                                           class="btn btn-outline-primary btn-sm"
                                                                           onclick="event.stopPropagation();">
                                                                            <i class="fas fa-edit me-1"></i>
                                                                            تعديل
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- جدول الموظف (مخفي افتراضياً) -->
                                                            <div class="employee-schedule mt-3"
                                                                 id="employeeSchedule{{ $accordionItemIndex }}_{{ $employeeId }}"
                                                                 style="display: none;">
                                                                <div class="row g-3">
                                                                    @foreach ($days as $dayKey => $dayName)
                                                                        @php
                                                                            $dayVisits = $weekVisits[$dayKey]['visits'] ?? [];
                                                                            $dayVisitCount = count($dayVisits);
                                                                            $dayCompletedVisits = 0;
                                                                            $dayNewClients = $weekVisits[$dayKey]['new_clients_count'] ?? 0;

                                                                            foreach ($dayVisits as $visit) {
                                                                                if ($visit->status === 'active') {
                                                                                    $dayCompletedVisits++;
                                                                                }
                                                                            }
                                                                        @endphp

                                                                        <div class="col-lg-3 col-md-6">
                                                                            <div class="day-card">
                                                                                <div class="day-header">
                                                                                    <h6 class="day-name">{{ $dayName }}</h6>
                                                                                    <div class="d-flex align-items-center">
                                                                                        <span class="visits-count">
                                                                                            {{ $dayVisitCount }} زيارة
                                                                                        </span>
                                                                                        @if ($dayNewClients > 0)
                                                                                            <span class="badge bg-info ms-2"
                                                                                                  title="عملاء جدد اليوم">
                                                                                                <i class="fas fa-user-plus me-1"></i>
                                                                                                {{ $dayNewClients }}
                                                                                            </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                                <div class="day-visits">
                                                                                    @if ($dayVisitCount > 0)
                                                                                        <div class="day-stats mb-2">
                                                                                            <span class="badge bg-success text-white">
                                                                                                <i class="fas fa-check-circle me-1"></i>
                                                                                                {{ $dayCompletedVisits }} مكتملة
                                                                                            </span>
                                                                                            @if (($dayVisitCount - $dayCompletedVisits) > 0)
                                                                                                <span class="badge bg-warning text-dark ms-1">
                                                                                                    <i class="fas fa-clock me-1"></i>
                                                                                                    {{ ($dayVisitCount - $dayCompletedVisits) }} غير مكتملة
                                                                                                </span>
                                                                                            @endif
                                                                                        </div>

                                                                                        @foreach ($dayVisits as $index => $visit)
                                                                                            @if (isset($visit->client))
                                                                                                @php
                                                                                                    $client = $visit->client;
                                                                                                    $lastNote = $client
                                                                                                        ->appointmentNotes()
                                                                                                        ->where('employee_id', auth()->id())
                                                                                                        ->where('process', 'إبلاغ المشرف')
                                                                                                        ->whereNotNull('employee_view_status')
                                                                                                        ->latest()
                                                                                                        ->first();

                                                                                                    $statusToShow = $client->status_client;

                                                                                                    if (
                                                                                                        auth()->user()->role === 'employee' &&
                                                                                                        $lastNote &&
                                                                                                        $lastNote->employee_id == auth()->id()
                                                                                                    ) {
                                                                                                        $statusToShow = $statuses->find($lastNote->employee_view_status);
                                                                                                    }

                                                                                                    $isNewClient = $visit->client->is_new_for_visit_date ?? false;
                                                                                                @endphp

                                                                                                <div class="visit-card-wrapper d-flex align-items-center justify-content-between mb-2 flex-wrap">
                                                                                                    <a href="{{ route('clients.show', $visit->client->id) }}"
                                                                                                       class="visit-card flex-grow-1 d-flex align-items-center text-decoration-none text-dark flex-wrap">
                                                                                                        <div class="visit-number me-3">
                                                                                                            {{ $index + 1 }}
                                                                                                        </div>

                                                                                                        <div class="visit-info me-auto">
                                                                                                            <h6 class="client-name mb-0">
                                                                                                                {{ $visit->client->trade_name ?? 'غير معروف' }}
                                                                                                            </h6>
                                                                                                            <p class="client-code mb-0 text-muted">
                                                                                                                {{ $visit->client->code ?? '---' }}
                                                                                                            </p>
                                                                                                        </div>

                                                                                                        <div class="d-flex align-items-center flex-wrap">
                                                                                                            @if ($visit->status === 'active')
                                                                                                                <span class="badge bg-success me-2 mb-1" style="font-size: 11px;">
                                                                                                                    <i class="fas fa-check-circle me-1"></i>
                                                                                                                    تمت الزيارة
                                                                                                                </span>
                                                                                                            @else
                                                                                                                <span class="badge bg-secondary me-2 mb-1" style="font-size: 11px;">
                                                                                                                    <i class="fas fa-times-circle me-1"></i>
                                                                                                                    لم تتم الزيارة
                                                                                                                </span>
                                                                                                            @endif

                                                                                                            @if ($isNewClient)
                                                                                                                <span class="badge bg-info me-2 mb-1" style="font-size: 11px;" title="عميل جديد">
                                                                                                                    <i class="fas fa-user-plus me-1"></i>
                                                                                                                    جديد
                                                                                                                </span>
                                                                                                            @endif

                                                                                                            @if ($statusToShow)
                                                                                                                <span class="badge rounded-pill mb-1" style="background-color: {{ $statusToShow->color }}; font-size: 11px;">
                                                                                                                    <i class="fas fa-circle me-1"></i>
                                                                                                                    {{ $statusToShow->name }}
                                                                                                                </span>
                                                                                                            @else
                                                                                                                <span class="badge rounded-pill bg-secondary mb-1" style="font-size: 11px;">
                                                                                                                    <i class="fas fa-question-circle me-1"></i>
                                                                                                                    غير محدد
                                                                                                                </span>
                                                                                                            @endif
                                                                                                        </div>
                                                                                                    </a>

                                                                                                    <button type="button"
                                                                                                            class="btn btn-sm btn-danger ms-3 delete-visit-btn"
                                                                                                            title="حذف الزيارة"
                                                                                                            data-visit-id="{{ $visit->id ?? '' }}"
                                                                                                            data-url="{{ isset($visit->id) ? route('itinerary.visits.destroy', $visit->id) : '#' }}">
                                                                                                        <i class="fas fa-trash"></i>
                                                                                                    </button>
                                                                                                </div>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    @else
                                                                                        <div class="no-visits text-center mt-3">
                                                                                            <i class="fas fa-calendar-times fa-2x text-muted"></i>
                                                                                            <p class="text-muted mt-2">لا يوجد زيارات مخططة</p>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // دالة لتوسيع جميع الأسابيع
// دالة لتوسيع جميع الأسابيع
function expandAll() {
    const collapseElements = document.querySelectorAll('.accordion-collapse');
    collapseElements.forEach(element => {
        if (!element.classList.contains('show')) {
            const button = document.querySelector(`[data-bs-target="#${element.id}"]`);
            if (button) {
                button.click();
            }
        }
    });
}

// دالة لطي جميع الأسابيع
function collapseAll() {
    const collapseElements = document.querySelectorAll('.accordion-collapse.show');
    collapseElements.forEach(element => {
        const button = document.querySelector(`[data-bs-target="#${element.id}"]`);
        if (button) {
            button.click();
        }
    });
}

// دالة لتوسيع جميع الموظفين في جميع الأسابيع
function expandAllEmployees() {
    const employeeSchedules = document.querySelectorAll('.employee-schedule');
    employeeSchedules.forEach(schedule => {
        if (schedule.style.display === 'none') {
            schedule.style.display = 'block';
            // تدوير الأيقونة
            const scheduleId = schedule.id;
            const toggleIcon = document.querySelector(`#employeeToggle${scheduleId.replace('employeeSchedule', '')}`);
            if (toggleIcon) {
                toggleIcon.classList.remove('fa-chevron-right');
                toggleIcon.classList.add('fa-chevron-down');
            }
        }
    });
}

// دالة لطي جميع الموظفين في جميع الأسابيع
function collapseAllEmployees() {
    const employeeSchedules = document.querySelectorAll('.employee-schedule');
    employeeSchedules.forEach(schedule => {
        if (schedule.style.display === 'block') {
            schedule.style.display = 'none';
            // تدوير الأيقونة
            const scheduleId = schedule.id;
            const toggleIcon = document.querySelector(`#employeeToggle${scheduleId.replace('employeeSchedule', '')}`);
            if (toggleIcon) {
                toggleIcon.classList.remove('fa-chevron-down');
                toggleIcon.classList.add('fa-chevron-right');
            }
        }
    });
}

// دالة لتوسيع/طي موظف معين
function toggleEmployee(weekIndex, employeeId) {
    const scheduleElement = document.getElementById(`employeeSchedule${weekIndex}_${employeeId}`);
    const toggleIcon = document.getElementById(`employeeToggle${weekIndex}_${employeeId}`);

    if (scheduleElement) {
        if (scheduleElement.style.display === 'none') {
            scheduleElement.style.display = 'block';
            if (toggleIcon) {
                toggleIcon.classList.remove('fa-chevron-right');
                toggleIcon.classList.add('fa-chevron-down');
            }
        } else {
            scheduleElement.style.display = 'none';
            if (toggleIcon) {
                toggleIcon.classList.remove('fa-chevron-down');
                toggleIcon.classList.add('fa-chevron-right');
            }
        }
    }
}

// دالة لتوسيع جميع الموظفين في أسبوع معين
function expandWeekEmployees(weekIndex) {
    const employeeSchedules = document.querySelectorAll(`#employeesContainer${weekIndex} .employee-schedule`);
    employeeSchedules.forEach(schedule => {
        if (schedule.style.display === 'none') {
            schedule.style.display = 'block';
            // تدوير الأيقونة
            const scheduleId = schedule.id;
            const toggleIcon = document.querySelector(`#employeeToggle${scheduleId.replace('employeeSchedule', '')}`);
            if (toggleIcon) {
                toggleIcon.classList.remove('fa-chevron-right');
                toggleIcon.classList.add('fa-chevron-down');
            }
        }
    });
}

// دالة لطي جميع الموظفين في أسبوع معين
function collapseWeekEmployees(weekIndex) {
    const employeeSchedules = document.querySelectorAll(`#employeesContainer${weekIndex} .employee-schedule`);
    employeeSchedules.forEach(schedule => {
        if (schedule.style.display === 'block') {
            schedule.style.display = 'none';
            // تدوير الأيقونة
            const scheduleId = schedule.id;
            const toggleIcon = document.querySelector(`#employeeToggle${scheduleId.replace('employeeSchedule', '')}`);
            if (toggleIcon) {
                toggleIcon.classList.remove('fa-chevron-down');
                toggleIcon.classList.add('fa-chevron-right');
            }
        }
    });
}

// تهيئة الأحداث عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // إضافة أحداث أزرار التحكم بالموظفين لكل أسبوع
    document.querySelectorAll('.expand-employees-btn').forEach(button => {
        button.addEventListener('click', function() {
            const weekIndex = this.getAttribute('data-week');
            expandWeekEmployees(weekIndex);
        });
    });

    document.querySelectorAll('.collapse-employees-btn').forEach(button => {
        button.addEventListener('click', function() {
            const weekIndex = this.getAttribute('data-week');
            collapseWeekEmployees(weekIndex);
        });
    });

    // إضافة أحداث أزرار حذف الزيارات
    document.querySelectorAll('.delete-visit-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const visitId = this.getAttribute('data-visit-id');
            const deleteUrl = this.getAttribute('data-url');

            if (!visitId || !deleteUrl || deleteUrl === '#') {
                alert('خطأ: معرف الزيارة غير صحيح');
                return;
            }

            if (confirm('هل أنت متأكد من حذف هذه الزيارة؟')) {
                // إرسال طلب الحذف
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // إزالة العنصر من DOM
                        this.closest('.visit-card-wrapper').remove();
                        alert('تم حذف الزيارة بنجاح');

                        // إعادة تحميل الصفحة لتحديث الإحصائيات
                        location.reload();
                    } else {
                        alert('حدث خطأ أثناء حذف الزيارة: ' + (data.message || 'خطأ غير معروف'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء حذف الزيارة');
                });
            }
        });
    });

    // إضافة تأثيرات hover للكروت
    document.querySelectorAll('.visit-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });

    // إضافة تأثيرات للأكورديون
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-bs-target');
            const targetElement = document.querySelector(target);
            const arrow = this.querySelector('.accordion-arrow');

            if (targetElement) {
                targetElement.addEventListener('shown.bs.collapse', function() {
                    if (arrow) {
                        arrow.style.transform = 'rotate(180deg)';
                    }
                });

                targetElement.addEventListener('hidden.bs.collapse', function() {
                    if (arrow) {
                        arrow.style.transform = 'rotate(0deg)';
                    }
                });
            }
        });
    });
});

// دالة لتصدير البيانات (اختيارية)
function exportData() {
    // يمكن إضافة وظيفة تصدير البيانات هنا
    console.log('تصدير البيانات...');
}

// دالة للبحث في الزيارات (اختيارية)
function searchVisits(searchTerm) {
    const visitCards = document.querySelectorAll('.visit-card');

    visitCards.forEach(card => {
        const clientName = card.querySelector('.client-name').textContent.toLowerCase();
        const clientCode = card.querySelector('.client-code').textContent.toLowerCase();

        if (clientName.includes(searchTerm.toLowerCase()) || clientCode.includes(searchTerm.toLowerCase())) {
            card.closest('.visit-card-wrapper').style.display = 'block';
        } else {
            card.closest('.visit-card-wrapper').style.display = 'none';
        }
    });
}

// دالة لتصفية الزيارات حسب الحالة
function filterByStatus(status) {
    const visitCards = document.querySelectorAll('.visit-card-wrapper');

    visitCards.forEach(wrapper => {
        const statusBadge = wrapper.querySelector('.badge');

        if (status === 'all') {
            wrapper.style.display = 'block';
        } else if (status === 'completed' && statusBadge && statusBadge.textContent.includes('تمت الزيارة')) {
            wrapper.style.display = 'block';
        } else if (status === 'pending' && statusBadge && statusBadge.textContent.includes('لم تتم الزيارة')) {
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
        }
    });
}

// دالة لطباعة التقرير
function printReport() {
    window.print();
}

// إضافة CSS للطباعة
const printStyles = `
    @media print {
        .btn, .employee-actions, .delete-visit-btn {
            display: none !important;
        }

        .accordion-collapse {
            display: block !important;
        }

        .employee-schedule {
            display: block !important;
        }

        .page-header {
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
        }

        .stats-card {
            border: 1px solid #000;
            margin-bottom: 10px;
        }

        .accordion-item {
            border: 1px solid #000;
            margin-bottom: 15px;
        }

        .day-card {
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        .visit-card {
            border: 1px solid #eee;
            margin-bottom: 5px;
            padding: 5px;
        }
    }
`;

// إضافة styles للطباعة
const styleElement = document.createElement('style');
styleElement.textContent = printStyles;
document.head.appendChild(styleElement);

</script>
@endsection
