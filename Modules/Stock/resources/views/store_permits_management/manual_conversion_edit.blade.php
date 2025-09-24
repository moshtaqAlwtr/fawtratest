@extends('master')

@section('title')
تعديل الأذون المخزنية
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل الأذون المخزنية</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('store_permits_management.index') }}">الأذون المخزنية</a>
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
        <div class="container-fluid">
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

            <form id="products_form" class="form-horizontal" action="{{ route('store_permits_management.update', $permit->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                            </div>

                            <div>
                                <a href="{{ route('store_permits_management.index') }}" class="btn btn-outline-danger">
                                    <i class="fa fa-ban"></i>الغاء
                                </a>
                                <button type="button" id="submit" class="btn btn-outline-primary">
                                    <i class="fa fa-save"></i>تحديث
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">تعديل الأذون المخزنية</h4>
                        </div>

                        <div class="card-body">
                            <div class="form-body row">
                                <div class="form-group col-md-2 mt-1">
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input type="checkbox" name="permission_type" checked="" value="3" disabled>
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إذن تحويل يدوي</span>
                                    </div>
                                </div>

                                <input type="hidden" name="permission_type" value="3">

                                <div class="form-group col-md-4">
                                    <label for="">التاريخ <span style="color: red">*</span></label>
                                    <input type="datetime-local" id="datetime" class="form-control" name="permission_date" value="{{ date('Y-m-d\TH:i', strtotime($permit->permission_date)) }}" required>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="">من مخزن <span style="color: red">*</span></label>
                                    <select class="form-control" name="from_store_houses_id" id="from_store_houses_id" required>
                                        <option value="">اختر المخزن</option>
                                        @foreach ($storeHouses as $storeHouse)
                                            <option value="{{ $storeHouse->id }}" {{ $permit->from_store_houses_id == $storeHouse->id ? 'selected' : '' }}>{{ $storeHouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="">الي مخزن <span style="color: red">*</span></label>
                                    <select class="form-control" name="to_store_houses_id" id="to_store_houses_id" required>
                                        <option value="">اختر المخزن</option>
                                        @foreach ($storeHouses as $storeHouse)
                                            <option value="{{ $storeHouse->id }}" {{ $permit->to_store_houses_id == $storeHouse->id ? 'selected' : '' }}>{{ $storeHouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">الحساب الفرعي</label>
                                    <select class="form-control" id="basicSelect" name="sub_account">
                                        <option value="1" {{ $permit->sub_account == 1 ? 'selected' : '' }}>الحساب الفرعي 1</option>
                                        <option value="2" {{ $permit->sub_account == 2 ? 'selected' : '' }}>الحساب الفرعي 2</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>رقم <span style="color: red">*</span></label>
                                    <input type="number" class="form-control" name="number" value="{{ $permit->number }}" required readonly>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="feedback2">الملاحظات </label>
                                    <textarea class="form-control" rows="2" placeholder="ادخل ملاحظاتك هنا" name="details">{{ $permit->details }}</textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>المرفقات</label>
                                    <input type="file" class="form-control" name="attachments" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    @if($permit->attachments)
                                        <small class="text-muted d-block mt-1">المرفق الحالي: <a href="{{ asset($permit->attachments) }}" target="_blank">عرض المرفق</a></small>
                                    @endif
                                </div>

                                <div class="form-group col-md-12">
                                    <table class="table table-striped" id="itemsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>البنود <span style="color: red">*</span></th>
                                                <th>سعر الوحدة</th>
                                                <th>الكمية <span style="color: red">*</span></th>
                                                <th>رصيد المخزن قبل</th>
                                                <th>رصيد المخزن بعد</th>
                                                <th>الإجمالي</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($permit->products as $index => $product)
                                            <tr>
                                                <td>
                                                    <select name="product_id[]" class="form-control select2 product-select" required>
                                                        <option value="" disabled>-- اختر البند --</option>
                                                        @foreach ($products as $prod)
                                                            <option value="{{ $prod->id }}" data-price="{{ $prod->sale_price }}" {{ $product->product_id == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" name="unit_price[]" class="form-control unit-price" value="{{ $product->unit_price }}" readonly></td>
                                                <td><input type="number" name="quantity[]" class="form-control quantity" value="{{ $product->quantity }}" min="1" required></td>
                                                <td class="stock-before">{{ $product->stock_before }}</td>
                                                <input type="hidden" name="stock_before[]" value="{{ $product->stock_before }}">
                                                <td class="stock-after">{{ $product->stock_after }}</td>
                                                <input type="hidden" name="stock_after[]" value="{{ $product->stock_after }}">
                                                <td><input type="number" name="total[]" class="form-control total" value="{{ $product->total }}" readonly></td>
                                                <td>
                                                    <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-minus"></i></button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <hr>
                                    <div class="d-flex justify-content-between mt-2">
                                        <button type="button" class="btn btn-outline-success btn-sm" id="addRow"><i class="fa fa-plus"></i> إضافة</button>
                                        <strong style="margin-left: 13rem;"><small class="text-muted">الإجمالي الكلي : </small><span class="grand-total">{{ $permit->grand_total }}</span></strong>
                                        <input type="hidden" name="grand_total" value="{{ $permit->grand_total }}">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // التحقق من الحفظ
            document.getElementById('submit').addEventListener('click', function (event) {
                event.preventDefault();

                if (validateForm()) {
                    showUpdateConfirmation();
                }
            });

            // منع التحويل لنفس المخزن
            function validateStoreSelection() {
                let fromStore = document.getElementById('from_store_houses_id').value;
                let toStore = document.getElementById('to_store_houses_id').value;

                if (fromStore && toStore && fromStore === toStore) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تحذير',
                        text: 'لا يمكنك التحويل لنفس المخزن!',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#d33'
                    });
                    return false;
                }
                return true;
            }

            // التحقق من صحة النموذج
            function validateForm() {
                // التحقق من الحقول الأساسية
                const requiredFields = [
                    { field: 'permission_date', message: 'يرجى إدخال التاريخ' },
                    { field: 'from_store_houses_id', message: 'يرجى اختيار المخزن المصدر' },
                    { field: 'to_store_houses_id', message: 'يرجى اختيار المخزن المقصد' },
                    { field: 'number', message: 'يرجى إدخال رقم الإذن' }
                ];

                for (let field of requiredFields) {
                    const element = document.querySelector(`[name="${field.field}"]`);
                    if (!element.value.trim()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: field.message,
                            confirmButtonText: 'حسناً'
                        });
                        return false;
                    }
                }

                // التحقق من المخازن
                if (!validateStoreSelection()) {
                    return false;
                }

                // التحقق من وجود بنود
                const tableBody = document.getElementById('itemsTable').querySelector('tbody');
                if (tableBody.children.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'يجب إضافة بند واحد على الأقل',
                        confirmButtonText: 'حسناً'
                    });
                    return false;
                }

                // التحقق من بيانات البنود
                const rows = tableBody.querySelectorAll('tr');
                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i];
                    const productSelect = row.querySelector('.product-select');
                    const quantityInput = row.querySelector('.quantity');

                    if (!productSelect.value) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: `يرجى اختيار البند في الصف رقم ${i + 1}`,
                            confirmButtonText: 'حسناً'
                        });
                        return false;
                    }

                    if (!quantityInput.value || quantityInput.value <= 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: `يرجى إدخال كمية صحيحة في الصف رقم ${i + 1}`,
                            confirmButtonText: 'حسناً'
                        });
                        return false;
                    }
                }

                return true;
            }

            // عرض رسالة تأكيد التحديث
            function showUpdateConfirmation() {
                Swal.fire({
                    title: 'تأكيد التحديث',
                    text: 'هل أنت متأكد من تحديث إذن التحويل؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، حدث',
                    cancelButtonText: 'إلغاء',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            setTimeout(() => {
                                resolve();
                            }, 1000);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('products_form').submit();
                    }
                });
            }

            // إضافة مستمعين للمخازن
            document.getElementById('from_store_houses_id').addEventListener('change', validateStoreSelection);
            document.getElementById('to_store_houses_id').addEventListener('change', validateStoreSelection);

            function validateStoreSelection() {
                let fromStore = document.getElementById('from_store_houses_id').value;
                let toStore = document.getElementById('to_store_houses_id').value;

                if (fromStore && toStore && fromStore === toStore) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تحذير',
                        text: 'لا يمكنك التحويل لنفس المخزن!',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#d33'
                    });
                    document.getElementById('to_store_houses_id').value = '';
                    return false;
                }
                return true;
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const storeSelect = document.getElementById('from_store_houses_id');
            let selectedStoreId = storeSelect.value;
            let selectedPermissionType = 3;

            const itemsTable = document.getElementById('itemsTable').querySelector('tbody');
            const addRowButton = document.getElementById('addRow');

            // حفظ المخزن المحدد عند تغييره
            storeSelect.addEventListener('change', function () {
                selectedStoreId = this.value;
                updateItemsStock();
            });

            // تحديث بيانات البنود عند تغيير المخزن
            function updateItemsStock() {
                const rows = itemsTable.querySelectorAll('tr');
                rows.forEach(row => {
                    const productSelect = row.querySelector('.product-select');
                    const productId = productSelect ? productSelect.value : null;

                    if (productId) {
                        fetch(`/get-product-stock/${selectedStoreId}/${productId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.stock !== null) {
                                    const stockBefore = data.stock;
                                    updateStockValues(row, stockBefore);
                                } else {
                                    updateStockValues(row, 0);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                updateStockValues(row, 0);
                            });
                    }
                });
            }

            // دالة لتحديث المخزون بعد بناءً على الكمية ونوع الإذن
            function updateStockAfter(row, stockBefore, quantity) {
                let stockAfter = stockBefore - quantity; // للتحويل

                row.querySelector('.stock-before').textContent = stockBefore;
                row.querySelector('input[name="stock_before[]"]').value = stockBefore;

                row.querySelector('.stock-after').textContent = stockAfter;
                row.querySelector('input[name="stock_after[]"]').value = stockAfter;
            }

            // تحديث المخزون بناءً على المنتج المختار
            itemsTable.addEventListener('change', function (e) {
                if (e.target.classList.contains('product-select')) {
                    if (!selectedStoreId) {
                        Swal.fire({
                            icon: 'warning',
                            text: 'يرجى اختيار المخزن أولاً قبل اختيار البند.',
                            confirmButtonText: 'حسنًا'
                        });
                        e.target.value = "";
                        return;
                    }

                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const productId = selectedOption.value;
                    const unitPrice = selectedOption.getAttribute('data-price') || 0;
                    const row = e.target.closest('tr');

                    row.querySelector('.unit-price').value = unitPrice;

                    if (productId) {
                        fetch(`/get-product-stock/${selectedStoreId}/${productId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.stock !== null) {
                                    updateStockValues(row, data.stock);
                                } else {
                                    updateStockValues(row, 0);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                updateStockValues(row, 0);
                            });
                    }
                }
            });

            // تحديث المخزون عند تغيير الكمية
            itemsTable.addEventListener('input', function (e) {
                if (e.target.classList.contains('quantity')) {
                    const row = e.target.closest('tr');
                    const stockBefore = parseInt(row.querySelector('.stock-before').textContent, 10) || 0;
                    const quantity = parseInt(e.target.value, 10) || 0;

                    updateStockAfter(row, stockBefore, quantity);
                    calculateTotal(row);
                }
            });

            // تحديث القيم المخفية مع المخزون
            function updateStockValues(row, stockBefore) {
                const quantity = parseInt(row.querySelector('.quantity').value, 10) || 0;
                updateStockAfter(row, stockBefore, quantity);
                calculateTotal(row);
            }

            // إضافة صف جديد
            addRowButton.addEventListener('click', function (e) {
                e.preventDefault();

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <select name="product_id[]" class="form-control select2 product-select" required>
                            <option value="" disabled selected>-- اختر البند --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="unit_price[]" class="form-control unit-price" readonly></td>
                    <td><input type="number" name="quantity[]" class="form-control quantity" value="1" min="1" required></td>
                    <td class="stock-before">0</td>
                    <input type="hidden" name="stock_before[]" value="0">
                    <td class="stock-after">0</td>
                    <input type="hidden" name="stock_after[]" value="0">
                    <td><input type="number" name="total[]" class="form-control total" readonly></td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-minus"></i></button>
                    </td>
                `;

                itemsTable.appendChild(newRow);
                attachRowEvents(newRow);
            });

            // حذف الصف
            itemsTable.addEventListener('click', function (e) {
                if (e.target.classList.contains('removeRow')) {
                    const row = e.target.closest('tr');
                    if (itemsTable.rows.length > 1) {
                        Swal.fire({
                            title: 'تأكيد الحذف',
                            text: 'هل أنت متأكد من حذف هذا البند؟',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'نعم، احذف',
                            cancelButtonText: 'إلغاء'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                row.remove();
                                updateGrandTotal();
                                Swal.fire(
                                    'تم الحذف!',
                                    'تم حذف البند بنجاح.',
                                    'success'
                                );
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            text: 'لا يمكنك حذف جميع الصفوف!',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: '#d33'
                        });
                    }
                }
            });

            // حساب الإجمالي للصف
            function calculateTotal(row) {
                const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const total = unitPrice * quantity;
                row.querySelector('.total').value = total.toFixed(2);
                updateGrandTotal();
            }

            // تحديث الإجمالي الكلي
            function updateGrandTotal() {
                let grandTotal = 0;
                document.querySelectorAll('.total').forEach(totalInput => {
                    grandTotal += parseFloat(totalInput.value) || 0;
                });
                document.querySelector('.grand-total').textContent = grandTotal.toFixed(2);
                document.querySelector('input[name="grand_total"]').value = grandTotal.toFixed(2);
            }

            function attachRowEvents(row) {
                const productSelect = row.querySelector('.product-select');
                const quantityInput = row.querySelector('.quantity');

                if (productSelect) {
                    productSelect.addEventListener('change', function () {
                        const selectedOption = productSelect.options[productSelect.selectedIndex];
                        const unitPrice = selectedOption.getAttribute('data-price');
                        row.querySelector('.unit-price').value = unitPrice;
                        calculateTotal(row);
                    });
                }

                if (quantityInput) {
                    quantityInput.addEventListener('input', function () {
                        calculateTotal(row);
                    });
                }
            }

            // ربط الأحداث بالصفوف الموجودة
            const existingRows = itemsTable.querySelectorAll('tr');
            existingRows.forEach(row => {
                attachRowEvents(row);
            });

            // حساب الإجمالي الكلي الأولي
            updateGrandTotal();
        });
    </script>

@endsection