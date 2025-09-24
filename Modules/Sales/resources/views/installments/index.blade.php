@extends('master')

@section('title')
    أتفاقات التقسيط
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أتفاقات التقسيط</h2>
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

    @include('layouts.alerts.success')
    @include('layouts.alerts.error')



    <div class="card my-4">
        <!-- الفلاتر -->
        <div class="card mb-4">
            <div class="card-body">
                <form class="row g-3" method="GET" action="{{ route('installments.index') }}">
    <div class="col-md-4 mb-3">
        <label for="status" class="form-label">حالة الفاتورة</label>
        <select id="status" name="status" class="form-control">
            <option value="الكل" {{ request('status') == 'الكل' ? 'selected' : '' }}>الكل</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>مكتمل</option>
            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>غير مكتمل</option>
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label for="installment_status" class="form-label">حالة القسط</label>
        <select id="installment_status" name="installment_status" class="form-control">
            <option value="">الكل</option>
            <option value="paid" {{ request('installment_status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
            <option value="unpaid" {{ request('installment_status') == 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label for="identifier" class="form-label">معرف القسط</label>
        <input type="text" id="identifier" name="identifier" class="form-control"
               placeholder="معرف القسط" value="{{ request('identifier') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="client" class="form-label">اسم العميل أو الرقم</label>
        <input type="text" id="client" name="client" class="form-control"
               placeholder="اسم العميل أو الرقم" value="{{ request('client') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="period" class="form-label">الفترة</label>
        <select id="period" name="period" class="form-control">
            <option value="">اختر الفترة</option>
            <option value="1" {{ request('period') == '1' ? 'selected' : '' }}>أسبوع</option>
            <option value="2" {{ request('period') == '2' ? 'selected' : '' }}>شهر</option>
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label for="fromDate" class="form-label">من تاريخ</label>
        <input type="date" id="fromDate" name="fromDate" class="form-control"
               value="{{ request('fromDate') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="toDate" class="form-label">إلى تاريخ</label>
        <input type="date" id="toDate" name="toDate" class="form-control"
               value="{{ request('toDate') }}">
    </div>

    <div class="col-12 text-right mt-3">
        <button type="submit" class="btn btn-primary me-2">بحث</button>
        <a href="{{ route('installments.index') }}" class="btn btn-outline-danger">إلغاء الفلاتر</a>
    </div>
</form>
            </div>
        </div>
    </div>


    @if (isset($installments) && !empty($installments) && count($installments) > 0)
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>المعرف</th>
                            <th>بيانات العميل</th>
                            <th>بيانات الدفع</th>
                            <th>مبلغ القسط </th>
                            <th>تاريخ الاستحقاق </th>
                            <th style="width: 10%">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($installments as $installment)
                            <tr>
                                <td>{{ $installment->id }}</td>
                                <td>{{ $installment->invoice->client->trade_name }}</td> <!-- Customer's trade name -->
                                <td>
                                    {{ $installment->invoice->grand_total }} / {{ $installment->installment_number }}
                                </td><!-- Total amount of the invoice -->
                                <td>{{ $installment->amount }}</td> <!-- Amount of the current installment -->

                                    <td>
                                        {{ $installment->due_date }}
                                        <br>
                                        <span class="text-info">{{ $installment->status }}</span> <!-- Display status next to due date -->
                                    </td>

                                <td>
                                    <div class="btn-group">
                                        <div class="dropdown">
                                            <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                type="button" id="dropdownMenuButton303" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"></button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('installments.show', $installment->id) }}">
                                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('installments.edit', $installment->id) }}">
                                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('installments.destroy', $installment->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fa fa-trash me-2 text-danger"></i>حذف
                                                        </button>
                                                    </form>

                                                </li>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Modal delete -->
                                {{-- <div class="modal fade text-left" id="modal_DELETE{{ $installment->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #EA5455 !important;">
                                                <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true" style="color: #DC3545">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <strong>
                                                    هل انت متاكد من انك تريد الحذف ؟
                                                </strong>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light waves-effect waves-light"
                                                    data-dismiss="modal">الغاء</button>
                                                <a href="{{ route('installments.destroy', $installment->id) }}"
                                                    class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <!--end delete-->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-danger text-xl-center" role="alert">
            <p class="mb-0">
                لا توجد اقساط حتى الان !!
            </p>
        </div>
    @endif

    {{-- {{ $shifts->links('pagination::bootstrap-5') }} --}}
    </div>
    </div>

@endsection
