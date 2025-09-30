{{-- resources/views/client/partials/per_page_selector.blade.php --}}
<div class="per-page-selector">
    <div class="selector-container">
        <label for="perPageSelect" class="selector-label">
            <i class="fas fa-list-ul me-1"></i>
            عرض
        </label>

        <div class="select-wrapper">
            <select id="perPageSelect" class="form-select per-page-select">
                <option value="10" {{ request('perPage', 50) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('perPage', 50) == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('perPage', 50) == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('perPage', 50) == 100 ? 'selected' : '' }}>100</option>
            </select>
            <i class="fas fa-chevron-down select-icon"></i>
        </div>

        <span class="selector-text">عنصر في الصفحة</span>
    </div>

    <!-- عرض إجمالي النتائج -->
    @if (isset($clients) && $clients->total() > 0)
        <div class="total-count">
            <small class="text-muted">
                <i class="fas fa-database me-1"></i>
                {{ number_format($clients->total()) }} إجمالي
            </small>
        </div>
    @endif
</div>

<style>
    .per-page-selector {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        padding: 12px 16px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .selector-container {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .selector-label {
        color: #495057;
        font-weight: 500;
        font-size: 14px;
        margin: 0;
        display: flex;
        align-items: center;
        white-space: nowrap;
    }

    .select-wrapper {
        position: relative;
        display: inline-block;
    }

    .per-page-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 6px 30px 6px 12px;
        font-size: 14px;
        font-weight: 500;
        color: #495057;
        cursor: pointer;
        transition: all 0.2s ease;
        min-width: 70px;
        text-align: center;
    }

    .per-page-select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .per-page-select:hover {
        border-color: #007bff;
    }

    .select-icon {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-size: 12px;
        pointer-events: none;
        transition: transform 0.2s ease;
    }

    .per-page-select:focus+.select-icon {
        transform: translateY(-50%) rotate(180deg);
    }

    .selector-text {
        color: #6c757d;
        font-size: 14px;
        white-space: nowrap;
    }

    .total-count {
        background: #fff;
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid #e9ecef;
    }

    .total-count small {
        font-size: 13px;
        display: flex;
        align-items: center;
    }

    /* للشاشات الصغيرة */
    @media (max-width: 576px) {
        .per-page-selector {
            flex-direction: column;
            text-align: center;
            gap: 10px;
        }

        .selector-container {
            justify-content: center;
            gap: 8px;
        }

        .selector-label,
        .selector-text {
            font-size: 13px;
        }

        .per-page-select {
            font-size: 13px;
            padding: 5px 25px 5px 10px;
        }
    }

    /* للغة العربية */
    [dir="rtl"] .select-wrapper {
        direction: rtl;
    }

    [dir="rtl"] .select-icon {
        right: auto;
        left: 8px;
    }

    [dir="rtl"] .per-page-select {
        padding: 6px 12px 6px 30px;
        text-align: center;
    }

    /* تأثيرات إضافية */
    .per-page-selector:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    }

    .per-page-select option {
        background: #fff;
        color: #495057;
        padding: 8px;
    }

    /* تحسين النمط عند التركيز */
    .per-page-select:focus {
        background: #fff;
    }

    /* أنيميشن للتغيير */
    .per-page-select {
        transition: all 0.3s ease;
    }

    .select-wrapper:hover .select-icon {
        color: #007bff;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const perPageSelect = document.getElementById('perPageSelect');

        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                const selectedValue = this.value;

                // إنشاء URL جديد مع القيمة المحددة
                const url = new URL(window.location);
                url.searchParams.set('perPage', selectedValue);
                url.searchParams.delete('page'); // إعادة تعيين رقم الصفحة إلى 1

                // إضافة تأثير loading
                this.disabled = true;
                this.style.opacity = '0.7';

                // إعادة توجيه إلى URL الجديد
                window.location.href = url.toString();
            });

            // تأثير بصري عند التغيير
            perPageSelect.addEventListener('focus', function() {
                this.closest('.per-page-selector').style.transform = 'scale(1.02)';
                this.closest('.per-page-selector').style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            });

            perPageSelect.addEventListener('blur', function() {
                this.closest('.per-page-selector').style.transform = 'scale(1)';
                this.closest('.per-page-selector').style.boxShadow = 'none';
            });
        }
    });
</script>
