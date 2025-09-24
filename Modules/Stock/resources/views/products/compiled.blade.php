@extends('master')

@section('title')
    المخزون
@stop


    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Font Awesome (لأيقونات مثل سلة المهملات) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Bootstrap CSS (إذا كنت تستخدم Bootstrap) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .remove-product {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            width: 38px;
            padding: 0;
        }

        .remove-product i {
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 1rem;
        }
    </style>
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة المنتجات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">اضافه منتج تجميعي</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <form id="products_form" class="form form-vertical" action="{{ route('products.compiled_store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>
                    <div>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i>الغاء
                        </a>
                        <button type="submit" form="products_form" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i>حفظ
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

                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="form-group">
                                                <label for="first-name-vertical">الاسم <span style="color: red">*</span></label>
                                                <input type="text" id="first-name-vertical" class="form-control" name="name" value="{{ old('name') }}">
                                                @error('name')
                                                    <span class="text-danger" id="basic-default-name-error" class="error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <input type="hidden" name="type" value="compiled">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="email-id-vertical">الرقم التسلسلي</label>
                                                <input type="text" id="email-id-vertical" class="form-control" name="serial_number" value="{{ $serial_number }}">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="email-id-vertical">الوصف</label>
                                                <textarea name="description" class="form-control" id="basicTextarea" rows="3">{{ old('description') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="password-vertical">الصور</label>
                                                        <input type="file" name="images" class="form-control"name="contact">
                                                    </div>
                                                </div>
                                                @if ($role)
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="template-unit">قالب الوحدة</label>
                                                            <select id="template-unit" name="sub_unit_id" class="form-control">
                                                                @foreach ($TemplateUnit as $key => $Templat)
                                                                    <option value="{{ $Templat->id }}" {{ $key == 0 ? 'selected' : '' }}>
                                                                        {{ $Templat->template ?? '' }}
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
                                                        <label for="first-name-vertical">التصنيف</label>
                                                        <select id="category-input" class="form-control" name="category_id">
                                                            <option value="">-- اختر التصنيف --</option>
                                                        </select>
                                                        @error('category_id')
                                                            <span class="text-danger" id="basic-default-name-error" class="error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="email-id-vertical">الماركة</label>
                                                        <input type="text" id="email-id-vertical" class="form-control" name="brand" value="{{ old('brand') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="first-name-vertical">Sales Account</label>
                                                        <input type="text" id="first-name-vertical" class="form-control" name="sales_account" value="{{ old('sales_account') }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="email-id-vertical">Sales Cost Account</label>
                                                        <input type="text" id="email-id-vertical" class="form-control" name="sales_account" value="{{ old('sales_cost_account') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="first-name-vertical">المورد</label>
                                                        <input type="number" id="first-name-vertical" class="form-control"name="supplier_id" value="{{ old('supplier_id') }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="email-id-vertical">باركود</label>
                                                        <input type="text" id="email-id-vertical" class="form-control" name="barcode" value="{{ old('barcode') }}">
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
                                                    <input type="checkbox" name="featured_product" id="featured_product">
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
                                    @if ($role)
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
                                                        <label for="first-name-vertical">سعر الشراء</label>
                                                        <input type="text" id="first-name-vertical" class="form-control" name="purchase_price" value="{{ old('purchase_price') }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="email-id-vertical">سعر البيع</label>
                                                        <input type="text" id="email-id-vertical" class="form-control" name="sale_price" value="{{ old('sale_price') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="first-name-vertical">الضريبه الاولى</label>
                                                    <select class="form-control" id="basicSelect" name="tax1">
                                                        <option value="">اختر ضريبة</option>
                                                        <option value="1">القيمة المضافة</option>
                                                        <option value="2">صفرية</option>
                                                        <option value="3">قيمة مضافة</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="email-id-vertical">الضريبه الثانية</label>
                                                    <select class="form-control" id="basicSelect" name="tax2">
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
                                                    <label for="first-name-vertical">اقل سعر بيع</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" name="min_sale_price" value="{{ old('min_sale_price') }}">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>الخصم</label>
                                                    <input type="text" id="email-id-vertical" class="form-control" name="discount" value="{{ old('discount') }}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label for="email-id-vertical">نوع الخصم</label>
                                                    <select class="form-control" id="basicSelect" name="discount_type">
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
                                                    <label for="first-name-vertical">هامش الربح نسبه مئوية</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" name="profit_margin" value="{{ old('profit_margin') }}">
                                                </div>
                                            </div>
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
                <h4 class="card-title">منتجات التجميعة</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="form-body">
                        <div class="row">
                            <!-- المخزن ونوع التجميعة (ثابتان) -->
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="store">المخزن</label>
                                    <select class="form-control" name="storehouse_id">
                                        @foreach ($storehouses as $storehouse)
                                        <option value="{{$storehouse->id}}">{{$storehouse->name ?? ""}}</option>
                                        @endforeach

                                        <!-- أضف خيارات المخزن هنا -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="assembly-type">نوع التجميعة</label>
                                    <select class="form-control" name="compile_type">
                                        <option value="Instant">فوري</option>
                                        <option value="Pre-made">معد مسبقا</option>
                                        <!-- أضف خيارات نوع التجميعة هنا -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- جدول المنتجات -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="products-table" style="display: none;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50%;">المنتج</th>
                                        <th style="width: 30%;">الكمية</th>
                                        <th style="width: 20%;">إجراء</th>
                                    </tr>
                                </thead>
                                <tbody id="product-fields">
                                    <!-- الصفوف الديناميكية ستضاف هنا -->
                                </tbody>
                            </table>
                        </div>

                        <!-- أزرار التحكم -->
                        <button type="button" id="add-product" class="btn btn-primary mt-3">إضافة منتج</button>
                        {{-- <button type="submit" class="btn btn-success mt-3">حفظ</button> --}}
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
                                            <label for="first-name-vertical">نوع التتبع</label>
                                            <select disabled class="form-control ProductTrackingInput" name="track_inventory">
                                                <option value="0">الرقم التسلسلي</option>
                                                <option value="1">رقم الشحنة</option>
                                                <option value="2">تاريخ الانتهاء</option>
                                                <option value="3">رقم الشحنة وتاريخ الانتهاء</option>
                                                <option value="4" selected="selected">الكمية فقط</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="email-id-vertical"> نبهني عند وصول الكمية إلى أقل من !</label>
                                            <input disabled type="number" value="{{ old('low_stock_alert', 0) }}" class="form-control ProductTrackingInput" name="low_stock_alert">
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
                <h4 class="card-title">خيارات اكثر</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="email-id-vertical">ملاخظات داخلية</label>
                                    <textarea class="form-control" id="basicTextarea" name="Internal_notes" rows="3">{{ old('Internal_notes') }}</textarea>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>وسوم</label>
                                    <input type="text" id="email-id-vertical" class="form-control" name="tags" value="{{ old('tags') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="first-name-vertical">الحالة</label>
                                    <select class="form-control" id="basicSelect" name="status">
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>نشط</option>
                                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>موقوف</option>
                                        <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>غير نشط</option>
                                    </select>
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

@section('scripts')
    <script>
        function remove_disabled() {
            if (document.getElementById("ProductTrackStock").checked) {
                disableForm(false);
            }
            if (!document.getElementById("ProductTrackStock").checked) {
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
    </script>
    <script>
        function remove_disabled_ckeckbox() {
            if (document.getElementById("available_online").checked)
                document.getElementById("featured_product").disabled = false;
            else
                document.getElementById("featured_product").disabled = true;
        }
    </script>
    <script>
        $(document).ready(function() {
            function fetchSubUnits(templateUnitId) {
                if (templateUnitId) {
                    $.ajax({
                        url: '/stock/products/get-sub-units',
                        type: 'GET',
                        data: {
                            template_unit_id: templateUnitId
                        },
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
        });
    </script>
   <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // تهيئة Select2 لجميع القوائم المنسدلة
            $('.product-select').select2({
                placeholder: "ابحث عن المنتج...",
                allowClear: true,
                width: '100%'
            });

            let productRowCount = 0;

// زر إضافة منتج جديد
$('#add-product').click(function() {
    let newRow = `
        <tr class="product-row">
            <td>
                <select class="form-control product-select" name="products[${productRowCount}][product_id]">
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" class="form-control quantity-input" name="products[${productRowCount}][quantity]" min="1" value="1">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger remove-product" onclick="removeProductRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    // إضافة الصف داخل الجدول
    $('#product-fields').append(newRow);

    // عرض الجدول إذا كان مخفيًا
    $('#products-table').show();

    // تهيئة Select2 للعنصر الجديد (إذا كنت تستخدم مكتبة Select2)
    $('.product-select').last().select2({
        placeholder: "ابحث عن المنتج...",
        allowClear: true,
        width: '100%'
    });

    productRowCount++;
});

// وظيفة لحذف صف المنتج
function removeProductRow(button) {
    $(button).closest('tr').remove();

    // إخفاء الجدول إذا كان فارغًا
    if ($('#product-fields').children().length === 0) {
        $('#products-table').hide();
    }
}


// وظيفة لحذف صف المنتج
function removeProductRow(button) {
    $(button).closest('.product-row').remove();
}


            // حذف سطر المنتج
            window.removeProductRow = function(button) {
                $(button).closest('.product-row').remove();
            };
        });
    </script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script>
    $(document).ready(function() {
        // تهيئة Select2 مع البحث عبر Ajax
        $('#category-input').select2({
            placeholder: "اختر التصنيف",
            allowClear: true, // يتيح مسح الاختيار
            minimumInputLength: 1, // يسمح بالبحث عند الكتابة
            ajax: {
                url: '/stock/products/getcategories', // المسار الذي يعيد التصنيفات من الخادم
                dataType: 'json',
                delay: 250, // تأخير البحث قليلاً لتحسين الأداء
                data: function(params) {
                    return {
                        search: params.term, // إرسال نص البحث
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results, // النتائج المرسلة من الخادم
                    };
                },
                cache: true // تخزين النتائج في الكاش لتحسين الأداء
            }
        });
    });



    </script>
  @endsection
