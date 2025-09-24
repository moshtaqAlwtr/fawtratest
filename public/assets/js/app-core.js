/**
 * App Core - الإعدادات الأساسية والثوابت
 * يحتوي على الإعدادات العامة والدوال المساعدة
 */

// إعدادات التطبيق
window.AppConfig = {
    // إعدادات الخريطة
    map: {
        defaultCenter: { lat: 24.7136, lng: 46.6753 }, // الرياض
        defaultZoom: {
            desktop: 11,
            tablet: 10,
            mobile: 9
        },
        maxZoom: {
            desktop: 14,
            tablet: 13,
            mobile: 13
        },
        gestureHandling: {
            desktop: 'greedy',
            mobile: 'cooperative'
        },
        breakpoints: {
            mobile: 768,
            tablet: 992
        }
    },

    // إعدادات البحث
    search: {
        debounceTime: 500,
        minSearchLength: 1
    },

    // إعدادات الإشعارات
    notifications: {
        autoHideTime: 5000,
        position: {
            top: '20px',
            right: '20px'
        }
    },

    // إعدادات التحميل
    loading: {
        fadeTime: 200,
        retryAttempts: 3,
        retryDelay: 2000
    },

    // إعدادات العملاء المخفيين
    hiddenClients: {
        hideTime: 24 * 60 * 60 * 1000, // 24 ساعة
        animationTime: 300
    }
};

