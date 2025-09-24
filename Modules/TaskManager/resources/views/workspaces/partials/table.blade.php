@if ($workspaces->count() > 0)
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <h6 class="mb-0">عرض {{ $workspaces->firstItem() }} إلى {{ $workspaces->lastItem() }} من {{ $workspaces->total() }} مساحة عمل</h6>
        </div>
        <div class="d-flex align-items-center gap-2">
            <select class="form-select form-select-sm" id="perPageSelect" style="width: auto;">
                <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span class="text-muted">عناصر لكل صفحة</span>
        </div>
    </div>

    <div class="table">
        <table class="table table-hover table-striped table-hover-custom">
            <thead class="table-light">
                <tr>
                    <th width="5%">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                    <th>مساحة العمل</th>
                    <th>المالك</th>
                    <th>المشاريع</th>
                    <th>الأعضاء</th>
                    <th>معدل الإكمال</th>
                    <th>الحالة</th>
                    <th>تاريخ الإنشاء</th>
                    <th style="width: 10%">خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($workspaces as $workspace)
                    <tr class="workspace-row" data-workspace-id="{{ $workspace->id }}">
                        <td>
                            <input type="checkbox" class="form-check-input workspace-checkbox" value="{{ $workspace->id }}">
                        </td>
                        <td style="white-space: normal; word-wrap: break-word; min-width: 200px;">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2"
                                    style="background-color: {{ $workspace->is_primary ? '#007bff' : '#6c757d' }}; width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    <i class="fas {{ $workspace->is_primary ? 'fa-star' : 'fa-project-diagram' }}"></i>
                                </div>
                                <div>
                                    <strong class="text-primary">{{ $workspace->title }}</strong>
                                    @if($workspace->description)
                                        <div class="text-muted small">{{ Str::limit($workspace->description, 50) }}</div>
                                    @endif
                                    @if($workspace->is_primary)
                                        <span class="badge badge-warning badge-sm">رئيسية</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($workspace->admin)
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2"
                                        style="background-color: #28a745; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.9rem;">
                                        {{ substr($workspace->admin->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <strong>{{ $workspace->admin->name }}</strong>
                                        <div class="text-muted small">{{ $workspace->admin->email }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-tasks text-primary me-1"></i>
                                    <strong>{{ $workspace->stats['total_projects'] ?? 0 }}</strong>
                                    <span class="text-muted ms-1">مشروع</span>
                                </div>
                                <div class="small text-muted">
                                    <span class="text-success">{{ $workspace->stats['active_projects'] ?? 0 }} نشط</span> |
                                    <span class="text-info">{{ $workspace->stats['completed_projects'] ?? 0 }} مكتمل</span>
                                </div>
                                @if(($workspace->stats['total_projects'] ?? 0) > 0)
                                    <div class="progress progress-custom mt-1" style="height: 4px;">
                                        @php
                                            $completionPercentage = ($workspace->stats['completed_projects'] ?? 0) / ($workspace->stats['total_projects'] ?? 1) * 100;
                                        @endphp
                                        <div class="progress-bar bg-success" style="width: {{ $completionPercentage }}%"></div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-users text-info me-1"></i>
                                <strong>{{ $workspace->stats['total_members'] ?? 0 }}</strong>
                                <span class="text-muted ms-1">عضو</span>
                            </div>
                            @if(($workspace->stats['total_members'] ?? 0) > 1)
                                <div class="small text-muted">
                                    <i class="fas fa-user-friends"></i> فريق عمل
                                </div>
                            @endif
                        </td>
                        <td>
                            @php
                                $totalProjects = $workspace->stats['total_projects'] ?? 0;
                                $completedProjects = $workspace->stats['completed_projects'] ?? 0;
                                $completionRate = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 1) : 0;

                                $progressClass = '';
                                if ($completionRate >= 80) $progressClass = 'bg-success';
                                elseif ($completionRate >= 60) $progressClass = 'bg-info';
                                elseif ($completionRate >= 40) $progressClass = 'bg-warning';
                                else $progressClass = 'bg-danger';
                            @endphp
                            <div class="d-flex align-items-center">
                                <div class="progress me-2" style="width: 60px; height: 8px;">
                                    <div class="progress-bar {{ $progressClass }}" style="width: {{ $completionRate }}%"></div>
                                </div>
                                <strong class="text-{{ $completionRate >= 70 ? 'success' : ($completionRate >= 40 ? 'warning' : 'danger') }}">
                                    {{ $completionRate }}%
                                </strong>
                            </div>
                            <div class="small text-muted mt-1">
                                {{ $completedProjects }} من {{ $totalProjects }}
                            </div>
                        </td>
                        <td>
                            @php
                                $statusClass = '';
                                $statusText = '';
                                $statusIcon = '';

                                if (($workspace->stats['total_projects'] ?? 0) == 0) {
                                    $statusClass = 'badge-secondary';
                                    $statusText = 'فارغة';
                                    $statusIcon = 'fa-inbox';
                                } elseif (($workspace->stats['active_projects'] ?? 0) > 0) {
                                    $statusClass = 'badge-success';
                                    $statusText = 'نشطة';
                                    $statusIcon = 'fa-play-circle';
                                } elseif (($workspace->stats['completed_projects'] ?? 0) == ($workspace->stats['total_projects'] ?? 0)) {
                                    $statusClass = 'badge-info';
                                    $statusText = 'مكتملة';
                                    $statusIcon = 'fa-check-circle';
                                } else {
                                    $statusClass = 'badge-warning';
                                    $statusText = 'متوقفة';
                                    $statusIcon = 'fa-pause-circle';
                                }
                            @endphp
                            <span class="badge {{ $statusClass }} workspace-status-badge rounded-pill">
                                <i class="fas {{ $statusIcon }} me-1"></i>
                                {{ $statusText }}
                            </span>
                            @if($workspace->is_primary)
                                <div class="small text-warning mt-1">
                                    <i class="fas fa-star"></i> مساحة رئيسية
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-dark">
                                    <i class="fa fa-calendar text-muted me-1"></i>
                                    {{ $workspace->created_at->format('Y-m-d') }}
                                </span>
                                <small class="text-muted">
                                    <i class="fa fa-clock me-1"></i>
                                    {{ $workspace->created_at->format('H:i') }}
                                </small>
                                <small class="text-muted">
                                    {{ $workspace->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item text-info"
                                            href="{{ route('workspaces.show', $workspace->id) }}">
                                            <i class="fas fa-eye me-2"></i>عرض المساحة
                                        </a>
                                        <a class="dropdown-item text-success"
                                            href="{{ route('workspaces.edit', $workspace->id) }}">
                                            <i class="fas fa-edit me-2"></i>تعديل المساحة
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item"
                                            href="{{ route('workspaces.analytics.detailed', $workspace->id) }}">
                                            <i class="fas fa-chart-bar me-2 text-primary"></i>تحليلات مفصلة
                                        </a>
                                        <a class="dropdown-item"
                                            href="{{ route('workspaces.analytics.export.single', $workspace->id) }}">
                                            <i class="fas fa-file-excel me-2 text-success"></i>تصدير البيانات
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        @if($workspace->is_primary)
                                            <span class="dropdown-item text-muted">
                                                <i class="fas fa-lock me-2"></i>مساحة رئيسية
                                            </span>
                                        @else
                                            <a class="dropdown-item text-danger delete-workspace"
                                                href="#" data-id="{{ $workspace->id }}">
                                                <i class="fas fa-trash me-2"></i>حذف المساحة
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- الترقيم -->
    @if($workspaces->hasPages())
        @include('taskmanager::workspaces.analytics.partials.pagination', ['items' => $workspaces])
    @endif

@else
    @php
        $hasSearchParams = request()->filled([
            'title', 'admin_id', 'is_primary', 'projects_min', 'projects_max',
            'members_min', 'members_max', 'from_date', 'to_date',
            'completion_min', 'completion_max'
        ]) || request()->has('page');
    @endphp

    @if($hasSearchParams)
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-search fa-3x text-muted"></i>
            </div>
            <h5 class="text-muted mb-2">لا توجد نتائج مطابقة</h5>
            <p class="text-muted mb-3">لا توجد مساحات عمل تطابق معايير البحث المحددة</p>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" id="clearSearchBtn" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-times me-1"></i>إعادة تعيين البحث
                </button>
                <a href="{{ route('workspaces.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>إنشاء مساحة جديدة
                </a>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-project-diagram fa-3x text-primary opacity-50"></i>
            </div>
            <h5 class="text-muted mb-2">ابدأ التحليل</h5>
            <p class="text-muted mb-3">استخدم نموذج البحث أعلاه لتحليل مساحات العمل</p>
            <a href="{{ route('workspaces.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>إنشاء مساحة عمل جديدة
            </a>
        </div>
    @endif
@endif

<script>
    // تحديث عدد العناصر المحددة في الوقت الفعلي
    $(document).ready(function() {
        function updateSelectionCount() {
            const selectedCount = $('.workspace-checkbox:checked').length;
            const totalCount = $('.workspace-checkbox').length;

            if (selectedCount > 0) {
                $('#bulkActionsBtn').removeClass('d-none').text(`إجراءات مجمعة (${selectedCount})`);
            } else {
                $('#bulkActionsBtn').addClass('d-none');
            }

            $('#selectAll').prop('indeterminate', selectedCount > 0 && selectedCount < totalCount);
            $('#selectAll').prop('checked', selectedCount === totalCount && totalCount > 0);
        }

        // تحديد/إلغاء تحديد الكل
        $(document).on('change', '#selectAll', function() {
            $('.workspace-checkbox').prop('checked', $(this).prop('checked'));
            updateSelectionCount();
        });

        // تحديث عداد المحدد
        $(document).on('change', '.workspace-checkbox', function() {
            updateSelectionCount();
        });

        // تفعيل متابعة التحديد
        updateSelectionCount();

        // حذف مساحة العمل
        $(document).on('click', '.delete-workspace', function(e) {
            e.preventDefault();
            let workspaceId = $(this).data('id');

            Swal.fire({
                title: 'تأكيد الحذف',
                text: 'هل أنت متأكد من حذف هذه المساحة؟ سيتم حذف جميع المشاريع والمهام المرتبطة بها.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/workspaces/${workspaceId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'تم الحذف!',
                                    text: 'تم حذف مساحة العمل بنجاح.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                // إعادة تحميل البيانات
                                if (typeof loadData === 'function') {
                                    loadData();
                                } else {
                                    location.reload();
                                }
                            } else {
                                Swal.fire('خطأ!', response.message || 'فشل في حذف مساحة العمل', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('خطأ!', 'حدث خطأ أثناء محاولة الحذف', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
