<?php

namespace Modules\TaskManager\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\TestMail;
use App\Models\Log as ModelsLog;
use Illuminate\Support\Str;

class ProjectController extends Controller
{




public function index()
{
    // جلب قائمة مساحات العمل لاستخدامها في البحث
    $workspaces = Workspace::select('id', 'title')->orderBy('title')->get();

    // جلب قائمة منشئي المشاريع
    $creators = User::select('id', 'name')
                   ->whereIn('id', Project::distinct()->pluck('created_by'))
                   ->orderBy('name')
                   ->get();

    // جلب البيانات الأولية
    $projects = $this->getProjectsAnalytics(request());

    // إذا كان طلب AJAX، أرجع البيانات فقط
    if (request()->wantsJson()) {
        return $this->getProjectsAnalytics(request(), true);
    }

    return view('taskmanager::project.index', compact('projects', 'workspaces', 'creators'));
}

/**
 * جلب بيانات تحليلات المشاريع
 */
public function getProjectsAnalytics(Request $request, $ajax = false)
{
    $perPage = $request->get('per_page', 25);

    $query = Project::select([
            'id', 'workspace_id', 'title', 'description', 'status', 'priority',
            'budget', 'cost', 'start_date', 'end_date', 'actual_end_date',
            'progress_percentage', 'created_by', 'created_at', 'updated_at'
        ])
        ->with([
            'workspace:id,title',
            'creator:id,name,email',
            'users:id,name,email'
        ])
        ->withCount(['tasks', 'comments']); // إضافة عدد المهام والتعليقات

    // باقي الفلاتر كما هي...

    // البحث في العنوان
    if ($request->filled('title')) {
        $query->where('title', 'like', '%' . $request->title . '%');
    }

    // فلترة حسب مساحة العمل
    if ($request->filled('workspace_id')) {
        $query->where('workspace_id', $request->workspace_id);
    }

    // فلترة حسب الحالة
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // فلترة حسب الأولوية
    if ($request->filled('priority')) {
        $query->where('priority', $request->priority);
    }

    // فلترة حسب المنشئ
    if ($request->filled('created_by')) {
        $query->where('created_by', $request->created_by);
    }

    // فلترة بالتاريخ
    if ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    // فلترة بالميزانية
    if ($request->filled('budget_min')) {
        $query->where('budget', '>=', $request->budget_min);
    }

    if ($request->filled('budget_max')) {
        $query->where('budget', '<=', $request->budget_max);
    }

    // فلترة بنسبة الإكمال
    if ($request->filled('progress_min')) {
        $query->where('progress_percentage', '>=', $request->progress_min);
    }

    if ($request->filled('progress_max')) {
        $query->where('progress_percentage', '<=', $request->progress_max);
    }

    // ترتيب النتائج
    $sortBy = $request->get('sort_by', 'created_at');
    $sortDirection = $request->get('sort_direction', 'desc');
    $query->orderBy($sortBy, $sortDirection);

    $projects = $query->paginate($perPage);

    // إضافة إحصائيات لكل مشروع
    $projects->getCollection()->transform(function ($project) {
        // حساب إحصائيات المهام
        $totalTasks = $project->tasks_count; // من withCount
        $completedTasks = $project->tasks()->where('status', 'completed')->count();
        $overdueTasks = $project->tasks()->where('due_date', '<', now())->where('status', '!=', 'completed')->count();
        $inProgressTasks = $project->tasks()->where('status', 'in_progress')->count();

        // حساب إحصائيات الفريق
        $totalMembers = $project->users->count();

        $project->stats = [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'overdue_tasks' => $overdueTasks,
            'in_progress_tasks' => $inProgressTasks,
            'total_members' => $totalMembers,
            'total_comments' => $project->comments_count, // من withCount
            'task_completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
        ];

        return $project;
    });

    if ($ajax) {
        $chartData = $this->getProjectChartData($projects->getCollection());

        return response()->json([
            'success' => true,
            'html' => view('taskmanager::project.partials.table', [
                'projects' => $projects
            ])->render(),
            'pagination' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
                'from' => $projects->firstItem(),
                'to' => $projects->lastItem(),
            ],
            'chartData' => $chartData
        ]);
    }

    return $projects;
}

/**
 * جلب الإحصائيات العامة للمشاريع
 */
