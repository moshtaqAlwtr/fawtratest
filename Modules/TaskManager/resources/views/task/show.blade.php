@extends('master')

@section('title')
    تفاصيل المهمة
@stop

@section('css')
<style>
/* تنسيقات عامة لتفاصيل المهمة */
.task-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.info-card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    border-left: 4px solid #007bff;
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.info-label {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 8px;
    font-weight: 500;
}

.info-value {
    font-size: 1rem;
    color: #495057;
    font-weight: 600;
}

/* تنسيقات الحالة والأولوية */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-not_started { background: #fff3cd; color: #856404; }
.status-in_progress { background: #d1ecf1; color: #0c5460; }
.status-completed { background: #d4edda; color: #155724; }
.status-overdue { background: #f8d7da; color: #721c24; }

.priority-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.priority-low { background: #e8f5e8; color: #28c76f; }
.priority-medium { background: #fff3cd; color: #ffc107; }
.priority-high { background: #f8d7da; color: #dc3545; }
.priority-urgent {
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    color: white;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(238, 90, 82, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(238, 90, 82, 0); }
    100% { box-shadow: 0 0 0 0 rgba(238, 90, 82, 0); }
}

/* تنسيقات دائرة التقدم */
.progress-circle-container {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto;
}

.progress-circle {
    transform: rotate(-90deg);
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.progress-percentage {
    font-size: 1.8rem;
    font-weight: bold;
    color: #495057;
    line-height: 1;
}

.progress-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 2px;
}

/* تنسيقات المستخدمين */
.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    border: 3px solid #007bff;
    object-fit: cover;
    margin-left: 10px;
}

.user-card {
    background: white;
    border-radius: 10px;
    padding: 15px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.user-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* تنسيقات قسم التعليقات */
.comments-section {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 25px;
    margin-top: 25px;
    max-height: 600px;
    overflow-y: auto;
}

.comments-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #dee2e6;
}

.comments-count {
    background: #007bff;
    color: white;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 600;
}

.comment-item {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    position: relative;
}

.comment-item:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f3f4;
}

.comment-user {
    display: flex;
    align-items: center;
    gap: 12px;
}

.comment-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #007bff;
    object-fit: cover;
}

.comment-user-info h6 {
    margin: 0;
    font-size: 0.95rem;
    color: #495057;
    font-weight: 600;
}

.comment-time {
    font-size: 0.8rem;
    color: #6c757d;
    direction: ltr;
    text-align: left;
}

.comment-content {
    color: #495057;
    line-height: 1.7;
    margin-bottom: 15px;
    font-size: 0.95rem;
}

.comment-actions {
    display: flex;
    gap: 15px;
    align-items: center;
}

.comment-action-btn {
    background: none;
    border: none;
    color: #6c757d;
    font-size: 0.85rem;
    padding: 5px 10px;
    border-radius: 6px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.comment-action-btn:hover {
    background: #f8f9fa;
    color: #495057;
}

.comment-action-btn.active {
    color: #007bff;
    background: #e3f2fd;
}

/* تنسيقات الردود */
.replies-container {
    margin-top: 15px;
    margin-right: 50px;
    padding-right: 20px;
    border-right: 3px solid #e9ecef;
}

.reply-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 10px;
    border: 1px solid #e9ecef;
}

/* تنسيقات responsive */
@media (max-width: 768px) {
    .task-info-grid {
        grid-template-columns: 1fr;
    }

    .replies-container {
        margin-right: 20px;
        padding-right: 15px;
    }

    .comment-actions {
        flex-wrap: wrap;
        gap: 10px;
    }
}

/* تنسيقات إضافية */
.no-comments {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.no-comments i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #adb5bd;
}

.btn-add-comment {
    background: linear-gradient(135deg, #28c76f, #20bf6b);
    border: none;
    padding: 12px 25px;
    border-radius: 25px;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-add-comment:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 199, 111, 0.3);
    color: white;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    z-index: 10;
}
</style>
@endsection

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0" id="task-title">تفاصيل المهمة</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">المهام</a></li>
                        <li class="breadcrumb-item active">تفاصيل</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
        <div class="form-group breadcrumb-right">
            <div class="btn-group">
                <button type="button" class="btn btn-warning btn-sm" onclick="editTask({{ $task->id }})">
                    <i class="feather icon-edit"></i> تعديل
                </button>
                <button type="button" class="btn btn-success btn-sm" onclick="duplicateTask({{ $task->id }})">
                    <i class="feather icon-copy"></i> تكرار
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="deleteTask({{ $task->id }})">
                    <i class="feather icon-trash-2"></i> حذف
                </button>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- تفاصيل المهمة -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4" id="main-task-title">
                        <i class="feather icon-file-text text-primary me-2"></i>
                        {{ $task->title }}
                    </h4>

                    @if($task->description)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">
                                <i class="feather icon-align-left me-1"></i>
                                الوصف
                            </h6>
                            <p class="text-secondary">{{ $task->description }}</p>
                        </div>
                    @endif

                    <div class="task-info-grid" id="task-info-container">
                        <!-- Loading placeholder -->
                        <div class="text-center py-3" id="task-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">جاري التحميل...</span>
                            </div>
                        </div>
                    </div>

                    <!-- المستخدمون المكلفون -->
                    <div id="assigned-users-section" style="display: none;">
                        <h6 class="text-muted mb-3">
                            <i class="feather icon-users me-1"></i>
                            الموظفين المكلفين (<span id="assigned-users-count">0</span>)
                        </h6>
                        <div class="row" id="assigned-users-list">
                            <!-- سيتم ملؤها عبر JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- قسم التعليقات -->
            <div class="comments-section">
                <div class="comments-header">
                    <h5 class="mb-0">
                        <i class="feather icon-message-circle me-2"></i>
                        التعليقات
                        <span class="comments-count" id="commentsCount">0</span>
                    </h5>
                    <button type="button" class="btn btn-add-comment" onclick="openCommentModal()">
                        <i class="feather icon-plus me-1"></i>
                        إضافة تعليق
                    </button>
                </div>

                <div id="commentsContainer">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <p class="mt-2 text-muted">جاري تحميل التعليقات...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="col-lg-4">
            <!-- نسبة الإنجاز -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <h6 class="card-title mb-4">
                        <i class="feather icon-trending-up text-success me-1"></i>
                        نسبة الإنجاز
                    </h6>

                    <div class="progress-circle-container mb-3" id="progress-circle">
                        <!-- سيتم ملؤها عبر JavaScript -->
                    </div>

                    <!-- تحديث نسبة الإنجاز -->
                    <div class="form-group mt-4">
                        <label>تحديث نسبة الإنجاز</label>
                        <input type="range" class="form-range" id="progress-slider" min="0" max="100" value="{{ $task->completion_percentage ?? 0 }}">
                        <div class="d-flex justify-content-between">
                            <small>0%</small>
                            <small id="progress-value">{{ $task->completion_percentage ?? 0 }}%</small>
                            <small>100%</small>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm mt-2" onclick="updateTaskProgress()">
                            تحديث النسبة
                        </button>
                    </div>

                    <div class="text-muted mt-3">
                        <small>آخر تحديث: <span id="last-updated">{{ $task->updated_at->diffForHumans() }}</span></small>
                    </div>
                </div>
            </div>

            <!-- إحصائيات إضافية -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="feather icon-bar-chart-2 text-info me-1"></i>
                        إحصائيات
                    </h6>

                    <div id="task-stats-container">
                        <!-- سيتم ملؤها عبر JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة/تعديل التعليق -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="commentModalLabel">
                    <i class="feather icon-message-circle me-2"></i>
                    <span id="commentModalTitle">إضافة تعليق جديد</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>

            <form id="commentForm">
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="commentFormErrors"></div>

                    <input type="hidden" id="comment_id" name="comment_id">
                    <input type="hidden" id="commentable_type" name="commentable_type" value="App\Models\Task">
                    <input type="hidden" id="commentable_id" name="commentable_id" value="{{ $task->id }}">
                    <input type="hidden" id="parent_id" name="parent_id">

                    <div class="form-group">
                        <label for="comment_content" class="form-label">
                            <i class="feather icon-edit-3 me-1"></i>
                            محتوى التعليق <span class="text-danger">*</span>
                        </label>
                        <textarea
                            id="comment_content"
                            name="content"
                            class="form-control"
                            rows="4"
                            placeholder="اكتب تعليقك هنا..."
                            required
                            maxlength="2000"
                        ></textarea>
                        <div class="form-text">
                            <span id="char_count">0</span> / 2000 حرف
                        </div>
                    </div>

                    <div id="reply_to_section" class="alert alert-info d-none">
                        <i class="feather icon-corner-down-left me-1"></i>
                        <strong>رد على:</strong> <span id="reply_to_user"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="cancelReply()">
                            إلغاء الرد
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="feather icon-x me-1"></i>
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-success" id="saveCommentBtn">
                        <i class="feather icon-send me-1"></i>
                        <span id="saveCommentBtnText">إرسال التعليق</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- يمكن إضافة modal إضافية للتعديل السريع للمهمة -->
<div class="modal fade" id="quickEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل سريع</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="quickEditForm">
                    <div class="form-group">
                        <label>الحالة</label>
                        <select name="status" id="quick_status" class="form-control">
                            <option value="not_started">لم تبدأ</option>
                            <option value="in_progress">قيد التنفيذ</option>
                            <option value="completed">مكتملة</option>
                            <option value="overdue">متأخرة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الأولوية</label>
                        <select name="priority" id="quick_priority" class="form-control">
                            <option value="low">منخفضة</option>
                            <option value="medium">متوسطة</option>
                            <option value="high">عالية</option>
                            <option value="urgent">عاجلة</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="saveQuickEdit()">حفظ</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    const taskId = {{ $task->id }};
    let currentCommentId = null;
    let isEditingComment = false;

    // تحميل البيانات
    loadTaskDetails();
    loadTaskComments();

    // Progress slider events
    $('#progress-slider').on('input', function() {
        const value = $(this).val();
        $('#progress-value').text(value + '%');
        updateProgressCircle(value);
    });

    // تحميل تفاصيل المهمة
    function loadTaskDetails() {
        $.ajax({
            url: `/tasks/${taskId}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    renderTaskDetails(response.task);
                    updateTaskStats(response.task);
                } else {
                    showError('فشل في تحميل تفاصيل المهمة');
                }
            },
            error: function(xhr) {
                console.error('خطأ في تحميل تفاصيل المهمة:', xhr);
                showError('حدث خطأ في تحميل التفاصيل');
            }
        });
    }

    // عرض تفاصيل المهمة
    function renderTaskDetails(task) {
        $('#task-loading').hide();

        const infoCardsHtml = `
            <div class="info-card">
                <div class="info-label">
                    <i class="feather icon-folder me-1"></i>
                    المشروع
                </div>
                <div class="info-value">${task.project?.title || 'غير محدد'}</div>
            </div>

            <div class="info-card">
                <div class="info-label">
                    <i class="feather icon-activity me-1"></i>
                    الحالة
                </div>
                <div class="info-value">
                    <span class="status-badge status-${task.status}">
                        ${getStatusIcon(task.status)}
                        ${getStatusName(task.status)}
                    </span>
                </div>
            </div>

            <div class="info-card">
                <div class="info-label">
                    <i class="feather icon-flag me-1"></i>
                    الأولوية
                </div>
                <div class="info-value">
                    <span class="priority-badge priority-${task.priority}">
                        ${getPriorityIcon(task.priority)}
                        ${getPriorityName(task.priority)}
                    </span>
                </div>
            </div>

            ${task.start_date ? `
                <div class="info-card">
                    <div class="info-label">
                        <i class="feather icon-play-circle me-1"></i>
                        تاريخ البدء
                    </div>
                    <div class="info-value">${formatDate(task.start_date)}</div>
                </div>
            ` : ''}

            ${task.due_date ? `
                <div class="info-card ${task.due_date < new Date().toISOString().split('T')[0] && task.status !== 'completed' ? 'border-danger' : ''}">
                    <div class="info-label">
                        <i class="feather icon-calendar me-1"></i>
                        تاريخ الانتهاء
                    </div>
                    <div class="info-value">
                        ${formatDate(task.due_date)}
                        ${task.due_date < new Date().toISOString().split('T')[0] && task.status !== 'completed' ?
                            '<span class="badge bg-danger ms-2">متأخرة</span>' : ''}
                    </div>
                </div>
            ` : ''}

            ${task.budget ? `
                <div class="info-card">
                    <div class="info-label">
                        <i class="feather icon-dollar-sign me-1"></i>
                        الميزانية
                    </div>
                    <div class="info-value">${Number(task.budget).toLocaleString()} ريال</div>
                </div>
            ` : ''}

            ${task.estimated_hours ? `
                <div class="info-card">
                    <div class="info-label">
                        <i class="feather icon-clock me-1"></i>
                        الساعات المقدرة
                    </div>
                    <div class="info-value">${task.estimated_hours} ساعة</div>
                </div>
            ` : ''}
        `;

        $('#task-info-container').html(infoCardsHtml);

        // عرض المستخدمين المكلفين
        if (task.assigned_users && task.assigned_users.length > 0) {
            renderAssignedUsers(task.assigned_users);
        }

        // تحديث دائرة التقدم
        updateProgressCircle(task.completion_percentage || 0);
        $('#progress-slider').val(task.completion_percentage || 0);
        $('#progress-value').text((task.completion_percentage || 0) + '%');
    }

    // عرض المستخدمين المكلفين
    function renderAssignedUsers(users) {
        const usersHtml = users.map(user => `
            <div class="col-md-6 mb-3">
                <div class="user-card">
                    <div class="d-flex align-items-center">
                        <img src="${user.avatar || '/default-avatar.png'}"
                             alt="${user.name}"
                             class="user-avatar">
                        <div>
                            <h6 class="mb-1">${user.name}</h6>
                            <small class="text-muted">${user.job_title || 'موظف'}</small>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        $('#assigned-users-list').html(usersHtml);
        $('#assigned-users-count').text(users.length);
        $('#assigned-users-section').show();
    }

    // تحديث دائرة التقدم
    function updateProgressCircle(percentage) {
        const color = getProgressColor(percentage);
        const circumference = 2 * Math.PI * 50;
        const offset = circumference - (percentage / 100) * circumference;

        const circleHtml = `
            <svg width="120" height="120" class="progress-circle">
                <circle cx="60" cy="60" r="50" fill="none"
                        stroke="#e9ecef" stroke-width="8"/>
                <circle cx="60" cy="60" r="50" fill="none"
                        stroke="${color}" stroke-width="8"
                        stroke-dasharray="${circumference}"
                        stroke-dashoffset="${offset}"
                        stroke-linecap="round"/>
            </svg>
            <div class="progress-text">
                <div class="progress-percentage">${percentage}%</div>
                <div class="progress-label">مكتمل</div>
            </div>
        `;

        $('#progress-circle').html(circleHtml);
    }

    // تحديث إحصائيات المهمة
    function updateTaskStats(task) {
        const statsHtml = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">منشئ المهمة:</span>
                <strong>${task.creator?.name || 'غير محدد'}</strong>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">تاريخ الإنشاء:</span>
                <strong>${formatDate(task.created_at)}</strong>
            </div>

            ${task.completed_date ? `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">تاريخ الإكمال:</span>
                    <strong class="text-success">${formatDate(task.completed_date)}</strong>
                </div>
            ` : ''}

            ${task.sub_tasks && task.sub_tasks.length > 0 ? `
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">المهام الفرعية:</span>
                    <strong>${task.sub_tasks.filter(st => st.status === 'completed').length}/${task.sub_tasks.length}</strong>
                </div>
            ` : ''}
        `;

        $('#task-stats-container').html(statsHtml);
        $('#last-updated').text(formatDateTime(task.updated_at));
    }

    // تحميل تعليقات المهمة
    function loadTaskComments() {
        $.ajax({
            url: `/comments/task/${taskId}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    renderComments(response.data);
                    $('#commentsCount').text(response.total || 0);
                } else {
                    showError('فشل في تحميل التعليقات');
                }
            },
            error: function(xhr) {
                console.error('خطأ في تحميل التعليقات:', xhr);
                $('#commentsContainer').html(`
                    <div class="no-comments">
                        <i class="feather icon-message-circle"></i>
                        <p>حدث خطأ في تحميل التعليقات</p>
                    </div>
                `);
            }
        });
    }

    // عرض التعليقات
    function renderComments(comments) {
        if (!comments || comments.length === 0) {
            $('#commentsContainer').html(`
                <div class="no-comments">
                    <i class="feather icon-message-circle"></i>
                    <p>لا توجد تعليقات حتى الآن</p>
                    <small class="text-muted">كن أول من يضيف تعليق على هذه المهمة</small>
                </div>
            `);
            return;
        }

        const commentsHtml = comments.map(comment => `
            <div class="comment-item" data-comment-id="${comment.id}">
                <div class="comment-header">
                    <div class="comment-user">
                        <img src="${comment.user?.avatar || '/default-avatar.png'}"
                             alt="${comment.user?.display_name || comment.user?.name}"
                             class="comment-avatar">
                        <div class="comment-user-info">
                            <h6>${comment.user?.display_name || comment.user?.name || 'مستخدم'}</h6>
                        </div>
                    </div>
                    <div class="comment-time">${comment.created_at_human || 'منذ لحظات'}</div>
                </div>

                <div class="comment-content">${comment.content}</div>

                <div class="comment-actions">
                    <button type="button" class="comment-action-btn" onclick="replyToComment(${comment.id}, '${comment.user?.display_name || comment.user?.name}')">
                        <i class="feather icon-corner-down-left me-1"></i>
                        رد
                    </button>

                    ${comment.user_id == userId ? `
                        <button type="button" class="comment-action-btn" onclick="editComment(${comment.id}, '${escapeHtml(comment.content)}')">
                            <i class="feather icon-edit me-1"></i>
                            تعديل
                        </button>

                        <button type="button" class="comment-action-btn text-danger" onclick="deleteComment(${comment.id})">
                            <i class="feather icon-trash-2 me-1"></i>
                            حذف
                        </button>
                    ` : ''}
                </div>

                ${comment.replies && comment.replies.length > 0 ? `
                    <div class="replies-container">
                        ${comment.replies.map(reply => `
                            <div class="reply-item" data-comment-id="${reply.id}">
                                <div class="comment-header">
                                    <div class="comment-user">
                                        <img src="${reply.user?.avatar || '/default-avatar.png'}"
                                             alt="${reply.user?.display_name || reply.user?.name}"
                                             class="comment-avatar" style="width: 30px; height: 30px;">
                                        <div class="comment-user-info">
                                            <h6 style="font-size: 0.85rem;">${reply.user?.display_name || reply.user?.name || 'مستخدم'}</h6>
                                        </div>
                                    </div>
                                    <div class="comment-time">${reply.created_at_human || 'منذ لحظات'}</div>
                                </div>

                                <div class="comment-content" style="font-size: 0.9rem;">${reply.content}</div>

                                ${reply.user_id == userId ? `
                                    <div class="comment-actions mt-2">
                                        <button type="button" class="comment-action-btn" onclick="editComment(${reply.id}, '${escapeHtml(reply.content)}')">
                                            <i class="feather icon-edit me-1"></i>
                                            تعديل
                                        </button>
                                        <button type="button" class="comment-action-btn text-danger" onclick="deleteComment(${reply.id})">
                                            <i class="feather icon-trash-2 me-1"></i>
                                            حذف
                                        </button>
                                    </div>
                                ` : ''}
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
        `).join('');

        $('#commentsContainer').html(commentsHtml);
    }

    // فتح modal إضافة تعليق
    window.openCommentModal = function(parentId = null, parentUserName = null) {
        isEditingComment = false;
        currentCommentId = null;

        $('#commentModalTitle').text('إضافة تعليق جديد');
        $('#saveCommentBtnText').text('إرسال التعليق');
        $('#comment_id').val('');
        $('#parent_id').val(parentId);
        $('#comment_content').val('');
        $('#commentFormErrors').addClass('d-none');

        if (parentId && parentUserName) {
            $('#reply_to_section').removeClass('d-none');
            $('#reply_to_user').text(parentUserName);
        } else {
            $('#reply_to_section').addClass('d-none');
        }

        updateCharCount();
        $('#commentModal').modal('show');
    };

    // رد على تعليق
    window.replyToComment = function(commentId, userName) {
        openCommentModal(commentId, userName);
    };

    // تعديل تعليق
    window.editComment = function(commentId, content) {
        isEditingComment = true;
        currentCommentId = commentId;

        $('#commentModalTitle').text('تعديل التعليق');
        $('#saveCommentBtnText').text('حفظ التعديل');
        $('#comment_id').val(commentId);
        $('#parent_id').val('');
        $('#comment_content').val(content);
        $('#reply_to_section').addClass('d-none');
        $('#commentFormErrors').addClass('d-none');

        updateCharCount();
        $('#commentModal').modal('show');
    };

    // إلغاء الرد
    window.cancelReply = function() {
        $('#parent_id').val('');
        $('#reply_to_section').addClass('d-none');
        $('#commentModalTitle').text('إضافة تعليق جديد');
    };

    // حذف تعليق
    window.deleteComment = function(commentId) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن حذف هذا التعليق!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/comments/${commentId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', response.message);
                            loadTaskComments();
                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('خطأ في حذف التعليق:', xhr);
                        showToast('error', 'حدث خطأ أثناء حذف التعليق');
                    }
                });
            }
        });
    };

    // تحديث نسبة الإنجاز
    window.updateTaskProgress = function() {
        const progressValue = $('#progress-slider').val();

        Swal.fire({
            title: 'جاري التحديث...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `/tasks/${taskId}/update-progress`,
            method: 'POST',
            data: {
                completion_percentage: progressValue
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التحديث!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    updateProgressCircle(progressValue);
                    loadTaskDetails(); // إعادة تحميل البيانات
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                console.error('خطأ في تحديث نسبة الإنجاز:', xhr);
                showToast('error', 'حدث خطأ أثناء التحديث');
            }
        });
    };

    // حفظ التعليق
    $('#commentForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const isEditing = isEditingComment && currentCommentId;

        const url = isEditing ? `/comments/${currentCommentId}` : '/comments';
        const method = isEditing ? 'PUT' : 'POST';

        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        if (isEditing) {
            formData.append('_method', 'PUT');
        }

        $('#saveCommentBtn').prop('disabled', true).html(`
            <div class="spinner-border spinner-border-sm me-1" role="status">
                <span class="visually-hidden">جاري الحفظ...</span>
            </div>
            جاري الحفظ...
        `);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#commentModal').modal('hide');
                    showToast('success', response.message);
                    loadTaskComments();
                } else {
                    showFormErrors(response.errors || {});
                }
            },
            error: function(xhr) {
                console.error('خطأ في حفظ التعليق:', xhr);

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showFormErrors(xhr.responseJSON.errors);
                } else {
                    showToast('error', 'حدث خطأ أثناء حفظ التعليق');
                }
            },
            complete: function() {
                $('#saveCommentBtn').prop('disabled', false).html(`
                    <i class="feather icon-send me-1"></i>
                    <span id="saveCommentBtnText">${isEditing ? 'حفظ التعديل' : 'إرسال التعليق'}</span>
                `);
            }
        });
    });

    // تحديث عداد الأحرف
    $('#comment_content').on('input', updateCharCount);

    function updateCharCount() {
        const content = $('#comment_content').val();
        const count = content.length;
        const maxLength = 2000;

        $('#char_count').text(count);

        const counter = $('#char_count').parent();
        counter.removeClass('text-warning text-danger');

        if (count > maxLength * 0.8) {
            counter.addClass('text-warning');
        }
        if (count > maxLength * 0.9) {
            counter.addClass('text-danger');
        }
    }

    // عرض أخطاء النموذج
    function showFormErrors(errors) {
        const errorContainer = $('#commentFormErrors');
        let errorHtml = '<ul class="mb-0">';

        Object.keys(errors).forEach(field => {
            if (Array.isArray(errors[field])) {
                errors[field].forEach(error => {
                    errorHtml += `<li>${error}</li>`;
                });
            } else {
                errorHtml += `<li>${errors[field]}</li>`;
            }
        });

        errorHtml += '</ul>';
        errorContainer.html(errorHtml).removeClass('d-none');
    }

    // إخفاء أخطاء النموذج عند الكتابة
    $('#comment_content').on('input', function() {
        $('#commentFormErrors').addClass('d-none');
    });

    // دوال المهام (تعديل، حذف، تكرار)
    window.editTask = function(taskId) {
        window.location.href = `/tasks/${taskId}/edit`;
    };

    window.deleteTask = function(taskId) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن حذف هذه المهمة!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/tasks/${taskId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '{{ route("tasks.index") }}';
                            });
                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('خطأ في حذف المهمة:', xhr);
                        showToast('error', 'حدث خطأ أثناء حذف المهمة');
                    }
                });
            }
        });
    };

    window.duplicateTask = function(taskId) {
        Swal.fire({
            title: 'تكرار المهمة',
            text: "سيتم إنشاء نسخة من هذه المهمة",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، كرر المهمة',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/tasks/${taskId}/duplicate`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التكرار!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = `/tasks/${response.task.id}`;
                            });
                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('خطأ في تكرار المهمة:', xhr);
                        showToast('error', 'حدث خطأ أثناء تكرار المهمة');
                    }
                });
            }
        });
    };

    // دوال مساعدة
    function getStatusName(status) {
        const statuses = {
            'not_started': 'لم تبدأ',
            'in_progress': 'قيد التنفيذ',
            'completed': 'مكتملة',
            'overdue': 'متأخرة'
        };
        return statuses[status] || status;
    }

    function getStatusIcon(status) {
        const icons = {
            'not_started': '<i class="feather icon-pause-circle"></i>',
            'in_progress': '<i class="feather icon-play-circle"></i>',
            'completed': '<i class="feather icon-check-circle"></i>',
            'overdue': '<i class="feather icon-alert-circle"></i>'
        };
        return icons[status] || '<i class="feather icon-circle"></i>';
    }

    function getPriorityName(priority) {
        const priorities = {
            'low': 'منخفضة',
            'medium': 'متوسطة',
            'high': 'عالية',
            'urgent': 'عاجلة'
        };
        return priorities[priority] || priority;
    }

    function getPriorityIcon(priority) {
        const icons = {
            'low': '<i class="feather icon-arrow-down"></i>',
            'medium': '<i class="feather icon-minus"></i>',
            'high': '<i class="feather icon-arrow-up"></i>',
            'urgent': '<i class="feather icon-zap"></i>'
        };
        return icons[priority] || '<i class="feather icon-flag"></i>';
    }

    function getProgressColor(percentage) {
        if (percentage >= 70) return '#28c76f';
        if (percentage >= 30) return '#ff9f43';
        return '#ea5455';
    }

    function formatDate(dateString) {
        if (!dateString) return 'غير محدد';

        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('ar-SA', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        } catch (e) {
            return dateString;
        }
    }

    function formatDateTime(dateString) {
        if (!dateString) return 'غير محدد';

        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('ar-SA', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return dateString;
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/'/g, '&#39;');
    }

    function showToast(type, message) {
        const icons = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        };

        Swal.fire({
            icon: icons[type] || 'info',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: type === 'error' ? 4000 : 2500,
            timerProgressBar: true
        });
    }

    function showError(message) {
        $('#task-info-container').html(`
            <div class="alert alert-danger text-center">
                <i class="feather icon-alert-circle me-2"></i>
                ${message}
            </div>
        `);
    }

    // متغير لمعرف المستخدم الحالي
    const userId = {{ auth()->id() ?? 'null' }};
});
</script>
<script>
    // ملف إضافي للـ JavaScript يمكن إضافته في أسفل صفحة show.blade.php

