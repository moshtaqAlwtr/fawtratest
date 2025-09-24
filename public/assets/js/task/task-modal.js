/**
 * إدارة النافذة المنبثقة للمهام
 */

class TaskModal {
    static open(taskId = null) {
        $('#formErrors').slideUp();
        $('#taskForm')[0].reset();
        $('.select2').val(null).trigger('change');

        if (taskId) {
            this.loadTaskData(taskId);
        } else {
            $('#taskModalTitle').text('إضافة مهمة جديدة');
            $('#saveTaskBtn').html('<i class="feather icon-save"></i> حفظ المهمة');
            $('#task_id').val('');
        }

        $('#taskModal').modal('show');
    }

    static loadTaskData(taskId) {
        $('#taskModalTitle').text('تعديل المهمة');
        $('#saveTaskBtn').html('<i class="feather icon-save"></i> تحديث المهمة');

        $('#taskModal .modal-body').append(
            '<div class="loading-overlay"><div class="spinner-border text-primary"></div></div>'
        );

        $.get(`/tasks/${taskId}`, (response) => {
            if (response.success) {
                this.fillForm(response.task);
            }
        }).always(() => {
            $('#taskModal .loading-overlay').remove();
        });
    }

    static fillForm(task) {
        $('#task_id').val(task.id);
        $('#project_id').val(task.project_id).trigger('change');

        // تحميل المهام الرئيسية للمشروع
        if (task.project_id) {
            setTimeout(() => {
                $('#parent_task_id').val(task.parent_task_id).trigger('change');
            }, 500);
        }

        $('#title').val(task.title);
        $('#description').val(task.description);
        $('#status').val(task.status).trigger('change');
        $('#priority').val(task.priority);
        $('#start_date').val(task.start_date);
        $('#due_date').val(task.due_date);
        $('#budget').val(task.budget);
        $('#estimated_hours').val(task.estimated_hours);
        $('#completion_percentage').val(task.completion_percentage);

        // تعيين المستخدمين
        if (task.assigned_users && task.assigned_users.length > 0) {
            const assignedIds = task.assigned_users.map(u => u.id);
            $('#assigned_users').val(assignedIds).trigger('change');
        }
    }

    static save() {
        const formData = new FormData($('#taskForm')[0]);
        const taskId = $('#task_id').val();

        $('#saveTaskBtn').prop('disabled', true)
            .html('<i class="feather icon-loader"></i> جاري الحفظ...');

        $.ajax({
            url: '/tasks/store',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    $('#taskModal').modal('hide');

                    if (taskId) {
                        this.updateExistingCard(response.task);
                    } else {
                        this.addNewCard(response.task);
                    }

                    window.taskManager.updateTaskCounts();
                    Utils.showToast('success', response.message);
                }
            },
            error: (xhr) => {
                if (xhr.status === 422) {
                    this.showErrors(xhr.responseJSON.errors);
                } else {
                    Utils.showToast('error', xhr.responseJSON?.message || 'حدث خطأ أثناء الحفظ');
                }
            },
            complete: () => {
                $('#saveTaskBtn').prop('disabled', false)
                    .html('<i class="feather icon-save"></i> حفظ');
            }
        });
    }

    static showErrors(errors) {
        let errorHtml = '<ul class="mb-0">';
        $.each(errors, (key, value) => {
            errorHtml += `<li>${value[0]}</li>`;
        });
        errorHtml += '</ul>';
        $('#formErrors').html(errorHtml).slideDown();
    }

    static updateExistingCard(task) {
        const card = $(`.task-card[data-task-id="${task.id}"]`);
        const oldStatus = card.closest('.task-column').data('status');
        const newStatus = task.status;

        if (oldStatus !== newStatus) {
            DragDropManager.moveCardToColumn(card, newStatus, oldStatus);
        }

        const newCardHtml = TaskCard.create(task);
        card.replaceWith(newCardHtml);
    }

    static addNewCard(task) {
        const column = $(`.task-column[data-status="${task.status}"]`);
        const cardHtml = TaskCard.create(task);
        const newCard = $(cardHtml).hide();

        column.prepend(newCard);
        newCard.fadeIn(400);

        window.taskManager.updateTaskCounts();
    }
}

// دالة عامة لفتح المودل
function openTaskModal(taskId = null) {
    TaskModal.open(taskId);
}

