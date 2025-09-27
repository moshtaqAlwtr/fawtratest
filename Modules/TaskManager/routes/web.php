<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\TaskManager\Http\Controllers\CommentController;
use Modules\TaskManager\Http\Controllers\ProjectController;
use Modules\TaskManager\Http\Controllers\TaskController;
use Modules\TaskManager\Http\Controllers\WorkspaceController;

Route::middleware(['auth'])->group(function () {
    Route::group(
        [
            'prefix' => LaravelLocalization::setLocale(),
            'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'check.branch'],
        ],
        function () {
            Route::prefix('workspaces')
                ->name('workspaces.')
                ->middleware(['auth'])
                ->group(function () {
                    // الصفحات الأساسية الموجودة
                    Route::get('/', [WorkspaceController::class, 'index'])->name('index');
                    Route::get('/create', [WorkspaceController::class, 'create'])->name('create');
                    Route::get('/{workspace}', [WorkspaceController::class, 'show'])->name('show');
                    Route::get('/{workspace}/edit', [WorkspaceController::class, 'edit'])->name('edit');
                    Route::post('/{workspace}/invite', [WorkspaceController::class, 'inviteMember'])->name('workspaces.invite');
                    Route::get('/invite/{token}/workspace/{workspace}/accept', [WorkspaceController::class, 'acceptInvite'])->name('workspace.invite.accept');
                    Route::get('/invite/{token}/workspace/{workspace}/decline', [WorkspaceController::class, 'declineInvite'])->name('workspace.invite.decline');

                    // مسار جلب المستخدمين المتاحين للدعوة
                    Route::get('/{workspace}/available-users', [WorkspaceController::class, 'getAvailableUsersForInvite'])->name('workspaces.available-users');

                    // مسار لعرض الدعوات المرسلة (اختياري)
                    Route::get('/{workspace}/invites', [WorkspaceController::class, 'getWorkspaceInvites'])->name('workspaces.invites.index');

                    // مسار لإلغاء الدعوة (اختياري)
                    Route::delete('/invites/{inviteId}', [WorkspaceController::class, 'cancelInvite'])->name('workspaces.invites.cancel');

                    // مسار إعادة إرسال الدعوة
                    Route::post('/invites/{inviteId}/resend', [WorkspaceController::class, 'resendInvite'])->name('workspaces.invites.resend');

                    // مسار إحصائيات الدعوات
                    Route::get('/{workspace}/invites/stats', [WorkspaceController::class, 'getInviteStats'])->name('workspaces.invites.stats');

                    // العمليات CRUD الموجودة
                    Route::post('/', [WorkspaceController::class, 'store'])->name('store');
                    Route::put('/{workspace}', [WorkspaceController::class, 'update'])->name('update');
                    Route::delete('/{workspace}', [WorkspaceController::class, 'destroy'])->name('destroy');

                    // مسارات التحليلات الجديدة
                    Route::prefix('analytics')
                        ->name('analytics.')
                        ->group(function () {
                            // صفحة التحليلات الرئيسية
                            Route::get('/', [WorkspaceController::class, 'analytics'])->name('index');

                            // جلب الإحصائيات العامة
                            Route::get('/stats', [WorkspaceController::class, 'getAnalyticsStats'])->name('stats');

                            // تحليلات مفصلة لمساحة عمل واحدة
                            Route::get('/{workspace}/detailed', [WorkspaceController::class, 'detailedAnalytics'])->name('detailed');

                            // تصدير جميع التحليلات
                            Route::get('/export', [WorkspaceController::class, 'exportAnalytics'])->name('export');

                            // تصدير تحليلات مساحة عمل واحدة
                            Route::get('/{workspace}/export', [WorkspaceController::class, 'exportSingleWorkspace'])->name('export.single');

                            // تحميل ملف التصدير
                            Route::get('/download/{filename}', function ($filename) {
                                $path = storage_path('app/exports/' . $filename);

                                if (!file_exists($path)) {
                                    abort(404, 'الملف غير موجود');
                                }

                                return response()->download($path)->deleteFileAfterSend(true);
                            })->name('download');
                        });

                    // مسارات API إضافية للتحليلات
                    Route::prefix('api')
                        ->name('api.')
                        ->group(function () {
                            // البحث السريع في مساحات العمل
                            Route::get('/search', [WorkspaceController::class, 'quickSearch'])->name('search');

                            // تبديل الحالة الرئيسية
                            Route::post('/{workspace}/toggle-primary', [WorkspaceController::class, 'togglePrimary'])->name('toggle-primary');

                            // جلب بيانات مساحة العمل مع الإحصائيات
                            Route::get('/{workspace}/data', [WorkspaceController::class, 'getWorkspace'])->name('data');
Route::get('/{workspace}/projects', [WorkspaceController::class, 'getWorkspaceProjects'])->name('projects');
                            // جلب مشاريع مساحة العمل
                            Route::get('/{workspace}/projects', [WorkspaceController::class, 'getWorkspaceProjects'])->name('projects');

                            // جلب أعضاء مساحة العمل
                            Route::get('/{workspace}/members', [WorkspaceController::class, 'getWorkspaceMembers'])->name('members');

                            // جلب إحصائيات مساحة العمل
                            Route::get('/{workspace}/stats', [WorkspaceController::class, 'getWorkspaceStats'])->name('stats');
                        });
                });
            // روتس التعليقات
            Route::prefix('comments')
                ->name('comments.')
                ->middleware('auth')
                ->group(function () {
                    Route::post('/', [CommentController::class, 'store'])->name('store');
                    Route::put('/{comment}', [CommentController::class, 'update'])->name('update');
                    Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');
                    Route::get('/{comment}/replies', [CommentController::class, 'getReplies'])->name('replies');
                });

            // route التعليقات المُصحح - خارج المجموعة
            Route::get('/comments/{type}/{id}', [CommentController::class, 'getComments'])
                ->name('comments.paginated')
                ->middleware('auth');

            // روتس المهام
            Route::prefix('tasks')
                ->name('tasks.')
                ->middleware('auth')
                ->group(function () {
                    Route::get('/', [TaskController::class, 'index'])->name('index');

                    Route::get('/{task}', [TaskController::class, 'show'])->name('show');
                    Route::put('/{task}', [TaskController::class, 'update'])->name('update');
                    Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
Route::get('/tasks/calendar', [TaskController::class, 'calendar'])->name('calendar');
Route::get('/tasks/calendar-events', [TaskController::class, 'calendarEvents'])->name('calendar.events');
                    // APIs المهام
                    Route::patch('/{task}/progress', [TaskController::class, 'updateProgress'])->name('update-progress');
                    Route::post('/{task}/files', [TaskController::class, 'uploadFile'])->name('upload-file');
                    Route::delete('/{task}/files', [TaskController::class, 'deleteFile'])->name('delete-file');
                    Route::post('/{task}/assign', [TaskController::class, 'assignUser'])->name('assign-user');
                    Route::delete('/{task}/unassign/{user}', [TaskController::class, 'unassignUser'])->name('unassign-user');
                });
Route::get('/comments/task/{taskId}', [CommentController::class, 'getTaskComments'])
    ->name('comments.task')
    ->middleware('auth');
            // روتس API للمستخدمين
            Route::prefix('api/users')
                ->name('users.api.')
                ->middleware('auth')
                ->group(function () {
                    Route::get('/', [TaskController::class, 'apiIndex'])->name('index');
                    Route::get('/search', [TaskController::class, 'quickSearch'])->name('search');
                });

            // إضافة الروتس المفقودة لتحديث المشاريع
            Route::prefix('projects')
                ->name('projects.')
                ->middleware('auth')
                ->group(function () {
                    // تصحيح روتس API التي كانت مفقودة
                    Route::get('/api/{project}/team', [ProjectController::class, 'getTeamMembers'])->name('api.team');
                    Route::patch('/api/{project}/members/{user}/role', [ProjectController::class, 'updateMemberRole'])->name('api.update-member-role');
                });

            // مسارات إدارة الأعضاء
            Route::prefix('workspaces/{workspace}/members')
                ->name('workspaces.members.')
                ->middleware(['auth'])
                ->group(function () {
                    // إضافة عضو
                    Route::post('/', [WorkspaceController::class, 'addMember'])->name('add');

                    // إزالة عضو
                    Route::delete('/{user}', [WorkspaceController::class, 'removeMember'])->name('remove');

                    // تغيير المالك
                    Route::post('/change-owner', [WorkspaceController::class, 'changeOwner'])->name('change-owner');
                });
            // Project Routes
            Route::prefix('projects')
                ->name('projects.')
                ->middleware('auth')
                ->group(function () {
                    // ======================================
                    // الصفحات الأساسية (HTML Pages)
                    // ======================================
                    Route::get('/', [ProjectController::class, 'index'])->name('index');
                    Route::get('/create', [ProjectController::class, 'create'])->name('create');
                    Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
                    Route::post('/store', [ProjectController::class, 'store'])->name(name: 'store');
                    Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
                    Route::put('/{project}/update', [ProjectController::class, 'update'])->name('update');
                    Route::post('/{project}/invite', [ProjectController::class, 'inviteMember'])->name('invite');

                    // ======================================
                    // العمليات الجماعية (Bulk Operations)
                    // ======================================
                    Route::post('/bulk/available-users', [ProjectController::class, 'getAvailableUsersForBulkInvite'])->name('bulk.available-users');
                    Route::post('/bulk/invite', [ProjectController::class, 'bulkInviteMembers'])->name('bulk.invite');
                    Route::post('/bulk/status', [ProjectController::class, 'bulkUpdateStatus'])->name('bulk.status');

                    // ======================================
                    // APIs للبيانات (Ajax Endpoints)
                    // ======================================

                    // APIs قائمة المشاريع
                    Route::get('/api/list', [ProjectController::class, 'getProjects'])->name('api.list');
                    Route::get('/api/search', [ProjectController::class, 'quickSearch'])->name('api.search');

                    // APIs تفاصيل المشروع
                    Route::get('/api/{project}/details', [ProjectController::class, 'getProject'])->name('api.details');
                    Route::get('/api/{project}/stats', [ProjectController::class, 'getProjectStats'])->name('api.stats');
                    Route::get('/api/{project}/tasks', [ProjectController::class, 'getProjectTasks'])->name('api.tasks');
                    Route::get('/api/{project}/comments', [ProjectController::class, 'getProjectComments'])->name('api.comments');
                    Route::get('/api/{project}/detailed', [ProjectController::class, 'detailedAnalytics'])->name('detailed');
                    // APIs تعديل المشروع
                    Route::get('/api/{project}/edit-data', [ProjectController::class, 'getEditData'])->name('api.edit-data');
                    Route::post('/api/{project}/update', [ProjectController::class, 'update'])->name('api.update');
                    Route::put('/api/{project}/update', [ProjectController::class, 'update'])->name('api.update-put');

                    // APIs العمليات المتقدمة
                    Route::delete('/api/{project}/delete', [ProjectController::class, 'destroy'])->name('api.destroy');
                    Route::patch('/api/{project}/progress', [ProjectController::class, 'updateProgress'])->name('api.progress');

                    // APIs إدارة الفريق
                    Route::post('/api/{project}/members', [ProjectController::class, 'addMember'])->name('api.add-member');
                    Route::delete('/api/{project}/members/{user}', [ProjectController::class, 'removeMember'])->name('api.remove-member');
                    Route::patch('/api/{project}/members/{user}/role', [ProjectController::class, 'updateMemberRole'])->name('api.update-member-role');

                    // APIs التصدير
                    Route::get('/{project}/export/{format}', [ProjectController::class, 'export'])->name('export');
                });

            // مسارات المشاريع (لجلب المهام الأساسية)
            Route::group(['prefix' => 'projects', 'middleware' => ['auth']], function () {
                Route::get('/{project}/tasks', [TaskController::class, 'getProjectTasks'])->name('projects.tasks');
            });
            Route::prefix('tasks')
                ->name('tasks.')
                ->middleware(['auth'])
                ->group(function () {
                    // الصفحة الرئيسية
                    Route::get('/', [TaskController::class, 'index'])->name('index');

                    // جلب المهام (AJAX)
                    Route::get('/get', [TaskController::class, 'getTasks'])->name('get');

                    // جلب المهام حسب المشروع
                    Route::get('/by-project/{projectId}', [TaskController::class, 'getTasksByProject'])->name('by-project');

                    // حفظ/تحديث المهمة
                    Route::post('/store', [TaskController::class, 'store'])->name('store');

                    // عرض تفاصيل المهمة
                    Route::get('/{id}', [TaskController::class, 'show'])->name('show');

                    // تحديث حالة المهمة
                    Route::post('/{id}/update-status', [TaskController::class, 'updateStatus'])->name('update-status');

                    // تحديث نسبة الإنجاز
                    Route::post('/{id}/update-progress', [TaskController::class, 'updateProgress'])->name('update-progress');

                    // تبديل المفضلة
                    Route::post('/{id}/toggle-favorite', [TaskController::class, 'toggleFavorite'])->name('toggle-favorite');

                    // تكرار المهمة
                    Route::post('/{id}/duplicate', [TaskController::class, 'duplicate'])->name('duplicate');

                    // حفظ كمسودة
                    Route::post('/save-draft', [TaskController::class, 'saveDraft'])->name('save-draft');

                    // حذف المهمة
                    Route::delete('/{id}', [TaskController::class, 'destroy'])->name('destroy');

                    // إحصائيات المهام
                    Route::get('/statistics/data', [TaskController::class, 'getStatistics'])->name('statistics');

                    // تصدير المهام
                    Route::get('/export/excel', [TaskController::class, 'exportTasks'])->name('export');
                });
        },
    );

    // Fallback route
    Route::fallback(function () {
        return view('errors.404');
    });
});

// مسارات قبول ورفض الدعوات للمشاريع
Route::get('/projects/invite/{token}/accept', [ProjectController::class, 'showAcceptInvite'])->name('projects.invite.accept');
Route::post('/projects/invite/{token}/accept', [ProjectController::class, 'acceptInvite'])->name('projects.invite.process');
Route::get('/projects/invite/{token}/decline', [ProjectController::class, 'declineInvite'])->name('projects.invite.decline');
