@extends('master')

@section('title')
    تعديل موعد
@stop

@section('head')
    <!-- تضمين ملفات Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="{{ route('appointments.update', $appointment->id) }}" method="POST" id="appointment-form">
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
                                        <label for="client">العميل</label>
                                        <select class="form-control select2" id="client" name="client_id" required>
                                            <option value="">اختر عميلاً</option>
                                            @if (@isset($clients) && !@empty($clients) && count($clients) > 0)
                                                @foreach ($clients as $client)
                                                    <option value="{{ $client->id }}" {{ $appointment->client_id == $client->id ? 'selected' : '' }}>
                                                        {{ $client->trade_name }}-{{ $client->code??'' }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="">لا يوجد عملاء متاحين حاليا</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="date" class="form-control datepicker" id="date" name="date"
                                            value="{{ $appointment->appointment_date }}" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="time">الوقت</label>
                                        <input type="time" class="form-control timepicker" id="time" name="time"
                                            value="{{ $appointment->time }}" required>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="duration">مدة</label>
                                        <input type="text" class="form-control" id="duration" name="duration"
                                            value="{{ $appointment->duration ?? '00:00' }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">

                            </div>
                            <div class="form-group">
                                <label for="status">الحالة</label>
                                <select name="status" id="status" class="form-control select2" required>
                                    <option value="1" {{ (int)$appointment->status === 1 || empty($appointment->status) ? 'selected' : '' }}>تم جدولته</option>
                                    <option value="2" {{ (int)$appointment->status === 2 ? 'selected' : '' }}>تم</option>
                                    <option value="3" {{ (int)$appointment->status === 3 ? 'selected' : '' }}>صرف النظر عنه</option>
                                    <option value="4" {{ (int)$appointment->status === 4 ? 'selected' : '' }}>تم جدولته مجددا</option>
                                </select>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="action_type">نوع الإجراء</label>
                                        <select name="action_type" id="action_type" class="form-control select2" required>
                                            <option value="">-- اختر الإجراء --</option>
                                            <option value="1" {{ $appointment->action_type == 1 ? 'selected' : '' }}>Delivery</option>
                                            <option value="2" {{ $appointment->action_type == 2 ? 'selected' : '' }}>Meeting</option>
                                            <option value="3" {{ $appointment->action_type == 3 ? 'selected' : '' }}>Reservation</option>
                                            <option value="4" {{ $appointment->action_type == 4 ? 'selected' : '' }}>اسم المشرف او المسؤول</option>
                                            <option value="5" {{ $appointment->action_type == 5 ? 'selected' : '' }}>زيارة سلبية</option>
                                            <option value="6" {{ $appointment->action_type == 6 ? 'selected' : '' }}>ملاحظة</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">الملاحظات/الشروط</h6>
                                </div>
                                <div class="card-body">
                                    <textarea id="tinyMCE" name="notes" class="form-control">{{ $appointment->notes }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="share_with_client"
                                        name="share_with_client" {{ $appointment->share_with_client ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="share_with_client">مشاركة مع العميل</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="is_recurring" value="0">
                                    <input type="checkbox" class="custom-control-input" id="recurring"
                                        name="is_recurring" value="1" onchange="toggleRecurringFields(this)" {{ $appointment->is_recurring ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="recurring">متكرر</label>
                                </div>

                                <div id="recurring-fields" class="mt-3" style="{{ $appointment->is_recurring ? '' : 'display: none;' }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="recurrence_type">التكرار</label>
                                                <select class="form-control" id="recurrence_type" name="recurrence_type">
                                                    <option value="1" {{ $appointment->recurrence_type == 1 ? 'selected' : '' }}>أسبوعي</option>
                                                    <option value="2" {{ $appointment->recurrence_type == 2 ? 'selected' : '' }}>كل أسبوعين</option>
                                                    <option value="3" {{ $appointment->recurrence_type == 3 ? 'selected' : '' }}>شهري</option>
                                                    <option value="4" {{ $appointment->recurrence_type == 4 ? 'selected' : '' }}>كل شهرين</option>
                                                    <option value="5" {{ $appointment->recurrence_type == 5 ? 'selected' : '' }}>كل سنة</option>
                                                    <option value="6" {{ $appointment->recurrence_type == 6 ? 'selected' : '' }}>كل سنتين</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="recurrence_date">تاريخ نهاية التكرار</label>
                                                <input type="date" class="form-control" id="recurrence_date" name="recurrence_date"
                                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ $appointment->recurrence_date }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Section -->
                    <div class="card">
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="assign_staff"
                                        name="assign_staff" onchange="toggleStaffFields(this)">
                                    <label class="custom-control-label" for="assign_staff">تعيين إلى موظفين</label>
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
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script>
        function toggleRecurringFields(checkbox) {
            const recurringFields = document.getElementById('recurring-fields');
            recurringFields.style.display = checkbox.checked ? 'block' : 'none';
        }
    </script>
@endsection
