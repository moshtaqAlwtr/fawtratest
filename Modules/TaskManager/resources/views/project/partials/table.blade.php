@if ($projects->count() > 0)
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <h6 class="mb-0">عرض {{ $projects->firstItem() }} إلى {{ $projects->lastItem() }} من
                {{ $projects->total() }} مشروع</h6>
        </div>
        <div class="d-flex align-items-center gap-2">
            <select class="form-select form-select-sm" id="perPageSelect" style="width: auto;">
                <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span class="text-muted">عناصر لكل صفحة</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-striped table-hover-custom">
            <thead class="table-light">
                <tr>
                    <th width="3%">
                        <i class="fas fa-chevron-down text-muted"></i>
                    </th>
                    <th width="3%">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                    <th>المشروع</th>
                    <th>مساحة العمل</th>

                    <th>المهام والتعليقات</th>

                    <th>الميزانية</th>
                    <th>الحالة</th>
                    <th>الجدول الزمني</th>
                    <th style="width: 10%">خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projects as $project)
                    <tr class="project-row" data-project-id="{{ $project->id }}">
                        <td>
                            <button class="btn btn-sm btn-link text-primary toggle-tasks"
                                data-project-id="{{ $project->id }}" data-bs-toggle="collapse"
                                data-bs-target="#tasks-{{ $project->id }}">
                                <i class="fas fa-chevron-right transition-icon"></i>
                            </button>
                        </td>

                        <td>
                            <input type="checkbox" class="form-check-input project-checkbox"
                                value="{{ $project->id }}">
                        </td>

                        <td style="white-space: normal; word-wrap: break-word; min-width: 200px;">
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    @php
                                        $priorityColors = [
                                            'low' => '#28a745',
                                            'medium' => '#ffc107',
                                            'high' => '#fd7e14',
                                            'urgent' => '#dc3545',
                                        ];
                                        $priorityColor = $priorityColors[$project->priority] ?? '#6c757d';
                                    @endphp

                                </div>
                                <div>
                                    <strong class="text-primary">{{ $project->title }}</strong>
                                    @if ($project->description)
                                        <div class="text-muted small">{{ Str::limit($project->description, 50) }}</div>
                                    @endif
                                    <div class="d-flex align-items-center mt-1">
                                        @php
                                            $priorityLabels = [
                                                'low' => 'منخفضة',
                                                'medium' => 'متوسطة',
                                                'high' => 'عالية',
                                                'urgent' => 'عاجلة',
                                            ];
                                            $priorityLabel = $priorityLabels[$project->priority] ?? 'غير محدد';
                                            $priorityBadgeClass = [
                                                'low' => 'success',
                                                'medium' => 'warning',
                                                'high' => 'info',
                                                'urgent' => 'danger',
                                            ];
                                            $badgeClass = $priorityBadgeClass[$project->priority] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }} badge-sm">{{ $priorityLabel }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            @if ($project->workspace)
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2"
                                        style="background-color: #6f42c1; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.9rem;">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $project->workspace->title }}</strong>
                                        <div class="small text-muted">مساحة العمل</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2"
                                        style="background-color: #28a745; width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white;">
                                        <i class="fas fa-tasks" style="font-size: 0.85rem;"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <strong class="text-success me-1">{{ $project->stats['total_tasks'] ?? 0 }}</strong>
                                            <span class="text-muted small">مهمة</span>
                                        </div>
                                        @if (($project->stats['total_tasks'] ?? 0) > 0)
                                            <div class="small text-muted">
                                                <span class="text-success">{{ $project->stats['completed_tasks'] ?? 0 }} مكتملة</span>
                                                @if (($project->stats['in_progress_tasks'] ?? 0) > 0)
                                                    | <span class="text-warning">{{ $project->stats['in_progress_tasks'] }} جارية</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2"
                                        style="background-color: #17a2b8; width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white;">
                                        <i class="fas fa-comments" style="font-size: 0.85rem;"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <strong class="text-info me-1">{{ $project->stats['total_comments'] ?? 0 }}</strong>
                                            <span class="text-muted small">تعليق</span>
                                        </div>
                                        @if (($project->stats['total_comments'] ?? 0) > 0)
                                            <div class="small text-muted">
                                                <i class="fas fa-user-edit"></i> نشط
                                            </div>
                                        @else
                                            <div class="small text-muted">لا توجد تعليقات</div>
                                        @endif
                                    </div>
                                </div>

                                @if (($project->stats['overdue_tasks'] ?? 0) > 0)
                                    <div class="alert alert-danger mb-0 p-1 px-2" style="font-size: 0.75rem;">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $project->stats['overdue_tasks'] }} متأخرة
                                    </div>
                                @endif
                            </div>
                        </td>



                        <td>
                            <div class="d-flex flex-column">
                                @php
                                    $currencySymbol = 'ر.س';
                                    $budget = $project->budget ?? 0;
                                    $spent = $project->cost ?? 0;
                                    $remaining = $budget - $spent;
                                    $usagePercentage = $budget > 0 ? ($spent / $budget) * 100 : 0;
                                @endphp
                                <div class="d-flex align-items-center mb-1">
                                    <strong class="text-primary">{{ number_format($budget, 0) }}</strong>
                                    <span class="ms-1">{{ $currencySymbol }}</span>
                                </div>
                                <div class="small">
                                    <div class="text-success">مصروف: {{ number_format($spent, 0) }} {{ $currencySymbol }}</div>
                                    <div class="text-{{ $remaining >= 0 ? 'info' : 'danger' }}">
                                        متبقي: {{ number_format($remaining, 0) }} {{ $currencySymbol }}
                                    </div>
                                </div>

                            </div>
                        </td>

                        <td>
                            @php
                                $statusConfig = [
                                    'new' => ['class' => 'secondary', 'text' => 'جديد', 'icon' => 'plus-circle'],
                                    'in_progress' => ['class' => 'success', 'text' => 'قيد التنفيذ', 'icon' => 'play-circle'],
                                    'completed' => ['class' => 'info', 'text' => 'مكتمل', 'icon' => 'check-circle'],
                                    'on_hold' => ['class' => 'warning', 'text' => 'متوقف', 'icon' => 'pause-circle'],
                                ];
                                $config = $statusConfig[$project->status] ?? $statusConfig['new'];
                            @endphp
                            <span class="badge badge-{{ $config['class'] }} project-status-badge rounded-pill">
                                <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                {{ $config['text'] }}
                            </span>
                            @if ($project->status == 'completed' && $project->actual_end_date)
                                <div class="small text-success mt-1">
                                    <i class="fas fa-calendar-check"></i>
                                    {{ \Carbon\Carbon::parse($project->actual_end_date)->format('Y-m-d') }}
                                </div>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex flex-column align-items-center">
                                <!-- المؤقت الدائري الصغير -->
                                <div class="circular-countdown"
                                     data-project-id="{{ $project->id }}"
                                     data-start="{{ $project->start_date }}"
                                     data-end="{{ $project->end_date }}">
                                    <svg width="80" height="80" viewBox="0 0 80 80">
                                        <circle class="countdown-bg-circle" cx="40" cy="40" r="35"></circle>
                                        <circle class="countdown-progress-circle" cx="40" cy="40" r="35" id="circle-{{ $project->id }}"></circle>
                                    </svg>
                                    <div class="countdown-text" id="countdown-text-{{ $project->id }}">
                                        <div class="countdown-days">0</div>
                                        <div class="countdown-time">00:00:00</div>
                                        <div class="countdown-status">متبقي</div>
                                    </div>
                                </div>

                                @php
                                    $now = now();
                                    $startDate = \Carbon\Carbon::parse($project->start_date);
                                    $endDate = \Carbon\Carbon::parse($project->end_date);
                                    $totalDays = $startDate->diffInDays($endDate);
                                    $passedDays = $startDate->diffInDays($now->min($endDate));

                                    if ($now->greaterThan($endDate)) {
                                        $remainingDays = -1 * $now->diffInDays($endDate);
                                    } elseif ($now->lessThan($startDate)) {
                                        $remainingDays = $startDate->diffInDays($endDate);
                                    } else {
                                        $remainingDays = $now->diffInDays($endDate);
                                    }

                                    $timelineProgress = $totalDays > 0 ? min(max(($passedDays / $totalDays) * 100, 0), 100) : 0;
                                @endphp

                            </div>
                        </td>

                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('projects.show', $project->id) }}"
                                   class="btn btn-sm btn-info"
                                   data-bs-toggle="tooltip"
                                   title="عرض المشروع">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('projects.edit', $project->id) }}"
                                   class="btn btn-sm btn-success"
                                   data-bs-toggle="tooltip"
                                   title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if ($project->status != 'completed')
                                    <button type="button"
                                            class="btn btn-sm btn-danger delete-project"
                                            data-id="{{ $project->id }}"
                                            data-bs-toggle="tooltip"
                                            title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <tr class="collapse tasks-detail-row" id="tasks-{{ $project->id }}">
                        <td colspan="11" class="p-0">
                            <div class="task-details-container bg-light p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <i class="fas fa-tasks text-primary me-2"></i>
                                        مهام المشروع ({{ $project->stats['total_tasks'] ?? 0 }})
                                    </h6>
                                    <button class="btn btn-sm btn-primary"
                                        onclick="loadProjectTasks({{ $project->id }})">
                                        <i class="fas fa-sync-alt me-1"></i>تحديث
                                    </button>
                                </div>

                                <div id="tasks-loading-{{ $project->id }}" class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="sr-only">جاري التحميل...</span>
                                    </div>
                                </div>

                                <div id="tasks-content-{{ $project->id }}" style="display: none;"></div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($projects->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <p class="text-muted small mb-0">
                    عرض {{ $projects->firstItem() }} إلى {{ $projects->lastItem() }} من إجمالي {{ $projects->total() }} مشروع
                </p>
            </div>
            <div>{{ $projects->links() }}</div>
        </div>
    @endif
@else
    <div class="text-center py-5">
        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
        <p class="text-muted">لا توجد مشاريع</p>
    </div>
@endif

<style>
/* المؤقت الدائري الصغير */
.circular-countdown {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto;
}

.circular-countdown svg {
    transform: rotate(-90deg);
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}

.countdown-bg-circle {
    fill: none;
    stroke: #e0e0e0;
    stroke-width: 6;
}

.countdown-progress-circle {
    fill: none;
    stroke: url(#gradient);
    stroke-width: 6;
    stroke-linecap: round;
    transition: stroke-dashoffset 0.5s ease;
}

.countdown-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    width: 100%;
}

.countdown-days {
    font-size: 1.2rem;
    font-weight: 700;
    color: #667eea;
    line-height: 1;
}

.countdown-time {
    font-size: 0.6rem;
    color: #6c757d;
    margin-top: 2px;
}

.countdown-status {
    font-size: 0.55rem;
    color: #999;
    margin-top: 1px;
}

/* ألوان ديناميكية للمؤقت */
.countdown-normal .countdown-progress-circle {
    stroke: #667eea;
}

.countdown-warning .countdown-progress-circle {
    stroke: #ffc107;
}

.countdown-warning .countdown-days {
    color: #ffc107;
}

.countdown-danger .countdown-progress-circle {
    stroke: #dc3545;
    animation: pulse-circle 2s infinite;
}

.countdown-danger .countdown-days {
    color: #dc3545;
}

.countdown-pending .countdown-progress-circle {
    stroke: #6c757d;
}

.countdown-pending .countdown-days {
    color: #6c757d;
}

@keyframes pulse-circle {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

/* باقي الأنماط */
.transition-icon {
    transition: transform 0.3s ease;
}

.toggle-tasks[aria-expanded="true"] .transition-icon {
    transform: rotate(90deg);
}

.task-details-container {
    border-top: 2px solid #dee2e6;
    background: linear-gradient(to bottom, #f8f9fa, #ffffff);
}

.task-card-improved {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
    transition: all 0.2s ease;
}

.task-card-improved:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-color: #007bff;
}

.task-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
}

.task-icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.task-title {
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 4px 0;
    line-height: 1.4;
}

.task-description {
    font-size: 13px;
    color: #6c757d;
    margin: 0;
    line-height: 1.5;
}

.task-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-bottom: 12px;
}

