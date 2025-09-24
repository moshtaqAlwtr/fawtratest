
@extends('master')

@section('title')
    تحليل الزيارات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تحليل الزيارات   </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

@foreach($groups as $group)
    <h4 class="mt-4">📍 {{ $group->name }}</h4>
    <table class="table table-bordered text-center">
        <thead class="table-light">
            <tr>
                <th>العميل</th>
                @foreach($dates as $date)
                    <th>{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</th>
                @endforeach
                <th>💰 مجموع المدفوع</th>
            </tr>
        </thead>
        <tbody>
            @foreach($group->customers as $customer)
                @php
                    $totalPaid = 0;
                    $debt = $customer->invoices->sum('total') - $customer->payments->sum('amount');
                @endphp
                <tr>
                    <td class="text-start">
                        {{ $customer->name }}
                        <br>
                        @if($debt > 0)
                            <span class="badge bg-danger">💰 {{ number_format($debt) }} ريال</span>
                        @else
                            <span class="badge bg-success">✅ مسدد</span>
                        @endif
                    </td>

                    @foreach($dates as $date)
                        @php
                            $visit = $customer->visits->firstWhere('visit_date', $date);
                            $icons = '';
                            $paidAmount = 0;
                        @endphp

                        @if($visit)
                            @php $icons .= '🚶‍♂️'; @endphp

                            @if($visit->note)
                                @php $icons .= ' 📝'; @endphp
                            @endif

                            @if($visit->invoice)
                                @php $icons .= ' 🧾'; @endphp
                            @endif

                            @if($visit->payment)
                                @php
                                    $paidAmount += $visit->payment->amount;
                                    $icons .= ' 💵' . number_format($visit->payment->amount);
                                @endphp
                            @endif

                            @php $totalPaid += $paidAmount; @endphp
                            <td>{!! $icons !!}</td>
                        @else
                            <td>❌</td>
                        @endif
                    @endforeach

                    <td><strong>{{ number_format($totalPaid) }} ريال</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endforeach


@endsection