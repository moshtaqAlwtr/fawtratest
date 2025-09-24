{{-- resources/views/attendance/barcode-scan.blade.php --}}

@extends('master')

@section('title', 'مسح الباركود - الحضور والانصراف')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">مسح الباركود</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                        <li class="breadcrumb-item active">مسح الباركود</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Current Time Display -->
    <div class="card mb-3">
        <div class="card-body text-center">
            <h3 class="mb-2">الوقت الحالي</h3>
            <h1 id="current-time" class="display-4 text-primary mb-0"></h1>
            <p id="current-date" class="text-muted"></p>
        </div>
    </div>

    <!-- Scanner Section -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">كاميرا مسح الباركود</h4>
                    <div class="float-right">
                        <button id="start-camera" class="btn btn-success btn-sm">
                            <i class="fa fa-camera"></i> تشغيل الكاميرا
                        </button>
                        <button id="stop-camera" class="btn btn-danger btn-sm" style="display: none;">
                            <i class="fa fa-stop"></i> إيقاف الكاميرا
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Camera Preview -->
                    <div id="camera-container" class="text-center">
                        <div id="scanner-placeholder" class="border rounded p-5" style="min-height: 300px; background-color: #f8f9fa;">
                            <i class="fa fa-camera fa-3x text-muted mb-3"></i>
                            <p class="text-muted">اضغط على "تشغيل الكاميرا" لبدء المسح</p>
                        </div>
                        <div id="scanner" style="display: none;"></div>
                    </div>

                    <!-- Manual Barcode Input -->
                    <div class="mt-3">
                        <label>أو أدخل الباركود يدوياً:</label>
                        <div class="input-group">
                            <input type="text" id="manual-barcode" class="form-control" placeholder="امسح أو اكتب الباركود هنا">
                            <div class="input-group-append">
                                <button id="process-manual" class="btn btn-primary">معالجة</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Info Panel -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">معلومات الموظف</h4>
                </div>
                <div class="card-body">
                    <div id="employee-info" style="display: none;">
                        <div class="text-center mb-3">
                            <img id="employee-photo" src="" alt="صورة الموظف" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                        </div>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>الاسم:</strong></td>
                                <td id="employee-name"></td>
                            </tr>
                            <tr>
                                <td><strong>القسم:</strong></td>
                                <td id="employee-department"></td>
                            </tr>
                            <tr>
                                <td><strong>الحالة:</strong></td>
                                <td id="attendance-status"></td>
                            </tr>
                        </table>

                        <!-- Action Buttons -->
                        <div class="text-center mt-3">
                            <button id="check-in-btn" class="btn btn-success btn-lg btn-block" style="display: none;">
                                <i class="fa fa-sign-in-alt"></i> تسجيل حضور
                            </button>
                            <button id="check-out-btn" class="btn btn-warning btn-lg btn-block" style="display: none;">
                                <i class="fa fa-sign-out-alt"></i> تسجيل انصراف
                            </button>
                            <div id="already-complete" class="alert alert-info" style="display: none;">
                                <i class="fa fa-check-circle"></i> تم إكمال الحضور والانصراف لهذا اليوم
                            </div>
                        </div>
                    </div>

                    <div id="no-employee" class="text-center text-muted">
                        <i class="fa fa-user-slash fa-3x mb-3"></i>
                        <p>لم يتم مسح أي باركود بعد</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="card-title">آخر العمليات</h4>
                </div>
                <div class="card-body">
                    <div id="recent-activity">
                        <p class="text-muted text-center">لا توجد عمليات حديثة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 id="barcode-scans">0</h3>
                    <p>مسح بالباركود</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- مكتبات الباركود -->
<script src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/barcode-scan.js') }}"></script>


<style>
#scanner {
    width: 100%;
    max-width: 500px;
    height: 300px;
    border: 2px solid #007bff;
    border-radius: 8px;
    margin: 0 auto;
}

.scanner-overlay {
    position: relative;
}

.scanner-overlay::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 200px;
    height: 2px;
    background: #ff0000;
    transform: translate(-50%, -50%);
    animation: scan-line 2s infinite;
}

