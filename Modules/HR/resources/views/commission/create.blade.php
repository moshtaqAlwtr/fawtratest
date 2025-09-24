@extends('master')

@section('title')
    اضافة قاعدة عمولة
@stop

<!-- شيفرة التنسيق و Select2 -->
<style>
    hr {
        border: none;
        height: 2px;
        background-color: #ac9191;
        width: 100%;
        margin: 20px auto;
    }
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        padding-top: 4px;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">اضافة قاعدة عمولة</h2>
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

<div class="content-body">
    <form id="clientForm" action="{{ route('commission.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>
                    <div>
                        <a href="{{ route('commission.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i>الغاء
                        </a>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i>حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- بيانات عامة -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">معلومات قواعد العمولة</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                    <!-- الاسم -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="name">الاسم <span style="color:red">*</span></label>
                                            <input type="text" id="name" class="form-control" name="name" required>
                                        </div>
                                    </div>
                                    <!-- الحالة -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="status">الحالة</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="active">نشط</option>
                                                <option value="deactive">غير نشط</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- الفترة -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="period">الفترة</label>
                                            <select class="form-control" id="period" name="period">
                                                <option value="quarterly">ربع سنوي</option>
                                                <option value="yearly">سنوي</option>
                                                <option value="monthly">شهري</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- حساب العمولة -->
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="commission_calculation">حساب العمولة</label>
                                            <select class="form-control" id="commission_calculation" name="commission_calculation">
                                                <option value="fully_paid">فواتير مدفوعة بالكامل</option>
                                                <option value="partially_paid">فواتير مدفوعة جزئيا</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- الموظفين -->
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="employees">الموظفين</label>
                                            <select id="employees" class="form-control select2" name="employee_id[]" multiple="multiple">
    @foreach($employees as $employee)
        <option value="{{ $employee->id }}"
            {{ (collect(old('employee_id'))->contains($employee->id)) ? 'selected' : '' }}>
            {{ $employee->name }}
        </option>
    @endforeach
</select>
@if($errors->has('employee_id'))
    <div class="text-danger mt-1">{{ $errors->first('employee_id') }}</div>
@endif

                                        </div>
                                    </div>

                                    <hr>

                                    <!-- اضافة بند -->
                                    <div class="col-md-12 mb-3">
                                        <div class="card">
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table" id="items-table">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:40%">البند <span style="color:red">*</span></th>
                                                                    <th style="width:30%">نسبة العمولة <span style="color:red">*</span></th>
                                                                    <th style="width:15%">الضبط</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="item-row">
                                                                    <td>
                                                                        <select name="items[0][product_id]" class="form-control product-select" required>
                                                                            <option value="">اختر البند</option>
                                                                            <option value="0">كل المنتجات</option>
                                                                            @foreach($products as $product)
                                                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="number" name="items[0][commission_percentage]" class="form-control tax" placeholder="ادخل النسبة هنا" min="0" max="100" step="0.01" required>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="3" class="text-right">
                                                                        <button type="button" id="add-row" class="btn btn-success">
                                                                            <i class="fa fa-plus"></i> إضافة صف
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- نوع الهدف -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-radio custom-control-inline mx-0">
                                                <input id="target_revenue_radio" class="custom-control-input target_type" required checked name="target_type" type="radio" value="amount">
                                                <label for="target_revenue_radio" class="custom-control-label">المبلغ المستهدف <span class="required">*</span></label>
                                            </div>
                                            <input id="target_amount_revenue" class="form-control mt-1" min="0" step="0.01" placeholder="المبلغ المستهدف" required name="value" type="number">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-radio custom-control-inline mx-0">
                                                <input id="target_volume_radio" class="custom-control-input target_type" required name="target_type" type="radio" value="quantity">
                                                <label for="target_volume_radio" class="custom-control-label">الكمية المستهدفة <span class="required">*</span></label>
                                            </div>
                                            <input id="target_amount_volume" class="form-control mt-1" placeholder="ادخل قيمة موجبة" required name="value" type="number">
                                        </div>
                                    </div>

                                    <!-- الملاحظات -->
                                    <div class="col-md-12 mb-3">
                                        <label for="notes">الملاحظات</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="5" style="resize: none;">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

