/**
 * ملف JavaScript الرئيسي لصفحة تفاصيل المشروع
 */

// متغيرات عامة
let currentProjectId;

$(document).ready(function() {
    // الحصول على ID المشروع من الصفحة
    currentProjectId = $('#project-stats').data('project-id');

    if (!currentProjectId) {
        console.error('لم يتم العثور على معرف المشروع');
        return;
    }

    // تحميل البيانات
    loadProjectDetails();
    loadProjectStats();
    loadProjectTasks();
    loadRecentComments();

    // إعداد الأحداث
    initializeEventListeners();
});

/**
 * تهيئة مستمعي الأحداث
 */
function initializeEventListeners() {
    // Progress slider
    $('#progress-slider').on('input', function() {
        const value = $(this).val();
        $('#progress-value').text(value + '%');
        $('#current-progress-bar').css('width', value + '%');
    });

    // Task completion slider
    $(document).on('input', '#task-completion', function() {
        $('#completion-value').text($(this).val());
    });

    // إغلاق Modal بـ Escape
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#task-modal').hasClass('show')) {
            $('#task-modal').modal('hide');
        }
    });

    // فتح modal التعليق
    $('#add-comment-modal').on('shown.bs.modal', function() {
        $('#comment-form textarea[name="content"]').focus();
    });

    // فتح modal المهمة
    $('#task-modal').on('shown.bs.modal', function() {
        $('#task-title').focus();
    });

    // تنظيف النموذج عند الإغلاق
    $('#task-modal').on('hidden.bs.modal', function() {
        resetTaskForm();
    });
}

/**
 * تحميل تفاصيل المشروع
 */
function loadProjectDetails() {
    $.get(`/projects/api/${currentProjectId}/details`)
        .done(function(response) {
            if (response.success) {
                renderProjectDetails(response.data);
            }
        })
        .fail(function() {
            console.error('خطأ في تحميل تفاصيل المشروع');
        })
        .always(function() {
            $('#project-loading').hide();
        });
}

/**
 * عرض تفاصيل المشروع
 */
function renderProjectDetails(project) {
    $('#project-title').text(project.title);

    const statusBadge = getStatusBadge(project.status);
    const priorityBadge = getPriorityBadge(project.priority);

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
                        <td>${project.creator.first_name} ${project.creator.last_name}</td>
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

/**
 * تحميل إحصائيات المشروع
 */
function loadProjectStats() {
    $.get(`/projects/api/${currentProjectId}/stats`)
        .done(function(response) {
            if (response.success) {
                renderStats(response.data);
            }
        });
}

/**
 * عرض الإحصائيات
 */
function renderStats(stats) {
    $('#stats-total-tasks').text(stats.tasks.total);
    $('#stats-completed-tasks').text(stats.tasks.completed);
    $('#stats-budget').text(formatMoney(stats.budget.total));
    $('#stats-progress').text(stats.progress + '%');
}

/**
 * عرض أعضاء الفريق
 */
function renderTeamMembers(members) {
    let html = '';
    members.forEach(function(member) {
        html += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <strong>${member.name}</strong>
                    <small class="d-block text-muted">${member.email}</small>
                </div>
                <span class="badge badge-outline-primary">${member.pivot.role}</span>
            </div>
        `;
    });
    $('#team-members').html(html);
}

/**
 * تحديث نسبة الإنجاز
 */
function updateProgress() {
    const progressValue = $('#progress-slider').val();

    $.ajax({
        url: `/projects/api/${currentProjectId}/progress`,
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

/**
 * حذف المشروع
 */
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
                url: `/projects/api/${projectId}/delete`,
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
                        window.location.href = '/projects';
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
