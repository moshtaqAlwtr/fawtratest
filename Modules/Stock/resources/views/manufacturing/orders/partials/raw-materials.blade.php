{{-- المواد الخام --}}
@if(isset($order->manufacturOrdersItem) && count($order->manufacturOrdersItem) > 0)
<div class="materials-card">
    <div class="materials-header">
        <strong><i class="fa fa-cubes"></i> المواد الخام</strong>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="thead-light">
                    <tr>
                        <th><i class="fa fa-box me-1"></i>المنتج</th>
                        <th><i class="fa fa-money-bill me-1"></i>السعر</th>
                        <th><i class="fa fa-calculator me-1"></i>الكمية</th>
                        <th><i class="fa fa-route me-1"></i>مسار الإنتاج الفرعي</th>
                        <th><i class="fa fa-chart-line me-1"></i>المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->manufacturOrdersItem as $item)
                        @if($item->rawProduct)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-cube text-primary me-2"></i>
                                    <strong>{{ $item->rawProduct->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light">
                                    {{ number_format($item->raw_unit_price, 2) }} ر.س
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $item->raw_quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">
                                    {{ $item->rawProductionStage->stage_name ?? 'غير محدد' }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-success">
                                    {{ number_format($item->raw_total, 2) }} ر.س
                                </strong>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <td colspan="4" class="text-right"><strong>إجمالي المواد الخام:</strong></td>
                        <td>
                            <strong class="text-success">
                                {{ number_format($order->manufacturOrdersItem->where('rawProduct')->sum('raw_total'), 2) }} ر.س
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif