@extends('master')
@section('css')
@endsection

@section('title')
    تفاصيل الاشتراك
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الاشتراكات</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تفاصيل الاشتراك</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
  @php
                                            $currency = $account_setting->currency ?? 'SAR';
                                            $currencySymbol = $currency == 'SAR' || empty($currency) ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">' : $currency;
                                        @endphp
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">تفاصيل الاشتراك</h3>
                    <div>
                        <a href="{{ route('periodic_invoices.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-right ml-2"></i>رجوع
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#general-info">معلومات عامة</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#next-invoice">الفاتورة القادمة</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#previous-invoices">الفواتير السابقة ({{ $periodicInvoice->instances()->whereNotNull('invoice_id')->count() }})</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- معلومات عامة -->
                        <div class="tab-pane fade show active" id="general-info">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <td style="width: 200px;">عدد الفواتير التي سيتم إصدارها:</td>
                                        <td>{{ $periodicInvoice->repeat_count }}</td>
                                        <td style="width: 200px;">تاريخ أول فاتورة:</td>
                                        <td>{{ $periodicInvoice->first_invoice_date }}</td>
                                    </tr>
                                    <tr>
                                        <td>فاتورة منشأة بالفعل:</td>
                                        <td>{{ $periodicInvoice->instances()->whereNotNull('invoice_id')->count() }}</td>
                                        <td>تاريخ آخر فاتورة:</td>
                                        <td>{{ $periodicInvoice->last_invoice_date }}</td>
                                    </tr>
                                    <tr>
                                        <td>الفواتير المتبقية:</td>
                                        <td>{{ $periodicInvoice->instances()->whereNull('invoice_id')->count() }}</td>
                                        <td>تاريخ الفاتورة المقبلة:</td>
                                        <td>{{ $periodicInvoice->next_invoice_date }}</td>
                                    </tr>
                                    <tr>
                                        <td>إجمالي الغير مدفوع:</td>
                                        <td>{{ number_format($periodicInvoice->grand_total ?? 0, 2) }} {!! $currencySymbol !!}</td>
                                        <td>إجمالي المدفوع:</td>
                                        <td>{{ number_format($periodicInvoice->total ?? 0, 2) }} {!! $currencySymbol !!}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- الفاتورة القادمة -->
                    <!-- الفاتورة القادمة -->
<div class="tab-pane fade" id="next-invoice">
    @php
        $nextInstance = $periodicInvoice->instances
            ->where('status', 1) // pending
            ->sortBy('due_date')
            ->first();
    @endphp

    @if ($nextInstance)
        <div class="alert alert-success">
            <strong>تاريخ الفاتورة القادمة:</strong>
            {{ \Carbon\Carbon::parse($nextInstance->due_date)->format('Y-m-d') }}
            <br>
            <strong>الحالة:</strong> لم تُصدر بعد (pending)
        </div>
    @else
        <div class="alert alert-warning" style="background-color: #fff3cd; border: 1px solid #ffeeba;">
            <i class="fas fa-exclamation-circle me-2"></i> لا توجد فواتير قادمة، ربما انتهى الاشتراك.
        </div>
        <div class="text-muted" style="font-size: 14px;">
            يمكنك تعديل عدد مرات التكرار أو تفعيل الاشتراك من جديد.
        </div>
    @endif
</div>


                        <!-- الفواتير السابقة -->
                        <div class="tab-pane fade" id="previous-invoices">
                            <div class="alert alert-info mb-3">
                                تم إنشاء الفاتورة بواسطة "{{ $periodicInvoice->repeat_count }}"
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>المعرف</th>
                                            <th>التاريخ</th>
                                            <th>تاريخ الإصدار</th>
                                            <th>الحالة</th>
                                            <th>آخر إرسال</th>
                                            <th>الإجمالي</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
    @php $hasInvoices = false; @endphp

    @foreach($periodicInvoice->instances as $instance)
        @if ($instance->invoice)
            @php $hasInvoices = true; @endphp
            <tr>
                <td>{{ str_pad($instance->invoice->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $instance->invoice->invoice_date }}</td>
                <td>{{ $instance->invoice->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($instance->invoice->status == 'draft')
                        <span class="badge badge-warning">غير مؤكدة</span>
                    @else
                        <span class="badge badge-success">مؤكدة</span>
                    @endif
                </td>
                <td>{{ $instance->invoice->last_sent_at ?? 'لم ترسل' }}</td>
                <td>{{ number_format($instance->invoice->grand_total, 2) }} {!! $currencySymbol !!}</td>
                <td class="text-center">
                    <a href="{{ route('invoices.show', $instance->invoice->id) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('invoices.print', $instance->invoice->id) }}" class="btn btn-sm btn-outline-info" title="PDF">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </td>
            </tr>
        @endif
    @endforeach

    @if (!$hasInvoices)
        <tr>
            <td colspan="7" class="text-center text-muted">
                لم يتم إصدار أي فواتير حتى الآن لهذا الاشتراك.
            </td>
        </tr>
    @endif
</tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
