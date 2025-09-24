@extends('master')

@section('title')
    الورديات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الورديات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>
                    <div>
                        <button form="shift_form" type="submit" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i> تحديث
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-header"><strong>معلومات وردية</strong></div>
            <div class="card-body">
                <form id="shift_form" class="form" action="{{ route('shift_management.update', $shift->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">الاسم <span style="color: red">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $shift->name) }}" required>
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="">النوع <span style="color: red">*</span></label>
                            @php
                                $savedType = ($shift->type == 1) ? 'basic' : 'advanced';
                            @endphp
                            <select class="form-control" name="type" data-saved-value="{{ old('type', $savedType) }}" required>
                                <option value="basic" {{ old('type', $savedType) == 'basic' ? 'selected' : '' }}>أساسي</option>
                                <option value="advanced" {{ old('type', $savedType) == 'advanced' ? 'selected' : '' }}>متقدم</option>
                            </select>
                        </div>
                    </div>
                    <br>

                    <!-- القسم الأساسي -->
                    <div id="basic">
                        <div class="card">
                            <div class="card-header p-1" style="background: #f8f8f8">
                                <strong>أيام العمل</strong>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>يوم</th>
                                            <th style="width: 50%">يوم عمل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $days = [
                                                'sunday' => 'الأحد',
                                                'monday' => 'الإثنين',
                                                'tuesday' => 'الثلاثاء',
                                                'wednesday' => 'الأربعاء',
                                                'thursday' => 'الخميس',
                                                'friday' => 'الجمعة',
                                                'saturday' => 'السبت'
                                            ];
                                            $workingDays = $shift->days->where('working_day', 1)->pluck('day')->toArray();
                                        @endphp

                                        @foreach ($days as $dayKey => $dayName)
                                        <tr>
                                            <td>{{ $dayName }}</td>
                                            <td>
                                                <div class="custom-control custom-switch custom-switch-success mr-2 mb-1">
                                                    <input type="checkbox"
                                                           class="custom-control-input"
                                                           id="{{ $dayKey }}"
                                                           name="days[{{ $dayKey }}][working_day]"
                                                           value="1"
                                                           {{ in_array($dayKey, $workingDays) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="{{ $dayKey }}"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header p-1" style="background: #f8f8f8">
                                <strong>قواعد الحضور</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        // استرجاع بيانات القواعد من اليوم الأول كمرجع
                                        $defaultDay = $shift->days->first();
                                    @endphp

                                    <div class="form-group col-md-6">
                                        <label for="">بداية الوردية <span style="color: red">*</span></label>
                                        <input type="time" name="start_time" class="form-control"
                                               value="{{ old('start_time', $defaultDay->start_time ?? '09:00') }}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">نهاية الوردية <span style="color: red">*</span></label>
                                        <input type="time" name="end_time" class="form-control"
                                               value="{{ old('end_time', $defaultDay->end_time ?? '17:00') }}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">بداية تسجيل الدخول <span style="color: red">*</span></label>
                                        <input type="time" name="login_start_time" class="form-control"
                                               value="{{ old('login_start_time', $defaultDay->login_start_time ?? '07:00') }}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">نهاية تسجيل الدخول <span style="color: red">*</span></label>
                                        <input type="time" name="login_end_time" class="form-control"
                                               value="{{ old('login_end_time', $defaultDay->login_end_time ?? '11:00') }}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">بداية تسجيل الخروج <span style="color: red">*</span></label>
                                        <input type="time" name="logout_start_time" class="form-control"
                                               value="{{ old('logout_start_time', $defaultDay->logout_start_time ?? '15:00') }}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">نهاية تسجيل الخروج <span style="color: red">*</span></label>
                                        <input type="time" name="logout_end_time" class="form-control"
                                               value="{{ old('logout_end_time', $defaultDay->logout_end_time ?? '19:00') }}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">فترة سماح التأخير <span style="color: red">*</span></label>
                                        <input type="number" name="grace_period" class="form-control"
                                               value="{{ old('grace_period', $defaultDay->grace_period ?? 15) }}"
                                               placeholder="دقائق" min="0" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">حساب التأخير <span style="color: red">*</span></label>
                                        <select class="form-control" name="delay_calculation" required>
                                            <option value="1" {{ old('delay_calculation', $defaultDay->delay_calculation ?? '') == 1 ? 'selected' : '' }}>
                                                بعد موعد بداية الوردية + مهلة التأخير
                                            </option>
                                            <option value="2" {{ old('delay_calculation', $defaultDay->delay_calculation ?? '') == 2 ? 'selected' : '' }}>
                                                من موعد بداية الوردية
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- القسم المتقدم -->
                    <div id="advanced" style="display: none">
                        <div class="card">
                            <div class="card-header p-1" style="background: #f8f8f8">
                                <strong>أيام العمل</strong>
                            </div>
                            <div class="card-body">
                                @foreach ($days as $dayKey => $dayName)
                                    @php
                                        // استرجاع بيانات اليوم الحالي من المجموعة
                                        $currentDay = $shift->days->where('day', $dayKey)->first();
                                        $isWorkingDay = in_array($dayKey, $workingDays);
                                    @endphp

                                    <div class="card mb-3" style="background: #f8f8f8">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-6">
                                                    <span><strong>{{ $dayName }}</strong></span>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <div class="custom-control custom-switch custom-switch-success">
                                                        <input type="checkbox"
                                                               class="custom-control-input"
                                                               id="{{ $dayKey }}_enabled"
                                                               name="days[{{ $dayKey }}][working_day]"
                                                               value="1"
                                                               onchange="toggleDayFields('{{ $dayKey }}')"
                                                               {{ $isWorkingDay ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="{{ $dayKey }}_enabled"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="{{ $dayKey }}_fields" class="row">
                                        <div class="form-group col-md-6">
                                            <label for="">بداية الوردية <span style="color: red">*</span></label>
                                            <input type="time" name="days[{{ $dayKey }}][start_time]" class="form-control"
                                                   value="{{ old('days.'.$dayKey.'.start_time', $currentDay->start_time ?? '09:00') }}">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="">نهاية الوردية <span style="color: red">*</span></label>
                                            <input type="time" name="days[{{ $dayKey }}][end_time]" class="form-control"
                                                   value="{{ old('days.'.$dayKey.'.end_time', $currentDay->end_time ?? '17:00') }}">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="">بداية تسجيل الدخول <span style="color: red">*</span></label>
                                            <input type="time" name="days[{{ $dayKey }}][login_start_time]" class="form-control"
                                                   value="{{ old('days.'.$dayKey.'.login_start_time', $currentDay->login_start_time ?? '07:00') }}">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="">نهاية تسجيل الدخول <span style="color: red">*</span></label>
                                            <input type="time" name="days[{{ $dayKey }}][login_end_time]" class="form-control"
                                                   value="{{ old('days.'.$dayKey.'.login_end_time', $currentDay->login_end_time ?? '11:00') }}">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="">بداية تسجيل الخروج <span style="color: red">*</span></label>
                                            <input type="time" name="days[{{ $dayKey }}][logout_start_time]" class="form-control"
                                                   value="{{ old('days.'.$dayKey.'.logout_start_time', $currentDay->logout_start_time ?? '15:00') }}">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="">نهاية تسجيل الخروج <span style="color: red">*</span></label>
                                            <input type="time" name="days[{{ $dayKey }}][logout_end_time]" class="form-control"
                                                   value="{{ old('days.'.$dayKey.'.logout_end_time', $currentDay->logout_end_time ?? '19:00') }}">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="">فترة سماح التأخير <span style="color: red">*</span></label>
                                            <input type="number" name="days[{{ $dayKey }}][grace_period]" class="form-control"
                                                   value="{{ old('days.'.$dayKey.'.grace_period', $currentDay->grace_period ?? 15) }}"
                                                   placeholder="دقائق" min="0">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="">حساب التأخير <span style="color: red">*</span></label>
                                            <select class="form-control" name="days[{{ $dayKey }}][delay_calculation]">
                                                <option value="1" {{ old('days.'.$dayKey.'.delay_calculation', $currentDay->delay_calculation ?? '') == 1 ? 'selected' : '' }}>
                                                    بعد موعد بداية الوردية + مهلة التأخير
                                                </option>
                                                <option value="2" {{ old('days.'.$dayKey.'.delay_calculation', $currentDay->delay_calculation ?? '') == 2 ? 'selected' : '' }}>
                                                    من موعد بداية الوردية
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // العناصر
        const typeSelect = document.querySelector('select[name="type"]');
        const basicSection = document.getElementById('basic');
        const advancedSection = document.getElementById('advanced');

        // تحديث العرض وتمكين/تعطيل المدخلات بناءً على الاختيار
        function toggleSections() {
            if (typeSelect.value === 'basic') {
                basicSection.style.display = 'block';
                advancedSection.style.display = 'none';

                // تمكين المدخلات داخل القسم الأساسي
                enableInputs(basicSection, true);
                // تعطيل المدخلات داخل القسم المتقدم
                enableInputs(advancedSection, false);

            } else if (typeSelect.value === 'advanced') {
                advancedSection.style.display = 'block';
                basicSection.style.display = 'none';

                // تعطيل المدخلات داخل القسم الأساسي
                enableInputs(basicSection, false);
                // تمكين المدخلات داخل القسم المتقدم
                enableInputs(advancedSection, true);

                // تحديث حالة الحقول للأيام المتقدمة
                @php
                    $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                @endphp
                @foreach ($days as $dayKey)
                    toggleDayFields('{{ $dayKey }}');
                @endforeach
            }
        }

        function enableInputs(section, enabled) {
            const inputs = section.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.disabled = !enabled;
            });
        }

        // إضافة الحدث عند تغيير الخيار
        typeSelect.addEventListener('change', toggleSections);

        // تعيين القيمة المحفوظة عند التحميل
        const savedValue = typeSelect.getAttribute('data-saved-value');
        if (savedValue) {
            typeSelect.value = savedValue;
        }

        // استدعاء الوظيفة عند التحميل
        toggleSections();

        // التعامل مع إرسال النموذج
        const form = document.getElementById('shift_form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم تحديث بيانات الوردية',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، حدّث',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    function toggleDayFields(dayKey) {
        const isChecked = document.getElementById(`${dayKey}_enabled`).checked;
        const fieldsContainer = document.getElementById(`${dayKey}_fields`);

        if (fieldsContainer) {
            fieldsContainer.style.display = isChecked ? 'flex' : 'none';
            const inputs = fieldsContainer.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.disabled = !isChecked;
                // إضافة أو إزالة required للحقول المطلوبة
                if (input.type === 'time' || input.name.includes('grace_period') || input.name.includes('delay_calculation')) {
                    if (isChecked) {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                }
            });
        }
    }

    // معالجة رسائل النجاح والخطأ
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'تمت العملية بنجاح',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'حدث خطأ',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
</script>
@endsection
