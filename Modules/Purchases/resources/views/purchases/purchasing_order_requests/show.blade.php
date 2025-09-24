@extends('master')

@section('title')
    عرض أمر الشراء
@stop

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/accept.css') }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض أمر الشراء</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('OrdersRequests.index') }}">أوامر الشراء</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <!-- كارد الحالة الرئيسي -->
    <div class="card status-card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <!-- الجانب الأيمن - حالة الأمر والرقم -->
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h5 class="mb-0 fw-bold">أمر شراء #{{ $purchaseOrdersRequests->code }}</h5>

                    <div class="d-flex align-items-center gap-2">
                        @if ($purchaseOrdersRequests->status == "Under Review")
                            <i class="fas fa-clock text-warning" style="font-size: 12px;"></i>
                            <span class="status-badge badge-under-review">تحت المراجعة</span>
                        @elseif ($purchaseOrdersRequests->status == "approval")
                            <i class="fas fa-check-circle text-success" style="font-size: 12px;"></i>
                            <span class="status-badge badge-approved">معتمد</span>
                        @elseif ($purchaseOrdersRequests->status == "disagree")
                            <i class="fas fa-times-circle text-danger" style="font-size: 12px;"></i>
                            <span class="status-badge badge-rejected">مرفوض</span>
                        @endif
                    </div>
                </div>

                <!-- الجانب الأيسر - أزرار متغيرة حسب الحالة -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if ($purchaseOrdersRequests->status == "Under Review")
                        <!-- أزرار الموافقة والرفض للأوامر تحت المراجعة -->
                        <button type="button" class="status-btn btn-approve" onclick="approveOrder()">
                            <i class="fas fa-check"></i>
                            <span>موافقة</span>
                        </button>

                        <button type="button" class="status-btn btn-reject" onclick="rejectOrder()">
                            <i class="fas fa-times"></i>
                            <span>رفض</span>
                        </button>

                    @elseif ($purchaseOrdersRequests->status == "approval")
                        <!-- أزرار للأوامر المعتمدة -->
                        <button type="button" class="status-btn btn-convert pulse-effect" onclick="convertToInvoice()">
                            <i class="fas fa-file-invoice"></i>
                            <span>تحويل إلى فاتورة</span>
                        </button>

                        <button type="button" class="status-btn btn-cancel-approval" onclick="cancelApproval()">
                            <i class="fas fa-undo"></i>
                            <span>إلغاء الموافقة</span>
                        </button>

                    @elseif ($purchaseOrdersRequests->status == "disagree")
                        <!-- زر للأوامر المرفوضة -->
                        <button type="button" class="status-btn btn-undo-rejection" onclick="undoRejection()">
                            <i class="fas fa-undo-alt"></i>
                            <span>التراجع عن الرفض</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- كارد الأدوات -->
    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2">
            <!-- زر تعديل -->
            <a href="{{ route('OrdersRequests.edit', $purchaseOrdersRequests->id) }}" class="btn btn-outline-primary btn-sm">
                تعديل <i class="fa fa-edit ms-1"></i>
            </a>
            <div class="vr"></div>

            <!-- زر نسخ -->
            <button type="button" class="btn btn-outline-info btn-sm" onclick="copyOrder()">
                نسخ <i class="fa fa-copy ms-1"></i>
            </button>
            <div class="vr"></div>

            <!-- زر حذف -->
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteOrder()">
                حذف <i class="fa fa-trash-alt ms-1"></i>
            </button>
            <div class="vr"></div>

            <!-- زر طباعة -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm" type="button" id="printDropdown"
                    data-bs-toggle="dropdown">
                    طباعة <i class="fa fa-print ms-1"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fa fa-file-pdf me-2 text-danger"></i>PDF طباعة</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa fa-file-excel me-2 text-success"></i>Excel تصدير</a></li>
                </ul>
            </div>

            <!-- زر المطبوعات -->
            <div class="dropdown">
                <button class="btn btn-outline-info btn-sm d-inline-flex align-items-center justify-content-center px-3"
                    style="min-width: 90px;" type="button" data-bs-toggle="dropdown">
                    المطبوعات <i class="fa fa-folder-open ms-1"></i>
                </button>
                <ul class="dropdown-menu py-1">
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i class="fa fa-file-alt me-2 text-primary"></i> Layout 1</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i class="fa fa-file-alt me-2 text-primary"></i> Layout 2</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i class="fa fa-file-alt me-2 text-primary"></i> Layout 3</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i class="fa fa-file-pdf me-2 text-danger"></i>نموذج 1</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i class="fa fa-file-pdf me-2 text-danger"></i>نموذج 2</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i class="fa fa-file-pdf me-2 text-danger"></i>نموذج 3</a></li>
                </ul>
            </div>

            <!-- زر إرسال إلى بريد المورد -->
            <button type="button" class="btn btn-outline-warning btn-sm" onclick="sendToSupplierEmail()">
                ارسل الى بريد المورد <i class="fa fa-envelope ms-1"></i>
            </button>

            <!-- زر إضافة ملاحظة أو مرفق -->
            <div class="dropdown">
                <button class="btn btn-outline-success btn-sm" type="button" data-bs-toggle="dropdown">
                    الملاحظات <i class="fa fa-paperclip ms-1"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="addNoteOrAttachment()">
                        <i class="fa fa-plus me-2 text-success"></i>إضافة ملاحظة جديدة</a>
                    </li>
                    <li><a class="dropdown-item" href="#" onclick="viewAllNotes()">
                        <i class="fa fa-list me-2 text-info"></i>عرض جميع الملاحظات</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">التفاصيل</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#items" role="tab">المنتجات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">سجل النشاطات</a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                <div class="tab-pane active" id="details" role="tabpanel">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="tab-pane fade show active" style="background: lightslategray; min-height: 100vh; padding: 20px;">
                                <div class="card shadow preview-card" style="max-width: 600px; margin: 20px auto;">
                                    <div class="card-body bg-white p-4" style="min-height: 400px; overflow: auto;">
                                        <div style="transform: scale(0.8); transform-origin: top center;">
                                            @include('purchases::purchases.purchasing_order_requests.pdf', ['purchaseOrdersRequests' => $purchaseOrdersRequests])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب المنتجات -->
                <div class="tab-pane" id="items" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>سعر الوحدة</th>
                                    <th>الخصم</th>
                                    <th>الضريبة</th>
                                    @if($purchaseOrdersRequests->shipping_cost > 0)
                                        <th>الشحن</th>
                                    @endif
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrdersRequests->items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product->name ?? $item->item }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ number_format($item->discount, 2) }}</td>
                                        <td>{{ number_format($item->tax, 2) }}</td>
                                        @if($purchaseOrdersRequests->shipping_cost > 0)
                                            <td>{{ number_format($item->shipping, 2) }}</td>
                                        @endif
                                        <td>{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="{{ $purchaseOrdersRequests->shipping_cost > 0 ? '7' : '6' }}" class="text-start fw-bold">الإجمالي</td>
                                    <td class="fw-bold">{{ number_format($purchaseOrdersRequests->grand_total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- تبويب سجل النشاطات -->
                <div class="tab-pane" id="activity" role="tabpanel">
                    <div class="row mt-4">
                        <div class="col-12">
                            @if ($logs && count($logs) > 0)
                                @php
                                    $previousDate = null;
                                @endphp

                                @foreach ($logs as $date => $dayLogs)
                                    @php
                                        $currentDate = \Carbon\Carbon::parse($date);
                                        $diffInDays = $previousDate ? $previousDate->diffInDays($currentDate) : 0;
                                    @endphp

                                    @if ($diffInDays > 7)
                                        <div class="timeline-date">
                                            <h4>{{ $currentDate->format('Y-m-d') }}</h4>
                                        </div>
                                    @endif

                                    <div class="timeline-day">{{ $currentDate->translatedFormat('l') }}</div>

                                    <ul class="timeline">
                                        @foreach ($dayLogs as $log)
                                            @if ($log)
                                                <li class="timeline-item">
                                                    <div class="timeline-content">
                                                        <div class="time">
                                                            <i class="far fa-clock"></i>
                                                            {{ $log->created_at->format('H:i:s') }}
                                                        </div>
                                                        <div>
                                                            <strong>{{ $log->user->name ?? 'مستخدم غير معروف' }}</strong>
                                                            {!! Str::markdown($log->description ?? 'لا يوجد وصف') !!}
                                                            <div class="text-muted">
                                                                {{ $log->user->branch->name ?? 'فرع غير معروف' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>

                                    @php
                                        $previousDate = $currentDate;
                                    @endphp
                                @endforeach
                            @else
                                <div class="alert alert-info text-center" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <p class="mb-0">لا توجد سجلات نشاط حتى الآن!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms للعمليات -->
    <form id="approveForm" action="{{ route('OrdersRequests.approve', $purchaseOrdersRequests->id) }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" id="approveNote" name="note">
    </form>

    <form id="rejectForm" action="{{ route('OrdersRequests.reject', $purchaseOrdersRequests->id) }}" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="cancelApprovalForm" action="{{ route('OrdersRequests.cancelApproval', $purchaseOrdersRequests->id) }}" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="undoRejectionForm" action="{{ route('OrdersRequests.undoRejection', $purchaseOrdersRequests->id) }}" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="convertToInvoiceForm" action="{{ route('OrdersRequests.convertToInvoice', $purchaseOrdersRequests->id) }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" id="convertNote" name="note">
    </form>

    <form id="deleteForm" action="{{ route('OrdersRequests.destroy', $purchaseOrdersRequests->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('scripts')
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // دالة الموافقة على أمر الشراء
        function approveOrder() {
            Swal.fire({
                title: 'تأكيد الموافقة',
                text: 'هل أنت متأكد من الموافقة على أمر الشراء رقم {{ $purchaseOrdersRequests->code }}؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'تأكيد الموافقة',
                cancelButtonText: 'إلغاء',
                input: 'textarea',
                inputPlaceholder: 'ملاحظات (اختياري)',
                inputAttributes: {
                    'aria-label': 'اكتب ملاحظاتك هنا'
                },
                showLoaderOnConfirm: true,
                preConfirm: (note) => {
                    document.getElementById('approveNote').value = note || '';
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approveForm').submit();
                }
            });
        }

        // دالة رفض أمر الشراء
        function rejectOrder() {
            Swal.fire({
                title: 'تأكيد الرفض',
                text: 'هل أنت متأكد من رفض أمر الشراء رقم {{ $purchaseOrdersRequests->code }}؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'تأكيد الرفض',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('rejectForm').submit();
                }
            });
        }

        // دالة تحويل إلى فاتورة
// دالة تحويل إلى فاتورة محدثة
function convertToInvoice() {
    const grandTotal = {{ $purchaseOrdersRequests->grand_total }};

    Swal.fire({
        title: 'تحويل إلى فاتورة',
        html: `
            <div class="text-start">
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-file-invoice me-2"></i>
                            تحويل أمر الشراء رقم {{ $purchaseOrdersRequests->code }} إلى فاتورة
                        </h6>
                        <div class="alert alert-info">
                            <strong>إجمالي الفاتورة:</strong> ${grandTotal.toLocaleString()} ريال
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">حالة الدفع:</label>
                        <div class="mt-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_status"
                                       id="payment_deferred" value="deferred" checked>
                                <label class="form-check-label" for="payment_deferred">
                                    <i class="fas fa-calendar-alt text-warning me-1"></i>
                                    <strong>آجلة</strong> - سيتم إضافتها للذمم المدينة
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_status"
                                       id="payment_paid" value="paid">
                                <label class="form-check-label" for="payment_paid">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    <strong>مدفوعة</strong> - ستخصم من الخزينة
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3" id="advance_payment_section">
                    <div class="col-12">
                        <label for="advance_amount" class="form-label fw-bold">
                            <i class="fas fa-money-bill-wave text-success me-1"></i>
                            المبلغ المدفوع مقدماً (اختياري):
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="advance_amount"
                                   placeholder="0" min="0" max="${grandTotal}" step="0.01">
                            <span class="input-group-text">ريال</span>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            اتركه فارغاً أو 0 إذا لم يتم دفع أي مبلغ مقدم
                        </small>
                    </div>
                </div>

                <div class="row mb-3" id="due_amount_display" style="display: none;">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <strong>المبلغ المستحق:</strong>
                            <span id="due_amount_text">${grandTotal.toLocaleString()}</span> ريال
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="convert_note" class="form-label">ملاحظات التحويل (اختياري):</label>
                        <textarea id="convert_note" class="form-control" rows="3"
                                  placeholder="ملاحظات إضافية حول عملية التحويل..."></textarea>
                    </div>
                </div>
            </div>
        `,
        width: '600px',
        showCancelButton: true,
        confirmButtonColor: '#4776E6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-sync-alt me-1"></i> تأكيد التحويل',
        cancelButtonText: '<i class="fas fa-times me-1"></i> إلغاء',
        showLoaderOnConfirm: true,
        didOpen: () => {
            // إضافة مستمعات الأحداث
            const paidRadio = document.getElementById('payment_paid');
            const deferredRadio = document.getElementById('payment_deferred');
            const advanceSection = document.getElementById('advance_payment_section');
            const dueAmountDisplay = document.getElementById('due_amount_display');
            const advanceAmountInput = document.getElementById('advance_amount');
            const dueAmountText = document.getElementById('due_amount_text');

            // دالة لحساب وعرض المبلغ المستحق
            function updateDueAmount() {
                const advanceAmount = parseFloat(advanceAmountInput.value) || 0;
                const dueAmount = grandTotal - advanceAmount;

                if (deferredRadio.checked && advanceAmount > 0) {
                    dueAmountDisplay.style.display = 'block';
                    dueAmountText.textContent = dueAmount.toLocaleString();
                } else {
                    dueAmountDisplay.style.display = 'none';
                }
            }

            // مستمع تغيير حالة الدفع
            function handlePaymentStatusChange() {
                if (paidRadio.checked) {
                    advanceSection.style.display = 'none';
                    dueAmountDisplay.style.display = 'none';
                    advanceAmountInput.value = '';
                } else {
                    advanceSection.style.display = 'block';
                    updateDueAmount();
                }
            }

            paidRadio.addEventListener('change', handlePaymentStatusChange);
            deferredRadio.addEventListener('change', handlePaymentStatusChange);
            advanceAmountInput.addEventListener('input', updateDueAmount);

            // تعيين الحالة الأولية
            handlePaymentStatusChange();
        },
        preConfirm: () => {
            const paymentStatus = document.querySelector('input[name="payment_status"]:checked').value;
            const advanceAmount = parseFloat(document.getElementById('advance_amount').value) || 0;
            const note = document.getElementById('convert_note').value.trim();

            // التحقق من صحة البيانات
            if (paymentStatus === 'deferred' && advanceAmount > grandTotal) {
                Swal.showValidationMessage('المبلغ المدفوع مقدماً لا يمكن أن يتجاوز إجمالي الفاتورة');
                return false;
            }

            if (paymentStatus === 'deferred' && advanceAmount < 0) {
                Swal.showValidationMessage('المبلغ المدفوع مقدماً لا يمكن أن يكون سالباً');
                return false;
            }

            return { paymentStatus, advanceAmount, note };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // إنشاء form وإرسال البيانات
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("OrdersRequests.convertToInvoice", $purchaseOrdersRequests->id) }}';

            // إضافة CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfInput);

            // إضافة حالة الدفع
            const paymentStatusInput = document.createElement('input');
            paymentStatusInput.type = 'hidden';
            paymentStatusInput.name = 'payment_status';
            paymentStatusInput.value = result.value.paymentStatus;
            form.appendChild(paymentStatusInput);

            // إضافة المبلغ المقدم
            const advanceAmountInput = document.createElement('input');
            advanceAmountInput.type = 'hidden';
            advanceAmountInput.name = 'advance_amount';
            advanceAmountInput.value = result.value.advanceAmount;
            form.appendChild(advanceAmountInput);

            // إضافة الملاحظة
            const noteInput = document.createElement('input');
            noteInput.type = 'hidden';
            noteInput.name = 'note';
            noteInput.value = result.value.note;
            form.appendChild(noteInput);

            // إضافة الform للصفحة وإرساله
            document.body.appendChild(form);
            form.submit();
        }
    });
}

        // دالة إلغاء الموافقة
        function cancelApproval() {
            Swal.fire({
                title: 'إلغاء الموافقة',
                text: 'هل أنت متأكد من إلغاء الموافقة على أمر الشراء؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'تأكيد الإلغاء',
                cancelButtonText: 'رجوع'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('cancelApprovalForm').submit();
                }
            });
        }

        // دالة التراجع عن الرفض
        function undoRejection() {
            Swal.fire({
                title: 'التراجع عن الرفض',
                text: 'هل تريد إعادة أمر الشراء إلى حالة "تحت المراجعة"؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'نعم، تراجع',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('undoRejectionForm').submit();
                }
            });
        }

        // دالة حذف أمر الشراء
        function deleteOrder() {
            Swal.fire({
                title: 'حذف أمر الشراء',
                text: 'هل أنت متأكد من حذف أمر الشراء رقم "{{ $purchaseOrdersRequests->code }}"؟ هذا الإجراء لا يمكن التراجع عنه!',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        }

        // دالة نسخ أمر الشراء
        function copyOrder() {
            Swal.fire({
                title: 'نسخ أمر الشراء',
                text: 'سيتم إنشاء نسخة جديدة من أمر الشراء',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'تأكيد النسخ',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // توجيه لصفحة إنشاء أمر شراء جديد بالبيانات المنسوخة
                    window.location.href = "{{ route('OrdersRequests.create') }}?copy_from={{ $purchaseOrdersRequests->id }}";
                }
            });
        }

        // دالة إرسال إلى بريد المورد
        function sendToSupplierEmail() {
            Swal.fire({
                title: 'إرسال إلى بريد المورد',
                text: 'سيتم إرسال أمر الشراء إلى بريد المورد الإلكتروني',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'إرسال',
                cancelButtonText: 'إلغاء',
                input: 'email',
                inputPlaceholder: 'أدخل البريد الإلكتروني للمورد',
                inputAttributes: {
                    'aria-label': 'البريد الإلكتروني'
                },
                showLoaderOnConfirm: true,
                preConfirm: (email) => {
                    if (!email) {
                        Swal.showValidationMessage('يرجى إدخال البريد الإلكتروني');
                        return false;
                    }
                    // هنا يمكن إضافة منطق الإرسال
                    return email;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('تم الإرسال!', 'تم إرسال أمر الشراء بنجاح', 'success');
                }
            });
        }

        // دالة إضافة ملاحظة أو مرفق
        function addNoteOrAttachment() {
            // الحصول على التاريخ والوقت الحالي
            const now = new Date();
            const currentDate = now.toISOString().split('T')[0];
            const currentTime = now.toTimeString().split(' ')[0].substring(0, 5);

            Swal.fire({
                title: 'إضافة ملاحظة أو مرفق',
                html: `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="process" class="form-label text-start d-block">نوع الإجراء:</label>
                            <select id="process" class="form-control">
                                <option value="">اختر نوع الإجراء</option>
                                <option value="مراجعة أمر الشراء">مراجعة أمر الشراء</option>
                                <option value="تعديل الكميات">تعديل الكميات</option>
                                <option value="التواصل مع المورد">التواصل مع المورد</option>
                                <option value="مناقشة شروط التسليم">مناقشة شروط التسليم</option>
                                <option value="متابعة التنفيذ">متابعة التنفيذ</option>
                                <option value="تحديث البيانات">تحديث البيانات</option>
                                <option value="ملاحظة عامة">ملاحظة عامة</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="noteDate" class="form-label text-start d-block">التاريخ:</label>
                            <input type="date" id="noteDate" class="form-control" value="${currentDate}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="noteTime" class="form-label text-start d-block">الوقت:</label>
                            <input type="time" id="noteTime" class="form-control" value="${currentTime}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="attachment" class="form-label text-start d-block">مرفق (اختياري):</label>
                            <input type="file" id="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label text-start d-block">تفاصيل الملاحظة:</label>
                        <textarea id="note" class="form-control" rows="4" placeholder="اكتب تفاصيل الملاحظة هنا..."></textarea>
                    </div>
                `,
                width: '600px',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-save me-1"></i> حفظ الملاحظة',
                cancelButtonText: '<i class="fas fa-times me-1"></i> إلغاء',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const process = document.getElementById('process').value;
                    const note = document.getElementById('note').value.trim();
                    const noteDate = document.getElementById('noteDate').value;
                    const noteTime = document.getElementById('noteTime').value;
                    const attachment = document.getElementById('attachment').files[0];

                    // التحقق من صحة البيانات
                    if (!process) {
                        Swal.showValidationMessage('يرجى اختيار نوع الإجراء');
                        return false;
                    }

                    if (!note) {
                        Swal.showValidationMessage('يرجى إدخال تفاصيل الملاحظة');
                        return false;
                    }

                    if (!noteDate) {
                        Swal.showValidationMessage('يرجى تحديد التاريخ');
                        return false;
                    }

                    if (!noteTime) {
                        Swal.showValidationMessage('يرجى تحديد الوقت');
                        return false;
                    }

                    return { process, note, noteDate, noteTime, attachment };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    saveNoteToDatabase(result.value);
                }
            });
        }

        // دالة حفظ الملاحظة في قاعدة البيانات
        function saveNoteToDatabase(noteData) {
            const formData = new FormData();
            formData.append('description', noteData.note);
            formData.append('process', noteData.process);
            formData.append('date', noteData.noteDate);
            formData.append('time', noteData.noteTime);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            if (noteData.attachment) {
                formData.append('attachment', noteData.attachment);
            }

            fetch(`{{ route('OrdersRequests.addNote', $purchaseOrdersRequests->id) }}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'تم الحفظ بنجاح!',
                        text: 'تم إضافة الملاحظة بنجاح',
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        // إعادة تحميل الصفحة لإظهار الملاحظة الجديدة
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: data.message || 'حدث خطأ أثناء حفظ الملاحظة',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء الاتصال بالخادم',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            });
        }

        // دالة عرض جميع الملاحظات
        function viewAllNotes() {
            // عرض loader أثناء التحميل
            Swal.fire({
                title: 'جاري تحميل الملاحظات...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`{{ route('OrdersRequests.getNotes', $purchaseOrdersRequests->id) }}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('البيانات المُستلمة:', data); // للتشخيص

                if (data.success) {
                    let notesHtml = '';

                    if (data.notes && data.notes.length > 0) {
                        data.notes.forEach((note, index) => {
                            // التأكد من وجود البيانات المطلوبة
                            const processName = note.process || 'غير محدد';
                            const employeeName = note.employee_name || 'غير محدد';
                            const noteDate = note.date || 'غير محدد';
                            const noteTime = note.time || 'غير محدد';
                            const description = note.description || 'لا يوجد وصف';

                            notesHtml += `
                                <div class="card mb-3 border-start border-primary border-3" id="note-${note.id}">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge bg-primary me-2">${index + 1}</span>
                                                    <h6 class="mb-0 text-primary fw-bold">${processName}</h6>
                                                </div>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-user me-1 text-info"></i><strong>الموظف:</strong> ${employeeName}
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-calendar me-1 text-success"></i><strong>التاريخ:</strong> ${noteDate}
                                                    <i class="fas fa-clock me-2 ms-3 text-warning"></i><strong>الوقت:</strong> ${noteTime}
                                                </small>
                                            </div>
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteNote(${note.id})"
                                                    title="حذف الملاحظة">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <div class="mt-3 p-3 bg-light rounded">
                                            <p class="mb-0 text-dark">${description}</p>
                                        </div>

                                        ${note.has_attachment ? `
                                            <div class="mt-3 pt-2 border-top">
                                                <a href="${note.attachment_url}" target="_blank"
                                                   class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-paperclip me-1"></i>عرض المرفق
                                                </a>
                                            </div>
                                        ` : ''}

                                        ${note.created_at ? `
                                            <div class="mt-2 pt-2 border-top">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>تم الإنشاء: ${note.created_at}
                                                </small>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        notesHtml = `
                            <div class="alert alert-info text-center py-4">
                                <i class="fas fa-info-circle fs-1 text-info mb-3"></i>
                                <h5 class="mb-2">لا توجد ملاحظات حتى الآن</h5>
                                <p class="mb-0 text-muted">يمكنك إضافة ملاحظة جديدة من خلال زر "إضافة ملاحظة جديدة"</p>
                            </div>
                        `;
                    }

                    Swal.fire({
                        title: `<i class="fas fa-sticky-note me-2 text-primary"></i>جميع الملاحظات (${data.notes ? data.notes.length : 0})`,
                        html: `
                            <div class="text-start" style="max-height: 500px; overflow-y: auto; padding: 10px;">
                                ${notesHtml}
                            </div>
                            <div class="mt-3 pt-3 border-top text-center">
                                <button type="button" class="btn btn-success btn-sm me-2" onclick="Swal.close(); addNoteOrAttachment();">
                                    <i class="fas fa-plus me-1"></i>إضافة ملاحظة جديدة
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="location.reload();">
                                    <i class="fas fa-sync me-1"></i>تحديث الصفحة
                                </button>
                            </div>
                        `,
                        width: '800px',
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: '<i class="fas fa-times me-1"></i>إغلاق',
                        cancelButtonColor: '#6c757d',
                        customClass: {
                            popup: 'swal-rtl'
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: data.message || 'لم يتم العثور على ملاحظات',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('خطأ في جلب الملاحظات:', error);
                Swal.fire({
                    title: 'خطأ في الاتصال!',
                    text: 'حدث خطأ أثناء جلب الملاحظات. يرجى التحقق من الاتصال بالإنترنت والمحاولة مرة أخرى.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    footer: `<small class="text-muted">تفاصيل الخطأ: ${error.message}</small>`
                });
            });
        }

        // دالة حذف ملاحظة
        function deleteNote(noteId) {
            Swal.fire({
                title: 'تأكيد الحذف',
                text: 'هل أنت متأكد من حذف هذه الملاحظة؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ route('OrdersRequests.deleteNote', '') }}/${noteId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('تم الحذف!', 'تم حذف الملاحظة بنجاح', 'success');
                            viewAllNotes(); // إعادة تحميل قائمة الملاحظات
                        } else {
                            Swal.fire('خطأ!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('خطأ!', 'حدث خطأ أثناء حذف الملاحظة', 'error');
                    });
                }
            });
        }

        // عرض رسائل النجاح والخطأ
        @if (session('success'))
            Swal.fire({
                title: 'تم بنجاح!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#28a745'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: 'خطأ!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        @endif

        // تأثيرات إضافية للتفاعل
        document.addEventListener('DOMContentLoaded', function() {
            // تأثير hover للأزرار
            const buttons = document.querySelectorAll('.status-btn');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px) scale(1.02)';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // تأثير نبضة للأزرار المهمة
            const convertBtn = document.querySelector('.btn-convert');
            if (convertBtn) {
                convertBtn.classList.add('pulse-effect');
            }
        });
    </script>
@endsection