public function getProjectAnalyticsStats()
{
    try {
        // إحصائيات المشاريع
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'in_progress')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $onHoldProjects = Project::where('status', 'on_hold')->count();

        // إحصائيات الميزانية
        $totalBudget = Project::sum('budget') ?? 0;
        $totalSpent = Project::sum('cost') ?? 0;
        $averageProgress = Project::avg('progress_percentage') ?? 0;

        // إحصائيات المهام
        $totalTasks = DB::table('tasks')->count();
        $completedTasks = DB::table('tasks')->where('status', 'completed')->count();
        $overdueTasks = DB::table('tasks')->where('due_date', '<', now())->where('status', '!=', 'completed')->count();

        // معدل الإكمال العام
        $completionRate = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 1) : 0;

        // حساب معدلات النمو (مقارنة بالشهر الماضي)
        $lastMonth = now()->subMonth();

        $lastMonthProjects = Project::where('created_at', '>=', $lastMonth)->count();
        $lastMonthActive = Project::where('created_at', '>=', $lastMonth)->where('status', 'in_progress')->count();

        $projectsGrowth = $totalProjects > 0 ? round(($lastMonthProjects / $totalProjects) * 100, 1) : 0;
        $activeGrowth = $activeProjects > 0 ? round(($lastMonthActive / $activeProjects) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_projects' => $totalProjects,
                'active_projects' => $activeProjects,
                'completed_projects' => $completedProjects,
                'on_hold_projects' => $onHoldProjects,
                'total_budget' => number_format($totalBudget, 0),
                'total_spent' => number_format($totalSpent, 0),
                'completion_rate' => $completionRate,
                'average_progress' => round($averageProgress, 1),
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'overdue_tasks' => $overdueTasks,
                'projects_growth' => $projectsGrowth,
                'active_growth' => $activeGrowth,
                'completion_growth' => 0, // يمكن حسابه حسب متطلباتك
                'budget_growth' => 0, // يمكن حسابه حسب متطلباتك
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Project analytics stats error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء جلب الإحصائيات',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

/**
 * إعداد بيانات الرسوم البيانية للمشاريع
 */
private function getProjectChartData($projects)
{
    $statusCounts = [
        'new' => 0,
        'in_progress' => 0,
        'completed' => 0,
        'on_hold' => 0
    ];

    $priorityCounts = [
        'low' => 0,
        'medium' => 0,
        'high' => 0,
        'urgent' => 0
    ];

    $timelineData = [];

    foreach ($projects as $project) {
        // عد الحالات
        if (isset($statusCounts[$project->status])) {
            $statusCounts[$project->status]++;
        }

        // عد الأولويات
        if (isset($priorityCounts[$project->priority])) {
            $priorityCounts[$project->priority]++;
        }

        // بيانات الجدول الزمني
        $monthKey = $project->created_at->format('Y-m');
        if (!isset($timelineData[$monthKey])) {
            $timelineData[$monthKey] = [
                'month' => $project->created_at->format('M Y'),
                'projects' => 0,
                'budget' => 0
            ];
        }
        $timelineData[$monthKey]['projects']++;
        $timelineData[$monthKey]['budget'] += $project->budget ?? 0;
    }

    return [
        'status' => [
            'labels' => ['جديد', 'قيد التنفيذ', 'مكتمل', 'متوقف'],
            'data' => array_values($statusCounts)
        ],
        'priority' => [
            'labels' => ['منخفضة', 'متوسطة', 'عالية', 'عاجلة'],
            'data' => array_values($priorityCounts)
        ],
        'timeline' => [
            'labels' => array_column($timelineData, 'month'),
            'projects' => array_column($timelineData, 'projects'),
            'budget' => array_column($timelineData, 'budget')
        ]
    ];
}





/**
 * تصدير تحليلات جميع المشاريع
 */
public function exportAnalytics(Request $request)
{
    try {
        $projects = $this->getProjectsAnalytics($request)->items();

        $export = [];
        foreach ($projects as $project) {
            $export[] = [
                'عنوان المشروع' => $project->title,
                'الوصف' => $project->description ?? '--',
                'مساحة العمل' => $project->workspace->title ?? '--',
                'المنشئ' => $project->creator->name ?? '--',
                'الحالة' => $this->getStatusLabel($project->status),
                'الأولوية' => $this->getPriorityLabel($project->priority),
                'الميزانية' => number_format($project->budget ?? 0, 2),
                'المصروف' => number_format($project->cost ?? 0, 2),
                'نسبة الإكمال (%)' => $project->progress_percentage ?? 0,
                'إجمالي المهام' => $project->stats['total_tasks'],
                'المهام المكتملة' => $project->stats['completed_tasks'],
                'المهام المتأخرة' => $project->stats['overdue_tasks'],
                'عدد أعضاء الفريق' => $project->stats['total_members'],
                'تاريخ البداية' => $project->start_date ? $project->start_date->format('Y-m-d') : '--',
                'تاريخ النهاية' => $project->end_date ? $project->end_date->format('Y-m-d') : '--',
                'تاريخ الإنشاء' => $project->created_at->format('Y-m-d H:i'),
            ];
        }

        $filename = 'projects-analytics-' . now()->format('Y-m-d-H-i') . '.xlsx';

        return response()->json([
            'success' => true,
            'message' => 'تم تصدير البيانات بنجاح',
            'download_url' => route('projects.analytics.download', $filename)
        ]);

    } catch (\Exception $e) {
        Log::error('Export projects analytics error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء تصدير البيانات'
        ], 500);
    }
}

/**
 * تصدير تحليلات مشروع واحد
 */
public function exportSingleProject(Project $project)
{
    try {
        $stats = $this->getDetailedProjectStats($project);

        $export = [
            'معلومات المشروع' => [
                'العنوان' => $project->title,
                'الوصف' => $project->description ?? '--',
                'مساحة العمل' => $project->workspace->title ?? '--',
                'المنشئ' => $project->creator->name ?? '--',
                'الحالة' => $this->getStatusLabel($project->status),
                'الأولوية' => $this->getPriorityLabel($project->priority),
                'تاريخ الإنشاء' => $project->created_at->format('Y-m-d H:i'),
            ],
            'إحصائيات المهام' => $stats['tasks'],
            'توزيع الأولويات' => $stats['priorities'],
            'الميزانية' => $stats['budget'],
            'الجدول الزمني' => [
                'تاريخ البداية' => $stats['timeline']['start_date']->format('Y-m-d'),
                'تاريخ النهاية' => $stats['timeline']['end_date']->format('Y-m-d'),
                'إجمالي الأيام' => $stats['timeline']['total_days'],
                'الأيام المتبقية' => $stats['timeline']['remaining_days']
            ],
            'الفريق' => $stats['team'],
            'الأداء' => $stats['performance']
        ];

        $filename = 'project-' . $project->id . '-analytics-' . now()->format('Y-m-d-H-i') . '.xlsx';

        return response()->json([
            'success' => true,
            'message' => 'تم تصدير بيانات المشروع بنجاح',
            'download_url' => route('projects.analytics.download', $filename)
        ]);

    } catch (\Exception $e) {
        Log::error('Export single project error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء تصدير بيانات المشروع'
        ], 500);
    }
}

/**
 * دوال مساعدة للحصول على تسميات الحالة والأولوية
 */
