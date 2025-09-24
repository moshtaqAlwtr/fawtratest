@extends('master')

@section('title')
    تقويم المهام
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/task.css') }}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    .calendar-container {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    
    .fc {
        direction: rtl;
        font-family: 'Cairo', sans-serif;
    }
    
    .fc-toolbar-title {
        font-size: 1.6rem !important;
        font-weight: bold;
        color: #2c3e50;
    }
    
    .fc-button {
        background: #3498db !important;
        border: none !important;
        border-radius: 6px !important;
        padding: 8px 15px !important;
        font-weight: 600 !important;
    }
    
    .fc-button:hover {
        background: #2980b9 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }
    
    .fc-button-active {
        background: #2c3e50 !important;
    }
    
    .fc-event {
        border-radius: 8px !important;
        padding: 4px 8px !important;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 12px !important;
        font-weight: 600 !important;
        border: none !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .fc-event:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 999;
    }
    
    .fc-event-title {
        font-weight: 600 !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .fc-daygrid-event {
        margin: 1px 2px !important;
        border-radius: 6px !important;
    }
    
    /* ألوان الحالات */
    .status-pending { 
        background: linear-gradient(135deg, #f39c12, #f1c40f) !important; 
        color: #fff !important; 
    }
    .status-in_progress { 
        background: linear-gradient(135deg, #3498db, #2980b9) !important; 
        color: #fff !important; 
    }
    .status-completed { 
        background: linear-gradient(135deg, #27ae60, #2ecc71) !important; 
        color: #fff !important; 
    }
    .status-cancelled { 
        background: linear-gradient(135deg, #e74c3c, #c0392b) !important; 
        color: #fff !important; 
    }
    .status-overdue {
        background: linear-gradient(135deg, #8e44ad, #9b59b6) !important;
        color: #fff !important;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    
    /* مؤشرات الأولوية */
    .priority-high { 
        border-right: 5px solid #e74c3c !important; 
        box-shadow: inset 5px 0 0 #e74c3c, 0 2px 8px rgba(0,0,0,0.1) !important;
    }
    .priority-medium { 
        border-right: 5px solid #f39c12 !important; 
        box-shadow: inset 5px 0 0 #f39c12, 0 2px 8px rgba(0,0,0,0.1) !important;
    }
    .priority-low { 
        border-right: 5px solid #27ae60 !important; 
        box-shadow: inset 5px 0 0 #27ae60, 0 2px 8px rgba(0,0,0,0.1) !important;
    }
    
    .filter-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;
        color: white;
    }
    
    .filter-section label {
        font-weight: 600;
        margin-bottom: 8px;
        color: white;
        font-size: 14px;
    }
    
    .filter-section .form-control {
        border-radius: 8px;
        border: none;
        padding: 10px 15px;
        font-size: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .filter-section .form-control:focus {
        box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
        transform: translateY(-1px);
    }
    
    .legend {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.3);
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255,255,255,0.1);
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
    }
    
    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        padding: 25px 20px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        text-align: center;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }
    
    .stat-card h4 {
        margin: 0 0 8px 0;
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .stat-card p {
        margin: 0;
        color: #7f8c8d;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* تحسينات المودال */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px 25px;
    }
    
    .modal-title {
        font-weight: 600;
        font-size: 1.2rem;
    }
    
    .task-details {
        padding: 10px 0;
    }
    
    .task-details p {
        margin-bottom: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #f8f9fa;
        font-size: 14px;
    }
    
    .task-details strong {
        color: #2c3e50;
        font-weight: 600;
        display: inline-block;
        min-width: 120px;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .progress-bar {
        width: 100%;
        height: 8px;
        background: #f8f9fa;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 5px;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #27ae60, #2ecc71);
        transition: width 0.3s ease;
    }
    
    /* Loading spinner */
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 50px;
    }
    
    .loading-spinner.show {
        display: block;
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 15px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .fc-toolbar {
            flex-direction: column !important;
            gap: 10px;
        }
        
        .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
        }
        
        .stats-cards {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
        
        .filter-section .row > div {
            margin-bottom: 15px;
        }
    }
</style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تقويم المهام</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">المهام</a></li>
                            <li class="breadcrumb-item active">التقويم</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- إحصائيات سريعة -->
        <div class="stats-cards">
            <div class="stat-card">
                <h4>{{ $tasksCount }}</h4>
                <p>إجمالي المهام</p>
            </div>
            <div class="stat-card">
                <h4>{{ $pendingTasks }}</h4>
                <p>مهام معلقة</p>
            </div>
            <div class="stat-card">
                <h4>{{ $inProgressTasks }}</h4>
                <p>مهام قيد التنفيذ</p>
            </div>
            <div class="stat-card">
                <h4>{{ $completedTasks }}</h4>
                <p>مهام مكتملة</p>
            </div>
            <div class="stat-card">
                <h4>{{ $overdueTasks }}</h4>
                <p>مهام متأخرة</p>
            </div>
        </div>

        <!-- فلاتر -->
        <div class="filter-section">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label>المشروع</label>
                    <select id="projectFilter" class="form-control">
                        <option value="">جميع المشاريع</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label>الحالة</label>
                    <select id="statusFilter" class="form-control">
                        <option value="">جميع الحالات</option>
                        <option value="pending">معلقة</option>
                        <option value="in_progress">قيد التنفيذ</option>
                        <option value="completed">مكتملة</option>
                        <option value="cancelled">ملغاة</option>
                        <option value="overdue">متأخرة</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label>الأولوية</label>
                    <select id="priorityFilter" class="form-control">
                        <option value="">جميع الأولويات</option>
                        <option value="high">عالية</option>
                        <option value="medium">متوسطة</option>
                        <option value="low">منخفضة</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label>المستخدم</label>
                    <select id="userFilter" class="form-control">
                        <option value="">جميع المستخدمين</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- دليل الألوان -->
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #f39c12, #f1c40f);"></div>
                    <span>معلقة</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #3498db, #2980b9);"></div>
                    <span>قيد التنفيذ</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #27ae60, #2ecc71);"></div>
                    <span>مكتملة</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #e74c3c, #c0392b);"></div>
                    <span>ملغاة</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);"></div>
                    <span>متأخرة</span>
                </div>
            </div>
        </div>

        <!-- التقويم -->
        <div class="calendar-container">
            <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner"></div>
             
            </div>
            <div id='calendar'></div>
        </div>
    </div>

    <!-- Modal لتفاصيل المهمة -->
    <div class="modal fade" id="taskModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="taskModalBody">
                    <!-- سيتم ملء المحتوى ديناميكياً -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <a href="" id="editTaskBtn" class="btn btn-primary">تعديل المهمة</a>
                    <a href="" id="viewTaskBtn" class="btn btn-info">عرض التفاصيل</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/ar.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var loadingSpinner = document.getElementById('loadingSpinner');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ar',
        direction: 'rtl',
        initialView: 'dayGridMonth',
        height: 'auto',
        aspectRatio: 1.5,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        buttonText: {
            today: 'اليوم',
            month: 'شهر',
            week: 'أسبوع',
            day: 'يوم',
            list: 'قائمة'
        },
        events: function(info, successCallback, failureCallback) {
            // إظهار مؤشر التحميل
            loadingSpinner.classList.add('show');
            
            $.ajax({
                url: '{{ route("tasks.calendar.events") }}',
                type: 'GET',
                data: {
                    project_id: $('#projectFilter').val() || '',
                    status: $('#statusFilter').val() || '',
                    priority: $('#priorityFilter').val() || '',
                    user_id: $('#userFilter').val() || '',
                    start: info.startStr,
                    end: info.endStr
                },
                success: function(data) {
                    console.log('✅ تم تحميل المهام بنجاح:', data.length, 'مهمة');
                    
                    // إخفاء مؤشر التحميل
                    loadingSpinner.classList.remove('show');
                    
                    // معالجة البيانات
                    const processedEvents = data.map(event => {
                        // تحديد إذا كانت المهمة متأخرة
                        const isOverdue = event.extendedProps.is_overdue;
                        const status = isOverdue ? 'overdue' : event.extendedProps.status;
                        
                        return {
                            ...event,
                            extendedProps: {
                                ...event.extendedProps,
                                display_status: status
                            }
                        };
                    });
                    
                    successCallback(processedEvents);
                },
                error: function(xhr, status, error) {
                    console.error('❌ خطأ في تحميل المهام:', error);
                    console.error('Response:', xhr.responseText);
                    
                    // إخفاء مؤشر التحميل
                    loadingSpinner.classList.remove('show');
                    
                    // إظهار رسالة خطأ
                    alert('حدث خطأ أثناء تحميل المهام. يرجى المحاولة مرة أخرى.');
                    failureCallback();
                }
            });
        },
        eventClick: function(info) {
            showTaskDetails(info.event);
        },
        eventDidMount: function(info) {
            const event = info.event;
            const el = info.el;
            
            // إضافة classes للحالات والأولويات
            const displayStatus = event.extendedProps.display_status || event.extendedProps.status;
            el.classList.add('status-' + displayStatus);
            
            if (event.extendedProps.priority) {
                el.classList.add('priority-' + event.extendedProps.priority);
            }
            
            // إضافة tooltip
            el.setAttribute('title', `
المشروع: ${event.extendedProps.project_name || 'غير محدد'}
الحالة: ${getStatusText(displayStatus)}
الأولوية: ${getPriorityText(event.extendedProps.priority)}
نسبة الإنجاز: ${event.extendedProps.completion_percentage || 0}%
            `.trim());
        },
        loading: function(isLoading) {
            if (isLoading) {
                console.log('🔄 جاري تحميل أحداث التقويم...');
            } else {
                console.log('✅ تم تحميل أحداث التقويم!');
            }
        },
        // تحسينات إضافية
        dayMaxEvents: 3,
        moreLinkClick: 'popover',
        dayHeaderFormat: { weekday: 'long' },
        eventDisplay: 'block',
        displayEventTime: false
    });

    calendar.render();

    // تحديث الفلاتر مع debouncing
    let filterTimeout;
    $('#projectFilter, #statusFilter, #priorityFilter, #userFilter').on('change', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(function() {
            console.log('🔄 تحديث الفلاتر...');
            calendar.refetchEvents();
        }, 300);
    });

    // دالة عرض تفاصيل المهمة
    function showTaskDetails(event) {
        const displayStatus = event.extendedProps.display_status || event.extendedProps.status;
        const statusText = getStatusText(displayStatus);
        const statusClass = 'status-' + displayStatus;
        const priorityText = getPriorityText(event.extendedProps.priority);
        
        $('#taskModalTitle').text(event.title);
        
        // تنسيق التواريخ
        const startDate = event.start ? event.start.toLocaleDateString('ar-SA') : 'غير محدد';
        const endDate = event.end ? event.end.toLocaleDateString('ar-SA') : 'غير محدد';
        
        // حساب نسبة الإنجاز
        const completionPercentage = event.extendedProps.completion_percentage || 0;
        
        let html = `
            <div class="task-details">
                <p><strong>المشروع:</strong> ${event.extendedProps.project_name || 'غير محدد'}</p>
                <p><strong>الوصف:</strong> ${event.extendedProps.description || 'لا يوجد وصف'}</p>
                <p><strong>الحالة:</strong> <span class="status-badge ${statusClass}">${statusText}</span></p>
                <p><strong>الأولوية:</strong> ${priorityText}</p>
                <p><strong>تاريخ البدء:</strong> ${startDate}</p>
                <p><strong>تاريخ الانتهاء:</strong> ${endDate}</p>
                <p><strong>نسبة الإنجاز:</strong> ${completionPercentage}%
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${completionPercentage}%;"></div>
                    </div>
                </p>
                ${event.extendedProps.assigned_users ? `<p><strong>المكلفون:</strong> ${event.extendedProps.assigned_users}</p>` : ''}
                ${event.extendedProps.is_overdue ? '<p style="color: #e74c3c; font-weight: bold;">⚠️ هذه المهمة متأخرة!</p>' : ''}
            </div>
        `;

        $('#taskModalBody').html(html);
        $('#editTaskBtn').attr('href', `/tasks/${event.id}/edit`);
        $('#viewTaskBtn').attr('href', `/tasks/${event.id}`);
        $('#taskModal').modal('show');
    }
    
    // دوال مساعدة لترجمة النصوص
    function getStatusText(status) {
        const statusTexts = {
            'pending': 'معلقة',
            'in_progress': 'قيد التنفيذ',
            'completed': 'مكتملة',
            'cancelled': 'ملغاة',
            'overdue': 'متأخرة'
        };
        return statusTexts[status] || status;
    }
    
    function getPriorityText(priority) {
        const priorityTexts = {
            'high': 'عالية',
            'medium': 'متوسطة',
            'low': 'منخفضة'
        };
        return priorityTexts[priority] || 'غير محدد';
    }
});
</script>
<script>
// إضافة هذا الكود في نهاية ملف JavaScript الخاص بالتقويم

