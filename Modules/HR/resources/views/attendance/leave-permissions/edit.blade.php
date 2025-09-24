@extends('master')

@section('title', 'تعديل أذونات إجازة')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل أذونات إجازة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('leave_permissions.index') }}">أذونات الإجازة</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Form Actions -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> إلزامية</label>
                </div>
                <div class="btn-group">
                    <a href="{{ route('leave_permissions.index') }}" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i> إلغاء
                    </a>
                    <button type="submit" form="editLeavePermissionForm" class="btn btn-outline-primary" id="updateBtn">
                        <i class="fa fa-edit"></i> تحديث
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4">معلومات إذن الإجازة</h4>
            <form id="editLeavePermissionForm" method="POST" action="{{ route('leave_permissions.update', $leavePermission->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- Employee Selection -->
                    <div class="col-md-6 mb-3">
                        <label for="employee" class="form-label">موظف <span class="text-danger">*</span></label>
                        <select class="form-control select2 @error('employee_id') is-invalid @enderror"
                                id="employee" name="employee_id" required>
                            <option value="" disabled>اختر موظف</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                        {{ old('employee_id', $leavePermission->employee_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }} - {{ $employee->employee_number ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div class="col-md-3 mb-3">
                        <label for="start-date" class="form-label">التاريخ من <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               id="start-date"
                               name="start_date"
                               value="{{ old('start_date', $leavePermission->start_date) }}"
                               required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div class="col-md-3 mb-3">
                        <label for="end-date" class="form-label">التاريخ إلى <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('end_date') is-invalid @enderror"
                               id="end-date"
                               name="end_date"
                               value="{{ old('end_date', $leavePermission->end_date) }}"
                               required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- نوع الإذن -->
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">نوع الإذن <span class="text-danger">*</span></label>
                        <select class="form-control @error('type') is-invalid @enderror"
                                id="type" name="type" required>
                            <option value="" disabled>اختر نوع الإذن</option>
                            <option value="late_arrival" {{ old('type', $leavePermission->type) == 'late_arrival' ? 'selected' : '' }}>الوصول المتأخر</option>
                            <option value="early_departure" {{ old('type', $leavePermission->type) == 'early_departure' ? 'selected' : '' }}>الانصراف المبكر</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- نوع الإجازة -->
                    <div class="col-md-6 mb-3">
                        <label for="leave-type" class="form-label">نوع الإجازة <span class="text-danger">*</span></label>
                        <select class="form-control @error('leave_type') is-invalid @enderror"
                                id="leave-type" name="leave_type" required>
                            <option value="" disabled>اختر نوع الإجازة</option>
                            <option value="annual_leave" {{ old('leave_type', $leavePermission->leave_type) == 'annual_leave' ? 'selected' : '' }}>إجازة اعتيادية</option>
                            <option value="emergency_leave" {{ old('leave_type', $leavePermission->leave_type) == 'emergency_leave' ? 'selected' : '' }}>إجازة عرضية</option>
                        </select>
                        @error('leave_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submission Date -->
                    <div class="col-md-6 mb-3">
                        <label for="application-date" class="form-label">تاريخ التقديم</label>
                        <input type="date"
                               class="form-control @error('submission_date') is-invalid @enderror"
                               id="application-date"
                               name="submission_date"
                               value="{{ old('submission_date', $leavePermission->submission_date) }}">
                        @error('submission_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Current Attachment Display -->
                    @if($leavePermission->attachments)
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المرفق الحالي</label>
                        <div class="d-flex align-items-center">
                            <a href="{{ asset('storage/' . $leavePermission->attachments) }}" target="_blank" class="btn btn-outline-info btn-sm me-2">
                                <i class="fa fa-eye"></i> عرض المرفق الحالي
                            </a>
                            <small class="text-muted">{{ basename($leavePermission->attachments) }}</small>
                        </div>
                    </div>
                    @endif

                    <!-- New Attachments -->
                    <div class="col-md-12 mb-3">
                        <label for="attachments" class="form-label">
                            @if($leavePermission->attachments)
                                تحديث المرفقات (اختياري)
                            @else
                                المرفقات
                            @endif
                        </label>
                        <div class="custom-file">
                            <input type="file"
                                   name="attachments"
                                   id="attachments"
                                   class="custom-file-input @error('attachments') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <label class="custom-file-label" for="attachments">اختر ملف جديد...</label>
                        </div>
                        <small class="form-text text-muted">
                            @if($leavePermission->attachments)
                                ترك هذا الحقل فارغ سيحتفظ بالمرفق الحالي.
                            @endif
                            الحد الأقصى لحجم الملف: 5 ميجابايت. الأنواع المدعومة: PDF, DOC, DOCX, JPG, PNG
                        </small>
                        @error('attachments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-md-12 mb-3">
                        <label for="notes" class="form-label">ملاحظة</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes"
                                  rows="3"
                                  placeholder="أدخل ملاحظاتك"
                                  name="notes"
                                  maxlength="1000">{{ old('notes', $leavePermission->notes) }}</textarea>
                        <small class="form-text text-muted">
                            <span id="notes-count">{{ strlen(old('notes', $leavePermission->notes)) }}</span>/1000 حرف
                        </small>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "اختر موظف",
        allowClear: true,
        dir: "rtl"
    });

    // File input label update
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName || 'اختر ملف جديد...');
    });

    // Notes character counter
    $('#notes').on('input', function() {
        let length = $(this).val().length;
        $('#notes-count').text(length);

        if (length > 1000) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Date validation
    $('#start-date').on('change', function() {
        let startDate = $(this).val();
        $('#end-date').attr('min', startDate);

        // Clear end date if it's before start date
        let endDate = $('#end-date').val();
        if (endDate && endDate < startDate) {
            $('#end-date').val('');

            // إظهار تحذير
            Swal.fire({
                title: 'تنبيه!',
                text: 'تم مسح تاريخ النهاية لأنه كان أقل من تاريخ البداية',
                icon: 'info',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    });

    // Form submission with SweetAlert confirmation
    $('#editLeavePermissionForm').on('submit', function(e) {
        e.preventDefault(); // منع الإرسال المباشر

        // التحقق من صحة البيانات أولاً
        let isValid = true;
        let errorMessage = '';

        // التحقق من الموظف
        if (!$('#employee').val()) {
            isValid = false;
            errorMessage = 'يرجى اختيار موظف';
        }
        // التحقق من تاريخ البداية
        else if (!$('#start-date').val()) {
            isValid = false;
            errorMessage = 'يرجى إدخال تاريخ البداية';
        }
        // التحقق من تاريخ النهاية
        else if (!$('#end-date').val()) {
            isValid = false;
            errorMessage = 'يرجى إدخال تاريخ النهاية';
        }
        // التحقق من نوع الإذن
        else if (!$('#type').val()) {
            isValid = false;
            errorMessage = 'يرجى اختيار نوع الإذن';
        }
        // التحقق من نوع الإجازة
        else if (!$('#leave-type').val()) {
            isValid = false;
            errorMessage = 'يرجى اختيار نوع الإجازة';
        }

        if (!isValid) {
            Swal.fire({
                title: 'بيانات ناقصة!',
                text: errorMessage,
                icon: 'warning',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // إظهار رسالة التأكيد للتعديل
        Swal.fire({
            title: 'تأكيد التحديث',
            text: 'هل أنت متأكد من تحديث بيانات إذن الإجازة؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#dc3545',
            confirmButtonText: '<i class="fa fa-edit"></i> نعم، حدث',
            cancelButtonText: '<i class="fa fa-times"></i> إلغاء',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-info mx-2',
                cancelButton: 'btn btn-danger mx-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // إظهار حالة التحميل
                $('#updateBtn').html('<i class="fa fa-spinner fa-spin"></i> جاري التحديث...').prop('disabled', true);

                // إظهار Toast للتحميل
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                Toast.fire({
                    icon: 'info',
                    title: 'جاري تحديث البيانات...'
                });

                // إرسال النموذج
                this.submit();
            }
        });
    });

    // Success/Error messages with SweetAlert
    @if(session('success'))
        Swal.fire({
            title: 'تم التحديث بنجاح!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'موافق',
            confirmButtonColor: '#28a745',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'خطأ في التحديث!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'موافق',
            confirmButtonColor: '#dc3545'
        });
    @endif

    // رسالة تأكيد إضافية لحذف المرفق (إذا كان موجود)
    $('.delete-attachment').on('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'حذف المرفق',
            text: 'هل أنت متأكد من حذف المرفق الحالي؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // يمكنك إضافة منطق حذف المرفق هنا
                window.location.href = $(this).attr('href');
            }
        });
    });
});
</script>
@endpush
