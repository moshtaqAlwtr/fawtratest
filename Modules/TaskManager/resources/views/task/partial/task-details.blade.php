{{-- Modal عرض تفاصيل المهمة مع التعليقات --}}
{{-- المسار: resources/views/taskmanager/task/modals/task-details.blade.php --}}

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
                    loadTaskComments(taskId);
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
        `;

        $('#taskDetailsContent').html(detailsHtml);
    }

    // تحميل تعليقات المهمة
    function loadTaskComments(taskId) {
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
        $('#commentable_id').val(currentTaskId);
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
        $('#commentable_id').val(currentTaskId);
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
                            loadTaskComments(currentTaskId);
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

    // حفظ التعليق
    $('#commentForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const isEditing = isEditingComment && currentCommentId;
        
        const url = isEditing ? `/comments/${currentCommentId}` : '/comments';
        const method = isEditing ? 'PUT' : 'POST';
        
        // إضافة CSRF token
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
                    loadTaskComments(currentTaskId);
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
        $('#taskDetailsContent').html(`
            <div class="alert alert-danger text-center">
                <i class="feather icon-alert-circle me-2"></i>
                ${message}
            </div>
        `);
    }

    // متغير لمعرف المستخدم الحالي (يجب تمريره من Blade)
    const userId = {{ auth()->id() ?? 'null' }};
});
</script>