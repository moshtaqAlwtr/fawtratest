/**
 * App Initialization - تهيئة التطبيق الرئيسي
 * يدير تهيئة جميع المديرين والأنظمة
 */

// المتغيرات العامة
window.mapManager = null;
window.searchManager = null;
window.hiddenClientsManager = null;
window.paginationManager = null;

// تهيئة Google Maps
window.initMap = function() {
    if (window.mapManager) {
        window.mapManager.initializeMap();
    }
};

// معالج أخطاء Google Maps
window.gm_authFailure = function() {
    console.error('❌ خطأ في مصادقة Google Maps API');
    if (window.mapManager) {
        window.mapManager.showMapError();
    }
};

// تهيئة التطبيق عند تحميل DOM
$(document).ready(function() {
    initializeApp();
});

/**
 * تهيئة التطبيق الرئيسي
 */
function initializeApp() {
    console.log('🚀 بدء تهيئة تطبيق إدارة العملاء');

    try {
        // تهيئة المديرين
        initializeManagers();

        // ربط الأحداث العامة
        bindGlobalEvents();

        // تحميل Google Maps
        loadGoogleMaps();

        // دعم زر الرجوع في المتصفح
        setupBrowserHistory();

        console.log('✅ تم تهيئة التطبيق بنجاح');

    } catch (error) {
        console.error('❌ خطأ في تهيئة التطبيق:', error);
        showGlobalError('حدث خطأ في تهيئة التطبيق. يرجى إعادة تحميل الصفحة.');
    }
}

/**
 * تهيئة المديرين
 */
function initializeManagers() {
    // تهيئة مدير البحث
    window.searchManager = new SearchManager();
    window.searchManager.init();

    // تهيئة مدير الخريطة
    window.mapManager = new MapManager();
    window.mapManager.init();

    // تهيئة مدير العملاء المخفيين
    window.hiddenClientsManager = new HiddenClientsManager();
    window.hiddenClientsManager.init();

    // تهيئة مدير الترقيم
    window.paginationManager = new PaginationManager();
    window.paginationManager.init();
}

/**
 * ربط الأحداث العامة
 */
function bindGlobalEvents() {
    // معالج الأخطاء العام
    window.addEventListener('error', handleGlobalError);

    // معالج تغيير حجم الشاشة
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleWindowResize, 250);
    });

    // تنظيف الذاكرة عند مغادرة الصفحة
    window.addEventListener('beforeunload', cleanup);

    // معالج الترقيم
    $(document).on('click', '.pagination-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && window.paginationManager && !window.searchManager.getIsLoading()) {
            window.paginationManager.goToPage(page);
        }
    });

    // تصدير Excel
    $('#exportExcelBtn').on('click', handleExcelExport);

    // دوال UI المساعدة
    window.toggleSearchFields = function(button) {
        if (window.searchManager) {
            window.searchManager.toggleSearchFields(button);
        }
    };

    window.toggleSearchText = function(button) {
        if (window.searchManager) {
            window.searchManager.toggleSearchText(button);
        }
    };

    window.openMap = function(lat, lng) {
        if (window.mapManager) {
            window.mapManager.openDirections(lat, lng);
        }
    };

    window.retryLoadMap = function() {
        if (window.mapManager) {
            window.mapManager.retryLoadMap();
        }
    };
}

/**
 * تحميل Google Maps
 */
