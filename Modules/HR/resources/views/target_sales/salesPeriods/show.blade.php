@extends('master')

@section('title')
    عرض فترة المبيعات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض فترة المبيعات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center" style="gap: 10px;">
                    <h4 class="mb-0">فترة المبيعات #1</h4>
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <span class="bg-secondary rounded-circle me-2" style="width: 8px; height: 8px;"></span>
                        <span>مفتوح</span>
                    </div>
                </div>
                <div class="d-flex gap-2" style="gap: 10px;">
                    <button class="btn btn-success d-flex align-items-center gap-2" style="gap: 10px;">
                        <i class="fa fa-check"></i>
                        <span>موافقة</span>
                    </button>
                    <button class="btn btn-danger d-flex align-items-center gap-2" style="gap: 10px;">
                        <i class="fa fa-times"></i>
                        <span>رفض</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2">

            <a href="#"
                class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-toggle="modal" data-target="#modal_DELETE1">
                حذف <i class="fa fa-trash ms-1"></i>
            </a>
            <div class="vr"></div>


        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">
                        <span>التفاصيل</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#commission" role="tab">
                        <span>عمولات مبيعات </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                        <span>سجل النشاطات</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                @php
    // جمع المبيعات (بدون ضريبة) في الفترة
    $total_sales = $SalesCommissions->sum('sales_amount') ?? 0;
    // جمع نسب العمولة (ratio) من كل صف (نسبة مئوية لكل فاتورة)
    $total_commission = $SalesCommissions->sum(function($row) {
        return $row->sales_amount * ($row->ratio / 100);
    });
    // قيمة الهدف
    $commission_value = $SalesCommission_periods->commission->value ?? 0;
    // هل الهدف نوعه مبلغ أم كمية
    $commission_type = $SalesCommission_periods->commission->target_type ?? 'amount';
    // المبيعات مع الضريبة
    $sales_with_vat = $total_sales * 1.15;
    // نسبة التقدم من الهدف
    $progress = $commission_value > 0 ? round(($sales_with_vat / $commission_value) * 100) : 0;
    $progress = $progress > 100 ? 100 : $progress;
    // تحقق الهدف أم لا
    $is_achieved = $sales_with_vat >= $commission_value;
@endphp

<div class="tab-pane active" id="details" role="tabpanel">
    <div class="px-3 pt-6">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-6 lh-1">
                    <div class="d-flex align-items-center">
                        <div class="ml-2">
                            <h5 class="font-weight-bold fs-20 lh-1 mb-1">
                                {{ $SalesCommission_periods->employee->name ?? "" }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-6 lh-1">
                    <p class="fs-20 font-weight-bold mb-1">
                        {{ $SalesCommission_periods->created_at ? $SalesCommission_periods->created_at->format('Y-m-d') : '' }}
                    </p>
                    <p class="fs-14 font-weight-medium mb-0 text-inactive">من</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-6 lh-1">
                    <p class="fs-20 font-weight-bold mb-1">
                        {{ $SalesCommission_periods->created_at ? $SalesCommission_periods->created_at->format('Y-m-d') : '' }}
                    </p>
                    <p class="fs-14 font-weight-medium mb-0 text-inactive">إلي</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-6">
                    <p class="text-inactive font-weight-medium mb-1">الهدف:</p>
                    <p class="font-weight-bold fs-18 text-dark-blue mb-0">{{ number_format($commission_value, 2) }}</p>
                    <p class="font-weight-medium fs-14 text-danger mb-0">
                        @if($is_achieved)
                            <span class="text-success">✅ تحقق الهدف</span>
                        @else
                            <span class="text-danger">❌ لم يتحقق الهدف</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-6">
                    <p class="text-inactive font-weight-semibold mb-1">المبيعات / الهدف:</p>
                    <div>
                        <div class="d-flex justify-content-between" style="margin-bottom: 2px;">
                            <span class="fs-5">
                                {{ number_format($commission_value, 2) }} /
                                {{ number_format($sales_with_vat, 2) }}
                            </span>
                        </div>
                        <div class="progress" style="height: 6px; margin: 2px 0;">
                            <div class="progress-bar {{ $is_achieved ? 'bg-success' : 'bg-info' }}"
                                role="progressbar"
                                style="width: {{ $progress }}%;"
                                aria-valuenow="{{ $progress }}"
                                aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between" style="margin-top: 2px;">
                            <span class="fs-5">{{ $progress }}%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-6">
                    <div class="w-100 p-3 text-center bg-danger text-white rounded-3">
                        <p class="fs-14 fs-16-lg font-weight-semibold mb-0 opacity-75">إجمالي العمولة المستحقة</p>
                        <p class="fs-16 fs-24-lg font-weight-bold mb-0">
                            {{ number_format($total_commission, 2) }} ر.س
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="spacer">
</div>

                <!-- تبويب عمولات المبيعات -->
                <div class="tab-pane" id="commission" role="tabpanel">
                    <div class="card">
                        <div class="card-body">

                            <table class="table" style="font-size: 1.1rem;">
                                <thead class="">
                                    <tr>

                                        <th>المعرف</th>
                                        <th>موظف</th>
                                        <th>العملية</th>
                                        <th>مبلغ المبيعات</th>
                                        <th>كمية المبيعات</th>
                                        <th>عمولة</th>
                                        <th>ترتيب بواسطة</th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($SalesCommissions as $SalesCommission)
                                    
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" style="width: 15px; height: 15px;">
                                                </div>
                                                <span style="margin-top: 20px">{{ $loop->iteration }}</span> <!-- الترقيم التسلسلي -->
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-sm bg-secondary">
                                                    <span class="avatar-content"></span>
                                                </div>
                                                {{$SalesCommission->employee->name ?? ""}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>فاتورة #{{$SalesCommission->invoice_number ?? ""}}</span>
                                                <small class="text-muted">{{ $SalesCommission->created_at ? $SalesCommission->created_at->format('Y-m-d') : '' }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $SalesCommission->sales_amount ? number_format($SalesCommission->sales_amount * 1.15, 2) : '' }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{$SalesCommission->sales_quantity ?? ""}}</span>
                                                <small class="text-muted">عناصر</small>
                                            </div>
                                        </td>
                                        <td>{{ $SalesCommission->sales_amount && $SalesCommission->ratio ? number_format((($SalesCommission->sales_amount * 1.15) * $SalesCommission->ratio) / 100, 2) : '' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                        type="button" id="dropdownMenuButton303" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('SalesCommission.show', $SalesCommission->id) }}">
                                                                <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                            </a>
                                                        </li>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                
                            </table>
                            {{-- @else
                                    <div class="alert alert-danger text-xl-center" role="alert">
                                        <p class="mb-0">
                                            لا توجد مسميات وظيفية مضافة حتى الان !!
                                        </p>
                                    </div>
                                @endif --}}
                            {{-- {{ $shifts->links('pagination::bootstrap-5') }} --}}
                        </div>
                    </div>
                </div>

                <!-- تبويب سجل النشاطات -->
                <div class="tab-pane" id="activity" role="tabpanel">
                    <div class="timeline p-4">
                        <!-- يمكن إضافة سجل النشاطات هنا -->
                        <p class="text-muted text-center">لا توجد نشاطات حتى الآن</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
