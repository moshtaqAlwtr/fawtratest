@foreach ($clients as $client)
<div class="client-item p-3 border-bottom hover-bg-light cursor-pointer {{ $client->id == ($selectedClient->id ?? null) ? 'selected bg-light' : '' }}" 
     data-client-id="{{ $client->id }}">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success country-badge">
                    {{ $client->country_code ?? 'SA' }}
                </span>
                <span class="client-number text-muted">#{{ $client->code }}</span>
                <span class="client-name text-primary fw-medium">{{ $client->trade_name }}</span>
            </div>
            <div class="client-info small text-muted mt-1">
                <i class="far fa-clock me-1"></i>
                {{ $client->created_at->format('H:i') }} |
                {{ $client->created_at->format('M d,Y') }}
            </div>
            @if ($client->phone)
                <div class="client-contact small text-muted mt-1">
                    <i class="fas fa-phone-alt me-1"></i>
                    {{ $client->phone }}
                </div>
            @endif
        </div>
        <div class="status-badge px-2 py-1 rounded
            @if (optional($client->latestStatus)->status == 'مديون') bg-warning
            @elseif(optional($client->latestStatus)->status == 'دائن')
                bg-danger
            @elseif(optional($client->latestStatus)->status == 'مميز')
                bg-primary
            @else
                bg-secondary 
            @endif text-white">
            {{ optional($client->latestStatus)->status ?? 'غير محدد' }}
        </div>
    </div>
</div>
@endforeach

@if ($clients->hasPages())
<div class="pagination-container mt-3 d-flex justify-content-center">
    <nav aria-label="Page navigation">
        <ul class="pagination mb-0">
            {{-- Previous Page Link --}}
            @if ($clients->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">السابق</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadPage({{ $clients->currentPage() - 1 }}); return false;">السابق</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($clients->getUrlRange(1, $clients->lastPage()) as $page => $url)
                @if ($page == $clients->currentPage())
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="loadPage({{ $page }}); return false;">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($clients->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadPage({{ $clients->currentPage() + 1 }}); return false;">التالي</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">التالي</span>
                </li>
            @endif
        </ul>
    </nav>
</div>
@endif
