@extends('master')

@section('title')
    تعديل اتفاقية قسط
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">  تعديل  اتفاقية قسط</h2>
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


    @include('layouts.alerts.success')
    @include('layouts.alerts.error')


    <div class="content-body">
        <form class="form" action="{{ route('installments.update', $installment->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card" style="max-width: 90%; margin: 0 auto;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h1 class="card-title"> معلومات اتفاقية التقسيط </h1>
                        </div>
                        <div>
                            <a href="" class="btn btn-outline-danger">
                                <i class="fa fa-ban"></i>الغاء
                            </a>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-save"></i>حفظ
                            </button>
                        </div>
                    </div>

                    <div class="form-body row">
                        <div class="form-group col-md-6">
                            <label for="client_id" class="form-label" style="margin-bottom: 10px"> العميل </label>
                            <select class="form-control duration-field" name="client_id" required>
                                <option value="">اختر العميل</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ $client->id == $invoice->client_id ? 'selected' : '' }}>
                                        {{ $client->trade_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-body row">
                        <div class="form-group col-md-6">
                            <label for="amount" class=""> مبلغ اتفاقية التقسيط </label>
                            <input type="number" id="amount" class="form-control" name="amount" value="{{ $invoice->grand_total }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="invoice_id" class=""> رقم الفاتورة  </label>
                            <input type="number" id="invoice_id" class="form-control" name="invoice_id" value="{{ $invoice->id }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="installment_amount" class=""> مبلغ القسط </label>
                            <input type="number" id="installment_amount" class="form-control" name="amount" oninput="calculateInstallments()">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="installments" class=""> عدد الاقساط </label>
                            <input type="number" id="installments" class="form-control" name="installment number">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="payment_rate" class=""> معدل السداد </label>
                            <select name="payment_rate" class="form-control duration-field" id="">
                                <option value=""> اختر معدل السداد</option>
                                <option value="1"> شهري </option>
                                <option value="2"> اسبوعي </option>
                                <option value="3"> سنوي </option>
                                <option value="4"> ربع سنوي </option>
                                <option value="5"> مرة كل اسبوعين </option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="due_date" class=""> تاريخ بدء السداد </label>
                            <input type="date" id="due_date" class="form-control" name="due_date" value="{{ $installment->due_date }}">
                        </div>

                    </div>

                    <div class="form-group col-md-6">
                        <label for="note" class=""> ملاحضات </label>
                        <textarea name="note" id="note" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
<script>
    function calculateInstallments() {
        const grandTotal = parseFloat(document.getElementById('amount').value);
        const installmentAmount = parseFloat(document.getElementById('installment_amount').value);

        if (!isNaN(grandTotal) && !isNaN(installmentAmount) && installmentAmount > 0) {
            const numberOfInstallments = Math.ceil(grandTotal / installmentAmount);
            document.getElementById('installments').value = numberOfInstallments;
        } else {
            document.getElementById('installments').value = '';
        }
    }
</script>

<script>
    // تعيين التاريخ الحالي كقيمة افتراضية لحقل تاريخ بدء السداد
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0]; // الحصول على التاريخ الحالي بصيغة YYYY-MM-DD
        document.getElementById('due_date').value = today; // تعيين القيمة لحقل الإدخال
    });
</script>
@endsection
