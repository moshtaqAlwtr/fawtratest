{{-- المواد الهالكة --}}
@php
    $hasWasteMaterials = $order->manufacturOrdersItem->filter(function($item) {
        return $item->endLifeProduct;
    })->count() > 0;
@endphp
@if($hasWasteMaterials)
<div class="materials-card">
    <div class="materials-header">
        <strong><i class="fa fa-trash"></i> المواد الهالكة</strong>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="thead-light">
                    <tr>
                        <th><i class="fa fa-box me-1"></i>المنتجات</th>
                        <th><i class="fa fa-money-bill me-1"></i>السعر</th>
                        <th><i class="fa fa-calculator me-1"></i>الكمية</th>
                        <th><i class="fa fa-route me-1"></i>مسار الإنتاج الفرعي</th>
                        <th><i class="fa fa-chart-line me-1"></i>المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->manufacturOrdersItem as $item)
                        @if($item->endLifeProduct)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-exclamation-triangle text-danger me-2"></i>
                                    <strong>{{ $item->endLifeProduct->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light">
                                    {{ number_format($item->end_life_unit_price, 2) }} ر.س
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-warning">
                                    {{ $item->end_life_quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">
                                    {{ $item->endLifeProductionStage->stage_name ?? 'غير محدد' }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-warning">
                                    {{ number_format($item->end_life_total, 2) }} ر.س
                                </strong>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-warning">
                        <td colspan="4" class="text-right"><strong>إجمالي المواد الهالكة:</strong></td>
                        <td>
                            <strong class="text-warning">
                                {{ number_format($order->manufacturOrdersItem->where('endLifeProduct')->sum('end_life_total'), 2) }} ر.س
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif