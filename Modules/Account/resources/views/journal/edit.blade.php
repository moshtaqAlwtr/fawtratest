@extends('master')

@section('title')
    أضف قيد
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أضف قيد</h2>
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
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>

                <div>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i>الغاء
                    </a>
                    <button type="submit" form="products_form" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i>حفظ
                    </button>
                </div>

            </div>
        </div>
    </div>
    <div class="container mt-5">
        <form action="{{ route('journal.update', $journal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- تعديل ��لى PUT لتحديد أنه تعديل -->
            <!-- الصف الأول مع الكرتين الأول والثاني في نفس السطر -->
            <div class="row">
                <!-- الكرت الأول -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <!-- التاريخ في سطر منفصل -->
                                <div class="col-md-12 mb-3">
                                    <label for="date">التاريخ <span class="text-danger">*</span></label>
                                    <input type="date" id="date" name="date" class="form-control" required>
                                </div>

                                <!-- العملة في سطر منفصل -->
                                <div class="col-md-12 mb-3">
                                    <label for="currency">العملة</label>
                                    <input type="text" id="currency" class="form-control" value="SAR" disabled>
                                </div>

                                <!-- الرقم في سطر منفصل -->
                                <div class="col-md-12 mb-3">
                                    <label for="number">رقم</label>
                                    <input type="text" id="number" class="form-control" value="48736" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الكرت الثاني -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <!-- الوصف في سطر منفصل -->
                                <div class="col-md-12 mb-3">
                                    <label for="description">الوصف <span class="text-danger">*</span></label>
                                    <textarea id="description" name="description" class="form-control" rows="2" required></textarea>
                                </div>

                                <!-- المرفقات في سطر منفصل -->
                                <div class="col-md-12 mb-3">
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

            <!-- جدول تفاصيل القيود -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="entries-table">
                            <thead>
                                <tr>
                                    <th>الحساب</th>
                                    <th>مدين</th>
                                    <th>دائن</th>
                                    <th>البيان</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="entries[0][account_id]" class="form-control" required>
                                            <option value="">اختر الحساب</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="entries[0][debit]" class="form-control debit"
                                            min="0" step="0.01">
                                    </td>
                                    <td>
                                        <input type="number" name="entries[0][credit]" class="form-control credit"
                                            min="0" step="0.01">
                                    </td>
                                    <td>
                                        <input type="text" name="entries[0][description]" class="form-control">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i
                                                class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">
                                        <button type="button" class="btn btn-primary" id="add-row">
                                            <i class="fas fa-plus"></i> إضافة سطر
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>المجموع</td>
                                    <td id="total-debit">0.00</td>
                                    <td id="total-credit">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@section('scripts')
    <script>
        $(document).ready(function() {
            let rowCount = 1;

            // إضافة سطر جديد
            $('#add-row').click(function() {
                let newRow = `
            <tr>
                <td>
                    <select name="entries[${rowCount}][account_id]" class="form-control" required>
                        <option value="">اختر الحساب</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="entries[${rowCount}][debit]" class="form-control debit" min="0" step="0.01">
                </td>
                <td>
                    <input type="number" name="entries[${rowCount}][credit]" class="form-control credit" min="0" step="0.01">
                </td>
                <td>
                    <input type="text" name="entries[${rowCount}][description]" class="form-control">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
                $('#entries-table tbody').append(newRow);
                rowCount++;
                updateTotals();
            });

            // حذف سطر
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                updateTotals();
            });

            // تحديث المجاميع
            $(document).on('input', '.debit, .credit', function() {
                updateTotals();
            });

            function updateTotals() {
                let totalDebit = 0;
                let totalCredit = 0;

                $('.debit').each(function() {
                    totalDebit += parseFloat($(this).val() || 0);
                });

                $('.credit').each(function() {
                    totalCredit += parseFloat($(this).val() || 0);
                });

                $('#total-debit').text(totalDebit.toFixed(2));
                $('#total-credit').text(totalCredit.toFixed(2));

                // التحقق من توازن القيود
                if (totalDebit !== totalCredit) {
                    $('#total-debit, #total-credit').addClass('text-danger');
                } else {
                    $('#total-debit, #total-credit').removeClass('text-danger');
                }
            }
        });
    </script>
@endsection
@endsection
