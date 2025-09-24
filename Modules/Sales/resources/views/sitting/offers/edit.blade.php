@extends('master')

@section('title')
    تعديل عرض
@stop
<!-- jQuery FIRST -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Select2 Arabic -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل عرض </h2>
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
    @include('layouts.alerts.error')
    @include('layouts.alerts.success')
    <div class="content-body">
        <div class="container-fluid">
            <form class="form-horizontal" action="{{ route('Offers.update', $offer->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
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
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title"> تفاصيل العرض </h4>
                        </div>

                        <div class="card-body">
                            <div class="form-body row">

                                <!-- اسم العرض -->
                                <div class="form-group col-md-12">
                                    <label for="">الاسم <span style="color: red">*</span></label>
                                    <input type="text" id="feedback2" class="form-control" placeholder="الاسم"
                                        name="name" value="{{ old('name', $offer->name) }}">
                                    @error('name')
                                        <small class="text-danger" id="basic-default-name-error" class="error">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <!-- صالح من -->
                                <div class="form-group col-md-6">
                                    <label for="">صالح من <span style="color: red">*</span></label>
                                    <input type="date" name="valid_from" class="form-control"
    value="{{ old('valid_from', $offer->valid_from ? $offer->valid_from->format('Y-m-d') : '') }}">
                                </div>

                                <!-- صالح الى -->
                                <div class="form-group col-md-6">
                                    <label for="">صالح الى <span style="color: red">*</span></label>
                                    <input type="date" name="valid_to" class="form-control"
                                    value="{{ old('valid_to', $offer->valid_to ? $offer->valid_to->format('Y-m-d') : '') }}">
                                
                                </div>

                                <!-- نوع العرض -->
                                <div class="form-group col-md-6">
                                    <label for="">النوع</label>
                                    <select class="form-control" name="type">
                                        <option value="1" {{ old('type', $offer->type) == 1 ? 'selected' : '' }}>خصم على البند</option>
                                        <option value="2" {{ old('type', $offer->type) == 2 ? 'selected' : '' }}>اشتري كمية واحصل خصم على البند</option>
                                    </select>
                                </div>

                                <!-- الكمية المطلوبة -->
                                <div class="form-group col-md-6">
                                    <label for="">الكمية المطلوبة لتطبيق العرض <span style="color: red">*</span></label>
                                    <input type="text" name="quantity" class="form-control" id="quantity"
                                        value="{{ old('quantity', $offer->quantity) }}">
                                </div>

                                <!-- نوع الخصم -->
                                <div class="form-group col-md-6">
                                    <label for="">نوع الخصم </label>
                                    <select class="form-control" name="discount_type">
                                        <option value="1" {{ old('discount_type', $offer->discount_type) == 1 ? 'selected' : '' }}>خصم حقيقي</option>
                                        <option value="2" {{ old('discount_type', $offer->discount_type) == 2 ? 'selected' : '' }}>خصم نسبي</option>
                                    </select>
                                </div>

                                <!-- قيمة الخصم -->
                                <div class="form-group col-md-6">
                                    <label for="">قيمة الخصم <span style="color: red">*</span></label>
                                    <input type="text" name="discount_value" class="form-control" id=""
                                        value="{{ old('discount_value', $offer->discount_value) }}">
                                </div>

                                

                                <!-- العميل -->
                              <!-- حقل العملاء -->
<div class="form-group col-md-6">
    <label for="">العميل</label>
    <select class="form-control select2" name="client_id[]" multiple="multiple">
        @foreach ($clients as $client)
            <option value="{{ $client->id }}"
                {{ in_array($client->id, $offer->clients->pluck('id')->toArray()) ? 'selected' : '' }}>
                {{ $client->trade_name }}
            </option>
        @endforeach
    </select>
</div>

                                <!-- التفعيل -->
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            {{ old('is_active', $offer->is_active) == 1 ? 'checked' : '' }} style="width: 3rem; height: 1.5rem;">
                                        <label class="form-check-label fw-bold"
                                            style="color: #34495e; margin-right: 20px">نشط</label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">تفاصيل وحدات العرض</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-body row">
                                <!-- نوع الوحدة -->
                                <div class="form-group col-md-6">
                                    <label for="">نوع الوحدة</label>
                                    <select class="form-control" name="unit_type">
                                        <option value="1" {{ old('unit_type', $offer->unit_type) == 1 ? 'selected' : '' }}>الكل</option>
                                        <option value="2" {{ old('unit_type', $offer->unit_type) == 2 ? 'selected' : '' }}>التصنيف</option>
                                        <option value="3" {{ old('unit_type', $offer->unit_type) == 3 ? 'selected' : '' }}>المنتجات</option>
                                    </select>
                                </div>

                             <!-- حقل المنتجات -->
