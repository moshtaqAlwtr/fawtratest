@extends('master')

@section('title', 'إضافة أذونات إجازة')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إضافة أذونات إجازة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('leave_permissions.index') }}">أذونات الإجازة</a></li>
                            <li class="breadcrumb-item active">إضافة جديد</li>
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
                    <button type="submit" form="addLeavePermissionForm" class="btn btn-outline-primary" id="saveBtn">
                        <i class="fa fa-save"></i> حفظ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4">معلومات إذن الإجازة</h4>
            <form id="addLeavePermissionForm" method="POST" action="{{ route('leave_permissions.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Employee Selection -->
                    <div class="col-md-6 mb-3">
                        <label for="employee" class="form-label">موظف <span class="text-danger">*</span></label>
                        <select class="form-control select2 @error('employee_id') is-invalid @enderror"
                                id="employee" name="employee_id" required>
                            <option value="" disabled selected>اختر موظف</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                        {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }} - {{ $employee->employee_number ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Leave Type -->

                    <!-- Start Date -->
                    <div class="col-md-3 mb-3">
                        <label for="start-date" class="form-label">التاريخ من <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               id="start-date"
                               name="start_date"
                               value="{{ old('start_date') }}"
                               min="{{ date('Y-m-d') }}"
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
                               value="{{ old('end_date') }}"
                               min="{{ date('Y-m-d') }}"
                               required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div class="col-md-6 mb-3">
    <label for="type" class="form-label">نوع الإجازة <span class="text-danger">*</span></label>
    <select class="form-control @error('type') is-invalid @enderror"
            id="type" name="type" required>
        <option value="" disabled selected>اختر نوع الإجازة</option>
        <option value="annual_leave" {{ old('type') == 'annual_leave' ? 'selected' : '' }}>إجازة اعتيادية</option>
        <option value="emergency_leave" {{ old('type') == 'emergency_leave' ? 'selected' : '' }}>إجازة عرضية</option>
    </select>
    @error('type')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-6 mb-3">
    <label for="leave-type" class="form-label">النوع <span class="text-danger">*</span></label>
    <select class="form-control @error('leave_type') is-invalid @enderror"
            id="leave-type" name="leave_type" required>
        <option value="" disabled selected>اختر النوع</option>
        <option value="late_arrival" {{ old('leave_type') == 'late_arrival' ? 'selected' : '' }}>الوصول المتأخر</option>
        <option value="early_departure" {{ old('leave_type') == 'early_departure' ? 'selected' : '' }}>الانصراف المبكر</option>
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
                               value="{{ old('submission_date', date('Y-m-d')) }}">
                        @error('submission_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Attachments -->
                    <div class="col-md-12 mb-3">
                        <label for="attachments" class="form-label">المرفقات</label>
                        <div class="custom-file">
                            <input type="file"
                                   name="attachments"
                                   id="attachments"
                                   class="custom-file-input @error('attachments') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <label class="custom-file-label" for="attachments">اختر ملف...</label>
                        </div>
                        <small class="form-text text-muted">
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
                                  maxlength="1000">{{ old('notes') }}</textarea>
                        <small class="form-text text-muted">
                            <span id="notes-count">0</span>/1000 حرف
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
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName || 'اختر ملف...');
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
        }
    });

    // Form submission with loading
    $('#addLeavePermissionForm').on('submit', function(e) {
        $('#saveBtn').html('<i class="fa fa-spinner fa-spin"></i> جاري الحفظ...').prop('disabled', true);
    });

    // Success/Error messages with SweetAlert
    @if(session('success'))
        Swal.fire({
            title: 'تم بنجاح!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'موافق'
        });
    @endif

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
@endpush
