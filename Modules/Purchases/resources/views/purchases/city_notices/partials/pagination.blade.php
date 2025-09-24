{{-- ملف: resources/views/purchases/city_notices/partials/pagination.blade.php --}}

@if ($cityNotices->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="pagination-info text-muted">
            عرض {{ $cityNotices->firstItem() }} إلى {{ $cityNotices->lastItem() }} من {{ $cityNotices->total() }} اشعار مدين
        </div>
        <nav aria-label="صفحات النتائج">
            <ul class="pagination pagination-sm mb-0">
                {{-- رابط الصفحة الأولى --}}
                @if ($cityNotices->currentPage() > 3)
                    <li class="page-item">
                        <a class="page-link" href="{{ $cityNotices->url(1) }}" aria-label="الأولى">
                            <i class="fa fa-angle-double-right"></i>
                        </a>
                    </li>
                @endif

                {{-- رابط الصفحة السابقة --}}
                @if ($cityNotices->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fa fa-angle-right"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $cityNotices->previousPageUrl() }}" aria-label="السابق">
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                @endif

                {{-- أرقام الصفحات --}}
                @php
                    $start = max($cityNotices->currentPage() - 2, 1);
                    $end = min($start + 4, $cityNotices->lastPage());
                    $start = max($end - 4, 1);
                @endphp

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $cityNotices->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $i }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $cityNotices->url($i) }}">{{ $i }}</a>
                        </li>
                    @endif
                @endfor

                {{-- رابط الصفحة التالية --}}
                @if ($cityNotices->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $cityNotices->nextPageUrl() }}" aria-label="التالي">
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

                {{-- رابط الصفحة الأخيرة --}}
                @if ($cityNotices->currentPage() < $cityNotices->lastPage() - 2)
                    <li class="page-item">
                        <a class="page-link" href="{{ $cityNotices->url($cityNotices->lastPage()) }}" aria-label="الأخيرة">
                            <i class="fa fa-angle-double-left"></i>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>

    {{-- معلومات إضافية عن الصفحات --}}
    <div class="row mt-2">
        <div class="col-md-6">
            <small class="text-muted">
                <i class="fa fa-info-circle me-1"></i>
                الصفحة {{ $cityNotices->currentPage() }} من {{ $cityNotices->lastPage() }}
                ({{ $cityNotices->total() }} اشعار مدين إجمالي)
            </small>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary" onclick="loadData(1)">
                    <i class="fa fa-fast-backward"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="loadData({{ max(1, $cityNotices->currentPage() - 1) }})">
                    <i class="fa fa-step-backward"></i>
                </button>
                <span class="btn btn-outline-primary">
                    {{ $cityNotices->currentPage() }} / {{ $cityNotices->lastPage() }}
                </span>
                <button type="button" class="btn btn-outline-secondary" onclick="loadData({{ min($cityNotices->lastPage(), $cityNotices->currentPage() + 1) }})">
                    <i class="fa fa-step-forward"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="loadData({{ $cityNotices->lastPage() }})">
                    <i class="fa fa-fast-forward"></i>
                </button>
            </div>
        </div>
    </div>
@else
    <div class="text-center text-muted mt-3">
        <small>
            @if($cityNotices->total() > 0)
                عرض جميع النتائج ({{ $cityNotices->total() }} اشعار مدين)
            @else
                لا توجد اشعارات مدينة للعرض
            @endif
        </small>
    </div>
@endif