// تحسينات إضافية للتقويم
$(document).ready(function() {
    
    // إضافة وظيفة السحب والإفلات لتغيير تاريخ المهام
    calendar.setOption('editable', true);
    calendar.setOption('eventDrop', function(info) {
        updateTaskDate(info.event, info.delta);
    });
    
    // إضافة وظيفة تغيير حجم الأحداث
    calendar.setOption('eventResize', function(info) {
        updateTaskDuration(info.event, info.endDelta);
    });
    
    // تحديث إحصائيات الفلاتر عند التغيير
    $('#projectFilter, #statusFilter, #priorityFilter, #userFilter').on('change', function() {
        updateFilterStats();
    });
    
    // إضافة اختصارات لوحة المفاتيح
    $(document).keydown(function(e) {
        // الضغط على مفتاح ESC لإغلاق المودال
        if (e.key === 'Escape' && $('#taskModal').hasClass('show')) {
            $('#taskModal').modal('hide');
        }
        
        // الضغط على مفتاح R لتحديث التقويم
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            refreshCalendar();
        }
    });
    
    // إضافة زر تحديث سريع
    $('.calendar-container').prepend(`
        <div class="calendar-actions" style="margin-bottom: 15px; text-align: right;">
            <button id="refreshBtn" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-sync-alt"></i> تحديث
            </button>
            <button id="todayBtn" class="btn btn-outline-info btn-sm">
                <i class="fas fa-calendar-day"></i> اليوم
            </button>
            <button id="exportBtn" class="btn btn-outline-success btn-sm">
                <i class="fas fa-download"></i> تصدير
            </button>
        </div>
    `);
    
    // وظائف الأزرار الجديدة
    $('#refreshBtn').click(refreshCalendar);
    $('#todayBtn').click(() => calendar.today());
    $('#exportBtn').click(exportCalendarData);
    
    // إضافة مؤشر للمهام المتأخرة في العنوان
    updateOverdueCounter();
    
    // تحديث العداد كل دقيقة
    setInterval(updateOverdueCounter, 60000);
});

