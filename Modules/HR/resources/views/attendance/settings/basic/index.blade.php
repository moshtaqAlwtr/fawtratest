@extends('master')

@section('title', 'الأعدادات الأساسية')

@section('css')
    <style>
        .employee-select {
            display: none;
        }
    </style>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الأعدادات الأساسية</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item active">إضافة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>
                <div>
                    <a href="{{ route('attendance.settings.index') }}" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i> إلغاء
                    </a>
                    <button type="submit" form="SettingsBasic" class="btn btn-outline-primary">
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

    <div class="card mt-5">
        <div class="card-body">
            <h4 class="mb-2 p-1" style="background: #f8f8f8">إعدادات الحضور</h4>
            <form id="SettingsBasic" method="POST" action="{{ route('settings_basic.update') }}">
                @csrf
                <div class="mb-2 row">
                    <div class="col-sm-6">
                        <label for="monthStart" class="form-label">شهر بداية السنة المالية <span class="text-danger">*</span></label>
                        <select id="monthStart" class="form-control" name="start_month">
                            @foreach (range(1, 12) as $month)
                                <option value="{{ $month }}" {{ isset($attendanceSettings) && $month == $attendanceSettings->start_month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($month)->locale('ar')->monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="dayStart" class="form-label">يوم بداية السنة المالية <span class="text-danger">*</span></label>
                        <select id="dayStart" class="form-control" name="start_day" {{ old('start_day', $attendanceSettings->start_day) ? 'value="' . old('start_day') . '"' : '' }}></select>
                    </div>
                </div>

                <p><small>الوردية الثانية :</small></p>
                <div class="mb-2">
                    <div class="custom-control custom-switch custom-switch-success custom-control-inline">
                        <input type="checkbox" class="custom-control-input" id="enableShiftTwo" name="allow_second_shift" {{ old('allow_second_shift', $attendanceSettings->allow_second_shift == 1) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="enableShiftTwo"></label>
                        <span class="switch-label">إتاحة الوردية الثانية للموظفين</span>
                    </div>
                </div>

                <h4 class="mb-2 p-1" style="background: #f8f8f8">إعدادات طلب الإجازة</h4>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="mb-2">
                            <div class="custom-control custom-switch custom-switch-success custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="allowBackdatedRequests" name="allow_backdated_requests" {{ old('allow_backdated_requests', $attendanceSettings->allow_backdated_requests == 1) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="allowBackdatedRequests"></label>
                                <span class="switch-label">السماح بإدخال طلبات الإجازة لتواريخ سابقة</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <p><small>الموافقة او رفض طلبات الأجازة بأي من :</small></p>
                    </div>

                    <div class="col-sm-6">
                        <div class="mb-2">
                            <div class="custom-control custom-switch custom-switch-success custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="directManagersApproval" name="direct_manager_approval" {{ old('direct_manager_approval', $attendanceSettings->direct_manager_approval == 1) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="directManagersApproval"></label>
                                <span class="switch-label">المديرين المباشرين</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="mb-2">
                            <div class="custom-control custom-switch custom-switch-success custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="departmentManagersApproval" name="department_manager_approval" {{ old('department_manager_approval', $attendanceSettings->department_manager_approval == 1) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="departmentManagersApproval"></label>
                                <span class="switch-label">مديري <a href="{{ route('department.index') }}" target="_blank">الأقسام <i class="fa fa-link"></i></a></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="mb-2">
                            <div class="custom-control custom-switch custom-switch-success custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="employeesApproval" name="employees_approval" {{ old('employees_approval', $attendanceSettings->employees_approval == 1) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="employeesApproval"></label>
                                <span class="switch-label">الموظفين</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6"></div>

                    @php
                        $selectedEmployees = $attendanceSettings->employees->pluck('id')->toArray();
                    @endphp
                    <div class="col-md-6 employee-select">
                        <label for="employee_id">اختر الموظفين:</label>
                        <select name="employee_id[]" id="employee_id" class="form-control select2" multiple="multiple">
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ in_array($employee->id, $selectedEmployees) ? 'selected' : '' }}>
                                    {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

            </form>
        </div>
    </div>

@endsection

@section('scripts')

    <script>
        // JavaScript لإعادة ضبط الأيام بناءً على الشهر والسنة
        document.addEventListener('DOMContentLoaded', function () {
            const monthSelect = document.getElementById('monthStart');
            const daySelect = document.getElementById('dayStart');

            // دالة لتحديد عدد الأيام في الشهر
            function getDaysInMonth(month, year) {
                return new Date(year, month, 0).getDate(); // تعيد عدد الأيام في الشهر
            }

            // عند تحميل الصفحة، تحديد اليوم المختار مسبقًا إذا كان موجودًا
            function updateDays() {
                const year = new Date().getFullYear();
                const month = parseInt(monthSelect.value);
                const days = getDaysInMonth(month, year);

                daySelect.innerHTML = '';

                for (let day = 1; day <= days; day++) {
                    const option = document.createElement('option');
                    option.value = day;
                    option.textContent = day;

                    // تحديد اليوم المختار مسبقًا
                    if (day === {{ $attendanceSettings->start_day ?? 'null' }}) {
                        option.selected = true;
                    }

                    daySelect.appendChild(option);
                }
            }

            // استدعاء الدالة عند تغيير الشهر
            monthSelect.addEventListener('change', updateDays);

            // تهيئة الأيام عند تحميل الصفحة
            updateDays();
        });

        //-------------------------------------------------

        $(document).ready(function () {
            // عرض الحقل إذا كان "الموظفين" محددًا
            if ($("#employeesApproval").is(":checked")) {
                $(".employee-select").show();
            }

            // إظهار/إخفاء الحقل عند التبديل
            $("#employeesApproval").on("change", function () {
                $(".employee-select").toggle($(this).is(":checked"));
            });
        });

    </script>

@endsection
