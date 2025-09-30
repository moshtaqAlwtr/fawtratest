{{-- resources/views/client/partials/pagination_controls.blade.php --}}
<nav aria-label="Page navigation" id="paginationContainer">
    @if ($clients->hasPages())
        <ul class="pagination pagination-elegant mb-0">
            @if ($clients->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" aria-label="First">
                        <i class="fas fa-angle-double-right"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link pagination-link" href="#" aria-label="First" data-page="1">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </li>
            @endif

            @if ($clients->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" aria-label="Previous">
                        <i class="fas fa-angle-right"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link pagination-link" href="#" aria-label="Previous"
                        data-page="{{ $clients->currentPage() - 1 }}">
                        <i class="fas fa-angle-right"></i>
                    </a>
                </li>
            @endif

            <li class="page-item active">
                <span class="page-link current-page">
                    صفحة {{ $clients->currentPage() }} من {{ $clients->lastPage() }}
                </span>
            </li>

            @if ($clients->hasMorePages())
                <li class="page-item">
                    <a class="page-link pagination-link" href="#" aria-label="Next"
                        data-page="{{ $clients->currentPage() + 1 }}">
                        <i class="fas fa-angle-left"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" aria-label="Next">
                        <i class="fas fa-angle-left"></i>
                    </span>
                </li>
            @endif

            @if ($clients->hasMorePages())
                <li class="page-item">
                    <a class="page-link pagination-link" href="#" aria-label="Last"
                        data-page="{{ $clients->lastPage() }}">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" aria-label="Last">
                        <i class="fas fa-angle-double-left"></i>
                    </span>
                </li>
            @endif
        </ul>
    @endif
</nav>

<style>
    .pagination-elegant {
        gap: 3px;
    }

    .pagination-elegant .page-link {
        border: 0;
        border-radius: 50rem;
        padding: 0.375rem 0.75rem;
        background: #f8f9fa;
        color: #6c757d;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .pagination-elegant .page-link:hover {
        background: #007bff;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
    }

    .pagination-elegant .page-item.active .page-link {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: #fff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.4);
        font-weight: 500;
    }

    .pagination-elegant .page-item.disabled .page-link {
        background: #e9ecef;
        color: #adb5bd;
        cursor: not-allowed;
    }

    .pagination-elegant .current-page {
        padding: 0.375rem 1rem;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    /* للشاشات الصغيرة */
    @media (max-width: 576px) {
        .pagination-elegant {
            justify-content: center;
        }

        .pagination-elegant .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .pagination-elegant .current-page {
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.pagination-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                // يمكنك تعديل هذا الجزء حسب طريقة عملك
                console.log('الانتقال للصفحة:', page);
            });
        });
    });
</script>
