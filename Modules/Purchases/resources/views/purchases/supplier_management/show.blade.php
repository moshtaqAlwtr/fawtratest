@extends('master')

@section('title')
    عرض المورد
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
    <style>
        .payment-section,
        .supplier-section {
            flex: 1;
            max-width: 50%;
        }

        .account-movements-table {
            font-size: 14px;
        }

        .account-movements-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .balance-positive {
            color: #28a745;
            font-weight: 600;
        }

        .balance-negative {
            color: #dc3545;
            font-weight: 600;
        }

        .amount-deposit {
            color: #28a745;
        }

        .amount-withdraw {
            color: #dc3545;
        }

        .operation-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض المورد</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات المورد -->
    <div class="card supplier-card">
        <div class="floating-elements"></div>
        <div class="floating-elements"></div>
        <div class="floating-elements"></div>

        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start supplier-card-content flex-wrap">
                <!-- معلومات الدفعة المطلوبة -->
                <div class="payment-section mx-2">
                    @php
                        $balance = $supplier->account->balance ?? 0;
                        $currencySymbol =
                            '<img src="' .
                            asset('assets/images/Saudi_Riyal.svg') .
                            '" alt="ريال سعودي" width="15" style="vertical-align: middle;">';

                        // تحديد نوع الحالة والألوان
                        if ($balance > 50000) {
                            $paymentClass = 'payment-info-danger';
                            $badgeClass = 'bg-danger';
                            $badgeText = 'عاجل';
                            $iconClass = 'fa-exclamation-triangle';
                            $textColor = '#dc3545';
                        } elseif ($balance > 0) {
                            $paymentClass = 'payment-info-warning';
                            $badgeClass = 'bg-warning text-dark';
                            $badgeText = 'متأخر';
                            $iconClass = 'fa-credit-card';
                            $textColor = '#e67e22';
                        } else {
                            $paymentClass = 'payment-info-success';
                            $badgeClass = 'bg-success';
                            $badgeText = 'مُسوّى';
                            $iconClass = 'fa-check-circle';
                            $textColor = '#28a745';
                        }
                    @endphp

                    <div class="payment-info text-center {{ $paymentClass }}">
                        <div class="icon-wrapper ">
                            <i class="fa "></i>
                        </div>
                        <div class="supplier-section">
                            <small class="text-muted d-block">
                                {{ $balance > 0 ? 'مطلوب دفعة' : 'الرصيد' }}
                            </small>
                            <div class="payment-amount" style="color: {{ $textColor }}">
                                {{ number_format(abs($balance), 2) }} {!! $currencySymbol !!}
                            </div>
                            <div class="mt-1">
                                <small class="badge {{ $badgeClass }}">{{ $badgeText }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تفاصيل المورد -->
                <div class="supplier-section">
                    <div class="supplier-details text-end">
                        <div class="supplier-name">
                            {{ $supplier->trade_name }}
                            <span class="supplier-id"># {{ $supplier->id }}</span>
                        </div>

                        <div class="account-info">
                            <small class="text-muted d-block mb-1">
                                <i class="fa fa-university me-1"></i>
                                حساب الأستاذ:
                            </small>

                            @if ($supplier->account)
                                <div class="account-link-wrapper">
                                    <div class="icon-wrapper account-icon">
                                        <i class="fa fa-link"></i>
                                    </div>
                                    <a href="{{ route('accounts_chart.index', $supplier->account->id) }}"
                                        class="account-link">
                                        {{ $supplier->account->name }} - {{ $supplier->account->code }}
                                        <i class="fa fa-external-link-alt"></i>
                                    </a>
                                </div>
                            @else
                                <div class="no-account-wrapper">
                                    <div class="icon-wrapper danger-icon">
                                        <i class="fa fa-exclamation-triangle"></i>
                                    </div>
                                    <span class="no-account">
                                        لا يوجد حساب مرتبط
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- أزرار الإجراءات -->
    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('SupplierManagement.edit', $supplier->id) }}"
                class="btn btn-outline-primary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                تعديل البيانات <i class="fa fa-edit ms-1"></i>
            </a>

            <a href="#"
                class="btn btn-outline-success btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-bs-toggle="modal" data-bs-target="#modal_opening_balance">
                إضافة رصيد افتتاحي <i class="fa fa-plus-circle ms-1"></i>
            </a>
            <div class="vr"></div>

            <a href="{{ route('SupplierManagement.statement', $supplier->id) }}"
                class="btn btn-outline-info btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                كشف حساب <i class="fa fa-file-text ms-1"></i>
            </a>
            <div class="vr"></div>

            <a href="{{ route('invoicePurchases.create', ['supplier_id' => $supplier->id]) }}"
                class="btn btn-outline-dark btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                إنشاء فاتورة شراء <i class="fa fa-shopping-cart ms-1"></i>
            </a>
            <div class="vr"></div>

            <a href="#"
                class="btn btn-outline-primary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                أضف رصيد مدفوعات <i class="fa fa-money ms-1"></i>
            </a>
            <div class="vr"></div>

            @if ($supplier->status == 1)
                <button onclick="changeStatus({{ $supplier->id }}, 0, '{{ $supplier->name }}')"
                    class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
                    style="min-width: 90px;">
                    إيقاف <i class="fa fa-ban ms-1"></i>
                </button>
            @else
                <button onclick="changeStatus({{ $supplier->id }}, 1, '{{ $supplier->name }}')"
                    class="btn btn-outline-success btn-sm d-inline-flex align-items-center justify-content-center px-3"
                    style="min-width: 90px;">
                    تفعيل <i class="fa fa-check ms-1"></i>
                </button>
            @endif
            <div class="vr"></div>

            <a href="#"
                class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-bs-toggle="modal" data-bs-target="#modal_DELETE1">
                حذف <i class="fa fa-trash ms-1"></i>
            </a>
        </div>

        <!-- التبويبات -->
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">
                        <i class="fa fa-info-circle me-1"></i>
                        <span>التفاصيل</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#invoices" role="tab">
                        <i class="fa fa-file-invoice me-1"></i>
                        <span>فواتير الشراء</span>
                        @if ($purchaseInvoices->total() > 0)
                            <span class="badge bg-primary ms-1">{{ $purchaseInvoices->total() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#payments" role="tab">
                        <i class="fa fa-credit-card me-1"></i>
                        <span>المدفوعات</span>
                        @if ($payments->total() > 0)
                            <span class="badge bg-success ms-1">{{ $payments->total() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#account-movements" role="tab">
                        <i class="fa fa-exchange-alt me-1"></i>
                        <span>حركة الحساب</span>
                        @if (count($accountMovements) > 0)
                            <span class="badge bg-info ms-1">{{ count($accountMovements) }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                        <i class="fa fa-history me-1"></i>
                        <span>سجل النشاطات</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                <div class="tab-pane active" id="details" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fa fa-building me-2"></i>معلومات المورد</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>الاسم التجاري:</strong></td>
                                            <td>{{ $supplier->trade_name ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>اسم الشركة:</strong></td>
                                            <td>{{ $supplier->name ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>البريد الإلكتروني:</strong></td>
                                            <td>{{ $supplier->email ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>رقم الهاتف:</strong></td>
                                            <td>{{ $supplier->phone ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>العنوان:</strong></td>
                                            <td>{{ $supplier->address ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الحالة:</strong></td>
                                            <td>
                                                @if ($supplier->status == 1)
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-danger">غير نشط</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب الفواتير -->
                <div class="tab-pane" id="invoices" role="tabpanel">
                    @if ($purchaseInvoices->count() > 0)
                        @include('purchases::purchases.invoices_purchase.partials.table', [
                            'purchaseData' => $purchaseInvoices,
                        ])
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-file-invoice fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-2">لا توجد فواتير</h5>
                            <p class="text-muted mb-3">لم يتم إنشاء أي فواتير شراء لهذا المورد بعد</p>
                            <a href="{{ route('invoicePurchases.create', ['supplier_id' => $supplier->id]) }}"
                                class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>إنشاء فاتورة شراء
                            </a>
                        </div>
                    @endif
                </div>

                <!-- تبويب المدفوعات -->
                <div class="tab-pane" id="payments" role="tabpanel">
                    @if ($payments->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0">عرض {{ $payments->firstItem() }} إلى {{ $payments->lastItem() }} من
                                    {{ $payments->total() }} نتيجة</h6>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم الدفع</th>
                                        <th>الفاتورة</th>
                                        <th>المبلغ</th>
                                        <th>تاريخ الدفع</th>
                                        <th>الحالة</th>
                                        <th style="width: 10%">خيارات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong
                                                        class="text-primary">#{{ $payment->payment_number ?? $payment->id }}</strong>
                                                    <div class="text-muted small">ID: {{ $payment->id }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($payment->purchase_invoice)
                                                    <div class="d-flex flex-column">
                                                        <strong
                                                            class="text-info">{{ $payment->purchase_invoice->code ?? '--' }}</strong>
                                                        <small class="text-muted">
                                                            <i class="fa fa-calendar text-muted me-1"></i>
                                                            {{ $payment->purchase_invoice->invoice_date ? \Carbon\Carbon::parse($payment->purchase_invoice->invoice_date)->format('Y-m-d') : '--' }}
                                                        </small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    @php
                                                        $currencySymbol =
                                                            '<img src="' .
                                                            asset('assets/images/Saudi_Riyal.svg') .
                                                            '" alt="ريال سعودي" width="15" style="vertical-align: middle;">';
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

                                                    switch ($payment->payment_status) {
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
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                            type="button" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item text-success"
                                                                href="{{ route('PaymentSupplier.showSupplierPayment', $payment->id) }}">
                                                                <i class="fas fa-eye me-2"></i>عرض العملية
                                                            </a>
                                                            <a class="dropdown-item text-success"
                                                                href="{{ route('PaymentSupplier.editSupplierPayment', $payment->id) }}">
                                                                <i class="fas fa-edit me-2"></i>تعديل العملية
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

                        <!-- الترقيم للمدفوعات -->
                        {{ $payments->links() }}
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-credit-card fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-2">لا توجد مدفوعات</h5>
                            <p class="text-muted mb-3">لم يتم إجراء أي عمليات دفع لهذا المورد بعد</p>
                            <a href="{{ route('PaymentSupplier.createPurchase', ['id' => 1]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>إضافة دفعة جديدة
                            </a>
                        </div>
                    @endif
                </div>

                <!-- تبويب حركة الحساب -->
                <div class="tab-pane" id="account-movements" role="tabpanel">
                    @if (count($accountMovements) > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0">
                                    <i class="fa fa-exchange-alt text-primary me-2"></i>
                                    حركة حساب المورد - إجمالي {{ count($accountMovements) }} عملية
                                </h6>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('SupplierManagement.statement', $supplier->id) }}"
                                   class="btn btn-outline-info btn-sm">
                                    <i class="fa fa-print me-1"></i>كشف حساب مفصل
                                </a>
                            </div>
                        </div>

                        <!-- ملخص الحساب -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-left-primary">
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                الرصيد الافتتاحي
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($supplier->opening_balance ?? 0, 2) }} ر.س
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-left-info">
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                الرصيد الحالي
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold {{ $supplier->account->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($supplier->account->balance ?? 0, 2) }} ر.س
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
    <table class="table table-hover table-striped account-movements-table">
        <thead class="table-light">
            <tr>
                <th width="12%">التاريخ</th>
                <th width="8%">المرجع</th>
                <th width="35%">العملية</th>
                <th width="15%" class="text-center">المبلغ</th>
                <th width="13%" class="text-center">الرصيد</th>
                <th width="8%" class="text-center">النوع</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($accountMovements as $movement)
                <tr>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="text-dark fw-bold">
                                {{ \Carbon\Carbon::parse($movement['date'])->format('Y-m-d') }}
                            </span>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($movement['date'])->format('H:i') }}
                            </small>
                        </div>
                    </td>
                    <td>
                        @if(isset($movement['reference_number']) && $movement['reference_number'])
                            <span class="badge bg-secondary">
                                {{ $movement['reference_number'] }}
                            </span>
                        @elseif(isset($movement['journalEntry']) && $movement['journalEntry'])
                            <span class="badge bg-info">
                                {{ $movement['journalEntry'] }}
                            </span>
                        @else
                            <span class="text-muted">--</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <strong>{{ $movement['operation'] }}</strong>
                            @if(isset($movement['invoice']) && $movement['invoice'])
                                <small class="text-muted">
                                    <i class="fa fa-file-invoice me-1"></i>
                                    فاتورة: {{ $movement['invoice']->code ?? $movement['invoice']->id }}
                                </small>
                            @endif
                            @if(isset($movement['client']) && $movement['client'])
                                <small class="text-muted">
                                    <i class="fa fa-user me-1"></i>
                                    {{ $movement['client']->name }}
                                </small>
                            @endif
                        </div>
                    </td>
                    <td class="text-center">
                        @if ($movement['deposit'] > 0)
                            <span class="amount-deposit fw-bold">
                                +{{ number_format($movement['deposit'], 2) }}
                            </span>
                        @elseif ($movement['withdraw'] > 0)
                            <span class="amount-withdraw fw-bold">
                                -{{ number_format($movement['withdraw'], 2) }}
                            </span>
                        @else
                            <span class="text-muted">0.00</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="{{ $movement['balance_after'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                            {{ number_format($movement['balance_after'], 2) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @switch($movement['type'])
                            @case('transaction')
                                <span class="badge bg-primary operation-badge">
                                    <i class="fa fa-exchange-alt me-1"></i>معاملة
                                </span>
                                @break
                            @case('expense')
                                <span class="badge bg-danger operation-badge">
                                    <i class="fa fa-arrow-down me-1"></i>مصروف
                                </span>
                                @break
                            @case('revenue')
                                <span class="badge bg-success operation-badge">
                                    <i class="fa fa-arrow-up me-1"></i>إيراد
                                </span>
                                @break
                            @default
                                <span class="badge bg-secondary operation-badge">
                                    <i class="fa fa-question me-1"></i>أخرى
                                </span>
                        @endswitch
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr class="fw-bold">
                <td colspan="3" class="text-center">المجموع</td>
                <td class="text-center">
                    @php
                        $totalDeposit = collect($accountMovements)->sum('deposit');
                        $totalWithdraw = collect($accountMovements)->sum('withdraw');
                        $netAmount = $totalDeposit - $totalWithdraw;
                    @endphp
                    <span class="{{ $netAmount >= 0 ? 'amount-deposit' : 'amount-withdraw' }}">
                        {{ $netAmount >= 0 ? '+' : '' }}{{ number_format($netAmount, 2) }}
                    </span>
                </td>
                <td class="text-center {{ $supplier->account->balance >= 0 ? 'balance-positive' : 'balance-negative' }}">
                    {{ number_format($supplier->account->balance ?? 0, 2) }}
                </td>
                <td class="text-center">
                    <span class="badge bg-info operation-badge">المستحق</span>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-exchange-alt fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-2">لا توجد حركة على الحساب</h5>
                            <p class="text-muted mb-3">
                                @if(!$supplier->account)
                                    لا يوجد حساب مرتبط بهذا المورد
                                @else
                                    لم يتم تسجيل أي حركة مالية على حساب هذا المورد بعد
                                @endif
                            </p>
                        </div>
                    @endif
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
                                    <i class="fas fa-info-circle mb-2"></i>
                                    <p class="mb-0">لا توجد سجلات نشاط حتى الآن!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="modal_DELETE1" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">حذف المورد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من حذف هذا المورد؟</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <form action="{{ route('SupplierManagement.destroy', $supplier->id) }}" method="POST"
                        class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Opening Balance -->
    <div class="modal fade" id="modal_opening_balance" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">إضافة رصيد افتتاحي للمورد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="openingBalanceForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="opening_balance" class="form-label">المبلغ (SAR)</label>
                            <input type="number" step="0.01" class="form-control" id="opening_balance"
                                name="opening_balance" required placeholder="أدخل المبلغ الافتتاحي">
                        </div>
                        <p>هل أنت متأكد من إضافة رصيد افتتاحي للمورد <strong>{{ $supplier->trade_name }}</strong>؟</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // دالة تغيير حالة المورد
        function changeStatus(supplierId, newStatus, supplierName) {
            const statusText = newStatus == 1 ? 'تفعيل' : 'إيقاف';
            const statusColor = newStatus == 1 ? '#28a745' : '#dc3545';
            const icon = newStatus == 1 ? 'success' : 'warning';

            Swal.fire({
                title: `${statusText} المورد`,
                html: `هل أنت متأكد من <strong>${statusText}</strong> المورد <br><strong>"${supplierName}"</strong>؟`,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: statusColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: `نعم، ${statusText}`,
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // عرض مؤشر التحميل
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

                    // إرسال طلب AJAX
                    $.ajax({
                        url: `/SupplierManagement/${supplierId}/update-status`,
                        type: 'POST',
                        data: {
                            status: newStatus,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'تم بنجاح!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // تحديث الصفحة
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'خطأ!',
                                    text: response.message || 'حدث خطأ أثناء تحديث الحالة',
                                    icon: 'error',
                                    confirmButtonText: 'موافق'
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'حدث خطأ غير متوقع';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'خطأ!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'موافق'
                            });
                        }
                    });
                }
            });
        }

        // معالجة إرسال نموذج الرصيد الافتتاحي
        $('#openingBalanceForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const supplierName = "{{ $supplier->trade_name }}";
            const amount = $('#opening_balance').val();

            Swal.fire({
                title: 'تأكيد إضافة الرصيد',
                html: `هل أنت متأكد من إضافة رصيد افتتاحي بقيمة <strong>${amount} SAR</strong> للمورد <strong>"${supplierName}"</strong>؟`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، إضافة',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-success me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // عرض مؤشر التحميل
                    Swal.fire({
                        title: 'جاري الإضافة...',
                        text: 'يرجى الانتظار',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // إرسال طلب AJAX
                    $.ajax({
                        url: "{{ route('SupplierManagement.updateOpeningBalance', $supplier->id) }}",
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'تم بنجاح!',
                                    text: 'تم إضافة الرصيد الافتتاحي بنجاح',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // إغلاق النافذة وتحديث الصفحة
                                    $('#modal_opening_balance').modal('hide');
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'خطأ!',
                                    text: response.message ||
                                        'حدث خطأ أثناء إضافة الرصيد',
                                    icon: 'error',
                                    confirmButtonText: 'موافق'
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'حدث خطأ غير متوقع';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'خطأ!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'موافق'
                            });
                        }
                    });
                }
            });
        });

        // تفعيل الترقيم للتبويبات
        $(document).ready(function() {
            // معالجة تغيير التبويبات
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                var targetTab = $(e.target).attr("href");

                // إذا كان التبويب النشط هو الفواتير أو المدفوعات، تحديث الترقيم
                if (targetTab === '#invoices' || targetTab === '#payments') {
                    // يمكن إضافة كود AJAX هنا لتحديث البيانات إذا لزم الأمر
                }
            });
        });
    </script>
@endsection
