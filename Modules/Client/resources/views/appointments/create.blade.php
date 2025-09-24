@extends('master')

@section('title')
    اضافة موعد جديد
@stop


@section('head')
    <!-- تضمين ملفات Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-12">
                <form action="{{ route('appointments.store') }}" method="POST" id="appointment-form">
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
                                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-danger">
                                        <i class="fa fa-ban"></i>الغاء
                                    </a>
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fa fa-save"></i>حفظ
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="client_id">العميل</label>
                                        <select class="form-control select2" id="client_id" name="client_id" required>
                                            <option value="">اختر العميل</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->trade_name }}-{{ $client->code??"" }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="date" class="form-control datepicker" id="date" name="date"
                                            value="{{ old('date') ? \Carbon\Carbon::parse(old('date'))->format('Y-m-d') : date('Y-m-d') }}"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="time">الوقت</label>
                                        <input type="time" class="form-control timepicker" id="time" name="time"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="duration">مدة</label>
                                        <input type="text" class="form-control" id="duration" name="duration"
                                            value="00:00" required>
                                    </div>
                                </div>
                            </div>


                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="action_type">نوع الإجراء</label>

                                        </div>
                                        <select class="form-control" id="action_type" name="action_type" required>
                                            <option value="">اختر نوع الإجراء</option>
                                            <option value="add_new" class="text-primary">+ تعديل قائمة الإجراءات</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="proceduresModal" tabindex="-1"
                                aria-labelledby="proceduresModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="proceduresModalLabel">تعديل قائمة الإجراءات</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="procedures-list">
                                                <!-- القائمة ستضاف هنا -->
                                            </div>
                                            <div class="mt-3">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="newProcedureName"
                                                        placeholder="اسم الإجراء الجديد">
                                                    <button class="btn btn-primary" type="button" id="addProcedureBtn">
                                                        <i class="fas fa-plus"></i> إضافة
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">إلغاء</button>
                                            <button type="button" class="btn btn-success" id="saveProcedures">حفظ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">الملاحظات/الشروط</h6>
                                </div>
                                <div class="card-body">
                                    <textarea id="tinyMCE" name="notes" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card">
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="share_with_client"
                                        name="share_with_client">
                                    <label class="custom-control-label" for="share_with_client">مشاركة مع
                                        العميل</label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card">



                    </div>
                    <div class="card">
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="recurring" name="recurring"
                                        onchange="toggleRecurringFields(this)">
                                    <label class="custom-control-label" for="recurring">متكرر</label>
                                </div>

                                <div id="recurring-fields" class="mt-3" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="recurrence_type">التكرار</label>
                                                <select class="form-control" id="recurrence_type" name="recurrence_type">
                                                    <option value="1"
                                                        {{ old('recurrence_type') == 1 ? 'selected' : '' }}>
                                                        أسبوعي
                                                    </option>
                                                    <option value="2"
                                                        {{ old('recurrence_type') == 2 ? 'selected' : '' }}>
                                                        كل أسبوعين
                                                    </option>
                                                    <option value="3"
                                                        {{ old('recurrence_type') == 3 ? 'selected' : '' }}>
                                                        شهري
                                                    </option>
                                                    <option value="4"
                                                        {{ old('recurrence_type') == 4 ? 'selected' : '' }}>
                                                        كل شهرين
                                                    </option>
                                                    <option value="5"
                                                        {{ old('recurrence_type') == 5 ? 'selected' : '' }}>
                                                        سنوي
                                                    </option>
                                                    <option value="6"
                                                        {{ old('recurrence_type') == 6 ? 'selected' : '' }}>
                                                        كل سنتين
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="recurrence_date">تاريخ نهاية التكرار</label>
                                                <input type="date" class="form-control" id="recurrence_date"
                                                    name="recurrence_date"
                                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="assign_employee"
                                    name="assign_employee" onchange="toggleStaffFields(this)">
                                <label class="form-check-label" for="assign_employee">
                                    تعيين موظف
                                </label>
                            </div>

                            <div id="staff-fields" style="display: none;">
                                <div class="form-group">
                                    <label for="created_by">اختر الموظف</label>
                                    <select class="form-control" id="created_by" name="created_by">
                                        <option value="">اختر الموظف</option>
                                        @foreach ($employees as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>     </div>


        </div>


    </div>
    </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            // تحميل الإجراءات من localStorage أو استخدام القائمة الافتراضية
            let procedures = JSON.parse(localStorage.getItem('procedures')) || [
                'متابعة',
                'تدقيق',
                'مراجعة',
                'اجتماع',
                'زيارة',
                'ملاحظة'
            ];

            // تحديث localStorage
            function saveProcedures() {
                localStorage.setItem('procedures', JSON.stringify(procedures));
            }

            // تحديث القائمة المنسدلة عند تحميل الصفحة
            updateSelectOptions();

            // إضافة إجراء جديد
            $('#addProcedureBtn').on('click', function() {
                const name = $('#newProcedureName').val().trim();
                if (name && procedures.length < 6) {
                    procedures.push(name);
                    updateProceduresList();
                    updateSelectOptions();
                    saveProcedures();
                    $('#newProcedureName').val('');
                } else if (procedures.length >= 6) {
                    alert('لا يمكن إضافة أكثر من 6 إجراءات');
                }
            });

            // تحديث قائمة الإجراءات في المودال
            function updateProceduresList() {
                let listHtml = '';
                procedures.forEach((proc, index) => {
                    listHtml += `
                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                    <span>${proc}</span>
                    <button class="btn btn-sm btn-outline-danger delete-procedure" data-index="${index}">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                </div>`;
                });
                $('#procedures-list').html(listHtml);
            }

            // عند فتح المودال
            $('#proceduresModal').on('show.bs.modal', function() {
                updateProceduresList();
            });

            // حذف إجراء
            $(document).on('click', '.delete-procedure', function() {
                const index = $(this).data('index');
                procedures.splice(index, 1);
                updateProceduresList();
                updateSelectOptions();
                saveProcedures();
            });

            // تحديث خيارات القائمة المنسدلة
            function updateSelectOptions() {
                let selectHtml = '<option value="">اختر نوع الإجراء</option>';
                procedures.forEach(proc => {
                    selectHtml += `<option value="${proc}">${proc}</option>`;
                });
                selectHtml += '<option value="add_new" class="text-primary">+ تعديل قائمة الإجراءات</option>';
                $('#action_type').html(selectHtml);
            }

            // حفظ التغييرات
            $('#saveProcedures').on('click', function() {
                $('#proceduresModal').modal('hide');
            });

            // السماح بالإضافة عند الضغط على Enter
            $('#newProcedureName').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#addProcedureBtn').click();
                }
            });

            // عند اختيار "تعديل قائمة الإجراءات" من القائمة المنسدلة
            $('#action_type').on('change', function() {
                if ($(this).val() === 'add_new') {
                    $('#proceduresModal').modal('show');
                    $(this).val(''); // إعادة تحديد القيمة إلى فارغة
                }
            });

            // التعامل مع خيار التكرار
            $('#is_recurring').change(function() {
                if ($(this).is(':checked')) {
                    $('#recurring_options').slideDown();
                } else {
                    $('#recurring_options').slideUp();
                }
            });

            // التعامل مع خيار تعيين موظف
            $('#assign_employee').change(function() {
                if ($(this).is(':checked')) {
                    $('#employee_options').slideDown();
                } else {
                    $('#employee_options').slideUp();
                }
            });

            // تحميل قائمة الموظفين
            function loadEmployees() {
                $.get('/employees/list', function(data) {
                    let options = '<option value="">اختر الموظف</option>';
                    data.forEach(function(employee) {
                        options += `<option value="${employee.id}">${employee.name}</option>`;
                    });
                    $('#employee_id').html(options);
                });
            }

            // تحميل الموظفين عند تفعيل خيار تعيين موظف
            $('#assign_employee').change(function() {
                if ($(this).is(':checked')) {
                    loadEmployees();
                }
            });
        });

        // دالة إظهار/إخفاء حقول التكرار
        function toggleRecurringFields(checkbox) {
            const recurringFields = document.getElementById('recurring-fields');
            if (checkbox.checked) {
                recurringFields.style.display = 'block';
            } else {
                recurringFields.style.display = 'none';
            }
        }

        // دالة إظهار/إخفاء حقول الموظفين
        function toggleStaffFields(checkbox) {
            const staffFields = document.getElementById('staff-fields');
            if (checkbox.checked) {
                staffFields.style.display = 'block';
            } else {
                staffFields.style.display = 'none';
            }
        }
    </script>
@endsection
