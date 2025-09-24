@extends('master')

@section('title')
التكاليف غير المباشرة
@stop

@section('css')
    <style>
        .section-header {
            cursor: pointer;
            font-weight: bold;
        }
        .restriction-info {
            font-size: 0.85em;
            color: #666;
        }
        .date-notice {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            color: #1976d2;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .loading-restrictions {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">التكاليف غير المباشرة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">اضافة</li>
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

            <form class="form-horizontal" id="indirectCostForm" action="{{ route('manufacturing.indirectcosts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                            </div>

                            <div>
                                <a href="{{ route('manufacturing.indirectcosts.index') }}" class="btn btn-outline-danger">
                                    <i class="fa fa-ban"></i>الغاء
                                </a>
                                <button type="submit" class="btn btn-outline-primary" id="saveButton">
                                    <i class="fa fa-save"></i>حفظ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">معلومات التكاليف غير المباشرة</h4>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="">الحساب <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="basicSelect" name="account_id" required>
                                        <option value="" disabled selected>-- اختر الحساب --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="">التاريخ من<span style="color: red">*</span></label>
                                    <input type="date" class="form-control" id="from_date" name="from_date" value="{{ old('from_date') }}" required>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="">التاريخ الى<span style="color: red">*</span></label>
                                    <input type="date" class="form-control" id="to_date" name="to_date" value="{{ old('to_date') }}" required>
                                </div>

                                <div class="form-group col-md-12">
                                    <p>نوع التوزيع <span style="color: red">*</span> </p>
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-inline-block mr-2">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input vs-radio-lg"
                                                           name="based_on" id="customRadio1" value="1"
                                                           {{ old('based_on', '1') == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="customRadio1">بناءً على الكمية</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block mr-2">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input vs-radio-lg"
                                                           name="based_on" id="customRadio2" value="2"
                                                           {{ old('based_on') == '2' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="customRadio2">بناءً على التكلفة</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                    </ul>
                                </div>

                                <div class="form-group col-md-12 mt-1">
                                    <p onclick="toggleSection('expenses')" class="d-flex justify-content-between section-header" style="background: #DBDEE2; width: 100%;">
                                        <span class="p-1 font-weight-bold"><i class="fa fa-money"></i> القيود اليومية (<span id="rowExpensesCount">1</span>)</span>
                                        <i class="feather icon-plus-circle p-1"></i>
                                    </p>

                                    <!-- رسالة تنبيه للتواريخ -->
                                    <div id="dateNotice" class="date-notice" style="display: none;">
                                        <i class="fa fa-info-circle"></i>
                                        يرجى اختيار التاريخ من والتاريخ إلى أولاً لعرض القيود المتاحة في هذه الفترة.
                                    </div>

                                    <div id="expenses" style="display: block">
                                        <table class="table table-striped" id="itemsTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>القيد المحاسبي</th>
                                                    <th>المجموع</th>
                                                    <th style="width: 10%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select class="form-control restriction-select" name="restriction_id[]" disabled>
                                                            <option value="" disabled selected>-- اختر التواريخ أولاً --</option>
                                                        </select>
                                                        <div class="restriction-info mt-1" style="display: none;">
                                                            <small class="text-muted">
                                                                <strong>الوصف:</strong> <span class="restriction-description"></span><br>
                                                                <strong>التاريخ:</strong> <span class="restriction-date"></span>
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control expenses-total"
                                                               name="restriction_total[]" value="0" min="0">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="ExpensesAddRow">
                                                <i class="fa fa-plus"></i> إضافة قيد
                                            </button>
                                            <strong style="margin-left: 13rem;">
                                                <small class="text-muted">الإجمالي الكلي : </small>
                                                <span class="expenses-grand-total">0.00</span>
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <p onclick="toggleSection('manufacturing')" class="d-flex justify-content-between section-header" style="background: #DBDEE2; width: 100%;">
                                        <span class="p-1 font-weight-bold"><i class="feather icon-package"></i> أوامر التصنيع (<span id="rowManufacturingCount">1</span>)</span>
                                        <i class="feather icon-plus-circle p-1"></i>
                                    </p>
                                    <div id="manufacturing" style="display: block">
                                        <table class="table table-striped" id="manufacturingTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>طلب التصنيع</th>
                                                    <th>المبلغ</th>
                                                    <th style="width: 10%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select name="manufacturing_order_id[]" class="form-control select2">
                                                            <option value="">-- اختر طلب التصنيع --</option>
                                                            @foreach ($manufacturing_orders as $manufacturing_order)
                                                                <option value="{{ $manufacturing_order->id }}">
                                                                    {{ $manufacturing_order->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" name="manufacturing_price[]"
                                                               class="form-control manufacturing-price" value="0" min="0">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="ManufacturingAddRow">
                                                <i class="fa fa-plus"></i> إضافة أمر تصنيع
                                            </button>
                                            <strong style="margin-left: 13rem;">
                                                <small class="text-muted">الإجمالي الكلي : </small>
                                                <span class="manufacturing-grand-total">0.00</span>
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" step="0.01" id="total" name="total" class="form-control" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

    <script>
        let availableRestrictions = [];

        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section.style.display === "none") {
                section.style.display = "block";
            } else {
                section.style.display = "none";
            }
        }

        // جلب القيود بناءً على التواريخ
        function fetchRestrictions() {
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            const dateNotice = document.getElementById('dateNotice');

            if (!fromDate || !toDate) {
                // إظهار رسالة التنبيه وتعطيل القيود
                dateNotice.style.display = 'block';
                disableRestrictionSelects();
                return;
            }

            if (fromDate > toDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'التاريخ من يجب أن يكون أقل من أو يساوي التاريخ إلى',
                    confirmButtonText: 'حسناً'
                });
                return;
            }

            // إخفاء رسالة التنبيه
            dateNotice.style.display = 'none';

            // إظهار مؤشر التحميل
            showLoadingInRestrictions();

            // طلب Ajax لجلب القيود
            fetch('{{ route("manufacturing.indirectcosts.getRestrictionsByDate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                   document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    from_date: fromDate,
                    to_date: toDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    availableRestrictions = data.restrictions;
                    updateRestrictionSelects();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ في جلب القيود',
                        confirmButtonText: 'حسناً'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ في الاتصال بالخادم',
                    confirmButtonText: 'حسناً'
                });
            });
        }

        function showLoadingInRestrictions() {
            const selects = document.querySelectorAll('.restriction-select');
            selects.forEach(select => {
                select.innerHTML = '<option value="">جاري التحميل...</option>';
                select.disabled = true;
            });
        }

        function disableRestrictionSelects() {
            const selects = document.querySelectorAll('.restriction-select');
            selects.forEach(select => {
                select.innerHTML = '<option value="" disabled selected>-- اختر التواريخ أولاً --</option>';
                select.disabled = true;
            });
        }

        function updateRestrictionSelects() {
            const selects = document.querySelectorAll('.restriction-select');

            selects.forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '<option value="" disabled selected>-- اختر القيد --</option>';

                availableRestrictions.forEach(restriction => {
                    const option = document.createElement('option');
                    option.value = restriction.id;
                    option.textContent = restriction.display_text;
                    option.dataset.reference = restriction.reference_number;
                    option.dataset.description = restriction.description;
                    option.dataset.date = restriction.date;

                    if (currentValue == restriction.id) {
                        option.selected = true;
                    }

                    select.appendChild(option);
                });

                select.disabled = false;
            });
        }

        // عرض تفاصيل القيد عند الاختيار
        function handleRestrictionChange(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const infoDiv = selectElement.parentNode.querySelector('.restriction-info');

            if (selectedOption.value) {
                const description = selectedOption.dataset.description || 'غير محدد';
                const date = selectedOption.dataset.date || 'غير محدد';

                infoDiv.querySelector('.restriction-description').textContent = description;
                infoDiv.querySelector('.restriction-date').textContent = date;
                infoDiv.style.display = 'block';
            } else {
                infoDiv.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const itemsTable = document.getElementById('itemsTable').querySelector('tbody');
            const ExpensesAddRowButton = document.getElementById('ExpensesAddRow');
            const manufacturingTable = document.getElementById('manufacturingTable').querySelector('tbody');
            const ManufacturingAddRowButton = document.getElementById('ManufacturingAddRow');
            const totalInput = document.getElementById('total');
            const fromDateInput = document.getElementById('from_date');
            const toDateInput = document.getElementById('to_date');
            const form = document.getElementById('indirectCostForm');

            // مراقبة تغيير التواريخ
            fromDateInput.addEventListener('change', fetchRestrictions);
            toDateInput.addEventListener('change', fetchRestrictions);

            // رسالة التأكيد عند الحفظ
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const total = parseFloat(totalInput.value) || 0;

                if (total <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يجب أن يكون المجموع الكلي أكبر من صفر',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }

                Swal.fire({
                    title: 'تأكيد الحفظ',
                    html: `
                        هل أنت متأكد من حفظ التكاليف غير المباشرة؟<br>
                        <strong>المجموع الكلي: ${total.toFixed(2)}</strong>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'نعم، احفظ',
                    cancelButtonText: 'إلغاء',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            form.submit();
                            resolve();
                        });
                    }
                });
            });

            function updateRowCount(table, countElementId) {
                const rowCount = table.querySelectorAll('tr').length;
                document.getElementById(countElementId).textContent = rowCount;
            }

            function calculateExpensesTotal() {
                let total = 0;
                itemsTable.querySelectorAll('tr').forEach(row => {
                    const priceInput = row.querySelector('.expenses-total');
                    if (priceInput) {
                        const price = parseFloat(priceInput.value) || 0;
                        total += price;
                    }
                });
                document.querySelector('.expenses-grand-total').textContent = total.toFixed(2);
                updateTotalSum();
            }

            function calculateManufacturingTotal() {
                let total = 0;
                manufacturingTable.querySelectorAll('tr').forEach(row => {
                    const priceInput = row.querySelector('.manufacturing-price');
                    if (priceInput) {
                        const price = parseFloat(priceInput.value) || 0;
                        total += price;
                    }
                });
                document.querySelector('.manufacturing-grand-total').textContent = total.toFixed(2);
                updateTotalSum();
            }

            function updateTotalSum() {
                const expensesTotal = parseFloat(document.querySelector('.expenses-grand-total').textContent) || 0;
                const manufacturingTotal = parseFloat(document.querySelector('.manufacturing-grand-total').textContent) || 0;
                const totalSum = expensesTotal + manufacturingTotal;
                totalInput.value = totalSum.toFixed(2);
            }

            function attachExpensesRowEvents(row) {
                const priceInput = row.querySelector('.expenses-total');
                const restrictionSelect = row.querySelector('.restriction-select');

                if (priceInput) {
                    priceInput.addEventListener('input', calculateExpensesTotal);
                }

                if (restrictionSelect) {
                    restrictionSelect.addEventListener('change', function() {
                        handleRestrictionChange(this);
                    });
                }
            }

            function attachManufacturingRowEvents(row) {
                const priceInput = row.querySelector('.manufacturing-price');
                if (priceInput) {
                    priceInput.addEventListener('input', calculateManufacturingTotal);
                }
            }

            function createRestrictionOptions() {
                let optionsHtml = '<option value="" disabled selected>-- اختر القيد --</option>';
                availableRestrictions.forEach(restriction => {
                    optionsHtml += `
                        <option value="${restriction.id}"
                                data-reference="${restriction.reference_number}"
                                data-description="${restriction.description}"
                                data-date="${restriction.date}">
                            ${restriction.display_text}
                        </option>
                    `;
                });
                return optionsHtml;
            }

            ExpensesAddRowButton.addEventListener('click', function (e) {
                e.preventDefault();

                if (availableRestrictions.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يجب اختيار التواريخ أولاً لعرض القيود المتاحة',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }

                const exNewRow = document.createElement('tr');
                exNewRow.innerHTML = `
                    <td>
                        <select class="form-control restriction-select" name="restriction_id[]">
                            ${createRestrictionOptions()}
                        </select>
                        <div class="restriction-info mt-1" style="display: none;">
                            <small class="text-muted">
                                <strong>الوصف:</strong> <span class="restriction-description"></span><br>
                                <strong>التاريخ:</strong> <span class="restriction-date"></span>
                            </small>
                        </div>
                    </td>
                    <td><input type="number" step="0.01" name="restriction_total[]" class="form-control expenses-total" value="0" min="0"></td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-minus"></i></button>
                    </td>
                `;

                itemsTable.appendChild(exNewRow);
                attachExpensesRowEvents(exNewRow);
                updateRowCount(itemsTable, 'rowExpensesCount');
            });

            ManufacturingAddRowButton.addEventListener('click', function (e) {
                e.preventDefault();

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <select name="manufacturing_order_id[]" class="form-control">
                            <option value="">-- اختر طلب التصنيع --</option>
                            @foreach ($manufacturing_orders as $manufacturing_order)
                                <option value="{{ $manufacturing_order->id }}">{{ $manufacturing_order->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="manufacturing_price[]" class="form-control manufacturing-price" value="0" min="0"></td>
                    <td style="width: 10%">
                        <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-minus"></i></button>
                    </td>
                `;

                manufacturingTable.appendChild(newRow);
                attachManufacturingRowEvents(newRow);
                updateRowCount(manufacturingTable, 'rowManufacturingCount');
            });

            // Event delegation for remove buttons مع إضافة رسالة تأكيد
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')) {
                    const button = e.target.classList.contains('removeRow') ? e.target : e.target.closest('.removeRow');
                    const row = button.closest('tr');
                    const table = button.closest('table');
                    const tableBody = table.querySelector('tbody');

                    if (tableBody.rows.length > 1) {
                        // رسالة تأكيد الحذف
                        Swal.fire({
                            title: 'تأكيد الحذف',
                            text: 'هل أنت متأكد من حذف هذا الصف؟',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'نعم، احذف',
                            cancelButtonText: 'إلغاء'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                row.remove();

                                if (table.id === 'itemsTable') {
                                    calculateExpensesTotal();
                                    updateRowCount(itemsTable, 'rowExpensesCount');
                                } else if (table.id === 'manufacturingTable') {
                                    calculateManufacturingTotal();
                                    updateRowCount(manufacturingTable, 'rowManufacturingCount');
                                }

                                // رسالة نجاح الحذف
                                Swal.fire({
                                    title: 'تم الحذف!',
                                    text: 'تم حذف الصف بنجاح',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'لا يمكنك حذف جميع الصفوف! يجب أن يبقى صف واحد على الأقل',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: '#d33'
                        });
                    }
                }
            });

            // Initialize existing rows
            itemsTable.querySelectorAll('tr').forEach(row => attachExpensesRowEvents(row));
            manufacturingTable.querySelectorAll('tr').forEach(row => attachManufacturingRowEvents(row));

            // Initial calculations
            calculateExpensesTotal();
            calculateManufacturingTotal();
            updateRowCount(itemsTable, 'rowExpensesCount');
            updateRowCount(manufacturingTable, 'rowManufacturingCount');

            // إظهار رسالة التنبيه في البداية
            document.getElementById('dateNotice').style.display = 'block';
        });
    </script>
@endsectionw