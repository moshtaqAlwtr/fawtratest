@extends('master')

@section('title')
    المخزون
@stop

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة المنتجات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">اضافه عملية على المخزون
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <div>
                            <strong>{{ $product->name }} </strong> | <small>{{ $product->serial_number }}#</small> | <span class="badge badge-pill badge badge-success">في المخزن</span>
                        </div>
                    </div>

                    <div></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <form id="stock_adjustment_form" action="{{ route('products.add_manual_stock_adjust',$product->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="operation_type">عملية اضافة او سحب <span style="color: red">*</span></label>
                                                <select class="form-control" name="type" id="operation_type" required>
                                                    <option value="" disabled selected>-- اختر العملية --</option>
                                                    <option value="1" {{ old('type') == 1 ? 'selected' : '' }}>اضافة</option>
                                                    <option value="2" {{ old('type') == 2 ? 'selected' : '' }}>سحب</option>
                                                </select>
                                            </div>
                                        </div>

                                       @if($role)
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="quantity">الكمية <span style="color: red">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" id="quantity" class="form-control" name="quantity"
                                                           value="{{ old('quantity') }}" min="0.01" step="0.01" required>
                                                    <select class="form-select form-select-sm" id="sub-unit" name="sub_unit_id" style="width: auto;">
                                                        @foreach ($SubUnits as $key => $SubUnit)
                                                            <option value="{{ $SubUnit->id }}" {{ $key == 0 ? 'selected' : '' }}>
                                                                {{ $SubUnit->larger_unit_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="quantity_simple">الكمية <span style="color: red">*</span></label>
                                                <input type="number" id="quantity_simple" class="form-control" name="quantity"
                                                       value="{{ old('quantity') }}" min="0.01" step="0.01" required>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="unit_price">سعر الوحدة <span style="color: red">*</span></label>
                                                <input type="number" id="unit_price" class="form-control" name="unit_price"
                                                       value="{{ old('unit_price', $product->sale_price) }}" min="0" step="0.01" required>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="date">تاريخ الحركة <span style="color: red">*</span></label>
                                                <input type="date" class="form-control" name="date" id="date"
                                                       value="{{ old('date') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="time">الوقت <span style="color: red">*</span></label>
                                                <input type="time" class="form-control" name="time" id="time"
                                                       value="{{ old('time') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="store_house">المستودع <span style="color: red">*</span></label>
                                                <select class="form-control" name="store_house_id" id="store_house" required>
                                                    <option value="" disabled selected>-- اختر المستودع --</option>
                                                    @foreach ($storehouses as $storehouse)
                                                        <option value="{{ $storehouse->id }}" {{ old('store_house_id') == $storehouse->id ? 'selected' : '' }}>
                                                            {{ $storehouse->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="type_of_operation">نوع العملية</label>
                                                <select class="form-control" name="type_of_operation" id="type_of_operation">
                                                    <option value="1" {{ old('type_of_operation') == 1 ? 'selected' : '' }}>رصيد افتتاحي</option>
                                                    <option value="2" {{ old('type_of_operation') == 2 ? 'selected' : '' }}>تسوية مخزونية</option>
                                                    <option value="3" {{ old('type_of_operation') == 3 ? 'selected' : '' }}>عملية يدوية</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="subaccount">الحساب الفرعي</label>
                                                <input type="text" class="form-control" name="subaccount" id="subaccount"
                                                       value="{{ old('subaccount') }}" placeholder="ادخل الحساب الفرعي">
                                            </div>
                                        </div>

                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="status">الحالة</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>معلق</option>
                                                    <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>مكتمل</option>
                                                    <option value="3" {{ old('status') == 3 ? 'selected' : '' }}>متجاهل</option>
                                                    <option value="4" {{ old('status') == 4 ? 'selected' : '' }}>مجدول مرة أخرى</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="duration">المدة (بالأيام)</label>
                                                <input type="number" class="form-control" name="duration" id="duration"
                                                       value="{{ old('duration') }}" min="0" placeholder="عدد الأيام">
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="attachments">المرفقات</label>
                                                <input type="file" class="form-control" name="attachments" id="attachments"
                                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                                                <small class="text-muted">الملفات المسموحة: PDF, DOC, DOCX, JPG, PNG, GIF</small>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="comments">ملاحظات</label>
                                                <textarea name="comments" class="form-control" id="comments" rows="2"
                                                          placeholder="ادخل ملاحظاتك هنا">{{ old('comments') }}</textarea>
                                            </div>
                                        </div>

                                        <!-- عرض الحسابات -->
                                        <div class="col-12 mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title">ملخص العملية</h6>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <strong>الكمية:</strong> <span id="summary_quantity">0</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <strong>سعر الوحدة:</strong> <span id="summary_unit_price">0.00</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <strong>الإجمالي:</strong> <span id="summary_total">0.00</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <strong>نوع العملية:</strong> <span id="summary_operation">غير محدد</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <label class="text-muted">الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-outline-warning mr-1 mb-1" id="reset_form">
                                                        <i class="fa fa-refresh"></i> تفريغ
                                                    </button>
                                                    <button type="submit" class="btn btn-primary mr-1 mb-1">
                                                        <i class="fa fa-save"></i> حفظ
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تعيين التاريخ والوقت الحاليين
            const now = new Date();

            // تنسيق التاريخ إلى YYYY-MM-DD
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const formattedDate = `${year}-${month}-${day}`;

            // تنسيق الوقت إلى HH:MM
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const formattedTime = `${hours}:${minutes}`;

            // تعيين القيم في حقلي الإدخال
            document.getElementById('date').value = formattedDate;
            document.getElementById('time').value = formattedTime;

            // العناصر المطلوبة
            const quantityInput = document.getElementById('quantity') || document.getElementById('quantity_simple');
            const unitPriceInput = document.getElementById('unit_price');
            const operationTypeSelect = document.getElementById('operation_type');
            const form = document.getElementById('stock_adjustment_form');
            const resetButton = document.getElementById('reset_form');

            // عناصر الملخص
            const summaryQuantity = document.getElementById('summary_quantity');
            const summaryUnitPrice = document.getElementById('summary_unit_price');
            const summaryTotal = document.getElementById('summary_total');
            const summaryOperation = document.getElementById('summary_operation');

            // دالة تحديث الملخص
            function updateSummary() {
                const quantity = parseFloat(quantityInput.value) || 0;
                const unitPrice = parseFloat(unitPriceInput.value) || 0;
                const total = quantity * unitPrice;
                const operationType = operationTypeSelect.options[operationTypeSelect.selectedIndex].text;

                summaryQuantity.textContent = quantity;
                summaryUnitPrice.textContent = unitPrice.toFixed(2);
                summaryTotal.textContent = total.toFixed(2);
                summaryOperation.textContent = operationType || 'غير محدد';
            }

            // ربط الأحداث
            if (quantityInput) {
                quantityInput.addEventListener('input', updateSummary);
            }
            if (unitPriceInput) {
                unitPriceInput.addEventListener('input', updateSummary);
            }
            if (operationTypeSelect) {
                operationTypeSelect.addEventListener('change', updateSummary);
            }

            // التحقق من صحة النموذج
            form.addEventListener('submit', function(e) {
                // منع الإرسال التلقائي في جميع الحالات
                e.preventDefault();

                const quantity = parseFloat(quantityInput.value);
                const unitPrice = parseFloat(unitPriceInput.value);
                const operationType = operationTypeSelect.value;
                const storeHouse = document.getElementById('store_house').value;

                // التحقق من الكمية
                if (!quantity || quantity <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في البيانات',
                        text: 'يجب إدخال كمية صحيحة أكبر من صفر!',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                // التحقق من سعر الوحدة
                if (!unitPrice || unitPrice < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في البيانات',
                        text: 'يجب إدخال سعر وحدة صحيح!',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                // التحقق من نوع العملية
                if (!operationType) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في البيانات',
                        text: 'يجب اختيار نوع العملية!',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                // التحقق من المستودع
                if (!storeHouse) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في البيانات',
                        text: 'يجب اختيار المستودع!',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                // إذا وصلنا هنا، فكل البيانات صحيحة - أظهر رسالة التأكيد
                const operationText = operationType == 1 ? 'إضافة' : 'سحب';
                const total = (quantity * unitPrice).toFixed(2);

                Swal.fire({
                    title: 'تأكيد العملية',
                    html: `
                        <div style="text-align: right; direction: rtl;">
                            <p><strong>نوع العملية:</strong> ${operationText}</p>
                            <p><strong>الكمية:</strong> ${quantity}</p>
                            <p><strong>سعر الوحدة:</strong> ${unitPrice}</p>
                            <p><strong>الإجمالي:</strong> ${total}</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، احفظ!',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // إرسال النموذج بعد التأكيد
                        form.removeEventListener('submit', arguments.callee);
                        form.submit();
                    }
                });
            });

            // زر التفريغ
            resetButton.addEventListener('click', function() {
                Swal.fire({
                    title: 'تأكيد التفريغ',
                    text: 'هل أنت متأكد من تفريغ جميع البيانات؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، فرغ!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.reset();
                        // إعادة تعيين التاريخ والوقت
                        document.getElementById('date').value = formattedDate;
                        document.getElementById('time').value = formattedTime;
                        updateSummary();

                        Swal.fire({
                            title: 'تم التفريغ!',
                            text: 'تم تفريغ جميع البيانات بنجاح.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // تحديث الملخص عند تحميل الصفحة
            updateSummary();

            // تحسين تجربة المستخدم - تنبيه عند تغيير نوع العملية
            operationTypeSelect.addEventListener('change', function() {
                const operationType = this.value;
                const operationText = operationType == 1 ? 'إضافة' : operationType == 2 ? 'سحب' : '';

                if (operationText) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'info',
                        title: `تم اختيار عملية ${operationText}`
                    });
                }
            });

            // التحقق من حجم الملف المرفق
            const attachmentsInput = document.getElementById('attachments');
            if (attachmentsInput) {
                attachmentsInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const fileSize = file.size / 1024 / 1024; // بالميجابايت
                        if (fileSize > 10) { // أكثر من 10 ميجابايت
                            Swal.fire({
                                icon: 'warning',
                                title: 'حجم الملف كبير',
                                text: 'حجم الملف يجب أن يكون أقل من 10 ميجابايت',
                                confirmButtonText: 'حسناً'
                            });
                            this.value = '';
                        }
                    }
                });
            }
        });
    </script>
@endsection
