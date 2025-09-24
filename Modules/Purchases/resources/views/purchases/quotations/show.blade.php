@extends('master')

@section('title')
    عرض طلب عرض سعر
@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض طلب عرض سعر</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>

                            </li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <div class="card">
        <div class="card-body p-1">
            <div class="d-flex justify-content-between align-items-center">
                <!-- الجانب الأيمن - حالة الطلب والرقم -->
                <div class="d-flex align-items-center gap-3">
                    <h5 class="mb-0">طلب عرض سعر {{ $purchaseQuotation->id }} #{{ $purchaseQuotation->code }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-circle text-warning" style="font-size: 8px;"></i>
                        @if ($purchaseQuotation->status == 1)
                            <span class="badge bg-warning">تحت المراجعة </span>
                        @elseif ($purchaseQuotation->status == 2)
                            <span class="badge bg-success">تم الموافقة علية </span>
                        @elseif ($purchaseQuotation->status == 3)
                            <span class="badge bg-danger">مرفوض</span>
                        @endif
                    </div>
                </div>

                <!-- الجانب الأيسر - أزرار الموافقة والرفض -->
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-exchange-alt me-1"></i>
                        تحويل إلى عرض مشتريات
                    </button>
                    <ul class="dropdown-menu">
                        @forelse($suppliers as $supplier)
                            <li>
                                <a class="dropdown-item"
                                    href="{{ route('pricesPurchase.create', ['supplier_id' => $supplier->id, 'quotation_id' => $purchaseQuotation->id]) }}">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div>{{ $supplier->name }}</div>
                                            <small class="text-muted">{{ $supplier->trade_name }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li>
                                <span class="dropdown-item text-muted">
                                    لا يوجد موردين
                                </span>
                            </li>
                        @endforelse
                    </ul>

                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2">
            <a href="{{ route('Quotations.edit', $purchaseQuotation->id) }}" class="btn btn-outline-primary btn-sm">
                تعديل <i class="fa fa-edit ms-1"></i>
            </a>
            <div class="vr"></div>

            <a href="{{ route('Quotations.duplicate', $purchaseQuotation->id) }}" class="btn btn-outline-info btn-sm">
                نسخ <i class="fa fa-copy ms-1"></i>
            </a>
            <div class="vr"></div>

            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                data-bs-target="#deleteModal">
                حذف <i class="fa fa-trash ms-1"></i>
            </button>
            <div class="vr"></div>

            <div class="dropdown">
                <button class="btn btn-outline-dark btn-sm" type="button" id="printDropdown" data-bs-toggle="dropdown">
                    طباعة <i class="fa fa-print ms-1"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="">
                            <i class="fa fa-file-pdf me-2 text-danger"></i>PDF طباعة</a></li>
                    <li><a class="dropdown-item" href="">
                            <i class="fa fa-file-excel me-2 text-success"></i>Excel تصدير</a></li>
                </ul>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">التفاصيل</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#suppliers" role="tab">الموردين</a>
                </li>

@if ($purchaseQuotation->generatedQuotations->count() > 0)
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#related" role="tab">
            <i class="fas fa-link me-1"></i>  عروض الاسعار
        </a>
    </li>
@endif

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#products" role="tab">المنتجات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">سجل النشاطات</a>
                </li>


            </ul>

            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                <div class="tab-pane active" id="details" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="text-start" style="width: 50%">
                                        <div class="mb-2">
                                            <label class="text-muted">رقم الطلب:</label>
                                            <div>{{ $purchaseQuotation->code }}</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">تاريخ الطلب:</label>
                                            <div>{{ $purchaseQuotation->order_date }}</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">منشئ الطلب:</label>
                                            <div>{{ $purchaseQuotation->creator->name ?? 'غير محدد' }}</div>
                                        </div>
                                    </td>
                                    <td class="text-start" style="width: 50%">
                                        <div class="mb-2">
                                            <label class="text-muted">تاريخ الاستحقاق:</label>
                                            <div>{{ $purchaseQuotation->due_date ?? 'غير محدد' }}</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">عدد المنتجات:</label>
                                            <div>{{ $purchaseQuotation->items->count() }}</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">الملاحظات:</label>
                                            <div>{{ $purchaseQuotation->notes ?? 'لا توجد ملاحظات' }}</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">المرفقات:</label>
                                            @if ($purchaseQuotation->attachments)
                                                <div><a href="{{ asset('storage/' . $purchaseQuotation->attachments) }}"
                                                        target="_blank">عرض المرفق</a></div>
                                            @else
                                                <div>لا توجد مرفقات</div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- تبويب الموردين -->
                <div class="tab-pane" id="suppliers" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم المورد</th>
                                    <th>الاسم التجاري</th>
                                    <th>رقم الهاتف</th>
                                    <th>البريد الإلكتروني</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseQuotation->suppliers as $supplier)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $supplier->name }}</td>
                                        <td>{{ $supplier->trade_name }}</td>
                                        <td>{{ $supplier->phone }}</td>
                                        <td>{{ $supplier->email }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

@if ($purchaseQuotation->generatedQuotations->count() > 0)
    <div class="tab-pane" id="related" role="tabpanel">
        <div class="table-responsive mt-3">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>رقم العرض</th>
                        <th>التاريخ</th>
                        <th>المورد</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchaseQuotation->generatedQuotations as $quotation)
                        <tr>
                            <td>{{ $quotation->code }}</td>
                            <td>{{ $quotation->date }}</td>
                            <td>{{ $quotation->supplier->trade_name ?? '-' }}</td>
                            <td>{{ number_format($quotation->grand_total, 2) }}</td>
                            <td>
                                @if ($quotation->status == 'Under Review')
                                    <span class="badge bg-warning">تحت المراجعة</span>
                                @elseif ($quotation->status == 'approved')
                                    <span class="badge bg-success">مقبول</span>
                                @elseif ($quotation->status == 'disagree')
                                    <span class="badge bg-danger">مرفوض</span>
                                @endif
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> عرض
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif




                <div class="tab-pane" id="products" role="tabpanel">
                    <div class="table-responsive mb-4">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-box me-2"></i>منتج</th>
                                    <th><i class="fas fa-sort-numeric-up me-2"></i>الكمية</th>
                                    <th><i class="fas fa-calendar-plus me-2"></i>تاريخ الاضافة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productDetails as $detail)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class=" p-2 me-3">
                                                    <i class="fas fa-cube text-primary"></i>
                                                </div>
                                                {{ $detail->product->name ?? '--' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $detail->quantity }}</span>
                                        </td>
                                        <td>{{ $detail->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">
                                            <i class="fas fa-inbox text-muted" style="font-size: 48px;"></i>
                                            <p class="text-muted mt-2">لا توجد منتجات مضافة</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
                                <div class="alert alert-danger text-xl-center" role="alert">
                                    <p class="mb-0">لا توجد سجلات نشاط حتى الآن!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">حذف طلب عرض السعر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من حذف طلب عرض السعر رقم "{{ $purchaseQuotation->code }}"؟</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <form action="{{ route('Quotations.destroy', $purchaseQuotation->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
