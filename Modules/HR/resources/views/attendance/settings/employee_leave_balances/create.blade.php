@extends('master')

@section('title', 'إضافة رصيد إجازة')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إضافة رصيد إجازة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('employee_leave_balances.index') }}">أرصدة الإجازات</a></li>
                            <li class="breadcrumb-item active">إضافة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
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
            <h4 class="mb-4 p-1" style="background: #f8f8f8">معلومات رصيد الإجازة</h4>
            <form id="addLeaveBalanceForm" method="POST" action="{{ route('employee_leave_balances.store') }}">
                @csrf
                <div class="row">
                    <!-- اختيار الموظف -->
                    <div class="col-md-6 mb-3">
                        <label for="employee_id" class="form-label">الموظف <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="employee_id" name="employee_id" required>
                            <option value="">اختر الموظف</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }} - {{ $employee->employee_code ?? $employee->id }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">يرجى اختيار الموظف</div>
                    </div>

                    <!-- اختيار نوع الإجازة -->
                    <div class="col-md-6 mb-3">
                        <label for="leave_type_id" class="form-label">نوع الإجازة <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="leave_type_id" name="leave_type_id" required>
                            <option value="">اختر نوع الإجازة</option>
                            @foreach($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->id }}" {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}
                                        data-max-days="{{ $leaveType->max_days_per_year }}">
                                    {{ $leaveType->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">يرجى اختيار نوع الإجازة</div>
                    </div>

                    <!-- السنة -->
                    <div class="col-md-6 mb-3">
                        <label for="year" class="form-label">السنة <span class="text-danger">*</span></label>
                        <select class="form-control" id="year" name="year" required>
                            @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                <option value="{{ $i }}" {{ old('year', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <div class="invalid-feedback">يرجى اختيار السنة</div>
                    </div>

                    <!-- الرصيد المبدئي -->
                    <div class="col-md-6 mb-3">
                        <label for="initial_balance" class="form-label">الرصيد المبدئي <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="initial_balance" name="initial_balance"
                               value="{{ old('initial_balance') }}" min="0" required>
                        <small class="text-muted" id="suggestedBalance"></small>
                        <div class="invalid-feedback">يرجى إدخال الرصيد المبدئي</div>
                    </div>

                    <!-- المرحل من السنة السابقة -->
                    <div class="col-md-6 mb-3">
                        <label for="carried_forward" class="form-label">المرحل من السنة السابقة</label>
                        <input type="number" class="form-control" id="carried_forward" name="carried_forward"
                               value="{{ old('carried_forward', 0) }}" min="0">
                        <small class="text-muted">الأيام المرحلة من العام السابق</small>
                    </div>

                    <!-- الرصيد الإضافي -->
                    <div class="col-md-6 mb-3">
                        <label for="additional_balance" class="form-label">الرصيد الإضافي</label>
                        <input type="number" class="form-control" id="additional_balance" name="additional_balance"
                               value="{{ old('additional_balance', 0) }}" min="0">
                        <small class="text-muted">أيام إضافية (مكافآت، تعويضات، إلخ)</small>
                    </div>

                    <!-- عرض الرصيد الإجمالي -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الرصيد الإجمالي المتاح</label>
                        <div class="alert alert-info mb-0" id="totalBalance">
                            <strong>الإجمالي: <span id="totalBalanceValue">0</span> يوم</strong>
                        </div>
                    </div>

                    <!-- الملاحظات -->
                    <div class="col-md-12 mb-3">
                        <label for="notes" class="form-label">الملاحظات</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="أدخل أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- تحقق من وجود رصيد سابق -->
                <div id="existingBalanceAlert" class="alert alert-warning" style="display: none;">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>تنبيه:</strong> يوجد رصيد سابق لهذا الموظف في نفس السنة ونوع الإجازة.
                    <div id="existingBalanceDetails"></div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('addLeaveBalanceForm');
        const employeeSelect = document.getElementById('employee_id');
        const leaveTypeSelect = document.getElementById('leave_type_id');
        const yearSelect = document.getElementById('year');
        const initialBalanceInput = document.getElementById('initial_balance');
        const carriedForwardInput = document.getElementById('carried_forward');
        const additionalBalanceInput = document.getElementById('additional_balance');

        // Initialize Select2
        $('.select2').select2({
            placeholder: 'اختر...',
            allowClear: true
        });

        // Update suggested balance when leave type changes
        leaveTypeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const maxDays = selectedOption.getAttribute('data-max-days');

            if (maxDays) {
                document.getElementById('suggestedBalance').textContent = `الحد الأقصى المقترح: ${maxDays} يوم`;
                if (!initialBalanceInput.value) {
                    initialBalanceInput.value = maxDays;
                    updateTotalBalance();
                }
            } else {
                document.getElementById('suggestedBalance').textContent = '';
            }
        });

        // Update total balance calculation
        function updateTotalBalance() {
            const initial = parseInt(initialBalanceInput.value) || 0;
            const carried = parseInt(carriedForwardInput.value) || 0;
            const additional = parseInt(additionalBalanceInput.value) || 0;
            const total = initial + carried + additional;

            document.getElementById('totalBalanceValue').textContent = total;
        }

        // Event listeners for balance calculation
        [initialBalanceInput, carriedForwardInput, additionalBalanceInput].forEach(input => {
            input.addEventListener('input', updateTotalBalance);
        });

        // Check for existing balance
        function checkExistingBalance() {
            const employeeId = employeeSelect.value;
            const leaveTypeId = leaveTypeSelect.value;
            const year = yearSelect.value;

            if (employeeId && leaveTypeId && year) {
                fetch(`/admin/employee-leave-balances/check-existing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        employee_id: employeeId,
                        leave_type_id: leaveTypeId,
                        year: year
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const alertDiv = document.getElementById('existingBalanceAlert');
                    const detailsDiv = document.getElementById('existingBalanceDetails');

                    if (data.exists) {
                        detailsDiv.innerHTML = `
                            <hr>
                            <strong>الرصيد الحالي:</strong> ${data.balance.initial_balance} يوم<br>
                            <strong>المستخدم:</strong> ${data.balance.used_balance} يوم<br>
                            <strong>المتبقي:</strong> ${data.balance.remaining_balance} يوم
                        `;
                        alertDiv.style.display = 'block';
                    } else {
                        alertDiv.style.display = 'none';
                    }
                });
            }
        }

        // Event listeners for checking existing balance
        [employeeSelect, leaveTypeSelect, yearSelect].forEach(select => {
            select.addEventListener('change', checkExistingBalance);
        });

        // Cancel Button
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
                    window.location.href = "{{ route('employee_leave_balances.index') }}";
                }
            });
        });

        // Save Button
        document.getElementById('saveBtn').addEventListener('click', function () {
            if (!validateForm()) {
                return;
            }

            Swal.fire({
                title: 'تأكيد الحفظ',
                text: 'هل تريد حفظ رصيد الإجازة؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'جاري الحفظ...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        });

        // Form validation
        function validateForm() {
            let isValid = true;
            const requiredFields = ['employee_id', 'leave_type_id', 'year', 'initial_balance'];

            // Clear previous validation states
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field.value || (fieldName === 'initial_balance' && parseInt(field.value) < 0)) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });

            // Validate numeric fields are not negative
            const numericFields = ['carried_forward', 'additional_balance'];
            numericFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field.value && parseInt(field.value) < 0) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });

            if (!isValid) {
                Swal.fire({
                    title: 'خطأ في البيانات!',
                    text: 'يرجى ملء جميع الحقول المطلوبة بشكل صحيح',
                    icon: 'error',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#d33'
                });
            }

            return isValid;
        }

        // Handle form responses
        @if(session('success'))
            Swal.fire({
                title: 'تم بنجاح!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('employee_leave_balances.index') }}";
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

        // Initialize total balance calculation
        updateTotalBalance();
    });
</script>
@endsection
