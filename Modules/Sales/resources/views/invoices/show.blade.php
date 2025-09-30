@extends('master')

@section('title')
    عرض فاتورة المبيعات
@stop

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/report.css') }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/accept.css') }}">
    <style>
        /* تخصيص الأزرار */
        .custom-btn {
            min-width: 120px;
            j margin: 5px;

            justify-content: center;

        }

        .custom-dropdown {
            min-width: 200px;

        }


        .tab-content {
            position: relative;
            z-index: 1;
        }

        .pdf-iframe {
            width: 100%;
            height: 800px;
            border: none;
            display: block;
            margin: 0 auto;
        }

        .sidebar {
            position: fixed;
            z-index: 100;

            top: 0;
            right: 0;
            height: 100vh;
            width: 250px;

            background: #f8f9fa;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .invoice-wrapper {

            contain: content;
            position: relative;
            z-index: 1;
            width: 100%;
            overflow: visible;
            padding: 20px 0;
        }


        .sidebar {
            position: fixed !important;
            right: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            transform: none !important;
            margin: 0 !important;
        }

        .main-content {
            transition: none !important;
            transform: none !important;
        }

        .main-content {
            margin-left: 250px;

            padding: 20px;
            width: calc(100% - 250px);
        }


        .pdf-wrapper {
            width: 100%;
            overflow-x: auto;
            background: white;
            padding: 20px;
            display: flex;
            justify-content: center;
        }


        [dir="rtl"] .pdf-wrapper {
            direction: rtl;
        }


        .tab-content>.active {
            overflow: visible !important;
        }

        .custom-dropdown .dropdown-item {
            padding: 0.5rem 1rem;

            font-size: 0.875rem;

        }

        .custom-dropdown .dropdown-item:hover {
            background-color: #f8f9fa;

            color: #0056b3;

        }


        .custom-btn i {
            margin-right: 5px;
        }

        /* تخصيصات لوحة التوقيع */
        #signature-pad {
            width: 100% !important;
            height: 200px !important;
            border: 1px solid #ddd;
            background: white;
        }

        .signature-history img {
            border: 1px solid #eee;
            padding: 5px;
            background: white;
        }

        .toast-container {
            z-index: 9999;
        }
    </style>

@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض فاتورة المبيعات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">فواتير المبيعات</a></li>
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
    <div class="invoice-header">
        <div class="row align-items-center justify-content-between">
            <!-- جهة اليمين: العنوان والمعلومات -->
            <div class="col-lg-8 text-end">
                <div class="row text-end">
                    <div class="col-md-6">
                        <h3 class="mb-3">
                            <i class="fas fa-file-invoice me-2"></i>
                            فاتورة المبيعات #{{ $invoice->id }}
                        </h3>
                        <p class="mb-2">
                            <i class="fas fa-user me-2"></i>
                            <strong>العميل:</strong>
                            {{ $invoice->client->trade_name ?? $invoice->client->first_name . ' ' . $invoice->client->last_name }}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-calendar me-2"></i>
                            <strong>تاريخ الفاتورة:</strong> {{ $invoice->created_at->format('Y-m-d') }}
                        </p>
                        @if ($invoice->journalEntry && $invoice->journalEntry->id)
                            <p class="mb-1">
                                <a href="{{ route('journal.show', $invoice->journalEntry->id) }}"
                                    class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-book me-1"></i> عرض القيد المحاسبي
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- جهة اليسار: الحالات + زر الطباعة -->
            <div class="col-lg-4 d-flex flex-column align-items-end gap-3">
                <!-- زر طباعة مصغر ومحاذى لليمين -->
                <button class="btn btn-success btn-bg action-btn"
                    onclick="printInvoice('{{ route('invoices.print', $invoice->id) }}')">
                    <i class="fas fa-print ms-1"></i> طباعة الفاتورة
                </button>

                <!-- الحالات: الدفع -->
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    @switch($invoice->payment_status)
                        @case(1)
                            <span class="status-badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>مدفوعة
                            </span>
                        @break

                        @case(2)
                            <span class="status-badge bg-warning">
                                <i class="fas fa-exclamation-circle me-1"></i>مدفوعة جزئياً
                            </span>
                        @break

                        @case(3)
                            <span class="status-badge bg-danger">
                                <i class="fas fa-times-circle me-1"></i>غير مدفوعة
                            </span>
                        @break

                        @case(4)
                            <span class="status-badge bg-info">
                                <i class="fas fa-info-circle me-1"></i>مستلمة
                            </span>
                        @break

                        @default
                            <span class="status-badge bg-secondary">
                                <i class="fas fa-question-circle me-1"></i>غير محدد
                            </span>
                    @endswitch
                </div>
            </div>
        </div>
    </div>

    <!-- كارد الأدوات -->
