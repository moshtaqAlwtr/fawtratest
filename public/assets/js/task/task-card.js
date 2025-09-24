/**
 * مكون بطاقة المهمة
 */

class TaskCard {
    static create(task) {
        const priorityClass = `priority-${task.priority}`;
        const progressPercent = task.completion_percentage || 0;
        const progressColor = this.getProgressColor(progressPercent);

        const radius = 25;
        const circumference = 2 * 3.14159 * radius;
        const offset = circumference - (progressPercent / 100) * circumference;

        const assignees = this.renderAssignees(task.assigned_users);
        const subTasksInfo = this.renderSubTasksInfo(task);

        return `
            <div class="card task-card ${priorityClass}" data-task-id="${task.id}" draggable="true">
                <div class="card-body">
                    <!-- رأس البطاقة -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="card-title mb-0 flex-1">${task.title}</h6>
                        <select class="form-control form-control-sm task-status-select ml-2"
                                data-task-id="${task.id}">
                            <option value="not_started" ${task.status == 'not_started' ? 'selected' : ''}>لم تبدأ</option>
                            <option value="in_progress" ${task.status == 'in_progress' ? 'selected' : ''}>قيد التنفيذ</option>
                            <option value="completed" ${task.status == 'completed' ? 'selected' : ''}>مكتملة</option>
                            <option value="overdue" ${task.status == 'overdue' ? 'selected' : ''}>متأخرة</option>
                        </select>
                    </div>

                    <!-- معلومات المشروع -->
                    <p class="card-text task-meta mb-3">
                        <i class="feather icon-folder"></i> ${task.project?.title || 'N/A'}
                    </p>

                    <!-- دائرة التقدم -->
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <div class="circular-progress">
                            <svg width="60" height="60" style="transform: rotate(-90deg);">
                                <circle cx="30" cy="30" r="25" fill="none" stroke="#e9ecef" stroke-width="6"/>
                                <circle cx="30" cy="30" r="25" fill="none" stroke="${progressColor}"
                                        stroke-width="6" stroke-dasharray="${circumference}"
                                        stroke-dashoffset="${offset}" stroke-linecap="round"
                                        class="progress-circle"/>
                            </svg>
                            <div class="progress-value">
                                <input type="number" class="progress-input" value="${progressPercent}"
                                       min="0" max="100" data-task-id="${task.id}"
                                       style="color: ${progressColor};">
                                <span style="color: ${progressColor};">%</span>
                            </div>
                        </div>
                    </div>

                    <!-- تاريخ الانتهاء -->
                    ${task.due_date ? `
                        <div class="task-due-date mb-3">
                            <i class="feather icon-calendar"></i>
                            <span>${Utils.formatDate(task.due_date)}</span>
                            ${this.renderOverdueBadge(task)}
                        </div>
                    ` : ''}

                    <!-- المهام الفرعية -->
                    ${subTasksInfo}

                    <!-- المستخدمون والأزرار -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="task-assignees">${assignees}</div>
                        <div class="task-actions">
                            <button class="btn btn-sm btn-info" onclick="showTaskDetails(${task.id})"
                                    title="عرض">
                                <i class="feather icon-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="openTaskModal(${task.id})"
                                    title="تعديل">
                                <i class="feather icon-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteTask(${task.id})"
                                    title="حذف">
                                <i class="feather icon-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    static renderAssignees(assignedUsers) {
        if (!assignedUsers || assignedUsers.length === 0) {
            return '<span class="text-muted small">لا يوجد</span>';
        }

        let html = '';
        const visibleUsers = assignedUsers.slice(0, 3);

        visibleUsers.forEach(user => {
            html += `
                <img src="${user.avatar || '/default-avatar.png'}"
                     title="${user.name}"
                     alt="${user.name}"
                     class="user-avatar">
            `;
        });

        if (assignedUsers.length > 3) {
            html += `
                <div class="extra-users-count">
                    +${assignedUsers.length - 3}
                </div>
            `;
        }

        return html;
    }

    static renderSubTasksInfo(task) {
        if (!task.sub_tasks || task.sub_tasks.length === 0) {
            return '';
        }

        const completed = task.sub_tasks.filter(st => st.status === 'completed').length;
        const total = task.sub_tasks.length;
        const percentage = Math.round((completed / total) * 100);

        return `
            <div class="sub-tasks-info mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted">
                        <i class="feather icon-list"></i> المهام الفرعية
                    </small>
                    <small class="text-muted">${completed}/${total}</small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-success" style="width: ${percentage}%"></div>
                </div>
            </div>
        `;
    }

    static renderOverdueBadge(task) {
        const today = new Date().toISOString().split('T')[0];
        if (task.due_date < today && task.status !== 'completed') {
            return '<span class="badge badge-danger badge-sm mr-1">متأخرة</span>';
        }
        return '';
    }

    static getProgressColor(percentage) {
        if (percentage >= 70) return '#28c76f';
        if (percentage >= 30) return '#ff9f43';
        return '#ea5455';
    }

    static updateProgress(cardElement, percentage) {
        const progressCircle = cardElement.querySelector('.progress-circle');
        const progressInput = cardElement.querySelector('.progress-input');

        if (!progressCircle || !progressInput) return;

        const radius = 25;
        const circumference = 2 * Math.PI * radius;
        const offset = circumference - (percentage / 100) * circumference;
        const color = this.getProgressColor(percentage);

        progressCircle.style.strokeDashoffset = offset;
        progressCircle.style.stroke = color;
        progressInput.style.color = color;

        const percentSpan = progressInput.nextElementSibling;
        if (percentSpan && percentSpan.tagName === 'SPAN') {
            percentSpan.style.color = color;
        }
    }
}

// حذف المهمة
function deleteTask(taskId) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "لن تتمكن من التراجع عن هذا!",
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
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    if (response.success) {
                        const card = $(`.task-card[data-task-id="${taskId}"]`);
                        const status = card.closest('.task-column').data('status');

                        card.fadeOut(300, function() {
                            $(this).remove();
                            window.taskManager.updateTaskCounts();
                        });

                        Utils.showToast('success', response.message);
                    }
                },
                error: () => {
                    Utils.showToast('error', 'حدث خطأ أثناء الحذف');
                }
            });
        }
    });
}