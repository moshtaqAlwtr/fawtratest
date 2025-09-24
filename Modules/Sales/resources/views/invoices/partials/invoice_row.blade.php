<tr class="align-middle invoice-row"
    data-url="{{ route('invoices.show', $invoice->id) }}"
    style="cursor: pointer;" data-status="{{ $invoice->payment_status }}">
    <td class="text-center border-start">
        <span class="invoice-number">#{{ $invoice->id }}</span>
    </td>
    <td>
        <div class="client-info">
            <div class="client-name mb-2">
                <i class="fas fa-user text-primary me-1"></i>
                <strong>{{ $invoice->client ? ($invoice->client->trade_name ?: $invoice->client->first_name . ' ' . $invoice->client->last_name) : 'عميل غير معروف' }}</strong>
            </div>
            @if ($invoice->client && $invoice->client->tax_number)
                <div class="tax-info mb-1">
                    <i class="fas fa-hashtag text-muted me-1"></i>
                    <span class="text-muted small">الرقم الضريبي: {{ $invoice->client->tax_number }}</span>
                </div>
            @endif
            @if ($invoice->client && $invoice->client->full_address)
                <div class="address-info">
                    <i class="fas fa-map-marker-alt text-muted me-1"></i>
                    <span class="text-muted small">{{ $invoice->client->full_address }}</span>
                </div>
            @endif
        </div>
    </td>
    <td>
        <div class="date-info mb-2">
            <i class="fas fa-calendar text-info me-1"></i>
            {{ $invoice->created_at ? $invoice->created_at->format($account_setting->time_formula ?? 'H:i:s d/m/Y') : '' }}
        </div>
        <div class="creator-info">
            <i class="fas fa-user text-muted me-1"></i>
            <span class="text-muted small">بواسطة: {{ $invoice->createdByUser->name ?? 'غير محدد' }}</span>
            <span class="text-muted small"> للمندوب {{ $invoice->employee->first_name ?? 'غير محدد' }} </span>
        </div>
    </td>
    <td>
        <div class="d-flex flex-column gap-2" style="margin-bottom: 60px">
            @php
                $payments = \App\Models\PaymentsProcess::where('invoice_id', $invoice->id)
                    ->where('type', 'client payments')
                    ->orderBy('created_at', 'desc')
                    ->get();
                $returnedInvoice = \App\Models\Invoice::where('type', 'returned')
                    ->where('reference_number', $invoice->id)
                    ->first();
            @endphp

            @if ($returnedInvoice)
                <span class="badge bg-danger text-white"><i class="fas fa-undo me-1"></i>مرتجع</span>
            @elseif ($invoice->type == 'normal' && $payments->count() == 0)
                <span class="badge bg-secondary text-white"><i class="fas fa-file-invoice me-1"></i>أنشئت فاتورة</span>
            @endif

            @if ($payments->count() > 0)
                <span class="badge bg-success text-white"><i class="fas fa-check-circle me-1"></i>أضيفت عملية دفع</span>
            @endif
        </div>
    </td>
    <td>
        @php
            $statusClass = match ($invoice->payment_status) {
                1 => 'success',
                2 => 'info',
                3 => 'danger',
                4 => 'secondary',
                default => 'dark',
            };
            $statusIcon = match ($invoice->payment_status) {
                1 => '<i class="fas fa-check-circle"></i>',
                2 => '<i class="fas fa-adjust"></i>',
                3 => '<i class="fas fa-times-circle"></i>',
                4 => '<i class="fas fa-hand-holding-usd"></i>',
                default => '<i class="fas fa-question-circle"></i>',
            };
            $statusText = match ($invoice->payment_status) {
                1 => 'مدفوعة بالكامل',
                2 => 'مدفوعة جزئياً',
                3 => 'غير مدفوعة',
                4 => 'مستلمة',
                default => 'غير معروفة',
            };
            $currency = $account_setting->currency ?? 'SAR';
            $currencySymbol = $currency == 'SAR' || empty($currency)
                ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                : $currency;
            $net_due = $invoice->due_value - $invoice->returned_payment;
        @endphp

        <div class="text-center">
            <span class="badge bg-{{ $statusClass }} text-white status-badge">
                {!! $statusIcon !!} {{ $statusText }}
            </span>
        </div>

        <div class="amount-info text-center mb-2">
            <h6 class="amount mb-1">
                {{ number_format($invoice->grand_total ?? $invoice->total, 2) }}
                <small class="currency">{!! $currencySymbol !!}</small>
            </h6>

            @if ($returnedInvoice)
                <span class="text-danger"> <i class="fas fa-undo-alt"></i> مرتجع :
                    {{ number_format($invoice->returned_payment, 2) ?? '' }}
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
            <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v" type="button"
                id="dropdownMenuButton{{ $invoice->id }}" data-bs-toggle="dropdown"
                data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('invoices.edit', $invoice->id) }}">
                    <i class="fa fa-edit me-2 text-success"></i>تعديل
                </a>
                <a class="dropdown-item" href="{{ route('invoices.show', $invoice->id) }}">
                    <i class="fa fa-eye me-2 text-primary"></i>عرض
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
                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="d-inline">
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
