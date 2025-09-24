/**
 * نظام إدارة التعليقات
 * المسار: public/assets/js/task/comments-system.js
 */

class CommentsSystem {
    constructor() {
        this.currentTaskId = null;
        this.currentCommentId = null;
        this.isEditingComment = false;
        this.userId = window.authUserId || null;
        this.init();
    }

    init() {
        this.initializeEventListeners();
        this.initializeCharacterCounter();
    }

    initializeEventListeners() {
        // حفظ التعليق
        $('#commentForm').on('submit', (e) => {
            e.preventDefault();
            this.saveComment();
        });

        // تحديث عداد الأحرف
        $('#comment_content').on('input', () => {
            this.updateCharCount();
        });

        // إخفاء الأخطاء عند الكتابة
        $('#comment_content').on('input', () => {
            $('#commentFormErrors').addClass('d-none');
        });

        // إغلاق modal التعليق
        $('#commentModal').on('hidden.bs.modal', () => {
            this.resetCommentForm();
        });
    }

    initializeCharacterCounter() {
        this.updateCharCount();
    }

    /**
     * فتح modal إضافة تعليق جديد
     */
    openCommentModal(taskId, parentId = null, parentUserName = null) {
        this.currentTaskId = taskId;
        this.isEditingComment = false;
        this.currentCommentId = null;

        $('#commentModalTitle').text('إضافة تعليق جديد');
        $('#saveCommentBtnText').text('إرسال التعليق');
        $('#comment_id').val('');
        $('#commentable_id').val(taskId);
        $('#parent_id').val(parentId || '');
        $('#comment_content').val('');
        $('#commentFormErrors').addClass('d-none');

        if (parentId && parentUserName) {
            $('#reply_to_section').removeClass('d-none');
            $('#reply_to_user').text(parentUserName);
        } else {
            $('#reply_to_section').addClass('d-none');
        }

        this.updateCharCount();
        $('#commentModal').modal('show');
    }

    /**
     * رد على تعليق
     */
    replyToComment(commentId, userName, taskId) {
        this.openCommentModal(taskId, commentId, userName);
    }

    /**
     * تعديل تعليق
     */
    editComment(commentId, content, taskId) {
        this.isEditingComment = true;
        this.currentCommentId = commentId;
        this.currentTaskId = taskId;

        $('#commentModalTitle').text('تعديل التعليق');
        $('#saveCommentBtnText').text('حفظ التعديل');
        $('#comment_id').val(commentId);
        $('#commentable_id').val(taskId);
        $('#parent_id').val('');
        $('#comment_content').val(content);
        $('#reply_to_section').addClass('d-none');
        $('#commentFormErrors').addClass('d-none');

        this.updateCharCount();
        $('#commentModal').modal('show');
    }

    /**
     * إلغاء الرد
     */
    cancelReply() {
        $('#parent_id').val('');
        $('#reply_to_section').addClass('d-none');
        $('#commentModalTitle').text('إضافة تعليق جديد');
    }

