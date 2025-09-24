@extends('master')

@section('title')
    عرض الماكينة
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/logs.css') }}">
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض الماكينة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('machines.index') }}">الماكينات</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <div class="card">
        <div class="card-title p-2">
            <button class="btn btn-outline-danger btn-sm waves-effect waves-light" id="deleteBtn"
                data-machine-id="{{ $machine->id }}" data-machine-name="{{ $machine->name }}">
                حذف <i class="fa fa-trash"></i>
            </button>
            <a href="{{ route('machines.edit', $machine->id) }}"
                class="btn btn-outline-primary btn-sm waves-effect waves-light">
                تعديل <i class="fa fa-edit"></i>
            </a>
            <button class="btn btn-outline-info btn-sm waves-effect waves-light" id="testConnectionBtn"
                data-machine-id="{{ $machine->id }}">
                اختبر الاتصال <i class="fa fa-bolt"></i>
            </button>
            <button class="btn btn-outline-secondary btn-sm waves-effect waves-light" id="pullDataBtn"
                data-machine-id="{{ $machine->id }}">
                اسحب بيانات <i class="fa fa-database"></i>
            </button>
            <button class="btn btn-outline-success btn-sm waves-effect waves-light" id="connectBtn"
                data-machine-id="{{ $machine->id }}">
                ربط <i class="fa fa-exchange-alt"></i>
            </button>
            <button class="btn btn-outline-dark btn-sm waves-effect waves-light status-toggle"
                data-machine-id="{{ $machine->id }}" data-current-status="{{ $machine->status }}">
                {{ $machine->status ? 'تعطيل' : 'تفعيل' }} <i class="fa fa-ban"></i>
            </button>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                        aria-controls="details" aria-selected="true">تفاصيل </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log" role="tab"
                        aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- تفاصيل  -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="tab-content">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <table class="table">
                                        <thead style="background: #f8f8f8">
                                            <tr>
                                                <th>تفاصيل الماكينة</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p><small>الاسم</small>: </p><span>{{ $machine->name }}</span>
                                                </td>
                                                <td>
                                                    <p><small>الرقم التسلسلي</small>: </p>
                                                    <strong>{{ $machine->serial_number ?? 'غير محدد' }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><small>اسم المضيف</small>: </p><span>{{ $machine->host_name }}</span>
                                                </td>
                                                <td>
                                                    <p><small>رقم المنفذ</small>: </p>
                                                    <span>{{ $machine->port_number }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><small>مفتاح الاتصال</small>: </p>
                                                    <span>{{ $machine->connection_key ?? 'غير محدد' }}</span>
                                                </td>
                                                <td>
                                                    <p><small>نوع الماكينة</small>: </p>
                                                    <span>{{ ucfirst($machine->machine_type) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                        <label for=""></label>
                                                        <input type="checkbox" disabled name="status"
                                                            {{ $machine->status == 1 ? 'checked' : '' }}>
                                                        <span class="vs-checkbox">
                                                            <span class="vs-checkbox--check">
                                                                <i class="vs-icon feather icon-check"></i>
                                                            </span>
                                                        </span>
                                                        <span class="">الحالة (مفعل/غير مفعل)</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p><small>تاريخ الإضافة</small>: </p>
                                                    <span>{{ $machine->created_at->format('Y-m-d H:i') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><small>آخر تحديث</small>: </p>
                                                    <span>{{ $machine->updated_at->format('Y-m-d H:i') }}</span>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- سجل النشاطات -->
                <div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="activity-log-tab">
                    <div class="row mt-4">
                        <div class="col-12">
                            @if ($logs && count($logs) > 0)
                                @php
                                    $previousDate = null;
                                @endphp

                                @foreach ($logs as $date => $dayLogs)
                                    @php
                                        $currentDate = \Carbon\Carbon::parse($date);
                                        $diffInDays = $previousDate ? $previousDate->diffInDays($currentDate) : 0;
                                    @endphp

                                    @if ($diffInDays > 7)
                                        <div class="timeline-date">
                                            <h4>{{ $currentDate->format('Y-m-d') }}</h4>
                                        </div>
                                    @endif

                                    <div class="timeline-day">{{ $currentDate->translatedFormat('l') }}</div>

                                    <ul class="timeline">
                                        @foreach ($dayLogs as $log)
                                            @if ($log)
                                                <li class="timeline-item">
                                                    <div class="timeline-content">
                                                        <div class="time">
                                                            <i class="far fa-clock"></i>
                                                            {{ $log->created_at->format('H:i:s') }}
                                                        </div>
                                                        <div>
                                                            <strong>{{ $log->user->name ?? 'مستخدم غير معروف' }}</strong>
                                                            {!! Str::markdown($log->description ?? 'لا يوجد وصف') !!}
                                                            <div class="text-muted">
                                                                {{ $log->user->branch->name ?? 'فرع غير معروف' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>

                                    @php
                                        $previousDate = $currentDate;
                                    @endphp
                                @endforeach
                            @else
                                <div class="alert alert-danger text-center" role="alert">
                                    <p class="mb-0">لا توجد سجلات نشاط حتى الآن!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // معالجة تغيير حالة الماكينة
            document.querySelector('.status-toggle')?.addEventListener('click', function() {
                const machineId = this.dataset.machineId;
                const currentStatus = this.dataset.currentStatus === '1';
                const actionText = currentStatus ? 'تعطيل' : 'تفعيل';

                Swal.fire({
                    title: `${actionText} الماكينة`,
                    text: `هل تريد ${actionText} هذه الماكينة؟`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: `نعم، ${actionText}`,
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: currentStatus ? '#dc3545' : '#28a745'
                }).then((result) => {
                    if (result.isConfirmed) {
                        toggleMachineStatus(machineId);
                    }
                });
            });

            // معالجة حذف الماكينة
            document.getElementById('deleteBtn')?.addEventListener('click', function() {
                const machineId = this.dataset.machineId;
                const machineName = this.dataset.machineName;

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف الماكينة "${machineName}" نهائياً ولا يمكن التراجع عن هذا الإجراء`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteMachine(machineId);
                    }
                });
            });

            // اختبار الاتصال
            document.getElementById('testConnectionBtn')?.addEventListener('click', function() {
                const machineId = this.dataset.machineId;
                testConnection(machineId);
            });

            // سحب البيانات
            document.getElementById('pullDataBtn')?.addEventListener('click', function() {
                const machineId = this.dataset.machineId;
                pullData(machineId);
            });

            // ربط الماكينة
            document.getElementById('connectBtn')?.addEventListener('click', function() {
                const machineId = this.dataset.machineId;
                connectMachine(machineId);
            });

            // رسائل النجاح/الخطأ من Session
            @if (session('success'))
                Swal.fire({
                    title: 'تم بنجاح!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'موافق'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    title: 'خطأ!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
            @endif
        });

        // دالة تغيير حالة الماكينة
        function toggleMachineStatus(machineId) {
            Swal.fire({
                title: 'جاري التحديث...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch("{{ route('machines.toggle-status', ':id') }}".replace(':id', machineId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'تم بنجاح!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'موافق'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'موافق'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ في الاتصال بالخادم',
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
            });
        }

        // دالة حذف الماكينة
        function deleteMachine(machineId) {
            Swal.fire({
                title: 'جاري الحذف...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch("{{ route('machines.destroy', ':id') }}".replace(':id', machineId), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'تم الحذف!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'موافق'
                    }).then(() => {
                        window.location.href = "{{ route('machines.index') }}";
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'موافق'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ في الاتصال بالخادم',
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
            });
        }

        // دالة اختبار الاتصال
        function testConnection(machineId) {
            Swal.fire({
                title: 'جاري اختبار الاتصال...',
                text: 'يرجى الانتظار',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // هنا يمكنك إضافة الطلب الفعلي لاختبار الاتصال
            fetch(`/machines/${machineId}/test-connection`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'نجح الاتصال!',
                        text: data.message || 'تم اختبار الاتصال بنجاح',
                        icon: 'success',
                        confirmButtonText: 'موافق'
                    });
                } else {
                    Swal.fire({
                        title: 'فشل الاتصال!',
                        text: data.message || 'فشل في الاتصال بالماكينة',
                        icon: 'error',
                        confirmButtonText: 'موافق'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء اختبار الاتصال',
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
            });
        }

        // دالة سحب البيانات
        function pullData(machineId) {
            Swal.fire({
                title: 'جاري سحب البيانات...',
                text: 'قد تستغرق هذه العملية بعض الوقت',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // هنا يمكنك إضافة الطلب الفعلي لسحب البيانات
            fetch(`/machines/${machineId}/pull-data`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'تم سحب البيانات!',
                        text: data.message || 'تم سحب البيانات بنجاح',
                        icon: 'success',
                        confirmButtonText: 'موافق'
                    });
                } else {
                    Swal.fire({
                        title: 'فشل سحب البيانات!',
                        text: data.message || 'فشل في سحب البيانات',
                        icon: 'error',
                        confirmButtonText: 'موافق'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء سحب البيانات',
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
            });
        }

        // دالة ربط الماكينة
        function connectMachine(machineId) {
            Swal.fire({
                title: 'جاري ربط الماكينة...',
                text: 'يرجى الانتظار',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // هنا يمكنك إضافة الطلب الفعلي لربط الماكينة
            fetch(`/machines/${machineId}/connect`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'تم الربط!',
                        text: data.message || 'تم ربط الماكينة بنجاح',
                        icon: 'success',
                        confirmButtonText: 'موافق'
                    });
                } else {
                    Swal.fire({
                        title: 'فشل الربط!',
                        text: data.message || 'فشل في ربط الماكينة',
                        icon: 'error',
                        confirmButtonText: 'موافق'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء ربط الماكينة',
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
            });
        }
    </script>
@endsection