{{-- resources/views/hr/attendance/settings/machines/table-content.blade.php --}}

@if($machines->count() > 0)
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">قائمة الماكينات</h5>
        <small class="text-muted">
            عرض {{ $machines->firstItem() }} - {{ $machines->lastItem() }}
            من أصل {{ $machines->total() }} نتيجة
        </small>
    </div>

    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th width="5%">#</th>
                <th width="25%">الاسم</th>
                <th width="10%">النوع</th>
                <th width="15%">المضيف</th>
                <th width="10%">رقم المنفذ</th>
                <th width="10%">الحالة</th>
                <th width="15%">تاريخ الإنشاء</th>
                <th width="10%">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($machines as $machine)
                <tr id="machine-{{ $machine->id }}">
                    <td>
                        <span class="badge badge-light">
                            {{ $loop->iteration + ($machines->currentPage() - 1) * $machines->perPage() }}
                        </span>
                    </td>
                    <td>
                        <div class="media align-items-center">
                            <div class="media-body">
                                <h6 class="mb-0 font-weight-bold">{{ $machine->name }}</h6>
                                @if($machine->serial_number)
                                    <small class="text-muted">{{ $machine->serial_number }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-info badge-lg">
                            <i class="fa fa-desktop me-1"></i>
                            {{ ucfirst($machine->machine_type) }}
                        </span>
                    </td>
                    <td>
                        <div class="text-muted">
                            <i class="fa fa-server me-1"></i>
                            {{ $machine->host_name }}
                        </div>
                    </td>
                    <td>
                        <div class="text-muted">
                            <i class="fa fa-plug me-1"></i>
                            {{ $machine->port_number }}
                        </div>
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input status-toggle"
                                   type="checkbox"
                                   data-machine-id="{{ $machine->id }}"
                                   {{ $machine->status ? 'checked' : '' }}
                                   onchange="toggleStatus({{ $machine->id }})">
                            <label class="form-check-label">
                                <span class="status-text badge {{ $machine->status ? 'badge-success' : 'badge-secondary' }} badge-lg">
                                    <i class="fa fa-{{ $machine->status ? 'check' : 'ban' }} me-1"></i>
                                    {{ $machine->status ? 'نشط' : 'غير نشط' }}
                                </span>
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="text-muted">
                            <i class="fa fa-calendar me-1"></i>
                            {{ $machine->created_at->format('d/m/Y') }}
                            <br>
                            <small>{{ $machine->created_at->diffForHumans() }}</small>
                        </div>
                    </td>
                    <td style="width: 10%">
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button"
                                        id="dropdownMenuButton{{ $machine->id }}"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false"></button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $machine->id }}">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('machines.show', $machine->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('machines.edit', $machine->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                    </li>


                                    <li>
                                        <a class="dropdown-item text-danger"
                                           href="#"
                                           data-toggle="modal"
                                           data-target="#modal_DELETE{{ $machine->id }}">
                                            <i class="fa fa-trash me-2"></i>حذف
                                        </a>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    @if($machines->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="pagination">
                {{ $machines->appends(request()->query())->links() }}
            </nav>
        </div>
    @endif

@else
    <div class="text-center py-5">
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fa fa-desktop fa-4x text-muted mb-4"></i>
            </div>
            <div class="empty-state-content">
                <h4 class="text-muted mb-3">لا توجد ماكينات</h4>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['name', 'status', 'machine_type']))
                        لم يتم العثور على أي نتائج تطابق معايير البحث المحددة.
                        <br>
                        جرب تغيير معايير البحث أو إعادة تعيين الفلاتر.
                    @else
                        لا توجد ماكينات مُضافة حتى الآن.
                        <br>
                        قم بإضافة ماكينة جديدة للبدء.
                    @endif
                </p>
                <div class="empty-state-actions">
                    @if(request()->hasAny(['name', 'status', 'machine_type']))
                        <button type="button" id="clearFilter" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-refresh me-1"></i>
                            إعادة تعيين الفلاتر
                        </button>
                    @endif
                    <a href="{{ route('machines.create') }}" class="btn btn-success">
                        <i class="fa fa-plus me-1"></i>
                        إضافة ماكينة جديدة
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
$(document).ready(function() {
    // تفعيل tooltips
    $('[title]').tooltip();

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