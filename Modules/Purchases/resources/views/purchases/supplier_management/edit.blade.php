@extends('master')

@section('title')
    تعديل المورد
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل مورد: {{ $supplier->trade_name }}</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('SupplierManagement.index') }}">الموردين</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <form id="supplierUpdateForm" action="{{ route('SupplierManagement.update', $supplier->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- أزرار الحفظ والإلغاء -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                        </div>
                        <div>
                            <a href="{{ route('SupplierManagement.index') }}" class="btn btn-outline-danger" id="cancelBtn">
                                <i class="fa fa-ban"></i> الغاء
                            </a>
                            <button type="button" class="btn btn-outline-primary" id="updateBtn">
                                <i class="fa fa-save"></i> تحديث
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- بيانات المورد -->
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">بيانات المورد</h4>
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
                                                    <input type="text" name="trade_name" id="trade_name"
                                                        class="form-control @error('trade_name') is-invalid @enderror"
                                                        value="{{ old('trade_name', $supplier->trade_name ?? '') }}"
                                                        required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-briefcase"></i>
                                                    </div>
                                                </div>
                                                @error('trade_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- الاسم الأول والأخير -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="first_name">الاسم الأول</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="first_name" id="first_name"
                                                        class="form-control @error('first_name') is-invalid @enderror"
                                                        value="{{ old('first_name', $supplier->first_name ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-user"></i>
                                                    </div>
                                                </div>
                                                @error('first_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="last_name">الاسم الأخير</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="last_name" id="last_name"
                                                        class="form-control @error('last_name') is-invalid @enderror"
                                                        value="{{ old('last_name', $supplier->last_name ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-user"></i>
                                                    </div>
                                                </div>
                                                @error('last_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- الهاتف والجوال -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="phone">الهاتف</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="phone" id="phone"
                                                        class="form-control @error('phone') is-invalid @enderror"
                                                        value="{{ old('phone', $supplier->phone ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-phone"></i>
                                                    </div>
                                                </div>
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="mobile">جوال</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="mobile" id="mobile"
                                                        class="form-control @error('mobile') is-invalid @enderror"
                                                        value="{{ old('mobile', $supplier->mobile ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-smartphone"></i>
                                                    </div>
                                                </div>
                                                @error('mobile')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- عنوان الشارع -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="street1">عنوان الشارع 1</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="street1" id="street1"
                                                        class="form-control @error('street1') is-invalid @enderror"
                                                        value="{{ old('street1', $supplier->street1 ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map-pin"></i>
                                                    </div>
                                                </div>
                                                @error('street1')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="street2">عنوان الشارع 2</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="street2" id="street2"
                                                        class="form-control @error('street2') is-invalid @enderror"
                                                        value="{{ old('street2', $supplier->street2 ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map-pin"></i>
                                                    </div>
                                                </div>
                                                @error('street2')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- المدينة والمنطقة والرمز البريدي -->
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="city">المدينة</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="city" id="city"
                                                        class="form-control @error('city') is-invalid @enderror"
                                                        value="{{ old('city', $supplier->city ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map"></i>
                                                    </div>
                                                </div>
                                                @error('city')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="region">المنطقة</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="region" id="region"
                                                        class="form-control @error('region') is-invalid @enderror"
                                                        value="{{ old('region', $supplier->region ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-map"></i>
                                                    </div>
                                                </div>
                                                @error('region')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="postal_code">الرمز البريدي</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="postal_code" id="postal_code"
                                                        class="form-control @error('postal_code') is-invalid @enderror"
                                                        value="{{ old('postal_code', $supplier->postal_code ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-mail"></i>
                                                    </div>
                                                </div>
                                                @error('postal_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- البلد -->
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label for="country">البلد</label>
                                                <select name="country" id="country" class="form-control @error('country') is-invalid @enderror">
                                                    <option value="">اختر البلد</option>
                                                    <option value="SA" {{ old('country', $supplier->country ?? '') == 'SA' ? 'selected' : '' }}>
                                                        المملكة العربية السعودية (SA)
                                                    </option>
                                                </select>
                                                @error('country')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- الرقم الضريبي والسجل التجاري -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="tax_number">الرقم الضريبي (اختياري)</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="tax_number" id="tax_number"
                                                        class="form-control @error('tax_number') is-invalid @enderror"
                                                        value="{{ old('tax_number', $supplier->tax_number ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file-text"></i>
                                                    </div>
                                                </div>
                                                @error('tax_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="commercial_registration">سجل تجاري (اختياري)</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="commercial_registration"
                                                        id="commercial_registration"
                                                        class="form-control @error('commercial_registration') is-invalid @enderror"
                                                        value="{{ old('commercial_registration', $supplier->commercial_registration ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file"></i>
                                                    </div>
                                                </div>
                                                @error('commercial_registration')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- قائمة الاتصال -->
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">قائمة الاتصال</h4>
                                                </div>
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="contact-fields-container" id="contactContainer">
                                                            <!-- حقول جهات الاتصال الموجودة -->
                                                            @if(isset($supplier->contacts) && count($supplier->contacts) > 0)
                                                                @foreach($supplier->contacts as $index => $contact)
                                                                    <div class="contact-fields-group mb-3 p-3 border rounded" style="background-color: #f8f9ff; border-color: #e3e6ef;">
                                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                                            <h6 class="mb-0 text-primary">جهة اتصال {{ $index + 1 }}</h6>
                                                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeContactFields(this)">
                                                                                <i class="fa fa-trash"></i> حذف
                                                                            </button>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-6 mb-2">
                                                                                <label>الاسم الأول</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="contacts[{{ $index }}][first_name]"
                                                                                    value="{{ old('contacts.'.$index.'.first_name', $contact->first_name ?? '') }}"
                                                                                    placeholder="الاسم الأول">
                                                                            </div>
                                                                            <div class="col-md-6 mb-2">
                                                                                <label>الاسم الأخير</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="contacts[{{ $index }}][last_name]"
                                                                                    value="{{ old('contacts.'.$index.'.last_name', $contact->last_name ?? '') }}"
                                                                                    placeholder="الاسم الأخير">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-6 mb-2">
                                                                                <label>البريد الإلكتروني</label>
                                                                                <input type="email" class="form-control"
                                                                                    name="contacts[{{ $index }}][email]"
                                                                                    value="{{ old('contacts.'.$index.'.email', $contact->email ?? '') }}"
                                                                                    placeholder="البريد الإلكتروني">
                                                                            </div>
                                                                            <div class="col-md-6 mb-2">
                                                                                <label>الهاتف</label>
                                                                                <input type="tel" class="form-control"
                                                                                    name="contacts[{{ $index }}][phone]"
                                                                                    value="{{ old('contacts.'.$index.'.phone', $contact->phone ?? '') }}"
                                                                                    placeholder="الهاتف">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-6 mb-2">
                                                                                <label>جوال</label>
                                                                                <input type="tel" class="form-control"
                                                                                    name="contacts[{{ $index }}][mobile]"
                                                                                    value="{{ old('contacts.'.$index.'.mobile', $contact->mobile ?? '') }}"
                                                                                    placeholder="جوال">
                                                                            </div>
                                                                        </div>
                                                                        @if($contact->id ?? false)
                                                                            <input type="hidden" name="contacts[{{ $index }}][id]" value="{{ $contact->id }}">
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            @endif
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

                <!-- بيانات الحساب -->
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">بيانات الحساب</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-body">
                                    <div class="row">
                                        <!-- رقم المورد -->
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label for="number_suply">رقم المورد</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="number_suply" class="form-control"
                                                        name="number_suply"
                                                        value="{{ old('number_suply', $supplier->number_suply ?? '') }}"
                                                        readonly
                                                        style="background-color: #f8f8f8; cursor: not-allowed;">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-hash"></i>
                                                    </div>
                                                </div>
                                                <small class="text-muted">يتم إنشاء الرقم تلقائياً</small>
                                            </div>
                                        </div>

                                        <!-- العملة -->
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label for="currency">العملة</label>
                                                <select name="currency" id="currency" class="form-control select2 @error('currency') is-invalid @enderror">
                                                    <option value="">اختر العملة</option>
                                                    @if(class_exists('\App\Helpers\CurrencyHelper'))
                                                        @foreach (\App\Helpers\CurrencyHelper::getAllCurrencies() as $code => $name)
                                                            <option value="{{ $code }}"
                                                                {{ old('currency', $supplier->currency ?? '') == $code ? 'selected' : '' }}>
                                                                {{ $code }} - {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="SAR" {{ old('currency', $supplier->currency ?? '') == 'SAR' ? 'selected' : '' }}>SAR - ريال سعودي</option>
                                                        <option value="USD" {{ old('currency', $supplier->currency ?? '') == 'USD' ? 'selected' : '' }}>USD - دولار أمريكي</option>
                                                        <option value="EUR" {{ old('currency', $supplier->currency ?? '') == 'EUR' ? 'selected' : '' }}>EUR - يورو</option>
                                                    @endif
                                                </select>
                                                @error('currency')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- الرصيد الافتتاحي -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="opening_balance">الرصيد الافتتاحي</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="number" id="opening_balance"
                                                        class="form-control @error('opening_balance') is-invalid @enderror"
                                                        name="opening_balance" step="0.01"
                                                        value="{{ old('opening_balance', $supplier->opening_balance ?? 0) }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-dollar-sign"></i>
                                                    </div>
                                                </div>
                                                @error('opening_balance')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- تاريخ الرصيد الاستحقاق -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="opening_balance_date">تاريخ الرصيد الاستحقاق</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="date" id="opening_balance_date"
                                                        class="form-control @error('opening_balance_date') is-invalid @enderror"
                                                        name="opening_balance_date"
                                                        value="{{ old('opening_balance_date', $supplier->opening_balance_date ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-calendar"></i>
                                                    </div>
                                                </div>
                                                @error('opening_balance_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- البريد الإلكتروني -->
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="email">البريد الإلكتروني</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="email" id="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        name="email"
                                                        value="{{ old('email', $supplier->email ?? '') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-mail"></i>
                                                    </div>
                                                </div>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- الملاحظات -->
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="notes">الملاحظات</label>
                                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                                    id="notes" name="notes" rows="5"
                                                    style="resize: vertical;">{{ old('notes', $supplier->notes ?? '') }}</textarea>
                                                @error('notes')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- المرفقات -->
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="attachments">المرفقات الجديدة</label>
                                                <input type="file" name="attachments" id="attachments"
                                                    class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                <div class="upload-area border rounded p-4 text-center position-relative"
                                                    onclick="document.getElementById('attachments').click()"
                                                    style="cursor: pointer; border: 2px dashed #007bff !important;">
                                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                                        <i class="fas fa-cloud-upload-alt text-primary fs-2"></i>
                                                        <div>
                                                            <span class="text-primary font-weight-bold">اضغط هنا لاختيار ملف جديد</span>
                                                            <br>
                                                            <small class="text-muted">أو اسحب الملف هنا</small>
                                                        </div>
                                                    </div>
                                                    <div id="file-name" class="mt-2 text-muted small"></div>
                                                </div>

                                                @if(isset($supplier->attachments) && count($supplier->attachments) > 0)
                                                    <div class="mt-3">
                                                        <h6 class="text-primary">الملفات الحالية:</h6>
                                                        <div class="border rounded p-2" style="background-color: #f8f9fa;">
                                                            @foreach($supplier->attachments as $attachment)
                                                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fa fa-file text-primary me-2"></i>
                                                                        <span>{{ $attachment->filename ?? 'ملف مرفق' }}</span>
                                                                    </div>
                                                                    <div>
                                                                        <a href="{{ $attachment->url ?? '#' }}" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                                                            <i class="fa fa-eye"></i> عرض
                                                                        </a>
                                                                        <a href="{{ $attachment->download_url ?? '#' }}" class="btn btn-sm btn-outline-success">
                                                                            <i class="fa fa-download"></i> تحميل
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            @endforeach
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
        </form>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // متغير عداد جهات الاتصال
        let contactCounter = {{ isset($supplier->contacts) ? count($supplier->contacts) : 0 }};
        let formChanged = false;

        // عرض رسائل الجلسة باستخدام SweetAlert2
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح!',
                text: '{{ session("success") }}',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#28a745',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'حدث خطأ!',
                text: '{{ session("error") }}',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#dc3545'
            });
        @endif

        @if($errors->any())
            let errorMessages = '';
            @foreach($errors->all() as $error)
                errorMessages += '• {{ $error }}\n';
            @endforeach

            Swal.fire({
                icon: 'error',
                title: 'يرجى تصحيح الأخطاء التالية:',
                text: errorMessages,
                confirmButtonText: 'موافق',
                confirmButtonColor: '#dc3545'
            });
        @endif

        // التعامل مع زر التحديث
        document.getElementById('updateBtn').addEventListener('click', function(e) {
            e.preventDefault();

            // التحقق من الحقول المطلوبة
            const tradeName = document.getElementById('trade_name').value.trim();

            if (!tradeName) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه!',
                    text: 'يرجى إدخال الاسم التجاري',
                    confirmButtonText: 'موافق',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            // رسالة التأكيد قبل التحديث
            Swal.fire({
                title: 'تأكيد التحديث',
                text: 'هل أنت متأكد من رغبتك في تحديث بيانات المورد؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، حدث البيانات',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // عرض رسالة التحميل
                    Swal.fire({
                        title: 'جاري التحديث...',
                        text: 'يرجى الانتظار',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // إرسال النموذج
                    document.getElementById('supplierUpdateForm').submit();
                }
            });
        });

        // التعامل مع زر الإلغاء
        document.getElementById('cancelBtn').addEventListener('click', function(e) {
            if (formChanged) {
                e.preventDefault();
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: 'سيتم فقدان جميع التغييرات غير المحفوظة',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، اخرج بدون حفظ',
                    cancelButtonText: 'العودة للتعديل'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formChanged = false;
                        window.location.href = this.href;
                    }
                });
            }
        });

        // دالة إضافة حقول جهة اتصال جديدة
        function addContactFields() {
            contactCounter++;
            const contactContainer = document.getElementById('contactContainer');
            const newContactGroup = document.createElement('div');
            newContactGroup.className = 'contact-fields-group mb-3 p-3 border rounded';
            newContactGroup.style.borderColor = '#e3e6ef';
            newContactGroup.style.backgroundColor = '#f8f9ff';
            newContactGroup.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-primary">جهة اتصال جديدة ${contactCounter}</h6>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeContactFields(this)">
                        <i class="fa fa-trash"></i> حذف
                    </button>
                </div>
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
                </div>
            `;
            contactContainer.appendChild(newContactGroup);

            // تحديد أن النموذج تم تغييره
            formChanged = true;

            // رسالة تأكيد الإضافة
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'تم إضافة جهة اتصال جديدة',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }

        // دالة حذف حقول جهة اتصال
        function removeContactFields(button) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف بيانات جهة الاتصال نهائياً',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    const contactGroup = button.closest('.contact-fields-group');
                    contactGroup.remove();
                    formChanged = true;

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'تم حذف جهة الاتصال',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            });
        }

        // التعامل مع تحديد الملف
        document.getElementById('attachments').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const fileNameDiv = document.getElementById('file-name');

            if (fileName) {
                fileNameDiv.textContent = `تم اختيار: ${fileName}`;
                fileNameDiv.style.color = '#28a745';
                formChanged = true;

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'تم اختيار الملف بنجاح',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            } else {
                fileNameDiv.textContent = '';
            }
        });

        // تتبع التغييرات في النموذج
        document.addEventListener('DOMContentLoaded', function() {
            console.log('تم تحميل صفحة تعديل المورد بنجاح مع SweetAlert2');

            // تتبع التغييرات في جميع الحقول
            const formInputs = document.querySelectorAll('#supplierUpdateForm input, #supplierUpdateForm select, #supplierUpdateForm textarea');

            formInputs.forEach(input => {
                input.addEventListener('change', function() {
                    formChanged = true;
                });

                input.addEventListener('input', function() {
                    formChanged = true;
                });
            });

            // منع فقدان البيانات عند الخروج من الصفحة
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = 'لديك تغييرات غير محفوظة. هل أنت متأكد من رغبتك في مغادرة الصفحة؟';
                }
            });

            // إزالة التحذير عند الإرسال
            const form = document.getElementById('supplierUpdateForm');
            form.addEventListener('submit', function() {
                formChanged = false;
            });

            // إضافة تأثيرات بصرية للحقول المطلوبة
            const requiredFields = document.querySelectorAll('input[required]');
            requiredFields.forEach(field => {
                field.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.style.borderColor = '#dc3545';
                        this.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
                    } else {
                        this.style.borderColor = '#28a745';
                        this.style.boxShadow = '0 0 0 0.2rem rgba(40, 167, 69, 0.25)';
                    }
                });

                field.addEventListener('focus', function() {
                    this.style.borderColor = '#007bff';
                    this.style.boxShadow = '0 0 0 0.2rem rgba(0, 123, 255, 0.25)';
                });
            });

            // تحسين تجربة المستخدم مع الحقول العادية
            const allInputs = document.querySelectorAll('.form-control');
            allInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });
        });

        // دالة التحقق من التغييرات
        function hasFormChanged() {
            return formChanged;
        }

        // إظهار رسالة ترحيب عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            // يمكن إضافة رسالة ترحيب إضافية هنا إذا لزم الأمر
        });
    </script>

    <style>
        .upload-area:hover {
            background-color: #f8f9fa;
            border-color: #007bff !important;
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }

        .contact-fields-group {
            transition: all 0.3s ease;
            border: 1px solid #e3e6ef !important;
        }

        .contact-fields-group:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .position-relative.focused .form-control-position i {
            color: #007bff;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* تحسين مظهر الملفات الحالية */
        .border.rounded.p-2 {
            border: 1px solid #dee2e6 !important;
        }

        .border-bottom.py-2:last-child {
            border-bottom: none !important;
        }

        /* تحسين responsive للموبايل */
        @media (max-width: 768px) {
            .contact-fields-group .row .col-md-6 {
                margin-bottom: 1rem;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }

            .upload-area {
                padding: 2rem 1rem !important;
            }
        }

        /* تحسين الأنيميشن */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .contact-fields-group {
            animation: fadeIn 0.3s ease;
        }
    </style>
@endsection
