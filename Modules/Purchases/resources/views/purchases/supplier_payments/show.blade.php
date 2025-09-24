@extends('master')

@section('title')
    عرض المدفوعات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة المدفوعات</h2>
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4>عملية الدفع #{{ $payment->id }}</h4>
                        <p>المورد: {{ $payment->purchase_invoice->supplier->trade_name ?? '' }}</p>
                        <a href="#">Journal #{{ $payment->journal_id }}</a>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('PaymentSupplier.editSupplierPayment', $payment->id) }}"
                            class="btn btn-success mx-1">تعديل</a>
                        <button class="btn btn-info dropdown-toggle mx-1" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            إيصال استلام
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item"
                                href="{{ route('PaymentSupplier.rereceipt', ['id' => $payment->id]) }}?type=a4"
                                target="_blank">إيصال مدفوعات</a>
                            <a class="dropdown-item"
                                href="{{ route('PaymentSupplier.rereceipt', ['id' => $payment->id]) }}?type=thermal"
                                target="_blank">إيصال مدفوعات حرارية</a>

                        </div>
                        <button class="btn btn-danger mx-1">حذف</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>بيانات الدفع</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <td>رقم الفاتورة</td>
                                <td>{{ $payment->purchase_invoice->code ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>وسيلة دفع</td>
                                <td>
                                    @if ($payment->Payment_method == 1)
                                        نقدي
                                    @elseif ($payment->Payment_method == 2)
                                        شيك
                                    @elseif ($payment->Payment_method == 3)
                                        تحويل بنكي
                                    @endif
                                </td>
                            </tr>
                            @php
                                $currency = $account_setting->currency ?? 'SAR';
                                $currencySymbol =
                                    $currency == 'SAR' || empty($currency)
                                        ? '<img src="' .
                                            asset('assets/images/Saudi_Riyal.svg') .
                                            '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                                        : $currency;
                            @endphp
                            <tr>
                                <td>المبلغ</td>
                                <td>{{ number_format($payment->amount, 2) }} {!! $currencySymbol !!}</td>
                            </tr>
                            <tr>
                                <td>حالة الدفع</td>
                                <td>{{ $payment->status_payment ? \App\Models\ClientPayment::STATUS_PAYMENT[$payment->status_payment] : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td>التاريخ</td>
                                <td>{{ $payment->payment_date }}</td>
                            </tr>
                            <tr>
                                <td>حصلت بواسطة</td>
                                <td>{{ $payment->employee->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>ملاحظات</td>
                                <td>{{ $payment->notes ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>بيانات المورد</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <td>اسم المورد</td>
                                <td>{{ $payment->purchase_invoice->supplier->trade_name ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>المدينة</td>
                                <td>{{ $payment->purchase_invoice->supplier->city ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>المنطقة</td>
                                <td>{{ $payment->purchase_invoice->supplier->region ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>الرمز البريدي</td>
                                <td>{{ $payment->purchase_invoice->supplier->postal_code ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>الهاتف</td>
                                <td>{{ $payment->purchase_invoice->supplier->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>البلد</td>
                                <td>{{ $payment->purchase_invoice->supplier->country ?? 'SA' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <style>
        .btn-success {
            background: linear-gradient(to right, #28a745, #218838);
        }

        .btn-info {
            background: linear-gradient(to right, #17a2b8, #138496);
        }

        .btn-danger {
            background: linear-gradient(to right, #dc3545, #c82333);
        }
    </style>
@endsection
