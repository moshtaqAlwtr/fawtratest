{{-- resources/views/stock/products/partials/timeline.blade.php --}}
@if(isset($stock_movements) && $stock_movements->count() > 0)
    <ul class="activity-timeline timeline-left list-unstyled">
        @foreach($stock_movements as $movement)
            <li>
                <div class="timeline-icon bg-success">
                    <i class="feather icon-package font-medium-2"></i>
                </div>
                <div class="timeline-info">
                    <p>
                        @if($movement->warehousePermits->permission_type == 2)
                            أنقص <strong>{{ $movement->warehousePermits->user->name }}</strong> <strong>{{ $movement->quantity }}</strong> وحدة من مخزون <strong><a href="{{ route('products.show', $product->id) }}" target="_blank">#{{ $product->serial_number }} ({{ $product->name }})</a></strong> يدويا (رقم العملية: <strong>#{{ $movement->warehousePermits->number }}</strong>)، وسعر الوحدة: <strong>{{ $movement->unit_price }}&nbsp;ر.س</strong>، وأصبح المخزون الباقي من المنتج: <strong>{{ $movement->stock_after }}</strong> وأصبح المخزون <strong>{{ $movement->warehousePermits->storeHouse->name }}</strong> رصيده <strong>{{ $movement->stock_after }}</strong> , متوسط السعر: <strong>{{ $average_cost }}&nbsp;ر.س</strong>
                        @else
                            أضاف <strong>{{ $movement->warehousePermits->user->name }}</strong> <strong>{{ $movement->quantity }}</strong> وحدة إلى مخزون <strong><a href="{{ route('products.show', $product->id) }}" target="_blank">#{{ $product->serial_number }} ({{ $product->name }})</a></strong> يدويا (رقم العملية: <strong>#{{ $movement->warehousePermits->number }}</strong>)، وسعر الوحدة: <strong>{{ $movement->unit_price }}&nbsp;ر.س</strong>، وأصبح المخزون الباقي من المنتج: <strong>{{ $movement->stock_after }}</strong> وأصبح المخزون <strong>{{ $movement->warehousePermits->storeHouse->name }}</strong> رصيده <strong>{{ $movement->stock_after }}</strong> , متوسط السعر: <strong>{{ $average_cost }}&nbsp;ر.س</strong>
                        @endif
                    </p>
                    <br>
                    <span>
                        <i class="fa fa-clock-o"></i> {{ $movement->warehousePermits->permission_date }} - <span class="tip observed tooltipstered" data-title="{{ $movement->warehousePermits->user->ip_address }}"> <i class="fa fa-user"></i> {{ $movement->warehousePermits->user->name }}</span> - <i class="fa fa-building"></i> {{ $movement->warehousePermits->storeHouse->name }}
                    </span>
                </div>
            </li>
            <hr>
        @endforeach
    </ul>

    {{-- Pagination --}}
@if($stock_movements->hasPages())
    <nav aria-label="Timeline pagination">
        <ul class="pagination justify-content-center">

            {{-- First Page Link --}}
            @if ($stock_movements->onFirstPage())
                <li class="page-item disabled"><span class="page-link">الأول</span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadTimeline(1)">الأول</a>
                </li>
            @endif

            {{-- Previous Page Link --}}
            @if ($stock_movements->onFirstPage())
                <li class="page-item disabled"><span class="page-link">السابق</span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadTimeline({{ $stock_movements->currentPage() - 1 }})">السابق</a>
                </li>
            @endif

            {{-- Current Page Number --}}
            <li class="page-item active"><span class="page-link">{{ $stock_movements->currentPage() }}</span></li>

            {{-- Next Page Link --}}
            @if ($stock_movements->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadTimeline({{ $stock_movements->currentPage() + 1 }})">التالي</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">التالي</span></li>
            @endif

            {{-- Last Page Link --}}
            @if ($stock_movements->currentPage() == $stock_movements->lastPage())
                <li class="page-item disabled"><span class="page-link">الأخير</span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadTimeline({{ $stock_movements->lastPage() }})">الأخير</a>
                </li>
            @endif

        </ul>
    </nav>
@endif

@else
    <div class="alert alert-danger text-xl-center" role="alert">
        <p class="mb-0">لا توجد عمليات مضافه حتى الان !!</p>
    </div>
@endif
