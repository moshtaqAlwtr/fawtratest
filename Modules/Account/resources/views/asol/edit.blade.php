@extends('master')

@section('title')
    تعديل اصل
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل  الاصول  </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <form id="clientForm" action="{{ route('Assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
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
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                        </div>

                        <div>
                            <a href="{{ route('Assets.index') }}" class="btn btn-outline-danger">
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
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">بيانات الأصل</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="code">الكود <span class="text-danger">*</span></label>
                                            <div class="position-relative has-icon-left">
                                                <input type="text" name="code" id="code" class="form-control"
                                                    value="{{ old('code', $asset->code) }}">
                                                <div class="form-control-position">
                                                    <i class="feather icon-hash"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">الاسم </label>
                                            <div class="position-relative has-icon-left">
                                                <input type="text" name="name" id="name" class="form-control"
                                                    value="{{ old('name', $asset->name) }}">
                                                <div class="form-control-position">
                                                    <i class="feather icon-box"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="date_price">تاريخ الشراء</label>
                                            <div class="position-relative has-icon-left">
                                                <input type="date" name="date_price" id="date_price" class="form-control"
                                                    value="{{ old('date_price', $asset->date_price) }}">
                                                <div class="form-control-position">
                                                    <i class="feather icon-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="date_service">تاريخ بداية الخدمة </label>
                                            <div class="position-relative has-icon-left">
                                                <input type="date" name="date_service" id="date_service"
                                                    class="form-control" value="{{ old('date_service', $asset->date_service) }}">
                                                <div class="form-control-position">
                                                    <i class="feather icon-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="account_id">حساب الأصل <span class="text-danger">*</span></label>
                                            <div class="position-relative has-icon-left">
                                                <select name="account_id" id="account_id" class="form-control select2" required>
                                                    <option value="">اختر حساب الأصل</option>
                                                    @foreach($accounts as $account)
                                                        <option value="{{ $account->id }}" {{ old('account_id', $asset->account_id) == $account->id ? 'selected' : '' }}>
                                                            {{ $account->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="form-control-position">
                                                    <i class="feather icon-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="place">المكان </label>
                                            <div class="position-relative has-icon-left">
                                                <input type="text" name="place" id="place" class="form-control"
                                                    value="{{ old('place', $asset->place) }}">
                                                <div class="form-control-position">
                                                    <i class="feather icon-map-pin"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="region_age">العمر الانتاجي </label>
                                            <div class="position-relative has-icon-left">
                                                <input type="number" name="region_age" id="region_age"
                                                    class="form-control" value="{{ old('region_age', $asset->region_age) }}">
                                                <div class="form-control-position">
                                                    <i class="feather icon-clock"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="quantity">الكمية</label>
                                            <div class="position-relative has-icon-left">
                                                <input type="number" name="quantity" id="quantity"
                                                    class="form-control" value="{{ old('quantity', $asset->quantity) }}">
                                                <div class="form-control-position">
                                                    <i class="feather icon-package"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="postal_code">الموضف</label>
                                            <div class="position-relative has-icon-left">
                                                <select name="employee_id" class="form-control select2" id="">
                                                    <option value="">اختر الموظف</option>
                                                    @foreach ($employees as $employee)
                                                        <option value="{{ $employee->id }}" {{ old('employee_id', $asset->employee_id) == $employee->id ? 'selected' : '' }}>
                                                            {{ $employee->full_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="form-control-position">
                                                    <i class="feather icon-mail"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- البلد -->
                                    <div class="col-md-12 mb-3">
                                        <label for="notes">الوصف</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="5" style="resize: none;">{{ old('notes', $asset->notes) }}</textarea>
                                    </div>
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
                                            </div>
                                        </div>



                                    </div>





                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title">بيانات التسعير</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                    <!-- رقم الكود -->
                                    <div class="col-8 mb-3">
                                        <div class="form-group">
                                            <label for="purchase_value">قيمة الشراء <span
                                                    class="text-danger">*</span></label>
                                            <div class="position-relative has-icon-left">
                                                <input type="number" step="0.01" id="purchase_value" class="form-control"
                                                    name="purchase_value" value="{{ old('purchase_value', $asset->purchase_value) }}">
                                                <div class="form-control-position">
                                                    <i class="feather icon-dollar-sign"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- طريقة الفاتورة -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="printing_method">ٌٍSRA</label>
                                            <div class="position-relative has-icon-left">
                                                <select name="currency" class="form-control select2" id="currency">
                                                    <option value="">SRA</option>
                                                    <option value="1" {{ old('currency', $asset->currency) == 1 ? 'selected' : '' }}>ريال</option>
                                                    <option value="2" {{ old('currency', $asset->currency) == 2 ? 'selected' : '' }}>دولار</option>

                                                </select>
                                                <div class="form-control-position">
                                                    <i class="feather icon-dollar-sign"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- الرصيد الافتتاحي -->
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="opening_balance">حساب النقدية </label>
                                            <div class="position-relative has-icon-left">
                                                <select name="cash_account" id="cash_account" class="form-control select2">
                                                    <option value="">اختر حساب النقدية</option>
                                                    @foreach ($accounts_all as $account)
                                                        
                                                         <option value="{{ $account->id }}" {{ old('account_id', $asset->cash_account) == $account->id ? 'selected' : '' }}>
                                                            {{ $account->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="form-control-position">
                                                    <i class="feather icon-credit-card"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- تاريخ الرصيد الاستحقاق -->
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="opening_balance_date">الضريبة 1</label>
                                            <div class="position-relative has-icon-left">
                                                <select name="tax1" class="form-control" id="">
                                                    <option value="1" {{ old('tax1', $asset->tax1) == 1 ? 'selected' : '' }}> القيمة المضافة</option>
                                                    <option value="2" {{ old('tax1', $asset->tax1) == 2 ? 'selected' : '' }}> صفرية</option>
                                                    <option value="3" {{ old('tax1', $asset->tax1) == 3 ? 'selected' : '' }}> قيمة مضافة</option>

                                                </select>
                                                <div class="form-control-position">
                                                    <i class="feather icon-percent"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="opening_balance_date">الضريبة 2</label>
                                            <div class="position-relative has-icon-left">
                                                <select name="tax2" class="form-control" id="">
                                                    <option value="1" {{ old('tax2', $asset->tax2) == 1 ? 'selected' : '' }}> القيمة المضافة</option>
                                                    <option value="2" {{ old('tax2', $asset->tax2) == 2 ? 'selected' : '' }}> صفرية</option>
                                                    <option value="3" {{ old('tax2', $asset->tax2) == 3 ? 'selected' : '' }}> قيمة مضافة</option>

                                                </select>
                                                <div class="form-control-position">
                                                    <i class="feather icon-percent"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- العملة -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">إهلاك الأصل</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>قيمة الخردة <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <input type="number" step="0.01" class="form-control" name="depreciation_value"
                                                    value="{{ old('depreciation_value', $asset->depreciation_value) }}">
                                                <div class="form-control-position">
                                                    <i class="feather icon-trending-down"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>طريقة الإهلاك <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <select name="depreciation_method" class="form-control select2">
                                                    <option value="">من فضلك اختر</option>
                                                    <option value="1" {{ old('depreciation_method', $asset->depreciation_method) == 1 ? 'selected' : '' }}>طريقة القسط الثابت</option>
                                                    <option value="2" {{ old('depreciation_method', $asset->depreciation_method) == 2 ? 'selected' : '' }}>طريقة القسط المتناقص</option>
                                                    <option value="3" {{ old('depreciation_method', $asset->depreciation_method) == 3 ? 'selected' : '' }}>وحدات الانتاج</option>
                                                    <option value="4" {{ old('depreciation_method', $asset->depreciation_method) == 4 ? 'selected' : '' }}>بدون الاهلاك</option>
                                                </select>
                                                <div class="form-control-position">
                                                    <i class="feather icon-bar-chart-2"></i>
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


            </div>

        </form>
    </div>


    </div>
    </div>

    <!------------------------->


@endsection

@section('scripts')

    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script>
        document.getElementById('clientForm').addEventListener('submit', function(e) {
            // e.preventDefault(); // احذف هذا السطر إذا كنت تريد أن يتم إرسال النموذج

            console.log('تم تقديم النموذج');

            // طباعة جميع البيانات المرسلة
            const formData = new FormData(this);
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
        });
    </script>
@endsection
