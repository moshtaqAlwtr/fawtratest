@extends('master')

@section('title')
    تعديل بنود راتب
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل بنود راتب</h2>
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
        <form class="form" action="{{ route('SalaryItems.update', $salaryItem) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
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

                <div class="card-body">
                    <h1 class="card-title"> معلومات بنود الراتب </h1>

                    <div class="form-body row">
                        <div class="form-group col-md-6">
                            <label for="feedback1" class="">الاسم </label>
                            <input type="text" id="feedback1" class="form-control" placeholder="الاسم" name="name"
                                value="{{ old('name', $salaryItem->name) }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="feedback2" class=""> نوع </label>
                            <select name="type" class="form-control" id=""
                                value="{{ old('type', $salaryItem->type) }}  >
                                <option value="">
                                اختر النوع</option>
                                <option value="1"> مستحق </option>
                                <option value="2"> مستقطع</option>
                            </select>
                        </div>


                    </div>
                    <div class="form-body row">


                        <div class="form-group col-md-6">
                            <label for="feedback2" class=""> الحالة </label>
                            <select name="status" class="form-control" id=""
                                value="{{ old('status', $salaryItem->status) }}">
                                <option value=""> اختر الحالة</option>
                                <option value="1"> نشط </option>
                                <option value="2"> معطل</option>
                                <option value="3"> غير نشط</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="feedback2" class=""> الوصف </label>
                            <textarea name="description" class="form-control" id="">{{ old('description', $salaryItem->description) }}</textarea>
                        </div>
                    </div>





                    <div class="form-body row">
                        <!-- المبلغ -->
                        <div class="col-md-6">
                            <div class="position-relative">
                                <div class="input-group form-group">
                                    <div class="input-group-prepend w-100">
                                        <div class="input-group-text w-100 text-right">
                                            <div
                                                class="custom-control custom-radio d-flex justify-content-start align-items-center w-100">
                                                <input id="amount_radio" name="salary_item_value" value="1"
                                                    value="{{ old('salary_item_value', $salaryItem->salary_item_value) }}"
                                                    class="custom-control-input" type="radio" checked
                                                    onchange="toggleFields()">
                                                <label for="amount_radio" class="custom-control-label mr-2">المبلغ
                                                    <span class="required">*</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="amount-inputs">
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <label for="amount" class="form-label" style="margin-bottom: 10px">
                                                ادخل المبلغ</label>
                                            <input type="number" step="0.01" class="form-control" name="amount"
                                                id="amount" placeholder="ادخل المبلغ"
                                                value="{{ old('amount', $salaryItem->amount) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- اختيار الصيغة الحسابية  -->
                        <div class="col-md-6">
                            <div class="position-relative">
                                <div class="input-group form-group">
                                    <div class="input-group-prepend w-100">
                                        <div class="input-group-text w-100">
                                            <div
                                                class="custom-control custom-radio d-flex justify-content-start align-items-center w-100">
                                                <input id="formula_radio" name="salary_item_value"
                                                    value="{{ old('salary_item_value', $salaryItem->salary_item_value) }}"
                                                    value="2" class="custom-control-input" type="radio"
                                                    onchange="toggleFields()">
                                                <label for="formula_radio" class="custom-control-label">الصيغة الحسابية
                                                    <span class="required">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <label for="calculation_formula" class="form-label">ادخل الصيغة الحسابية </label>
                                        <input type="text" class="form-control" id="calculation_formula"
                                            placeholder="ادخل الصيغة الحسابية" name="calculation_formula"
                                            value="{{ old('calculation_formula', $salaryItem->calculation_formula) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-body row">


                        <div class="form-group col-md-6">
                            <label for="feedback2" class=""> الشرط ؟ </label>
                            <input type="text" class="form-control" placeholder="الشرط ؟" name="condition"
                                value="{{ old('condition', $salaryItem->condition) }}">
                        </div>




                    </div>
                    <div class="form-body row">


                        <div class="form-group col-md-6">
                            <label for="feedback2" class="">الحساب الافتراضي ؟ </label>
                            <select name="chart_of_account_id" class="form-control" value="{{ old('chart_of_account_id', $salaryItem->chart_of_account_id) }}">
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}"
                                        {{ old('chart_of_account_id', $salaryItem->chart_of_account_id) == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group form-group">
                                <div class="input-group-text w-100 text-left">
                                    <div
                                        class="custom-control custom-checkbox d-flex justify-content-start align-items-center w-100">
                                        <input id="duration_check" class="custom-control-input" name="reference_value"
                                            value="{{ old('reference_value', $salaryItem->reference_value) }}"
                                            type="checkbox">
                                        <label for="duration_check" class="custom-control-label ml-2">
                                            قيمة مرجعية فقط؟ <span class="required">*</span>
                                        </label>
                                    </div>
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
    <script>
        $(document).ready(function() {
            toggleFields(); // تشغيل الدالة عند تحميل الصفحة
        });

        function toggleFields() {
            const amountInput = document.getElementById('amount');
            const formulaInput = document.getElementById('calculation_formula');

            if (document.getElementById('amount_radio').checked) {
                amountInput.disabled = false;
                formulaInput.disabled = true;
                formulaInput.value = ''; // تفريغ قيمة الصيغة الحسابية
            }

            if (document.getElementById('formula_radio').checked) {
                amountInput.disabled = true;
                formulaInput.disabled = false;
                amountInput.value = ''; // تفريغ قيمة المبلغ
            }
        }
    </script>
@endsection
