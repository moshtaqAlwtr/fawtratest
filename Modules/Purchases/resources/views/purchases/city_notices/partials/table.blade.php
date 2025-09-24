{{-- ملف: resources/views/purchases/invoices_purchase/partials/table.blade.php --}}

@if ($cityNotices->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th style="width: 5%">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                    <th>رقم الفاتورة</th>
                    <th>المورد</th>
                    <th>التاريخ</th>
                    <th>المبلغ الإجمالي</th>
                    <th>حالة الاستلام</th>
                    <th style="width: 10%">خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cityNotices as $invoice)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input order-checkbox" value="{{ $invoice->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2" style="background-color: #4B6584">
                                    <span
                                        class="avatar-content">{{ Str::upper(substr($invoice->reference_number, 0, 1)) }}</span>
                                </div>
                                <div>
                                    {{ $invoice->reference_number }}
                                    <div class="text-muted small">#{{ $invoice->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $invoice->supplier->trade_name ?? 'غير محدد' }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d H:i') }}<br>
                            <small class="text-muted">أضيفت بواسطة: {{ $invoice->creator->name ?? '---' }}</small>
                        </td>


                        @php
                            $currency = $account_setting->currency ?? 'SAR';
                            $currencySymbol =
                                $currency == 'SAR' || empty($currency)
                                    ? '<img src="' .
                                        asset('assets/images/Saudi_Riyal.svg') .
                                        '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                                    : $currency;
                        @endphp
                        <td>
                            {{ number_format($invoice->grand_total, 2) }} {!! $currencySymbol !!}
                            <br>

                        </td>

                        <td>
                            {{-- حالة الدفع --}}
                            @if ($invoice->type == 'City Notice')
                                <span class="badge bg-success"> اشعار مدين </span>
                            @endif

                            <br>

                            {{-- حالة الاستلام --}}
                            @if ($invoice->receiving_status == 'received')
                                <span class="badge bg-success mt-1">تم الاستلام</span>
                            @elseif ($invoice->receiving_status == 'partially_receiv')
                                <span class="badge bg-danger mt-1">تم الاستلام جزئياً</span>
                            @elseif ($invoice->receiving_status == 'not_received')
                                <span class="badge bg-warning mt-1">لم يتم الاستلام</span>
                            @endif
                        </td>

                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('CityNotices.show', $invoice->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                        <a class="dropdown-item" href="{{ route('CityNotices.edit', $invoice->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                        <a class="dropdown-item" href="" target="_blank">
                                            <i class="fa fa-print me-2 text-info"></i>طباعة
                                        </a>
                                        <a class="dropdown-item text-danger delete-city-notice" href="#"
                                            data-id="{{ $invoice->id }}">
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
    @include('purchases::purchases.city_notices.partials.pagination', [
        'cityNotices' => $cityNotices,
    ])
@else
    <div class="alert alert-info text-center" role="alert">
        <i class="fa fa-info-circle me-2"></i>
        <p class="mb-0">لا يوجد مرتجعات مشتريات تطابق معايير البحث</p>
    </div>
@endif