/**
 * تحديث تاريخ المهمة عند السحب والإفلات
 */
function updateTaskDate(event, delta) {
    const taskId = event.id;
    const newStartDate = event.start;
    const newEndDate = event.end;
    
    // إظهار مؤشر التحميل
    showLoadingSpinner(true);
    
    $.ajax({
        url: `/tasks/calendar/${taskId}/status`,
        type: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            start_date: newStartDate ? newStartDate.toISOString().split('T')[0] : null,
            due_date: newEndDate ? newEndDate.toISOString().split('T')[0] : null,
            status: event.extendedProps.status
        },
        success: function(response) {
            showNotification('تم تحديث تاريخ المهمة بنجاح', 'success');
            updateTaskStats();
        },
        error: function(xhr) {
            showNotification('حدث خطأ أثناء تحديث المهمة', 'error');
            // التراجع عن التغيير
            calendar.refetchEvents();
        },
        complete: function() {
            showLoadingSpinner(false);
        }
    });
}

/**
 * تحديث مدة المهمة
 */
function updateTaskDuration(event, endDelta) {
    const taskId = event.id;
    const newEndDate = event.end;
    
    $.ajax({
        url: `/tasks/calendar/${taskId}/status`,
        type: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            due_date: newEndDate ? newEndDate.toISOString().split('T')[0] : null,
            status: event.extendedProps.status
        },
        success: function(response) {
            showNotification('تم تحديث مدة المهمة بنجاح', 'success');
        },
        error: function(xhr) {
            showNotification('حدث خطأ أثناء التحديث', 'error');
            calendar.refetchEvents();
        }
    });
}

