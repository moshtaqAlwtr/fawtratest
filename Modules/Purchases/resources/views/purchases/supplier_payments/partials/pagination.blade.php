<!-- تصحيح: ملف التنقل يتم تحميله -->
@if ($payments->hasPages() || $payments->total() > 0)
    <div class="row align-items-center mt-3 mb-3" style="border: 2px solid #007bff; padding: 15px; background-color: #f8f9fa;">
        <div class="col-md-6">
            <nav aria-label="Pagination Navigation">
                <ul class="pagination pagination-sm mb-0 justify-content-start">
                    {{-- Previous Page Link --}}
                    @if ($payments->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">السابق</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="{{ $payments->currentPage() - 1 }}">السابق</a>
                        </li>
                    @endif

                    {{-- First Page --}}
                    @if($payments->currentPage() > 3)
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="1">1</a>
                        </li>
                        @if($payments->currentPage() > 4)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endif

                    {{-- Pagination Elements --}}
                    @for ($i = max(1, $payments->currentPage() - 2); $i <= min($payments->lastPage(), $payments->currentPage() + 2); $i++)
                        @if ($i == $payments->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $i }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="{{ $i }}">{{ $i }}</a>
                            </li>
                        @endif
                    @endfor

                    {{-- Last Page --}}
                    @if($payments->currentPage() < $payments->lastPage() - 2)
                        @if($payments->currentPage() < $payments->lastPage() - 3)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="{{ $payments->lastPage() }}">{{ $payments->lastPage() }}</a>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($payments->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="{{ $payments->currentPage() + 1 }}">التالي</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">التالي</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end align-items-center">
                <small class="text-muted me-3">
                    صفحة {{ $payments->currentPage() }} من {{ $payments->lastPage() }}
                </small>
                <div class="input-group input-group-sm" style="width: 120px;">
                    <input type="number"
                           class="form-control"
                           id="gotoPage"
                           placeholder="الصفحة"
                           min="1"
                           max="{{ $payments->lastPage() }}"
                           value="{{ $payments->currentPage() }}">
                    <button class="btn btn-outline-secondary" type="button" id="gotoPageBtn">
                        <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- إضافة أنماط مخصصة للتنقل -->
<style>
.pagination .page-link {
    color: #007bff;
    background-color: #fff;
    border: 1px solid #dee2e6;
    padding: 0.375rem 0.75rem;
    margin: 0 2px;
    border-radius: 0.25rem;
    transition: all 0.15s ease-in-out;
}

.pagination .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #adb5bd;
}

.pagination .page-item.active .page-link {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // التنقل المباشر للصفحة
    const gotoPageBtn = document.getElementById('gotoPageBtn');
    const gotoPageInput = document.getElementById('gotoPage');

    if (gotoPageBtn && gotoPageInput) {
        gotoPageBtn.addEventListener('click', function() {
            const page = parseInt(gotoPageInput.value);
            const maxPage = parseInt(gotoPageInput.getAttribute('max'));

            if (page >= 1 && page <= maxPage && page !== {{ $payments->currentPage() }}) {
                // تشغيل حدث تغيير الصفحة
                if (typeof window.loadDataWithPage === 'function') {
                    window.loadDataWithPage(page);
                } else {
                    // النسخة الاحتياطية
                    window.currentPage = page;
                    if (typeof loadData === 'function') {
                        loadData();
                    }
                }
            }
        });

        // التنقل بالضغط على Enter
        gotoPageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                gotoPageBtn.click();
            }
        });
    }
});
</script>
