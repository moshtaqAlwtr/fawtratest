@extends('master')

@section('title', 'سياسة الإجازات')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">سياسة الإجازات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item active">إضافة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>
                <div>
                    <button type="button" id="cancelBtn" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i> إلغاء
                    </button>
                    <button type="button" id="saveBtn" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i> حفظ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4 p-1" style="background: #f8f8f8">سياسة الإجازات</h4>
            <form id="addHolidayForm" method="POST" action="{{ route('leave_policy.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        <div class="invalid-feedback">يرجى إدخال اسم سياسة الإجازات</div>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="0" {{ old('status') == '0' ? 'selected' : 'selected' }}>نشط</option>
                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                        <div class="invalid-feedback">يرجى اختيار حالة السياسة</div>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="اكتب وصف للسياسة...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <br><br>
                <h4 class="mb-4 p-1" style="background: #f8f8f8">الإجازات</h4>
                <div class="col-md-6">
                    <p>عدد الإجازات المختارة: <strong id="holidayCount">1</strong></p>
                </div>
                <table class="table table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>الرقم المتسلسل</th>
                            <th>نوع الإجازة</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="holidayTable">
                        <tr>
                            <td style="width: 10%">1</td>
                            <td>
                                <select name="leave_type_id[]" class="form-control leave-type-select" required>
                                    <option value="" disabled selected>-- اختر نوع الإجازة --</option>
                                    @foreach ($leave_types as $leave_type)
                                        <option value="{{ $leave_type->id }}" {{ old('leave_type_id.0') == $leave_type->id ? 'selected' : '' }}>{{ $leave_type->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">يرجى اختيار نوع الإجازة</div>
                            </td>
                            <td style="width: 10%">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-row">إزالة <i class="fa fa-minus-circle"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" id="addRow" class="btn btn-outline-success btn-sm">إضافة <i class="fa fa-plus-circle"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const holidayTable = document.getElementById('holidayTable');
        const holidayCount = document.getElementById('holidayCount');
        const form = document.getElementById('addHolidayForm');
        let rowCount = holidayTable.rows.length;

        // Leave types for dynamic rows
        const leaveTypesOptions = `
            <option value="" disabled selected>-- اختر نوع الإجازة --</option>
            @foreach ($leave_types as $leave_type)
                <option value="{{ $leave_type->id }}">{{ $leave_type->name }}</option>
            @endforeach
        `;

        // Function to update holiday count
        function updateHolidayCount() {
            holidayCount.textContent = holidayTable.rows.length;
            // Update serial numbers
            const rows = holidayTable.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.children[0].textContent = index + 1;
            });
        }

        // Function to check for duplicate leave types
        function checkDuplicateLeaveTypes() {
            const selects = document.querySelectorAll('.leave-type-select');
            const selectedValues = [];
            let hasDuplicates = false;

            selects.forEach(select => {
                if (select.value) {
                    if (selectedValues.includes(select.value)) {
                        hasDuplicates = true;
                    }
                    selectedValues.push(select.value);
                }
            });

            return hasDuplicates;
        }

        // Add Row Functionality
        document.getElementById('addRow').addEventListener('click', function () {
            rowCount++;
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td style="width: 10%">${rowCount}</td>
                <td>
                    <select name="leave_type_id[]" class="form-control leave-type-select" required>
                        ${leaveTypesOptions}
                    </select>
                    <div class="invalid-feedback">يرجى اختيار نوع الإجازة</div>
                </td>
                <td style="width: 10%">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row">إزالة <i class="fa fa-minus-circle"></i></button>
                </td>
            `;
            holidayTable.appendChild(newRow);
            updateHolidayCount();
        });

        // Remove Row Functionality
        holidayTable.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
                if (holidayTable.rows.length === 1) {
                    Swal.fire({
                        title: 'تنبيه!',
                        text: 'لا يمكن حذف آخر إجازة. يجب أن تحتوي السياسة على نوع إجازة واحد على الأقل.',
                        icon: 'warning',
                        confirmButtonText: 'موافق',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                const row = e.target.closest('tr');
                row.remove();
                updateHolidayCount();
            }
        });

        // Cancel Button Functionality
        document.getElementById('cancelBtn').addEventListener('click', function () {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم إلغاء جميع التغييرات التي قمت بها!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، إلغاء',
                cancelButtonText: 'لا، استمر'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('leave_policy.index') }}";
                }
            });
        });

        // Save Button Functionality
        document.getElementById('saveBtn').addEventListener('click', function () {
            // Validate form
            if (!validateForm()) {
                return;
            }

            Swal.fire({
                title: 'تأكيد الحفظ',
                text: 'هل تريد حفظ سياسة الإجازات؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'جاري الحفظ...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    form.submit();
                }
            });
        });

        // Form validation function
        function validateForm() {
            let isValid = true;
            const name = document.getElementById('name');
            const status = document.getElementById('status');
            const leaveTypeSelects = document.querySelectorAll('.leave-type-select');

            // Clear previous validation states
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            // Validate name
            if (!name.value.trim()) {
                name.classList.add('is-invalid');
                isValid = false;
            }

            // Validate status
            if (!status.value) {
                status.classList.add('is-invalid');
                isValid = false;
            }

            // Validate leave types
            leaveTypeSelects.forEach((select, index) => {
                if (!select.value) {
                    select.classList.add('is-invalid');
                    isValid = false;
                }
            });

            // Check for duplicate leave types
            if (checkDuplicateLeaveTypes()) {
                isValid = false;
                Swal.fire({
                    title: 'أنواع إجازات مكررة!',
                    text: 'يوجد أنواع إجازات مكررة في السياسة. يرجى اختيار أنواع مختلفة.',
                    icon: 'error',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#d33'
                });
                return false;
            }

            if (!isValid) {
                Swal.fire({
                    title: 'خطأ في البيانات!',
                    text: 'يرجى ملء جميع الحقول المطلوبة',
                    icon: 'error',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#d33'
                });
            }

            return isValid;
        }

        // Handle form submission response
        @if(session('success'))
            Swal.fire({
                title: 'تم بنجاح!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('leave_policy.index') }}";
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'حدث خطأ!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#d33'
            });
        @endif

        @if ($errors->any())
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += '• {{ $error }}\n';
            @endforeach

            Swal.fire({
                title: 'أخطاء في البيانات!',
                text: errorMessages,
                icon: 'error',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#d33'
            });
        @endif
    });
</script>
@endsection