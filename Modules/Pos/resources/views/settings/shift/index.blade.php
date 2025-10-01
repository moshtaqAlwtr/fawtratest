
@extends('master')

@section('title')
أعدادات ورديات نقاط البيع
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">أعدادات ورديات نقاط البيع</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">الرئيسية</li>
                        <li class="breadcrumb-item active">الإعدادات</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- عرض رسائل النجاح --}}
@if (session('success'))
    <div class="alert alert-success">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- عرض رسائل الخطأ --}}
@if (session('error'))
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
    </div>
@endif

<div class="card-body">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h6>إجمالي الورديات: {{ $shifts->total() }}</h6>
                    </div>
                    <div>
                        <a href="{{ route('pos.settings.shift.create') }}" class="btn btn-outline-primary">
                            <i class="fa fa-plus me-2"></i> وردية جديدة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج البحث -->
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">بحث وتصفية</h5>
                <form method="GET" action="{{ route('pos.settings.shift.index') }}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="search" class="form-label">البحث بالاسم</label>
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="ابحث عن وردية..."
                                   value="{{ request('search') }}">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="parent_id" class="form-label">التصنيف الرئيسي</label>
                            <select id="parent_id" name="parent_id" class="form-control">
                                <option value="">جميع التصنيفات</option>
                                @foreach($parentShifts as $parent)
                                    <option value="{{ $parent->id }}" 
                                            {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('pos.settings.shift.index') }}" class="btn btn-secondary">
                            <i class="fa fa-refresh me-1"></i> إلغاء الفلتر
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- عرض النتائج -->
        <div class="card mt-3">
            <div class="card-body">
                <h5>النتائج ({{ $shifts->count() }} من {{ $shifts->total() }})</h5>
                
                @if($shifts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>اسم الوردية</th>
                                    <th>التصنيف الرئيسي</th>
                                    <th>المرفقات</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shifts as $shift)
                                    <tr>
                                        <td>
                                            <strong>{{ $shift->name }}</strong>
                                            @if($shift->description)
                                                <p class="mb-0 text-muted small">{{ Str::limit($shift->description, 50) }}</p>
                                            @endif
                                        </td>
                                        <td>
                                            @if($shift->parent)
                                                <span class="badge badge-info">{{ $shift->parent->name }}</span>
                                            @else
                                                <span class="badge badge-secondary">وردية رئيسية</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($shift->attachment)
                                                <a href="{{ Storage::url($shift->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-download"></i> تحميل
                                                </a>
                                            @else
                                                <span class="text-muted">لا توجد مرفقات</span>
                                            @endif
                                        </td>
                                        <td>{{ $shift->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                            type="button" 
                                                            data-toggle="dropdown" 
                                                            aria-haspopup="true" 
                                                            aria-expanded="false">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    
                                                    <div class="dropdown-menu">
                                                        
                                                        <a class="dropdown-item" 
                                                           href="{{ route('pos.settings.shift.edit', $shift->id) }}">
                                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                        </a>
                                                        
                                                        <div class="dropdown-divider"></div>
                                                        
                                                        <a class="dropdown-item text-danger" 
                                                           href="#" 
                                                           onclick="confirmDelete({{ $shift->id }}, '{{ $shift->name }}')">
                                                            <i class="fa fa-trash me-2"></i>حذف
                                                        </a>
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
                    <div class="d-flex justify-content-center mt-3">
                        {{ $shifts->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد ورديات</h5>
                        <p class="text-muted">لم يتم العثور على أي ورديات بناءً على معايير البحث المحددة.</p>
                        <a href="{{ route('pos.settings.shift.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i> إضافة وردية جديدة
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- نموذج تأكيد الحذف -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف الوردية "<span id="shiftName"></span>"؟
                <br><small class="text-muted">هذا الإجراء لا يمكن التراجع عنه.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(shiftId, shiftName) {
    document.getElementById('shiftName').textContent = shiftName;
    document.getElementById('deleteForm').action = `/POS/Shift/delete/${shiftId}`;
    $('#deleteModal').modal('show');
}

// إخفاء رسائل النجاح تلقائياً
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => alert.style.display = 'none');
    }, 5000);
});
</script>

@endsection
