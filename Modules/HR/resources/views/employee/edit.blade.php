@extends('master')

@section('title')
    تعديل موظف
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أضافة موظف </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">تعديل
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">

        <form action="{{ route('employee.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
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
                            <a href="{{ route('employee.index') }}" class="btn btn-outline-danger">
                                <i class="fa fa-ban"></i>الغاء
                            </a>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-save"></i>تحديث
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white font-weight-bold p-1">
                    معلومات الموظف
                </div>
                <div class="card-body">

                    <div class="form-row">

                        <div class="form-group col-md-4">
                            <label for="first_name">الاسم الأول <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" placeholder="أدخل الاسم الأول"
                                value="{{ old('first_name', $employee->first_name) }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="middle_name">الاسم الأوسط</label>
                            <input type="text" name="middle_name" class="form-control" placeholder="أدخل الاسم الأوسط"
                                value="{{ old('middle_name', $employee->middle_name) }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="last_name">اللقب</label>
                            <input type="text" name="nickname" class="form-control" placeholder="أدخل اللقب"
                                value="{{ old('nickname', $employee->nickname) }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="employee_photo">صورة الموظف</label>
                            <div class="custom-file">
                                <input type="file" name="employee_photo" class="custom-file-input">
                                <label class="custom-file-label" for="employee_photo">اختر صورة الموظف</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="notes">الملاحظات</label>
                            <textarea name="notes" class="form-control" id="notes" rows="2" placeholder="أدخل ملاحظات">{{ old('notes', $employee->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="form-row">

                        <div class="form-group col-md-4">
                            <label for="email">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" id="email"
                                placeholder="example@email.com" value="{{ old('email', $employee->email) }}">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="employee_type">نوع الموظف</label>
                            <select name="employee_type" class="form-control">
                                <option value="1"
                                    {{ old('employee_type', $employee->employee_type) == 1 ? 'selected' : '' }}>موظف</option>
                                <option value="2"
                                    {{ old('employee_type' . $employee->employee_type) == 2 ? 'selected' : '' }}>مستخدم
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="status">الحالة <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" id="status" required>
                                <option value="1" {{ old('status', $employee->status) == 1 ? 'selected' : '' }}>نشط
                                </option>
                                <option value="2" {{ old('status', $employee->status) == 2 ? 'selected' : '' }}>غير نشط
                                </option>
                            </select>
                        </div>

                    </div>

                    <div class="form-row pb-2">
                        <div class="form-check form-check-inline ml-4">
                            <input name="allow_system_access" class="form-check-input" type="checkbox">
                            <label class="form-check-label" for="allow_access">السماح بالدخول الى النظام</label>
                        </div>
                        <div class="form-check form-check-inline ml-4">
                            <input name="send_credentials" class="form-check-input" type="checkbox">
                            <label class="form-check-label" for="send_data">إرسال بيانات الدخول عبر البريد
                                الإلكتروني</label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="id_number">لغة العرض </label>
                            <select name="language" class="form-control">
                                <option value="1" {{ old('language', $employee->language) == 1 ? 'selected' : '' }}>
                                    العربيه</option>
                                <option value="2" {{ old('language', $employee->language) == 2 ? 'selected' : '' }}>
                                    انجليزيه</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="nationality">الدور الوظيفي</label>
                            <select name="Job_role_id" class="form-control">
                                @foreach ($job_roles as $job_role)
                                    <option value="{{ $job_role->id }}"
                                        {{ old('Job_role_id') == $job_role->id ? 'selected' : '' }}>
                                        {{ $job_role->role_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="gender">الفروع المسموح الدخول بها</label>
                            <select name="branch_id" class="form-control">
                                @foreach ($branches as $branche)
                                    <option value="{{ $branche->id }}">{{ $branche->name ?? '' }}</option>
                                @endforeach


                            </select>
                        </div>


                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white font-weight-bold p-1">
                    معلومات شخصية
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="dob">تاريخ الميلاد <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date_of_birth"
                                value="{{ old('date_of_birth', $employee->date_of_birth) }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="type">النوع</label>
                            <select class="form-control" name="gender">
                                <option value="1" {{ old('gender', $employee->gender) == 1 ? 'selected' : '' }}>ذكر
                                </option>
                                <option value="2" {{ old('gender', $employee->gender) == 2 ? 'selected' : '' }}>انثى
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nationalityStatus">حالة المواطنة</label>
                            <select class="form-control" name="nationality_status">
                                <option selected disabled>من فضلك اختر</option>
                                <option value="1"
                                    {{ old('nationality_status', $employee->nationality_status) == 1 ? 'selected' : '' }}>
                                    مواطن</option>
                                <option value="2"
                                    {{ old('nationality_status', $employee->nationality_status) == 2 ? 'selected' : '' }}>
                                    مقيم</option>
                                <option value="3"
                                    {{ old('nationality_status', $employee->nationality_status) == 3 ? 'selected' : '' }}>
                                    زائر</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="country">البلد <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="country">
                                <option value="" disabled selected>اختر البلد</option>
                                @php
                                    $arabic_countries = [
                                        1 => 'المملكة العربية السعودية',
                                        3 => 'الكويت',
                                        18 => 'السودان',
                                        4 => 'قطر',
                                        13 => 'اليمن',
                                        5 => 'البحرين',
                                        6 => 'سلطنة عمان',
                                        7 => 'مصر',
                                        8 => 'الأردن',
                                        9 => 'لبنان',
                                        10 => 'سوريا',
                                        11 => 'العراق',
                                        12 => 'فلسطين',
                                        2 => 'الإمارات العربية المتحدة',
                                        14 => 'الجزائر',
                                        15 => 'المغرب',
                                        16 => 'تونس',
                                        17 => 'ليبيا',
                                        19 => 'موريتانيا',
                                        20 => 'جيبوتي',
                                        21 => 'الصومال',
                                        22 => 'جزر القمر',
                                    ];
                                @endphp
                                @foreach ($arabic_countries as $key => $country)
                                    <option value="{{ $key }}"
                                        {{ old('country', $employee->country) == $key ? 'selected' : '' }}>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white font-weight-bold p-1">
                    المجموعات والاتجاهات المسموحة
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="groups-directions-table">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40%">المجموعة</th>
                                    <th width="40%">الاتجاه</th>
                                    <th width="20%">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($employeeGroups->count() > 0)
                                    @foreach ($employeeGroups as $employeeGroup)
                                        <tr>
                                            <td>
                                                <select name="groups[]" class="form-control select2">
                                                    <option value="">اختر مجموعة</option>
                                                    @foreach ($groups as $group)
                                                        <option value="{{ $group->id }}"
                                                            {{ $employeeGroup->group_id == $group->id ? 'selected' : '' }}>
                                                            {{ $group->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="directions[]" class="form-control select2">
                                                    <option value="">اختر اتجاه</option>
                                                    @foreach ($directions as $direction)
                                                        <option value="{{ $direction->id }}"
                                                            {{ $employeeGroup->direction_id == $direction->id ? 'selected' : '' }}>
                                                            {{ $direction->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                                    <i class="fa fa-trash"></i> حذف
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <select name="groups[]" class="form-control select2">
                                                <option value="">اختر مجموعة</option>
                                                @foreach ($groups as $group)
                                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="directions[]" class="form-control select2">
                                                <option value="">اختر اتجاه</option>
                                                @foreach ($directions as $direction)
                                                    <option value="{{ $direction->id }}">{{ $direction->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i class="fa fa-trash"></i> حذف
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-success mt-2" id="add-row">
                        <i class="fa fa-plus"></i> إضافة صف جديد
                    </button>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-primary text-white font-weight-bold p-1">
                    معلومات تواصل
                </div>
                <div class="card-body">
                    <div class="form-row">

                        <div class="form-group col-md-6">
                            <label for="phone">رقم الجوال</label>
                            <input type="text" class="form-control" name="mobile_number"
                                value="{{ old('mobile_number', $employee->mobile_number) }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="phone">رقم الهاتف</label>
                            <input type="text" class="form-control" name="phone_number"
                                value="{{ old('phone_number', $employee->phone_number) }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="personalEmail">البريد الإلكتروني الشخصي</label>
                            <input type="email" class="form-control" name="personal_email"
                                value="{{ old('personal_email', $employee->personal_email) }}">
                        </div>

                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white font-weight-bold p-1">
                    العنوان الحالي
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="address1">عنوان 1</label>
                            <input type="text" class="form-control" name="current_address_1"
                                value="{{ old('current_address_1', $employee->current_address_1) }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="address2">عنوان 2</label>
                            <input type="text" class="form-control" name="current_address_2"
                                value="{{ old('current_address_2', $employee->current_address_2) }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="city">المدينة</label>
                            <input type="text" class="form-control" name="city"
                                value="{{ old('city', $employee->city) }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="region">المنطقة</label>
                            <input type="text" class="form-control" name="region"
                                value="{{ old('region', $employee->region) }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="postalCode">الرمز البريدي</label>
                            <input type="text" class="form-control" name="postal_code"
                                value="{{ old('postal_code', $employee->postal_code) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- قسم معلومات وظيفية -->
            <div class="card">
                <div class="card-header bg-primary text-white font-weight-bold p-1">
                    معلومات وظيفة
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="jobTitle">المسمى الوظيفي</label>
                            <select class="form-control" name="job_title_id">
                                <option selected value="">اختر المسمى الوظيفي</option>
                                @foreach ($jobTitles as $jobTitle)
                                    <option value="{{ $jobTitle->id }}"
                                        {{ old('job_title_id', $employee->job_title_id) == $jobTitle->id ? 'selected' : '' }}>
                                        {{ $jobTitle->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="department">القسم</label>
                            <select class="form-control" name="department_id">
                                <option selected value="">اختر قسم</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="">المستوى الوظيفي</label>
                            <select class="form-control" name="job_level_id">
                                <option selected value="">اختر المستوى الوظيفي</option>
                                @foreach ($jobLevels as $jobLevel)
                                    <option value="{{ $jobLevel->id }}"
                                        {{ old('job_level_id', $employee->job_level_id) == $jobLevel->id ? 'selected' : '' }}>
                                        {{ $jobLevel->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="">نوع وظيفة</label>
                            <select class="form-control" name="job_type_id">
                                <option selected value="">اختر نوع وظيفة</option>
                                @foreach ($jobTypes as $jobType)
                                    <option value="{{ $jobType->id }}"
                                        {{ old('job_type_id', $employee->job_type_id) == $jobType->id ? 'selected' : '' }}>
                                        {{ $jobType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="branch">فرع <span class="text-danger">*</span></label>
                            <select class="form-control" name="branch_id">
                                <option selected value="">اختر فرع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id', $employee->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="manager">المدير المباشر</label>
                            <select class="form-control" name="direct_manager_id">
                                <option selected value="">اختر موظف</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ old('direct_manager_id', $employee->direct_manager_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="">تاريخ الالتحاق</label>
                            <input type="date" class="form-control" name="hire_date"
                                value="{{ old('hire_date', $employee->hire_date) }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="">ورديــة</label>
                            <select class="form-control" name="shift_id">
                                <option selected value="">اختر وردية</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}"
                                        {{ old('shift_id', $employee->shift_id) == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- الخيارات المالية -->
                        <div
                            class="form-group col-md-12 gradient-background d-flex justify-content-start align-items-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="defaultDate" checked>
                                <label class="form-check-label" for="defaultDate">استخدام التاريخ المالي الافتراضي</label>
                            </div>
                            <div class="form-check form-check-inline ml-4">
                                <input class="form-check-input" type="radio" id="customDate">
                                <label class="form-check-label" for="customDate">تاريخ مالي مخصص</label>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="month">الشهر</label>
                            {{ $employee->custom_financial_month }}
                            <select class="custom-select" id="month" name="custom_financial_month">
                                <option value="" disabled selected>اختر الشهر</option>
                                @php
                                    $months = [
                                        1 => 'يناير',
                                        2 => 'فبراير',
                                        3 => 'مارس',
                                        4 => 'أبريل',
                                        5 => 'مايو',
                                        6 => 'يونيو',
                                        7 => 'يوليو',
                                        8 => 'أغسطس',
                                        9 => 'سبتمبر',
                                        10 => 'أكتوبر',
                                        11 => 'نوفمبر',
                                        12 => 'ديسمبر',
                                    ];
                                @endphp

                                @foreach ($months as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('custom_financial_month', $employee->custom_financial_month) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="day">يوم</label>
                            {{ $employee->custom_financial_day }}
                            <select class="custom-select" id="day" name="custom_financial_day">
                                <option value="" disabled selected>اختر اليوم</option>
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}"
                                        {{ old('custom_financial_day', $employee->custom_financial_day) == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header text-center bg-primary text-white p-1">
                    معلومات الحضور
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="attendancePolicy">سياسة الإجازات</label>
                            <select class="custom-select" id="attendancePolicy">
                                <option selected>اختر سياسة الإجازات</option>
                                <!-- إضافة الخيارات الأخرى -->
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="attendanceSettings">معدلات الحضور</label>
                            <select class="custom-select" id="attendanceSettings">
                                <option selected>اختر قيد الحضور</option>
                                <!-- إضافة الخيارات الأخرى -->
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="attendanceRoster">ورديات الحضور</label>
                            <select class="custom-select" id="attendanceRoster">
                                <option selected>اختر وردية الحضور</option>
                                <!-- إضافة الخيارات الأخرى -->
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="holidayList">قوائم العطلات</label>
                            <select class="custom-select" id="holidayList">
                                <option selected>اختر قائمة العطلات</option>
                                <!-- إضافة الخيارات الأخرى -->
                            </select>
                        </div>



                    </div>
                </div>
            </div>

            <!-- زر الإرسال -->

        </form>

    </div>


    <script>
        $(document).ready(function() {
            // إضافة صف جديد
            $('#add-row').click(function() {
                var newRow = `
                <tr>
                    <td>
                        <select name="groups[]" class="form-control select2">
                            <option value="">اختر مجموعة</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="directions[]" class="form-control select2">
                            <option value="">اختر اتجاه</option>
                            @foreach ($directions as $direction)
                                <option value="{{ $direction->id }}">{{ $direction->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            <i class="fa fa-trash"></i> حذف
                        </button>
                    </td>
                </tr>
            `;
                $('#groups-directions-table tbody').append(newRow);
                $('.select2').select2(); // إعادة تهيئة select2 للعناصر الجديدة
            });

            // حذف صف
            $(document).on('click', '.remove-row', function() {
                if ($('#groups-directions-table tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    alert('يجب أن يبقى صف واحد على الأقل');
                }
            });
        });
    </script>

@endsection
