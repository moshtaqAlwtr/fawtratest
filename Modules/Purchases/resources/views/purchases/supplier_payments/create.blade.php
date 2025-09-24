@extends('master')

@section('title')
    اضافة عملية الدفع
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اضافة عملية دفع </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="paymentForm" action="{{ route('PaymentSupplier.storePurchase') }}" method="POST" enctype="multipart/form-data">
        @csrf
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
                        <button type="submit" class="btn btn-outline-primary" id="submitBtn">
                            <i class="fa fa-save"></i> اضافة عملية الدفع
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
                            step="0.01" value="{{ $amount ?? old('amount') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="payment_date" class="form-label">تاريخ الدفع <span style="color: red">*</span></label>
                        <input type="date" id="payment_date" name="payment_date" class="form-control"
                               value="{{ old('payment_date', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="payment_type" class="form-label">وسيلة الدفع <span style="color: red">*</span></label>
                        <select name="payment_type" class="form-control" id="payment_type" required>
                            <option value="">اختر نوع الدفع</option>
                            @foreach ($payments as $payment)
                                <option value="{{$payment->id}}" {{ old('payment_type') == $payment->id ? 'selected' : '' }}>
                                    {{$payment->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="treasury_id" class="form-label">الخزينة المستخدمة</label>
                        <input type="text" class="form-control" placeholder="الخزينة المستخدمة"
                            value="{{$mainTreasuryAccount->name ?? "الخزينة الرئيسية"}}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status_payment" class="form-label">حالة الدفع <span style="color: red">*</span></label>
                        <select name="status_payment" class="form-control" id="status_payment" required>
                            <option value="">اختر حالة الدفع</option>
                            <option value="2" {{ old('status_payment') == '2' ? 'selected' : '' }}>غير مكتمل</option>
                            <option value="1" {{ old('status_payment') == '1' ? 'selected' : '' }}>مكتمل</option>
                            <option value="4" {{ old('status_payment') == '4' ? 'selected' : '' }}>تحت المراجعة</option>
                            <option value="5" {{ old('status_payment') == '5' ? 'selected' : '' }}>فاشلة</option>
                            <option value="3" {{ old('status_payment') == '3' ? 'selected' : '' }}>مسودة</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="employee" class="form-label">تم التحصيل بواسطة <span style="color: red">*</span></label>
                        <input type="hidden" name="employee_id" value="{{ auth()->user()->employee_id }}">
                        <input type="text" class="form-control"
                               value="{{ auth()->user()->employee->full_name ?? auth()->user()->name }}" readonly>
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
                            placeholder="مثل: رقم الشيك، رقم التحويل">{{ old('payment_data') }}</textarea>
                    </div>
                </div>

                <input type="hidden" name="invoice_id" value="{{ $invoiceId }}">
                <input type="hidden" name="installment_id" value="{{ $installmentId ?? '' }}">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea id="notes" name="notes" class="form-control" rows="2"
                                placeholder="أي ملاحظات إضافية">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="attachments" class="form-label">المرفقات</label>
                        <input id="attachments" type="file" name="attachments" class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">يمكنك رفع ملف PDF أو صورة (الحد الأقصى 2 ميجابايت)</small>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // معالجة إرسال النموذج مع SweetAlert2
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
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

        // عرض رسالة التأكيد
        Swal.fire({
            title: 'تأكيد عملية الدفع',
            html: `
                <div class="confirmation-details" style="text-align: right; direction: rtl;">
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
                    <p style="color: #666; font-size: 14px;">هل أنت متأكد من تسجيل عملية الدفع هذه؟</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fa fa-save"></i> تأكيد الحفظ',
            cancelButtonText: '<i class="fa fa-times"></i> إلغاء',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-danger',
                htmlContainer: 'text-right'
            },
            width: '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                // عرض مؤشر التحميل
                Swal.fire({
                    title: 'جاري المعالجة...',
                    text: 'يتم تسجيل عملية الدفع، يرجى الانتظار',
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
            text: 'هل أنت متأكد من إلغاء عملية الدفع؟ سيتم فقدان جميع البيانات المدخلة.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fa fa-check"></i> نعم، إلغاء',
            cancelButtonText: '<i class="fa fa-times"></i> العودة',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // إعادة تعيين النموذج
                document.getElementById('paymentForm').reset();
                document.getElementById('payment_date').value = new Date().toISOString().split('T')[0];

                Swal.fire({
                    icon: 'info',
                    title: 'تم الإلغاء',
                    text: 'تم إلغاء عملية الدفع وإعادة تعيين النموذج',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#28a745',
                    timer: 2000,
                    timerProgressBar: true
                });
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

    // معالجة رسائل النجاح من الخادم
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح!',
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

    // دالة للتحقق من جلب تفاصيل الفاتورة
    function getInvoiceDetails(invoiceId) {
        fetch(`/payments/invoice-details/${invoiceId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // تحديث النموذج بتفاصيل الفاتورة
                    document.getElementById('remaining_amount').textContent = data.data.remaining_amount;
                    document.getElementById('client_name').textContent = data.data.client_name;
                    document.getElementById('invoice_total').textContent = data.data.grand_total;
                    document.getElementById('total_paid').textContent = data.data.total_paid;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'خطأ في جلب تفاصيل الفاتورة',
                        confirmButtonText: 'موافق'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في الشبكة',
                    text: 'حدث خطأ في الاتصال، يرجى المحاولة مرة أخرى',
                    confirmButtonText: 'موافق'
                });
            });
    }

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
</style>
@endsection