private function getStatusLabel($status)
{
    $labels = [
        'new' => 'جديد',
        'in_progress' => 'قيد التنفيذ',
        'completed' => 'مكتمل',
        'on_hold' => 'متوقف'
    ];

    return $labels[$status] ?? $status;
}



    /**
     * API: جلب قائمة المشاريع مع Ajax
     */
    public function getProjects(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $query = Project::select([
                'id', 'workspace_id', 'title', 'status', 'priority',
                'budget', 'cost', 'start_date', 'end_date',
                'progress_percentage', 'created_by', 'created_at'
            ])
            ->with([
                'workspace:id,title',

                'users:id,name,email'
            ]);

        // فلترة حسب مساحة العمل
        if ($request->filled('workspace_id')) {
            $query->where('workspace_id', $request->workspace_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // البحث في العنوان
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // ترتيب النتائج
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $projects = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $projects->items(),
            'pagination' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
                'from' => $projects->firstItem(),
                'to' => $projects->lastItem(),
            ]
        ]);
    }

    /**
     * عرض صفحة إضافة مشروع (صفحة خالية - البيانات تحمل بـ Ajax)
     */
 public function create()
    {
        $workspaces = Workspace::all();
        $users = User::all();

        return view('taskmanager::project.create', compact('workspaces', 'users'));
    }


 public function store(Request $request)
{
    try {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'workspace_id' => 'required|exists:workspaces,id',
            'status' => 'required|in:new,in_progress,on_hold,completed',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id'
        ], [
            'title.required' => 'اسم المشروع مطلوب',
            'workspace_id.required' => 'مساحة العمل مطلوبة',
            'workspace_id.exists' => 'مساحة العمل المختارة غير موجودة',
            'start_date.required' => 'تاريخ البداية مطلوب',
            'end_date.required' => 'تاريخ النهاية مطلوب',
            'end_date.after' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'البيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        // بدء معاملة قاعدة البيانات
        DB::beginTransaction();

        // إنشاء المشروع
        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'workspace_id' => $request->workspace_id,
            'status' => $request->status ?? 'new',
            'priority' => $request->priority ?? 'medium',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'budget' => $request->budget ?? 0,
            'cost' => $request->cost ?? 0,
            'progress_percentage' => 0,
            'created_by' => Auth::id()
        ]);

        // إرفاق المستخدمين بالمشروع
        $insertData = [];

        // إضافة المدير
        $insertData[] = [
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'role' => 'manager',

            'created_at' => now(),
            'updated_at' => now()
        ];

        // إضافة أعضاء الفريق
        if ($request->has('team_members') && is_array($request->team_members)) {
            foreach ($request->team_members as $userId) {
                $userId = (int) $userId;

                // تجنب إضافة المدير مرة أخرى
                if ($userId !== Auth::id()) {
                    $insertData[] = [
                        'project_id' => $project->id,
                        'user_id' => $userId,
                        'role' => 'member',

                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
        }

        // إدراج البيانات في جدول project_user
        if (!empty($insertData)) {
            DB::table('project_user')->insert($insertData);
        }

        // تأكيد المعاملة
        DB::commit();

        // تحميل العلاقات للاستجابة
        $project->load('users');

        // إرجاع استجابة نجاح
        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المشروع بنجاح',
            'data' => [
                'project' => $project,
                'redirect_url' => route('projects.show', $project->id)
            ]
        ]);

    } catch (\Exception $e) {
        // التراجع عن المعاملة في حالة الخطأ
        DB::rollBack();

        // تسجيل الخطأ
        Log::error('خطأ في إنشاء المشروع: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'user_id' => Auth::id(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء إنشاء المشروع'
        ], 500);
    }
}

    /**
     * عرض نموذج تعديل مشروع
     */
    public function edit(Project $project)
    {
        $workspaces = Workspace::all();
        $users = User::all();

        return view('taskmanager::project.edit', compact('project', 'workspaces', 'users'));
    }

    /**
     * تحديث مشروع موجود
     */
   public function update(Request $request, Project $project)
{
    try {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'workspace_id' => 'required|exists:workspaces,id',
            'status' => 'required|in:new,in_progress,on_hold,completed',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'actual_end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id'
        ], [
            'title.required' => 'اسم المشروع مطلوب',
            'workspace_id.required' => 'مساحة العمل مطلوبة',
            'workspace_id.exists' => 'مساحة العمل المختارة غير موجودة',
            'start_date.required' => 'تاريخ البداية مطلوب',
            'end_date.required' => 'تاريخ النهاية مطلوب',
            'end_date.after' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
            'progress_percentage.max' => 'نسبة الإنجاز لا يمكن أن تتجاوز 100%'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'البيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        // بدء معاملة قاعدة البيانات
        DB::beginTransaction();

        // تحديث بيانات المشروع
        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'workspace_id' => $request->workspace_id,
            'status' => $request->status,
            'priority' => $request->priority,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'actual_end_date' => $request->actual_end_date,
            'budget' => $request->budget ?? 0,
            'cost' => $request->cost ?? 0,
            'progress_percentage' => $request->progress_percentage ?? 0
        ]);

        // تحديث أعضاء الفريق بنفس الطريقة الآمنة

        // 1. حذف الأعضاء الحاليين (ما عدا المدير)
        DB::table('project_user')
            ->where('project_id', $project->id)
            ->where('role', 'member')
            ->delete();

        // 2. إضافة الأعضاء الجدد باستخدام DB::table
        if ($request->has('team_members') && is_array($request->team_members)) {
            $insertData = [];

            foreach ($request->team_members as $userId) {
                $userId = (int) $userId;

                // تجنب إضافة المنشئ/المدير مرة أخرى
                if ($userId !== $project->created_by) {
                    $insertData[] = [
                        'project_id' => $project->id,
                        'user_id' => $userId,
                        'role' => 'member',

                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            // إدراج البيانات الجديدة
            if (!empty($insertData)) {
                DB::table('project_user')->insert($insertData);
            }
        }

        // تأكيد المعاملة
        DB::commit();

        // تحميل العلاقات للاستجابة
        $project->load('users');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المشروع بنجاح',
            'data' => [
                'project' => $project,
                'redirect_url' => route('projects.show', $project->id)
            ]
        ]);

    } catch (\Exception $e) {
        // التراجع عن المعاملة في حالة الخطأ
        DB::rollBack();

        // تسجيل الخطأ
        Log::error('خطأ في تحديث المشروع: ' . $e->getMessage(), [
            'project_id' => $project->id,
            'request_data' => $request->all(),
            'user_id' => Auth::id(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء تحديث المشروع'
        ], 500);
    }
}
    /**
     * API: حذف المشروع
     */
    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المشروع بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المشروع',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * API: إضافة عضو جديد للمشروع
     */
    public function addMember(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:manager,member,viewer',
        ]);

        // التحقق من عدم وجود المستخدم مسبقاً
        if ($project->users()->where('user_id', $request->user_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم موجود بالفعل في المشروع',
            ], 422);
        }

        $project->users()->attach($request->user_id, [
            'role' => $request->role,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة العضو بنجاح',
        ]);
    }

    /**
     * API: إزالة عضو من المشروع
     */
    public function removeMember(Project $project, User $user)
    {
        // التحقق من وجود المستخدم في المشروع
        if (!$project->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موجود في المشروع',
            ], 422);
        }

        $project->users()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'تم إزالة العضو بنجاح',
        ]);
    }


/**
 * API: جلب آخر التعليقات
 */
public function getRecentComments(Project $project)
{
    try {
        $comments = $project->comments()
            ->select(['id', 'content', 'user_id', 'created_at'])
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
    } catch (\Exception $e) {
        Log::error('Error fetching recent comments: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في جلب التعليقات'
        ], 500);
    }
}



    /**
     * API: جلب تفاصيل مشروع واحد
     */
    public function getProject(Project $project)
    {
        try {
            $project->load([
                'workspace:id,title',
                'creator:id,name,email',
                'users:id,name,email'
            ]);

            return response()->json([
                'success' => true,
                'data' => $project
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching project: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب تفاصيل المشروع'
            ], 500);
        }
    }

    /**
     * API: جلب إحصائيات مشروع
     */
    public function getProjectStats(Project $project)
    {
        try {
            // إحصائيات المهام
            $totalTasks = $project->tasks()->count();
            $completedTasks = $project->tasks()->where('status', 'completed')->count();
            $inProgressTasks = $project->tasks()->where('status', 'in_progress')->count();
            $pendingTasks = $project->tasks()->where('status', 'pending')->count();
            $overdueTasks = $project->tasks()->where('due_date', '<', now())
                                  ->where('status', '!=', 'completed')->count();

            // إحصائيات الميزانية
            $totalBudget = $project->budget ?? 0;
            $totalTaskBudget = $project->tasks()->sum('budget') ?? 0;
            $remainingBudget = $totalBudget - $totalTaskBudget;

            // إحصائيات الفريق
            $totalMembers = $project->users()->count();

            // حساب معدل الإكمال
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

            $stats = [
                'tasks' => [
                    'total' => $totalTasks,
                    'completed' => $completedTasks,
                    'in_progress' => $inProgressTasks,
                    'pending' => $pendingTasks,
                    'overdue' => $overdueTasks,
                    'completion_rate' => $completionRate
                ],
                'budget' => [
                    'total' => $totalBudget,
                    'spent' => $totalTaskBudget,
                    'remaining' => $remainingBudget,
                    'usage_percentage' => $totalBudget > 0 ? round(($totalTaskBudget / $totalBudget) * 100, 1) : 0
                ],
                'progress' => $project->progress_percentage ?? 0,
                'team' => [
                    'total_members' => $totalMembers
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching project stats: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب إحصائيات المشروع'
            ], 500);
        }
    }

    /**
     * API: جلب مهام المشروع
     */

    /**
     * API: جلب بيانات التعديل
     */
    public function getEditData(Project $project)
    {
        try {
            $project->load([
                'workspace:id,title',
                'users:id,name,email'
            ]);

            $workspaces = Workspace::select('id', 'title')->orderBy('title')->get();
            $availableUsers = User::select('id', 'name', 'email')->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'project' => $project,
                    'workspaces' => $workspaces,
                    'available_users' => $availableUsers
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching edit data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب بيانات التعديل'
            ], 500);
        }
    }

    /**
     * API: تحديث دور عضو في الفريق
     */
    public function updateMemberRole(Project $project, User $user, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role' => 'required|in:manager,member,viewer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'البيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            // التحقق من وجود المستخدم في المشروع
            if (!$project->users()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير موجود في هذا المشروع'
                ], 404);
            }

            // تحديث الدور
            $project->users()->updateExistingPivot($user->id, [
                'role' => $request->role,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث دور العضو بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating member role: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحديث دور العضو'
            ], 500);
        }
    }

    /**
     * API: البحث السريع في المشاريع
     */
    public function quickSearch(Request $request)
    {
        try {
            $query = $request->get('query', '');
            $limit = $request->get('limit', 10);

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $projects = Project::select(['id', 'title', 'status', 'progress_percentage'])
                ->with('workspace:id,title')
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $projects
            ]);

        } catch (\Exception $e) {
            Log::error('Error in quick search: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في البحث'
            ], 500);
        }
    }

    /**
     * API: تصدير المشروع
     */
    public function export(Project $project, $format)
    {
        try {
            if (!in_array($format, ['pdf', 'excel', 'csv'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'نوع التصدير غير مدعوم'
                ], 400);
            }

            // جلب بيانات المشروع مع العلاقات
            $project->load([
                'workspace',
                'creator',
                'users',
                'tasks' => function ($query) {
                    $query->with(['creator', 'assignedUsers']);
                },
                'comments' => function ($query) {
                    $query->with('user')->whereNull('parent_id');
                }
            ]);

            // تحضير البيانات للتصدير
            $exportData = [
                'project' => [
                    'title' => $project->title,
                    'description' => $project->description,
                    'workspace' => $project->workspace->title,
                    'status' => $this->getStatusLabel($project->status),
                    'priority' => $this->getPriorityLabel($project->priority),
                    'budget' => $project->budget,
                    'progress_percentage' => $project->progress_percentage,
                    'start_date' => $project->start_date->format('Y-m-d'),
                    'end_date' => $project->end_date->format('Y-m-d'),
                    'created_by' => $project->creator->name,
                    'created_at' => $project->created_at->format('Y-m-d H:i')
                ],
                'team' => $project->users->map(function ($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $this->getRoleLabel($user->pivot->role)
                    ];
                }),
                'tasks' => $project->tasks->map(function ($task) {
                    return [
                        'title' => $task->title,
                        'description' => $task->description,
                        'status' => $this->getTaskStatusLabel($task->status),
                        'priority' => $this->getPriorityLabel($task->priority),
                        'budget' => $task->budget,
                        'completion_percentage' => $task->completion_percentage,
                        'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
                        'assigned_users' => $task->assignedUsers->pluck('name', 'email')->map(function ($last, $first) {
                            return $first . ' ' . $last;
                        })->values()->toArray(),
                        'is_overdue' => $task->isOverdue()
                    ];
                }),
                'comments' => $project->comments->map(function ($comment) {
                    return [
                        'author' => $comment->user->name ,
                        'content' => $comment->content,
                        'created_at' => $comment->created_at->format('Y-m-d H:i'),
                        'replies_count' => $comment->replies()->count()
                    ];
                })
            ];

            // هنا يمكن إضافة المنطق الفعلي للتصدير حسب النوع
            // مؤقتاً سنرجع البيانات JSON
            $filename = 'project_' . $project->id . '_export_' . now()->format('Y_m_d_H_i') . '.' . $format;

            return response()->json([
                'success' => true,
                'message' => 'تم تحضير ملف التصدير بنجاح',
                'data' => $exportData,
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            Log::error('Error exporting project: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير المشروع'
            ], 500);
        }
    }


    private function getTaskStatusLabel($status)
    {
        $labels = [
            'pending' => 'معلق',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي'
        ];

        return $labels[$status] ?? $status;
    }

    private function getPriorityLabel($priority)
    {
        $labels = [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'urgent' => 'عاجلة'
        ];

        return $labels[$priority] ?? $priority;
    }

    private function getRoleLabel($role)
    {
        $labels = [
            'manager' => 'مدير',
            'member' => 'عضو',
            'viewer' => 'مشاهد'
        ];

        return $labels[$role] ?? $role;
    }

    /**
     * API: جلب تفاصيل المشروع
     */
    public function getDetails(Project $project)
    {
        try {
            $project->load([
                'workspace:id,title',
                'creator:id,name,email',
                'users:id,name,email'
            ]);

            return response()->json([
                'success' => true,
                'data' => $project
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching project details: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب تفاصيل المشروع'
            ], 500);
        }
    }

    /**
     * API: جلب إحصائيات المشروع
     */
    public function getStats(Project $project)
    {
        try {
            // إحصائيات المهام
            $totalTasks = $project->tasks()->count();
            $completedTasks = $project->tasks()->where('status', 'completed')->count();
            $inProgressTasks = $project->tasks()->where('status', 'in_progress')->count();
            $overdueTasks = $project->tasks()
                ->where('due_date', '<', now())
                ->where('status', '!=', 'completed')
                ->count();

            // الميزانية
            $totalBudget = $project->budget ?? 0;
            $totalSpent = $project->cost ?? 0;

            // نسبة الإنجاز
            $progress = $project->progress_percentage ?? 0;

            // عدد أعضاء الفريق
            $teamMembers = $project->users()->count();

            $stats = [
                'tasks' => [
                    'total' => $totalTasks,
                    'completed' => $completedTasks,
                    'in_progress' => $inProgressTasks,
                    'overdue' => $overdueTasks,
                    'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0
                ],
                'budget' => [
                    'total' => $totalBudget,
                    'spent' => $totalSpent,
                    'remaining' => $totalBudget - $totalSpent,
                    'usage_percentage' => $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 1) : 0
                ],
                'progress' => $progress,
                'team' => [
                    'total_members' => $teamMembers
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching project stats: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب إحصائيات المشروع'
            ], 500);
        }
    }

    /**
     * API: جلب مهام المشروع مع الموظفين المكلفين
     */
    public function getTasks(Project $project)
    {
        try {
            $tasks = $project->tasks()
                ->select([
                    'id', 'title', 'description', 'status', 'priority',
                    'start_date', 'due_date', 'completion_percentage','budget',
                    'created_at', 'updated_at'
                ])
                ->with(['assignedUsers:id,name,email'])
                ->orderBy('created_at', 'desc')
                ->get();

            // تنسيق البيانات
            $tasks->transform(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'start_date' => $task->start_date,
                    'due_date' => $task->due_date,
                    'completion_percentage' => $task->completion_percentage ?? 0,
                    'cost' => $task->cost,
                    'assigned_users' => $task->assignedUsers->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email
                        ];
                    }),
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $tasks
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching project tasks: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب مهام المشروع'
            ], 500);
        }
    }

    /**
     * API: جلب أعضاء فريق المشروع
     */
    public function getTeamMembers(Project $project)
    {
        try {
            $members = $project->users()
                ->select(['users.id', 'users.name', 'users.email'])
                ->withPivot('role', 'created_at')
                ->orderBy('pivot_role', 'asc')
                ->orderBy('users.name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $members
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching team members: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب أعضاء الفريق'
            ], 500);
        }
    }

    /**
     * API: تحديث نسبة إنجاز المشروع
     */
    public function updateProgress(Request $request, Project $project)
    {
        try {
            $validator = Validator::make($request->all(), [
                'progress_percentage' => 'required|integer|min:0|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'البيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $project->update([
                'progress_percentage' => $request->progress_percentage
            ]);

            // إذا وصلت النسبة إلى 100%، غيّر الحالة إلى مكتمل
            if ($request->progress_percentage == 100) {
                $project->update([
                    'status' => 'completed',
                    'actual_end_date' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث نسبة الإنجاز بنجاح',
                'data' => [
                    'progress_percentage' => $project->progress_percentage,
                    'status' => $project->status
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating progress: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحديث نسبة الإنجاز'
            ], 500);
        }
    }

    /**
     * API: حذف المشروع
     */
    public function apiDestroy(Project $project)
    {
        try {
            // التحقق من الصلاحيات
            if (Auth::id() !== $project->created_by) {
                return response()->json([
                    'success' => false,
                    'message' => 'ليس لديك صلاحية حذف هذا المشروع'
                ], 403);
            }

            DB::beginTransaction();

            // حذف العلاقات
            $project->users()->detach();
            $project->tasks()->delete();
            $project->comments()->delete();

            // حذف المشروع
            $project->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المشروع بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting project: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في حذف المشروع'
            ], 500);
        }
    }

    /**
     * عرض تحليلات المشروع المفصلة
     */
    public function detailedAnalytics(Project $project)
    {
        $project->load([
            'workspace:id,title',
            'creator:id,name,email',
            'users:id,name,email',
            'tasks:id,title,status,priority,due_date,completion_percentage,created_at'
        ]);

        // إحصائيات مفصلة
        $stats = $this->getDetailedProjectStats($project);

        return view('taskmanager::project.detailed', compact('project', 'stats'));
    }

    /**
     * حساب إحصائيات مفصلة للمشروع
     */
    private function getDetailedProjectStats(Project $project)
    {
        $tasks = $project->tasks;

        $stats = [
            'tasks' => [
                'total' => $tasks->count(),
                'completed' => $tasks->where('status', 'completed')->count(),
                'in_progress' => $tasks->where('status', 'in_progress')->count(),
                'pending' => $tasks->where('status', 'pending')->count(),
                'overdue' => $tasks->where('due_date', '<', now())->where('status', '!=', 'completed')->count(),
            ],
            'priorities' => [
                'urgent' => $tasks->where('priority', 'urgent')->count(),
                'high' => $tasks->where('priority', 'high')->count(),
                'medium' => $tasks->where('priority', 'medium')->count(),
                'low' => $tasks->where('priority', 'low')->count(),
            ],
            'budget' => [
                'total' => $project->budget ?? 0,
                'spent' => $project->cost ?? 0,
                'remaining' => ($project->budget ?? 0) - ($project->cost ?? 0),
                'usage_percentage' => $project->budget > 0 ? round((($project->cost ?? 0) / $project->budget) * 100, 1) : 0
            ],
            'timeline' => [
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
                'actual_end_date' => $project->actual_end_date,
                'total_days' => $project->start_date->diffInDays($project->end_date),
                'passed_days' => $project->start_date->diffInDays(now()),
                'remaining_days' => now()->diffInDays($project->end_date, false)
            ],
            'team' => [
                'total_members' => $project->users->count(),
                'managers' => $project->users->where('pivot.role', 'manager')->count(),
                'members' => $project->users->where('pivot.role', 'member')->count()
            ],
            'performance' => [
                'progress_percentage' => $project->progress_percentage ?? 0,
                'average_task_progress' => $tasks->count() > 0 ? round($tasks->avg('completion_percentage'), 1) : 0,
                'on_time_performance' => $this->calculateOnTimePerformance($project)
            ]
        ];

        // إحصائيات شهرية
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthTasks = $tasks->filter(function ($task) use ($month) {
                return $task->created_at->format('Y-m') === $month->format('Y-m');
            });

            $monthlyStats[] = [
                'month' => $month->format('M Y'),
                'tasks_created' => $monthTasks->count(),
                'tasks_completed' => $monthTasks->where('status', 'completed')->count()
            ];
        }
        $stats['monthly_performance'] = $monthlyStats;

        return $stats;
    }

    /**
     * حساب أداء الالتزام بالمواعيد
     */
    private function calculateOnTimePerformance(Project $project)
    {
        $completedTasks = $project->tasks()->where('status', 'completed')->get();

        if ($completedTasks->count() === 0) {
            return 0;
        }

        $onTimeTasks = $completedTasks->filter(function ($task) {
            return $task->completed_date && $task->due_date &&
                   \Carbon\Carbon::parse($task->completed_date)->lte(\Carbon\Carbon::parse($task->due_date));
        })->count();

        return round(($onTimeTasks / $completedTasks->count()) * 100, 1);
    }
 public function show(Project $project)
    {
         $projects = Project::all();
          $users = User::where('role', 'manager')->get();
        // تحميل العلاقات الأساسية
        $project->load([
            'workspace:id,title',
            'creator:id,name,email',
            'users:id,name,email',
            'tasks' => function($query) {
                $query->select([
                    'id', 'project_id', 'title', 'description', 'status', 'priority',
                    'start_date', 'due_date', 'completion_percentage',
                    'created_by', 'created_at', 'updated_at'
                ])->with([
                    'creator:id,name,email',
                    'assignedUsers:id,name,email'
                ])->orderBy('created_at', 'desc');
            },
            'comments' => function($query) {
                $query->whereNull('parent_id')
                      ->with(['user:id,name,email', 'replies.user:id,name,email'])
                      ->orderBy('created_at', 'desc')
                      ->limit(10);
            }
        ]);

        // حساب الإحصائيات
        $stats = $this->calculateProjectStats($project);

        return view('taskmanager::project.show', compact('users','projects', 'project', 'stats'));
    }

    /**
     * API: جلب تفاصيل المشروع مع المهام والتعليقات
     */
    public function getProjectDetails(Project $project)
    {
        try {
            // تحميل العلاقات
            $project->load([
                'workspace:id,title',
                'creator:id,name,email',
                'users:id,name,email',
                'tasks' => function($query) {
                    $query->with([
                        'creator:id,name,email',
                        'assignedUsers:id,name,email'
                    ])->orderBy('priority', 'desc')->orderBy('created_at', 'desc');
                }
            ]);

            // حساب الإحصائيات
            $stats = $this->calculateProjectStats($project);

            return response()->json([
                'success' => true,
                'data' => [
                    'project' => $project,
                    'stats' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching project details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب تفاصيل المشروع'
            ], 500);
        }
    }

    /**
     * API: جلب مهام المشروع مع التفاصيل
     */
    public function getProjectTasks(Project $project, Request $request)
    {
        try {
            $perPage = $request->get('per_page', 20);
            $status = $request->get('status');
            $priority = $request->get('priority');
            $search = $request->get('search');

            $query = $project->tasks()
                ->select([
                    'id', 'title', 'description', 'status', 'priority',
                    'start_date', 'due_date', 'completion_percentage',
                    'created_by', 'parent_task_id', 'created_at', 'updated_at'
                ])
                ->with([
                    'creator:id,name,email',
                    'assignedUsers:id,name,email',
                    'comments' => function($q) {
                        $q->whereNull('parent_id')->limit(3);
                    }
                ]);

            // فلترة حسب الحالة
            if ($status) {
                $query->where('status', $status);
            }

            // فلترة حسب الأولوية
            if ($priority) {
                $query->where('priority', $priority);
            }

            // البحث
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $tasks = $query->orderBy('priority', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);

            // إضافة معلومات إضافية لكل مهمة
            $tasks->getCollection()->transform(function ($task) {
                $task->is_overdue = $task->due_date && $task->due_date < now() && $task->status !== 'completed';
                $task->days_remaining = $task->due_date ? now()->diffInDays($task->due_date, false) : null;
                $task->comments_count = $task->comments()->count();
                $task->subtasks_count = Task::where('parent_task_id', $task->id)->count();

                return $task;
            });

            return response()->json([
                'success' => true,
                'data' => $tasks->items(),
                'pagination' => [
                    'current_page' => $tasks->currentPage(),
                    'last_page' => $tasks->lastPage(),
                    'per_page' => $tasks->perPage(),
                    'total' => $tasks->total(),
                    'from' => $tasks->firstItem(),
                    'to' => $tasks->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching project tasks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب مهام المشروع'
            ], 500);
        }
    }

    /**
     * API: جلب تعليقات المشروع
     */
    public function getProjectComments(Project $project, Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);

            $comments = $project->comments()
                ->whereNull('parent_id') // التعليقات الرئيسية فقط
                ->with([
                    'user:id,name,email',
                    'replies' => function($query) {
                        $query->with('user:id,name,email')
                              ->orderBy('created_at', 'asc');
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // إضافة معلومات إضافية
            $comments->getCollection()->transform(function ($comment) {
                $comment->time_ago = $comment->created_at->diffForHumans();
                $comment->replies_count = $comment->replies->count();

                // تنسيق الردود
                $comment->replies->transform(function($reply) {
                    $reply->time_ago = $reply->created_at->diffForHumans();
                    return $reply;
                });

                return $comment;
            });

            return response()->json([
                'success' => true,
                'data' => $comments->items(),
                'pagination' => [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                    'from' => $comments->firstItem(),
                    'to' => $comments->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching project comments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب تعليقات المشروع'
            ], 500);
        }
    }

    /**
     * API: إضافة تعليق للمشروع
     */
    public function addComment(Request $request, Project $project)
    {
        try {
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|min:3|max:1000',
                'parent_id' => 'nullable|exists:comments,id'
            ], [
                'content.required' => 'محتوى التعليق مطلوب',
                'content.min' => 'يجب أن يكون التعليق 3 أحرف على الأقل',
                'content.max' => 'التعليق طويل جداً',
                'parent_id.exists' => 'التعليق المرجعي غير موجود'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'البيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            // إنشاء التعليق
            $comment = $project->comments()->create([
                'content' => $request->content,
                'user_id' => Auth::id(),
                'parent_id' => $request->parent_id
            ]);

            // تحميل بيانات المستخدم
            $comment->load('user:id,name,email');

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة التعليق بنجاح',
                'data' => $comment
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding comment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إضافة التعليق'
            ], 500);
        }
    }

    /**
     * API: تحديث تعليق
     */
    public function updateComment(Request $request, Project $project, Comment $comment)
    {
        try {
            // التحقق من الصلاحية
            if ($comment->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ليس لديك صلاحية تعديل هذا التعليق'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'content' => 'required|string|min:3|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $comment->update([
                'content' => $request->content
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث التعليق بنجاح',
                'data' => $comment
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating comment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحديث التعليق'
            ], 500);
        }
    }

    /**
     * API: حذف تعليق
     */
    public function deleteComment(Project $project, Comment $comment)
    {
        try {
            // التحقق من الصلاحية
            if ($comment->user_id !== Auth::id() && $project->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ليس لديك صلاحية حذف هذا التعليق'
                ], 403);
            }

            // حذف الردود أولاً
            $comment->replies()->delete();

            // حذف التعليق
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف التعليق بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting comment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في حذف التعليق'
            ], 500);
        }
    }

    /**
     * حساب إحصائيات المشروع
     */
    private function calculateProjectStats(Project $project)
    {
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', 'completed')->count();
        $inProgressTasks = $project->tasks()->where('status', 'in_progress')->count();
        $pendingTasks = $project->tasks()->where('status', 'pending')->count();
        $overdueTasks = $project->tasks()
            ->where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        // إحصائيات الأولوية
        $urgentTasks = $project->tasks()->where('priority', 'urgent')->count();
        $highTasks = $project->tasks()->where('priority', 'high')->count();
        $mediumTasks = $project->tasks()->where('priority', 'medium')->count();
        $lowTasks = $project->tasks()->where('priority', 'low')->count();

        // إحصائيات الميزانية
        $totalBudget = $project->budget ?? 0;
        $totalSpent = $project->cost ?? 0;
        $tasksSpent = $project->tasks()->sum('budget') ?? 0;

        // إحصائيات التعليقات
        $totalComments = $project->comments()->count();
        $recentComments = $project->comments()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // إحصائيات الفريق
        $totalMembers = $project->users()->count();

        // معدل الإكمال
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
        $averageTaskProgress = $totalTasks > 0 ? round($project->tasks()->avg('completion_percentage'), 1) : 0;

        return [
            'tasks' => [
                'total' => $totalTasks,
                'completed' => $completedTasks,
                'in_progress' => $inProgressTasks,
                'pending' => $pendingTasks,
                'overdue' => $overdueTasks,
                'completion_rate' => $completionRate,
                'average_progress' => $averageTaskProgress
            ],
            'priorities' => [
                'urgent' => $urgentTasks,
                'high' => $highTasks,
                'medium' => $mediumTasks,
                'low' => $lowTasks
            ],
            'budget' => [
                'total' => $totalBudget,
                'spent' => $totalSpent,
                'tasks_spent' => $tasksSpent,
                'remaining' => $totalBudget - $totalSpent,
                'usage_percentage' => $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 1) : 0
            ],
            'comments' => [
                'total' => $totalComments,
                'recent' => $recentComments
            ],
            'team' => [
                'total_members' => $totalMembers
            ],
            'progress' => $project->progress_percentage ?? 0
        ];
    }