<div class="form-group col-md-6">
    <label for="">المنتج</label>
    <select class="form-control select2" name="product_id[]" multiple="multiple">
        @foreach ($products as $prod)
            <option value="{{ $prod->id }}"
                {{ in_array($prod->id, $offer->products->pluck('id')->toArray()) ? 'selected' : '' }}>
                {{ $prod->name }}
            </option>
        @endforeach
    </select>
</div>

                               <!-- حقل التصنيفات -->
<div class="form-group col-md-6">
    <label for="">التصنيف</label>
    <select class="form-control select2" name="category_id[]" multiple="multiple">
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}"
                {{ in_array($cat->id, $offer->categories->pluck('id')->toArray()) ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
</div>

                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // عند تغيير حقل "نوع الوحدة"
        $('select[name="unit_type"]').change(function() {
            var selectedValue = $(this).val();
            var productField = $('select[name="product_id"]').closest('.form-group');
            var categoryField = $('select[name="category_id"]').closest('.form-group');

            productField.hide();
            categoryField.hide();

            if (selectedValue === "3") {
                productField.show();
            } else if (selectedValue === "2") {
                categoryField.show();
            } else {
                productField.hide();
                categoryField.hide();
            }
        });

        // إخفاء الحقول عند التحميل الأولي
        $('select[name="product_id"], select[name="category_id"]').closest('.form-group').hide();

        // عند تغيير حقل "النوع"
        $('select[name="type"]').change(function() {
            var selectedValue = $(this).val();
            var quantityInput = $('input[name="quantity"]');

            if (selectedValue === "") {
                quantityInput.val("0").prop("readonly", true);
            } else if (selectedValue === "discount") {
                quantityInput.val("").prop("readonly", false);
            }
        });

        // تعيين الحالة الأولية عند تحميل الصفحة
        if ($('select[name="type"]').val() === "") {
            $('input[name="quantity"]').val("0").prop("readonly", true);
        }
    });
    $(document).ready(function() {
    // تهيئة Select2
    $('.select2').select2({
        language: 'ar',
        dir: 'rtl',
        width: '100%'
    });

    // التحكم في إظهار/إخفاء الحقول حسب نوع الوحدة
    $('[name="unit_type"]').change(function() {
        const unitType = $(this).val();
        
        $('[name="product_id[]"]').closest('.form-group').toggle(unitType === '3');
        $('[name="category_id[]"]').closest('.form-group').toggle(unitType === '2');
    }).trigger('change');

    // التحكم في حقل الكمية
    $('[name="type"]').change(function() {
        const type = $(this).val();
        const quantityInput = $('[name="quantity"]');
        
        if (type === '1') {
            quantityInput.val('1').prop('readonly', true);
        } else {
            quantityInput.val('').prop('readonly', false);
        }
    }).trigger('change');
});
</script>

<style>
    /* حل مشكلة ظهور حقل الإدخال المزدوج */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ddd;
        padding: 5px;
        min-height: 42px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #7367f0;
        border-color: #7367f0;
        color: #fff;
        padding: 3px 10px;
    }
</style>

<script>
    $(document).ready(function() {
        // تهيئة Select2 مع إعدادات متقدمة
        $('.select2').each(function() {
            $(this).select2({
                language: 'ar',
                dir: 'rtl',
                placeholder: $(this).attr('placeholder') || 'اختر عناصر',
                width: '100%',
                dropdownAutoWidth: true,
                allowClear: true,
                closeOnSelect: false
            });
        });

        // إخفاء حقول المنتجات والتصنيفات في البداية
        $('[name="product_id[]"], [name="category_id[]"]').closest('.form-group').hide();

        // التحكم في إظهار/إخفاء الحقول حسب نوع الوحدة
        $('[name="unit_type"]').change(function() {
            const unitType = $(this).val();

            // إخفاء جميع الحقول أولاً
            $('[name="product_id[]"], [name="category_id[]"]').closest('.form-group').hide();

            // إظهار الحقل المناسب
            if (unitType === '3') {
                $('[name="product_id[]"]').closest('.form-group').show();
            } else if (unitType === '2') {
                $('[name="category_id[]"]').closest('.form-group').show();
            }
        }).trigger('change');

        // التحكم في حقل الكمية حسب نوع العرض
        $('[name="type"]').change(function() {
            const offerType = $(this).val();
            const quantityInput = $('#quantity');

            if (offerType === '1') {
                quantityInput.val('1').prop('readonly', true);
            } else {
                quantityInput.val('').prop('readonly', false);
            }
        }).trigger('change');
    });
</script>
@endsection
