@extends('master')

@section('title')
الأعدادات العامة
@stop

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">الأعدادات العامة</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
                        <li class="breadcrumb-item active">عرض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
            </div>

            <div class="d-flex align-items-center">
                <a href="{{ route('products.index') }}" class="btn btn-outline-danger mr-2">
                    <i class="fa fa-ban"></i> الغاء
                </a>
                <button type="submit" form="products_form" class="btn btn-outline-primary">
                    <i class="fa fa-save"></i> حفظ
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h5 class="card-title">خيارات الفاتورة</h5>
        <div class="container">
            <div class="row">
                <!-- السطر الأول -->
                <div class="col-md-6 mb-3">
                    <div class="custom-control custom-switch custom-switch-success d-flex align-items-center">
                        <input type="checkbox" class="custom-control-input" id="invoiceDetailsSwitch1" name="invoiceDetailsSwitch1" value="1">
                        <label class="custom-control-label" for="invoiceDetailsSwitch1"></label>
                        <span class="switch-label">عرض بنود الفاتورة لكل يوم</span>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="custom-control custom-switch custom-switch-success d-flex align-items-center">
                        <input type="checkbox" class="custom-control-input" id="invoiceDetailsSwitch2" name="invoiceDetailsSwitch2" value="1">
                        <label class="custom-control-label" for="invoiceDetailsSwitch2"></label>
                        <span class="switch-label">إنشاء فواتير مسودة لأمر الحجز</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- السطر الثاني -->
                <div class="col-md-6 mb-3">
                    <div class="custom-control custom-switch custom-switch-success d-flex align-items-center">
                        <input type="checkbox" class="custom-control-input" id="enableCustomerBooking" name="enableCustomerBooking" value="1" onchange="toggleDropdown()">
                        <label class="custom-control-label" for="enableCustomerBooking"></label>
                        <span class="switch-label">تمكين حجز العميل</span>
                    </div>
               <!-- أزرار الراديو -->
               <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="customerBookingEnabled" name="customerBooking" class="custom-control-input" value="enabled" onchange="toggleRadioDropdown(true)">
                <label class="custom-control-label" for="customerBookingEnabled">تم تفعيله</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="customerBookingDisabled" name="customerBooking" class="custom-control-input" value="disabled" onchange="toggleRadioDropdown(false)" checked>
                <label class="custom-control-label" for="customerBookingDisabled">تم تعطيله</label>
            </div>
            </div>
                    <!-- القائمة المنسدلة -->
                    <div id="customerBookingDropdown" class="mt-2" style="display: none;">
                        <label for="customerBookingStatus" class="form-label">الحالة الأولية لأمر الحجز الخاص بالعملاء <span style="color: red">*</span></label>
                        <select id="customerBookingStatus" name="customerBookingStatus" class="form-control">
                            <option value="" disabled selected>من فضلك اختر</option>
                            <option value="confirmed">تم تأكيده</option>
                            <option value="pending">في انتظار التأكيد</option>
                            <option value="cancelled">تم إلغاؤه</option>
                        </select>
                    </div>
            

                <div class="col-md-6 mb-3">
                    <label for="additionalFields" class="form-label">حقول إضافية في أمر الحجز لواجهة المتجر</label>
                    <input type="text" id="additionalFields" class="form-control" placeholder="أختر الحقول الإضافية لأمر الحجز">
                </div>
            </div>
        </div>
    </div>
</div>

@extends('master')

@section('title')
الأعدادات العامة
@stop

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">الأعدادات العامة</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
                        <li class="breadcrumb-item active">عرض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
            </div>

            <div class="d-flex align-items-center">
                <a href="{{ route('products.index') }}" class="btn btn-outline-danger mr-2">
                    <i class="fa fa-ban"></i> الغاء
                </a>
                <button type="submit" form="products_form" class="btn btn-outline-primary">
                    <i class="fa fa-save"></i> حفظ
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h5 class="card-title">خيارات الفاتورة</h5>
        <div class="container">
            <div class="row">
                <!-- السطر الأول -->
                <div class="col-md-6 mb-3">
                    <div class="custom-control custom-switch custom-switch-success d-flex align-items-center">
                        <input type="checkbox" class="custom-control-input" id="invoiceDetailsSwitch1" name="invoiceDetailsSwitch1" value="1">
                        <label class="custom-control-label" for="invoiceDetailsSwitch1"></label>
                        <span class="switch-label">عرض بنود الفاتورة لكل يوم</span>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="custom-control custom-switch custom-switch-success d-flex align-items-center">
                        <input type="checkbox" class="custom-control-input" id="invoiceDetailsSwitch2" name="invoiceDetailsSwitch2" value="1">
                        <label class="custom-control-label" for="invoiceDetailsSwitch2"></label>
                        <span class="switch-label">إنشاء فواتير مسودة لأمر الحجز</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- السطر الثاني -->
                <div class="col-md-6 mb-3">
                    <div class="custom-control custom-switch custom-switch-success d-flex align-items-center">
                        <input type="checkbox" class="custom-control-input" id="enableCustomerBooking" name="enableCustomerBooking" value="1" onchange="toggleDropdown()">
                        <label class="custom-control-label" for="enableCustomerBooking"></label>
                        <span class="switch-label">تمكين حجز العميل</span>
                    </div>

                    <!-- حاوية أزرار الراديو والقائمة المنسدلة -->
                    <div id="customerBookingContainer" class="mt-2" style="display: none;">
                        <!-- أزرار الراديو -->
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customerBookingEnabled" name="customerBooking" class="custom-control-input" value="enabled" onchange="toggleRadioDropdown(true)">
                            <label class="custom-control-label" for="customerBookingEnabled">تم تفعيله</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customerBookingDisabled" name="customerBooking" class="custom-control-input" value="disabled" onchange="toggleRadioDropdown(false)" checked>
                            <label class="custom-control-label" for="customerBookingDisabled">تم تعطيله</label>
                        </div>

                        <!-- القائمة المنسدلة -->
                        <div id="customerBookingDropdown" class="mt-2" style="display: none;">
                            <label for="customerBookingStatus" class="form-label">الحالة الأولية لأمر الحجز الخاص بالعملاء <span style="color: red">*</span></label>
                            <select id="customerBookingStatus" name="customerBookingStatus" class="form-control">
                                <option value="" disabled selected>من فضلك اختر</option>
                                <option value="confirmed"> مؤكد</option>
                                <option value="pending">معلق</option>
                                <option value="cancelled">ملغي</option>
                                <option value="cancelled">منتهي</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="additionalFields" class="form-label">حقول إضافية في أمر الحجز لواجهة المتجر</label>
                    <input type="text" id="additionalFields" class="form-control" placeholder="أختر الحقول الإضافية لأمر الحجز">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDropdown() {
        const checkbox = document.getElementById('enableCustomerBooking');
        const container = document.getElementById('customerBookingContainer');
        const radioEnabled = document.getElementById('customerBookingEnabled');
        const dropdown = document.getElementById('customerBookingDropdown');

        if (checkbox.checked) {
            container.style.display = 'block';
            if (radioEnabled.checked) {
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        } else {
            container.style.display = 'none';
            dropdown.style.display = 'none';
        }
    }

    function toggleRadioDropdown(isEnabled) {
        const dropdown = document.getElementById('customerBookingDropdown');
        dropdown.style.display = isEnabled ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        // تحديث الحالة عند تحميل الصفحة
        toggleDropdown();
    });
</script>
@endsection

@endsection
