@extends('master')

@section('title')
    تعديل عملية الدفع
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل عملية الدفع</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="paymentEditForm" action="{{ route('PaymentSupplier.updateSupplierPayment', $payment) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- عرض الأخطاء -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-danger" id="cancelBtn">
                            <i class="fa fa-ban"></i> الغاء
                        </button>
                        <button type="submit" class="btn btn-outline-primary" id="updateBtn">
                            <i class="fa fa-save"></i> تحديث عملية الدفع
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- الحقول -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="amount" class="form-label">المبلغ <span style="color: red">*</span></label>
                        <input type="number" id="amount" name="amount" class="form-control" placeholder="المبلغ"
                            step="0.01" value="{{ old('amount', $payment->amount) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="payment_date" class="form-label">تاريخ الدفع <span style="color: red">*</span></label>
                        <input type="date" id="payment_date" name="payment_date" class="form-control"
                               value="{{ old('payment_date', $payment->payment_date) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="payment_type" class="form-label">وسيلة الدفع <span style="color: red">*</span></label>
                        <select name="payment_type" class="form-control" id="payment_type" required>
                            <option value="">اختر نوع الدفع</option>
                            @foreach ($payments as $paymentMethod)
                                <option value="{{$paymentMethod->id}}"
                                    {{ old('payment_type', $payment->payment_type) == $paymentMethod->id ? 'selected' : '' }}>
                                    {{$paymentMethod->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="treasury_id" class="form-label">الخزينة المستخدمة</label>
                        <input type="text" class="form-control" placeholder="الخزينة المستخدمة"
                            value="{{$mainTreasuryAccount->name ?? "الخزينة الرئيسية"}}" readonly>
                        <input type="hidden" name="treasury_id" value="{{ $treasury_id }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status_payment" class="form-label">حالة الدفع <span style="color: red">*</span></label>
                        <select name="status_payment" class="form-control" id="status_payment" required>
                            <option value="">اختر حالة الدفع</option>
                            <option value="2" {{ old('status_payment', $payment->status_payment) == 2 ? 'selected' : '' }}>غير مكتمل</option>
                            <option value="1" {{ old('status_payment', $payment->status_payment) == 1 ? 'selected' : '' }}>مكتمل</option>
                            <option value="4" {{ old('status_payment', $payment->status_payment) == 4 ? 'selected' : '' }}>تحت المراجعة</option>
                            <option value="5" {{ old('status_payment', $payment->status_payment) == 5 ? 'selected' : '' }}>فاشلة</option>
                            <option value="3" {{ old('status_payment', $payment->status_payment) == 3 ? 'selected' : '' }}>مسودة</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="employee_id" class="form-label">تم التحصيل بواسطة <span style="color: red">*</span></label>
                        <select id="employee_id" name="employee_id" class="form-control" required>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ $employee->id == old('employee_id', $payment->employee_id) ? 'selected' : '' }}>
                                    {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="invoice_id_display" class="form-label">رقم المعرف</label>
                        <input type="text" id="invoice_id_display" name="id" class="form-control" placeholder="رقم المعرف"
                            value="{{ $invoiceId }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="payment_data" class="form-label">بيانات الدفع</label>
                        <textarea id="payment_data" name="payment_data" class="form-control" rows="2"
                            placeholder="مثل: رقم الشيك، رقم التحويل">{{ old('payment_data', $payment->payment_data) }}</textarea>
                    </div>
                </div>

                <input type="hidden" name="invoice_id" value="{{ $invoiceId }}">
                @if(isset($installmentId))
                    <input type="hidden" name="installment_id" value="{{ $installmentId }}">
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea id="notes" name="notes" class="form-control" rows="2"
                                placeholder="أي ملاحظات إضافية">{{ old('notes', $payment->notes) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="attachments" class="form-label">المرفقات</label>
                        <input id="attachments" type="file" name="attachments" class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">يمكنك رفع ملف PDF أو صورة (الحد الأقصى 2 ميجابايت)</small>
                        @if($payment->attachments)
                            <div class="mt-2">
                                <small class="text-info">
                                    <i class="fa fa-paperclip"></i>
                                    يوجد مرفق حالي - سيتم استبداله إذا رفعت ملف جديد
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // معالجة إرسال نموذج التعديل مع SweetAlert2
    document.getElementById('paymentEditForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // الحصول على قيم النموذج
        const amount = document.getElementById('amount').value;
        const paymentDate = document.getElementById('payment_date').value;
        const paymentType = document.getElementById('payment_type').value;
        const statusPayment = document.getElementById('status_payment').value;
        const paymentData = document.getElementById('payment_data').value;
        const notes = document.getElementById('notes').value;

        // التحقق من الحقول المطلوبة
        if (!amount || !paymentDate || !paymentType || !statusPayment) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ في البيانات',
                text: 'يرجى ملء جميع الحقول المطلوبة',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        // التحقق من صحة المبلغ
        if (parseFloat(amount) <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ في المبلغ',
                text: 'يجب أن يكون المبلغ أكبر من صفر',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        // الحصول على نصوص الخيارات المحددة
        const paymentTypeText = getPaymentTypeText(paymentType);
        const statusText = getStatusText(statusPayment);
        const employeeText = getEmployeeText();

        // عرض رسالة التأكيد للتحديث
        Swal.fire({
            title: 'تأكيد تعديل عملية الدفع',
            html: `
                <div class="confirmation-details" style="text-align: right; direction: rtl;">
                    <div class="alert alert-info mb-3">
                        <i class="fa fa-info-circle"></i>
                        سيتم تحديث عملية الدفع بالبيانات التالية:
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>المبلغ:</strong></div>
                        <div class="col-6">${parseFloat(amount).toFixed(2)} ر.س</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>التاريخ:</strong></div>
                        <div class="col-6">${paymentDate}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>وسيلة الدفع:</strong></div>
                        <div class="col-6">${paymentTypeText}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>الحالة:</strong></div>
                        <div class="col-6">${statusText}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>الموظف:</strong></div>
                        <div class="col-6">${employeeText}</div>
                    </div>
                    ${paymentData ? `
                    <div class="row mb-2">
                        <div class="col-6"><strong>بيانات الدفع:</strong></div>
                        <div class="col-6">${paymentData}</div>
                    </div>` : ''}
                    ${notes ? `
                    <div class="row mb-2">
                        <div class="col-6"><strong>الملاحظات:</strong></div>
                        <div class="col-6">${notes}</div>
                    </div>` : ''}
                    <hr style="margin: 15px 0;">
                    <p style="color: #666; font-size: 14px;">هل أنت متأكد من تحديث عملية الدفع؟</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fa fa-save"></i> تأكيد التحديث',
            cancelButtonText: '<i class="fa fa-times"></i> إلغاء',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-danger',
                htmlContainer: 'text-right'
            },
            width: '550px'
        }).then((result) => {
            if (result.isConfirmed) {
                // عرض مؤشر التحميل
                Swal.fire({
                    title: 'جاري التحديث...',
                    text: 'يتم تحديث عملية الدفع، يرجى الانتظار',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // إرسال النموذج فعلياً
                setTimeout(() => {
                    this.submit();
                }, 1000);
            }
        });
    });

    // معالجة زر الإلغاء
    document.getElementById('cancelBtn').addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'تأكيد الإلغاء',
            text: 'هل أنت متأكد من إلغاء التعديل؟ سيتم فقدان جميع التغييرات.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fa fa-check"></i> نعم، إلغاء التعديل',
            cancelButtonText: '<i class="fa fa-times"></i> العودة للتعديل',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // إعادة تحميل الصفحة للعودة للبيانات الأصلية
                window.location.reload();
            }
        });
    });

    // دالة للحصول على نص وسيلة الدفع
    function getPaymentTypeText(value) {
        const select = document.getElementById('payment_type');
        const option = select.querySelector(`option[value="${value}"]`);
        return option ? option.textContent : 'غير محدد';
    }

    // دالة للحصول على نص حالة الدفع
    function getStatusText(value) {
        const statuses = {
            '1': 'مكتمل',
            '2': 'غير مكتمل',
            '3': 'مسودة',
            '4': 'تحت المراجعة',
            '5': 'فاشلة'
        };
        return statuses[value] || 'غير محدد';
    }

    // دالة للحصول على اسم الموظف المحدد
    function getEmployeeText() {
        const select = document.getElementById('employee_id');
        const selectedOption = select.options[select.selectedIndex];
        return selectedOption ? selectedOption.textContent : 'غير محدد';
    }

    // معالجة رسائل النجاح من الخادم
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'تم التحديث بنجاح!',
            text: '{{ session('success') }}',
            confirmButtonText: 'موافق',
            confirmButtonColor: '#28a745',
            timer: 5000,
            timerProgressBar: true
        });
    @endif

    // معالجة رسائل الخطأ من الخادم
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'حدث خطأ!',
            text: '{{ session('error') }}',
            confirmButtonText: 'موافق',
            confirmButtonColor: '#dc3545'
        });
    @endif

    // تحسين تجربة المستخدم - إضافة تأثيرات بصرية
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('focus', function() {
            this.style.borderColor = '#007bff';
            this.style.boxShadow = '0 0 0 0.2rem rgba(0, 123, 255, 0.25)';
        });

        element.addEventListener('blur', function() {
            if (this.value && this.checkValidity()) {
                this.style.borderColor = '#28a745';
                this.style.boxShadow = '0 0 0 0.2rem rgba(40, 167, 69, 0.25)';
            } else if (this.value && !this.checkValidity()) {
                this.style.borderColor = '#dc3545';
                this.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
            } else {
                this.style.borderColor = '#ced4da';
                this.style.boxShadow = 'none';
            }
        });
    });

    // إضافة مؤشرات لتمييز الحقول المعدلة
    const originalValues = {};

    // حفظ القيم الأصلية
    document.querySelectorAll('input, select, textarea').forEach(element => {
        if (element.name && element.type !== 'hidden' && element.type !== 'file') {
            originalValues[element.name] = element.value;
        }
    });

    // مراقبة التغييرات
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('input', function() {
            if (this.name && originalValues.hasOwnProperty(this.name)) {
                if (this.value !== originalValues[this.name]) {
                    this.classList.add('field-modified');
                } else {
                    this.classList.remove('field-modified');
                }
            }
        });
    });
</script>

<style>
    .confirmation-details {
        font-size: 14px;
    }

    .confirmation-details .row {
        margin-bottom: 8px;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }

    .confirmation-details .row:last-child {
        border-bottom: none;
    }

    .swal2-html-container {
        text-align: right !important;
        direction: rtl !important;
    }

    .form-control:focus {
        transition: all 0.3s ease;
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .field-modified {
        border-left: 4px solid #ffc107 !important;
        background-color: rgba(255, 193, 7, 0.1) !important;
    }

    .field-modified:focus {
        border-left: 4px solid #ffc107 !important;
        border-color: #007bff !important;
    }

    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
        border-radius: 6px;
        padding: 10px;
        font-size: 14px;
    }
</style>
@endsection