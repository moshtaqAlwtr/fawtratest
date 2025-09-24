@extends('master')


@section('title')
    الفواتير
@stop

@section('css')


    <style>
        .card-header button.active {
            border: 2px solid #007bff;
            font-weight: bold;
        }

        .card-header button {
            transition: all 0.3s ease;
        }

        .card-header button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .content-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .content-header-title {
                font-size: 1.5rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .card {
                margin: 10px;
                padding: 10px;
            }


            .table {
                font-size: 0.8rem;
                width: 100%;
                overflow-x: auto;
                /* Allow horizontal scrolling */
            }

            .table th,
            .table td {
                white-space: nowrap;
                /* Prevent text from wrapping */
            }

            .form-check {
                margin-bottom: 10px;
            }

            .form-control {
                width: 100%;
            }

            .dropdown-menu {
                min-width: 200px;
            }
        }

        /* Additional styles for smaller devices */
        @media (max-width: 480px) {
            .invoice-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .invoice-number,
            .amount-info {
                text-align: left;
            }

            .client-info {
                margin-bottom: 10px;
            }

            .table th,
            .table td {
                font-size: 0.7rem;
                /* Smaller font size for mobile */
            }
        }
    </style>
@endsection
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة الفواتير </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard_sales.index') }}">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>

                    </div>

                </div>
            </div>
        </div>
    </div>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <p class="mb-0">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="content-body">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <!-- Checkbox لتحديد الكل -->


                        <div class="d-flex flex-wrap justify-content-between">
                            <a href="{{ route('invoices.create') }}" class="btn btn-success btn-sm flex-fill me-1 mb-1">
                                <i class="fas fa-plus-circle me-1"></i>فاتورة جديدة
                            </a>

                            <button class="btn btn-outline-primary btn-sm flex-fill mb-1">
                                <i class="fas fa-cloud-upload-alt me-1"></i>استيراد
                            </button>
                        </div>



                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center p-2">
                    <div class="d-flex gap-2">
                        <span class="hide-button-text">بحث وتصفية</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="toggleSearchFields(this)">
                            <i class="fa fa-times"></i>
                            <span class="hide-button-text">اخفاء</span>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                            data-bs-target="#advancedSearchForm" onclick="toggleSearchText(this)">
                            <i class="fa fa-filter"></i>
                            <span class="button-text">متقدم</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form" id="searchForm" method="GET" action="{{ route('invoices.index') }}">
                        <div class="row g-3">
                            <!-- Client, Invoice Number, and Status -->
                            <div class="col-md-4">
                                <label for="client_id">أي العميل</label>
                                <select name="client_id" class="form-control select2" id="client_id">
                                    <option value="">أي العميل</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" data-client-number="{{ $client->id }}"
                                            data-client-name="{{ $client->trade_name }}"
                                            {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->trade_name }} ({{ $client->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="invoice_number">رقم الفاتورة</label>
                                <input type="text" id="invoice_number" class="form-control" placeholder="رقم الفاتورة"
                                    name="invoice_number" value="{{ request('invoice_number') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="status">حالة الفاتورة</label>
                                <select name="status" class="form-control" id="status">
                                    <option value="">الحالة</option>
                                    <option value="1" {{ request('Payment_status') == 1 ? 'selected' : '' }}> مدفوعة
                                        بالكامل</option>
                                    <option value="2" {{ request('Payment_status') == 2 ? 'selected' : '' }}>مدفوعة
                                        جزئيًا</option>
                                    <option value="3" {{ request('Payment_status') == 3 ? 'selected' : '' }}>غير
                                        مدفوعة بالكامل</option>
                                    <option value="4" {{ request('Payment_status') == 4 ? 'selected' : '' }}>مرتجع
                                    </option>
                                    <option value="5" {{ request('Payment_status') == 5 ? 'selected' : '' }}>مرتجع
                                        جزئي</option>
                                    <option value="6" {{ request('Payment_status') == 6 ? 'selected' : '' }}>مدفوع
                                        بزيادة</option>
                                    <option value="7" {{ request('Payment_status') == 7 ? 'selected' : '' }}>مستحقة
                                        الدفع</option>
                                </select>
                            </div>
                        </div>

                        <!-- Advanced Search -->
                        <div class="collapse" id="advancedSearchForm">
                            <div class="row g-3 mt-2">




                                <!-- 6. الإجمالي (من) -->
                                <div class="col-md-3">
                                    <label for="total_from">الإجمالي أكبر من</label>
                                    <input type="text" id="total_from" class="form-control"
                                        placeholder="الإجمالي أكبر من" name="total_from"
                                        value="{{ request('total_from') }}">
                                </div>

                                <!-- 7. الإجمالي (إلى) -->
                                <div class="col-md-3">
                                    <label for="total_to">الإجمالي أصغر من</label>
                                    <input type="text" id="total_to" class="form-control" placeholder="الإجمالي أصغر من"
                                        name="total_to" value="{{ request('total_to') }}">
                                </div>





                                <!-- 10. التاريخ (من) -->
                                <div class="col-md-3">
                                    <label for="from_date">التاريخ من</label>
                                    <input type="date" id="from_date" class="form-control" name="from_date"
                                        value="{{ request('from_date') }}">
                                </div>

                                <!-- 11. التاريخ (إلى) -->
                                <div class="col-md-3">
                                    <label for="to_date">التاريخ إلى</label>
                                    <input type="date" id="to_date" class="form-control" name="to_date"
                                        value="{{ request('to_date') }}">
                                </div>


                                <!-- 13. تاريخ الاستحقاق (من) -->
                                <!--<div class="col-md-3">-->
                                <!--    <label for="due_date_from">تاريخ الاستحقاق (من)</label>-->
                                <!--    <input type="date" id="due_date_from" class="form-control" name="due_date_from"-->
                                <!--        value="{{ request('due_date_from') }}">-->
                                <!--</div>-->

                                <!-- 14. تاريخ الاستحقاق (إلى) -->
                                <!--<div class="col-md-3">-->
                                <!--    <label for="due_date_to">تاريخ الاستحقاق (إلى)</label>-->
                                <!--    <input type="date" id="due_date_to" class="form-control" name="due_date_to"-->
                                <!--        value="{{ request('due_date_to') }}">-->
                                <!--</div>-->





                                <!-- 20. حالة التسليم -->
                                <!--<div class="col-md-4">-->
                                <!--    <label for="delivery_status">حالة التسليم</label>-->
                                <!--    <select name="delivery_status" class="form-control" id="delivery_status">-->
                                <!--        <option value="">حالة التسليم</option>-->
                                <!--        <option value="delivered"-->
                                <!--            {{ request('delivery_status') == 'delivered' ? 'selected' : '' }}>تم التسليم-->
                                <!--        </option>-->
                                <!--        <option value="pending"-->
                                <!--            {{ request('delivery_status') == 'pending' ? 'selected' : '' }}>قيد الانتظار-->
                                <!--        </option>-->
                                <!--    </select>-->
                                <!--</div>-->

                                <!-- 21. أضيفت بواسطة (الموظفين) -->
                                <div class="col-md-6">
                                    <label for="added_by_employee">أضيفت بواسطة </label>
                                    <select name="added_by_employee" class="form-control" id="added_by_employee">
                                        <option value="">أضيفت بواسطة</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ request('added_by_employee') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 22. مسؤول المبيعات (المستخدمين) -->
                                <div class="col-md-6">
                                    <label for="sales_person_user">مسؤول المبيعات </label>
                                    <select name="sales_person_user" class="form-control" id="sales_person_user">
                                        <option value="">مسؤول المبيعات</option>
                                        @foreach ($employees_sales_person as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ request('sales_person_user') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 23. Post Shift -->
                                <!--<div class="col-md-4">-->
                                <!--    <label for="post_shift">Post Shift</label>-->
                                <!--    <input type="text" id="post_shift" class="form-control" placeholder="Post Shift"-->
                                <!--        name="post_shift" value="{{ request('post_shift') }}">-->
                                <!--</div>-->

                                <!-- 24. خيارات الشحن -->
                                <!--<div class="col-md-4">-->
                                <!--    <label for="shipping_option">خيارات الشحن</label>-->
                                <!--    <select name="shipping_option" class="form-control" id="shipping_option">-->
                                <!--        <option value="">خيارات الشحن</option>-->
                                <!--        <option value="standard"-->
                                <!--            {{ request('shipping_option') == 'standard' ? 'selected' : '' }}>عادي</option>-->
                                <!--        <option value="express"-->
                                <!--            {{ request('shipping_option') == 'express' ? 'selected' : '' }}>سريع</option>-->
                                <!--    </select>-->
                                <!--</div>-->


                            </div>
                        </div>


                        <!-- Action Buttons -->
                        <div class="form-actions mt-2">
                            <button type="submit" class="btn btn-primary">بحث</button>
                            <a href="{{ route('invoices.index') }}" type="reset"
                                class="btn btn-outline-warning">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center p-3">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-sm btn-outline-primary" onclick="filterInvoices('all')">
                            <i class="fas fa-list me-1"></i> الكل
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="filterInvoices('late')">
                            <i class="fas fa-clock me-1"></i> متأخر
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="filterInvoices('due')">
                            <i class="fas fa-calendar-day me-1"></i> مستحقة الدفع
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="filterInvoices('unpaid')">
                            <i class="fas fa-times-circle me-1"></i> غير مدفوع
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="filterInvoices('draft')">
                            <i class="fas fa-file-alt me-1"></i> مسودة
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="filterInvoices('overpaid')">
                            <i class="fas fa-check-double me-1"></i> مدفوع بزيادة
                        </button>

                    </div>
                </div>

                <!-- Invoice Table -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="testTable" class="table table-hover nowrap" style="width:100%">
                            <thead class="bg-light" style="background-color: #BABFC7 !important;">

                                <tr class="bg-gradient-light text-center">

                                    <th class="border-start">رقم الفاتورة</th>
                                    <th>معلومات العميل</th>
                                    <th>تاريخ الفاتورة</th>
                                    <th>المصدر والعملية</th>
                                    <th>المبلغ والحالة</th>
                                    <th style="width: 100px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="invoiceTableBody">
                                @foreach ($invoices as $invoice)
                                    <tr class="align-middle invoice-row"
                                        onclick="window.location.href='{{ route('invoices.show', $invoice->id) }}'"
                                        style="cursor: pointer;" data-status="{{ $invoice->payment_status }}">

                                        <td class="text-center border-start"><span
                                                class="invoice-number">#{{ $invoice->id }}</span></td>
                                        <td>
                                            <div class="client-info">
                                                <div class="client-name mb-2">
                                                    <i class="fas fa-user text-primary me-1"></i>
                                                    <strong>{{ $invoice->client ? ($invoice->client->trade_name ?: $invoice->client->first_name . ' ' . $invoice->client->last_name) : 'عميل غير معروف' }}</strong>
                                                </div>
                                                @if ($invoice->client && $invoice->client->tax_number)
                                                    <div class="tax-info mb-1">
                                                        <i class="fas fa-hashtag text-muted me-1"></i>
                                                        <span class="text-muted small">الرقم الضريبي:
                                                            {{ $invoice->client->tax_number }}</span>
                                                    </div>
                                                @endif
                                                @if ($invoice->client && $invoice->client->full_address)
                                                    <div class="address-info">
                                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                                        <span
                                                            class="text-muted small">{{ $invoice->client->full_address }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <td
                                            data-order="{{ $invoice->created_at ? $invoice->created_at->timestamp : '' }}">
                                            <div class="date-info mb-2">
                                                <i class="fas fa-calendar text-info me-1"></i>
                                                {{ $invoice->created_at ? $invoice->created_at->format($account_setting->time_formula ?? 'H:i:s d/m/Y') : '' }}
                                            </div>
                                            <div class="creator-info">
                                                <i class="fas fa-user text-muted me-1"></i>
                                                <span class="text-muted small">بواسطة:
                                                    {{ $invoice->createdByUser->name ?? 'غير محدد' }}</span>
                                                <span class="text-muted small">
                                                    للمندوب {{ $invoice->employee->first_name ?? 'غير محدد' }} </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-2" style="margin-bottom: 60px">
                                                @php
                                                    $payments = \App\Models\PaymentsProcess::where(
                                                        'invoice_id',
                                                        $invoice->id,
                                                    )
                                                        ->where('type', 'client payments')
                                                        ->orderBy('created_at', 'desc')
                                                        ->get();
                                                @endphp

                                                @php
                                                    $returnedInvoice = \App\Models\Invoice::where('type', 'returned')
                                                        ->where('reference_number', $invoice->id)
                                                        ->first();
                                                @endphp

                                                @if ($returnedInvoice)
                                                    <span class="badge bg-danger text-white"><i
                                                            class="fas fa-undo me-1"></i>مرتجع</span>
                                                @elseif ($invoice->type == 'normal' && $payments->count() == 0)
                                                    <span class="badge bg-secondary text-white"><i
                                                            class="fas fa-file-invoice me-1"></i>أنشئت فاتورة</span>
                                                @endif

                                                @if ($payments->count() > 0)
                                                    <span class="badge bg-success text-white"><i
                                                            class="fas fa-check-circle me-1"></i>أضيفت عملية دفع</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    1 => [
                                                        'class' => 'success',
                                                        'icon' => '<i class="fas fa-check-circle"></i>',
                                                        'text' => 'مدفوعة بالكامل',
                                                    ],
                                                    2 => [
                                                        'class' => 'info',
                                                        'icon' => '<i class="fas fa-adjust"></i>',
                                                        'text' => 'مدفوعة جزئياً',
                                                    ],
                                                    3 => [
                                                        'class' => 'danger',
                                                        'icon' => '<i class="fas fa-times-circle"></i>',
                                                        'text' => 'غير مدفوعة',
                                                    ],
                                                    4 => [
                                                        'class' => 'secondary',
                                                        'icon' => '<i class="fas fa-hand-holding-usd"></i>',
                                                        'text' => 'مستلمة',
                                                    ],
                                                ];

                                                $status = $statusMap[$invoice->payment_status] ?? [
                                                    'class' => 'dark',
                                                    'icon' => '<i class="fas fa-question-circle"></i>',
                                                    'text' => 'غير معروفة',
                                                ];

                                                $currency = $account_setting->currency ?? 'SAR';
                                                $currencySymbol =
                                                    $currency === 'SAR' || empty($currency)
                                                        ? '<img src="' .
                                                            asset('assets/images/Saudi_Riyal.svg') .
                                                            '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                                                        : $currency;

                                                $net_due = $invoice->due_value - $invoice->returned_payment;
                                            @endphp

                                            <div class="text-center">
                                                <span class="badge bg-{{ $status['class'] }} text-white status-badge">
                                                    {!! $status['icon'] !!} {{ $status['text'] }}
                                                </span>
                                            </div>

                                            <div class="amount-info text-center mb-2">
                                                <h6 class="amount mb-1">
                                                    {{ number_format($invoice->grand_total ?? $invoice->total, 2) }}
                                                    <small class="currency">{!! $currencySymbol !!}</small>
                                                </h6>

                                                @if ($returnedInvoice)
                                                    <span class="text-danger">
                                                        <i class="fas fa-undo-alt"></i> مرتجع :
                                                        {{ number_format($invoice->returned_payment, 2) }}
                                                        {!! $currencySymbol !!}
                                                    </span>
                                                @endif

                                                @if ($invoice->due_value > 0)
                                                    <div class="due-amount">
                                                        <small class="text-danger">
                                                            <i class="fas fa-exclamation-triangle"></i> المبلغ المستحق:
                                                            {{ number_format($net_due, 2) }}
                                                            {!! $currencySymbol !!}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <div class="dropdown" onclick="event.stopPropagation()">
                                                <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v "
                                                    type="button" id="dropdownMenuButton{{ $invoice->id }}"
                                                    data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                                    aria-haspopup="true" aria-expanded="false"></button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item"
                                                        href="{{ route('invoices.edit', $invoice->id) }}">
                                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('invoices.show', $invoice->id) }}">
                                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('invoices.generatePdf', $invoice->id) }}">
                                                        <i class="fa fa-file-pdf me-2 text-danger"></i>PDF
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('invoices.generatePdf', $invoice->id) }}">
                                                        <i class="fa fa-print me-2 text-dark"></i>طباعة
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('invoices.send', $invoice->id) }}">
                                                        <i class="fa fa-envelope me-2 text-warning"></i>إرسال إلى العميل
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('paymentsClient.create', ['id' => $invoice->id]) }}">
                                                        <i class="fa fa-credit-card me-2 text-info"></i>إضافة عملية دفع
                                                    </a>

                                                    <form action="{{ route('invoices.destroy', $invoice->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-trash me-2"></i>حذف
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
                </div>
                @if ($invoices->isEmpty())
                    <div class="alert alert-warning m-3" role="alert">
                        <p class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>لا توجد فواتير</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

@section('scripts')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="{{ asset('assets/js/search.js') }}"></script>
    <script></script>

    <!-- jQuery و DataTables -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- DataTables الأساسية -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <!-- أزرار التصدير -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>

    <!-- تعريب -->
    <script src="https://cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json"></script>

    <script>
        $(document).ready(function() {
            $('#testTable').DataTable({
                "order": [
                    [2, "desc"]
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json'
                },
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: 'تصدير إلى Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'print',
                        text: 'طباعة',
                        className: 'btn btn-warning btn-sm'
                    },
                    {
                        extend: 'copy',
                        text: 'نسخ',
                        className: 'btn btn-info btn-sm'
                    }
                ]
            });
        });
    </script>

@endsection
