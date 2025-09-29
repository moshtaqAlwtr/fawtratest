{{-- ملف: resources/views/sales/invoices/partials/table.blade.php --}}

@if ($invoices->count() > 0)
    <div class="table">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th style="width: 5%">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                    <th>رقم الفاتورة</th>
                    <th>العميل</th>
                    <th>التاريخ</th>
                    <th>المبلغ الإجمالي</th>
                    <th>الحالة</th>
                    <th style="width: 10%">خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    @php
                        $returnedInvoice = \App\Models\Invoice::where('type', 'returned')
                            ->where('reference_number', $invoice->id)
                            ->first();

                        $payments = \App\Models\PaymentsProcess::where('invoice_id', $invoice->id)
                            ->where('type', 'client payments')
                            ->orderBy('created_at', 'desc')
                            ->get();

                        $currency = $account_setting->currency ?? 'SAR';
                        $currencySymbol = $currency == 'SAR' || empty($currency)
                            ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                            : $currency;

                        $net_due = $invoice->due_value - ($invoice->returned_payment ?? 0);
                    @endphp

                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input invoice-checkbox" value="{{ $invoice->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2" style="background-color: #28a745">
                                    <span class="avatar-content">#</span>
                                </div>
                                <div>
                                    #{{ $invoice->id }}
                                    <div class="text-muted small">فاتورة مبيعات</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="client-info">
                                <strong>
                                    {{ $invoice->client ? ($invoice->client->trade_name ?: $invoice->client->first_name . ' ' . $invoice->client->last_name) : 'عميل غير معروف' }}
                                </strong>
                                @if ($invoice->client && $invoice->client->tax_number)
                                    <div class="text-muted small">الرقم الضريبي: {{ $invoice->client->tax_number }}</div>
                                @endif
                                @if ($invoice->client && $invoice->client->full_address)
                                    <div class="text-muted small">{{ $invoice->client->full_address }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            {{ $invoice->created_at ? $invoice->created_at->format($account_setting->time_formula ?? 'Y-m-d H:i') : '' }}
                            <br>
                            <small class="text-muted">أضيفت بواسطة: {{ $invoice->createdByUser->name ?? 'غير محدد' }}</small>
                            @if($invoice->employee)
                                <br><small class="text-muted">للمندوب: {{ $invoice->employee->first_name ?? 'غير محدد' }}</small>
                            @endif
                        </td>
                        <td>
                            {{ number_format($invoice->grand_total ?? $invoice->total, 2) }} {!! $currencySymbol !!}
                            @if ($invoice->due_value > 0)
                                <br>
                                <small class="text-danger">(المبلغ المستحق: {{ number_format($net_due, 2) }}) {!! $currencySymbol !!}</small>
                            @endif
                        </td>
                        <td>
                            {{-- Badge للحالة العامة --}}
                            @if ($returnedInvoice)
                                <span class="badge bg-danger">مرتجع</span>
                            @elseif ($invoice->type == 'normal' && $payments->count() == 0)
                                <span class="badge bg-secondary">أنشئت فاتورة</span>
                            @endif

                            @if ($payments->count() > 0)
                                <span class="badge bg-success">أضيفت عملية دفع</span>
                            @endif

                            <br>

                            {{-- Badge لحالة الدفع --}}
                            @php
                                $statusMap = [
                                    1 => ['class' => 'success', 'text' => 'مدفوعة بالكامل'],
                                    2 => ['class' => 'info', 'text' => 'مدفوعة جزئياً'],
                                    3 => ['class' => 'danger', 'text' => 'غير مدفوعة'],
                                    4 => ['class' => 'secondary', 'text' => 'مستلمة'],
                                ];
                                $status = $statusMap[$invoice->payment_status] ?? ['class' => 'dark', 'text' => 'غير معروفة'];
                            @endphp

                            <span class="badge bg-{{ $status['class'] }} mt-1">{{ $status['text'] }}</span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('invoices.show', $invoice->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                        <a class="dropdown-item" href="{{ route('invoices.edit', $invoice->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                        <a class="dropdown-item" href="{{ route('invoices.generatePdf', $invoice->id) }}">
                                            <i class="fa fa-file-pdf me-2 text-danger"></i>PDF
                                        </a>
                                        <a class="dropdown-item" href="{{ route('invoices.generatePdf', $invoice->id) }}">
                                            <i class="fa fa-print me-2 text-dark"></i>طباعة
                                        </a>
                                        <a class="dropdown-item" href="{{ route('invoices.send', $invoice->id) }}">
                                            <i class="fa fa-envelope me-2 text-warning"></i>إرسال إلى العميل
                                        </a>
                                        <a class="dropdown-item" href="{{ route('paymentsClient.create', ['id' => $invoice->id]) }}">
                                            <i class="fa fa-credit-card me-2 text-info"></i>إضافة عملية دفع
                                        </a>
                                        <a class="dropdown-item text-danger delete-invoice" href="#" data-id="{{ $invoice->id }}">
                                            <i class="fa fa-trash me-2"></i>حذف
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

    {{-- الترقيم --}}
    @include('sales::invoices.partials.pagination', ['invoices' => $invoices])
@else
    <div class="alert alert-info text-center" role="alert">
        <i class="fa fa-info-circle me-2"></i>
        <p class="mb-0">لا يوجد فواتير تطابق معايير البحث</p>
    </div>
@endif