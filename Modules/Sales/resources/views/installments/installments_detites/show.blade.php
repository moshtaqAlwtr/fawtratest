@extends('master')

@section('title')
    عرض القسط
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض القسط #{{ $installment->id }}</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('installments.index') }}">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض القسط</li>
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
            <a href="{{ route('installments.edit', $installment->id) }}" class="btn btn-outline-primary btn-sm waves-effect waves-light">تعديل <i class="fa fa-edit"></i></a>
            <a href="{{ route('installments.index') }}" class="btn btn-outline-secondary btn-sm waves-effect waves-light">العودة إلى القائمة <i class="fa fa-arrow-left"></i></a>
            <a href="{{ route('paymentsClient.create', ['id' => $installment->id, 'type' => 'installment']) }}" class="btn btn-outline-danger btn-sm waves-effect waves-light">قم بالدفع <i class="fa fa-dollar"></i></a>
            <a href="{{ route('installments.show', $installment->id) }}" class="btn btn-outline-info btn-sm waves-effect waves-light">عرض اتفاقية الأقساط <i class="fa fa-file-alt"></i></a>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true">تفاصيل القسط</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log" role="tab" aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="card">
                        <div class="card-body">
                            <table class="table">
                                <thead style="background: #f8f8f8">
                                    <tr>
                                        <th>المعرف</th>
                                        <th>بيانات العميل</th>
                                        <th>مبلغ القسط</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>عدد الأقساط</th>
                                        <th>ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $installment->id }}</td>
                                        <td>{{ $installment->invoice->client->trade_name }}</td>
                                        <td>{{ number_format($installment->amount, 2) }} ر.س</td>
                                        <td>{{ $installment->due_date }}</td>
                                        <td>{{ $installment->installment_number }}</td>
                                        <td>{{ $installment->note }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="activity-log-tab">
                    <div class="card">
                        <div class="card-body">

                                <div class="alert alert-danger">لا توجد نشاطات مسجلة لهذا القسط.</div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