/**
 * تحديث إحصائيات الفلاتر
 */
function updateFilterStats() {
    const activeFilters = {
        project: $('#projectFilter').val(),
        status: $('#statusFilter').val(),
        priority: $('#priorityFilter').val(),
        user: $('#userFilter').val()
    };
    
    // عد الفلاتر النشطة
    const activeFilterCount = Object.values(activeFilters).filter(v => v !== '').length;
    
    // إضافة مؤشر للفلاتر النشطة
    $('.filter-section').attr('data-active-filters', activeFilterCount);
    
    if (activeFilterCount > 0) {
        $('.filter-section').addClass('has-active-filters');
    } else {
        $('.filter-section').removeClass('has-active-filters');
    }
}

/**
 * تحديث عداد المهام المتأخرة
 */
function updateOverdueCounter() {
    const overdueEvents = calendar.getEvents().filter(event => 
        event.extendedProps.is_overdue
    ).length;
    
    if (overdueEvents > 0) {
        if (!$('#overdueCounter').length) {
            $('.content-header-title').append(`
                <span id="overdueCounter" class="badge badge-danger ml-2" style="margin-right: 10px;">
                    ${overdueEvents} متأخرة
                </span>
            `);
        } else {
            $('#overdueCounter').text(`${overdueEvents} متأخرة`);
        }
    } else {
        $('#overdueCounter').remove();
    }
}

