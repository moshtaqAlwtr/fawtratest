@if ($purchaseQuotation->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm mb-0">
            {{-- Previous Page Link --}}
            @if ($purchaseQuotation->onFirstPage())
                <li class="page-item disabled">
                    <button class="btn btn-sm btn-outline-secondary px-2" disabled>
                        <i class="fa fa-angle-right"></i>
                    </button>
                </li>
            @else
                <li class="page-item">
                    <a class="btn btn-sm btn-outline-secondary px-2" href="{{ $purchaseQuotation->previousPageUrl() }}">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            @endif

            {{-- Page Info --}}
            <li class="page-item mx-2">
                <span class="text-muted">
                    صفحة {{ $purchaseQuotation->currentPage() }} من {{ $purchaseQuotation->lastPage() }}
                </span>
            </li>

            {{-- Next Page Link --}}
            @if ($purchaseQuotation->hasMorePages())
                <li class="page-item">
                    <a class="btn btn-sm btn-outline-secondary px-2" href="{{ $purchaseQuotation->nextPageUrl() }}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <button class="btn btn-sm btn-outline-secondary px-2" disabled>
                        <i class="fa fa-angle-left"></i>
                    </button>
                </li>
            @endif
        </ul>
    </nav>
@endif
