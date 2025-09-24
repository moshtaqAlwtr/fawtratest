@extends('master')

@section('title')
    إعدادات المخزون
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/sitting.css') }}">
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- رأس الصفحة -->
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="main-title">📦 إعدادات المخزون</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-custom">
                                    <li class="breadcrumb-item">
                                        <a href="">🏠 الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        ⚙️ إعدادات المخزون
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('inventory_settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- بطاقة معلومات الحفظ -->
            <div class="custom-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">💾 إعدادات الحفظ</h5>
                </div>
                <div class="card-body-custom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="required-text">
                            <i class="fas fa-info-circle me-2"></i>
                            قم بتخصيص إعدادات المخزون حسب احتياجات نشاطك التجاري
                        </div>
                        <div>
                            <a href="" class="btn btn-cancel me-2">
                                <i class="fa fa-ban me-2"></i> إلغاء
                            </a>
                            <button type="submit" class="btn btn-save">
                                <i class="fa fa-save me-2"></i> حفظ الإعدادات
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- رسائل النجاح والخطأ -->
            @if (Session::has('success'))
                <div class="alert alert-success-custom" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3" style="font-size: 24px;"></i>
                        <p class="mb-0 font-weight-bold">
                            {{ Session::get('success') }}
                        </p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger-custom" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3" style="font-size: 24px;"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p class="mb-1 font-weight-bold">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- بطاقة الإعدادات الأساسية -->
            <div class="custom-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">⚙️ الإعدادات الأساسية</h5>
                </div>
                <div class="card-body-custom">
                    <div class="row">
                        <!-- الإعدادات الافتراضية -->
                        <div class="col-md-6">
                            <h6 class="text-muted mb-4">
                                <i class="fas fa-cog me-2"></i>
                                الإعدادات الافتراضية
                            </h6>

                            <div class="form-group mb-4">
                                <label class="form-label">
                                    <i class="fas fa-layer-group me-2" style="color: #667eea;"></i>
                                    الحساب الفرعي
                                </label>
                                <select class="form-control custom-select select2" name="sub_account">
                                    <option value="" selected disabled>-- اختر الحساب الفرعي --</option>
                                 @foreach ($storehouses as $storehouse)
                                        <option value="{{ $storehouse->id }}" {{ isset($general_settings) && old('storehouse_id', $general_settings->storehouse_id ?? null) == $storehouse->id ? 'selected' : '' }}>
                                            {{ $storehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">
                                    <i class="fas fa-warehouse me-2" style="color: #667eea;"></i>
                                    المستودع الافتراضي
                                </label>
                                <select class="form-control custom-select select2" name="storehouse_id">
                                    <option value="" selected disabled>-- اختر المستودع --</option>
                                    @foreach ($storehouses as $storehouse)
                                        <option value="{{ $storehouse->id }}" {{ isset($general_settings) && old('storehouse_id', $general_settings->storehouse_id ?? null) == $storehouse->id ? 'selected' : '' }}>
                                            {{ $storehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">
                                    <i class="fas fa-tags me-2" style="color: #667eea;"></i>
                                    قائمة الأسعار الافتراضية
                                </label>
                                <select class="form-control custom-select select2" name="price_list_id">
                                    <option value="" selected disabled>-- اختر قائمة الأسعار --</option>
                                    @foreach ($price_lists as $price_list)
                                        <option value="{{ $price_list->id }}" {{ isset($general_settings) && old('price_list_id', $general_settings->price_list_id ?? null) == $price_list->id ? 'selected' : '' }}>
                                            {{ $price_list->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- معلومات إضافية -->
                        <div class="col-md-6">
                            <div class="info-box">
                                <i class="fas fa-lightbulb mb-3" style="font-size: 2rem; color: #667eea;"></i>
                                <h6 class="mb-3">نصائح مهمة</h6>
                                <ul class="info-list">
                                    <li>اختر المستودع الذي سيتم استخدامه افتراضياً في العمليات</li>
                                    <li>قائمة الأسعار الافتراضية ستطبق على جميع المنتجات الجديدة</li>
                                    <li>يمكن تغيير هذه الإعدادات في أي وقت</li>
                                    <li>تأكد من حفظ التغييرات قبل مغادرة الصفحة</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة صلاحيات المخزون -->
            <div class="custom-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">🔐 صلاحيات وخيارات المخزون</h5>
                </div>
                <div class="card-body-custom">
                    <div class="permissions-container">
                        <h6 class="text-muted mb-4">
                            <i class="fas fa-shield-alt me-2"></i>
                            اختر الخيارات والصلاحيات المناسبة لإدارة المخزون:
                        </h6>
                        <div class="permissions-grid">

                            <!-- إتاحة المخزون السالب -->
                            <div class="permission-item {{ isset($general_settings->enable_negative_stock) && $general_settings->enable_negative_stock == 1 ? 'checked' : '' }}"
                                onclick="toggleCheckbox('enable_negative_stock')">
                                <input type="checkbox" class="custom-checkbox" id="enable_negative_stock"
                                    name="enable_negative_stock" value="1"
                                    {{ isset($general_settings->enable_negative_stock) && $general_settings->enable_negative_stock == 1 ? 'checked' : '' }}
                                    onchange="updateItemStyle(this)">
                                <span class="checkmark"></span>
                                <label class="permission-label" for="enable_negative_stock">
                                    <i class="fas fa-minus-circle me-2" style="color: #667eea;"></i>
                                    إتاحة المخزون السالب
                                </label>
                            </div>

                            <!-- خيارات التسعير المتقدمة -->
                            <div class="permission-item {{ isset($general_settings->advanced_pricing_options) && $general_settings->advanced_pricing_options == 1 ? 'checked' : '' }}"
                                onclick="toggleCheckbox('advanced_pricing_options')">
                                <input type="checkbox" class="custom-checkbox" id="advanced_pricing_options"
                                    name="advanced_pricing_options" value="1"
                                    {{ isset($general_settings->advanced_pricing_options) && $general_settings->advanced_pricing_options == 1 ? 'checked' : '' }}
                                    onchange="updateItemStyle(this)">
                                <span class="checkmark"></span>
                                <label class="permission-label" for="advanced_pricing_options">
                                    <i class="fas fa-dollar-sign me-2" style="color: #667eea;"></i>
                                    خيارات التسعير المتقدمة
                                </label>
                            </div>

                            <!-- تفعيل الطلبات المخزنية -->
                            <div class="permission-item {{ isset($general_settings->enable_stock_requests) && $general_settings->enable_stock_requests == 1 ? 'checked' : '' }}"
                                onclick="toggleCheckbox('enable_stock_requests')">
                                <input type="checkbox" class="custom-checkbox" id="enable_stock_requests"
                                    name="enable_stock_requests" value="1"
                                    {{ isset($general_settings->enable_stock_requests) && $general_settings->enable_stock_requests == 1 ? 'checked' : '' }}
                                    onchange="updateItemStyle(this)">
                                <span class="checkmark"></span>
                                <label class="permission-label" for="enable_stock_requests">
                                    <i class="fas fa-clipboard-list me-2" style="color: #667eea;"></i>
                                    تفعيل الطلبات المخزنية
                                </label>
                            </div>

                            <!-- الأذون المخزنية للمبيعات -->
                            <div class="permission-item {{ isset($general_settings->enable_sales_stock_authorization) && $general_settings->enable_sales_stock_authorization == 1 ? 'checked' : '' }}"
                                onclick="toggleCheckbox('enable_sales_stock_authorization')">
                                <input type="checkbox" class="custom-checkbox" id="enable_sales_stock_authorization"
                                    name="enable_sales_stock_authorization" value="1"
                                    {{ isset($general_settings->enable_sales_stock_authorization) && $general_settings->enable_sales_stock_authorization == 1 ? 'checked' : '' }}
                                    onchange="updateItemStyle(this)">
                                <span class="checkmark"></span>
                                <label class="permission-label" for="enable_sales_stock_authorization">
                                    <i class="fas fa-shopping-cart me-2" style="color: #667eea;"></i>
                                    تفعيل الأذون المخزنية لفواتير المبيعات
                                </label>
                            </div>

                            <!-- الأذون المخزنية للمشتريات -->
                            <div class="permission-item {{ isset($general_settings->enable_purchase_stock_authorization) && $general_settings->enable_purchase_stock_authorization == 1 ? 'checked' : '' }}"
                                onclick="toggleCheckbox('enable_purchase_stock_authorization')">
                                <input type="checkbox" class="custom-checkbox" id="enable_purchase_stock_authorization"
                                    name="enable_purchase_stock_authorization" value="1"
                                    {{ isset($general_settings->enable_purchase_stock_authorization) && $general_settings->enable_purchase_stock_authorization == 1 ? 'checked' : '' }}
                                    onchange="updateItemStyle(this)">
                                <span class="checkmark"></span>
                                <label class="permission-label" for="enable_purchase_stock_authorization">
                                    <i class="fas fa-truck me-2" style="color: #667eea;"></i>
                                    تفعيل الأذون المخزنية لفواتير الشراء
                                </label>
                            </div>

                            <!-- تتبع المنتجات -->
                            <div class="permission-item {{ isset($general_settings->track_products_by_serial_or_batch) && $general_settings->track_products_by_serial_or_batch == 1 ? 'checked' : '' }}"
                                onclick="toggleCheckbox('track_products_by_serial_or_batch')">
                                <input type="checkbox" class="custom-checkbox" id="track_products_by_serial_or_batch"
                                    name="track_products_by_serial_or_batch" value="1"
                                    {{ isset($general_settings->track_products_by_serial_or_batch) && $general_settings->track_products_by_serial_or_batch == 1 ? 'checked' : '' }}
                                    onchange="updateItemStyle(this)">
                                <span class="checkmark"></span>
                                <label class="permission-label" for="track_products_by_serial_or_batch">
                                    <i class="fas fa-barcode me-2" style="color: #667eea;"></i>
                                    تتبع المنتجات بالرقم المسلسل أو رقم الشحنة
                                </label>
                            </div>

                            <!-- عناصر التتبع السالبة -->
                            <div class="permission-item {{ isset($general_settings->allow_negative_tracking_elements) && $general_settings->allow_negative_tracking_elements == 1 ? 'checked' : '' }}"
                                onclick="toggleCheckbox('allow_negative_tracking_elements')">
                                <input type="checkbox" class="custom-checkbox" id="allow_negative_tracking_elements"
                                    name="allow_negative_tracking_elements" value="1"
                                    {{ isset($general_settings->allow_negative_tracking_elements) && $general_settings->allow_negative_tracking_elements == 1 ? 'checked' : '' }}
                                    onchange="updateItemStyle(this)">
                                <span class="checkmark"></span>
                                <label class="permission-label" for="allow_negative_tracking_elements">
                                    <i class="fas fa-search-minus me-2" style="color: #667eea;"></i>
                                    السماح بعناصر التتبع السالبة
                                </label>
                            </div>

                            <!-- نظام الوحدات المتعددة -->
                            <div class="permission-item {{ isset($general_settings->enable_multi_units_system) && $general_settings->enable_multi_units_system == 1 ? 'checked' : '' }}"
                                onclick="toggleCheckbox('enable_multi_units_system')">
                                <input type="checkbox" class="custom-checkbox" id="enable_multi_units_system"
                                    name="enable_multi_units_system" value="1"
                                    {{ isset($general_settings->enable_multi_units_system) && $general_settings->enable_multi_units_system == 1 ? 'checked' : '' }}
                                    onchange="updateItemStyle(this)">
                                <span class="checkmark"></span>
                                <label class="permission-label" for="enable_multi_units_system">
                                    <i class="fas fa-cubes me-2" style="color: #667eea;"></i>
                                    إتاحة نظام الوحدات المتعددة
                                </label>
                            </div>

                            <!-- حساب كمية الجرد حسب التاريخ -->
                            <div class="permission-item {{ isset($general_settings->inventory_quantity_by_date) && $general_settings->inventory_quantity_by_date == 1 ? 'checked' : '' }}"
                                onclick="toggleCheckbox('inventory_quantity_by_date')">
                                <input type="checkbox" class="custom-checkbox" id="inventory_quantity_by_date"
                                    name="inventory_quantity_by_date" value="1"
                                    {{ isset($general_settings->inventory_quantity_by_date) && $general_settings->inventory_quantity_by_date == 1 ? 'checked' : '' }}
                                    onchange="updateItemStyle(this)">
                                <span class="checkmark"></span>
                                <label class="permission-label" for="inventory_quantity_by_date">
                                    <i class="fas fa-calendar-alt me-2" style="color: #667eea;"></i>
                                    حساب كمية الجرد حسب تاريخ الجرد
                                </label>
                            </div>

                            <!-- التجميعات والوحدات المركبة -->
                            <div class="permission-item {{ isset($general_settings->enable_assembly_and_compound_units) && $general_settings->enable_assembly_and_compound_units == 1 ? 'checked' : '' }}"
                                onclick="toggleCheckbox('enable_assembly_and_compound_units')">
                                <input type="checkbox" class="custom-checkbox" id="enable_assembly_and_compound_units"
                                    name="enable_assembly_and_compound_units" value="1"
                                    {{ isset($general_settings->enable_assembly_and_compound_units) && $general_settings->enable_assembly_and_compound_units == 1 ? 'checked' : '' }}
                                    onchange="updateItemStyle(this)">
                                <span class="checkmark"></span>
                                <label class="permission-label" for="enable_assembly_and_compound_units">
                                    <i class="fas fa-puzzle-piece me-2" style="color: #667eea;"></i>
                                    إتاحة نظام التجميعات والوحدات المركبة
                                </label>
                            </div>

                            <!-- إظهار الكمية الإجمالية والمتوفرة -->
                            <div class="permission-item {{ isset($general_settings->show_available_quantity_in_warehouse) && $general_settings->show_available_quantity_in_warehouse == 1 ? 'checked' : '' }}"
                                onclick="toggleCheckbox('show_available_quantity_in_warehouse')">
                                <input type="checkbox" class="custom-checkbox" id="show_available_quantity_in_warehouse"
                                    name="show_available_quantity_in_warehouse" value="1"
                                    {{ isset($general_settings->show_available_quantity_in_warehouse) && $general_settings->show_available_quantity_in_warehouse == 1 ? 'checked' : '' }}
                                    onchange="updateItemStyle(this)">
                                <span class="checkmark"></span>
                                <label class="permission-label" for="show_available_quantity_in_warehouse">
                                    <i class="fas fa-eye me-2" style="color: #667eea;"></i>
                                    إظهار الكمية الإجمالية والمتوفرة في المخزن
                                </label>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // وظيفة لتبديل حالة الـ checkbox
        function toggleCheckbox(id) {
            const checkbox = document.getElementById(id);
            checkbox.checked = !checkbox.checked;
            updateItemStyle(checkbox);
        }

        // وظيفة لتحديث مظهر العنصر
        function updateItemStyle(checkbox) {
            const item = checkbox.closest('.permission-item');
            if (checkbox.checked) {
                item.classList.add('checked');
            } else {
                item.classList.remove('checked');
            }
        }

        // تهيئة المظهر عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.custom-checkbox');
            checkboxes.forEach(checkbox => {
                updateItemStyle(checkbox);
            });
        });
    </script>
@endsection