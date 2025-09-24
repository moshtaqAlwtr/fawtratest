{{-- ملف: resources/views/purchases/invoices_purchase/partials/pagination.blade.php --}}

@if ($purchaseData->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="pagination-info text-muted">
            عرض {{ $purchaseData->firstItem() }} إلى {{ $purchaseData->lastItem() }} من {{ $purchaseData->total() }} نتيجة
        </div>

        <nav aria-label="صفحات النتائج">
            <ul class="pagination pagination-sm mb-0">
                {{-- رابط الصفحة السابقة --}}
                @if ($purchaseData->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fa fa-angle-right"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $purchaseData->previousPageUrl() }}" aria-label="السابق">
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                @endif

                {{-- أرقام الصفحات --}}
                @foreach ($purchaseData->getUrlRange(1, $purchaseData->lastPage()) as $page => $url)
                    @if ($page == $purchaseData->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- رابط الصفحة التالية --}}
                @if ($purchaseData->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $purchaseData->nextPageUrl() }}" aria-label="التالي">
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
            </ul>
        </nav>
    </div>
@endif
