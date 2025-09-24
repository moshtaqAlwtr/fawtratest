@extends('master')

@section('title', 'تخطيط خط السير للمناديب')

@section('content')
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Cairo', sans-serif;
        }

        .itinerary-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .itinerary-header h2 {
            color: #333;
            font-weight: 700;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .card-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            font-weight: 600;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            padding: 1rem 1.5rem;
        }

        .day-card .card-header {
            background: linear-gradient(135deg, #343a40, #495057);
        }

        .client-assignment-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .day-assignment {
            margin-bottom: 25px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #007bff;
        }

        .day-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .day-title i {
            color: #007bff;
        }

        .client-select-wrapper {
            position: relative;
            margin-bottom: 10px;
        }

        .client-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.95rem;
            background-color: white;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: left 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-left: 40px;
        }

        .client-select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .client-select:hover {
            border-color: #007bff;
        }

        .selected-clients-list {
            min-height: 150px;
            max-height: 300px;
            overflow-y: auto;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .selected-client-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            margin-bottom: 10px;
            background: white;
            border: 1px solid #e0e0e0;
            border-left: 4px solid #28a745;
            border-radius: 8px;
            transition: all 0.3s ease;
            animation: slideInRight 0.3s ease-in-out;
        }

        .selected-client-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .selected-client-info .client-name {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .selected-client-info .client-meta {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 2px;
        }

        .remove-client-btn {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }

        .remove-client-btn:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .client-count-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 10px;
            min-width: 60px;
            text-align: center;
        }

        .empty-day-message {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 25px 20px;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            background-color: #fafafa;
            margin: 10px 0;
        }

        .empty-day-message i {
            font-size: 1.5rem;
            margin-bottom: 10px;
            display: block;
        }

        .activity-icons {
            display: flex;
            gap: 8px;
            font-size: 1rem;
        }

        .loading-spinner {
            display: none;
            position: absolute;
            left: 50%;
            top: 40%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }

        #client-search {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        #client-search:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            outline: none;
        }

        #save-itinerary {
            font-weight: 600;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            transition: all 0.3s ease;
        }

        #save-itinerary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        #save-itinerary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .available-clients-list {
            min-height: 200px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            overflow-y: auto;
            max-height: 50vh;
        }

        .available-client-card {
            padding: 12px 15px;
            margin-bottom: 10px;
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-left: 5px solid #17a2b8;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .available-client-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .available-client-card.client-assigned {
            background-color: #f8f9fa;
            border-left-color: #28a745;
            opacity: 0.8;
        }

        .available-client-card.client-assigned:hover {
            opacity: 1;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 768px) {
            .day-assignment {
                padding: 15px;
            }

            .client-select {
                padding: 10px 35px 10px 12px;
            }

            .selected-client-card {
                padding: 10px 12px;
            }
        }
    </style>

    <div class="card">
        <div class="card-body">
            <div class="container-fluid">
                <div class="itinerary-header">
                    <h2><i class="fas fa-route text-primary"></i> تخطيط خط السير الأسبوعي</h2>
                    <h6 id="week-info" class="text-muted"></h6>
                    <button id="save-itinerary" class="btn btn-success shadow-sm">
                        <i class="fas fa-save"></i> حفظ خط السير
                    </button>
                </div>

                <div class="row">
                    <!-- Left Column: Controls and Available Clients -->
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-cogs"></i> الإعدادات والفلاتر</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="employee-select" class="font-weight-bold">اختر المندوب</label>
                                    <select id="employee-select" class="form-control client-select select2"
                                        {{ auth()->user()->role === 'employee' ? 'disabled' : '' }}>
                                        @if (auth()->user()->role !== 'employee')
                                            <option value="">-- اختر مندوب --</option>
                                        @endif
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ auth()->user()->id == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- إضافة اختيار السنة والأسبوع -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="year-select" class="font-weight-bold">السنة</label>
                                        <select id="year-select" class="form-control client-select">
                                            @for ($year = date('Y') - 2; $year <= date('Y') + 2; $year++)
                                                <option value="{{ $year }}"
                                                    {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="week-select" class="font-weight-bold">الأسبوع</label>
                                        <select id="week-select" class="form-control client-select select2">
                                            @for ($i = 1; $i <= 52; $i++)
                                                <option value="{{ $i }}">الأسبوع {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="group-select" class="font-weight-bold">اختر مجموعة العملاء</label>
                                    <select id="group-select" class="form-control client-select select2"
                                        {{ auth()->user()->role === 'employee' && $groups->isEmpty() ? 'disabled' : '' }}>
                                        <option value="">-- اختر مجموعة --</option>
                                        @if (auth()->user()->role === 'employee')
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-users"></i> العملاء المتاحين</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <input type="text" id="client-search" class="form-control"
                                        placeholder="ابحث عن عميل بالاسم أو الكود...">
                                </div>
                                <div id="available-clients-container" style="position: relative;">
                                    <div class="loading-spinner spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <div id="available-clients-list" class="available-clients-list">
                                        <p class="text-center text-muted mt-4">الرجاء اختيار مندوب ومجموعة لعرض العملاء.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Weekly Schedule with Dropdown -->
                    <div class="col-lg-8">
                        <div class="client-assignment-container">
                            @php
                                $days = [
                                    'saturday' => ['name' => 'السبت', 'icon' => 'fa-calendar-day'],
                                    'sunday' => ['name' => 'الأحد', 'icon' => 'fa-sun'],
                                    'monday' => ['name' => 'الاثنين', 'icon' => 'fa-briefcase'],
                                    'tuesday' => ['name' => 'الثلاثاء', 'icon' => 'fa-calendar-check'],
                                    'wednesday' => ['name' => 'الأربعاء', 'icon' => 'fa-calendar-alt'],
                                    'thursday' => ['name' => 'الخميس', 'icon' => 'fa-calendar-week'],
                                    'friday' => ['name' => 'الجمعة', 'icon' => 'fa-mosque'],
                                ];
                            @endphp

                            @foreach ($days as $dayEn => $dayInfo)
                                <div class="day-assignment" data-day="{{ $dayEn }}">
                                    <div class="day-title">
                                        <i class="fas {{ $dayInfo['icon'] }}"></i>
                                        {{ $dayInfo['name'] }}
                                        <span class="client-count-badge" id="count-{{ $dayEn }}">0 عميل</span>
                                    </div>

                                    <div class="client-select-wrapper">
                                        <select class="client-select day-client-select select2"
                                            data-day="{{ $dayEn }}" disabled>
                                            <option value="">-- اختر عميل لإضافته --</option>
                                        </select>
                                    </div>

                                    <div class="selected-clients-list" id="clients-{{ $dayEn }}">
                                        <div class="empty-day-message">
                                            <i class="fas fa-calendar-plus text-muted"></i>
                                            لم يتم تعيين عملاء لهذا اليوم بعد
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // --- المتغيرات العامة ---
        let currentYear = {{ date('Y') }};
        let currentWeek = getCurrentWeek();
        let availableClients = [];
        let dayAssignments = {
            saturday: [],
            sunday: [],
            monday: [],
            tuesday: [],
            wednesday: [],
            thursday: [],
            friday: []
        };

        // --- عناصر الواجهة ---
        const employeeSelect = $('#employee-select');
        const yearSelect = $('#year-select');
        const weekSelect = $('#week-select');
        const groupSelect = $('#group-select');
        const availableClientsList = $('#available-clients-list');
        const spinner = $('#available-clients-container .loading-spinner');

        // --- التهيئة الأولية ---
        initializeWeekSelect();
        updateWeekInfo();

        // --- مستمعي الأحداث ---
        employeeSelect.on('change', handleEmployeeChange);
        yearSelect.on('change', handleYearChange);
        weekSelect.on('change', handleWeekChange);
        groupSelect.on('change', handleGroupChange);
        $(document).on('change', '.day-client-select', handleClientSelection);
        $(document).on('click', '.remove-client-btn', handleRemoveClient);
        $('#save-itinerary').on('click', saveItinerary);
        $('#client-search').on('keyup', handleClientSearch);

        // --- الدوال الأساسية ---

        function handleEmployeeChange() {
            const employeeId = $(this).val();
            resetUI();
            if (employeeId) {
                fetchGroupsForEmployee(employeeId);
                loadItineraryForWeek();
            }
        }

        function handleYearChange() {
            currentYear = $(this).val();
            updateWeekInfo();
            loadItineraryForWeek();
        }

        function handleWeekChange() {
            currentWeek = $(this).val();
            updateWeekInfo();
            loadItineraryForWeek();
        }

        function handleGroupChange() {
            const groupId = $(this).val();
            if (groupId) {
                fetchClientsForGroup(groupId);
            } else {
                availableClientsList.html(
                    '<p class="text-center text-muted">الرجاء اختيار مجموعة لعرض العملاء.</p>');
                $('.day-client-select').prop('disabled', true).html(
                    '<option value="">-- اختر مجموعة أولاً --</option>');
            }
        }

        function handleClientSelection() {
            const day = $(this).data('day');
            const clientId = $(this).val();

            if (clientId) {
                const client = availableClients.find(c => c.id == clientId);
                if (client && !dayAssignments[day].find(c => c.id == clientId)) {
                    addClientToDay(day, client);
                    $(this).val('');
                }
            }
        }

        function handleRemoveClient() {
            const day = $(this).data('day');
            const clientId = $(this).data('client-id');
            removeClientFromDay(day, clientId);
        }

        function handleClientSearch() {
            filterAvailableClients($(this).val());
        }

        function loadItineraryForWeek() {
            const employeeId = employeeSelect.val();
            if (!employeeId) return;

            resetDayAssignments();

            $.ajax({
                url: `/api/employees/${employeeId}/itinerary`,
                method: 'GET',
                data: { year: currentYear, week: currentWeek },
                success: function(itinerary) {
                    if (itinerary?.length > 0) {
                        itinerary.forEach(visit => {
                            const day = visit.day_of_week;
                            if (visit.client && dayAssignments[day]) {
                                if (!dayAssignments[day].find(c => c.id == visit.client.id)) {
                                    dayAssignments[day].push(visit.client);
                                }
                            }
                        });
                    }
                    updateAllDayDisplays();
                },
                error: function(xhr) {
                    console.error('خطأ في جلب البيانات:', xhr.responseJSON);
                    updateAllDayDisplays();
                }
            });
        }

        function fetchClientsForGroup(groupId) {
            spinner.show();
            $('.day-client-select').prop('disabled', true);

            $.ajax({
                url: `/api/groups/${groupId}/clients`,
                method: 'GET',
                success: function(clients) {
                    spinner.hide();
                    availableClients = mergeClients(clients, dayAssignments);
                    updateAvailableClientsList();
                    updateDayClientSelects();

                    if (availableClients.length > 0) {
                        $('.day-client-select').prop('disabled', false);
                    } else {
                        availableClientsList.html(
                            '<p class="text-center text-muted">لا يوجد عملاء متاحين في هذه المجموعة.</p>');
                    }
                },
                error: function(xhr) {
                    spinner.hide();
                    showError('فشل في جلب العملاء', xhr.responseJSON?.message);
                }
            });
        }

function saveItinerary() {
    const employeeId = employeeSelect.val();
    if (!employeeId) {
        showAlert('error', 'خطأ', 'الرجاء اختيار مندوب أولاً');
        return;
    }

    // تحضير بيانات الزيارات بشكل صحيح
    const visits = {};
    Object.keys(dayAssignments).forEach(day => {
        visits[day] = dayAssignments[day]
            .filter(client => client && client.id)
            .map(client => client.id);
    });

    // إظهار تأكيد الحفظ
    Swal.fire({
        title: 'تأكيد الحفظ',
        text: 'هل أنت متأكد من حفظ خط السير؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، احفظ',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            executeSave(employeeId, visits);
        }
    });
}

function executeSave(employeeId, visits) {
    const saveBtn = $('#save-itinerary');
    const originalText = saveBtn.html();
    saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');

    // إرسال البيانات كـ JSON
    $.ajax({
        url: '{{ route("itinerary.store") }}',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            employee_id: employeeId,
            year: currentYear,
            week_number: currentWeek,
            visits: visits,
            _token: '{{ csrf_token() }}'
        }),
        success: function(response) {
            saveBtn.prop('disabled', false).html(originalText);
            if (response.success) {
                showAlert('success', 'تم الحفظ', response.message);
            } else {
                showAlert('error', 'خطأ', response.message);
            }
        },
        error: function(xhr) {
            saveBtn.prop('disabled', false).html(originalText);
            const errorMsg = xhr.responseJSON?.message || 'فشل في الاتصال بالخادم';
            showAlert('error', 'خطأ في الحفظ', errorMsg);
            console.error('تفاصيل الخطأ:', xhr.responseJSON);
        }
    });
}

        function handleSaveResponse(response) {
            if (response.success) {
                showAlert('success', 'تم الحفظ', response.message, 2000);
                refreshItineraryView();
            } else {
                showAlert('error', 'خطأ', response.message || 'حدث خطأ غير متوقع');
            }
        }

        function handleSaveError(xhr) {
            const errorMsg = xhr.responseJSON?.message ||
                            xhr.responseJSON?.error ||
                            'فشل في الاتصال بالخادم';
            console.error('تفاصيل الخطأ:', xhr.responseJSON);
            showAlert('error', 'خطأ في الحفظ', errorMsg);
        }

        function addClientToDay(day, client) {
            if (!dayAssignments[day].find(c => c.id == client.id)) {
                dayAssignments[day].push(client);
                updateDayDisplay(day);
                updateDayClientSelects();
                updateAvailableClientsList();
            }
        }

        function removeClientFromDay(day, clientId) {
            dayAssignments[day] = dayAssignments[day].filter(c => c.id != clientId);
            updateDayDisplay(day);
            updateDayClientSelects();
            updateAvailableClientsList();
        }

        function updateDayDisplay(day) {
            const container = $(`#clients-${day}`);
            const countBadge = $(`#count-${day}`);

            container.empty();
            countBadge.text(`${dayAssignments[day].length} عميل`);

            if (dayAssignments[day].length === 0) {
                container.html(`
                    <div class="empty-day-message">
                        <i class="fas fa-calendar-plus text-muted"></i>
                        لم يتم تعيين عملاء لهذا اليوم بعد
                    </div>
                `);
            } else {
                dayAssignments[day].forEach(client => {
                    container.append(createSelectedClientCard(client, day));
                });
            }

            $('[data-toggle="tooltip"]').tooltip();
        }

        function updateAllDayDisplays() {
            Object.keys(dayAssignments).forEach(day => {
                updateDayDisplay(day);
            });
        }

        function resetDayAssignments() {
            Object.keys(dayAssignments).forEach(day => {
                dayAssignments[day] = [];
            });
        }

        function updateDayClientSelects() {
            $('.day-client-select').each(function() {
                const day = $(this).data('day');
                const availableForDay = availableClients.filter(client =>
                    !dayAssignments[day].find(assigned => assigned.id == client.id)
                );

                let options = '<option value="">-- اختر عميل لإضافته --</option>';
                availableForDay.forEach(client => {
                    options += `<option value="${client.id}">${client.trade_name} - ${client.code}</option>`;
                });

                $(this).html(options).prop('disabled', availableForDay.length === 0);
            });
        }

        function updateAvailableClientsList() {
            availableClientsList.empty();

            if (availableClients.length === 0) {
                availableClientsList.html(
                    '<p class="text-center text-muted">لا يوجد عملاء متاحين في هذه المجموعة.</p>');
                return;
            }

            availableClients.forEach(client => {
                availableClientsList.append(createAvailableClientCard(client));
            });

            $('[data-toggle="tooltip"]').tooltip();
        }

        function mergeClients(newClients, assignments) {
            const assignedClients = [];
            Object.values(assignments).forEach(dayClients => {
                dayClients.forEach(client => {
                    if (!assignedClients.find(c => c.id === client.id)) {
                        assignedClients.push(client);
                    }
                });
            });

            return [...newClients, ...assignedClients].filter((client, index, self) =>
                index === self.findIndex((c) => c.id === client.id)
            );
        }

        function filterAvailableClients(searchTerm) {
            const term = searchTerm.toLowerCase();
            $('.available-client-card').each(function() {
                const $card = $(this);
                const name = $card.find('.client-name').text().toLowerCase();
                const code = $card.find('.client-meta').text().toLowerCase();
                $card.toggle(name.includes(term) || code.includes(term));
            });
        }

        function confirmSave(totalVisits, callback) {
            Swal.fire({
                title: 'تأكيد الحفظ',
                text: `هل أنت متأكد من حفظ ${totalVisits} زيارة؟`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => result.isConfirmed && callback());
        }

        function refreshItineraryView() {
            console.log('تم تحديث عرض خط السير');
            // يمكن إضافة تحديثات إضافية هنا
        }

        function resetUI() {
            groupSelect.prop('disabled', true).html('<option value="">-- اختر مجموعة --</option>');
            availableClientsList.html(
                '<p class="text-center text-muted mt-4">اختر مندوب ومجموعة لعرض العملاء.</p>');
            $('.day-client-select').prop('disabled', true).html(
                '<option value="">-- اختر عميل لإضافته --</option>');
            resetDayAssignments();
            updateAllDayDisplays();
        }

        function showAlert(icon, title, text, timer = null) {
            const options = {
                icon: icon,
                title: title,
                text: text,
                confirmButtonText: 'حسناً',
                reverseButtons: true
            };

            if (timer) {
                options.timer = timer;
                options.showConfirmButton = false;
            }

            Swal.fire(options);
        }

        function showError(title, message) {
            Swal.fire({
                icon: 'error',
                title: title,
                text: message || 'حدث خطأ غير متوقع',
                confirmButtonText: 'حسناً'
            });
        }

        function createSelectedClientCard(client, day) {
            const visitIcon = createActivityIcon('fa-walking', client.visits, 'زيارة');
            const invoiceIcon = createActivityIcon('fa-file-invoice-dollar', client.invoices, 'فاتورة');
            const noteIcon = createActivityIcon('fa-sticky-note', client.appointment_notes, 'ملاحظة');
            const receiptIcon = createActivityIcon('fa-receipt', client.receipts, 'سند قبض');

            const isAssignedElsewhere = Object.keys(dayAssignments).some(otherDay =>
                otherDay !== day && dayAssignments[otherDay].find(c => c.id == client.id)
            );

            return `
                <div class="selected-client-card" data-client-id="${client.id}">
                    <div class="selected-client-info">
                        <div class="client-name">${client.trade_name}</div>
                        <div class="client-meta">الكود: ${client.code} | ${client.city || 'غير محدد'}</div>
                        <div class="activity-icons mt-1">${visitIcon}${invoiceIcon}${noteIcon}${receiptIcon}</div>
                        ${isAssignedElsewhere ? '<small class="text-warning"><i class="fas fa-exclamation-triangle"></i> مُعيَّن في يوم آخر</small>' : ''}
                    </div>
                    <button class="remove-client-btn" data-day="${day}" data-client-id="${client.id}" title="إزالة العميل">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`;
        }

        function createAvailableClientCard(client) {
            const visitIcon = createActivityIcon('fa-walking', client.visits, 'زيارة');
            const invoiceIcon = createActivityIcon('fa-file-invoice-dollar', client.invoices, 'فاتورة');
            const noteIcon = createActivityIcon('fa-sticky-note', client.appointment_notes, 'ملاحظة');
            const receiptIcon = createActivityIcon('fa-receipt', client.receipts, 'سند قبض');

            const isAssigned = Object.values(dayAssignments).some(dayClients =>
                dayClients.find(c => c.id == client.id)
            );

            return `
                <div class="available-client-card ${isAssigned ? 'client-assigned' : ''}" data-client-id="${client.id}">
                    <div class="client-info">
                        <strong class="client-name">${client.trade_name}</strong>
                        <small class="d-block text-muted client-meta">الكود: ${client.code} | ${client.city || 'غير محدد'}</small>
                        ${isAssigned ? '<small class="text-success"><i class="fas fa-check"></i> مُعيَّن</small>' : ''}
                    </div>
                    <div class="activity-icons">${visitIcon}${invoiceIcon}${noteIcon}${receiptIcon}</div>
                </div>`;
        }

        function createActivityIcon(iconClass, data, type) {
            if (data?.length > 0) {
                const latestItem = data[0];
                const date = new Date(latestItem.created_at).toLocaleDateString('ar-EG-u-nu-latn', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

                let tooltipText = `آخر ${type}: ${date}`;
                if (type === 'ملاحظة' && latestItem.description) {
                    tooltipText += ` - ${latestItem.description}`;
                }

                return `<i class="fas ${iconClass} text-success" data-toggle="tooltip" title="${tooltipText}"></i>`;
            }
            return `<i class="fas ${iconClass} text-muted" data-toggle="tooltip" title="لا يوجد ${type}ات"></i>`;
        }

        function getCurrentWeek() {
            const now = new Date();
            const startOfYear = new Date(now.getFullYear(), 0, 1);
            const pastDaysOfYear = (now - startOfYear) / 86400000;
            return Math.ceil((pastDaysOfYear + startOfYear.getDay() + 1) / 7);
        }

        function initializeWeekSelect() {
            const currentWeekNumber = getCurrentWeek();
            weekSelect.val(currentWeekNumber);
            currentWeek = currentWeekNumber;
        }

        function updateWeekInfo() {
            $('#week-info').text(`العام: ${currentYear}, الأسبوع: ${currentWeek}`);
        }

        function fetchGroupsForEmployee(employeeId) {
            $.ajax({
                url: `/api/employees/${employeeId}/groups`,
                method: 'GET',
                success: function(groups) {
                    let options = '<option value="">-- اختر مجموعة --</option>';
                    groups.forEach(group => options +=
                        `<option value="${group.id}">${group.name}</option>`);
                    groupSelect.html(options).prop('disabled', false);
                },
                error: function() {
                    showError('خطأ', 'فشل في جلب مجموعات العميل');
                }
            });
        }

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection
