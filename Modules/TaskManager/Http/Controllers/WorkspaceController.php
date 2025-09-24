<?php

namespace Modules\TaskManager\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\notifications;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Str;

class WorkspaceController extends Controller
{
    public function index()
    {
        // جلب قائمة المديرين لاستخدامها في البحث
        $admins = User::select('id', 'name')
                      ->whereIn('id', Workspace::distinct()->pluck('admin_id'))
                      ->orderBy('name')
                      ->get();

        // جلب البيانات الأولية
        $workspaces = $this->getWorkspacesAnalytics(request());

        // إذا كان طلب AJAX، أرجع البيانات فقط
        if (request()->wantsJson()) {
            return $this->getWorkspacesAnalytics(request(), true);
        }

        return view('taskmanager::workspaces.index', compact('workspaces', 'admins'));
    }

    /**
     * جلب بيانات تحليلات مساحات العمل
     */
    public function getWorkspacesAnalytics(Request $request, $ajax = false)
    {
        $perPage = $request->get('per_page', 25);

        $query = Workspace::select([
                'id', 'title', 'description', 'admin_id',
                'is_primary', 'created_at', 'updated_at'
            ])
            ->with([
                'admin:id,name,email',
                'projects:id,workspace_id,title,status',
                // إزالة relation مع users مباشرة
            ]);

        // البحث في العنوان
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // فلترة حسب المالك
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        // فلترة المساحات الرئيسية
        if ($request->filled('is_primary')) {
            $query->where('is_primary', $request->is_primary);
        }

        // فلترة بالتاريخ
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // فلترة بعدد المشاريع
        if ($request->filled('projects_min') || $request->filled('projects_max')) {
            $query->withCount('projects');

            if ($request->filled('projects_min')) {
                $query->having('projects_count', '>=', $request->projects_min);
            }

            if ($request->filled('projects_max')) {
                $query->having('projects_count', '<=', $request->projects_max);
            }
        }

        // فلترة بعدد الأعضاء - تعديل للحصول على الأعضاء من خلال المشاريع
        if ($request->filled('members_min') || $request->filled('members_max')) {
            $query->withCount([
                'projects as members_count' => function ($query) {
                    $query->join('project_user', 'projects.id', '=', 'project_user.project_id')
                          ->select(DB::raw('COUNT(DISTINCT project_user.user_id)'));
                }
            ]);

            if ($request->filled('members_min')) {
                $query->having('members_count', '>=', $request->members_min);
            }

            if ($request->filled('members_max')) {
                $query->having('members_count', '<=', $request->members_max);
            }
        }

        // ترتيب النتائج
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $workspaces = $query->paginate($perPage);

        // إضافة إحصائيات لكل مساحة عمل
        $workspaces->getCollection()->transform(function ($workspace) {
            $totalProjects = $workspace->projects->count();
            $activeProjects = $workspace->projects->where('status', 'in_progress')->count();
            $completedProjects = $workspace->projects->where('status', 'completed')->count();
            $onHoldProjects = $workspace->projects->where('status', 'on_hold')->count();

            // حساب عدد الأعضاء من خلال المشاريع
            $totalMembers = DB::table('project_user')
                             ->join('projects', 'project_user.project_id', '=', 'projects.id')
                             ->where('projects.workspace_id', $workspace->id)
                             ->distinct('project_user.user_id')
                             ->count();

            $workspace->stats = [
                'total_projects' => $totalProjects,
                'active_projects' => $activeProjects,
                'completed_projects' => $completedProjects,
                'on_hold_projects' => $onHoldProjects,
                'total_members' => $totalMembers,
                'completion_rate' => $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 1) : 0,
            ];

            return $workspace;
        });

        // فلترة بمعدل الإكمال (بعد حساب الإحصائيات)
        if ($request->filled('completion_min') || $request->filled('completion_max')) {
            $workspaces->getCollection()->filter(function ($workspace) use ($request) {
                $completionRate = $workspace->stats['completion_rate'];

                $meetsMin = !$request->filled('completion_min') || $completionRate >= $request->completion_min;
                $meetsMax = !$request->filled('completion_max') || $completionRate <= $request->completion_max;

                return $meetsMin && $meetsMax;
            });
        }

        if ($ajax) {
            // إعداد بيانات الرسوم البيانية
            $chartData = $this->getChartData($workspaces->getCollection());

            return response()->json([
                'success' => true,
                'html' => view('taskmanager::workspaces.partials.table', [
                    'workspaces' => $workspaces
                ])->render(),
                'pagination' => [
                    'current_page' => $workspaces->currentPage(),
                    'last_page' => $workspaces->lastPage(),
                    'per_page' => $workspaces->perPage(),
                    'total' => $workspaces->total(),
                    'from' => $workspaces->firstItem(),
                    'to' => $workspaces->lastItem(),
                ],
                'chartData' => $chartData
            ]);
        }

