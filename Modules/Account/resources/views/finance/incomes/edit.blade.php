@extends('master')

@section('title')
    تعديل سند قبض
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> تعديل سند قبض </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">اضافه
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

  @include('layouts.alerts.error')
        @include('layouts.alerts.success')

    <div class="content-body">

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>

                    <div>
                        <a href="{{ route('incomes.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i>الغاء
                        </a>

                        <button type="submit" form="incomes_form" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i>حفظ
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="incomes_form" action="{{ route('incomes.update', $income->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        @php
                            $currency = $account_setting->currency ?? 'SAR';
                            $currencySymbol =
                                $currency == 'SAR' || empty($currency)
                                    ? '<img src="' .
                                        asset('assets/images/Saudi_Riyal.svg') .
                                        '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                                    : htmlspecialchars($currency);
                        @endphp

                        <div class="form-group col-md-3">
                            <label for="amount">المبلغ <span style="color: red">*</span></label>
                            <input type="text" class="form-control form-control-lg py-3" id="amount"
                                placeholder="{{ $currencySymbol }} 0.00" name="amount" value="{{ old('amount', $income->amount) }}">
                            @error('amount')
                                <span class="text-danger" id="basic-default-name-error">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-3">
                            <label for="total_amount">المبلغ الإجمالي بعد الضريبة</label>
                            <input type="text" class="form-control form-control-lg py-3" id="total_amount"
                                placeholder="{{ $currencySymbol }} 0.00" name="total_amount" readonly value="{{ old('total_amount', $income->total_amount) }}">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="description">الوصف</label>
                            <textarea class="form-control" id="description" rows="3" name="description">{{ old('description', $income->description) }}</textarea>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="attachments">المرفقات</label>
                            <input type="file" name="attachments[]" id="attachments" class="d-none" multiple>
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
                            <!-- عرض المرفقات الحالية إن وجدت -->
                            @if($income->attachments)
                                <div class="mt-2">
                                    @foreach(json_decode($income->attachments) as $attachment)
                                        <div>{{ basename($attachment) }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="code-number">رقم الكود</label>
                            <input type="text" class="form-control" id="code-number" name="code"
                                value="{{ old('code', $income->code) }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="date">التاريخ</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $income->date) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="category">التصنيف</label>
                            <select id="category" class="form-control" name="incomes_category_id">
                                <option selected disabled>-- إضافة تصنيف --</option>
                                @foreach ($incomes_categories as $incomes_category)
                                    <option value="{{ $incomes_category->id }}"
                                        {{ old('incomes_category_id', $income->incomes_category_id) == $incomes_category->id ? 'selected' : '' }}>
                                        {{ $incomes_category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="seller">البائع</label>
                            <select id="seller" class="form-control" name="vendor_id">
                                <option selected disabled>اختر بائع</option>
                                <option value="1" {{ old('vendor_id', $income->vendor_id) == 1 ? 'selected' : '' }}>بائع 1</option>
                                <option value="2" {{ old('vendor_id', $income->vendor_id) == 2 ? 'selected' : '' }}>بائع 2</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="warehouse">خزينة</label>
                            <input type="text" class="form-control" placeholder=" "
                                value="{{ $MainTreasury->name ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="min-limit">الحساب الفرعي </label>
                            <div class="form-group">
                                <label for="account_id">الحساب</label>
                                <select class="form-control select2" name="account_id" id="account_id" required>
                                    <option value="" selected disabled>اختر حساب</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}"
                                            {{ old('account_id', $income->account_id) == $account->id ? 'selected' : '' }}>
                                            {{ $account->customer->code ?? '' }} - {{ $account->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="tax">الضرائب</label>
                            <button type="button" class="btn btn-info btn-block" onclick="toggleTaxFields()">إضافة
                                ضرائب</button>
                        </div>
                    </div>

                    <!-- حقول الضرائب -->
                    <div id="tax-fields" class="tax-fields" style="{{ old('tax1') || $income->tax1 ? 'display: block;' : 'display: none;' }}">
                        <span class="remove-tax" onclick="removeTaxFields()">إزالة الضرائب ×</span>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="tax1">الضريبة الأولى</label>
                                <select id="tax1" class="form-control" name="tax1">
                                    <option>اختر الضريبة</option>
                                    @foreach ($taxs as $tax)
                                        <option value="{{ $tax->tax }}"
                                            {{ old('tax1', $income->tax1) == $tax->tax ? 'selected' : '' }}>
                                            {{ $tax->name ?? '' }}</option>
                                    @endforeach
                                </select>
                                <input type="text" class="form-control mt-2" placeholder="المبلغ" name="tax1_amount"
                                    value="{{ old('tax1_amount', $income->tax1_amount) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tax2">الضريبة الثانية</label>
                                <select id="tax2" class="form-control" name="tax2">
                                    <option>اختر الضريبة</option>
                                    @foreach ($taxs as $tax)
                                        <option value="{{ $tax->tax }}"
                                            {{ old('tax2', $income->tax2) == $tax->tax ? 'selected' : '' }}>
                                            {{ $tax->name ?? '' }}</option>
                                    @endforeach
                                </select>
                                <input type="text" class="form-control mt-2" placeholder="المبلغ" name="tax2_amount"
                                    value="{{ old('tax2_amount', $income->tax2_amount) }}">
                            </div>
                        </div>
                    </div>

                    <div class="container mt-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-2">
                                        <label for="checkbox">مكرر</label>
                                        <input type="checkbox" id="checkbox" name="is_recurring" value="1"
                                            {{ old('is_recurring', $income->is_recurring) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <!-- حقل التكرار و تاريخ الإنتهاء يظهر عند تحديد الـ checkbox -->
                                <div class="row" id="duplicate-options-container" style="{{ old('is_recurring', $income->is_recurring) ? 'display: block;' : 'display: none;' }}">
                                    <div class="form-group col-md-4">
                                        <label for="duplicate-options">التكرار</label>
                                        <select id="duplicate-options" class="form-control" name="recurring_interval">
                                            <option value="">حدد التكرار</option>
                                            <option value="weekly" {{ old('recurring_interval', $income->recurring_interval) == 'weekly' ? 'selected' : '' }}>إسبوعي</option>
                                            <option value="bi-weekly" {{ old('recurring_interval', $income->recurring_interval) == 'bi-weekly' ? 'selected' : '' }}>كل أسبوعين</option>
                                            <option value="monthly" {{ old('recurring_interval', $income->recurring_interval) == 'monthly' ? 'selected' : '' }}>شهري</option>
                                            <option value="yearly" {{ old('recurring_interval', $income->recurring_interval) == 'yearly' ? 'selected' : '' }}>سنوي</option>
                                            <option value="daily" {{ old('recurring_interval', $income->recurring_interval) == 'daily' ? 'selected' : '' }}>يومي</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- حقل تاريخ الإنتهاء -->
                                <div class="row" id="end-date-container" style="{{ old('is_recurring', $income->is_recurring) ? 'display: block;' : 'display: none;' }}">
                                    <div class="form-group col-md-4">
                                        <label for="end-date">تاريخ الإنتهاء</label>
                                        <input type="date" class="form-control" id="end-date" name="end_date"
                                            value="{{ old('end_date', $income->end_date) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <!-- JavaScript للتحكم في إظهار وإخفاء الخيارات -->
    <script>
        document.getElementById('checkbox').addEventListener('change', function() {
            var duplicateOptionsContainer = document.getElementById('duplicate-options-container');
            var endDateContainer = document.getElementById('end-date-container');
            if (this.checked) {
                duplicateOptionsContainer.style.display = 'block'; // إظهار خيارات التكرار
                endDateContainer.style.display = 'block'; // إظهار حقل تاريخ الإنتهاء
            } else {
                duplicateOptionsContainer.style.display = 'none'; // إخفاء خيارات التكرار
                endDateContainer.style.display = 'none'; // إخفاء حقل تاريخ الإنتهاء
            }
        });
    </script>
    <script>
        function toggleTaxFields() {
            $("#tax-fields").slideToggle();
        }

        function removeTaxFields() {
            $("#tax-fields").slideUp();
        }
    </script>
    <!-- إضافة مكتبة jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



    <script>
        $(document).ready(function() {
            function calculateTax() {
                let amount = parseFloat($("#amount").val()) || 0; // المبلغ الأساسي
                let tax1Rate = parseFloat($("#tax1").val()) || 0; // نسبة الضريبة الأولى
                let tax2Rate = parseFloat($("#tax2").val()) || 0; // نسبة الضريبة الثانية

                // حساب قيمة الضرائب
                let tax1Amount = (amount * tax1Rate) / 100;
                let tax2Amount = (amount * tax2Rate) / 100;

                // تحديث الحقول بقيم الضرائب
                $("input[name='tax1_amount']").val(tax1Amount.toFixed(2));
                $("input[name='tax2_amount']").val(tax2Amount.toFixed(2));

                // حساب المجموع النهائي مع الضرائب
                let totalAmount = amount + tax1Amount + tax2Amount;
                $("#total_amount").val(totalAmount.toFixed(2));
            }

            // عند إدخال المبلغ أو تغيير الضريبة يتم تحديث الحساب
            $("#amount, #tax1, #tax2").on("input change", calculateTax);
        });
    </script>
@endsection
