{{-- ملف partial لعرض بطاقة المهمة المحسّنة مع المؤقت التنازلي --}}
{{-- المسار: resources/views/taskmanager/task/partial/task-card.blade.php --}}

@php
    $priorityClass = 'priority-' . $task->priority;
    $progressPercent = $task->completion_percentage ?? 0;

    // تحديد لون التقدم
    if ($progressPercent >= 70) {
        $progressColor = '#28c76f';
    } elseif ($progressPercent >= 30) {
        $progressColor = '#ff9f43';
    } else {
        $progressColor = '#ea5455';
    }

    $radius = 25;
    $circumference = 2 * 3.14159 * $radius;
    $offset = $circumference - ($progressPercent / 100) * $circumference;
@endphp

<div class="card task-card {{ $priorityClass }}" data-task-id="{{ $task->id }}" draggable="true">
    <div class="card-body">

        {{-- رأس البطاقة مع العنوان والمؤقت --}}
        <div class="task-header">
            <h6 class="task-title">{{ $task->title }}</h6>
            
            {{-- المؤقت الدائري الصغير --}}
            @if($task->due_date)
                <div class="task-countdown-mini" 
                     data-task-id="{{ $task->id }}"
                     data-start="{{ $task->start_date ?? now() }}" 
                     data-end="{{ $task->due_date }}">
                    <svg width="65" height="65" viewBox="0 0 65 65">
                        <circle class="countdown-bg" cx="32.5" cy="32.5" r="28"></circle>
                        <circle class="countdown-progress" cx="32.5" cy="32.5" r="28" id="task-circle-{{ $task->id }}"></circle>
                    </svg>
                    <div class="countdown-info" id="task-countdown-{{ $task->id }}">
                        <div class="countdown-number">0</div>
                        <div class="countdown-unit">يوم</div>
                        <div class="countdown-time">00:00:00</div>
                    </div>
                </div>
            @endif
        </div>

        {{-- معلومات المشروع --}}
        <div class="task-project">
            <i class="feather icon-folder"></i>
            <span>{{ $task->project->title ?? 'لا يوجد مشروع' }}</span>
        </div>

        {{-- قسم التقدم والتاريخ --}}
        <div class="task-progress-section">
            {{-- دائرة التقدم --}}
            <div class="circular-progress-wrapper">
                <div class="circular-progress">
                    <svg width="70" height="70" style="transform: rotate(-90deg);">
                        <circle cx="35" cy="35" r="{{ $radius }}" fill="none" stroke="#e9ecef" stroke-width="6"/>
                        <circle cx="35" cy="35" r="{{ $radius }}" fill="none" stroke="{{ $progressColor }}"
                                stroke-width="6" stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $offset }}" stroke-linecap="round"
                                class="progress-circle"/>
                    </svg>
                    <div class="progress-value">
                        <input type="number" class="progress-input" value="{{ $progressPercent }}"
                               min="0" max="100" data-task-id="{{ $task->id }}"
                               style="color: {{ $progressColor }};">
                        <span class="percent-sign" style="color: {{ $progressColor }};">%</span>
                    </div>
                </div>
                <div class="progress-label">نسبة الإنجاز</div>
            </div>

            {{-- تاريخ الانتهاء --}}
            @if($task->due_date)
                <div class="task-due-section">
                    <div class="due-date-label">موعد التسليم</div>
                    <div class="due-date-value">
                        <i class="feather icon-calendar"></i>
                        <span>{{ \Carbon\Carbon::parse($task->due_date)->format('Y/m/d') }}</span>
                    </div>
                    @if($task->due_date < now()->toDateString() && $task->status !== 'completed')
                        <span class="badge badge-danger badge-sm">متأخرة</span>
                    @endif
                </div>
            @endif
        </div>

        {{-- المهام الفرعية --}}
        @if($task->subTasks && $task->subTasks->count() > 0)
            @php
                $completedSubTasks = $task->subTasks->where('status', 'completed')->count();
                $totalSubTasks = $task->subTasks->count();
                $subTasksPercentage = round(($completedSubTasks / $totalSubTasks) * 100);
            @endphp
            <div class="sub-tasks-section">
                <div class="sub-tasks-header">
                    <span class="sub-tasks-icon"><i class="feather icon-list"></i> المهام الفرعية</span>
                    <span class="sub-tasks-count">{{ $completedSubTasks }}/{{ $totalSubTasks }}</span>
                </div>
                <div class="sub-tasks-progress">
                    <div class="sub-tasks-progress-bar" style="width: {{ $subTasksPercentage }}%"></div>
                </div>
            </div>
        @endif

        {{-- قسم المستخدمين المكلفين --}}
        <div class="task-assignees-section">
            <div class="assignees-header">
                <i class="feather icon-users"></i>
                <span>الموظفين المكلفين</span>
            </div>

            @if($task->assignedUsers && $task->assignedUsers->count() > 0)
                <div class="assignees-grid">
                    @foreach($task->assignedUsers as $user)
                        <div class="assignee-card">
                          
                            <div class="assignee-info">
                                <div class="assignee-name">{{ $user->name }}</div>
                                <div class="assignee-role">{{ $user->job_title ?? 'موظف' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-assignees-card">
                    <i class="feather icon-user-x"></i>
                    <span>لا يوجد موظفين مكلفين بهذه المهمة</span>
                </div>
            @endif
        </div>

        {{-- أزرار الإجراءات --}}
        <div class="task-footer">
            <div class="task-actions">
                <button class="action-btn btn-view" onclick="showTaskDetails({{ $task->id }})" title="عرض التفاصيل">
                    <i class="feather icon-eye"></i>
                </button>
                <button class="action-btn btn-edit" onclick="openTaskModal({{ $task->id }})" title="تعديل">
                    <i class="feather icon-edit"></i>
                </button>
                <button class="action-btn btn-delete" onclick="deleteTask({{ $task->id }})" title="حذف">
                    <i class="feather icon-trash-2"></i>
                </button>
            </div>
        </div>

    </div>
</div>

<style>
/* ===== البطاقة الأساسية ===== */
.task-card {
    margin-bottom: 15px;
    cursor: move;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    background: #ffffff;
    border: 1px solid #f0f0f0;
    overflow: hidden;
}

.task-card:hover {
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.task-card .card-body {
    padding: 1.5rem;
}

/* ===== رأس البطاقة مع المؤقت ===== */
.task-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f0f0f0;
}

.task-title {
    flex: 1;
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    line-height: 1.4;
}

/* ===== المؤقت الدائري للمهمة ===== */
.task-countdown-mini {
    position: relative;
    width: 65px;
    height: 65px;
    flex-shrink: 0;
}

.task-countdown-mini svg {
    transform: rotate(-90deg);
}

.countdown-bg {
    fill: none;
    stroke: #e9ecef;
    stroke-width: 5;
}

.countdown-progress {
    fill: none;
    stroke: #667eea;
    stroke-width: 5;
    stroke-linecap: round;
    transition: stroke-dashoffset 0.5s ease;
}

.countdown-info {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    width: 100%;
}

.countdown-number {
    font-size: 16px;
    font-weight: 700;
    color: #667eea;
    line-height: 1;
}

.countdown-unit {
    font-size: 8px;
    color: #6c757d;
    margin-top: 1px;
}

.countdown-time {
    font-size: 9px;
    color: #6c757d;
    margin-top: 2px;
    font-weight: 600;
}

/* ألوان ديناميكية للمؤقت */
.task-countdown-normal .countdown-progress {
    stroke: #667eea;
}

.task-countdown-normal .countdown-number {
    color: #667eea;
}

.task-countdown-warning .countdown-progress {
    stroke: #ffc107;
}

.task-countdown-warning .countdown-number {
    color: #ffc107;
}

.task-countdown-danger .countdown-progress {
    stroke: #dc3545;
    animation: pulse-countdown 2s infinite;
}

.task-countdown-danger .countdown-number {
    color: #dc3545;
}

@keyframes pulse-countdown {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

/* ===== معلومات المشروع ===== */
.task-project {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1.25rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.task-project i {
    color: #7367f0;
    font-size: 1rem;
}

/* ===== قسم التقدم والتاريخ ===== */
.task-progress-section {
    display: flex;
    justify-content: space-around;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1.25rem;
    padding: 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
}

/* ===== دائرة التقدم ===== */
.circular-progress-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.circular-progress {
    position: relative;
    width: 70px;
    height: 70px;
    transition: transform 0.3s ease;
}

.circular-progress:hover {
    transform: scale(1.05);
}

.progress-circle {
    transition: stroke-dashoffset 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.progress-value {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-input {
    width: 40px;
    border: none;
    text-align: center;
    font-size: 1.1rem;
    font-weight: 700;
    background: transparent;
    outline: none;
}

.percent-sign {
    font-size: 0.9rem;
    font-weight: 600;
    margin-left: 2px;
}

.progress-label {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 500;
}

/* ===== تاريخ الانتهاء ===== */
.task-due-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.due-date-label {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 500;
}

.due-date-value {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #495057;
    font-weight: 600;
}

.due-date-value i {
    color: #7367f0;
}

/* ===== المهام الفرعية ===== */
.sub-tasks-section {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 10px;
    margin-bottom: 1.25rem;
}

.sub-tasks-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.sub-tasks-icon {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #495057;
    font-weight: 500;
}

.sub-tasks-icon i {
    color: #7367f0;
}

.sub-tasks-count {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 600;
}

.sub-tasks-progress {
    height: 6px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.sub-tasks-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #28c76f 0%, #48da89 100%);
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 10px;
}

/* ===== قسم المستخدمين المكلفين ===== */
.task-assignees-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
}

.assignees-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
}

.assignees-header i {
    color: #7367f0;
    font-size: 1.1rem;
}

.assignees-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 0.75rem;
}

.assignee-card {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #ffffff;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.assignee-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #7367f0;
}

.assignee-avatar {
    flex-shrink: 0;
}

.assignee-avatar img {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: 2px solid #7367f0;
    object-fit: cover;
}

.assignee-info {
    flex: 1;
    min-width: 0;
}

.assignee-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: #2c3e50;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0.2rem;
}

.assignee-role {
    font-size: 0.75rem;
    color: #6c757d;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.no-assignees-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1.5rem;
    background: #ffffff;
    border-radius: 10px;
    border: 2px dashed #dee2e6;
    color: #6c757d;
    text-align: center;
}

.no-assignees-card i {
    font-size: 2rem;
    color: #adb5bd;
}

.no-assignees-card span {
    font-size: 0.85rem;
}

/* ===== قسم التذييل ===== */
.task-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
}

/* ===== أزرار الإجراءات ===== */
.task-actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: white;
}

.action-btn i {
    font-size: 1rem;
}

.btn-view {
    background: linear-gradient(135deg, #00cfe8 0%, #0bb7e8 100%);
}

.btn-edit {
    background: linear-gradient(135deg, #7367f0 0%, #5e50ee 100%);
}

.btn-delete {
    background: linear-gradient(135deg, #ea5455 0%, #e63757 100%);
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.action-btn:active {
    transform: translateY(0);
}

/* ===== الأولوية ===== */
.priority-low {
    border-left: 4px solid #28c76f;
}

.priority-medium {
    border-left: 4px solid #ff9f43;
}

.priority-high {
    border-left: 4px solid #ea5455;
}

.priority-urgent {
    border-left: 4px solid #e83e8c;
    box-shadow: 0 4px 16px rgba(232, 62, 140, 0.25);
}

/* ===== Badge ===== */
.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-weight: 600;
}

/* ===== حالة السحب ===== */
.task-card.dragging {
    opacity: 0.6;
    transform: rotate(3deg) scale(1.02);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .task-progress-section {
        flex-direction: column;
    }

    .task-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .task-countdown-mini {
        align-self: flex-end;
    }

    .assignees-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .assignees-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// المؤقت التنازلي للمهام
$(document).ready(function() {
    const taskCircumference = 2 * Math.PI * 28;

    function updateTaskCountdowns() {
        $('.task-countdown-mini').each(function() {
            const $countdown = $(this);
            const taskId = $countdown.data('task-id');
            const startDate = new Date($countdown.data('start'));
            const endDate = new Date($countdown.data('end'));
            const now = new Date();

            const circle = $(`#task-circle-${taskId}`);
            const info = $(`#task-countdown-${taskId}`);
            
            let days, hours, minutes, seconds, statusClass, progress;

            if (now < startDate) {
                // المهمة لم تبدأ
                const diff = startDate - now;
                days = Math.floor(diff / (1000 * 60 * 60 * 24));
                hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                seconds = Math.floor((diff % (1000 * 60)) / 1000);
                statusClass = 'task-countdown-normal';
                progress = 0;
                
                info.find('.countdown-number').text(days);
                info.find('.countdown-unit').text('لبدء');
                info.find('.countdown-time').text(`${pad(hours)}:${pad(minutes)}:${pad(seconds)}`);
                
            } else if (now > endDate) {
                // المهمة متأخرة
                const diff = now - endDate;
                days = Math.floor(diff / (1000 * 60 * 60 * 24));
                hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                seconds = Math.floor((diff % (1000 * 60)) / 1000);
                statusClass = 'task-countdown-danger';
                progress = 100;
                
                info.find('.countdown-number').text(days);
                info.find('.countdown-unit').text('متأخر');
                info.find('.countdown-time').text(`${pad(hours)}:${pad(minutes)}:${pad(seconds)}`);
                
            } else {
                // المهمة جارية
                const diff = endDate - now;
                days = Math.floor(diff / (1000 * 60 * 60 * 24));
                hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                const totalTime = endDate - startDate;
                const elapsed = now - startDate;
                progress = (elapsed / totalTime) * 100;
                
                if (days <= 2) {
                    statusClass = 'task-countdown-danger';
                } else if (days <= 5) {
                    statusClass = 'task-countdown-warning';
                } else {
                    statusClass = 'task-countdown-normal';
                }
                
                info.find('.countdown-number').text(days);
                info.find('.countdown-unit').text('يوم');
                info.find('.countdown-time').text(`${pad(hours)}:${pad(minutes)}:${pad(seconds)}`);
            }

            // تحديث الدائرة
            const offset = taskCircumference - (progress / 100 * taskCircumference);
            circle.css({
                'stroke-dasharray': taskCircumference,
                'stroke-dashoffset': offset
            });

            // تحديث اللون
            $countdown.removeClass('task-countdown-normal task-countdown-warning task-countdown-danger')
                     .addClass(statusClass);
        });
    }

    function pad(num) {
        return num.toString().padStart(2, '0');
    }

    // تحديث كل ثانية
    updateTaskCountdowns();
    setInterval(updateTaskCountdowns, 1000);
});
</script>