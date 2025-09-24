/**
 * نظام السحب والإفلات المحسّن
 */

class DragDropManager {
    static draggedTask = null;
    static originalColumn = null;

    static init() {
        this.attachEventListeners();
    }

    static attachEventListeners() {
        // بداية السحب
        $(document).on('dragstart', '.task-card', (e) => {
            this.onDragStart(e);
        });

        // انتهاء السحب
        $(document).on('dragend', '.task-card', (e) => {
            this.onDragEnd(e);
        });

        // التمرير فوق العمود
        $(document).on('dragover', '.task-column', (e) => {
            this.onDragOver(e);
        });

        // مغادرة العمود
        $(document).on('dragleave', '.task-column', (e) => {
            this.onDragLeave(e);
        });

        // إسقاط العنصر
        $(document).on('drop', '.task-column', (e) => {
            this.onDrop(e);
        });
    }

    static onDragStart(e) {
        this.draggedTask = $(e.currentTarget);
        this.originalColumn = this.draggedTask.closest('.task-column');

        this.draggedTask.addClass('dragging');
        e.originalEvent.dataTransfer.effectAllowed = 'move';
        e.originalEvent.dataTransfer.setData('taskId', this.draggedTask.data('task-id'));
        this.draggedTask.data('original-status', this.originalColumn.data('status'));
    }

    static onDragEnd(e) {
        $(e.currentTarget).removeClass('dragging');
        $('.task-column').removeClass('drag-over');
        this.draggedTask = null;
        this.originalColumn = null;
    }

    static onDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        $(e.currentTarget).addClass('drag-over');
        e.originalEvent.dataTransfer.dropEffect = 'move';
    }

    static onDragLeave(e) {
        const rect = e.currentTarget.getBoundingClientRect();
        const x = e.originalEvent.clientX;
        const y = e.originalEvent.clientY;

        if (x < rect.left || x > rect.right || y < rect.top || y > rect.bottom) {
            $(e.currentTarget).removeClass('drag-over');
        }
    }

    static onDrop(e) {
        e.preventDefault();
        e.stopPropagation();

        const targetColumn = $(e.currentTarget);
        targetColumn.removeClass('drag-over');

        if (!this.draggedTask) return;

        const taskId = this.draggedTask.data('task-id');
        const newStatus = targetColumn.data('status');
        const oldStatus = this.originalColumn.data('status');

        if (oldStatus === newStatus) return;

        this.performMove(taskId, newStatus, oldStatus, this.draggedTask, targetColumn);
    }

    static performMove(taskId, newStatus, oldStatus, taskCard, targetColumn) {
        // تطبيق التحديث المرئي فوراً
        taskCard.addClass('updating');
        taskCard.detach().appendTo(targetColumn);
        taskCard.find('.task-status-select').val(newStatus);

        // تحديث نسبة الإنجاز
        const completionPercentage = this.getCompletionByStatus(newStatus);
        taskCard.find('.progress-input').val(completionPercentage);
        TaskCard.updateProgress(taskCard[0], completionPercentage);

        // تحديث العدادات
        this.updateColumnCount(oldStatus);
        this.updateColumnCount(newStatus);

        // إرسال التحديث للخادم
        $.ajax({
            url: `/tasks/${taskId}/update-status`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                status: newStatus,
                completion_percentage: completionPercentage
            },
            success: (response) => {
                if (response.success) {
                    this.updateCardData(taskCard, response.task);
                    Utils.showToast('success', 'تم نقل المهمة بنجاح');
                    taskCard.addClass('just-moved');
                    setTimeout(() => taskCard.removeClass('just-moved'), 1000);
                } else {
                    this.revertMove(taskCard, oldStatus, newStatus);
                    Utils.showToast('error', response.message || 'فشل في تحديث المهمة');
                }
            },
            error: (xhr) => {
                this.revertMove(taskCard, oldStatus, newStatus);
                let errorMsg = 'فشل في نقل المهمة';

                if (xhr.status === 0) errorMsg = 'تحقق من الاتصال بالإنترنت';
                else if (xhr.status === 403) errorMsg = 'ليس لديك صلاحية';
                else if (xhr.status === 404) errorMsg = 'المهمة غير موجودة';
                else if (xhr.status >= 500) errorMsg = 'خطأ في الخادم';

                Utils.showToast('error', errorMsg);
            },
            complete: () => {
                taskCard.removeClass('updating');
            }
        });
    }

    static moveCardToColumn(card, newStatus, oldStatus) {
        const targetColumn = $(`.task-column[data-status="${newStatus}"]`);

        card.fadeOut(250, function() {
            card.detach().appendTo(targetColumn);
            card.fadeIn(250);
        });

        this.updateColumnCount(oldStatus);
        this.updateColumnCount(newStatus);
    }

    static updateColumnCount(status) {
        const column = $(`.task-column[data-status="${status}"]`);
        const count = column.find('.task-card').length;
        const badge = $(`.task-count-${status}`);

        badge.fadeOut(150, function() {
            $(this).text(count).fadeIn(150);
        });
    }

    static revertMove(taskCard, oldStatus, newStatus) {
        const originalColumn = $(`.task-column[data-status="${oldStatus}"]`);
        taskCard.detach().appendTo(originalColumn);
        taskCard.find('.task-status-select').val(oldStatus);

        const originalCompletion = this.getCompletionByStatus(oldStatus);
        taskCard.find('.progress-input').val(originalCompletion);
        TaskCard.updateProgress(taskCard[0], originalCompletion);

        this.updateColumnCount(oldStatus);
        this.updateColumnCount(newStatus);

        taskCard.addClass('move-failed');
        setTimeout(() => taskCard.removeClass('move-failed'), 1500);
    }

    static updateCardData(card, taskData) {
        TaskCard.updateProgress(card[0], taskData.completion_percentage);
        card.find('.task-status-select').val(taskData.status);
        card.find('.progress-input').val(taskData.completion_percentage);

        card.removeClass('priority-low priority-medium priority-high priority-urgent')
            .addClass(`priority-${taskData.priority}`);
    }

    static getCompletionByStatus(status) {
        const map = {
            'not_started': 0,
            'in_progress': 50,
            'completed': 100,
            'overdue': 25
        };
        return map[status] || 0;
    }
}