public function inviteMember(Request $request, $projectId)
{
    try {
        $request->validate([
            'email' => 'required|email|max:255',
            'role' => 'required|in:manager,member,viewer',
            'invite_message' => 'nullable|string|max:500',
        ]);

        $project = DB::table('projects')->where('id', $projectId)->first();
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'المشروع غير موجود'
            ], 404);
        }

        // التحقق من الصلاحيات (مدير المشروع أو مدير المساحة)
        $hasPermission = DB::table('project_user')
            ->where('project_id', $projectId)
            ->where('user_id', Auth::id())
            ->where('role', 'manager')
            ->exists();

        $isWorkspaceAdmin = DB::table('workspaces')
            ->where('id', $project->workspace_id)
            ->where('admin_id', Auth::id())
            ->exists();

        if (!$hasPermission && !$isWorkspaceAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'غير مخول لدعوة أعضاء في هذا المشروع'
            ], 403);
        }

        // التحقق إذا كان المستخدم موجود في النظام
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            // إذا كان المستخدم موجود، التحقق من عدم وجوده في المشروع
            $alreadyMember = DB::table('project_user')
                ->where('project_id', $projectId)
                ->where('user_id', $existingUser->id)
                ->exists();

            if ($alreadyMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا المستخدم عضو بالفعل في المشروع'
                ], 409);
            }

            // إضافة المستخدم الموجود مباشرة
            DB::table('project_user')->insert([
                'project_id' => $projectId,
                'user_id' => $existingUser->id,
                'role' => $request->role,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // إرسال إشعار للمستخدم الموجود باستخدام TestMail
            try {
                $this->sendExistingUserNotificationWithTestMail($existingUser, $project, $request->role);
            } catch (\Exception $e) {
                Log::warning('Failed to send notification to existing user', [
                    'error' => $e->getMessage(),
                    'user_id' => $existingUser->id,
                    'project_id' => $projectId
                ]);
                // لا نوقف العملية إذا فشل الإشعار
            }

            return response()->json([
                'success' => true,
                'message' => "تمت إضافة {$existingUser->name} للمشروع بنجاح"
            ]);
        }

        // التحقق من وجود دعوة معلقة في جدول project_user
        $existingInvite = DB::table('project_user')
            ->where('project_id', $projectId)
            ->where('email', $request->email)
            ->where('status', 'pending')
            ->first();

        if ($existingInvite) {
            // تحديث الدعوة الموجودة بدلاً من إنشاء واحدة جديدة
            DB::table('project_user')
                ->where('id', $existingInvite->id)
                ->update([
                    'role' => $request->role,
                    'invite_message' => $request->invite_message,
                    'invited_by' => Auth::id(),
                    'updated_at' => now(),
                    'expires_at' => now()->addDays(7), // تمديد انتهاء الصلاحية
                ]);

            // إعادة إرسال الدعوة باستخدام TestMail
            try {
                $this->resendProjectInviteWithTestMail($existingInvite->id, $request->invite_message);

                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث وإعادة إرسال دعوة المشروع إلى ' . $request->email
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to resend project invite', [
                    'error' => $e->getMessage(),
                    'invite_id' => $existingInvite->id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'تم تحديث الدعوة ولكن فشل في إرسال البريد الإلكتروني'
                ], 500);
            }
        }

        // إنشاء دعوة جديدة
        try {
            $this->createProjectInviteWithTestMail(
                $request->email,
                $projectId,
                $request->role,
                $request->invite_message
            );

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال دعوة المشروع بنجاح إلى ' . $request->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create project invite', [
                'error' => $e->getMessage(),
                'project_id' => $projectId,
                'email' => $request->email
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في إنشاء الدعوة: ' . $e->getMessage()
            ], 500);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'خطأ في البيانات المدخلة',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Project invite error: ' . $e->getMessage(), [
            'project_id' => $projectId,
            'email' => $request->email ?? 'N/A',
            'user_id' => Auth::id(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ غير متوقع أثناء معالجة الدعوة'
        ], 500);
    }
}

