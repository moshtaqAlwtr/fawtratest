@extends('master')

@section('title', 'تعديل الماكينة')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل الماكينة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('machines.index') }}">الماكينات</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">تعديل بيانات الماكينة: {{ $machine->name }}</h4>
        </div>
        <div class="card-body">
            <form id="machine-edit-form" action="{{ route('machines.update', $machine->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- اسم الماكينة -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">اسم الماكينة <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name', $machine->name) }}"
                            placeholder="أدخل اسم الماكينة"
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الرقم التسلسلي -->
                    <div class="col-md-6 mb-3">
                        <label for="serial_number" class="form-label">الرقم التسلسلي</label>
                        <input
                            type="text"
                            class="form-control @error('serial_number') is-invalid @enderror"
                            id="serial_number"
                            name="serial_number"
                            value="{{ old('serial_number', $machine->serial_number) }}"
                            placeholder="أدخل الرقم التسلسلي">
                        @error('serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- نوع الماكينة -->
                    <div class="col-md-6 mb-3">
                        <label for="machine_type" class="form-label">نوع الماكينة <span class="text-danger">*</span></label>
                        <select class="form-control select2 @error('machine_type') is-invalid @enderror" id="machine_type" name="machine_type" required>
                            <option value="">اختر نوع الماكينة</option>
                            <option value="zkteco" {{ old('machine_type', $machine->machine_type) == 'zkteco' ? 'selected' : '' }}>ZkTeco</option>
                            <option value="hikvision" {{ old('machine_type', $machine->machine_type) == 'hikvision' ? 'selected' : '' }}>Hikvision</option>
                            <option value="suprema" {{ old('machine_type', $machine->machine_type) == 'suprema' ? 'selected' : '' }}>Suprema</option>
                            <option value="other" {{ old('machine_type', $machine->machine_type) == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                        @error('machine_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- المضيف -->
                    <div class="col-md-6 mb-3">
                        <label for="host_name" class="form-label">المضيف <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control @error('host_name') is-invalid @enderror"
                            id="host_name"
                            name="host_name"
                            value="{{ old('host_name', $machine->host_name) }}"
                            placeholder="مثال: 192.168.1.100 أو dns.website.com"
                            required>
                        @error('host_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- رقم المنفذ -->
                    <div class="col-md-6 mb-3">
                        <label for="port_number" class="form-label">رقم المنفذ <span class="text-danger">*</span></label>
                        <input
                            type="number"
                            class="form-control @error('port_number') is-invalid @enderror"
                            id="port_number"
                            name="port_number"
                            value="{{ old('port_number', $machine->port_number) }}"
                            placeholder="4022"
                            min="1"
                            max="65535"
                            required>
                        @error('port_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- مفتاح الاتصال -->
                    <div class="col-md-6 mb-3">
                        <label for="connection_key" class="form-label">مفتاح الاتصال</label>
                        <input
                            type="text"
                            class="form-control @error('connection_key') is-invalid @enderror"
                            id="connection_key"
                            name="connection_key"
                            value="{{ old('connection_key', $machine->connection_key) }}"
                            placeholder="أدخل مفتاح الاتصال (اختياري)">
                        @error('connection_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الحالة -->
                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="status"
                                name="status"
                                value="1"
                                {{ old('status', $machine->status) ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">
                                تفعيل الماكينة
                            </label>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-primary me-2 waves-effect waves-light">
                        <i class="fa fa-save me-2"></i>تحديث
                    </button>
                    <a href="{{ route('machines.index') }}" class="btn btn-outline-secondary waves-effect waves-light">
                        <i class="fa fa-arrow-right me-2"></i>رجوع
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('machine-edit-form');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // التحقق من صحة البيانات
        const name = document.getElementById('name').value.trim();
        const machineType = document.getElementById('machine_type').value;
        const hostName = document.getElementById('host_name').value.trim();
        const portNumber = document.getElementById('port_number').value;

        if (!name || !machineType || !hostName || !portNumber) {
            Swal.fire({
                title: 'خطأ!',
                text: 'يرجى ملء جميع الحقول المطلوبة',
                icon: 'error',
                confirmButtonText: 'موافق'
            });
            return;
        }

        // إظهار رسالة التأكيد
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم تحديث بيانات الماكينة',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، حدث',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // إظهار رسالة التحميل
                Swal.fire({
                    title: 'جاري التحديث...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // إرسال النموذج
                form.submit();
            }
        });
    });

    // إظهار رسالة النجاح إذا كانت موجودة في الجلسة
    @if(session('success'))
        Swal.fire({
            title: 'تم بنجاح!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'موافق'
        });
    @endif

    // إظهار رسالة الخطأ إذا كانت موجودة في الجلسة
    @if(session('error'))
        Swal.fire({
            title: 'خطأ!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'موافق'
        });
    @endif
});
</script>
@endsection