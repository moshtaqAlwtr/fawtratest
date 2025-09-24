@extends('master')


@section('title')
    تتبع الوقت
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-selection {
            height: 38px !important;
            padding: 5px !important;
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تتبع الوقت</h2>
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
    <div class="card ">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center" style="gap: 10px">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTimeModal">
                                    <i class="fas fa-plus"></i> إضافة جديدة
                                </button>
                                <button class="btn btn-secondary">
                                    <i class="fas fa-file-import"></i> استيراد
                                </button>
                                <a href="{{ route('SittingTrackTime.create') }}" class="btn btn-secondary">
                                    <i class="fas fa-cog"></i>
                                </a>
                                <div class="btn-group ms-2">
                                    <a href="{{ route('reports.time_tracking.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-pie-chart"></i>
                                    </a>

                                </div>
                            </div>
                            <div class="d-flex align-items-center col-3">
                                <select class="form-select select2" id="employee_select">
                                    <option value="">ساعات عملي</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 10px">
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary" data-bs-toggle="collapse"
                                        href="#collapseExample" role="button" aria-expanded="false"
                                        aria-controls="collapseExample">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                        class="form-control datepicker" id="dateDropdown" readonly
                                        placeholder="اختر التاريخ" data-bs-toggle="datepicker">
                                        <i class="fas fa-calendar"></i> Friday 27 Dec
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <div class="btn-group">
                    <a href="{{ route('TrackTime.show') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-th"></i>
                    </a>
                    <a href="{{ route('TrackTime.show') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i>
                    </a>
                </div>
            </div>

            <div class="d-flex align-items-center mb-3">
                <button class="btn btn-link text-decoration-none" id="prevWeek">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="d-flex flex-grow-1 text-center">
                    <div class="flex-grow-1 border-end p-2" id="day0"></div>
                    <div class="flex-grow-1 border-end p-2" id="day1"></div>
                    <div class="flex-grow-1 border-end p-2" id="day2"></div>
                    <div class="flex-grow-1 border-end p-2" id="day3"></div>
                    <div class="flex-grow-1 border-end p-2" id="day4"></div>
                    <div class="flex-grow-1 border-end p-2" id="day5"></div>
                    <div class="flex-grow-1 p-2" id="day6"></div>
                </div>
                <button class="btn btn-link text-decoration-none" id="nextWeek">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="p-2 text-center">
                    <div class="text-muted small">الإجمالي</div>
                    <div class="fw-bold">00:00</div>
                </div>
            </div>

            <div class="text-center text-muted py-5">
                لا يوجد سجلات لهذا اليوم، اضغط زر إضافة سجل جديد لإضافة سجل جديد
            </div>

            <div class="text-start mt-3">
                <div>إجمالي اليوم: 00:00</div>
            </div>
        </div>
    </div>

    <div class="collapse" id="collapseExample">
        <div class="card card-body">
            <div class="form-body row">
                <div class="form-group col-md-3">
                    <label for="feedback2" class=""> عرض ب</label>
                    <select name="project_id" class="form-control select2">
                        <option value=""> المشروع </option>
                        <option value="1"> النشاط </option>
                        <option value="2"> الموظف </option>
                        <option value="3"> التاريخ </option>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="feedback2" class=""> الصيغة</label>
                    <select id="feedback2" class="form-control select2">
                        <option value="">اختر النشاط </option>
                        <option value="1">نشط </option>
                        <option value="0">غير نشط</option>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="employee_filter" class="">الموظف</label>
                    <select id="employee_filter" class="form-control select2">
                        <option value="">اختر الموظف </option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="project_filter" class="">المشروع</label>
                    <select id="project_filter" class="form-control select2">
                        <option value="">اختر المشروع</option>

                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="activity_filter" class="">النشاط</label>
                    <select id="activity_filter" class="form-control select2">
                        <option value="">اختر النشاط</option>

                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="date_filter" class="">التاريخ</label>
                    <input type="text" id="date_filter" class="form-control datepicker" placeholder="اختر التاريخ">
                </div>

                <div class="form-group col-md-3">
                    <label for="notes_filter" class="">الملاحظات</label>
                    <input type="text" id="notes_filter" class="form-control" placeholder="بحث في الملاحظات">
                </div>

                <div class="form-group col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addTimeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 py-3">
                    <h6 class="modal-title fs-5">إضافة وقت</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="display: none;"></button>
                    <button type="button" class="btn text-danger" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-6">
                                <label class="mb-2">النشاط</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="mb-2">المشروع</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-4">
                            <label class="mb-2">الوقت ؟</label>
                            <input type="text" class="form-control text-center fs-2 fw-light" value="0:00" readonly
                                style="height: 80px;">
                        </div>
                        <div class="col-8">
                            <label class="mb-2">الملاحظات</label>
                            <textarea class="form-control" style="height: 80px; resize: none;"></textarea>
                        </div>

                    </div>
                    <div class="mb-4">
                        <label class="mb-2">موظف</label>
                        <section class="d-flex flex-wrap gap-2 select2">
                            @foreach ($employees as $employee)
                                <option value=" {{ $employee->id }}"> {{ $employee->full_name }} </option>
                            @endforeach
                        </section>
                    </div>
                </div>
                <div class="modal-footer border-0 py-3">
                    <div class="d-flex justify-content-between w-100">
                        <button type="button" class="btn btn-success">إضافة</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <div>
                            <button type="button" class="btn btn-danger">
                                <i class="fas fa-power-off"></i>
                                <span id="timerDisplay">0:00:00</span>
                            </button>
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-play"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('scripts')
    <script src="{{ URL::asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/ar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة مكتبة التقويم
            flatpickr("#dateDropdown", {
                locale: "ar",
                dateFormat: "Y-m-d",
                defaultDate: "today",
                disableMobile: "true",
                theme: "material_blue",
                onChange: function(selectedDates, dateStr, instance) {
                    // يمكنك إضافة الإجراءات التي تريدها عند تغيير التاريخ هنا
                    console.log('Selected date:', dateStr);
                }
            });

            // تهيئة عناصر Bootstrap
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            });

            // تعريف المتغيرات العامة
            let currentDate = new Date();
            const weekDays = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
            const months = ['يناير', 'فبراير', 'مارس', 'إبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر',
                'أكتوبر', 'نوفمبر', 'ديسمبر'
            ];

            // دالة لتنسيق التاريخ بالعربية
            function formatArabicDate(date) {
                const day = weekDays[date.getDay()];
                const dayOfMonth = date.getDate();
                const month = months[date.getMonth()];
                return `${day} ${dayOfMonth} ${month}`;
            }

            // دالة لعرض أيام الأسبوع
            function displayWeek(startDate) {
                for (let i = 0; i < 7; i++) {
                    const date = new Date(startDate);
                    date.setDate(startDate.getDate() + i);
                    document.getElementById(`day${i}`).textContent = formatArabicDate(date);

                    // تمييز اليوم الحالي
                    if (date.toDateString() === new Date().toDateString()) {
                        document.getElementById(`day${i}`).classList.add('bg-light');
                    } else {
                        document.getElementById(`day${i}`).classList.remove('bg-white');
                    }
                }
            }

            // الحصول على بداية الأسبوع الحالي
            function getStartOfWeek(date) {
                const diff = date.getDate() - date.getDay();
                return new Date(date.setDate(diff));
            }

            // تحديث عرض الأسبوع الحالي
            let currentWeekStart = getStartOfWeek(currentDate);
            displayWeek(currentWeekStart);

            // معالجة النقر على زر الأسبوع السابق
            document.getElementById('prevWeek').addEventListener('click', function() {
                currentWeekStart.setDate(currentWeekStart.getDate() - 7);
                displayWeek(currentWeekStart);
            });

            // معالجة النقر على زر الأسبوع التالي
            document.getElementById('nextWeek').addEventListener('click', function() {
                currentWeekStart.setDate(currentWeekStart.getDate() + 7);
                displayWeek(currentWeekStart);
            });

            // متغيرات المؤقت
            let timerInterval;
            let seconds = 0;
            let isRunning = false;

            // دالة تحديث عرض المؤقت
            function updateTimerDisplay() {
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;
                document.getElementById('timerDisplay').textContent =
                    `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }

            // زر التشغيل/الإيقاف
            document.querySelector('.btn-primary').addEventListener('click', function() {
                if (!isRunning) {
                    timerInterval = setInterval(() => {
                        seconds++;
                        updateTimerDisplay();
                    }, 1000);
                    this.innerHTML = '<i class="fas fa-pause"></i>';
                } else {
                    clearInterval(timerInterval);
                    this.innerHTML = '<i class="fas fa-play"></i>';
                }
                isRunning = !isRunning;
            });

            // زر إعادة الضبط
            document.querySelector('.btn-danger').addEventListener('click', function() {
                clearInterval(timerInterval);
                seconds = 0;
                updateTimerDisplay();
                document.querySelector('.btn-primary').innerHTML = '<i class="fas fa-play"></i>';
                isRunning = false;
            });

            // تهيئة Select2
            $('.select2').select2({
                placeholder: "ساعات عملي ",
                allowClear: true,
                dir: "rtl",
                language: "ar"
            });
        });
    </script>
@endsection
