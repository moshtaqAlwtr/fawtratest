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
    </style>
@endsection

@section('content')
    <!-- تأكد من وجود CSRF token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
        <form class="form" action="{{ route('Quotations.store') }}" method="post" enctype="multipart/form-data"
            id="quotationForm">
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
                                    value="{{ 'QUO-' . str_pad((\App\Models\PurchaseQuotation::max('id') ?? 0) + 1, 5, '0', STR_PAD_LEFT) }}">
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
                        <div class="col-lg-8 col-md-6 col-12">
                            <div class="form-group">
                                <label for="supplier_id" class="form-label">اختر الموردين <span
                                        class="text-danger">*</span></label>
                                <select id="supplier_id" class="form-control select2" name="supplier_id[]"
                                    multiple="multiple" required>
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
                                        <select class="form-control item-select" name="items[0][product_id]" required>
                                            <option value="">اختر البند</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control quantity-input" placeholder="الكمية"
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
                        input.name = input.name.replace('[0]', `[${rowIndex}]`);
                    }
                });

                // Add to table
                document.getElementById('tableBody').appendChild(newRow);

                // Add remove functionality
                const removeBtn = newRow.querySelector('.remove-row');
                removeBtn.addEventListener('click', function() {
                    const visibleRows = document.querySelectorAll(
                        '#tableBody .item-row:not(#templateRow):not([style*="display: none"])');

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

            // Update row numbers
            function updateRowNumbers() {
                const rows = document.querySelectorAll('#tableBody .item-row:not(#templateRow)');
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
                Swal.fire({
                    title: 'تم الإضافة!',
                    text: 'تم إضافة بند جديد بنجاح.',
                    icon: 'success',
                    timer: 1000,
                    showConfirmButton: false
                });
            });

            // File input change event
            document.getElementById('attachments').addEventListener('change', function(e) {
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

            // Form submission handler
            const form = document.getElementById('quotationForm');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate form
                if (!validateForm()) {
                    return false;
                }

                // Show confirmation dialog
                Swal.fire({
                    title: 'تأكيد الحفظ',
                    html: '<div class="text-right">' +
                          '<p>هل أنت متأكد من رغبتك في حفظ عرض السعر الجديد؟</p>' +
                          '<div class="alert alert-info text-right mt-2">' +
                          '<i class="fas fa-info-circle"></i> سيتم إرسال الإشعارات للموردين المحددين' +
                          '</div></div>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-save"></i> نعم، احفظ',
                    cancelButtonText: '<i class="fas fa-times"></i> إلغاء',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-success mx-1',
                        cancelButton: 'btn btn-secondary mx-1'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading indicator
                        Swal.fire({
                            title: 'جاري الحفظ',
                            html: 'يرجى الانتظار أثناء حفظ بيانات عرض السعر...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit the form via AJAX
                        const formData = new FormData(form);
                        
                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                Swal.fire({
                                    title: 'تم بنجاح!',
                                    text: 'تم حفظ عرض السعر بنجاح',
                                    icon: 'success',
                                    confirmButtonText: 'حسناً',
                                    timer: 2000,
                                    timerProgressBar: true
                                }).then(() => {
                                    // Redirect to show page or index
                                    window.location.href = data.redirect || '{{ route("Quotations.index") }}';
                                });
                            } else {
                                // Show error message
                                let errorMessage = data.message || 'حدث خطأ أثناء حفظ البيانات';
                                if (data.errors) {
                                    errorMessage = Object.values(data.errors).join('<br>');
                                }
                                
                                Swal.fire({
                                    title: 'خطأ!',
                                    html: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'حسناً'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'خطأ!',
                                text: 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى',
                                icon: 'error',
                                confirmButtonText: 'حسناً'
                            });
                        });
                        setTimeout(() => {
                            document.getElementById('quotationForm').submit();
                        }, 800);
                    }
                });
            });

            // Validation function
            function validateForm() {
                // Check if at least one item row exists
                const visibleRows = document.querySelectorAll(
                    '#tableBody .item-row:not(#templateRow):not([style*="display: none"])');
                
                // Check if title is empty
                const title = document.getElementById('title').value.trim();
                if (!title) {
                    Swal.fire({
                        title: 'حقل مطلوب',
                        text: 'يرجى إدخال مسمى لعرض السعر',
                        icon: 'warning',
                        confirmButtonText: 'حسناً'
                    });
                    document.getElementById('title').focus();
                    return false;
                }
                
                // Check if at least one supplier is selected
                const supplierSelect = document.getElementById('supplier_id');
                if (!supplierSelect.selectedOptions.length) {
                    Swal.fire({
                        title: 'حقل مطلوب',
                        text: 'يرجى اختيار مورد واحد على الأقل',
                        icon: 'warning',
                        confirmButtonText: 'حسناً'
                    });
                    supplierSelect.focus();
                    return false;
                }

                if (visibleRows.length === 0) {
                    Swal.fire({
                        title: 'خطأ في الإدخال',
                        text: 'يجب إضافة بند واحد على الأقل',
                        icon: 'error',
                        confirmButtonText: 'حسناً'
                    });
                    return false;
                }
                
                // Validate each row
                let isValid = true;
                visibleRows.forEach((row, index) => {
                    const productSelect = row.querySelector('.item-select');
                    const quantityInput = row.querySelector('.quantity-input');
                    
                    if (!productSelect.value) {
                        Swal.fire({
                            title: 'حقل مطلوب',
                            text: `يرجى اختيار منتج للبند رقم ${index + 1}`,
                            icon: 'warning',
                            confirmButtonText: 'حسناً'
                        });
                        productSelect.focus();
                        isValid = false;
                        return false;
                    }
                    
                    if (!quantityInput.value || parseInt(quantityInput.value) <= 0) {
                        Swal.fire({
                            title: 'قيمة غير صالحة',
                            text: `يرجى إدخال كمية صحيحة للبند رقم ${index + 1}`,
                            icon: 'warning',
                            confirmButtonText: 'حسناً'
                        });
                        quantityInput.focus();
                        isValid = false;
                        return false;
                    }
                });
                
                if (!isValid) {
                    return false;
                }

                // Check required main form fields
                const title = document.getElementById('title').value.trim();
                const orderDate = document.getElementById('order_date').value;
                const supplierSelect = document.getElementById('supplier_id');
                const selectedSuppliers = Array.from(supplierSelect.selectedOptions).map(option => option.value);

                if (!title) {
                    document.getElementById('title').focus();
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'يرجى إدخال مسمى عرض السعر.',
                        icon: 'error',
                        confirmButtonText: 'موافق'
                    });
                    return false;
                }

                if (!orderDate) {
                    document.getElementById('order_date').focus();
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'يرجى إدخال تاريخ الطلب.',
                        icon: 'error',
                        confirmButtonText: 'موافق'
                    });
                    return false;
                }

                if (selectedSuppliers.length === 0) {
                    supplierSelect.focus();
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'يرجى اختيار مورد واحد على الأقل.',
                        icon: 'error',
                        confirmButtonText: 'موافق'
                    });
                    return false;
                }

                // Check item rows
                for (let i = 0; i < visibleRows.length; i++) {
                    const row = visibleRows[i];
                    const itemSelect = row.querySelector('select');
                    const quantityInput = row.querySelector('.quantity-input');

                    if (!itemSelect.value) {
                        itemSelect.focus();
                        Swal.fire({
                            title: 'خطأ!',
                            text: `يرجى اختيار البند في الصف رقم ${i + 1}.`,
                            icon: 'error',
                            confirmButtonText: 'موافق'
                        });
                        return false;
                    }

                    if (!quantityInput.value || quantityInput.value <= 0) {
                        quantityInput.focus();
                        Swal.fire({
                            title: 'خطأ!',
                            text: `يرجى إدخال كمية صحيحة في الصف رقم ${i + 1}.`,
                            icon: 'error',
                            confirmButtonText: 'موافق'
                        });
                        return false;
                    }
                }

                return true;
            }
            
            // Handle cancel button
            document.getElementById('cancelBtn').addEventListener('click', function() {
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: 'سيتم فقدان جميع البيانات غير المحفوظة',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، إلغاء',
                    cancelButtonText: 'تراجع',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("Quotations.index") }}';
                    }
                });
            });

            // Cancel button functionality
            document.getElementById('cancelBtn').addEventListener('click', function(e) {
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
                        window.location.href = "{{ route('Quotations.index') }}";
                    }
                });
            });

            // Initialize with first row
            addNewRow();

            // Initialize select2
            $('.select2').select2({
                placeholder: "اختر الموردين",
                allowClear: true,
                dir: 'rtl'
            });
        });
    </script>
@endsection
