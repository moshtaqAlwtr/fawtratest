<?php

namespace Modules\TaskManager\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * عرض صفحة إدارة المهام المحسنة
     */
public function index(Request $request)
{
    $projects = Project::all();
    $users = User::all();

    // جلب المهام مع العلاقات المطلوبة
    $tasksQuery = Task::with([
        'creator',
        'assignedUsers',
        'project',
        'subTasks',
        'comments' => function($query) {
            $query->latest()->limit(3);
        }
    ])->whereNull('parent_task_id');

    // تطبيق الفلاتر إن وجدت
    if ($request->filled('project_id')) {
        $tasksQuery->where('project_id', $request->project_id);
    }

    if ($request->filled('assignee')) {
        $tasksQuery->whereHas('assignedUsers', function($query) use ($request) {
            $query->where('user_id', $request->assignee);
        });
    }

    if ($request->filled('priority')) {
        $tasksQuery->where('priority', $request->priority);
    }

    if ($request->filled('status')) {
        $tasksQuery->where('status', $request->status);
    }

    // فلتر التواريخ
    if ($request->filled('date_from')) {
        $tasksQuery->where('due_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $tasksQuery->where('due_date', '<=', $request->date_to);
    }

    // تحديث المهام المتأخرة
    $this->updateOverdueTasks();

    // الحالات المعرفة
    $statuses = [
        'not_started' => 'لم تبدأ',
        'in_progress' => 'قيد التنفيذ',
        'completed'   => 'مكتملة',
        'overdue'     => 'متأخرة',
    ];

    // معالجة الحالات غير المعرفة
    $tasks = $tasksQuery->get()->groupBy(function ($task) use ($statuses) {
        return array_key_exists($task->status, $statuses) ? $task->status : 'unknown';
    });

    // إضافة خيار "غير معروفة" إذا فيه حالات مش معرفة
    if ($tasks->has('unknown')) {
        $statuses['unknown'] = 'غير معروفة';
    }

    // التأكد من وجود جميع الحالات المطلوبة في المصفوفة
    foreach (['not_started', 'in_progress', 'completed', 'overdue'] as $status) {
        if (!$tasks->has($status)) {
            $tasks[$status] = collect();
        }
    }

    // استخراج حالة الطلب الحالية بشكل آمن
    $statusKey = $request->status;
    $statusLabel = $statuses[$statusKey] ?? 'غير معروف';

    return view('taskmanager::task.index', compact('tasks', 'projects', 'users', 'statuses', 'statusLabel'));
}


    /**
     * جلب المهام عبر AJAX مع تحسينات
     */
    public function getTasks(Request $request)
    {
        try {
            $cacheKey = 'tasks_' . md5(serialize($request->all()));

            // محاولة الحصول على البيانات من الكاش
            $tasks = Cache::remember($cacheKey, 300, function() use ($request) {
                $query = Task::with([
                    'creator',
                    'assignedUsers',
                    'project',
                    'subTasks',
                    'comments' => function($q) { $q->latest()->limit(3); }
                ]);

                // تطبيق الفلاتر
                $this->applyFilters($query, $request);

                return $query->whereNull('parent_task_id')->get();
            });

            return response()->json([
                'success' => true,
                'tasks' => $tasks,
                'total_count' => $tasks->count(),
                'cache_key' => $cacheKey
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في جلب المهام عبر AJAX: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب المهام'
            ], 500);
        }
    }

    /**
     * حفظ مهمة جديدة أو تحديث موجودة مع تحسينات
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'status' => 'required|in:not_started,in_progress,completed,overdue',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date|after_or_equal:today',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0|max:9999999.99',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999',
            'completion_percentage' => 'nullable|integer|min:0|max:100',
            'parent_task_id' => 'nullable|exists:tasks,id',
            'assigned_users' => 'nullable|array',
            'assigned_users.*' => 'exists:users,id',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
            'is_urgent' => 'boolean',
            'send_notifications' => 'boolean'
        ], [
            'project_id.required' => 'يجب اختيار المشروع',
            'title.required' => 'عنوان المهمة مطلوب',
            'title.max' => 'عنوان المهمة لا يجب أن يتجاوز 255 حرف',
            'description.max' => 'الوصف لا يجب أن يتجاوز 5000 حرف',
            'status.in' => 'حالة المهمة غير صحيحة',
            'priority.in' => 'أولوية المهمة غير صحيحة',
            'start_date.after_or_equal' => 'تاريخ البدء لا يمكن أن يكون في الماضي',
            'due_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
            'budget.max' => 'الميزانية كبيرة جداً',
            'estimated_hours.max' => 'الساعات المقدرة كبيرة جداً',
            'files.*.max' => 'حجم الملف لا يجب أن يتجاوز 10 ميجابايت',
            'files.*.mimes' => 'نوع الملف غير مدعوم'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $taskData = $request->except(['assigned_users', 'files', 'send_notifications']);
            $taskData['created_by'] = auth()->id();

            // تحديد الحالة تلقائياً حسب نسبة الإنجاز
            if ($request->has('completion_percentage')) {
                $percentage = (int) $request->completion_percentage;
                if ($percentage == 0) {
                    $taskData['status'] = 'not_started';
                } elseif ($percentage == 100) {
                    $taskData['status'] = 'completed';
                    $taskData['completed_date'] = now();
                } elseif ($percentage > 0 && $percentage < 100) {
                    $taskData['status'] = 'in_progress';
                }
            }

            // معالجة المهمة العاجلة
            if ($request->boolean('is_urgent')) {
                $taskData['priority'] = 'urgent';
                $taskData['is_urgent'] = true;
            }

            $task = null;
            $message = '';

            if ($request->has('id') && $request->id) {
                // تحديث مهمة موجودة
                $task = Task::findOrFail($request->id);
                $this->authorizeTaskAction($task, 'update');

                $task->update($taskData);
                $message = 'تم تحديث المهمة بنجاح';

                // تسجيل النشاط
                $this->logTaskActivity($task, 'updated', 'تم تحديث المهمة');

            } else {
                // إنشاء مهمة جديدة
                $task = Task::create($taskData);
                $message = 'تم إضافة المهمة بنجاح';

                // تسجيل النشاط
                $this->logTaskActivity($task, 'created', 'تم إنشاء المهمة');
            }

            // تعيين المستخدمين
            if ($request->has('assigned_users') && is_array($request->assigned_users)) {
                $syncData = [];
                foreach ($request->assigned_users as $userId) {
                    $syncData[$userId] = [
                        'assigned_at' => now(),
                        'assigned_by' => auth()->id()
                    ];
                }
                $task->assignedUsers()->sync($syncData);

                // إرسال إشعارات للمستخدمين المعينين
                if ($request->boolean('send_notifications')) {
                    $this->sendTaskNotifications($task, $request->assigned_users, 'assigned');
                }
            }

            // رفع الملفات
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $this->uploadTaskFile($task, $file);
                }
            }

            // مسح الكاش
            $this->clearTasksCache();

            DB::commit();

            // إعادة تحميل العلاقات
            $task->load(['creator', 'assignedUsers', 'project', 'subTasks', 'comments']);

            return response()->json([
                'success' => true,
                'message' => $message,
                'task' => $task
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في حفظ المهمة: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ المهمة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث حالة المهمة مع تحسينات
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:not_started,in_progress,completed,overdue',
            'completion_percentage' => 'nullable|integer|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $task = Task::findOrFail($id);
            $this->authorizeTaskAction($task, 'update');

            $oldStatus = $task->status;
            $task->status = $request->status;

            // تحديث نسبة الإنجاز تلقائياً حسب الحالة
            if ($request->has('completion_percentage')) {
                $task->completion_percentage = $request->completion_percentage;
            } else {
                switch ($request->status) {
                    case 'not_started':
                        $task->completion_percentage = 0;
                        break;
                    case 'in_progress':
                        if ($task->completion_percentage == 0) {
                            $task->completion_percentage = 25;
                        }
                        break;
                    case 'completed':
                        $task->completion_percentage = 100;
                        if (!$task->completed_date) {
                            $task->completed_date = now();
                        }
                        break;
                    case 'overdue':
                        if ($task->completion_percentage == 0) {
                            $task->completion_percentage = 10;
                        }
                        break;
                }
            }

            $task->save();

            // تسجيل النشاط
            $this->logTaskActivity($task, 'status_changed', "تم تغيير الحالة من {$oldStatus} إلى {$request->status}");

            // إرسال إشعارات للمستخدمين المعينين
            $this->sendTaskNotifications($task, $task->assignedUsers->pluck('id')->toArray(), 'status_updated');

            // تحديث المهام الفرعية إذا كانت المهمة مكتملة
            if ($request->status === 'completed') {
                $this->updateSubTasksStatus($task);
            }

            // مسح الكاش
            $this->clearTasksCache();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة المهمة بنجاح',
                'task' => $task->load(['creator', 'assignedUsers', 'project'])
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في تحديث حالة المهمة: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الحالة'
            ], 500);
        }
    }

    /**
     * تحديث نسبة الإنجاز مع تحسينات
     */
    public function updateProgress(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'completion_percentage' => 'required|integer|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $task = Task::findOrFail($id);
            $this->authorizeTaskAction($task, 'update');

            $oldPercentage = $task->completion_percentage;
            $task->completion_percentage = $request->completion_percentage;

            // تحديث الحالة تلقائياً حسب النسبة
            if ($request->completion_percentage == 100) {
                $task->status = 'completed';
                if (!$task->completed_date) {
                    $task->completed_date = now();
                }
            } elseif ($request->completion_percentage == 0) {
                $task->status = 'not_started';
            } elseif ($request->completion_percentage > 0 && $request->completion_percentage < 100) {
                if ($task->status === 'not_started' || $task->status === 'completed') {
                    $task->status = 'in_progress';
                }
            }

            $task->save();

            // تسجيل النشاط
            $this->logTaskActivity($task, 'progress_updated', "تم تحديث نسبة الإنجاز من {$oldPercentage}% إلى {$request->completion_percentage}%");

            // إرسال إشعار إذا وصلت النسبة لمعالم مهمة
            $milestones = [25, 50, 75, 100];
            if (in_array($request->completion_percentage, $milestones)) {
                $this->sendTaskNotifications($task, $task->assignedUsers->pluck('id')->toArray(), 'milestone_reached');
            }

            // مسح الكاش
            $this->clearTasksCache();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث نسبة الإنجاز بنجاح',
                'task' => $task
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في تحديث نسبة الإنجاز: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحديث'
            ], 500);
        }
    }

    /**
     * تبديل حالة المفضلة
     */
    public function toggleFavorite($id)
    {
        try {
            $task = Task::findOrFail($id);
            $this->authorizeTaskAction($task, 'view');

            $userId = auth()->id();
            $isFavorite = $task->favorites()->where('user_id', $userId)->exists();

            if ($isFavorite) {
                $task->favorites()->detach($userId);
                $message = 'تم إزالة المهمة من المفضلة';
            } else {
                $task->favorites()->attach($userId, ['created_at' => now()]);
                $message = 'تم إضافة المهمة للمفضلة';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_favorite' => !$isFavorite
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحديث المفضلة'
            ], 500);
        }
    }

    /**
     * تكرار المهمة
     */
    public function duplicate($id)
    {
        try {
            $originalTask = Task::findOrFail($id);
            $this->authorizeTaskAction($originalTask, 'view');

            DB::beginTransaction();

            // إنشاء نسخة من المهمة
            $newTask = $originalTask->replicate();
            $newTask->title = $originalTask->title . ' (نسخة)';
            $newTask->status = 'not_started';
            $newTask->completion_percentage = 0;
            $newTask->completed_date = null;
            $newTask->created_by = auth()->id();
            $newTask->save();

            // نسخ المستخدمين المعينين
            $assignedUsers = $originalTask->assignedUsers()->get();
            foreach ($assignedUsers as $user) {
                $newTask->assignedUsers()->attach($user->id, [
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id()
                ]);
            }

            // نسخ الملفات
            if ($originalTask->hasFiles()) {
                foreach ($originalTask->getFiles() as $file) {
                    $this->copyTaskFile($originalTask, $newTask, $file);
                }
            }

            DB::commit();

            // مسح الكاش
            $this->clearTasksCache();

            $newTask->load(['creator', 'assignedUsers', 'project', 'subTasks']);

            return response()->json([
                'success' => true,
                'message' => 'تم تكرار المهمة بنجاح',
                'task' => $newTask
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في تكرار المهمة: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تكرار المهمة'
            ], 500);
        }
    }

    /**
     * حفظ مسودة
     */
    public function saveDraft(Request $request)
    {
        try {
            $draftData = $request->all();
            $draftData['is_draft'] = true;
            $draftData['created_by'] = auth()->id();

            $cacheKey = 'task_draft_' . auth()->id();
            Cache::put($cacheKey, $draftData, 3600); // ساعة واحدة

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ المسودة بنجاح',
                'cache_key' => $cacheKey
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في حفظ المسودة'
            ], 500);
        }
    }

    /**
     * حذف المهمة مع تحسينات
     */
    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $this->authorizeTaskAction($task, 'delete');

            DB::beginTransaction();

            // حذف الملفات من التخزين
            if ($task->hasFiles()) {
                foreach ($task->getFiles() as $file) {
                    Storage::disk('public')->delete('tasks/' . $file['filename']);
                }
            }

            // حذف المهام الفرعية
            $task->subTasks()->delete();

            // تسجيل النشاط قبل الحذف
            $this->logTaskActivity($task, 'deleted', 'تم حذف المهمة');

            // حذف المهمة
            $task->delete();

            DB::commit();

            // مسح الكاش
            $this->clearTasksCache();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المهمة بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في حذف المهمة: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحذف'
            ], 500);
        }
    }

    // الدوال المساعدة

    /**
     * تطبيق الفلاتر على الاستعلام
     */
    private function applyFilters($query, $request)
    {
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('assignee') && $request->assignee) {
            $query->whereHas('assignedUsers', function($q) use ($request) {
                $q->where('user_id', $request->assignee);
            });
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('due_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('due_date', '<=', $request->date_to);
        }

        return $query;
    }

    /**
     * تحديث المهام المتأخرة
     */
    private function updateOverdueTasks()
    {
        Task::where('due_date', '<', Carbon::today())
            ->whereNotIn('status', ['completed'])
            ->update(['status' => 'overdue']);
    }

    /**
     * التحقق من صلاحيات المهمة
     */
    private function authorizeTaskAction($task, $action)
    {
        $user = auth()->user();

        // المنشئ يمكنه فعل أي شيء
        if ($task->created_by === $user->id) {
            return true;
        }

        // المستخدمين المعينين يمكنهم العرض والتحديث
        if ($task->assignedUsers->contains($user->id)) {
            if (in_array($action, ['view', 'update'])) {
                return true;
            }
        }

        // الإداريين يمكنهم فعل أي شيء
        if ($user->hasRole('admin')) {
            return true;
        }

        abort(403, 'غير مصرح لك بتنفيذ هذا الإجراء');
    }

    /**
     * رفع ملف المهمة
     */
    private function uploadTaskFile($task, $file)
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('tasks', $filename, 'public');

        $task->addFile(
            $filename,
            $file->getClientOriginalExtension(),
            $file->getClientOriginalName(),
            $file->getSize()
        );

        return $filename;
    }

    /**
     * نسخ ملف المهمة
     */
    private function copyTaskFile($originalTask, $newTask, $file)
    {
        $originalPath = 'tasks/' . $file['filename'];
        $newFilename = time() . '_' . uniqid() . '.' . $file['extension'];
        $newPath = 'tasks/' . $newFilename;

        if (Storage::disk('public')->exists($originalPath)) {
            Storage::disk('public')->copy($originalPath, $newPath);

            $newTask->addFile(
                $newFilename,
                $file['extension'],
                $file['original_name'],
                $file['size'] ?? 0
            );
        }
    }

    /**
     * تسجيل نشاط المهمة
     */
    private function logTaskActivity($task, $action, $description)
    {
        // هنا يمكن إضافة نظام تسجيل الأنشطة
        Log::info("Task Activity: {$action} - Task ID: {$task->id} - {$description}");
    }

    /**
     * إرسال إشعارات المهمة
     */
    private function sendTaskNotifications($task, $userIds, $type)
    {
        // هنا يمكن إضافة نظام الإشعارات
        foreach ($userIds as $userId) {
            // إرسال إشعار للمستخدم
        }
    }

    /**
     * تحديث حالة المهام الفرعية
     */
    private function updateSubTasksStatus($task)
    {
        if ($task->subTasks()->count() > 0) {
            $task->subTasks()->update([
                'status' => 'completed',
                'completion_percentage' => 100,
                'completed_date' => now()
            ]);
        }
    }

    /**
     * مسح كاش المهام
     */
    private function clearTasksCache()
    {
        Cache::forget('tasks_*');
        // يمكن تحسين هذا باستخدام tags إذا كان متاحاً
    }
    public function calendar()
    {
        try {
            // جلب جميع المشاريع للفلتر مرتبة أبجدياً
            $projects = Project::select('id', 'title')
                ->orderBy('title')
                ->get();

            // جلب جميع المستخدمين النشطين للفلتر
            $users = User::select('id', 'name', 'email')
                ->orderBy('name')
                ->get();

            // إحصائيات المهام مع تحسينات
            $tasksCount = Task::count();
            $pendingTasks = Task::where('status', 'pending')->count();
            $inProgressTasks = Task::where('status', 'in_progress')->count();
            $completedTasks = Task::where('status', 'completed')->count();

            // المهام المتأخرة (لم تكتمل وتاريخ الانتهاء قد مضى)
            $overdueTasks = Task::where('due_date', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count();

            // إضافة معلومات Debug
            Log::info('Calendar page loaded', [
                'tasks_count' => $tasksCount,
                'projects_count' => $projects->count(),
                'users_count' => $users->count()
            ]);

            return view('taskmanager::task.calendar', compact(
                'projects',
                'users',
                'tasksCount',
                'pendingTasks',
                'inProgressTasks',
                'completedTasks',
                'overdueTasks'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading calendar page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل صفحة التقويم');
        }
    }

    /**
     * جلب المهام للتقويم (API) مع فلترة وترتيب محسن
     */
    public function calendarEvents(Request $request)
    {
        try {
            // تسجيل البيانات المرسلة
            Log::info('Calendar events request', [
                'filters' => $request->all()
            ]);

            // إنشاء الاستعلام الأساسي مع العلاقات
            $query = Task::with([
                'project:id,title',
                'assignedUsers:id,name,email',
                'creator:id,name'
            ]);

            // تطبيق فلتر المشروع
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
                Log::info('Applied project filter', ['project_id' => $request->project_id]);
            }

            // تطبيق فلتر الحالة
            if ($request->filled('status')) {
                if ($request->status === 'overdue') {
                    // فلترة المهام المتأخرة
                    $query->where('due_date', '<', now())
                          ->whereNotIn('status', ['completed', 'cancelled']);
                    Log::info('Applied overdue filter');
                } else {
                    $query->where('status', $request->status);
                    Log::info('Applied status filter', ['status' => $request->status]);
                }
            }

            // تطبيق فلتر الأولوية
            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
                Log::info('Applied priority filter', ['priority' => $request->priority]);
            }

            // تطبيق فلتر المستخدم
            if ($request->filled('user_id')) {
                $query->whereHas('assignedUsers', function($q) use ($request) {
                    $q->where('users.id', $request->user_id);
                });
                Log::info('Applied user filter', ['user_id' => $request->user_id]);
            }

            // ترتيب النتائج حسب الأولوية ثم التاريخ
            $query->orderByRaw("
                CASE priority
                    WHEN 'high' THEN 1
                    WHEN 'medium' THEN 2
                    WHEN 'low' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('start_date')
            ->orderBy('created_at', 'desc');

            // جلب المهام
            $tasks = $query->get();

            Log::info('Tasks fetched', [
                'count' => $tasks->count(),
                'sample_task' => $tasks->first() ? [
                    'id' => $tasks->first()->id,
                    'title' => $tasks->first()->title,
                    'start_date' => $tasks->first()->start_date,
                    'due_date' => $tasks->first()->due_date,
                    'status' => $tasks->first()->status
                ] : null
            ]);

            // تحويل المهام لصيغة FullCalendar
            $events = $tasks->map(function($task) {
                // التحقق من كون المهمة متأخرة
                $isOverdue = $task->due_date &&
                            $task->due_date->isPast() &&
                            !in_array($task->status, ['completed', 'cancelled']);

                // تحديد اللون حسب الحالة
                $color = $this->getTaskColor($task->status, $isOverdue);

                // جمع أسماء المستخدمين المكلفين
                $assignedUsers = $task->assignedUsers ? $task->assignedUsers->pluck('name')->join('، ') : '';

                // تحديد تواريخ البداية والنهاية
                $startDate = null;
                $endDate = null;

                // أولوية لتاريخ البداية
                if ($task->start_date) {
                    $startDate = $task->start_date->format('Y-m-d');
                } elseif ($task->created_at) {
                    // إذا لم يكن هناك تاريخ بداية، استخدم تاريخ الإنشاء
                    $startDate = $task->created_at->format('Y-m-d');
                }

                // تاريخ النهاية
                if ($task->due_date) {
                    $endDate = $task->due_date->format('Y-m-d');
                }

                // تحديد عنوان المهمة مع مؤشرات
                $title = $task->title;
                if ($isOverdue) {
                    $title = '⚠️ ' . $title;
                }
                if ($task->priority === 'high') {
                    $title = '🔴 ' . $title;
                }

                $event = [
                    'id' => $task->id,
                    'title' => $title,
                    'start' => $startDate,
                    'end' => $endDate,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'description' => $task->description ?? '',
                        'status' => $task->status,
                        'priority' => $task->priority ?? 'medium',
                        'project_name' => optional($task->project)->title ?? optional($task->project)->name,
                        'project_color' => optional($task->project)->color,
                        'completion_percentage' => $task->completion_percentage ?? 0,
                        'assigned_users' => $assignedUsers,
                        'creator_name' => optional($task->creator)->name,
                        'is_overdue' => $isOverdue,
                        'created_at' => optional($task->created_at)->format('Y-m-d H:i'),
                        'updated_at' => optional($task->updated_at)->format('Y-m-d H:i')
                    ]
                ];

                return $event;
            })
            ->filter(function($event) {
                // إزالة المهام التي ليس لها تاريخ بداية
                return $event['start'] !== null;
            })
            ->values();

            Log::info('Events processed', [
                'total_tasks' => $tasks->count(),
                'valid_events' => $events->count(),
                'sample_event' => $events->first()
            ]);

            // إرجاع البيانات مباشرة كمصفوفة
            return response()->json($events->toArray());

        } catch (\Exception $e) {
            Log::error('Error loading calendar events', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'error' => true,
                'message' => 'حدث خطأ أثناء تحميل المهام',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * تحديد لون المهمة حسب الحالة
     */
    private function getTaskColor($status, $isOverdue = false)
    {
        if ($isOverdue) {
            return '#8e44ad'; // بنفسجي للمهام المتأخرة
        }

        return match($status) {
            'pending' => '#f39c12',        // برتقالي
            'in_progress' => '#3498db',    // أزرق
            'completed' => '#27ae60',      // أخضر
            'cancelled' => '#e74c3c',      // أحمر
            default => '#95a5a6'           // رمادي للحالات الأخرى
        };
    }

    /**
     * تحديث حالة المهمة (للسحب والإفلات)
     */
    public function updateTaskStatus(Request $request, $taskId)
    {
        try {
            $request->validate([
                'status' => 'nullable|in:pending,in_progress,completed,cancelled',
                'start_date' => 'nullable|date',
                'due_date' => 'nullable|date'
            ]);

            $task = Task::findOrFail($taskId);

            $updateData = [];

            if ($request->has('status')) {
                $updateData['status'] = $request->status;
            }

            if ($request->has('start_date')) {
                $updateData['start_date'] = $request->start_date ? Carbon::parse($request->start_date) : null;
            }

            if ($request->has('due_date')) {
                $updateData['due_date'] = $request->due_date ? Carbon::parse($request->due_date) : null;
            }

            $task->update($updateData);

            Log::info('Task updated successfully', [
                'task_id' => $taskId,
                'updates' => $updateData
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المهمة بنجاح',
                'task' => $task->load(['project', 'assignedUsers'])
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating task', [
                'task_id' => $taskId,
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحديث',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // دالة اختبار سريعة - يمكنك حذفها لاحقاً
    public function testTasks()
    {
        $tasks = Task::with(['project', 'assignedUsers'])
            ->take(5)
            ->get();

        return response()->json([
            'total_tasks' => Task::count(),
            'sample_tasks' => $tasks,
            'database_connection' => 'OK'
        ]);
    }

/**
 * إضافة ملف للمهمة (دالة مساعدة مفقودة)
 */
private function addTaskFile($task, $filename, $extension, $originalName, $size)
{
    // إذا كان لديك جدول منفصل للملفات
    $task->files()->create([
        'filename' => $filename,
        'original_name' => $originalName,
        'extension' => $extension,
        'size' => $size,
        'uploaded_by' => auth()->id(),
        'path' => 'tasks/' . $filename
    ]);

    // أو إذا كنت تستخدم JSON field
    $files = $task->files ?? [];
    $files[] = [
        'filename' => $filename,
        'original_name' => $originalName,
        'extension' => $extension,
        'size' => $size,
        'uploaded_by' => auth()->id(),
        'path' => 'tasks/' . $filename,
        'uploaded_at' => now()->toISOString()
    ];

    $task->update(['files' => $files]);
}

/**
 * عرض تفاصيل المهمة مع التعليقات
 * يجب إضافة هذه الدالة إلى TaskController
 */
public function show($id)
{
    try {
        $task = Task::with([
            'creator:id,name,email',
            'assignedUsers:id,name,email,avatar,job_title',
            'project:id,title',
            'subTasks' => function($query) {
                $query->select('id', 'parent_task_id', 'title', 'status', 'completion_percentage')
                      ->orderBy('created_at', 'asc');
            },
            'comments' => function($query) {
                $query->whereNull('parent_id')
                      ->with([
                          'user:id,name,email,avatar',
                          'replies.user:id,name,email,avatar'
                      ])
                      ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        // التحقق من الصلاحيات
        $this->authorizeTaskAction($task, 'view');

        // تحديث وقت آخر مشاهدة للمهمة
        $this->updateLastViewed($task);

        // إذا كان طلب AJAX، أرجع البيانات كـ JSON
        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'task' => $task,
                'comments_count' => $task->comments->sum(function($comment) {
                    return 1 + $comment->replies->count();
                })
            ]);
        }

        // إذا كان طلب عادي، أرجع الـ view
        return view('taskmanager::task.show', compact('task'));

    } catch (\Exception $e) {
        Log::error('خطأ في عرض تفاصيل المهمة: ' . $e->getMessage(), [
            'task_id' => $id,
            'user_id' => auth()->id()
        ]);

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل تفاصيل المهمة'
            ], 500);
        }

        return redirect()->route('tasks.index')
                        ->with('error', 'حدث خطأ في تحميل تفاصيل المهمة');
    }
}

/**
 * تحديث وقت آخر مشاهدة للمهمة
 */
private function updateLastViewed($task)
{
    $userId = auth()->id();

    // يمكن إضافة جدول لتتبع آخر مشاهدة
    // أو استخدام Cache للحفظ المؤقت
    Cache::put("task_last_viewed_{$task->id}_{$userId}", now(), 3600);
}
}
