@extends('master')

@section('title')
    عرض مساحة العمل
@stop

@section('head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
    <style>
        .workspace-section {
            flex: 1;
            max-width: 50%;
        }

        .floating-elements {
            position: absolute;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-elements:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: -2s;
        }

        .floating-elements:nth-child(2) {
            top: 40%;
            right: 10%;
            animation-delay: -4s;
        }

        .floating-elements:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: -6s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .workspace-card {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #f7f7f7 0%, #fffeff 100%);
            color: black;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .workspace-card-content {
            position: relative;
            z-index: 2;
        }

        .stats-info {
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .stats-info-primary {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(0, 123, 255, 0.05));
            border: 1px solid rgba(0, 123, 255, 0.2);
        }

        .stats-info-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .stats-info-warning {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));
            border: 1px solid rgba(255, 193, 7, 0.2);
        }

        .stats-amount {
            font-size: 2rem;
            font-weight: 700;
            margin: 1rem 0;
        }

        .workspace-details {
            padding: 1rem;
        }

        .workspace-name {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .workspace-id {
            opacity: 0.8;
            font-size: 1rem;
            margin-right: 0.5rem;
        }

        .icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .primary-icon {
            background: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }

        .success-icon {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .warning-icon {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        /* Timeline Styles */
        .timeline {
            position: relative;
            padding-right: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            right: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #007bff, transparent);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-dot {
            position: absolute;
            right: -25px;
            top: 20px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #007bff;
        }

        .note-box {
            transition: all 0.3s ease;
            border-right: 3px solid #007bff;
        }

        .note-box:hover {
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
            transform: translateX(-2px);
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Badge Colors */
        .badge-primary {
            background-color: #007bff !important;
        }

        .badge-success {
            background-color: #28a745 !important;
        }

        .badge-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .badge-danger {
            background-color: #dc3545 !important;
        }

        .badge-info {
            background-color: #17a2b8 !important;
        }

        .badge-secondary {
            background-color: #6c757d !important;
        }

        /* Button Improvements */
        .btn-outline-primary:hover,
        .btn-outline-success:hover,
        .btn-outline-info:hover,
        .btn-outline-warning:hover,
        .btn-outline-danger:hover,
        .btn-outline-dark:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }

        /* Table Improvements */
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }

        .table td {
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .member-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #007bff, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .project-progress {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
        }

        .project-progress .progress-bar {
            border-radius: 4px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .workspace-section {
                max-width: 100%;
                margin: 0 0 1rem 0;
            }

            .workspace-card-content {
                flex-direction: column;
                text-align: center;
            }

            .workspace-details {
                text-align: center !important;
            }

            .card-title {
                flex-direction: column;
                gap: 1rem !important;
            }

            .card-title .btn {
                width: 100%;
                min-width: auto !important;
            }

            .card-title .vr {
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    @if (session('toast_message'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastr.{{ session('toast_type', 'success') }}('{{ session('toast_message') }}', '', {
                    positionClass: 'toast-bottom-left',
                    closeButton: true,
                    progressBar: true,
                    timeOut: 5000
                });
            });
        </script>
    @endif

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض مساحة العمل</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('workspaces.index') }}">مساحات العمل</a></li>
                            <li class="breadcrumb-item active">{{ $workspace->title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <!-- معلومات مساحة العمل -->
    <div class="card workspace-card">
        <div class="floating-elements"></div>
        <div class="floating-elements"></div>
        <div class="floating-elements"></div>

        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start workspace-card-content flex-wrap">
                <!-- إحصائيات مساحة العمل -->
                <div class="workspace-section mx-2">
                    <div class="stats-info text-center stats-info-primary">
                        <div class="icon-wrapper primary-icon">
                            <i class="fa fa-project-diagram"></i>
                        </div>
                        <div class="workspace-section">
                            <small class="text-muted d-block">إجمالي المشاريع</small>
                            <div class="stats-amount" style="color: #007bff">
                                <span id="totalProjects">0</span>
                            </div>
                            <div class="mt-1">
                                @if($workspace->is_primary)
                                    <small class="badge badge-warning">مساحة رئيسية</small>
                                @else
                                    <small class="badge badge-info">مساحة فرعية</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تفاصيل مساحة العمل -->
                <div class="workspace-section">
                    <div class="workspace-details text-end">
                        <div class="workspace-name">
                            {{ $workspace->title }}
                            <span class="workspace-id"># {{ $workspace->id }}</span>
                        </div>

                        <div class="account-info">
                            <small class="text-muted d-block mb-1">
                                <i class="fa fa-user me-1"></i>
                                المالك:
                            </small>

                            <div class="account-link-wrapper">
                                <div class="d-flex align-items-center justify-content-end">
                                    <div class="member-avatar me-2">
                                        {{ substr($workspace->admin->name ?? 'غ', 0, 1) }}
                                    </div>
                                    <span class="fw-bold">{{ $workspace->admin->name ?? 'غير محدد' }}</span>
                                </div>
                            </div>
                        </div>

                        @if($workspace->description)
                            <div class="mt-2">
                                <small class="text-muted">{{ Str::limit($workspace->description, 100) }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- أزرار الإجراءات -->
    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2 flex-wrap">
            @if(Auth::id() === $workspace->admin_id)
                <a href="{{ route('workspaces.edit', $workspace->id) }}"
                    class="btn btn-outline-primary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                    style="min-width: 90px;">
                    تعديل البيانات <i class="fa fa-edit ms-1"></i>
                </a>
            @endif

            <a href="#"
                class="btn btn-outline-success btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" onclick="createNewProject()">
                إضافة مشروع <i class="fa fa-plus-circle ms-1"></i>
            </a>
            <div class="vr"></div>

            <a href="#"
                class="btn btn-outline-info btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-bs-toggle="modal" data-bs-target="#inviteMemberModal">
                دعوة عضو <i class="fa fa-user-plus ms-1"></i>
            </a>
            <div class="vr"></div>

            <button onclick="window.print()"
                class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                طباعة <i class="fa fa-print ms-1"></i>
            </button>
            <div class="vr"></div>

            @if(Auth::id() === $workspace->admin_id)
                <a href="#"
                    class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
                    style="min-width: 90px;" data-bs-toggle="modal" data-bs-target="#modal_DELETE">
                    حذف <i class="fa fa-trash ms-1"></i>
                </a>
            @endif
        </div>

        <!-- التبويبات -->
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">
                        <i class="fa fa-info-circle me-1"></i>
                        <span>التفاصيل</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#projects" role="tab">
                        <i class="fa fa-project-diagram me-1"></i>
                        <span>المشاريع</span>
                        <span class="badge bg-primary ms-1" id="projectsCount">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#members" role="tab">
                        <i class="fa fa-users me-1"></i>
                        <span>الأعضاء</span>
                        <span class="badge bg-success ms-1" id="membersCount">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#analytics" role="tab">
                        <i class="fa fa-chart-bar me-1"></i>
                        <span>التحليلات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                        <i class="fa fa-history me-1"></i>
                        <span>النشاط الأخير</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                <div class="tab-pane active" id="details" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fa fa-info-circle me-2"></i>معلومات أساسية</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>اسم المساحة:</strong></td>
                                            <td>{{ $workspace->title }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الوصف:</strong></td>
                                            <td>{{ $workspace->description ?? 'لا يوجد وصف' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>النوع:</strong></td>
                                            <td>
                                                @if($workspace->is_primary)
                                                    <span class="badge badge-warning">مساحة رئيسية</span>
                                                @else
                                                    <span class="badge badge-info">مساحة فرعية</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>تاريخ الإنشاء:</strong></td>
                                            <td>{{ $workspace->created_at->format('Y/m/d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>آخر تحديث:</strong></td>
                                            <td>{{ $workspace->updated_at->format('Y/m/d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fa fa-chart-line me-2"></i>الإحصائيات</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="stats-info stats-info-primary p-3">
                                                <div class="icon-wrapper primary-icon">
                                                    <i class="fa fa-tasks"></i>
                                                </div>
                                                <h4 id="activeProjects">0</h4>
                                                <small class="text-muted">مشاريع نشطة</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="stats-info stats-info-success p-3">
                                                <div class="icon-wrapper success-icon">
                                                    <i class="fa fa-check-circle"></i>
                                                </div>
                                                <h4 id="completedProjects">0</h4>
                                                <small class="text-muted">مشاريع مكتملة</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stats-info stats-info-warning p-3">
                                                <div class="icon-wrapper warning-icon">
                                                    <i class="fa fa-percentage"></i>
                                                </div>
                                                <h4 id="completionRate">0%</h4>
                                                <small class="text-muted">معدل الإنجاز</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stats-info p-3" style="background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(23, 162, 184, 0.05)); border: 1px solid rgba(23, 162, 184, 0.2);">
                                                <div class="icon-wrapper" style="background: rgba(23, 162, 184, 0.1); color: #17a2b8;">
                                                    <i class="fa fa-users"></i>
                                                </div>
                                                <h4 id="totalMembers">0</h4>
                                                <small class="text-muted">إجمالي الأعضاء</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب المشاريع -->
              <!-- تبويب المشاريع -->
<div class="tab-pane" id="projects" role="tabpanel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">مشاريع مساحة العمل</h5>
        <div class="d-flex gap-2">
            <select class="form-control form-control-sm" id="projectStatusFilter" style="width: auto;">
                <option value="">جميع الحالات</option>
                <option value="pending">في الانتظار</option>
                <option value="in_progress">قيد التنفيذ</option>
                <option value="completed">مكتمل</option>
                <option value="on_hold">متوقف</option>
            </select>
            <button class="btn btn-sm btn-primary" onclick="createNewProject()">
                <i class="fas fa-plus me-1"></i>مشروع جديد
            </button>
        </div>
    </div>

    <div id="projectsList">
        <!-- سيتم تحميل المشاريع هنا -->
    </div>
</div>
                <!-- تبويب الأعضاء -->
                <div class="tab-pane" id="members" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">أعضاء مساحة العمل</h5>
                    </div>

                    <div id="membersList">
                        <!-- سيتم تحميل الأعضاء هنا عبر AJAX -->
                    </div>
                </div>

                <!-- تبويب التحليلات -->
                <div class="tab-pane" id="analytics" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>توزيع حالات المشاريع</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="projectStatusChart" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>الأعضاء الأكثر نشاطاً</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="membersActivityChart" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب النشاط -->
                <div class="tab-pane" id="activity" role="tabpanel">
                    <h5 class="mb-3">النشاط الأخير</h5>
                    <div id="activityList">
                        <!-- سيتم تحميل النشاط هنا -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="modal_DELETE" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">حذف مساحة العمل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من حذف هذه المساحة؟</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>تحذير:</strong> سيؤدي حذف المساحة إلى حذف جميع المشاريع المرتبطة بها.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-danger" onclick="deleteWorkspace()">حذف نهائي</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Invite Member -->
 <!-- Modal Invite Member المحدث -->
<div class="modal fade" id="inviteMemberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">دعوة عضو جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- طرق الدعوة -->
                <ul class="nav nav-tabs mb-3" id="inviteMethodTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="email-tab" data-bs-toggle="tab" data-bs-target="#email-method" type="button" role="tab">
                            <i class="fas fa-envelope me-1"></i>بالبريد الإلكتروني
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users-method" type="button" role="tab">
                            <i class="fas fa-user-friends me-1"></i>اختيار من المستخدمين
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="inviteMethodContent">
                    <!-- الدعوة بالبريد الإلكتروني -->
                    <div class="tab-pane fade show active" id="email-method" role="tabpanel">
                        <form id="inviteMemberForm">
                            <div class="mb-3">
                                <label for="member_email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="member_email" placeholder="أدخل البريد الإلكتروني للعضو" required>
                                <small class="form-text text-muted">
                                    سيتم البحث عن المستخدم بهذا البريد الإلكتروني وإرسال دعوة له
                                </small>
                            </div>
                        </form>
                    </div>

                    <!-- اختيار من المستخدمين المتاحين -->
                    <div class="tab-pane fade" id="users-method" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label">المستخدمين المتاحين للدعوة</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="userSearchInput" placeholder="ابحث عن مستخدم...">
                                <button class="btn btn-outline-secondary" type="button" id="refreshUsersBtn">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>

                            <div id="availableUsersList" style="max-height: 300px; overflow-y: auto;">
                                <!-- سيتم تحميل المستخدمين هنا -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    بعد قبول الدعوة، سيتمكن العضو من المشاركة في مشاريع هذه المساحة
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-info" onclick="inviteMember()">
                    <i class="fas fa-paper-plane me-1"></i>إرسال الدعوة
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

// JavaScript مُصحح مع المسارات الصحيحة

$(document).ready(function() {
    const workspaceId = {{ $workspace->id }};
    console.log('Workspace ID:', workspaceId);

    // تحميل البيانات الأولية
    loadWorkspaceStats();
    loadProjects();
    loadMembers();

    // معالجة تبويبات
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr("href");
        if (target === '#analytics') {
            loadAnalytics();
        } else if (target === '#activity') {
            loadActivity();
        }
    });

    // فلترة المشاريع
    $('#projectStatusFilter').change(function() {
        loadProjects();
    });

    // تحميل المستخدمين المتاحين
    $('#users-tab').on('click', function() {
        loadAvailableUsers();
    });
});

function loadWorkspaceStats() {
    const workspaceId = {{ $workspace->id }};
    console.log('Loading stats for workspace:', workspaceId);

    // المسار الصحيح: /workspaces/api/{workspace}/stats
    $.ajax({
        url: `/workspaces/api/${workspaceId}/stats`,
        method: 'GET',
        success: function(response) {
            console.log('Stats response:', response);
            if (response.success) {
                $('#totalProjects').text(response.data.projects.total);
                $('#activeProjects').text(response.data.projects.active);
                $('#completedProjects').text(response.data.projects.completed);
                $('#totalMembers').text(response.data.members.total);
                $('#completionRate').text(response.data.projects.completion_rate + '%');
                $('#projectsCount').text(response.data.projects.total);
                $('#membersCount').text(response.data.members.total);
            }
        },
        error: function(xhr, status, error) {
            console.error('Stats error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                url: `/workspaces/api/${workspaceId}/stats`,
                error: error
            });
            // عرض قيم افتراضية في حالة الخطأ
            $('#totalProjects, #activeProjects, #completedProjects, #totalMembers').text('--');
            $('#completionRate').text('0%');
        }
    });
}

function loadProjects() {
    const workspaceId = {{ $workspace->id }};
    const status = $('#projectStatusFilter').val();
    console.log('Loading projects for workspace:', workspaceId, 'with status:', status);

    // المسار الصحيح: /workspaces/api/{workspace}/projects
    $.ajax({
        url: `/workspaces/api/${workspaceId}/projects`,
        method: 'GET',
        data: { status: status },
        success: function(response) {
            console.log('Projects response:', response);
            if (response.success) {
                displayProjects(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Projects error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                url: `/workspaces/api/${workspaceId}/projects`,
                error: error
            });
            $('#projectsList').html(`
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    حدث خطأ أثناء تحميل المشاريع. تأكد من وجود الدوال المطلوبة في Controller.
                </div>
            `);
        }
    });
}

function loadMembers() {
    const workspaceId = {{ $workspace->id }};
    console.log('Loading members for workspace:', workspaceId);

    // المسار الصحيح: /workspaces/api/{workspace}/members
    $.ajax({
        url: `/workspaces/api/${workspaceId}/members`,
        method: 'GET',
        success: function(response) {
            console.log('Members response:', response);
            if (response.success) {
                displayMembers(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Members error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                url: `/workspaces/api/${workspaceId}/members`,
                error: error
            });
            $('#membersList').html(`
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    حدث خطأ أثناء تحميل الأعضاء. تأكد من وجود الدوال المطلوبة في Controller.
                </div>
            `);
        }
    });
}

function loadAvailableUsers() {
    const workspaceId = {{ $workspace->id }};
    console.log('Loading available users for workspace:', workspaceId);

    $('#availableUsersList').html(`
        <div class="text-center py-3">
            <i class="fas fa-spinner fa-spin fa-2x text-muted mb-2"></i>
            <div class="text-muted">جاري تحميل المستخدمين...</div>
        </div>
    `);

    // المسار الصحيح: /workspaces/{workspace}/available-users (هذا موجود مباشرة)
    $.ajax({
        url: `/workspaces/${workspaceId}/available-users`,
        method: 'GET',
        success: function(response) {
            console.log('Available users response:', response);
            if (response.success) {
                availableUsers = response.data;
                displayUsers(availableUsers);
            } else {
                showUsersError('حدث خطأ أثناء تحميل المستخدمين');
            }
        },
        error: function(xhr, status, error) {
            console.error('Available users error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                url: `/workspaces/${workspaceId}/available-users`,
                error: error
            });
            showUsersError('حدث خطأ أثناء تحميل المستخدمين - تحقق من وجود الدالة في Controller');
        }
    });
}

function displayProjects(projects) {
    let html = '';

    if (projects.length === 0) {
        html = `
            <div class="text-center py-5">
                <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد مشاريع</h5>
                <p class="text-muted">لم يتم إنشاء أي مشاريع في هذه المساحة بعد</p>
                <button class="btn btn-primary" onclick="createNewProject()">
                    <i class="fas fa-plus me-1"></i>إنشاء مشروع جديد
                </button>
            </div>
        `;
    } else {
        projects.forEach(project => {
            const statusClass = getStatusClass(project.status);
            const statusText = getStatusText(project.status);

            html += `
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">
                                <a href="/projects/${project.id}" class="text-decoration-none">
                                    ${project.title}
                                </a>
                            </h6>
                            <small class="text-muted">${project.description || 'لا يوجد وصف'}</small>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge ${statusClass}">${statusText}</span>
                            <button class="btn btn-sm btn-outline-primary" onclick="inviteToProject(${project.id}, '${project.title}')">
                                <i class="fas fa-user-plus"></i> دعوة عضو
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="mb-2">
                                    <i class="fas fa-users text-primary me-1"></i>
                                    الأعضاء (${project.members_count})
                                </h6>
                                ${displayProjectMembers(project.members, project.id)}
                            </div>
                            ${project.pending_count > 0 ? `
                            <div class="col-md-4">
                                <h6 class="mb-2">
                                    <i class="fas fa-clock text-warning me-1"></i>
                                    دعوات معلقة (${project.pending_count})
                                </h6>
                                ${displayPendingInvites(project.pending_invites)}
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
    }

    $('#projectsList').html(html);
}

function displayProjectMembers(members, projectId) {
    if (members.length === 0) {
        return '<p class="text-muted small">لا يوجد أعضاء في هذا المشروع</p>';
    }

    let html = '<div class="d-flex flex-wrap gap-2">';
    members.forEach(member => {
        const roleText = getRoleText(member.role);
        const roleClass = getRoleClass(member.role);

        html += `
            <div class="d-flex align-items-center border rounded p-2" style="min-width: 200px;">
                <div class="member-avatar me-2" style="width: 32px; height: 32px; font-size: 14px;">
                    ${member.name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="font-size: 13px;">${member.name}</div>
                    <small class="badge ${roleClass}" style="font-size: 10px;">${roleText}</small>
                </div>
            </div>
        `;
    });
    html += '</div>';
    return html;
}

function displayPendingInvites(invites) {
    if (invites.length === 0) {
        return '';
    }

    let html = '<div class="list-group list-group-flush">';
    invites.forEach(invite => {
        const roleText = getRoleText(invite.role);
        const isExpired = new Date(invite.expires_at) < new Date();

        html += `
            <div class="list-group-item p-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold" style="font-size: 13px;">${invite.email}</div>
                        <small class="text-muted">${roleText}</small>
                    </div>
                    ${isExpired ?
                        '<small class="badge bg-danger">منتهية</small>' :
                        '<small class="badge bg-warning">معلقة</small>'
                    }
                </div>
            </div>
        `;
    });
    html += '</div>';
    return html;
}

function getRoleText(role) {
    const roles = {
        'manager': 'مدير',
        'member': 'عضو',
        'viewer': 'مشاهد'
    };
    return roles[role] || 'عضو';
}

function getRoleClass(role) {
    const classes = {
        'manager': 'badge-danger',
        'member': 'badge-primary',
        'viewer': 'badge-secondary'
    };
    return classes[role] || 'badge-secondary';
}

// دالة دعوة عضو للمشروع
function inviteToProject(projectId, projectTitle) {
    Swal.fire({
        title: `دعوة عضو إلى: ${projectTitle}`,
        html: `
            <div class="text-start">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" id="invite_email" class="form-control mb-3" placeholder="أدخل البريد الإلكتروني">

                <label class="form-label">الدور في المشروع</label>
                <select id="invite_role" class="form-control mb-3">
                    <option value="member">عضو</option>
                    <option value="manager">مدير</option>
                    <option value="viewer">مشاهد</option>
                </select>

                <label class="form-label">رسالة اختيارية</label>
                <textarea id="invite_message" class="form-control" rows="3" placeholder="أضف رسالة للمدعو (اختياري)"></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'إرسال الدعوة',
        cancelButtonText: 'إلغاء',
        width: 600,
        preConfirm: () => {
            const email = $('#invite_email').val();
            const role = $('#invite_role').val();
            const message = $('#invite_message').val();

            if (!email) {
                Swal.showValidationMessage('الرجاء إدخال البريد الإلكتروني');
                return false;
            }

            return { email, role, message };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            sendProjectInvite(projectId, result.value);
        }
    });
}

function sendProjectInvite(projectId, data) {
    Swal.fire({
        title: 'جاري الإرسال...',
        text: 'يرجى الانتظار',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: `/projects/${projectId}/invite`,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            email: data.email,
            role: data.role,
            invite_message: data.message
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم إرسال الدعوة!',
                    text: response.message,
                    timer: 3000
                });

                // تحديث قائمة المشاريع
                loadProjects();
            }
        },
        error: function(xhr) {
            let errorMsg = 'حدث خطأ أثناء إرسال الدعوة';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: errorMsg
            });
        }
    });
}

function displayMembers(members) {
    let html = '';

    if (members.length === 0) {
        html = `
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">لا يوجد أعضاء</h5>
                <p class="text-muted">لا يوجد أعضاء مشاركون في مشاريع هذه المساحة</p>
            </div>
        `;
    } else {
        html = '<div class="row">';
        members.forEach(member => {
            html += `
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center p-3 border rounded">
                        <div class="member-avatar me-3">
                            ${member.name.charAt(0)}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">${member.name}</h6>
                            <small class="text-muted">${member.email}</small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">المشاريع</small>
                            <span class="badge badge-primary">${member.projects_count || 0}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
    }

    $('#membersList').html(html);
}

function displayUsers(users) {
    if (users.length === 0) {
        $('#availableUsersList').html(`
            <div class="text-center py-4 text-muted">
                <i class="fas fa-user-friends fa-2x mb-2 d-block"></i>
                <div>لا يوجد مستخدمين متاحين للدعوة</div>
                <small>جميع المستخدمين المسجلين إما أعضاء بالفعل أو لديهم دعوات معلقة</small>
            </div>
        `);
        return;
    }

    let html = '<div class="list-group">';
    users.forEach(user => {
        const isSelected = selectedUser && selectedUser.id === user.id;
        html += `
            <div class="list-group-item list-group-item-action ${isSelected ? 'active' : ''}"
                 style="cursor: pointer;"
                 onclick="selectUser(${user.id}, '${user.name}', '${user.email}')">
                <div class="d-flex align-items-center">
                    <div class="member-avatar me-3" style="width: 40px; height: 40px;">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${user.name}</div>
                        <small class="text-muted">${user.email}</small>
                    </div>
                    ${isSelected ? '<i class="fas fa-check text-white"></i>' : ''}
                </div>
            </div>
        `;
    });
    html += '</div>';

    $('#availableUsersList').html(html);
}

function showUsersError(message) {
    $('#availableUsersList').html(`
        <div class="text-center py-4 text-danger">
            <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
            <div>${message}</div>
            <button class="btn btn-outline-primary btn-sm mt-2" onclick="loadAvailableUsers()">
                <i class="fas fa-redo me-1"></i>إعادة المحاولة
            </button>
        </div>
    `);
}

function loadAnalytics() {
    console.log('Loading analytics...');
    // يمكن إضافة تحميل التحليلات هنا
    const ctx1 = document.getElementById('projectStatusChart');
    const ctx2 = document.getElementById('membersActivityChart');

    if (ctx1 && ctx2) {
        new Chart(ctx1.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['في الانتظار', 'قيد التنفيذ', 'مكتمل', 'متوقف'],
                datasets: [{
                    data: [0, 0, 0, 0],
                    backgroundColor: ['#ffc107', '#007bff', '#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        new Chart(ctx2.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'عدد المشاريع',
                    data: [],
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
}

function loadActivity() {
    console.log('Loading activity...');
    $('#activityList').html(`
        <div class="text-center py-5">
            <i class="fas fa-history fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">قريباً</h5>
            <p class="text-muted">ستتوفر سجلات النشاط قريباً</p>
        </div>
    `);
}

function deleteWorkspace() {
    const workspaceId = {{ $workspace->id }};
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: 'سيتم حذف مساحة العمل وجميع مشاريعها نهائياً',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/workspaces/${workspaceId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'تم الحذف!',
                            text: response.message,
                            icon: 'success'
                        }).then(() => {
                            window.location.href = '{{ route("workspaces.index") }}';
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Delete error:', xhr);
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء حذف مساحة العمل',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

function createNewProject() {
    const workspaceId = {{ $workspace->id }};
    window.location.href = `/projects/create?workspace_id=${workspaceId}`;
}

function inviteMember() {
    const workspaceId = {{ $workspace->id }};
    let email = '';

    // تحديد البريد الإلكتروني حسب الطريقة المختارة
    if ($('#email-tab').hasClass('active')) {
        email = $('#member_email').val().trim();
    } else if ($('#users-tab').hasClass('active') && selectedUser) {
        email = selectedUser.email;
    }

    if (!email) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'الرجاء إدخال البريد الإلكتروني أو اختيار مستخدم',
            confirmButtonText: 'موافق'
        });
        return;
    }

    console.log('Sending invite to:', email, 'for workspace:', workspaceId);

    Swal.fire({
        title: 'جاري الإرسال...',
        text: 'يرجى الانتظار',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // المسار الصحيح: /workspaces/{workspace}/invite (هذا موجود مباشرة)
    $.ajax({
        url: `/workspaces/${workspaceId}/invite`,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            email: email
        },
        success: function(response) {
            console.log('Invite response:', response);
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم إرسال الدعوة!',
                    text: response.message,
                    timer: 5000,
                    timerProgressBar: true
                });

                $('#inviteMemberModal').modal('hide');
                $('#member_email').val('');

                if (typeof loadMembers === 'function') {
                    setTimeout(loadMembers, 1000);
                }
            }
        },
        error: function(xhr) {
            console.error('Invite error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                response: xhr.responseJSON,
                url: `/workspaces/${workspaceId}/invite`
            });

            let errorMsg = 'حدث خطأ أثناء إرسال الدعوة';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: errorMsg,
                confirmButtonText: 'موافق'
            });
        }
    });
}

// متغيرات عامة
let availableUsers = [];
let selectedUser = null;

// دالة اختيار المستخدم
window.selectUser = function(id, name, email) {
    selectedUser = { id, name, email };
    // إعادة رسم القائمة لإظهار الاختيار
    displayUsers(availableUsers);

    // تعبئة البريد الإلكتروني في التبويب الأول
    $('#member_email').val(email);
};

// دوال مساعدة
function getStatusClass(status) {
    const classes = {
        'pending': 'badge-warning',
        'in_progress': 'badge-primary',
        'completed': 'badge-success',
        'on_hold': 'badge-danger'
    };
    return classes[status] || 'badge-secondary';
}

function getStatusText(status) {
    const texts = {
        'pending': 'في الانتظار',
        'in_progress': 'قيد التنفيذ',
        'completed': 'مكتمل',
        'on_hold': 'متوقف'
    };
    return texts[status] || 'غير محدد';
}
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@endsection
