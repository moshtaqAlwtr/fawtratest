// public/js/barcode-scanner.js

class BarcodeAttendanceSystem {
    constructor() {
        this.codeReader = null;
        this.currentEmployee = null;
        this.scanning = false;
        this.audioEnabled = true;
        this.vibrationEnabled = true;

        this.init();
    }

    /**
     * تهيئة النظام
     */
    init() {
        this.setupElements();
        this.setupEventListeners();
        this.startTimeUpdater();
        this.loadSettings();

        console.log('نظام الباركود جاهز للاستخدام');
    }

    /**
     * إعداد عناصر DOM
     */
    setupElements() {
        this.elements = {
            startCameraBtn: document.getElementById('start-camera'),
            stopCameraBtn: document.getElementById('stop-camera'),
            scanner: document.getElementById('scanner'),
            scannerPlaceholder: document.getElementById('scanner-placeholder'),
            manualInput: document.getElementById('manual-barcode'),
            processManualBtn: document.getElementById('process-manual'),
            employeeInfo: document.getElementById('employee-info'),
            noEmployee: document.getElementById('no-employee'),
            checkInBtn: document.getElementById('check-in-btn'),
            checkOutBtn: document.getElementById('check-out-btn'),
            alreadyComplete: document.getElementById('already-complete'),
            currentTime: document.getElementById('current-time'),
            currentDate: document.getElementById('current-date'),
            recentActivity: document.getElementById('recent-activity')
        };
    }

