@extends('master')

@section('title')
أعدادات أجهزة نقاط البيع
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">أعدادات أجهزة نقاط البيع</h2>
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
                        <h6>إجمالي الأجهزة: {{ $devices->total() }}</h6>
                    </div>
                    <div>
                        <a href="{{ route('pos.settings.devices.create') }}" class="btn btn-outline-primary">
                            <i class="fa fa-plus me-2"></i> جهاز جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج البحث -->
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">بحث وتصفية</h5>
                <form method="GET" action="{{ route('pos.settings.devices.index') }}">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="search" class="form-label">البحث بالاسم</label>
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="ابحث عن جهاز..."
                                   value="{{ request('search') }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="store_id" class="form-label">المخزن</label>
                            <select id="store_id" name="store_id" class="form-control">
                                <option value="">جميع المخازن</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" 
                                            {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">جميع الحالات</option>
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" 
                                            {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('pos.settings.devices.index') }}" class="btn btn-secondary">
                            <i class="fa fa-refresh me-1"></i> إلغاء الفلتر
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- عرض النتائج -->
        <div class="card mt-3">
            <div class="card-body">
                <h5>النتائج ({{ $devices->count() }} من {{ $devices->total() }})</h5>
                
                @if($devices->count() > 0)
                    <div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>الصورة</th>
                <th>اسم الجهاز</th>
                <th>المخزن</th>
                <th>الحالة</th>
                <th>تاريخ الإنشاء</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devices as $device)
                <tr>
                    <td>
                        @if($device->device_image)
                            <img src="{{ $device->image_url }}" 
                                 alt="{{ $device->device_name }}" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        @else
                            <div style="width: 50px; height: 50px; background-color: #f8f9fa; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-image text-muted"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $device->device_name }}</strong>
                        @if($device->description)
                            <p class="mb-0 text-muted small">{{ Str::limit($device->description, 50) }}</p>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-info">
                            {{ $device->store->name ?? 'غير محدد' }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusClass = match($device->device_status) {
                                'active' => 'badge-success',
                                'inactive' => 'badge-secondary',
                                'maintenance' => 'badge-warning',
                                'damaged' => 'badge-danger',
                                default => 'badge-secondary'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">
                            {{ $device->status_text }}
                        </span>
                    </td>
                    <td>{{ $device->created_at->format('Y-m-d H:i') }}</td>
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
    @if($device->device_status == 'active')
        <a class="dropdown-item" 
           href="#" 
           onclick="toggleStatus({{ $device->id }}, 'inactive', '{{ $device->device_name }}')">
            <i class="fa fa-pause-circle me-2 text-warning"></i>تعطيل
        </a>
    @else
        <a class="dropdown-item" 
           href="#" 
           onclick="toggleStatus({{ $device->id }}, 'active', '{{ $device->device_name }}')">
            <i class="fa fa-play-circle me-2 text-success"></i>تفعيل
        </a>
    @endif
    
    <a class="dropdown-item" 
       href="{{ route('pos.settings.devices.edit', $device->id) }}">
        <i class="fa fa-edit me-2 text-primary"></i>تعديل
    </a>
    
    <div class="dropdown-divider"></div>
    
    <a class="dropdown-item text-danger" 
       href="#" 
       onclick="confirmDelete({{ $device->id }}, '{{ $device->device_name }}')">
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

<!-- نموذج تأكيد تغيير الحالة -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">تأكيد تغيير الحالة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من <span id="statusAction"></span> الجهاز "<span id="statusDeviceName"></span>"؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <form id="statusForm" method="POST" style="display: inline;">
                    @csrf
                
                    <input type="hidden" name="device_status" id="newStatus">
                    <button type="submit" class="btn btn-primary" id="statusConfirmBtn">تأكيد</button>
                </form>
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
                هل أنت متأكد من حذف الجهاز "<span id="deviceName"></span>"؟
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


                    <!-- الترقيم -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $devices->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد أجهزة</h5>
                        <p class="text-muted">لم يتم العثور على أي أجهزة بناءً على معايير البحث المحددة.</p>
                        <a href="{{ route('pos.settings.devices.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i> إضافة جهاز جديد
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
                هل أنت متأكد من حذف الجهاز "<span id="deviceName"></span>"؟
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
function confirmDelete(deviceId, deviceName) {
    document.getElementById('deviceName').textContent = deviceName;
    document.getElementById('deleteForm').action = `/pos/settings/devices/${deviceId}`;
    $('#deleteModal').modal('show');
}

// إخفاء رسائل النجاح تلقائياً
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.display = 'none';
        }, 5000);
    }
});
</script>
<script>
function toggleStatus(deviceId, newStatus, deviceName) {
    const actionText = newStatus === 'active' ? 'تفعيل' : 'تعطيل';
    
    document.getElementById('statusAction').textContent = actionText;
    document.getElementById('statusDeviceName').textContent = deviceName;
    document.getElementById('newStatus').value = newStatus;
    
    // تصحيح المسار ليتوافق مع الـ Route الجديد
    document.getElementById('statusForm').action = `/POS/Devices/toggle-status/${deviceId}`;
    document.getElementById('statusConfirmBtn').textContent = actionText;
    
    $('#statusModal').modal('show');
}

function confirmDelete(deviceId, deviceName) {
    document.getElementById('deviceName').textContent = deviceName;
    // تحديث مسار الحذف ليتوافق مع الـ Routes
    document.getElementById('deleteForm').action = `/POS/Devices/delete/${deviceId}`;
    $('#deleteModal').modal('show');
}
</script>


@endsection
