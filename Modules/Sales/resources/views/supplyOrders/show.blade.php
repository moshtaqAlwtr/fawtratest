@extends('master')

@section('title')
    عرض امر تشغيل
@stop

@section('css')
<style>
    :root {
        --soft-background: #f8f9fa;
        --soft-primary: #6a89a0;
        --soft-text: #495057;
        --soft-border: #e9ecef;
        --soft-muted: #adb5bd;
    }

    .card-custom {
        background-color: var(--soft-background);
        border: 1px solid var(--soft-border);
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border-radius: 12px;
    }

    .section-header {
        color: var(--soft-primary);
        border-bottom: 2px solid var(--soft-border);
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .info-label {
        color: var(--soft-muted);
        font-weight: 500;
        margin-bottom: 5px;
    }

    .info-value {
        color: var(--soft-text);
        font-weight: 600;
    }

    .status-badge {
        background-color: var(--soft-primary);
        color: white;
        padding: 5px 10px;
        border-radius: 6px;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

@endsection
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض امر توريد </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-end">
                        <span class="h5">العميل: {{ $supplyOrder->client->trade_name }}</span>
                    </div>
                    <div class="d-flex gap-3">
                        <!-- زر الحالة -->
                        <div class="dropdown">
                            <button class="btn btn-light btn-md" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                اختر الحالة
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><span class="badge bg-info">جاري الشحن</span></a></li>
                                <li><a class="dropdown-item" href="#"><span class="badge bg-warning">تحت التجهيز</span></a></li>
                                <li><a class="dropdown-item" href="#"><span class="badge bg-success">تم التسليم</span></a></li>
                                <li><a class="dropdown-item" href="#"><span class="badge bg-danger">تم الدفع</span></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('SupplyOrders.edit_status') }}"><i class="fas fa-cog"></i> تعديل قائمة الحالات</a></li>
                            </ul>
                        </div>

                        <!-- زر الإضافة -->
                        <div class="dropdown">
                            <button class="btn btn-success btn-md d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-plus me-2"></i>اضافة
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('invoices.create') }}">فاتورة جديدة</a></li>
                                <li><a class="dropdown-item" href="{{ route('CreditNotes.create') }}">إنشاء إشعار دائن</a></li>
                                <li><a class="dropdown-item" href="{{route('questions.create')}}">عرض سعر جديد</a></li>
                                <li><a class="dropdown-item" href="#">أضف رصيد مدفوعات</a></li>
                                <li><a class="dropdown-item" href="#">إضافة مصروف</a></li>
                                <li><a class="dropdown-item" href="#">إضافة إيراد</a></li>
                                <li><a class="dropdown-item" href="#">فاتورة شراء جديد</a></li>
                                <li><a class="dropdown-item" href="#">إذن إضافة مخزن</a></li>
                                <li><a class="dropdown-item" href="#">إذن صرف مخزن</a></li>
                                <li><a class="dropdown-item" href="#">إضافة معاملة موجودة</a></li>
                                <li><a class="dropdown-item" href="#">إضافة وقت</a></li>
                                <li><a class="dropdown-item" href="{{route('journal.create')}}">إضافة قيد</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2">
                    <a href="" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                        <i class="fas fa-edit me-1"></i> تعديل
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="fas fa-print me-1"></i> طباعة
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-warning d-inline-flex align-items-center">
                        <i class="fas fa-paperclip me-1"></i> إضافة ملاحظة/مرفق
                    </a>
                    <a href="" class="btn btn-sm btn-outline-info d-inline-flex align-items-center">
                        <i class="fas fa-calendar-alt me-1"></i> ترتيب موعد
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-success d-inline-flex align-items-center">
                        <i class="fas fa-exchange-alt me-1"></i> تعيين معاملة
                    </a>
                    <a href="" class="btn btn-sm btn-outline-dark d-inline-flex align-items-center">
                        <i class="fas fa-copy me-1"></i> نسخ
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">
                        <i class="fas fa-trash-alt me-1"></i> حذف
                    </a>
                </div>
            </div>

            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" aria-controls="details"
                        role="tab" aria-selected="true">
                        <span class="badge badge-pill badge-primary"></span> التفاصيل</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="invoices-tab" data-toggle="tab" href="#invoices" aria-controls="invoices"
                        role="tab" aria-selected="false">
                        امر التوريد <span class="badge badge-pill badge-primary"></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="appointments-tab" data-toggle="tab" href="#appointments"
                        aria-controls="appointments" role="tab" aria-selected="false">
                        المواعيد <span class="badge badge-pill badge-primary"></span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="account-movement-tab" data-toggle="tab" href="#account-movement"
                        aria-controls="account-movement" role="tab" aria-selected="false">
                        سجل النشاطات <span class="badge badge-pill badge-info"></span>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="details" aria-labelledby="details-tab" role="tabpanel">
                <div class="container-fluid p-4">
                    <div class="card card-custom shadow-sm">
                        <div class="card-header bg-white border-0 pt-4 pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="section-header mb-0 me-3">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    تفاصيل أمر التوريد
                                </h3>
                                <span class="status-badge ms-3">
                                    {{ match($supplyOrder->status) {
                                        1 => 'مفتوح',
                                        2 => 'مغلق',
                                        3 => 'قيد التنفيذ',
                                        default => 'غير محدد'
                                    } }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <!-- معلومات العميل -->
                            <div class="row mb-4 align-items-center">
                                <div class="col-12">
                                    <h4 class="info-value mb-2">
                                        <i class="bi bi-person-fill me-2"></i>
                                        {{ $supplyOrder->client->name }}
                                    </h4>
                                    <p class="text-muted mb-0">
                                        رقم أمر التوريد: #{{ $supplyOrder->id }}
                                    </p>
                                </div>
                            </div>

                            <!-- معلومات أساسية -->
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="bg-white border rounded-3 p-3 h-100">
                                        <h6 class="section-header">
                                            <i class="bi bi-briefcase me-2"></i>
                                            معلومات عامة
                                        </h6>
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <div class="info-label">رقم الأمر</div>
                                                <div class="info-value">{{ $supplyOrder->order_number }}</div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="info-label">المسمى</div>
                                                <div class="info-value">{{ $supplyOrder->name }}</div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="info-label">تاريخ البدء</div>
                                                <div class="info-value">{{ $supplyOrder->start_date }}</div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="info-label">تاريخ الانتهاء</div>
                                                <div class="info-value">{{ $supplyOrder->end_date }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="bg-white border rounded-3 p-3 h-100">
                                        <h6 class="section-header">
                                            <i class="bi bi-cash-coin me-2"></i>
                                            المعلومات المالية
                                        </h6>
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <div class="info-label">الميزانية</div>
                                                <div class="info-value">
                                                    {{ number_format($supplyOrder->budget, 2) }} {{ $supplyOrder->currency }}
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="info-label">الوسوم</div>
                                                <div class="info-value">{{ $supplyOrder->tag ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- الوصف -->
                            <div class="mt-4 bg-white border rounded-3 p-3">
                                <h6 class="section-header">
                                    <i class="bi bi-text-paragraph me-2"></i>
                                    الوصف
                                </h6>
                                <p class="text-muted">
                                    {{ $supplyOrder->description ?? 'لا يوجد وصف' }}
                                </p>
                            </div>

                            <!-- معلومات الموظفين والشحن -->
                            <div class="row mt-4 g-4">
                                <div class="col-md-6">
                                    <div class="bg-white border rounded-3 p-3 h-100">
                                        <h6 class="section-header">
                                            <i class="bi bi-people me-2"></i>
                                            الموظفون المعنيون
                                        </h6>
                                        <div class="info-value">
                                            {{ $supplyOrder->employee->name ?? 'لم يتم تحديد موظف' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-white border rounded-3 p-3 h-100">
                                        <h6 class="section-header">
                                            <i class="bi bi-truck me-2"></i>
                                            بيانات الشحن
                                        </h6>
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <div class="info-label">رقم التتبع</div>
                                                <div class="info-value">
                                                    {{ $supplyOrder->tracking_number ?? '-' }}
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="info-label">عنوان الشحن</div>
                                                <div class="info-value">
                                                    {{ $supplyOrder->shipping_address ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- بوليصة الشحن -->
                            <div class="mt-4 bg-white border rounded-3 p-3">
                                <h6 class="section-header">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>
                                    بوليصة الشحن
                                </h6>
                                @if($supplyOrder->shipping_policy_file)
                                    <div class="text-center">
                                        <img src="{{ asset($supplyOrder->shipping_policy_file) }}"
                                             alt="بوليصة الشحن"
                                             class="img-fluid rounded-3 shadow-sm"
                                             style="max-height: 300px; object-fit: contain;">
                                    </div>
                                @else
                                    <div class="alert alert-light text-center" role="alert">
                                        لا يوجد بوليصة شحن مرفقة
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <!-- Bootstrap Icons -->

                <div class="tab-pane" id="invoices" aria-labelledby="invoices-tab" role="tabpanel">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="tab-pane fade show active"
                                style="background: lightslategray; min-height: 100vh; padding: 20px;">
                                <div class="card shadow" style="max-width: 600px; margin: 20px auto;">
                                    <div class="card-body bg-white p-4" style="min-height: 400px; overflow: auto;">
                                        <div style="transform: scale(0.8); transform-origin: top center;">
                                            @include('supplyOrders.pdf', ['supplyOrder' => $supplyOrder])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- تبويبة المواعيد  --}}
                <div class="tab-pane" id="appointments" aria-labelledby="appointments-tab" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-sm btn-outline-primary filter-appointments" data-filter="all">
                                    الكل
                                    <span class="badge badge-light"></span>
                                </button>
                                <button class="btn btn-sm btn-outline-success filter-appointments" data-filter="">
                                    تم
                                    <span class="badge badge-light"></span>
                                </button>
                                <button class="btn btn-sm btn-outline-warning filter-appointments" data-filter="">
                                    تم صرف النظر عنه
                                    <span class="badge badge-light"></span>
                                </button>
                                <button class="btn btn-sm btn-outline-danger filter-appointments" data-filter="">
                                    تم جدولته
                                    <span class="badge badge-light"></span>
                                </button>
                                <button class="btn btn-sm btn-outline-info filter-appointments" data-filter="">
                                    تم جدولته مجددا
                                    <span class="badge badge-light"></span>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="appointments-container">
                                <div class="card mb-2 appointment-item" data-appointment-id="" data-status=""
                                    data-date="">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <strong>#</strong>
                                                <p class="mb-0"></p>
                                                <small class="text-muted"></small>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-0">
                                                    <small></small>
                                                </p>
                                                <small class="text-muted">
                                                    بواسطة:
                                                </small>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <span class="badge status-badge">

                                                </span>
                                            </div>
                                            <div class="col-md-2 text-end">

                                            </div>
                                        </div>

                                        <!-- معلومات إضافية للموعد -->
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <strong>نوع الإجراء:</strong>

                                                </small>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <strong>مدة الموعد:</strong>

                                                </small>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <small class="text-muted">
                                                    <strong>ملاحظات:</strong>

                                                </small>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- Modal for Adding Notes -->
                                <div class="modal fade" id="noteModal" tabindex="-1" role="dialog"
                                    aria-labelledby="noteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="noteModalLabel">إضافة ملاحظات للموعد</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form id="noteForm" method="POST">

                                                <div class="modal-body">
                                                    <input type="hidden" name="status" value="">
                                                    <div class="form-group">
                                                        <label for="notes">الملاحظات</label>
                                                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="أدخل ملاحظاتك هنا"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">إلغاء</button>
                                                    <button type="button" class="btn btn-primary"
                                                        onclick="submitCompletedAppointment()">حفظ الملاحظات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info text-center">
                                    لا توجد مواعيد
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/applmintion.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
