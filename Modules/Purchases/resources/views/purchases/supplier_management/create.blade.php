@extends('master')

@section('title')
    اضافة مورد
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> اضافة مورد</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">اضافة مورد</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <form id="supplierForm" action="{{ route('SupplierManagement.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                        </div>

                        <div>
                            <a href="{{ route('SupplierManagement.index') }}" class="btn btn-outline-danger">
                                <i class="fa fa-ban"></i> الغاء
                            </a>
                            <button type="button" id="saveBtn" class="btn btn-outline-primary">
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
                            <h4 class="card-title">بيانات دليل المورد </h4>
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
                                                        value="{{ old('trade_name') }}" required>
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
                                                        class="form-control" value="{{ old('first_name') }}">
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
                                                        class="form-control" value="{{ old('last_name') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-user"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الهاتف والجوال -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="phone">الهاتف</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="phone" id="phone" class="form-control"
                                                        value="{{ old('phone') }}">
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
                                                        value="{{ old('mobile') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-smartphone"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- عنوان الشارع -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="street1">عنوان الشارع 1</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="street1" id="street1" class="form-control"
                                                        value="{{ old('street1') }}">
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
                                                    <input type="text" name="street2" id="street2"
                                                        class="form-control" value="{{ old('street2') }}">
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
                                                    <input type="text" name="city" id="city"
                                                        class="form-control" value="{{ old('city') }}">
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
                                                    <input type="text" name="region" id="region"
                                                        class="form-control" value="{{ old('region') }}">
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
                                                        class="form-control" value="{{ old('postal_code') }}">
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
                                                    <option value="SA" {{ old('country') == 'SA' ? 'selected' : '' }}>
                                                        المملكة العربية السعودية (SA)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- الرقم الضريبي والسجل التجاري -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="tax_number">الرقم الضريبي (اختياري)</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="tax_number" id="tax_number"
                                                        class="form-control" value="{{ old('tax_number') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file-text"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="commercial_registration">سجل تجاري (اختياري)</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" name="commercial_registration"
                                                        id="commercial_registration" class="form-control"
                                                        value="{{ old('commercial_registration') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

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
                                                            <button type="button"
                                                                class="btn btn-outline-success mr-1 mb-1"
                                                                onclick="addContactFields()">
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
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label for="number_suply">رقم المورد</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="number_suply" class="form-control"
                                                        name="number_suply" value="{{ $nextNumber }}" readonly
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
                                                <select name="currency" id="currency"
                                                    class="form-control select2 @error('currency') is-invalid @enderror">
                                                    <option value="">اختر العملة</option>
                                                    @if (class_exists('\App\Helpers\CurrencyHelper'))
                                                        @foreach (\App\Helpers\CurrencyHelper::getAllCurrencies() as $code => $name)
                                                            <option value="{{ $code }}"
                                                                {{ old('currency', 'SAR') == $code ? 'selected' : '' }}>
                                                                {{ $code }} - {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="SAR" {{ old('currency', 'SAR') == 'SAR' ? 'selected' : '' }}>
                                                            SAR - ريال سعودي</option>
                                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>
                                                            USD - دولار أمريكي</option>
                                                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>
                                                            EUR - يورو</option>
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
                                                    <input type="number" id="opening_balance" class="form-control"
                                                        name="opening_balance" value="{{ old('opening_balance', 0) }}" step="0.01">
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
                                                    <input type="date" id="opening_balance_date" class="form-control"
                                                        name="opening_balance_date"
                                                        value="{{ old('opening_balance_date') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- البريد الإلكتروني -->
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="email">البريد الإلكتروني</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="email" id="email" class="form-control"
                                                        name="email" value="{{ old('email') }}">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-mail"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الملاحظات -->
                                        <div class="col-md-12 mb-3">
                                            <label for="notes">الملاحظات</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="5" style="resize: none;">{{ old('notes') }}</textarea>
                                        </div>

                                        <!-- المرفقات -->
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="attachments">المرفقات</label>
                                                <input type="file" name="attachments" id="attachments" class="d-none">
                                                <div class="upload-area border rounded p-3 text-center position-relative"
                                                    onclick="document.getElementById('attachments').click()">
                                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                                        <i class="fas fa-cloud-upload-alt text-primary"></i>
                                                        <span class="text-primary">اضغط هنا</span>
                                                        <span>أو</span>
                                                        <span class="text-primary">اختر من جهازك</span>
                                                    </div>
                                                    <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                                                        <i class="fas fa-file-alt fs-3 text-secondary"></i>
                                                    </div>
                                                    <div id="file-name" class="mt-2 text-muted small"></div>
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
        </form>
    </div>
@endsection


@section('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // إضافة متغير contactCounter في بداية السكريبت
        let contactCounter = 0;

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

        // التعامل مع زر الحفظ
        document.getElementById('saveBtn').addEventListener('click', function(e) {
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

            // رسالة التأكيد قبل الحفظ
            Swal.fire({
                title: 'تأكيد الحفظ',
                text: 'هل أنت متأكد من رغبتك في حفظ بيانات المورد؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // عرض رسالة التحميل
                    Swal.fire({
                        title: 'جاري الحفظ...',
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
                    document.getElementById('supplierForm').submit();
                }
            });
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
                    <h6 class="mb-0 text-primary">جهة اتصال ${contactCounter}</h6>
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

        // إضافة استماع للأحداث عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            console.log('الصفحة محملة بنجاح مع SweetAlert2');

            // إضافة تأثيرات بصرية للحقول المطلوبة
            const requiredFields = document.querySelectorAll('input[required]');
            requiredFields.forEach(field => {
                field.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.style.borderColor = '#dc3545';
                    } else {
                        this.style.borderColor = '#28a745';
                    }
                });
            });
        });
    </script>
@endsection
