@extends('master')

@section('title', 'تحليلات المشاريع')
@section('css')

<style>
/* أنماط خاصة بنظام دعوة الأعضاء في صفحة الفهرس */
.bulk-actions-bar {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    padding: 15px 25px;
    border-radius: 50px;
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
    z-index: 1000;
    display: none;
    animation: slideUpFade 0.3s ease;
}

@keyframes slideUpFade {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

.bulk-actions-bar .selected-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 5px 10px;
    border-radius: 15px;
    margin-left: 10px;
    font-weight: bold;
}

.bulk-invite-modal .user-selection-item {
    transition: all 0.2s ease;
    cursor: pointer;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 8px;
}

.bulk-invite-modal .user-selection-item:hover {
    background-color: #f8f9fa;
    border-color: #007bff;
}

.bulk-invite-modal .user-selection-item.selected {
    background-color: #e3f2fd;
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
}

.user-avatar-invite {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #007bff, #0056b3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-right: 12px;
}

.member-info {
    flex: 1;
}

.member-name {
    font-weight: 600;
    margin-bottom: 2px;
    color: #2c3e50;
}

.member-email {
    font-size: 13px;
    color: #6c757d;
}

.selected-projects-summary {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}

.project-chip {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 20px;
    padding: 5px 12px;
    margin: 2px;
    display: inline-block;
    font-size: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.project-actions-toolbar {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 10px 15px;
    margin-bottom: 15px;
    border: 1px solid #dee2e6;
}

/* تحسينات للجدول */
.project-row.selected {
    background-color: #f0f8ff !important;
    border-left: 4px solid #007bff;
}

.table-hover tbody tr.selected:hover {
    background-color: #e3f2fd !important;
}

/* تحسينات responsive للمودال */
@media (max-width: 768px) {
    .bulk-actions-bar {
        left: 10px;
        right: 10px;
        transform: none;
        border-radius: 10px;
        padding: 10px 15px;
    }

    .bulk-actions-bar .btn {
        padding: 8px 12px;
        font-size: 12px;
    }
}
</style>
@endsection
@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تحليلات المشاريع</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">المشاريع</a></li>
                            <li class="breadcrumb-item active">التحليلات</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- إحصائيات عامة -->
        <div class="row mb-4" id="statsContainer">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="stat-icon bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 class="mb-0" id="totalProjects">0</h3>
                                        <p class="mb-0 text-muted">إجمالي المشاريع</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-light-primary" id="projectsGrowth">+0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="stat-icon bg-success text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-play-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 class="mb-0" id="activeProjects">0</h3>
                                        <p class="mb-0 text-muted">المشاريع النشطة</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-light-success" id="activeGrowth">+0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="stat-icon bg-info text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 class="mb-0" id="completionRate">0%</h3>
                                        <p class="mb-0 text-muted">معدل الإكمال</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-light-info" id="completionGrowth">+0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="stat-icon bg-warning text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 class="mb-0" id="totalBudget">0</h3>
                                        <p class="mb-0 text-muted">إجمالي الميزانية</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-light-warning" id="budgetGrowth">+0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- شريط الأدوات العلوي -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <!-- زر التصدير -->
                        <button class="btn btn-outline-success btn-sm d-flex align-items-center rounded-pill px-3" id="exportAnalyticsBtn">
                            <i class="fas fa-file-excel me-1"></i>تصدير التحليلات
                        </button>

                        <!-- زر الطباعة -->
                        <button class="btn btn-outline-secondary btn-sm d-flex align-items-center rounded-pill px-3" id="printAnalyticsBtn">
                            <i class="fas fa-print me-1"></i>طباعة
                        </button>

                        <!-- زر التحديث -->
                        <button class="btn btn-outline-primary btn-sm d-flex align-items-center rounded-pill px-3" id="refreshAnalyticsBtn">
                            <i class="fas fa-sync-alt me-1"></i>تحديث
                        </button>
                    </div>

                    <!-- معلومات النتائج -->
                    <div class="d-flex align-items-center gap-2" id="top-pagination-info">
                        <span class="text-muted mx-2 results-info">0 مشروع</span>
                    </div>
                    <div class="d-flex align-items-center gap-2" id="top-pagination-info">
                        <a href="{{ route('projects.create') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center rounded-pill px-3" id="refreshAnalyticsBtn">
                            <i class="fas fa-plus me-1"></i>إنشاء مشروع
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج البحث والفلترة -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center p-2">
                <div class="d-flex gap-2">
                    <span class="hide-button-text">بحث وتصفية</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-sm" onclick="toggleSearchFields(this)">
                        <i class="fa fa-times"></i>
                        <span class="hide-button-text">اخفاء</span>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                        data-bs-target="#advancedSearchForm" onclick="toggleSearchText(this)">
                        <i class="fa fa-filter"></i>
                        <span class="button-text">متقدم</span>
                    </button>
                    <button type="button" id="resetSearch" class="btn btn-outline-warning btn-sm">
                        <i class="fa fa-refresh"></i>
                        إعادة تعيين
                    </button>
                </div>
            </div>

            <div class="card-body">
                <form class="form" id="searchForm">
                    <div class="row g-3" id="basicSearchFields">
                        <!-- 1. عنوان المشروع -->
                        <div class="col-md-4 mb-3">
                            <input type="text" id="title" class="form-control" placeholder="عنوان المشروع"
                                name="title">
                        </div>

                        <!-- 2. مساحة العمل -->
                        <div class="col-md-4 mb-3">
                            <select name="workspace_id" class="form-control select2" id="workspace_id">
                                <option value="">اختر مساحة العمل</option>
                                @foreach ($workspaces as $workspace)
                                    <option value="{{ $workspace->id }}">{{ $workspace->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. الحالة -->
                        <div class="col-md-4 mb-3">
                            <select name="status" class="form-control select2" id="status">
                                <option value="">الحالة</option>
                                <option value="new">جديد</option>
                                <option value="in_progress">قيد التنفيذ</option>
                                <option value="completed">مكتمل</option>
                                <option value="on_hold">متوقف</option>
                            </select>
                        </div>
                    </div>

                    <!-- البحث المتقدم -->
                    <div class="collapse" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                            <!-- 4. الأولوية -->
                            <div class="col-md-4 mb-3">
                                <select name="priority" class="form-control select2" id="priority">
                                    <option value="">الأولوية</option>
                                    <option value="low">منخفضة</option>
                                    <option value="medium">متوسطة</option>
                                    <option value="high">عالية</option>
                                    <option value="urgent">عاجلة</option>
                                </select>
                            </div>

                            <!-- 5. منشئ المشروع -->
                            <div class="col-md-4 mb-3">
                                <select name="created_by" class="form-control select2" id="created_by">
                                    <option value="">منشئ المشروع</option>
                                    @foreach ($creators as $creator)
                                        <option value="{{ $creator->id }}">{{ $creator->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 6. من (التاريخ) -->
                            <div class="col-md-4 mb-3">
                                <input type="date" id="from_date" class="form-control" placeholder="من"
                                    name="from_date">
                            </div>

                            <!-- 7. إلى (التاريخ) -->
                            <div class="col-md-4 mb-3">
                                <input type="date" id="to_date" class="form-control" placeholder="إلى"
                                    name="to_date">
                            </div>

                            <!-- 8. الميزانية (من) -->
                            <div class="col-md-4 mb-3">
                                <input type="number" id="budget_min" class="form-control"
                                    placeholder="الميزانية (من)" name="budget_min" min="0" step="0.01">
                            </div>

                            <!-- 9. الميزانية (إلى) -->
                            <div class="col-md-4 mb-3">
                                <input type="number" id="budget_max" class="form-control"
                                    placeholder="الميزانية (إلى)" name="budget_max" min="0" step="0.01">
                            </div>

                            <!-- 10. نسبة الإكمال (من) -->
                            <div class="col-md-4 mb-3">
                                <input type="number" id="progress_min" class="form-control"
                                    placeholder="نسبة الإكمال (من %)" name="progress_min" min="0" max="100">
                            </div>

                            <!-- 11. نسبة الإكمال (إلى) -->
                            <div class="col-md-4 mb-3">
                                <input type="number" id="progress_max" class="form-control"
                                    placeholder="نسبة الإكمال (إلى %)" name="progress_max" min="0" max="100">
                            </div>
                        </div>
                    </div>

                    <!-- الأزرار -->
                    <div class="form-actions mt-2">
                        <button type="submit" class="btn btn-primary">بحث</button>
                        <button type="button" id="resetSearchBtn" class="btn btn-outline-warning">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- بطاقة النتائج -->
        <div class="card">
            <div class="card-body position-relative">
                <!-- مؤشر التحميل -->
                <div id="loadingIndicator" class="loading-overlay" style="display: none;">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">جاري التحميل...</span>
                        </div>
                        <p class="mt-2 text-muted">جاري تحميل البيانات...</p>
                    </div>
                </div>

                <!-- نتائج البحث -->
                <div id="resultsContainer">
                    @include('taskmanager::project.partials.table', [
                        'projects' => $projects,
                    ])
                </div>
            </div>
        </div>
    </div>

    <!-- شريط الإجراءات الجماعية -->
    <div class="bulk-actions-bar" id="bulkActionsBar">
        <div class="d-flex align-items-center">
            <span class="selected-count" id="selectedCount">0 مشروع محدد</span>
            <div class="d-flex gap-2 ms-3">
                <button class="btn btn-light btn-sm" onclick="bulkInviteMembers()">
                    <i class="fas fa-user-plus me-1"></i>دعوة أعضاء
                </button>
                <button class="btn btn-outline-light btn-sm" onclick="bulkStatusUpdate()">
                    <i class="fas fa-edit me-1"></i>تعديل الحالة
                </button>
                <button class="btn btn-outline-light btn-sm" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i>إلغاء التحديد
                </button>
            </div>
        </div>
    </div>

    <!-- Modal دعوة أعضاء جماعية -->
    <div class="modal fade bulk-invite-modal" id="bulkInviteMembersModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h4 class="modal-title">
                        <i class="fas fa-users me-2"></i>
                        دعوة أعضاء للمشاريع المحددة
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- ملخص المشاريع المحددة -->
                    <div class="selected-projects-summary">
                        <h6 class="mb-2">
                            <i class="fas fa-project-diagram me-2"></i>
                            المشاريع المحددة (<span id="modalSelectedCount">0</span>):
                        </h6>
                        <div id="selectedProjectsList" class="d-flex flex-wrap"></div>
                    </div>

                    <!-- طرق الدعوة -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#bulk-email-invite" role="tab">
                                <i class="fas fa-envelope me-1"></i>بالبريد الإلكتروني
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#bulk-users-invite" role="tab"
                               onclick="loadBulkAvailableUsers()">
                                <i class="fas fa-users me-1"></i>من المستخدمين المتاحين
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- الدعوة بالبريد الإلكتروني -->
                        <div class="tab-pane active" id="bulk-email-invite" role="tabpanel">
                            <form id="bulkInviteByEmailForm">
                                <div class="form-group">
                                    <label>البريد الإلكتروني *</label>
                                    <input type="email" class="form-control" id="bulk_invite_email"
                                           placeholder="أدخل البريد الإلكتروني للعضو المراد دعوته" required>
                                    <small class="form-text text-muted">
                                        سيتم البحث عن المستخدم وإرسال دعوة له لجميع المشاريع المحددة
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>الدور في المشاريع *</label>
                                    <select class="form-control" id="bulk_invite_role" required>
                                        <option value="">اختر الدور</option>
                                        <option value="member">عضو - يمكنه العمل على المهام</option>
                                        <option value="manager">مدير - يمكنه إدارة المشروع والمهام</option>
                                        <option value="viewer">مشاهد - يمكنه مشاهدة المشروع فقط</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>رسالة الدعوة (اختياري)</label>
                                    <textarea class="form-control" id="bulk_invite_message" rows="3"
                                            placeholder="أضف رسالة شخصية للمدعو..."></textarea>
                                </div>
                            </form>
                        </div>

                        <!-- من المستخدمين المتاحين -->
                        <div class="tab-pane" id="bulk-users-invite" role="tabpanel">
                            <div class="form-group">
                                <label>البحث عن المستخدمين</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="bulkUserSearchInput"
                                           placeholder="ابحث بالاسم أو البريد الإلكتروني...">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                                onclick="loadBulkAvailableUsers()">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>الدور للمستخدمين المختارين</label>
                                <select class="form-control" id="bulk_users_role">
                                    <option value="member">عضو</option>
                                    <option value="manager">مدير</option>
                                    <option value="viewer">مشاهد</option>
                                </select>
                            </div>

                            <div id="bulkAvailableUsersList" style="max-height: 300px; overflow-y: auto;">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">جاري التحميل...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>ملاحظة:</strong> سيتم إرسال الدعوة لجميع المشاريع المحددة. العضو المدعو سيتمكن من الوصول لهذه المشاريع حسب الصلاحيات المحددة
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-info" onclick="sendBulkInvite()">
                        <i class="fas fa-paper-plane me-1"></i>إرسال الدعوات
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
        // متغيرات النظام الجماعي
        let selectedProjects = new Set();
        let selectedUsersForBulkInvite = new Set();
        let availableUsersForBulkInvite = [];

        $(document).ready(function() {
            let currentPage = 1;
            let isLoading = false;
            let searchXHR = null;

            // تهيئة Select2
            $('.select2').select2({
                width: '100%',
                placeholder: 'اختر...',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "لا توجد نتائج";
                    }
                }
            });

            // معالجة تحديد جميع المشاريع
            $(document).on('change', '#selectAll', function() {
                const isChecked = $(this).is(':checked');
                $('.project-checkbox').prop('checked', isChecked).trigger('change');
            });

            // معالجة تحديد مشروع واحد
            $(document).on('change', '.project-checkbox', function() {
                const projectId = $(this).val();
                const isChecked = $(this).is(':checked');
                const projectTitle = $(this).closest('tr').find('.text-primary').text();

                if (isChecked) {
                    selectedProjects.add({
                        id: projectId,
                        title: projectTitle
                    });
                    $(this).closest('tr').addClass('selected');
                } else {
                    selectedProjects.forEach(project => {
                        if (project.id === projectId) {
                            selectedProjects.delete(project);
                        }
                    });
                    $(this).closest('tr').removeClass('selected');
                }

                updateBulkActionsBar();
                updateSelectAllCheckbox();
            });

            // باقي الكود الأصلي...
            $('#searchForm').submit(function(e) {
                e.preventDefault();
                if (!isLoading) {
                    currentPage = 1;
                    loadData();
                }
            });

            $('#searchForm input, #searchForm select').on('change input', function() {
                if (searchXHR) {
                    searchXHR.abort();
                }

                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(function() {
                    if (!isLoading) {
                        currentPage = 1;
                        loadData();
                    }
                }, 500);
            });

            // إعادة تعيين البحث
            $('#resetSearch, #resetSearchBtn').click(function() {
                $('#searchForm')[0].reset();
                $('.select2').val(null).trigger('change');
                currentPage = 1;
                loadData();
            });

            // تحديث البيانات
            $('#refreshAnalyticsBtn').click(function() {
                if (!isLoading) {
                    loadData();
                    loadStats();
                }
            });

            // دالة تحميل البيانات
            function loadData() {
                if (isLoading) return;

                isLoading = true;
                showLoading();

                // جمع بيانات النموذج
                let formData = $('#searchForm').serializeArray()
                    .filter(item => item.name !== '_token')
                    .reduce((obj, item) => {
                        obj[item.name] = item.value;
                        return obj;
                    }, {});

                formData.page = currentPage;

                // إلغاء أي طلب سابق
                if (searchXHR) {
                    searchXHR.abort();
                }

                searchXHR = $.ajax({
                    url: "",
                    method: 'GET',
                    data: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#resultsContainer').html(response.html);
                            updatePaginationInfo(response);
                            // إعادة تعيين التحديدات بعد تحديث الجدول
                            clearSelection();
                        } else {
                            showError('حدث خطأ أثناء جلب البيانات');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.statusText !== 'abort') {
                            handleAjaxError(xhr);
                        }
                    },
                    complete: function() {
                        isLoading = false;
                        hideLoading();
                        searchXHR = null;
                    }
                });
            }

            function showLoading() {
                $('#loadingIndicator').show();
            }

            function hideLoading() {
                $('#loadingIndicator').hide();
            }

            function showError(message) {
                console.error(message);
                // يمكن إضافة عرض رسالة خطأ للمستخدم هنا
            }

            function handleAjaxError(xhr) {
                let message = 'حدث خطأ غير متوقع';
                if (xhr.status === 422) {
                    message = 'خطأ في البيانات المرسلة';
                } else if (xhr.status === 500) {
                    message = 'خطأ في الخادم';
                }
                showError(message);
            }

            function updatePaginationInfo(response) {
                if (response.pagination) {
                    $('.results-info').text(`${response.pagination.total} مشروع`);
                }
            }

            // تحميل البيانات الأولية
            loadData();
        });

        // دوال النظام الجماعي
        function updateBulkActionsBar() {
            const count = selectedProjects.size;
            if (count > 0) {
                $('#selectedCount').text(`${count} مشروع محدد`);
                $('#bulkActionsBar').fadeIn();
            } else {
                $('#bulkActionsBar').fadeOut();
            }
        }

        function updateSelectAllCheckbox() {
            const totalCheckboxes = $('.project-checkbox').length;
            const checkedCheckboxes = $('.project-checkbox:checked').length;

            if (checkedCheckboxes === 0) {
                $('#selectAll').prop('indeterminate', false).prop('checked', false);
            } else if (checkedCheckboxes === totalCheckboxes) {
                $('#selectAll').prop('indeterminate', false).prop('checked', true);
            } else {
                $('#selectAll').prop('indeterminate', true);
            }
        }

        function clearSelection() {
            selectedProjects.clear();
            $('.project-checkbox').prop('checked', false);
            $('.project-row').removeClass('selected');
            $('#selectAll').prop('checked', false).prop('indeterminate', false);
            updateBulkActionsBar();
        }

        // دالة فتح مودال الدعوة الجماعية
        function bulkInviteMembers() {
            if (selectedProjects.size === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'لم يتم تحديد مشاريع',
                    text: 'الرجاء تحديد مشروع واحد على الأقل للمتابعة'
                });
                return;
            }

            // تحديث ملخص المشاريع المحددة
            updateSelectedProjectsSummary();

            // فتح المودال
            $('#bulkInviteMembersModal').modal('show');
        }

        function updateSelectedProjectsSummary() {
            const count = selectedProjects.size;
            $('#modalSelectedCount').text(count);

            let html = '';
            selectedProjects.forEach(project => {
                html += `<span class="project-chip">${project.title}</span>`;
            });

            $('#selectedProjectsList').html(html);
        }

        // تحميل المستخدمين المتاحين للدعوة الجماعية
        function loadBulkAvailableUsers() {
            $('#bulkAvailableUsersList').html(`
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">جاري التحميل...</span>
                    </div>
                </div>
            `);

            // جمع معرفات المشاريع المحددة
            const projectIds = Array.from(selectedProjects).map(p => p.id);

            $.ajax({
                url: '{{ route("projects.bulk.available-users") }}',

                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    project_ids: projectIds
                },
                success: function(response) {
                    if (response.success) {
                        availableUsersForBulkInvite = response.data;
                        displayBulkAvailableUsers(availableUsersForBulkInvite);
                    } else {
                        showBulkUserListError('لا توجد مستخدمين متاحين للدعوة');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading bulk available users:', xhr);
                    showBulkUserListError('حدث خطأ أثناء تحميل المستخدمين المتاحين');
                }
            });
        }

        function displayBulkAvailableUsers(users) {
            if (users.length === 0) {
                $('#bulkAvailableUsersList').html(`
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-users" style="font-size: 2rem;"></i>
                        <div class="mt-2">لا يوجد مستخدمين متاحين للدعوة</div>
                        <small>جميع المستخدمين إما أعضاء بالفعل في هذه المشاريع أو لديهم دعوات معلقة</small>
                    </div>
                `);
                return;
            }

            let html = '';
            users.forEach(user => {
                const isSelected = selectedUsersForBulkInvite.has(user.id);
                html += `
                    <div class="user-selection-item ${isSelected ? 'selected' : ''}"
                         onclick="toggleBulkUserSelection(${user.id})">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar-invite">
                                ${user.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="member-info">
                                <div class="member-name">${user.name}</div>
                                <div class="member-email">${user.email}</div>
                                ${user.last_login_at ?
                                    `<small class="text-success">آخر دخول: ${formatDate(user.last_login_at)}</small>` :
                                    '<small class="text-muted">لم يسجل دخول بعد</small>'
                                }
                            </div>
                            <div class="ms-auto">
                                <input type="checkbox" ${isSelected ? 'checked' : ''}
                                       onchange="toggleBulkUserSelection(${user.id})">
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#bulkAvailableUsersList').html(html);
        }

        function toggleBulkUserSelection(userId) {
            if (selectedUsersForBulkInvite.has(userId)) {
                selectedUsersForBulkInvite.delete(userId);
            } else {
                selectedUsersForBulkInvite.add(userId);
            }

            // تحديث العرض
            displayBulkAvailableUsers(availableUsersForBulkInvite);
        }

        function showBulkUserListError(message) {
            $('#bulkAvailableUsersList').html(`
                <div class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem;"></i>
                    <div class="mt-2">${message}</div>
                    <button class="btn btn-outline-primary btn-sm mt-2" onclick="loadBulkAvailableUsers()">
                        <i class="fas fa-sync-alt me-1"></i>إعادة المحاولة
                    </button>
                </div>
            `);
        }

        // إرسال الدعوة الجماعية
        function sendBulkInvite() {
            const activeTab = $('.nav-tabs .nav-link.active').attr('href');
            const projectIds = Array.from(selectedProjects).map(p => p.id);

            let inviteData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                project_ids: projectIds
            };

            // تحديد طريقة الدعوة والبيانات
            if (activeTab === '#bulk-email-invite') {
                const email = $('#bulk_invite_email').val().trim();
                const role = $('#bulk_invite_role').val();
                const message = $('#bulk_invite_message').val().trim();

                if (!email || !role) {
                    Swal.fire({
                        icon: 'error',
                        title: 'بيانات ناقصة',
                        text: 'الرجاء إدخال البريد الإلكتروني واختيار الدور'
                    });
                    return;
                }

                inviteData.email = email;
                inviteData.role = role;
                inviteData.message = message;
                inviteData.invite_type = 'email';

            } else if (activeTab === '#bulk-users-invite') {
                const role = $('#bulk_users_role').val();

                if (selectedUsersForBulkInvite.size === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'لم يتم تحديد مستخدمين',
                        text: 'الرجاء اختيار مستخدم واحد على الأقل للدعوة'
                    });
                    return;
                }

                inviteData.user_ids = Array.from(selectedUsersForBulkInvite);
                inviteData.role = role;
                inviteData.invite_type = 'users';
            }

            // إرسال الدعوة
            Swal.fire({
                title: 'جاري الإرسال...',
                html: `
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <p>جاري إرسال الدعوات لـ <strong>${selectedProjects.size}</strong> مشروع...</p>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false
            });

            $.ajax({
                url: '{{ route("projects.bulk.invite") }}',
                method: 'POST',
                data: inviteData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم إرسال الدعوات!',
                            html: `
                                <div class="text-center">
                                    <p class="mb-2">${response.message}</p>
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        تم إرسال <strong>${response.data.sent_count || selectedProjects.size}</strong> دعوة بنجاح
                                    </div>
                                </div>
                            `,
                            timer: 5000
                        }).then(() => {
                            $('#bulkInviteMembersModal').modal('hide');
                            resetBulkInviteForm();
                            clearSelection(); // مسح التحديد بعد إرسال الدعوات
                        });
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'حدث خطأ أثناء إرسال الدعوات';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMsg = errors.join('\n');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: errorMsg
                    });
                }
            });
        }

        // إعادة تعيين نموذج الدعوة الجماعية
        function resetBulkInviteForm() {
            $('#bulk_invite_email').val('');
            $('#bulk_invite_role').val('');
            $('#bulk_invite_message').val('');
            $('#bulk_users_role').val('member');
            selectedUsersForBulkInvite.clear();
        }

        // تحديث الحالة الجماعي
        function bulkStatusUpdate() {
            if (selectedProjects.size === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'لم يتم تحديد مشاريع',
                    text: 'الرجاء تحديد مشروع واحد على الأقل للمتابعة'
                });
                return;
            }

            Swal.fire({
                title: `تحديث حالة ${selectedProjects.size} مشروع`,
                html: `
                    <div class="text-start">
                        <label class="form-label">الحالة الجديدة</label>
                        <select id="new_status" class="form-control">
                            <option value="new">جديد</option>
                            <option value="in_progress">قيد التنفيذ</option>
                            <option value="completed">مكتمل</option>
                            <option value="on_hold">متوقف</option>
                        </select>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'تحديث',
                cancelButtonText: 'إلغاء',
                width: 500,
                preConfirm: () => {
                    const status = $('#new_status').val();
                    if (!status) {
                        Swal.showValidationMessage('الرجاء اختيار الحالة');
                        return false;
                    }
                    return { status };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateProjectsStatus(result.value.status);
                }
            });
        }

        function updateProjectsStatus(newStatus) {
            const projectIds = Array.from(selectedProjects).map(p => p.id);

            Swal.fire({
                title: 'جاري التحديث...',
                text: 'يرجى الانتظار',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("projects.bulk.status") }}',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    project_ids: projectIds,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم التحديث!',
                            text: response.message,
                            timer: 3000
                        }).then(() => {
                            // إعادة تحميل البيانات
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'حدث خطأ أثناء التحديث';
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

        // دوال مساعدة
        function formatDate(date) {
            if (!date) return '';
            return new Date(date).toLocaleDateString('ar-SA');
        }

        // معالجة إغلاق المودال
        $('#bulkInviteMembersModal').on('hidden.bs.modal', function() {
            resetBulkInviteForm();
        });

        // البحث في المستخدمين
        $('#bulkUserSearchInput').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            if (searchTerm) {
                const filteredUsers = availableUsersForBulkInvite.filter(user =>
                    user.name.toLowerCase().includes(searchTerm) ||
                    user.email.toLowerCase().includes(searchTerm)
                );
                displayBulkAvailableUsers(filteredUsers);
            } else {
                displayBulkAvailableUsers(availableUsersForBulkInvite);
            }
        });

        // دوال إضافية للبحث والفلاتر
        function toggleSearchFields(button) {
            const fields = $('#basicSearchFields');
            if (fields.is(':visible')) {
                fields.hide();
                $(button).find('.hide-button-text').text('إظهار');
                $(button).find('i').removeClass('fa-times').addClass('fa-eye');
            } else {
                fields.show();
                $(button).find('.hide-button-text').text('اخفاء');
                $(button).find('i').removeClass('fa-eye').addClass('fa-times');
            }
        }

        function toggleSearchText(button) {
            const text = $(button).find('.button-text');
            if (text.text() === 'متقدم') {
                text.text('بسيط');
            } else {
                text.text('متقدم');
            }
        }
    </script>
@endsection
