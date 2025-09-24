@extends('master')

@section('title')
    اضافة اتفاقية قسط
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اضافة اتفاقية قسط</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('installments.index') }}">اتفاقيات التقسيط</a></li>
                            <li class="breadcrumb-item active">اضافة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.success')
    @include('layouts.alerts.error')

    <div class="content-body">
        <form class="form" action="{{ route('installments.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card" style="max-width: 90%; margin: 0 auto;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h1 class="card-title">معلومات اتفاقية التقسيط</h1>
                        </div>
                        <div>
                            <a href="{{ route('installments.index') }}" class="btn btn-outline-danger">
                                <i class="fa fa-ban"></i> الغاء
                            </a>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-save"></i> حفظ
                            </button>
                        </div>
                    </div>

                    <div class="form-body row mt-3">
                        <!-- معلومات العميل -->
                        <div class="form-group col-md-6">
                            <label for="client_id" class="form-label">العميل</label>
                            <select class="form-control" name="client_id" id="client_id" required>
                                <option value="">اختر العميل</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ $client->id == $invoice->client_id ? 'selected' : '' }}>
                                        {{ $client->trade_name }} - {{ $client->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- معلومات الفاتورة -->
                        <div class="form-group col-md-6">
                            <label for="invoice_id">رقم الفاتورة</label>
                            <input type="text" id="invoice_id" class="form-control"
                                   value="{{ $invoice->invoice_number }} ({{ $invoice->id }})" readonly>
                            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                        </div>

                        <!-- المبلغ الكلي -->
                        <div class="form-group col-md-6">
                            <label for="amount">مبلغ الفاتورة الكلي</label>
                            <input type="number" id="amount" class="form-control"
                                   name="amount" value="{{ $invoice->grand_total }}" readonly>
                        </div>

                        <!-- مبلغ القسط -->
                        <div class="form-group col-md-6">
                            <label for="installment_amount">مبلغ القسط الأساسي</label>
                            <div class="input-group">
                                <input type="number" id="installment_amount" class="form-control"
                                       name="amount" oninput="calculateInstallments()"
                                       min="1" step="0.01" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">ر.س</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">أدخل المبلغ الأساسي لكل قسط</small>
                        </div>

                        <!-- عدد الأقساط -->
                        <div class="form-group col-md-6">
                            <label for="number_of_installments">عدد الأقساط</label>
                            <input type="number" id="number_of_installments" class="form-control"
                                   name="installment_number" readonly>
                        </div>

                        <!-- معدل السداد -->
                        <div class="form-group col-md-6">
                            <label for="payment_rate">معدل السداد</label>
                            <select name="payment_rate" id="payment_rate" class="form-control" required>
                                <option value="">اختر معدل السداد</option>
                                <option value="1">شهري</option>
                                <option value="2">اسبوعي</option>
                                <option value="3">سنوي</option>
                                <option value="4">ربع سنوي</option>
                                <option value="5">مرة كل اسبوعين</option>
                            </select>
                        </div>

                        <!-- تاريخ بدء السداد -->
                        <div class="form-group col-md-6">
                            <label for="due_date">تاريخ بدء السداد</label>
                            <input type="date" id="due_date" class="form-control" name="due_date" required>
                        </div>

                        <!-- ملاحظات -->
                        <div class="form-group col-md-12">
                            <label for="note">ملاحظات</label>
                            <textarea name="note" id="note" class="form-control" rows="3"
                                      placeholder="أضف أي ملاحظات إضافية..."></textarea>
                        </div>
                    </div>

                    <!-- تفاصيل الأقساط -->
                    <div id="installment_breakdown" class="mt-4 p-3 border rounded bg-light" style="display: none;">
                        <h5 class="mb-3 text-primary"><i class="fa fa-calculator"></i> تفاصيل خطة التقسيط</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light-primary">
                                    <tr>
                                        <th width="10%">#</th>
                                        <th width="30%">مبلغ القسط</th>
                                        <th width="30%">تاريخ الاستحقاق</th>
                                        <th width="30%">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody id="installment_rows">
                                    <!-- سيتم ملؤها بالجافاسكربت -->
                                </tbody>
                            </table>
                        </div>
                        <div id="remainder_notice" class="alert alert-info mt-3" style="display: none;">
                            <i class="fa fa-info-circle"></i>
                            <strong>ملاحظة:</strong> المبلغ المتبقي (<span id="remainder_value">0</span> ر.س) سيتم إضافته للقسط الأخير
                        </div>
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
            document.getElementById('number_of_installments').value = numberOfInstallments;

            // حساب توزيع الأقساط
            displayInstallmentBreakdown(grandTotal, installmentAmount, numberOfInstallments);
        } else {
            document.getElementById('number_of_installments').value = '';
            document.getElementById('installment_breakdown').style.display = 'none';
        }
    }

    function displayInstallmentBreakdown(totalAmount, installmentAmount, numberOfInstallments) {
        const breakdownDiv = document.getElementById('installment_breakdown');
        const rowsContainer = document.getElementById('installment_rows');
        const remainderNotice = document.getElementById('remainder_notice');
        const remainderValueSpan = document.getElementById('remainder_value');

        // حساب المبلغ المتبقي
        const totalBaseInstallments = installmentAmount * (numberOfInstallments - 1);
        let remainder = totalAmount - totalBaseInstallments;

        // التأكد من أن المتبقي ليس سالباً
        if (remainder < 0) {
            remainder = 0;
        }

        // عرض المبلغ المتبقي إذا كان أكبر من صفر
        if (remainder > 0) {
            remainderValueSpan.textContent = remainder.toFixed(2);
            remainderNotice.style.display = 'block';
        } else {
            remainderNotice.style.display = 'none';
        }

        // حساب تواريخ الأقساط
        const paymentRate = document.getElementById('payment_rate').value;
        const startDate = document.getElementById('due_date').value;
        let dueDate = startDate ? new Date(startDate) : new Date();

        // إنشاء صفوف الأقساط
        let rowsHTML = '';
        for (let i = 1; i <= numberOfInstallments; i++) {
            const amount = (i === numberOfInstallments)
                ? remainder
                : installmentAmount;

            // تنسيق التاريخ
            const formattedDate = dueDate.toISOString().split('T')[0];

            rowsHTML += `
                <tr>
                    <td>${i}</td>
                    <td>${amount.toFixed(2)} ر.س</td>
                    <td>${formattedDate}</td>
                    <td><span class="badge badge-warning">غير مكتمل</span></td>
                </tr>
            `;

            // حساب تاريخ الاستحقاق التالي
            dueDate = calculateNextDueDate(dueDate, paymentRate);
        }

        rowsContainer.innerHTML = rowsHTML;
        breakdownDiv.style.display = 'block';
    }

    function calculateNextDueDate(currentDate, paymentRate) {
        const date = new Date(currentDate);

        switch (parseInt(paymentRate)) {
            case 1: // شهري
                date.setMonth(date.getMonth() + 1);
                break;
            case 2: // أسبوعي
                date.setDate(date.getDate() + 7);
                break;
            case 3: // سنوي
                date.setFullYear(date.getFullYear() + 1);
                break;
            case 4: // ربع سنوي
                date.setMonth(date.getMonth() + 3);
                break;
            case 5: // مرة كل أسبوعين
                date.setDate(date.getDate() + 14);
                break;
            default: // افتراضي شهري
                date.setMonth(date.getMonth() + 1);
        }

        return date;
    }

    // تحديث تواريخ الأقساط عند تغيير معدل السداد أو تاريخ البدء
    document.getElementById('payment_rate').addEventListener('change', function() {
        if (document.getElementById('installment_breakdown').style.display === 'block') {
            calculateInstallments();
        }
    });

    document.getElementById('due_date').addEventListener('change', function() {
        if (document.getElementById('installment_breakdown').style.display === 'block') {
            calculateInstallments();
        }
    });

    // تعيين تاريخ اليوم كتاريخ افتراضي عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('due_date').value = today;
    });
</script>
@endsection

@section('styles')
<style>
    .form-group label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #5A5A5A;
    }
    .card-title {
        font-size: 1.5rem;
        color: #3F3F3F;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
    .bg-light-primary {
        background-color: #e1f0ff !important;
    }
    .table th {
        font-weight: 600;
    }
</style>
@endsection
