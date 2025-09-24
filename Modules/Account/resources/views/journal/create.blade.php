@extends('master')

@section('title')
أضف قيد
@stop

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">أضف قيد</h2>
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
<div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>

                    <div>
                        <a href="{{ route('journal.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i>الغاء
                        </a>
                        <button type="submit" form="journal_form" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i>حفظ
                        </button>
                    </div>

                </div>
            </div>
        </div>
<div class="container mt-5">
    <form action="{{ route('journal.store') }}" method="POST" enctype="multipart/form-data" id="journal_form">
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
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        <!-- الصف الأول مع الكرتين الأول والثاني في نفس السطر -->
        <div class="row">
            <!-- الكرت الأول -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- التاريخ في سطر منفصل -->
                            <div class="col-md-12 mb-3">
                                <label for="date">التاريخ <span class="text-danger">*</span></label>
                                <input type="date" id="date" name="journal_entry[date]" class="form-control" required>
                            </div>

                            <!-- العملة في سطر منفصل -->
                            <div class="col-md-12 mb-3">
                                <label for="currency">العملة</label>
                                <input type="text" id="currency" name="journal_entry[currency]" class="form-control" value="SAR" readonly>
                            </div>

                            <!-- الرقم المرجعي في سطر منفصل -->
                            <div class="col-md-12 mb-3">
                                <label for="reference_number">رقم مرجعي</label>
                                <input type="text" id="reference_number" name="journal_entry[reference_number]" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الكرت الثاني -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- الوصف في سطر منفصل -->
                            <div class="col-md-12 mb-3">
                                <label for="description">الوصف <span class="text-danger">*</span></label>
                                <textarea id="description" name="journal_entry[description]" class="form-control" rows="2" required></textarea>
                            </div>

                            <!-- المرفقات في سطر منفصل -->
                            <div class="col-md-12 mb-3">
                                <label for="attachments">المرفقات</label>
                                <input type="file" name="journal_entry[attachment]" id="attachments" class="d-none">
                                <div class="upload-area border rounded p-3 text-center position-relative"
                                    onclick="document.getElementById('attachments').click()">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <i class="fas fa-cloud-upload-alt text-primary"></i>
                                        <span class="text-primary">اضغط هنا</span>
                                        <span>أو</span>
                                        <span class="text-primary">اختر من جهازك</span>
                                    </div>
                                    <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                                        <i class="fas fa-file-alt fs-3 text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول تفاصيل القيود -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="entries-table">
                        <thead>
                            <tr>
                                <th>الحساب</th>
                                <th>مدين</th>
                                <th>دائن</th>
                                <th>البيان</th>
                                <th>مركز التكلفة</th>
                                <th>نوع الضريبة</th>
                                <th>نسبة الضريبة</th>
                                <th>قيمة الضريبة</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    @if(!empty($accountPath))
                              <ul>
                                         @foreach($accountPath as $parent)
                                     <li>{{ $parent->name }}</li>
                                                              @endforeach
                                               </ul>
                                             @endif

                                             <select name="details[0][account_id]" class="form-control account-search" required>
                                                <option value="">اختر الحساب</option>
                                                @foreach($sortedAccounts as $account)
                                                <option value="{{ $account->id }}">
                                                    @php
                                                        $indentation = str_repeat('—', $account->level);  // حساب المسافة البادئة بناءً على المستوى
                                                    @endphp
                                                    {{ $indentation }} {{ $account->name }} ({{ $account->code }})
                                                </option>
                                            @endforeach
                                            </select>
                                            
                                </td>
                                <td>
                                    <input type="number" name="details[0][debit]" class="form-control debit calc-tax" min="0" step="0.01">
                                </td>
                                <td>
                                    <input type="number" name="details[0][credit]" class="form-control credit calc-tax" min="0" step="0.01">
                                </td>
                                <td>
                                    <input type="text" name="details[0][description]" class="form-control">
                                </td>
                                <td>
                                    <select name="details[0][cost_center_id]" class="form-control">
                                        <option value="">اختر مركز التكلفة</option>
                                        @foreach($costCenters as $center)
                                            <option value="{{ $center->id }}">{{ $center->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="details[0][tax_type]" class="form-control tax-type calc-tax">
                                        <option value="">بدون ضريبة</option>
                                        <option value="vat">ضريبة القيمة المضافة</option>
                                        <option value="tax">ضريبة</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="details[0][tax_rate]" class="form-control tax-rate calc-tax">
                                        <option value="0">0%</option>
                                        <option value="15">15%</option>
                                        <option value="5">5%</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="details[0][tax_amount]" class="form-control tax-amount" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <button type="button" class="btn btn-primary" id="add-row">
                                        <i class="fas fa-plus"></i> إضافة سطر
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>المجموع</td>
                                <td id="total-debit">0.00</td>
                                <td id="total-credit">0.00</td>
                                <td id="total-tax">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@section('scripts')
<!-- إضافة مكتبة Select2 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    let rowCount = 1;

    // تهيئة Select2 للعناصر الموجودة عند تحميل الصفحة
    $('.account-search').select2({
        placeholder: "ابحث عن الحساب أو الكود...",
        allowClear: true,
        width: '100%',
        // تفعيل البحث في الحقلين: الاسم والكود
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }
            if (data.text.toLowerCase().includes(params.term.toLowerCase()) || data.id.includes(params.term)) {
                return data;
            }
            return null;
        }
    });

    // إضافة سطر جديد
    $('#add-row').click(function() {
        let newRow = `
            <tr>
                <td>
                    <select name="details[${rowCount}][account_id]" class="form-control account-search" required>
                        <option value="">اختر الحساب</option>
                          @foreach($sortedAccounts as $account)
                                                <option value="{{ $account->id }}">
                                                    @php
                                                        $indentation = str_repeat('—', $account->level);  // حساب المسافة البادئة بناءً على المستوى
                                                    @endphp
                                                    {{ $indentation }} {{ $account->name }} ({{ $account->code }})
                                                </option>
                                            @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="details[${rowCount}][debit]" class="form-control debit calc-tax" min="0" step="0.01">
                </td>
                <td>
                    <input type="number" name="details[${rowCount}][credit]" class="form-control credit calc-tax" min="0" step="0.01">
                </td>
                <td>
                    <input type="text" name="details[${rowCount}][description]" class="form-control">
                </td>
                <td>
                    <select name="details[${rowCount}][cost_center_id]" class="form-control">
                        <option value="">اختر مركز التكلفة</option>
                        @foreach($costCenters as $center)
                            <option value="{{ $center->id }}">{{ $center->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="details[${rowCount}][tax_type]" class="form-control tax-type calc-tax">
                        <option value="">بدون ضريبة</option>
                        <option value="vat">ضريبة القيمة المضافة</option>
                        <option value="tax">ضريبة</option>
                    </select>
                </td>
                <td>
                    <select name="details[${rowCount}][tax_rate]" class="form-control tax-rate calc-tax">
                        <option value="0">0%</option>
                        <option value="15">15%</option>
                        <option value="5">5%</option>
                    </select>
                </td>
                <td>
                    <input type="number" name="details[${rowCount}][tax_amount]" class="form-control tax-amount" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;

        let newRowElement = $(newRow);
        $('#entries-table tbody').append(newRowElement);

        // تهيئة Select2 فقط للعناصر الجديدة مع ضبط العرض لمنع التمدد
        newRowElement.find('.account-search').select2({
            placeholder: "ابحث عن الحساب...",
            allowClear: true,
            width: '100%'
        });

        rowCount++;
        updateTotals();
    });

    // حذف سطر
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        updateTotals();
    });

    // تحديث المجاميع عند تغيير الإدخالات
    $(document).on('input', '.debit, .credit', function() {
        updateTotals();
    });

    // حساب الضريبة لكل سطر عند تغيير القيم
    $(document).on('change', '.calc-tax', function() {
        let row = $(this).closest('tr');
        calculateRowTax(row);
        updateTotals();
    });

    function calculateRowTax(row) {
        let debit = parseFloat(row.find('.debit').val()) || 0;
        let credit = parseFloat(row.find('.credit').val()) || 0;
        let taxType = row.find('.tax-type').val();
        let taxRate = parseFloat(row.find('.tax-rate').val()) || 0;
        let amount = debit > 0 ? debit : credit;
        let taxAmount = 0;

        if (taxType && amount > 0) {
            taxAmount = (amount * taxRate) / 100;
        }

        row.find('.tax-amount').val(taxAmount.toFixed(2));
    }

    function updateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;
        let totalTax = 0;

        $('.debit').each(function() {
            totalDebit += parseFloat($(this).val()) || 0;
        });

        $('.credit').each(function() {
            totalCredit += parseFloat($(this).val()) || 0;
        });

        $('.tax-amount').each(function() {
            let taxAmount = parseFloat($(this).val()) || 0;
            totalTax += taxAmount;
            let row = $(this).closest('tr');
            if (row.find('.debit').val() > 0) {
                totalDebit += taxAmount;
            } else {
                totalCredit += taxAmount;
            }
        });

        $('#total-debit').text(totalDebit.toFixed(2));
        $('#total-credit').text(totalCredit.toFixed(2));
        $('#total-tax').text(totalTax.toFixed(2));

        if (totalDebit !== totalCredit) {
            $('#total-debit, #total-credit').addClass('text-danger');
        } else {
            $('#total-debit, #total-credit').removeClass('text-danger');
        }
    }
});
</script>
@endsection
