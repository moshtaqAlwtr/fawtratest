@extends('master')

@section('title')
    عرض العرض
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">{{ $offer->title }}</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                            <li class="breadcrumb-item active">
                                @if ($offer->status == 1)
                                    <div class="badge badge-pill badge badge-success">نشط</div>
                                @else
                                    <div class="badge badge-pill badge badge-danger">غير نشط</div>
                                @endif
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <div class="card">
        <div class="card-title p-2">
            <a href="#" class="btn btn-outline-danger btn-sm waves-effect waves-light" data-toggle="modal"
                data-target="#modal_DELETE1">حذف <i class="fa fa-trash"></i></a>
            <a href="{{ route('Offers.edit', $offer->id) }}"
                class="btn btn-outline-primary btn-sm waves-effect waves-light">تعديل <i class="fa fa-edit"></i></a>

        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                        aria-controls="details" aria-selected="true">تفاصيل العرض</a>
                </li>


                <li class="nav-item">
                    <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log" role="tab"
                        aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- تفاصيل العرض -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="tab-content">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <table class="table">
                                        <thead style="background: #f8f8f8">
                                            <tr>
                                                <th colspan="4">معلومات العرض</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><small>الاسم</small></td>
                                                <td>{{ $offer->name }}</td>
                                                <td><small>النوع</small></td>
                                                <td>{{ $offer->type }}</td>
                                            </tr>
                                            <tr>
                                                <td><small>صالح من</small></td>
                                                <td>{{ $offer->valid_from }}</td>
                                                <td><small>صالح حتى</small></td>
                                                <td>{{ $offer->valid_to }}</td>
                                            </tr>
                                            <tr>
                                                <td><small>نوع الخصم</small></td>
                                                @if($offer->discount_type == 1)
                                                <td class="text-success">
                                                    <i class="fas fa-money-bill-wave"></i> مبلغ ثابت
                                                </td>
                                                <td><small>قيمة الخصم</small></td>
                                                <td class="text-center">
                                                    <span class="badge badge-primary">
                                                        {{ number_format($offer->discount_value, 2) }} ريال
                                                    </span>
                                                </td>
                                            @else
                                                <td class="text-info">
                                                    <i class="fas fa-percentage"></i> نسبي
                                                </td>
                                                <td><small>قيمة الخصم</small></td>
                                                <td class="text-center">
                                                    <span class="badge badge-info">
                                                        {{ number_format($offer->discount_value, 2) }}%
                                                    </span>
                                                </td>
                                            @endif
                                                
                                            </tr>
                                            <tr>
                                                <td><small> الكمية المطلوبة لتطبيق العرض</small></td>
                                                <td>{{ $offer->quantity }}</td>
                                                <td><small>الحالة</small></td>
                                                <td>
                                                    @if ($offer->status == 1)
                                                        <div class="badge badge-pill badge badge-success">نشط</div>
                                                    @else
                                                        <div class="badge badge-pill badge badge-danger">غير نشط</div>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="table mt-3">
                                        <thead style="background: #f8f8f8">
                                            <tr>
                                                <th colspan="2">تفاصيل البند العرض</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><small>نوع الوحدة</small></td>
                                                <td>
                                                    @if ($offer->unit_type == 1)
                                                        الكل
                                                    @elseif($offer->unit_type == 2)
                                                    التصنيفات
                                                    @else
                                                        المنتجات
                                                    @endif
                                                </td>
                                            </tr>

                                            @if ($offer->unit_type == 2) <!-- إذا كان نوع الوحدة هو "التصنيفات" -->
                                            <tr>
                                                <td><small>التصنيفات المشمولة</small></td>
                                                <td>
                                                    @foreach ($offer->categories as $category)
                                                        <span class="badge badge-primary mr-1">
                                                            {{ $category->name }}
                                                        </span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif
                                        
                                        @if ($offer->unit_type == 3) <!-- إذا كان نوع الوحدة هو "المنتجات" -->
                                            <tr>
                                                <td><small>المنتجات المشمولة</small></td>
                                                <td>
                                                    @foreach ($offer->products as $product)
                                                        <span class="badge badge-info mr-1">
                                                            {{ $product->name }}
                                                        </span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><small>العملاء المشمولين</small></td>
                                            <td>
                                                @foreach ($offer->clients as $client)
                                                    <span class="badge badge-info mr-1">
                                                        {{ $client->trade_name }}
                                                    </span>
                                                @endforeach
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- المنتجات -->


            </div>
        </div>
    </div>
@endsection
