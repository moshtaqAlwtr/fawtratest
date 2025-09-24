@if ($payments->count() > 0)
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <h6 class="mb-0">عرض {{ $payments->firstItem() }} إلى {{ $payments->lastItem() }} من {{ $payments->total() }} نتيجة</h6>
        </div>
        <div class="d-flex align-items-center gap-2">
            <select class="form-select form-select-sm" id="perPageSelect" style="width: auto;">
                <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span class="text-muted">عناصر لكل صفحة</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-light">
                <tr>
                    <th width="5%">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                    <th>رقم الدفع</th>
                    <th>الفاتورة</th>
                    <th>المورد</th>
                    <th>المبلغ</th>
                    <th>تاريخ الدفع</th>
                    <th>الحالة</th>
                    <th style="width: 10%">خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr class="payment-row" data-payment-id="{{ $payment->id }}">
                        <td>
                            <input type="checkbox" class="form-check-input order-checkbox" value="{{ $payment->id }}">
                        </td>
                        <td style="white-space: normal; word-wrap: break-word; min-width: 200px;">
                            <div class="d-flex flex-column">
                                <strong class="text-primary">#{{ $payment->payment_number ?? $payment->id }}</strong>
                                <div class="text-muted small">ID: {{ $payment->id }}</div>
                                @if($payment->reference_number)
                                    <div class="text-muted small">المرجع: {{ $payment->reference_number }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if ($payment->purchase_invoice)
                                <div class="d-flex flex-column">
                                    <strong class="text-info">{{ $payment->purchase_invoice->code ?? '--' }}</strong>
                                    <small class="text-muted">
                                        <i class="fa fa-calendar text-muted me-1"></i>
                                        {{ $payment->purchase_invoice->invoice_date ? \Carbon\Carbon::parse($payment->purchase_invoice->invoice_date)->format('Y-m-d') : '--' }}
                                    </small>
                                    @if($payment->purchase_invoice->reference_number)
                                        <small class="text-muted">مرجع: {{ $payment->purchase_invoice->reference_number }}</small>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                        <td>
                            @if ($payment->purchase_invoice && $payment->purchase_invoice->supplier)
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2"
                                        style="background-color: #28a745; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        <span class="avatar-content">{{ substr($payment->purchase_invoice->supplier->trade_name ?? 'M', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <strong>{{ $payment->purchase_invoice->supplier->trade_name ?? 'غير محدد' }}</strong>
                                        @if ($payment->purchase_invoice->supplier->phone)
                                            <div class="text-muted small">
                                                <i class="fa fa-phone me-1"></i>{{ $payment->purchase_invoice->supplier->phone }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif($payment->supplier)
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2"
                                        style="background-color: #28a745; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        <span class="avatar-content">{{ substr($payment->supplier->trade_name ?? 'M', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <strong>{{ $payment->supplier->trade_name ?? 'غير محدد' }}</strong>
                                        @if ($payment->supplier->phone)
                                            <div class="text-muted small">
                                                <i class="fa fa-phone me-1"></i>{{ $payment->supplier->phone }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                @php
                                    $currencySymbol = '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">';
                                @endphp
                                <h6 class="mb-0 font-weight-bold text-success">
                                    {{ number_format($payment->amount, 2) }} {!! $currencySymbol !!}
                                </h6>
                                <small class="text-muted">
                                    <i class="fa fa-credit-card me-1"></i>
                                    @if ($payment->Payment_method == 1)
                                        نقدي
                                    @elseif($payment->Payment_method == 2)
                                        شيك
                                    @elseif($payment->Payment_method == 3)
                                        تحويل بنكي
                                    @else
                                        غير محدد
                                    @endif
                                </small>
                                @if($payment->employee)
                                    <small class="text-muted mt-1">
                                        <i class="fa fa-user me-1"></i>{{ $payment->employee->name }}
                                    </small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-dark">
                                    <i class="fa fa-calendar text-muted me-1"></i>
                                    {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') : \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d') }}
                                </span>
                                <small class="text-muted">
                                    <i class="fa fa-clock me-1"></i>
                                    {{ $payment->created_at->format('H:i') }}
                                </small>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusClass = '';
                                $statusText = '';
                                $statusIcon = '';

                                switch($payment->payment_status) {
                                    case 1:
                                        $statusClass = 'badge-success';
                                        $statusText = 'مكتمل';
                                        $statusIcon = 'fa-check-circle';
                                        break;
                                    case 2:
                                        $statusClass = 'badge-warning';
                                        $statusText = 'غير مكتمل';
                                        $statusIcon = 'fa-clock';
                                        break;
                                    case 3:
                                        $statusClass = 'badge-secondary';
                                        $statusText = 'مسودة';
                                        $statusIcon = 'fa-file-alt';
                                        break;
                                    case 4:
                                        $statusClass = 'badge-info';
                                        $statusText = 'تحت المراجعة';
                                        $statusIcon = 'fa-eye';
                                        break;
                                    case 5:
                                        $statusClass = 'badge-danger';
                                        $statusText = 'فاشلة';
                                        $statusIcon = 'fa-times-circle';
                                        break;
                                    default:
                                        $statusClass = 'badge-light';
                                        $statusText = 'غير معروف';
                                        $statusIcon = 'fa-question';
                                }
                            @endphp
                            <span class="badge {{ $statusClass }} rounded-pill">
                                <i class="fas {{ $statusIcon }} me-1"></i>
                                {{ $statusText }}
                            </span>
                            @if ($payment->payment_status == 1)
                                <small class="text-success mt-1 d-block">
                                    <i class="fas fa-check-circle"></i> مؤكد
                                </small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item text-success delete-payment"
                                            href="{{ route('PaymentSupplier.showSupplierPayment', $payment->id) }}"
                                            data-id="">
                                            <i class="fas fa-eye me-2"></i>عرض العملية
                                        </a>
                                        <a class="dropdown-item text-success delete-payment"
                                            href="{{ route('PaymentSupplier.editSupplierPayment', $payment->id) }}"
                                            data-id="">
                                            <i class="fas fa-edit me-2"></i>تعديل العملية
                                        </a>
                                        <a class="dropdown-item text-danger delete-payment"
                                            href="{{ route('PaymentSupplier.destroySupplierPayment', $payment->id) }}"
                                            data-id="">
                                            <i class="fas fa-trash me-2"></i>حذف العملية
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="" target="_blank">
                                            <i class="fas fa-file-pdf me-2 text-warning"></i>إيصال (A4)
                                        </a>
                                        <a class="dropdown-item" href="" target="_blank">
                                            <i class="fas fa-file-pdf me-2 text-info"></i>إيصال (حراري)
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- معلومات النتائج فقط - الترقيم يتم عرضه من pagination.blade.php -->
    @if($payments->hasPages())
        <div class="d-flex justify-content-center align-items-center mt-3">
            <div class="text-muted">
                عرض {{ $payments->firstItem() }} إلى {{ $payments->lastItem() }} من {{ $payments->total() }} نتائج
            </div>
        </div>
    @endif

@else
    @php
        $hasSearchParams = request()->filled([
            'invoice_number', 'payment_number', 'supplier', 'added_by', 'payment_status',
            'customization', 'from_date', 'to_date', 'identifier', 'transfer_id',
            'total_greater_than', 'total_less_than', 'custom_field', 'invoice_origin',
            'filter'
        ]) || request()->has('page');
    @endphp

    @if($hasSearchParams)
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-search fa-3x text-muted"></i>
            </div>
            <h5 class="text-muted mb-2">لا توجد نتائج مطابقة</h5>
            <p class="text-muted mb-3">لا توجد مدفوعات موردين تطابق معايير البحث المحددة</p>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" id="clearSearchBtn" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-times me-1"></i>إعادة تعيين البحث
                </button>
                <a href="{{ route('PaymentSupplier.createPurchase', 1) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>إضافة دفعة جديدة
                </a>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-receipt fa-3x text-primary opacity-50"></i>
            </div>
            <h5 class="text-muted mb-2">ابدأ البحث</h5>
            <p class="text-muted mb-3">استخدم نموذج البحث أعلاه للعثور على مدفوعات الموردين</p>
            <a href="{{ route('PaymentSupplier.createPurchase', 1) }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>إضافة دفعة جديدة
            </a>
        </div>
    @endif
@endif

<script>
    // تحديث عدد العناصر المحددة في الوقت الفعلي
    $(document).ready(function() {
        function updateSelectionCount() {
            const selectedCount = $('.order-checkbox:checked').length;
            const totalCount = $('.order-checkbox').length;

            if (selectedCount > 0) {
                $('#bulkActionsBtn').removeClass('d-none').text(`إجراءات مجمعة (${selectedCount})`);
            } else {
                $('#bulkActionsBtn').addClass('d-none');
            }

            $('#selectAll').prop('indeterminate', selectedCount > 0 && selectedCount < totalCount);
            $('#selectAll').prop('checked', selectedCount === totalCount && totalCount > 0);
        }

        // تفعيل متابعة التحديد
        updateSelectionCount();
    });
</script>