@extends('client')

@section('title')
    تعديل العميل - {{ $client->trade_name }}
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل العميل</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">تعديل
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <form id="clientForm" action="{{ route('clients.Client_store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <input type="hidden" value="{{$client->id}}">

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                        </div>

                        <div>
                            <a href="{{ route('clients.index') }}" class="btn btn-outline-danger">
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
                                                <label for="trade_name">الاسم التجاري <span
                                                        class="text-danger">*</span></label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="trade_name" id="trade_name"
                                                        class="form-control" value="{{ old('trade_name', $client->trade_name) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-briefcase"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الاسم الأول والأخير -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="first_name">الاسم الأول</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="first_name" id="first_name"
                                                        class="form-control" value="{{ old('first_name', $client->first_name) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-user"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="last_name">الاسم الأخير</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="last_name" id="last_name"
                                                        class="form-control" value="{{ old('last_name', $client->last_name) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-user"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الهاتف والجوال -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="phone">هاتف</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="phone" id="phone" class="form-control"
                                                        value="{{ old('phone', $client->phone) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-phone"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="mobile">جوال</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="mobile" id="mobile" class="form-control"
                                                        value="{{ old('mobile', $client->mobile) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-smartphone"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- البريد الإلكتروني -->


                                        <!-- العنوان -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="street1">عنوان الشارع 1</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="street1" id="street1" class="form-control"
                                                        value="{{ old('street1', $client->street1) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map-pin"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="street2">عنوان الشارع 2</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="street2" id="street2" class="form-control"
                                                        value="{{ old('street2', $client->street2) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map-pin"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- المدينة والمنطقة والرمز البريدي -->
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="city">المدينة</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="city" id="city" class="form-control"
                                                        value="{{ old('city', $client->city) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="region">المنطقة</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="region" id="region" class="form-control"
                                                        value="{{ old('region', $client->region) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="postal_code">الرمز البريدي</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="postal_code" id="postal_code"
                                                        class="form-control" value="{{ old('postal_code', $client->postal_code) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map-pin"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="country">البلد</label>
                                                <div class="position-relative has-icon-left">
                                                    <select name="country" id="country" class="form-control">
                                                        <option value="SA" {{ old('country', $client->country) == 'SA' ? 'selected' : '' }}>المملكة العربية السعودية</option>
                                                        <option value="AE" {{ old('country', $client->country) == 'AE' ? 'selected' : '' }}>الإمارات العربية المتحدة</option>
                                                        <option value="BH" {{ old('country', $client->country) == 'BH' ? 'selected' : '' }}>البحرين</option>
                                                        <option value="KW" {{ old('country', $client->country) == 'KW' ? 'selected' : '' }}>الكويت</option>
                                                        <option value="OM" {{ old('country', $client->country) == 'OM' ? 'selected' : '' }}>عمان</option>
                                                        <option value="QA" {{ old('country', $client->country) == 'QA' ? 'selected' : '' }}>قطر</option>
                                                    </select>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-flag"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الدولة -->
                                        <div class="col-6 mb-3">
                                            <div class="form-group">
                                                <label for="tax_number">الرقم الضريبي</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="tax_number" id="tax_number"
                                                        class="form-control" value="{{ old('tax_number', $client->tax_number) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file-text"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="commercial_registration">السجل التجاري</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="commercial_registration"
                                                        id="commercial_registration" class="form-control"
                                                        value="{{ old('commercial_registration', $client->commercial_registration) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="credit_limit">الحد الائتماني (SAR)</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="number" name="credit_limit" id="credit_limit"
                                                        class="form-control" value="{{ old('credit_limit', $client->credit_limit) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-credit-card"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- المدة الائتمانية -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="credit_period">المدة الائتمانية (أيام)</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="number" name="credit_period" id="credit_period"
                                                        class="form-control" value="{{ old('credit_period', $client->credit_period) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-clock"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <button type="button" class="btn btn-outline-primary" onclick="toggleMap()">
                                                <i class="feather icon-map"></i> إظهار الخريطة
                                            </button>
                                            <div id="map" style="display: none; height: 300px; margin-top: 10px;" class="border rounded"></div>
                                        </div>
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
                                                        <button type="button" class="btn btn-outline-success mr-1 mb-1 إضافة"  onclick="addContactFields()">
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

                <div class="col-md-6">
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
                                                    <input type="text" id="code" class="form-control" name="code"
                                                        value="{{ old('code', $client->code) }}" required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-hash"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- طريقة الطباعة -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="printing_method">طريقة الطباعة</label>
                                                <div class="position-relative has-icon-left">
                                                    <select name="printing_method" id="printing_method" class="form-control">
                                                        <option value="">اختر طريقة الطباعة</option>
                                                        <option value="1" {{ old('printing_method', $client->printing_method) == '1' ? 'selected' : '' }}>
                                                            طباعة</option>
                                                        <option value="2" {{ old('printing_method', $client->printing_method) == '2' ? 'selected' : '' }}>
                                                            ارسل عبر البريد</option>
                                                    </select>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-printer"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الرصيد الافتتاحي -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="opening_balance">الرصيد الافتتاحي</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="number" step="0.01" name="opening_balance" id="opening_balance"
                                                        class="form-control" value="{{ old('opening_balance', $client->opening_balance) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-dollar-sign"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- تاريخ الرصيد الاستحقاق -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="opening_balance_date">تاريخ الرصيد الاستحقاق</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="date" name="opening_balance_date" id="opening_balance_date"
                                                        class="form-control" value="{{ old('opening_balance_date', $client->opening_balance_date) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الرقم الضريبي -->


                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label for="email">البريد الإلكتروني</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="email" name="email" id="email" class="form-control"
                                                        value="{{ old('email', $client->email) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-mail"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- السجل التجاري -->


                                        <!-- العملة -->
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="currency">العملة</label>
                                                <div class="position-relative has-icon-left">
                                                    <select class="form-control" id="currency" name="currency">
                                                        <option value="">اختر العملة</option>
                                                        <option value="SAR" {{ old('currency', $client->currency) == 'SAR' ? 'selected' : '' }}>
                                                            ريال سعودي</option>
                                                        <option value="USD" {{ old('currency', $client->currency) == 'USD' ? 'selected' : '' }}>
                                                            دولار أمريكي</option>
                                                        <option value="EUR" {{ old('currency', $client->currency) == 'EUR' ? 'selected' : '' }}>
                                                            يورو</option>
                                                    </select>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-dollar-sign"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="client_type">تصنيف العميل</label>
                                                <div class="position-relative has-icon-left">
                                                    <select name="client_type" id="client_type" class="form-control">
                                                        <option value="">اختر نوع العميل</option>
                                                        <option value="1" {{ old('client_type', $client->client_type) == '1' ? 'selected' : '' }}>عميل عادي</option>
                                                        <option value="2" {{ old('client_type', $client->client_type) == '2' ? 'selected' : '' }}>عميل VIP</option>
                                                    </select>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-users"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الملاحظات -->
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label for="notes">ملاحظات</label>
                                                <div class="position-relative has-icon-left">
                                                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $client->notes) }}</textarea>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file-text"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- الحد الائتماني -->

                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="attachments" class="form-label">المرفقات</label>
                                                <input type="file" class="form-control @error('attachments') is-invalid @enderror"
                                                       id="attachments" name="attachments">
                                                @error('attachments')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                @if($client->attachments)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('uploads/clients/' . $client->attachments) }}"
                                                             alt="مرفق العميل" class="img-thumbnail" style="max-width: 200px;">
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
        </form>
    </div>
</div>
@endsection


@section('scripts')
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
@endsection