    /**
     * إعداد مستمعي الأحداث
     */
    setupEventListeners() {
        // أزرار الكاميرا
        this.elements.startCameraBtn?.addEventListener('click', () => this.startScanning());
        this.elements.stopCameraBtn?.addEventListener('click', () => this.stopScanning());

        // الإدخال اليدوي
        this.elements.processManualBtn?.addEventListener('click', () => this.processManualBarcode());
        this.elements.manualInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.processManualBarcode();
            }
        });

        // أزرار الحضور والانصراف
        this.elements.checkInBtn?.addEventListener('click', () => this.performCheckIn());
        this.elements.checkOutBtn?.addEventListener('click', () => this.performCheckOut());

        // تحديث الإحصائيات عند التركيز على النافذة
        window.addEventListener('focus', () => this.loadDashboardStats());

        // تنظيف الكاميرا عند إغلاق النافذة
        window.addEventListener('beforeunload', () => this.cleanup());
    }

    /**
     * بدء مسح الباركود
     */
    async startScanning() {
        try {
            if (this.scanning) return;

            // التحقق من دعم الكاميرا
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                this.showError('المتصفح لا يدعم الوصول للكاميرا');
                return;
            }

            // إنشاء قارئ الباركود
            this.codeReader = new ZXing.BrowserMultiFormatReader();

            // إعداد الواجهة
            this.elements.scannerPlaceholder.style.display = 'none';
            this.elements.scanner.style.display = 'block';
            this.elements.startCameraBtn.style.display = 'none';
            this.elements.stopCameraBtn.style.display = 'inline-block';

            // إضافة زوايا المسح
            this.addScannerOverlay();

            // بدء المسح
            const result = await this.codeReader.decodeFromVideoDevice(null, 'scanner', (result, err) => {
                if (result) {
                    this.handleBarcodeScanned(result.text);
                }

                if (err && !(err instanceof ZXing.NotFoundException)) {
                    console.warn('تحذير مسح الباركود:', err);
                }
            });

            this.scanning = true;
            this.showInfo('الكاميرا نشطة - امسح الباركود');

        } catch (error) {
            console.error('خطأ في تشغيل الكاميرا:', error);
            this.showError('فشل في تشغيل الكاميرا. تأكد من الأذونات.');
            this.resetCameraUI();
        }
    }

    /**
     * إيقاف مسح الباركود
     */
    stopScanning() {
        if (this.codeReader) {
            this.codeReader.reset();
            this.codeReader = null;
        }

        this.scanning = false;
        this.resetCameraUI();
        this.showInfo('تم إيقاف الكاميرا');
    }

    /**
     * إعادة تعيين واجهة الكاميرا
     */
    resetCameraUI() {
        this.elements.scanner.style.display = 'none';
        this.elements.scannerPlaceholder.style.display = 'block';
        this.elements.startCameraBtn.style.display = 'inline-block';
        this.elements.stopCameraBtn.style.display = 'none';
        this.removeScannerOverlay();
    }

    /**
     * إضافة تأثير المسح
     */
    addScannerOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'scanner-overlay';
        overlay.innerHTML = `
            <div class="scanner-corners">
                <div class="corner-tl"></div>
                <div class="corner-tr"></div>
                <div class="corner-bl"></div>
                <div class="corner-br"></div>
            </div>
        `;
        this.elements.scanner.appendChild(overlay);
    }

    /**
     * إزالة تأثير المسح
     */
    removeScannerOverlay() {
        const overlay = this.elements.scanner.querySelector('.scanner-overlay');
        if (overlay) {
            overlay.remove();
        }
    }

    /**
     * معالجة الباركود الممسوح
     */
    handleBarcodeScanned(barcode) {
        // منع المسح المكرر
        if (this.lastScannedBarcode === barcode &&
            Date.now() - this.lastScanTime < 3000) {
            return;
        }

        this.lastScannedBarcode = barcode;
        this.lastScanTime = Date.now();

        // تأثيرات النجاح
        this.playSuccessSound();
        this.vibrateDevice();
        this.flashSuccess();

        // معالجة الباركود
        this.processBarcode(barcode);

        // إيقاف المسح مؤقتاً
        this.temporaryPause();
    }

    /**
     * معالجة الباركود اليدوي
     */
    processManualBarcode() {
        const barcode = this.elements.manualInput.value.trim();
        if (!barcode) {
            this.showError('يرجى إدخال الباركود');
            return;
        }

        this.processBarcode(barcode);
        this.elements.manualInput.value = '';
    }

    /**
     * معالجة الباركود
     */
    async processBarcode(barcode) {
        try {
            this.showLoading('جاري التحقق من الباركود...');

            const response = await fetch('/barcode-attendance/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ barcode: barcode })
            });

            const data = await response.json();
            this.hideLoading();

            if (data.success) {
                this.currentEmployee = data.employee;
                this.displayEmployeeInfo(data.employee, data.attendance_status);
                this.addToRecentActivity('مسح باركود', data.employee.name, 'الآن');
            } else {
                this.showError(data.message || 'فشل في معالجة الباركود');
                this.currentEmployee = null;
                this.hideEmployeeInfo();
            }

        } catch (error) {
            this.hideLoading();
            console.error('خطأ في معالجة الباركود:', error);
            this.showError('حدث خطأ في الاتصال بالخادم');
        }
    }

    /**
     * عرض معلومات الموظف
     */
    displayEmployeeInfo(employee, attendanceStatus) {
        // إخفاء رسالة "لا يوجد موظف"
        this.elements.noEmployee.style.display = 'none';
        this.elements.employeeInfo.style.display = 'block';

        // تحديث المعلومات
        document.getElementById('employee-name').textContent = employee.name;
        document.getElementById('employee-department').textContent = employee.department;

        // تحديث الصورة
        const photoElement = document.getElementById('employee-photo');
        photoElement.src = employee.photo ?
            `/storage/${employee.photo}` :
            '/images/default-avatar.png';

        // تحديث أزرار الحضور
        this.updateAttendanceButtons(attendanceStatus);

        // تأثير بصري
        this.elements.employeeInfo.classList.add('employee-card', 'fade-in');
        setTimeout(() => {
            this.elements.employeeInfo.classList.remove('employee-card', 'fade-in');
        }, 3000);
    }

    /**
     * تحديث أزرار الحضور والانصراف
     */
    updateAttendanceButtons(attendanceStatus) {
        // إخفاء جميع الأزرار
        this.elements.checkInBtn.style.display = 'none';
        this.elements.checkOutBtn.style.display = 'none';
        this.elements.alreadyComplete.style.display = 'none';

        let statusText = '';
        let statusClass = '';

        if (!attendanceStatus.is_checked_in) {
            // لم يسجل حضور
            this.elements.checkInBtn.style.display = 'block';
            this.elements.checkInBtn.classList.add('btn-pulse');
            statusText = 'لم يسجل حضور اليوم';
            statusClass = 'text-warning';
        } else if (attendanceStatus.is_checked_in && !attendanceStatus.is_checked_out) {
            // حاضر ولم ينصرف
            this.elements.checkOutBtn.style.display = 'block';
            this.elements.checkOutBtn.classList.add('btn-pulse');
            statusText = `حاضر منذ ${attendanceStatus.check_in_time}`;
            statusClass = 'text-success';
        } else {
            // مكتمل
            this.elements.alreadyComplete.style.display = 'block';
            statusText = `مكتمل - حضور: ${attendanceStatus.check_in_time}, انصراف: ${attendanceStatus.check_out_time}`;
            statusClass = 'text-info';
        }

        const statusElement = document.getElementById('attendance-status');
        statusElement.textContent = statusText;
        statusElement.className = statusClass;
    }

    /**
     * إخفاء معلومات الموظف
     */
    hideEmployeeInfo() {
        this.elements.employeeInfo.style.display = 'none';
        this.elements.noEmployee.style.display = 'block';
        this.currentEmployee = null;

        // إزالة تأثيرات النبض
        this.elements.checkInBtn?.classList.remove('btn-pulse');
        this.elements.checkOutBtn?.classList.remove('btn-pulse');
    }

    /**
     * تسجيل حضور
     */
    async performCheckIn() {
        if (!this.currentEmployee) return;

        try {
            this.showLoading('جاري تسجيل الحضور...');

            const response = await fetch('/barcode-attendance/check-in', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    employee_id: this.currentEmployee.id,
                    method: this.scanning ? 'barcode' : 'manual'
                })
            });

            const data = await response.json();
            this.hideLoading();

            if (data.success) {
                this.showSuccess('تم تسجيل الحضور بنجاح!', data.data);
                this.updateButtonsAfterCheckIn(data.data);
                this.loadDashboardStats();
                this.addToRecentActivity('حضور', data.data.employee_name, data.data.check_in_time);
                this.playSuccessSound();
                this.vibrateDevice();
            } else {
                this.showError(data.message || 'فشل في تسجيل الحضور');
            }

        } catch (error) {
            this.hideLoading();
            console.error('خطأ في تسجيل الحضور:', error);
            this.showError('حدث خطأ في الاتصال بالخادم');
        }
    }

    /**
     * تسجيل انصراف
     */
    async performCheckOut() {
        if (!this.currentEmployee) return;

        try {
            this.showLoading('جاري تسجيل الانصراف...');

            const response = await fetch('/barcode-attendance/check-out', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    employee_id: this.currentEmployee.id,
                    method: this.scanning ? 'barcode' : 'manual'
                })
            });

            const data = await response.json();
            this.hideLoading();

            if (data.success) {
                this.showSuccess('تم تسجيل الانصراف بنجاح!', data.data);
                this.updateButtonsAfterCheckOut(data.data);
                this.loadDashboardStats();
                this.addToRecentActivity('انصراف', data.data.employee_name, data.data.check_out_time);
                this.playSuccessSound();
                this.vibrateDevice();
            } else {
                this.showError(data.message || 'فشل في تسجيل الانصراف');
            }

        } catch (error) {
            this.hideLoading();
            console.error('خطأ في تسجيل الانصراف:', error);
            this.showError('حدث خطأ في الاتصال بالخادم');
        }
    }

    /**
     * تحديث الأزرار بعد تسجيل الحضور
     */
    updateButtonsAfterCheckIn(data) {
        this.elements.checkInBtn.style.display = 'none';
        this.elements.checkInBtn.classList.remove('btn-pulse');
        this.elements.checkOutBtn.style.display = 'block';
        this.elements.checkOutBtn.classList.add('btn-pulse');

        const statusElement = document.getElementById('attendance-status');
        statusElement.textContent = `حاضر منذ ${data.check_in_time}`;
        statusElement.className = 'text-success';
    }

    /**
     * تحديث الأزرار بعد تسجيل الانصراف
     */
    updateButtonsAfterCheckOut(data) {
        this.elements.checkOutBtn.style.display = 'none';
        this.elements.checkOutBtn.classList.remove('btn-pulse');
        this.elements.alreadyComplete.style.display = 'block';

        const statusElement = document.getElementById('attendance-status');
        statusElement.textContent = `مكتمل - العمل: ${data.working_hours}`;
        statusElement.className = 'text-info';
    }

    /**
     * تحميل إحصائيات الداشبورد
     */
    async loadDashboardStats() {
        try {
            const response = await fetch('/barcode-attendance/stats');
            const data = await response.json();

            // تحديث الإحصائيات
            this.updateStatsCards(data);

        } catch (error) {
            console.error('خطأ في تحميل الإحصائيات:', error);
        }
    }

    /**
     * تحديث كاردات الإحصائيات
     */
    updateStatsCards(data) {
        const updateCard = (selector, value) => {
            const element = document.querySelector(selector);
            if (element) {
                const oldValue = parseInt(element.textContent) || 0;
                if (oldValue !== value) {
                    element.textContent = value;
                    element.closest('.card').classList.add('success-animation');
                    setTimeout(() => {
                        element.closest('.card').classList.remove('success-animation');
                    }, 600);
                }
            }
        };

        updateCard('#present-count', data.employees_present_today || 0);
        updateCard('#late-count', data.late_arrivals_today || 0);
        updateCard('#absent-count', (data.total_employees - data.total_check_ins_today) || 0);
        updateCard('#barcode-scans', data.barcode_scans_today || 0);
    }

    /**
     * إضافة نشاط جديد
     */
    addToRecentActivity(action, employeeName, time) {
        if (!this.elements.recentActivity) return;

        const activityItem = document.createElement('div');
        activityItem.className = 'recent-activity-item fade-in';

        const actionColor = action === 'حضور' ? 'success' :
                          action === 'انصراف' ? 'warning' : 'primary';

        activityItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>${employeeName}</strong>
                    <small class="d-block text-muted">${new Date().toLocaleTimeString('ar-SA')}</small>
                </div>
                <span class="badge badge-${actionColor}">${action}</span>
            </div>
        `;

        // إضافة في المقدمة
        if (this.elements.recentActivity.children.length === 0 ||
            this.elements.recentActivity.children[0].tagName === 'P') {
            this.elements.recentActivity.innerHTML = '';
        }

        this.elements.recentActivity.insertBefore(activityItem, this.elements.recentActivity.firstChild);

        // الحفاظ على آخر 5 عناصر
        while (this.elements.recentActivity.children.length > 5) {
            this.elements.recentActivity.removeChild(this.elements.recentActivity.lastChild);
        }
    }

    /**
     * تحديث الوقت
     */
    startTimeUpdater() {
        const updateTime = () => {
            const now = new Date();

            if (this.elements.currentTime) {
                this.elements.currentTime.textContent = now.toLocaleTimeString('ar-SA', {
                    hour12: false,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }

            if (this.elements.currentDate) {
                this.elements.currentDate.textContent = now.toLocaleDateString('ar-SA', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
        };

        updateTime();
        setInterval(updateTime, 1000);
    }
}
