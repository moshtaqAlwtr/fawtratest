@extends('master')

@section('title', 'تحليلات مفصلة - ' . $project->title)

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/task.css') }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تحليلات مفصلة للمشروع</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">المشاريع</a></li>

                            <li class="breadcrumb-item active">{{ $project->title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- رأس المشروع -->
        <div class="project-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <i class="fas fa-project-diagram fa-3x"></i>
                        </div>
                        <div>
                            <h1 class="mb-1">{{ $project->title }}</h1>
                            <p class="mb-0 opacity-75">{{ $project->description ?: 'لا يوجد وصف' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-layer-group me-2"></i>
                                <span>مساحة العمل: {{ $project->workspace->title ?? 'غير محدد' }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user-crown me-2"></i>
                                <span>منشئ المشروع: {{ $project->creator->name ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                @php
                                    $priorityConfig = [
                                        'low' => ['class' => 'priority-low', 'text' => 'منخفضة', 'icon' => 'chevron-down'],
                                        'medium' => ['class' => 'priority-medium', 'text' => 'متوسطة', 'icon' => 'minus'],
                                        'high' => ['class' => 'priority-high', 'text' => 'عالية', 'icon' => 'chevron-up'],
                                        'urgent' => ['class' => 'priority-urgent', 'text' => 'عاجلة', 'icon' => 'exclamation']
                                    ];
                                    $priority = $priorityConfig[$project->priority] ?? $priorityConfig['medium'];
                                @endphp
                                <span class="priority-badge {{ $priority['class'] }}">
                                    <i class="fas fa-{{ $priority['icon'] }} me-1"></i>{{ $priority['text'] }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center">
                                @php
                                    $statusConfig = [
                                        'new' => ['class' => 'secondary', 'text' => 'جديد', 'icon' => 'plus-circle'],
                                        'in_progress' => ['class' => 'success', 'text' => 'قيد التنفيذ', 'icon' => 'play-circle'],
                                        'completed' => ['class' => 'info', 'text' => 'مكتمل', 'icon' => 'check-circle'],
                                        'on_hold' => ['class' => 'warning', 'text' => 'متوقف', 'icon' => 'pause-circle']
                                    ];
                                    $status = $statusConfig[$project->status] ?? $statusConfig['new'];
                                @endphp
                                <span class="badge badge-{{ $status['class'] }} badge-lg">
                                    <i class="fas fa-{{ $status['icon'] }} me-1"></i>{{ $status['text'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-light">
                            <i class="fas fa-eye me-1"></i>عرض المشروع
                        </a>
                        <a href="" class="btn btn-light">
                            <i class="fas fa-download me-1"></i>تصدير
                        </a>
                        <button class="btn btn-light" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>طباعة
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body text-center">
                        <div class="stat-icon text-primary mb-3">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3 class="mb-1">{{ $stats['tasks']['total'] }}</h3>
                        <p class="text-muted mb-0">إجمالي المهام</p>
                        <div class="progress mt-2" style="height: 4px;">
                            @php
                                $completedPercentage = $stats['tasks']['total'] > 0 ?
                                    ($stats['tasks']['completed'] / $stats['tasks']['total']) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-primary" style="width: {{ $completedPercentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card budget-card">
                    <div class="card-body text-center">
                        <div class="stat-icon mb-3">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h3 class="mb-1">{{ number_format($stats['budget']['total'], 0) }}</h3>
                        <p class="mb-0 opacity-75">الميزانية الإجمالية</p>
                        <div class="mt-2">
                            <small>مصروف: {{ number_format($stats['budget']['spent'], 0) }} ({{ $stats['budget']['usage_percentage'] }}%)</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card timeline-card">
                    <div class="card-body text-center">
                        <div class="stat-icon mb-3">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="mb-1">{{ $stats['timeline']['remaining_days'] }}</h3>
                        <p class="mb-0 opacity-75">
                            @if($stats['timeline']['remaining_days'] >= 0)
                                أيام متبقية
                            @else
                                أيام متأخرة
                            @endif
                        </p>
                        <div class="mt-2">
                            <small>من {{ $stats['timeline']['total_days'] }} يوم إجمالي</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body text-center">
                        <div class="stat-icon text-success mb-3">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <h3 class="mb-1">{{ $stats['performance']['progress_percentage'] }}%</h3>
                        <p class="text-muted mb-0">نسبة الإكمال</p>
                        <div class="mt-2">
                            <small class="text-success">أداء المواعيد: {{ $stats['performance']['on_time_performance'] }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الرسوم البيانية والتحليلات -->
        <div class="row">
            <!-- توزيع حالات المهام -->
            <div class="col-12 col-lg-6">
                <div class="card analytics-card">
                    <div class="card-header">
                        <h4 class="card-title">توزيع حالات المهام</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="taskStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- توزيع الأولويات -->
            <div class="col-12 col-lg-6">
                <div class="card analytics-card">
                    <div class="card-header">
                        <h4 class="card-title">توزيع أولويات المهام</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="priorityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الأداء الشهري -->
            <div class="col-12">
                <div class="card analytics-card">
                    <div class="card-header">
                        <h4 class="card-title">الأداء الشهري (آخر 6 أشهر)</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- تفاصيل المهام والفريق -->
        <div class="row">
            <!-- قائمة المهام -->
            <div class="col-12 col-lg-8">
                <div class="card analytics-card">
                    <div class="card-header">
                        <h4 class="card-title">المهام الحالية</h4>
                    </div>
                    <div class="card-body">
                        @if($project->tasks->count() > 0)
                            @foreach($project->tasks->take(10) as $task)
                                @php
                                    $taskClass = 'task-pending';
                                    if ($task->status == 'completed') $taskClass = 'task-completed';
                                    elseif ($task->status == 'in_progress') $taskClass = 'task-progress';
                                    elseif ($task->due_date && $task->due_date < now() && $task->status != 'completed') $taskClass = 'task-overdue';
                                @endphp
                                <div class="task-item {{ $taskClass }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $task->title }}</h6>
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="badge badge-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : 'secondary') }} badge-sm">
                                                    {{ $task->status == 'completed' ? 'مكتملة' : ($task->status == 'in_progress' ? 'قيد التنفيذ' : 'معلقة') }}
                                                </span>
                                                @if($task->due_date)
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="progress" style="width: 80px; height: 6px;">
                                                <div class="progress-bar bg-{{ $task->completion_percentage >= 100 ? 'success' : 'primary' }}"
                                                     style="width: {{ $task->completion_percentage ?? 0 }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $task->completion_percentage ?? 0 }}%</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @if($project->tasks->count() > 10)
                                <div class="text-center mt-3">
                                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-outline-primary">
                                        عرض جميع المهام ({{ $project->tasks->count() }})
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد مهام</h5>
                                <p class="text-muted">لم يتم إنشاء أي مهام في هذا المشروع بعد</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- فريق المشروع -->
            <div class="col-12 col-lg-4">
                <div class="card analytics-card">
                    <div class="card-header">
                        <h4 class="card-title">فريق المشروع</h4>
                    </div>
                    <div class="card-body">
                        @if($project->users->count() > 0)
                            @foreach($project->users as $user)
                                <div class="team-member">
                                    <div class="member-avatar bg-primary">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <small class="text-muted">{{ $user->email }}</small>
                                        <div>
                                            <span class="badge badge-{{ $user->pivot->role == 'manager' ? 'warning' : 'info' }} badge-sm">
                                                {{ $user->pivot->role == 'manager' ? 'مدير' : 'عضو' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            انضم {{ \Carbon\Carbon::parse($user->pivot->joined_at)->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا يوجد فريق</h5>
                                <p class="text-muted">لم يتم إضافة أعضاء لهذا المشروع</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- معلومات إضافية -->
                <div class="card analytics-card mt-3">
                    <div class="card-header">
                        <h4 class="card-title">معلومات المشروع</h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary">{{ $stats['performance']['average_task_progress'] }}%</h4>
                                    <small class="text-muted">متوسط تقدم المهام</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info">{{ $stats['team']['total_members'] }}</h4>
                                <small class="text-muted">عدد أعضاء الفريق</small>
                            </div>
                        </div>

                        <hr>

                        <div class="timeline">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>تاريخ البداية</strong>
                                <span>{{ $stats['timeline']['start_date']->format('Y-m-d') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong>تاريخ النهاية المخطط</strong>
                                <span>{{ $stats['timeline']['end_date']->format('Y-m-d') }}</span>
                            </div>
                            @if($stats['timeline']['actual_end_date'])
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>تاريخ الانتهاء الفعلي</strong>
                                    <span class="text-success">{{ $stats['timeline']['actual_end_date']->format('Y-m-d') }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2">
                                <strong>المدة الإجمالية</strong>
                                <span>{{ $stats['timeline']['total_days'] }} يوم</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>الأيام المتبقية</strong>
                                <span class="text-{{ $stats['timeline']['remaining_days'] >= 0 ? 'success' : 'danger' }}">
                                    {{ abs($stats['timeline']['remaining_days']) }} يوم
                                    {{ $stats['timeline']['remaining_days'] >= 0 ? 'متبقي' : 'تأخير' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // إعداد الرسوم البيانية
            initCharts();

            function initCharts() {
                // رسم بياني لحالات المهام
                const statusCtx = document.getElementById('taskStatusChart').getContext('2d');
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['مكتملة', 'قيد التنفيذ', 'معلقة', 'متأخرة'],
                        datasets: [{
                            data: [
                                {{ $stats['tasks']['completed'] }},
                                {{ $stats['tasks']['in_progress'] }},
                                {{ $stats['tasks']['pending'] }},
                                {{ $stats['tasks']['overdue'] }}
                            ],
                            backgroundColor: ['#28a745', '#007bff', '#6c757d', '#dc3545'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    font: {
                                        family: 'Cairo, sans-serif'
                                    }
                                }
                            }
                        }
                    }
                });

                // رسم بياني للأولويات
                const priorityCtx = document.getElementById('priorityChart').getContext('2d');
                new Chart(priorityCtx, {
                    type: 'bar',
                    data: {
                        labels: ['عاجلة', 'عالية', 'متوسطة', 'منخفضة'],
                        datasets: [{
                            label: 'عدد المهام',
                            data: [
                                {{ $stats['priorities']['urgent'] }},
                                {{ $stats['priorities']['high'] }},
                                {{ $stats['priorities']['medium'] }},
                                {{ $stats['priorities']['low'] }}
                            ],
                            backgroundColor: ['#6f42c1', '#dc3545', '#ffc107', '#28a745'],
                            borderColor: ['#6f42c1', '#dc3545', '#ffc107', '#28a745'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // رسم بياني للأداء الشهري
                const performanceCtx = document.getElementById('performanceChart').getContext('2d');
                new Chart(performanceCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(collect($stats['monthly_performance'])->pluck('month')) !!},
                        datasets: [{
                            label: 'المهام المنشأة',
                            data: {!! json_encode(collect($stats['monthly_performance'])->pluck('tasks_created')) !!},
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderWidth: 3,
                            fill: false,
                            tension: 0.4
                        }, {
                            label: 'المهام المكتملة',
                            data: {!! json_encode(collect($stats['monthly_performance'])->pluck('tasks_completed')) !!},
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 3,
                            fill: false,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