// عرض تفاصيل المهمة
function showTaskDetails(taskId) {
    $('#taskDetailsContent').html(
        '<div class="text-center"><div class="spinner-border text-primary"></div></div>'
    );
    $('#taskDetailsModal').modal('show');

    $.get(`/tasks/${taskId}`, (response) => {
        if (response.success) {
            const html = TaskDetailsView.render(response.task);
            $('#taskDetailsContent').html(html);
        }
    });
}

// عرض تفاصيل المهمة
class TaskDetailsView {
    static render(task) {
        const assignees = task.assigned_users
            ? task.assigned_users.map(u => `<span class="badge badge-primary mr-1">${u.name}</span>`).join('')
            : '<span class="text-muted">لا يوجد</span>';

        const progressPercent = task.completion_percentage || 0;
        const progressColor = this.getProgressColor(progressPercent);

        return `
            <div class="task-details">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 class="mb-0">${task.title}</h4>
                    <span class="badge badge-${this.getPriorityBadgeClass(task.priority)} badge-lg">
                        ${Utils.getPriorityName(task.priority)}
                    </span>
                </div>
                <hr>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <i class="feather icon-folder text-primary"></i>
                            <strong>المشروع:</strong> ${task.project?.title || 'N/A'}
                        </div>
                        <div class="detail-item">
                            <i class="feather icon-info text-info"></i>
                            <strong>الحالة:</strong>
                            <span class="badge badge-info">${Utils.getStatusName(task.status)}</span>
                        </div>
                        <div class="detail-item">
                            <i class="feather icon-flag text-warning"></i>
                            <strong>الأولوية:</strong>
                            <span class="badge badge-${this.getPriorityBadgeClass(task.priority)}">
                                ${Utils.getPriorityName(task.priority)}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <i class="feather icon-calendar text-success"></i>
                            <strong>تاريخ البدء:</strong> ${task.start_date || 'غير محدد'}
                        </div>
                        <div class="detail-item">
                            <i class="feather icon-calendar text-danger"></i>
                            <strong>تاريخ الانتهاء:</strong> ${task.due_date || 'غير محدد'}
                        </div>
                        <div class="detail-item">
                            <i class="feather icon-user text-secondary"></i>
                            <strong>منشئ المهمة:</strong> ${task.creator?.name || 'N/A'}
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <i class="feather icon-dollar-sign text-success"></i>
                            <strong>الميزانية:</strong> ${task.budget || 'غير محدد'}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <i class="feather icon-clock text-primary"></i>
                            <strong>الساعات المقدرة:</strong> ${task.estimated_hours || 'غير محدد'}
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <strong class="d-block mb-2">
                        <i class="feather icon-users text-info"></i> المستخدمون المعينون:
                    </strong>
                    <div>${assignees}</div>
                </div>

                <div class="mb-4">
                    <strong class="d-block mb-2">
                        <i class="feather icon-trending-up"></i> نسبة الإنجاز:
                    </strong>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar ${progressColor}" role="progressbar"
                             style="width: ${progressPercent}%"
                             aria-valuenow="${progressPercent}"
                             aria-valuemin="0" aria-valuemax="100">
                            ${progressPercent}%
                        </div>
                    </div>
                </div>

                ${task.description ? `
                    <div class="mb-3">
                        <strong class="d-block mb-2">
                            <i class="feather icon-file-text"></i> الوصف:
                        </strong>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0">${task.description}</p>
                            </div>
                        </div>
                    </div>
                ` : ''}

                ${task.sub_tasks && task.sub_tasks.length > 0 ? `
                    <div class="mb-3">
                        <strong class="d-block mb-2">
                            <i class="feather icon-list"></i> المهام الفرعية (${task.sub_tasks.length}):
                        </strong>
                        <ul class="list-group">
                            ${task.sub_tasks.map(subTask => `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    ${subTask.title}
                                    <span class="badge badge-${subTask.status === 'completed' ? 'success' : 'secondary'}">
                                        ${Utils.getStatusName(subTask.status)}
                                    </span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                ` : ''}
            </div>
        `;
    }

    static getProgressColor(percentage) {
        if (percentage >= 70) return 'bg-success';
        if (percentage >= 30) return 'bg-warning';
        return 'bg-danger';
    }

    static getPriorityBadgeClass(priority) {
        const classes = {
            'low': 'success',
            'medium': 'warning',
            'high': 'danger',
            'urgent': 'dark'
        };
        return classes[priority] || 'secondary';
    }
}