function loadGoogleMaps() {
    // التحقق من وجود مفتاح API
    if (!window.googleMapsApiKey) {
        console.error('❌ مفتاح Google Maps API غير متوفر');
        return;
    }

    // التحقق من عدم وجود سكريبت محمل مسبقاً
    if (document.querySelector('script[src*="maps.googleapis.com"]')) {
        console.log('📍 Google Maps محمل مسبقاً');
        return;
    }

    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${window.googleMapsApiKey}&libraries=places&callback=window.initMap`;
    script.async = true;
    script.defer = true;

    script.onerror = function() {
        console.error('❌ فشل في تحميل Google Maps');
        if (window.mapManager) {
            window.mapManager.showMapError();
        }
    };

    script.onload = function() {
        console.log('✅ تم تحميل Google Maps بنجاح');
    };

    document.head.appendChild(script);
}

/**
 * دعم زر الرجوع في المتصفح
 */
function setupBrowserHistory() {
    window.addEventListener('popstate', function() {
        if (window.searchManager) {
            window.searchManager.updateCurrentFilters();
            window.searchManager.performSearch();
        }
    });
}

/**
 * معالج تغيير حجم الشاشة
 */
function handleWindowResize() {
    // تحديث حالة الكارد حسب حالة الخريطة
    const isMapOpen = localStorage.getItem('mapOpen') === 'true';
    const actionCard = $('#actionCard');

    if (isMapOpen) {
        actionCard.addClass('map-open').removeClass('map-closed');
    } else {
        actionCard.addClass('map-closed').removeClass('map-open');
    }

    // إعلام مدير الخريطة بتغيير الحجم
    if (window.mapManager && window.mapManager.getIsMapLoaded()) {
        window.mapManager.handleResize();
    }
}

/**
 * معالج الأخطاء العام
 */
function handleGlobalError(event) {
    console.error('خطأ عام:', event.error);

    // معالجة خاصة لأخطاء Google Maps
    if (event.error && event.error.message &&
        (event.error.message.includes('google') || event.error.message.includes('map'))) {
        setTimeout(() => {
            if (window.mapManager && !window.mapManager.getIsMapLoaded() && typeof window.initMap === 'function') {
                window.initMap();
            }
        }, 2000);
    }
}

/**
 * معالج تصدير Excel
 */
function handleExcelExport() {
    const button = $(this);
    const originalHtml = button.html();

    // إظهار مؤشر التحميل
    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري التصدير...');

    // محاكاة عملية التصدير (يمكن استبدالها بالتنفيذ الفعلي)
    setTimeout(() => {
        button.prop('disabled', false).html(originalHtml);

        // إظهار رسالة نجاح
        if (window.hiddenClientsManager) {
            window.hiddenClientsManager.showNotification('تم تصدير البيانات بنجاح', 'success');
        } else {
            alert('تم تصدير البيانات بنجاح');
        }
    }, 2000);
}

/**
 * إظهار خطأ عام
 */
function showGlobalError(message) {
    const errorDiv = $(`
        <div class="alert alert-danger alert-dismissible fade show position-fixed"
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-exclamation-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);

    $('body').append(errorDiv);

    // إزالة تلقائية بعد 10 ثواني
    setTimeout(() => {
        errorDiv.alert('close');
    }, 10000);
}

/**
 * تنظيف الموارد
 */
function cleanup() {
    console.log('🧹 تنظيف موارد التطبيق');

    if (window.searchManager) {
        window.searchManager.cleanup();
    }

    if (window.mapManager) {
        window.mapManager.cleanup();
    }

    if (window.hiddenClientsManager) {
        window.hiddenClientsManager.cleanup();
    }

    if (window.paginationManager) {
        window.paginationManager.cleanup();
    }

    // مسح المؤقتات
    if (window.searchTimeout) {
        clearTimeout(window.searchTimeout);
    }
}

/**
 * إعادة تهيئة التطبيق (للاستخدام عند الحاجة)
 */
window.reinitializeApp = function() {
    cleanup();
    setTimeout(() => {
        initializeApp();
    }, 100);
};

/**
 * فحص حالة التطبيق
 */
window.checkAppStatus = function() {
    const status = {
        searchManager: !!window.searchManager,
        mapManager: !!window.mapManager,
        hiddenClientsManager: !!window.hiddenClientsManager,
        paginationManager: !!window.paginationManager,
        mapLoaded: window.mapManager ? window.mapManager.getIsMapLoaded() : false,
        hiddenClientsCount: window.hiddenClientsManager ? window.hiddenClientsManager.getHiddenClients().length : 0
    };

    console.log('📊 حالة التطبيق:', status);
    return status;
};

// تصدير الدوال للاستخدام العام
window.initializeApp = initializeApp;
window.cleanup = cleanup;
