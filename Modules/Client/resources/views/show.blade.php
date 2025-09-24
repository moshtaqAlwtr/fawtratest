@extends('master')

@section('title')
    عرض العميل
@stop

@section('head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
    <style>
        .payment-section,
        .client-section {
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

        .floating-elements {
            position: absolute;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-elements:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: -2s;
        }

        .floating-elements:nth-child(2) {
            top: 40%;
            right: 10%;
            animation-delay: -4s;
        }

        .floating-elements:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: -6s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .client-card {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #f7f7f7 0%, #fffeff 100%);
            color:black;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .client-card-content {
            position: relative;
            z-index: 2;
        }

        .payment-info {
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .payment-info-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .payment-info-warning {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));
            border: 1px solid rgba(255, 193, 7, 0.2);
        }

        .payment-info-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .payment-amount {
            font-size: 2rem;
            font-weight: 700;
            margin: 1rem 0;
        }

        .client-details {
            padding: 1rem;
        }

        .client-name {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .client-id {
            opacity: 0.8;
            font-size: 1rem;
            margin-right: 0.5rem;
        }

        .account-info {
            margin-top: 1rem;
        }

        .account-link-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .account-link {
            color: #17a2b8;
            text-decoration: none;
            font-weight: 600;
        }

        .account-link:hover {
            text-decoration: underline;
        }

        .no-account-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .no-account {
            color: #dc3545;
            font-weight: 600;
        }

        .icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .account-icon {
            background: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }

        .danger-icon {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        /* Timeline Styles */
        .timeline {
            position: relative;
            padding-right: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            right: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #007bff, transparent);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-dot {
            position: absolute;
            right: -25px;
            top: 20px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #007bff;
        }

        .note-box {
            transition: all 0.3s ease;
            border-right: 3px solid #007bff;
        }

        .note-box:hover {
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
            transform: translateX(-2px);
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Badge Colors */
        .badge-success {
            background-color: #28a745 !important;
        }

        .badge-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .badge-danger {
            background-color: #dc3545 !important;
        }

        .badge-info {
            background-color: #17a2b8 !important;
        }

        .badge-secondary {
            background-color: #6c757d !important;
        }

        /* Button Improvements */
        .btn-outline-primary:hover,
        .btn-outline-success:hover,
        .btn-outline-info:hover,
        .btn-outline-warning:hover,
        .btn-outline-danger:hover,
        .btn-outline-dark:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }

        /* Table Improvements */
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }

        .table td {
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .payment-section,
            .client-section {
                max-width: 100%;
                margin: 0 0 1rem 0;
            }

            .client-card-content {
                flex-direction: column;
                text-align: center;
            }

            .client-details {
                text-align: center !important;
            }

            .timeline {
                padding-right: 20px;
            }

            .timeline::before {
                right: 10px;
            }

            .timeline-dot {
                right: -15px;
            }

            .card-title {
                flex-direction: column;
                gap: 1rem !important;
            }

            .card-title .btn {
                width: 100%;
                min-width: auto !important;
            }

            .card-title .vr {
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (session('toast_message'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastr.{{ session('toast_type', 'success') }}('{{ session('toast_message') }}', '', {
                    positionClass: 'toast-bottom-left',
                    closeButton: true,
                    progressBar: true,
                    timeOut: 5000
                });
            });
        </script>
    @endif

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض العميل</h2>
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

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <!-- معلومات العميل -->
    <div class="card client-card">
        <div class="floating-elements"></div>
        <div class="floating-elements"></div>
        <div class="floating-elements"></div>

        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start client-card-content flex-wrap">
                <!-- معلومات الدفعة المطلوبة -->
                <div class="payment-section mx-2">
                    @php
                        $balance = $due ?? 0;
                        $currencySymbol = $account_setting->currency ?? 'SAR';
                        $currencySymbol = $currencySymbol == 'SAR' || empty($currencySymbol) ?
                            '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">' :
                            $currencySymbol;

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
                        <div class="icon-wrapper">
                            <i class="fa {{ $iconClass }}"></i>
                        </div>
                        <div class="client-section">
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

                <!-- تفاصيل العميل -->
                <div class="client-section">
                    <div class="client-details text-end">
                        <div class="client-name">
                            {{ $client->trade_name ?: ($client->first_name . ' ' . $client->last_name) }}
                            <span class="client-id"># {{ $client->code }}</span>
                        </div>

                        <div class="account-info">
                            <small class="text-muted d-block mb-1">
                                <i class="fa fa-university me-1"></i>
                                حساب الأستاذ:
                            </small>

                            @if ($client->account_client && $client->account_client->client_id == $client->id)
                                <div class="account-link-wrapper">
                                    <div class="icon-wrapper account-icon">
                                        <i class="fa fa-link"></i>
                                    </div>
                                    <a href="{{ route('journal.generalLedger', ['account_id' => $client->account_client->id]) }}"
                                        class="account-link">
                                        {{ $client->account_client->name }} - {{ $client->account_client->code }}
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

   <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        @php
                            $currentStatus = $client->status;
                        @endphp


                        <form method="POST" action="{{ route('clients.updateStatusClient') }}" class="flex-grow-1"
                            style="min-width: 220px;">
                            @csrf
                            <input type="hidden" name="client_id" value="{{ $client->id }}">
                            <div class="dropdown w-100">
                                <button class="btn w-100 text-start dropdown-toggle" type="button"
                                    id="clientStatusDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                    style="background-color: {{ $currentStatus->color ?? '#e0f7fa' }}; color: #000; border: 1px solid #ccc; height: 42px;">
                                    {{ $currentStatus->name ?? 'اختر الحالة' }}
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="clientStatusDropdown"
                                    style="border-radius: 8px;">
                                    @foreach ($statuses as $status)
                                        <li>
                                            <button type="submit"
                                                class="dropdown-item text-white d-flex align-items-center justify-content-between"
                                                name="status_id" value="{{ $status->id }}"
                                                style="background-color: {{ $status->color }};">
                                                <span><i class="fas fa-thumbtack me-1"></i> {{ $status->name }}</span>
                                            </button>
                                        </li>
                                    @endforeach
                                    <li>
                                        <a href="{{ route('SupplyOrders.edit_status') }}"
                                            class="dropdown-item text-muted d-flex align-items-center justify-content-center"
                                            style="border-top: 1px solid #ddd; padding: 8px;">
                                            <i class="fas fa-cog me-2"></i> تعديل قائمة الحالات - العميل
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- أزرار الإجراءات -->
    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2 flex-wrap">
            @if (auth()->user()->hasPermissionTo('Edit_Client'))
                <a href="{{ route('clients.edit', $client->id) }}"
                    class="btn btn-outline-primary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                    style="min-width: 90px;">
                    تعديل البيانات <i class="fa fa-edit ms-1"></i>
                </a>
            @endif

            <a href="#"
                class="btn btn-outline-success btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-bs-toggle="modal" data-bs-target="#modal_opening_balance">
                إضافة رصيد افتتاحي <i class="fa fa-plus-circle ms-1"></i>
            </a>
            <div class="vr"></div>

            <a href="{{ route('clients.statement', $client->id) }}"
                class="btn btn-outline-info btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                كشف حساب <i class="fa fa-file-text ms-1"></i>
            </a>
            <div class="vr"></div>

            <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}"
                class="btn btn-outline-dark btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                إنشاء فاتورة <i class="fa fa-file-invoice ms-1"></i>
            </a>
            <div class="vr"></div>

            <a href="{{ route('incomes.create') }}"
                class="btn btn-outline-primary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                سند القبض <i class="fa fa-money ms-1"></i>
            </a>
            <div class="vr"></div>

            <a href="{{ route('appointments.create') }}"
                class="btn btn-outline-success btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                ترتيب موعد <i class="fa fa-calendar-plus ms-1"></i>
            </a>
            <div class="vr"></div>

            @if (auth()->user()->role === 'manager')
                <form action="{{ route('clients.force-show', $client) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit"
                        class="btn btn-outline-warning btn-sm d-inline-flex align-items-center justify-content-center px-3"
                        style="min-width: 90px;">
                        إظهار في الخريطة <i class="fa fa-map-marker-alt ms-1"></i>
                    </button>
                </form>
            @endif

            <a href="#"
                class="btn btn-outline-primary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-bs-toggle="modal" data-bs-target="#assignEmployeeModal">
                تعيين موظفين <i class="fa fa-user-plus ms-1"></i>
            </a>
            <div class="vr"></div>

            <a href="{{ route('CreditNotes.create') }}"
                class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                إشعار دائن <i class="fa fa-file-invoice-dollar ms-1"></i>
            </a>
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
                        <span>الفواتير</span>
                        @if (isset($invoices) && $invoices->count() > 0)
                            <span class="badge bg-primary ms-1">{{ $invoices->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#payments" role="tab">
                        <i class="fa fa-credit-card me-1"></i>
                        <span>المدفوعات</span>
                        @if (isset($payments) && $payments->where('type', 'client payments')->count() > 0)
                            <span class="badge bg-success ms-1">{{ $payments->where('type', 'client payments')->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#appointments" role="tab">
                        <i class="fa fa-calendar-alt me-1"></i>
                        <span>المواعيد</span>
                        @if ($client->appointments->count() > 0)
                            <span class="badge bg-info ms-1">{{ $client->appointments->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#notes" role="tab">
                        <i class="fa fa-sticky-note me-1"></i>
                        <span>الملاحظات</span>
                        @if (isset($ClientRelations) && count($ClientRelations) > 0)
                            <span class="badge bg-warning ms-1">{{ count($ClientRelations) }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#visits" role="tab">
                        <i class="fa fa-walking me-1"></i>
                        <span>الزيارات</span>
                        @if (isset($visits) && $visits->count() > 0)
                            <span class="badge bg-secondary ms-1">{{ $visits->count() }}</span>
                        @endif
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
                                    <h5><i class="fa fa-user me-2"></i>معلومات العميل الأساسية</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>الاسم التجاري:</strong></td>
                                            <td>{{ $client->trade_name ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الاسم الأول:</strong></td>
                                            <td>{{ $client->first_name ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الاسم الأخير:</strong></td>
                                            <td>{{ $client->last_name ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>رقم الهاتف:</strong></td>
                                            <td>{{ $client->phone ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الجوال:</strong></td>
                                            <td>{{ $client->mobile ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>البريد الإلكتروني:</strong></td>
                                            <td class="text-break">{{ $client->email ?? 'غير محدد' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fa fa-map-marker-alt me-2"></i>العنوان والمعلومات الإضافية</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>العنوان:</strong></td>
                                            <td>{{ $client->street1 }} {{ $client->street2 }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>المدينة:</strong></td>
                                            <td>{{ $client->city ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>المنطقة:</strong></td>
                                            <td>{{ $client->region ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الرمز البريدي:</strong></td>
                                            <td>{{ $client->postal_code ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الدولة:</strong></td>
                                            <td>{{ $client->country ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الرقم الضريبي:</strong></td>
                                            <td>{{ $client->tax_number ?? 'غير محدد' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fa fa-building me-2"></i>معلومات تجارية</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>السجل التجاري:</strong></td>
                                            <td>{{ $client->commercial_registration ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>حد الائتمان:</strong></td>
                                            <td>{{ $client->credit_limit ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>فترة الائتمان:</strong></td>
                                            <td>{{ $client->credit_period ? $client->credit_period . ' يوم' : 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>طريقة الطباعة:</strong></td>
                                            <td>
                                                @if ($client->printing_method == 1)
                                                    طباعة عادية
                                                @elseif($client->printing_method == 2)
                                                    طباعة حرارية
                                                @else
                                                    غير محدد
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>نوع العميل:</strong></td>
                                            <td>
                                                @if ($client->client_type == 1)
                                                    فرد
                                                @elseif($client->client_type == 2)
                                                    شركة
                                                @else
                                                    غير محدد
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fa fa-dollar-sign me-2"></i>المعلومات المالية</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>الرصيد الافتتاحي:</strong></td>
                                            <td>{{ number_format($client->opening_balance ?? 0, 2) }} {!! $currencySymbol !!}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>تاريخ الرصيد الافتتاحي:</strong></td>
                                            <td>{{ $client->opening_balance_date ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الرصيد الحالي:</strong></td>
                                            <td>
                                                <span class="{{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($balance, 2) }} {!! $currencySymbol !!}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>الحالة:</strong></td>
                                            <td>
                                                @if (isset($client->status))
                                                    <span class="badge" style="background-color: {{ $client->status->color ?? '#007BFF' }}; color: white;">
                                                        {{ $client->status->name ?? 'غير محدد' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">غير محدد</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (isset($client->employees) && $client->employees->count() > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fa fa-users me-2"></i>الموظفون المعينون</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            @foreach ($client->employees as $employee)
                                                <div class="col-auto">
                                                    <div class="badge bg-primary d-flex align-items-center fs-6 py-2 px-3">
                                                        <a href="{{ route('employee.show', $employee->id) }}"
                                                            class="text-white text-decoration-none me-2">
                                                            <i class="fa fa-user me-1"></i>
                                                            {{ $employee->full_name }}
                                                        </a>
                                                        @if (auth()->user()->role === 'manager')
                                                            <form action="{{ route('clients.remove-employee', $client->id) }}"
                                                                method="POST" class="mb-0">
                                                                @csrf
                                                                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                                                <button type="submit" class="btn btn-sm btn-link text-white p-0"
                                                                    onclick="return confirm('هل أنت متأكد من إزالة هذا الموظف؟')">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($client->notes)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fa fa-comment me-2"></i>ملاحظات إضافية</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-break">{{ $client->notes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- تبويب الفواتير -->
                <div class="tab-pane" id="invoices" role="tabpanel">
                    @if (isset($invoices) && $invoices->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0">عرض 1 إلى {{ $invoices->count() }} من {{ $invoices->count() }} نتيجة</h6>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم الفاتورة</th>
                                        <th>التاريخ</th>
                                        <th>المبلغ</th>
                                        <th>الحالة</th>
                                        <th>المبلغ المستحق</th>
                                        <th style="width: 10%">خيارات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong class="text-primary">#{{ $invoice->id }}</strong>
                                                    <div class="text-muted small">{{ $invoice->code ?? 'ID: ' . $invoice->id }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark">
                                                        <i class="fa fa-calendar text-muted me-1"></i>
                                                        {{ $invoice->created_at ? $invoice->created_at->format('Y-m-d') : '--' }}
                                                    </span>
                                                    <small class="text-muted">
                                                        <i class="fa fa-clock me-1"></i>
                                                        {{ $invoice->created_at ? $invoice->created_at->format('H:i') : '--' }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-0 font-weight-bold text-dark">
                                                        {{ number_format($invoice->grand_total ?? $invoice->total, 2) }} {!! $currencySymbol !!}
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="fa fa-user text-muted me-1"></i>
                                                        {{ $invoice->createdByUser->name ?? 'غير محدد' }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match ($invoice->payment_status) {
                                                        1 => 'badge-success',
                                                        2 => 'badge-warning',
                                                        3 => 'badge-danger',
                                                        4 => 'badge-secondary',
                                                        default => 'badge-light',
                                                    };
                                                    $statusText = match ($invoice->payment_status) {
                                                        1 => 'مدفوعة بالكامل',
                                                        2 => 'مدفوعة جزئياً',
                                                        3 => 'غير مدفوعة',
                                                        4 => 'مستلمة',
                                                        default => 'غير معروفة',
                                                    };
                                                    $statusIcon = match ($invoice->payment_status) {
                                                        1 => 'fa-check-circle',
                                                        2 => 'fa-clock',
                                                        3 => 'fa-times-circle',
                                                        4 => 'fa-file-alt',
                                                        default => 'fa-question',
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }} rounded-pill">
                                                    <i class="fas {{ $statusIcon }} me-1"></i>
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td>
                                                @if (isset($invoice->due_value) && $invoice->due_value > 0)
                                                    <span class="text-danger fw-bold">
                                                        {{ number_format($invoice->due_value, 2) }} {!! $currencySymbol !!}
                                                    </span>
                                                @else
                                                    <span class="text-success">--</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item text-primary"
                                                            href="{{ route('invoices.show', $invoice->id) }}">
                                                            <i class="fas fa-eye me-2"></i>عرض الفاتورة
                                                        </a>
                                                        <a class="dropdown-item text-success"
                                                            href="{{ route('invoices.edit', $invoice->id) }}">
                                                            <i class="fas fa-edit me-2"></i>تعديل الفاتورة
                                                        </a>
                                                        <a class="dropdown-item text-info"
                                                            href="{{ route('paymentsClient.create', ['id' => $invoice->id]) }}">
                                                            <i class="fas fa-credit-card me-2"></i>إضافة دفع
                                                        </a>
                                                        <a class="dropdown-item text-warning"
                                                            href="{{ route('invoices.generatePdf', $invoice->id) }}">
                                                            <i class="fas fa-file-pdf me-2"></i>طباعة PDF
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-file-invoice fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-2">لا توجد فواتير</h5>
                            <p class="text-muted mb-3">لم يتم إنشاء أي فواتير لهذا العميل بعد</p>
                            <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>إنشاء فاتورة جديدة
                            </a>
                        </div>
                    @endif
                </div>

                <!-- تبويب المدفوعات -->
                <div class="tab-pane" id="payments" role="tabpanel">
                    @if (isset($payments) && $payments->where('type', 'client payments')->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0">عرض 1 إلى {{ $payments->where('type', 'client payments')->count() }} من
                                    {{ $payments->where('type', 'client payments')->count() }} نتيجة</h6>
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
                                    @foreach ($payments->where('type', 'client payments') as $payment)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong class="text-primary">#{{ $payment->id }}</strong>
                                                    <div class="text-muted small">{{ $payment->payment_number ?? 'ID: ' . $payment->id }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($payment->invoice)
                                                    <div class="d-flex flex-column">
                                                        <strong class="text-info">{{ $payment->invoice->code ?? $payment->invoice->id }}</strong>
                                                        <small class="text-muted">
                                                            <i class="fa fa-calendar text-muted me-1"></i>
                                                            {{ $payment->invoice->created_at ? $payment->invoice->created_at->format('Y-m-d') : '--' }}
                                                        </small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
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
                                                            {{ $payment->payment_method ?? 'غير محدد' }}
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
                                                        case 4:
                                                            $statusClass = 'badge-info';
                                                            $statusText = 'تحت المراجعة';
                                                            $statusIcon = 'fa-sync';
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
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item text-success"
                                                            href="{{ route('paymentsClient.show', $payment->id) }}">
                                                            <i class="fas fa-eye me-2"></i>عرض العملية
                                                        </a>
                                                        <a class="dropdown-item text-success"
                                                            href="{{ route('paymentsClient.edit', $payment->id) }}">
                                                            <i class="fas fa-edit me-2"></i>تعديل العملية
                                                        </a>
                                                        <a class="dropdown-item text-warning"
                                                            href="{{ route('paymentsClient.rereceipt', ['id' => $payment->id]) }}?type=a4"
                                                            target="_blank">
                                                            <i class="fas fa-file-pdf me-2"></i>إيصال PDF
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-credit-card fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-2">لا توجد مدفوعات</h5>
                            <p class="text-muted mb-3">لم يتم إجراء أي عمليات دفع لهذا العميل بعد</p>
                            <a href="{{ route('incomes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>إضافة دفعة جديدة
                            </a>
                        </div>
                    @endif
                </div>

                <!-- تبويب المواعيد -->
                <div class="tab-pane" id="appointments" role="tabpanel">
                    @if ($client->appointments->count() > 0)
                        @php
                            $completedAppointments = $client->appointments->where('status', 2);
                            $ignoredAppointments = $client->appointments->where('status', 3);
                            $pendingAppointments = $client->appointments->where('status', 1);
                            $rescheduledAppointments = $client->appointments->where('status', 4);
                        @endphp

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button class="btn btn-sm btn-outline-primary filter-appointments active" data-filter="all">
                                الكل <span class="badge badge-light">{{ $client->appointments->count() }}</span>
                            </button>
                            <button class="btn btn-sm btn-outline-success filter-appointments" data-filter="2">
                                تم <span class="badge badge-light">{{ $completedAppointments->count() }}</span>
                            </button>
                            <button class="btn btn-sm btn-outline-warning filter-appointments" data-filter="3">
                                تم صرف النظر <span class="badge badge-light">{{ $ignoredAppointments->count() }}</span>
                            </button>
                            <button class="btn btn-sm btn-outline-danger filter-appointments" data-filter="1">
                                مجدول <span class="badge badge-light">{{ $pendingAppointments->count() }}</span>
                            </button>
                            <button class="btn btn-sm btn-outline-info filter-appointments" data-filter="4">
                                معاد جدولته <span class="badge badge-light">{{ $rescheduledAppointments->count() }}</span>
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم الموعد</th>
                                        <th>العنوان</th>
                                        <th>الوصف</th>
                                        <th>التاريخ</th>
                                        <th>الموظف</th>
                                        <th>الحالة</th>
                                        <th>خيارات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($client->appointments as $appointment)
                                        <tr data-status="{{ $appointment->status }}">
                                            <td>
                                                <strong class="text-primary">#{{ $appointment->id }}</strong>
                                            </td>
                                            <td>{{ $appointment->title }}</td>
                                            <td>{{ Str::limit($appointment->description, 50) }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark">
                                                        <i class="fa fa-calendar text-muted me-1"></i>
                                                        {{ $appointment->created_at->format('Y-m-d') }}
                                                    </span>
                                                    <small class="text-muted">
                                                        <i class="fa fa-clock me-1"></i>
                                                        {{ $appointment->created_at->format('H:i') }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>{{ $appointment->employee->name ?? 'غير محدد' }}</td>
                                            <td>
                                                @php
                                                    $statusClass = '';
                                                    $statusText = '';
                                                    $statusIcon = '';

                                                    switch ($appointment->status) {
                                                        case 1:
                                                            $statusClass = 'badge-warning';
                                                            $statusText = 'مجدول';
                                                            $statusIcon = 'fa-clock';
                                                            break;
                                                        case 2:
                                                            $statusClass = 'badge-success';
                                                            $statusText = 'تم';
                                                            $statusIcon = 'fa-check-circle';
                                                            break;
                                                        case 3:
                                                            $statusClass = 'badge-danger';
                                                            $statusText = 'صرف النظر';
                                                            $statusIcon = 'fa-times-circle';
                                                            break;
                                                        case 4:
                                                            $statusClass = 'badge-info';
                                                            $statusText = 'معاد جدولته';
                                                            $statusIcon = 'fa-redo';
                                                            break;
                                                        default:
                                                            $statusClass = 'badge-secondary';
                                                            $statusText = 'غير محدد';
                                                            $statusIcon = 'fa-question';
                                                    }
                                                @endphp
                                                <span class="badge {{ $statusClass }} rounded-pill">
                                                    <i class="fas {{ $statusIcon }} me-1"></i>
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <form action="{{ route('appointments.update-status', $appointment->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="1">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa fa-clock me-2 text-warning"></i>مجدول
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('appointments.update-status', $appointment->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="2">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa fa-check me-2 text-success"></i>تم
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('appointments.update-status', $appointment->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="3">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa fa-times me-2 text-danger"></i>صرف النظر
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('appointments.update-status', $appointment->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="4">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa fa-redo me-2 text-info"></i>إعادة جدولة
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-2">لا توجد مواعيد</h5>
                            <p class="text-muted mb-3">لم يتم ترتيب أي مواعيد لهذا العميل بعد</p>
                            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>ترتيب موعد جديد
                            </a>
                        </div>
                    @endif
                </div>

                <!-- تبويب الملاحظات -->
                <div class="tab-pane" id="notes" role="tabpanel">
                    @if (isset($ClientRelations) && count($ClientRelations) > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0">
                                    <i class="fa fa-sticky-note text-primary me-2"></i>
                                    سجل الملاحظات - إجمالي {{ count($ClientRelations) }} ملاحظة
                                </h6>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('appointment.notes.create', $client->id) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fa fa-plus me-1"></i>إضافة ملاحظة جديدة
                                </a>
                            </div>
                        </div>

                        <div class="timeline-container">
                            <div class="timeline">
                                @foreach ($ClientRelations as $note)
                                    <div class="timeline-item mb-4">
                                        <div class="timeline-content d-flex flex-column flex-md-row">
                                            <!-- نقطة الخط الزمني -->
                                            <div class="timeline-dot-container d-none d-md-flex align-items-start">
                                                <div class="timeline-dot bg-primary"></div>
                                            </div>

                                            <!-- محتوى الملاحظة -->
                                            <div class="note-main-content flex-grow-1">
                                                <!-- حالة العميل وقت الملاحظة -->
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    @if(isset($statuses) && isset($client->status_id))
                                                        <span class="badge"
                                                            style="background-color: {{ $statuses->find($client->status_id)->color ?? '#007BFF' }}; color: white;">
                                                            {{ $statuses->find($client->status_id)->name ?? '' }}
                                                        </span>
                                                    @endif
                                                    <small class="text-muted d-md-none">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ $note->created_at->format('d/m/Y H:i') }}
                                                    </small>
                                                </div>

                                                <!-- مربع الملاحظة -->
                                                <div class="note-box border rounded bg-white shadow-sm p-3">
                                                    <!-- رأس الملاحظة -->
                                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <i class="fas fa-user me-1"></i>
                                                                {{ $note->employee->name ?? 'غير معروف' }}
                                                            </h6>
                                                            <small class="text-muted">
                                                                <i class="fas fa-tag me-1"></i>
                                                                {{ $note->process ?? 'بدون تصنيف' }}
                                                            </small>
                                                        </div>
                                                        <small class="text-muted d-none d-md-block">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ $note->created_at->format('d/m/Y H:i') }}
                                                        </small>
                                                    </div>

                                                    <hr class="my-2">

                                                    <!-- محتوى الملاحظة -->
                                                    <div class="note-content mb-3">
                                                        <p class="mb-2">{{ $note->description ?? 'لا يوجد وصف' }}</p>

                                                        <!-- البيانات الإضافية -->
                                                        @if ($note->deposit_count || $note->site_type || $note->competitor_documents)
                                                            <div class="additional-data mt-3 p-2 bg-light rounded">
                                                                <div class="row">
                                                                    @if ($note->deposit_count)
                                                                        <div class="col-12 col-sm-6 col-md-4 mb-2">
                                                                            <span class="d-block text-primary">
                                                                                <i class="fas fa-boxes me-1"></i>
                                                                                عدد العهدة:
                                                                            </span>
                                                                            <span class="fw-bold">{{ $note->deposit_count }}</span>
                                                                        </div>
                                                                    @endif

                                                                    @if ($note->site_type)
                                                                        <div class="col-12 col-sm-6 col-md-4 mb-2">
                                                                            <span class="d-block text-primary">
                                                                                <i class="fas fa-store me-1"></i>
                                                                                نوع الموقع:
                                                                            </span>
                                                                            <span class="fw-bold">
                                                                                @switch($note->site_type)
                                                                                    @case('independent_booth')
                                                                                        بسطة مستقلة
                                                                                    @break
                                                                                    @case('grocery')
                                                                                        بقالة
                                                                                    @break
                                                                                    @case('supplies')
                                                                                        تموينات
                                                                                    @break
                                                                                    @case('markets')
                                                                                        أسواق
                                                                                    @break
                                                                                    @case('station')
                                                                                        محطة
                                                                                    @break
                                                                                    @default
                                                                                        {{ $note->site_type }}
                                                                                @endswitch
                                                                            </span>
                                                                        </div>
                                                                    @endif

                                                                    @if ($note->competitor_documents)
                                                                        <div class="col-12 col-sm-6 col-md-4 mb-2">
                                                                            <span class="d-block text-primary">
                                                                                <i class="fas fa-file-contract me-1"></i>
                                                                                استندات المنافسين:
                                                                            </span>
                                                                            <span class="fw-bold">{{ $note->competitor_documents }}</span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- المرفقات -->
                                                    @php
                                                        $files = json_decode($note->attachments, true);
                                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                                    @endphp

                                                    @if (is_array($files) && count($files))
                                                        <div class="attachments mt-3">
                                                            <h6 class="mb-2">
                                                                <i class="fas fa-paperclip me-1"></i>
                                                                المرفقات:
                                                            </h6>
                                                            <div class="d-flex flex-wrap gap-2">
                                                                @foreach ($files as $file)
                                                                    @php
                                                                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                                                                        $fileUrl = asset('assets/uploads/notes/' . $file);
                                                                    @endphp

                                                                    @if (in_array(strtolower($ext), $imageExtensions))
                                                                        <a href="{{ $fileUrl }}"
                                                                            data-fancybox="gallery-{{ $note->id }}"
                                                                            class="d-inline-block me-2 mb-2">
                                                                            <img src="{{ $fileUrl }}" alt="مرفق صورة"
                                                                                class="img-thumbnail"
                                                                                style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ $fileUrl }}" target="_blank"
                                                                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                                                                            <i class="fas fa-file-alt me-2"></i>
                                                                            {{ Str::limit($file, 15) }}
                                                                        </a>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- أدوات الملاحظة -->
                                                    <div class="note-actions mt-3 pt-2 border-top d-flex justify-content-end flex-wrap gap-2">
                                                        <button class="btn btn-sm btn-outline-secondary edit-note"
                                                            data-note-id="{{ $note->id }}"
                                                            data-process="{{ $note->process }}"
                                                            data-description="{{ $note->description }}"
                                                            data-deposit-count="{{ $note->deposit_count }}"
                                                            data-site-type="{{ $note->site_type }}"
                                                            data-competitor-documents="{{ $note->competitor_documents }}">
                                                            <i class="fas fa-edit me-1"></i> تعديل
                                                        </button>
                                                        <form action="#" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('هل أنت متأكد من حذف هذه الملاحظة؟')">
                                                                <i class="fas fa-trash me-1"></i> حذف
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-sticky-note fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-2">لا توجد ملاحظات</h5>
                            <p class="text-muted mb-3">لم يتم إضافة أي ملاحظات لهذا العميل بعد</p>
                            <a href="{{ route('appointment.notes.create', $client->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>إضافة ملاحظة جديدة
                            </a>
                        </div>
                    @endif
                </div>

                <!-- تبويب الزيارات -->
                <div class="tab-pane" id="visits" role="tabpanel">
                    @if (isset($visits) && $visits->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0">
                                    <i class="fa fa-walking text-primary me-2"></i>
                                    زيارات العميل - إجمالي {{ $visits->count() }} زيارة
                                </h6>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم الزيارة</th>
                                        <th>تاريخ الزيارة</th>
                                        <th>وقت الانصراف</th>
                                        <th>الموظف</th>
                                        <th>ملاحظات</th>
                                        <th>خيارات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($visits as $visit)
                                        <tr>
                                            <td>
                                                <strong class="text-primary">#{{ $visit->id }}</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark">
                                                        <i class="fa fa-calendar text-muted me-1"></i>
                                                        {{ $visit->visit_date }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($visit->departure_time)
                                                    <span class="text-success">
                                                        <i class="fa fa-clock text-muted me-1"></i>
                                                        {{ $visit->departure_time }}
                                                    </span>
                                                @else
                                                    <span class="text-warning">
                                                        <i class="fa fa-clock text-muted me-1"></i>
                                                        لم ينصرف بعد
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold">{{ $visit->employee->name ?? 'غير محدد' }}</span>
                                                    @if ($visit->employee && $visit->employee->branch)
                                                        <small class="text-muted">{{ $visit->employee->branch->name }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if ($visit->notes)
                                                    <span class="text-dark">{{ Str::limit($visit->notes, 50) }}</span>
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item text-primary" href="#">
                                                            <i class="fas fa-eye me-2"></i>عرض التفاصيل
                                                        </a>
                                                        <a class="dropdown-item text-success" href="#">
                                                            <i class="fas fa-edit me-2"></i>تعديل الزيارة
                                                        </a>
                                                        <form action="#" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger"
                                                                onclick="return confirm('هل أنت متأكد من حذف هذه الزيارة؟')">
                                                                <i class="fas fa-trash me-2"></i>حذف
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-walking fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-2">لا توجد زيارات</h5>
                            <p class="text-muted mb-3">لم يتم تسجيل أي زيارات لهذا العميل بعد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="modal_DELETE1" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">حذف العميل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من حذف هذا العميل؟</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>تحذير:</strong> سيؤدي حذف العميل إلى حذف جميع البيانات المرتبطة به (الفواتير، المدفوعات، المواعيد، الملاحظات).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">حذف نهائي</button>
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
                    <h5 class="modal-title">إضافة رصيد افتتاحي للعميل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="openingBalanceForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="opening_balance" class="form-label">المبلغ ({!! $currencySymbol !!})</label>
                            <input type="number" step="0.01" class="form-control" id="opening_balance"
                                name="opening_balance" value="{{ $client->opening_balance ?? 0 }}"
                                required placeholder="أدخل المبلغ الافتتاحي">
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            سيتم إضافة هذا المبلغ كرصيد افتتاحي للعميل <strong>{{ $client->trade_name ?: ($client->first_name . ' ' . $client->last_name) }}</strong>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>حفظ الرصيد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Assign Employee -->
    <div class="modal fade" id="assignEmployeeModal" tabindex="-1" aria-labelledby="assignEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="assignEmployeeModalLabel">تعيين موظفين للعميل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('clients.assign-employees', $client->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                        <div class="mb-3">
                            <label for="employee_select" class="form-label">اختر الموظفين</label>
                            <select name="employee_id[]" multiple class="form-control select2" id="employee_select">
                                @if(isset($employees))
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            @if ($client->employees && $client->employees->contains('id', $employee->id)) selected @endif>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            يمكنك اختيار عدة موظفين لتعيينهم مع هذا العميل
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-1"></i>تعيين الموظفين
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // معالجة إرسال نموذج الرصيد الافتتاحي
        $('#openingBalanceForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const clientName = "{{ $client->trade_name ?: ($client->first_name . ' ' . $client->last_name) }}";
            const amount = $('#opening_balance').val();

            Swal.fire({
                title: 'تأكيد إضافة الرصيد',
                html: `هل أنت متأكد من إضافة رصيد افتتاحي بقيمة <strong>${amount} SAR</strong> للعميل <strong>"${clientName}"</strong>؟`,
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
                        url: "{{ route('clients.updateOpeningBalance', $client->id) }}",
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
                                    text: response.message || 'حدث خطأ أثناء إضافة الرصيد',
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

        // فلترة المواعيد
        $(document).ready(function() {
            $('.filter-appointments').click(function() {
                const filter = $(this).data('filter');

                // إزالة الفئة النشطة من جميع الأزرار وإضافتها للزر المحدد
                $('.filter-appointments').removeClass('active');
                $(this).addClass('active');

                // إظهار/إخفاء الصفوف حسب الفلتر
                $('#appointments tbody tr').each(function() {
                    const rowStatus = $(this).data('status');
                    if (filter === 'all' || rowStatus == filter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // تفعيل Select2 للموظفين إذا كان متوفراً
            if ($.fn.select2) {
                $('#employee_select').select2({
                    placeholder: 'اختر الموظفين',
                    allowClear: true,
                    dropdownParent: $('#assignEmployeeModal')
                });
            }
        });

        // معالجة تأكيد حذف العميل
        $('#modal_DELETE1 form').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'تأكيد الحذف النهائي',
                text: 'هل أنت متأكد من حذف هذا العميل نهائياً؟ سيتم حذف جميع البيانات المرتبطة به!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف نهائياً',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // تحسين تجربة المستخدم للتبويبات
        $(document).ready(function() {
            // إضافة مؤثرات بصرية للتبويبات
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                $(e.target.getAttribute('href')).addClass('fade-in');
                setTimeout(() => {
                    $(e.target.getAttribute('href')).removeClass('fade-in');
                }, 300);
            });

            // تحسين عرض الجداول على الأجهزة المحمولة
            $('.table-responsive').each(function() {
                if ($(this).width() > $(this).parent().width()) {
                    $(this).addClass('scrollable-table');
                }
            });
        });

        // معالجة النوافذ المنبثقة للتوست
        @if (session('toast_message'))
            toastr.{{ session('toast_type', 'success') }}('{{ session('toast_message') }}', '', {
                positionClass: 'toast-bottom-left',
                closeButton: true,
                progressBar: true,
                timeOut: 5000
            });
        @endif
    </script>

    <!-- مكتبات إضافية -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

