@extends('master')

@section('title')
    المخزون
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة المنتجات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">اضافه منتج</li>
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
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>
                    <div>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i> الغاء
                        </a>
                        <button type="button" id="save-product-btn" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i> حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.alerts.success')
        @include('layouts.alerts.error')

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">تفاصيل المنتج</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form id="products_form" class="form form-vertical" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="form-group">
                                                <label for="product-name">الاسم <span style="color: red">*</span></label>
                                                <input type="text" id="product-name" class="form-control" name="name" value="{{ old('name') }}" required>
                                                @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <input type="hidden" name="type" value="products">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="serial-number">الرقم التسلسلي</label>
                                                <input type="text" id="serial-number" class="form-control" name="serial_number" value="{{ $serial_number ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="description">الوصف</label>
                                                <textarea name="description" class="form-control" id="description" rows="3">{{ old('description') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="product-images">الصور</label>
                                                        <input type="file" name="images" class="form-control" id="product-images" accept="image/*">
                                                    </div>
                                                </div>
                                                @if(isset($role) && $role)
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="template-unit">قالب الوحدة</label>
                                                        <select id="template-unit" name="sub_unit_id" class="form-control">
                                                            @foreach ($TemplateUnit as $key => $Templat)
                                                                <option value="{{ $Templat->id }}" {{ $key == 0 ? 'selected' : '' }}>
                                                                    {{ $Templat->template ?? "" }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="category-input">التصنيف</label>
                                                        <select id="category-input" class="form-control" name="category_id">
                                                            <option value="">-- اختر التصنيف --</option>
                                                        </select>
                                                        @error('category_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="brand">الماركة</label>
                                                        <input type="text" id="brand" class="form-control" name="brand" value="{{ old('brand') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="sales-account">Sales Account</label>
                                                        <input type="text" id="sales-account" class="form-control" name="sales_account" value="{{ old('sales_account') }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="sales-cost-account">Sales Cost Account</label>
                                                        <input type="text" id="sales-cost-account" class="form-control" name="sales_cost_account" value="{{ old('sales_cost_account') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="supplier-id">المورد</label>
                                                        <input type="number" id="supplier-id" class="form-control" name="supplier_id" value="{{ old('supplier_id') }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="barcode">باركود</label>
                                                        <input type="text" id="barcode" class="form-control" name="barcode" value="{{ old('barcode') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-12">
                                            <fieldset class="checkbox">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" name="available_online" id="available_online" onchange="remove_disabled_ckeckbox()">
                                                    <span class="vs-checkbox">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                    <span class="">متاح اون لاين</span>
                                                </div>
                                            </fieldset>
                                        </div>

                                        <div class="form-group col-12">
                                            <fieldset class="checkbox">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" name="featured_product" id="featured_product" disabled>
                                                    <span class="vs-checkbox">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                    <span class="">منتج مميز</span>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">تفاصيل التسعير</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                    @if(isset($role) && $role)
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group d-flex align-items-center">
                                                    <input placeholder="سعر الشراء" type="text" id="purchase-price" class="form-control me-2" name="purchase_price" value="{{ old('purchase_price') }}">
                                                    <select class="form-control" id="purchase-type" name="purchase_unit_id"></select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group d-flex align-items-center">
                                                    <input placeholder="سعر البيع" type="text" id="sale-price" class="form-control me-2" name="sale_price" value="{{ old('sale_price') }}">
                                                    <select class="form-control" id="sale-type" name="sales_unit_id"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="purchase-price-simple">سعر الشراء</label>
                                                    <input type="text" id="purchase-price-simple" class="form-control" name="purchase_price" value="{{ old('purchase_price') }}">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="sale-price-simple">سعر البيع</label>
                                                    <input type="text" id="sale-price-simple" class="form-control" name="sale_price" value="{{ old('sale_price') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="tax1">الضريبه الاولى</label>
                                                    <select class="form-control" id="tax1" name="tax1">
                                                        <option value="">اختر ضريبة</option>
                                                        <option value="1">القيمة المضافة</option>
                                                        <option value="2">صفرية</option>
                                                        <option value="3">قيمة مضافة</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="tax2">الضريبه الثانية</label>
                                                    <select class="form-control" id="tax2" name="tax2">
                                                        <option value="">اختر ضريبة</option>
                                                        <option value="1">القيمة المضافة</option>
                                                        <option value="2">صفرية</option>
                                                        <option value="3">قيمة مضافة</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="min-sale-price">اقل سعر بيع</label>
                                                    <input type="text" id="min-sale-price" class="form-control" name="min_sale_price" value="{{ old('min_sale_price') }}">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="discount">الخصم</label>
                                                    <input type="text" id="discount" class="form-control" name="discount" value="{{ old('discount') }}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label for="discount-type">نوع الخصم</label>
                                                    <select class="form-control" id="discount-type" name="discount_type">
                                                        <option value="1">%</option>
                                                        <option value="2">$</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="profit-margin">هامش الربح نسبه مئوية</label>
                                                    <input type="text" id="profit-margin" class="form-control" name="profit_margin" value="{{ old('profit_margin') }}">
                                                </div>
                                            </div>
                                            @if(isset($price_lists))
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label for="price-list-select">قائمة الاسعار</label>
                                                    <select class="form-control" id="price-list-select" name="price_list_id">
                                                        <option value="">اختر قائمة اسعار</option>
                                                        @foreach ($price_lists as $price_list)
                                                            <option value="{{ $price_list->id }}" data-price="{{ $price_list->default_price }}">{{ $price_list->name ?? "" }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label for="default-price-input">السعر الافتراضي</label>
                                                    <input type="text" id="default-price-input" class="form-control" name="price_list" disabled>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">ادارة المخزون</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="form-body">
                        <div class="row">
                            <div class="form-group col-12">
                                <fieldset class="checkbox">
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input type="checkbox" id="ProductTrackStock" onchange="remove_disabled()">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تتبع المخزون</span>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="tracking-select">نوع التتبع</label>
                                            <select disabled id="tracking-select" class="form-control ProductTrackingInput" name="track_inventory">
                                                <option value="">اختر النوع</option>
                                                <option value="2">تاريخ الانتهاء</option>
                                                <option value="4">الكمية فقط</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="low_stock_alert">نبهني عند وصول الكمية إلى أقل من !</label>
                                            <input disabled type="number" value="{{ old('low_stock_alert', 0) }}" class="form-control ProductTrackingInput" name="low_stock_alert">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6" id="expiry-date-field" style="display: none;">
                                <div class="form-group">
                                    <label for="expiry_date">تاريخ الانتهاء</label>
                                    <input type="date" class="form-control" name="expiry_date">
                                </div>
                            </div>

                            <div class="col-6" id="notify-before-expiry-field" style="display: none;">
                                <div class="form-group">
                                    <label for="notify_before_days">نبهني قبل الانتهاء (بالأيام)</label>
                                    <input type="number" class="form-control" name="notify_before_days">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">خيارات اكثر</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="internal-notes">ملاخظات داخلية</label>
                                    <textarea class="form-control" id="internal-notes" name="Internal_notes" rows="3">{{ old('Internal_notes') }}</textarea>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="tags">وسوم</label>
                                    <input type="text" id="tags" class="form-control" name="tags" value="{{ old('tags') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="status">الحالة</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>نشط</option>
                                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>موقوف</option>
                                        <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<!-- تحميل مكتبات jQuery و SweetAlert2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- تحميل مكتبة Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // تكامل SweetAlert2 مع النموذج
    $('#save-product-btn').on('click', function(e) {
        e.preventDefault();

        // التحقق من صحة البيانات الأساسية
        if (!validateForm()) {
            return false;
        }

        // عرض تأكيد الحفظ
        Swal.fire({
            title: 'تأكيد الحفظ',
            text: 'هل أنت متأكد من حفظ بيانات المنتج؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: '<i class="fa fa-save"></i> نعم، احفظ',
            cancelButtonText: '<i class="fa fa-times"></i> إلغاء',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitFormWithLoading();
            }
        });
    });

    // دالة التحقق من صحة النموذج
    function validateForm() {
        const productName = $('#product-name').val().trim();

        if (productName === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ في البيانات',
                text: 'يرجى إدخال اسم المنتج',
                confirmButtonText: 'موافق',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
            $('#product-name').focus();
            return false;
        }

        return true;
    }

    // دالة إرسال النموذج مع مؤشر التحميل
    function submitFormWithLoading() {
        // عرض مؤشر التحميل
        Swal.fire({
            title: 'جاري الحفظ...',
            text: 'يرجى الانتظار',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // إرسال النموذج عبر Ajax
        const formData = new FormData($('#products_form')[0]);

        $.ajax({
            url: $('#products_form').attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح!',
                    text: 'تم حفظ بيانات المنتج بنجاح',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-list"></i> عرض المنتجات',
                    cancelButtonText: '<i class="fa fa-plus"></i> إضافة منتج جديد',
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#28a745',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-success'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('products.index') }}";
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // إعادة تعيين النموذج لإضافة منتج جديد
                        $('#products_form')[0].reset();
                        $('#category-input').val(null).trigger('change');
                        $('#featured_product').prop('disabled', true);
                        disableForm(true);
                        $('#ProductTrackStock').prop('checked', false);
                    }
                });
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء الحفظ. يرجى المحاولة مرة أخرى.';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في الحفظ',
                    text: errorMessage,
                    confirmButtonText: 'موافق',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            }
        });
    }

    // باقي الوظائف الموجودة
    function remove_disabled() {
        if (document.getElementById("ProductTrackStock").checked) {
            disableForm(false);
        } else {
            disableForm(true);
        }
    }

    function disableForm(flag) {
        var elements = document.getElementsByClassName("ProductTrackingInput");
        for (var i = 0, len = elements.length; i < len; ++i) {
            elements[i].readOnly = flag;
            elements[i].disabled = flag;
        }
    }

    // ربط الدالة بالنافذة العامة
    window.remove_disabled = remove_disabled;
    window.remove_disabled_ckeckbox = function() {
        if(document.getElementById("available_online").checked) {
            document.getElementById("featured_product").disabled = false;
        } else {
            document.getElementById("featured_product").disabled = true;
            document.getElementById("featured_product").checked = false;
        }
    }

    // تهيئة Select2 للتصنيفات
    $('#category-input').select2({
        placeholder: "اختر التصنيف",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: '/stock/products/getcategories',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                };
            },
            processResults: function(data) {
                return {
                    results: data.results,
                };
            },
            cache: true
        }
    });

    @if(isset($role) && $role)
    // جلب الوحدات الفرعية
    function fetchSubUnits(templateUnitId) {
        if (templateUnitId) {
            $.ajax({
                url: '/stock/products/get-sub-units',
                type: 'GET',
                data: { template_unit_id: templateUnitId },
                success: function(response) {
                    if (response.length > 0) {
                        $('#purchase-type, #sale-type').empty();
                        $.each(response, function(index, subUnit) {
                            $('#purchase-type, #sale-type').append('<option value="' + subUnit.id + '">' + subUnit.sub_discrimination + '</option>');
                        });
                        $('#purchase-type, #sale-type').val(response[0].id);
                    } else {
                        $('#purchase-type, #sale-type').empty().append('<option value="">لا توجد وحدات فرعية</option>');
                    }
                }
            });
        } else {
            $('#purchase-type, #sale-type').empty().append('<option value="">لا توجد وحدات فرعية</option>');
        }
    }

    let firstTemplateUnitId = $('#template-unit').val();
    fetchSubUnits(firstTemplateUnitId);

    $('#template-unit').change(function() {
        let templateUnitId = $(this).val();
        fetchSubUnits(templateUnitId);
    });
    @endif

    @if(isset($price_lists))
    // التعامل مع قائمة الأسعار
    $('#price-list-select').addEventListener('change', function() {
        var defaultPriceInput = document.getElementById('default-price-input');
        var selectedOption = this.options[this.selectedIndex];

        if (selectedOption.value) {
            defaultPriceInput.disabled = false;
            defaultPriceInput.value = selectedOption.getAttribute('data-price');
        } else {
            defaultPriceInput.disabled = true;
            defaultPriceInput.value = '';
        }
    });
    @endif

    // التعامل مع تغيير نوع التتبع
    $('#tracking-select').addEventListener('change', function() {
        const expiryDateField = document.getElementById('expiry-date-field');
        const notifyBeforeExpiryField = document.getElementById('notify-before-expiry-field');

        if (this.value == '2') {
            expiryDateField.style.display = 'block';
            notifyBeforeExpiryField.style.display = 'block';
        } else {
            expiryDateField.style.display = 'none';
            notifyBeforeExpiryField.style.display = 'none';
        }
    });

    // تنظيف الرسائل عند التفاعل مع الحقول
    $('input, select, textarea').on('focus', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.text-danger').hide();
    });
});