.task-info-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 6px;
}

.task-info-item i {
    font-size: 14px;
    width: 20px;
    text-align: center;
}

.task-info-label {
    font-size: 12px;
    color: #6c757d;
    margin: 0 4px 0 0;
}

.task-info-value {
    font-size: 13px;
    font-weight: 600;
    color: #2c3e50;
}

.task-progress-bar {
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin: 8px 0;
}

.task-progress-fill {
    height: 100%;
    transition: width 0.3s ease;
}

.assigned-users-compact {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.user-avatar-compact {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 13px;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.task-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.tasks-empty-state {
    text-align: center;
    padding: 40px 20px;
}

.tasks-empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
}

@media print {
    .task-card-improved {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
</style>

<script>
$(document).ready(function() {
    const circumference = 2 * Math.PI * 35;

    // تحديث جميع المؤقتات الدائرية
    function updateAllCountdowns() {
        $('.circular-countdown').each(function() {
            const $countdown = $(this);
            const projectId = $countdown.data('project-id');
            const startDate = new Date($countdown.data('start'));
            const endDate = new Date($countdown.data('end'));
            const now = new Date();

            updateCircularCountdown(projectId, startDate, endDate, now, $countdown);
        });
    }

    function updateCircularCountdown(projectId, startDate, endDate, now, $element) {
        const circle = $(`#circle-${projectId}`);
        const textEl = $(`#countdown-text-${projectId}`);

        let days, hours, minutes, seconds, diff, progress, statusText, statusClass;

        if (now < startDate) {
            // المشروع لم يبدأ
            diff = startDate - now;
            days = Math.floor(diff / (1000 * 60 * 60 * 24));
            hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            seconds = Math.floor((diff % (1000 * 60)) / 1000);
            statusText = 'حتى البداية';
            statusClass = 'countdown-pending';
            progress = 0;

        } else if (now > endDate) {
            // المشروع متأخر
            diff = now - endDate;
            days = Math.floor(diff / (1000 * 60 * 60 * 24));
            hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            seconds = Math.floor((diff % (1000 * 60)) / 1000);
            statusText = 'متأخر';
            statusClass = 'countdown-danger';
            progress = 100;

        } else {
            // المشروع جاري
            diff = endDate - now;
            days = Math.floor(diff / (1000 * 60 * 60 * 24));
            hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            seconds = Math.floor((diff % (1000 * 60)) / 1000);
            statusText = 'متبقي';

            // حساب التقدم
            const totalTime = endDate - startDate;
            const elapsed = now - startDate;
            progress = (elapsed / totalTime) * 100;

            // تحديد اللون بناءً على الأيام المتبقية
            if (days <= 3) {
                statusClass = 'countdown-warning';
            } else {
                statusClass = 'countdown-normal';
            }
        }

        // تحديث النص
        textEl.html(`
            <div class="countdown-days">${days}</div>
            <div class="countdown-time">${pad(hours)}:${pad(minutes)}:${pad(seconds)}</div>
            <div class="countdown-status">${statusText}</div>
        `);

        // تحديث الدائرة
        const offset = circumference - (progress / 100 * circumference);
        circle.css({
            'stroke-dasharray': circumference,
            'stroke-dashoffset': offset
        });

        // تحديث اللون
        $element.removeClass('countdown-normal countdown-warning countdown-danger countdown-pending').addClass(statusClass);
    }

    function pad(num) {
        return num.toString().padStart(2, '0');
    }

    // تحديث كل ثانية
    updateAllCountdowns();
    setInterval(updateAllCountdowns, 1000);

    // تحميل المهام عند فتح القسم
    $(document).on('show.bs.collapse', '.tasks-detail-row', function() {
        const projectId = $(this).attr('id').replace('tasks-', '');
        loadProjectTasks(projectId);
    });

    // تفعيل tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// دالة تحميل مهام المشروع
function loadProjectTasks(projectId) {
    const loadingDiv = $(`#tasks-loading-${projectId}`);
    const contentDiv = $(`#tasks-content-${projectId}`);

    loadingDiv.show();
    contentDiv.hide();

    $.ajax({
        url: `/projects/api/${projectId}/tasks`,
        method: 'GET',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                const html = renderTasksImproved(projectId, response.data);
                contentDiv.html(html).show();
                contentDiv.find('[data-bs-toggle="tooltip"]').tooltip();
            } else {
                contentDiv.html(getEmptyState()).show();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading tasks:', error);
            contentDiv.html(getErrorState()).show();
        },
        complete: function() {
            loadingDiv.hide();
        }
    });
}

// دالة عرض المهام المحسّنة
function renderTasksImproved(projectId, tasks) {
    let html = '<div class="tasks-list-improved">';

    tasks.forEach(task => {
        const statusConfig = getStatusConfig(task.status);
        const priorityConfig = getPriorityConfig(task.priority);

        html += `
            <div class="task-card-improved">
                <div class="task-header">
                    <div class="task-icon-wrapper" style="background-color: ${priorityConfig.color};">
                        <i class="fas ${priorityConfig.icon} text-white"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="task-title">${escapeHtml(task.title)}</h6>
                        ${task.description ? `<p class="task-description">${escapeHtml(task.description)}</p>` : ''}
                        <div class="d-flex gap-2 mt-2">
                            <span class="task-badge" style="background-color: ${statusConfig.color}20; color: ${statusConfig.color};">
                                <i class="fas ${statusConfig.icon}"></i>
                                ${statusConfig.text}
                            </span>
                            <span class="task-badge" style="background-color: ${priorityConfig.color}20; color: ${priorityConfig.color};">
                                <i class="fas ${priorityConfig.icon}"></i>
                                ${priorityConfig.text}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="task-info-grid">
                    ${task.start_date ? `
                        <div class="task-info-item">
                            <i class="far fa-calendar text-primary"></i>
                            <span class="task-info-label">البداية:</span>
                            <span class="task-info-value">${formatDateArabic(task.start_date)}</span>
                        </div>
                    ` : ''}

                    ${task.due_date ? `
                        <div class="task-info-item">
                            <i class="far fa-calendar-check text-danger"></i>
                            <span class="task-info-label">الاستحقاق:</span>
                            <span class="task-info-value">${formatDateArabic(task.due_date)}</span>
                        </div>
                    ` : ''}

                    ${task.cost ? `
                        <div class="task-info-item">
                            <i class="fas fa-money-bill-wave text-success"></i>
                            <span class="task-info-label">التكلفة:</span>
                            <span class="task-info-value">${formatMoney(task.cost)}</span>
                        </div>
                    ` : ''}
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">نسبة الإنجاز</small>
                        <strong style="color: ${statusConfig.color};">${task.completion_percentage}%</strong>
                    </div>
                    <div class="task-progress-bar">
                        <div class="task-progress-fill" style="width: ${task.completion_percentage}%; background-color: ${statusConfig.color};"></div>
                    </div>
                </div>

                ${renderAssignedUsersImproved(task.assigned_users)}
            </div>
        `;
    });

    html += '</div>';
    return html;
}

function renderAssignedUsersImproved(assignedUsers) {
    if (!assignedUsers || assignedUsers.length === 0) {
        return `
            <div class="text-center py-2">
                <small class="text-muted">
                    <i class="fas fa-user-slash me-1"></i>
                    لم يتم تعيين موظفين
                </small>
            </div>
        `;
    }

    const colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#e67e22', '#16a085'];
    let html = '<div class="assigned-users-compact"><small class="text-muted me-2">المكلفين:</small>';

    const displayCount = Math.min(assignedUsers.length, 5);

    for (let i = 0; i < displayCount; i++) {
        const user = assignedUsers[i];
        const color = colors[i % colors.length];
        const initial = user.name.charAt(0).toUpperCase();

        html += `
            <div class="user-avatar-compact"
                 style="background-color: ${color};"
                 data-bs-toggle="tooltip"
                 title="${escapeHtml(user.name)}">
                ${initial}
            </div>
        `;
    }

    if (assignedUsers.length > 5) {
        html += `
            <div class="user-avatar-compact"
                 style="background-color: #6c757d;"
                 data-bs-toggle="tooltip"
                 title="+${assignedUsers.length - 5} آخرين">
                +${assignedUsers.length - 5}
            </div>
        `;
    }

    html += '</div>';
    return html;
}

function getStatusConfig(status) {
    const configs = {
        'not_started': { color: '#6c757d', text: 'لم تبدأ', icon: 'fa-clock' },
        'in_progress': { color: '#ffc107', text: 'قيد التنفيذ', icon: 'fa-play-circle' },
        'completed': { color: '#28a745', text: 'مكتملة', icon: 'fa-check-circle' },
        'overdue': { color: '#dc3545', text: 'متأخرة', icon: 'fa-exclamation-circle' }
    };
    return configs[status] || configs['not_started'];
}

function getPriorityConfig(priority) {
    const configs = {
        'low': { color: '#28a745', text: 'منخفضة', icon: 'fa-arrow-down' },
        'medium': { color: '#17a2b8', text: 'متوسطة', icon: 'fa-minus' },
        'high': { color: '#ffc107', text: 'عالية', icon: 'fa-arrow-up' },
        'urgent': { color: '#dc3545', text: 'عاجلة', icon: 'fa-exclamation' }
    };
    return configs[priority] || configs['medium'];
}

function formatDateArabic(date) {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('ar-SA', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatMoney(amount) {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR',
        minimumFractionDigits: 0
    }).format(amount);
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function getEmptyState() {
    return `
        <div class="tasks-empty-state">
            <i class="fas fa-tasks"></i>
            <p class="text-muted mb-0">لا توجد مهام في هذا المشروع</p>
        </div>
    `;
}

function getErrorState() {
    return `
        <div class="tasks-empty-state">
            <i class="fas fa-exclamation-triangle text-warning"></i>
            <p class="text-danger mb-0">حدث خطأ في تحميل المهام</p>
            <button class="btn btn-sm btn-outline-primary mt-2" onclick="location.reload()">
                <i class="fas fa-redo me-1"></i>إعادة المحاولة
            </button>
        </div>
    `;
}
</script>
