@extends('master')

@section('title', 'العقود')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">العقود</h2>
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
<form id="contractForm" class="form" action="{{ route('Contracts.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <label>الحقول التي عليها علامة <span style="color:red">*</span> الزامية</label>
            <div>
                <a href="" class="btn btn-outline-danger"><i class="fa fa-ban"></i> الغاء</a>
                <button type="submit" class="btn btn-outline-primary"><i class="fa fa-save"></i> حفظ</button>
            </div>
        </div>
    </div>

    {{-- بيانات أساسية --}}
    <div class="card" style="max-width:90%;margin:0 auto;">
        <div class="card-body">
            <div class="form-body row">
                <div class="form-group col-md-6">
                    <label for="employee_id">الموظف</label>
                    <select name="employee_id" id="employee_id" class="form-control" required>
                        <option value="">اختر الموظف</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="feedback2">الكود</label>
                    <input type="text" id="feedback2" class="form-control" value="{{ $nextNumber }}" readonly disabled>
                    <small class="text-muted">رقم تسلسلي تلقائي</small>
                </div>
            </div>

            <div class="form-body row">
                <div class="form-group col-md-6">
                    <label for="job_title_id">المسمى الوظيفي</label>
                    <select name="job_title_id" id="job_title_id" class="form-control" required>
                        <option value="">اختر المسمى الوظيفي</option>
                        @foreach ($jopTitle as $jop)
                            <option value="{{ $jop->id }}" {{ old('job_title_id') == $jop->id ? 'selected' : '' }}>
                                {{ $jop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="job_level_id">المستوى الوظيفي</label>
                    <select name="job_level_id" id="job_level_id" class="form-control" required>
                        <option value="">اختر المستوى الوظيفي</option>
                        @foreach ($functionalLevels as $functionalLevel)
                            <option value="{{ $functionalLevel->id }}" {{ old('job_level_id') == $functionalLevel->id ? 'selected' : '' }}>
                                {{ $functionalLevel->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-body row">
                <div class="form-group col-md-6">
                    <label for="parent_contract_id">العقد الاساسي</label>
                    <select name="parent_contract_id" id="parent_contract_id" class="form-control">
                        <option value="">اختر العقد الاساسي</option>
                        {{-- املأ القائمة إن توفرت --}}
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="description">الوصف</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
            </div>

            <div class="form-body row">
                <div class="form-group col-md-6">
                    <label for="start_date">تاريخ البدء <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" id="start_date" class="form-control">
                </div>
            </div>

            <div class="form-body row">
                <div class="col-md-6">
                    <div class="position-relative">
                        <div class="input-group form-group">
                            <div class="input-group-prepend w-100">
                                <div class="input-group-text w-100">
                                    <div class="custom-control custom-radio d-flex align-items-center w-100">
                                        <input id="duration_radio" name="contract_type" class="custom-control-input" type="radio" value="1">
                                        <label for="duration_radio" class="custom-control-label">مدة <span class="required">*</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="duration-inputs" class="form-group">
                        <div class="form-row">
                            <div class="col-md-6 form-group mb-3 input-error-target">
                                <input class="form-control" placeholder="مدة" type="number" value="1" name="duration">
                            </div>
                            <div class="col-md-6 form-group mb-0 input-error-target">
                                <select class="form-control" name="duration_unit">
                                    <option value="1">يومي</option>
                                    <option value="2">شهر</option>
                                    <option value="3">سنة</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="position-relative">
                        <div class="input-group form-group">
                            <div class="input-group-prepend w-100">
                                <div class="input-group-text w-100">
                                    <div class="custom-control custom-radio custom-control-inline mx-0">
                                        <input id="enddate_radio" name="contract_type" class="custom-control-input" type="radio" value="2">
                                        <label for="enddate_radio" class="custom-control-label">تاريخ الإنتهاء <span class="required">*</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-body row" style="margin-top:20px">
                            <div class="form-group col-md-12">
                                <input type="date" id="end_date" class="form-control" name="end_date">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-body row" style="margin-top:20px">
                <div class="form-group col-md-6">
                    <label for="join_date">تاريخ الالتحاق <span class="text-danger">*</span></label>
                    <input type="date" id="join_date" class="form-control" name="join_date">
                </div>
                <div class="form-group col-md-6">
                    <label for="probation_end_date">تاريخ نهاية الاختبار <span class="text-danger">*</span></label>
                    <input type="date" id="probation_end_date" class="form-control" name="probation_end_date">
                </div>
            </div>

            <div class="form-body row">
                <div class="form-group col-md-6">
                    <label for="contract_date">تاريخ توقيع العقد</label>
                    <input type="date" id="contract_date" class="form-control" name="contract_date">
                </div>
            </div>
        </div>
    </div>

    {{-- بيانات الراتب --}}
    <div class="card" style="max-width:90%;margin:20px auto 0">
        <div class="card-body">
            <h5 class="card-title">بيانات الراتب</h5>

            <div class="row mb-3">
                <div class="form-group col-md-6">
                    <label for="receiving_cycle">دورة القبض</label>
                    <select name="receiving_cycle" id="receiving_cycle" class="form-control">
                        <option value="">اختر دورة القبض</option>
                        <option value="1">شهري</option>
                        <option value="2">اسبوعي</option>
                        <option value="3">سنوي</option>
                        <option value="4">ربع سنوي</option>
                        <option value="5">مرة كل اسبوعين</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="mb-1">قالب الرواتب <span class="text-danger">*</span></label>
                    <select class="form-control" name="salary_temp_id">
                        <option value="">اختر القالب</option>
                        @foreach ($salaryTemplates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>

                <x-form.select label="عملة " name="currency" id="from_currency" col="6">
                    <option value="">العملة</option>
                    @foreach (\App\Helpers\CurrencyHelper::getAllCurrencies() as $code => $name)
                        <option value="{{ $code }}">{{ $code }} {{ $name }}</option>
                    @endforeach
                </x-form.select>
            </div>

            {{-- مستحق --}}
            <div class="mt-4">
                <h6>مستحق</h6>
                <div class="table-responsive">
                    <table class="table">
                        <thead style="background:#e9ecef">
                            <tr>
                                <th style="width:50px"></th>
                                <th>بند الراتب</th>
                                <th>الصيغة الحسابية</th>
                                <th>المبلغ</th>
                                <th style="width:50px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="align-middle text-center"><i class="fas fa-lock text-muted"></i></td>
                                <td class="align-middle text-center"><span>basic</span></td>
                                <td style="background:#e9ecef"></td>
                                <td>
                                    <input type="text" class="form-control" name="amount" placeholder="ادخل القيمة" style="background-color:#fff3cd;">
                                </td>
                                <td class="align-middle">
                                    <div class="row">
                                        <i class="fas fa-key text-muted" style="margin-right:5px;"></i>
                                        <span>رئيسي</span>
                                    </div>
                                </td>
                            </tr>
                            {{-- TEMPLATE: مستحق --}}
                        </tbody>
                    </table>

                    {{-- template خارج tbody لنسخ نظيف --}}
                    <template id="tpl-addition-row">
                        <tr style="background-color:#fff7d6;">
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
                                        <option value="_create_new_addition">اضافة بند مستحقات جديد</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control calculation-input" placeholder="ادخل الصيغة" name="addition_calculation_formula[]">
                            </td>
                            <td>
                                <input type="text" class="form-control amount-input" placeholder="ادخل المبلغ" name="addition_amount[]">
                            </td>
                            <td class="align-middle text-center">
                                <i class="fas fa-minus-circle text-danger remove-row" style="cursor:pointer;"></i>
                            </td>
                        </tr>
                    </template>

                    <a class="btn btn-success add-row-button-addition"><i class="fas fa-plus"></i> إضافة</a>
                </div>
            </div>

            {{-- مستقطع --}}
            <div class="mt-4">
                <h6>مستقطع</h6>
                <div class="table-responsive">
                    <table class="table">
                        <thead style="background:#e9ecef">
                            <tr>
                                <th style="width:50px"></th>
                                <th>بند الراتب</th>
                                <th>الصيغة الحسابية</th>
                                <th>المبلغ</th>
                                <th style="width:50px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- TEMPLATE فقط عبر <template> أدناه --}}
                        </tbody>
                    </table>

                    <template id="tpl-deduction-row">
                        <tr style="background-color:#fff7d6;">
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
                                <input type="text" class="form-control calculation-input" placeholder="ادخل الصيغة" name="deduction_calculation_formula[]">
                            </td>
                            <td>
                                <input type="text" class="form-control amount-input" placeholder="ادخل المبلغ" name="deduction_amount[]">
                            </td>
                            <td class="align-middle text-center">
                                <i class="fas fa-minus-circle text-danger remove-row" style="cursor:pointer;"></i>
                            </td>
                        </tr>
                    </template>

                    <a class="btn btn-success add-row-button-deduction"><i class="fas fa-plus"></i> إضافة</a>
                </div>
            </div>

            {{-- المرفقات --}}
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

        </div> {{-- card-body --}}
    </div> {{-- card --}}

    <div id="additionalForm" style="display:none;">
        <div>معلومات بند الراتب</div>
        <div>{{-- حقول لاحقًا --}}</div>
    </div>

</form>
</div>
@endsection

@section('scripts')
{{-- لو عندك salaries.js خليه، مافيه تعارض --}}
<script src="{{ asset('assets/js/salaries.js') }}"></script>
<script>
(function ($) {
    'use strict';

    function enhanceSelects($scope) {
        if (!$.fn.select2) return;
        $scope.find('select.item-select').each(function () {
            const $sel = $(this);
            if (!$sel.hasClass('select2-hidden-accessible')) {
                $sel.select2({ width: '100%', placeholder: 'الرجاء الاختيار' });
            }
        });
    }

    function appendRowFromTemplate(tplId, $tbody) {
        const tpl = document.getElementById(tplId);
        if (!tpl) return;
        const html = tpl.innerHTML.trim();
        const $row = $(html);

        // صفّر القيم
        $row.find('select.item-select').val('');
        $row.find('.calculation-input').val('');
        $row.find('.amount-input').val('');

        $tbody.append($row);
        enhanceSelects($row); // فعّل Select2 بعد إدراج الصف
    }

    // إضافة مستحق
    $(document).on('click', '.add-row-button-addition', function (e) {
        e.preventDefault();
        const $tbody = $(this).closest('.table-responsive').find('tbody');
        appendRowFromTemplate('tpl-addition-row', $tbody);
    });

    // إضافة مستقطع
    $(document).on('click', '.add-row-button-deduction', function (e) {
        e.preventDefault();
        const $tbody = $(this).closest('.table-responsive').find('tbody');
        appendRowFromTemplate('tpl-deduction-row', $tbody);
    });

    // حذف صف
    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
    });

    // تهيئة لأي صفوف جاهزة ليست تيمبلِت
    $(function () {
        enhanceSelects($('table').find('tr'));
    });
})(jQuery);
</script>
@endsection
