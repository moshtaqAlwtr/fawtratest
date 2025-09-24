@extends('master')

@section('title')
    عرض قواعد العمولة
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض قواعد العمولة </h2>
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
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-md bg-light-primary">
                        <span class="avatar-content fs-4">ت</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0 fw-bolder">{{$commission->name ?? "" }}  </h5>
                                <small class="text-muted">#</small>
                            </div>
                            <div class="vr mx-2"></div>
                            <div class="d-flex align-items-center">
                                @if($commission->status == "active")
                                <small class="text-success">
                                    <i class="fa fa-circle me-1" style="font-size: 8px;"></i>
                                     نشط
                                </small>
                                @else
                                 <small class="text-danger">
                                    <i class="fa fa-circle me-1" style="font-size: 8px;"></i>
                                    غير نشط
                                </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

               
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2">
            <a href="{{ route('commission.edit', $commission->id) }}"
                class="btn btn-outline-primary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                تعديل <i class="fa fa-edit ms-1"></i>
            </a>
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
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                        <span>سجل النشاطات</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                <div class="tab-pane active" id="details" role="tabpanel">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="details" role="tabpanel"
                                            style="font-size: 1.1rem;">
                                            <!-- معلومات قواعد العمولة -->
                                            <div style="background-color: #f8f9fa;"
                                                class="d-flex justify-content-between align-items-center p-2 rounded mb-3">
                                                <h5 class="mb-0">معلومات قواعد العمولة</h5>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tbody class="text-end">
                                                        <tr>
                                                            <td class="text-muted" style="width: 50%;">الاسم:</td>
                                                            <td>{{$commission->name ?? "" }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">الفترة:</td>
                                                            <td> @if($commission->period == "monthly")
                                                                
                                                                <strong>شهري</strong>
                                                                @elseif($commission->period == "yearly")
                                                                <strong>سنوي</strong>
                                                                @else
                                                                <strong>ربع سنوي </strong>
                                                                @endif</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">نوع الهدف:</td>
                                                            <td><span class="text-muted">{{$commission->value}}</span> <span
                                                                    class="text-muted">
                                                                    @if($commission->target_type == "amount")
                                                                    <span>(مبلغ)</span>
                                                                    @else
                                                                    <span>(كمية)</span>
                                                                    @endif
                                                                
                                                                </span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">حساب العمولة:</td>
                                                            <td>
                                                                @if($commission->commission_calculation == "fully_paid")
                                                                <span> فواتير مدفوعة بالكامل </span>
                                                                @else
                                                                <span>  فواتير مدفوعة جزئيا </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">العملة:</td>
                                                            <td>{{$commission->currency ?? "" }}</td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="mt-4">
                                                <h6 class="text-muted mb-3">قائمة الموظفين</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-borderless">
                                                        <tbody>
                                                            @foreach ($commissionUsers as $index => $user)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td> <!-- رقم التسلسل -->
                                                                <td>
                                                                    <div class="w-100 text-end">
                                                                        <span>{{ $user->employee->name }}</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        
                                                        
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- تطبق على البنود التالية -->
                                            <div class="mt-4">
                                                <h6 class="mb-3">تطبق على البنود التالية</h6>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr class="text-end">
                                                                <th>البند</th>
                                                                <th>نوع الوحدة</th>
                                                                <th>نسبة العمولة</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($CommissionProducts as $Commission)
                                                            <tr class="text-end">
                                                                <td>-</td>
                                                                <td>
                                                                    {{$Commission->products->name ?? "كل المنتجات"}}
                                                                </td>
                                                                <td><span class="text-muted">{{$Commission->commission_percentage}} %</span></td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- الملاحظات -->
                                            <div class="mt-4">
                                                <h6 class="mb-3">الملاحظات:</h6>
                                                <div class="p-3  rounded">
                                                    <p class="text-muted mb-0">{{$commission->notes ?? "لا توجد ملاحظات"}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب سجل النشاطات -->
                <div class="tab-pane" id="activity" role="tabpanel">
                    <div class="timeline p-4">
                        <!-- يمكن إضافة سجل النشاطات هنا -->
                         <style>
    .timeline {
        position: relative;
        margin: 20px 0;
        padding: 0;
        list-style: none;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 50px;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #28a745 0%, #218838 100%);
        right: 50px;
        margin-right: -2px;
    }
    .timeline-item {
        margin: 0 0 20px;
        padding-right: 100px;
        position: relative;
        text-align: right;
    }
    .timeline-item::before {
        content: "\f067";
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 30px;
        top: 15px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(145deg, #28a745, #218838);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ffffff;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }
    .timeline-content {
        background: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    .timeline-content .time {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 10px;
    }

    .filter-bar {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    .timeline-day {
        background-color: #ffffff;
        padding: 10px 20px;
        border-radius: 30px;
        text-align: center;
        margin-bottom: 20px;
        font-weight: bold;
        color: #333;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        display: inline-block;
        position: relative;
        top: 0;
        right: 50px;
        transform: translateX(50%);
    }
    .filter-bar .form-control {
        border-radius: 8px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }
    .filter-bar .btn-outline-secondary {
        border-radius: 8px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }
    .timeline-date {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin: 20px 0;
        color: #333;
    }
</style>
<div class="card">
    <div class="card">
        <div class="container">
            <div class="row mt-4">
                <div class="col-12">
                    <!-- شريط التصفية -->
                    <div class="filter-bar d-flex justify-content-between align-items-center">
                       
                      
                    </div>

                    <!-- الجدول الزمني -->
                    @if(isset($actives_logs) && $actives_logs->count() > 0)
                        @php
                            $previousDate = null;
                        @endphp

                        @foreach($actives_logs as $date => $dayLogs)
                            @php
                                $currentDate = \Carbon\Carbon::parse($date);
                                $diffInDays = $previousDate ? $previousDate->diffInDays($currentDate) : 0;
                            @endphp

                            @if($diffInDays > 7)
                                <div class="timeline-date">
                                    <h4>{{ $currentDate->format('Y-m-d') }}</h4>
                                </div>
                            @endif

                            <div class="timeline-day">{{ $currentDate->translatedFormat('l') }}</div>

                            <ul class="timeline">
                                @foreach($dayLogs as $log)
                                    @if ($log)
                                        <li class="timeline-item">
                                            <div class="timeline-content">
                                                <div class="time">
                                                    <i class="far fa-clock"></i> {{ $log->created_at->format('H:i:s') ?? "" }}
                                                </div>
                                                <div>
                                                    <strong>{{ $log->user->name ?? 'مستخدم غير معروف' }}</strong>
                                                    {!! Str::markdown($log->description ?? 'لا يوجد وصف') !!}
                                                    <div class="text-muted">{{ $log->user->branch->name ?? 'فرع غير معروف' }}</div>
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
                            <p class="mb-0">لا توجد عمليات مضافه حتى الان !!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection
