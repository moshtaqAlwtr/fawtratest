@extends('master')

@section('title')
    تعديل قواعد العمولة
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> تعديل قواعد العمولة</h2>
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
        <div class="card mb-5">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
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

        <form class="form mt-4">
            <div class="card" style="max-width: 90%; margin: 0 auto;">
                <h1>
                </h1>
                <div class="card-body">

                    <div class="form-body row mb-5">
                        <div class="form-group col-md-6 mb-3">
                            <label for="feedback2" class="">اسم العمولة <span class="text-danger">*</span></label>
                            <input type="text" id="feedback2" class="form-control" placeholder="اسم العمولة"
                                name="commission_name">
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label for="feedback1" class="">الحالة <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" id="">
                                <option value="1">نشط</option>
                                <option value="0">غير نشط</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-body row mb-5">
                        <div class="form-group col-md-6 mb-3">
                            <label for="feedback1" class="">الفترة <span class="text-danger">*</span></label>
                            <select name="period" class="form-control" id="">
                                <option value="">الفترة السنوية</option>
                                <option value="">الفترة الشهرية</option>
                                <option value="">الفترة اليومية</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label for="feedback1" class="">حساب العمولة <span class="text-danger">*</span></label>
                            <select name="commission_account" class="form-control" id="">
                                <option value=""> اختر حساب العمولة </option>
                                <option value=""> فواتير مدفوعة بالكامل </option>
                                <option value=""> فواتير مدفوعة جزئيا </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-body row mb-5">
                        <div class="form-group col-md-6 mb-3">
                            <label for="feedback1" class="">الموظفين <span class="text-danger">*</span></label>
                            <select name="employees[]" class="form-control" id="">
                                <option value=""> اختر الموظفين</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label for="feedback1" class="">العملة <span class="text-danger">*</span></label>
                            <select name="currency" class="form-control" id="">
                                <option value="SAR">SAR</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card" style="max-width: 90%; margin: 0 auto; margin-top: 20px">
                <div class="card-body">
                    <div class="mt-4">
                        <h6>تطبق على البنود التالية </h6>
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
                                            <div class="position-relative">
                                                <select name="item_type" class="form-control"    style="background-color: #fff3cd;">
                                                    <option value="">إختر البند</option>
                                                    <option value="all_products">كل المنتجات</option>
                                                    <option value="category">التصنيف</option>
                                                    <option value="item">البند</option>
                                                </select>

                                            </div>
                                        </td>

                                        <td class="" style="background: #fff3cd"></td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="ادخل القيمة"
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
                                                <select name="item_type" class="form-control">
                                                    <option value="">إختر البند</option>
                                                    <option value="all_products">كل المنتجات</option>
                                                    <option value="category">التصنيف</option>
                                                    <option value="item">البند</option>
                                                </select>

                                            </div>
                                        </td>
                                        <td>
                                            <select name="" class="form-control" id="">
                                                <option value=""> اختر الصيغة</option>


                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="ادخل المبلغ">
                                        </td>
                                        <td class="align-middle text-center">
                                            <i class="fas fa-minus-circle text-danger"></i>
                                            <span class="text-danger ms-2">إزالة</span>
                                        </td>
                                    </tr>
                                    <tr id="newRow" style="display: none; background-color: #fff7d6;">
                                        <td class="align-middle text-center">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td>
                                            <select name="item_type" class="form-control">
                                                <option value="">إختر البند</option>
                                                <option value="all_products">كل المنتجات</option>
                                                <option value="category">التصنيف</option>
                                                <option value="item">البند</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button id="showFormButton" class="btn btn-success"><i
                                                    class="fas fa-plus-circle"></i> إضافة بند</button>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="ادخل المبلغ">
                                        </td>
                                        <td class="align-middle text-center">
                                            <i class="fas fa-minus-circle text-danger"></i>
                                            <span class="text-danger ms-2">إزالة</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="mb-5">
                                <a id="addRowButton" class="btn btn-success">
                                    <li class="fas fa-plus"></li>إضافة
                                </a>
                            </div>
                        </div>
                    </div>


                    <div class="form-body row" style="margin-bottom: 20px">


                        <div class="col-md-6 mb-3">
                            <div class="position-relative">
                                <div class="input-group form-group">
                                    <div class="input-group-prepend w-100">
                                        <div class="input-group-text w-100">
                                            <div
                                                class="custom-control custom-radio d-flex justify-content-start align-items-center w-100">
                                                <input id="duration_radio" name="contract_type"
                                                    class="custom-control-input" type="radio" value="duration">
                                                <label for="duration_radio" class="custom-control-label">نوع الهدف <span
                                                        class="required">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="duration-inputs">
                                <div class="form-row">
                                    <div class="col-md-12 form-group mb-3 mb-md-3  input-error-target">
                                        <input class="form-control" placeholder=" المبلع المستهدف" type="number"
                                            value="1">
                                    </div>

                                </div>
                            </div>
                        </div>



                        <div class="col-md-6 mb-3">
                            <div class="position-relative">
                                <div class="input-group form-group">
                                    <div class="input-group-prepend w-100">
                                        <div class="input-group-text w-100">
                                            <div
                                                class="custom-control custom-radio d-flex justify-content-start align-items-center w-100">

                                                <div class="custom-control custom-radio custom-control-inline mx-0">
                                                    <input id="enddate_radio" name="contract_type"
                                                        class="custom-control-input" type="radio" value="enddate">
                                                    <label for="enddate_radio" class="custom-control-label">الكمية
                                                        المستهدفة
                                                        <span class="required">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-body row " style="margin-top: 20px">
                                    <div class="form-group col-md-12">
                                        <input type="number" id="end_date" class="form-control" name="end_date"
                                            placeholder="ادخل قيمة موجبة ">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- المرفقات -->
                    <div class="mt-2">

                        <div class="form-group">
                            <label for=""> الملاحظات</label>
                            <textarea class="form-control" id="notes" name="notes"></textarea>
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
        document.getElementById('addRowButton').addEventListener('click', function() {
            const template = document.getElementById('newRow');
            const tbody = template.parentNode;
            const newRow = template.cloneNode(true);
            newRow.style.display = '';
            newRow.id = '';
            tbody.appendChild(newRow);
        });
    </script>
    <script>
        document.getElementById('showFormButton').addEventListener('click', function() {
            document.getElementById('additionalForm').style.display = 'block';
        });
    </script>
    <script>
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set duration as default
            document.getElementById('duration_radio').checked = true;
            document.getElementById('enddate_radio').checked = false;

            // Initial toggle of fields
            toggleFields();

            // Add event listeners to radio buttons
            document.getElementById('duration_radio').addEventListener('change', toggleFields);
            document.getElementById('enddate_radio').addEventListener('change', toggleFields);
        });

        function toggleFields() {
            const durationRadio = document.getElementById('duration_radio');
            const endDateRadio = document.getElementById('enddate_radio');

            // Get all duration inputs
            const durationInputs = document.querySelectorAll('#duration-inputs input, #duration-inputs select');
            const endDateInput = document.getElementById('end_date');

            if (durationRadio.checked) {
                // Enable duration inputs, disable end date
                durationInputs.forEach(input => {
                    input.removeAttribute('disabled');
                    input.style.backgroundColor = '#ffffff';
                });
                endDateInput.setAttribute('disabled', 'disabled');
                endDateInput.style.backgroundColor = '#e9ecef';
                endDateInput.value = '';
            } else {
                // Enable end date, disable duration inputs
                durationInputs.forEach(input => {
                    input.setAttribute('disabled', 'disabled');
                    input.style.backgroundColor = '#e9ecef';
                });
                endDateInput.removeAttribute('disabled');
                endDateInput.style.backgroundColor = '#ffffff';
            }
        }
    </script>
    <style>
        .form-control {
            width: 100%;
        }
    </style>
@endsection
