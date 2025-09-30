{{-- ملف: resources/views/sales/retend_invoice/partials/pagination.blade.php --}}
@if ($return->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3 w-100">
        <div class="pagination-info text-muted">
            عرض {{ $return->firstItem() }} إلى {{ $return->lastItem() }} من {{ $return->total() }} نتيجة
        </div>
        <nav aria-label="صفحات النتائج">
            <ul class="pagination pagination-sm mb-0">
                {{-- الصفحة الأولى --}}
                @if ($return->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link"><i class="fa fa-angle-double-right"></i></span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $return->url(1) }}" aria-label="الأول">
                            <i class="fa fa-angle-double-right"></i>
                        </a>
                    </li>
                @endif

                {{-- الصفحة السابقة --}}
                @if ($return->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link"><i class="fa fa-angle-right"></i></span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $return->previousPageUrl() }}" aria-label="السابق">
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                @endif

                {{-- رقم الصفحة الحالية --}}
                <li class="page-item active">
                    <span class="page-link">{{ $return->currentPage() }}</span>
                </li>

                {{-- الصفحة التالية --}}
                @if ($return->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $return->nextPageUrl() }}" aria-label="التالي">
                            <i class="fa fa-angle-left"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link"><i class="fa fa-angle-left"></i></span>
                    </li>
                @endif

                {{-- الصفحة الأخيرة --}}
                @if ($return->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $return->url($return->lastPage()) }}" aria-label="الأخير">
                            <i class="fa fa-angle-double-left"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link"><i class="fa fa-angle-double-left"></i></span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif