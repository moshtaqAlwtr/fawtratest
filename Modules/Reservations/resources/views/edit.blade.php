@extends('master')

@section('title')
    تعديل حجز
@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
    * {
        font-family: 'Cairo', sans-serif;
    }

    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
        color: #495057;
    }

    .main-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-header {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 1px solid #dee2e6;
    }

    .page-title {
        color: #2c3e50;
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 2.2rem;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "←";
        color: #6c757d;
    }

    .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #495057;
    }

    /* شريط التقدم */
    .progress-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
    }

    .progress-steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
    }

    .progress-steps::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 50px;
        right: 50px;
        height: 3px;
        background: #28a745;
        z-index: 1;
    }

    .progress-step {
        text-align: center;
        position: relative;
        z-index: 2;
        flex: 1;
    }

    .step-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.2rem;
        margin: 0 auto 10px;
        transition: all 0.3s ease;
        position: relative;
        box-shadow: 0 5px 15px rgba(40,167,69,0.4);
    }

    .step-circle::before {
        content: '✓';
        position: absolute;
        font-size: 1.5rem;
    }

    .step-label {
        font-size: 0.9rem;
        color: #28a745;
        font-weight: 600;
    }

    /* البطاقات */
    .custom-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .custom-card:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .card-title {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 20px;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        background: white;
    }

    /* تفاصيل الحجز */
    .reservation-details {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 25px;
        border: 1px solid #dee2e6;
        position: sticky;
        top: 20px;
    }

    .detail-item {
        margin-bottom: 15px;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }

    .detail-value {
        color: #007bff;
        font-weight: 500;
    }

    /* الأزرار */
    .btn-custom {
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
    }

    .btn-primary-custom:hover {
        background: linear-gradient(135deg, #0056b3, #004085);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,123,255,0.4);
    }

    .btn-danger-custom {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }

    .btn-danger-custom:hover {
        background: linear-gradient(135deg, #c82333, #a71e2a);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220,53,69,0.4);
    }

    .btn-success-custom {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }

    .btn-success-custom:hover {
        background: linear-gradient(135deg, #1e7e34, #155724);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40,167,69,0.4);
    }

    .button-container {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 25px;
        padding: 20px 0;
    }

    .action-buttons {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 20px;
    }

    .required-note {
        background: linear-gradient(135deg, #fff3cd, #ffeaa7);
        border: 1px solid #ffeaa7;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        color: #856404;
        font-weight: 500;
    }

    /* التوست */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }

    .toast {
        border: none;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .toast-success {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .toast-error {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }

    .toast-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: #212529;
    }

    /* أيقونات الخطوات */
    .step-icon {
        font-size: 1.2rem;
    }

    /* تحسينات إضافية */
    .invalid-feedback {
        display: block;
        font-size: 0.875rem;
        color: #dc3545;
        margin-top: 5px;
    }

    .is-invalid {
        border-color: #dc3545;
    }

    /* Loading spinner */
    .loading-spinner {
        display: none;
        margin-left: 10px;
    }

    .btn-loading {
        pointer-events: none;
        opacity: 0.7;
    }

    /* تأثيرات بصرية */
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-group {
        margin-bottom: 20px;
    }

    .edit-mode-indicator {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }

    /* تحسينات للشاشات الصغيرة */
    @media (max-width: 768px) {
        .main-container {
            padding: 10px;
        }

        .button-container {
            flex-direction: column;
            gap: 10px;
        }

        .btn-custom {
            width: 100%;
        }

        .progress-steps {
            flex-direction: column;
            gap: 15px;
        }

        .progress-steps::before {
            display: none;
        }
    }
</style>
@endsection

@section('content')
<!-- Toast Container -->
<div class="toast-container"></div>

<div class="main-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-edit"></i>
            تعديل حجز
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home"></i> الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('Reservations.index') }}">الحجوزات</a></li>
                <li class="breadcrumb-item active">تعديل حجز</li>
            </ol>
        </nav>

        <!-- Edit Mode Indicator -->
        <div class="edit-mode-indicator">
            <i class="fas fa-edit"></i>
            وضع التعديل - تحديث بيانات الحجز
        </div>
    </div>

    <!-- Progress Bar - All Steps Completed -->
    <div class="progress-container">
        <div class="progress-steps">
            <div class="progress-step">
                <div class="step-circle">
                    <i class="fas fa-check step-icon"></i>
                </div>
                <div class="step-label">خدمة</div>
            </div>
            <div class="progress-step">
                <div class="step-circle">
                    <i class="fas fa-check step-icon"></i>
                </div>
                <div class="step-label">موظف</div>
            </div>
            <div class="progress-step">
                <div class="step-circle">
                    <i class="fas fa-check step-icon"></i>
                </div>
                <div class="step-label">التاريخ</div>
            </div>
            <div class="progress-step">
                <div class="step-circle">
                    <i class="fas fa-check step-icon"></i>
                </div>
                <div class="step-label">العميل</div>
            </div>
        </div>
    </div>

    <!-- Required Fields Note -->
    <div class="required-note">
        <i class="fas fa-info-circle me-2"></i>
        الحقول التي عليها علامة <span style="color: red">*</span> إلزامية
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-calendar-edit fa-2x text-primary"></i>
                <div>
                    <h6 class="mb-0">تعديل بيانات الحجز</h6>
                    <small class="text-muted">قم بتحديث البيانات المطلوبة واحفظ التغييرات</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('Reservations.index') }}" class="btn btn-custom btn-danger-custom">
                    <i class="fas fa-times me-2"></i>
                    إلغاء
                </a>
                <button type="submit" form="editForm" class="btn btn-custom btn-success-custom" id="saveButton">
                    <i class="fas fa-save me-2"></i>
                    حفظ التعديلات

                </button>
            </div>
        </div>
    </div>

    <form id="editForm" action="{{ route('Reservations.update', $booking->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Form Fields -->
            <div class="col-lg-8">
                <!-- Service Selection -->
                <div class="custom-card fade-in">
                    <h5 class="card-title">
                        <i class="fas fa-cog"></i>
                        اختيار الخدمة
                    </h5>
                    <div class="form-group">
                        <label for="serviceSelect" class="form-label">اختر خدمة <span style="color: red">*</span></label>
                        <select id="serviceSelect" class="form-select @error('product_id') is-invalid @enderror" name="product_id" required>
                            <option value="">اختر خدمة</option>
                            @foreach ($Products as $Product)
                                <option value="{{ $Product->id }}" {{ $booking->product_id == $Product->id ? 'selected' : '' }}>{{ $Product->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Employee Selection -->
                <div class="custom-card fade-in">
                    <h5 class="card-title">
                        <i class="fas fa-user"></i>
                        اختيار الموظف
                    </h5>
                    <div class="form-group">
                        <label for="employeeSelect" class="form-label">اختر موظف <span style="color: red">*</span></label>
                        <select id="employeeSelect" class="form-control select2 @error('employee_id') is-invalid @enderror" name="employee_id" required>
                            <option value="">اختر موظف</option>
                            @foreach ($Employees as $Employee)
                                <option value="{{ $Employee->id }}" {{ $booking->employee_id == $Employee->id ? 'selected' : '' }}>{{ $Employee->name ?? "" }}</option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Date & Time Selection -->
                <div class="custom-card fade-in">
                    <h5 class="card-title">
                        <i class="fas fa-calendar"></i>
                        التاريخ والوقت
                    </h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="dateSelect" class="form-label">اختر التاريخ <span style="color: red">*</span></label>
                            <input type="date" id="dateSelect" class="form-control @error('appointment_date') is-invalid @enderror" name="appointment_date" value="{{ $booking->appointment_date }}" required>
                            @error('appointment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="startTime" class="form-label">وقت البدء <span style="color: red">*</span></label>
                            <input type="time" id="startTime" class="form-control @error('start_time') is-invalid @enderror" name="start_time" value="{{ $booking->start_time }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="endTime" class="form-label">وقت الانتهاء <span style="color: red">*</span></label>
                            <input type="time" id="endTime" class="form-control @error('end_time') is-invalid @enderror" name="end_time" value="{{ $booking->end_time }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Client Selection -->
                <div class="custom-card fade-in">
                    <h5 class="card-title">
                        <i class="fas fa-user-check"></i>
                        اختيار العميل
                    </h5>
                    <div class="form-group">
                        <label for="clientSelect" class="form-label">اختر عميل <span style="color: red">*</span></label>
                        <select id="clientSelect" class="form-control select2 @error('client_id') is-invalid @enderror" name="client_id" required>
                            <option value="">اختر عميل</option>
                            @foreach ($Clients as $Client)
                                <option value="{{ $Client->id }}" {{ $booking->client_id == $Client->id ? 'selected' : '' }}>{{ $Client->trade_name ?? "" }}</option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Reservation Details -->
            <div class="col-lg-4">
                <div class="reservation-details">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        تفاصيل الحجز المحدث
                    </h5>
                    <div class="detail-item">
                        <div class="detail-label">الخدمة المختارة:</div>
                        <div class="detail-value" id="selectedService">{{ $Products->firstWhere('id', $booking->product_id)?->name ?? 'غير محدد' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">الموظف المختار:</div>
                        <div class="detail-value" id="selectedEmployee">{{ $Employees->firstWhere('id', $booking->employee_id)?->first_name ?? 'غير محدد' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">التاريخ:</div>
                        <div class="detail-value" id="selectedDate">{{ $booking->appointment_date ?? 'غير محدد' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">الفترة الزمنية:</div>
                        <div class="detail-value" id="selectedTimeRange">
                            @if($booking->start_time && $booking->end_time)
                                {{ $booking->start_time }} - {{ $booking->end_time }}
                            @else
                                غير محدد
                            @endif
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">العميل المختار:</div>
                        <div class="detail-value" id="selectedClient">{{ $Clients->firstWhere('id', $booking->client_id)?->trade_name ?? 'غير محدد' }}</div>
                    </div>

                    <!-- Booking Status -->
                    <div class="detail-item">
                        <div class="detail-label">حالة الحجز:</div>
                        <div class="detail-value">
                            <span class="badge bg-warning">قيد التعديل</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // عرض التوست
    function showToast(message, type = 'success') {
        const toastContainer = document.querySelector('.toast-container');
        const toastId = 'toast_' + Date.now();

        const toastHTML = `
            <div id="${toastId}" class="toast toast-${type}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);

        // إظهار التوست
        toastElement.style.display = 'block';
        setTimeout(() => {
            toastElement.style.opacity = '1';
            toastElement.style.transform = 'translateX(0)';
        }, 100);

        // إخفاء التوست بعد 4 ثواني
        setTimeout(() => {
            toastElement.style.opacity = '0';
            toastElement.style.transform = 'translateX(100%)';
            setTimeout(() => {
                toastElement.remove();
            }, 300);
        }, 4000);
    }

    // التحقق من صحة البيانات
    function validateForm() {
        let isValid = true;
        const fields = [
            { id: 'serviceSelect', name: 'الخدمة' },
            { id: 'employeeSelect', name: 'الموظف' },
            { id: 'dateSelect', name: 'التاريخ' },
            { id: 'startTime', name: 'وقت البدء' },
            { id: 'endTime', name: 'وقت الانتهاء' },
            { id: 'clientSelect', name: 'العميل' }
        ];

        fields.forEach(field => {
            const element = document.getElementById(field.id);
            if (!element.value) {
                showToast(`يرجى اختيار ${field.name}`, 'error');
                element.focus();
                isValid = false;
                return false;
            }
        });

        // التحقق من الأوقات
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;

        if (startTime && endTime && startTime >= endTime) {
            showToast('يجب أن يكون وقت البدء أقل من وقت الانتهاء', 'error');
            isValid = false;
        }

        return isValid;
    }

    // تحديث تفاصيل الحجز
    function updateReservationDetails() {
        // الخدمة
        const serviceSelect = document.getElementById('serviceSelect');
        const selectedService = serviceSelect.options[serviceSelect.selectedIndex].text;
        document.getElementById('selectedService').textContent = serviceSelect.value ? selectedService : 'غير محدد';

        // الموظف
        const employeeSelect = document.getElementById('employeeSelect');
        const selectedEmployee = employeeSelect.options[employeeSelect.selectedIndex].text;
        document.getElementById('selectedEmployee').textContent = employeeSelect.value ? selectedEmployee : 'غير محدد';

        // التاريخ والوقت
        const selectedDate = document.getElementById('dateSelect').value;
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;

        document.getElementById('selectedDate').textContent = selectedDate || 'غير محدد';
        document.getElementById('selectedTimeRange').textContent =
            (startTime && endTime) ? `${startTime} - ${endTime}` : 'غير محدد';

        // العميل
        const clientSelect = document.getElementById('clientSelect');
        const selectedClient = clientSelect.options[clientSelect.selectedIndex].text;
        document.getElementById('selectedClient').textContent = clientSelect.value ? selectedClient : 'غير محدد';
    }

    // أحداث التغيير للحقول
    document.getElementById('serviceSelect').addEventListener('change', function() {
        updateReservationDetails();
        if (this.value) {
            showToast('تم تحديث الخدمة بنجاح', 'success');
        }
    });

    document.getElementById('employeeSelect').addEventListener('change', function() {
        updateReservationDetails();
        if (this.value) {
            showToast('تم تحديث الموظف بنجاح', 'success');
        }
    });

    document.getElementById('dateSelect').addEventListener('change', function() {
        updateReservationDetails();
        if (this.value) {
            showToast('تم تحديث التاريخ بنجاح', 'success');
        }
    });

    document.getElementById('startTime').addEventListener('change', function() {
        updateReservationDetails();
    });

    document.getElementById('endTime').addEventListener('change', function() {
        updateReservationDetails();
        const startTime = document.getElementById('startTime').value;
        if (this.value && startTime && this.value > startTime) {
            showToast('تم تحديد الوقت بنجاح', 'success');
        }
    });

    document.getElementById('clientSelect').addEventListener('change', function() {
        updateReservationDetails();
        if (this.value) {
            showToast('تم تحديث العميل بنجاح', 'success');
        }
    });

    // حفظ التعديلات
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (validateForm()) {
        // إظهار التحميل
        const saveBtn = document.getElementById('saveButton');
        const spinner = saveBtn.querySelector('.loading-spinner');

        saveBtn.classList.add('btn-loading');
        spinner.style.display = 'inline-block';

        // حذف أو تعليق هذا السطر لعدم عرض التوست
        // showToast('جاري حفظ التعديلات...', 'warning');

        // إرسال النموذج بعد ثانيتين
        setTimeout(() => {
            this.submit();
        }, 2000);
    }
});


    // تعيين الحد الأدنى للتاريخ (اليوم)
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateSelect').min = today;

    // إضافة تأثيرات بصرية للتفاعل
    document.querySelectorAll('.form-control, .form-select').forEach(element => {
        element.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });

        element.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // تأثير للأزرار
    document.querySelectorAll('.btn-custom').forEach(button => {
        button.addEventListener('mouseenter', function() {
            if (!this.disabled && !this.classList.contains('btn-loading')) {
                this.style.transform = 'translateY(-2px) scale(1.05)';
            }
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // رسالة ترحيب للتعديل
    setTimeout(() => {
        showToast('وضع التعديل مُفعل - يمكنك تحديث بيانات الحجز', 'warning');
    }, 500);

    // التعامل مع الأخطاء من Laravel
    @if ($errors->any())
        setTimeout(() => {
            showToast('يرجى تصحيح الأخطاء المذكورة', 'error');
        }, 1000);
    @endif

    // رسالة نجاح من Laravel
    @if (session('success'))
        setTimeout(() => {
            showToast('{{ session('success') }}', 'success');
        }, 500);
    @endif

    // تحذير عند محاولة الخروج مع وجود تغييرات غير محفوظة
    let formChanged = false;

    // مراقبة التغييرات في النموذج
    document.querySelectorAll('#editForm input, #editForm select').forEach(element => {
        element.addEventListener('change', function() {
            formChanged = true;
        });
    });

    // تحذير عند محاولة الخروج
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            const confirmationMessage = 'لديك تغييرات غير محفوظة. هل تريد المغادرة دون حفظ؟';
            e.returnValue = confirmationMessage;
            return confirmationMessage;
        }
    });

    // إزالة التحذير عند الحفظ
    document.getElementById('editForm').addEventListener('submit', function() {
        formChanged = false;
    });

    // تأكيد الإلغاء
    document.querySelector('a[href*="Reservations.index"]').addEventListener('click', function(e) {
        if (formChanged) {
            if (!confirm('لديك تغييرات غير محفوظة. هل تريد المغادرة دون حفظ؟')) {
                e.preventDefault();
            }
        }
    });

    // إضافة تأثير تحميل للصفحة
    document.querySelectorAll('.custom-card').forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 150);
    });

    // تتبع الحالة الأصلية للمقارنة
    const originalValues = {
        service: document.getElementById('serviceSelect').value,
        employee: document.getElementById('employeeSelect').value,
        date: document.getElementById('dateSelect').value,
        startTime: document.getElementById('startTime').value,
        endTime: document.getElementById('endTime').value,
        client: document.getElementById('clientSelect').value
    };

    // التحقق من وجود تغييرات
    function checkForChanges() {
        const currentValues = {
            service: document.getElementById('serviceSelect').value,
            employee: document.getElementById('employeeSelect').value,
            date: document.getElementById('dateSelect').value,
            startTime: document.getElementById('startTime').value,
            endTime: document.getElementById('endTime').value,
            client: document.getElementById('clientSelect').value
        };

        const hasChanges = Object.keys(originalValues).some(key =>
            originalValues[key] !== currentValues[key]
        );

        formChanged = hasChanges;

        // تحديث مؤشر التغييرات
        const statusBadge = document.querySelector('.detail-value .badge');
        if (hasChanges) {
            statusBadge.className = 'badge bg-warning';
            statusBadge.textContent = 'تم التعديل';
        } else {
            statusBadge.className = 'badge bg-info';
            statusBadge.textContent = 'لا توجد تغييرات';
        }
    }

    // مراقبة التغييرات
    document.querySelectorAll('#editForm input, #editForm select').forEach(element => {
        element.addEventListener('change', checkForChanges);
    });

    // تشغيل التحقق الأولي
    checkForChanges();
});
</script>
@endsection
