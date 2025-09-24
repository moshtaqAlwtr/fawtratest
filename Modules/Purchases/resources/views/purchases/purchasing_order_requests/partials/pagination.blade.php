@if ($purchaseOrdersRequests->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm mb-0">
            {{-- Previous Page Link --}}
            @if ($purchaseQuotation->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fa fa-angle-right"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $purchaseOrdersRequests->previousPageUrl() }}" aria-label="Previous">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($purchaseOrdersRequests->getUrlRange(1, $purchaseOrdersRequests->lastPage()) as $page => $url)
                @if ($page == $purchaseOrdersRequests->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($purchaseOrdersRequests->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $purchaseOrdersRequests->nextPageUrl() }}" aria-label="Next">
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

    <span class="text-muted mx-2">
        {{ $purchaseOrdersRequests->firstItem() }}-{{ $purchaseOrdersRequests->lastItem() }} من {{ $purchaseOrdersRequests->total() }}
    </span>
@else
    <span class="text-muted">صفحة 1 من 1</span>
@endif
