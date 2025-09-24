@extends('master')

@section('title')
    الايرادات
@stop
@section('css')
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
        .timeline-date {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            color: #333;
        }
    </style>
    @endsection
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الايرادات</h2>
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
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <strong> سند قبض </strong> | <small>{{ $income->code }} #</small> | <span
                            class="badge badge-pill badge badge-success">اصدر</span>
                    </div>

                    <div>
                        {{-- <a href="{{ route('incomes.edit', $income->id) }}" class="btn btn-outline-primary">
                            <i class="fa fa-edit"></i>تعديل
                        </a> --}}
                    </div>

                </div>
            </div>
        </div>

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="container" style="max-width: 1200px">
            <div class="card">
                <div class="card-title p-2">
                    <a href="{{ route('incomes.edit', $income->id) }}" class="btn btn-outline-primary btn-sm">تعديل <i
                            class="fa fa-edit"></i></a>
                    <a href="#" class="btn btn-outline-danger btn-sm" data-toggle="modal"
                        data-target="#modal_DELETE{{ $income->id }}">حذف <i class="fa fa-trash"></i></a>

                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" aria-controls="home"
                                role="tab" aria-selected="false">التفاصيل</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile"
                                role="tab" aria-selected="false">مطبوعات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="activate-tab" data-toggle="tab" href="#activate" aria-controls="about"
                                role="tab" aria-selected="true">سجل النشاطات</a>
                        </li>

                    </ul>
                    <div class="tab-content">

                        <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">

                            <div class="card">
                                <div class="card-header">
                                    <strong>التفاصيل :</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <table class="table">
                                            <tr>
                                                <td style="width: 50%">
                                                    @if ($income->attachments)
                                                        <img src="{{ asset('assets/uploads/incomes/' . $income->attachments) }}"
                                                            alt="img" width="150">
                                                    @else
                                                        <img src="{{ asset('assets/uploads/no_image.jpg') }}" alt="img"
                                                            width="150">
                                                    @endif
                                                    <br><br>
                                                    <strong>الوصف </strong>: {{ $income->description }}
                                                </td>

                                                <td>
                                                    <strong> الكود </strong>: {{ $income->code }}#
                                                    <br><br>
                                                    <strong>المبلغ </strong>: {{ $income->amount }}
                                                    <br><br>
                                                    <strong>التاريخ </strong>: {{ $income->date }}
                                                    <br><br>
                                                    <strong>خزينة </strong>: {{ $income->store_id }}
                                                    <br><br>
                                                    <strong>الحساب الفرعي </strong>: {{ $income->account->name?? 'لا يوجد' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane" id="profile" aria-labelledby="profile-tab" role="tabpanel "
                            style="background: rgba(0, 0, 0, 0.05);">

                            <!-- عرض سند PDF -->
                            @include('account::finance.incomes.print_normal', ['income' => $income])


                        </div>
                        <div class="tab-pane" id="about" aria-labelledby="about-tab" role="tabpanel">
                            <p>time table</p>
                        </div>
                      <div class="tab-pane" id="activate" aria-labelledby="activate-tab" role="tabpanel">


    <div class="row mt-4">
        <div class="col-12">
            @if($logs && count($logs) > 0)
                @php
                    $previousDate = null;
                @endphp

                @foreach($logs as $date => $dayLogs)
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
                                            <i class="far fa-clock"></i> {{ $log->created_at }}
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


        <!-- Modal delete -->
        <div class="modal fade text-left" id="modal_DELETE{{ $income->id }}" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #EA5455 !important;">
                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف سند قبض</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="color: #DC3545">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <strong>
                          عفوا لا يمكن الحذف يمكنك اصدار سند صرف
                        </strong>
                    </div>

                </div>
            </div>
        </div>
        <!--end delete-->

    </div>

@endsection

@section('scripts')

@endsection
