@extends('master')

@section('title', 'تعديل خط السير للمندوب')

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
        }

        .empty-day-message {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            background-color: #fafafa;
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
        /* إضافة هذه الأنماط لملف CSS الموجود */

.available-client-card.client-assigned {
    background-color: #f8f9fa;
    border-left-color: #28a745;
    opacity: 0.8;
}

.available-client-card.client-assigned:hover {
    opacity: 1;
}

/* تحسين شكل زر الحفظ أثناء التحميل */
#save-itinerary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* تحسين رسائل الحالة الفارغة */
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

/* تحسين عرض العدادات */
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

/* تحسين شكل البطاقات المحددة */
.selected-client-card {
    animation: slideInRight 0.3s ease-in-out;
    border-left: 4px solid #28a745;
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
    </style>

    <div class="card">
        <div class="card-body">
            <div class="container-fluid">
                <div class="itinerary-header">
                    <h2><i class="fas fa-route text-primary"></i> تعديل خط السير للمندوب: {{ $employee->name }}</h2>
                    <h6 id="week-info" class="text-muted"></h6>
                    <button id="save-itinerary" class="btn btn-success shadow-sm">
                        <i class="fas fa-save"></i> حفظ التعديلات
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
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="year-select" class="font-weight-bold">السنة</label>
                                        <select id="year-select" class="form-control client-select">
                                            @for ($year = date('Y') - 2; $year <= date('Y') + 2; $year++)
                                                <option value="{{ $year }}"
                                                    {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="week-select" class="font-weight-bold">الأسبوع</label>
                                        <select id="week-select" class="form-control client-select select2">
                                            @for ($i = 1; $i <= 52; $i++)
                                                <option value="{{ $i }}"
                                                    {{ $i == $currentWeek ? 'selected' : '' }}>الأسبوع {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="group-select" class="font-weight-bold">اختر مجموعة العملاء</label>
                                    <select id="group-select" class="form-control client-select select2">
                                        <option value="">-- اختر مجموعة --</option>
                                        @foreach ($groups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
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
                                        <p class="text-center text-muted mt-4">الرجاء اختيار مجموعة لعرض العملاء.</p>
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
                                    'sunday' => ['name' => 'الأحد', 'icon' => 'fa-calendar-day'],
                                    'monday' => ['name' => 'الإثنين', 'icon' => 'fa-calendar-day'],
                                    'tuesday' => ['name' => 'الثلاثاء', 'icon' => 'fa-calendar-day'],
                                    'wednesday' => ['name' => 'الأربعاء', 'icon' => 'fa-calendar-day'],
                                    'thursday' => ['name' => 'الخميس', 'icon' => 'fa-calendar-day'],
                                    'friday' => ['name' => 'الجمعة', 'icon' => 'fa-calendar-day'],
                                ];
                            @endphp

                            @foreach ($days as $day => $dayInfo)
                                <div class="day-assignment" data-day="{{ $day }}">
                                    <div class="day-title">
                                        <i class="fas {{ $dayInfo['icon'] }}"></i>
                                        {{ $dayInfo['name'] }}
                                        <span class="client-count-badge" id="count-{{ $day }}">0 عميل</span>
                                    </div>

                                    <div class="client-select-wrapper">
                                        <select class="client-select day-client-select select2"
                                            data-day="{{ $day }}" disabled>
                                            <option value="">-- اختر عميل لإضافته --</option>
                                        </select>
                                    </div>

                                    <div class="selected-clients-list" id="clients-{{ $day }}">
                                        @php
                                            $dayVisit = $itinerary[$day] ?? null;
                                        @endphp
                                        @if (!$dayVisit)
                                            <div class="empty-day-message">
                                                <i class="fas fa-info-circle text-muted"></i>
                                                لا توجد عملاء محددين لهذا اليوم
                                            </div>
                                        @else
                                            <div class="selected-client-card">
                                                <div class="selected-client-info">
                                                    <h5 class="client-name">{{ $dayVisit->client->trade_name }}</h5>
                                                    <p class="client-meta">
                                                        <i class="fas fa-code text-primary"></i>
                                                        {{ $dayVisit->client->code }}
                                                        <i class="fas fa-map-marker-alt text-success"></i>
                                                        {{ $dayVisit->client->city }}
                                                    </p>
                                                </div>
                                                <button class="remove-client-btn" data-day="{{ $day }}"
                                                    data-client-id="{{ $dayVisit->client_id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endif
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
            const employeeId = {{ $employee->id }};
            let currentYear = {{ $currentYear }};
            let currentWeek = {{ $currentWeek }};
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

            // --- UI Elements ---
            const yearSelect = $('#year-select');
            const weekSelect = $('#week-select');
            const groupSelect = $('#group-select');
            const availableClientsList = $('#available-clients-list');
            const spinner = $('#available-clients-container .loading-spinner');

            // --- Initialization ---
            updateWeekInfo();

            // تحميل البيانات المحفوظة عند بدء تشغيل الصفحة
            loadSavedItinerary();

            // --- Event Listeners ---

            // 1. Year selection change
            yearSelect.on('change', function() {
                currentYear = $(this).val();
                updateWeekInfo();
                loadSavedItinerary();
            });

            // 2. Week selection change
            weekSelect.on('change', function() {
                currentWeek = $(this).val();
                updateWeekInfo();
                loadSavedItinerary();
            });

            // 3. Group selection change
            groupSelect.on('change', function() {
                const groupId = $(this).val();
                if (groupId) {
                    fetchClientsForGroup(groupId);
                } else {
                    availableClientsList.html(
                        '<p class="text-center text-muted">الرجاء اختيار مجموعة لعرض العملاء.</p>');
                    $('.day-client-select').prop('disabled', true).html(
                        '<option value="">-- اختر مجموعة أولاً --</option>');
                }
            });

            // 4. Day client selection change
            $(document).on('change', '.day-client-select', function() {
                const day = $(this).data('day');
                const clientId = $(this).val();

                if (clientId) {
                    const client = availableClients.find(c => c.id == clientId);
                    if (client && !dayAssignments[day].find(c => c.id == clientId)) {
                        addClientToDay(day, client);
                        $(this).val(''); // Reset selection
                    }
                }
            });

            // 5. Remove client from day
            $(document).on('click', '.remove-client-btn', function() {
                const day = $(this).data('day');
                const clientId = $(this).data('client-id');
                removeClientFromDay(day, clientId);
            });

            // 6. Save Itinerary button click
            $('#save-itinerary').on('click', function() {
                saveItinerary();
            });

            // 7. Client search input
            $('#client-search').on('keyup', function() {
                filterAvailableClients($(this).val());
            });

            // --- Core Functions ---

            function loadSavedItinerary() {
                // مسح البيانات الحالية
                Object.keys(dayAssignments).forEach(day => {
                    dayAssignments[day] = [];
                });

                // جلب البيانات المحفوظة
                $.ajax({
                    url: `/api/employees/${employeeId}/itinerary`,
                    method: 'GET',
                    data: {
                        year: currentYear,
                        week: currentWeek
                    },
                    success: function(itinerary) {
                        console.log('تم جلب البيانات المحفوظة:', itinerary);

                        if (itinerary && itinerary.length > 0) {
                            // تجميع البيانات حسب اليوم
                            itinerary.forEach(visit => {
                                const day = visit.day_of_week;
                                if (visit.client && dayAssignments[day]) {
                                    // التأكد من عدم تكرار العميل في نفس اليوم
                                    if (!dayAssignments[day].find(c => c.id == visit.client
                                        .id)) {
                                        dayAssignments[day].push(visit.client);
                                    }
                                }
                            });

                            // تحديث عرض جميع الأيام
                            Object.keys(dayAssignments).forEach(day => {
                                updateDayDisplay(day);
                            });

                            console.log('تم تحديث dayAssignments:', dayAssignments);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('خطأ في جلب البيانات المحفوظة:', error);
                        Swal.fire('تنبيه', 'حدث خطأ في جلب البيانات المحفوظة', 'warning');
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
                        console.log('تم جلب عملاء المجموعة:', clients);

                        // دمج العملاء الجدد مع الموجودين في خط السير
                        const assignedClients = [];
                        Object.values(dayAssignments).forEach(dayClients => {
                            dayClients.forEach(client => {
                                if (!assignedClients.find(c => c.id === client.id)) {
                                    assignedClients.push(client);
                                }
                            });
                        });

                        availableClients = [...clients, ...assignedClients];

                        // إزالة التكرارات
                        availableClients = availableClients.filter((client, index, self) =>
                            index === self.findIndex((c) => c.id === client.id)
                        );

                        updateAvailableClientsList();
                        updateDayClientSelects();

                        if (availableClients.length > 0) {
                            $('.day-client-select').prop('disabled', false);
                        } else {
                            availableClientsList.html(
                                '<p class="text-center text-muted">لا يوجد عملاء متاحين في هذه المجموعة.</p>'
                            );
                        }
                    },
                    error: function(xhr) {
                        spinner.hide();
                        let errorMsg = 'فشل في جلب العملاء.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('خطأ', errorMsg, 'error');
                        console.error('Error details:', xhr.responseJSON);
                    }
                });
            }

            function addClientToDay(day, client) {
                // التأكد من عدم وجود العميل مسبقاً
                if (!dayAssignments[day].find(c => c.id == client.id)) {
                    dayAssignments[day].push(client);
                    updateDayDisplay(day);
                    updateDayClientSelects();
                }
            }

            function removeClientFromDay(day, clientId) {
                dayAssignments[day] = dayAssignments[day].filter(c => c.id != clientId);
                updateDayDisplay(day);
                updateDayClientSelects();
            }

            function updateDayDisplay(day) {
                const container = $(`#clients-${day}`);
                const countBadge = $(`#count-${day}`);

                container.empty();

                if (dayAssignments[day].length === 0) {
                    container.html(`
                <div class="empty-day-message">
                    <i class="fas fa-calendar-plus text-muted"></i>
                    <p>لم يتم تعيين عملاء لهذا اليوم بعد</p>
                </div>
            `);
                    countBadge.text('0 عميل');
                } else {
                    dayAssignments[day].forEach(client => {
                        const clientCard = createSelectedClientCard(client, day);
                        container.append(clientCard);
                    });
                    countBadge.text(`${dayAssignments[day].length} عميل`);
                }

                // تفعيل tooltips
                $('[data-toggle="tooltip"]').tooltip();
            }

            function updateDayClientSelects() {
                $('.day-client-select').each(function() {
                    const day = $(this).data('day');
                    const currentVal = $(this).val();

                    // Get clients not assigned to this day
                    const availableForDay = availableClients.filter(client =>
                        !dayAssignments[day].find(assigned => assigned.id == client.id)
                    );

                    let options = '<option value="">-- اختر عميل لإضافته --</option>';
                    availableForDay.forEach(client => {
                        options +=
                            `<option value="${client.id}">${client.trade_name} - ${client.code}</option>`;
                    });

                    $(this).html(options);
                    if (currentVal && availableForDay.find(c => c.id == currentVal)) {
                        $(this).val(currentVal);
                    }
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
                    const clientCard = createAvailableClientCard(client);
                    availableClientsList.append(clientCard);
                });

                $('[data-toggle="tooltip"]').tooltip();
            }

            function saveItinerary() {
                const visits = {};
                Object.keys(dayAssignments).forEach(day => {
                    visits[day] = dayAssignments[day].map(client => client.id);
                });

                console.log('بيانات الحفظ:', visits);

                // تعطيل زر الحفظ أثناء العملية
                const saveBtn = $('#save-itinerary');
                const originalText = saveBtn.html();
                saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');

                $.ajax({
                    url: '{{ route('itinerary.update', $employee->id) }}',
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        employee_id: employeeId,
                        year: currentYear,
                        week_number: currentWeek,
                        visits: visits
                    },
                    success: function(response) {
                        saveBtn.prop('disabled', false).html(originalText);

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحفظ بنجاح!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('خطأ', response.message || 'حدث خطأ غير متوقع.', 'error');
                        }
                    },
                    error: function(xhr) {
                        saveBtn.prop('disabled', false).html(originalText);

                        let errorMsg = 'فشل في الاتصال بالخادم.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('خطأ', errorMsg, 'error');
                        console.error('خطأ في الحفظ:', xhr.responseJSON);
                    }
                });
            }

            // --- Helper & Utility Functions ---

            function updateWeekInfo() {
                $('#week-info').text(`العام: ${currentYear}, الأسبوع: ${currentWeek}`);
            }

            function filterAvailableClients(searchTerm) {
                const term = searchTerm.toLowerCase();
                $('.available-client-card').each(function() {
                    const name = $(this).find('.client-name').text().toLowerCase();
                    const code = $(this).find('.client-meta').text().toLowerCase();
                    $(this).toggle(name.includes(term) || code.includes(term));
                });
            }

            function createSelectedClientCard(client, day) {
                if (!client) return '';

                const visitIcon = createActivityIcon('fa-walking', client.visits, 'زيارة');
                const invoiceIcon = createActivityIcon('fa-file-invoice-dollar', client.invoices, 'فاتورة');
                const noteIcon = createActivityIcon('fa-sticky-note', client.appointment_notes, 'ملاحظة');
                const receiptIcon = createActivityIcon('fa-receipt', client.receipts, 'سند قبض');

                return `
            <div class="selected-client-card" data-client-id="${client.id}">
                <div class="selected-client-info">
                    <div class="client-name">${client.trade_name}</div>
                    <div class="client-meta">الكود: ${client.code} | ${client.city || 'غير محدد'}</div>
                    <div class="activity-icons mt-1">${visitIcon}${invoiceIcon}${noteIcon}${receiptIcon}</div>
                </div>
                <button class="remove-client-btn" data-day="${day}" data-client-id="${client.id}" title="إزالة العميل">
                    <i class="fas fa-times"></i>
                </button>
            </div>`;
            }

            function createAvailableClientCard(client) {
                if (!client) return '';

                const visitIcon = createActivityIcon('fa-walking', client.visits, 'زيارة');
                const invoiceIcon = createActivityIcon('fa-file-invoice-dollar', client.invoices, 'فاتورة');
                const noteIcon = createActivityIcon('fa-sticky-note', client.appointment_notes, 'ملاحظة');
                const receiptIcon = createActivityIcon('fa-receipt', client.receipts, 'سند قبض');

                // التحقق من وجود العميل في أي يوم
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
                if (data && data.length > 0) {
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
                } else {
                    return `<i class="fas ${iconClass} text-muted" data-toggle="tooltip" title="لا يوجد ${type}ات"></i>`;
                }
            }

            // Initialize tooltips on page load
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
