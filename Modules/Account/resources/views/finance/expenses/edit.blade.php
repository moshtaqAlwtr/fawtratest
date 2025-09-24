@extends('master')

@section('title')
سند صرف
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">سند صرف</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">اضافه
                            </li>
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
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i>الغاء
                        </a>

                        <button type="submit" form="expenses_form" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i>تحديث
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="expenses_form" action="{{ route('expenses.update',$expense->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="amount">المبلغ <span style="color: red">*</span></label>
                            <input type="text" class="form-control form-control-lg py-3" id="amount" placeholder="ر.س 0.00" name="amount" value="{{ old('amount',$expense->amount) }}">
                            @error('amount')
                            <span class="text-danger" id="basic-default-name-error" class="error">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-4">
                            <label for="description">الوصف</label>
                            <textarea class="form-control" id="description" rows="3" name="description">{{ old('description',$expense->description) }}</textarea>
                        </div>

                        <div class="form-group col-md-4">
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

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="code-number">رقم الكود</label>
                            <input type="text" class="form-control" id="code-number" name="code" value="{{ old('code',$expense->code) }}">
                            @error('code')
                            <span class="text-danger" id="basic-default-name-error" class="error">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-4">
                            <label for="date">التاريخ</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ old('date',$expense->date) }}" >
                        </div>
                        <div class="form-group col-md-4">
                            <label for="unit">الوحدة</label>
                            <select id="unit" class="form-control" name="unit_id">
                                <option selected disabled>حدد الوحدة</option>
                                <option value="1" {{ old('unit_id',$expense->unit_id) == 1 ? 'selected' : '' }}>وحدة 1</option>
                                <option value="2" {{ old('unit_id',$expense->unit_id) == 2 ? 'selected' : '' }}>وحدة 2</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="category">التصنيف</label>
                            <select id="category" class="form-control" name="expenses_category_id">
                                <option selected disabled>-- إضافة تصنيف --</option>
                                @foreach($expenses_categories as $expenses_category)
                                    <option value="{{ $expenses_category->id }}" {{ old('expenses_category_id',$expense->expenses_category_id) == $expenses_category->id ? 'selected' : '' }}>{{ $expenses_category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="seller">البائع</label>
                            <select id="seller" class="form-control" name="vendor_id">
                                <option selected disabled>اختر بائع</option>
                                <option value="1" {{ old('vendor_id',$expense->vendor_id) == 2 ? 'selected' : '' }}>بائع 1</option>
                                <option value="2" {{ old('vendor_id',$expense->vendor_id) == 2 ? 'selected' : '' }}>بائع 2</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="warehouse">خزينة</label>
                            <select id="warehouse" class="form-control" name="store_id">
                                <option selected disabled value="1">رئيسي</option>
                                <option value="2" {{ old('store_id',$expense->store_id) == 2 ? 'selected' : '' }}>فرعي</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="min-limit">الحساب الفرعي </label>
                            <input type="text" class="form-control" id="min-limit" name="sup_account" value="{{ old('sup_account',$expense->sup_account) }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="items">المورد</label>
                            <select id="items" class="form-control" name="vendor_id">
                                <option selected disabled>اختر مورد</option>
                                <option value="1" {{ old('vendor_id',$expense->vendor_id) == 2 ? 'selected' : '' }}>مورد 1</option>
                                <option value="2" {{ old('vendor_id',$expense->vendor_id) == 2 ? 'selected' : '' }}>مورد 2</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="tax">الضرائب</label>
                            <button type="button" class="btn btn-info btn-block" onclick="toggleTaxFields()">إضافة ضرائب</button>
                        </div>
                    </div>

                    <!-- حقول الضرائب -->
                    <div id="tax-fields" class="tax-fields">
                        <span class="remove-tax" onclick="removeTaxFields()">إزالة الضرائب ×</span>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="tax1">الضريبة الأولى</label>
                                <select id="tax1" class="form-control" name="tax1">
                                    <option selected disabled>-</option>
                                    <option value="1" {{ old('tax1',$expense->tax1) == 1 ? 'selected' : '' }}>ضريبة 1</option>
                                    <option value="2" {{ old('tax1',$expense->tax1) == 2 ? 'selected' : '' }}>ضريبة 2</option>
                                </select>
                                <input type="text" class="form-control mt-2" placeholder="المبلغ" name="tax1_amount" value="{{ old('tax1_amount',$expense->tax1_amount) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tax2">الضريبة الثانية</label>
                                <select id="tax2" class="form-control" name="tax2">
                                    <option selected disabled>-</option>
                                    <option value="1" {{ old('tax2',$expense->tax2) == 1 ? 'selected' : '' }}>ضريبة 1</option>
                                    <option value="2" {{ old('tax2',$expense->tax2) == 2 ? 'selected' : '' }}>ضريبة 2</option>
                                </select>
                                <input type="text" class="form-control mt-2" placeholder="المبلغ" name="tax2_amount" value="{{ old('tax2_amount',$expense->tax2_amount) }}">
                            </div>
                        </div>
                    </div>

                    <div class="container mt-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-2">
                                        <label for="checkbox">مكرر</label>
                                        <input type="checkbox" id="checkbox" name="is_recurring" {{ $expense->is_recurring == 1 ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <!-- حقل التكرار و تاريخ الإنتهاء يظهر عند تحديد الـ checkbox -->
                                <div class="row" id="duplicate-options-container" style="display: none;">
                                    <div class="form-group col-md-4">
                                        <label for="duplicate-options">التكرار</label>
                                        <select id="duplicate-options" class="form-control">
                                            <option selected>حدد التكرار</option>
                                            <option value="weekly">إسبوعي</option>
                                            <option value="bi-weekly">كل أسبوعين</option>
                                            <option value="monthly">شهري</option>
                                            <option value="yearly">سنوي</option>
                                            <option value="daily">يومي</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- حقل تاريخ الإنتهاء -->
                                <div class="row" id="end-date-container" style="display: none;">
                                    <div class="form-group col-md-4">
                                        <label for="end-date">تاريخ الإنتهاء</label>
                                        <input type="date" class="form-control" id="end-date" name="end_date">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
<!-- JavaScript للتحكم في إظهار وإخفاء الخيارات -->
<script>
    document.getElementById('checkbox').addEventListener('change', function() {
        var duplicateOptionsContainer = document.getElementById('duplicate-options-container');
        var endDateContainer = document.getElementById('end-date-container');
        if (this.checked) {
            duplicateOptionsContainer.style.display = 'block';  // إظهار خيارات التكرار
            endDateContainer.style.display = 'block';           // إظهار حقل تاريخ الإنتهاء
        } else {
            duplicateOptionsContainer.style.display = 'none';  // إخفاء خيارات التكرار
            endDateContainer.style.display = 'none';           // إخفاء حقل تاريخ الإنتهاء
        }
    });
</script>
<script>
        function toggleTaxFields() {
            $("#tax-fields").slideToggle();
        }

        function removeTaxFields() {
            $("#tax-fields").slideUp();
        }
    </script>
@endsection
