@extends('master')

@section('title')
    أضف حجز
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
        background: #dee2e6;
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
        background: #dee2e6;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.2rem;
        margin: 0 auto 10px;
        transition: all 0.3s ease;
        position: relative;
    }

    .step-circle.active {
        background: linear-gradient(135deg, #007bff, #0056b3);
        box-shadow: 0 5px 15px rgba(0,123,255,0.4);
        transform: scale(1.1);
    }

    .step-circle.completed {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        box-shadow: 0 5px 15px rgba(40,167,69,0.4);
    }

    .step-circle.completed::before {
        content: '✓';
        position: absolute;
        font-size: 1.5rem;
    }

    .step-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
    }

    .step-circle.active + .step-label {
        color: #007bff;
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

    .btn-secondary-custom {
        background: linear-gradient(135deg, #6c757d, #545b62);
        color: white;
    }

    .btn-secondary-custom:hover {
        background: linear-gradient(135deg, #545b62, #383d41);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108,117,125,0.4);
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

    /* الأنيميشن */
    .step {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
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

    /* تحسينات للشاشات الصغيرة */
    @media (max-width: 768px) {
        .progress-steps {
            flex-direction: column;
            gap: 20px;
        }

        .progress-steps::before {
            display: none;
        }

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
            <i class="fas fa-calendar-plus"></i>
            أضف حجز
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home"></i> الرئيسية</a></li>
                <li class="breadcrumb-item active">أضف حجز</li>
            </ol>
        </nav>
    </div>

    <!-- Progress Bar -->
    <div class="progress-container">
        <div class="progress-steps">
            <div class="progress-step">
                <div class="step-circle active" id="step1Circle">
                    <i class="fas fa-cog step-icon"></i>
                </div>
                <div class="step-label">خدمة</div>
            </div>
            <div class="progress-step">
                <div class="step-circle" id="step2Circle">
                    <i class="fas fa-user step-icon"></i>
                </div>
                <div class="step-label">موظف</div>
            </div>
            <div class="progress-step">
                <div class="step-circle" id="step3Circle">
                    <i class="fas fa-calendar step-icon"></i>
                </div>
                <div class="step-label">التاريخ</div>
            </div>
            <div class="progress-step">
                <div class="step-circle" id="step4Circle">
                    <i class="fas fa-user-check step-icon"></i>
                </div>
                <div class="step-label">العميل</div>
            </div>
        </div>
    </div>

    <form id="clientForm" action="{{ route('Reservations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Step 1: Service Selection -->
        <div id="step1" class="step">
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="custom-card">
                        <h5 class="card-title">
                            <i class="fas fa-cog"></i>
                            موعد جديد
                        </h5>
                        <div class="mb-3">
                            <label for="serviceSelect" class="form-label">اختر خدمة</label>
                            <select id="serviceSelect" class="form-select @error('product_id') is-invalid @enderror" name="product_id" required>
                                <option value="">اختر خدمة</option>
                                @foreach ($Products as $Product)
                                    <option value="{{ $Product->id }}" {{ old('product_id') == $Product->id ? 'selected' : '' }}>{{ $Product->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="reservation-details">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            تفاصيل الحجز
                        </h5>
                        <div class="detail-item">
                            <div class="detail-label">الخدمات المختارة:</div>
                            <div class="detail-value" id="selectedService">لم يتم الاختيار</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="button-container">
                <button type="button" id="nextButton1" class="btn btn-custom btn-primary-custom" disabled>
                    التالي
                    <i class="fas fa-arrow-left ms-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Employee Selection -->
        <div id="step2" class="step" style="display: none;">
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="custom-card">
                        <h5 class="card-title">
                            <i class="fas fa-user"></i>
                            اختر الموظف
                        </h5>
                        <div class=" col-4 mb-3">
                            <label for="employeeSelect_1" class="form-label">اختر موظف</label>
                            <select id="employeeSelect_1" class="form-select select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                                <option value="">اختر موظف</option>
                                @foreach ($Employees as $Employee)
                                    <option value="{{ $Employee->id }}" {{ old('employee_id') == $Employee->id ? 'selected' : '' }}>{{ $Employee->name ?? "" }}</option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="reservation-details">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            تفاصيل الحجز
                        </h5>
                        <div class="detail-item">
                            <div class="detail-label">الخدمات المختارة:</div>
                            <div class="detail-value" id="selectedServiceStep2">لم يتم الاختيار</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">الموظف المختار:</div>
                            <div class="detail-value" id="selectedEmployee">لم يتم الاختيار</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="button-container">
                <button type="button" id="prevButton2" class="btn btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-right me-2"></i>
                    السابق
                </button>
                <button type="button" id="nextButton2" class="btn btn-custom btn-primary-custom" disabled>
                    التالي
                    <i class="fas fa-arrow-left ms-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: Date & Time Selection -->
        <div id="step3" class="step" style="display: none;">
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="custom-card">
                        <h5 class="card-title">
                            <i class="fas fa-calendar"></i>
                            اختر التاريخ والوقت
                        </h5>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="dateSelect" class="form-label">اختر التاريخ</label>
                                <input type="date" id="dateSelect" class="form-control @error('appointment_date') is-invalid @enderror" name="appointment_date" value="{{ old('appointment_date') }}" required>
                                @error('appointment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="startTime" class="form-label">وقت البدء</label>
                                <input type="time" id="startTime" class="form-control @error('start_time') is-invalid @enderror" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="endTime" class="form-label">وقت الانتهاء</label>
                                <input type="time" id="endTime" class="form-control @error('end_time') is-invalid @enderror" name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="reservation-details">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            تفاصيل الحجز
                        </h5>
                        <div class="detail-item">
                            <div class="detail-label">الخدمات المختارة:</div>
                            <div class="detail-value" id="selectedServiceStep3">لم يتم الاختيار</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">الموظف المختار:</div>
                            <div class="detail-value" id="selectedEmployeeStep3">لم يتم الاختيار</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">التاريخ:</div>
                            <div class="detail-value" id="selectedDate">لم يتم الاختيار</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">الفترة الزمنية:</div>
                            <div class="detail-value" id="selectedTimeRange">لم يتم الاختيار</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="button-container">
                <button type="button" id="prevButton3" class="btn btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-right me-2"></i>
                    السابق
                </button>
                <button type="button" id="nextButton3" class="btn btn-custom btn-primary-custom" disabled>
                    التالي
                    <i class="fas fa-arrow-left ms-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 4: Client Selection -->
        <div id="step4" class="step" style="display: none;">
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="custom-card">
                        <h5 class="card-title">
                            <i class="fas fa-user-check"></i>
                            اختر العميل
                        </h5>
                        <div class="mb-3">
                            <label for="clientSelect" class="form-label">اختر عميل</label>
                            <select id="clientSelect" class="form-select select2  @error('client_id') is-invalid @enderror" name="client_id" required>
                                <option value="">اختر عميل</option>
                                @foreach ($Clients as $Client)
                                    <option value="{{ $Client->id }}" {{ old('client_id') == $Client->id ? 'selected' : '' }}>{{ $Client->trade_name ?? "" }}</option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="reservation-details">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            تفاصيل الحجز
                        </h5>
                        <div class="detail-item">
                            <div class="detail-label">الخدمات المختارة:</div>
                            <div class="detail-value" id="selectedServiceStep4">لم يتم الاختيار</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">الموظف المختار:</div>
                            <div class="detail-value" id="selectedEmployeeStep4">لم يتم الاختيار</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">التاريخ والوقت:</div>
                            <div class="detail-value" id="selectedDateTimeStep4">لم يتم الاختيار</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">العميل المختار:</div>
                            <div class="detail-value" id="selectedClient">لم يتم الاختيار</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="button-container">
                <button type="button" id="prevButton4" class="btn btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-right me-2"></i>
                    السابق
                </button>
                <button type="submit" id="saveButton" class="btn btn-custom btn-success-custom" disabled>
                    <i class="fas fa-save me-2"></i>
                    حفظ الحجز

                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 4;

    // عرض التوست
    function showToast(message, type = 'success') {
        const toastContainer = document.querySelector('.toast-container');
        const toastId = 'toast_' + Date.now();

        const toastHTML = `
            <div id="${toastId}" class="toast toast-${type}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
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

    // تحديث شريط التقدم
    function updateProgress() {
        for (let i = 1; i <= totalSteps; i++) {
            const circle = document.getElementById(`step${i}Circle`);
            const icon = circle.querySelector('.step-icon');

            if (i < currentStep) {
                circle.className = 'step-circle completed';
                icon.className = 'fas fa-check step-icon';
            } else if (i === currentStep) {
                circle.className = 'step-circle active';
                // إعادة تعيين الأيقونة الأصلية
                if (i === 1) icon.className = 'fas fa-cog step-icon';
                else if (i === 2) icon.className = 'fas fa-user step-icon';
                else if (i === 3) icon.className = 'fas fa-calendar step-icon';
                else if (i === 4) icon.className = 'fas fa-user-check step-icon';
            } else {
                circle.className = 'step-circle';
                // إعادة تعيين الأيقونة الأصلية
                if (i === 1) icon.className = 'fas fa-cog step-icon';
                else if (i === 2) icon.className = 'fas fa-user step-icon';
                else if (i === 3) icon.className = 'fas fa-calendar step-icon';
                else if (i === 4) icon.className = 'fas fa-user-check step-icon';
            }
        }
    }

    // إظهار الخطوة
    function showStep(step) {
        document.querySelectorAll('.step').forEach((stepElement, index) => {
            stepElement.style.display = index + 1 === step ? 'block' : 'none';
        });
        updateProgress();
    }

    // التحقق من صحة البيانات
    function validateStep(step) {
        let isValid = true;

        switch(step) {
            case 1:
                const service = document.getElementById('serviceSelect').value;
                if (!service) {
                    showToast('يرجى اختيار خدمة', 'error');
                    isValid = false;
                }
                break;

            case 2:
                const employee = document.getElementById('employeeSelect_1').value;
                if (!employee) {
                    showToast('يرجى اختيار موظف', 'error');
                    isValid = false;
                }
                break;

            case 3:
                const date = document.getElementById('dateSelect').value;
                const startTime = document.getElementById('startTime').value;
                const endTime = document.getElementById('endTime').value;

                if (!date || !startTime || !endTime) {
                    showToast('يرجى تعبئة جميع حقول التاريخ والوقت', 'error');
                    isValid = false;
                } else if (startTime >= endTime) {
                    showToast('يجب أن يكون وقت البدء أقل من وقت الانتهاء', 'error');
                    isValid = false;
                }
                break;

            case 4:
                const client = document.getElementById('clientSelect').value;
                if (!client) {
                    showToast('يرجى اختيار عميل', 'error');
                    isValid = false;
                }
                break;
        }

        return isValid;
    }

    // تحديث تفاصيل الحجز
    function updateReservationDetails() {
        // الخدمة
        const serviceSelect = document.getElementById('serviceSelect');
        const selectedService = serviceSelect.options[serviceSelect.selectedIndex].text;
        document.getElementById('selectedService').textContent = serviceSelect.value ? selectedService : 'لم يتم الاختيار';
        document.getElementById('selectedServiceStep2').textContent = serviceSelect.value ? selectedService : 'لم يتم الاختيار';
        document.getElementById('selectedServiceStep3').textContent = serviceSelect.value ? selectedService : 'لم يتم الاختيار';
        document.getElementById('selectedServiceStep4').textContent = serviceSelect.value ? selectedService : 'لم يتم الاختيار';

        // الموظف
        const employeeSelect = document.getElementById('employeeSelect_1');
        const selectedEmployee = employeeSelect.options[employeeSelect.selectedIndex].text;
        document.getElementById('selectedEmployee').textContent = employeeSelect.value ? selectedEmployee : 'لم يتم الاختيار';
        document.getElementById('selectedEmployeeStep3').textContent = employeeSelect.value ? selectedEmployee : 'لم يتم الاختيار';
        document.getElementById('selectedEmployeeStep4').textContent = employeeSelect.value ? selectedEmployee : 'لم يتم الاختيار';

        // التاريخ والوقت
        const selectedDate = document.getElementById('dateSelect').value;
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;

        document.getElementById('selectedDate').textContent = selectedDate || 'لم يتم الاختيار';
        document.getElementById('selectedTimeRange').textContent = (startTime && endTime) ? `${startTime} - ${endTime}` : 'لم يتم الاختيار';
        document.getElementById('selectedDateTimeStep4').textContent =
            (selectedDate && startTime && endTime) ? `${selectedDate} من ${startTime} إلى ${endTime}` : 'لم يتم الاختيار';

        // العميل
        const clientSelect = document.getElementById('clientSelect');
        const selectedClient = clientSelect.options[clientSelect.selectedIndex].text;
        document.getElementById('selectedClient').textContent = clientSelect.value ? selectedClient : 'لم يتم الاختيار';

        // تفعيل/إلغاء تفعيل الأزرار
        updateButtonStates();
    }

    // تحديث حالة الأزرار
    function updateButtonStates() {
        // زر الخطوة 1
        const nextBtn1 = document.getElementById('nextButton1');
        nextBtn1.disabled = !document.getElementById('serviceSelect').value;

        // زر الخطوة 2
        const nextBtn2 = document.getElementById('nextButton2');
        nextBtn2.disabled = !document.getElementById('employeeSelect_1').value;

        // زر الخطوة 3
        const nextBtn3 = document.getElementById('nextButton3');
        const date = document.getElementById('dateSelect').value;
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;
        nextBtn3.disabled = !(date && startTime && endTime);

        // زر الحفظ
        const saveBtn = document.getElementById('saveButton');
        saveBtn.disabled = !document.getElementById('clientSelect').value;
    }

    // أحداث الخطوة 1
    document.getElementById('serviceSelect').addEventListener('change', function() {
        updateReservationDetails();
        if (this.value) {
            showToast('تم اختيار الخدمة بنجاح');
        }
    });

    document.getElementById('nextButton1').addEventListener('click', function() {
        if (validateStep(1)) {
            currentStep = 2;
            showStep(currentStep);
            showToast('انتقال إلى اختيار الموظف');
        }
    });

    // أحداث الخطوة 2
    document.getElementById('employeeSelect_1').addEventListener('change', function() {
        updateReservationDetails();
        if (this.value) {
            showToast('تم اختيار الموظف بنجاح');
        }
    });

    document.getElementById('nextButton2').addEventListener('click', function() {
        if (validateStep(2)) {
            currentStep = 3;
            showStep(currentStep);
            showToast('انتقال إلى اختيار التاريخ والوقت');
        }
    });

    document.getElementById('prevButton2').addEventListener('click', function() {
        currentStep = 1;
        showStep(currentStep);
    });

    // أحداث الخطوة 3
    document.getElementById('dateSelect').addEventListener('change', function() {
        updateReservationDetails();
        if (this.value) {
            showToast('تم اختيار التاريخ بنجاح');
        }
    });

    document.getElementById('startTime').addEventListener('change', function() {
        updateReservationDetails();
    });

    document.getElementById('endTime').addEventListener('change', function() {
        updateReservationDetails();
        const startTime = document.getElementById('startTime').value;
        if (this.value && startTime && this.value > startTime) {
            showToast('تم تحديد الوقت بنجاح');
        }
    });

    document.getElementById('nextButton3').addEventListener('click', function() {
        if (validateStep(3)) {
            currentStep = 4;
            showStep(currentStep);
            showToast('انتقال إلى اختيار العميل');
        }
    });

    document.getElementById('prevButton3').addEventListener('click', function() {
        currentStep = 2;
        showStep(currentStep);
    });

    // أحداث الخطوة 4
    document.getElementById('clientSelect').addEventListener('change', function() {
        updateReservationDetails();
        if (this.value) {
            showToast('تم اختيار العميل بنجاح');
        }
    });

    document.getElementById('prevButton4').addEventListener('click', function() {
        currentStep = 3;
        showStep(currentStep);
    });

    // حفظ الحجز
    document.getElementById('clientForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (validateStep(4)) {
            // إظهار التحميل
            const saveBtn = document.getElementById('saveButton');
            const spinner = saveBtn.querySelector('.loading-spinner');

            saveBtn.classList.add('btn-loading');
            spinner.style.display = 'inline-block';

            showToast('جاري حفظ الحجز...', 'success');

            // إرسال النموذج بعد ثانيتين
            setTimeout(() => {
                this.submit();
            }, 2000);
        }
    });

    // تعيين الحد الأدنى للتاريخ (اليوم)
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateSelect').min = today;

    // تهيئة الصفحة
    showStep(currentStep);
    updateReservationDetails();

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
            if (!this.disabled) {
                this.style.transform = 'translateY(-2px) scale(1.05)';
            }
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // رسالة ترحيب
    setTimeout(() => {
        showToast('مرحباً بك! يرجى اتباع الخطوات لإضافة حجز جديد', 'success');
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
});
</script>
@section('scripts')
