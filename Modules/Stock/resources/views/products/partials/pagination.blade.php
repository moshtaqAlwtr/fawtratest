{{-- resources/views/stock/products/partials/pagination.blade.php --}}

@if ($products->hasPages())
    <nav aria-label="تصفح المنتجات" class="pagination-wrapper">
        <ul class="pagination pagination-custom justify-content-center mb-0">
            {{-- الرابط للصفحة الأولى --}}
            @if ($products->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fas fa-angle-double-right" data-bs-toggle="tooltip" title="الصفحة الأولى"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link pagination-link" href="#" data-page="1"
                       data-bs-toggle="tooltip" title="الصفحة الأولى">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </li>
            @endif

            {{-- الرابط للصفحة السابقة --}}
            @if ($products->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fas fa-angle-right" data-bs-toggle="tooltip" title="الصفحة السابقة"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link pagination-link" href="#" data-page="{{ $products->currentPage() - 1 }}"
                       data-bs-toggle="tooltip" title="الصفحة السابقة">
                        <i class="fas fa-angle-right"></i>
                    </a>
                </li>
            @endif

            {{-- أرقام الصفحات --}}
            @php
                $start = max(1, $products->currentPage() - 2);
                $end = min($products->lastPage(), $products->currentPage() + 2);

                // التأكد من عرض 5 صفحات على الأقل إذا كان ممكناً
                if ($end - $start < 4) {
                    if ($start == 1) {
                        $end = min($products->lastPage(), $start + 4);
                    } else {
                        $start = max(1, $end - 4);
                    }
                }
            @endphp

            {{-- إظهار الصفحة الأولى إذا لم تكن ضمن النطاق --}}
            @if ($start > 1)
                <li class="page-item">
                    <a class="page-link pagination-link" href="#" data-page="1">1</a>
                </li>
                @if ($start > 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            {{-- أرقام الصفحات في النطاق --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $products->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link pagination-link" href="#" data-page="{{ $page }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            {{-- إظهار الصفحة الأخيرة إذا لم تكن ضمن النطاق --}}
            @if ($end < $products->lastPage())
                @if ($end < $products->lastPage() - 1)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link pagination-link" href="#" data-page="{{ $products->lastPage() }}">{{ $products->lastPage() }}</a>
                </li>
            @endif

            {{-- الرابط للصفحة التالية --}}
            @if ($products->hasMorePages())
                <li class="page-item">
                    <a class="page-link pagination-link" href="#" data-page="{{ $products->currentPage() + 1 }}"
                       data-bs-toggle="tooltip" title="الصفحة التالية">
                        <i class="fas fa-angle-left"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fas fa-angle-left" data-bs-toggle="tooltip" title="الصفحة التالية"></i>
                    </span>
                </li>
            @endif

            {{-- الرابط للصفحة الأخيرة --}}
            @if ($products->hasMorePages())
                <li class="page-item">
                    <a class="page-link pagination-link" href="#" data-page="{{ $products->lastPage() }}"
                       data-bs-toggle="tooltip" title="الصفحة الأخيرة">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fas fa-angle-double-left" data-bs-toggle="tooltip" title="الصفحة الأخيرة"></i>
                    </span>
                </li>
            @endif
        </ul>

        {{-- معلومات الصفحة الحالية --}}
        <div class="pagination-info mt-2 text-center">
            <small class="text-muted">
                الصفحة {{ $products->currentPage() }} من {{ $products->lastPage() }}
                ({{ $products->total() }} منتج إجمالي)
            </small>
        </div>

        {{-- Quick Jump للصفحات --}}
        @if ($products->lastPage() > 10)
            <div class="quick-jump mt-2 text-center">
                <div class="input-group input-group-sm d-inline-flex" style="width: auto;">
                    <span class="input-group-text">الذهاب إلى صفحة:</span>
                    <input type="number" class="form-control" id="jumpToPage"
                           min="1" max="{{ $products->lastPage() }}"
                           style="width: 80px;">
                    <button class="btn btn-outline-primary" type="button" onclick="jumpToPage()">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </div>
            </div>
        @endif
    </nav>
@endif

<style>
.pagination-wrapper {
    background: white;
    padding: 1rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.pagination-custom {
    margin-bottom: 0;
}

.pagination-custom .page-link {
    border: none;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 8px;
    color: #6c757d;
    background: #f8f9fa;
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination-custom .page-link:hover {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.pagination-custom .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.pagination-custom .page-item.disabled .page-link {
    opacity: 0.6;
    pointer-events: none;
}
</style>