    /**
     * حذف تعليق
     */
    deleteComment(commentId) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن حذف هذا التعليق!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                this.performDeleteComment(commentId);
            }
        });
    }

    /**
     * تنفيذ حذف التعليق
     */
    performDeleteComment(commentId) {
        $.ajax({
            url: `/comments/${commentId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: () => {
                this.showCommentLoading(commentId, true);
            },
            success: (response) => {
                if (response.success) {
                    this.showToast('success', response.message || 'تم حذف التعليق بنجاح');
                    this.loadTaskComments(this.currentTaskId);
                } else {
                    this.showToast('error', response.message || 'فشل في حذف التعليق');
                }
            },
            error: (xhr) => {
                console.error('خطأ في حذف التعليق:', xhr);
                this.showToast('error', 'حدث خطأ أثناء حذف التعليق');
            },
            complete: () => {
                this.showCommentLoading(commentId, false);
            }
        });
    }

    /**
     * حفظ التعليق
     */
    saveComment() {
        const formData = new FormData($('#commentForm')[0]);
        const isEditing = this.isEditingComment && this.currentCommentId;

        const url = isEditing ? `/comments/${this.currentCommentId}` : '/comments';
        
        // إضافة CSRF token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        if (isEditing) {
            formData.append('_method', 'PUT');
        }

        this.showSaveLoading(true);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    $('#commentModal').modal('hide');
                    this.showToast('success', response.message || 'تم حفظ التعليق بنجاح');
                    this.loadTaskComments(this.currentTaskId);
                    this.resetCommentForm();
                } else {
                    this.showFormErrors(response.errors || {});
                }
            },
            error: (xhr) => {
                console.error('خطأ في حفظ التعليق:', xhr);

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    this.showFormErrors(xhr.responseJSON.errors);
                } else {
                    this.showToast('error', 'حدث خطأ أثناء حفظ التعليق');
                }
            },
            complete: () => {
                this.showSaveLoading(false);
            }
        });
    }

    /**
     * تحميل تعليقات المهمة
     */
    loadTaskComments(taskId) {
        const container = $('#commentsContainer');
        
        container.html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
                <p class="mt-2 text-muted">جاري تحميل التعليقات...</p>
            </div>
        `);

        $.ajax({
            url: `/comments/task/${taskId}`,
            type: 'GET',
            success: (response) => {
                if (response.success) {
                    this.renderComments(response.data);
                    $('#commentsCount').text(response.total || 0);
                } else {
                    this.showError('فشل في تحميل التعليقات');
                }
            },
            error: (xhr) => {
                console.error('خطأ في تحميل التعليقات:', xhr);
                container.html(`
                    <div class="text-center py-4">
                        <i class="feather icon-alert-circle text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-2 text-muted">حدث خطأ في تحميل التعليقات</p>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="commentsSystem.loadTaskComments(${taskId})">
                            <i class="feather icon-refresh-cw me-1"></i>
                            إعادة المحاولة
                        </button>
                    </div>
                `);
            }
        });
    }

    /**
     * عرض التعليقات
     */
    renderComments(comments) {
        const container = $('#commentsContainer');

        if (!comments || comments.length === 0) {
            container.html(`
                <div class="text-center py-5">
                    <i class="feather icon-message-circle text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">لا توجد تعليقات حتى الآن</h5>
                    <p class="text-muted">كن أول من يضيف تعليق على هذه المهمة</p>
                    <button type="button" class="btn btn-primary mt-2" onclick="commentsSystem.openCommentModal(${this.currentTaskId})">
                        <i class="feather icon-plus me-1"></i>
                        إضافة تعليق
                    </button>
                </div>
            `);
            return;
        }

        const commentsHtml = comments.map(comment => this.renderComment(comment)).join('');
        container.html(commentsHtml);
    }

    /**
     * عرض تعليق واحد
     */
    renderComment(comment) {
        const isOwner = comment.user_id == this.userId;
        const repliesHtml = comment.replies && comment.replies.length > 0 
            ? comment.replies.map(reply => this.renderReply(reply)).join('') 
            : '';

        return `
            <div class="comment-item" data-comment-id="${comment.id}">
                <div class="comment-header">
                    <div class="comment-user">
                        <img src="${comment.user?.avatar || '/default-avatar.png'}" 
                             alt="${this.getUserDisplayName(comment.user)}" 
                             class="comment-avatar"
                             onerror="this.src='/default-avatar.png'">
                        <div class="comment-user-info">
                            <h6>${this.getUserDisplayName(comment.user)}</h6>
                        </div>
                    </div>
                    <div class="comment-time">${comment.created_at_human || 'منذ لحظات'}</div>
                </div>

                <div class="comment-content">${this.escapeHtml(comment.content)}</div>

                <div class="comment-actions">
                    <button type="button" class="comment-action-btn" 
                            onclick="commentsSystem.replyToComment(${comment.id}, '${this.getUserDisplayName(comment.user)}', ${this.currentTaskId})">
                        <i class="feather icon-corner-down-left me-1"></i>
                        رد
                    </button>
                    
                    ${isOwner ? `
                        <button type="button" class="comment-action-btn" 
                                onclick="commentsSystem.editComment(${comment.id}, '${this.escapeHtml(comment.content)}', ${this.currentTaskId})">
                            <i class="feather icon-edit me-1"></i>
                            تعديل
                        </button>
                        
                        <button type="button" class="comment-action-btn text-danger" 
                                onclick="commentsSystem.deleteComment(${comment.id})">
                            <i class="feather icon-trash-2 me-1"></i>
                            حذف
                        </button>
                    ` : ''}
                </div>

                ${repliesHtml ? `
                    <div class="replies-container">
                        ${repliesHtml}
                    </div>
                ` : ''}
            </div>
        `;
    }

    /**
     * عرض رد على تعليق
     */
    renderReply(reply) {
        const isOwner = reply.user_id == this.userId;

        return `
            <div class="reply-item" data-comment-id="${reply.id}">
                <div class="comment-header">
                    <div class="comment-user">
                        <img src="${reply.user?.avatar || '/default-avatar.png'}" 
                             alt="${this.getUserDisplayName(reply.user)}" 
                             class="comment-avatar" style="width: 32px; height: 32px;"
                             onerror="this.src='/default-avatar.png'">
                        <div class="comment-user-info">
                            <h6 style="font-size: 0.9rem;">${this.getUserDisplayName(reply.user)}</h6>
                        </div>
                    </div>
                    <div class="comment-time">${reply.created_at_human || 'منذ لحظات'}</div>
                </div>
                
                <div class="comment-content" style="font-size: 0.9rem;">${this.escapeHtml(reply.content)}</div>
                
                ${isOwner ? `
                    <div class="comment-actions mt-2">
                        <button type="button" class="comment-action-btn" 
                                onclick="commentsSystem.editComment(${reply.id}, '${this.escapeHtml(reply.content)}', ${this.currentTaskId})">
                            <i class="feather icon-edit me-1"></i>
                            تعديل
                        </button>
                        <button type="button" class="comment-action-btn text-danger" 
                                onclick="commentsSystem.deleteComment(${reply.id})">
                            <i class="feather icon-trash-2 me-1"></i>
                            حذف
                        </button>
                    </div>
                ` : ''}
            </div>
        `;
    }

    /**
     * تحديث عداد الأحرف
     */
    updateCharCount() {
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

    /**
     * عرض أخطاء النموذج
     */
    showFormErrors(errors) {
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

    /**
     * إعادة تعيين نموذج التعليق
     */
    resetCommentForm() {
        $('#commentForm')[0].reset();
        $('#commentFormErrors').addClass('d-none');
        $('#reply_to_section').addClass('d-none');
        this.currentCommentId = null;
        this.isEditingComment = false;
        this.updateCharCount();
    }

    /**
     * عرض حالة التحميل لحفظ التعليق
     */
    showSaveLoading(show = true) {
        const btn = $('#saveCommentBtn');
        const text = $('#saveCommentBtnText');
        
        if (show) {
            btn.prop('disabled', true);
            btn.html(`
                <div class="spinner-border spinner-border-sm me-1" role="status">
                    <span class="visually-hidden">جاري الحفظ...</span>
                </div>
                جاري الحفظ...
            `);
        } else {
            btn.prop('disabled', false);
            btn.html(`
                <i class="feather icon-send me-1"></i>
                <span id="saveCommentBtnText">${this.isEditingComment ? 'حفظ التعديل' : 'إرسال التعليق'}</span>
            `);
        }
    }

    /**
     * عرض حالة التحميل للتعليق
     */
    showCommentLoading(commentId, show = true) {
        const commentItem = $(`.comment-item[data-comment-id="${commentId}"], .reply-item[data-comment-id="${commentId}"]`);
        
        if (show) {
            if (!commentItem.find('.loading-overlay').length) {
                commentItem.css('position', 'relative').append(`
                    <div class="loading-overlay">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">جاري المعالجة...</span>
                        </div>
                    </div>
                `);
            }
        } else {
            commentItem.find('.loading-overlay').remove();
        }
    }

    /**
     * عرض رسالة Toast
     */
    showToast(type, message) {
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
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    /**
     * عرض رسالة خطأ
     */
    showError(message) {
        $('#commentsContainer').html(`
            <div class="alert alert-danger text-center">
                <i class="feather icon-alert-circle me-2"></i>
                ${message}
            </div>
        `);
    }

    /**
     * الحصول على اسم المستخدم للعرض
     */
    getUserDisplayName(user) {
        if (!user) return 'مستخدم';
        
        return user.display_name || user.name || 'مستخدم';
    }

    /**
     * تشفير HTML لمنع XSS
     */
    escapeHtml(text) {
        if (!text) return '';
        
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/'/g, '&#39;').replace(/"/g, '&quot;');
    }
}

// تهيئة نظام التعليقات عند تحميل الصفحة
$(document).ready(function() {
    window.commentsSystem = new CommentsSystem();
    
    // تعريف الدوال العامة للوصول إليها من HTML
    window.openCommentModal = (taskId, parentId = null, parentUserName = null) => {
        window.commentsSystem.openCommentModal(taskId, parentId, parentUserName);
    };
    
    window.replyToComment = (commentId, userName, taskId) => {
        window.commentsSystem.replyToComment(commentId, userName, taskId);
    };
    
    window.editComment = (commentId, content, taskId) => {
        window.commentsSystem.editComment(commentId, content, taskId);
    };
    
    window.deleteComment = (commentId) => {
        window.commentsSystem.deleteComment(commentId);
    };
    
    window.cancelReply = () => {
        window.commentsSystem.cancelReply();
    };
});