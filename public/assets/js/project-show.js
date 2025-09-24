// assets/js/project-show.js
// إدارة صفحة عرض تفاصيل المشروع

class ProjectDetailsManager {
    constructor(projectId) {
        this.projectId = projectId;
        this.loadingStates = {
            details: false,
            stats: false,
            tasks: false,
            team: false,
            comments: false
        };
        this.init();
    }

    init() {
        this.setupEventHandlers();
        this.loadAllData();
        this.setupProgressSlider();
    }

    setupEventHandlers() {
        // زر تحديث البيانات
        $('.refresh-data-btn').on('click', () => this.refreshAllData());

        // إعداد الـ modals
        $('#add-member-modal').on('show.bs.modal', () => this.loadAvailableUsers());
        $('#add-task-modal').on('show.bs.modal', () => this.setupTaskModal());

        // إزالة رسائل الخطأ عند إغلاق الـ modals
        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('.was-validated').removeClass('was-validated');
            $(this).find('.is-invalid').removeClass('is-invalid');
        });
    }

    setupProgressSlider() {
        $('#progress-slider').on('input', function() {
            const value = $(this).val();
            $('#progress-value').text(value + '%');
            $('#current-progress-bar').css('width', value + '%');
        });
    }

    async loadAllData() {
        // تحميل جميع البيانات بالتوازي
        const promises = [
            this.loadProjectDetails(),
            this.loadProjectStats(),
            this.loadProjectTasks(),
            this.loadTeamMembers(),
            this.loadRecentComments()
        ];

        try {
            await Promise.allSettled(promises);
        } catch (error) {
            console.error('Error loading data:', error);
        }
    }

    async loadProjectDetails() {
        if (this.loadingStates.details) return;

        this.loadingStates.details = true;
        this.showLoading('project-loading');
        this.hideError('project-error');

        try {
            const response = await $.get(`/projects/api/${this.projectId}/details`);

            if (response.success) {
                this.renderProjectDetails(response.data);
                this.hideLoading('project-loading');
            } else {
                throw new Error(response.message || 'فشل في تحميل تفاصيل المشروع');
            }
        } catch (error) {
            this.handleError('project', error.responseJSON?.message || error.message || 'حدث خطأ في تحميل تفاصيل المشروع');
        } finally {
            this.loadingStates.details = false;
        }
    }

    renderProjectDetails(project) {
        $('#project-title').text(project.title);

        const statusBadge = this.getStatusBadge(project.status);
        const priorityBadge = this.getPriorityBadge(project.priority);

        const html = `
            <div class="row">
                <div class="col-md-6">
                    <h5>${project.title}</h5>
                    <p class="text-muted">${project.description || 'لا يوجد وصف'}</p>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>مساحة العمل:</strong></td>
                            <td>${project.workspace?.title || '--'}</td>
                        </tr>
                        <tr>
                            <td><strong>الحالة:</strong></td>
                            <td>${statusBadge}</td>
                        </tr>
                        <tr>
                            <td><strong>الأولوية:</strong></td>
                            <td>${priorityBadge}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ البداية:</strong></td>
                            <td>${this.formatDate(project.start_date)}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ النهاية:</strong></td>
                            <td>${this.formatDate(project.end_date)}</td>
                        </tr>
                        <tr>
                            <td><strong>المنشئ:</strong></td>
                            <td>${project.creator?.first_name || ''} ${project.creator?.last_name || ''}</td>
                        </tr>
                    </table>
                </div>
            </div>
        `;

        $('#project-details').html(html);
        this.updateProgressSlider(project.progress_percentage || 0);
    }

    async loadProjectStats() {
        if (this.loadingStates.stats) return;

        this.loadingStates.stats = true;
        this.showStatsLoading();

        try {
            const response = await $.get(`/projects/api/${this.projectId}/stats`);

            if (response.success) {
                this.renderStats(response.data);
            } else {
                throw new Error(response.message || 'فشل في تحميل إحصائيات المشروع');
            }
        } catch (error) {
            this.showError('فشل في تحميل الإحصائيات');
            console.error('Stats error:', error);
        } finally {
            this.loadingStates.stats = false;
        }
    }

    showStatsLoading() {
        $('#stats-total-tasks').html('<span class="loading-spinner"></span>');
        $('#stats-completed-tasks').html('<span class="loading-spinner"></span>');
        $('#stats-budget').html('<span class="loading-spinner"></span>');
        $('#stats-progress').html('<span class="loading-spinner"></span>');
    }

    renderStats(stats) {
        $('#stats-total-tasks').text(stats.tasks?.total || 0);
        $('#stats-completed-tasks').text(stats.tasks?.completed || 0);
        $('#stats-budget').text(this.formatMoney(stats.budget?.total || 0));
        $('#stats-progress').text((stats.progress || 0) + '%');

        this.updateStatsColors(stats);
    }

    updateStatsColors(stats) {
        // تحديث ألوان الإحصائيات بناءً على الأداء
        const completionRate = stats.tasks?.completion_rate || 0;
        const budgetUsage = stats.budget?.usage_percentage || 0;

        // ألوان المهام المكتملة
        const completedElement = $('#stats-completed-tasks');
        completedElement.removeClass('warning info success');
        if (completionRate >= 80) {
            completedElement.addClass('success');
        } else if (completionRate >= 50) {
            completedElement.addClass('info');
        } else {
            completedElement.addClass('warning');
        }

        // ألوان الميزانية
        const budgetElement = $('#stats-budget');
        budgetElement.removeClass('primary warning danger');
        if (budgetUsage > 90) {
            budgetElement.addClass('danger');
        } else if (budgetUsage > 70) {
            budgetElement.addClass('warning');
        } else {
            budgetElement.addClass('primary');
        }
    }

    async loadProjectTasks() {
        if (this.loadingStates.tasks) return;

        this.loadingStates.tasks = true;
        this.showLoading('tasks-loading');
        this.hideError('tasks-error');

        try {
            const response = await $.get(`/projects/api/${this.projectId}/tasks`);

            if (response.success) {
                if (response.data && response.data.length > 0) {
                    this.renderTasks(response.data);
                    $('#tasks-list').show();
                    $('#no-tasks').hide();
                } else {
                    $('#no-tasks').show();
                    $('#tasks-list').hide();
                }
                this.hideLoading('tasks-loading');
            } else {
                throw new Error(response.message || ' ');
            }
        } catch (error) {
            this.handleError('tasks', error.responseJSON?.message || error.message || 'حدث خطأ في تحميل المهام');
        } finally {
            this.loadingStates.tasks = false;
        }
    }

    renderTasks(tasks) {
        let html = '';
        tasks.forEach(task => {
            const statusBadge = this.getTaskStatusBadge(task.status);
            const priorityBadge = this.getPriorityBadge(task.priority);
            const assigneeName = task.assignee ?
                `${task.assignee.first_name || ''} ${task.assignee.last_name || ''}`.trim() : '';

            const isOverdue = this.isOverdue(task.due_date, task.status);
            const progressColor = this.getProgressBarColor(task.completion_percentage || 0);

            html += `
                <div class="card border-left-primary mb-2 priority-${task.priority}">
                    <div class="card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-1">${task.title}</h6>
                                ${task.description ? `<p class="text-muted small mb-2">${task.description}</p>` : ''}
                                <div class="d-flex flex-wrap">
                                    ${statusBadge}
                                    ${priorityBadge}
                                    <span class="badge badge-light ml-1 mb-1">تكلفة: ${this.formatMoney(task.cost || 0)}</span>
                                    ${assigneeName ? `<span class="badge badge-info ml-1 mb-1">المكلف: ${assigneeName}</span>` : ''}
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <small class="text-muted">تاريخ الاستحقاق:</small>
                                <div class="small ${isOverdue ? 'text-danger font-weight-bold' : ''}">${this.formatDate(task.due_date)}</div>
                                <div class="progress mt-1" style="height: 5px;">
                                    <div class="progress-bar ${progressColor}"
                                         style="width: ${task.completion_percentage || 0}%"></div>
                                </div>
                                <small>${task.completion_percentage || 0}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        $('#tasks-list').html(html);
    }

    async loadTeamMembers() {
        if (this.loadingStates.team) return;

        this.loadingStates.team = true;
        this.showLoading('team-loading');
        this.hideError('team-error');

        try {
            const response = await $.get(`/projects/api/${this.projectId}/team`);

            if (response.success) {
                this.renderTeamMembers(response.data);
                this.hideLoading('team-loading');
            } else {
                throw new Error(response.message || 'فشل في تحميل أعضاء الفريق');
            }
        } catch (error) {
            this.handleError('team', error.responseJSON?.message || error.message || 'حدث خطأ في تحميل أعضاء الفريق');
        } finally {
            this.loadingStates.team = false;
        }
    }

    renderTeamMembers(members) {
        let html = '';
        if (members && members.length > 0) {
            members.forEach(member => {
                const roleClass = this.getRoleClass(member.pivot?.role || 'member');
                const roleLabel = this.getRoleLabel(member.pivot?.role || 'member');

                html += `
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                        <div class="flex-grow-1">
                            <strong class="d-block">${member.first_name || ''} ${member.last_name || ''}</strong>
                            <small class="text-muted">${member.email || ''}</small>
                            ${member.pivot?.joined_at ? `<small class="d-block text-muted">انضم في: ${this.formatDate(member.pivot.joined_at)}</small>` : ''}
                        </div>
                        <div class="text-right">
                            <span class="badge ${roleClass}">${roleLabel}</span>
                            <div class="btn-group-sm mt-1">
                                <button class="btn btn-outline-danger btn-sm" onclick="projectManager.removeMember(${member.id})" title="إزالة العضو">
                                    <i class="feather icon-user-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html = '<p class="text-muted text-center">لا يوجد أعضاء في الفريق</p>';
        }
        $('#team-members').html(html);
    }

    async loadRecentComments() {
        if (this.loadingStates.comments) return;

        this.loadingStates.comments = true;
        this.showLoading('comments-loading');
        this.hideError('comments-error');

        try {
            const response = await $.get(`/projects/api/${this.projectId}/comments`);

            if (response.success) {
                this.renderRecentComments(response.data);
                this.hideLoading('comments-loading');
            } else {
                throw new Error(response.message || 'فشل في تحميل التعليقات');
            }
        } catch (error) {
            this.handleError('comments', error.responseJSON?.message || error.message || 'حدث خطأ في تحميل التعليقات');
        } finally {
            this.loadingStates.comments = false;
        }
    }

    renderRecentComments(comments) {
        let html = '';
        if (comments && comments.length > 0) {
            comments.forEach(comment => {
                html += `
                    <div class="mb-3 p-2 border-left border-primary">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <strong class="small">${comment.user?.first_name || ''} ${comment.user?.last_name || ''}</strong>
                            <small class="text-muted">${this.formatDateTime(comment.created_at)}</small>
                        </div>
                        <p class="small mb-0">${comment.content || ''}</p>
                    </div>
                `;
            });
        } else {
            html = '<p class="text-muted text-center">لا توجد تعليقات</p>';
        }
        $('#recent-comments').html(html);
    }

    async updateProgress() {
        const progressValue = $('#progress-slider').val();

        try {
            const response = await $.ajax({
                url: `/projects/api/${this.projectId}/progress`,
                method: 'PATCH',
                data: { progress_percentage: progressValue },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            if (response.success) {
                this.showSuccess(response.message || 'تم تحديث نسبة الإنجاز بنجاح');
                await this.loadProjectStats();

                if (progressValue == 100) {
                    setTimeout(() => this.loadProjectDetails(), 500);
                }
            } else {
                throw new Error(response.message || 'فشل في تحديث نسبة الإنجاز');
            }
        } catch (error) {
            this.showError(error.responseJSON?.message || error.message || 'حدث خطأ في تحديث نسبة الإنجاز');
        }
    }

    async removeMember(userId) {
        if (!confirm('هل أنت متأكد من إزالة هذا العضو من الفريق؟')) {
            return;
        }

        try {
            const response = await $.ajax({
                url: `/projects/api/${this.projectId}/members/${userId}`,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            if (response.success) {
                this.showSuccess(response.message || 'تم إزالة العضو بنجاح');
                await this.loadTeamMembers();
                await this.loadProjectStats();
            } else {
                throw new Error(response.message || 'فشل في إزالة العضو');
            }
        } catch (error) {
            this.showError(error.responseJSON?.message || error.message || 'حدث خطأ في إزالة العضو');
        }
    }

    async deleteProject() {
        if (!confirm('هل أنت متأكد من حذف هذا المشروع؟ سيتم حذف جميع المهام والتعليقات المرتبطة به.')) {
            return;
        }

        const deleteBtn = $('button[onclick*="deleteProject"]');
        deleteBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> جاري الحذف...');

        try {
            const response = await $.ajax({
                url: `/projects/api/${this.projectId}/delete`,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            if (response.success) {
                this.showSuccess(response.message || 'تم حذف المشروع بنجاح');
                setTimeout(() => {
                    window.location.href = '/projects';
                }, 1500);
            } else {
                throw new Error(response.message || 'فشل في حذف المشروع');
            }
        } catch (error) {
            let message = 'حدث خطأ في حذف المشروع';
            if (error.status === 403) {
                message = 'ليس لديك صلاحية حذف هذا المشروع';
            } else if (error.responseJSON?.message) {
                message = error.responseJSON.message;
            }
            this.showError(message);
            deleteBtn.prop('disabled', false).html('<i class="feather icon-trash-2"></i> حذف');
        }
    }

    async addComment(content) {
        if (!content.trim()) {
            this.showError('يرجى كتابة التعليق');
            return false;
        }

        try {
            const response = await $.ajax({
                url: '/comments',
                method: 'POST',
                data: {
                    content: content,
                    commentable_type: 'App\\Models\\Project',
                    commentable_id: this.projectId
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            if (response.success) {
                this.showSuccess('تم إضافة التعليق بنجاح');
                await this.loadRecentComments();
                return true;
            } else {
                throw new Error(response.message || 'فشل في إضافة التعليق');
            }
        } catch (error) {
            this.showError(error.responseJSON?.message || error.message || 'حدث خطأ في إضافة التعليق');
            return false;
        }
    }

    async addMember(userId, role) {
        if (!userId) {
            this.showError('يرجى اختيار مستخدم');
            return false;
        }

        try {
            const response = await $.ajax({
                url: `/projects/api/${this.projectId}/members`,
                method: 'POST',
                data: { user_id: userId, role: role },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            if (response.success) {
                this.showSuccess('تم إضافة العضو بنجاح');
                await this.loadTeamMembers();
                await this.loadProjectStats();
                return true;
            } else {
                throw new Error(response.message || 'فشل في إضافة العضو');
            }
        } catch (error) {
            this.showError(error.responseJSON?.message || error.message || 'حدث خطأ في إضافة العضو');
            return false;
        }
    }

    async loadAvailableUsers() {
        try {
            const response = await $.get('/api/users');
            if (response.success) {
                let options = '<option value="">اختر مستخدماً...</option>';
                response.data.forEach(user => {
                    options += `<option value="${user.id}">${user.first_name} ${user.last_name} (${user.email})</option>`;
                });
                $('#user-select').html(options);
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    setupTaskModal() {
        // تحميل أعضاء الفريق للمهمة
        this.loadTeamMembers().then(() => {
            const members = $('#team-members .d-flex');
            let options = '<option value="">لا يوجد مكلف محدد</option>';

            // استخراج أعضاء الفريق من DOM
            members.each(function() {
                const memberText = $(this).find('strong').text();
                const memberId = $(this).find('button[onclick*="removeMember"]').attr('onclick').match(/\d+/)?.[0];
                if (memberId && memberText) {
                    options += `<option value="${memberId}">${memberText}</option>`;
                }
            });

            $('#task-assigned').html(options);
        });

        // تعيين تاريخ افتراضي
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        $('#task-due-date').val(tomorrow.toISOString().split('T')[0]);
    }

    refreshAllData() {
        // إعادة تعيين حالات التحميل
        this.loadingStates = {
            details: false,
            stats: false,
            tasks: false,
            team: false,
            comments: false
        };

        this.loadAllData();
    }

    updateProgressSlider(percentage) {
        $('#progress-slider').val(percentage);
        $('#progress-value').text(percentage + '%');
        $('#current-progress-bar').css('width', percentage + '%');
    }

    // دوال الإظهار والإخفاء
    showLoading(elementId) {
        $(`#${elementId}`).show();
    }

    hideLoading(elementId) {
        $(`#${elementId}`).hide();
    }

    showError(elementId) {
        $(`#${elementId}-error`).show();
    }

    hideError(elementId) {
        $(`#${elementId}-error`).hide();
    }

    handleError(section, message) {
        this.hideLoading(`${section}-loading`);
        this.showError(`${section}-error`);
        $(`#${section}-error-message`).text(message);
        this.showError(message);
    }

    // دوال الرسائل
    showSuccess(message) {
        this.showMessage(message, 'success');
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        }
    }

    showError(message) {
        this.showMessage(message, 'error');
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        }
    }

    showMessage(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        const icon = {
            'success': 'check-circle',
            'error': 'alert-circle',
            'warning': 'alert-triangle',
            'info': 'info'
        }[type] || 'info';

        const html = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="feather icon-${icon}"></i> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;

        $('#status-messages').append(html);

        setTimeout(() => {
            $('#status-messages .alert:first').fadeOut(() => {
                $(this).remove();
            });
        }, 5000);
    }

    // دوال مساعدة للتنسيق
    getStatusBadge(status) {
        const statuses = {
            'new': '<span class="badge badge-info">جديد</span>',
            'in_progress': '<span class="badge badge-warning">قيد التنفيذ</span>',
            'completed': '<span class="badge badge-success">مكتمل</span>',
            'on_hold': '<span class="badge badge-secondary">متوقف</span>'
        };
        return statuses[status] || `<span class="badge badge-light">${status}</span>`;
    }

    getTaskStatusBadge(status) {
        const statuses = {
            'not_started': '<span class="badge badge-light">لم تبدأ</span>',
            'in_progress': '<span class="badge badge-warning">قيد التنفيذ</span>',
            'completed': '<span class="badge badge-success">مكتملة</span>',
            'overdue': '<span class="badge badge-danger">متأخرة</span>',
            'pending': '<span class="badge badge-info">معلقة</span>'
        };
        return statuses[status] || `<span class="badge badge-light">${status}</span>`;
    }

    getPriorityBadge(priority) {
        const priorities = {
            'low': '<span class="badge badge-light">منخفضة</span>',
            'medium': '<span class="badge badge-primary">متوسطة</span>',
            'high': '<span class="badge badge-warning">عالية</span>',
            'urgent': '<span class="badge badge-danger">عاجلة</span>'
        };
        return priorities[priority] || `<span class="badge badge-light">${priority}</span>`;
    }

    getRoleClass(role) {
        const classes = {
            'manager': 'badge-success',
            'member': 'badge-primary',
            'viewer': 'badge-secondary'
        };
        return classes[role] || 'badge-primary';
    }

    getRoleLabel(role) {
        const labels = {
            'manager': 'مدير',
            'member': 'عضو',
            'viewer': 'مشاهد'
        };
        return labels[role] || 'عضو';
    }

    isOverdue(dueDate, status) {
        if (status === 'completed') return false;
        return new Date(dueDate) < new Date();
    }

    getProgressBarColor(percentage) {
        if (percentage >= 80) return 'bg-success';
        if (percentage >= 50) return 'bg-info';
        if (percentage >= 25) return 'bg-warning';
        return 'bg-danger';
    }

    formatMoney(amount) {
        if (!amount || amount === 0) return '0.00 ر.س';
        return new Intl.NumberFormat('ar-SA', {
            style: 'currency',
            currency: 'SAR',
            minimumFractionDigits: 2
        }).format(amount);
    }

    formatDate(date) {
        if (!date) return '--';
        return new Date(date).toLocaleDateString('ar-SA', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    formatDateTime(dateTime) {
        const date = new Date(dateTime);
        return date.toLocaleDateString('ar-SA') + ' ' + date.toLocaleTimeString('ar-SA', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

// متغير عام لإدارة المشروع
let projectManager;

// تهيئة عند تحميل الصفحة
$(document).ready(function() {
    // تهيئة مدير المشروع مع ID المشروع
    const projectId = window.projectId || $('#project-data').data('project-id');
    if (projectId) {
        projectManager = new ProjectDetailsManager(projectId);
    }
});

// دوال عامة للاستخدام في HTML
function updateProgress() {
    if (projectManager) {
        projectManager.updateProgress();
    }
}

function deleteProject(projectId) {
    if (projectManager) {
        projectManager.deleteProject();
    }
}

function addComment() {
    if (!projectManager) return;

    const content = $('#comment-form textarea[name="content"]').val().trim();
    const submitBtn = $('#add-comment-modal').find('button[onclick="addComment()"]');

    if (!content) {
        projectManager.showError('يرجى كتابة التعليق');
        return;
    }

    submitBtn.prop('disabled', true).text('جاري الحفظ...');

    projectManager.addComment(content).then(success => {
        if (success) {
            $('#add-comment-modal').modal('hide');
            $('#comment-form')[0].reset();
        }
    }).finally(() => {
        submitBtn.prop('disabled', false).text('حفظ التعليق');
    });
}

function addMember() {
    if (!projectManager) return;

    const form = $('#member-form')[0];
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const userId = $('#user-select').val();
    const role = $('#role-select').val();
    const submitBtn = $('#add-member-modal').find('button[onclick="addMember()"]');

    submitBtn.prop('disabled', true).text('جاري الإضافة...');

    projectManager.addMember(userId, role).then(success => {
        if (success) {
            $('#add-member-modal').modal('hide');
            $('#member-form')[0].reset();
        }
    }).finally(() => {
        submitBtn.prop('disabled', false).text('إضافة العضو');
    });
}