<script>
    // الخيارات HTML للمنتجات
    let productsOptions = `
        <option value="">اختر البند</option>
        <option value="0">كل المنتجات</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
    `;

    $(document).ready(function () {
        // تفعيل Select2 لأول مرة
        $('.select2').select2({
            placeholder: "اختر الموظفين",
            allowClear: true,
            width: '100%'
        });
        $('.product-select').select2({
            placeholder: "اختر البند",
            width: '100%'
        });

        // إضافة صف جديد
        $('#add-row').click(function () {
            let rowCount = $('#items-table tbody tr').length;
            let newRow = `
                <tr class="item-row">
                    <td>
                        <select name="items[${rowCount}][product_id]" class="form-control product-select" required>
                            ${productsOptions}
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" name="items[${rowCount}][commission_percentage]" class="form-control tax" placeholder="ادخل النسبة هنا" min="0" max="100" step="0.01" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#items-table tbody').append(newRow);

            // تفعيل Select2 في الصف الجديد فقط
            $('#items-table tbody tr:last .product-select').select2({
                placeholder: "اختر البند",
                width: '100%'
            });

            updateProductSelectOptions();
        });

        // حذف صف
        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
            updateProductSelectOptions();
        });

        // منع تكرار المنتج أو "كل المنتجات"
        $(document).on('change', '.product-select', function () {
            updateProductSelectOptions();
        });

        // دالة منع التكرار
        function updateProductSelectOptions() {
            let selectedValues = [];
            $('.product-select').each(function () {
                let val = $(this).val();
                if (val !== '' && val !== null) selectedValues.push(val);
            });

            $('.product-select').each(function () {
                let $select = $(this);
                let currentVal = $select.val();

                // تفعيل جميع الخيارات أولاً
                $select.find('option').prop('disabled', false);

                // تعطيل القيم المختارة في بقية الصفوف
                $('.product-select').not($select).each(function () {
                    let otherVal = $(this).val();
                    if (otherVal !== '' && otherVal !== null) {
                        $select.find('option[value="' + otherVal + '"]').prop('disabled', true);
                    }
                });

                // إبقاء الخيار الحالي مفعل حتى لو هو مكرر (ضروري لـ Select2)
                $select.find('option[value="' + currentVal + '"]').prop('disabled', false);
            });

            // إعادة تفعيل Select2 ليظهر التعطيل
            $('.product-select').select2({
                placeholder: "اختر البند",
                width: '100%'
            });
        }

        // منع إرسال النموذج إذا كان فيه نسبة فارغة
        $('#clientForm').on('submit', function (e) {
            let isValid = true;
            $('.tax').each(function () {
                if ($(this).val() === '' || $(this).val() === null) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            if (!isValid) {
                e.preventDefault();
                alert('يرجى تعبئة جميع نسب العمولة وعدم ترك أي حقل فارغ');
            }
        });

        // تفعيل وإلغاء الحقول حسب نوع الهدف
        const revenueInput = document.getElementById('target_amount_revenue');
        const volumeInput = document.getElementById('target_amount_volume');
        const revenueRadio = document.getElementById('target_revenue_radio');
        const volumeRadio = document.getElementById('target_volume_radio');

        function toggleInputs() {
            if (revenueRadio.checked) {
                revenueInput.disabled = false;
                revenueInput.readOnly = false;
                revenueInput.style.backgroundColor = '';
                volumeInput.disabled = true;
                volumeInput.readOnly = true;
                volumeInput.style.backgroundColor = '#f2f2f2';
            } else if (volumeRadio.checked) {
                volumeInput.disabled = false;
                volumeInput.readOnly = false;
                volumeInput.style.backgroundColor = '';
                revenueInput.disabled = true;
                revenueInput.readOnly = true;
                revenueInput.style.backgroundColor = '#f2f2f2';
            }
        }
        toggleInputs();
        revenueRadio.addEventListener('change', toggleInputs);
        volumeRadio.addEventListener('change', toggleInputs);
    });
</script>
