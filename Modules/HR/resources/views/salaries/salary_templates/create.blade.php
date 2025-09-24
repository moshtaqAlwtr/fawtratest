@extends('master')

@section('title')
    اضافة قالب الراتب
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اضافة قالب الراتب</h2>
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
        <form class="form" action="{{ route('SalaryTemplates.store') }}" method="post" enctype="multipart/form-data">
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
                        <div class="form-group col-md-6">
                            <label for="feedback1" class=""> الاسم </label>
                            <input type="text" id="feedback1" class="form-control" placeholder="الاسم" name="name">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="contract_date" class="">الحالة </label>
                            <select name="status" class="form-control" id="">
                                <option value=""> اختر الحالة</option>
                                <option value="1">نشط</option>
                                <option value="2">غير نشط </option>

                            </select>
                        </div>
                    </div>

                    <div class="form-body row">
                        <div class="form-group col-md-6">
                            <label for="contract_date" class="">دورة القبض </label>
                            <select name="receiving_cycle" class="form-control" id="">
                                <option value=""> اختر دورة القبض</option>
                                <option value="1"> شهري </option>
                                <option value="2"> اسبوعي </option>
                                <option value="3"> سنوي </option>
                                <option value="4"> ربع سنوي </option>
                                <option value="5"> مرة كل اسبوعين </option>




                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="start_date" class="">الوصف <span class="text-danger">*</span></label>
                            <textarea name="description" id="" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card" style="max-width: 90%; margin: 0 auto; margin-top: 20px">
                <div class="card-body">


                    <!-- مستحق -->
                    <div class="mt-4">
                        <h6>مستحق</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="" style="background: #e9ecef">
                                    <tr>
                                        <th style="width: 50px"></th>
                                        <th>بند الراتب</th>
                                        <th>الصيغة الحسابية</th>
                                        <th>المبلغ</th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="align-middle text-center">
                                            <li class="fas fa-lock text-muted"></li>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span>basic</span>
                                        </td>
                                        <td class="" style="background: #e9ecef"></td>
                                        <td>
                                            <input type="text" class="form-control" name="basic_amount" placeholder="ادخل القيمة"
                                                style="background-color: #fff3cd;">
                                        </td>
                                        <td class="align-middle">
                                            <div class="row">
                                                <i class="fas fa-key text-muted" style="margin-right: 5px;"></i>
                                                <span>رئيسي</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr id="newRow" style="display: none; background-color: #fff7d6;">
                                        <td class="align-middle text-center">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td>
                                            <div class="position-relative">
                                                <select class="form-control item-select" name="addition_type[]" data-type="addition">
                                                    <option value="">اختر البند</option>
                                                    @foreach ($additionItems as $item)
                                                        <option value="{{ $item->id }}"
                                                                data-calculation="{{ $item->calculation_formula }}"
                                                                data-amount="{{ $item->amount }}">
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach
                                                    <option value=""> اضافة بند مستحقات جديد</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control calculation-input" placeholder="ادخل الصيغة"
                                                   name="addition_calculation_formula[]" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control amount-input" placeholder="ادخل المبلغ"
                                                   name="addition_amount[]" >
                                        </td>
                                        <td class="align-middle text-center">
                                            <i class="fas fa-minus-circle text-danger remove-row" style="cursor: pointer;"></i>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <a class="btn btn-success add-row-button">
                                <i class="fas fa-plus"></i> إضافة
                            </a>
                        </div>
                    </div>

                    <!-- مستقطع -->
                    <div class="mt-4">
                        <h6>مستقطع</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="" style="background: #e9ecef">
                                    <tr>
                                        <th style="width: 50px"></th>
                                        <th>بند الراتب</th>
                                        <th>الصيغة الحسابية</th>
                                        <th>المبلغ</th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="align-middle text-center">
                                            <li class="fas fa-lock text-muted"></li>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span>basic</span>
                                        </td>
                                        <td class="" style="background: #e9ecef"></td>
                                        <td>
                                            <input type="text" class="form-control" name="deduction_basic_amount" placeholder="ادخل القيمة"
                                                style="background-color: #fff3cd;">
                                        </td>
                                        <td class="align-middle">
                                            <div class="row">
                                                <i class="fas fa-key text-muted" style="margin-right: 5px;"></i>
                                                <span>رئيسي</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr id="newRow2" style="display: none; background-color: #fff7d6;">
                                        <td class="align-middle text-center">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td>
                                            <div class="position-relative">
                                                <select class="form-control item-select" name="deduction_type[]" data-type="deduction">
                                                    <option value="">اختر البند</option>
                                                    @foreach ($deductionItems as $item)
                                                        <option value="{{ $item->id }}"
                                                                data-calculation="{{ $item->calculation_formula }}"
                                                                data-amount="{{ $item->amount }}">
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control calculation-input" placeholder="ادخل الصيغة"
                                                name="deduction_calculation_formula[]">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control amount-input" placeholder="ادخل المبلغ"
                                                name="deduction_amount[]">
                                        </td>
                                        <td class="align-middle text-center">
                                            <i class="fas fa-minus-circle text-danger remove-row" style="cursor: pointer;"></i>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <a class="btn btn-success add-row-button">
                                <i class="fas fa-plus"></i> إضافة
                            </a>
                        </div>
                    </div>


                    <!-- مستقطع -->

                </div>
            </div>
        </form>

    </div>

    </div>

@endsection

@section('scripts')


<script>
    // دالة لإضافة صف جديد
    function addNewRow(button) {
        const table = button.closest('.table-responsive').querySelector('table');
        const template = table.querySelector('tr[id^="newRow"]');
        const newRow = template.cloneNode(true);
        newRow.style.display = '';
        newRow.removeAttribute('id');

        // إضافة مستمع حدث للقائمة المنسدلة
        const select = newRow.querySelector('select');
        if (select) {
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const row = this.closest('tr');
                const calculationInput = row.querySelector('input[name*="calculation_formula"]');
                const amountInput = row.querySelector('input[name*="amount"]');

                if (selectedOption && selectedOption.value) {
                    calculationInput.value = selectedOption.getAttribute('data-calculation') || '';
                    amountInput.value = selectedOption.getAttribute('data-amount') || '';
                } else {
                    calculationInput.value = '';
                    amountInput.value = '';
                }
            });
        }

        // إضافة مستمع حدث لزر الإزالة
        const removeButton = newRow.querySelector('.remove-row');
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                newRow.remove();
            });
        }

        table.querySelector('tbody').appendChild(newRow);
    }

    // إضافة مستمعات الأحداث لأزرار الإضافة
    document.querySelectorAll('.add-row-button').forEach(button => {
        button.addEventListener('click', function() {
            addNewRow(this);
        });
    });

    // إضافة مستمعات الأحداث للقوائم المنسدلة الموجودة
    document.querySelectorAll('select[name*="type"]').forEach(select => {
        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const row = this.closest('tr');
            const calculationInput = row.querySelector('input[name*="calculation_formula"]');
            const amountInput = row.querySelector('input[name*="amount"]');

            if (selectedOption && selectedOption.value) {
                calculationInput.value = selectedOption.getAttribute('data-calculation') || '';
                amountInput.value = selectedOption.getAttribute('data-amount') || '';
            } else {
                calculationInput.value = '';
                amountInput.value = '';
            }
        });
    });

    // إضافة مستمعات الأحداث لأزرار الإزالة الموجودة
    document.querySelectorAll('.remove-row').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('tr').remove();
        });
    });
</script>

@endsection