@keyframes scan-line {
    0%, 100% { opacity: 0; }
    50% { opacity: 1; }
}

.employee-card {
    border: 2px solid #28a745;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.btn-scan-action {
    font-size: 1.1rem;
    padding: 12px 24px;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.btn-scan-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let codeReader = null;
    let currentEmployee = null;
    let scanning = false;

    // عناصر DOM
    const startCameraBtn = document.getElementById('start-camera');
    const stopCameraBtn = document.getElementById('stop-camera');
    const scannerDiv = document.getElementById('scanner');
    const scannerPlaceholder = document.getElementById('scanner-placeholder');
    const manualBarcodeInput = document.getElementById('manual-barcode');
    const processManualBtn = document.getElementById('process-manual');
    const employeeInfo = document.getElementById('employee-info');
    const noEmployee = document.getElementById('no-employee');
    const checkInBtn = document.getElementById('check-in-btn');
    const checkOutBtn = document.getElementById('check-out-btn');
    const alreadyComplete = document.getElementById('already-complete');

    // تحديث الوقت كل ثانية
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('ar-SA', {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        const dateString = now.toLocaleDateString('ar-SA', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        document.getElementById('current-time').textContent = timeString;
        document.getElementById('current-date').textContent = dateString;
    }

    // بدء تحديث الوقت
    updateTime();
    setInterval(updateTime, 1000);

    // تحميل الإحصائيات
    loadDashboardStats();
    setInterval(loadDashboardStats, 30000); // كل 30 ثانية

    // تشغيل الكاميرا
    startCameraBtn.addEventListener('click', function() {
        startScanning();
    });

    // إيقاف الكاميرا
    stopCameraBtn.addEventListener('click', function() {
        stopScanning();
    });

    // معالجة الباركود اليدوي
    processManualBtn.addEventListener('click', function() {
        const barcode = manualBarcodeInput.value.trim();
        if (barcode) {
            processBarcode(barcode);
        }
    });

    // معالجة الباركود عند الضغط على Enter
    manualBarcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            processManualBtn.click();
        }
    });

    // تسجيل الحضور
    checkInBtn.addEventListener('click', function() {
        if (currentEmployee) {
            performCheckIn(currentEmployee.id);
        }
    });

    // تسجيل الانصراف
    checkOutBtn.addEventListener('click', function() {
        if (currentEmployee) {
            performCheckOut(currentEmployee.id);
        }
    });

    /**
     * بدء مسح الباركود
     */
    function startScanning() {
        if (scanning) return;

        codeReader = new ZXing.BrowserBarcodeReader();

        scannerPlaceholder.style.display = 'none';
        scannerDiv.style.display = 'block';
        startCameraBtn.style.display = 'none';
        stopCameraBtn.style.display = 'inline-block';

        codeReader.decodeFromVideoDevice(null, 'scanner', (result, err) => {
            if (result) {
                // تم مسح الباركود بنجاح
                playSuccessSound();
                vibrateDevice();
                processBarcode(result.text);

                // إيقاف المسح مؤقتاً لمنع المسح المكرر
                setTimeout(() => {
                    if (scanning) {
                        codeReader.reset();
                        startScanning();
                    }
                }, 2000);
            }

            if (err && !(err instanceof ZXing.NotFoundException)) {
                console.error('خطأ في المسح:', err);
            }
        });

        scanning = true;
    }

    /**
     * إيقاف مسح الباركود
     */
    function stopScanning() {
        if (codeReader) {
            codeReader.reset();
            codeReader = null;
        }

        scanning = false;
        scannerDiv.style.display = 'none';
        scannerPlaceholder.style.display = 'block';
        startCameraBtn.style.display = 'inline-block';
        stopCameraBtn.style.display = 'none';
    }

    /**
     * معالجة الباركود
     */
    function processBarcode(barcode) {
        showLoading('جاري التحقق من الباركود...');

        fetch('{{ route("barcode.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ barcode: barcode })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();

            if (data.success) {
                currentEmployee = data.employee;
                displayEmployeeInfo(data.employee, data.attendance_status);
                manualBarcodeInput.value = '';
            } else {
                showError(data.message || 'فشل في معالجة الباركود');
                currentEmployee = null;
                hideEmployeeInfo();
            }
        })
        .catch(error => {
            hideLoading();
            console.error('خطأ:', error);
            showError('حدث خطأ في الاتصال بالخادم');
        });
    }

    /**
     * عرض معلومات الموظف
     */
    function displayEmployeeInfo(employee, attendanceStatus) {
        // إخفاء رسالة "لا يوجد موظف"
        noEmployee.style.display = 'none';
        employeeInfo.style.display = 'block';

        // عرض معلومات الموظف
        document.getElementById('employee-name').textContent = employee.name;
        document.getElementById('employee-department').textContent = employee.department;

        // عرض صورة الموظف
        const photoElement = document.getElementById('employee-photo');
        if (employee.photo) {
            photoElement.src = `/storage/${employee.photo}`;
        } else {
            photoElement.src = '/images/default-avatar.png';
        }

        // تحديث حالة الحضور والأزرار
        updateAttendanceButtons(attendanceStatus);

        // إضافة تأثير بصري
        employeeInfo.classList.add('employee-card');
        setTimeout(() => {
            employeeInfo.classList.remove('employee-card');
        }, 3000);
    }

    /**
     * تحديث أزرار الحضور والانصراف
     */
    function updateAttendanceButtons(attendanceStatus) {
        // إخفاء جميع الأزرار أولاً
        checkInBtn.style.display = 'none';
        checkOutBtn.style.display = 'none';
        alreadyComplete.style.display = 'none';

        let statusText = '';

        if (!attendanceStatus.is_checked_in && !attendanceStatus.is_checked_out) {
            // لم يسجل حضور بعد
            checkInBtn.style.display = 'block';
            statusText = 'لم يسجل حضور';
        } else if (attendanceStatus.is_checked_in && !attendanceStatus.is_checked_out) {
            // مسجل حضور ولم ينصرف بعد
            checkOutBtn.style.display = 'block';
            statusText = `حاضر منذ ${attendanceStatus.check_in_time}`;
        } else if (attendanceStatus.is_checked_in && attendanceStatus.is_checked_out) {
            // مكتمل الحضور والانصراف
            alreadyComplete.style.display = 'block';
            statusText = `مكتمل - حضور: ${attendanceStatus.check_in_time}, انصراف: ${attendanceStatus.check_out_time}`;
        }

        document.getElementById('attendance-status').textContent = statusText;
    }

    /**
     * إخفاء معلومات الموظف
     */
    function hideEmployeeInfo() {
        employeeInfo.style.display = 'none';
        noEmployee.style.display = 'block';
        currentEmployee = null;
    }

    /**
     * تسجيل حضور
     */
    function performCheckIn(employeeId) {
        showLoading('جاري تسجيل الحضور...');

        fetch('{{ route("barcode.checkin") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                employee_id: employeeId,
                method: scanning ? 'barcode' : 'manual'
            })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();

            if (data.success) {
                showSuccess('تم تسجيل الحضور بنجاح!', data.data);
                // تحديث الأزرار
                checkInBtn.style.display = 'none';
                checkOutBtn.style.display = 'block';
                document.getElementById('attendance-status').textContent = `حاضر منذ ${data.data.check_in_time}`;

                // تحديث الإحصائيات
                loadDashboardStats();
                addToRecentActivity('حضور', data.data.employee_name, data.data.check_in_time);
            } else {
                showError(data.message || 'فشل في تسجيل الحضور');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('خطأ:', error);
            showError('حدث خطأ في الاتصال بالخادم');
        });
    }

    /**
     * تسجيل انصراف
     */
    function performCheckOut(employeeId) {
        showLoading('جاري تسجيل الانصراف...');

        fetch('{{ route("barcode.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                employee_id: employeeId,
                method: scanning ? 'barcode' : 'manual'
            })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();

            if (data.success) {
                showSuccess('تم تسجيل الانصراف بنجاح!', data.data);
                // تحديث الأزرار
                checkOutBtn.style.display = 'none';
                alreadyComplete.style.display = 'block';
                document.getElementById('attendance-status').textContent =
                    `مكتمل - حضور: ${data.data.check_in_time}, انصراف: ${data.data.check_out_time}`;

                // تحديث الإحصائيات
                loadDashboardStats();
                addToRecentActivity('انصراف', data.data.employee_name, data.data.check_out_time);
            } else {
                showError(data.message || 'فشل في تسجيل الانصراف');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('خطأ:', error);
            showError('حدث خطأ في الاتصال بالخادم');
        });
    }

    /**
     * تحميل إحصائيات الداشبورد
     */
    function loadDashboardStats() {
        fetch('{{ route("barcode.stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('present-count').textContent = data.employees_present_today || 0;
            document.getElementById('late-count').textContent = data.late_arrivals_today || 0;
            document.getElementById('absent-count').textContent = data.total_employees - data.total_check_ins_today || 0;
            document.getElementById('barcode-scans').textContent = data.barcode_scans_today || 0;
        })
        .catch(error => {
            console.error('خطأ في تحميل الإحصائيات:', error);
        });
    }

    /**
     * إضافة نشاط جديد للقائمة
     */
    function addToRecentActivity(action, employeeName, time) {
        const activityDiv = document.getElementById('recent-activity');
        const activityItem = document.createElement('div');
        activityItem.className = 'border-bottom pb-2 mb-2';
        activityItem.innerHTML = `
            <small class="text-muted">${new Date().toLocaleTimeString('ar-SA')}</small><br>
            <strong>${employeeName}</strong><br>
            <span class="badge badge-${action === 'حضور' ? 'success' : 'warning'}">${action}</span>
            <span class="text-muted">${time}</span>
        `;

        // إضافة العنصر في المقدمة
        if (activityDiv.children.length === 0 || activityDiv.children[0].tagName === 'P') {
            activityDiv.innerHTML = '';
        }
        activityDiv.insertBefore(activityItem, activityDiv.firstChild);

        // الحفاظ على آخر 5 عناصر فقط
        while (activityDiv.children.length > 5) {
            activityDiv.removeChild(activityDiv.lastChild);
        }
    }

    /**
     * تشغيل صوت النجاح
     */
    function playSuccessSound() {
        try {
            const audio = new Audio('/sounds/beep-success.mp3');
            audio.play().catch(e => console.log('لا يمكن تشغيل الصوت'));
        } catch (e) {
            console.log('ملف الصوت غير موجود');
        }
    }

    /**
     * اهتزاز الجهاز
     */
    function vibrateDevice() {
        if ('vibrate' in navigator) {
            navigator.vibrate(200);
        }
    }

    /**
     * عرض رسالة تحميل
     */
    function showLoading(message) {
        Swal.fire({
            title: message,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    /**
     * إخفاء رسالة التحميل
     */
    function hideLoading() {
        Swal.close();
    }

    /**
     * عرض رسالة نجاح
     */
    function showSuccess(message, data = null) {
        let html = `<p>${message}</p>`;
        if (data) {
            html += `
                <div class="text-left mt-3">
                    <strong>الموظف:</strong> ${data.employee_name}<br>
                    <strong>الوقت:</strong> ${data.check_in_time || data.check_out_time}<br>
                    ${data.working_hours ? `<strong>ساعات العمل:</strong> ${data.working_hours}` : ''}
                </div>
            `;
        }

        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح!',
            html: html,
            confirmButtonText: 'موافق',
            confirmButtonColor: '#28a745'
        });
    }

    /**
     * عرض رسالة خطأ
     */
    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: message,
            confirmButtonText: 'موافق',
            confirmButtonColor: '#dc3545'
        });
    }

    // التنظيف عند مغادرة الصفحة
    window.addEventListener('beforeunload', function() {
        if (codeReader) {
            codeReader.reset();
        }
    });
});
</script>
@endsection
