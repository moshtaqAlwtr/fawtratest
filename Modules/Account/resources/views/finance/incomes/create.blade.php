@extends('master')

@section('title')
    إنشاء سند قبض
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إنشاء سند قبض جديد</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('incomes.index') }}">سندات القبض</a></li>
                            <li class="breadcrumb-item active">إنشاء</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span class="text-danger">*</span> إلزامية</label>
                    </div>
                    <div>
                        <a href="{{ route('incomes.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i> إلغاء
                        </a>
                        <button type="submit" form="income_form" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i> حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="income_form" action="{{ route('incomes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        @php
                            $currency = $account_setting->currency ?? 'SAR';
                            $currencySymbol =
                                $currency == 'SAR' || empty($currency)
                                    ? '<i class="fas fa-riyal-sign"></i>'
                                    : $currency;
                        @endphp

                        <div class="form-group col-md-3">
                            <label for="amount">المبلغ <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{!! $currencySymbol !!}</span>
                                </div>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                                    required>
                            </div>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="total_amount">المبلغ الإجمالي بعد الضريبة</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{!! $currencySymbol !!}</span>
                                </div>
                                <input type="text" class="form-control" id="total_amount" readonly>
                            </div>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="description">الوصف</label>
                            <textarea class="form-control" id="description" name="description" rows="1"></textarea>
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
                            <small class="text-muted">يمكنك رفع أكثر من ملف</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="code">رقم السند</label>
                            <input type="text" class="form-control" id="code" name="code"
                                value="{{ $nextCode }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="date">التاريخ <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="treasury_id">الخزينة</label>
                            <input type="hidden" name="treasury_id" value="{{ $MainTreasury->id ?? '' }}">
                            <input type="text" class="form-control"
                                value="{{ $MainTreasury->name ?? 'لا توجد خزينة متاحة' }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="incomes_category_id">تصنيف الإيراد <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="incomes_category_id" name="incomes_category_id"
                                required>
                                <option value="">اختر تصنيف الإيراد</option>
                                @foreach ($incomes_categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="seller">البائع</label>
                            <input type="text" class="form-control" id="seller" name="seller">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="account_id">الحساب <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="account_id" name="account_id" required>
                                <option value="">اختر حساب العميل</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->customer->code ?? '' }} - {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-info" onclick="toggleTaxFields()">
                                <i class="fas fa-plus"></i> إضافة ضرائب
                            </button>
                        </div>
                    </div>

                    <div id="tax-fields" class="tax-fields mt-3" style="display: none;">
                        <div class="card">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">الضرائب</h5>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeTaxFields()">
                                        <i class="fas fa-times"></i> إزالة
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="tax1">الضريبة الأولى</label>
                                        <select class="form-control" id="tax1" name="tax1">
                                            <option value="">اختر الضريبة</option>
                                            @foreach ($taxs as $tax)
                                                <option value="{{ $tax->tax }}">{{ $tax->name }}
                                                    ({{ $tax->tax }}%)</option>
                                            @endforeach
                                        </select>
                                        <input type="number" step="0.01" class="form-control mt-2" id="tax1_amount"
                                            name="tax1_amount" placeholder="مبلغ الضريبة" readonly>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="tax2">الضريبة الثانية</label>
                                        <select class="form-control" id="tax2" name="tax2">
                                            <option value="">اختر الضريبة</option>
                                            @foreach ($taxs as $tax)
                                                <option value="{{ $tax->tax }}">{{ $tax->name }}
                                                    ({{ $tax->tax }}%)</option>
                                            @endforeach
                                        </select>
                                        <input type="number" step="0.01" class="form-control mt-2" id="tax2_amount"
                                            name="tax2_amount" placeholder="مبلغ الضريبة" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="is_recurring"
                                            name="is_recurring" value="1">
                                        <label class="custom-control-label" for="is_recurring">دفعة متكررة</label>
                                    </div>
                                </div>
                                <div class="card-body" id="recurring_fields" style="display: none;">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="recurring_frequency">تكرار الدفعة</label>
                                            <select class="form-control" id="recurring_frequency"
                                                name="recurring_frequency">
                                                <option value="weekly">أسبوعي</option>
                                                <option value="monthly">شهري</option>
                                                <option value="quarterly">ربع سنوي</option>
                                                <option value="yearly">سنوي</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="end_date">تاريخ الانتهاء</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // عرض/إخفاء حقول الضرائب
        function toggleTaxFields() {
            $('#tax-fields').slideToggle();
        }

        function removeTaxFields() {
            $('#tax-fields').slideUp();
            $('#tax1, #tax2').val('');
            $('#tax1_amount, #tax2_amount').val('');
            calculateTotal();
        }

        // عرض/إخفاء حقول الدفعة المتكررة
        $('#is_recurring').change(function() {
            if ($(this).is(':checked')) {
                $('#recurring_fields').slideDown();
            } else {
                $('#recurring_fields').slideUp();
            }
        });

        // حساب الضرائب والمبلغ الإجمالي
        function calculateTotal() {
            let amount = parseFloat($('#amount').val()) || 0;
            let tax1Rate = parseFloat($('#tax1').val()) || 0;
            let tax2Rate = parseFloat($('#tax2').val()) || 0;

            let tax1Amount = amount * (tax1Rate / 100);
            let tax2Amount = amount * (tax2Rate / 100);

            $('#tax1_amount').val(tax1Amount.toFixed(2));
            $('#tax2_amount').val(tax2Amount.toFixed(2));

            let totalAmount = amount + tax1Amount + tax2Amount;
            $('#total_amount').val(totalAmount.toFixed(2));
        }

        // استدعاء حساب الضرائب عند تغيير القيم
        $('#amount, #tax1, #tax2').on('input change', calculateTotal);

        // تأكيد الحفظ
        $('#income_form').submit(function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'تأكيد الحفظ',
                text: 'هل أنت متأكد من حفظ سند القبض؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // عرض رسالة النجاح إذا كانت موجودة
        @if (session('success'))
            Swal.fire({
                title: 'تم بنجاح',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'حسناً'
            });
        @endif

        // عرض رسالة الخطأ إذا كانت موجودة
        @if (session('error'))
            Swal.fire({
                title: 'خطأ',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'حسناً'
            });
        @endif
    </script>
@endsection
