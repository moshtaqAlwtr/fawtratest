@extends('master')

@section('title')
    عرض طلب شراء
@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
    <!-- SweetAlert2 CSS -->

    <!-- إضافة CSS مخصص في قسم head -->
@section('css')

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
      <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">

      <link rel="stylesheet" href="{{ asset('assets/css/accept.css') }}">



@endsection



@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">عرض طلب شراء</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('OrdersPurchases.index') }}">طلبات الشراء</a></li>
                        <li class="breadcrumb-item active">عرض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.alerts.error')
@include('layouts.alerts.success')

 <div class="card status-card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <!-- الجانب الأيمن - حالة الطلب والرقم -->
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h5 class="mb-0 fw-bold">طلب شراء {{ $purchaseOrder->id }} #{{ $purchaseOrder->code }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        @if ($purchaseOrder->status == "Under Review")
                            <i class="fas fa-clock text-warning" style="font-size: 12px;"></i>
                            <span class="status-badge badge-under-review">تحت المراجعة</span>
                        @elseif ($purchaseOrder->status == "approval")
                            <i class="fas fa-check-circle text-success" style="font-size: 12px;"></i>
                            <span class="status-badge badge-approved">تم الموافقة عليه</span>
                        @elseif ($purchaseOrder->status == "disagree")
                            <i class="fas fa-times-circle text-danger" style="font-size: 12px;"></i>
                            <span class="status-badge badge-rejected">مرفوض</span>
                        @endif
                    </div>
                </div>

                <!-- الجانب الأيسر - أزرار متغيرة حسب الحالة -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if ($purchaseOrder->status == "Under Review")
                        <!-- أزرار الموافقة والرفض للطلبات تحت المراجعة -->
                        <button type="button" class="status-btn btn-approve" onclick="approveOrder()">
                            <i class="fas fa-check"></i>
                            <span>موافقة</span>
                        </button>

                        <button type="button" class="status-btn btn-reject" onclick="rejectOrder()">
                            <i class="fas fa-times"></i>
                            <span>رفض</span>
                        </button>

                    @elseif ($purchaseOrder->status == "approval")
                        <!-- أزرار للطلبات المعتمدة -->
                        <button type="button" class="status-btn btn-create-quote pulse-effect" onclick="createPurchasePrice()">
                            <i class="fas fa-dollar-sign"></i>
                            <span>عرض سعر شراء</span>
                        </button>

                        <button type="button" class="status-btn btn-cancel-approval" onclick="cancelApproval()">
                            <i class="fas fa-undo"></i>
                            <span>إلغاء الموافقة</span>
                        </button>

                    @elseif ($purchaseOrder->status == "disagree")
                        <!-- زر للطلبات المرفوضة -->
                        <button type="button" class="status-btn btn-undo-rejection" onclick="undoRejection()">
                            <i class="fas fa-undo-alt"></i>
                            <span>التراجع عن الرفض</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

