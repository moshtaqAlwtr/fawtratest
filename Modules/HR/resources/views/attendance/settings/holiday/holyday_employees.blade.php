@extends('master')

@section('title', 'قوائم العطلات')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">{{ $holiday_list->name }}</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item active">تحصيص</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">

        <!-- Form Actions -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>
                    <div>
                        <a href="{{ route('holiday_lists.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i> إلغاء
                        </a>
                        <button type="submit" form="addAttendanceForm" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i> حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.alerts.success')
        @include('layouts.alerts.error')

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Section -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('holiday_lists.add_holyday_employees', $holiday_list->id) }}" method="POST" id="addAttendanceForm">
                    @csrf
                    <p style="background-color: #f8f8f8; padding: 10px"><strong>قم بالتخصيص للموظفين</strong></p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="attendance-date" class="form-label">الاسم <span style="color: red">*</span></label>
                            <input type="text" name="name" class="form-control" disabled value="{{ old('name', $holiday_list->name) }}">
                        </div>
                    </div>

                    <hr>

                    <!-- Attendance Status -->
                    <div class="mb-3">
                        <p class="mb-2"><strong>إختيار المعايير</strong></p>
                        <div class="row g-3">
                            <!-- Present -->
                            <div class="col-12">
                                <ul class="list-unstyled mb-0">
                                    <li class="d-inline-block mr-5">
                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="use_rules" id="customRadio1" {{ old('use_rules') == 'rules' ? 'checked' : '' }} value="rules">
                                                <label class="custom-control-label" for="customRadio1">إختيار القواعد <span style="color: red">*</span></label>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="d-inline-block mr-5">
                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="use_rules" id="customRadio2" {{ old('use_rules') == 'employees' ? 'checked' : '' }} value="employees">
                                                <label class="custom-control-label" for="customRadio2">إختيار الموظفين <span style="color: red">*</span></label>
                                            </div>
                                        </fieldset>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div id="employee" style="display: {{ old('use_rules') == 'employees' ? 'block' : 'none' }}">
                        <p style="background-color: #f8f8f8; padding: 10px"><strong>اختيار الموظفين</strong></p>

                        <div class="row g-3 mb-3">

                            <div class="col-md-6">
                                <label for="" selected>اختر الموظفين</label>
                                <select id="employee-select-employees" name="employee_id[]" class="form-control select2" multiple="multiple">
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ in_array($employee->id, old('employee_id', [])) ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                <small class="text-danger" id="basic-default-name-error" class="error">
                                    {{ $message }}
                                </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div id="rules"style="display: {{ old('use_rules') == 'rules' ? 'block' : 'none' }}">
                        <p style="background-color: #f8f8f8; padding: 10px"><strong>اختيار القواعد</strong></p>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for=""></label>
                                <select id="branch" name="branch_id" class="form-control select2">
                                    <option value="" disabled selected>اختر فرع</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for=""></label>
                                <select id="department" name="department_id" class="form-control select2">
                                    <option value="" disabled selected>اختر قسم</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for=""></label>
                                <select id="job_title" name="job_title_id" class="form-control select2">
                                    <option value="" disabled selected>اختر المسمى الوظيفي</option>
                                    @foreach ($job_titles as $job_title)
                                        <option value="{{ $job_title->id }}" {{ old('job_title_id') == $job_title->id ? 'selected' : '' }}>{{ $job_title->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="">اختر الموظفين المستبعدين</label>
                                <select id="employee-select-rules" name="employee_id[]" class="form-control select2" multiple="multiple">
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ in_array($employee->id, old('employee_id', [])) ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                <small class="text-danger" id="basic-default-name-error" class="error">
                                    {{ $message }}
                                </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('input[name="use_rules"]');
            const employeeSection = document.getElementById('employee');
            const rulesSection = document.getElementById('rules');
            const employeeInputs = employeeSection.querySelectorAll('input, select, textarea');
            const rulesInputs = rulesSection.querySelectorAll('input, select, textarea');

            function toggleInputs(section, inputs, isEnabled) {
                inputs.forEach(input => {
                    if (isEnabled) {
                        input.removeAttribute('disabled');
                    } else {
                        input.setAttribute('disabled', 'disabled');
                    }
                });
            }

            radios.forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.id === 'customRadio1') {
                        employeeSection.style.display = 'none';
                        rulesSection.style.display = 'block';

                        toggleInputs(employeeSection, employeeInputs, false);
                        toggleInputs(rulesSection, rulesInputs, true);
                    } else if (this.id === 'customRadio2') {
                        employeeSection.style.display = 'block';
                        rulesSection.style.display = 'none';

                        toggleInputs(employeeSection, employeeInputs, true);
                        toggleInputs(rulesSection, rulesInputs, false);
                    }

                    $('#employee-select-employees').select2({
                        allowClear: true
                    });

                    $('#employee-select-rules').select2({
                        allowClear: true
                    });
                });
            });

            // عند تحميل الصفحة، تحقق من الحالة الحالية
            if (document.getElementById('customRadio1').checked) {
                toggleInputs(employeeSection, employeeInputs, false);
                toggleInputs(rulesSection, rulesInputs, true);
            } else if (document.getElementById('customRadio2').checked) {
                toggleInputs(employeeSection, employeeInputs, true);
                toggleInputs(rulesSection, rulesInputs, false);
            }
        });
    </script>
@endsection
