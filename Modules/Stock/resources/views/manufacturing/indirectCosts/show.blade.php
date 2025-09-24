@extends('master')

@section('title')
التكاليف غير المباشرة
@stop
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">التكاليف غير المباشرة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-title">
                <div class="d-flex justify-content-between align-items-center flex-wrap p-1">
                    <div>
                        <a href="{{ route('manufacturing.indirectcosts.edit', $indirectCost->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-edit"></i>تعديل
                        </a>
                        <a class="btn btn-sm btn-outline-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $indirectCost->id }}">
                            <i class="fa fa-trash me-2"></i>حذف
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="false">معلومات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="activate-tab" data-toggle="tab" href="#activate" aria-controls="about" role="tab" aria-selected="true">سجل النشاطات</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">
                        <div class="card">
                            <div class="card-header p-1" style="background: #f8f8f8">
                                <strong>معلومات التكاليف غير المباشرة</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td style="width: 50%">
                                                    <p><small>الحساب</small></p>
                                                    <strong>{{ $indirectCost->account->name }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>اجمالي التكاليف</small></p>
                                                    <strong>{{ $indirectCost->total }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%">
                                                    <p><small>من تاريخ</small></p>
                                                    <strong>{{ $indirectCost->from_date }}</strong>
                                                </td>
                                                <td>
                                                    <p><small>الي تاريخ</small></p>
                                                    <strong>{{ $indirectCost->to_date }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%">
                                                    <p><small>نوع التوزيع</small></p>
                                                    <strong>
                                                        @if($indirectCost->based_on == 1)
                                                            بناءً على الكمية
                                                        @else
                                                            بناءً على التكلفة
                                                        @endif
                                                    </strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card">

                            @if(isset($indirectCost->indirectCostItems) && !@empty($indirectCost->indirectCostItems) && count($indirectCost->indirectCostItems) > 0)
                                <div class="card-body">
                                    <p><strong>القيود اليومية : </strong></p>
                                    <div class="row">
                                        <table class="table table-striped">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 50%">قيد</th>
                                                    <th>المجموع</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($indirectCost->indirectCostItems as $item)
                                                    <tr>
                                                        <td>{{ $item->restriction_id }}</td>
                                                        <td>{{ $item->restriction_total }} ر.س</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @if(isset($indirectCost->indirectCostItems) && !@empty($indirectCost->indirectCostItems) && count($indirectCost->indirectCostItems) > 0)
                                <div class="card-body">
                                    <p><strong>أوامر التصنيع : </strong></p>
                                    <div class="row">
                                        <table class="table table-striped">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 50%">أمر التصنيع</th>
                                                    <th>المبلغ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($indirectCost->indirectCostItems as $item)
                                                    <tr>
                                                        <td>{{ $item->ManufacturingOrder->name }}</td>
                                                        <td>{{ $item->manufacturing_price }} ر.س</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>

                    <div class="tab-pane" id="activate" aria-labelledby="activate-tab" role="tabpanel">
                        <div class="row mt-4">
                            <div class="col-12">
                                <!-- Loading للوغز -->
                                <div id="logsLoading" class="text-center p-4" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">جاري تحميل السجلات...</span>
                                    </div>
                                    <p class="mt-2">جاري تحميل سجل النشاطات...</p>
                                </div>

                                <!-- محتوى السجلات -->
                                <div id="logsContent">
                                    @if (isset($logs) && count($logs) > 0)
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
                                        <div class="alert alert-info text-center" role="alert">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <p class="mb-0">لا توجد سجلات نشاط حتى الآن!</p>
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

    <!-- Modal delete -->
    <div class="modal fade text-left" id="modal_DELETE{{ $indirectCost->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #EA5455 !important;">
                    <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف التكاليف غير المباشرة</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: #DC3545">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <strong>
                        هل انت متاكد من انك تريد الحذف ؟
                    </strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">الغاء</button>
                    <a href="{{ route('manufacturing.indirectcosts.destroy', $indirectCost->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                </div>
            </div>
        </div>
    </div>
    <!--end delete-->

@endsection
