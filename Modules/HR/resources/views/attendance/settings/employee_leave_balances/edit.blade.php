@extends('master')

@section('title', 'تعديل رصيد إجازة')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل رصيد إجازة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('employee_leave_balances.index') }}">أرصدة الإجازات</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
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
                    <button type="button" id="updateBtn" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i> تحديث
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Balance Info -->
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3 p-1" style="background: #e8f4fd; color: #31708f;">معلومات الرصيد الحالي</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="alert alert-info mb-0">
                        <strong>الموظف:</strong><br>
                        {{ $balance->employee->full_name }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-primary mb-0">
                        <strong>نوع الإجازة:</strong><br>
                        {{ $balance->leaveType->name }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-success mb-0">
                        <strong>الرصيد المستخدم:</strong><br>
                        {{ $balance->used_balance }} يوم
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-warning mb-0">
                        <strong>الرصيد المتبقي:</strong><br>
                        {{ $balance->getActualRemainingBalance() }} يوم
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4 p-1" style="background: #f8f8f8">تعديل معلومات الرصيد</h4>
            <form id="editLeaveBalanceForm" method="POST" action="{{ route('employee_leave_balances.update', $balance->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- معلومات غير قابلة للتعديل -->
                    <div class="col-md-6 mb-3">
                        <label for="employee_display" class="form-label">الموظف</label>
                        <input type="text" class="form-control" id="employee_display"
                               value="{{ $balance->employee->full_name }} - {{ $balance->employee->employee_code ?? $balance->employee->id }}"
                               disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="leave_type_display" class="form-label">نوع الإجازة</label>
                        <input type="text" class="form-control" id="leave_type_display"
                               value="{{ $balance->leaveType->name }}" disabled>
                    </div>

                    <!-- السنة -->
                    <div class="col-md-6 mb-3">
                        <label for="year" class="form-label">السنة <span class="text-danger">*</span></label>
                        <select class="form-control" id="year" name="year" required>
                            @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                <option value="{{ $i }}" {{ old('year', $balance->year) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <div class="invalid-feedback">يرجى اختيار السنة</div>
                    </div>

                    <!-- الرصيد المبدئي -->
                    <div class="col-md-6 mb-3">
                        <label for="initial_balance" class="form-label">الرصيد المبدئي <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="initial_balance" name="initial_balance"
                               value="{{ old('initial_balance', $balance->initial_balance) }}" min="0" required>
                        <div class="invalid-feedback">يرجى إدخال الرصيد المبدئي</div>
                    </div>

                    <!-- المرحل من السنة السابقة -->
                    <div class="col-md-6 mb-3">
                        <label for="carried_forward" class="form-label">المرحل من السنة السابقة</label>
                        <input type="number" class="form-control" id="carried_forward" name="carried_forward"
                               value="{{ old('carried_forward', $balance->carried_forward) }}" min="0">
                        <small class="text-muted">الأيام المرحلة من العام السابق</small>
                    </div>

                    <!-- الرصيد الإضافي -->
                    <div class="col-md-6 mb-3">
                        <label for="additional_balance" class="form-label">الرصيد الإضافي</label>
                        <input type="number" class="form-control" id="additional_balance" name="additional_balance"
                               value="{{ old('additional_balance', $balance->additional_balance) }}" min="0">
                        <small class="text-muted">أيام إضافية (مكافآت، تعويضات، إلخ)</small>
                    </div>

                    <!-- عرض الرصيد المحدث -->
                    <div class="col-md-12 mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="alert alert-info mb-0">
                                    <strong>الرصيد الإجمالي الجديد: <span id="newTotalBalance">{{ $balance->getTotalAvailableBalance() }}</span> يوم</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-secondary mb-0">
                                    <strong>المستخدم: {{ $balance->used_balance }} يوم</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-success mb-0" id="newRemainingAlert">
                                    <strong>المتبقي الجديد: <span id="newRemainingBalance">{{ $balance->getActualRemainingBalance() }}</span> يوم</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تحذير إذا كان الرصيد الجديد أقل من المستخدم -->
                    <div id="insufficientBalanceWarning" class="col-md-12 mb-3" style="display: none;">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>تحذير:</strong> الرصيد الإجمالي الجديد أقل من الرصيد المستخدم حالياً.
                            هذا قد يؤثر على الطلبات المعتمدة للموظف.
                        </div>
                    </div>

                    <!-- الملاحظات -->
                    <div class="col-md-12 mb-3">
                        <label for="notes" class="form-label">الملاحظات</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="أدخل سبب التعديل أو أي ملاحظات إضافية...">{{ old('notes', $balance->notes) }}</textarea>
                        <small class="text-muted">يُنصح بذكر سبب التعديل في الملاحظات</small>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- معلومات الاستخدام إذا كان هناك طلبات معتمدة -->
    @if($balance->used_balance > 0)
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3 p-1" style="background: #fff3cd; color: #856404;">معلومات الاستخدام</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-warning">
                        <strong>الطلبات المعتمدة:</strong> {{ $approvedRequestsCount ?? 0 }} طلب<br>
                        <strong>إجمالي الأيام المستخدمة:</strong> {{ $balance->used_balance }} يوم
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <strong>آخر استخدام:</strong> {{ $lastUsageDate ?? 'غير متوفر' }}<br>
                        <strong>معدل الاستخدام:</strong> {{ $usagePercentage ?? 0 }}%
                    </div>
                </div>
            </div>
            <small class="text-warning">
                <i class="fa fa-exclamation-triangle"></i>
                تنبيه: هذا الرصيد مُستخدم حالياً. التعديلات قد تؤثر على حسابات الإجازات الحالية.
            </small>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('editLeaveBalanceForm');
        const initialBalanceInput = document.getElementById('initial_balance');
        const carriedForwardInput = document.getElementById('carried_forward');
        const additionalBalanceInput = document.getElementById('additional_balance');
        const usedBalance = {{ $balance->used_balance }};

        const originalValues = {
            year: {{ $balance->year }},
            initial_balance: {{ $balance->initial_balance }},
            carried_forward: {{ $balance->carried_forward }},
            additional_balance: {{ $balance->additional_balance }},
            notes: '{{ $balance->notes ?? '' }}'
        };

        // Update balance calculations
        function updateBalanceCalculation() {
            const initial = parseInt(initialBalanceInput.value) || 0;
            const carried = parseInt(carriedForwardInput.value) || 0;
            const additional = parseInt(additionalBalanceInput.value) || 0;
            const newTotal = initial + carried + additional;
            const newRemaining = Math.max(0, newTotal - usedBalance);

            document.getElementById('newTotalBalance').textContent = newTotal;
            document.getElementById('newRemainingBalance').textContent = newRemaining;

            // Show warning if insufficient balance
            const warningDiv = document.getElementById('insufficientBalanceWarning');
            const remainingAlert = document.getElementById('newRemainingAlert');

            if (newTotal < usedBalance) {
                warningDiv.style.display = 'block';
                remainingAlert.className = 'alert alert-danger mb-0';
            } else {
                warningDiv.style.display = 'none';
                remainingAlert.className = 'alert alert-success mb-0';
            }
        }

        // Event listeners for balance calculation
        [initialBalanceInput, carriedForwardInput, additionalBalanceInput].forEach(input => {
            input.addEventListener('input', updateBalanceCalculation);
        });

        // Check if form has changes
        function hasChanges() {
            const currentValues = {
                year: parseInt(document.getElementById('year').value),
                initial_balance: parseInt(initialBalanceInput.value) || 0,
                carried_forward: parseInt(carriedForwardInput.value) || 0,
                additional_balance: parseInt(additionalBalanceInput.value) || 0,
                notes: document.getElementById('notes').value
            };

            return JSON.stringify(currentValues) !== JSON.stringify(originalValues);
        }

        // Cancel Button
        document.getElementById('cancelBtn').addEventListener('click', function () {
            if (hasChanges()) {
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: 'سيتم إلغاء جميع التغييرات التي قمت بها!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، إلغاء التغييرات',
                    cancelButtonText: 'لا، استمر في التعديل'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('employee_leave_balances.index') }}";
                    }
                });
            } else {
                window.location.href = "{{ route('employee_leave_balances.index') }}";
            }
        });

        // Update Button
        document.getElementById('updateBtn').addEventListener('click', function () {
            if (!hasChanges()) {
                Swal.fire({
                    title: 'لا توجد تغييرات!',
                    text: 'لم تقم بإجراء أي تغييرات على البيانات',
                    icon: 'info',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            if (!validateForm()) {
                return;
            }

            let confirmText = 'هل تريد حفظ التغييرات على رصيد الإجازة؟';
            @if($balance->used_balance > 0)
                confirmText = 'هذا الرصيد مُستخدم حالياً. هل تريد المتابعة مع التحديث؟';
            @endif

            Swal.fire({
                title: 'تأكيد التحديث',
                text: confirmText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احفظ التغييرات',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Additional warning for insufficient balance
                    const newTotal = (parseInt(initialBalanceInput.value) || 0) +
                                   (parseInt(carriedForwardInput.value) || 0) +
                                   (parseInt(additionalBalanceInput.value) || 0);

                    if (newTotal < usedBalance) {
                        Swal.fire({
                            title: 'تحذير: رصيد غير كافي!',
                            text: `الرصيد الجديد (${newTotal}) أقل من المستخدم (${usedBalance}). هذا قد يؤثر على الطلبات المعتمدة. هل تريد المتابعة؟`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'نعم، احفظ رغم التحذير',
                            cancelButtonText: 'إلغاء'
                        }).then((confirmResult) => {
                            if (confirmResult.isConfirmed) {
                                Swal.fire({
                                    title: 'جاري التحديث...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                                form.submit();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'جاري التحديث...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        form.submit();
                    }
                }
            });
        });

        // Form validation
        function validateForm() {
            let isValid = true;
            const year = document.getElementById('year');

            // Clear previous validation states
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            // Validate year
            if (!year.value) {
                year.classList.add('is-invalid');
                isValid = false;
            }

            // Validate initial balance
            if (!initialBalanceInput.value || parseInt(initialBalanceInput.value) < 0) {
                initialBalanceInput.classList.add('is-invalid');
                isValid = false;
            }

            // Validate numeric fields are not negative
            const numericFields = [carriedForwardInput, additionalBalanceInput];
            numericFields.forEach(field => {
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
                title: 'تم التحديث بنجاح!',
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

        // Warn before leaving page with unsaved changes
        window.addEventListener('beforeunload', function (e) {
            if (hasChanges()) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Initialize balance calculation
        updateBalanceCalculation();
    });
</script>
@endsection
