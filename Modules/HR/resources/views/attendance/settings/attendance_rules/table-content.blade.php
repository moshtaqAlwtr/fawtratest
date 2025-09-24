{{-- resources/views/hr/attendance/settings/attendance_rules/table-content.blade.php --}}

@if($attendanceRules->count() > 0)
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">قائمة قواعد الحضور</h5>
        <small class="text-muted">
            عرض {{ $attendanceRules->firstItem() }} - {{ $attendanceRules->lastItem() }}
            من أصل {{ $attendanceRules->total() }} نتيجة
        </small>
    </div>

    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th width="5%">#</th>
                <th width="25%">الاسم</th>
                <th width="10%">اللون</th>
                <th width="15%">الوردية</th>
                <th width="10%">الحالة</th>
                <th width="15%">تاريخ الإنشاء</th>
                <th width="20%">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendanceRules as $rule)
                <tr id="rule-{{ $rule->id }}">
                    <td>
                        <span class="badge badge-light">
                            {{ $loop->iteration + ($attendanceRules->currentPage() - 1) * $attendanceRules->perPage() }}
                        </span>
                    </td>
                    <td>
                        <div class="media align-items-center">
                            <div class="media-body">
                                <h6 class="mb-0 font-weight-bold">{{ $rule->name }}</h6>
                                @if($rule->description)
                                    <small class="text-muted">{{ Str::limit($rule->description, 50) }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div style="width: 24px; height: 24px; background-color: {{ $rule->color }};
                                        border-radius: 4px; margin-left: 8px; border: 1px solid #dee2e6;">
                            </div>
                            <code class="text-muted small">{{ $rule->color }}</code>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-info badge-lg">
                            <i class="fa fa-clock-o me-1"></i>
                            {{ $rule->shift->name ?? 'غير محدد' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $rule->status == 'active' ? 'badge-success' : 'badge-secondary' }} badge-lg">
                            <i class="fa fa-{{ $rule->status == 'active' ? 'check' : 'ban' }} me-1"></i>
                            {{ $rule->status == 'active' ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                    <td>
                        <div class="text-muted">
                            <i class="fa fa-calendar me-1"></i>
                            {{ $rule->created_at->format('d/m/Y') }}
                            <br>
                            <small>{{ $rule->created_at->diffForHumans() }}</small>
                        </div>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('attendance-rules.show', $rule->id) }}"
                               class="btn btn-info"
                               title="عرض التفاصيل"
                               data-toggle="tooltip">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="{{ route('attendance-rules.edit', $rule->id) }}"
                               class="btn btn-primary"
                               title="تعديل"
                               data-toggle="tooltip">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button"
                                    class="btn {{ $rule->status == 'active' ? 'btn-warning' : 'btn-success' }}"
                                    onclick="toggleStatus({{ $rule->id }}, '{{ $rule->status }}')"
                                    title="{{ $rule->status == 'active' ? 'إلغاء التفعيل' : 'تفعيل' }}"
                                    data-toggle="tooltip">
                                <i class="fa fa-{{ $rule->status == 'active' ? 'ban' : 'check' }}"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-danger"
                                    onclick="deleteRule({{ $rule->id }}, '{{ $rule->name }}')"
                                    title="حذف"
                                    data-toggle="tooltip">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    @if($attendanceRules->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="pagination">
                {{ $attendanceRules->appends(request()->query())->links() }}
            </nav>
        </div>
    @endif

@else
    <div class="text-center py-5">
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fa fa-inbox fa-4x text-muted mb-4"></i>
            </div>
            <div class="empty-state-content">
                <h4 class="text-muted mb-3">لا توجد قواعد حضور</h4>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['keywords', 'status', 'shift']))
                        لم يتم العثور على أي نتائج تطابق معايير البحث المحددة.
                        <br>
                        جرب تغيير معايير البحث أو إعادة تعيين الفلاتر.
                    @else
                        لا توجد قواعد حضور مُضافة حتى الآن.
                        <br>
                        قم بإضافة قاعدة حضور جديدة للبدء.
                    @endif
                </p>
                <div class="empty-state-actions">
                    @if(request()->hasAny(['keywords', 'status', 'shift']))
                        <button type="button" id="clearFilter" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-refresh me-1"></i>
                            إعادة تعيين الفلاتر
                        </button>
                    @endif
                    <a href="{{ route('attendance-rules.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i>
                        إضافة قاعدة جديدة
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
$(document).ready(function() {
    // تفعيل tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // إضافة تأثيرات hover للجدول
    $('tbody tr').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );
});
</script>