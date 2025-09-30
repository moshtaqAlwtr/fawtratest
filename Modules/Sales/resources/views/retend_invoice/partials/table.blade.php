{{-- ملف: resources/views/sales/retend_invoice/partials/table.blade.php --}}

@if ($return->count() > 0)
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
                    <th>المرجع</th>
                    <th>المبلغ الإجمالي</th>
                    <th style="width: 10%">خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($return as $retur)
                    @php
                        $currency = $account_setting->currency ?? 'SAR';
                        $currencySymbol = $currency == 'SAR' || empty($currency)
                            ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                            : $currency;
                    @endphp

                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input invoice-checkbox" value="{{ $retur->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2" style="background-color: #dc3545">
                                    <span class="avatar-content">#</span>
                                </div>
                                <div>
                                    #{{ $retur->id }}
                                    <div class="text-muted small">فاتورة مرتجعة</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="client-info">
                                <strong>
                                    {{ $retur->client ? ($retur->client->trade_name ?: $retur->client->first_name . ' ' . $retur->client->last_name) : 'عميل غير معروف' }}
                                </strong>
                                @if ($retur->client && $retur->client->tax_number)
                                    <div class="text-muted small">الرقم الضريبي: {{ $retur->client->tax_number }}</div>
                                @endif
                                @if ($retur->client && $retur->client->full_address)
                                    <div class="text-muted small">{{ $retur->client->full_address }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            {{ $retur->created_at ? $retur->created_at->format('H:i:s d/m/Y') : '' }}
                            <br>
                            <small class="text-muted">أضيفت بواسطة: {{ $retur->createdByUser->name ?? 'غير محدد' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-warning">
                                <i class="fas fa-undo-alt"></i> #{{ $retur->reference_number ?? '--' }}
                            </span>
                        </td>
                        <td>
                            <strong class="text-danger">
                                {{ number_format($retur->grand_total ?? $retur->total, 2) }}
                                {!! $currencySymbol !!}
                            </strong>
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('ReturnIInvoices.show', $retur->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                        <a class="dropdown-item" href="{{ route('ReturnIInvoices.edit', $retur->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                        <a class="dropdown-item" href="{{ route('ReturnIInvoices.print', $retur->id) }}">
                                            <i class="fa fa-print me-2 text-dark"></i>طباعة
                                        </a>
                                        <a class="dropdown-item" href="">
                                            <i class="fa fa-envelope me-2 text-warning"></i>إرسال إلى العميل
                                        </a>
                                        <a class="dropdown-item text-danger delete-invoice" href="#" data-id="{{ $retur->id }}">
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
    @include('sales::retend_invoice.partials.pagination', ['return' => $return])
@else
    <div class="alert alert-info text-center" role="alert">
        <i class="fa fa-info-circle me-2"></i>
        <p class="mb-0">لا يوجد فواتير مرتجعة تطابق معايير البحث</p>
    </div>
@endif