@extends('master')

@section('title')
    اضافة طلب شراء
@stop

@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اضافة طلب شراء</h2>
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
        <form class="form" action="{{ route('OrdersPurchases.store') }}" method="post" enctype="multipart/form-data"
            id="purchaseOrderForm">
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
                                    required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group">
                                <label for="code" class="form-label">الكود</label>
                                <input type="text" id="code" class="form-control" placeholder="الكود" name="code"
                                    value="{{ $code }}">
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group">
                                <label for="order_date" class="form-label">تاريخ الطلب <span
                                        class="text-danger">*</span></label>
                                <input type="date" id="order_date" class="form-control" name="order_date"
                                    value="{{ old('order_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group">
                                <label for="due_date" class="form-label">تاريخ الاستحقاق</label>
                                <input type="date" id="due_date" class="form-control" name="due_date">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">المنتجات</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="productsTable">
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
                                <tr id="templateRow" style="display: none;" class="product-row">
                                    <td class="text-center align-middle">
                                        <span class="badge bg-secondary">1</span>
                                    </td>
                                    <td>
                                        <select class="form-control item-select" name="items[0][product_id]" required>
                                            <option value="">اختر البند</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control amount-input" placeholder="الكمية"
                                            name="items[0][quantity]" min="1" required>
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

                    <div class="d-flex justify-content-start mt-3">
                        <button type="button" class="btn btn-success" id="addRowBtn">
                            <i class="fas fa-plus me-1"></i> إضافة صف
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notes and Attachments Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Notes Section -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="notes" class="form-label">الملاحظات</label>
                                <textarea class="form-control" name="notes" id="notes" rows="4" placeholder="اكتب ملاحظاتك هنا..."></textarea>
                            </div>
                        </div>

                        <!-- Attachments Section -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="attachments" class="form-label">المرفقات</label>
                                <input type="file" name="attachments" id="attachments" class="d-none"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
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

    <style>
        .upload-area:hover {
            background-color: #f8f9fa !important;
            border-color: #007bff !important;
        }

        .product-row {
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

    <script>
        // تأكد من تحميل SweetAlert2
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 is not loaded!');
        } else {
            console.log('SweetAlert2 loaded successfully');
        }

        document.addEventListener('DOMContentLoaded', function() {
            let rowIndex = 0;

            // Test if SweetAlert2 is working
            console.log('DOM loaded, SweetAlert2:', typeof Swal);

            // Add new row function
            function addNewRow() {
                const templateRow = document.getElementById('templateRow');
                const newRow = templateRow.cloneNode(true);

                // Update the row
                newRow.id = `row_${rowIndex}`;
                newRow.style.display = '';

                // Update name attributes with current index
                const inputs = newRow.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace('INDEX', rowIndex);
                    }
                });

                // Add to table
                document.getElementById('tableBody').appendChild(newRow);

                // Add remove functionality
                const removeBtn = newRow.querySelector('.remove-row');
                removeBtn.addEventListener('click', function() {
                    const visibleRows = document.querySelectorAll(
                        '#tableBody .product-row:not(#templateRow):not([style*="display: none"])');

                    if (visibleRows.length <= 1) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'تحذير!',
                                text: 'لا يمكن حذف آخر صف. يجب أن يكون هناك منتج واحد على الأقل.',
                                icon: 'warning',
                                confirmButtonText: 'موافق'
                            });
                        } else {
                            alert('لا يمكن حذف آخر صف. يجب أن يكون هناك منتج واحد على الأقل.');
                        }
                        return;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'هل أنت متأكد؟',
                            text: "سيتم حذف هذا الصف نهائياً!",
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
                                Swal.fire({
                                    title: 'تم الحذف!',
                                    text: 'تم حذف الصف بنجاح.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    } else {
                        if (confirm('هل أنت متأكد من حذف هذا الصف؟')) {
                            newRow.remove();
                            updateRowNumbers();
                        }
                    }
                });

                rowIndex++;
                updateRowNumbers();
            }

            // Update row numbers
            function updateRowNumbers() {
                const rows = document.querySelectorAll('#tableBody .product-row:not(#templateRow)');
                rows.forEach((row, index) => {
                    if (row.style.display !== 'none') {
                        const badge = row.querySelector('.badge');
                        if (badge) {
                            badge.textContent = index + 1;
                        }
                    }
                });
            }

            // Add row button event
            document.getElementById('addRowBtn').addEventListener('click', function() {
                addNewRow();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'تم الإضافة!',
                        text: 'تم إضافة صف جديد بنجاح.',
                        icon: 'success',
                        timer: 1000,
                        showConfirmButton: false
                    });
                }
            });

            // File input change event
            document.getElementById('attachments').addEventListener('change', function(e) {
                const selectedFile = document.getElementById('selectedFile');
                if (e.target.files.length > 0) {
                    selectedFile.textContent = `تم اختيار: ${e.target.files[0].name}`;
                    selectedFile.style.display = 'block';

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'تم اختيار الملف!',
                            text: `الملف: ${e.target.files[0].name}`,
                            icon: 'info',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                } else {
                    selectedFile.style.display = 'none';
                }
            });

            // Submit button click handler
            document.getElementById('submitBtn').addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Submit button clicked'); // للتأكد من عمل الزر

                // Validate form
                if (!validateForm()) {
                    return;
                }

                // Show confirmation
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'تأكيد الحفظ',
                        text: "هل أنت متأكد من حفظ طلب الشراء؟",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'نعم، احفظ!',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
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

                            // Submit form directly
                            setTimeout(() => {
                                document.getElementById('purchaseOrderForm').submit();
                            }, 800);
                        }
                    });
                } else {
                    // Fallback if SweetAlert2 is not available
                    if (confirm('هل أنت متأكد من حفظ طلب الشراء؟')) {
                        document.getElementById('purchaseOrderForm').submit();
                    }
                }
            });

            // Backup form submission handler
            document.getElementById('purchaseOrderForm').addEventListener('submit', function(e) {
                // Only prevent if submitted via button click
                if (e.submitter && e.submitter.id === 'submitBtn') {
                    e.preventDefault();
                    return;
                }
            });

            // Validation function
            function validateForm() {
                // Check if at least one product row exists
                const visibleRows = document.querySelectorAll(
                    '#tableBody .product-row:not(#templateRow):not([style*="display: none"])');

                if (visibleRows.length === 0) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'خطأ!',
                            text: 'يجب إضافة منتج واحد على الأقل.',
                            icon: 'error',
                            confirmButtonText: 'موافق'
                        });
                    } else {
                        alert('يجب إضافة منتج واحد على الأقل.');
                    }
                    return false;
                }

                // Check required main form fields
                const title = document.getElementById('title').value.trim();
                const orderDate = document.getElementById('order_date').value;

                if (!title) {
                    document.getElementById('title').focus();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'خطأ!',
                            text: 'يرجى إدخال مسمى الطلب.',
                            icon: 'error',
                            confirmButtonText: 'موافق'
                        });
                    } else {
                        alert('يرجى إدخال مسمى الطلب.');
                    }
                    return false;
                }

                if (!orderDate) {
                    document.getElementById('order_date').focus();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'خطأ!',
                            text: 'يرجى إدخال تاريخ الطلب.',
                            icon: 'error',
                            confirmButtonText: 'موافق'
                        });
                    } else {
                        alert('يرجى إدخال تاريخ الطلب.');
                    }
                    return false;
                }

                // Check product rows
                for (let i = 0; i < visibleRows.length; i++) {
                    const row = visibleRows[i];
                    const productSelect = row.querySelector('select');
                    const quantityInput = row.querySelector('input[type="number"]');

                    if (!productSelect.value) {
                        productSelect.focus();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'خطأ!',
                                text: `يرجى اختيار البند في الصف رقم ${i + 1}.`,
                                icon: 'error',
                                confirmButtonText: 'موافق'
                            });
                        } else {
                            alert(`يرجى اختيار البند في الصف رقم ${i + 1}.`);
                        }
                        return false;
                    }

                    if (!quantityInput.value || quantityInput.value <= 0) {
                        quantityInput.focus();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'خطأ!',
                                text: `يرجى إدخال كمية صحيحة في الصف رقم ${i + 1}.`,
                                icon: 'error',
                                confirmButtonText: 'موافق'
                            });
                        } else {
                            alert(`يرجى إدخال كمية صحيحة في الصف رقم ${i + 1}.`);
                        }
                        return false;
                    }
                }

                return true;
            }

            // Cancel button functionality
            document.getElementById('cancelBtn').addEventListener('click', function(e) {
                e.preventDefault();

                if (typeof Swal !== 'undefined') {
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
                } else {
                    if (confirm('هل أنت متأكد من إلغاء جميع البيانات؟')) {
                        resetForm();
                    }
                }
            });

            function resetForm() {
                // Reset form
                document.getElementById('purchaseOrderForm').reset();

                // Clear table rows except template
                const rows = document.querySelectorAll('#tableBody .product-row:not(#templateRow)');
                rows.forEach(row => row.remove());

                // Reset row index and add first row
                rowIndex = 0;
                addNewRow();

                // Hide selected file display
                document.getElementById('selectedFile').style.display = 'none';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'تم الإلغاء!',
                        text: 'تم إلغاء العملية وحذف البيانات.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }

            // Initialize with first row
            addNewRow();
        });
    </script>

@endsection
