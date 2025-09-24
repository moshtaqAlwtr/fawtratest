@extends('master')

@section('title')
إدارة المهام
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">إدارة المهام</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                        <li class="breadcrumb-item active">المهام</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
        <button type="button" class="btn btn-primary" id="btnAddTask">
            <i class="feather icon-plus"></i> إضافة مهمة جديدة
        </button>
    </div>
</div>

<div class="content-body">
    <!-- فلاتر البحث المحسنة -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label>المشروع</label>
                    <select class="form-control select2" id="filterProject">
                        <option value="">جميع المشاريع</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>الحالة</label>
                    <select class="form-control select2" id="filterStatus">
                        <option value="">جميع الحالات</option>
                        <option value="not_started">لم تبدأ</option>
                        <option value="in_progress">قيد التنفيذ</option>
                        <option value="completed">مكتملة</option>
                        <option value="overdue">متأخرة</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>الأولوية</label>
                    <select class="form-control select2" id="filterPriority">
                        <option value="">جميع الأولويات</option>
                        <option value="low">منخفضة</option>
                        <option value="medium">متوسطة</option>
                        <option value="high">عالية</option>
                        <option value="urgent">عاجلة</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>المستخدم المعين</label>
                    <select class="form-control select2" id="filterAssignee">
                        <option value="">الجميع</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary btn-block" id="btnFilterTasks">
                        <i class="feather icon-search"></i> بحث
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- لوحة المهام بالسحب والإفلات -->
    <div class="row task-board">
        @foreach($statuses as $statusKey => $statusName)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ $statusName }}</h4>
                        <span class="badge badge-pill task-count task-count-{{ $statusKey }}">
                            {{ isset($tasks[$statusKey]) ? $tasks[$statusKey]->count() : 0 }}
                        </span>
                    </div>
                    <div class="card-body task-column" data-status="{{ $statusKey }}">
                        @if(isset($tasks[$statusKey]))
                            @foreach($tasks[$statusKey] as $task)
                                @include('taskmanager::task.partial.task-card', ['task' => $task])
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal إضافة/تعديل المهمة -->
<div class="modal fade" id="taskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalTitle">إضافة مهمة جديدة</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="taskForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="task_id">

                <div class="modal-body">
                    <div class="alert alert-danger" id="formErrors" style="display: none;"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>المشروع <span class="text-danger">*</span></label>
                                <select name="project_id" id="project_id" class="form-control select2" required>
                                    <option value="">اختر المشروع</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>المهمة الرئيسية</label>
                                <select name="parent_task_id" id="parent_task_id" class="form-control select2">
                                    <option value="">لا يوجد (مهمة رئيسية)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>عنوان المهمة <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>الوصف</label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>الحالة <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control select2" required>
                                    <option value="not_started">لم تبدأ</option>
                                    <option value="in_progress">قيد التنفيذ</option>
                                    <option value="completed">مكتملة</option>
                                    <option value="overdue">متأخرة</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>الأولوية <span class="text-danger">*</span></label>
                                <select name="priority" id="priority" class="form-control" required>
                                    <option value="low">منخفضة</option>
                                    <option value="medium">متوسطة</option>
                                    <option value="high">عالية</option>
                                    <option value="urgent">عاجلة</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>تاريخ البدء</label>
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>تاريخ الانتهاء</label>
                                <input type="date" name="due_date" id="due_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>الميزانية</label>
                                <input type="number" name="budget" id="budget" class="form-control" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>الساعات المقدرة</label>
                                <input type="number" name="estimated_hours" id="estimated_hours" class="form-control" step="0.5">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>نسبة الإنجاز (%)</label>
                                <input type="number" name="completion_percentage" id="completion_percentage" class="form-control" min="0" max="100" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>تعيين المستخدمين</label>
                        <select name="assigned_users[]" id="assigned_users" class="form-control select2" multiple>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>إرفاق ملفات</label>
                        <input type="file" name="files[]" id="files" class="form-control" multiple>
                        <small class="text-muted">الحد الأقصى: 10 ميجابايت</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="send_notifications" name="send_notifications" value="1">
                            <label class="custom-control-label" for="send_notifications">إرسال إشعارات للمستخدمين</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" id="saveTaskBtn">
                        <i class="feather icon-save"></i> حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- تضمين Modal عرض تفاصيل المهمة مع التعليقات --}}
@include('taskmanager::task.modals.task-details')

{{-- Modal عرض تفاصيل المهمة مع التعليقات --}}
<div class="modal fade task-details-modal" id="taskDetailsModal" tabindex="-1" role="dialog" aria-labelledby="taskDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="taskDetailsModalLabel">
                    <i class="feather icon-eye me-2"></i>
                    تفاصيل المهمة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            
            <div class="modal-body" id="taskDetailsContent">
                {{-- محتوى ديناميكي سيتم تحميله عبر AJAX --}}
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-3 text-muted">جاري تحميل تفاصيل المهمة...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal إضافة/تعديل التعليق --}}
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
                    <input type="hidden" id="commentable_type" name="commentable_type" value="App\\Models\\Task">
                    <input type="hidden" id="commentable_id" name="commentable_id">
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

@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/task-manager.css') }}">

<style>
/* تنسيقات Modal عرض التفاصيل */
.task-details-modal .modal-dialog {
    max-width: 1100px;
}

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

/* تنسيقات النموذج */
.form-floating textarea {
    min-height: 120px;
    resize: vertical;
}

