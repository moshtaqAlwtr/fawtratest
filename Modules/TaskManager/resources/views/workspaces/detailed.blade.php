@extends('master')

@section('title', 'تحليلات مفصلة - ' . $workspace->title)

@section('css')
    <style>
        .analytics-card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .analytics-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .progress-ring {
            transform: rotate(-90deg);
        }

        .chart-container {
            position: relative;
            height: 350px;
            margin-bottom: 2rem;
        }

        .workspace-header {
            background: linear-gradient(135deg, #f9f9fa 0%, #ffffff 100%);
            color: black;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .member-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 0.5rem;
        }

        .project-status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .timeline-item {
            position: relative;
            padding-left: 2rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0.5rem;
            width: 10px;
            height: 10px;
            background: #007bff;
            border-radius: 50%;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: 0.75rem;
            top: 1.5rem;
            width: 2px;
            height: calc(100% - 1rem);
            background: #e9ecef;
        }

        .timeline-item:last-child::after {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تحليلات مفصلة</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('workspaces.index') }}">مساحات العمل</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('workspaces.analytics.index') }}">التحليلات</a></li>
                            <li class="breadcrumb-item active">{{ $workspace->title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- رأس مساحة العمل -->
        <div class="workspace-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-3">
                            <i class="fas fa-project-diagram fa-3x"></i>
                        </div>
                        <div>
                            <h1 class="mb-1">{{ $workspace->title }}</h1>
                            <p class="mb-0 opacity-75">{{ $workspace->description ?: 'لا يوجد وصف' }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-crown me-2"></i>
                        <span>مالك المساحة: {{ $workspace->admin->name }}</span>
                        @if($workspace->is_primary)
                            <span class="badge badge-warning ms-3">مساحة رئيسية</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <a href="{{ route('workspaces.show', $workspace->id) }}" class="btn btn-light">
                            <i class="fas fa-eye me-1"></i>عرض المساحة
                        </a>
                        <a href="{{ route('workspaces.analytics.export.single', $workspace->id) }}" class="btn btn-light">
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
                        <h3 class="mb-1">{{ $stats['projects']['total'] }}</h3>
                        <p class="text-muted mb-0">إجمالي المشاريع</p>
                        <div class="progress mt-2" style="height: 4px;">
                            @php
                                $completedPercentage = $stats['projects']['total'] > 0 ?
                                    ($stats['projects']['completed'] / $stats['projects']['total']) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-primary" style="width: {{ $completedPercentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body text-center">
                        <div class="stat-icon text-success mb-3">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <h3 class="mb-1">{{ $stats['projects']['active'] }}</h3>
                        <p class="text-muted mb-0">مشاريع نشطة</p>
                        <small class="text-success">
                            @if($stats['projects']['total'] > 0)
                                {{ round(($stats['projects']['active'] / $stats['projects']['total']) * 100, 1) }}%
                            @else
                                0%
                            @endif
                            من الإجمالي
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body text-center">
                        <div class="stat-icon text-info mb-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="mb-1">{{ $stats['projects']['completed'] }}</h3>
                        <p class="text-muted mb-0">مشاريع مكتملة</p>
                        <small class="text-info">
                            @if($stats['projects']['total'] > 0)
                                {{ round(($stats['projects']['completed'] / $stats['projects']['total']) * 100, 1) }}%
                            @else
                                0%
                            @endif
                            معدل الإكمال
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body text-center">
                        <div class="stat-icon text-warning mb-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="mb-1">{{ $stats['members'] }}</h3>
                        <p class="text-muted mb-0">عدد الأعضاء</p>
                        <small class="text-warning">
                            أعضاء فعالون
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- الرسوم البيانية والتحليلات -->
        <div class="row">
            <!-- توزيع حالات المشاريع -->
            <div class="col-12 col-lg-6">
                <div class="card analytics-card">
                    <div class="card-header">
                        <h4 class="card-title">توزيع حالات المشاريع</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="priorityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الجدول الزمني للمشاريع -->
            <div class="col-12">
                <div class="card analytics-card">
                    <div class="card-header">
                        <h4 class="card-title">الجدول الزمني للمشاريع (آخر 6 أشهر)</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="timelineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- تفاصيل المشاريع والأعضاء -->
        <div class="row">
            <!-- قائمة المشاريع -->
            <div class="col-12 col-lg-8">
                <div class="card analytics-card">
                    <div class="card-header">
                        <h4 class="card-title">المشاريع الحالية</h4>
                    </div>
                    <div class="card-body">
                        @if($workspace->projects->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>اسم المشروع</th>
                                            <th>الحالة</th>
                                            <th>الأولوية</th>
                                            <th>التقدم</th>
                                            <th>التاريخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($workspace->projects as $project)
                                            <tr>
                                                <td>
                                                    <strong>{{ $project->title }}</strong>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusConfig = [
                                                            'in_progress' => ['class' => 'success', 'text' => 'قيد التنفيذ', 'icon' => 'play-circle'],
                                                            'completed' => ['class' => 'info', 'text' => 'مكتمل', 'icon' => 'check-circle'],
                                                            'on_hold' => ['class' => 'warning', 'text' => 'متوقف', 'icon' => 'pause-circle'],
                                                            'pending' => ['class' => 'secondary', 'text' => 'في الانتظار', 'icon' => 'clock']
                                                        ];
                                                        $config = $statusConfig[$project->status] ?? $statusConfig['pending'];
                                                    @endphp
                                                    <span class="badge badge-{{ $config['class'] }}">
                                                        <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                                        {{ $config['text'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $priorityConfig = [
                                                            'high' => ['class' => 'danger', 'text' => 'عالية'],
                                                            'medium' => ['class' => 'warning', 'text' => 'متوسطة'],
                                                            'low' => ['class' => 'info', 'text' => 'منخفضة']
                                                        ];
                                                        $priority = $priorityConfig[$project->priority] ?? $priorityConfig['medium'];
                                                    @endphp
                                                    <span class="badge badge-{{ $priority['class'] }}">
                                                        {{ $priority['text'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-2" style="width: 100px; height: 8px;">
                                                            @php
                                                                $progress = $project->progress_percentage ?? 0;
                                                                $progressClass = $progress >= 80 ? 'success' : ($progress >= 50 ? 'info' : ($progress >= 25 ? 'warning' : 'danger'));
                                                            @endphp
                                                            <div class="progress-bar bg-{{ $progressClass }}" style="width: {{ $progress }}%"></div>
                                                        </div>
                                                        <span class="small">{{ $progress }}%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="small">
                                                        @if($project->start_date)
                                                            <div><strong>البداية:</strong> {{ \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') }}</div>
                                                        @endif
                                                        @if($project->end_date)
                                                            <div><strong>النهاية:</strong> {{ \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد مشاريع</h5>
                                <p class="text-muted">لم يتم إنشاء أي مشاريع في هذه المساحة بعد</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- قائمة الأعضاء -->
            <div class="col-12 col-lg-4">
                <div class="card analytics-card">
                    <div class="card-header">
                        <h4 class="card-title">أعضاء المساحة</h4>
                    </div>
                    <div class="card-body">
                        @if($workspace->users->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($workspace->users as $user)
                                    <div class="list-group-item d-flex align-items-center border-0 px-0">
                                        <div class="member-avatar bg-primary me-3">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                            @if($workspace->admin_id == $user->id)
                                                <div>
                                                    <span class="badge badge-warning badge-sm">مالك المساحة</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            @php
                                                $userProjects = $workspace->projects->filter(function($project) use ($user) {
                                                    return $project->users && $project->users->contains($user->id);
                                                })->count();
                                            @endphp
                                            <div class="small text-muted">{{ $userProjects }} مشروع</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد أعضاء</h5>
                                <p class="text-muted">لم يتم إضافة أعضاء لهذه المساحة</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- معلومات إضافية -->
                <div class="card analytics-card mt-3">
                    <div class="card-header">
                        <h4 class="card-title">معلومات إضافية</h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary">{{ $stats['performance']['average_progress'] }}%</h4>
                                    <small class="text-muted">متوسط التقدم</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info">{{ $workspace->created_at->diffInDays() }}</h4>
                                <small class="text-muted">يوماً منذ الإنشاء</small>
                            </div>
                        </div>

                        <hr>

                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="fw-bold">تاريخ الإنشاء</div>
                                <small class="text-muted">{{ $workspace->created_at->format('Y-m-d H:i') }}</small>
                                <div class="small">{{ $workspace->created_at->diffForHumans() }}</div>
                            </div>

                            @if($workspace->updated_at != $workspace->created_at)
                                <div class="timeline-item">
                                    <div class="fw-bold">آخر تحديث</div>
                                    <small class="text-muted">{{ $workspace->updated_at->format('Y-m-d H:i') }}</small>
                                    <div class="small">{{ $workspace->updated_at->diffForHumans() }}</div>
                                </div>
                            @endif

                            @if($workspace->projects->count() > 0)
                                <div class="timeline-item">
                                    <div class="fw-bold">آخر مشروع</div>
                                    @php $lastProject = $workspace->projects->sortByDesc('created_at')->first(); @endphp
                                    <small class="text-muted">{{ $lastProject->title }}</small>
                                    <div class="small">{{ $lastProject->created_at->diffForHumans() }}</div>
                                </div>
                            @endif
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
                // رسم بياني لحالات المشاريع
                const statusCtx = document.getElementById('projectStatusChart').getContext('2d');
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['نشط', 'مكتمل', 'متوقف', 'في الانتظار'],
                        datasets: [{
                            data: [
                                {{ $stats['projects']['active'] }},
                                {{ $stats['projects']['completed'] }},
                                {{ $stats['projects']['on_hold'] }},
                                {{ $stats['projects']['pending'] }}
                            ],
                            backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#6c757d'],
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
                        labels: ['أولوية عالية', 'أولوية متوسطة', 'أولوية منخفضة'],
                        datasets: [{
                            label: 'عدد المشاريع',
                            data: [
                                {{ $stats['priorities']['high'] }},
                                {{ $stats['priorities']['medium'] }},
                                {{ $stats['priorities']['low'] }}
                            ],
                            backgroundColor: ['#dc3545', '#ffc107', '#17a2b8'],
                            borderColor: ['#dc3545', '#ffc107', '#17a2b8'],
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

                // رسم بياني للجدول الزمني
                const timelineCtx = document.getElementById('timelineChart').getContext('2d');
                new Chart(timelineCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(collect($stats['timeline'])->pluck('month')) !!},
                        datasets: [{
                            label: 'المشاريع المنشأة',
                            data: {!! json_encode(collect($stats['timeline'])->pluck('projects')) !!},
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
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
            }
        });
    </script>
@endsection
