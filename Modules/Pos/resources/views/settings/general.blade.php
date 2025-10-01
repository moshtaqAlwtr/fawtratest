@extends('master')

@section('title')
الإعدادات-عام
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">الإعدادات-عام</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">الرئيسية</li>
                        <li class="breadcrumb-item active">الإعدادات</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pos.settings.store') }}" method="POST">
        @csrf
        
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>

                    <div>
                        <a href="{{ route('pos.settings.general') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i> الغاء
                        </a>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i> حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <!-- Row 1: Default Customer and Invoice Template -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="default_customer_id" class="form-label">العميل الافتراضي</label>
                        <select id="default_customer_id" name="default_customer_id" class="form-control select2">
                            <option value="">اختر عميل</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                    {{ old('default_customer_id', $settings->default_customer_id ?? '') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->trade_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="invoice_template" class="form-label">قالب الفاتورة الافتراضي *</label>
                        <select id="invoice_template" name="invoice_template" class="form-control" required>
                            <option value="thermal" {{ old('invoice_template', $settings->invoice_template ?? '') == 'thermal' ? 'selected' : '' }}>
                                فاتورة حرارية
                            </option>
                            <option value="electronic" {{ old('invoice_template', $settings->invoice_template ?? '') == 'electronic' ? 'selected' : '' }}>
                                فاتورة إلكترونية
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Row 2: Payment Methods -->
                <!--<div class="row mb-3">-->
                <!--    <div class="col-md-6">-->
                <!--        <label class="form-label">طرق الدفع المفعلة *</label>-->
                <!--        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto; background-color: #fafafa;">-->
                <!--            @foreach($paymentMethods as $index => $method)-->
                <!--                <div class="vs-checkbox-con vs-checkbox-primary mb-1 {{ $index == 0 ? 'first-method' : '' }}" -->
                <!--                     style="border: 1px solid #e9ecef; border-radius: 5px; padding: 8px; cursor: pointer; transition: all 0.2s;"-->
                <!--                     data-method-id="{{ $method->id }}">-->
                <!--                    <input name="active_payment_method_ids[]" -->
                <!--                           type="checkbox" -->
                <!--                           value="{{ $method->id }}"-->
                <!--                           id="payment_method_{{ $method->id }}"-->
                <!--                           {{ in_array($method->id, old('active_payment_method_ids', $settings->active_payment_method_ids ?? [])) ? 'checked' : '' }}-->
                <!--                           {{ $index == 0 ? 'data-first="true"' : '' }}-->
                <!--                           class="payment-method-checkbox permission-checkbox-sales permission-main-checkbox">-->
                <!--                    <span class="vs-checkbox">-->
                <!--                        <span class="vs-checkbox--check">-->
                <!--                            <i class="vs-icon feather icon-check"></i>-->
                <!--                        </span>-->
                <!--                    </span>-->
                <!--                    <span class="payment-method-name">-->
                <!--                        {{ $method->name }}-->
                <!--                        @if($index == 0)-->
                <!--                            <span class="badge badge-warning badge-sm">مطلوب</span>-->
                <!--                        @endif-->
                <!--                    </span>-->
                <!--                </div>-->
                <!--            @endforeach-->
                <!--        </div>-->
                <!--        <small class="text-muted">الطريقة الأولى مطلوبة ولا يمكن إلغاؤها</small>-->
                <!--    </div>-->
                    
                <!--    <div class="col-md-6">-->
                <!--        <label for="default_payment_method_id" class="form-label">طريقة الدفع الافتراضية *</label>-->
                <!--        <select id="default_payment_method_id" name="default_payment_method_id" class="form-control select2" required>-->
                <!--            <option value="">اختر طريقة الدفع الافتراضية</option>-->
                <!--            {{-- سيتم ملء هذه الخيارات بواسطة JavaScript --}}-->
                <!--        </select>-->
                <!--        <small class="text-muted">ستظهر الطرق المفعلة فقط</small>-->
                        
                        <!-- عرض الطرق المفعلة حالياً -->
                <!--        <div class="mt-3">-->
                <!--            <label class="form-label text-muted">الطرق المفعلة حالياً:</label>-->
                <!--            <div id="active_methods_list" class="alert alert-light p-2">-->
                                <!-- سيتم ملؤها بواسطة JavaScript -->
                <!--            </div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->

                <!-- Row 3: Allowed Categories -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="allowed_categories_type" class="form-label">التصنيفات المسموح بها *</label>
                        <select id="allowed_categories_type" name="allowed_categories_type" class="form-control" required>
                            <option value="all" {{ old('allowed_categories_type', $settings->allowed_categories_type ?? 'all') == 'all' ? 'selected' : '' }}>
                                الكل
                            </option>
                            <option value="except" {{ old('allowed_categories_type', $settings->allowed_categories_type ?? 'all') == 'except' ? 'selected' : '' }}>
                                ما عدا
                            </option>
                            <option value="only" {{ old('allowed_categories_type', $settings->allowed_categories_type ?? 'all') == 'only' ? 'selected' : '' }}>
                                فقط
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6" id="categories_selection_wrapper">
                        <label for="allowed_categories_ids" class="form-label">اختر التصنيفات</label>
                        <select id="allowed_categories_ids" name="allowed_categories_ids[]" 
                                class="form-control select2" multiple>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ in_array($category->id, old('allowed_categories_ids', $settings->allowed_categories_ids ?? [])) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Checkbox Options -->
                <div class="mb-4">
                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                        <input name="enable_departments" type="checkbox" 
                               {{ old('enable_departments', $settings->enable_departments ?? false) ? 'checked' : '' }}
                               class="permission-checkbox-sales permission-main-checkbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">تفعيل أزرار الأقسام</span>
                    </div>

                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                        <input name="apply_custom_fields_validation" type="checkbox" 
                               {{ old('apply_custom_fields_validation', $settings->apply_custom_fields_validation ?? false) ? 'checked' : '' }}
                               class="permission-checkbox-sales permission-main-checkbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">تطبيق إعدادات التحقق من الحقول المخصصة</span>
                    </div>

                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                        <input name="show_product_images" type="checkbox" 
                               {{ old('show_product_images', $settings->show_product_images ?? false) ? 'checked' : '' }}
                               class="permission-checkbox-sales permission-main-checkbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">عرض صور المنتجات</span>
                    </div>

                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                        <input name="show_print_window_after_confirm" type="checkbox" 
                               {{ old('show_print_window_after_confirm', $settings->show_print_window_after_confirm ?? false) ? 'checked' : '' }}
                               class="permission-checkbox-sales permission-main-checkbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">عرض نافذة الطباعة بعد تأكيد الفاتورة</span>
                    </div>

                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                        <input name="accounting_system_per_invoice" type="checkbox" 
                               {{ old('accounting_system_per_invoice', $settings->accounting_system_per_invoice ?? false) ? 'checked' : '' }}
                               class="permission-checkbox-sales permission-main-checkbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">نظام المحاسبة لكل فاتورة</span>
                    </div>

                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                        <input name="enable_auto_settlement" type="checkbox" 
                               {{ old('enable_auto_settlement', $settings->enable_auto_settlement ?? false) ? 'checked' : '' }}
                               class="permission-checkbox-sales permission-main-checkbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">تفعيل التسوية التلقائية</span>
                    </div>

                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                        <input name="enable_sales_settlement" type="checkbox" 
                               {{ old('enable_sales_settlement', $settings->enable_sales_settlement ?? false) ? 'checked' : '' }}
                               class="permission-checkbox-sales permission-main-checkbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">تفعيل تسوية المبيعات</span>
                    </div>
                </div>

                <!-- Accounts Section -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="profit_account_id" class="form-label">حساب الارباح</label>
                        <select id="profit_account_id" name="profit_account_id" class="form-control select2">
                            <option value="">اختر الحساب</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" 
                                    {{ old('profit_account_id', $settings->profit_account_id ?? '') == $account->id ? 'selected' : '' }}>
                                    {{ $account->code }} - {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="loss_account_id" class="form-label">حساب الخسائر</label>
                        <select id="loss_account_id" name="loss_account_id" class="form-control select2">
                            <option value="">اختر الحساب</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" 
                                    {{ old('loss_account_id', $settings->loss_account_id ?? '') == $account->id ? 'selected' : '' }}>
                                    {{ $account->code }} - {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'اختر من القائمة',
        allowClear: true,
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            },
            searching: function() {
                return "جاري البحث...";
            }
        }
    });

    // Store initial selected values
    const initialSettings = {
        defaultPaymentMethod: '{{ old("default_payment_method_id", $settings->default_payment_method_id ?? "") }}',
        activePaymentMethods: @json(old('active_payment_method_ids', $settings->active_payment_method_ids ?? []))
    };

    // تهيئة الحالة الأولية
    initializePaymentMethods();

    // معالج النقر على حاوي طريقة الدفع
    $('.vs-checkbox-con[data-method-id]').on('click', function(e) {
        e.preventDefault();
        const checkbox = $(this).find('.payment-method-checkbox');
        const isFirst = checkbox.data('first') === true;
        
        // تبديل حالة الـ checkbox
        const newState = !checkbox.prop('checked');
        
        // منع إلغاء تفعيل الطريقة الأولى
        if (isFirst && !newState) {
            alert('لا يمكن إلغاء تفعيل طريقة الدفع الأولى - هي مطلوبة دائماً');
            return;
        }
        
        // تطبيق الحالة الجديدة
        checkbox.prop('checked', newState);
        
        // التحقق من وجود طريقة واحدة مفعلة على الأقل
        const checkedCount = $('.payment-method-checkbox:checked').length;
        if (checkedCount === 0) {
            checkbox.prop('checked', true);
            alert('يجب أن تكون هناك طريقة دفع واحدة مفعلة على الأقل');
            return;
        }
        
        // تحديث العرض
        updateAllDisplays();
    });

    // Handle categories type change
    $('#allowed_categories_type').change(function() {
        const type = $(this).val();
        const wrapper = $('#categories_selection_wrapper');
        
        if (type === 'all') {
            wrapper.hide();
            $('#allowed_categories_ids').val(null).trigger('change');
        } else {
            wrapper.show();
        }
    });

    function initializePaymentMethods() {
        // التأكد من أن الطريقة الأولى مفعلة دائماً
        $('.payment-method-checkbox[data-first="true"]').prop('checked', true);
        
        // تفعيل الطرق المحفوظة مسبقاً
        if (initialSettings.activePaymentMethods && initialSettings.activePaymentMethods.length > 0) {
            initialSettings.activePaymentMethods.forEach(function(methodId) {
                $('#payment_method_' + methodId).prop('checked', true);
            });
        }
        
        // التحقق من وجود طريقة واحدة مفعلة على الأقل
        const checkedCount = $('.payment-method-checkbox:checked').length;
        if (checkedCount === 0) {
            $('.payment-method-checkbox[data-first="true"]').prop('checked', true);
        }
        
        updateAllDisplays();
    }
    
    function updateAllDisplays() {
        updateDefaultPaymentMethods();
        updateActiveMethodsDisplay();
        updateVisualState();
    }
    
    function updateDefaultPaymentMethods() {
        const defaultSelect = $('#default_payment_method_id');
        const currentDefault = defaultSelect.val() || initialSettings.defaultPaymentMethod;
        const activeCheckboxes = $('.payment-method-checkbox:checked');
        
        // حفظ الخيارات للاستخدام
        const optionsData = [];
        activeCheckboxes.each(function() {
            const methodId = $(this).val();
            const methodName = $(this).closest('.vs-checkbox-con').find('.payment-method-name').clone();
            methodName.find('.badge').remove();
            const cleanName = methodName.text().trim();
            optionsData.push({id: methodId, name: cleanName});
        });
        
        // مسح وإعادة بناء الخيارات
        defaultSelect.empty();
        defaultSelect.append('<option value="">اختر طريقة الدفع الافتراضية</option>');
        
        optionsData.forEach(function(option) {
            const optionElement = new Option(option.name, option.id);
            defaultSelect.append(optionElement);
        });
        
        // استعادة الاختيار أو اختيار الأول
        if (currentDefault && optionsData.find(opt => opt.id === currentDefault)) {
            defaultSelect.val(currentDefault);
        } else if (optionsData.length > 0) {
            defaultSelect.val(optionsData[0].id);
        }
        
        // تحديث Select2
        defaultSelect.trigger('change');
    }
    
    function updateActiveMethodsDisplay() {
        const activeCheckboxes = $('.payment-method-checkbox:checked');
        const activeList = $('#active_methods_list');
        
        if (activeCheckboxes.length > 0) {
            let html = '';
            activeCheckboxes.each(function() {
                const methodContainer = $(this).closest('.vs-checkbox-con[data-method-id]');
                const methodNameElement = methodContainer.find('.payment-method-name');
                
                // إزالة أي badges وأخذ النص النظيف
                const methodName = methodNameElement.clone().find('.badge').remove().end().text().trim();
                const isFirst = $(this).data('first') === true;
                
                const badgeClass = isFirst ? 'badge-warning' : 'badge-success';
                const extraText = isFirst ? ' (مطلوب)' : '';
                
                html += `<span class="badge ${badgeClass} mr-1 mb-1">${methodName}${extraText}</span>`;
            });
            activeList.html(html);
        } else {
            activeList.html('<span class="text-muted">لا توجد طرق دفع مفعلة</span>');
        }
    }
    
    function updateVisualState() {
        $('.vs-checkbox-con[data-method-id]').each(function() {
            const checkbox = $(this).find('.payment-method-checkbox');
            const isFirst = checkbox.data('first') === true;
            
            // تحديث الألوان والحدود بناءً على الحالة
            if (checkbox.is(':checked')) {
                if (isFirst) {
                    $(this).css({
                        'border-color': '#ffc107',
                        'background-color': '#fff3cd'
                    });
                } else {
                    $(this).css({
                        'border-color': '#28a745',
                        'background-color': '#d4edda'
                    });
                }
            } else {
                $(this).css({
                    'border-color': '#e9ecef',
                    'background-color': '#fafafa'
                });
            }
        });
    }

    // Trigger change on page load to set initial state
    $('#allowed_categories_type').trigger('change');
    
    // Form validation before submit
    $('form').submit(function(e) {
        const activePayments = $('.payment-method-checkbox:checked').length;
        const defaultPayment = $('#default_payment_method_id').val();
        
        if (activePayments === 0) {
            e.preventDefault();
            alert('يجب اختيار طريقة دفع واحدة على الأقل');
            return false;
        }
        
        if (!defaultPayment) {
            e.preventDefault();
            alert('يجب اختيار طريقة الدفع الافتراضية');
            return false;
        }
        
        // التحقق من أن طريقة الدفع الافتراضية ضمن المفعلة
        const activeIds = $('.payment-method-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (!activeIds.includes(defaultPayment)) {
            e.preventDefault();
            alert('طريقة الدفع الافتراضية يجب أن تكون من ضمن الطرق المفعلة');
            return false;
        }
    });
});
</script>

<style>
.vs-checkbox-con[data-method-id]:hover {
    border-color: #007bff !important;
    background-color: #f8f9fa !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.first-method {
    border-color: #ffc107 !important;
    background-color: #fff3cd !important;
}

.badge-sm {
    font-size: 0.75em;
    padding: 0.25em 0.5em;
}
</style>

@endsection