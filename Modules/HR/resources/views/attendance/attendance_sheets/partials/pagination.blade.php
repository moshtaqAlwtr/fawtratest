{{-- resources/views/hr/attendance/attendance_sheets/partials/pagination.blade.php --}}

@if($attendanceSheets->hasPages())
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <!-- Pagination Info -->
                <div class="pagination-info">
                    <span class="text-muted">
                        عرض {{ $attendanceSheets->firstItem() }} إلى {{ $attendanceSheets->lastItem() }}
                        من إجمالي {{ $attendanceSheets->total() }} نتيجة
                    </span>
                </div>

                <!-- Pagination Links -->
                <nav aria-label="صفحات النتائج">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if ($attendanceSheets->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fa fa-chevron-right"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $attendanceSheets->previousPageUrl() }}" rel="prev">
                                    <i class="fa fa-chevron-right"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($attendanceSheets->getUrlRange(1, $attendanceSheets->lastPage()) as $page => $url)
                            @if ($page == $attendanceSheets->currentPage())
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
                        @if ($attendanceSheets->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $attendanceSheets->nextPageUrl() }}" rel="next">
                                    <i class="fa fa-chevron-left"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fa fa-chevron-left"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>

                <!-- Quick Page Jump -->
                <div class="page-jump d-flex align-items-center">
                    <span class="text-muted me-2">الذهاب إلى صفحة:</span>
                    <input type="number" id="pageJump" class="form-control form-control-sm"
                           style="width: 70px;" min="1" max="{{ $attendanceSheets->lastPage() }}"
                           placeholder="{{ $attendanceSheets->currentPage() }}">
                    <button type="button" id="jumpToPage" class="btn btn-sm btn-outline-primary ms-2">
                        <i class="fa fa-arrow-left"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Handle page jump
    $('#jumpToPage').on('click', function() {
        var page = $('#pageJump').val();
        if (page && page > 0 && page <= {{ $attendanceSheets->lastPage() }}) {
            performSearch(page);
        }
    });

    // Handle enter key in page jump input
    $('#pageJump').on('keypress', function(e) {
        if (e.which == 13) {
            $('#jumpToPage').click();
        }
    });
    </script>

    <style>
    .pagination-info {
        font-size: 0.875rem;
    }

    .page-jump input {
        text-align: center;
    }

    .pagination .page-link {
        border-radius: 0.25rem;
        margin: 0 0.125rem;
        border: 1px solid #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }

        .page-jump {
            justify-content: center;
        }

        .pagination-info {
            text-align: center;
        }
    }
    </style>
@endif
