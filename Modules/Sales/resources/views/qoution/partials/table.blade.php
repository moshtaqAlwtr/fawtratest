{{-- ملف: resources/views/sales/qoution/partials/table.blade.php --}}

@if ($quotes->count() > 0)
    <div class="table">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th style="width: 5%">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                    <th>رقم عرض السعر</th>
                    <th>العميل</th>
                    <th>التاريخ</th>
                    <th>المبلغ الإجمالي</th>
                    <th>الحالة</th>
                    <th style="width: 10%">خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($quotes as $quote)
                    @php
                        $currency = $account_setting->currency ?? 'SAR';
                        $currencySymbol = $currency == 'SAR' || empty($currency)
                            ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                            : $currency;

                        $statusClass = $quote->status == 1 ? 'success' : 'info';
                        $statusText = $quote->status == 1 ? 'مفتوح' : 'مغلق';
                    @endphp

                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input quote-checkbox" value="{{ $quote->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2" style="background-color: #007bff">
                                    <span class="avatar-content">#</span>
                                </div>
                                <div>
                                    #{{ $quote->id }}
                                    <div class="text-muted small">عرض سعر</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="client-info">
                                <strong>
                                    {{ $quote->client ? ($quote->client->trade_name ?: $quote->client->first_name . ' ' . $quote->client->last_name) : 'عميل غير معروف' }}
                                </strong>
                                @if ($quote->client && $quote->client->tax_number)
                                    <div class="text-muted small">الرقم الضريبي: {{ $quote->client->tax_number }}</div>
                                @endif
                                @if ($quote->client && $quote->client->full_address)
                                    <div class="text-muted small">{{ $quote->client->full_address }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            {{ $quote->created_at ? $quote->created_at->format($account_setting->time_formula ?? 'Y-m-d H:i') : '' }}
                            <br>
                            <small class="text-muted">أضيفت بواسطة: {{ $quote->creator->name ?? 'غير محدد' }}</small>
                        </td>
                        <td>
                            <strong class="text-danger fs-6">
                                {{ number_format($quote->grand_total ?? $quote->total, 2) }} {!! $currencySymbol !!}
                            </strong>
                        </td>
                        <td>
                            <span class="badge bg-{{ $statusClass }}">
                                <i class="fas fa-circle me-1"></i> {{ $statusText }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('questions.show', $quote->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                        <a class="dropdown-item" href="{{ route('questions.edit', $quote->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                        <a class="dropdown-item" href="{{ route('questions.pdf', $quote->id) }}">
                                            <i class="fa fa-file-pdf me-2 text-danger"></i>PDF
                                        </a>
                                        <a class="dropdown-item" href="{{ route('questions.pdf', $quote->id) }}">
                                            <i class="fa fa-print me-2 text-dark"></i>طباعة
                                        </a>
                                        <a class="dropdown-item" href="{{ route('questions.email', $quote->id) }}">
                                            <i class="fa fa-envelope me-2 text-warning"></i>إرسال إلى العميل
                                        </a>
                                        <a class="dropdown-item text-danger delete-quote" href="#" data-id="{{ $quote->id }}">
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
    @include('sales::qoution.partials.pagination', ['quotes' => $quotes])
@else
    <div class="alert alert-info text-center" role="alert">
        <i class="fa fa-info-circle me-2"></i>
        <p class="mb-0">لا يوجد عروض أسعار تطابق معايير البحث</p>
    </div>
@endif
