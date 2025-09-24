@extends('master')

@section('title')
    اضافة عميل
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> اضافة عميل</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">اضافة عميل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <form id="clientForm" action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- حقلين مخفيين لتخزين الإحداثيات -->
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                        </div>

                        <div>
                            <a href="{{ route('clients.index') }}" class="btn btn-outline-danger">
                                <i class="fa fa-ban"></i> الغاء
                            </a>
                            <button type="submit" class="btn btn-outline-primary" id="submitBtn">
                                <i class="fa fa-save"></i> حفظ
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">بيانات العميل</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-body">
                                    <div class="row">
                                        <!-- الاسم التجاري -->
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label for="trade_name">الاسم التجاري <span class="text-danger">*</span></label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="trade_name" id="trade_name" class="form-control" value="{{ old('trade_name') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-briefcase"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الاسم الأول والأخير -->
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="first_name">الاسم الأول</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-user"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="last_name">الاسم الأخير</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-user"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الهاتف والجوال -->
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="phone">الهاتف</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-phone"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="mobile">جوال</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="mobile" id="mobile" class="form-control" value="{{ old('mobile') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-smartphone"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- عنوان الشارع -->
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="street1">عنوان الشارع 1</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="street1" id="street1" class="form-control" value="{{ old('street1') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map-pin"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="street2">عنوان الشارع 2</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="street2" id="street2" class="form-control" value="{{ old('street2') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map-pin"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- المدينة والمنطقة والرمز البريدي -->
                                        <div class="col-md-4 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="city">المدينة</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="region">المنطقة</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="region" id="region" class="form-control" value="{{ old('region') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="postal_code">الرمز البريدي</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-mail"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- البلد -->
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label for="country">البلد</label>
                                                <select name="country" id="country" class="form-control">
                                                    <option value="SA" {{ old('country') == 'SA' ? 'selected' : '' }}>المملكة العربية السعودية (SA)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- الرقم الضريبي والسجل التجاري -->
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="tax_number">الرقم الضريبي (اختياري)</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="tax_number" id="tax_number" class="form-control" value="{{ old('tax_number') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file-text"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="commercial_registration">سجل تجاري (اختياري)</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="commercial_registration" id="commercial_registration" class="form-control" value="{{ old('commercial_registration') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الحد الائتماني والمدة الائتمانية -->
                                        @foreach ($GeneralClientSettings as $GeneralClientSetting)
                                            @if ($GeneralClientSetting->is_active)
                                                @if ($GeneralClientSetting->key == 'credit_limit')
                                                    <div class="col-md-6 col-12 mb-3">
                                                        <div class="form-group">
                                                            <label for="credit_limit">الحد الائتماني</label>
                                                            <div class="position-relative has-icon-left">
                                                                <input type="number" name="credit_limit" id="credit_limit" class="form-control" value="{{ old('credit_limit', 0) }}">
                                                                <div class="form-control-position">
                                                                    <span>SAR</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        @endforeach

                                        @foreach ($GeneralClientSettings as $GeneralClientSetting)
                                            @if ($GeneralClientSetting->is_active)
                                                @if ($GeneralClientSetting->key == 'credit_duration')
                                                    <div class="col-md-6 col-12 mb-3">
                                                        <div class="form-group">
                                                            <label for="credit_period">المدة الائتمانية</label>
                                                            <div class="position-relative has-icon-left">
                                                                <input type="number" name="credit_period" id="credit_period" class="form-control" value="{{ old('credit_period', 0) }}">
                                                                <div class="form-control-position">
                                                                    <span>أيام</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        @endforeach

                                        <!-- المجموعة ونوع الزيارة -->
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="region_id">المجموعة <span class="text-danger">*</span></label>
                                                <div class="position-relative has-icon-left">
                                                    <select class="form-control select2" id="region_id" name="region_id">
                                                        <option value="">اختر المجموعة</option>
                                                        @foreach ($Regions_groub as $Region_groub)
                                                            <option value="{{ $Region_groub->id }}">{{ $Region_groub->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="visit_type">نوع الزيارة</label>
                                                <div class="position-relative has-icon-left">
                                                    <select class="form-control" id="visit_type" name="visit_type">
                                                        <option value="am">صباحية</option>
                                                        <option value="pm">مسائية</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- زر إظهار الخريطة -->
                                        @foreach ($GeneralClientSettings as $GeneralClientSetting)
                                            @if ($GeneralClientSetting->is_active && $GeneralClientSetting->key == 'location')
                                                <div class="col-12 mb-3">
                                                    <button type="button" class="btn btn-outline-primary mb-2" id="showMapBtn">
                                                        <i class="feather icon-map"></i> إظهار الخريطة
                                                    </button>

                                                    <div id="map-container" style="display: none;">
                                                        <div id="map" style="height: 400px; width: 100%;"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        <!-- قائمة الاتصال -->
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">قائمة الاتصال</h4>
                                                </div>
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="contact-fields-container" id="contactContainer">
                                                            <!-- الحقول الديناميكية ستضاف هنا -->
                                                        </div>
                                                        <div class="text-right mt-1">
                                                            <button type="button" class="btn btn-outline-success mr-1 mb-1" onclick="addContactFields()">
                                                                <i class="feather icon-plus"></i> إضافة جهة اتصال
                                                            </button>
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

                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">بيانات الحساب</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-body">
                                    <div class="row">
                                        <!-- رقم الكود -->
                                        <div class="col-6 mb-3">
                                            <div class="form-group">
                                                <label for="code">رقم الكود <span class="text-danger">*</span></label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="code" class="form-control" name="code" value="{{ old('code', $newCode) }}" readonly>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-hash"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- طريقة الفاتورة -->
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="printing_method">طريقة الفاتورة</label>
                                                <div class="position-relative has-icon-left">
                                                    <select class="form-control" id="printing_method" name="printing_method">
                                                        <option value="1" {{ old('printing_method') == 1 ? 'selected' : '' }}>الطباعة</option>
                                                        <option value="2" {{ old('printing_method') == 2 ? 'selected' : '' }}>ارسل عبر البريد</option>
                                                    </select>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file-text"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الرصيد الافتتاحي -->
                                        @foreach ($GeneralClientSettings as $GeneralClientSetting)
                                            @if ($GeneralClientSetting->is_active && $GeneralClientSetting->key == 'opening_balance')
                                                <div class="col-md-6 col-12 mb-3">
                                                    <div class="form-group">
                                                        <label for="opening_balance">الرصيد الافتتاحي</label>
                                                        <div class="position-relative has-icon-left">
                                                            <input type="number" id="opening_balance" class="form-control" name="opening_balance" value="{{ old('opening_balance') }}">
                                                            <div class="form-control-position">
                                                                <i class="feather icon-dollar-sign"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        <!-- تاريخ الرصيد الاستحقاق -->
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="opening_balance_date">تاريخ الرصيد الاستحقاق</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="date" id="opening_balance_date" class="form-control" name="opening_balance_date" value="{{ old('opening_balance_date', date('Y-m-d')) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- العملة -->
                                        <div class="col-md-12 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="currency">العملة</label>
                                                <div class="position-relative has-icon-left">
                                                    <select class="form-control" id="currency" name="currency">
                                                        <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>SAR</option>
                                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                                    </select>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-credit-card"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- البريد الإلكتروني -->
                                        <div class="col-md-12 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="email">البريد الإلكتروني</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-mail"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- التصنيف -->
                                        <div class="col-md-12 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="category_id">التصنيف</label>
                                                <select class="form-control select2" name="category_id" id="category_id">
                                                    <option value="">اختر التصنيف</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- الملاحظات -->
                                        <div class="col-md-12 col-12 mb-3">
                                            <label for="notes">الملاحظات</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="5" style="resize: none;">{{ old('notes') }}</textarea>
                                        </div>

                                        <!-- المرفقات ونوع العميل والفرع -->
                                        @foreach ($GeneralClientSettings as $GeneralClientSetting)
                                            @if ($GeneralClientSetting->is_active && $GeneralClientSetting->key == 'image')
                                                <div class="col-md-12 col-12 mb-3">
                                                    <div class="form-group">
                                                        <label for="attachments">المرفقات</label>
                                                        <input type="file" name="attachments" id="attachments" class="d-none">
                                                        <div class="upload-area border rounded p-3 text-center position-relative" onclick="document.getElementById('attachments').click()">
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
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        <!-- نوع العميل -->
                                        <div class="col-md-12 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="client_type">نوع العميل</label>
                                                <div class="position-relative has-icon-left">
                                                    <select class="form-control" name="client_type" id="client_type">
                                                        <option value="1" {{ old('client_type') == 1 ? 'selected' : '' }}>عميل VIP</option>
                                                        <option value="2" {{ old('client_type') == 2 ? 'selected' : '' }}>عميل عادي</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الفرع -->
                                        <div class="col-md-12 col-12 mb-3">
                                            <div class="form-group">
                                                <label for="branch_id">الفرع <span class="text-danger">*</span></label>
                                                <select class="form-control" name="branch_id" id="branch_id" required>
                                                    <option value="">اختر الفرع</option>
                                                    @foreach ($branches as $branche)
                                                        <option value="{{ $branche->id }}">{{ $branche->name ?? 'لا يوجد فروع' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- الموظفين المسؤولين -->
                                        @if (auth()->user()->role === 'manager')
                                            <div class="col-md-12 col-12 mb-3">
                                                <div class="form-group">
                                                    <label for="employee_select" class="form-label">الموظفين المسؤولين</label>
                                                    <select id="employee_select" class="form-control">
                                                        <option value="">اختر الموظف</option>
                                                        @foreach ($employees as $employee)
                                                            <option value="{{ $employee->id }}" data-name="{{ $employee->full_name }}">
                                                                {{ $employee->full_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <!-- الحقل الحقيقي الذي سترسله للباك إند -->
                                                    <div id="selected_employees"></div>

                                                    <!-- هنا سيظهر الموظفون المختارون -->
                                                    <ul id="employee_list" class="mt-2 list-group"></ul>
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
        </form>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initGoogleMaps" async defer></script>

    <script>
        // متغيرات الخريطة
        let map;
        let marker;
        let searchBox;
        let contactCounter = 0;
        let selectedEmployeeIds = [];
        let isGoogleMapsLoaded = false;
        let isMapInitialized = false;

        // دالة تهيئة Google Maps (callback function)
        function initGoogleMaps() {
            isGoogleMapsLoaded = true;
            console.log('Google Maps تم تحميله بنجاح');
        }

        // دالة إضافة حقول جهة اتصال جديدة
        function addContactFields() {
            contactCounter++;
            const contactContainer = document.getElementById('contactContainer');
            const newContactGroup = document.createElement('div');
            newContactGroup.className = 'contact-fields-group mb-3 p-3 border rounded';
            newContactGroup.innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label>الاسم الأول</label>
                        <input type="text" class="form-control" name="contacts[${contactCounter}][first_name]" placeholder="الاسم الأول">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>الاسم الأخير</label>
                        <input type="text" class="form-control" name="contacts[${contactCounter}][last_name]" placeholder="الاسم الأخير">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label>البريد الإلكتروني</label>
                        <input type="email" class="form-control" name="contacts[${contactCounter}][email]" placeholder="البريد الإلكتروني">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>الهاتف</label>
                        <input type="tel" class="form-control" name="contacts[${contactCounter}][phone]" placeholder="الهاتف">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label>جوال</label>
                        <input type="tel" class="form-control" name="contacts[${contactCounter}][mobile]" placeholder="جوال">
                    </div>
                    <div class="col-md-6 mb-2 text-right">
                        <button type="button" class="btn btn-danger mt-2" onclick="removeContactFields(this)">
                            <i class="fa fa-trash"></i> حذف
                        </button>
                    </div>
                </div>
                <hr>
            `;
            contactContainer.appendChild(newContactGroup);
        }

        // دالة حذف حقول جهة اتصال
        function removeContactFields(button) {
            const contactGroup = button.closest('.contact-fields-group');
            contactGroup.remove();
        }

        // دالة تهيئة الخريطة
        function initMap(lat = 24.7136, lng = 46.6753) {
            if (!isGoogleMapsLoaded) {
                console.log('انتظار تحميل Google Maps...');
                setTimeout(() => initMap(lat, lng), 500);
                return;
            }

            // تعيين الإحداثيات في الحقول المخفية
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            // إنشاء الخريطة
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat, lng },
                zoom: 15,
            });

            // إنشاء علامة
            marker = new google.maps.Marker({
                position: { lat, lng },
                map: map,
                draggable: true,
                title: 'موقع العميل',
            });

            // إنشاء صندوق البحث
            const searchBoxElement = document.getElementById('search-box');
            searchBox = new google.maps.places.SearchBox(searchBoxElement);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(searchBoxElement);

            // حدث عند تغيير نتائج البحث
            searchBox.addListener('places_changed', function() {
                const places = searchBox.getPlaces();
                if (places.length === 0) return;

                const place = places[0];
                const newLat = place.geometry.location.lat();
                const newLng = place.geometry.location.lng();

                updateMapPosition(newLat, newLng);
                fetchAddressFromCoordinates(newLat, newLng);
            });

            // حدث عند سحب العلامة
            marker.addListener('dragend', function() {
                const newLat = marker.getPosition().lat();
                const newLng = marker.getPosition().lng();
                document.getElementById('latitude').value = newLat;
                document.getElementById('longitude').value = newLng;
                fetchAddressFromCoordinates(newLat, newLng);
            });

            // حدث عند النقر على الخريطة
            map.addListener('click', function(event) {
                const newLat = event.latLng.lat();
                const newLng = event.latLng.lng();
                updateMapPosition(newLat, newLng);
            });

            isMapInitialized = true;
            console.log('تم تهيئة الخريطة بنجاح');
        }

        // دالة تحديث موقع الخريطة
        function updateMapPosition(lat, lng) {
            map.setCenter({ lat, lng });
            marker.setPosition({ lat, lng });
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            fetchAddressFromCoordinates(lat, lng);
        }

        // دالة جلب العنوان من الإحداثيات
        function fetchAddressFromCoordinates(lat, lng) {
            const geocoder = new google.maps.Geocoder();
            const latLng = { lat, lng };

            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    const addressComponents = results[0].address_components;

                    // تحديث حقول العنوان تلقائياً
                    document.getElementById('country').value = getAddressComponent(addressComponents, 'country');
                    document.getElementById('region').value = getAddressComponent(addressComponents, 'administrative_area_level_1');
                    document.getElementById('city').value = getAddressComponent(addressComponents, 'locality') || getAddressComponent(addressComponents, 'administrative_area_level_2');
                    document.getElementById('postal_code').value = getAddressComponent(addressComponents, 'postal_code');
                    document.getElementById('street1').value = getAddressComponent(addressComponents, 'route');
                    document.getElementById('street2').value = getAddressComponent(addressComponents, 'neighborhood');
                }
            });
        }

        // دالة مساعدة لجلب مكونات العنوان
        function getAddressComponent(components, type) {
            const component = components.find(c => c.types.includes(type));
            return component ? component.long_name : '';
        }

        // دالة طلب إذن الموقع وإظهار الخريطة
        function requestLocationPermission() {
            // التحقق من تحميل Google Maps أولاً
            if (!isGoogleMapsLoaded) {
                Swal.fire({
                    icon: 'info',
                    title: 'جاري التحميل...',
                    text: 'جاري تحميل الخريطة، يرجى الانتظار قليلاً',
                    timer: 2000,
                    showConfirmButton: false
                });

                // إعادة المحاولة بعد ثانية
                setTimeout(requestLocationPermission, 1000);
                return;
            }

            // إظهار الخريطة وصندوق البحث
            document.getElementById('map-container').style.display = 'block';
            document.getElementById('search-box').style.display = 'block';

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        if (!isMapInitialized) {
                            initMap(position.coords.latitude, position.coords.longitude);
                        } else {
                            updateMapPosition(position.coords.latitude, position.coords.longitude);
                        }
                    },
                    (error) => {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تحديد الموقع',
                            text: 'يرجى السماح بالوصول إلى الموقع لعرض الخريطة، أو استخدم خاصية البحث في الخريطة.',
                            confirmButtonText: 'موافق'
                        });

                        // إظهار الخريطة بموقع افتراضي (الرياض)
                        if (!isMapInitialized) {
                            initMap();
                        }
                    }
                );
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'المتصفح لا يدعم تحديد الموقع. يرجى استخدام متصفح آخر.',
                    confirmButtonText: 'موافق'
                });

                // إظهار الخريطة بموقع افتراضي
                if (!isMapInitialized) {
                    initMap();
                }
            }
        }

        // إدارة الموظفين
        function initEmployeeManagement() {
            const employeeSelect = document.getElementById('employee_select');
            const employeeList = document.getElementById('employee_list');
            const selectedEmployees = document.getElementById('selected_employees');

            if (employeeSelect) {
                employeeSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const employeeId = selectedOption.value;
                    const employeeName = selectedOption.dataset.name;

                    if (employeeId && !selectedEmployeeIds.includes(employeeId)) {
                        selectedEmployeeIds.push(employeeId);

                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center';
                        li.textContent = employeeName;

                        const removeBtn = document.createElement('button');
                        removeBtn.textContent = 'حذف';
                        removeBtn.className = 'btn btn-sm btn-danger';
                        removeBtn.onclick = () => {
                            li.remove();
                            selectedEmployeeIds = selectedEmployeeIds.filter(id => id !== employeeId);
                            updateHiddenInputs();
                        };

                        li.appendChild(removeBtn);
                        employeeList.appendChild(li);
                        updateHiddenInputs();
                    }

                    this.value = '';
                });
            }

            function updateHiddenInputs() {
                selectedEmployees.innerHTML = '';
                selectedEmployeeIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'employee_client_id[]';
                    input.value = id;
                    selectedEmployees.appendChild(input);
                });
            }
        }

        // معالجة إرسال النموذج بـ AJAX
        function handleFormSubmission() {
            const form = document.getElementById('clientForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // التحقق من الحقول المطلوبة
                const tradeName = document.getElementById('trade_name').value.trim();
                const regionId = document.getElementById('region_id').value;
                const branchId = document.getElementById('branch_id').value;
                const latitude = document.getElementById('latitude').value;
                const longitude = document.getElementById('longitude').value;

                if (!tradeName) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في البيانات',
                        text: 'يرجى إدخال الاسم التجاري',
                        confirmButtonText: 'موافق'
                    });
                    return;
                }

                if (!regionId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في البيانات',
                        text: 'يرجى اختيار المجموعة',
                        confirmButtonText: 'موافق'
                    });
                    return;
                }

                if (!branchId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في البيانات',
                        text: 'يرجى اختيار الفرع',
                        confirmButtonText: 'موافق'
                    });
                    return;
                }

                if (!latitude || !longitude) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تحديد الموقع مطلوب',
                        text: 'يرجى تحديد موقع العميل على الخريطة قبل الحفظ!',
                        confirmButtonText: 'إظهار الخريطة',
                        showCancelButton: true,
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            requestLocationPermission();
                        }
                    });
                    return;
                }

                // تعطيل زر الإرسال وإظهار مؤشر التحميل
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري الحفظ...';

                // إنشاء FormData
                const formData = new FormData(form);

                // إرسال البيانات بـ AJAX
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم بنجاح!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            }
                        });
                    } else {
                        // عرض أخطاء التحقق
                        let errorMessage = data.message || 'حدث خطأ أثناء حفظ البيانات';

                        if (data.errors) {
                            const errorList = Object.values(data.errors).flat();
                            errorMessage = errorList.join('\n');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ في البيانات',
                            text: errorMessage,
                            confirmButtonText: 'موافق'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في الشبكة',
                        text: 'حدث خطأ أثناء إرسال البيانات. يرجى المحاولة مرة أخرى.',
                        confirmButtonText: 'موافق'
                    });
                })
                .finally(() => {
                    // إعادة تفعيل زر الإرسال
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa fa-save"></i> حفظ';
                });
            });
        }

        // تهيئة الصفحة عند التحميل
        document.addEventListener('DOMContentLoaded', function() {
            // إضافة مستمع لزر إظهار الخريطة
            const showMapBtn = document.getElementById('showMapBtn');
            if (showMapBtn) {
                showMapBtn.addEventListener('click', function() {
                    // تغيير نص الزر أثناء التحميل
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري تحميل الخريطة...';
                    this.disabled = true;

                    // استدعاء دالة إظهار الخريطة
                    requestLocationPermission();

                    // إعادة النص الأصلي بعد ثانيتين
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }, 2000);
                });
            }

            // تهيئة إدارة الموظفين
            initEmployeeManagement();

            // تهيئة معالجة إرسال النموذج
            handleFormSubmission();

            // إظهار اسم الملف المحدد
            const attachmentsInput = document.getElementById('attachments');
            if (attachmentsInput) {
                attachmentsInput.addEventListener('change', function() {
                    const fileName = this.files[0]?.name;
                    if (fileName) {
                        const uploadArea = document.querySelector('.upload-area');
                        uploadArea.innerHTML = `
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-check text-success"></i>
                                <span class="text-success">${fileName}</span>
                            </div>
                        `;
                    }
                });
            }
        });

        // windows callback للتأكد من تحميل Google Maps
        window.initGoogleMaps = initGoogleMaps;
    </script>
@endsection
