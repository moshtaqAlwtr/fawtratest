@extends('master')

@section('title', 'تحديث أيام الحضور')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تحديث أيام الحضور</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item active">تحديث</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">

        <!-- Form Actions -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>
                    <div>
                        <a href="{{ route('attendanceDays.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i> إلغاء
                        </a>
                        <button type="button" id="updateButton" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i> تحديث
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.alerts.success')
        @include('layouts.alerts.error')

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Section -->
        <div class="card">
            <div class="card-body">
                <form id="updateAttendanceForm" action="{{ route('attendanceDays.update', $attendanceDay->id) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <p style="background-color: #f8f8f8; padding: 10px"><strong>معلومات الحضور</strong></p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="employee-select" class="form-label">الموظف <span style="color: red">*</span></label>
                            <select id="employee-select" name="employee_id" class="form-control select2" required>
                                <option value="" disabled>اختر الموظف</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ $attendanceDay->employee_id == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <small class="text-danger" id="basic-default-name-error" class="error">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="attendance-date" class="form-label">تاريخ الحضور <span
                                    style="color: red">*</span></label>
                            <input type="date" name="attendance_date" class="form-control"
                                placeholder="ادخل تاريخ الحضور"
                                value="{{ old('attendance_date', $attendanceDay->attendance_date) }}" required>
                            @error('attendance_date')
                                <small class="text-danger" id="basic-default-name-error" class="error">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>

                    <hr>

                    <!-- Attendance Status -->
                    <div class="mb-3">
                        <p class="mb-2"><strong>الحالة</strong></p>
                        <div class="row g-3">
                            <!-- Present -->
                            <div class="col-12">
                                <ul class="list-unstyled mb-0">
                                    <li class="d-inline-block mr-5">
                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="status"
                                                    id="customRadio1"
                                                    {{ old('status', $attendanceDay->status) == 'present' ? 'checked' : '' }}
                                                    value="present" required>
                                                <label class="custom-control-label" for="customRadio1">حاضر <span
                                                        style="color: red">*</span></label>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="d-inline-block mr-5">
                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="status"
                                                    id="customRadio2"
                                                    {{ old('status', $attendanceDay->status) == 'absent' ? 'checked' : '' }}
                                                    value="absent" required>
                                                <label class="custom-control-label" for="customRadio2">إجازة <span
                                                        style="color: red">*</span></label>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="d-inline-block mr-5">
                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="status"
                                                    id="customRadio3"
                                                    {{ old('status', $attendanceDay->status) == 'late' ? 'checked' : '' }}
                                                    value="late" required>
                                                <label class="custom-control-label" for="customRadio3">غائب <span
                                                        style="color: red">*</span></label>
                                            </div>
                                        </fieldset>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div id="attendance-rules" style="display: none">
                        <p style="background-color: #f8f8f8; padding: 10px"><strong>قواعد الحضور</strong></p>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="start-shift" class="form-label">بداية الوردية <span
                                        style="color: red">*</span></label>
                                <input type="time" name="start_shift" class="form-control"
                                    value="{{ old('start_shift', $attendanceDay->start_shift) }}">

                            </div>
                            <div class="col-md-6">
                                <label for="end-shift" class="form-label">نهاية الوردية <span
                                        style="color: red">*</span></label>
                                <input type="time" name="end_shift" class="form-control"
                                    value="{{ old('end_shift', $attendanceDay->end_shift) }}">

                            </div>
                            <div class="col-md-6">
                                <label for="login-time" class="form-label">تسجيل الدخول <span
                                        style="color: red">*</span></label>
                                <input type="time" name="login_time" class="form-control"
                                    value="{{ old('login_time', $attendanceDay->login_time) }}">

                            </div>
                            <div class="col-md-6">
                                <label for="logout-time" class="form-label">تسجيل الخروج <span
                                        style="color: red">*</span></label>
                                <input type="time" name="logout_time" class="form-control"
                                    value="{{ old('logout_time', $attendanceDay->logout_time) }}">

                            </div>
                        </div>
                    </div>

                    <div id="vacation-rules" style="display: none">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="absence-type" class="form-label">نوع الاجازة <span
                                        style="color: red">*</span></label>
                                <select id="absence-type" name="absence_type" class="form-control">
                                    <option value="" disabled>اختر نوع الاجازة</option>
                                    <option value="1"
                                        {{ old('absence_type', $attendanceDay->absence_type) == 1 ? 'selected' : '' }}>
                                        اجازة اعتيادية</option>
                                    <option value="2"
                                        {{ old('absence_type', $attendanceDay->absence_type) == 2 ? 'selected' : '' }}>
                                        اجازة عرضية</option>
                                </select>
                                @error('absence_type')
                                    <small class="text-danger" id="basic-default-name-error" class="error">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="absence-balance" class="form-label">رصيد الاجازة <span
                                        style="color: red">*</span></label>
                                <input type="number" name="absence_balance" class="form-control"
                                    value="{{ old('absence_balance', $attendanceDay->absence_balance) }}">
                                @error('absence_balance')
                                    <small class="text-danger" id="basic-default-name-error" class="error">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">الملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="أدخل الملاحظات هنا">{{ old('notes', $attendanceDay->notes) }}</textarea>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <!-- SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeeSelect = document.getElementById('employee-select');
            const attendanceDate = document.querySelector('input[name="attendance_date"]');
            const radios = document.querySelectorAll('input[name="status"]');
            const attendanceRules = document.getElementById('attendance-rules');
            const vacationRules = document.getElementById('vacation-rules');
            const updateButton = document.getElementById('updateButton');
            const form = document.getElementById('updateAttendanceForm');

            function toggleRadios() {
                const isEmployeeSelected = employeeSelect.value !== '';
                const isDateSelected = attendanceDate.value !== '';
                radios.forEach(radio => {
                    radio.disabled = !(isEmployeeSelected && isDateSelected);
                });

                const selectedStatus = document.querySelector('input[name="status"]:checked');
                if (selectedStatus) {
                    toggleSections(selectedStatus.value);
                }
            }

            function toggleSections(status) {
                // الحصول على العناصر
                const startShiftInput = document.querySelector('input[name="start_shift"]');
                const endShiftInput = document.querySelector('input[name="end_shift"]');
                const loginTimeInput = document.querySelector('input[name="login_time"]');
                const logoutTimeInput = document.querySelector('input[name="logout_time"]');
                const absenceTypeSelect = document.querySelector('select[name="absence_type"]');
                const absenceBalanceInput = document.querySelector('input[name="absence_balance"]');

                if (status === 'present') { // حاضر
                    attendanceRules.style.display = 'block';
                    vacationRules.style.display = 'none';

                    // جعل حقول الحضور مطلوبة
                    if (startShiftInput) startShiftInput.required = true;
                    if (endShiftInput) endShiftInput.required = true;
                    if (loginTimeInput) loginTimeInput.required = true;
                    if (logoutTimeInput) logoutTimeInput.required = true;

                    // إزالة required من حقول الإجازة
                    if (absenceTypeSelect) absenceTypeSelect.required = false;
                    if (absenceBalanceInput) absenceBalanceInput.required = false;

                } else if (status === 'absent') { // إجازة
                    attendanceRules.style.display = 'none';
                    vacationRules.style.display = 'block';

                    // إزالة required من حقول الحضور
                    if (startShiftInput) startShiftInput.required = false;
                    if (endShiftInput) endShiftInput.required = false;
                    if (loginTimeInput) loginTimeInput.required = false;
                    if (logoutTimeInput) logoutTimeInput.required = false;

                    // جعل حقول الإجازة مطلوبة
                    if (absenceTypeSelect) absenceTypeSelect.required = true;
                    if (absenceBalanceInput) absenceBalanceInput.required = true;

                } else { // غائب أو حالة أخرى
                    attendanceRules.style.display = 'none';
                    vacationRules.style.display = 'none';

                    // إزالة required من جميع الحقول الاختيارية
                    if (startShiftInput) startShiftInput.required = false;
                    if (endShiftInput) endShiftInput.required = false;
                    if (loginTimeInput) loginTimeInput.required = false;
                    if (logoutTimeInput) logoutTimeInput.required = false;
                    if (absenceTypeSelect) absenceTypeSelect.required = false;
                    if (absenceBalanceInput) absenceBalanceInput.required = false;
                }
            }

            // إضافة مستمعين للأحداث
            employeeSelect.addEventListener('change', toggleRadios);
            attendanceDate.addEventListener('input', toggleRadios);

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    toggleSections(this.value);
                });
            });

            // SweetAlert2 للتأكيد قبل التحديث
            updateButton.addEventListener('click', function(e) {
                e.preventDefault();

                // التحقق من صحة النموذج باستخدام HTML5 validation
                if (!form.checkValidity()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تحذير!',
                        text: 'يرجى ملء جميع الحقول المطلوبة بشكل صحيح',
                        confirmButtonText: 'موافق',
                        confirmButtonColor: '#ffc107'
                    });
                    form.reportValidity();
                    return;
                }

                // عرض تأكيد التحديث
                Swal.fire({
                    title: 'تأكيد التحديث',
                    text: 'هل أنت متأكد من تحديث بيانات الحضور؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'نعم، حدث',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'جاري التحديث...',
                            text: 'يرجى الانتظار',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        form.submit();
                    }
                });
            });

            // تشغيل التحديث الأولي
            toggleRadios();

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'تم التحديث بنجاح!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#28a745'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#dc3545'
                });
            @endif
        });
    </script>
@endsection
