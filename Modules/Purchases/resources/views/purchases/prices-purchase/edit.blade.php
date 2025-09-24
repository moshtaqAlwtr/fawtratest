@extends('master')

@section('title')
    تعديل عرض سعر شراء
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل عرض سعر شراء</h2>
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
        <form class="form" action="{{ route('Quotations.update', $purchaseQuotation->id) }}" method="post" enctype="multipart/form-data">
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

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                        </div>
                        <div>
                            <a href="" class="btn btn-outline-danger">
                                <i class="fa fa-ban"></i>الغاء
                            </a>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-save"></i>حفظ
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card" style="max-width: 90%; margin: 0 auto;">
                <h1>
                </h1>
                <div class="card-body">
                    <div class="form-body row">

                        <div class="form-group col-md-4">
                            <label for="feedback1" class=""> الكود </label>
                            <input type="text" id="feedback1" class="form-control" placeholder="الكود" name="code" value="{{ str_pad((\App\Models\PurchaseQuotation::max('id') ?? 0) + 1, 5, '0', STR_PAD_LEFT) }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="feedback1" class=""> تاريخ الطلب </label>
                            <input type="date" id="feedback1" class="form-control" name="order_date">

                        </div>
                        <div class="form-group col-md-4">
                            <label for="feedback1" class=""> تاريخ الاستحقاق </label>
                            <input type="date" id="feedback1" class="form-control" name="due_date">

                        </div>
                    </div>
                    <div class="form-body row">
                        <div class="form-group col-md-12">
                            <label for="supplier_id" class=""> اختر الموردين <span style="color: red">*</span></label>
                            <select id="supplier_id" class="form-control select2" name="supplier_id[]" multiple="multiple" required>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ in_array($supplier->id, old('supplier_id', [])) ? 'selected' : '' }}>
                                        {{ $supplier->trade_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                </div>

            </div>


    </div>
    <div class="card" style="max-width: 90%; margin: 0 auto; margin-top: 20px">
        <div class="card-body">






            <div class="mt-4">
                <h6>المنتج <span style="color: red">*</span></h6>
                <div class="table-responsive">
                    <table class="table" id="products-table">
                        <thead style="background: #e9ecef">
                            <tr>
                                <th style="width: 50px"></th>
                                <th>بند</th>
                                <th>الكمية</th>
                                <th style="width: 50px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- الصف الأول الافتراضي -->
                            <tr class="product-row">
                                <td class="align-middle text-center">
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </td>
                                <td>
                                    <div class="position-relative">
                                        <select class="form-control item-select" name="product_details[0][product_id]" required>
                                            <option value="">اختر البند</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control amount-input" placeholder="ادخل كمية"
                                        name="product_details[0][quantity]" min="1" required>
                                </td>
                                <td class="align-middle text-center">
                                    <i class="fas fa-minus-circle text-danger remove-row" style="cursor: pointer;"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success" onclick="addNewRow()">
                        <i class="fas fa-plus"></i> إضافة منتج
                    </button>
                </div>
            </div>



        </div>

    </div>



    <div class="card" style="max-width: 90%; margin: 0 auto; margin-top: 20px">
        <div class="card-body">
            <!-- الملاحظات -->
            <div class="mt-4">
                <h6 class="mb-2">الملاحظات</h6>
                <textarea class="form-control" name="notes" rows="4" placeholder="اكتب ملاحظاتك هنا..."></textarea>
            </div>
            <div class="mt-4">
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


        </form>
    </div>


@endsection

@section('scripts')
    <script>
        let rowCounter = 1;

        function addNewRow() {
            const tbody = document.querySelector('#products-table tbody');
            const newRow = document.createElement('tr');
            newRow.className = 'product-row';

            newRow.innerHTML = `
                <td class="align-middle text-center">
                    <i class="fas fa-grip-vertical text-muted"></i>
                </td>
                <td>
                    <div class="position-relative">
                        <select class="form-control item-select" name="product_details[${rowCounter}][product_id]" required>
                            <option value="">اختر البند</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control amount-input" placeholder="ادخل كمية"
                        name="product_details[${rowCounter}][quantity]" min="1" required>
                </td>
                <td class="align-middle text-center">
                    <i class="fas fa-minus-circle text-danger remove-row" onclick="removeRow(this)" style="cursor: pointer;"></i>
                </td>
            `;

            tbody.appendChild(newRow);
            rowCounter++;
        }

        function removeRow(element) {
            const tbody = document.querySelector('#products-table tbody');
            if (tbody.querySelectorAll('.product-row').length > 1) {
                element.closest('.product-row').remove();
            } else {
                alert('يجب أن يكون هناك منتج واحد على الأقل');
            }
        }

        // إضافة مستمع لأحداث النقر على أزرار الحذف الموجودة
        document.querySelectorAll('.remove-row').forEach(button => {
            button.onclick = function() {
                removeRow(this);
            };
        });

        // التحقق من النموذج قبل الإرسال
        document.getElementById('quotationForm').onsubmit = function(e) {
            const rows = document.querySelectorAll('.product-row');
            let isValid = true;

            rows.forEach(row => {
                const productId = row.querySelector('.item-select').value;
                const quantity = row.querySelector('.amount-input').value;

                if (!productId || !quantity) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('الرجاء ملء جميع حقول المنتجات والكميات');
            }
        };
    </script>
@endsection