<div class="card">


        <div class="card-body">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- زر تعديل -->
                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-outline-primary btn-sm">
                    تعديل <i class="fa fa-edit ms-1"></i>
                </a>
                <div class="vr"></div>

                <!-- زر نسخ -->
                <button type="button" class="btn btn-outline-info btn-sm" onclick="copyInvoice()">
                    نسخ <i class="fa fa-copy ms-1"></i>
                </button>
                <div class="vr"></div>

                <!-- زر حذف -->
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteInvoice()">
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
                        <li><a class="dropdown-item" href="{{ route('invoices.generatePdf', $invoice->id) }}">
                                <i class="fa fa-file-pdf me-2 text-danger"></i>PDF طباعة</a></li>
                        <li><a class="dropdown-item" href="{{ route('invoices.print', $invoice->id) }}" target="_blank">
                                <i class="fa fa-print me-2 text-primary"></i>طباعة مباشرة</a></li>
                    </ul>
                </div>

                <!-- زر إضافة عملية دفع -->
                <a href="{{ route('paymentsClient.create', ['id' => $invoice->id, 'type' => 'invoice']) }}"
                    class="btn btn-outline-primary btn-sm">
                    إضافة عملية دفع <i class="fa fa-credit-card ms-1"></i>
                </a>

                <!-- زر القسائم -->
                <div class="dropdown">
                    <button class="btn btn-outline-info btn-sm" type="button" data-bs-toggle="dropdown">
                        القسائم <i class="fa fa-tags ms-1"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('invoices.label', $invoice->id) }}">
                                <i class="fa fa-file-pdf me-2 text-primary"></i>تحميل ملصق الطرد</a></li>
                        <li><a class="dropdown-item" href="{{ route('invoices.picklist', $invoice->id) }}">
                                <i class="fa fa-list me-2 text-primary"></i>قائمة الاستلام</a></li>
                        <li><a class="dropdown-item" href="{{ route('invoices.shipping_label', $invoice->id) }}">
                                <i class="fa fa-truck me-2 text-primary"></i>ملصق التوصيل</a></li>
                    </ul>
                </div>

                <!-- زر إرسال عبر -->
                <div class="dropdown">
                    <button class="btn btn-outline-success btn-sm" type="button" data-bs-toggle="dropdown">
                        إرسال عبر <i class="fa fa-share ms-1"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" target="_blank"
                                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $invoice->client->phone) }}?text={{ urlencode(
                                    'مرحبًا ' .
                                        $invoice->client->trade_name .
                                        ',' .
                                        "\n\n" .
                                        'يسعدنا إعلامكم بأن فاتورتكم أصبحت جاهزة. يمكنكم الاطلاع عليها من خلال الرابط التالي:' .
                                        "\n" .
                                        route('invoices.print', ['id' => $invoice->id, 'embed' => true]) .
                                        "\n\n" .
                                        'مع أطيب التحيات،' .
                                        "\n" .
                                        ($account_setting->trade_name ?? 'مؤسسة أعمال خاصة للتجارة'),
                                ) }}">
                                <i class="fab fa-whatsapp me-2 text-success"></i>واتساب
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- زر اتفاقية تقسيط -->
                <a href="{{ route('installments.create', ['id' => $invoice->id]) }}"
                    class="btn btn-outline-secondary btn-sm">
                    اتفاقية تقسيط <i class="fa fa-handshake ms-1"></i>
                </a>

                <!-- زر المرتجع -->
                <div class="dropdown">
                    <button class="btn btn-outline-warning btn-sm" type="button" data-bs-toggle="dropdown">
                        مرتجع <i class="fa fa-undo ms-1"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item"
                                href="{{ route('ReturnIInvoices.create', ['id' => $invoice->id]) }}">
                                <i class="fa fa-file-invoice me-2 text-primary"></i>إصدار فاتورة راجعة</a></li>
                        <li><a class="dropdown-item" href="{{ route('CreditNotes.create', ['id' => $invoice->id]) }}">
                                <i class="fa fa-credit-card me-2 text-primary"></i>إصدار إشعار دائن</a></li>
                    </ul>
                </div>

                <!-- زر إضافة ملاحظة أو مرفق -->
                <div class="dropdown">
                    <button class="btn btn-outline-dark btn-sm" type="button" data-bs-toggle="dropdown">
                        الملاحظات <i class="fa fa-paperclip ms-1"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="addNoteOrAttachment()">
                                <i class="fa fa-plus me-2 text-success"></i>إضافة ملاحظة جديدة</a></li>
                        <li><a class="dropdown-item" href="#" onclick="viewAllNotes()">
                                <i class="fa fa-list me-2 text-info"></i>عرض جميع الملاحظات</a></li>
                    </ul>
                </div>

                <!-- خيارات أخرى -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown">
                        خيارات أخرى <i class="fa fa-cog ms-1"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('appointments.create') }}">
                                <i class="fa fa-calendar-alt me-2 text-primary"></i>ترتيب موعد</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger"
                                href="{{ route('invoices.destroy', ['id' => $invoice->id]) }}">
                                <i class="fa fa-trash-alt me-2"></i>حذف</a></li>
                    </ul>
                </div>
            </div>
