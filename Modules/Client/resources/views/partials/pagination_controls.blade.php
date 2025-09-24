
{{-- resources/views/client/partials/pagination_controls.blade.php --}}
<nav aria-label="Page navigation" id="paginationContainer">
    @if ($clients->hasPages())
        <ul class="pagination pagination-sm mb-0 pagination-links">
            @if ($clients->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link border-0 rounded-pill" aria-label="First">
                        <i class="fas fa-angle-double-right"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0 rounded-pill pagination-link" href="#" aria-label="First" data-page="1">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </li>
            @endif

            @if ($clients->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link border-0 rounded-pill" aria-label="Previous">
                        <i class="fas fa-angle-right"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0 rounded-pill pagination-link" href="#" aria-label="Previous" data-page="{{ $clients->currentPage() - 1 }}">
                        <i class="fas fa-angle-right"></i>
                    </a>
                </li>
            @endif

            <li class="page-item">
                <span class="page-link border-0 bg-light rounded-pill px-3">
                    صفحة {{ $clients->currentPage() }} من {{ $clients->lastPage() }}
                </span>
            </li>

            @if ($clients->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0 rounded-pill pagination-link" href="#" aria-label="Next" data-page="{{ $clients->currentPage() + 1 }}">
                        <i class="fas fa-angle-left"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link border-0 rounded-pill" aria-label="Next">
                        <i class="fas fa-angle-left"></i>
                    </span>
                </li>
            @endif

            @if ($clients->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0 rounded-pill pagination-link" href="#" aria-label="Last" data-page="{{ $clients->lastPage() }}">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link border-0 rounded-pill" aria-label="Last">
                        <i class="fas fa-angle-double-left"></i>
                    </span>
                </li>
            @endif
        </ul>
    @endif
</nav>
