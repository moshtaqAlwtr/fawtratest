{{-- ملف: resources/views/stock/store_permits_management/partials/pagination.blade.php --}}

@if ($wareHousePermits->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="pagination-info text-muted">
            عرض {{ $wareHousePermits->firstItem() }} إلى {{ $wareHousePermits->lastItem() }} من {{ $wareHousePermits->total() }} نتيجة
        </div>

        <nav aria-label="صفحات الأذون المخزنية">
            <ul class="pagination pagination-sm mb-0">
                {{-- رابط الأول --}}
                @if ($wareHousePermits->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fa fa-angle-double-right"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $wareHousePermits->url(1) }}" aria-label="الأول">
                            <i class="fa fa-angle-double-right"></i>
                        </a>
                    </li>
                @endif

                {{-- رابط الصفحة السابقة --}}
                @if ($wareHousePermits->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fa fa-angle-right"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $wareHousePermits->previousPageUrl() }}" aria-label="السابق">
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                @endif

                {{-- عرض رقم الصفحة الحالية فقط --}}
                <li class="page-item">
                    <span class="page-link bg-light">
                        صفحة {{ $wareHousePermits->currentPage() }} من {{ $wareHousePermits->lastPage() }}
                    </span>
                </li>

                {{-- رابط الصفحة التالية --}}
                @if ($wareHousePermits->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $wareHousePermits->nextPageUrl() }}" aria-label="التالي">
                            <i class="fa fa-angle-left"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fa fa-angle-left"></i>
                        </span>
                    </li>
                @endif

                {{-- رابط الأخير --}}
                @if ($wareHousePermits->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $wareHousePermits->url($wareHousePermits->lastPage()) }}" aria-label="الأخير">
                            <i class="fa fa-angle-double-left"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fa fa-angle-double-left"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif