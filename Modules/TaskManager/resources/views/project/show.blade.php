@extends('master')

@section('title')
    تفاصيل المشروع
@stop

@section('css')

    <style>
        /* بطاقة المهمة المحسّنة */
        .task-card-improved {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }

        .task-card-improved:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #007bff;
        }

        /* رأس المهمة */
        .task-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
        }

        .task-icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .task-title {
            font-size: 15px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 4px 0;
            line-height: 1.4;
        }

        .task-description {
            font-size: 13px;
            color: #6c757d;
            margin: 0;
            line-height: 1.5;
        }

        /* معلومات المهمة */
        .task-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 12px;
        }

        .task-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .task-info-item i {
            font-size: 14px;
            width: 20px;
            text-align: center;
        }

        .task-info-label {
            font-size: 12px;
            color: #6c757d;
            margin: 0 4px 0 0;
        }

        .task-info-value {
            font-size: 13px;
            font-weight: 600;
            color: #2c3e50;
        }

        /* شريط التقدم */
        .task-progress-bar {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin: 8px 0;
        }

        .task-progress-fill {
            height: 100%;
            transition: width 0.3s ease;
        }

        /* الموظفين المكلفين */
        .assigned-users-compact {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .user-avatar-compact {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 13px;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* الشارات */
        .task-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .tasks-empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .tasks-empty-state i {
            font-size: 48px;
            color: #dee2e6;
            margin-bottom: 16px;
        }

        /* تحسينات إضافية */
        .gap-2 {
            gap: 0.5rem;
        }

        .me-1 {
            margin-right: 0.25rem;
        }

        .me-2 {
            margin-right: 0.5rem;
        }
    </style>
@endsection

@section('content')
@include('taskmanager::project.partials.task-modal')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0" id="project-title">تفاصيل المشروع</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">المشاريع</a></li>
                            <li class="breadcrumb-item active">تفاصيل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
            <div class="form-group breadcrumb-right">
                <div class="btn-group">
                    <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning btn-sm">
                        <i class="feather icon-edit"></i> تعديل
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteProject({{ $project->id }})">
                        <i class="feather icon-trash-2"></i> حذف
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- الإحصائيات -->
        <div class="row match-height" id="project-stats">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="success" id="stats-total-tasks">0</h3>
                                    <span>إجمالي المهام</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather icon-list font-large-2 success float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="warning" id="stats-completed-tasks">0</h3>
                                    <span>المهام المكتملة</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather icon-check-circle font-large-2 warning float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="primary" id="stats-budget">0</h3>
                                    <span>الميزانية</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather icon-dollar-sign font-large-2 primary float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="info" id="stats-progress">0%</h3>
                                    <span>نسبة الإنجاز</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="feather icon-trending-up font-large-2 info float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- تفاصيل المشروع -->
            <div class="col-lg-8 col-md-7 col-12">
                <!-- معلومات المشروع -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">معلومات المشروع</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body" id="project-details">
                            <!-- Loading -->
                            <div id="project-loading" class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">جاري التحميل...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قائمة المهام -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">المهام</h4>
                        <div class="heading-elements">
                            <button type="button" class="btn btn-primary btn-sm" id="btnAddTask"
                                onclick="openTaskModal({{ $project->id }})">
                                <i class="feather icon-plus"></i> إضافة مهمة
                            </button>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div id="tasks-loading" class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">جاري تحميل المهام...</span>
                                </div>
                            </div>
                            <div id="tasks-list" style="display: none;"></div>
                            <div id="no-tasks" class="tasks-empty-state" style="display: none;">
                                <i class="fas fa-tasks"></i>
                                <p class="text-muted mb-0">لا توجد مهام في هذا المشروع</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="col-lg-4 col-md-5 col-12">
                <!-- تحديث نسبة الإنجاز -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">تحديث نسبة الإنجاز</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="form-group">
                                <label>نسبة الإنجاز الحالية</label>
                                <div class="progress mb-2">
                                    <div class="progress-bar" id="current-progress-bar" style="width: 0%"></div>
                                </div>
                                <input type="range" class="custom-range" id="progress-slider" min="0"
                                    max="100" value="0">
                                <div class="d-flex justify-content-between">
                                    <small>0%</small>
                                    <small id="progress-value">0%</small>
                                    <small>100%</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-block" onclick="updateProgress()">
                                تحديث النسبة
                            </button>
                        </div>
                    </div>
                </div>

                <!-- فريق المشروع -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">فريق المشروع</h4>
                        <div class="heading-elements">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                data-target="#add-member-modal">
                                <i class="feather icon-user-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div id="team-members"></div>
                        </div>
                    </div>
                </div>

                <!-- آخر التعليقات -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">آخر التعليقات</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div id="recent-comments"></div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-block"
                                    data-toggle="modal" data-target="#add-comment-modal">
                                    <i class="feather icon-message-circle"></i> إضافة تعليق
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal إضافة تعليق -->
    <div class="modal fade" id="add-comment-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">إضافة تعليق جديد</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="comment-form">
                        <div class="form-group">
                            <label>التعليق</label>
                            <textarea name="content" class="form-control" rows="4" placeholder="اكتب تعليقك هنا..." required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" onclick="addComment()">حفظ التعليق</button>
                </div>
            </div>
        </div>
    </div>

 @include('taskmanager::project.partials.task-modal')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
// فتح مودال إضافة مهمة
function openTaskModal(projectId = null) {
    // إعادة تعيين النموذج
    $('#taskForm')[0].reset();
    $('#task_id').val('');

    // تحديد المشروع تلقائياً
    if (projectId) {
        $('#project_id').val(projectId).trigger('change');
    }

    // تحديث عنوان المودال
    $('#taskModalTitle').text('إضافة مهمة جديدة');
    $('#taskModalIcon').removeClass('icon-edit').addClass('icon-plus');

    // إعادة تعيين الخطوات
    currentStep = 1;
    showStep(1);
    updateStepNavigation();
    updateFormProgress();

    // فتح المودال
    $('#taskModal').modal('show');
}

// دالة حفظ المهمة
function saveTask() {
    const formData = new FormData($('#taskForm')[0]);

    Swal.fire({
        title: 'جاري الحفظ...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '{{ route("tasks.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح!',
                    text: 'تم حفظ المهمة بنجاح',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    $('#taskModal').modal('hide');

                    // إضافة المهمة للقائمة مباشرة
                    if (response.data) {
                        addTaskToList(response.data);
                    }

                    // تحديث الإحصائيات
                    loadProjectStats();
                });
            }
        },
        error: function(xhr) {
            let errorMessage = 'حدث خطأ في حفظ المهمة';

            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join('\n');
            }

            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: errorMessage,
                confirmButtonText: 'حسناً'
            });
        }
    });
}
    </script>
    <script>
        $(document).ready(function() {
            const projectId = {{ $project->id }};

            // تحميل البيانات
            loadProjectDetails();
            loadProjectStats();
            loadProjectTasks();
            loadRecentComments();

            // Progress slider
            $('#progress-slider').on('input', function() {
                const value = $(this).val();
                $('#progress-value').text(value + '%');
                $('#current-progress-bar').css('width', value + '%');
            });

            function loadProjectDetails() {
                $.get(`{{ url('projects/api') }}/${projectId}/details`)
                    .done(function(response) {
                        if (response.success) {
                            renderProjectDetails(response.data);
                        }
                    })
                    .always(function() {
                        $('#project-loading').hide();
                    });
            }

            function renderProjectDetails(project) {
                $('#project-title').text(project.title);

                let statusBadge = getStatusBadge(project.status);
                let priorityBadge = getPriorityBadge(project.priority);

                let html = `
            <div class="row">
                <div class="col-md-6">
                    <h5>${project.title}</h5>
                    <p class="text-muted">${project.description || 'لا يوجد وصف'}</p>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>مساحة العمل:</strong></td>
                            <td>${project.workspace.title}</td>
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
                            <td>${formatDate(project.start_date)}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ النهاية:</strong></td>
                            <td>${formatDate(project.end_date)}</td>
                        </tr>
                        <tr>
                            <td><strong>المنشئ:</strong></td>
                            <td>${project.creator.name} ${project.creator.name}</td>
                        </tr>
                    </table>
                </div>
            </div>
        `;

                $('#project-details').html(html);

                // تحديث نسبة الإنجاز
                $('#progress-slider').val(project.progress_percentage);
                $('#progress-value').text(project.progress_percentage + '%');
                $('#current-progress-bar').css('width', project.progress_percentage + '%');

                // عرض فريق المشروع
                renderTeamMembers(project.users);
            }

            function loadProjectStats() {
                $.get(`{{ url('projects/api') }}/${projectId}/stats`)
                    .done(function(response) {
                        if (response.success) {
                            renderStats(response.data);
                        }
                    });
            }

            function renderStats(stats) {
                $('#stats-total-tasks').text(stats.tasks.total);
                $('#stats-completed-tasks').text(stats.tasks.completed);
                $('#stats-budget').text(formatMoney(stats.budget.total));
                $('#stats-progress').text(stats.progress + '%');
            }

            function loadProjectTasks() {
                $.get(`{{ url('projects/api') }}/${projectId}/tasks`)
                    .done(function(response) {
                        if (response.success && response.data.length > 0) {
                            renderTasks(response.data);
                            $('#tasks-list').show();
                        } else {
                            $('#no-tasks').show();
                        }
                    })
                    .always(function() {
                        $('#tasks-loading').hide();
                    });
            }

            function renderTasks(tasks) {
                let html = '<div class="tasks-list-improved">';

                tasks.forEach(function(task) {
                    const statusConfig = getStatusConfig(task.status);
                    const priorityConfig = getPriorityConfig(task.priority);

                    html += `
                <div class="task-card-improved">
                    <!-- رأس المهمة -->
                    <div class="task-header">
                        <div class="task-icon-wrapper" style="background-color: ${priorityConfig.color};">
                            <i class="fas ${priorityConfig.icon} text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="task-title">${escapeHtml(task.title)}</h6>
                            ${task.description ? `
                                    <p class="task-description">${escapeHtml(task.description)}</p>
                                ` : ''}
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

                        ${task.cost ? `
                                <div class="task-info-item">
                                    <i class="fas fa-money-bill-wave text-success"></i>
                                    <span class="task-info-label">التكلفة:</span>
                                    <span class="task-info-value">${formatMoney(task.cost)}</span>
                                </div>
                            ` : ''}
                    </div>

                    <!-- شريط التقدم -->
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">نسبة الإنجاز</small>
                            <strong style="color: ${statusConfig.color};">${task.completion_percentage}%</strong>
                        </div>
                        <div class="task-progress-bar">
                            <div class="task-progress-fill" style="width: ${task.completion_percentage}%; background-color: ${statusConfig.color};"></div>
                        </div>
                    </div>

                    <!-- الموظفين المكلفين -->
                    ${renderAssignedUsers(task.assigned_users || [])}
                </div>
            `;
                });

                html += '</div>';
                $('#tasks-list').html(html);
            }

            function renderTeamMembers(members) {
                let html = '';
                members.forEach(function(member) {
                    html += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong>${member.name} </strong>
                        <small class="d-block text-muted">${member.email}</small>
                    </div>
                    <span class="badge badge-outline-primary">${member.pivot.role}</span>
                </div>
            `;
                });
                $('#team-members').html(html);
            }

            function loadRecentComments(page = 1) {
                $('#recent-comments').html(`
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="sr-only">جاري التحميل...</span>
                </div>
            </div>
        `);

                $.ajax({
                    url: `{{ route('comments.paginated', ['type' => 'project', 'id' => $project->id]) }}`,
                    method: 'GET',
                    data: {
                        page: page,
                        per_page: 5
                    },
                    success: function(response) {
                        if (response.success && response.data && response.data.length > 0) {
                            renderRecentComments(response.data);
                        } else {
                            $('#recent-comments').html(
                                '<p class="text-muted text-center small">لا توجد تعليقات</p>');
                        }
                    },
                    error: function(xhr) {
                        console.error('خطأ في تحميل التعليقات:', xhr);
                        $('#recent-comments').html(
                            '<p class="text-danger text-center small">خطأ في تحميل التعليقات</p>');
                    }
                });
            }

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
                            <p class="text-muted mb-1" style="font-size: 12px; line-height: 1.4;">${comment.content}</p>
                            ${comment.replies_count > 0 ?
                                `<small class="text-info" style="font-size: 11px;">
                                        <i class="feather icon-message-circle" style="font-size: 10px;"></i>
                                        ${comment.replies_count} رد
                                    </small>`
                                : ''
                            }
                        </div>
                    </div>
                </div>
            `;
                });

                $('#recent-comments').html(html);
            }

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
        });

        // تحديث نسبة الإنجاز
        function updateProgress() {
            const progressValue = $('#progress-slider').val();
            const projectId = {{ $project->id }};

            $.ajax({
                    url: `{{ url('projects/api') }}/${projectId}/progress`,
                    method: 'PATCH',
                    data: {
                        progress_percentage: progressValue
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        loadProjectStats();
                    }
                })
                .fail(function() {
                    toastr.error('حدث خطأ في تحديث نسبة الإنجاز');
                });
        }

        // إضافة تعليق جديد
        function addComment() {
            const content = $('#comment-form textarea[name="content"]').val();

            if (!content.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'يرجى كتابة التعليق',
                    confirmButtonText: 'حسناً',
                    customClass: {
                        confirmButton: 'btn btn-warning'
                    }
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
                    url: `{{ url('comments') }}`,
                    method: 'POST',
                    data: {
                        content: content,
                        commentable_type: 'App\\Models\\Project',
                        commentable_id: {{ $project->id }}
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
                            showConfirmButton: false,
                            customClass: {
                                popup: 'animated fadeInUp'
                            }
                        }).then(() => {
                            $('#add-comment-modal').modal('hide');
                            $('#comment-form')[0].reset();
                            loadRecentComments();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: response.message || 'حدث خطأ في إضافة التعليق',
                            confirmButtonText: 'حسناً',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                    }
                })
                .fail(function(xhr) {
                    let errorMessage = 'حدث خطأ في إضافة التعليق';

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const errorMessages = Object.values(errors).flat();
                        errorMessage = errorMessages.join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: errorMessage,
                        confirmButtonText: 'حسناً',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        }
                    });
                });
        }

        // حذف مشروع
        function deleteProject(projectId) {
            Swal.fire({
                title: 'تأكيد الحذف',
                text: 'هل أنت متأكد من حذف هذا المشروع؟ سيتم حذف جميع المهام والتعليقات المرتبطة به.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                            url: `{{ url('projects/api') }}/${projectId}/delete`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        })
                        .done(function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحذف!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = '{{ route('projects.index') }}';
                                });
                            }
                        })
                        .fail(function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: 'حدث خطأ في حذف المشروع'
                            });
                        });
                }
            });
        }

        // دوال مساعدة
        function getStatusBadge(status) {
            const statuses = {
                'new': '<span class="badge badge-info">جديد</span>',
                'in_progress': '<span class="badge badge-warning">قيد التنفيذ</span>',
                'completed': '<span class="badge badge-success">مكتمل</span>',
                'on_hold': '<span class="badge badge-secondary">متوقف</span>'
            };
            return statuses[status] || status;
        }

        function getPriorityBadge(priority) {
            const priorities = {
                'low': '<span class="badge badge-light">منخفض</span>',
                'medium': '<span class="badge badge-primary">متوسط</span>',
                'high': '<span class="badge badge-warning">عالي</span>',
                'urgent': '<span class="badge badge-danger">عاجل</span>'
            };
            return priorities[priority] || priority;
        }

        function getStatusConfig(status) {
            const configs = {
                'not_started': {
                    color: '#6c757d',
                    text: 'لم تبدأ',
                    icon: 'fa-clock'
                },
                'in_progress': {
                    color: '#ffc107',
                    text: 'قيد التنفيذ',
                    icon: 'fa-play-circle'
                },
                'completed': {
                    color: '#28a745',
                    text: 'مكتملة',
                    icon: 'fa-check-circle'
                },
                'overdue': {
                    color: '#dc3545',
                    text: 'متأخرة',
                    icon: 'fa-exclamation-circle'
                }
            };
            return configs[status] || configs['not_started'];
        }

        function getPriorityConfig(priority) {
            const configs = {
                'low': {
                    color: '#28a745',
                    text: 'منخفضة',
                    icon: 'fa-arrow-down'
                },
                'medium': {
                    color: '#17a2b8',
                    text: 'متوسطة',
                    icon: 'fa-minus'
                },
                'high': {
                    color: '#ffc107',
                    text: 'عالية',
                    icon: 'fa-arrow-up'
                },
                'urgent': {
                    color: '#dc3545',
                    text: 'عاجلة',
                    icon: 'fa-exclamation'
                }
            };
            return configs[priority] || configs['medium'];
        }

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

        function formatMoney(amount) {
            return new Intl.NumberFormat('ar-SA', {
                style: 'currency',
                currency: 'SAR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        }

        function formatDate(date) {
            if (!date) return '';
            return new Date(date).toLocaleDateString('ar-SA');
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        // تحسين تجربة المستخدم عند فتح النموذج
        $(document).ready(function() {
            $('#add-comment-modal').on('shown.bs.modal', function() {
                $('#comment-form textarea[name="content"]').focus();
            });

            // تفعيل tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <SCript>
// إضافة مهمة جديدة للقائمة مباشرة
function addTaskToList(task) {
    // إخفاء رسالة "لا توجد مهام" إذا كانت ظاهرة
    $('#no-tasks').hide();

    // إظهار قائمة المهام
    $('#tasks-list').show();

    const statusConfig = getStatusConfig(task.status);
    const priorityConfig = getPriorityConfig(task.priority);

    const taskHtml = `
        <div class="task-card-improved" data-task-id="${task.id}">
            <!-- رأس المهمة -->
            <div class="task-header">
                <div class="task-icon-wrapper" style="background-color: ${priorityConfig.color};">
                    <i class="fas ${priorityConfig.icon} text-white"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="task-title">${escapeHtml(task.title)}</h6>
                    ${task.description ? `
                        <p class="task-description">${escapeHtml(task.description)}</p>
                    ` : ''}
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

                ${task.budget ? `
                    <div class="task-info-item">
                        <i class="fas fa-money-bill-wave text-success"></i>
                        <span class="task-info-label">التكلفة:</span>
                        <span class="task-info-value">${formatMoney(task.budget)}</span>
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

    // إضافة المهمة في بداية القائمة (أو يمكنك استخدام .append() لإضافتها في النهاية)
    if ($('#tasks-list .tasks-list-improved').length === 0) {
        $('#tasks-list').html('<div class="tasks-list-improved">' + taskHtml + '</div>');
    } else {
        $('#tasks-list .tasks-list-improved').prepend(taskHtml);
    }

    // إضافة تأثير بصري للمهمة الجديدة
    $(`[data-task-id="${task.id}"]`).hide().fadeIn(600);

    // تفعيل tooltips للعناصر الجديدة
    $('[data-toggle="tooltip"]').tooltip();
}
    </SCript>
@endsection