.char-counter {
    font-size: 0.8rem;
    color: #6c757d;
    text-align: left;
    direction: ltr;
}

.char-counter.warning {
    color: #ffc107;
}

.char-counter.danger {
    color: #dc3545;
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // تحديد معرف المستخدم الحالي للاستخدام في JavaScript
    window.authUserId = {{ auth()->id() ?? 'null' }};
</script>

<!-- الملفات المنظمة -->
<script src="{{ asset('assets/js/task/utils.js') }}"></script>
<script src="{{ asset('assets/js/task/task-card.js') }}"></script>
<script src="{{ asset('assets/js/task/drag-drop.js') }}"></script>
<script src="{{ asset('assets/js/task/task-updater.js') }}"></script>
<script src="{{ asset('assets/js/task/task-modal.js') }}"></script>
<script src="{{ asset('assets/js/task/comments-system.js') }}"></script>
<script src="{{ asset('assets/js/task/task-manager.js') }}"></script>

<script>
$(document).ready(function() {
    // متغيرات عامة
    let currentTaskId = null;
    let currentCommentId = null;
    let isEditingComment = false;

    // فتح modal تفاصيل المهمة
    window.showTaskDetails = function(taskId) {
        currentTaskId = taskId;
        $('#taskDetailsModal').modal('show');
        loadTaskDetails(taskId);
    };

    // تحميل تفاصيل المهمة
    function loadTaskDetails(taskId) {
        const loadingHtml = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
                <p class="mt-3 text-muted">جاري تحميل تفاصيل المهمة...</p>
            </div>
        `;
        
        $('#taskDetailsContent').html(loadingHtml);

        $.ajax({
            url: `/tasks/${taskId}/details`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    renderTaskDetails(response.task);
                    if (window.commentsSystem) {
                        window.commentsSystem.loadTaskComments(taskId);
                    }
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
        const progressColor = getProgressColor(task.completion_percentage || 0);
        const circumference = 2 * Math.PI * 50;
        const offset = circumference - ((task.completion_percentage || 0) / 100) * circumference;

        const detailsHtml = `
            <div class="row">
                <!-- معلومات المهمة الأساسية -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="feather icon-file-text text-primary me-2"></i>
                                ${task.title}
                            </h4>
                            
                            ${task.description ? `
                                <div class="mb-4">
                                    <h6 class="text-muted mb-2">
                                        <i class="feather icon-align-left me-1"></i>
                                        الوصف
                                    </h6>
                                    <p class="text-secondary">${task.description}</p>
                                </div>
                            ` : ''}

                            <div class="task-info-grid">
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
                            </div>

                            <!-- المستخدمون المكلفون -->
                            ${task.assigned_users && task.assigned_users.length > 0 ? `
                                <div class="mb-4">
                                    <h6 class="text-muted mb-3">
                                        <i class="feather icon-users me-1"></i>
                                        الموظفين المكلفين (${task.assigned_users.length})
                                    </h6>
                                    <div class="row">
                                        ${task.assigned_users.map(user => `
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
                                        `).join('')}
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>

                <!-- إحصائيات ونسبة الإنجاز -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-4">
                                <i class="feather icon-trending-up text-success me-1"></i>
                                نسبة الإنجاز
                            </h6>
                            
                            <div class="progress-circle-container mb-3">
                                <svg width="120" height="120" class="progress-circle">
                                    <circle cx="60" cy="60" r="50" fill="none" 
                                            stroke="#e9ecef" stroke-width="8"/>
                                    <circle cx="60" cy="60" r="50" fill="none" 
                                            stroke="${progressColor}" stroke-width="8"
                                            stroke-dasharray="${circumference}"
                                            stroke-dashoffset="${offset}"
                                            stroke-linecap="round"/>
                                </svg>
                                <div class="progress-text">
                                    <div class="progress-percentage">${task.completion_percentage || 0}%</div>
                                    <div class="progress-label">مكتمل</div>
                                </div>
                            </div>

                            <div class="text-muted">
                                <small>آخر تحديث: ${formatDateTime(task.updated_at)}</small>
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
                    <button type="button" class="btn btn-add-comment" onclick="openCommentModal(${task.id})">
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
        `;

        $('#taskDetailsContent').html(detailsHtml);
    }

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

    function showError(message) {
        $('#taskDetailsContent').html(`
            <div class="alert alert-danger text-center">
                <i class="feather icon-alert-circle me-2"></i>
                ${message}
            </div>
        `);
    }

    // فتح modal إضافة تعليق جديد
    window.openCommentModal = function(taskId, parentId = null, parentUserName = null) {
        if (window.commentsSystem) {
            window.commentsSystem.openCommentModal(taskId, parentId, parentUserName);
        }
    };

    // دوال التعليقات العامة
    window.replyToComment = function(commentId, userName, taskId) {
        if (window.commentsSystem) {
            window.commentsSystem.replyToComment(commentId, userName, taskId);
        }
    };
    
    window.editComment = function(commentId, content, taskId) {
        if (window.commentsSystem) {
            window.commentsSystem.editComment(commentId, content, taskId);
        }
    };
    
    window.deleteComment = function(commentId) {
        if (window.commentsSystem) {
            window.commentsSystem.deleteComment(commentId);
        }
    };
    
    window.cancelReply = function() {
        if (window.commentsSystem) {
            window.commentsSystem.cancelReply();
        }
    };
});
</script>
@endsection