/**
 * إرسال إشعار للمستخدم الموجود باستخدام TestMail (نفس طريقة الموظفين)
 */
private function sendExistingUserNotificationWithTestMail($user, $project, $role)
{
    $roleText = match($role) {
        'manager' => 'مدير',
        'member' => 'عضو',
        'viewer' => 'مشاهد',
        default => 'عضو'
    };

    // إعداد بيانات البريد بنفس طريقة الموظفين
    $details = [
        'name' => $user->name,
        'email' => $user->email,
        'project_title' => $project->title,
        'role' => $roleText,
        'type' => 'project_notification', // لتمييز نوع البريد
        'message' => "تم إضافتك إلى مشروع: {$project->title} بدور {$roleText}"
    ];

    // إرسال البريد باستخدام TestMail
    Mail::to($user->email)->send(new TestMail($details));

    // تسجيل العملية في اللوج
    ModelsLog::create([
        'type' => 'project_log',
        'type_id' => $project->id,
        'type_log' => 'log',
        'description' => 'تم إضافة المستخدم **' . $user->name . '** للمشروع **' . $project->title . '**',
        'created_by' => auth()->id(),
    ]);
}

/**
 * إنشاء دعوة مشروع جديدة باستخدام TestMail
 */
private function createProjectInviteWithTestMail($email, $projectId, $role, $message = null)
{
    // التحقق من عدم وجود دعوة معلقة
    $existingInvite = DB::table('project_user')
        ->where('project_id', $projectId)
        ->where('email', $email)
        ->where('status', 'pending')
        ->where('expires_at', '>', now())
        ->first();

    if ($existingInvite) {
        throw new \Exception('توجد دعوة معلقة لهذا البريد الإلكتروني');
    }

    // إنشاء رمز دعوة فريد
    $inviteToken = Str::random(64);
    $expiresAt = now()->addDays(7);

    // توليد كلمة مرور مؤقتة
    $tempPassword = $this->generateRandomPassword();

    // إدراج الدعوة
    $inviteId = DB::table('project_user')->insertGetId([
        'project_id' => $projectId,
        'email' => $email,
        'role' => $role,
        'status' => 'pending',
        'invite_token' => $inviteToken,
        'invited_at' => now(),
        'expires_at' => $expiresAt,
        'invited_by' => Auth::id(),
        'invite_message' => $message,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // إرسال بريد الدعوة باستخدام TestMail
    $this->sendProjectInviteEmailWithTestMail($email, $projectId, $inviteToken, $role, $message, $tempPassword);

    // تسجيل العملية في اللوج
    $project = DB::table('projects')->where('id', $projectId)->first();
    ModelsLog::create([
        'type' => 'project_log',
        'type_id' => $projectId,
        'type_log' => 'log',
        'description' => 'تم إرسال دعوة للمشروع **' . $project->title . '** إلى **' . $email . '**',
        'created_by' => auth()->id(),
    ]);

    return $inviteId;
}

/**
 * إرسال بريد دعوة المشروع باستخدام TestMail
 */
private function sendProjectInviteEmailWithTestMail($email, $projectId, $token, $role, $message = null, $tempPassword)
{
    $project = DB::table('projects')
        ->join('workspaces', 'projects.workspace_id', '=', 'workspaces.id')
        ->select('projects.*', 'workspaces.title as workspace_title')
        ->where('projects.id', $projectId)
        ->first();

    $inviterName = Auth::user()->name;
    $acceptUrl = url("/projects/invite/{$token}/accept");

    $roleText = match($role) {
        'manager' => 'مدير',
        'member' => 'عضو',
        'viewer' => 'مشاهد',
        default => 'عضو'
    };

    // إعداد بيانات البريد بنفس طريقة الموظفين
    $details = [
        'name' => 'مستخدم جديد',
        'email' => $email,
        'password' => $tempPassword,
        'project_title' => $project->title,
        'workspace_title' => $project->workspace_title,
        'role' => $roleText,
        'inviter_name' => $inviterName,
        'accept_url' => $acceptUrl,
        'invite_message' => $message,
        'type' => 'project_invite', // لتمييز نوع البريد
        'expires_at' => now()->addDays(7)->format('Y-m-d H:i')
    ];

    // إرسال البريد باستخدام TestMail
    Mail::to($email)->send(new TestMail($details));
}

/**
 * إعادة إرسال دعوة المشروع باستخدام TestMail
 */
private function resendProjectInviteWithTestMail($inviteId, $message = null)
{
    $invite = DB::table('project_user')
        ->join('projects', 'project_user.project_id', '=', 'projects.id')
        ->join('workspaces', 'projects.workspace_id', '=', 'workspaces.id')
        ->where('project_user.id', $inviteId)
        ->select(
            'project_user.*',
            'projects.title as project_title',
            'workspaces.title as workspace_title'
        )
        ->first();

    if (!$invite) {
        throw new \Exception('الدعوة غير موجودة');
    }

    // توليد كلمة مرور جديدة
    $newPassword = $this->generateRandomPassword();

    // إرسال البريد باستخدام TestMail
    $this->sendProjectInviteEmailWithTestMail(
        $invite->email,
        $invite->project_id,
        $invite->invite_token,
        $invite->role,
        $message ?? $invite->invite_message,
        $newPassword
    );
}

/**
 * قبول الدعوة وإنشاء المستخدم (نفس طريقة الموظفين)
 */
public function acceptInvite(Request $request, $token)
{
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        $invite = DB::table('project_user')
            ->where('invite_token', $token)
            ->where('status', 'pending')
            ->first();

        if (!$invite || now()->isAfter($invite->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'الدعوة غير صحيحة أو منتهية الصلاحية'
            ], 400);
        }

        DB::beginTransaction();

        // إنشاء المستخدم الجديد (نفس طريقة الموظفين)
        $user = User::create([
            'name' => $request->name,
            'email' => $invite->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'employee', // الدور الافتراضي
            'email_verified_at' => now(),
        ]);

        // تحديث سجل project_user
        DB::table('project_user')
            ->where('invite_token', $token)
            ->update([
                'user_id' => $user->id,
                'status' => 'active',
                'email' => null, // إزالة البريد لأن لدينا user_id الآن
                'invite_token' => null,
                'updated_at' => now(),
            ]);

        // تسجيل العملية في اللوج
        $project = DB::table('projects')->where('id', $invite->project_id)->first();
        ModelsLog::create([
            'type' => 'project_log',
            'type_id' => $invite->project_id,
            'type_log' => 'log',
            'description' => 'تم قبول الدعوة وإنشاء حساب جديد **' . $user->name . '** للمشروع **' . $project->title . '**',
            'created_by' => $user->id,
        ]);

        DB::commit();

        // تسجيل دخول المستخدم تلقائياً
        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'مرحباً بك! تم إنشاء حسابك بنجاح وإضافتك للمشروع',
            'redirect_url' => route('projects.show', $invite->project_id)
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollback();
        return response()->json([
            'success' => false,
            'message' => 'خطأ في البيانات المدخلة',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Accept invite error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء قبول الدعوة. يرجى المحاولة مرة أخرى.'
        ], 500);
    }
}

/**
 * دالة لتوليد كلمة مرور عشوائية (نفس طريقة الموظفين)
 */
private function generateRandomPassword($length = 10)
{
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
}


}
