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
                            <li class="breadcrumb-item active">تعديل منتج</li>
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
                        <button type="button" id="update-product-btn" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i> تحديث
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        @if($product->type == "products")
                        <h4 class="card-title">تفاصيل المنتج</h4>
                        @else
                        <h4 class="card-title">تفاصيل الخدمة</h4>
                        @endif
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form id="products_form" class="form form-vertical" action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="form-group">
                                                <label for="product-name">الاسم <span style="color: red">*</span></label>
                                                <input type="text" id="product-name" class="form-control" name="name" value="{{ old('name', $product->name) }}" required>
                                                @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="serial-number">الرقم التسلسلي</label>
                                                <input type="text" id="serial-number" class="form-control" name="serial_number" value="{{ old('serial_number', $product->serial_number) }}">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="description">الوصف</label>
                                                <textarea name="description" class="form-control" id="description" rows="3">{{ old('description', $product->description) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="product-images">الصور</label>
                                                <input type="file" name="images" class="form-control" id="product-images" accept="image/*">
                                                @if($product->images)
                                                <small class="text-muted">الصورة الحالية موجودة - سيتم استبدالها عند رفع صورة جديدة</small>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="category-select">التصنيف</label>
                                                        <select name="category_id" id="category-select" class="form-control">
                                                            <option value="">-- اختر التصنيف --</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                                    {{ $category->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="brand">الماركة</label>
                                                        <input type="text" id="brand" class="form-control" name="brand" value="{{ old('brand', $product->brand) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="sales-account">Sales Account</label>
                                                        <input type="text" id="sales-account" class="form-control" name="sales_account" value="{{ old('sales_account', $product->sales_account) }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="sales-cost-account">Sales Cost Account</label>
                                                        <input type="text" id="sales-cost-account" class="form-control" name="sales_cost_account" value="{{ old('sales_cost_account', $product->sales_cost_account) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="supplier-id">المورد</label>
                                                        <input type="number" id="supplier-id" class="form-control" name="supplier_id" value="{{ old('supplier_id', $product->supplier_id) }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="barcode">باركود</label>
                                                        <input type="text" id="barcode" class="form-control" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-12">
                                            <fieldset class="checkbox">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" name="available_online" {{ $product->available_online == 1 ? 'checked' : '' }} id="available_online" onchange="remove_disabled_ckeckbox()">
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
                                                    <input type="checkbox" name="featured_product" {{ $product->featured_product == 1 ? 'checked' : '' }} id="featured_product" {{ $product->available_online != 1 ? 'disabled' : '' }}>
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
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="purchase-price">سعر الشراء</label>
                                                    <input type="text" id="purchase-price" class="form-control" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="sale-price">سعر البيع</label>
                                                    <input type="text" id="sale-price" class="form-control" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="tax1">الضريبه الاولى</label>
                                                    <select class="form-control" id="tax1" name="tax1">
                                                        <option value="">اختر ضريبة</option>
                                                        <option value="1" {{ old('tax1', $product->tax1) == 1 ? 'selected' : '' }}>القيمة المضافة</option>
                                                        <option value="2" {{ old('tax1', $product->tax1) == 2 ? 'selected' : '' }}>صفرية</option>
                                                        <option value="3" {{ old('tax1', $product->tax1) == 3 ? 'selected' : '' }}>قيمة مضافة</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="tax2">الضريبه الثانية</label>
                                                    <select class="form-control" id="tax2" name="tax2">
                                                        <option value="">اختر ضريبة</option>
                                                        <option value="1" {{ old('tax2', $product->tax2) == 1 ? 'selected' : '' }}>القيمة المضافة</option>
                                                        <option value="2" {{ old('tax2', $product->tax2) == 2 ? 'selected' : '' }}>صفرية</option>
                                                        <option value="3" {{ old('tax2', $product->tax2) == 3 ? 'selected' : '' }}>قيمة مضافة</option>
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
                                                    <input type="text" id="min-sale-price" class="form-control" name="min_sale_price" value="{{ old('min_sale_price', $product->min_sale_price) }}">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="discount">الخصم</label>
                                                    <input type="text" id="discount" class="form-control" name="discount" value="{{ old('discount', $product->discount) }}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label for="discount-type">نوع الخصم</label>
                                                    <select class="form-control" id="discount-type" name="discount_type">
                                                        <option value="1" {{ old('discount_type', $product->discount_type) == 1 ? 'selected' : '' }}>%</option>
                                                        <option value="2" {{ old('discount_type', $product->discount_type) == 2 ? 'selected' : '' }}>$</option>
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
                                                    <input type="text" id="profit-margin" class="form-control" name="profit_margin" value="{{ old('profit_margin', $product->profit_margin) }}">
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

        @if($product->type == "products")
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
                                        <input type="checkbox" id="ProductTrackStock" {{ $product->track_inventory ? 'checked' : '' }} onchange="remove_disabled()">
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
                                            <select {{ !$product->track_inventory ? 'disabled' : '' }} id="tracking-select" class="form-control ProductTrackingInput" name="track_inventory">
                                                <option value="0" {{ old('track_inventory', $product->track_inventory) == 0 ? 'selected' : '' }}>الرقم التسلسلي</option>
                                                <option value="1" {{ old('track_inventory', $product->track_inventory) == 1 ? 'selected' : '' }}>رقم الشحنة</option>
                                                <option value="2" {{ old('track_inventory', $product->track_inventory) == 2 ? 'selected' : '' }}>تاريخ الانتهاء</option>
                                                <option value="3" {{ old('track_inventory', $product->track_inventory) == 3 ? 'selected' : '' }}>رقم الشحنة وتاريخ الانتهاء</option>
                                                <option value="4" {{ old('track_inventory', $product->track_inventory) == 4 ? 'selected' : '' }}>الكمية فقط</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="low_stock_alert">نبهني عند وصول الكمية إلى أقل من !</label>
                                            <input {{ !$product->track_inventory ? 'disabled' : '' }} type="number" value="{{ old('low_stock_alert', $product->low_stock_alert) }}" class="form-control ProductTrackingInput" name="low_stock_alert">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

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
                                    <textarea class="form-control" id="internal-notes" name="Internal_notes" rows="3">{{ old('Internal_notes', $product->Internal_notes) }}</textarea>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="tags">وسوم</label>
                                    <input type="text" id="tags" class="form-control" name="tags" value="{{ old('tags', $product->tags) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="status">الحالة</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="0" {{ old('status', $product->status) == 0 ? 'selected' : '' }}>نشط</option>
                                        <option value="1" {{ old('status', $product->status) == 1 ? 'selected' : '' }}>موقوف</option>
                                        <option value="2" {{ old('status', $product->status) == 2 ? 'selected' : '' }}>غير نشط</option>
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

<script>
$(document).ready(function() {
    // تكامل SweetAlert2 مع نموذج التعديل
    $('#update-product-btn').on('click', function(e) {
        e.preventDefault();

        // التحقق من صحة البيانات الأساسية
        if (!validateForm()) {
            return false;
        }

        // عرض تأكيد التحديث
        Swal.fire({
            title: 'تأكيد التحديث',
            text: 'هل أنت متأكد من تحديث بيانات المنتج؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: '<i class="fa fa-save"></i> نعم، حدث',
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
            title: 'جاري التحديث...',
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

        // إضافة تشخيص للبيانات المرسلة
        console.log('البيانات المرسلة:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        $.ajax({
            url: $('#products_form').attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('نجحت العملية:', response);

                // التحقق من نوع الاستجابة
                if (response.success || response.message || typeof response === 'string') {
                    let message = response.message || response || 'تم تحديث بيانات المنتج بنجاح';

                    Swal.fire({
                        icon: 'success',
                        title: 'تم التحديث بنجاح!',
                        text: message,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fa fa-list"></i> عرض المنتجات',
                        cancelButtonText: '<i class="fa fa-edit"></i> متابعة التعديل',
                        confirmButtonColor: '#007bff',
                        cancelButtonColor: '#6c757d',
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                window.location.href = "{{ route('products.index') }}";
                            }
                        }
                    });
                } else {
                    // إذا كانت الاستجابة إعادة توجيه HTML
                    window.location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.log('خطأ في Ajax:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });

                let errorMessage = 'حدث خطأ أثناء التحديث';
                let errorDetails = '';

                // تحليل نوع الخطأ
                if (xhr.status === 422) {
                    // خطأ في التحقق من البيانات
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMessage = 'يوجد أخطاء في البيانات المدخلة:';
                        errorDetails = Object.values(errors).flat().join('\n• ');
                        errorDetails = '• ' + errorDetails;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                } else if (xhr.status === 500) {
                    // خطأ في الخادم
                    errorMessage = 'خطأ في الخادم الداخلي';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorDetails = xhr.responseJSON.message;
                    } else {
                        errorDetails = 'تفاصيل الخطأ: ' + xhr.statusText;
                    }
                } else if (xhr.status === 404) {
                    errorMessage = 'المنتج غير موجود أو تم حذفه';
                } else if (xhr.status === 419) {
                    errorMessage = 'انتهت صلاحية الجلسة';
                    errorDetails = 'يرجى إعادة تحميل الصفحة والمحاولة مرة أخرى';
                } else if (xhr.status === 0) {
                    errorMessage = 'مشكلة في الاتصال بالشبكة';
                    errorDetails = 'تأكد من الاتصال بالإنترنت';
                } else {
                    errorMessage = 'خطأ غير محدد (كود: ' + xhr.status + ')';
                    errorDetails = xhr.statusText || error;
                }

                // عرض تفاصيل الخطأ
                Swal.fire({
                    icon: 'error',
                    title: errorMessage,
                    html: errorDetails ? '<div style="text-align: right; white-space: pre-line;">' + errorDetails + '</div>' : 'يرجى المحاولة مرة أخرى',
                    confirmButtonText: 'موافق',
                    footer: xhr.status ? '<small>كود الخطأ: ' + xhr.status + '</small>' : null,
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            }
        });
    }

    // تنظيف الرسائل عند التفاعل مع الحقول
    $('input, select, textarea').on('focus', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.text-danger').hide();
    });
});

// دوال للتوافق مع الكود الموجود
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

// تطبيق الحالة الأولية للحقول
document.addEventListener('DOMContentLoaded', function() {
    // تطبيق حالة تتبع المخزون الأولية
    remove_disabled();

    // تطبيق حالة المنتج المميز الأولية
    remove_disabled_ckeckbox();
});
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

/* تحسين مظهر الحقول المعطلة */
.form-control:disabled,
.form-control[readonly] {
    background-color: #f8f9fa;
    opacity: 0.6;
}
</style>
@endsection