        return $workspaces;
    }

    /**
     * جلب الإحصائيات العامة
     */
    public function getAnalyticsStats()
    {
        try {
            // إحصائيات مساحات العمل
            $totalWorkspaces = Workspace::count();
            $primaryWorkspaces = Workspace::where('is_primary', true)->count();

            // إحصائيات المشاريع
            $activeProjects = DB::table('projects')
                               ->join('workspaces', 'projects.workspace_id', '=', 'workspaces.id')
                               ->where('projects.status', 'in_progress')
                               ->count();

            $completedProjects = DB::table('projects')
                               ->join('workspaces', 'projects.workspace_id', '=', 'workspaces.id')
                               ->where('projects.status', 'completed')
                               ->count();

            $totalProjects = DB::table('projects')
                              ->join('workspaces', 'projects.workspace_id', '=', 'workspaces.id')
                              ->count();

            // إحصائيات الأعضاء - من خلال المشاريع
            $totalMembers = DB::table('project_user')
                             ->join('projects', 'project_user.project_id', '=', 'projects.id')
                             ->join('workspaces', 'projects.workspace_id', '=', 'workspaces.id')
                             ->distinct('project_user.user_id')
                             ->count();

            // معدل الإكمال العام
            $completionRate = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 1) : 0;

            // حساب معدلات النمو (مقارنة بالشهر الماضي)
            $lastMonth = now()->subMonth();

            $lastMonthWorkspaces = Workspace::where('created_at', '>=', $lastMonth)->count();
            $lastMonthProjects = DB::table('projects')
                                ->where('created_at', '>=', $lastMonth)
                                ->count();

            $workspacesGrowth = $totalWorkspaces > 0 ? round(($lastMonthWorkspaces / $totalWorkspaces) * 100, 1) : 0;
            $projectsGrowth = $totalProjects > 0 ? round(($lastMonthProjects / $totalProjects) * 100, 1) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_workspaces' => $totalWorkspaces,
                    'primary_workspaces' => $primaryWorkspaces,
                    'active_projects' => $activeProjects,
                    'completed_projects' => $completedProjects,
                    'total_projects' => $totalProjects,
                    'total_members' => $totalMembers,
                    'completion_rate' => $completionRate,
                    'workspaces_growth' => $workspacesGrowth,
                    'projects_growth' => $projectsGrowth,
                    'members_growth' => 0,
                    'completion_growth' => 0,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Analytics stats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإحصائيات',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * إعداد بيانات الرسوم البيانية
     */
    private function getChartData($workspaces)
    {
        $totalActive = 0;
        $totalCompleted = 0;
        $totalOnHold = 0;
        $membersData = [];

        foreach ($workspaces as $workspace) {
            $totalActive += $workspace->stats['active_projects'];
            $totalCompleted += $workspace->stats['completed_projects'];
            $totalOnHold += $workspace->stats['on_hold_projects'];

            $membersData[] = [
                'name' => Str::limit($workspace->title, 15),
                'members' => $workspace->stats['total_members']
            ];
        }

        // أفضل 10 مساحات من ناحية عدد الأعضاء
        $topMembers = collect($membersData)
                        ->sortByDesc('members')
                        ->take(10)
                        ->values();

        return [
            'projects' => [
                'active' => $totalActive,
                'completed' => $totalCompleted,
                'on_hold' => $totalOnHold,
            ],
            'members' => [
                'labels' => $topMembers->pluck('name')->toArray(),
                'data' => $topMembers->pluck('members')->toArray(),
            ]
        ];
    }

    /**
     * دالة للحصول على أعضاء مساحة العمل من خلال المشاريع
     */

    public function create()
    {
        // جلب جميع المستخدمين باستثناء المستخدم الحالي
        $users = User::select('id', 'name', 'email')
                     ->where('id', '!=', Auth::id())
                     ->orderBy('name')
                     ->get();

        $currentUser = Auth::user();

        return view('taskmanager::workspaces.create', compact('users', 'currentUser'));
    }

    /**
     * حفظ مساحة العمل الجديدة - بدون ربط أعضاء مباشرة
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_primary' => 'nullable|in:0,1',
                // إزالة validation للأعضاء
            ]);

            DB::beginTransaction();

            // إنشاء مساحة العمل
            $workspace = Workspace::create([
                'title' => $request->title,
                'description' => $request->description,
                'admin_id' => Auth::id(),
                'is_primary' => $request->is_primary == '1',
            ]);

            // إذا كانت رئيسية، إلغاء باقي المساحات الرئيسية للمستخدم الحالي
            if ($request->is_primary == '1') {
                Workspace::where('admin_id', Auth::id())
                         ->where('id', '!=', $workspace->id)
                         ->update(['is_primary' => false]);
            }

            DB::commit();
                    notifications::create([
            'user_id' => Auth::user()->id,
            'type' => 'workspace',
            'title' => $request->title . ' أنشأ مساحة العمل',
            'description' => 'تم انشاء مساحة العمل',
        ]);


            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء مساحة العمل بنجاح. يمكنك إضافة الأعضاء من خلال إنشاء مشاريع.',
                'data' => [
                    'workspace_id' => $workspace->id,
                    'redirect_url' => route('workspaces.analytics.detailed', $workspace->id)
                ]
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

            Log::error('Workspace creation error: ' . $e->getMessage(), [
                'admin_id' => Auth::id(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء مساحة العمل. يرجى المحاولة مرة أخرى.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * عرض صفحة تعديل مساحة العمل
     */
    public function edit(Workspace $workspace)
    {
        // التأكد من أن المستخدم مخول لتعديل هذه المساحة
        if ($workspace->admin_id !== Auth::id()) {
            abort(403, 'غير مخول لتعديل هذه المساحة');
        }

        return view('taskmanager::workspaces.edit', compact('workspace'));
    }

    /**
     * تحديث مساحة العمل - بدون تحديث الأعضاء
     */
    public function update(Request $request, Workspace $workspace)
    {
        // التأكد من أن المستخدم مخول لتحديث هذه المساحة
        if ($workspace->admin_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مخول لتعديل هذه المساحة'
            ], 403);
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_primary' => 'nullable|in:0,1',
                // إزالة validation للأعضاء
            ]);

            DB::beginTransaction();

            // تحديث بيانات مساحة العمل
            $workspace->update([
                'title' => $request->title,
                'description' => $request->description,
                'is_primary' => $request->is_primary == '1',
            ]);

            // إذا كانت رئيسية، إلغاء باقي المساحات الرئيسية للمستخدم الحالي
            if ($request->is_primary == '1') {
                Workspace::where('admin_id', Auth::id())
                         ->where('id', '!=', $workspace->id)
                         ->update(['is_primary' => false]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث مساحة العمل بنجاح'
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

            Log::error('Workspace update error: ' . $e->getMessage(), [
                'workspace_id' => $workspace->id,
                'admin_id' => Auth::id(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث مساحة العمل. يرجى المحاولة مرة أخرى.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * حذف مساحة العمل
     */
    public function destroy(Workspace $workspace)
    {
        // التحقق من وجود مشاريع
        if ($workspace->projects()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف مساحة العمل لأنها تحتوي على مشاريع'
            ], 422);
        }

        try {
            // حذف مساحة العمل مباشرة (لا توجد علاقات users لحذفها)
            $workspace->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف مساحة العمل بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف مساحة العمل',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // باقي الدوال الأخرى...

    public function getWorkspaces(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $query = Workspace::select([
                'id', 'title', 'description', 'admin_id',
                'is_primary', 'created_at', 'updated_at'
            ])
            ->with([
                'admin:id,name,email',
                'projects:id,workspace_id,title,status',
            ]);

        // البحث في العنوان
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // فلترة حسب المالك
        if ($request->filled('owner_id')) {
            $query->where('admin_id', $request->owner_id);
        }

        // فلترة المساحات الرئيسية
        if ($request->filled('is_primary')) {
            $query->where('is_primary', $request->is_primary);
        }

        // ترتيب النتائج
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $workspaces = $query->paginate($perPage);

        // إضافة إحصائيات لكل مساحة عمل
        $workspaces->getCollection()->transform(function ($workspace) {
            // حساب عدد الأعضاء من خلال المشاريع
            $totalMembers = DB::table('project_user')
                             ->join('projects', 'project_user.project_id', '=', 'projects.id')
                             ->where('projects.workspace_id', $workspace->id)
                             ->distinct('project_user.user_id')
                             ->count();

            $workspace->stats = [
                'total_projects' => $workspace->projects->count(),
                'active_projects' => $workspace->projects->where('status', 'in_progress')->count(),
                'completed_projects' => $workspace->projects->where('status', 'completed')->count(),
                'total_members' => $totalMembers,
            ];
            return $workspace;
        });

        return response()->json([
            'success' => true,
            'data' => $workspaces->items(),
            'pagination' => [
                'current_page' => $workspaces->currentPage(),
                'last_page' => $workspaces->lastPage(),
                'per_page' => $workspaces->perPage(),
                'total' => $workspaces->total(),
                'from' => $workspaces->firstItem(),
                'to' => $workspaces->lastItem(),
            ]
        ]);
    }

    public function getWorkspace(Workspace $workspace)
    {
        $workspace->load([
            'admin:id,name,email',
            'projects:id,workspace_id,title,status,priority,progress_percentage'
        ]);

        return response()->json([
            'success' => true,
            'data' => $workspace
        ]);
    }
public function detailedAnalytics(Workspace $workspace)
{
    $workspace->load([
        'admin:id,name,email',
        'projects:id,workspace_id,title,status,priority,progress_percentage,start_date,end_date',
        'users:id,name,email'
    ]);

    // إحصائيات مفصلة
    $stats = $this->getDetailedWorkspaceStats($workspace);

    return view('taskmanager::workspaces.detailed', compact('workspace', 'stats'));
}

private function getDetailedWorkspaceStats(Workspace $workspace)
{
    $projects = $workspace->projects;

    $stats = [
        'projects' => [
            'total' => $projects->count(),
            'active' => $projects->where('status', 'in_progress')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
            'on_hold' => $projects->where('status', 'on_hold')->count(),
            'pending' => $projects->where('status', 'pending')->count(),
        ],
        'priorities' => [
            'high' => $projects->where('priority', 'high')->count(),
            'medium' => $projects->where('priority', 'medium')->count(),
            'low' => $projects->where('priority', 'low')->count(),
        ],
        'timeline' => [],
        'members' => $workspace->users->count(),
        'performance' => []
    ];

    // إحصائيات الأداء
    $totalProgress = $projects->sum('progress_percentage');
    $stats['performance']['average_progress'] = $projects->count() > 0 ?
        round($totalProgress / $projects->count(), 1) : 0;

    // الجدول الزمني للمشاريع (آخر 6 أشهر)
    $timelineData = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $monthProjects = $projects->filter(function ($project) use ($month) {
            return $project->created_at->format('Y-m') === $month->format('Y-m');
        })->count();

        $timelineData[] = [
            'month' => $month->format('M Y'),
            'projects' => $monthProjects
        ];
    }
    $stats['timeline'] = $timelineData;

    return $stats;
}


public function show(Workspace $workspace)
{
    // تحميل البيانات المطلوبة
    $workspace->load([
        'admin:id,name,email',
        'projects' => function ($query) {
            $query->select('id', 'workspace_id', 'title', 'status', 'priority', 'progress_percentage', 'start_date', 'end_date', 'created_by')
                  ->with('users:id,name')
                  ->orderBy('created_at', 'desc');
        }
    ]);

    return view('taskmanager::workspaces.show', compact('workspace'));
}

/**
 * جلب مشاريع مساحة العمل مع الترقيم
 */
/**
 * جلب مشاريع مساحة العمل مع أعضائها
 */
public function getWorkspaceProjects(Workspace $workspace)
{
    $projects = DB::table('projects')
        ->select('projects.*')
        ->where('projects.workspace_id', $workspace->id)
        ->orderBy('projects.created_at', 'desc')
        ->get()
        ->map(function ($project) {
            // جلب أعضاء المشروع
            $members = DB::table('project_user')
                ->join('users', 'project_user.user_id', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'project_user.role',
                    'project_user.status'
                )
                ->where('project_user.project_id', $project->id)
                ->where('project_user.status', 'active')
                ->get();

            // جلب الدعوات المعلقة
            $pendingInvites = DB::table('project_user')
                ->select('email', 'role', 'invited_at', 'expires_at')
                ->where('project_id', $project->id)
                ->where('status', 'pending')
                ->get();

            $project->members = $members;
            $project->pending_invites = $pendingInvites;
            $project->members_count = $members->count();
            $project->pending_count = $pendingInvites->count();

            return $project;
        });

    return response()->json([
        'success' => true,
        'data' => $projects
    ]);
}

public function getAvailableUsersForInvite(Workspace $workspace)
{
    if ($workspace->admin_id !== Auth::id()) {
        return response()->json([
            'success' => false,
            'message' => 'غير مخول'
        ], 403);
    }

    // المستخدمين الذين لديهم دور موظف أو مدير فقط
    // وليسوا أعضاء في أي مشروع في هذه المساحة
    $availableUsers = User::select('users.id', 'users.name', 'users.email', 'users.role')
        ->where('users.id', '!=', $workspace->admin_id) // استبعاد المالك
        ->whereIn('users.role', ['employee', 'manager']) // فقط الموظفين والمديرين
        ->whereNotIn('users.id', function($query) use ($workspace) {
            $query->select('project_user.user_id')
                  ->from('project_user')
                  ->join('projects', 'project_user.project_id', '=', 'projects.id')
                  ->where('projects.workspace_id', $workspace->id);
        })
        ->orderBy('users.name')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $availableUsers,
        'count' => $availableUsers->count()
    ]);
}
public function getWorkspaceMembers(Workspace $workspace)
{
    $members = DB::table('users')
                 ->select('users.id', 'users.name', 'users.email', DB::raw('COUNT(DISTINCT projects.id) as projects_count'))
                 ->join('project_user', 'users.id', '=', 'project_user.user_id')
                 ->join('projects', 'project_user.project_id', '=', 'projects.id')
                 ->where('projects.workspace_id', $workspace->id)
                 ->groupBy('users.id', 'users.name', 'users.email')
                 ->orderBy('projects_count', 'desc')
                 ->get();

    return response()->json([
        'success' => true,
        'data' => $members
    ]);
}

/**
 * جلب إحصائيات مساحة العمل
 */
public function getWorkspaceStats(Workspace $workspace)
{
    $totalProjects = $workspace->projects()->count();
    $activeProjects = $workspace->projects()->where('status', 'in_progress')->count();
    $completedProjects = $workspace->projects()->where('status', 'completed')->count();
    $onHoldProjects = $workspace->projects()->where('status', 'on_hold')->count();
    $pendingProjects = $workspace->projects()->where('status', 'pending')->count();

    $totalTasks = DB::table('tasks')
                   ->join('projects', 'tasks.project_id', '=', 'projects.id')
                   ->where('projects.workspace_id', $workspace->id)
                   ->count();

    $completedTasks = DB::table('tasks')
                       ->join('projects', 'tasks.project_id', '=', 'projects.id')
                       ->where('projects.workspace_id', $workspace->id)
                       ->where('tasks.status', 'completed')
                       ->count();

    // حساب عدد الأعضاء من خلال المشاريع
    $totalMembers = DB::table('project_user')
                     ->join('projects', 'project_user.project_id', '=', 'projects.id')
                     ->where('projects.workspace_id', $workspace->id)
                     ->distinct('project_user.user_id')
                     ->count();

    return response()->json([
        'success' => true,
        'data' => [
            'projects' => [
                'total' => $totalProjects,
                'active' => $activeProjects,
                'completed' => $completedProjects,
                'on_hold' => $onHoldProjects,
                'pending' => $pendingProjects,
                'completion_rate' => $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 2) : 0,
            ],
            'tasks' => [
                'total' => $totalTasks,
                'completed' => $completedTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
            ],
            'members' => [
                'total' => $totalMembers,
            ],
            'created_at' => $workspace->created_at->format('Y-m-d'),
            'is_primary' => $workspace->is_primary
        ]
    ]);
}

/**
 * جلب تحليلات مساحة العمل للرسوم البيانية
 */
public function getWorkspaceAnalytics(Workspace $workspace)
{
    // إحصائيات حالات المشاريع
    $projectStats = [
        'pending' => $workspace->projects()->where('status', 'pending')->count(),
        'in_progress' => $workspace->projects()->where('status', 'in_progress')->count(),
        'completed' => $workspace->projects()->where('status', 'completed')->count(),
        'on_hold' => $workspace->projects()->where('status', 'on_hold')->count(),
    ];

    // أكثر الأعضاء نشاطاً
    $activeMembers = DB::table('users')
                      ->select('users.name', DB::raw('COUNT(DISTINCT projects.id) as projects_count'))
                      ->join('project_user', 'users.id', '=', 'project_user.user_id')
                      ->join('projects', 'project_user.project_id', '=', 'projects.id')
                      ->where('projects.workspace_id', $workspace->id)
                      ->groupBy('users.id', 'users.name')
                      ->orderBy('projects_count', 'desc')
                      ->limit(10)
                      ->get();

    return response()->json([
        'success' => true,
        'data' => [
            'project_status' => $projectStats,
            'active_members' => $activeMembers,
        ]
    ]);
}


/**
 * إنشاء دعوة مساحة العمل للمستخدم الموجود
 */
private function createWorkspaceInvite(User $user, Workspace $workspace)
{
    try {
        // إنشاء رمز دعوة فريد
        $inviteToken = Str::random(32);
        $expiresAt = now()->addDays(7); // الدعوة صالحة لمدة 7 أيام

        // حفظ الدعوة في قاعدة البيانات
        $inviteId = DB::table('workspace_invites')->insertGetId([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'email' => $user->email,
            'invited_by' => Auth::id(),
            'token' => $inviteToken,
            'expires_at' => $expiresAt,
            'status' => 'pending',
            'type' => 'workspace',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إرسال بريد الدعوة
        $this->sendInviteEmail($user, $workspace, $inviteToken);

        // يمكن أيضاً إرسال إشعار داخلي للمستخدم
        $this->sendInternalNotification($user, $workspace);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الدعوة بنجاح إلى ' . $user->name,
            'data' => [
                'invite_id' => $inviteId,
                'invited_user' => $user->name,
                'invited_email' => $user->email,
                'invite_expires_at' => $expiresAt->format('Y-m-d H:i'),
                'workspace_title' => $workspace->title
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Create workspace invite error: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * إرسال بريد الدعوة للمستخدم
 */
private function sendInviteEmail(User $user, Workspace $workspace, $token)
{
    $inviteUrl = url("/workspaces/invite/{$token}/workspace/{$workspace->id}/accept");
    $declineUrl = url("/workspaces/invite/{$token}/workspace/{$workspace->id}/decline");
    $adminName = Auth::user()->name;
    $subject = "دعوة للانضمام إلى مساحة العمل: {$workspace->title}";

    $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='color: white; margin: 0; font-size: 24px;'>دعوة للانضمام إلى مساحة العمل</h1>
            </div>

            <div style='padding: 30px; background: #f8f9fa; border-radius: 0 0 10px 10px;'>
                <h2 style='color: #333; margin-top: 0;'>مرحباً {$user->name}</h2>

                <p style='font-size: 16px; line-height: 1.6; color: #555;'>
                    تمت دعوتك من قبل <strong>{$adminName}</strong> للانضمام إلى مساحة العمل:
                </p>

                <div style='background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-right: 4px solid #007bff;'>
                    <h3 style='margin: 0 0 10px 0; color: #007bff; font-size: 20px;'>{$workspace->title}</h3>
                    <p style='margin: 0; color: #666; font-size: 14px;'>" . ($workspace->description ?: 'مساحة عمل للتعاون في المشاريع') . "</p>
                </div>

                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$inviteUrl}' style='background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 5px;'>
                        قبول الدعوة
                    </a>
                    <a href='{$declineUrl}' style='background: #dc3545; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 5px;'>
                        رفض الدعوة
                    </a>
                </div>

                <div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin-top: 20px;'>
                    <p style='margin: 0; color: #856404; font-size: 14px;'>
                        <strong>ملاحظة:</strong> هذه الدعوة صالحة لمدة 7 أيام فقط.
                        <br>بعد قبول الدعوة، ستتمكن من المشاركة في مشاريع هذه المساحة.
                    </p>
                </div>

                <hr style='margin: 30px 0; border: none; border-top: 1px solid #dee2e6;'>

                <p style='font-size: 12px; color: #6c757d; text-align: center; margin: 0;'>
                    إذا لم تكن تتوقع هذه الدعوة، يمكنك تجاهل هذا البريد الإلكتروني بأمان.
                    <br>أو يمكنك الضغط على 'رفض الدعوة' لرفضها نهائياً.
                </p>
            </div>
        </div>
    ";

    // إرسال البريد
    try {
        mail($user->email, $subject, $message, [
            'MIME-Version' => '1.0',
            'Content-type' => 'text/html; charset=UTF-8',
            'From' => config('mail.from.address', 'noreply@example.com'),
            'Reply-To' => Auth::user()->email,
            'X-Mailer' => 'PHP/' . phpversion()
        ]);

        Log::info("Workspace invite email sent to {$user->email} for workspace {$workspace->title}");

    } catch (\Exception $e) {
        Log::error("Failed to send workspace invite email: " . $e->getMessage());
        // لا نرمي خطأ هنا لأن الدعوة تم حفظها في قاعدة البيانات
    }
}

/**
 * إرسال إشعار داخلي للمستخدم (اختياري)
 */
private function sendInternalNotification(User $user, Workspace $workspace)
{
    try {
        // يمكنك هنا إضافة إشعار داخلي في النظام
        // مثل إضافة record في جدول notifications

        DB::table('notifications')->insert([
            'user_id' => $user->id,
            'title' => 'دعوة جديدة لمساحة العمل',
            'message' => "تمت دعوتك للانضمام إلى مساحة العمل: {$workspace->title}",
            'type' => 'workspace_invite',
            'data' => json_encode([
                'workspace_id' => $workspace->id,
                'workspace_title' => $workspace->title,
                'invited_by' => Auth::user()->name
            ]),
            'read' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

    } catch (\Exception $e) {
        // لا نرمي خطأ لأن هذا اختياري
        Log::warning("Failed to send internal notification: " . $e->getMessage());
    }
}

/**
 * الحصول على قائمة المستخدمين المتاحين للدعوة
 */


/**
 * إرسال بريد إعلامي بسيط
 */
private function sendSimpleNotificationEmail(User $user, Workspace $workspace)
{
    $workspaceUrl = route('workspaces.show', $workspace->id);
    $adminName = Auth::user()->name;
    $subject = "إعلام: تم إضافتك لمساحة العمل {$workspace->title}";

    $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='color: white; margin: 0; font-size: 24px;'>إعلام بإضافتك لمساحة عمل</h1>
            </div>

            <div style='padding: 30px; background: #f8f9fa; border-radius: 0 0 10px 10px;'>
                <h2 style='color: #333; margin-top: 0;'>مرحباً {$user->name}</h2>

                <p style='font-size: 16px; line-height: 1.6; color: #555;'>
                    نود إعلامك أن <strong>{$adminName}</strong> قد أضافك لمساحة العمل:
                </p>

                <div style='background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-right: 4px solid #28a745;'>
                    <h3 style='margin: 0 0 10px 0; color: #28a745; font-size: 20px;'>{$workspace->title}</h3>
                    <p style='margin: 0; color: #666; font-size: 14px;'>" . ($workspace->description ?: 'مساحة عمل للتعاون في المشاريع') . "</p>
                </div>

                <div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p style='margin: 0; color: #155724; font-size: 14px;'>
                        <strong>ملاحظة:</strong> ستتمكن من المشاركة في مشاريع هذه المساحة عندما يتم إضافتك لمشروع محدد.
                        <br>سيتم إشعارك عند إضافتك لأي مشروع.
                    </p>
                </div>

                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$workspaceUrl}' style='background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>
                        عرض مساحة العمل
                    </a>
                </div>

                <hr style='margin: 30px 0; border: none; border-top: 1px solid #dee2e6;'>

                <p style='font-size: 12px; color: #6c757d; text-align: center; margin: 0;'>
                    هذه رسالة إعلامية. للاستفسارات تواصل مع {$adminName}.
                </p>
            </div>
        </div>
    ";

    // إرسال البريد
    try {
        mail($user->email, $subject, $message, [
            'MIME-Version' => '1.0',
            'Content-type' => 'text/html; charset=UTF-8',
            'From' => config('mail.from.address', 'noreply@example.com'),
            'Reply-To' => Auth::user()->email,
            'X-Mailer' => 'PHP/' . phpversion()
        ]);

        Log::info("Notification email sent to {$user->email} for workspace {$workspace->title}");

    } catch (\Exception $e) {
        Log::error("Failed to send notification email: " . $e->getMessage());
    }
}

/**
 * الحصول على المستخدمين المتاحين للإعلام
 */

/**
 * الحصول على المستخدمين المتاحين للإعلام (موظفين ومديرين فقط)
 */
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
                    ]);
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

                $this->sendExistingUserNotification($existingUser, $project, $request->role);

                return response()->json([
                    'success' => true,
                    'message' => "تمت إضافة {$existingUser->name} للمشروع بنجاح"
                ]);
            }

            // إذا لم يكن المستخدم موجود، إنشاء دعوة
            $this->createProjectInvite($request->email, $projectId, $request->role, $request->invite_message);

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال دعوة المشروع بنجاح إلى ' . $request->email
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Project invite error: ' . $e->getMessage(), [
                'project_id' => $projectId,
                'email' => $request->email,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الدعوة'
            ], 500);
        }
    }

    /**
     * إنشاء دعوة مشروع للمستخدم الجديد
     */
    private function createProjectInvite($email, $projectId, $role, $message = null)
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

        // إدراج الدعوة
        DB::table('project_user')->insert([
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

        // إرسال بريد الدعوة
        $this->sendProjectInviteEmail($email, $projectId, $inviteToken, $role, $message);
    }

    /**
     * إرسال بريد دعوة المشروع
     */
    private function sendProjectInviteEmail($email, $projectId, $token, $role, $message = null)
    {
        $project = DB::table('projects')
            ->join('workspaces', 'projects.workspace_id', '=', 'workspaces.id')
            ->select('projects.*', 'workspaces.title as workspace_title')
            ->where('projects.id', $projectId)
            ->first();

        $inviterName = Auth::user()->name;
        $acceptUrl = url("/projects/invite/{$token}/accept");
        $declineUrl = url("/projects/invite/{$token}/decline");

        $roleText = match($role) {
            'manager' => 'مدير',
            'member' => 'عضو',
            'viewer' => 'مشاهد',
            default => 'عضو'
        };

        $subject = "دعوة للانضمام إلى مشروع: {$project->title}";

        $messageBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; direction: rtl;'>
                <div style='background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='color: white; margin: 0; font-size: 24px;'>دعوة للانضمام إلى مشروع</h1>
                </div>

                <div style='padding: 30px; background: #f8f9fa; border-radius: 0 0 10px 10px;'>
                    <h2 style='color: #333; margin-top: 0;'>مرحباً!</h2>

                    <p style='font-size: 16px; line-height: 1.6; color: #555;'>
                        تمت دعوتك من قبل <strong>{$inviterName}</strong> للانضمام إلى:
                    </p>

                    <div style='background: white; padding: 25px; border-radius: 8px; margin: 25px 0; border-right: 4px solid #4f46e5;'>
                        <h3 style='margin: 0 0 10px 0; color: #4f46e5; font-size: 20px;'>{$project->title}</h3>
                        <p style='margin: 5px 0; color: #666; font-size: 14px;'>
                            <strong>مساحة العمل:</strong> {$project->workspace_title}
                        </p>
                        <p style='margin: 5px 0; color: #666; font-size: 14px;'>
                            <strong>دورك في المشروع:</strong> {$roleText}
                        </p>
                        " . ($project->description ? "<p style='margin: 10px 0 0 0; color: #888; font-size: 14px;'>{$project->description}</p>" : "") . "
                    </div>";

        if ($message) {
            $messageBody .= "
                <div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; border-right: 3px solid #2196f3;'>
                    <p style='margin: 0; color: #1565c0; font-size: 14px;'>
                        <strong>رسالة من {$inviterName}:</strong><br>
                        {$message}
                    </p>
                </div>";
        }

        $messageBody .= "
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$acceptUrl}' style='background: #28a745; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 10px; font-size: 16px;'>
                            قبول الدعوة والانضمام
                        </a>
                        <br>
                        <a href='{$declineUrl}' style='background: #dc3545; color: white; padding: 10px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 10px; font-size: 14px;'>
                            رفض الدعوة
                        </a>
                    </div>

                    <div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 5px; margin: 25px 0;'>
                        <p style='margin: 0; color: #856404; font-size: 14px; line-height: 1.5;'>
                            <strong>ملاحظة مهمة:</strong><br>
                            • هذه الدعوة صالحة لمدة 7 أيام فقط<br>
                            • عند قبول الدعوة، ستحتاج لإنشاء حساب جديد وتعيين كلمة مرور<br>
                            • بعد التسجيل، ستتمكن من الوصول للمشروع والمشاركة في المهام
                        </p>
                    </div>

                    <hr style='margin: 30px 0; border: none; border-top: 1px solid #dee2e6;'>

                    <p style='font-size: 12px; color: #6c757d; text-align: center; margin: 0; line-height: 1.4;'>
                        إذا لم تكن تتوقع هذه الدعوة، يمكنك تجاهل هذا البريد الإلكتروني بأمان.<br>
                        أو يمكنك الضغط على 'رفض الدعوة' لرفضها نهائياً.
                    </p>
                </div>
            </div>
        ";

        // إرسال البريد
        try {
            mail($email, $subject, $messageBody, [
                'MIME-Version' => '1.0',
                'Content-type' => 'text/html; charset=UTF-8',
                'From' => config('mail.from.address', 'noreply@example.com'),
                'Reply-To' => Auth::user()->email,
                'X-Mailer' => 'PHP/' . phpversion()
            ]);

            Log::info("Project invite email sent to {$email} for project {$project->title}");

        } catch (\Exception $e) {
            Log::error("Failed to send project invite email: " . $e->getMessage());
            throw new \Exception('فشل في إرسال بريد الدعوة');
        }
    }

    /**
     * صفحة قبول الدعوة
     */
    public function showAcceptInvite($token)
    {
        $invite = DB::table('project_user')
            ->join('projects', 'project_user.project_id', '=', 'projects.id')
            ->join('workspaces', 'projects.workspace_id', '=', 'workspaces.id')
            ->leftJoin('users as inviter', 'project_user.invited_by', '=', 'inviter.id')
            ->select(
                'project_user.*',
                'projects.title as project_title',
                'projects.description as project_description',
                'workspaces.title as workspace_title',
                'inviter.name as inviter_name'
            )
            ->where('project_user.invite_token', $token)
            ->where('project_user.status', 'pending')
            ->first();

        if (!$invite) {
            return view('taskmanager::invites.invalid', [
                'message' => 'رابط الدعوة غير صحيح أو منتهي الصلاحية'
            ]);
        }

        if (now()->isAfter($invite->expires_at)) {
            DB::table('project_user')
                ->where('invite_token', $token)
                ->update(['status' => 'expired']);

            return view('taskmanager::invites.expired');
        }

        $roleText = match($invite->role) {
            'manager' => 'مدير',
            'member' => 'عضو',
            'viewer' => 'مشاهد',
            default => 'عضو'
        };

        return view('taskmanager::invites.accept', compact('invite', 'roleText'));
    }

    /**
     * معالجة قبول الدعوة
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

            // إنشاء المستخدم الجديد
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

            // إنشاء إشعار
            notifications::create([
                'user_id' => $user->id,
                'type' => 'project_join',
                'title' => 'مرحباً بك في المشروع',
                'description' => 'تم قبول دعوتك بنجاح وإضافتك للمشروع',
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
     * رفض الدعوة
     */
    public function declineInvite($token)
    {
        $invite = DB::table('project_user')
            ->where('invite_token', $token)
            ->where('status', 'pending')
            ->first();

        if (!$invite) {
            return view('taskmanager::invites.invalid', [
                'message' => 'رابط الدعوة غير صحيح'
            ]);
        }

        DB::table('project_user')
            ->where('invite_token', $token)
            ->update(['status' => 'declined']);

        return view('taskmanager::invites.declined');
    }

    /**
     * إرسال إشعار للمستخدم الموجود
     */
    private function sendExistingUserNotification($user, $project, $role)
    {
        $roleText = match($role) {
            'manager' => 'مدير',
            'member' => 'عضو',
            'viewer' => 'مشاهد',
            default => 'عضو'
        };

        $subject = "تم إضافتك إلى مشروع: {$project->title}";
        $projectUrl = route('projects.show', $project->id);
        $inviterName = Auth::user()->name;

        $message = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; direction: rtl;'>
                <div style='background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='color: white; margin: 0; font-size: 24px;'>تم إضافتك لمشروع جديد!</h1>
                </div>

                <div style='padding: 30px; background: #f8f9fa; border-radius: 0 0 10px 10px;'>
                    <h2 style='color: #333; margin-top: 0;'>مرحباً {$user->name}</h2>

                    <p style='font-size: 16px; line-height: 1.6; color: #555;'>
                        تم إضافتك من قبل <strong>{$inviterName}</strong> إلى المشروع:
                    </p>

                    <div style='background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-right: 4px solid #28a745;'>
                        <h3 style='margin: 0 0 10px 0; color: #28a745; font-size: 20px;'>{$project->title}</h3>
                        <p style='margin: 5px 0; color: #666; font-size: 14px;'>
                            <strong>دورك في المشروع:</strong> {$roleText}
                        </p>
                    </div>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$projectUrl}' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>
                            عرض المشروع الآن
                        </a>
                    </div>
                </div>
            </div>
        ";

        try {
            mail($user->email, $subject, $message, [
                'MIME-Version' => '1.0',
                'Content-type' => 'text/html; charset=UTF-8',
                'From' => config('mail.from.address', 'noreply@example.com'),
                'Reply-To' => Auth::user()->email,
                'X-Mailer' => 'PHP/' . phpversion()
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send user notification: " . $e->getMessage());
        }
    }
 public function showPendingInvites($projectId)
    {
        $project = DB::table('projects')->where('id', $projectId)->first();
        if (!$project) {
            abort(404, 'المشروع غير موجود');
        }

        // التحقق من الصلاحيات
        $hasPermission = DB::table('project_user')
            ->where('project_id', $projectId)
            ->where('user_id', Auth::id())

            ->exists();

        $isWorkspaceAdmin = DB::table('workspaces')
            ->where('id', $project->workspace_id)
            ->where('admin_id', Auth::id())
            ->exists();

        if (!$hasPermission && !$isWorkspaceAdmin) {
            abort(403, 'غير مخول لعرض دعوات هذا المشروع');
        }

        // جلب الدعوات المعلقة
        $pendingInvites = DB::table('project_user')
            ->leftJoin('users as inviter', 'project_user.invited_by', '=', 'inviter.id')
            ->select(
                'project_user.*',
                'inviter.name as inviter_name'
            )
            ->where('project_user.project_id', $projectId)
            ->where('project_user.status', 'pending')
            ->orderBy('project_user.invited_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pendingInvites->map(function ($invite) {
                $roleText = match($invite->role) {
                    'manager' => 'مدير',
                    'member' => 'عضو',
                    'viewer' => 'مشاهد',
                    default => 'عضو'
                };

                return [
                    'token' => $invite->invite_token,
                    'email' => $invite->email,
                    'role' => $invite->role,
                    'role_text' => $roleText,
                    'invited_by' => $invite->inviter_name,
                    'invited_at' => $invite->invited_at,
                    'expires_at' => $invite->expires_at,
                    'is_expired' => now()->isAfter($invite->expires_at),
                    'message' => $invite->invite_message
                ];
            })
        ]);
    }

    /**
     * إلغاء دعوة معلقة
     */
    public function cancelInvite($projectId, $token)
    {
        try {
            $project = DB::table('projects')->where('id', $projectId)->first();
            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'المشروع غير موجود'
                ], 404);
            }

            // التحقق من الصلاحيات
            $hasPermission = DB::table('project_user')
                ->where('project_id', $projectId)
                ->where('user_id', Auth::id())

                ->exists();

            $isWorkspaceAdmin = DB::table('workspaces')
                ->where('id', $project->workspace_id)
                ->where('admin_id', Auth::id())
                ->exists();

            if (!$hasPermission && !$isWorkspaceAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مخول لإلغاء دعوات هذا المشروع'
                ], 403);
            }

            // العثور على الدعوة وحذفها
            $invite = DB::table('project_user')
                ->where('project_id', $projectId)
                ->where('invite_token', $token)
                ->where('status', 'pending')
                ->first();

            if (!$invite) {
                return response()->json([
                    'success' => false,
                    'message' => 'الدعوة غير موجودة أو تم قبولها مسبقاً'
                ], 404);
            }

            // حذف الدعوة
            DB::table('project_user')
                ->where('project_id', $projectId)
                ->where('invite_token', $token)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الدعوة بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Cancel invite error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إلغاء الدعوة'
            ], 500);
        }
    }

    /**
     * إعادة إرسال دعوة
     */
    public function resendInvite($projectId, $token)
    {
        try {
            $project = DB::table('projects')->where('id', $projectId)->first();
            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'المشروع غير موجود'
                ], 404);
            }

            // التحقق من الصلاحيات
            $hasPermission = DB::table('project_user')
                ->where('project_id', $projectId)
                ->where('user_id', Auth::id())

                ->exists();

            $isWorkspaceAdmin = DB::table('workspaces')
                ->where('id', $project->workspace_id)
                ->where('admin_id', Auth::id())
                ->exists();

            if (!$hasPermission && !$isWorkspaceAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مخول لإعادة إرسال دعوات هذا المشروع'
                ], 403);
            }

            // العثور على الدعوة
            $invite = DB::table('project_user')
                ->where('project_id', $projectId)
                ->where('invite_token', $token)
                ->where('status', 'pending')
                ->first();

            if (!$invite) {
                return response()->json([
                    'success' => false,
                    'message' => 'الدعوة غير موجودة أو تم قبولها مسبقاً'
                ], 404);
            }

            // إنشاء رمز جديد وتمديد الصلاحية
            $newToken = Str::random(64);
            $newExpiresAt = now()->addDays(7);

            // تحديث الدعوة
            DB::table('project_user')
                ->where('project_id', $projectId)
                ->where('invite_token', $token)
                ->update([
                    'invite_token' => $newToken,
                    'expires_at' => $newExpiresAt,
                    'invited_at' => now(),
                    'invited_by' => Auth::id(),
                    'updated_at' => now()
                ]);

            // إعادة إرسال البريد
            $this->sendProjectInviteEmail($invite->email, $projectId, $newToken, $invite->role, $invite->invite_message);

            return response()->json([
                'success' => true,
                'message' => 'تم إعادة إرسال الدعوة بنجاح إلى ' . $invite->email
            ]);

        } catch (\Exception $e) {
            Log::error('Resend invite error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إعادة إرسال الدعوة'
            ], 500);
        }
    }

    /**
     * تنظيف الدعوات المنتهية الصلاحية (يمكن تشغيله كـ Command)
     */
    public function cleanupExpiredInvites()
    {
        try {
            $expiredCount = DB::table('project_user')
                ->where('status', 'pending')
                ->where('expires_at', '<', now())
                ->update(['status' => 'expired']);

            Log::info("Cleaned up {$expiredCount} expired project invites");

            return response()->json([
                'success' => true,
                'message' => "تم تنظيف {$expiredCount} دعوة منتهية الصلاحية"
            ]);

        } catch (\Exception $e) {
            Log::error('Cleanup expired invites error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تنظيف الدعوات المنتهية'
            ], 500);
        }
    }

    /**
     * إحصائيات الدعوات للمشروع
     */
    public function getProjectInviteStats($projectId)
    {
        $stats = [
            'pending' => DB::table('project_user')
                ->where('project_id', $projectId)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->count(),

            'expired' => DB::table('project_user')
                ->where('project_id', $projectId)
                ->where('status', 'expired')
                ->count(),

            'declined' => DB::table('project_user')
                ->where('project_id', $projectId)
                ->where('status', 'declined')
                ->count(),

            'active_members' => DB::table('project_user')
                ->where('project_id', $projectId)
                ->where('status', 'active')
                ->whereNotNull('user_id')
                ->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
