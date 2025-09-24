{{-- resources/views/purchases/supplier_management/partials/pagination.blade.php --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
    @if($suppliers->total() > 0)
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0 flex-wrap">
                    <li class="page-item {{ $suppliers->onFirstPage() ? 'disabled' : '' }}">
                        <a class="btn btn-sm btn-outline-secondary px-2"
                            href="{{ $suppliers->previousPageUrl() }}" aria-label="Previous">
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                    <li class="page-item mx-2 d-flex align-items-center">
                        <span class="text-muted">صفحة {{ $suppliers->currentPage() }} من
                            {{ $suppliers->lastPage() }}</span>
                    </li>
                    <li class="page-item {{ !$suppliers->hasMorePages() ? 'disabled' : '' }}">
                        <a class="btn btn-sm btn-outline-secondary px-2" href="{{ $suppliers->nextPageUrl() }}"
                            aria-label="Next">
                            <i class="fa fa-angle-left"></i>
                        </a>
                    </li>
                </ul>
            </nav>

            <span class="text-muted mx-2 mx-sm-3">
                {{ $suppliers->firstItem() ?? 0 }}-{{ $suppliers->lastItem() ?? 0 }} من
                {{ $suppliers->total() }}
            </span>
        </div>
    @else
        <div></div>
    @endif

    <div class="d-flex align-items-center gap-3">
        @if($suppliers->total() > 0)
            <button class="btn btn-light" title="تصدير البيانات">
                <i class="fa fa-cloud"></i>
            </button>
        @endif

        <a href="{{ route('SupplierManagement.create') }}" class="btn btn-success">
            <i class="fa fa-plus me-1"></i>
            <span class="d-none d-sm-inline">أضف المورد</span>
            <span class="d-inline d-sm-none">إضافة</span>
        </a>
    </div>
</div>
