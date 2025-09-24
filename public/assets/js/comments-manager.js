/**
 * ملف إدارة التعليقات
 */

/**
 * تحميل التعليقات الأخيرة
 */
function loadRecentComments(page = 1) {
    $('#recent-comments').html(`
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="sr-only">جاري التحميل...</span>
            </div>
        </div>
    `);

    $.ajax({
        url: `/comments/paginated`,
        method: 'GET',
        data: {
            type: 'project',
            id: currentProjectId,
            page: page,
            per_page: 5
        },
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                renderRecentComments(response.data);
            } else {
                $('#recent-comments').html(
                    '<p class="text-muted text-center small">لا توجد تعليقات</p>'
                );
            }
        },
        error: function(xhr) {
            console.error('خطأ في تحميل التعليقات:', xhr);
            $('#recent-comments').html(
                '<p class="text-danger text-center small">خطأ في تحميل التعليقات</p>'
            );
        }
    });
}

/**
 * عرض التعليقات الأخيرة
 */
function renderRecentComments(comments) {
    let html = '';

    comments.forEach(function(comment) {
        const userName = comment.user ?
            (comment.user.display_name || comment.user.name ||
                `${comment.user.first_name || ''} ${comment.user.last_name || ''}`.trim() ||
                'مستخدم') :
            'مستخدم';

        const userInitial = userName.charAt(0).toUpperCase();
        const commentDate = formatCommentDate(comment.created_at);

        html += `
            <div class="comment-item mb-3 pb-2 border-bottom" data-comment-id="${comment.id}">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-2" style="margin-right: 0.5rem;">
                        <div class="avatar-sm">
                            <div class="avatar-title rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                                ${userInitial}
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 text-primary" style="font-size: 13px;">${userName}</h6>
                            <small class="text-muted" style="font-size: 11px;">${commentDate}</small>
                        </div>
                        <p class="text-muted mb-1" style="font-size: 12px; line-height: 1.4;">${escapeHtml(comment.content)}</p>
                        ${comment.replies_count > 0 ?
                            `<small class="text-info" style="font-size: 11px;">
                                <i class="feather icon-message-circle" style="font-size: 10px;"></i>
                                ${comment.replies_count} رد
                            </small>` : ''
                        }
                    </div>
                </div>
            </div>
        `;
    });

    $('#recent-comments').html(html);
}

/**
 * إضافة تعليق جديد
 */
function addComment() {
    const content = $('#comment-form textarea[name="content"]').val();

    if (!content.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه',
            text: 'يرجى كتابة التعليق',
            confirmButtonText: 'حسناً'
        });
        return;
    }

    Swal.fire({
        title: 'جاري إضافة التعليق...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/comments',
        method: 'POST',
        data: {
            content: content,
            commentable_type: 'App\\Models\\Project',
            commentable_id: currentProjectId
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح!',
                text: 'تم إضافة التعليق بنجاح',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                $('#add-comment-modal').modal('hide');
                $('#comment-form')[0].reset();
                loadRecentComments();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: response.message || 'حدث خطأ في إضافة التعليق'
            });
        }
    })
    .fail(function(xhr) {
        let errorMessage = 'حدث خطأ في إضافة التعليق';

        if (xhr.status === 422 && xhr.responseJSON?.errors) {
            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
        } else if (xhr.responseJSON?.message) {
            errorMessage = xhr.responseJSON.message;
        }

        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: errorMessage
        });
    });
}

/**
 * تنسيق تاريخ التعليق
 */
function formatCommentDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / (1000 * 60));

    if (diffInMinutes < 1) return 'الآن';
    if (diffInMinutes < 60) return `منذ ${diffInMinutes} د`;
    if (diffInMinutes < 1440) return `منذ ${Math.floor(diffInMinutes / 60)} س`;
    if (diffInMinutes < 43200) return `منذ ${Math.floor(diffInMinutes / 1440)} يوم`;

    return date.toLocaleDateString('ar-SA', {
        month: 'short',
        day: 'numeric'
    });
}