// دوال مساعدة عامة
window.AppUtils = {
    /**
     * التحقق من نوع الجهاز
     */
    getDeviceType: () => {
        const width = window.innerWidth;
        if (width <= AppConfig.map.breakpoints.mobile) return 'mobile';
        if (width <= AppConfig.map.breakpoints.tablet) return 'tablet';
        return 'desktop';
    },

    /**
     * التحقق من كون الجهاز محمول
     */
    isMobile: () => {
        return window.innerWidth <= AppConfig.map.breakpoints.mobile;
    },

    /**
     * التحقق من كون الجهاز لوحي
     */
    isTablet: () => {
        const width = window.innerWidth;
        return width > AppConfig.map.breakpoints.mobile && width <= AppConfig.map.breakpoints.tablet;
    },

    /**
     * التحقق من كون الجهاز سطح مكتب
     */
    isDesktop: () => {
        return window.innerWidth > AppConfig.map.breakpoints.tablet;
    },

    /**
     * تنسيق التاريخ
     */
    formatDate: (date, locale = 'ar-SA') => {
        if (!date) return 'غير محدد';
        try {
            return new Date(date).toLocaleString(locale);
        } catch (error) {
            console.error('خطأ في تنسيق التاريخ:', error);
            return 'تاريخ غير صحيح';
        }
    },

    /**
     * التحقق من صحة اللون
     */
    isValidColor: (color) => {
        if (!color) return false;

        // فحص الألوان hex
        const validHex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
        if (validHex.test(color)) return true;

        // فحص الألوان المعيارية
        const standardColors = [
            'red', 'green', 'blue', 'yellow', 'orange',
            'purple', 'pink', 'brown', 'black', 'white',
            'gray', 'grey'
        ];
        if (standardColors.includes(color.toLowerCase())) return true;

        // فحص ألوان rgb
        if (color.startsWith('rgb')) return true;

        return false;
    },

    /**
     * الحصول على لون افتراضي آمن
     */
    getSafeColor: (color, defaultColor = '#4361ee') => {
        return AppUtils.isValidColor(color) ? color : defaultColor;
    },

    /**
     * تنسيق النص العربي
     */
    formatArabicText: (text) => {
        if (!text) return '';
        return text.trim().replace(/\s+/g, ' ');
    },

    /**
     * اختصار النص
     */
    truncateText: (text, maxLength = 50) => {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    },

    /**
     * التحقق من وجود الإنترنت
     */
    isOnline: () => {
        return navigator.onLine;
    },

    /**
     * إنشاء معرف فريد
     */
    generateUniqueId: () => {
        return 'id_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
    },

    /**
     * تأخير التنفيذ
     */
    delay: (ms) => {
        return new Promise(resolve => setTimeout(resolve, ms));
    },

    /**
     * تنظيف HTML
     */
    sanitizeHtml: (html) => {
        const div = document.createElement('div');
        div.textContent = html;
        return div.innerHTML;
    },

    /**
     * التحقق من وجود عنصر في DOM
     */
    elementExists: (selector) => {
        return document.querySelector(selector) !== null;
    },

    /**
     * انتظار تحميل عنصر
     */
    waitForElement: async (selector, timeout = 5000) => {
        return new Promise((resolve, reject) => {
            const element = document.querySelector(selector);
            if (element) {
                resolve(element);
                return;
            }

            const observer = new MutationObserver((mutations) => {
                const element = document.querySelector(selector);
                if (element) {
                    observer.disconnect();
                    resolve(element);
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            setTimeout(() => {
                observer.disconnect();
                reject(new Error(`لم يتم العثور على العنصر ${selector} خلال ${timeout}ms`));
            }, timeout);
        });
    },

    /**
     * نسخ نص إلى الحافظة
     */
    copyToClipboard: async (text) => {
        try {
            if (navigator.clipboard) {
                await navigator.clipboard.writeText(text);
                return true;
            } else {
                // طريقة بديلة للمتصفحات القديمة
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    return true;
                } catch (err) {
                    document.body.removeChild(textArea);
                    return false;
                }
            }
        } catch (err) {
            console.error('فشل في النسخ:', err);
            return false;
        }
    },

    /**
     * تحويل البيانات إلى JSON بأمان
     */
    safeJsonParse: (jsonString, defaultValue = null) => {
        try {
            return JSON.parse(jsonString);
        } catch (error) {
            console.error('خطأ في تحليل JSON:', error);
            return defaultValue;
        }
    },

    /**
     * تحويل البيانات إلى نص JSON بأمان
     */
    safeJsonStringify: (data, defaultValue = '{}') => {
        try {
            return JSON.stringify(data);
        } catch (error) {
            console.error('خطأ في تحويل JSON:', error);
            return defaultValue;
        }
    },

    /**
     * فحص الاتصال بالإنترنت
     */
    checkInternetConnection: async () => {
        try {
            const response = await fetch('/ping', {
                method: 'HEAD',
                cache: 'no-cache'
            });
            return response.ok;
        } catch (error) {
            return false;
        }
    }
};

// أحداث عامة للتطبيق
window.AppEvents = {
    // حدث تحميل الخريطة
    MAP_LOADED: 'app:map_loaded',
    MAP_ERROR: 'app:map_error',

    // أحداث البحث
    SEARCH_START: 'app:search_start',
    SEARCH_COMPLETE: 'app:search_complete',
    SEARCH_ERROR: 'app:search_error',

    // أحداث العملاء المخفيين
    CLIENT_HIDDEN: 'app:client_hidden',
    CLIENT_SHOWN: 'app:client_shown',

    // أحداث الترقيم
    PAGE_CHANGED: 'app:page_changed',

    // أحداث عامة
    APP_READY: 'app:ready',
    APP_ERROR: 'app:error'
};

// نظام إدارة الأحداث
window.AppEventManager = {
    listeners: {},

    /**
     * إضافة مستمع للحدث
     */
    on: (event, callback) => {
        if (!AppEventManager.listeners[event]) {
            AppEventManager.listeners[event] = [];
        }
        AppEventManager.listeners[event].push(callback);
    },

    /**
     * إزالة مستمع الحدث
     */
    off: (event, callback) => {
        if (!AppEventManager.listeners[event]) return;

        const index = AppEventManager.listeners[event].indexOf(callback);
        if (index > -1) {
            AppEventManager.listeners[event].splice(index, 1);
        }
    },

    /**
     * إطلاق حدث
     */
    emit: (event, data = null) => {
        if (!AppEventManager.listeners[event]) return;

        AppEventManager.listeners[event].forEach(callback => {
            try {
                callback(data);
            } catch (error) {
                console.error(`خطأ في معالج الحدث ${event}:`, error);
            }
        });
    },

    /**
     * مسح جميع المستمعين
     */
    clear: () => {
        AppEventManager.listeners = {};
    }
};

// معالج الأخطاء العام
window.AppErrorHandler = {
    /**
     * تسجيل خطأ
     */
    logError: (error, context = '') => {
        console.error(`[${context}] خطأ:`, error);

        // يمكن إضافة إرسال الأخطاء لخدمة المراقبة هنا
        // مثل Sentry أو LogRocket
    },

    /**
     * معالجة خطأ AJAX
     */
    handleAjaxError: (xhr, status, error, context = '') => {
        let message = 'حدث خطأ غير متوقع';

        if (xhr.status === 0) {
            message = 'فقدان الاتصال بالإنترنت';
        } else if (xhr.status === 404) {
            message = 'الصفحة المطلوبة غير موجودة';
        } else if (xhr.status === 500) {
            message = 'خطأ في الخادم';
        } else if (status === 'timeout') {
            message = 'انتهت مهلة الطلب';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
        }

        AppErrorHandler.logError(`${status}: ${error}`, context);
        return message;
    }
};

// تهيئة معالج الأخطاء العام
window.addEventListener('error', (event) => {
    AppErrorHandler.logError(event.error, 'Global Error Handler');
});

window.addEventListener('unhandledrejection', (event) => {
    AppErrorHandler.logError(event.reason, 'Unhandled Promise Rejection');
});

// إعلام أن النواة جاهزة
console.log('✅ تم تحميل نواة التطبيق');
AppEventManager.emit(AppEvents.APP_READY);