</div>
<div class="card">

            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#invoice-preview" role="tab">معاينة
                            الفاتورة</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#items" role="tab">المنتجات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#payments" role="tab">المدفوعات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#warehouse" role="tab">المخزون</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#returns" role="tab">الفواتير المرتجعة
                            ({{ $return_invoices->count() ?? 0 }})</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#profit" role="tab">ربح الفاتورة</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#signature" role="tab">التوقيع</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">سجل النشاطات</a>
                    </li>
                </ul>

                <div class="tab-content p-3">
                    <!-- تبويب معاينة الفاتورة -->

                    <!-- تبويب المنتجات -->
                    <div class="tab-pane active" id="invoice-preview" role="tabpanel">

                        <iframe src="{{ route('invoices.print', ['id' => $invoice->id, 'embed' => true]) }}"
                            class="pdf-iframe" frameborder="0"></iframe>
                    </div>
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
                                        <th>الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoice->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? 'غير متوفر' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->product->product_details->quantity ?? 'غير متوفر' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">لا توجد عناصر في الفاتورة</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>



                    <!-- تبويب الفواتير المرتجعة -->
                    <div class="tab-pane" id="returns" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>العميل</th>
                                        <th>الرقم الضريبي</th>
                                        <th>التاريخ</th>
                                        <th>المرجع</th>
                                        <th>المبلغ</th>
                                        <th>بواسطة</th>
                                        <th>الخيارات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($return_invoices ?? [] as $return)
                                        @php
                                            $currency = $account_setting->currency ?? 'SAR';
                                            $currencySymbol =
                                                $currency == 'SAR' || empty($currency)
                                                    ? '<img src="' .
                                                        asset('assets/images/Saudi_Riyal.svg') .
                                                        '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                                                    : $currency;
                                        @endphp
                                        <tr>
                                            <td><strong>#{{ $return->id }}</strong></td>
                                            <td>{{ $return->client ? ($return->client->trade_name ?: $return->client->first_name . ' ' . $return->client->last_name) : 'عميل غير معروف' }}
                                            </td>
                                            <td>{{ $return->client->tax_number ?? '-' }}</td>
                                            <td>{{ $return->created_at->format('H:i:s d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-undo-alt"></i>
                                                    #{{ $return->reference_number ?? '--' }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-danger">
                                                    {{ number_format($return->grand_total ?? $return->total, 2) }}
                                                    {!! $currencySymbol !!}
                                                </strong>
                                            </td>
                                            <td>{{ $return->createdByUser->name ?? 'غير محدد' }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item"
                                                            href="{{ route('ReturnIInvoices.edit', $return->id) }}">
                                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                        </a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('ReturnIInvoices.show', $return->id) }}">
                                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                        </a>
                                                        <form action="{{ route('invoices.destroy', $return->id) }}"
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
                                    @empty
                                        <tr>
                                            <td colspan="8">
                                                <div class="alert alert-warning m-0">
                                                    <i class="fas fa-exclamation-circle me-2"></i> لا توجد فواتير مرتجعة
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- تبويب ربح الفاتورة -->
                    <div class="tab-pane" id="profit" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>الكمية</th>
                                        <th>سعر البيع</th>
                                        <th>متوسط السعر</th>
                                        <th>الربح</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoice->items as $item)
                                        <tr>
                                            <td>
                                                #{{ $item->id }} {{ $item->product->name }}
                                                <div class="store_handle"></div>
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>
                                                @if ($item->product)
                                                    {{ number_format($item->product->sale_price, 2) }} ر.س
                                                @else
                                                    غير متوفر
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->product)
                                                    {{ number_format($item->product->purchase_price, 2) }} ر.س
                                                @else
                                                    غير متوفر
                                                @endif
                                            </td>
                                            <td>
                                                <span dir="ltr">
                                                    @if ($item->product)
                                                        {{ number_format(($item->product->sale_price - $item->product->purchase_price) * $item->quantity, 2) }}
                                                    @else
                                                        غير متوفر
                                                    @endif
                                                </span> ر.س
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">لا توجد عناصر في الفاتورة</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">الإجمالي</td>
                                        <td>
                                            <b>
                                                <span dir="ltr">
                                                    {{ number_format(
                                                        $invoice->items->sum(function ($item) {
                                                            return $item->product ? ($item->product->sale_price - $item->product->purchase_price) * $item->quantity : 0;
                                                        }),
                                                        2,
                                                    ) }}
                                                </span> ر.س
                                            </b>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- تبويب التوقيع -->
                    <div class="tab-pane" id="signature" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">التوقيع الإلكتروني</h4>

                                <!-- نموذج التوقيع -->
                                <form action="{{ route('invoices.signatures.store', $invoice->id) }}" method="POST"
                                    id="signature-form">
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="signer-name" class="form-label">الاسم الكامل *</label>
                                            <input type="text" name="signer_name" id="signer-name"
                                                class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="signer-role" class="form-label">الصفة *</label>
                                            <input type="text" name="signer_role" id="signer-role"
                                                class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="amount-paid" class="form-label">المبلغ المدفوع (في حالة دفع
                                                العميل)</label>
                                            <input type="number" name="amount_paid" id="amount-paid"
                                                class="form-control" step="0.01">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">التوقيع *</label>
                                        <div style="border: 1px dashed #ccc; height: 200px; position: relative;">
                                            <canvas id="signature-pad"
                                                style="width: 100%; height: 100%; touch-action: none;"></canvas>
                                            <div id="signature-guide"
                                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #ccc; font-size: 16px; pointer-events: none;">
                                                الرجاء التوقيع هنا
                                            </div>
                                        </div>
                                        <input type="hidden" name="signature_data" id="signature-data">
                                        <small class="text-muted">يمكنك التوقيع بإصبعك أو بالقلم</small>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="button" id="clear-signature" class="btn btn-danger">
                                            <i class="fas fa-eraser me-1"></i> مسح التوقيع
                                        </button>
                                        <button type="submit" id="save-signature" class="btn btn-success">
                                            <i class="fas fa-save me-1"></i> حفظ التوقيع
                                        </button>
                                    </div>
                                </form>

                                <!-- سجل التواقيع -->
                                <div class="mt-4">
                                    <h5>سجل التواقيع</h5>
                                    @if (isset($invoice->signatures) && $invoice->signatures->count() > 0)
                                        @foreach ($invoice->signatures as $signature)
                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>الاسم:</strong> {{ $signature->signer_name }}
                                                            @if ($signature->signer_role)
                                                                | <strong>الصفة:</strong> {{ $signature->signer_role }}
                                                            @endif
                                                            @if ($signature->amount_paid)
                                                                | <strong>المبلغ المدفوع:</strong>
                                                                {{ number_format($signature->amount_paid, 2) }} ر.س
                                                            @endif
                                                        </div>
                                                        <small>{{ $signature->created_at->format('Y-m-d H:i') }}</small>
                                                    </div>
                                                    <div class="mt-2 text-center">
                                                        <img src="{{ $signature->signature_data }}"
                                                            style="max-height: 80px; border: 1px solid #eee; background: white; padding: 5px;">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">لا توجد تواقيع مسجلة لهذه الفاتورة</div>
                                    @endif
                                </div>
                            </div>
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

        <!-- نماذج مخفية للعمليات -->
        <form id="deleteForm" action="{{ route('invoices.destroy', $invoice->id) }}" method="POST"
            style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endsection

    @section('scripts')
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

        <script>
            $(document).ready(function() {
                // تهيئة Toastr
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "progressBar": true,
                    "positionClass": "toast-top-left",
                    "preventDuplicates": true,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut",
                    "rtl": true,
                    "escapeHtml": true
                };

                // تهيئة لوحة التوقيع
                const canvas = document.getElementById('signature-pad');
                if (canvas) {
                    const signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255,255,255)',
                        penColor: 'rgb(0,0,0)',
                        minWidth: 0.7,
                        maxWidth: 2,
                        throttle: 0,
                        onBegin: () => {
                            const guide = document.getElementById('signature-guide');
                            if (guide) guide.style.display = 'none';
                        }
                    });

                    // ضبط حجم Canvas
                    function resizeCanvas() {
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        const width = canvas.offsetWidth;
                        const height = canvas.offsetHeight;

                        if (width === 0 || height === 0) return;

                        const data = !signaturePad.isEmpty() ? signaturePad.toData() : null;

                        canvas.width = width * ratio;
                        canvas.height = height * ratio;
                        canvas.getContext('2d').scale(ratio, ratio);

                        canvas.style.width = width + 'px';
                        canvas.style.height = height + 'px';

                        if (data) {
                            signaturePad.fromData(data);
                        }
                    }

                    window.addEventListener('resize', resizeCanvas);
                    resizeCanvas();

                    // مسح التوقيع
                    document.getElementById('clear-signature')?.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (!signaturePad.isEmpty()) {
                            Swal.fire({
                                title: 'هل أنت متأكد؟',
                                text: "سيتم مسح التوقيع الحالي تماماً!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'نعم، امسح!',
                                cancelButtonText: 'إلغاء'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    signaturePad.clear();
                                    const guide = document.getElementById('signature-guide');
                                    if (guide) guide.style.display = 'block';
                                    toastr.success('تم مسح التوقيع بنجاح', 'عملية ناجحة');
                                }
                            });
                        } else {
                            toastr.info('لا يوجد توقيع لمسحه', 'ملاحظة');
                        }
                    });

                    // حفظ التوقيع
                    document.getElementById('signature-form')?.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const signerName = document.getElementById('signer-name').value.trim();
                        const signerRole = document.getElementById('signer-role').value.trim();

                        if (!signerName) {
                            toastr.error('الرجاء إدخال الاسم الكامل للموقع', 'حقل مطلوب');
                            return;
                        }

                        if (!signerRole) {
                            toastr.error('الرجاء إدخال صفة الموقع', 'حقل مطلوب');
                            return;
                        }

                        if (signaturePad.isEmpty()) {
                            toastr.error('الرجاء تقديم التوقيع أولاً', 'توقيع مطلوب');
                            return;
                        }

                        Swal.fire({
                            title: 'تأكيد الحفظ',
                            text: "هل أنت متأكد من حفظ هذا التوقيع؟",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'نعم، احفظه!',
                            cancelButtonText: 'إلغاء'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const signatureData = signaturePad.toDataURL();
                                document.getElementById('signature-data').value = signatureData;

                                Swal.fire({
                                    title: 'جاري الحفظ',
                                    html: 'يرجى الانتظار...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                const form = this;
                                axios.post(form.action, new FormData(form))
                                    .then(response => {
                                        Swal.close();
                                        if (response.data.success) {
                                            toastr.success('تم حفظ التوقيع بنجاح', 'نجاح');
                                            setTimeout(() => {
                                                window.location.reload();
                                            }, 2000);
                                        } else {
                                            toastr.error(response.data.message ||
                                                'حدث خطأ أثناء الحفظ', 'خطأ');
                                        }
                                    })
                                    .catch(error => {
                                        Swal.close();
                                        let errorMessage = 'فشل في الحفظ. يرجى المحاولة لاحقًا.';
                                        if (error.response && error.response.data && error.response
                                            .data.errors) {
                                            const errors = error.response.data.errors;
                                            errorMessage = Object.values(errors).join('<br>');
                                        }
                                        toastr.error(errorMessage, 'خطأ');
                                    });
                            }
                        });
                    });
                }
            });

            // دالة حذف الفاتورة
            function deleteInvoice() {
                Swal.fire({
                    title: 'حذف فاتورة المبيعات',
                    text: 'هل أنت متأكد من حذف فاتورة المبيعات رقم "{{ $invoice->id }}"؟ هذا الإجراء لا يمكن التراجع عنه!',
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

            // دالة نسخ الفاتورة
            function copyInvoice() {
                Swal.fire({
                    title: 'نسخ فاتورة المبيعات',
                    text: 'سيتم إنشاء نسخة جديدة من فاتورة المبيعات',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#17a2b8',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'تأكيد النسخ',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('invoices.create') }}?copy_from={{ $invoice->id }}";
                    }
                });
            }

            // دالة طباعة الفاتورة
            function printInvoice(url) {
                window.open(url, '_blank');
            }

            // دالة إضافة ملاحظة
            function addNoteOrAttachment() {
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
                                <option value="مراجعة الفاتورة">مراجعة الفاتورة</option>
                                <option value="تأكيد الدفع">تأكيد الدفع</option>
                                <option value="تأكيد التسليم">تأكيد التسليم</option>
                                <option value="التواصل مع العميل">التواصل مع العميل</option>
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

                        return {
                            process,
                            note,
                            noteDate,
                            noteTime,
                            attachment
                        };
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

                // يجب تعديل هذا المسار حسب مسارات فواتير المبيعات في مشروعك
fetch(`{{ route('invoices.addNote', $invoice->id) }}`, {
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
                Swal.fire({
                    title: 'جاري تحميل الملاحظات...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`{{ route('invoices.getNotes', $invoice->id) }}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            let notesHtml = '';

                            if (data.notes && data.notes.length > 0) {
                                data.notes.forEach((note, index) => {
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
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteNote(${note.id})" title="حذف الملاحظة">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <div class="mt-3 p-3 bg-light rounded">
                                            <p class="mb-0 text-dark">${description}</p>
                                        </div>

                                        ${note.has_attachment ? `
                                                    <div class="mt-3 pt-2 border-top">
                                                        <a href="${note.attachment_url}" target="_blank" class="btn btn-outline-info btn-sm">
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
                        fetch(`{{ route('invoices.deleteNote', ':id') }}`.replace(':id', noteId), {

                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('تم الحذف!', 'تم حذف الملاحظة بنجاح', 'success');
                                    viewAllNotes();
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
                const buttons = document.querySelectorAll('.action-btn');
                buttons.forEach(button => {
                    button.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-2px) scale(1.02)';
                    });

                    button.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0) scale(1)';
                    });
                });
            });
        </script>
    @endsection
