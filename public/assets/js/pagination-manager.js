/**
 * Pagination Manager - مدير الترقيم
 * يدير عمليات التنقل بين الصفحات
 */

class PaginationManager {
    constructor() {
        this.currentPage = 1;
        this.lastPage = 1;
        this.perPage = 50;
    }

    /**
     * تهيئة مدير الترقيم
     */
    init() {
        this.bindEvents();
    }

    /**
     * ربط الأحداث
     */
    bindEvents() {
        // معالج النقر على روابط الترقيم
        $(document).on('click', '.pagination-link', (e) => {
            e.preventDefault();
            const page = parseInt($(e.currentTarget).data('page'));

            if (page && !isNaN(page) && page !== this.currentPage) {
                this.goToPage(page);
            }
        });
    }

    /**
     * الانتقال إلى صفحة محددة
     */
    goToPage(page) {
        if (window.searchManager && !window.searchManager.getIsLoading()) {
            this.currentPage = page;

            // تحديث الفلاتر
            const currentFilters = window.searchManager.getCurrentFilters();
            currentFilters.page = page;

            // تنفيذ البحث مع الصفحة الجديدة
            window.searchManager.performSearch();
        }
    }

    /**
     * الانتقال إلى الصفحة التالية
     */
    nextPage() {
        if (this.currentPage < this.lastPage) {
            this.goToPage(this.currentPage + 1);
        }
    }

    /**
     * الانتقال إلى الصفحة السابقة
     */
    previousPage() {
        if (this.currentPage > 1) {
            this.goToPage(this.currentPage - 1);
        }
    }

    /**
     * الانتقال إلى الصفحة الأولى
     */
    firstPage() {
        this.goToPage(1);
    }

    /**
     * الانتقال إلى الصفحة الأخيرة
     */
    lastPageGo() {
        this.goToPage(this.lastPage);
    }

    /**
     * تحديث معلومات الترقيم
     */
    updatePaginationInfo(paginationData) {
        this.currentPage = paginationData.current_page || 1;
        this.lastPage = paginationData.last_page || 1;
        this.perPage = paginationData.per_page || 50;
    }

    /**
     * الحصول على الصفحة الحالية
     */
    getCurrentPage() {
        return this.currentPage;
    }

    /**
     * الحصول على الصفحة الأخيرة
     */
    getLastPage() {
        return this.lastPage;
    }

    /**
     * التحقق من إمكانية الانتقال للصفحة التالية
     */
    hasNextPage() {
        return this.currentPage < this.lastPage;
    }

    /**
     * التحقق من إمكانية الانتقال للصفحة السابقة
     */
    hasPreviousPage() {
        return this.currentPage > 1;
    }

    /**
     * تنظيف الموارد
     */
    cleanup() {
        this.currentPage = 1;
        this.lastPage = 1;
        this.perPage = 50;
    }
}

// تصدير الكلاس
window.PaginationManager = PaginationManager;
