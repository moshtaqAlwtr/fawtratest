/**
 * ملف إدارة المهام
 */

/**
 * تحميل مهام المشروع
 */
function loadProjectTasks() {
    $.get(`/projects/api/${currentProjectId}/tasks`)
        .done(function(response) {
            if (response.success && response.data.length > 0) {
                renderTasks(response.data);
                $('#tasks-list').show();
                $('#no-tasks').hide();
            } else {
                $('#tasks-list').hide();
                $('#no-tasks').show();
            }
        })
        .fail(function() {
            console.error('خطأ في تحميل المهام');
        })
        .always(function() {
            $('#tasks-loading').hide();
        });
}

/**
 * عرض قائمة المهام
 */
function renderTasks(tasks) {
    let html = '<div class="tasks-list-improved">';

    tasks.forEach(function(task) {
        html += generateTaskCard(task);
    });

    html += '</div>';
    $('#tasks-list').html(html);

    // تفعيل tooltips
    $('[data-toggle="tooltip"]').tooltip();
}

/**
 * توليد بطاقة المهمة
 */
function generateTaskCard(task) {
    const statusConfig = getStatusConfig(task.status);
    const priorityConfig = getPriorityConfig(task.priority);

    return `
        <div class="task-card-improved" data-task-id="${task.id}">
            <!-- رأس المهمة -->
            <div class="task-header">
                <div class="task-icon-wrapper" style="background-color: ${priorityConfig.color};">
                    <i class="fas ${priorityConfig.icon} text-white"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="task-title">${escapeHtml(task.title)}</h6>
                    ${task.description ? `<p class="task-description">${escapeHtml(task.description)}</p>` : ''}
                    <div class="d-flex gap-2 mt-2" style="gap: 0.5rem;">
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

            <!-- معلومات المهمة -->
            <div class="task-info-grid">
                ${task.start_date ? `
                    <div class="task-info-item">
                        <i class="far fa-calendar text-primary"></i>
                        <span class="task-info-label">البداية:</span>
                        <span class="task-info-value">${formatDate(task.start_date)}</span>
                    </div>
                ` : ''}

                ${task.due_date ? `
                    <div class="task-info-item">
                        <i class="far fa-calendar-check text-danger"></i>
                        <span class="task-info-label">الاستحقاق:</span>
                        <span class="task-info-value">${formatDate(task.due_date)}</span>
                    </div>
                ` : ''}

                ${task.cost || task.budget ? `
                    <div class="task-info-item">
                        <i class="fas fa-money-bill-wave text-success"></i>
                        <span class="task-info-label">التكلفة:</span>
                        <span class="task-info-value">${formatMoney(task.cost || task.budget)}</span>
                    </div>
                ` : ''}
            </div>

            <!-- شريط التقدم -->
            <div class="mb-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted">نسبة الإنجاز</small>
                    <strong style="color: ${statusConfig.color};">${task.completion_percentage || 0}%</strong>
                </div>
                <div class="task-progress-bar">
                    <div class="task-progress-fill" style="width: ${task.completion_percentage || 0}%; background-color: ${statusConfig.color};"></div>
                </div>
            </div>

            <!-- الموظفين المكلفين -->
            ${renderAssignedUsers(task.assigned_users || [])}
        </div>
    `;
}

/**
 * فتح نافذة إضافة مهمة
 */
function openTaskModalForProject(projectId) {
    resetTaskForm();
    $('#task-project-id').val(projectId);
    $('#task-modal-title').text('إضافة مهمة جديدة');
    loadAvailableUsers(projectId);
    $('#task-modal').modal('show');
}

/**
 * تحميل المستخدمين المتاحين
 */
function loadAvailableUsers(projectId) {
    $.get(`/projects/api/${projectId}/users`)
        .done(function(response) {
            if (response.success && response.data) {
                let options = '';
                response.data.forEach(function(user) {
                    options += `<option value="${user.id}">${user.name} - ${user.email}</option>`;
                });
                $('#task-assigned-users').html(options);

                // تهيئة Select2
                if ($.fn.select2) {
                    $('#task-assigned-users').select2({
                        placeholder: 'اختر الموظفين',
                        dir: 'rtl'
                    });
                }
            }
        })
        .fail(function() {
            console.error('خطأ في تحميل المستخدمين');
        });
}

/**
 * حفظ المهمة - الدالة المحسّنة
 */
function saveTask() {
    const form = $('#task-form')[0];
    const formData = new FormData(form);

    // التحقق من صحة النموذج
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: 'تأكيد الحفظ',
        text: 'هل أنت متأكد من حفظ هذه المهمة؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم، احفظ',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            // مؤشر التحميل
            Swal.fire({
                title: 'جاري الحفظ...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/tasks',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم بنجاح!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#task-modal').modal('hide');

                            // إضافة المهمة للقائمة مباشرة
                            addTaskToList(response.data);

                            // تحديث الإحصائيات
                            loadProjectStats();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: response.message || 'حدث خطأ أثناء الحفظ'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'حدث خطأ أثناء حفظ المهمة';

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في الحفظ',
                        text: errorMessage
                    });
                }
            });
        }
    });
}

/**
 * إضافة مهمة للقائمة مباشرة
 */
function addTaskToList(task) {
    // إخفاء رسالة "لا توجد مهام"
    $('#no-tasks').hide();
    $('#tasks-list').show();

    const taskHtml = generateTaskCard(task);

    // إنشاء القائمة إذا لم تكن موجودة
    if ($('#tasks-list .tasks-list-improved').length === 0) {
        $('#tasks-list').html('<div class="tasks-list-improved"></div>');
    }

    // إضافة المهمة في بداية القائمة
    $('#tasks-list .tasks-list-improved').prepend(taskHtml);

    // تأثير بصري
    $(`[data-task-id="${task.id}"]`).hide().fadeIn(600);

    // تفعيل tooltips
    $('[data-toggle="tooltip"]').tooltip();
}

/**
 * إعادة تعيين نموذج المهمة
 */
function resetTaskForm() {
    $('#task-form')[0].reset();
    $('#task-id').val('');
    $('#completion-value').text('0');

    if ($.fn.select2) {
        $('#task-assigned-users').val(null).trigger('change');
    }
}

/**
 * عرض الموظفين المكلفين
 */
function renderAssignedUsers(assignedUsers) {
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
    let html = '<div class="assigned-users-compact">';
    html += '<small class="text-muted me-2" style="margin-right: 0.5rem;">المكلفين:</small>';

    const displayCount = Math.min(assignedUsers.length, 5);

    for (let i = 0; i < displayCount; i++) {
        const user = assignedUsers[i];
        const color = colors[i % colors.length];
        const initial = user.name ? user.name.charAt(0).toUpperCase() : 'U';

        html += `
            <div class="user-avatar-compact"
                 style="background-color: ${color};"
                 data-toggle="tooltip"
                 title="${escapeHtml(user.name || 'مستخدم')} - ${escapeHtml(user.email || '')}">
                ${initial}
            </div>
        `;
    }

    if (assignedUsers.length > 5) {
        const remaining = assignedUsers.length - 5;
        html += `
            <div class="user-avatar-compact"
                 style="background-color: #6c757d;"
                 data-toggle="tooltip"
                 title="+${remaining} آخرين">
                +${remaining}
            </div>
        `;
    }

    html += '</div>';
    return html;
}
