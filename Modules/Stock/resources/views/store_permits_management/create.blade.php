@extends('master')

@section('title')
 اضف عملية على المخزون
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اضف عملية على المخزون </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">اضافة
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

            <form class="form-horizontal" action="{{ route('store_permits_management.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
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
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa fa-save"></i>حفظ
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">الأذون المخزنية</h4>
                        </div>

                        <div class="card-body">
                            <div class="form-body row">
                                <div class="form-group col-md-2 mt-1">
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input type="checkbox" name="permission_type" id="permissionType" checked="" value="1" disabled>
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">إذن إضافة مخزن</span>
                                    </div>
                                </div>

                                <input type="hidden" name="permission_type" value="1">

                                <div class="form-group col-md-4">
                                    <label for="">التاريخ</label>
                                    <input type="datetime-local" id="datetime" class="form-control" name="permission_date">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">المحزن</label>
                                    <select class="form-control" name="store_houses_id">
                                        @foreach ($storeHouses as $storeHouse)
                                            <option value="{{ $storeHouse->id }}" {{ old('store_houses_id') == $storeHouse->id ? 'selected' : '' }}>{{ $storeHouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">الحساب الفرعي</label>
                                    <select class="form-control" id="basicSelect" name="sub_account">
                                        <option value="1" {{ old('sub_account') == 0 ? 'selected' : '' }}>الحساب الفرعي 1</option>
                                        <option value="2" {{ old('sub_account') == 1 ? 'selected' : '' }}>الحساب الفرعي 2</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>رقم <span style="color: red">*</span></label>
                                    <input type="number" class="form-control" name="number" value="{{ $serial_number }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="feedback2">الملاحظات </label>
                                    <textarea class="form-control" rows="2" placeholder="ادخل ملاحظاتك هنا" name="details">{{ old('details') }}</textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>المرفقات</label>
                                    <input type="file" class="form-control" name="attachments" value="{{ old('attachments') }}">
                                </div>

                                <div class="form-group col-md-12">
                                    <table class="table table-striped" id="itemsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>البنود</th>
                                                <th>سعر الوحدة</th>
                                                <th>الكمية</th>
                                                <th>رصيد المخزن قبل</th>
                                                <th>رصيد المخزن بعد</th>
                                                <th>الإجمالي</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    <hr>
                                    <div class="d-flex justify-content-between mt-2">
                                        <button type="button" class="btn btn-outline-success btn-sm" id="addRow"><i class="fa fa-plus"></i> إضافة</button>
                                        <strong style="margin-left: 13rem;"><small class="text-muted">الإجمالي الكلي : </small><span class="grand-total">0.00</span></strong>
                                        <input type="hidden" name="grand_total" value="">
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
            const input = document.getElementById('datetime');
            const now = new Date();
            now.setHours(now.getUTCHours() + 5);
            const formattedDateTime = now.toISOString().slice(0, 16);
            input.value = formattedDateTime;
        });
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const storeSelect = document.querySelector('select[name="store_houses_id"]'); // قائمة المخازن
            let selectedStoreId = storeSelect.value; // تعيين القيمة الافتراضية للمخزن المحدد
            let selectedPermissionType = 1; // نوع الإذن الافتراضي
            const itemsTable = document.getElementById('itemsTable').querySelector('tbody'); // جدول البنود

            // حفظ المخزن المحدد عند تغييره
            storeSelect.addEventListener('change', function () {
                selectedStoreId = this.value;
                updateItemsStock();
            });

            // تحديث البنود بناءً على المخزن الجديد
            function updateItemsStock() {
                const rows = itemsTable.querySelectorAll('tr'); // جميع الصفوف في الجدول
                rows.forEach(row => {
                    const productSelect = row.querySelector('.product-select'); // قائمة البنود
                    const productId = productSelect ? productSelect.value : null; // معرف البند

                    if (productId) {
                        fetch(`/get-product-stock/${selectedStoreId}/${productId}`)
                            .then(response => response.json())
                            .then(data => {
                                const stockBefore = data.stock !== null ? data.stock : 0;
                                row.querySelector('.stock-before').textContent = stockBefore;
                                row.querySelector('.stock-before-input').value = stockBefore; // تحديث القيمة المخفية

                                const quantity = parseInt(row.querySelector('.quantity').value, 10) || 0;
                                updateStockAfter(row, stockBefore, quantity);
                            });
                    }
                });
            }

            // عند اختيار بند، يتم التحقق من المخزون في المخزن المحدد
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
                    row.querySelector('.quantity').value = 1; // تعيين الكمية الافتراضية

                    fetch(`/get-product-stock/${selectedStoreId}/${productId}`)
                        .then(response => response.json())
                        .then(data => {
                            const stockBefore = data.stock !== null ? data.stock : 0;
                            row.querySelector('.stock-before').textContent = stockBefore;
                            row.querySelector('.stock-before-input').value = stockBefore; // تحديث القيمة المخفية

                            const quantity = parseInt(row.querySelector('.quantity').value, 10) || 0;
                            updateStockAfter(row, stockBefore, quantity);
                        });
                }
            });

            // تحديث المخزون بعد عند تغيير الكمية
            itemsTable.addEventListener('input', function (e) {
                if (e.target.classList.contains('quantity')) {
                    const row = e.target.closest('tr');
                    const stockBefore = parseInt(row.querySelector('.stock-before').textContent, 10) || 0;
                    const quantity = parseInt(e.target.value, 10) || 0;

                    updateStockAfter(row, stockBefore, quantity);
                }
            });

            // دالة لحساب "رصيد المخزون بعد" وتحديث القيم المخفية
            function updateStockAfter(row, stockBefore, quantity) {
                let stockAfter = stockBefore;

                if (selectedPermissionType === 1) {
                    stockAfter = stockBefore + quantity; // في حالة الإضافة: نزيد الكمية
                } else if (selectedPermissionType === 2 || selectedPermissionType === 3) {
                    stockAfter = stockBefore - quantity; // في حالة الصرف أو التحويل: ننقص الكمية
                }

                row.querySelector('.stock-after').textContent = stockAfter;
                row.querySelector('.stock-after-input').value = stockAfter; // تحديث القيمة المخفية
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemsTable = document.getElementById('itemsTable').querySelector('tbody');
            const addRowButton = document.getElementById('addRow');

            // Function to calculate total for a row
            function calculateTotal(row) {
                const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const total = unitPrice * quantity;
                row.querySelector('.total').value = total.toFixed(2);
                updateGrandTotal();
            }

            // Function to update grand total
            function updateGrandTotal() {
                let grandTotal = 0;
                document.querySelectorAll('.total').forEach(totalInput => {
                    grandTotal += parseFloat(totalInput.value) || 0;
                });
                document.querySelector('.grand-total').textContent = grandTotal.toFixed(2);
                document.querySelector('input[name="grand_total"]').value = grandTotal.toFixed(2);
            }

            // Function to attach event listeners to a row
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

            // Attach events to the first row
            const firstRow = document.getElementById('firstRow');
            if (firstRow) {
                attachRowEvents(firstRow);
            }

            // Add Row
            addRowButton.addEventListener('click', function (e) {
                e.preventDefault();

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <tr>
                        <td>
                            <select name="product_id[]" class="form-control select2 product-select">
                                <option value="" disabled selected>-- اختر البند --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="unit_price[]" class="form-control unit-price" readonly></td>
                        <td><input type="number" name="quantity[]" class="form-control quantity" value="1" min="1"></td>
                        <td class="stock-before">0</td>
                        <input type="hidden" name="stock_before[]" class="stock-before-input" value="0">
                        <td class="stock-after">0</td>
                        <input type="hidden" name="stock_after[]" class="stock-after-input" value="0">
                        <td><input type="number" name="total[]" class="form-control total" readonly></td>
                        <td>
                            <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-minus"></i></button>
                        </td>
                    </tr>
                `;

                itemsTable.appendChild(newRow);
                attachRowEvents(newRow);
            });

            itemsTable.addEventListener('click', function (e) {
                if (e.target.classList.contains('removeRow')) {
                    const row = e.target.closest('tr');
                    if (itemsTable.rows.length > 1) {
                        row.remove();
                        updateGrandTotal();
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

        });
    </script>


@endsection
