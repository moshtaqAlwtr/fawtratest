@extends('master')

@section('title')
    اتفاقية الاقساط
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اتفاقية الاقساط</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card my-4">
        <!-- الفلاتر -->
        <div class="card mb-4">
            <div class="card-body">
                <form class="row g-3" method="GET" action="{{ route('installments.agreement_installments') }}">

                    <div class="col-md-4 mb-3"> <!-- Added mb-3 for bottom margin -->
                        <label for="status" class="form-label">اختر الحالة</label>
                        <select id="status" name="status" class="form-control">
                            <option selected>الكل</option>
                            <option value="1">مكتمل</option>
                            <option value="2">غير مكتمل</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3"> <!-- Added mb-3 for bottom margin -->
                        <label for="identifier" class="form-label">بحث بواسطة معرف القسط</label>
                        <input type="text" id="identifier" name="identifier" class="form-control"
                            placeholder="بحث بواسطة معرف القسط">
                    </div>

                    <div class="col-md-4 mb-3"> <!-- Added mb-3 for bottom margin -->
                        <label for="client" class="form-label">بحث باسم العميل أو الرقم التعريفي</label>
                        <input type="text" id="client" name="client" class="form-control"
                            placeholder="بحث باسم العميل">
                    </div>

                    <div class="col-md-4 mb-3"> <!-- Added mb-3 for bottom margin -->
                        <label for="period" class="form-label">اختر الفترة</label>
                        <select id="period" name="period" class="form-control">
                            <option selected>اختر الفترة</option>
                            <option value="1">أسبوع</option>
                            <option value="2">شهر</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3"> <!-- Added mb-3 for bottom margin -->
                        <label for="fromDate" class="form-label">تاريخ استحقاق القسط من</label>
                        <input type="date" id="fromDate" name="fromDate" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3"> <!-- Added mb-3 for bottom margin -->
                        <label for="toDate" class="form-label">تاريخ استحقاق القسط الى</label>
                        <input type="date" id="toDate" name="toDate" class="form-control">
                    </div>
                    <!-- الأزرار في سطر منفصل -->
                    <div class="col-12 text-right mt-3">
                        <button type="submit" class="btn btn-primary me-2">بحث</button>
                        <a href="{{ route('installments.agreement_installments') }}" class="btn btn-outline-danger">إلغاء
                            الفلاتر</a>
                    </div>

                </form>
            </div>
        </div>
    </div>



    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>المعرف</th>
                        <th>اسم العميل</th>
                        <th>مبلغ القسط</th>
                        <th>تاريخ الاستحقاق</th>
                        <th>الحالة</th>
                        <th style="width: 10%">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($installments as $detail)
                        <tr>
                            <td>{{ $detail->id }}</td>
                            <td>{{ $detail->installment->invoice->client->trade_name??'' }}</td>
                            <td>{{ number_format($detail->amount, 2) }} ر.س</td>
                            <td>{{ $detail->due_date->format('Y-m-d') }}</td>
                            <td>
                                @if ($detail->status =='unactive')
                                    <span class="badge bg-success">غير مكتمل</span>
                                @elseif ($detail->status == 'active')
                                    <span class="badge bg-warning text-dark">مكتمل</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('paymentsClient.create', ['id' => $detail->id, 'type' => 'installment_detail']) }}"
                                        class="btn btn-sm btn-info">
                                        <i class="fa fa-credit-card"></i> دفع
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