// دوال إضافية للتوافق مع الكود الموجود
function remove_disabled() {
    if (document.getElementById("ProductTrackStock").checked) {
        disableForm(false);
    } else {
        disableForm(true);
    }
}

function disableForm(flag) {
    var elements = document.getElementsByClassName("ProductTrackingInput");
    for (var i = 0, len = elements.length; i < len; ++i) {
        elements[i].readOnly = flag;
        elements[i].disabled = flag;
    }
}

function remove_disabled_ckeckbox() {
    if(document.getElementById("available_online").checked) {
        document.getElementById("featured_product").disabled = false;
    } else {
        document.getElementById("featured_product").disabled = true;
        document.getElementById("featured_product").checked = false;
    }
}
</script>

<style>
/* تحسينات CSS للمظهر */
.swal2-popup {
    font-family: 'Cairo', sans-serif;
    direction: rtl;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.vs-checkbox-con:hover {
    cursor: pointer;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

/* تحسين مظهر Select2 */
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

/* تحسين مظهر رسائل الخطأ */
.text-danger {
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}

.is-invalid {
    border-color: #dc3545;
}

/* تحسين المباعدات */
.form-group {
    margin-bottom: 1rem;
}

.card {
    margin-bottom: 1.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* تحسين الأزرار */
.btn {
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
}

.btn i {
    margin-left: 0.5rem;
}

/* تحسين Checkboxes */
.vs-checkbox-con {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.vs-checkbox-con span:last-child {
    margin-right: 0.5rem;
}
</style>
@endsection