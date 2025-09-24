@extends('master')

@section('title')
    تعديل قاعدة عمولة
@stop

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
    .text-danger { font-size: 0.9em; }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تعديل قاعدة عمولة</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                        <li class="breadcrumb-item active">تعديل</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <form id="clientForm" action="{{ route('commission.update', $Commission->id) }}" method="POST" enctype="multipart/form-data">
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
                                            <input type="text" id="name" class="form-control" name="name"
                                                value="{{ old('name', $Commission->name) }}" required>
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- الحالة -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="status">الحالة</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="active" {{ old('status', $Commission->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                                <option value="deactive" {{ old('status', $Commission->status) == 'deactive' ? 'selected' : '' }}>غير نشط</option>
                                            </select>
                                            @error('status')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- الفترة -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="period">الفترة</label>
                                            <select class="form-control" id="period" name="period">
                                                <option value="quarterly" {{ old('period', $Commission->period) == 'quarterly' ? 'selected' : '' }}>ربع سنوي</option>
                                                <option value="yearly" {{ old('period', $Commission->period) == 'yearly' ? 'selected' : '' }}>سنوي</option>
                                                <option value="monthly" {{ old('period', $Commission->period) == 'monthly' ? 'selected' : '' }}>شهري</option>
                                            </select>
                                            @error('period')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- حساب العمولة -->
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="commission_calculation">حساب العمولة</label>
                                            <select class="form-control" id="commission_calculation" name="commission_calculation">
                                                <option value="fully_paid" {{ old('commission_calculation', $Commission->commission_calculation) == 'fully_paid' ? 'selected' : '' }}>فواتير مدفوعة بالكامل</option>
                                                <option value="partially_paid" {{ old('commission_calculation', $Commission->commission_calculation) == 'partially_paid' ? 'selected' : '' }}>فواتير مدفوعة جزئيا</option>
                                            </select>
                                            @error('commission_calculation')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- الموظفين -->
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="employees">الموظفين</label>
                                            <select id="employees" class="form-control select2" name="employee_id[]" multiple="multiple">
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}"
                                                        {{ in_array($employee->id, old('employee_id', $CommissionUsers)) ? 'selected' : '' }}>
                                                        {{ $employee->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('employee_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                      <!-- بنود العمولة -->
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
                                                            @php
                                                                // للبنود: لو فيه old() ارسم القيم القديمة، وإلا البيانات القديمة من القاعدة
                                                                $commissionItems = old('items', $CommissionProducts->map(function($prod) {
                                                                    return [
                                                                        'product_id' => $prod->product_id,
                                                                        'commission_percentage' => $prod->commission_percentage
                                                                    ];
                                                                })->toArray());
                                                            @endphp
                                                            <tbody>
                                                            @foreach($commissionItems as $index => $item)
                                                                <tr class="item-row">
                                                                    <td>
                                                                        <select name="items[{{ $index }}][product_id]" class="form-control product-select" required>
                                                                            <option value="">اختر البند</option>
                                                                            <option value="0" {{ old("items.$index.product_id", $item['product_id']) == '0' ? 'selected' : '' }}>كل المنتجات</option>
                                                                            @foreach($products as $product)
                                                                                <option value="{{ $product->id }}"
                                                                                    {{ old("items.$index.product_id", $item['product_id']) == $product->id ? 'selected' : '' }}>
                                                                                    {{ $product->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error("items.$index.product_id")
                                                                            <span class="text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="number" name="items[{{ $index }}][commission_percentage]" class="form-control tax"
                                                                                value="{{ old("items.$index.commission_percentage", $item['commission_percentage']) }}"
                                                                                min="0" max="100" step="0.01" required>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                        @error("items.$index.commission_percentage")
                                                                            <span class="text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
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
                                                        <!-- رسالة خطأ عامة للبنود (مثل اختيار كل المنتجات مع غيرها) -->
                                                        @if ($errors->has('items.0.product_id'))
                                                            <div class="alert alert-danger mt-2">
                                                                {{ $errors->first('items.0.product_id') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- هاية بنود العمولة --!>
                                    
                                    <!-- نوع الهدف -->
                                    </hr>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <div class="custom-control custom-radio custom-control-inline mx-0">
                                                <input id="target_revenue_radio" class="custom-control-input target_type"
                                                    required name="target_type" type="radio" value="amount"
                                                    {{ old('target_type', $Commission->target_type) == 'amount' ? 'checked' : '' }}>
                                                <label for="target_revenue_radio" class="custom-control-label">المبلغ المستهدف <span class="required">*</span></label>
                                            </div>
                                            <input id="target_amount_revenue" class="form-control mt-1" min="0" step="0.01"
                                                placeholder="المبلغ المستهدف" name="value" type="number"
                                                value="{{ old('target_type', $Commission->target_type) == 'amount' ? old('value', $Commission->value) : '' }}">
                                            @if(old('target_type', $Commission->target_type) == 'amount')
                                                @error('value')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <div class="custom-control custom-radio custom-control-inline mx-0">
                                                <input id="target_volume_radio" class="custom-control-input target_type"
                                                    required name="target_type" type="radio" value="quantity"
                                                    {{ old('target_type', $Commission->target_type) == 'quantity' ? 'checked' : '' }}>
                                                <label for="target_volume_radio" class="custom-control-label">الكمية المستهدفة <span class="required">*</span></label>
                                            </div>
                                            <input id="target_amount_volume" class="form-control mt-1" placeholder="ادخل قيمة موجبة"
                                                name="value" type="number"
                                                value="{{ old('target_type', $Commission->target_type) == 'quantity' ? old('value', $Commission->value) : '' }}">
                                            @if(old('target_type', $Commission->target_type) == 'quantity')
                                                @error('value')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            @endif
                                        </div>
                                    </div>
                                    <!-- الملاحظات -->
                                    <div class="col-md-12 mb-3">
                                        <label for="notes">الملاحظات</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="5" style="resize: none;">{{ old('notes', $Commission->notes) }}</textarea>
                                        @error('notes')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <hr>
                                 
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
    let productsOptions = `
        <option value="">اختر البند</option>
        <option value="0">كل المنتجات</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
    `;

    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "اختر الموظفين",
            allowClear: true,
            width: '100%'
        });
        $('.product-select').select2({
            placeholder: "اختر البند",
            width: '100%'
        });

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
            $('#items-table tbody tr:last .product-select').select2({
                placeholder: "اختر البند",
                width: '100%'
            });
            updateProductSelectOptions();
        });

        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
            updateProductSelectOptions();
        });

        $(document).on('change', '.product-select', function () {
            updateProductSelectOptions();
        });

        function updateProductSelectOptions() {
            let selectedValues = [];
            $('.product-select').each(function () {
                let val = $(this).val();
                if (val !== '' && val !== null) selectedValues.push(val);
            });

            $('.product-select').each(function () {
                let $select = $(this);
                let currentVal = $select.val();
                $select.find('option').prop('disabled', false);

                $('.product-select').not($select).each(function () {
                    let otherVal = $(this).val();
                    if (otherVal !== '' && otherVal !== null) {
                        $select.find('option[value="' + otherVal + '"]').prop('disabled', true);
                    }
                });
                $select.find('option[value="' + currentVal + '"]').prop('disabled', false);
            });
            $('.product-select').select2({
                placeholder: "اختر البند",
                width: '100%'
            });
        }

        // نفس toggle radio كما في الانشاء
        const revenueInput = document.getElementById('target_amount_revenue');
        const volumeInput = document.getElementById('target_amount_volume');
        const revenueRadio = document.getElementById('target_revenue_radio');
        const volumeRadio = document.getElementById('target_volume_radio');

        function toggleInputs() {
            if (revenueRadio && volumeRadio && revenueInput && volumeInput) {
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
        }
        toggleInputs();
        if (revenueRadio) revenueRadio.addEventListener('change', toggleInputs);
        if (volumeRadio) volumeRadio.addEventListener('change', toggleInputs);
    });
</script>
