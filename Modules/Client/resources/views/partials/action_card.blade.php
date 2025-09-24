<div class="card shadow-sm border-0 rounded-3" id="actionCard">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap justify-content-end align-items-center" style="gap: 10px;">
            <!-- زر الخريطة -->
            <button id="toggleMapButton" class="bg-white border d-flex align-items-center justify-content-center"
                style="width: 44px; height: 44px; border-radius: 6px; position: relative;" title="عرض الخريطة"
                data-tooltip="عرض الخريطة">
                <i class="fas fa-map-marked-alt text-primary"></i>
            </button>

            <!-- باقي الأزرار -->
            <label class="bg-white border d-flex align-items-center justify-content-center"
                style="width: 44px; height: 44px; cursor: pointer; border-radius: 6px;" title="تحميل ملف">
                <i class="fas fa-cloud-upload-alt text-primary"></i>
                <input type="file" name="file" class="d-none">
            </label>

            <button type="submit" class="bg-white border d-flex align-items-center justify-content-center"
                style="width: 44px; height: 44px; border-radius: 6px;" title="استيراد ك Excel">
                <i class="fas fa-database text-primary"></i>
            </button>

            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#creditLimitModal"
                class="bg-white border d-flex align-items-center justify-content-center"
                style="width: 44px; height: 44px; border-radius: 6px;" title="حد ائتماني">
                <i class="fas fa-credit-card text-primary"></i>
            </a>

            <button id="exportExcelBtn" class="bg-white border d-flex align-items-center justify-content-center"
                style="width: 44px; height: 44px; border-radius: 6px;" title="تصدير ك Excel">
                <i class="fas fa-file-excel text-primary"></i>
            </button>

            <a href="{{ route('clients.create') }}"
                class="btn btn-success d-flex align-items-center justify-content-center"
                style="height: 44px; padding: 0 16px; font-weight: bold; border-radius: 6px;">
                <i class="fas fa-plus ms-2"></i>
                أضف العميل
            </a>
        </div>
    </div>
</div>
