@extends('master')

@section('title')
    عرض السلفة
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض السلفة </h2>
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
      
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
        <div class="card-body p-1">
            <div class="d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted">الدفعة القادمة:</span>
                 <span>
    @php
        $nextPayment = $ancestor->payments()
            ->where('status', 'unpaid')
            ->orderBy('due_date')
            ->first();
    @endphp

    @if($nextPayment)
        @php
            $dueDate = \Carbon\Carbon::parse($nextPayment->due_date);
        @endphp
        
        @if($dueDate->isPast())
            <span class="text-danger">
                {{ $dueDate->format('Y-m-d') }}
                <small>(متأخر)</small>
            </span>
        @elseif($dueDate->isToday())
            <span class="text-warning">
                {{ $dueDate->format('Y-m-d') }}
                <small>(اليوم)</small>
            </span>
        @else
            <span class="text-success">
                {{ $dueDate->format('Y-m-d') }}
            </span>
        @endif
    @else
        <span class="badge badge-success">تم السداد بالكامل</span>
    @endif
</span>
                    <span class="text-muted px-2">|</span>
                    <span>سلفة #{{$id}}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-light"><i class="fa fa-chevron-right"></i></button>
                    <button class="btn btn-sm btn-light"><i class="fa fa-chevron-left"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2">
            <a href="{{ route('ancestor.edit', $id) }}"
                class="btn btn-outline-primary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                تعديل <i class="fa fa-edit ms-1"></i>
            </a>
            <div class="vr"></div>

          
            <div class="vr"></div>

            <div class="vr"></div>

           <a href="{{ route('ancestor.pay', $id) }}"
   class="btn btn-outline-success btn-sm d-inline-flex align-items-center justify-content-center px-3"
   style="min-width: 90px;">
   قم بالدفع القسط <i class="fa fa-money-bill ms-1"></i>
</a>
            <div class="vr"></div>

        
            <div class="vr"></div>

            <a href="#"
                class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                اضافة ملاحظة/مرفق <i class="fa fa-paperclip ms-1"></i>
            </a>
            <div class="vr"></div>

            <div class="dropdown d-inline-block">
                <button class="btn btn-outline-dark btn-sm d-inline-flex align-items-center justify-content-center px-3"
                    style="min-width: 90px;" type="button" id="printDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    المطبوعات <i class="fa fa-print ms-1"></i>
                </button>
                <ul class="dropdown-menu py-1" aria-labelledby="printDropdown">
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-alt me-2 text-primary"></i>Contract Layout 1</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-alt me-2 text-primary"></i>Contract Layout 2</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-alt me-2 text-primary"></i>Contract Layout 3</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-pdf me-2 text-danger"></i>نموذج 1 عقد</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-pdf me-2 text-danger"></i>نموذج 2 عقد</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-pdf me-2 text-danger"></i>نموذج 3 عقد</a></li>
                </ul>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">
                        <span>التفاصيل</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#installments" role="tab">
                        <span>الاقساط</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                        <span>سجل النشاطات</span>
                    </a>
                </li>
            </ul>
 @php
                                            $currency = $account_setting->currency ?? 'SAR';
                                            $currencySymbol = $currency == 'SAR' || empty($currency) ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">' : $currency;
                                        @endphp
            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                <div class="tab-pane active" id="details" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="text-start" style="width: 50%">
                                        <div class="mb-2">
                                            <label class="text-muted">موظف:</label>
                                            <div>{{ $ancestor->employee->full_name }} </div>
                                        </div>
                                      <div class="mb-2">
    <label class="text-muted">حالة السداد:</label>
    <div class="mt-1">
        <div style="width: fit-content;">
            <div style="font-weight: bold; margin-bottom: 4px; position: relative;">
                <!-- شريط التقدم -->
                <div style="border-bottom: 2px solid #ffc107; width: {{ $progressPercentage }}%; position: absolute; bottom: -2px;"></div>
                <div style="border-bottom: 1px solid #dee2e6; width: fit-content;">
                    {{ number_format($ancestor->amount, 2) }} {!! $currencySymbol !!} (إجمالي السلفة)
                </div>
            </div>
            
            <div style="color: #666; font-size: 0.9em;">
                <span>{{ number_format($totalPaid, 2) }} {!! $currencySymbol !!} مدفوعة ({{ $paidInstallments }} قسط)</span>
                <span class="mx-2">|</span>
                <span>{{ number_format($ancestor->amount - $totalPaid, 2) }} {!! $currencySymbol !!} متبقي</span>
            </div>
        </div>
    </div>
</div>

<!-- عرض تفاصيل الأقساط -->

                                        <div class="mb-2">
                                            <label class="text-muted">معدل السداد:</label>
                                            <div>
                                                @if ($ancestor->payment_rate == 1)
                                                    اسبوعي
                                                @elseif ($ancestor->payment_rate == 2)
                                                    شهري
                                                @else
                                                    سنوي
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">الخزنة:</label>
                                            <div>{{$ancestor->account->name ?? "" }}</div>
                                        </div>
                                    </td>
                                    <td class="text-start" style="width: 50%">
                                        <div class="mb-2">
                                            <label class="text-muted">تاريخ التقديم:</label>
                                            <div>{{ $ancestor->submission_date }}</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">قيمة القسط:</label>
                                            <div>{{ number_format($ancestor->installment_amount, 2) }}</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">تاريخ بدء الأقساط:</label>
                                            <div>{{ $ancestor->installment_start_date }}</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">مصروف:</label>
                                            <div>#000001</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="mb-2">
                                            <label class="text-muted">ملاحظة:</label>
                                            <div>{{$ancestor->note ?? ""}}</div>
                                        </div>
                                       <div class="mb-2">
    <label class="text-muted">الدفع من قسيمة الراتب:</label>
    @if($ancestor->pay_from_salary == 1)
        <i class="fas fa-check-circle text-success"></i>
    @else
        <i class="fas fa-times-circle text-danger"></i>
    @endif
</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="installments" role="tabpanel">
                   <div class="card mt-3">
    <div class="card-header">
        <h5>تفاصيل الأقساط</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المبلغ</th>
                    <th>تاريخ الاستحقاق</th>
                    <th>تاريخ الدفع</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ancestor->payments as $payment)
                <tr>
                    <td>{{ $payment->installment_number }}</td>
                    <td>{{ number_format($payment->amount, 2) }} ر.س</td>
                    <td>{{ $payment->due_date }}</td>
                    <td>
                        @if($payment->status == 'paid')
                            {{ $payment->payment_date ? $payment->payment_date : '--' }}
                        @else
                            --
                        @endif
                    </td>
                    <td>
                        @if($payment->status == 'paid')
                            <span class="badge badge-success">تم الدفع</span>
                        @else
                            <span class="badge badge-warning">غير مدفوع</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

             
                </div>

                <!-- تبويب سجل النشاطات -->
                <!--<div class="tab-pane" id="activity" role="tabpanel">-->
                <!--    <div class="timeline p-4">-->
                        <!-- يمكن إضافة سجل النشاطات هنا -->
                <!--        <p class="text-muted text-center">لا توجد نشاطات حتى الآن</p>-->
                <!--    </div>-->
                <!--</div>-->
            </div>
        </div>
    </div>
@endsection