/**
 * تصدير بيانات التقويم
 */
function exportCalendarData() {
    const events = calendar.getEvents();
    const exportData = events.map(event => ({
        'العنوان': event.title,
        'المشروع': event.extendedProps.project_name || '',
        'الحالة': getStatusText(event.extendedProps.status),
        'الأولوية': getPriorityText(event.extendedProps.priority),
        'تاريخ البداية': event.start ? event.start.toLocaleDateString('ar-SA') : '',
        'تاريخ النهاية': event.end ? event.end.toLocaleDateString('ar-SA') : '',
        'نسبة الإنجاز': (event.extendedProps.completion_percentage || 0) + '%',
        'المكلفون': event.extendedProps.assigned_users || ''
    }));
    
    // تحويل إلى CSV
    const csv = convertToCSV(exportData);
    downloadCSV(csv, 'tasks_calendar.csv');
}

/**
 * تحويل البيانات إلى CSV
 */
function convertToCSV(data) {
    if (!data.length) return '';
    
    const headers = Object.keys(data[0]);
    const csvContent = [
        headers.join(','),
        ...data.map(row => headers.map(header => `"${row[header] || ''}"`).join(','))
    ].join('\n');
    
    return csvContent;
}

/**
 * تحميل ملف CSV
 */
function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

/**
 * إظهار مؤشر التحميل
 */
function showLoadingSpinner(show) {
    if (show) {
        $('#loadingSpinner').addClass('show');
    } else {
        $('#loadingSpinner').removeClass('show');
    }
}

/**
 * إظهار إشعارات
 */
function showNotification(message, type = 'info') {
    // إزالة الإشعارات السابقة
    $('.notification-toast').remove();
    
    const toast = $(`
        <div class="notification-toast alert alert-${type === 'error' ? 'danger' : type}" 
             style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="close" onclick="$(this).parent().remove()">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(toast);
    
    // إخفاء تلقائي بعد 5 ثوان
    setTimeout(() => {
        toast.fadeOut(() => toast.remove());
    }, 5000);
}

/**
 * تحديث التقويم
 */
function refreshCalendar() {
    showLoadingSpinner(true);
    calendar.refetchEvents();
    updateTaskStats();
    showNotification('تم تحديث التقويم بنجاح', 'success');
}

/**
 * تحديث إحصائيات المهام
 */
function updateTaskStats() {
    // يمكنك إضافة AJAX call لتحديث الإحصائيات هنا
    // أو حسابها من الأحداث الموجودة في التقويم
}

// CSS إضافي للتحسينات
$('<style>').text(`
    .has-active-filters {
        border: 2px solid #3498db !important;
        box-shadow: 0 0 15px rgba(52, 152, 219, 0.3) !important;
    }
    
    .calendar-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    
    .calendar-actions .btn {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .notification-toast {
        animation: slideIn 0.3s ease;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    #overdueCounter {
        animation: pulse 2s infinite;
    }
    
    .fc-event.overdue-task {
        animation: blink 1.5s infinite;
    }
    
    @keyframes blink {
        50% { opacity: 0.7; }
    }
`).appendTo('head');
</script>
@endsection