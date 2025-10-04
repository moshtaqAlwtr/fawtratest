@extends('master')

@section('title')
    اضافة عرض سعر شراء
@stop

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .swal2-popup {
            font-family: 'Tajawal', sans-serif;
            direction: rtl;
        }
        .swal2-title {
            font-weight: 600;
        }
        .swal2-html-container {
            font-size: 1.1em;
        }
        .upload-area:hover {
            background-color: #f8f9fa !important;
            border-color: #007bff !important;
        }
        .item-row {
            background-color: #fff;
        }
        .table th {
            font-weight: 600;
            color: #495057;
        }
        @media (max-width: 768px) {
            .d-flex.gap-2 {
                justify-content: center;
            }
            .table-responsive {
                font-size: 14px;
            }
            .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
        }
        @media (max-width: 576px) {
            .card {
                margin: 0 10px !important;
                max-width: calc(100% - 20px) !important;
            }
            .table-responsive {
                font-size: 12px;
            }
            .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }
        }
    </style>
@endsection

@section('content')


    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اضافة عرض سعر شراء</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <form class="form" action="{{ route('Quotations.store') }}" method="post" enctype="multipart/form-data" id="quotationForm">
            @csrf
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

            <!-- Action Buttons Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <label class="mb-0">الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-danger" id="cancelBtn">
                                <i class="fa fa-ban me-1"></i>الغاء
                            </button>
                            <button type="submit" class="btn btn-outline-primary" id="submitBtn">
                                <i class="fa fa-save me-1"></i>حفظ
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group">
                                <label for="title" class="form-label">مسمى <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" id="title" placeholder="مسمى"
                                       value="{{ $purchaseOrder ? $purchaseOrder->title : '' }}" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group">
                                <label for="code" class="form-label">الكود</label>
                                <input type="text" id="code" class="form-control" placeholder="الكود" name="code"
                                    value="{{ 'QUO-' . str_pad((\App\Models\PurchaseQuotation::max('id') ?? 0) + 1, 5, '0', STR_PAD_LEFT) }}">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group">
                                <label for="order_date" class="form-label">تاريخ الطلب <span class="text-danger">*</span></label>
                                <input type="date" id="order_date" class="form-control" name="order_date"
                                    value="{{ $purchaseOrder ? $purchaseOrder->order_date : old('order_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group">
                                <label for="due_date" class="form-label">تاريخ الاستحقاق</label>
                                <input type="date" id="due_date" class="form-control" name="due_date"
                                       value="{{ $purchaseOrder ? $purchaseOrder->due_date : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-12">
                            <div class="form-group">
                                <label for="supplier_id" class="form-label">اختر الموردين <span class="text-danger">*</span></label>
                                <select id="supplier_id" class="form-control select2" name="supplier_id[]" multiple="multiple" required>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->trade_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">البنود</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th>البند <span class="text-danger">*</span></th>
                                    <th style="width: 150px;">الكمية <span class="text-danger">*</span></th>
                                    <th style="width: 60px;" class="text-center">العمليات</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Template Row (Hidden) -->
                                <tr id="templateRow" style="display: none;" class="item-row">
                                    <td class="text-center align-middle">
                                        <span class="badge bg-secondary">1</span>
                                    </td>
                                    <td>
                                        <select class="form-control item-select" name="items[INDEX][product_id]" required>
                                            <option value="">اختر البند</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control quantity-input" placeholder="الكمية"
                                            name="items[INDEX][quantity]" min="1" required>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-danger btn-sm remove-row" title="حذف الصف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="order_id" value="{{ request('order_id') }}">

                    <div class="d-flex justify-content-start mt-3">
                        <button type="button" class="btn btn-success" id="addRowBtn">
                            <i class="fas fa-plus me-1"></i> إضافة بند
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notes and Attachments Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="notes" class="form-label">الملاحظات</label>
                                <textarea class="form-control" name="notes" id="notes" rows="4" placeholder="اكتب ملاحظاتك هنا...">{{ $purchaseOrder ? $purchaseOrder->notes : '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="attachments" class="form-label">المرفقات</label>
                                <input type="file" name="attachments" id="attachments" class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <div class="upload-area border-2 border-dashed rounded p-4 text-center position-relative bg-light"
                                    onclick="document.getElementById('attachments').click()" style="cursor: pointer;">
                                    <div class="d-flex flex-column align-items-center gap-2">
                                        <i class="fas fa-cloud-upload-alt text-primary fs-2"></i>
                                        <div class="d-flex flex-wrap justify-content-center gap-1">
                                            <span class="text-primary">اضغط هنا</span>
                                            <span>أو</span>
                                            <span class="text-primary">اختر من جهازك</span>
                                        </div>
                                        <small class="text-muted">PDF, DOC, DOCX, JPG, PNG</small>
                                    </div>
                                    <div id="selectedFile" class="mt-2 text-success" style="display: none;"></div>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        $('.select2').select2({
            width: '100%',
            dir: 'rtl',
            placeholder: 'اختر من القائمة',
            allowClear: true
        });

        let rowIndex = 0;

        // إذا كان هناك بيانات من طلب الشراء، قم بتحميلها
        @if($purchaseOrder && $orderItems->count() > 0)
            // تحميل البيانات من طلب الشراء
            @foreach($orderItems as $index => $item)
                addNewRowWithData({{ $item->product_id }}, {{ $item->quantity }});
            @endforeach
        @else
            // إضافة صف فارغ إذا لم تكن هناك بيانات
            addNewRow();
        @endif

        // Add new row function with data
        function addNewRowWithData(productId = null, quantity = null) {
            const templateRow = document.getElementById('templateRow');
            const newRow = templateRow.cloneNode(true);

            newRow.id = `row_${rowIndex}`;
            newRow.style.display = '';

            // Update name attributes with correct index
            const inputs = newRow.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace('INDEX', rowIndex);
                }

                // تعيين القيم إذا تم تمريرها
                if (input.classList.contains('item-select') && productId) {
                    input.value = productId;
                }
                if (input.classList.contains('quantity-input') && quantity) {
                    input.value = quantity;
                }

                if (!productId && !quantity) {
                    input.value = ''; // Clear values for new empty rows
                }
            });

            document.getElementById('tableBody').appendChild(newRow);

            // Add remove functionality
            const removeBtn = newRow.querySelector('.remove-row');
            removeBtn.addEventListener('click', function() {
                const allRows = document.querySelectorAll('#tableBody .item-row:not(#templateRow)');
                const visibleRows = Array.from(allRows).filter(row => row.style.display !== 'none');

                if (visibleRows.length <= 1) {
                    Swal.fire({
                        title: 'تحذير!',
                        text: 'لا يمكن حذف آخر صف. يجب أن يكون هناك بند واحد على الأقل.',
                        icon: 'warning',
                        confirmButtonText: 'موافق'
                    });
                    return;
                }

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "سيتم حذف هذا البند نهائياً!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، احذف!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        newRow.remove();
                        updateRowNumbers();
                        updateRowIndexes();
                        Swal.fire({
                            title: 'تم الحذف!',
                            text: 'تم حذف البند بنجاح.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });

            rowIndex++;
            updateRowNumbers();
        }

        // Add new row function (empty)
        function addNewRow() {
            addNewRowWithData(null, null);
        }

        // Update row numbers
        function updateRowNumbers() {
            const allRows = document.querySelectorAll('#tableBody .item-row:not(#templateRow)');
            const visibleRows = Array.from(allRows).filter(row => row.style.display !== 'none');

            visibleRows.forEach((row, index) => {
                const badge = row.querySelector('.badge');
                if (badge) {
                    badge.textContent = index + 1;
                }
            });
        }

        // إضافة دالة جديدة لتحديث فهارس الصفوف
        function updateRowIndexes() {
            const allRows = document.querySelectorAll('#tableBody .item-row:not(#templateRow)');
            const visibleRows = Array.from(allRows).filter(row => row.style.display !== 'none');

            visibleRows.forEach((row, index) => {
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.name) {
                        // تحديث الفهرس في اسم الحقل
                        input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                    }
                });
            });
        }

        // Add row button event
        document.getElementById('addRowBtn').addEventListener('click', function() {
            addNewRow();
            Swal.fire({
                title: 'تم الإضافة!',
                text: 'تم إضافة بند جديد بنجاح.',
                icon: 'success',
                timer: 1000,
                showConfirmButton: false
            });
        });

        document.getElementById('attachments').addEventListener('change', function (e) {
            const selectedFile = document.getElementById('selectedFile');
            if (e.target.files.length > 0) {
                selectedFile.textContent = `تم اختيار: ${e.target.files[0].name}`;
                selectedFile.style.display = 'block';

                Swal.fire({
                    title: 'تم اختيار الملف!',
                    text: `الملف: ${e.target.files[0].name}`,
                    icon: 'info',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                selectedFile.style.display = 'none';
            }
        });

        // زر الحفظ - مع تعطيل الصف Template
        document.getElementById('submitBtn').addEventListener('click', function (e) {
            e.preventDefault();

            if (!validateForm()) return;

            // تعطيل حقول الصف Template قبل الإرسال
            const templateRow = document.getElementById('templateRow');
            const templateInputs = templateRow.querySelectorAll('input, select');
            templateInputs.forEach(input => {
                input.setAttribute('disabled', 'disabled');
            });

            Swal.fire({
                title: 'تأكيد الحفظ',
                text: "هل أنت متأكد من حفظ عرض السعر؟",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احفظ!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'جاري الحفظ...',
                        text: 'يرجى الانتظار',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    allowSubmit = true;
                    setTimeout(() => {
                        document.getElementById('quotationForm').submit();
                    }, 800);
                } else {
                    // إعادة تفعيل الحقول إذا ألغى المستخدم
                    templateInputs.forEach(input => {
                        input.removeAttribute('disabled');
                    });
                }
            });
        });

        // منع الإرسال التلقائي
        document.getElementById('quotationForm').addEventListener('submit', function (e) {
            if (!allowSubmit) {
                e.preventDefault();
            }
        });

        // زر الإلغاء
        document.getElementById('cancelBtn').addEventListener('click', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم إلغاء جميع البيانات المدخلة!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، إلغاء!',
                cancelButtonText: 'العودة'
            }).then((result) => {
                if (result.isConfirmed) {
                    resetForm();
                }
            });
        });

        function resetForm() {
            document.getElementById('quotationForm').reset();
            $('.select2').val(null).trigger('change');

            const rows = document.querySelectorAll('#tableBody tr.item-row:not(#templateRow)');
            rows.forEach(row => row.remove());

            rowIndex = 0;
            addNewRow();

            document.getElementById('selectedFile').style.display = 'none';

            Swal.fire({
                title: 'تم الإلغاء!',
                text: 'تم إلغاء العملية وحذف البيانات.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }

        // إضافة أول صف تلقائياً
        addNewRow();
    });
    </script>
@endsection
