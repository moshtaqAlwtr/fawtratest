<div class="card mb-3">
    <div class="card-header bg-light">
        <h6 class="card-title mb-0">
            <i class="feather icon-filter"></i>
            فلاتر البحث
        </h6>
    </div>
    <div class="card-body">
        <form id="stockMovementsFilterForm">
            <div class="row">
                {{-- فلتر المصدر --}}
                <div class="col-md-4 mb-3">
                    <label for="source_filter" class="form-label">المصدر</label>
                    <select class="form-control select2" id="source_filter" name="source_id">
                        <option value="">جميع المصادر</option>
                        @if(isset($permission_sources))
                            @foreach($permission_sources as $source)
                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- فلتر التاريخ من --}}
                <div class="col-md-3 mb-3">
                    <label for="date_from_filter" class="form-label">من تاريخ</label>
                    <input type="date" class="form-control" id="date_from_filter" name="date_from">
                </div>

                {{-- فلتر التاريخ إلى --}}
                <div class="col-md-3 mb-3">
                    <label for="date_to_filter" class="form-label">إلى تاريخ</label>
                    <input type="date" class="form-control" id="date_to_filter" name="date_to">
                </div>

                {{-- أزرار التحكم --}}
                <div class="col-md-2 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary btn-sm" onclick="applyFilters()">
                            <i class="feather icon-search"></i> بحث
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                            <i class="feather icon-x"></i> مسح
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@if(isset($stock_movements) && $stock_movements->count() > 0)
    <table class="table">
        <thead class="table-light">
            <tr>
                <th style="width: 40%">العملية</th>
                <th>حركة</th>
                <th>المخزون بعد</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stock_movements as $stock_movement)
                @if($stock_movement->warehousePermits->permissionSource->id == 13)
                    {{-- صف المخزن المصدر (سحب الكمية) --}}
                    <tr>
                        <td>
                            <strong>{{ $stock_movement->warehousePermits->permission_date }} (#{{ $stock_movement->warehousePermits->id }})</strong><br>
                            <span>تحويل مخزني ({{ $stock_movement->warehousePermits->number }}#)</span><br>
                            <span>🔻 من: {{ $stock_movement->warehousePermits->fromStoreHouse->name }}</span>
                        </td>
                        <td>
                            <strong>{{ number_format($stock_movement->quantity) }}</strong>
                            <i class="feather icon-minus text-danger"></i>
                            <br>
                            <small><abbr class="initialism" title="سعر الوحدة">{{ $product->sale_price ?? 0.00 }}&nbsp;ر.س</abbr></small>
                        </td>
                        <td>
                            <strong>{{ number_format($stock_movement->stock_after) }}</strong><br>
                            <small><abbr title="سعر الوحدة">{{ $product->sale_price }} ر.س</abbr></small>
                        </td>
                    </tr>

                    {{-- صف المخزن المستقبل (إضافة الكمية) --}}
                    <tr>
                        <td>
                            <strong>{{ $stock_movement->warehousePermits->permission_date }} (#{{ $stock_movement->warehousePermits->id }})</strong><br>
                            <span>تحويل مخزني ({{ $stock_movement->warehousePermits->number }}#)</span><br>
                            <span>🔺 إلى: {{ $stock_movement->warehousePermits->toStoreHouse->name }}</span>
                        </td>
                        <td>
                            <strong>{{ number_format($stock_movement->quantity) }}</strong>
                            <i class="feather icon-plus text-success"></i>
                            <br>
                            <small><abbr class="initialism" title="سعر الوحدة">{{ $product->sale_price ?? 0.00 }}&nbsp;ر.س</abbr></small>
                        </td>
                        <td>
                            @php
                                $stockBeforeTo = \App\Models\ProductDetails::where('product_id', $product->id)
                                    ->where('store_house_id', $stock_movement->warehousePermits->to_store_house_id)
                                    ->sum('quantity');
                                $stockAfterTo = $stockBeforeTo + $stock_movement->quantity;
                            @endphp
                            <strong>{{ number_format($stockAfterTo) }}</strong><br>
                            <small><abbr title="سعر الوحدة">{{ $product->sale_price }} ر.س</abbr></small>
                        </td>
                    </tr>

                @elseif($stock_movement->warehousePermits->permission_type != 10)
                    {{-- العمليات العادية (إضافة أو صرف) --}}
                    <tr>
                        <td>
                            <strong>{{ $stock_movement->warehousePermits->permission_date }} (#{{ $stock_movement->warehousePermits->id }})</strong><br>
                            <span>
                                @if($stock_movement->warehousePermits->permission_type == 1)
                                    إضافة مخزن
                                @elseif($stock_movement->warehousePermits->permission_type == 2)
                                    صرف مخزن
                                @endif
                                ({{ $stock_movement->warehousePermits->number }}#)
                            </span><br>
                            <span>{{ $stock_movement->warehousePermits->storeHouse->name }}</span>
                        </td>
                        <td>
                            <strong>
                                {{ number_format($stock_movement->quantity) }}
                                @if($stock_movement->warehousePermits->permission_type == 1)
                                    <i class="feather icon-plus text-success"></i>
                                @elseif($stock_movement->warehousePermits->permission_type == 2)
                                    <i class="feather icon-minus text-danger"></i>
                                @endif
                            </strong>
                            <br>
                            <small><abbr class="initialism" title="سعر الوحدة">{{ $product->sale_price ?? 0.00 }}&nbsp;ر.س</abbr></small>
                        </td>
                        <td>
                            <strong>{{ number_format($stock_movement->stock_after) }}</strong><br>
                            <small><abbr title="سعر الوحدة">{{ $product->sale_price }} ر.س</abbr></small>
                        </td>
                    </tr>
                @endif

                @if ($stock_movement->warehousePermits->permission_type == 10)
                    {{-- حساب مبيعات الفواتير --}}
                    <tr>
                        <td>
                            <strong>{{ $stock_movement->warehousePermits->permission_date }} (#{{ $stock_movement->warehousePermits->id }})</strong><br>
                            <span> فاتورة رقم ({{ $stock_movement->warehousePermits->number }}#)</span><br>
                        </td>
                        <td>
                            <strong>{{ number_format($stock_movement->quantity) }}</strong>
                            <i class="feather icon-minus text-danger"></i>
                            <br>
                            <small><abbr class="initialism" title="سعر الوحدة">{{ $product->sale_price ?? 0.00 }}&nbsp;ر.س</abbr></small>
                        </td>
                        <td>
                            <strong>{{ number_format($stock_movement->stock_after) }}</strong><br>
                            <small><abbr title="سعر الوحدة">{{ $product->sale_price }} ر.س</abbr></small>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    {{-- Pagination --}}
@if($stock_movements->hasPages())
    <nav aria-label="Stock movements pagination">
        <ul class="pagination justify-content-center">

            {{-- First Page Link --}}
            @if ($stock_movements->onFirstPage())
                <li class="page-item disabled"><span class="page-link">الأول</span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadStockMovements(1)">الأول</a>
                </li>
            @endif

            {{-- Previous Page Link --}}
            @if ($stock_movements->onFirstPage())
                <li class="page-item disabled"><span class="page-link">السابق</span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadStockMovements({{ $stock_movements->currentPage() - 1 }})">السابق</a>
                </li>
            @endif

            {{-- Current Page --}}
            <li class="page-item active"><span class="page-link">{{ $stock_movements->currentPage() }}</span></li>

            {{-- Next Page Link --}}
            @if ($stock_movements->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadStockMovements({{ $stock_movements->currentPage() + 1 }})">التالي</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">التالي</span></li>
            @endif

            {{-- Last Page Link --}}
            @if ($stock_movements->currentPage() == $stock_movements->lastPage())
                <li class="page-item disabled"><span class="page-link">الأخير</span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadStockMovements({{ $stock_movements->lastPage() }})">الأخير</a>
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
