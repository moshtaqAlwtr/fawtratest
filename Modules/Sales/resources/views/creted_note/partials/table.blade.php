{{-- ملف: resources/views/sales/credit_notes/partials/table.blade.php --}}

@if ($credits->count() > 0)
    <div class="table">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th style="width: 5%">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                    <th>رقم الإشعار</th>
                    <th>العميل</th>
                    <th>التاريخ</th>
                    <th>المبلغ الإجمالي</th>
                    <th>الحالة</th>
                    <th style="width: 10%">خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($credits as $credit)
                    @php
                        $currency = $account_setting->currency ?? 'SAR';
                        $currencySymbol = $currency == 'SAR' || empty($currency)
                            ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                            : $currency;

                        $statusMap = [
                            1 => ['class' => 'success', 'text' => 'مسودة'],
                            2 => ['class' => 'warning', 'text' => 'قيد الانتظار'],
                            3 => ['class' => 'primary', 'text' => 'معتمد'],
                            4 => ['class' => 'info', 'text' => 'تم التحويل إلى فاتورة'],
                            5 => ['class' => 'danger', 'text' => 'ملغى'],
                        ];
                        $status = $statusMap[$credit->status] ?? ['class' => 'secondary', 'text' => 'غير معروف'];
                    @endphp

                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input credit-checkbox" value="{{ $credit->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2" style="background-color: #17a2b8">
                                    <span class="avatar-content">#</span>
                                </div>
                                <div>
                                    #{{ $credit->credit_number }}
                                    <div class="text-muted small">إشعار دائن</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="client-info">
                                <strong>
                                    {{ $credit->client ? ($credit->client->trade_name ?: $credit->client->first_name . ' ' . $credit->client->last_name) : 'عميل غير معروف' }}
                                </strong>
                                @if ($credit->client && $credit->client->tax_number)
                                    <div class="text-muted small">الرقم الضريبي: {{ $credit->client->tax_number }}</div>
                                @endif
                                @if ($credit->client && $credit->client->full_address)
                                    <div class="text-muted small">{{ $credit->client->full_address }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            {{ $credit->credit_date ?? '--' }}
                            <br>
                            <small class="text-muted">أضيفت بواسطة: {{ $credit->createdBy->name ?? 'غير محدد' }}</small>
                        </td>
                        <td>
                            <strong class="text-danger">
                                {{ number_format($credit->grand_total, 2) }} {!! $currencySymbol !!}
                            </strong>
                        </td>
                        <td>
                            <span class="badge bg-{{ $status['class'] }}">
                                <i class="fas fa-circle me-1"></i> {{ $status['text'] }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('CreditNotes.show', $credit->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                        <a class="dropdown-item" href="{{ route('CreditNotes.edit', $credit->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                        <a class="dropdown-item" href="{{ route('CreditNotes.generatePdf', $credit->id) }}">
                                            <i class="fa fa-file-pdf me-2 text-danger"></i>PDF
                                        </a>
                                        <a class="dropdown-item" href="{{ route('CreditNotes.generatePdf', $credit->id) }}">
                                            <i class="fa fa-print me-2 text-dark"></i>طباعة
                                        </a>
                                        <a class="dropdown-item" href="{{ route('CreditNotes.send', $credit->id) }}">
                                            <i class="fa fa-envelope me-2 text-warning"></i>إرسال إلى العميل
                                        </a>
                                        <a class="dropdown-item text-danger delete-credit" href="#" data-id="{{ $credit->id }}">
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
    @include('sales::credit_notes.partials.pagination', ['credits' => $credits])
@else
    <div class="alert alert-info text-center" role="alert">
        <i class="fa fa-info-circle me-2"></i>
        <p class="mb-0">لا توجد إشعارات دائنة تطابق معايير البحث</p>
    </div>
@endif