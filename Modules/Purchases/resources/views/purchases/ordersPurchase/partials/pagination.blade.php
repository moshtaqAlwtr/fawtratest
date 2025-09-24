@if ($purchaseOrders->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm mb-0">
            {{-- Previous Page Link --}}
            @if ($purchaseOrders->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fa fa-angle-right"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $purchaseOrders->previousPageUrl() }}" aria-label="Previous">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($purchaseOrders->getUrlRange(1, $purchaseOrders->lastPage()) as $page => $url)
                @if ($page == $purchaseOrders->currentPage())
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
            @if ($purchaseOrders->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $purchaseOrders->nextPageUrl() }}" aria-label="Next">
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
        {{ $purchaseOrders->firstItem() }}-{{ $purchaseOrders->lastItem() }} من {{ $purchaseOrders->total() }}
    </span>
@else
    <span class="text-muted">صفحة 1 من 1</span>
@endif
