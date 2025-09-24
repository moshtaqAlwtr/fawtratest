/**
 * Search Manager - مدير البحث والفلتر
 * يدير جميع عمليات البحث والفلترة والترقيم
 */

class SearchManager {
    constructor() {
        this.isLoading = false;
        this.currentFilters = {};
        this.searchTimeout = null;
    }

    /**
     * تهيئة مدير البحث
     */
    init() {
        this.updateCurrentFilters();
        this.bindEvents();
        this.initializeSelect2();
    }

    /**
     * ربط الأحداث
     */
    bindEvents() {
        // البحث
        $('#searchForm').on('submit', (e) => {
            e.preventDefault();
            this.performSearch();
        });

        // إعادة تعيين الفلاتر
        $('#resetFilters').on('click', () => this.resetFilters());

        // تغيير عدد العناصر لكل صفحة
        $('#perPageSelect').on('change', () => this.handlePerPageChange());

        // البحث التلقائي
        $('#searchForm input, #searchForm select').on('change', () => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.currentFilters.page = 1;
                this.performSearch();
            }, 500);
        });

        // معالج البحث في الخريطة
        this.initializeMapSearchControls();
    }

    /**
     * تحديث الفلاتر الحالية
     */
    updateCurrentFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        this.currentFilters = {};
        for (const [key, value] of urlParams) {
            if (value) this.currentFilters[key] = value;
        }
    }

    /**
     * تنفيذ البحث
     */
    performSearch() {
        if (this.isLoading) return;

        const formData = new FormData(document.getElementById('searchForm'));
        const searchParams = {};

        for (const [key, value] of formData) {
            if (value && value.trim() !== '') {
                searchParams[key] = value.trim();
            }
        }

        if (this.currentFilters.page) searchParams.page = this.currentFilters.page;
        if (this.currentFilters.perPage) searchParams.perPage = this.currentFilters.perPage;

        this.currentFilters = { ...searchParams };
        this.updateURL(searchParams);
        this.loadClients(searchParams);

        // تحديث الخريطة إذا كانت محملة
        if (window.mapManager && window.mapManager.getIsMapLoaded()) {
            window.mapManager.loadMapData(searchParams);
        }
    }

    /**
     * تحميل العملاء
     */
    loadClients(params = {}) {
        if (this.isLoading) return;

        this.isLoading = true;
        this.showLoading();

        $.ajax({
            url: window.clientRoutes.index,
            method: 'GET',
            data: {
                ...params,
                ajax: true
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: (response) => {
                if (response.success) {
                    $('#clientsContainer').html(response.html).addClass('fade-in');
                    this.updatePagination(response.pagination);
                    this.updatePerPageSelect(params.perPage || 50);
                }
            },
            error: (xhr) => {
                console.error('خطأ في تحميل البيانات:', xhr);
                this.showError('حدث خطأ أثناء تحميل البيانات. يرجى إعادة المحاولة.');
            },
            complete: () => {
                this.isLoading = false;
                this.hideLoading();
                setTimeout(() => $('#clientsContainer').removeClass('fade-in'), 300);
            }
        });
    }

    /**
     * معالج تغيير عدد العناصر لكل صفحة
     */
    handlePerPageChange() {
        const perPage = $('#perPageSelect').val();
        this.currentFilters.perPage = perPage;
        this.currentFilters.page = 1;
        this.performSearch();
    }

    /**
     * إعادة تعيين الفلاتر
     */
    resetFilters() {
        document.getElementById('searchForm').reset();
        if ($.fn.select2) $('.select2').val(null).trigger('change');

        this.currentFilters = { perPage: 50 };
        this.updateURL({});
        this.loadClients({ perPage: 50 });

        // إعادة تعيين الخريطة
        if (window.mapManager && window.mapManager.getIsMapLoaded()) {
            window.mapManager.loadMapData({});
        }
    }

    /**
     * تحديث رابط الصفحة
     */
    updateURL(params) {
        const url = new URL(window.location.href);
        url.search = '';
        Object.keys(params).forEach(key => {
            if (params[key]) url.searchParams.set(key, params[key]);
        });
        window.history.pushState({}, '', url);
    }

    /**
     * تحديث شريط الترقيم
     */
    updatePagination(paginationData) {
        let paginationHtml = '';

        if (paginationData.last_page > 1) {
            paginationHtml = '<ul class="pagination pagination-sm mb-0 pagination-links">';

            // الصفحة الأولى
            if (paginationData.on_first_page) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link border-0 rounded-pill"><i class="fas fa-angle-double-right"></i></span></li>';
            } else {
                paginationHtml += '<li class="page-item"><a class="page-link border-0 rounded-pill pagination-link" href="#" data-page="1"><i class="fas fa-angle-double-right"></i></a></li>';
            }

            // الصفحة السابقة
            if (paginationData.on_first_page) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link border-0 rounded-pill"><i class="fas fa-angle-right"></i></span></li>';
            } else {
                paginationHtml += '<li class="page-item"><a class="page-link border-0 rounded-pill pagination-link" href="#" data-page="' + (paginationData.current_page - 1) + '"><i class="fas fa-angle-right"></i></a></li>';
            }

            // الصفحة الحالية
            paginationHtml += '<li class="page-item"><span class="page-link border-0 bg-light rounded-pill px-3">صفحة ' + paginationData.current_page + ' من ' + paginationData.last_page + '</span></li>';

            // الصفحة التالية
            if (paginationData.has_more_pages) {
                paginationHtml += '<li class="page-item"><a class="page-link border-0 rounded-pill pagination-link" href="#" data-page="' + (paginationData.current_page + 1) + '"><i class="fas fa-angle-left"></i></a></li>';
            } else {
                paginationHtml += '<li class="page-item disabled"><span class="page-link border-0 rounded-pill"><i class="fas fa-angle-left"></i></span></li>';
            }

            // الصفحة الأخيرة
            if (paginationData.has_more_pages) {
                paginationHtml += '<li class="page-item"><a class="page-link border-0 rounded-pill pagination-link" href="#" data-page="' + paginationData.last_page + '"><i class="fas fa-angle-double-left"></i></a></li>';
            } else {
                paginationHtml += '<li class="page-item disabled"><span class="page-link border-0 rounded-pill"><i class="fas fa-angle-double-left"></i></span></li>';
            }

            paginationHtml += '</ul>';
        }

        $('#paginationContainer').html(paginationHtml);
    }

    /**
     * تحديث اختيار عدد العناصر لكل صفحة
     */
    updatePerPageSelect(perPage) {
        $('#perPageSelect').val(perPage);
    }

    /**
     * إظهار مؤشر التحميل
     */
    showLoading() {
        $('#loadingOverlay').fadeIn(200);
    }

    /**
     * إخفاء مؤشر التحميل
     */
    hideLoading() {
        $('#loadingOverlay').fadeOut(200);
    }

    /**
     * إظهار رسالة خطأ
     */
    showError(message) {
        $('#clientsContainer').html(`
            <div class="alert alert-danger text-center py-4" role="alert">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <h5 class="mb-2">${message}</h5>
                <button class="btn btn-sm btn-outline-primary mt-2" onclick="window.searchManager.performSearch()">
                    <i class="fas fa-sync-alt me-1"></i> إعادة المحاولة
                </button>
            </div>
        `);
    }

    /**
     * تهيئة Select2
     */
    initializeSelect2() {
        if ($.fn.select2) {
            $('.select2').select2({
                placeholder: 'اختر من القائمة',
                allowClear: true,
                dir: 'rtl'
            });
        }
    }

    /**
     * تهيئة عناصر التحكم في البحث بالخريطة
     */
    initializeMapSearchControls() {
        const searchInput = document.getElementById('clientSearch');
        if (searchInput) {
            // إضافة زر مسح البحث
            const clearButton = document.createElement('button');
            clearButton.innerHTML = '<i class="fas fa-times"></i>';
            clearButton.title = 'مسح البحث';
            clearButton.className = 'search-clear-button';

            clearButton.addEventListener('click', (e) => {
                e.stopPropagation();
                searchInput.value = '';
                this.filterMapMarkers('');
                clearButton.style.display = 'none';
                searchInput.focus();
            });

            // إضافة الزر لحاوي البحث
            const searchContainer = searchInput.parentElement;
            if (searchContainer) {
                searchContainer.style.position = 'relative';
                searchContainer.appendChild(clearButton);
            }

            // إظهار/إخفاء زر المسح حسب المحتوى
            searchInput.addEventListener('input', function() {
                const searchValue = this.value.toLowerCase().trim();
                clearButton.style.display = searchValue ? 'block' : 'none';
                window.searchManager.filterMapMarkers(searchValue);
            });

            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    clearButton.style.display = 'none';
                    this.filterMapMarkers('');
                }
            });
        }
    }

    /**
     * فلترة علامات الخريطة
     */
    filterMapMarkers(searchValue) {
        if (window.mapManager && window.mapManager.getIsMapLoaded()) {
            window.mapManager.filterMarkers(searchValue);
        }
    }

    /**
     * دوال مساعدة لواجهة المستخدم
     */
    toggleSearchFields(button) {
        const cardBody = $(button).closest('.card').find('.card-body');
        const hideButtonText = $(button).find('.hide-button-text');
        const icon = $(button).find('i');

        if (cardBody.is(':visible')) {
            cardBody.slideUp(300);
            hideButtonText.text('إظهار');
            icon.removeClass('fa-times').addClass('fa-search');
        } else {
            cardBody.slideDown(300);
            hideButtonText.text('إخفاء');
            icon.removeClass('fa-search').addClass('fa-times');
        }
    }

    toggleSearchText(button) {
        const buttonText = $(button).find('.button-text');
        const isExpanded = $('#advancedSearchForm').hasClass('show');
        buttonText.text(isExpanded ? 'متقدم' : 'بسيط');
    }

    /**
     * الحصول على الفلاتر الحالية
     */
    getCurrentFilters() {
        return this.currentFilters;
    }

    /**
     * التحقق من حالة التحميل
     */
    getIsLoading() {
        return this.isLoading;
    }

    /**
     * تنظيف الموارد
     */
    cleanup() {
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        this.currentFilters = {};
        this.isLoading = false;
    }
}

// تصدير الكلاس
window.SearchManager = SearchManager;
