/**
 * Hidden Clients Manager - مدير العملاء المخفيين
 * يدير عمليات إخفاء وإظهار العملاء من الخريطة
 */

class HiddenClientsManager {
    constructor() {
        this.hiddenClients = [];
    }

    /**
     * تهيئة مدير العملاء المخفيين
     */
    init() {
        this.loadHiddenClients();
        this.bindEvents();
    }

    /**
     * ربط الأحداث
     */
    bindEvents() {
        // عند الضغط على زر الإخفاء في الكارد
        $(document).on('click', '.hide-from-map-btn, .hide-from-map-link', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const clientId = $(e.currentTarget).data('client-id');
            const clientName = $(e.currentTarget).data('client-name');

            this.hideClient(clientId, clientName);
        });

        // عند الضغط على زر الإظهار في الكونتينر
        $(document).on('click', '.show-client-btn', (e) => {
            const clientId = $(e.currentTarget).data('client-id');
            this.showClient(clientId);
        });

        // تحديث أيقونة السهم عند فتح/إغلاق الكونتينر
        $('#hiddenClientsCollapse').on('shown.bs.collapse', () => {
            $('#toggleIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        });

        $('#hiddenClientsCollapse').on('hidden.bs.collapse', () => {
            $('#toggleIcon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        });

        // تحديث النظام عند تحديث الصفحة بـ AJAX
        $(document).ajaxComplete((event, xhr, settings) => {
            // التحقق من أن الطلب يخص صفحة العملاء
            if (settings.url && settings.url.includes('clients') &&
                xhr.responseJSON && xhr.responseJSON.html &&
                !settings.url.includes('hide-from-map') &&
                !settings.url.includes('show-in-map')) {

                // إعادة إخفاء العملاء المخفية بعد تحديث AJAX
                setTimeout(() => {
                    this.hiddenClients.forEach(client => {
                        $(`.client-card[data-client-id="${client.id}"]`).addClass('hidden').hide();
                        // إخفاء من الخريطة أيضاً
                        this.hideClientFromMap(client.id);
                    });
                }, 200);
            }
        });
    }

    /**
     * إخفاء عميل
     */
    hideClient(clientId, clientName) {
        // تحضير URL
        const url = window.clientRoutes.hideFromMap.replace(':id', clientId);

        // إرسال طلب للخادم
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: window.csrfToken
            },
            beforeSend: () => {
                // إظهار مؤشر التحميل على الزر
                $(`.hide-from-map-btn[data-client-id="${clientId}"]`)
                    .prop('disabled', true)
                    .addClass('loading')
                    .html('<i class="fas fa-spinner fa-spin me-1"></i> جاري الإخفاء...');
            },
            success: (response) => {
                if (response.success) {
                    // إضافة العميل للقائمة المحلية
                    const client = {
                        id: clientId,
                        name: clientName,
                        code: this.getClientCode(clientId),
                        hidden_at: new Date().toLocaleString('ar-SA'),
                        expires_at: new Date(Date.now() + 24 * 60 * 60 * 1000).toLocaleString('ar-SA')
                    };

                    this.hiddenClients.push(client);

                    // إخفاء الكارد من الصفحة
                    $(`.client-card[data-client-id="${clientId}"]`).addClass('hidden').fadeOut(300);

                    // تحديث الخريطة - إخفاء العميل من الخريطة
                    this.hideClientFromMap(clientId);

                    // تحديث العرض
                    this.updateDisplay();

                    // إظهار الإشعار
                    this.showNotification(`تم إخفاء العميل: ${clientName} لمدة 24 ساعة`, 'success');

                    // فتح الكونتينر إذا كان مغلقاً
                    if (!$('#hiddenClientsCollapse').hasClass('show')) {
                        $('#hiddenClientsCollapse').collapse('show');
                    }
                } else {
                    this.showNotification(response.message || 'حدث خطأ في إخفاء العميل', 'error');
                }
            },
            error: (xhr) => {
                let errorMessage = 'حدث خطأ في الاتصال بالخادم';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                this.showNotification(errorMessage, 'error');
            },
            complete: () => {
                // إعادة تفعيل الزر
                $(`.hide-from-map-btn[data-client-id="${clientId}"]`)
                    .prop('disabled', false)
                    .removeClass('loading')
                    .html('<i class="fas fa-eye-slash me-1"></i> إخفاء');
            }
        });
    }

    /**
     * إظهار عميل
     */
    showClient(clientId) {
        // تحضير URL
        const url = window.clientRoutes.showInMap.replace(':id', clientId);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: window.csrfToken
            },
            beforeSend: () => {
                // إظهار مؤشر التحميل على الزر
                $(`.show-client-btn[data-client-id="${clientId}"]`)
                    .prop('disabled', true)
                    .addClass('loading')
                    .html('<i class="fas fa-spinner fa-spin me-1"></i> جاري الإظهار...');
            },
            success: (response) => {
                if (response.success) {
                    // إزالة العميل من القائمة المحلية
                    this.hiddenClients = this.hiddenClients.filter(client => client.id != clientId);

                    // إظهار الكارد في الصفحة
                    $(`.client-card[data-client-id="${clientId}"]`).removeClass('hidden').fadeIn(300);

                    // تحديث الخريطة - إظهار العميل في الخريطة
                    this.showClientInMap(clientId);

                    // تحديث العرض
                    this.updateDisplay();

                    // إظهار الإشعار
                    this.showNotification(`تم إظهار العميل: ${response.client_name}`, 'success');
                } else {
                    this.showNotification(response.message || 'حدث خطأ في إظهار العميل', 'error');
                }
            },
            error: (xhr) => {
                let errorMessage = 'حدث خطأ في الاتصال بالخادم';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                this.showNotification(errorMessage, 'error');
            },
            complete: () => {
                // إعادة تفعيل الزر
                $(`.show-client-btn[data-client-id="${clientId}"]`)
                    .prop('disabled', false)
                    .removeClass('loading')
                    .html('<i class="fas fa-eye me-1"></i> إظهار');
            }
        });
    }

    /**
     * إخفاء العميل من الخريطة
     */
    hideClientFromMap(clientId) {
        if (window.mapManager && window.mapManager.getIsMapLoaded()) {
            window.mapManager.hideClientFromMap(clientId);
        }
    }

    /**
     * إظهار العميل في الخريطة
     */
    showClientInMap(clientId) {
        if (window.mapManager && window.mapManager.getIsMapLoaded()) {
            window.mapManager.showClientInMap(clientId);
        }
    }

    /**
     * تحميل العملاء المخفيين من الخادم
     */
    loadHiddenClients() {
        $.ajax({
            url: window.clientRoutes.getHiddenClients,
            type: 'GET',
            success: (response) => {
                if (response.success) {
                    this.hiddenClients = response.hidden_clients;
                    this.updateDisplay();

                    // إخفاء الكاردات المخفية من العرض
                    this.hiddenClients.forEach(client => {
                        $(`.client-card[data-client-id="${client.id}"]`).addClass('hidden').hide();
                        // إخفاء العميل من الخريطة أيضاً
                        this.hideClientFromMap(client.id);
                    });
                }
            },
            error: () => {
                console.log('فشل في تحميل العملاء المخفيين');
            }
        });
    }

    /**
     * تحديث عرض الكونتينر
     */
    updateDisplay() {
        const container = $('#hiddenClientsContainer');
        const list = $('#hiddenClientsList');
        const badge = $('#hiddenCountBadge');

        if (this.hiddenClients.length === 0) {
            container.removeClass('show').addClass('hide').hide();
            badge.hide();
            list.html(`
                <div class="text-center text-muted py-3" id="emptyMessage">
                    <i class="fas fa-eye fa-2x mb-2"></i>
                    <div>لا يوجد عملاء مخفيين حالياً</div>
                </div>
            `);
        } else {
            container.removeClass('hide').addClass('show').show();
            badge.text(this.hiddenClients.length).show();

            const html = this.hiddenClients.map(client => `
                <div class="hidden-client-item">
                    <div class="client-info flex-grow-1">
                        <h6 class="fw-bold text-primary mb-1">${client.name}</h6>
                        <div class="client-meta mb-1">
                            <i class="fas fa-barcode me-1"></i>
                            كود: ${client.code || '---'}
                        </div>
                        <div class="client-meta">
                            <i class="fas fa-clock me-1"></i>
                            تم الإخفاء: ${client.hidden_at}
                        </div>
                    </div>
                    <div>
                        <button class="show-client-btn" data-client-id="${client.id}">
                            <i class="fas fa-eye me-1"></i>
                            إظهار
                        </button>
                    </div>
                </div>
            `).join('');

            list.html(html);
        }
    }

    /**
     * الحصول على كود العميل من الكارد
     */
    getClientCode(clientId) {
        const card = $(`.client-card[data-client-id="${clientId}"]`);
        const codeElement = card.find('h7');
        return codeElement.length > 0 ? codeElement.text().trim() : '---';
    }

    /**
     * إظهار الإشعارات
     */
    showNotification(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        };

        const iconClass = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        };

        const notification = $(`
            <div class="alert ${alertClass[type]} alert-dismissible fade show notification" role="alert">
                <i class="fas ${iconClass[type]} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

        $('body').append(notification);

        // إزالة الإشعار تلقائياً بعد 5 ثواني
        setTimeout(() => {
            notification.alert('close');
        }, 5000);
    }

    /**
     * الحصول على العملاء المخفيين
     */
    getHiddenClients() {
        return this.hiddenClients;
    }

    /**
     * التحقق من كون العميل مخفي
     */
    isClientHidden(clientId) {
        return this.hiddenClients.some(client => client.id == clientId);
    }

    /**
     * تنظيف الموارد
     */
    cleanup() {
        this.hiddenClients = [];
    }
}

// تصدير الكلاس
window.HiddenClientsManager = HiddenClientsManager;
