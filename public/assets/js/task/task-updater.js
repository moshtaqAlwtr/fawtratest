/**
 * نظام التحديث الفوري للمهام
 */

class TaskUpdater {
    static updateStatus(taskId, newStatus, card) {
        const oldStatus = card.closest('.task-column').data('status');

        if (oldStatus === newStatus) return;

        card.addClass('updating');
        card.find('select, input, button').prop('disabled', true);

        const completionPercentage = DragDropManager.getCompletionByStatus(newStatus);

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
                    DragDropManager.updateCardData(card, response.task);
                    DragDropManager.moveCardToColumn(card, newStatus, oldStatus);
                    Utils.showToast('success', 'تم تحديث الحالة بنجاح');
                }
            },
            error: (xhr) => {
                Utils.showToast('error', 'فشل في تحديث الحالة');
                card.find('.task-status-select').val(oldStatus);
            },
            complete: () => {
                card.removeClass('updating');
                card.find('select, input, button').prop('disabled', false);
            }
        });
    }

    static updateProgress(taskId, percentage, card) {
        if (percentage < 0 || percentage > 100) {
            Utils.showToast('error', 'النسبة يجب أن تكون بين 0 و 100');
            return;
        }

        card.addClass('updating');

        $.ajax({
            url: `/tasks/${taskId}/update-progress`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                completion_percentage: percentage
            },
            success: (response) => {
                if (response.success) {
                    TaskCard.updateProgress(card[0], percentage);

                    if (response.task.status !== card.closest('.task-column').data('status')) {
                        const oldStatus = card.closest('.task-column').data('status');
                        card.find('.task-status-select').val(response.task.status);
                        DragDropManager.moveCardToColumn(card, response.task.status, oldStatus);
                    }

                    Utils.showToast('success', 'تم تحديث نسبة الإنجاز');
                }
            },
            error: () => {
                Utils.showToast('error', 'فشل في التحديث');
            },
            complete: () => {
                card.removeClass('updating');
            }
        });
    }

    static checkOverdueTasks() {
        const today = new Date().toISOString().split('T')[0];

        $('.task-card').each(function() {
            const card = $(this);
            const dueDateElement = card.find('.task-due-date span');

            if (dueDateElement.length === 0) return;

            const dueDate = dueDateElement.text();
            const status = card.closest('.task-column').data('status');

            if (dueDate && dueDate < today && status !== 'completed') {
                const taskId = card.data('task-id');
                card.find('.task-status-select').val('overdue');
                TaskUpdater.updateStatus(taskId, 'overdue', card);
            }
        });
    }
}