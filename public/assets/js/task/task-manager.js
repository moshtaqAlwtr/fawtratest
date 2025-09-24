/**
 * ملف إدارة المهام الرئيسي
 * يحتوي على التهيئة الأساسية وتنسيق الملفات
 */

class TaskManager {
    constructor() {
        this.currentProjectId = null;
        this.currentFilters = {};
        this.init();
    }

    init() {
        this.initializeSelect2();
        this.initializeEventListeners();
        this.initializeDragAndDrop();
        this.loadTasks();
        this.startAutoRefresh();
    }

    initializeSelect2() {
        $('.select2').select2({
            dir: 'rtl',
            language: {
                noResults: () => "لا توجد نتائج",
                searching: () => "جاري البحث..."
            }
        });

        $('#taskModal').on('shown.bs.modal', () => {
            $('.select2').select2({
                dropdownParent: $('#taskModal'),
                dir: 'rtl'
            });
        });
    }

    initializeEventListeners() {
        // فلتر المشروع
        $('#filterProject').on('change', (e) => {
            this.currentProjectId = e.target.value;
            this.filterTasks();
        });

        // باقي الفلاتر
        $('#filterStatus, #filterPriority').on('change', () => {
            this.filterTasks();
        });

        // زر البحث
        $('#btnFilterTasks').on('click', () => {
            this.filterTasks();
        });

        // فتح modal الإضافة
        $('#btnAddTask').on('click', () => {
            TaskModal.open();
        });

        // حفظ المهمة
        $('#taskForm').on('submit', (e) => {
            e.preventDefault();
            TaskModal.save();
        });

        // تحديث الحالة الفورية
        $(document).on('change', '.task-status-select', (e) => {
            const taskId = $(e.target).data('task-id');
            const newStatus = $(e.target).val();
            const card = $(e.target).closest('.task-card');
            TaskUpdater.updateStatus(taskId, newStatus, card);
        });

        // تحديث نسبة الإنجاز
        $(document).on('change', '.progress-input', (e) => {
            const taskId = $(e.target).data('task-id');
            const percentage = parseInt($(e.target).val());
            const card = $(e.target).closest('.task-card');
            TaskUpdater.updateProgress(taskId, percentage, card);
        });

        // تحديث المهام عند تغيير المشروع في المودل
        $('#project_id').on('change', (e) => {
            const projectId = e.target.value;
            if (projectId) {
                this.loadParentTasks(projectId);
            } else {
                $('#parent_task_id').html('<option value="">لا يوجد (مهمة رئيسية)</option>');
            }
        });
    }

    initializeDragAndDrop() {
        DragDropManager.init();
    }

    filterTasks() {
        const filters = {
            project_id: $('#filterProject').val(),
            status: $('#filterStatus').val(),
            priority: $('#filterPriority').val()
        };

        this.currentFilters = filters;
        this.loadTasks(filters);
    }

    loadTasks(filters = {}) {
        $.ajax({
            url: '/tasks/get',
            type: 'GET',
            data: filters,
            beforeSend: () => {
                $('.task-column').addClass('loading');
            },
            success: (response) => {
                if (response.success) {
                    this.renderTasks(response.tasks);
                }
            },

            complete: () => {
                $('.task-column').removeClass('loading');
            }
        });
    }

    renderTasks(tasks) {
        $('.task-column').html('');

        tasks.forEach(task => {
            const column = $(`.task-column[data-status="${task.status}"]`);
            const cardHtml = TaskCard.create(task);
            column.append(cardHtml);
        });

        this.updateTaskCounts();
    }

    updateTaskCounts() {
        $('.task-column').each(function() {
            const status = $(this).data('status');
            const count = $(this).find('.task-card').length;
            $(`.task-count-${status}`).text(count);
        });
    }

    loadParentTasks(projectId) {
        $.ajax({
            url: `/tasks/by-project/${projectId}`,
            type: 'GET',
            success: (response) => {
                if (response.success) {
                    let options = '<option value="">لا يوجد (مهمة رئيسية)</option>';
                    response.tasks.forEach(task => {
                        options += `<option value="${task.id}">${task.title}</option>`;
                    });
                    $('#parent_task_id').html(options);
                }
            }
        });
    }

    startAutoRefresh() {
        setInterval(() => {
            TaskUpdater.checkOverdueTasks();
        }, 30000);
    }
}

// تهيئة عند تحميل الصفحة
$(document).ready(() => {
    window.taskManager = new TaskManager();
});