<div class="card">
    <div class="card-title p-2 d-flex align-items-center gap-2">
        <a href="{{ route('OrdersPurchases.edit', $purchaseOrder->id) }}" class="btn btn-outline-primary btn-sm">
            تعديل <i class="fa fa-edit ms-1"></i>
        </a>
        <div class="vr"></div>

        <a href="#" class="btn btn-outline-info btn-sm">
            نسخ <i class="fa fa-copy ms-1"></i>
        </a>
        <div class="vr"></div>

        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteOrder()">
            حذف <i class="fa fa-trash ms-1"></i>
        </button>
        <div class="vr"></div>

        <div class="dropdown">
            <button class="btn btn-outline-dark btn-sm" type="button" id="printDropdown" data-bs-toggle="dropdown">
                طباعة <i class="fa fa-print ms-1"></i>
            </button>
            <ul class="dropdown-menu">
   <li> <a href="{{ route('OrdersPurchases.pdf', $purchaseOrder->id) }}"
           class="btn btn-danger d-flex align-items-center">
            <i class="fas fa-file-pdf me-2"></i>
            تحميل PDF
        </a></li>
                <li><a class="dropdown-item" href="#"><i class="fa fa-file-excel me-2 text-success"></i>Excel
                        تصدير</a></li>
            </ul>
        </div>
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">التفاصيل</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#products" role="tab">المنتجات</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">سجل النشاطات</a>
            </li>
        </ul>

        <div class="tab-content p-3">
            <!-- تبويب التفاصيل -->
            <div class="tab-pane active" id="details" role="tabpanel">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="text-start" style="width: 50%">
                                    <div class="mb-2">
                                        <label class="text-muted">رقم الطلب:</label>
                                        <div>{{ $purchaseOrder->code }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="text-muted">العنوان:</label>
                                        <div>{{ $purchaseOrder->title }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="text-muted">تاريخ الطلب:</label>
                                        <div>{{ $purchaseOrder->order_date }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="text-muted">منشئ الطلب:</label>
                                        <div>{{ $purchaseOrder->creator->name ?? 'غير محدد' }}</div>
                                    </div>
                                </td>
                                <td class="text-start" style="width: 50%">
                                    <div class="mb-2">
                                        <label class="text-muted">تاريخ الاستحقاق:</label>
                                        <div>{{ $purchaseOrder->due_date ?? 'غير محدد' }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="text-muted">عدد المنتجات:</label>
                                        <div>{{ $purchaseOrder->productDetails->count() }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="text-muted">الملاحظات:</label>
                                        <div>{{ $purchaseOrder->notes ?? 'لا توجد ملاحظات' }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="text-muted">المرفقات:</label>
                                        @if ($purchaseOrder->attachments)
                                            <div><a href="{{ asset('assets/uploads/purchase_orders/' . $purchaseOrder->attachments) }}"
                                                    target="_blank">عرض المرفق</a></div>
                                        @else
                                            <div>لا توجد مرفقات</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- تبويب المنتجات -->
            <div class="tab-pane" id="products" role="tabpanel">
                <div class="table-responsive mb-4">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>منتج</th>
                                <th>الكمية</th>
                                <th>تاريخ الاضافة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productDetails as $detail)
                                <tr>
                                    <td>{{ $detail->product->name ?? '--' }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ $detail->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
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
                            <div class="alert alert-danger text-xl-center" role="alert">
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
<form id="approveForm" action="{{ route('OrdersPurchases.approve', $purchaseOrder->id) }}" method="POST"
    style="display: none;">
    @csrf
    <input type="hidden" id="approveNote" name="note">
</form>

<form id="rejectForm" action="{{ route('OrdersPurchases.reject', $purchaseOrder->id) }}" method="POST"
    style="display: none;">
    @csrf
</form>

<form id="cancelApprovalForm" action="{{ route('OrdersPurchases.cancelApproval', $purchaseOrder->id) }}"
    method="POST" style="display: none;">
    @csrf
</form>

<form id="undoRejectionForm" action="{{ route('OrdersPurchases.undoRejection', $purchaseOrder->id) }}"
    method="POST" style="display: none;">
    @csrf
</form>

<form id="deleteForm" action="{{ route('OrdersPurchases.destroy', $purchaseOrder->id) }}" method="POST"
    style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // دالة الموافقة على الطلب
    function approveOrder() {
        Swal.fire({
            title: 'تأكيد الموافقة',
            text: 'هل أنت متأكد من الموافقة على طلب الشراء رقم {{ $purchaseOrder->code }}؟',
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

    // دالة رفض الطلب
    function rejectOrder() {
        Swal.fire({
            title: 'تأكيد الرفض',
            text: 'هل أنت متأكد من رفض طلب الشراء رقم {{ $purchaseOrder->code }}؟',
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

    // دالة إنشاء عرض سعر شراء
    function createPurchasePrice() {
        Swal.fire({
            title: 'عرض سعر شراء',
            text: 'سيتم توجيهك لصفحة إنشاء عرض سعر جديد للطلب',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'متابعة',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // توجيه لصفحة إنشاء عرض السعر
                window.location.href = "{{ route('Quotations.create') }}?order_id={{ $purchaseOrder->id }}";
            }
        });
    }

    // دالة إلغاء الموافقة
    function cancelApproval() {
        Swal.fire({
            title: 'إلغاء الموافقة',
            text: 'هل أنت متأكد من إلغاء الموافقة على هذا الطلب؟',
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
            text: 'هل تريد إعادة هذا الطلب إلى حالة "تحت المراجعة"؟',
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

    // دالة حذف الطلب
    function deleteOrder() {
        Swal.fire({
            title: 'حذف طلب الشراء',
            text: 'هل أنت متأكد من حذف طلب الشراء رقم "{{ $purchaseOrder->code }}"؟ هذا الإجراء لا يمكن التراجع عنه!',
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
</script>
@endsection