// تحسين تجربة المستخدم مع التعليقات
$(document).ready(function() {

    // تفعيل تحديث تلقائي للتعليقات كل 30 ثانية
    let commentsRefreshInterval = setInterval(function() {
        if (!$('#commentModal').hasClass('show')) {
            loadTaskComments();
        }
    }, 30000);

    // إيقاف التحديث التلقائي عند مغادرة الصفحة
    $(window).on('beforeunload', function() {
        if (commentsRefreshInterval) {
            clearInterval(commentsRefreshInterval);
        }
    });

    // تحسين عرض الـ tooltips
    function initTooltips() {
        $('[data-toggle="tooltip"]').tooltip({
            trigger: 'hover',
            delay: { show: 500, hide: 100 }
        });
    }

    // استدعاء تفعيل الـ tooltips بعد تحميل التعليقات
    $(document).on('DOMNodeInserted', function() {
        initTooltips();
    });

    // تحسين النموذج
    $('#commentModal').on('shown.bs.modal', function() {
        $('#comment_content').focus();
        updateCharCount();
    });

    // تنظيف النموذج عند إغلاقه
    $('#commentModal').on('hidden.bs.modal', function() {
        resetCommentForm();
    });

    // إعادة تعيين نموذج التعليق
    function resetCommentForm() {
        $('#commentForm')[0].reset();
        $('#comment_id').val('');
        $('#parent_id').val('');
        $('#commentFormErrors').addClass('d-none');
        $('#reply_to_section').addClass('d-none');
        $('#commentModalTitle').text('إضافة تعليق جديد');
        $('#saveCommentBtnText').text('إرسال التعليق');
        isEditingComment = false;
        currentCommentId = null;
        updateCharCount();
    }

    // تحسين عملية السحب والإفلات للملفات (إذا كنت تريد إضافة ملفات للتعليقات)
    let dragCounter = 0;

    $('#comment_content').on('dragenter', function(e) {
        e.preventDefault();
        dragCounter++;
        $(this).addClass('drag-over');
    });

    $('#comment_content').on('dragleave', function(e) {
        e.preventDefault();
        dragCounter--;
        if (dragCounter === 0) {
            $(this).removeClass('drag-over');
        }
    });

    // إضافة تأثيرات بصرية للتفاعل
    $(document).on('click', '.comment-action-btn', function() {
        $(this).addClass('clicked');
        setTimeout(() => {
            $(this).removeClass('clicked');
        }, 150);
    });

    // تحسين البحث في التعليقات
    function addCommentsSearch() {
        const searchHtml = `
            <div class="comments-search mb-3">
                <div class="input-group">
                    <input type="text"
                           id="commentsSearchInput"
                           class="form-control form-control-sm"
                           placeholder="البحث في التعليقات...">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-sm"
                                type="button"
                                onclick="clearCommentsSearch()">
                            <i class="feather icon-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('.comments-header').after(searchHtml);
    }

    // البحث في التعليقات
    $(document).on('input', '#commentsSearchInput', function() {
        const searchTerm = $(this).val().toLowerCase().trim();

        $('.comment-item').each(function() {
            const commentContent = $(this).find('.comment-content').text().toLowerCase();
            const userName = $(this).find('.comment-user-info h6').text().toLowerCase();

            if (commentContent.includes(searchTerm) || userName.includes(searchTerm) || searchTerm === '') {
                $(this).show().removeClass('search-hidden');
            } else {
                $(this).hide().addClass('search-hidden');
            }
        });

        // عرض رسالة عدم وجود نتائج
        const visibleComments = $('.comment-item:visible').length;
        if (visibleComments === 0 && searchTerm !== '') {
            if ($('#noSearchResults').length === 0) {
                $('#commentsContainer').append(`
                    <div id="noSearchResults" class="no-comments">
                        <i class="feather icon-search"></i>
                        <p>لا توجد نتائج للبحث عن "${searchTerm}"</p>
                    </div>
                `);
            }
        } else {
            $('#noSearchResults').remove();
        }
    });

    // مسح البحث
    window.clearCommentsSearch = function() {
        $('#commentsSearchInput').val('');
        $('.comment-item').show().removeClass('search-hidden');
        $('#noSearchResults').remove();
    };

    // إضافة مؤشر الكتابة للتعليقات المباشرة
    let typingTimer;
    $('#comment_content').on('input', function() {
        clearTimeout(typingTimer);
        showTypingIndicator();

        typingTimer = setTimeout(function() {
            hideTypingIndicator();
        }, 2000);
    });

    function showTypingIndicator() {
        if ($('#typingIndicator').length === 0) {
            $('.comments-header').after(`
                <div id="typingIndicator" class="typing-indicator">
                    <small class="text-muted">
                        <i class="feather icon-edit-3 me-1"></i>
                        ${getCurrentUserName()} يكتب تعليقاً...
                    </small>
                </div>
            `);
        }
    }

    function hideTypingIndicator() {
        $('#typingIndicator').fadeOut(300, function() {
            $(this).remove();
        });
    }

    function getCurrentUserName() {
        return '{{ auth()->user()->name ?? "مستخدم" }}';
    }

    // تحسين عرض الأخطاء
    function showFormErrors(errors) {
        const errorContainer = $('#commentFormErrors');
        let errorHtml = '<div class="alert-content">';
        errorHtml += '<strong><i class="feather icon-alert-circle me-1"></i>يرجى تصحيح الأخطاء التالية:</strong>';
        errorHtml += '<ul class="mb-0 mt-2">';

        Object.keys(errors).forEach(field => {
            if (Array.isArray(errors[field])) {
                errors[field].forEach(error => {
                    errorHtml += `<li>${error}</li>`;
                });
            } else {
                errorHtml += `<li>${errors[field]}</li>`;
            }
        });

        errorHtml += '</ul></div>';
        errorContainer.html(errorHtml).removeClass('d-none');

        // إضافة تأثير الاهتزاز
        errorContainer.addClass('shake');
        setTimeout(() => {
            errorContainer.removeClass('shake');
        }, 600);
    }

    // إضافة تأثير التحميل للتعليقات
    function showCommentsLoading() {
        $('#commentsContainer').html(`
            <div class="comments-loading text-center py-4">
                <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
                <p class="text-muted mb-0">جاري تحميل التعليقات...</p>
            </div>
        `);
    }

    // تحسين عرض الوقت النسبي
    function updateRelativeTimes() {
        $('.comment-time').each(function() {
            const timeElement = $(this);
            const originalTime = timeElement.data('original-time');

            if (originalTime) {
                const now = new Date();
                const commentTime = new Date(originalTime);
                const diffInMinutes = Math.floor((now - commentTime) / (1000 * 60));

                let relativeTime;
                if (diffInMinutes < 1) {
                    relativeTime = 'الآن';
                } else if (diffInMinutes < 60) {
                    relativeTime = `منذ ${diffInMinutes} د`;
                } else if (diffInMinutes < 1440) {
                    relativeTime = `منذ ${Math.floor(diffInMinutes / 60)} س`;
                } else if (diffInMinutes < 43200) {
                    relativeTime = `منذ ${Math.floor(diffInMinutes / 1440)} يوم`;
                } else {
                    relativeTime = commentTime.toLocaleDateString('ar-SA', {
                        month: 'short',
                        day: 'numeric'
                    });
                }

                timeElement.text(relativeTime);
            }
        });
    }

    // تحديث الأوقات كل دقيقة
    setInterval(updateRelativeTimes, 60000);

    // إضافة اختصارات لوحة المفاتيح
    $(document).on('keydown', function(e) {
        // Ctrl/Cmd + Enter لإرسال التعليق
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 13) {
            if ($('#commentModal').hasClass('show') && $('#comment_content').is(':focus')) {
                e.preventDefault();
                $('#commentForm').submit();
            }
        }

        // Escape لإغلاق المودال
        if (e.keyCode === 27) {
            if ($('#commentModal').hasClass('show')) {
                $('#commentModal').modal('hide');
            }
        }
    });

    // إضافة معاينة للتعليق قبل الإرسال
    $('#comment_content').on('input', function() {
        const content = $(this).val();
        if (content.trim().length > 50) {
            showCommentPreview(content);
        } else {
            hideCommentPreview();
        }
    });

    function showCommentPreview(content) {
        if ($('#commentPreview').length === 0) {
            $('#comment_content').after(`
                <div id="commentPreview" class="comment-preview mt-2 p-2 border rounded">
                    <small class="text-muted d-block mb-1">
                        <i class="feather icon-eye me-1"></i>معاينة:
                    </small>
                    <div class="preview-content"></div>
                </div>
            `);
        }

        $('#commentPreview .preview-content').html(formatContentPreview(content));
    }

    function hideCommentPreview() {
        $('#commentPreview').remove();
    }

    function formatContentPreview(content) {
        // تحويل النص إلى HTML بسيط مع كسر الأسطر
        return content.replace(/\n/g, '<br>').substring(0, 200) + (content.length > 200 ? '...' : '');
    }

    // إضافة إحصائيات التعليقات
    function updateCommentsStats() {
        const totalComments = $('.comment-item').length;
        const totalReplies = $('.reply-item').length;
        const todayComments = $('.comment-item[data-today="true"]').length;

        if ($('#commentsStats').length === 0) {
            $('.comments-header').after(`
                <div id="commentsStats" class="comments-stats mb-3 p-2 bg-light rounded">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="d-block font-weight-bold" id="totalCommentsCount">${totalComments}</small>
                            <small class="text-muted">تعليقات</small>
                        </div>
                        <div class="col-4">
                            <small class="d-block font-weight-bold" id="totalRepliesCount">${totalReplies}</small>
                            <small class="text-muted">ردود</small>
                        </div>
                        <div class="col-4">
                            <small class="d-block font-weight-bold" id="todayCommentsCount">${todayComments}</small>
                            <small class="text-muted">اليوم</small>
                        </div>
                    </div>
                </div>
            `);
        } else {
            $('#totalCommentsCount').text(totalComments);
            $('#totalRepliesCount').text(totalReplies);
            $('#todayCommentsCount').text(todayComments);
        }
    }

    // تحديث الإحصائيات عند تحميل التعليقات
    $(document).on('commentsLoaded', updateCommentsStats);

    // إضافة تأثيرات CSS
    $('<style>').text(`
        .comment-action-btn.clicked {
            transform: scale(0.95);
            background-color: var(--bs-primary) !important;
            color: white !important;
        }

        .drag-over {
            border: 2px dashed var(--bs-primary) !important;
            background-color: rgba(var(--bs-primary-rgb), 0.05) !important;
        }

        .shake {
            animation: shake 0.6s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
            20%, 40%, 60%, 80% { transform: translateX(2px); }
        }

        .typing-indicator {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .comment-preview {
            background-color: #f8f9fa;
            font-size: 0.9em;
            line-height: 1.4;
        }

        .comments-stats {
            border: 1px solid #dee2e6;
        }

        .search-hidden {
            display: none !important;
        }
    `).appendTo('head');

});

// إضافة دالة لإظهار رسائل النجاح مع تأثيرات أفضل
function showSuccessMessage(message) {
    // إنشاء عنصر الرسالة
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show success-message" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="feather icon-check-circle me-2"></i>
            <strong>${message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    $('body').append(alertHtml);

    // إزالة الرسالة تلقائياً بعد 3 ثواني
    setTimeout(() => {
        $('.success-message').alert('close');
    }, 3000);
}
</script>
@endsection
